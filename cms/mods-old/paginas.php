<?php



$db_table='config_pages';

$tamanho = array('img1'=>'500','img2'=>'1360','img3'=>'0');


function Main()

{

	global $admin, $admin_mods, $config, $on, $in, $db_table;

	

	$admin->breadcrumbs();

	$admin->pageTitle();

	$admin->saveLog('acessou','');

	

	$query = "SELECT * FROM " . $db_table . " WHERE menu='Y' && ativa='Y' ORDER BY menu_order,nome";

	echo '

	<div class="row">

		<div class="columns large-12">

			<h4>Menu Principal</h4>

		</div>

	</div>

	<div class="row">

		<div class="columns large-12">';

			if(mysql_num_rows(mysql_query($query)) > 0)

			{

				echo '

				<table id="tabela_menu" class="list-table">

					<thead>

						<tr>

							<th width="200">

							Páginas

							<span class="right disabled">Arraste as linhas para reordenar</span>

							</th>

						</tr>

					</thead>

					<tbody>';

					$rr = mysql_query($query);

					while ($arr = mysql_fetch_array($rr))

					{

						echo '

						<tr id="' . $arr['id'] . '" class="ui-state-default">

							<td>

								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['nome'] . '</a>

							</td>

						</tr>';

					}

					echo '

					</tbody>

				</table>';

			}

			else

			{

				echo '

				<div data-alert class="alert-box info radius">

				  Nenhum item encontrado.

				  <a href="#" class="close">&times;</a>

				</div>';

			}

			echo '

		</div>

	</div>';



	$query = "SELECT * FROM " . $db_table . " WHERE menu='N' && ativa='Y' ORDER BY nome";

	echo '

	<div class="row margintop20">

		<div class="columns large-12">

			<h4>Outras páginas</h4>

		</div>

	</div>

	<div class="row">

		<div class="columns large-12">';

			if(mysql_num_rows(mysql_query($query)) > 0)

			{

				echo '

				<table class="list-table">

					<thead>

						<tr>

							<th width="200">Página</th>

						</tr>

					</thead>

					<tbody>';

					$rr = mysql_query($query);

					while ($arr = mysql_fetch_array($rr))

					{

						echo '

						<tr>

							<td>

								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['nome'] . '</a>

							</td>

						</tr>';

					}

					echo '

					</tbody>

				</table>';

			}

			else

			{

				echo '

				<div data-alert class="alert-box info radius">

				  Nenhum item encontrado.

				  <a href="#" class="close">&times;</a>

				</div>';

			}

			echo '

		</div>

	</div>';

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

		    <div class="large-12 columns">

		      <label>Nome da Página

		        <input type="text" name="nome" placeholder="Nome da Página" value="' . $arr['nome'] . '" required />

		      </label>

		    </div>

		  </div>

		  <div class="row">
			    <div class="large-6 columns image-box end">'
			    . $admin->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
			    . '<br/><br/>
			    </div>
		  </div>

		  <div class="row">

		    <div class="large-2 columns">

		      <label>

		        <input type="checkbox" id="menu" name="menu" value="Y"';

			    	if($arr['menu'] == 'Y') echo ' checked';

			    	echo ' /> Mostrar no menu

		      </label>

		    </div>

		    <div id="abrir-em" class="large-4 columns">

				<div class="row">

			        <div class="small-3 columns">

			          <label for="right-label" class="right">Abrir em</label>

			        </div>

			        <div class="small-9 columns">

			            <select name="target">

				          <option value="_top"'; if($arr['target'] == '_top') echo ' selected'; echo '>Mesma aba</option>

				          <option value="_blank"'; if($arr['target'] == '_blank') echo ' selected'; echo '>Nova aba</option>

				        </select>

			        </div>

			      </div>

		    </div>

		    <div class="large-3 columns end">

		      <label>

		        <input type="checkbox" id="anchor" name="anchor" value="Y"';

			    	if($arr['anchor'] == 'Y') echo ' checked';

			    	echo ' /> Âncora (#mesma-pagina)

		      </label>

		    </div>

		  </div>

		</fieldset>

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

		if($admin->isDeveloper())
		{
			echo '
			<fieldset>
			  <legend>Desenvolvedor</legend>
			  <div class="row">
				    <div class="large-12 columns">
				    	<input name="ativa" id="ativa" type="checkbox" value="Y"';
				    	if($arr['ativa'] == 'Y') echo ' checked';
				    	echo '><label for="ativa"><strong>Página ativa</strong></label>
			      </div>
		      </div>
			  <div class="row">
			  	<div class="columns large-6">
				  <div class="row collapse">
				  	<label>Nome do arquivo</label>
				    <div class="small-3 large-3 columns">
				      <span class="prefix">/pages/</span>
				    </div>
				    <div class="small-5 large-4 columns">
				      <input type="text" name="page" placeholder="Nome do arquivo" value="' . $arr['page'] . '">
				    </div>
				    <div class="small-3 large-2 columns end">
				      <span class="postfix">.html</span>
				    </div>
				  </div>
				  <div class="row">
				  	<div class="large-12 columns">
				  		<label class="marginbottom10 bold">Javascripts</label>';
				  		$javascripts = explode(',',$arr['javascripts']);
				  		$r2 = mysql_query("SELECT * FROM config_plugins ORDER by descricao");
				  		while($ar2 = mysql_fetch_array($r2))
				  		{
				  			echo '
							    <div class="large-12 columns">
							    <label for="' . $ar2['chave'] . '">
							    	<input name="javascripts[]" id="' . $ar2['chave'] . '" type="checkbox" value="' . $ar2['chave'] . '"';
							    	if(in_array($ar2['chave'],$javascripts)) echo ' checked';
							    	echo '> ' . $ar2['nome'] . '</label>
							    </div>';
				  		}
				  		echo '
				  	</div>
				  </div>
			  	</div>
			  	<div class="columns large-6">
				  <div class="row">
				  	<label class="marginbottom10 bold">Módulos do CMS</label>
			  		
			  		<div class="row collapse">
				  		<div class="small-3 large-3 columns">
					      <span class="prefix">/cms/mods/</span>
					    </div>
					    <div class="small-5 large-4 columns">
					      <input type="text" name="admin_mod1" placeholder="Módulo do CMS" value="' . $arr['admin_mod1'] . '">
					    </div>
					    <div class="small-3 large-2 columns end">
					      <span class="postfix">.php</span>
					    </div>
			  		</div>';

			  		if(!empty($arr['admin_mod2']))
			  		{
			  			echo '
				  		<div class="row collapse">
					  		<div class="small-3 large-3 columns">
						      <span class="prefix">/cms/mods/</span>
						    </div>
						    <div class="small-5 large-4 columns">
						      <input type="text" name="admin_mod2" placeholder="Módulo do CMS" value="' . $arr['admin_mod2'] . '">
						    </div>
						    <div class="small-3 large-2 columns end">
						      <span class="postfix">.php</span>
						    </div>
				  		</div>';		  			
			  		}

			  		if(!empty($arr['admin_mod3']))
			  		{
			  			echo '
				  		<div class="row collapse">
					  		<div class="small-3 large-3 columns">
						      <span class="prefix">/cms/mods/</span>
						    </div>
						    <div class="small-5 large-4 columns">
						      <input type="text" name="admin_mod3" placeholder="Módulo do CMS" value="' . $arr['admin_mod3'] . '">
						    </div>
						    <div class="small-3 large-2 columns end">
						      <span class="postfix">.php</span>
						    </div>
				  		</div>';
				  	}
				  	echo '			    
				  </div>
			  	</div>
			  </div>
			</fieldset>';
		}else
		{
			echo '
			<input type="hidden" name="ativa" value="' . $arr['ativa'] . '" />';
		}

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



	//editando pre-vars

	$dontPost[] = 'permissoes'; $dontPost[] = 'imagem';

	if(empty($_POST['ativa'])) $_POST['ativa'] = 'N';

	if(empty($_POST['menu'])) $_POST['menu'] = 'N';

	if(empty($_POST['anchor'])) $_POST['anchor'] = 'N';


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



function updatemenu()

{

	global $admin, $on, $in;

	$admin->saveLog('alterou a ordem do menu','');



	foreach ($_POST['neworder'] as $key => $value)

	{

		mysql_query("UPDATE config_pages SET menu_order='" . $key . "' WHERE id='" . $value . "'");

	}

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

	

	case "updatemenu";

	updatemenu();

	break;	

}