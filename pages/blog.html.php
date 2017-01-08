<?php
if(!empty($_GET['page']))
{
	require_once('../class/class.config.php');
	require_once('../class/class.MySQLDb.php');
	require_once('../class/class.dados.php');
	require_once('../db.php');
}
$Dados = New Dados;
global $db;

$Setup = New Setup;
$config = $Setup->GeneralConfigVar();

require_once($config['site-raiz'].'class/class.blog.php');
$Blog = New Blog;

//titulo_blog
list($titulo_blog) = mysql_fetch_row(mysql_query("SELECT nome FROM config_pages WHERE id='6'"));
if(empty($_GET['page']))
{
	Parser::__alloc("titulo_blog",$titulo_blog);
}

//Título e Meta Tags
$title = $Blog->title($Dados, $config, $db);
if(empty($_GET['page']))
{
	Parser::__alloc("title",$title);
}

//Assunto
$assuntos = $Blog->ShowTags($Dados, $config, $db);
if(empty($_GET['page']))
{
	Parser::__alloc("assuntos",utf8_encode($assuntos));
}

//Listando posts da Tags
if($_GET['search-tag'] == 'true')
{
	$news = $Blog->ListTags($Dados, $config, $db);
}
//Listando posts específico
elseif(is_numeric($_GET['id']))
{
	$news = $Blog->mostrar_post($_GET['id'], 'single', $Dados, $config, $db);
}
//Listando todos os posts
else
{
	$news = $Blog->ListPosts($Dados, $config, $db, $_GET['page']);
}

if(empty($_GET['page']))
{
	Parser::__alloc("ultima_news",utf8_encode($news));
}
else
{
	echo $news;
}