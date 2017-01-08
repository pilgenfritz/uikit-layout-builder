<?php

//die('em manutenção');


//force remove dir
/*$dirPath='/home/agenciar/public_html/dev/ztestefinal';
system('/bin/rm -rf ' . escapeshellarg($dirPath));
exec('rm -rf '.escapeshellarg($dir));
die();*/

if(!isset($_POST['criar']))
{
	echo '
	<!doctype html>
	<!--[if IE 9]><html class="lt-ie10" lang="en" > <![endif]-->
	<html class="no-js" lang="en" data-useragent="Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)">
	<head>
	<meta charset="utf-8"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Criar novo Projeto</title>
	<meta name="description" content="Criar novo projeto."/>
	<meta name="author" content="Agência Ready"/>
	<meta name="copyright" content="Agência Ready (c) 2016"/>
	<link rel="stylesheet" href="cms/css/foundation/foundation.css"/>
	<script src="js/modernizr.js"></script>
	</head>
	<body>
		<div class="row">
			<div class="columns large-12 twelve text-center">
				<h1 style="margin:100px 0 30px 0;">Criar novo projeto</h1>
			</div>
			<form action="_criar.php" class="custom" method="POST">
				<input name="criar" type="hidden" value="Y" />
				<fieldset>
					<div class="row">
						<div class="columns large-12 twelve">
							<label for="empresa">
								Nome da Empresa
								<input type="text" id="empresa" name="empresa" required placeholder="Nome da Empresa">
							</label>
						</div>
					</div>
					<div class="row">
						<div class="columns large-12 twelve">
							<label for="dominio">
								Domínio
								<input type="text" id="dominio" name="dominio" required placeholder="Dominio">
							</label>
						</div>
					</div>
					<div class="row">
						<div class="columns large-12 twelve">
							<label for="db_name">
								Selecione uma tabela
								<select id="db_name" name="db_name" required>';
								for ($i=1; $i <= 35; $i++)
								{
									$tabela = 'agenciaready' . str_pad($i, 2, "0", STR_PAD_LEFT);
									$link = mysql_connect('mysql12-farm60.kinghost.net', $tabela, 'Qweiop12');
									if (!$link) {
									    die('Não foi possível conectar: ' . mysql_error());
									}

									list($num_tabelas) = mysql_fetch_row(mysql_query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $tabela . "';"));
									echo "SELECT meta_title FROM " . $tabela . ".config_general WHERE id = '1';";
									list($empresa) = mysql_fetch_row(mysql_query("SELECT valor FROM " . $tabela . ".config_general WHERE id='1'"));

									if($i == 1) $empresa_display = '(_base)';
									elseif(!empty($empresa)) $empresa_display = '(' . utf8_encode($empresa) . ')';
									else $empresa_display = '';

									echo '
									<option value="' . $tabela . '"'; if($num_tabelas > 0) echo ' disabled'; echo '>' . $tabela  . ' ' . $empresa_display . '</option>';

									mysql_close($link);
								}
								echo '
								</select>
							</label>
						</div>
					</div>

					<div class="row">
						<div class="columns large-12 twelve text-center">
							<button>Criar projeto</button>
						</div>
					</div>
				</fieldset>
			</form>
		</div>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="cms/js/foundation.min.js"></script>
	<script>
	    $(document).foundation();
	  </script>
	<script>
	      $(document).foundation();
	      var doc = document.documentElement;
	      doc.setAttribute(\'data-useragent\', navigator.userAgent);
	    </script>
	</body>
	</html>';
}else
{
	$destination_dir = '/home/agenciar/public_html/dev/' . $_POST['dominio'];

	if(!is_dir($destination_dir . "/"))
	{
		// connect and login to FTP server
		$ftp_conn = ftp_connect('ftp.agenciaready.com.br') or die("Could not connect to ftp_server");
		$login = ftp_login($ftp_conn, 'dev@agenciaready.com.br', 'a[HO-Flqrt3d');

		//ftp put files
		ftp_mkdir($ftp_conn, '/' . $_POST['dominio']);
		ftp_putAll($ftp_conn, '/home/agenciar/public_html/dev/_base', '/' . $_POST['dominio']);

		//change perms
		ftp_chmod($ftp_conn, 0777, '/' . $_POST['dominio']);
		ftp_chmodAllDir($ftp_conn, '/home/agenciar/public_html/dev', '/' . $_POST['dominio'] . '/img', 0777);
		ftp_chmodAllDir($ftp_conn, '/home/agenciar/public_html/dev', '/' . $_POST['dominio'] . '/files', 0777);
		ftp_chmod($ftp_conn, 0777, '/' . $_POST['dominio'] . '/db.php');
		ftp_chmod($ftp_conn, 0777, '/' . $_POST['dominio'] . '/_htaccess_modelo.txt');
		ftp_delete($ftp_conn, '/' . $_POST['dominio'] . '/_criar.php');
		ftp_delete($ftp_conn, '/base.sql');
		
		ftp_close($ftp_conn);

		//export to file dump mysql
		$return_var = NULL;
		$output = NULL;
		$command = "/usr/bin/mysqldump -u agenciaready01 -h mysql12-farm60.kinghost.net -pQweiop12 agenciaready01 > /home/agenciar/public_html/dev/base.sql";
		exec($command, $output, $return_var);

		//import dump mysql file
		exec("mysql -u " . $_POST['db_name'] . " -h mysql12-farm60.kinghost.net -pQweiop12 " . $_POST['db_name'] . " < /home/agenciar/public_html/dev/base.sql");

	    //editing mysql
	    $link2 = mysql_connect('mysql12-farm60.kinghost.net', $_POST['db_name'], 'Qweiop12');
	    mysql_select_db($_POST['db_name'], $link2) or die('Could not select database.');
		mysql_query("UPDATE config_general SET valor='" . $_POST['empresa'] . "' WHERE id='1' LIMIT 1",$link2);
		mysql_query("UPDATE config_pages SET meta_title='" . $_POST['empresa'] . "' WHERE meta_title='Nome da empresa'",$link2);
		mysql_query("UPDATE config_general SET valor='http://dev.agenciaready.com.br/" . $_POST['dominio'] . "/' WHERE id='7' LIMIT 1",$link2);
		mysql_query("UPDATE config_general SET valor='/home/agenciar/public_html/dev/" . $_POST['dominio'] . "/' WHERE id='8' LIMIT 1",$link2);
	    mysql_query("CREATE TABLE IF NOT EXISTS `@" . create_slug($_POST['empresa']) . "` (`id` int(1) NOT NULL) ENGINE=MyISAM DEFAULT CHARSET=latin1;",$link2);

		//editing db.php
		$file_path = $destination_dir . "/db.php";
		$content = file_get_contents($file_path);
		$data_to_write = str_replace("agenciaready01", $_POST['db_name'], $content);
		file_put_contents($file_path, $data_to_write);

		//editing _htaccess_modelo.txt
		$file_path = $destination_dir . "/_htaccess_modelo.txt";
		$content = file_get_contents($file_path);
		$data_to_write = str_replace("/_base/", '/' . $_POST['dominio'] . '/', $content);
		file_put_contents($file_path, $data_to_write);

		//renaming htaccess_modelo.txt
		rename($file_path,$destination_dir . "/.htaccess");

	    //redirecionando para o projeto
		header('Location: http://dev.agenciaready.com.br/' . $_POST['dominio']);
	}else
	{
		echo '<strong>Erro!</strong> Este diretório já existe.';
	}
}

