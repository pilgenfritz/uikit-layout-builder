<?php

class Blog
{

	function title($Dados, $config, $db)
	{
		if(!is_numeric($_GET['id']))
		{
			  //Tags Blog
			  $Tags = $db->FetchSingle("SELECT * FROM tags WHERE id = '5'");
			  $Tags = explode('#',strip_tags($Tags['texto']));
			  $meta_titulo = trim($Tags[0]);
			  $meta_descricao = trim($Tags[1]);
			  $meta_heywords = trim($Tags[2]);
			  $meta_imagem = 'http://' . $_SERVER['HTTP_HOST'] . '/img/logo.png';
		}
		elseif($_GET['search-tag'] == 'true')
		{
			$tags = $db->FetchSingle("SELECT * FROM auxtables_tags WHERE id = '" . $_GET['id'] . "'");
			$meta_titulo = 'Tag: ' . utf8_encode($tags['nome']) . ' | Blog ' . $config['company'];
			$meta_descricao = $config['description'];
			$meta_heywords = $config['keywords'];
			$meta_imagem = 'http://' . $_SERVER['HTTP_HOST'] . '/img/logo.jpg';
		}
		else
		{
			$blog = $db->FetchSingle("SELECT * FROM novidades_v2 WHERE id = '" . $_GET['id'] . "'");
			$meta_titulo = utf8_encode($blog['titulo']) . ' | Blog ' . $config['company'];
			$meta_descricao = substr(strip_tags(utf8_encode($blog['texto'])),0,200);
			$meta_heywords = $config['keywords'];
			$meta_imagem = 'http://' . $_SERVER['HTTP_HOST'] . '/img/novidades_v2/' . $blog['img1'];
		}
		$head = '
		  <title>' . $meta_titulo . '</title>
		  <meta name="description" content="' . $meta_descricao . '" />
		  <meta name="keywords" content="' . $meta_heywords . '" />
		  <meta property="og:title" content="' . $meta_titulo . '" />
		  <meta property="og:description" content="' . $meta_descricao . '" />
		  <meta property="og:type" content="company" /> 
		  <meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" /> 
		  <meta property="og:image" content="' . $meta_imagem . '" />    
		  <meta property="og:site_name" content="' . $config['company'] . '" />';

		return $head;
	}

	function _getAddPlus()
	{
		/*$addplus = '
	  		<!-- AddThis Button BEGIN -->
			<div class="addthis_toolbox addthis_default_style top">
				<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
				<a class="addthis_button_tweet"></a>
				<!--<a class="addthis_button_pinterest_pinit"></a>
				<a class="addthis_counter addthis_pill_style"></a>-->
			</div>		
			<!-- AddThis Button END -->';*/

	  	return $addplus;
	}


	function mostrar_post($id, $tipo, $Dados, $config, $db)
	{
		$query = "SELECT * FROM novidades_v2 WHERE id='" . $id . "'";
	  	$News = $db->FetchSingle($query);

	  	if(is_numeric($_GET['page'])) $titulo = $News['titulo'];
	  	else $titulo = $News['titulo'];

	  		$data = strtotime($News['data']);
			$mes = date('m',$data);
			$dia = date('d',$data);
			$link = '<a href="blog/' . $News['id'] . '/' . $Dados->create_slug($News['titulo']) . '">';

			$ultima_news .= '
			  <div class="large-5 five columns">
	            ' . $link . '
		            <figure>
		              <div class="div-opacity"> </div>
		              <img src="img/novidades_v2/' . $News['img1'] . '" alt="' . $News['titulo'] . '" /></a>
		              <figcaption>
			       		 <h4>' . $dia . '</h4>
			       		 <h5>' . strtoupper(substr($Dados->nome_mes($mes),0,3)) . '</h5>
			     	  </figcaption>
		            </figure>
			    </a>
	          </div>
	          <div class="large-7 seven columns">
	            ' . $link . '
	            <h3>' . utf8_decode($titulo) . '</h3>';
	            if($tipo != 'single')
	            {
	            	$ultima_news .= '
		            <p>' . $Dados->cortar_palavras(strip_tags($News['texto']),25) . '</p>
		            ' . $link . '<span class="ler-mais">Ler mais</span></a>';
	            }else
	            {
	            	$ultima_news .= $News['texto'];
	            }
	            $ultima_news .= '
	            </a>
	          </div>
	          <div style="clear:both;"></div>
	          <div id="related">
						<div class="row">
							<div class="large-12 twelve columns text-center">
								<h3>Posts Relacionados</h3>
							</div>';
						$list_posts = $this->listaRelacionados($News['id']);
						for($i=0; $i < 3; $i++) { 
							$rrPost = mysql_query("SELECT * FROM novidades where id='".$list_posts[$i]."' order by ordem");
							$totalPost = mysql_num_rows($rrPost);
							$c=0;
							while($arrPost = mysql_fetch_array($rrPost)){
								$c++;
								if($c == $totalPost){
									$end = ' end';
								}else{
									$end = '';
								}
								$ultima_news .= $this->mostrar_post($arrPost['id'], 'list', $Dados, $config, $db, $pos, $nr, $end);
							}
						}
	$ultima_news .='
					</div>
					</div> 
					';

		return $ultima_news;
	}

