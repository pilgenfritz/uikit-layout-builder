<?php
class Dados
{
	public function gerar_link_seo($input,$substitui = '-',$remover_palavras = false,$array_palavras = array())
	{
		$input1 = $input; //$this->remover_acentuacao($input);
		//Colocar em minúsculas, remover a pontuação
		$resultado = trim(ereg_replace(' +',' ',preg_replace('/[^a-zA-Z0-9\s]/','',strtolower($input1))));
	 
		//Remover as palavras que não ajudam no SEO
		//Coloco as palavras por defeito no remover_palavras(), assim eu não esse array
		if($remover_palavras) { $resultado = $this->remover_palavras($resultado,$substitui,$array_palavras); }
	 
		//Converte os espaços para o que o utilizador quiser
		//Normalmente um hífen ou um underscore
		return str_replace(' ',$substitui,$resultado);
	}

	public function hex2rgb($hex) {
	   $hex = str_replace("#", "", $hex);

	   if(strlen($hex) == 3) {
	      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	   } else {
	      $r = hexdec(substr($hex,0,2));
	      $g = hexdec(substr($hex,2,2));
	      $b = hexdec(substr($hex,4,2));
	   }
	   $rgb = $r . ',' . $g . ',' . $b;
	   return $rgb; // returns an array with the rgb values
	}
	
	public function formadata($t)
	{
		if(ereg('/',$t))
		{
			ereg ("([0-9]{2})/([0-9]{2})/([0-9]{4})", $t, $data);
			$data = "$data[3]-$data[2]-$data[1]";
		}else
		{
			ereg ("([0-9]{4})-([0-9]{2})-([0-9]{2})", $t, $data);
			$data = "$data[3]/$data[2]/$data[1]";
		}
	    return $data;
	}
	
	public function create_slug($title)
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
	
	public function create_slug_($title)
	{
		return str_replace('-','_',$this->create_slug($title));
	}
	
	public function remover_palavras($input,$substitui,$array_palavras = array(),$palavras_unicas = true)
	{
		//Separar todas as palavras baseadas em espaços
		$array_entrada = explode(' ',$input);
	 
		//Criar o array de saída
		$resultado = array();
	 
		//Faz-se um loop às palavras, remove-se as palavras indesejadas e mantém-se as que interessam
		foreach($array_entrada as $palavra)
		{
			if(!in_array($palavra,$array_palavras) && ($palavras_unica ? !in_array($palavra,$resultado) : true))
			{
				$resultado[] = $palavra;
			}
		}
	 
		return implode($substitui,$resultado);
	}

	/*public function remover_acentuacao($str)
	{
		$str = utf8_encode($str);
		$remover = array("à" => "a","á" => "a","ã" => "a","â" => "a","é" => "e","ê" => "e","ì" => "i","í" => "i","ó" => "o","õ" => "o","ô" => "o","ú" => "u","ü" => "u","ç" => "c","À" => "A","Á" => "A","Ã" => "A","Â" => "A","É" => "E","Ê" => "E","Í" => "I","Ó" => "O","Õ" => "O","Ô" => "O","Ù" => "U","Ú" => "U","Ü" => "U");
		return strtr($str, $remover);
	}*/
	public function nl2p($texto)
	{
		$var = str_replace("\n", "</p>\n<p>", '<p>'.$texto.'</p>');
		return $var;
	}
	
	public function extrair_youtube_code($iframe)
	{
		//<iframe width="970" height="546" src="//www.youtube.com/embed/MBMmqnOvPEo" frameborder="0" allowfullscreen=""></iframe>
		$var = explode('"',$iframe);
		foreach ($var as $key => $value) {
			if(ereg('youtube.com',$value))
			{
				$value = str_replace('https://','',$value);
				$value = str_replace('http://','',$value);
				$value = str_replace('www.youtube.com/embed/','',$value);
				$value = str_replace('//www.youtube.com/embed/','',$value);
				$value = explode('?',$value);
				$final = $value[0];

				//$value = str_replace('//www.youtube.com/embed/','',$value);
				//$final = str_replace('"','',$value);
				break;
			}
		}
		return $final;
	}

	public function getTexto($textoId,$getTitulo,$strip_tags)
	{
		list($titulo,$texto) = mysql_fetch_row(mysql_query("SELECT titulo".LANG.",texto".LANG." FROM textos WHERE id='" . $textoId . "'"));

		if($strip_tags) $texto = strip_tags($texto);
		if($getTitulo) $texto = array($titulo,$texto);
		
		return $texto;
	}

	public function getFrase($fraseId)
	{
		list($frase) = mysql_fetch_row(mysql_query("SELECT frase".LANG." FROM frases WHERE id='" . $fraseId . "'"));
		
		return $frase;
	}
	
	public function cortar_palavras($str,$nr)
	{
		$str = strip_tags($str);
		$texto = explode(' ',$str);
		$final = ''; $c = 0;
		foreach($texto as $k => $v)
		{
			if(strlen($v) > 10) $c = $c+2;
			elseif(strlen($v) > 2) $c++;
			if($c > $nr)
			{
				$final .= '...';
				break;
			}
			else
			{
				if($c == $nr) $final .= $v;
				else $final .= $v . ' ';
			}
		}
		return $final;
	}
	
	public function nome_mes($str)
	{
		$str = (int) $str;
		$meses = array('','Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
		return $meses[$str];
	}
	
	public function file_ext($nome)
	{
		$nome = strtolower(basename($nome));
		$ext = array_pop(explode(".", $nome));
		return $ext;
	}
	
	public function capitalize($str, $e = array())
	{
	        return join(' ',
	                           array_map(
	                                   create_function(
	                                           '$str',
	                                           'return (!in_array($str, '
	                                                   . var_export($e, true)
	                                                   . ')) ? ucfirst($str) : $str;'
	                                   ),
	                                   explode(' ', strtolower($str))
	                           )
	                   );
	}
	
	public function titulo_endereco($var)
	{
		if(ereg('.',$var))
		{
			$exp = explode('.',$var);
			if($exp[1] != '') $final = $exp[1];
			else $final = $exp[0];
		}
		else
		{
			$final = $var;
		}
		return $final;
	}
	
	public function maiuscula($t) {
		$t = strtoupper(trim($t));
		$minusculo = array("á","à","ã","â","ä","é","è","ê","ë","í","ì","î", "ï","ó","ò","õ","ô","ö","ú","ù","û","ü","ç");
		$maiusculo = array("Á","À","Ã","Â","Ä","É","È","Ê","Ë","Í","Ì","Î", "Ï","Ó","Ò","Õ","Ô","Ö","Ú","Ù","Û","Ü","Ç");
		
		for ( $X = 0; $X < count($minusculo); $X++ ) { $t = str_replace($minusculo[$X], $maiusculo[$X], $t); }
		
		return $t;
	}
}