<?php

require_once('class/class.MySQLDb.php');

//conection data

/*$db_host =		'mysql12-farm60.kinghost.net';
$db_username =	'agenciaready35';
$db_name =		'agenciaready35';
$db_pass =		'Qweiop12';*/

$db_host =		'localhost';
$db_username =	'root';
$db_name =		'cerva';
$db_pass =		'Didier24';

//db connect
try
{
	$db = new MySQLDb($db_host, $db_username, $db_pass, $db_name);
}
catch(Exception $e)
{
	echo $e->getMessage();
}