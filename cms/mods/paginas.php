<?php

$db_table='config_pages';
$tamanho = array('img1'=>'500','img2'=>'1360','img3'=>'0');

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou','');

	if(mysql_num_rows(mysql_query("SELECT id FROM config_pages WHERE menu='Y' && ativa='Y' LIMIT 1")) > 0)
	{
		echo '
		<div class="row">
			<div class="columns large-12 large-centered">
				<h3>Menu Principal</h3>
				<div id="main-menu" class="dd">
					' . listChilds(0,'Y','Y') . '
				</div>
			</div>
		</div>';
	}

	if(mysql_num_rows(mysql_query("SELECT id FROM config_pages WHERE menu='N' && ativa='Y' LIMIT 1")) > 0)
	{
		echo '
		<p>&nbsp;</p>

		<div class="row">
			<div class="columns large-12 large-centered">
				<h3>Outras páginas</h3>
				<div id="other-pages" class="dd">
					' . listChilds(0,'N','Y') . '
				</div>
			</div>
		</div>';
	}

	if(mysql_num_rows(mysql_query("SELECT id FROM config_pages WHERE ativa='N' LIMIT 1")) > 0)
	{
		echo '
		<p>&nbsp;</p>

		<div class="row">
			<div class="columns large-12 large-centered">
				<h3>Páginas inativas</h3>
				<div id="inactive-pages" class="dd">
					' . listChilds(0,'','N') . '
				</div>
			</div>
		</div>';
	}
}

function listChilds($id,$menu,$ativa)
{
	global $admin;

	if(!empty($menu)) $query_menu = "menu='" . $menu . "' && ";

	$query = "SELECT * FROM config_pages WHERE " . $query_menu . " ativa='" . $ativa . "' && cid='" . $id . "' ORDER BY menu_order,nome";

	$rr = mysql_query($query);
	if(mysql_num_rows(mysql_query($query)) > 0)
	{
		$data .= '<ol class="dd-list">';
		while ($arr = mysql_fetch_array($rr))
		{
			$data .= '
			<li class="dd-item" data-id="' . $arr['id'] . '">
	            <div class="dd-handle">
		            <a href="index.php?on=paginas&in=editar&id=' . $arr['id'] . '" class="dd-nodrag">
		            	' . $arr['nome'] . ' ' . $arr['subnome'] . '
		            </a>
		        	<span style="font-weight:normal;">&nbsp /' . $arr['page'] . '</span>';

		        	if($arr['ativa'] == 'Y')
		        	{ 
		        		$data .= '
		        		<a href="index.php?on=paginas&in=changeAction&action=add&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu ';
		        		if($arr['menu'] == 'Y') $data .= 'active" data-tooltip aria-haspopup="true" class="has-tip" title="Remover do Menu">';
		        		else $data .= 'disabled" data-tooltip aria-haspopup="true" class="has-tip" title="Adicionar ao Menu">';

		        		$data .= '<i class="fa fa-bars" aria-hidden="true"></i></a>';
		        	}
		        	
		        	if($arr['ativa'] == 'N')
		        	{
		        		$data .= '
		        		<a href="index.php?on=paginas&in=changeAction&action=erase&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu confirm" data-tooltip aria-haspopup="true" class="has-tip" title="Confirmar a exclusão definitiva da página?">Confirmar</a>
		        		<i class="dd-nodrag fa fa-trash right" aria-hidden="true" data-tooltip aria-haspopup="true" class="has-tip" title="Excluir Página"></i>';
		        	}
		        	
		        	$data .= '
		        	<a href="index.php?on=paginas&in=changeAction&action=activate&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu ';
		        			if($arr['ativa'] == 'Y') $data .= '" data-tooltip aria-haspopup="true" class="has-tip" title="Desativar Página"><i class="fa fa-toggle-on set-active" aria-hidden="true" data-id="18"></i>';
		        			else $data .= 'remove" data-tooltip aria-haspopup="true" class="has-tip" title="Ativar Página"><i class="fa fa-toggle-off set-active" aria-hidden="true" data-id="18"></i>'; 
		        	$data .= '</a>';

		        	if($admin->isDeveloper() && $arr['menu'] == 'Y' && $arr['link'] == 'Y')
		        	{
		        		$data .= '
		        		<a href="index.php?on=paginas&in=changeAction&action=anchor&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu ';
		        		if($arr['anchor'] == 'Y') $data .= 'active" data-tooltip aria-haspopup="true" class="has-tip" title="Alternar para abrir um link">';
		        		else $data .= 'disabled" data-tooltip aria-haspopup="true" class="has-tip" title="Alternar para abrir uma âncora">';

		        		$data .= '<i class="fa fa-hashtag" aria-hidden="true"></i></a>';
		        	}


		        	if($admin->isDeveloper() && $arr['menu'] == 'Y' && $arr['link'] == 'Y')
		        	{ 
		        		$data .= '
		        		<a href="index.php?on=paginas&in=changeAction&action=target&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu ';
		        		if($arr['target'] == '_blank') $data .= '" data-tooltip aria-haspopup="true" class="has-tip" title="Alternar para abrir na mesma janela">';
		        		else $data .= 'disabled" data-tooltip aria-haspopup="true" class="has-tip" title="Alternar para abrir em uma nova janela">';

		        		$data .= '<i class="fa fa-external-link" aria-hidden="true"></i></a>';
		        	}

		        	if($admin->isDeveloper() && $arr['menu'] == 'Y')
		        	{
		        		$data .= '
		        		<a href="index.php?on=paginas&in=changeAction&action=link&id=' . $arr['id'] . '&print=Y" class="dd-nodrag right menu ';
		        		if($arr['link'] == 'Y') $data .= 'active" data-tooltip aria-haspopup="true" class="has-tip" title="Desativar link"><i class="fa fa-link" aria-hidden="true"></i>';
		        		else $data .= 'remove" data-tooltip aria-haspopup="true" class="has-tip" title="Ativar Link"><i class="fa fa-chain-broken" aria-hidden="true"></i>';

		        		$data .= '</a>';
		        	}

		        	$data .= '
	           	</div>
	            ' . listChilds($arr['id'],$menu,$ativa) . '
	        </li>';
		}
		$data .= '</ol>';
	}

	return $data;
}

