<?php
if (eregi("arquivo.php",$PHP_SELF))
{
    Header("Location: /404.php");
    die();
}

class Arquivo {

	function gerar_nome($folder,$nome,$var_name)
	{
		global $dados;

		$ext = '.' . $this->ImgExt($nome);
		$tmp = $dados->create_slug(str_replace($ext,'',$nome)) . '-' . $var_name;
		$final = $tmp . $ext;
		if(file_exists($folder . $final))
		{
			for ($i=1; $i < 99999; $i++)
			{
				$final = $tmp . '-' . $i . $ext;
				if(!file_exists($folder . $final)) break;
			}
		}
		return $final;
	}

	function FolderPerms($folder,$tipo)
	{
		global $config, $admin;

		if(!file_exists($folder))
		{
			if(!is_writable($config['site-raiz'] . $tipo))
			{
				echo $admin->alertMysql('O diretório "' . $config['site-raiz'] . $tipo . '" deve ter permissão 0777.');
			}
			$oldumask = umask(0);
			if(!mkdir($folder,0777,true))
			{
				echo $admin->alertMysql('Erro ao tentar criar o diretório: ' . $folder);
			}
			umask($oldumask); 
		}elseif(!is_writable($folder))
		{
			if(!chmod($folder, 0777))
			{
				echo $admin->alertMysql('O diretório ' . $folder . ' já existe. Deve ser dada permissões 0777 via FTP.');
			}
		}
	}

	function Imagem($pasta,$nome,$file,$var_name,$width)
	{
		//die($pasta . '<br /> ' . $nome . '<br /> ' . $file . '<br /> ' . $var_name . '<br /> ' . $width);
		global $admin, $config, $on, $dados;
		$folder = $config['site-raiz'] . 'img/' . $on . '/'; //folder de gravação
		$this->FolderPerms($folder,'img'); //permissões dos dirs
		//nome final do arquivo
		if(!empty($var_name)) $var_name .= '-';
		if($width=='0') $var_name .= 'original'; else $var_name .= $width . 'px';
		$filename = $this->gerar_nome($folder,$nome,$var_name);
		$nfile = $folder . $filename; //caminho final do file
		/*echo getcwd() . '<br />';
		die($file . '<br />' . $nfile);*/
		if(!copy($file,$nfile)) //copiando
		{
		    echo $admin->alertMysql('Problemas ao copiar o arquivo para: ' . $nfile);
		}
		elseif($width > 0) //redimensionando
		{
			$size = getimagesize( $nfile );
			$height = ( int )(( $width/$size[0] )*$size[1] );
			$thumbnail = ImageCreateTrueColor( $width, $height );
			$src_img = $this->imageCreateFromAny($nfile);
			/*echo '1';*/
			//$src_img = ImageCreateFromJPEG( $nfile );
			ImageCopyResampled( $thumbnail, $src_img, 0, 0, 0, 0, $width, $height, $size[0], $size[1] );
			copy( $thumbnail, $nfile ); //ImageJPEG
			ImageDestroy( $thumbnail );
			//otimiza tamanho da imagem
			/*if(!empty($config['tinyPngKey']) && function_exists('curl_version'))
			{
				$this->tinyPNGCurl($nfile);
			}*/
		}
		return $filename;
	}

	function imageCreateFromAny($filepath)
	{ 
	    //$type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize() 
	    $type = pathinfo($filepath, PATHINFO_EXTENSION);
	    $allowedTypes = array( 
	        1,  // [] gif 
	        2,  // [] jpg 
	        3,  // [] png 
	        6   // [] bmp 
	    ); 
	    if (!in_array($type, $allowedTypes)) { 
	        return false; 
	    } 
	    switch ($type) { 
	        case 1 : 
	            $im = imageCreateFromGif($filepath); 
	        break; 
	        case 2 : 
	        	/*echo '2 / ' . $type;
	        	if(!function_exists('imageCreateFromJpeg')) echo 'não existe'; else echo 'existe';
	        	echo '<br />' . $filepath;*/
	            //print_r(error_get_last());
	            $im = imageCreateFromJpeg($filepath); 
	            //echo '3 / ' . $type;
	        break; 
	        case 3 : 
	            $im = imageCreateFromPng($filepath); 
	        break; 
	        case 6 : 
	            $im = imageCreateFromBmp($filepath); 
	        break; 
	    }    
	    return $im;  
	}

	function tinyPNGCurl($fileFullPath)
	{
		global $admin, $config, $on, $dados;

		$request = curl_init();
		curl_setopt_array($request, array(
		  CURLOPT_URL => "https://api.tinify.com/shrink",
		  CURLOPT_USERPWD => "api:" . $config['tinyPngKey'],
		  CURLOPT_POSTFIELDS => file_get_contents($fileFullPath),
		  CURLOPT_BINARYTRANSFER => true,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_HEADER => true,
		  /* Uncomment below if you have trouble validating our SSL certificate.
		     Download cacert.pem from: http://curl.haxx.se/ca/cacert.pem */
		  // CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
		  CURLOPT_SSL_VERIFYPEER => true
		));

		$response = curl_exec($request);
		if (curl_getinfo($request, CURLINFO_HTTP_CODE) === 201)
		{
		  /* Compression was successful, retrieve output from Location header. */
		  $headers = substr($response, 0, curl_getinfo($request, CURLINFO_HEADER_SIZE));
		  foreach (explode("\r\n", $headers) as $header) {
		    if (strtolower(substr($header, 0, 10)) === "location: ") {
		      $request = curl_init();
		      curl_setopt_array($request, array(
		        CURLOPT_URL => substr($header, 10),
		        CURLOPT_RETURNTRANSFER => true,
		        /* Uncomment below if you have trouble validating our SSL certificate. */
		        // CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
		        CURLOPT_SSL_VERIFYPEER => true
		      ));
		      file_put_contents($fileFullPath, curl_exec($request));
		    }
		  }
		}
		else
		{
		  print(curl_error($request));
		  print("Compression failed");
		}
	}

