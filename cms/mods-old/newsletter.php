<?php

global $admin;

//set default table
$db_table = $admin->getTabelaModulo($_GET['on']);

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	$admin->breadcrumbs();
	$admin->pageTitle();
	checkMySql();
	
	$query = "SELECT * FROM " . $db_table . " ORDER BY data DESC";
	echo '
	<div class="row">
		<div class="columns large-12">';
		if($admin->isDeveloper()) echo '<button id="zerar-banco">Zerar</button>';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<thead>
						<tr>
							<th style="width:200px;">Data</th>
							<th>Nome</th>
							<th>E-mail</th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['data'] . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['nome'] . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['email'] . '</a>
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
		      <label>Nome
		        <input type="text" name="nome" placeholder="Nome" value="' . $arr['nome'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>E-mail
		        <input type="text" name="email" placeholder="E-mail" value="' . $arr['email'] . '" required />
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

function export()
{
	global $config;
	
	require_once("../class/class.cms.newsletter.php");
	
	$nws = new Newsletter(0);
	$nws->fieldName = 'nome';
	$nws->order = "data";
	$nws->tableName = "newsletter";
	$nws->query();
	$nws->fetch();
	ob_clean();
	header("Pragma: no-cache");
	header("Content-Type: text/comma-separated-values; charset=ISO-8859-1");
	header("Content-type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=\"Lista de e-mails - " . $config['company'] . ".csv\"");
	echo $nws->out;
	die();
}

function checkMySql()
{
	global $db_table;
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` int(5) NOT NULL AUTO_INCREMENT,
					  `nome` varchar(255) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `data` datetime NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
}

function truncate()
{
	global $db_table, $on;
	mysql_query("TRUNCATE " . $db_table);
	header('Location: index.php?on=' . $on);
}

switch($in)
{
	default;
	Main();
	break;

	case "truncate";
	truncate();
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
	
	case "export";
	export();
	break;	
}