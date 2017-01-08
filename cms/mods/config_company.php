<?php

$db_table = 'config_general';

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	$admin->breadcrumbs();
	$admin->pageTitle();
	
	echo '
	<div class="row">
		<div class="columns large-12">';

		while_part("Empresa"," WHERE tipo='company' 
								&& chave NOT LIKE '%google-maps-%'
								&& chave NOT LIKE '%social-%'
								&& chave!='company-address'
								&& chave!='company-contato'
								&& chave!='company-fone'
								&& chave!='general_description'
								&& chave!='general_keywords'
				  ");

		while_part("Contato"," WHERE tipo='company' && chave='company-contato' || chave='company-fone'");

		while_part("Localização"," WHERE tipo='company' && chave LIKE '%google-maps-%' || chave = 'company-address'");

		while_part("Meta Tags (Google)"," WHERE tipo='company' && (chave='general_keywords' || chave='general_description')");

		while_part("Redes Sociais"," WHERE tipo='company' && chave LIKE '%social-%'");

		echo '
		</div>
	</div>';

}

function while_part($titulo,$where)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	echo '
	<fieldset style="padding:0;">
		<legend style="margin-left:10px;">' . $titulo . '</legend>
		<table class="list-table" style="border:0; margin:0 0 10px 0;">
			<tbody>';
			
			$query = "SELECT * FROM " . $db_table . $where . " ORDER BY id";
			$rr = mysql_query($query);
			while ($arr = mysql_fetch_array($rr))
			{
				if(is_html($arr['valor'])) $arr['valor']='<span class="disabled">[Conteúdo HTML]</span>';
				if(empty($arr['valor'])) $arr['valor']='<span class="disabled">(vazio)</span>';
				echo '
				<tr>
					<td style="width:30%;">
						<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['descricao'] . '</a>
					</td>
					<td>
						<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['valor'] . '</a>
					</td>
				</tr>';
			}

			echo '
			</tbody>
		</table>
	</fieldset>';
}

function Form($id)

{

	global $admin, $admin_mods, $config, $on, $in, $db_table;

	$admin->breadcrumbs();

	$admin->pageTitle();

	if($in == 'editar')

	{

		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE tipo='company' && id='" . $id . "' LIMIT 1"));

		if($admin->isDeveloper())
		{
			showCode($id);
		}
	}

	echo '

	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>
		  <legend>Formulário</legend>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Chave
		        <input type="text" name="chave" placeholder="Chave" value="' . $arr['chave'] . '"'; if($in == 'editar') echo ' disabled'; echo '/>
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Descrição
		        <input type="text" name="descricao" placeholder="Descrição" value="' . $arr['descricao'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Conteúdo';
		      if($arr['chave'] == 'google-maps-iframe') echo ' (<a href="http://goo.gl/e764Ka" target="_blank">Saiba como</a>)';
		      if($arr['chave'] == 'general_keywords') echo ' (<a href="http://goo.gl/G5xNw8" target="_blank">Saiba mais</a>)';
		      if($arr['chave'] == 'google-maps-coordenadas') echo ' (<a href="http://goo.gl/5Z9KOy" target="_blank">Saiba como</a> ou utilize uma <a href="http://www.mapcoordinates.net/pt" target="_blank">ferramenta</a> não oficial)';
		      if($arr['campo'] == 'textarea' || $arr['campo'] == 'html')
		      {
		      	if($arr['campo'] == 'html') $class='ckeditor';

		      	echo '

		        <textarea name="valor" placeholder="Conteúdo" required class="height100 ' . $class . '">' . $arr['valor'] . '</textarea>';

		      }else

		      {

		      	echo '

		        <input type="text" name="valor" placeholder="Conteúdo" value="' . $arr['valor'] . '" />';

		      }

		        echo '

		      </label>
		    </div>

		  </div>

		</fieldset>

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

	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;

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

			$campos .= $key;

			$valores .= "'$value'";

			if(!empty($_POST['id'])) $update .= " $key='$value'"; //se vier do form editar, add na var update

		}

	}

	//gravando informações no banco

	if(empty($_POST['id'])) //se vier de um form de inclusão

	{

		mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));

		$id = mysql_insert_id();

	}else

	{

		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));

		$id = $_POST['id'];

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

		mysql_query("DELETE FROM " . $db_table . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));

		//redirecionando página

		header('Location: index.php?on=' . $on);

	}	

}

function showCode($id)
{
	global $db_table, $dados;

	$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'"));
	echo '
		<div class="row show-code">
			<div class="columns large-12">
				<a href="javascript:void(0);" class="open-code right"><i class="fa fa-code" aria-hidden="true"></i>
</a>
				<dl class="tabs show-code" data-tab>
				  <dd class="active"><a href="#panel2-1">PHP</a></dd>
				  <dd><a href="#panel2-2">HTML</a></dd>
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
				    <pre>
						<code>';
							$codigo = 
							'//Config Var: ' . utf8_decode($arr['descricao'])."\r\n"
							.'list($config_' . $dados->create_slug_(trim($arr['chave'])) . ') = mysql_fetch_row(mysql_query("SELECT valor FROM ' . $db_table . ' WHERE chave=\'' . $arr['chave'] . '\'"));'."\r\n"
							.'Parser::__alloc("config_' . $dados->create_slug_(trim($arr['chave'])) . '",$config_' . $dados->create_slug_(trim($arr['chave'])) . ');';
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>';
							$codigo = 
							'<!--Config Var: ' . $arr['chave']."-->\r\n"
							.'<div class="row">'."\r\n"
							.'  <div class="columns large-12 twelve">'."\r\n"
							.'    <var name="config_' . $dados->create_slug_($arr['chave']) . '" />'."\r\n"
							.'  </div>'."\r\n"
							.'</div>'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				</div>
			</div>
		</div>';
}

function is_html($string)
{
  return preg_match("/<[^<]+>/",$string,$m) != 0;
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

}