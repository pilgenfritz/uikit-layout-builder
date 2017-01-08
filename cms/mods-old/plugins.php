<?php
$db_table = 'config_plugins';
function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	
	$query = "SELECT * FROM " . $db_table . " ORDER BY id";
	$admin->breadcrumbs();
	$admin->pageTitle();
	
	echo '
	<div class="row">
		<div class="columns large-12">
			<table class="list-table">
				<thead>
					<tr>
						<th width="300">Nome</th>
						<th class="hide-for-small">Descrição</th>
					</tr>
				</thead>
				<tbody>';
				$rr = mysql_query($query);
				while ($arr = mysql_fetch_array($rr))
				{
					echo '
					<tr>
						<td>
							<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['nome'] . '</a>';
							if(mysql_num_rows(mysql_query("SELECT * FROM config_pages WHERE javascripts NOT LIKE '%" . $arr['chave'] . "%'")) > 0)
							{
								echo '
								<a href="index.php?on=' . $on . '&in=setDefaultPages&id=' . $arr['id'] . '&print=Y" class="label active radius right">Set Padrão</a>';
							}else
							{
								echo '
								<span class="label secondary radius right" style="margin-left:15px;">Padrão</span>';
							}
						echo '
						</td>
						<td class="hide-for-small">
							<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['descricao'] . '</a>
						</td>
					</tr>';
				}
				echo '
				</tbody>
			</table>
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
		        <input type="text" name="chave" placeholder="Chave" value="' . $arr['chave'] . '" required'; if(!empty($arr['chave'])) echo ' disabled'; echo ' />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Nome do Plugin
		        <input type="text" name="nome" placeholder="Nome do Plugin" value="' . $arr['nome'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Descrição do Plugin
		        <input type="text" name="descricao" placeholder="Descrição do Plugin" value="' . $arr['descricao'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>.js
		        <textarea name="valor" placeholder="Conteúdo Javascript" style="height:150px;">' . $arr['valor'] . '</textarea>
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>.css
		        <textarea name="css" placeholder="Conteúdo CSS" style="height:150px;">' . $arr['css'] . '</textarea>
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		    	<h3>Exemplos de uso</h3>
			    <ul class="tabs" data-tab>
				  <li class="tab-title active"><a href="#panel1">JS</a></li>
				  <li class="tab-title"><a href="#panel2">HTML</a></li>
				  <li class="tab-title"><a href="#panel3">CSS</a></li>
				</ul>
				<div class="tabs-content">
				  <div class="content active" id="panel1">
				    <textarea name="uso_js" placeholder="Exemplo de uso JS" style="height:150px;">' . $arr['uso_js'] . '</textarea>
				  </div>
				  <div class="content" id="panel2">
				    <textarea name="uso_html" placeholder="Exemplo de uso HTML" style="height:150px;">' . $arr['uso_html'] . '</textarea>
				  </div>
				  <div class="content" id="panel3">
				    <textarea name="uso_css" placeholder="Exemplo de uso CSS" style="height:150px;">' . $arr['uso_css'] . '</textarea>
				  </div>
				</div>
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

function setDefaultPages($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	//lendo info do plugin
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));
	
	//adicionando plugin à todas as páginas
	mysql_query("UPDATE config_pages SET javascripts=CONCAT('" . $arr['chave'] . ",',javascripts) WHERE javascripts NOT LIKE '%" . $arr['chave'] . "%'") or die($admin->alertMysql(mysql_error()));
	
	//redirecionando página
	header('Location: index.php?on=' . $on);
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

	case "setDefaultPages";
	setDefaultPages($id);
	break;	
}