function ftp_putAll($conn_id, $src_dir, $dst_dir)
{
    $d = dir($src_dir);
    while($file = $d->read()) { // do this for each file in the directory
        if ($file != "." && $file != "..") { // to prevent an infinite loop
            if (is_dir($src_dir."/".$file)) { // do the following if it is a directory
                if (!@ftp_chdir($conn_id, $dst_dir."/".$file)) {
                    ftp_mkdir($conn_id, $dst_dir."/".$file); // create directories that do not yet exist
                }
                ftp_putAll($conn_id, $src_dir."/".$file, $dst_dir."/".$file); // recursive part
            } else {
                $upload = ftp_put($conn_id, $dst_dir."/".$file, $src_dir."/".$file, FTP_BINARY); // put the files
            }
        }
    }
    $d->close();
}

function ftp_chmodAllDir($ftp_conn, $server_dir, $ftp_dir, $perm)
{
	ftp_chmod($ftp_conn, $perm, $ftp_dir);

	$src_dir = $server_dir . $ftp_dir;
    $d = dir($src_dir);
    while($file = $d->read()) // do this for each file in the directory
    {
        if ($file != "." && $file != "..") // to prevent an infinite loop
        {
            if (is_dir($src_dir."/".$file)) // do the following if it is a directory
            {
                ftp_chmod($ftp_conn, $perm, $ftp_dir . '/' . $file);
            }
        }
    }
    $d->close();
}

function create_slug($title)
{
    // Trim, utf8_decode
    $title = trim(($title));
    // Remove stress
    $title = strtr(utf8_decode($title), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'),
                                       'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
    // Switch to lowercase
    $title = strtolower($title);
    // Remove spaces
    $title = preg_replace('`\s`', '-', $title);
    // Remove other characters
    // challet : I modified the replacement to follow former rules used to generate slugs in SFR JT
    // it was [^0-9a-z-_] , replacing by ''
    $title = preg_replace('`[^0-9a-z-]`', '-', $title);
    // Remove double
    $title = preg_replace('`(-)+`', '-', $title);
    
    return $title;
}