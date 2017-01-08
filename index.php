<?php
//php settings
error_reporting(E_ERROR | E_PARSE);
session_start();

//load dependencies
require_once('class.parser.php');
require_once('class/class.setup.php');
require_once('class/class.dados.php');
require_once('class/class.layout.php');
//require_once('languages.php');
require_once('db.php');

//languages
/*if(!in_array($_GET['lang'],$idiomasList))
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /_base/pt-br/" . $_GET['lang']);
	die();
}*/

DEFINE('LANGLINK','/' . $_GET['lang'] . '/');
if($_GET['lang'] == 'pt-br' || !isset($_GET['lang'])) DEFINE('LANG',''); else DEFINE('LANG','_' . $_GET['lang']);

//parser
if(!(isset($_GET['p']) && !empty($_GET['p']))) $_GET['p'] = 'index';

$parser = new Parser("pages/" . $_GET['p'] . ".html");