function Form($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	$admin->breadcrumbs();
	$admin->pageTitle();

	if($in == 'editar')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "' LIMIT 1"));
		$admin->saveLog('visualizou',"Página: " . $arr['nome'] . " / ID: " . $arr['id']);
	}
	else
	{
		$arr['meta_title'] = '{page-name} | {nome-da-empresa}';
		$arr['meta_description'] = '{default-description}';
		$arr['meta_keywords'] = '{default-keywords}';
	}



	echo '

	<div class="row">

	  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
	  	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>

		  <legend>Dados Gerais</legend>

		  <div class="row">

		    <div class="'; if($admin->isDeveloper()) echo 'large-7'; else echo 'large-12'; echo ' columns">

		      <label>Título da página

		        <input type="text" name="nome" id="nome" placeholder="Nome da Página" value="' . $arr['nome'] . '" required />

		      </label>

		    </div>';

		    if($admin->isDeveloper())
		    {
			    echo '
			    <div class="columns large-5">
			  	  <div class="row collapse">
				  	<label>Nome do arquivo</label>
				    <div class="small-3 large-4 columns">
				      <span class="prefix" style="height: 49px; line-height: 49px;">/pages/</span>
				    </div>
				    <div class="small-5 large-6 columns">
				      <input type="text" name="page" id="page" placeholder="Nome do arquivo" value="' . $arr['page'] . '" style="border-bottom: 1px solid #ccc;"'; if(!empty($arr['page'])) echo ' class="preenchido"'; echo '>
				    </div>
				    <div class="small-3 large-2 columns end">
				      <span class="postfix" style="height: 49px; line-height: 49px;">.html</span>
				    </div>
				  </div>
			  	</div>';

			}

		  	echo '
		  </div>

		  <div class="row">

		    <div class="large-12 columns">

		      <label>Subtítulo da Página

		        <input type="text" name="subnome" placeholder="Subtítulo da Página" value="' . $arr['subnome'] . '" />

		      </label>

		    </div>

		  </div>

		  <div class="row">

		    <div class="large-12 columns">

		      <label>Texto do Cabeçalho

		        <input type="text" name="texto" placeholder="Texto do Cabeçalho" value="' . $arr['texto'] . '" />

		      </label>

		    </div>

		  </div>

		  <div class="row">
			    <div class="large-6 columns image-box end">'
			    . $admin->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
			    . '<br/><br/>
			    </div>
		  </div>

		</fieldset>';

		if($admin->isDeveloper())
		{
			echo '
			<fieldset>
			  <legend>Plugins Javascript</legend>
			  <div class="row">
			  	<div class="columns large-12">';
			  		$javascripts = explode(',',$arr['javascripts']);
			  		$r2 = mysql_query("SELECT * FROM config_plugins ORDER by nome");
			  		while($ar2 = mysql_fetch_array($r2))
			  		{
			  			echo '
						    <div class="large-4 columns end">
						    <label for="' . $ar2['chave'] . '">
						    	<input name="javascripts[]" id="' . $ar2['chave'] . '" type="checkbox" value="' . $ar2['chave'] . '"';
						    	if(in_array($ar2['chave'],$javascripts)) echo ' checked';
						    	echo '> ' . $ar2['nome'] . '</label>
						    </div>';
			  		}
			  		echo '
			  	</div>
			  </div>
			</fieldset>';
		}

		echo '

		<fieldset id="meta-tags">

		  <legend>Meta Tags</legend>

		  <div class="row">

		    <div class="large-12 columns">

		      <label>Título

		        <input type="text" name="meta_title" placeholder="Título" value="' . $arr['meta_title'] . '" required />

		      </label>

		    </div>

		  </div>

		  <div class="row">

		    <div class="large-12 columns">

		      <label>Descrição

		        <textarea name="meta_description" placeholder="Descrição"  required>' . $arr['meta_description'] . '</textarea>

		      </label>

		    </div>

		  </div>

		  <div class="row">

		    <div class="large-12 columns">

		      <label>Palavras-chave

		        <textarea name="meta_keywords" placeholder="Palavras-chave"  required>' . $arr['meta_keywords'] . '</textarea>

		      </label>

		    </div>

		  </div>

		</fieldset>';

	echo '
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">
				<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar <i class="fa fa-times" aria-hidden="true"></i></a>
		    	<button type="submit">' . $admin->formButton() . '</button>
			</div>
		</div>
	  </form>
	</div>';

}