	function listaRelacionados($id){
		$arrPosts = array();
		$rr = mysql_query("SELECT * FROM novidades_vinculos WHERE pid='".$id."' order by tid");
		while($arr = mysql_fetch_array($rr)){
			$rrp = mysql_query("SELECT * FROM novidades_vinculos WHERE tid='".$arr['tid']."'");
			while($arrp = mysql_fetch_array($rrp)){
				if(!in_array($arrp['pid'], $arrPosts) &&  $arrp['pid'] != $id){
					$arrPosts[] = $arrp['pid'];
				}
			}	
		}
		return $arrPosts;
	}

	function ListTags($Dados, $config, $db)
	{
		//selecionando posts
		$posts = array(); $c=0;
		$query = "SELECT * FROM novidades_v2_tags WHERE cid='" . $_GET['id'] . "'";
		$q = $db->Query($query);
		while($News = mysql_fetch_array($q))
		{
			$c++;
			$qU = "SELECT * FROM novidades_v2 WHERE id='" . $News['sid'] . "'";
	  		$nU = $db->FetchSingle($qU);
			//criando array
			$posts[$c]['id'] = $News['sid'];
			$posts[$c]['data'] = $nU['data'];
		}
		//print_r(array_sort($posts, 'data', SORT_DESC)); // Sort by oldest first
		foreach ($posts as $key => $value)
		{
			$ultima_news .= $this->mostrar_post($value['id'], 'list', $Dados, $config, $db);
		}

		return $ultima_news;
	}

	function ListPosts($Dados, $config, $db, $pg)
	{
		if(is_numeric($pg)) $paginacao = true;

		if(empty($pg)) $pg = 1;
		$max = 4;
		$c = ($pg - 1) * $max;
		$nr = mysql_num_rows(mysql_query("SELECT id FROM blog"));
		$np = ceil($nr/$max);	

		$ultima_news = '';
	    $query = "SELECT * FROM novidades_v2 ORDER BY data DESC,id DESC LIMIT $c, $max";
		$q = $db->Query($query);
		while($News = mysql_fetch_array($q))
		{
		  $ultima_news .= $this->mostrar_post($News['id'], 'list', $Dados, $config, $db);
		}

		if($paginacao) $ultima_news = $this->write_html($ultima_news);

		return $ultima_news;
	}

	function ShowTags($Dados, $config, $db)
	{
		//array nomes
		$assuntos = ''; $c=0;
		$q = $db->Query("SELECT * FROM auxtables_tags ORDER BY nome");
		while($arr = mysql_fetch_array($q))
		{
			//echo mysql_num_rows(mysql_query("SELECT id FROM blog_tags WHERE cid='" . $arr['id'] . "'"));
			if(mysql_num_rows(mysql_query("SELECT id FROM blog_tags WHERE cid='" . $arr['id'] . "'")) > 0)
			{
				$c++;
				$link = '<a href="blog/tag/' . $arr['id'] . '/' . $Dados->gerar_link_seo($arr['nome']) . '">';
				$assuntos .= '
			            <div class="old-register">
			              <p class="title">' . $link  . '<span>#' . $c . '</span>' . $arr['nome'] .'</a></p>
			            </div>
						';	
			}
		}

		return $assuntos;
	}

	function write_html($content)
	{
		$html = '
<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
</head>
<body>
	<div id="content">
	' . $content . '
	<a id="next" href="index3.html"></a>
	</div>
</body>
</html>';
		return $html;
	}
}