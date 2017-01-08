<?php

//header
date_default_timezone_set('America/Sao_Paulo');
header("Content-Type: text/html;charset=UTF-8",true);
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
//change settings
ini_set("display_errors", 0);
error_reporting(E_WARNING);
session_start();
extract($_REQUEST,EXTR_SKIP);

//direct access verify
if (ereg("\.\.",$on) || eregi(".php",$PHP_SELF) && !eregi("index.php",$PHP_SELF))
{
	header("Location: /404.php");
	die();
}

//class includes
include_once("../db.php");
include_once("class/class.setup.php");
include_once("class/class.arquivo.php");
include_once("../class/class.dados.php");

//calling classes
$admin = new Admin;
$dados = new Dados;
$arquivo = new Arquivo;

//defining config var
$config = $admin->GeneralConfigVar();
$admin_mods = $admin->GeneralConfigMods();
$dontPost = array('on','in','aid','id','MAX_FILE_SIZE');

//defining language
$admin->setIdioma();

//if get logout, call logout on class
if($_GET['on'] == 'logout')
{
	$admin->Logout();
	die();
}
//if recover pass
if($_GET['on'] == 'recuperar-senha' || $_POST['on'] == 'recuperar-senha')
{
	$admin->RecuperarSenha();
	die();
}

$arr = mysql_fetch_array(mysql_query("SELECT * FROM admins_mods WHERE modulo='" . $_GET['on'] . "' LIMIT 1"));
if(!empty($arr['copia_de'])) $include_on = $arr['copia_de'];
elseif(!empty($_GET['on'])) $include_on = $_GET['on'];
else $include_on = $on;

if(empty($_GET['in']) && empty($_POST['in'])) $in = 'listar';

if($admin->admval())
{
	//if wants to download
	if($_GET['on'] == 'download'){ $arquivo->Download($_GET['nome'],$_GET['desc']); die(); }
	
	//if returns from pixlr image editor
	if($_GET['in'] == 'retorno_pixlr_imagem'){ $arquivo->retorno_pixlr_imagem(); die(); }

	//if set star
	if($_GET['on'] == 'set-star'){ mysql_query("UPDATE " . $_GET['table'] . " SET star='" . $_GET['status'] . "' WHERE id='" . $_GET['id'] . "' LIMIT 1") or die($admin->alertMysql(mysql_error())); die(); }

	//if set active
	if($_GET['on'] == 'set-active'){ mysql_query("UPDATE " . $_GET['table'] . " SET active='" . $_GET['status'] . "' WHERE id='" . $_GET['id'] . "' LIMIT 1") or die($admin->alertMysql(mysql_error())); die(); }

	if(isset($admin_mods[$on]) && ($admin->direito($admin_mods[$on])))
	{
		//echo '#1';
		if($admin->showHeader()) $admin->Cabecalho($in);
		include("mods/".$include_on.".php");
		if($admin->showHeader()) $admin->Rodape($in);
	}
	else
	{
		//echo '#2';
		foreach($admin_mods as $mod => $vals)
		{
			if($admin->direito($admin_mods[$mod]))
			{
				$on = $mod;
				$include_on = $mod;
				break;
			}
		}
		if($admin->showHeader()) $admin->Cabecalho($in);
		include("mods/".$include_on.".php");
		if($admin->showHeader()) $admin->Rodape($in);
	}
}
else
{
	if( (!isset($_POST['anome'])) || (!isset($_POST['senha'])) || (!$admin->Logar($_POST['anome'],$_POST['senha'])) )
	{
		//echo '#3';
		$_SESSION['url_redirect'] = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER ['REQUEST_URI'];
		if(!$admin->Logar($_POST['anome'],$_POST['senha']) && isset($_POST['anome']) && isset($_POST['senha']))
		{
			header('Location: index.php?error=senha');
			die();
		}
		else
		{
			$on = 'Login';
			$admin->Login();
			die();
		}
	}
	else
	{
		foreach($admin_mods as $mod => $vals)
		{
			if($admin->direito($admin_mods[$mod]))
			{
				$on = $mod;
				$include_on = $mod;
				break;
			}
		}
		//echo '#' . $on;
		if($admin->showHeader()) $admin->Cabecalho($in);
		include("mods/".$include_on.".php");
		if($admin->showHeader()) $admin->Rodape($in);
		/*if(!empty($_SESSION['url_redirect']))
		{
			//echo '#4.1';
			$url_redirect = $_SESSION['url_redirect']; unset($_SESSION['url_redirect']);
			header('Location: ' . $url_redirect);
			die();
		}
		else
		{
			//echo '#4.2';		
				
		}*/
	}
}