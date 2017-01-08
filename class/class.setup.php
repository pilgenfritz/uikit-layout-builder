<?php
class Setup
{
	public function GeneralConfigVar()
	{
		global $db;
		$config = array();
		$rr = $db->Query("SELECT chave,valor FROM config_general");
		while($arr = mysql_fetch_array($rr))
		{
			$config[$arr['chave']] = $arr['valor'];
		}
		return $config;
	}

	public function meta_titles()
	{
		global $db;
		$config = $this->GeneralConfigVar();
		
		$pagina = $db->FetchSingle("SELECT * FROM config_pages WHERE page='" . $_GET['p'] . "'");

		$changeFrom = array('{nome-da-empresa}','{page-name}','{default-description}','{default-keywords}');
		$changeTo = array($config['company'],$pagina['nome'],$config['general_description'],$config['general_keywords']);

		foreach ($pagina as $key => $value) {
			$pagina[$key] = str_replace($changeFrom,$changeTo,$pagina[$key]);
		}
		
		$final = '<base href="' . $config['site-url'] . '" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />

	<!-- Page title -->
	<title>' . $pagina['meta_title'] . '</title>
	<meta name="description" content="' . $pagina['meta_description'] . '" />
	<meta name="keywords" content="' . $pagina['meta_keywords'] . '" />
	<meta name="author" content="Agência Ready" />

	<!-- fb og: tags  -->
	<meta property="og:title" content="' . $pagina['meta_title'] . '" />
	<meta property="og:description" content="' . $pagina['meta_description'] . '" />
	<meta property="og:type" content="company" /> 
	<meta property="og:url" content="http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" /> 
	<meta property="og:image" content="' . $config['site-url'] . 'img/logo.png" />
	<meta property="og:site_name" content="' . $config['company'] . '" />';

  return $final;
	}

	public function stylesheets()
	{
		global $db;

		$config = $this->GeneralConfigVar();
  $stylesheets .= '
  
	<!-- Fonts -->
	' . $config['google-fonts'];

	/*$stylesheets .= '
  
	<!-- Plugins -->
	 <!-- Animate.css--><link rel="stylesheet" href="css/plugins/animate.css" />';*/
  	/*$pagina = $db->FetchSingle("SELECT * FROM config_pages WHERE page='" . $_GET['p'] . "'");
	$base_js_plugins = explode(',',$pagina['javascripts']);
	foreach ($base_js_plugins as $value)
	{
		$plugin = $db->FetchSingle("SELECT * FROM config_plugins WHERE chave='" . $value . "'");
	if(!empty($plugin['css'])) $stylesheets .= '
	 <!-- ' . $plugin['nome'] . ' -->' . $plugin['css'];
	}*/

  $stylesheets .= '
	<link rel="stylesheet" href="dist/css/plugins-concat.css" />
	<!-- Stylesheets -->
	<link rel="stylesheet" href="css/uikit/uikit.min.css" />
	<link rel="stylesheet" href="css/uikit/slidenav.min.css" />
	<link rel="stylesheet" href="css/style.css" />';
  
  	if(file_exists($config['site-raiz'] . 'css/pages/' . $_GET['p'] . '.css'))
  	{
  	$stylesheets .= '
	<link rel="stylesheet" href="css/pages/' . $_GET['p'] . '.css">';
  	}

  $stylesheets .= '

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->';

		return $stylesheets;
	}

	public function google_services()
	{
		$config = $this->GeneralConfigVar();
		
		if(!empty($config['google-analytics']))
		{
		  $google_services .= '
		  <!-- Google Analytics -->
		' . $config['google-analytics'];
		}
		if(!empty($config['google-site-verification']))
		{
			$google_services .= '
  			<meta name="google-site-verification" content="' . $config['google-site-verification'] . '" />'; 
		}
		return $google_services;
	}

	public function javascripts()
	{
		global $db;
		
		$config = $this->GeneralConfigVar();

		// <script async src="dist/js/plugins-concat.js"></script>

		$site_javascripts.='
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
		<script src="dist/js/uikit-concat.js"></script>
		<script type="text/javascript" src="js/plugins/jquery.backstretch.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js"></script>
		<script type="text/javascript" src="js/plugins/scrollmagic.min.js"></script>
		<script type="text/javascript" src="js/plugins/scrollmagic-gsap.js"></script>
		
		
		<!-- Plugins -->';
		/*$pagina = $db->FetchSingle("SELECT * FROM config_pages WHERE page='" . $_GET['p'] . "'");
		$base_js_plugins = explode(',',$pagina['javascripts']);
		foreach ($base_js_plugins as $value)
		{
			$plugin = $db->FetchSingle("SELECT * FROM config_plugins WHERE chave='" . $value . "'");
			if(!empty($plugin['valor']))$site_javascripts .= '	
		 <!-- ' . $plugin['nome'] . ' -->' . $plugin['valor'];
		}*/

		$site_javascripts .= '
		<!-- Website JS -->
		<script type="text/javascript" src="js/functions.js"></script>	
		<script type="text/javascript" src="js/base.js"></script>
		<script type="text/javascript" src="js/events.js"></script>';
	  	if(file_exists($config['site-raiz'] . 'js/pages/' . $_GET['p'] . '.js'))
		{
			$site_javascripts .= '
			<script src="js/pages/' . $_GET['p'] . '.js"></script>';
		}
	  	return $site_javascripts;
	}

	public function ModuloOptionsCheck($modulo,$campo)
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;

		$query = mysql_query("SELECT * FROM admins_mods_options WHERE modulo='" . $modulo . "' && campo='" . $campo . "' && mostrar='Y' LIMIT 1");
		if(mysql_num_rows($query) == 1) return true;

		return false;
	}
}