function Salvar()

{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $_FILES;

	$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "' LIMIT 1"));

	//editando pre-vars

	$dontPost[] = 'permissoes'; $dontPost[] = 'imagem';

	/*if(empty($_POST['ativa'])) $_POST['ativa'] = 'N';

	if(empty($_POST['menu'])) $_POST['menu'] = 'N';*/

	//if(empty($_POST['anchor'])) $_POST['anchor'] = 'N';

	//if($arr['ativa'] != $_POST['ativa'] || $arr['menu'] != $_POST['menu']) $_POST['cid'] = '0';


	//tratando vars enviadas

	$campos=''; $valores=''; $c=0;

	foreach ($_POST as $key => $value)

	{

		//restrições

		$show=true;

		if(in_array($key,$dontPost)) $show=false;



		//se tudo ok, adiciona campo na lista

		if($show)

		{

			$c++;

			if($c>1){ $campos .= ','; $valores .= ','; $update .= ','; } //add virgulas antes dos campos



			if($key == 'javascripts')

			{

				$value='';

				foreach ($_POST['javascripts'] as $v)

				{

					$value .= $v . ',';

				}

			}



			$campos .= $key;

			$valores .= "'$value'";

			if(!empty($_POST['id'])) $update .= " $key='$value'"; //se vier do form editar, add na var update

		}

	}

	$arq = New Arquivo;
	if (!empty($_FILES['imagem']['name']))
	{
		$campos_original = array('img1','img2','img3');
		foreach ($campos_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";			
		}
	}



	//gravando informações no banco

	if(empty($_POST['id'])) //se vier de um form de inclusão

	{

		mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));

		$id = mysql_insert_id();

		$admin->saveLog('inseriu',"Nome: " . $_POST['nome'] . " / ID: " . $id);

	}else

	{

		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));

		$id = $_POST['id'];

		$admin->saveLog('editou',"Nome: " . $_POST['nome'] . " / ID: " . $id);

	}



	//redirecionando página

	header('Location: index.php?on=' . $on);

}



function Apagar($id)

{

	global $admin, $admin_mods, $config, $on, $in, $db_table;





	if(empty($_POST['conf']))

	{

		$admin->breadcrumbs();

		$admin->pageTitle();



		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));



		echo '

		<div class="row">

		  <form method="post" action="index.php?on=' . $on . '">

		  	<input type="hidden" name="in" value="apagar" />

		  	<input type="hidden" name="id" value="' . $arr['id'] . '" />

		  	<input type="hidden" name="conf" value="aham" />

		    <fieldset>

			  <legend>Tem certeza que deseja apagar este registro?</legend>

			  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>

			  <div class="row">

			    <div class="large-12 columns text-center">

			    	<h4>' . $arr['nome'] . '</h4>

				</div>

			  </div>

			  <div class="row">

			    <div class="large-12 columns text-center marginleft20 margintop20">

			    	<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '" class="button secondary">Cancelar</a>

			    	<button type="submit" class="alert marginleft20">Confirmar exclusão</a>

				</div>

			  </div>

			</fieldset>				

		  </form>

		</div>';

	}

	else

	{

		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $_POST['id'] . "'")) or die($admin->alertMysql("O Registro não existe."));

		mysql_query("DELETE FROM " . $db_table . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));

		$admin->saveLog('apagou',"Nome: " . $arr['nome'] . " / ID: " . $arr['id']);



		//redirecionando página

		header('Location: index.php?on=' . $on);

	}	

}