	function retorno_pixlr_imagem()
	{
		global $admin, $config, $on, $dados;

		//print_r($_GET);

		if(!empty($_GET['image']))
		{
			//folder de gravação
			$folder = $config['site-raiz'] . 'img/pixlr/';
			$ext = '.' . $this->ImgExt($_GET['image']);
			$nfile = $folder . (rand(349,09480984)*date('s')) . $ext;

			//vindos do GET
			$campos = explode(',',$_GET['campos']);
			$tamanhos = explode(',',$_GET['tamanhos']);

			//permissões do diretório
			$this->FolderPerms($folder,'img');

			$remoteContents = $this->my_file_get_contents($_GET['image']);

			//echo $remoteContents;

			//copiando para o dir temp
			//if(!copy($remoteContents,$nfile))
			if(!file_put_contents($nfile, $remoteContents))
			{
			    echo $admin->alertMysql('Problemas ao copiar o arquivo para: ' . $nfile);
			}

			$c=0;
			foreach ($campos as $campo)
			{
				list($nome_atual) = mysql_fetch_row(mysql_query("SELECT " . $campo . " FROM " . $_GET['db_table'] . " WHERE id='" . $_GET['id'] . "'"));
				/*echo "SELECT " . $campo . " FROM " . $_GET['db_table'] . " WHERE id='" . $_GET['id'] . "'"; die();*/
				//echo($on.'/'.$nome_atual.'/'.$nfile.'/'.'/'.$tamanhos[$c]);
				$img = $this->Imagem($on,$nome_atual,$nfile,'',$tamanhos[$c]);
				mysql_query("UPDATE " . $_GET['db_table'] . " SET " . $campo . "='" . $img . "' WHERE id='" . $_GET['id'] . "' LIMIT 1");
				$c++;
			}
				//die();
			unlink($nfile);
		}

		echo '<!doctype html>
				<html lang="en">
				<head>
					<meta charset="UTF-8">
					<title>Document</title>
				</head>
				<body>
					<script src="../js/vendor/jquery.js"></script>

					<!--fancybox_v2-->
					<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/jquery.mousewheel-3.0.6.pack.js"></script>
					<link rel="stylesheet" href="../js/plugins/jquery.fancybox-v2.1.4/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />
					<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/jquery.fancybox.pack.js?v=2.1.4"></script>
					<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/fancybox-init.js"></script>
					<script>
						parent.$.fancybox.close();
					</script>
				</body>
				</html>';
	}

	function my_file_get_contents( $site_url )
	{
		$ch = curl_init();
		$timeout = 10;
		curl_setopt ($ch, CURLOPT_URL, $site_url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$file_contents = curl_exec($ch);
		curl_close($ch);
		return $file_contents;
	}

	function Upload($pasta,$nome,$file)
	{
		global $admin, $config, $on, $dados;

		//folder de gravação
		$folder = $config['site-raiz'] . 'files/' . $on . '/';

		//permissões dos dirs
		$this->FolderPerms($folder,'files');

		//nome final do arquivo
		$ext = '.' . $this->ImgExt($nome);
		$filename = $dados->create_slug(str_replace($ext,'',$nome)) . $ext;

		//path / final file
		$nfile = $folder . $filename;

		//copiando
		if(!copy($file,$nfile))
		{
		    echo $admin->alertMysql('Problemas ao copiar o arquivo para: ' . $nfile);
		}
		
		return $filename;
	}

	function Download($arquivo,$desc)
	{
		global $config;
		
		$arquivo = $config['site-raiz'] . $arquivo;
		$ext = $this->ImgExt($arquivo);

		header("Content-type:application/$ext");
		header("Content-Length: ".filesize($arquivo));
		header('Content-Disposition: attachment; filename=' . $desc.'.'.$ext);
		//header("Content-Description: $desc");
		readfile($arquivo);
	}

	function ImgExt($nome)
	{
		$nome = strtolower(basename($nome));
		$ext = array_pop(explode(".", $nome));
		return $ext;
	}

	function apagar_arquivo($id)
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
			  	<input type="hidden" name="in" value="apagar_arquivo" />
			  	<input type="hidden" name="return_in" value="' . $_GET['return_in'] . '" />
			  	<input type="hidden" name="campo" value="' . $_GET['campo'] . '" />
			  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
			  	<input type="hidden" name="conf" value="aham" />
			    <fieldset>
				  <legend>Tem certeza que deseja apagar este arquivo?</legend>
				  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>
				  <div class="row">
				    <div class="large-12 columns text-center">
		    			<img src="../img/cms/filetype-icons/' . strtolower($admin->getExt($arr[$_GET['campo']])). '-icon.png" style="width:40px; margin-right:5px;" />' . $arr[$_GET['campo']] . '
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

			$admin->saveLog('apagou',"Arquivo: " . $_POST['campo'] . " / ID: " . $_POST['id']);
			mysql_query("UPDATE " . $db_table . " SET " . $_POST['campo'] . "='' WHERE id='" . $_POST['id'] . "'");
			unlink($config['site-raiz'] . 'files/' . $on . '/' . $arr[$_POST['campo']]);
			
			//redirecionando página
			header('Location: index.php?on=' . $on . '&in=' . $_POST['return_in'] . '&id=' . $_POST['id']);
		}	
	}	

}