function updateMenuOrder()
{
	global $admin, $on, $in;

	$admin->saveLog('alterou a ordem do menu','');

	$newList = str_replace('\"','"',$_POST['neworder']);
	$neworder = json_decode($newList, true);
	//print_r($neworder);

	$c=0;
	foreach ($neworder as $key => $value)
	{
		$c++;
		mysql_query("UPDATE config_pages SET cid='0', menu_order='" . $c . "' WHERE id='" . $value['id'] . "'");

		$i=0;
		if(is_array($value['children']))
		{
			foreach ($value['children'] as $childKey => $childValue)
			{
				$i++;
				mysql_query("UPDATE config_pages SET cid='" . $value['id'] . "', menu_order='" . $i . "' WHERE id='" . $childValue['id'] . "'");

				$h=0;
				if(is_array($childValue['children']))
				{
					foreach ($childValue['children'] as $childGKey => $childGValue)
					{
					$h++;
						mysql_query("UPDATE config_pages SET cid='" . $childValue['id'] . "', menu_order='" . $h . "' WHERE id='" . $childGValue['id'] . "'");
						
					}
				}
				
			}
		}
	}

}

function changeAction($action,$id)
{
	global $admin, $on, $in;

	$arr = mysql_fetch_array(mysql_query("SELECT * FROM config_pages WHERE id='" . $id . "' LIMIT 1"));
	
	if($action == 'add')
	{
		$admin->saveLog('ativou item no menu','');

		if($arr['menu'] == 'N') $menu_value='Y';
		else $menu_value='N';

		mysql_query("UPDATE config_pages SET cid='0', menu='" . $menu_value . "' WHERE id='" . $id . "'");
		mysql_query("UPDATE config_pages SET menu='" . $menu_value . "' WHERE cid='" . $id . "'");
	}
	elseif($action == 'activate')
	{
		$admin->saveLog('ativou uma página','');

		if($arr['ativa'] == 'N') $menu_value='Y';
		else $menu_value='N';

		mysql_query("UPDATE config_pages SET cid='0', ativa='" . $menu_value . "' WHERE id='" . $id . "'");
		mysql_query("UPDATE config_pages SET ativa='" . $menu_value . "' WHERE cid='" . $id . "'");
	}
	elseif($action == 'erase')
	{
		$admin->saveLog('excluiu uma página','');

		if($arr['ativa'] == 'N')
		{
			mysql_query("DELETE FROM config_pages WHERE id='" . $id . "'");
		}
	}
	elseif($action == 'anchor')
	{
		$admin->saveLog('Alterou página para âncora','');

		if($arr['anchor'] == 'N') $menu_value='Y';
		else $menu_value='N';

		mysql_query("UPDATE config_pages SET anchor='" . $menu_value . "' WHERE id='" . $id . "'");
	}
	elseif($action == 'target')
	{
		$admin->saveLog('Alterou página para âncora','');

		if($arr['target'] == '_top') $menu_value='_blank';
		else $menu_value='_top';

		mysql_query("UPDATE config_pages SET target='" . $menu_value . "' WHERE id='" . $id . "'");
	}
	elseif($action == 'link')
	{
		$admin->saveLog('Desativou um link','');

		if($arr['link'] == 'N') $menu_value='Y';
		else $menu_value='N';

		mysql_query("UPDATE config_pages SET link='" . $menu_value . "' WHERE id='" . $id . "'");
	}

	header('Location: index.php?on=paginas');
}



switch($in)
{
	default;
	Main();
	break;
	
	case "novo";
	case "editar";
	Form($id);
	break;

	case "inserir";
	case "salvar";
	Salvar();
	break;

	case "apagar";
	Apagar($id);
	break;

	case "changeAction";
	changeAction($action, $id);
	break;

	case "updateMenuOrder";
	updateMenuOrder();
	break;

}