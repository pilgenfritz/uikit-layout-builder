<?php

class Admin
{
	public $cnome;
	public $sessid;
	//public $cuid;
	//public $climite;
	public $slimite;
	//public $tb;
	public $ip;
	public $agora;

	public $aid;
	public $anome;
	public $nome;
	public $senha;
	public $super;
	
	
	public function Admin()
	{
		global $tabela, $_GET, $_COOKIE;
		//$this->tb = $tabela;
		$this->cnome = "Admin_id";
		$this->ip = $this->Get_IP();
		$this->agora = date("YmdHis");
		$this->slimite = date("YmdHis",time()+(3600*24));

		if(isset($_COOKIE[$this->cnome]))
		{
			$this->Get_Adm_Cookie();
			if($this->Validar()) $this->Renov_Sess();
		}
	
	}
	
	public function retorna($v)
	{
		return $this->$v;
	}
	
	public function Get_Adm_Cookie()
	{
		global $_COOKIE;
		$arr = explode('::',base64_decode($_COOKIE[$this->cnome]));
		$this->caid   = $arr[0];
		$this->sessid = $arr[1];
		$this->ctempo = $arr[2];
		$this->csenha = $arr[3];
	}
	
	public function Validar()
	{
		$user_data = mysql_query("SELECT * FROM admins WHERE aid='" . $this->caid . "' LIMIT 1");
		if(mysql_num_rows($user_data) == 1)
		{
			$arr_user = mysql_fetch_array($user_data);
			foreach ($arr_user as $key => $val)
			{
				if(!is_numeric($key))
				{
					$this->$key = $val;
					//echo "$key = $val<br/>";
				}
			}
			
			$mods_data = mysql_query("SELECT modulo FROM admins_mods WHERE ativo='Y'");
			while($arr_mods = mysql_fetch_array($mods_data))
			{
				if(mysql_num_rows(mysql_query("SELECT id FROM admins_permissoes WHERE aid='" . $this->caid . "' && modulo='" . $arr_mods['modulo'] . "' && permissao='Y'")) > 0)
				{
					$this->$arr_mods['modulo'] = "Y";
				}else
				{
					$this->$arr_mods['modulo'] = "N";
				}
			}
			$this->teste = ok;

			if($this->senha == $this->csenha) {
				if($this->Val_Sess())
				{
					$this->admval = 1;
					return true;
				}
			}
		}
		return false;
	}
	
	public function Val_Sess()
	{
		mysql_query("DELETE FROM admins_sessao WHERE stempo < '" . $this->agora . "'");
		$result = mysql_query("SELECT * FROM admins_sessao WHERE sessid='" . $this->sessid . "' AND aid='" . $this->caid . "' AND ip ='" . $this->ip . "'");
		if(mysql_num_rows($result) == 1) 
		return true;
		return false;
	}

	public function Renov_Sess()
	{
		mysql_query("UPDATE admins_sessao SET stempo='" . $this->slimite . "' WHERE sessid='" . $this->sessid . "' AND aid='" . $this->aid . "'");
		$this->NovoCookie();
	}
	
	public function NovaSess()
	{
		$this->sessid = $this->Nova_Sessid();
		mysql_query("DELETE FROM admins_sessao WHERE aid='" . $this->aid . "'");
		mysql_query("INSERT INTO admins_sessao values('" . $this->sessid . "','" . $this->aid . "','" . $this->slimite . "','" . $this->ip . "')");
		mysql_query("INSERT INTO admins_acessos values(NULL,'" . $this->aid . "','" . $this->ip . "','".date("Y-m-d H:i:s")."')");
		
		$this->NovoCookie();
		$this->admval = 1;
	}
	
	public function Nova_Sessid()
	{
    	srand((double)microtime()*1000000);
	    return(substr(md5(rand(0,999999)),0,32));
	}
	
	public function NovoCookie()
	{
		$s = base64_encode($this->aid."::".$this->sessid."::".$this->slimite."::".$this->senha);
		setcookie($this->cnome,$s);
	}
	
	public function Get_IP()
	{
		return getenv("REMOTE_ADDR");
	}
	
	public function admval()
	{
		if($this->admval == '1')
			return true; 
		return false;
	}
	
	public function Logout()
	{
		mysql_query("DELETE FROM admins_sessao WHERE aid='" . $this->aid . "'");
		setcookie($this->cnome,"");
		header("Location: index.php");
	}
	
	public function Logar($anome,$senha)
	{
		$result = mysql_query("SELECT aid, senha FROM admins WHERE anome='" . $anome . "'");
		if(mysql_num_rows($result) == 1)
		{
			list($aid,$senhar) = mysql_fetch_row($result);
			if(md5($senha) == $senhar)
			{
				$this->aid = $aid;
				$this->senha = $senhar;
				$this->caid = $aid;
				$this->NovaSess();
				$this->Validar();
				return true;
			}
		}
		return false;
	}
	
	public function direito($d) {
		if($this->$d == 'Y' || $this->super == 'Y')
			return true;
		return false;
	}

	public function setIdioma()
	{
		include_once('../languages.php');

		if(!isset($_SESSION['idiomasList']) || $_SESSION['idiomasList'] != $idiomasList)
		{
			$_SESSION['idiomasList'] = $idiomasList;
		}

		if(!empty($_GET['redirect_to']))
		{
			if($_GET['val'] == 'pt-br') unset($_SESSION['lang']);
			else $_SESSION['lang'] = '_' . $_GET['val'];
				
			header('Location:' . urldecode($_GET['redirect_to']));
			die();
		}
	}

	public function Cabecalho()
	{
		global $admin_mods, $config, $on, $in;

		echo '
		<!doctype html>
		<html class="no-js" lang="en">
		  <head>
		    <meta charset="utf-8" />
		    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
		    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		    <title>ReadyCMS - ' . $admin_mods[$on]['titulo'] . ' | ' . $config['company'] . '</title>
		    <link rel="stylesheet" href="css/foundation.min.css" />
		    <link rel="stylesheet" href="css/style.css" />';
		    $arr = mysql_fetch_array(mysql_query("SELECT * FROM admins_mods WHERE modulo='" . $_GET['on'] . "' LIMIT 1"));
			if(!empty($arr['copia_de']) && file_exists($config['site-raiz'] . 'cms/css/mods/' . $arr['copia_de'] . '.css'))
			{
		    	echo '
		    	<link rel="stylesheet" href="css/mods/' . $arr['copia_de'] . '.css" />';
			}
		    if(file_exists($config['site-raiz'] . 'cms/css/mods/' . $on . '.css'))
		    {
		    	echo '
		    	<link rel="stylesheet" href="css/mods/' . $on . '.css" />';
		    }
		    echo '
		    <link rel="stylesheet" href="../fonts/fontawesome/css/font-awesome.min.css">
		    <script src="js/modernizr.js"></script>
		    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
		  </head>
		  <body'; 
		  		if($on == 'Login' || $on == 'recuperar-senha')
		  		{
		  			echo ' style="background-image:url(http://www.agenciaready.com.br/500px/index.php); background-size: cover; background-position: bottom;" class="loading"';
		  		}

		  		echo '
		  		>
		  		<div class="se-pre-con"></div>';


		  	echo '<div id="container">';

		  if(!$this->admval() || $_GET['no-header'] == 'Y')
		  {
		  	echo '<div class="spacer-header"></div>';
		  }
		  else
		  {
		  	echo '
		  	<div class="off-canvas-wrap" data-offcanvas>
  				<div class="inner-wrap">
  					<nav class="tab-bar">
				      <section class="left-small">
				        <a class="left-off-canvas-toggle menu-icon" ><span></span></a>
				      </section>

				      <section class="tab-bar-section">
				        <img id="logo" src="img/logo-cms.png"  />
				      </section>


				      <section class="menu-items-top right logout">
				        <a href="index.php?on=logout">
				        	<i class="fa fa-sign-out" aria-hidden="true"></i> Sair
				        </a>
				      </section>

				      <section class="menu-items-top right">
				        <a href="index.php?on=admins">
				        	<i class="fa fa-users" aria-hidden="true"></i> Usuários
				        </a>
				      </section>

				      <section class="menu-items-top right">
				        <a href="index.php?on=admins&in=editar&id=' . $this->aid . '">
				        	<i class="fa fa-user" aria-hidden="true"></i> Meus Dados
				        </a>
				      </section>

				      <section class="menu-items-top right">
				        <a href="' . $config['site-url'] . '" target="_blank">
				        	<i class="fa fa-desktop" aria-hidden="true"></i> Acessar
				        </a>
				      </section>';

				      if(count($_SESSION['idiomasList']) > 1)
				      {

				      	echo '
						<section class="menu-items-top idioma right">
							<span>Idioma</span>';

				      	foreach ($_SESSION['idiomasList'] as $key => $value) {
				      		echo '
				      		<a href="index.php?on=setIdioma&val=' . $value . '&redirect_to=' . urlencode('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . '"'; if(ltrim($_SESSION['lang'],'_') == $value || empty($_SESSION['lang']) && $value == 'pt-br') echo ' class="active"'; echo '>' . strtoupper(str_replace('-br','',$value)) . '</a>';
				      	}
						echo '
						</section>';

				      }

				      echo '
				    </nav>

  					<!-- Off Canvas Menu -->
				    <aside class="left-off-canvas-menu">
				      <ul class="off-canvas-list">
				        <li><label>Módulos</label></li>';
				        foreach($admin_mods as $mod => $vals)
						{
							if($this->direito($mod) && $admin_mods[$mod]['tipo'] == 'user')
							{
				        		echo '
				        		<li>';
				        			if($this->isDeveloper())
				        			{
					        			echo '
					        			<span class="remove-mod" data-modulo="' . $mod . '">&times;</span>';
				        			}
				        			echo '
				        			<a href="index.php?on=' . $mod . '">
				        				<i class="fa ' . $admin_mods[$mod]['faicon'] . '" aria-hidden="true"></i>
				        				' . $admin_mods[$mod]['titulo'] . '
				        			</a>
				        		</li>';
							}
						}
				        echo '
				      </ul>';
				      	$mc=0;
				      	foreach ($admin_mods as $mod => $vals)
				      	{
				      		if($this->direito($mod) && $admin_mods[$mod]['tipo'] == 'config')
				      		{
				      			$mc++;
				      			$menu_config_write .= '
				      			<li>
				      				<a href="index.php?on=' . $mod . '">
				      					<i class="fa ' . $admin_mods[$mod]['faicon'] . '" aria-hidden="true"></i>
				      					' . $admin_mods[$mod]['titulo'] . '
				      				</a>
				      			</li>';
				      		}
				      	}
				      	if($mc > 0)
				      	{
					      	echo '
					      	<ul class="off-canvas-list">
					        	<li><label>Configuração</label></li>'
					        	. $menu_config_write .'
					        </ul>';
				      	}

				      	$mt=0;
				      	foreach ($admin_mods as $mod => $vals)
				      	{
				      		if($this->direito($mod) && $admin_mods[$mod]['tipo'] == 'dev')
				      		{
				      			$mt++;
				      			$menu_tools_write .= '
				      			<li>
				      				<a href="index.php?on=' . $mod . '">
				      					<i class="fa ' . $admin_mods[$mod]['faicon'] . '" aria-hidden="true"></i>
				      					' . $admin_mods[$mod]['titulo'] . '
				      				</a>
				      			</li>';
				      		}
				      	}
				      	if($mt > 0 && $this->isDeveloper())
				      	{
					      	echo '
					      	<ul class="off-canvas-list">
					        	<li><label>Programação</label></li>'
					        	. $menu_tools_write .'
					        </ul>';
				      	}

				        /*echo '
				        <!--<li><a href="#">Dados da Empresa</a></li>
				        <li><a href="#">Logs</a></li>
				        	Logs de alterações
				        	recuperaçao de senha
				        	sessoes ativas
				        	histórico de acessos
				        <li><a href="#">Layout</a></li>
				        	Número de linhas por tabela
				        	Cores
				        -->';
				        echo '*/
				      echo '<br /><br /><br />
				    </aside>';
		  }	
	}

	public function isDeveloper($vaid)
	{
		global $db;

		if(empty($vaid)) $vaid = $this->retorna('aid');

		list($email) = mysql_fetch_row($db->Query("SELECT anome FROM admins WHERE aid='" . $vaid . "' LIMIT 1"));
		if(ereg('agenciaready.com.br',$email)) return true;
		return false;
	}

	public function showHeader()
	{
		global $print, $on, $in, $_POST;

		$dont = array('inserir','salvar','inserir_cat','salvar_cat','inserir_img','salvar_img','export');

		if($_POST['conf'] == 'aham') return false; //para forms com mesmo no POST (apagar)

		if(!in_array($in,$dont) && $print != 'Y') return true;

		return false;
	}

	public function alertMysql($texto)
	{
		if(!$this->showHeader()) $this->Cabecalho($in);
		echo '
			<div data-alert class="alert-box alert radius" style="margin:50px 20px;">
			  <strong>Erro!</strong> ' . $texto . '
			  <a href="javascript:history.back(1);" class="close">&times;</a>
			</div>';
		if(!$this->showHeader()) $this->Rodape($in);
	}

	public function Rodape()
	{
		global $config, $on;

		if($this->admval())
		{
			echo '

			  <div class="row height100"></div>

			  <!-- close the off-canvas menu -->
			  <a class="exit-off-canvas"></a>

			  </div>
			</div>';
		}

		echo '
		</div>
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	    <script src="js/ckeditor/ckeditor.js"></script>
	    <script src="js/ckeditor/config.js"></script>
		<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>';

		/*selectize*/
		if($_GET['on'] == 'blog' || $_GET['on'] == 'novidades')
		{
			echo '
			<link rel="stylesheet" href="js/selectize.js-master/dist/css/selectize.default.css">
			<script src="js/selectize.js-master/dist/js/standalone/selectize.js"></script>
			<script src="js/selectize.js-master/examples/js/index.js"></script>';
		}

		if($_GET['on'] == 'paginas')
		{
			echo '
			<link rel="stylesheet" href="js/jquery.nestable/jquery.nestable.css">
			<script src="js/jquery.nestable/jquery.nestable.js"></script>';
		}

		/*colpick*/
		echo '
		<script src="js/colpick/colpick.js" type="text/javascript"></script>
		<link rel="stylesheet" href="js/colpick/colpick.css" type="text/css"/>';

		/*maskedinput*/
		echo '
		<script src="js/jquery.maskedinput-1.4/jquery.maskedinput-1.4.min.js" type="text/javascript"></script>';

		/*highlight*/
		echo '
		<script src="js/highlightjs/highlight.pack.js"></script>
		<link rel="stylesheet" href="js/highlightjs/styles/github.css">';

		/*fancybox_v2*/
		if($_GET['on'] == '')
		{	
			echo '
			<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/jquery.mousewheel-3.0.6.pack.js"></script>
			<link rel="stylesheet" href="../js/plugins/jquery.fancybox-v2.1.4/jquery.fancybox.css?v=2.1.4" type="text/css" media="screen" />
			<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/jquery.fancybox.pack.js?v=2.1.4"></script>
			<script type="text/javascript" src="../js/plugins/jquery.fancybox-v2.1.4/fancybox-cms.js"></script>';
		}

		echo '
	    <script src="js/events.js"></script>';

		$arr = mysql_fetch_array(mysql_query("SELECT * FROM admins_mods WHERE modulo='" . $_GET['on'] . "' LIMIT 1"));
		if(!empty($arr['copia_de']) && file_exists($config['site-raiz'] . 'cms/css/mod-' . $arr['copia_de'] . '.css'))
		{
	    	echo '
	    	<script src="js/mods/' . $arr['copia_de'] . '.js"></script>';
		}

	    if(file_exists($config['site-raiz'] . 'cms/js/mods/' . $on . '.js'))
	    {
	    	echo '
	    	<script src="js/mods/' . $on . '.js"></script>';
	    }

	    echo '
		    <script src="js/foundation.min.js"></script>
		    <script>
		      $(document).foundation();
		    </script>
	  	  </body>
		</html>';
	}

	public function Login()
	{
		$on = 'Login';

		$msg = array(
			'senha-alterada' => 'Sua senha foi alterada com sucesso.',
			'senha' => 'Usu&aacute;rio ou Senha incorretos.'
			);

		$this->Cabecalho();
		echo '
			<div class="row">
				<div class="large-12 columns text-center">
					<img src="img/logo-cms.png" />
				</div>
			</div>
			<div class="row">
				<div class="large-6 columns large-centered text-right">
				<form id="login" method="post" action="index.php">
					<fieldset class="panel">
						<label for="anome">E-mail</label>
						<input type="email" name="anome" value="" placeholder="Informe seu e-mail" required />

						<label for="senha">Senha</label>
						<input type="password" name="senha" value="" placeholder="Informe sua senha" required />';
						if(isset($_GET['success']))
						{
							echo '
							<div data-alert class="alert-box success radius text-left">
							  ' . $msg[$_GET['success']] . '
							  <a href="#" class="close">&times;</a>
							</div>';
						}
						elseif(isset($_GET['error']))
						{
							echo '
							<div data-alert class="alert-box alert radius text-left">
							  ' . $msg[$_GET['error']] . '
							  <a href="#" class="close">&times;</a>
							</div>';
						}
						echo '
						<a href="index.php?on=recuperar-senha" class="only-icon">
							<i class="fa fa-key" aria-hidden="true"></i> <span>Recuperar senha</span>
						</a>
						<button type="submit" name="enviar">
							Entrar &nbsp;  <i class="fa fa-arrow-right" aria-hidden="true"></i>
						</button>
					</fieldset>
				</form>
				</div>
			</div>
			<div id="login-copy">
				<p>@' . date("Y") . ' Agência Ready.</p>
			</div>';
		$this->Rodape();
	}

	public function isChaveCorreta($chave)
	{
		global $db, $email, $config;

		if(isset($chave))
		{
			$num_chave = mysql_num_rows($db->Query("SELECT * FROM admins_recuperar_senha WHERE chave='" . $chave . "'"));
			if($num_chave == 0)
			{
				header('Location: index.php?on=recuperar-senha&error=chave-icorreta');
				die();
			}
			else
			{
				list($utilizada) = mysql_fetch_row($db->Query("SELECT utilizada FROM admins_recuperar_senha WHERE chave='" . $chave . "'"));
				if($utilizada != 'N')
				{
					header('Location: index.php?on=recuperar-senha&error=chave-utilizada');
					die();					
				}
			}
		}

	}

	public function RecuperarSenha()
	{
		global $db, $email, $config;

		if(isset($_POST['email']))
		{
			$num_users = mysql_num_rows($db->Query("SELECT * FROM admins WHERE anome='" . $_POST['email'] . "'"));
			if($num_users == 0)
			{
				header('Location: index.php?on=recuperar-senha&error=user');
				die();
			}
		}
		

		if(isset($_POST['senha']) && isset($_POST['senha2']) && isset($_POST['chave']))
		{
			$this->isChaveCorreta($_POST['chave']);

			if($_POST['senha'] != $_POST['senha2'])
			{
				header('Location: index.php?on=recuperar-senha&error=senhas-diferentes&chave=' . $_POST['chave']);
				die();
			}
			else
			{
				list($email_db) = mysql_fetch_row($db->Query("SELECT email FROM admins_recuperar_senha WHERE chave='" . $_POST['chave'] . "' LIMIT 1"));
				$db->Query("UPDATE admins SET senha='" . md5($_POST['senha']) . "' WHERE anome='" . $email_db . "' LIMIT 1");
				$db->Query("UPDATE admins_recuperar_senha SET utilizada='Y' WHERE chave='" . $_POST['chave'] . "' LIMIT 1");
				header('Location: index.php?success=senha-alterada');
				die();
			}
		}else
		{
			$this->isChaveCorreta($_GET['chave']);
		}

		$msg = array(
				'user' => 'Usu&aacute;rio n&atilde;o cadastrado.',
				'chave-icorreta' => 'A chave de seguran&ccedil;a est&aacute; incorreta.',
				'chave-utilizada' => 'Chave de seguran&ccedil;a j&aacute; utilizada.<br/>Solicite nova altera&ccedil;&atilde;o de senha.',
				'senhas-diferentes' => 'As senhas digitadas s&atilde;o diferentes.'
				);

		$this->Cabecalho();
			echo '
				<div class="row">
					<div class="large-6 columns large-centered text-right">
					<form id="login" method="post" action="index.php?on=recuperar-senha">
						<fieldset class="panel">
							<div class="row">
								<div class="large-12 columns text-center">
									<h1>Recuperar senha</h1>';
									if(isset($_GET['chave']))
									{
										echo '
										<p>Legal, <strong>estamos quase lá</strong>! Agora é só digitar abaixo a nova senha, repetindo no campo abaixo.</p>';
									}elseif(!isset($_POST['email']))
									{
										echo '
										<p>Informe abaixo o e-mail cadastrado para receber o link de recuperação de senha.</p>';
									}
									echo '
								</div>
							</div>';
							if(isset($_GET['chave']))
							{
								echo '
								<input type="hidden" name="chave" value="' . $_GET['chave'] . '" />
								<input type="password" name="senha" value="" placeholder="Nova senha" required />
								<input type="password" name="senha2" value="" placeholder="Repita a senha" required />';
							}
							elseif(!isset($_POST['email']))
							{
								echo '<input type="email" name="email" value="" placeholder="Seu e-mail" required />';
							}

							if(isset($_GET['error']))
							{
								echo '<p class="label alert radius left">' . $msg[$_GET['error']] . '</p>';
							}
							if(isset($_POST['email']) && $num_users > 0)
							{
								$chave_recuperar = md5(rand(00000, 99999) . '_' . date("Y-m-d H:i:s"));
								$db->Query("INSERT INTO admins_recuperar_senha values(NULL,'" . $_POST['email'] . "','" . $chave_recuperar . "','N','".date("Y-m-d H:i:s")."')");

								$mensagem = '
								<h2>Recupera&ccedil;&atilde;o de senha</h2>
								<p>Recebemos uma solicita&ccedil;&atilde;o de recupera&ccedil;&atilde;o de senha no site ' . $config['company-domain'] . '.</p>
								<p>Para gerar uma nova senha, <a href="' . $config['site-url'] . 'cms/index.php?on=recuperar-senha&chave=' . $chave_recuperar . '">clique aqui</a></p>
								<p>Se você não realizou esta solicitação, somente ignore este mensagem.</p>
								<p>[E-mail enviado pelo CMS da Agência Ready instalado no site ' . $config['company-domain'] . ']</p>';

								/*$email->from('Agência Ready','suporte@agenciaready.com.br');
								$email->to($_POST['email']);
								$email->subject('Recuperar senha');
								$email->text($mensagem);
								$email->send();*/

								echo '
								<div data-alert class="alert-box success radius text-center">
								  Enviamos um e-mail com instru&ccedil;&otilde;es para gerar uma nova senha.
								</div>';
							}

					echo '<a href="index.php?on=">
							<i class="fa fa-angle-double-left" aria-hidden="true"></i> voltar
						  </a>';
					if(!isset($_POST['email']))
					{
						echo '
						  <button type="submit" name="enviar">
						  	Enviar &nbsp; <i class="fa fa-envelope-o" aria-hidden="true"></i> 
						  </button>';
					}
					echo '
						</fieldset>
					</form>
					</div>
				</div>';
		$this->Rodape();
	}

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

	public function GeneralConfigMods()
	{
		global $db;

		$admin_mods = array();
		$rr = $db->Query("SELECT * FROM admins_mods WHERE ativo='Y' ORDER BY nome");
		while($arr = mysql_fetch_array($rr))
		{
			$admin_mods[$arr['modulo']]['titulo'] = $arr['nome'];
			foreach(explode('|',$arr['submenu']) as $submenu)
			{
				//$admin_mods[$arr['modulo']]['submenu'][] = $submenu;
				$submenu_links = explode(',',$submenu);
				$admin_mods[$arr['modulo']]['submenu'][$submenu_links[0]] = $submenu_links[1]	;
				$admin_mods[$arr['modulo']]['tipo'] = $arr['tipo'];
				$admin_mods[$arr['modulo']]['faicon'] = $arr['faicon'];
			}
			unset($submenu);
		}
		return $admin_mods;
	}

	public function pageTitle()
	{
		global $admin, $_GET, $_POST, $on, $in;
		list($modulo) = mysql_fetch_row(mysql_query("SELECT nome FROM admins_mods WHERE modulo='" . $on . "'"));

		if(isset($_GET['confirm']))
		{
			if($_GET['confirm'] == 'update') $text = 'As alterações foram salvas com sucesso.';
			elseif($_GET['confirm'] == 'insert') $text = 'O registro foi incluído com sucesso.';
			elseif($_GET['confirm'] == 'delete') $text = 'O registro foi excluido com sucesso.';

			echo '
			<div class="row">
				<div data-alert class="alert-box success" style="margin-bottom:0;">
				  <p>' . $text . '</p>
				  <a href="#" class="close">&times;</a>
				</div>
			</div>';
		}

		/*echo '
		<div class="row">
			<div class="columns large-12">
				<h2>' . $modulo . '</h2>
			</div>
		</div>';*/
	}

	public function formButton()
	{
		global $admin, $_GET, $_POST, $on, $in;
		$botoes = array(
						'editar' => 'Salvar <i class="fa fa-check" aria-hidden="true"></i>',
						'novo' => 'Criar <i class="fa fa-plus-square" aria-hidden="true"></i>',
						'apagar' => 'Confirmar exclusão <i class="fa fa-trash-o" aria-hidden="true"></i>',
						'nova_cat' => 'Criar categoria <i class="fa fa-plus-square" aria-hidden="true"></i>',
						'editar_cat' => 'Salvar categoria <i class="fa fa-check" aria-hidden="true"></i>',
						'apagar_cat' => 'Excluir categoria <i class="fa fa-trash-o" aria-hidden="true"></i>',
						'nova_img' => 'Criar imagem <i class="fa fa-plus-square" aria-hidden="true"></i>',
						'editar_img' => 'Salvar imagem <i class="fa fa-check" aria-hidden="true"></i>',
						'apagar_img' => 'Excluir imagem <i class="fa fa-trash-o" aria-hidden="true"></i>'
						);
		if(empty($botoes[$in])) $botoes[$in] = ucfirst(str_replace('_',' ',$in));

		return $botoes[$in];
	}
	
	public function inputImageWOptions($label,$name_input,$campo_titulo,$campos_original,$db_table,$arr,$tamanhos)
	{
		global $config, $on, $in;

		if(empty($arr[$campo_titulo])) $titulo = $label; else $titulo = $arr[$campo_titulo];

		$campos = explode(',', $campos_original);

		if(!empty($arr[$campos[0]]))
	    {
	    	$final = '
	    	<h5>' . $label .'</h5>
	    	<img src="../img/' . $on . '/' . $arr[$campos[0]] . '" alt="' . $arr['titulo'] . '" class="main-image" />
	    	<ul id="image-options">
	    	  <li>
	    	  	<a href="../img/' . $on . '/' . $arr[$campos[1]] . '" class="various">
	    	  		<i class="fa fa-search has-tip tip-top" aria-hidden="true" data-tooltip title="Ampliar imagem"></i>
	    	  	</a>
	    	  </li>
	    	  <li>
	    	  	<a href="javascript:void(0);">
	    	  		<i class="fa fa-pencil trocar-imagem has-tip tip-top" aria-hidden="true" data-tooltip title="Trocar imagem"></i>
	    	  	</a>
	    	  </li>
	    	  <li>
	    	  	<!--<a href="index.php?on=download&nome=img/' . $on . '/' . $arr[$campos[1]] . '&desc=' . $titulo .'">-->
	    	  	<a href="../img/' . $on . '/' . $arr[$campos[1]] . '" download="' . str_replace('.','',strip_tags($titulo)) .'">
	    	  		<i class="fa fa-cloud-download has-tip tip-top" data-tooltip title="Download da imagem" aria-hidden="true"></i>
	    	  	</a>
	    	  </li>
	    	  <li>
	    	  	<a href="http://pixlr.com/express/?'
	    	  			.'locktarget=true'
	    	  			.'&locktitle=true'
	    	  			.'&referrer=Agência Ready'
	    	  			.'&exit=' . urlencode('http://' . $_SERVER['SERVER_NAME'] . str_replace('&in='.$in,'&in=retorno_pixlr_imagem',$_SERVER['REQUEST_URI']))
	    	  			.'&target=' . urlencode('http://' . $_SERVER['SERVER_NAME'] . str_replace('&in='.$in,'&in=retorno_pixlr_imagem',$_SERVER['REQUEST_URI']) . '&campos=' . $campos_original . '&tamanhos=' . $tamanhos['img1'] .','.$tamanhos['img2'].','.$tamanhos['img3'] .'&db_table='.$db_table)
	    	  			.'&image=' . $config['site-url'] . 'img/' . $on . '/' . $arr[$campos[1]]
	    	  			.'&title=' . $titulo

	    	  			.'" class="openpixlr-cms" data-fancybox-type="iframe" >
	    	  		<i class="fa fa-magic has-tip tip-top" aria-hidden="true" data-tooltip title="Editar imagem com Pixlr"></i>
	    	  	</a>
	    	  </li>';
	    	  if($_GET['in'] != 'editar_img' && !($_GET['on'] == 'imagens' && $_GET['in'] == 'editar'))
	    	  {
	    	  	$final .= '
	    	  	  <li>
		    	  	<a href="index.php?on=' . $on . '&in=apagar_imagem&campos=' . $campos_original . '&id=' . $arr['id'] . '">
						<i class="fa fa-trash-o has-tip tip-top" aria-hidden="true" data-tooltip title="Apagar imagem"></i>
		    	  	</a>
		    	  </li>';
	    	  }
	    	  /*$file = $config['site-raiz'] . 'img/' . $on . '/' . $arr[$campos[1]];
	    	  if(is_file($file))
	    	  {
				  $im = new Imagick($file);
				  $arquivo = new Arquivo;
				  $propriedades = $im->getImageProperties();
				  $info = 'Formato: ' . $im->getImageFormat() . '<br/>'
				  		  .'Tamanho: ' . $arquivo->formatSizeUnits(filesize($file)) . '<br/>'
				  		  .'Largura: ' . $im->getImageWidth() . ' pixels<br/>'
				  		  .'Altura: ' . $im->getImageHeight() . ' pixels<br/>'
				  		  .'Criação: ' . date('d/m/Y H:i:s',filectime($file)) . '<br/>'
				  		  .'Modificação: ' . date('d/m/Y H:i:s',filemtime($file));
	    	  }
	    	  
	    	  <li>
	    	  	<a href="javascript:void(0);">
	    	  		<img src="img/icon-image-info.png" data-tooltip class="has-tip tip-top" title="' . $info . '" alt="' . $info . '" />
	    	  	</a>
	    	  </li>*/
	    	  $final .= '
	    	</ul>
	        <input type="file" name="' . $name_input . '" class="hide margintop10" />
	        <a href="javascript:void(0)" class="trocar-imagem-cancel">cancelar</a>';
	    }else
	    {
	    	$final = '
	    	<h5>' . $label .'</h5>
	        <input type="file" name="' . $name_input . '" />';
	    }

	    return $final;
	}

	public function inputFileWOptions($label,$name_input,$campo_titulo,$db_table,$arr)
	{
		global $config, $on, $in;

		if(!empty($arr[$campo_titulo])) $titulo = $arr[$campo_titulo]; else $titulo = $label;

		$final = '
	    	<h5>' . $label .'</h5>';

		if(!empty($arr[$name_input]))
	    {
	    	$final .= '
	    	<div class="file-desc">
	    		<img src="img/filetype-icons/' . strtolower($this->getExt($arr[$name_input])). '-icon.png" style="width:40px; margin-right:5px;" />' . $arr[$name_input] . '
	    	</div>
	    	<ul id="file-options">';
	    	if(strtolower($this->getExt($arr[$name_input])) == 'pdf')
	    	{
	    		$final .= '
	    		<li>
		    	  	<a href="' . $config['site-url'] . '/files/' . $on . '/' . $arr[$name_input] . '" class="open-iframe" data-fancybox-type="iframe" style="color:#333;">
		    	  		<img src="img/icon-image-open-black.png" data-tooltip class="has-tip tip-top" title="Abrir arquivo" alt="Abrir arquivo" />
		    	  	</a>
		    	  </li>';
	    	}
	    	  $final .= '
	    	  <li>
	    	  	<a href="javascript:void(0);">
	    	  		<img src="img/icon-image-edit-black.png" data-tooltip class="trocar-arquivo has-tip tip-top" title="Upload de novo arquivo" alt="Upload de novo arquivo" />
	    	  	</a>
	    	  </li>
	    	  <li>
	    	  	<a href="index.php?on=download&nome=files/' . $on . '/' . $arr[$name_input] . '&desc=' . $titulo .'">
	    	  		<img src="img/icon-image-download-black.png" data-tooltip class="has-tip tip-top" title="Download do arquivo" alt="Download do arquivo" />
	    	  	</a>
	    	  </li>
	    	  <li>
	    	  	<a href="index.php?on=' . $on . '&in=apagar_arquivo&campo=' . $name_input . '&return_in=' . $in . '&id=' . $arr['id'] . '">
					<img src="img/icon-image-delete-black.png" data-tooltip class="has-tip tip-top" title="Apagar arquivo" alt="Apagar arquivo" />
	    	  	</a>
	    	  </li>';

			  $file = $config['site-raiz'] . 'files/' . $on . '/' . $arr[$name_input];
			  $arquivo = new Arquivo;
			  $info = 'Formato: ' . strtoupper($this->getExt($file)) . '<br/>'
			  		  .'Tamanho: ' . $arquivo->formatSizeUnits(filesize($file)) . '<br/>'
			  		  .'Criação: ' . date('d/m/Y H:i:s',filectime($file)) . '<br/>'
			  		  .'Modificação: ' . date('d/m/Y H:i:s',filemtime($file));
	    	  $final .= '
	    	  <li>
	    	  	<a href="javascript:void(0);">
	    	  		<img src="img/icon-image-info-black.png" data-tooltip class="has-tip tip-top" title="' . $info . '" alt="' . $info . '" />
	    	  	</a>
	    	  </li>
	    	</ul>
	        <input type="file" name="' . $name_input . '" class="hide margintop10" />
	        <a href="javascript:void(0)" class="trocar-arquivo-cancel">cancelar</a>';
		}else
	    {
	    	$final .= '
	        <input type="file" name="' . $name_input . '" />';
	    }

	    return $final;

	}

	public function getExt($nome)
	{
		$nome = strtolower(basename($nome));
		$ext = array_pop(explode(".", $nome));
		return $ext;
	}

	public function breadcrumbs()
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;
		list($modulo) = mysql_fetch_row(mysql_query("SELECT nome FROM admins_mods WHERE modulo='" . $on . "'"));
			
		list($modulo,$faicon) = mysql_fetch_row(mysql_query("SELECT nome,faicon FROM admins_mods WHERE modulo='" . $on . "'"));

		echo '
		<div class="row top-title-options">
			<div id="submenu" class="columns large-6">
				<h1><i class="fa ' . $faicon . '" aria-hidden="true"></i> ' . $modulo . '</h1>
				<ul class="inline-list">
					<li><i class="fa fa-caret-right" aria-hidden="true"></i></li>';
					//print_r($admin_mods);
					foreach ($admin_mods[$on]['submenu'] as $key => $value)
					{
						if($key == $in) $class=' class="ativo"'; else $class='';
						
						//avaliando restrições de mostragem
						$value = explode('*',$value);
						$nome = $value[0];
						
						//if(empty($value[1]))

						if($this->ModuloOptionsCheck($on,$value[1]) || $value[1] == 'dev' && $this->isDeveloper() || empty($value[1]))
						{
							echo '
							<li ' . $class . '><a href="index.php?on=' . $on . '&in=' . $key . '">' . $value[0] . '</a></li>';
						}
					}
					echo '
				</ul>
			</div>
			<div class="columns large-6 text-right list-right-options">';

				if($_GET['in'] == 'galeria_fotos' && is_numeric($_GET['id']))
				{
				  echo '
				  <a href="index.php?on=' . $on . '&in=editar&id=' . $_GET['id'] . '">
				  	<i class="fa fa-arrow-left" aria-hidden="true"></i> Voltar ao item
				  </a>';
				}
				if($_GET['in'] != 'listar' && !empty($_GET['in']))
				{
				  echo '
				  <a href="index.php?on=' . $on . '">
				  	<i class="fa fa-list" aria-hidden="true"></i> Voltar para a lista
				  </a>';
				}
			  echo '
			</div>
		</div>';
	}

	public function PaginacaoInit($query,$max,$pg)
	{
		if(empty($pg)) $pg=1;
		$c = ($pg-1)*$max;
		$nr = mysql_num_rows(mysql_query($query));
		$np = ceil($nr/$max);

		return array(
					'c' => $c,
					'nr' => $nr,
					'np' => $np
					);

	}
	public function Paginacao($nr,$pg,$np,$adt)
	{
		global $on, $in;

		if(empty($pg)) $pg='1';
	
		echo '
		<div class="pagination-centered">
		  <ul class="pagination">';
	
		if($np > 1)
		{
			if($pg > 1)
			{
				$a = ($pg-1);
				echo '
				<li class="arrow"><a href="index.php?on=' . $on . '&in=' . $in . '&pg=' . $a.$ll.$adt . '">&laquo;</a></li>';
			}
			else
			{
				echo '
				<li class="arrow unavailable">&laquo;</li>';
			}
		
			if ($np > 1)
			{
				$min = ($pg-5);
				if ($min < 1)
				{
					$min = 1;
				}
				$max = ($pg+5);
				if ($max > $np)
				{
					$max = $np;
				}
				
				for($i=$min; $i<=$max;$i++)
				{
					if($i == $pg)
					{
						echo '
						<li class="current"><a href="">' . $i . '</a></li>';
					}
					else
					{
						echo '
						<li><a href="index.php?on=' . $on . '&in=' . $in . '&pg=' . $i.$ll.$adt . '">' . $i . '</a></li>';
					}
				}
			}
			if($nr != 0)
			{
				if($pg != $np)
				{
					$a = ($pg+1);
					echo '
					<li class="arrow"><a href="index.php?on=' . $on . '&in=' . $in . '&pg=' . $a.$ll.$adt . '">&raquo;</a></li>';
				}
				else
				{
					echo '
					<li class="arrow unavailable">&raquo;</li>';
				}
			}
		}

		echo '   
		  </ul>
		</div>';
	}

	public function saveLog($acao,$log)
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;

		//die('#'.$log.'#');

		mysql_query("INSERT INTO admins_logs values(NULL,'" . $this->aid . "','" . $this->ip . "','" . $log . "','" . str_replace(' ','-',trim($acao)) . "','" . $on . "','" . date('Y-m-d H:i:s') . "')");

		//$log = $this->nome . ' ' .  . ' um registro no módulo '
	}

	public function ModuloOptionsCheck($modulo,$campo)
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;

		$query = mysql_query("SELECT * FROM admins_mods_options WHERE modulo='" . $modulo . "' && campo='" . $campo . "' && mostrar='Y' LIMIT 1");
		if(mysql_num_rows($query) == 1) return true;

		return false;
	}

	public function getTabelaModulo($modulo)
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;
		list($tabela) = mysql_fetch_row(mysql_query("SELECT tabela FROM admins_mods WHERE modulo='" . $modulo . "' LIMIT 1"));
		if(empty($tabela)) $tabela = $on;
		
		return $tabela;
	}

	public function showSelectCat($db_table)
	{
		global $admin_mods, $admin, $_GET, $_POST, $on, $in;
		
		//vars
		$filtroName = 'filtro_' . $db_table;
		if($_GET[$filtroName] != ''){ $$filtroName = $_GET[$filtroName]; }
		elseif(isset($_GET[$filtroName]) && empty($_GET[$filtroName]) ||
			   empty($_GET[$filtroName]) && empty($_SESSION[$filtroName])){
				list($$filtroName) = mysql_fetch_row(mysql_query("SELECT id FROM " . $db_table . "_cat ORDER BY titulo LIMIT 1"));
		}
		elseif($_SESSION[$filtroName] != ''){ $$filtroName = $_SESSION[$filtroName]; }
		$_SESSION[$filtroName] = $$filtroName;

		//select
		$select = '
		<div class="row">
			<div class="columns large-8 text-right margintop10 marginbottom10">Categoria</div>
			<div class="columns large-4 filtro-settings">
				<select id="filterPages" name="filtro_' . $db_table . '" required>';
				$c=0;
		        $r2 = mysql_query("SELECT * FROM " . $db_table . "_cat ORDER by titulo");
		        while($ar2 = mysql_fetch_array($r2))
		        {
		        	$c++;
		        	$select .= '
		        	<option value="' . $ar2['id'] . '"'; if($$filtroName == $ar2['id']) $select .= ' selected'; $select .= '>' . $ar2['titulo'] . '</option>';
		        }
		        $select .= '
		        </select>
				<a href="index.php?on=' . $on . '&in=categorias">
					<i class="fa fa-pencil-square-o settings right" aria-hidden="true"></i>
				</a>
			</div>
		</div>';	
		if(!empty($$filtroName)) $query=" WHERE cid='" . $$filtroName . "'";

		return array('select' => $select, 'query' => $query);
	}

	public function galeria_fotos($id)
	{
		global $admin_mods, $config, $on, $in, $db_table;
		$this->breadcrumbs();
		$this->pageTitle();
		$this->saveLog('acessou','');
		$this->checkMySql_img();
		$_SESSION['last_gallery_id'] = $id;

		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'"));
		
		if($this->isDeveloper())
		{
			$this->showCode_galeria($id);
		}

		$query = "SELECT * FROM " . $db_table . "_imagens WHERE cid='" . $id . "' ORDER BY ordem";
		echo '
		<div class="row">
			<div class="columns large-6">
			  <h4>Galeria do item ' . $arr['titulo'] . '</h4>
			</div>
		</div>
		<div class="row">
			<div class="columns large-12">';
				if(mysql_num_rows(mysql_query($query)) > 0)
				{
					echo '
					<table id="tabela_imagens" class="list-table">
						<thead>
							<tr>
								<th width="200">
								Imagens
								<span class="right disabled">Arraste para reordenar</span>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
							  <td>
								<div class="row">';
								$rr = mysql_query($query);
								while ($arr = mysql_fetch_array($rr))
								{
									echo '
									<div id="' . $arr['id'] . '" class="ui-state-default columns crop large-4 end">
										<a href="index.php?on=' . $on . '&in=editar_img&id=' . $arr['id'] . '">
											<img src="../img/' . $on . '/' . $arr['img1'] . '" />
										</a>
									</div>';
								}
								echo '
								</div>
							  </td>
							</tr>
						</tbody>
					</table>';
				}
				else
				{
					echo '
					<div data-alert class="alert-box info radius">
					  Nenhuma imagem encontrada nesta galeria.
					  <a href="#" class="close">&times;</a>
					</div>';
				}
				echo '
			</div>
		</div>';
	}

	public function Form_img($id)
	{
		global $admin_mods, $config, $on, $in, $db_table, $tamanho;

		$this->breadcrumbs();
		$this->pageTitle();
		$this->checkMySql_img();

		if($in == 'editar_img')
		{
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_imagens WHERE id='" . $id . "' LIMIT 1"));
			$this->saveLog('visualizou',"Página: " . $arr['titulo'] . " / ID: " . $arr['id']);
			list($categoria) = mysql_fetch_row(mysql_query("SELECT titulo FROM " . $db_table . " WHERE id='" . $arr['cid'] . "'"));
			echo '
			<div class="row">
				<div class="columns large-12">
				  <h3>Editando foto da <a href="index.php?on=' . $on . '&in=galeria_fotos&id=' . $arr['cid'] . '">galeria de fotos</a> de <a href="index.php?on=' . $on . '&in=editar&id=' . $arr['cid'] . '">' . $categoria . '</a></h3>
				</div>
			</div>';
		}elseif(!empty($_SESSION['last_gallery_id']))
		{
			$arr['cid'] = $_SESSION['last_gallery_id'];
		}
		echo '
		<div class="row">
		  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
		  	<input type="hidden" name="MAX_FILE_SIZE" value="1000000000000">
		  	<input type="hidden" name="in" value="salvar_img" />
		  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
		    <fieldset>
			  <legend>Imagem</legend>';
			  
			  if($in == 'nova_img')
			  {
			  	echo '
				  <div class="row">
				    <div class="large-6 columns">
				    <input type="file" id="imagem" name="imagem[]" multiple class="has-tip tip-top" data-tooltip title="Você pode enviar várias imagens por vez" />
				    </div>
				  </div>';
			  }else
			  {
				  echo '
				  <div class="row">
				    <div class="large-6 columns image-box">'
				    . $this->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table.'_imagens',$arr,$tamanho)
				    . '<br /><br />
				    </div>
				  </div>';
			  }
			  echo '
			  <div id="descricao" class="row">
			    <div class="large-9 columns">
			      <label>Descrição / Palavras-chave
			        <input type="text" name="titulo" placeholder="Título" value="' . $arr['titulo'] . '" />
			      </label>
			    </div>
			  </div>
			  <div class="row">
		  		<div class="large-9 columns end">
		  			<label>Item
			            <select name="cid">';
			            $r2 = mysql_query("SELECT * FROM " . $db_table . " ORDER by titulo");
				        while($ar2 = mysql_fetch_array($r2))
				        {
				        	echo '
				        	<option value="' . $ar2['id'] . '"'; if($arr['cid'] == $ar2['id']) echo ' selected'; echo '>' . $ar2['titulo'] . '</option>';
				        }
				        echo '
				        </select>
				    </label>
		        </div>
			  </div>
			</fieldset>
			<div class="row ">
				<div class="large-12 columns margintop20 text-right">';
				 	if(!empty($arr['id'])) echo '<a href="index.php?on=' . $on . '&in=apagar_img&id=' . $arr['id'] . '" class="button alert marginright10 left hide-for-small">Apagar</a>';
				 	echo '
					<a href="index.php?on=' . $on . '&in=galeria_fotos&id=' . $arr['cid'] . '" class="button secondary marginright10">Cancelar</a>
			    	<button type="submit">' . $this->formButton() . '</button>
				</div>
			</div>
		  </form>
		</div>';
	}

	public function Salvar_img()
	{
		global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $_FILES, $tamanho, $tamanho_galeria;
		//editando pre-vars
		$dontPost[] = 'permissoes'; $dontPost[] = 'imagem';
		$_POST['destaque']='';
		//tratando vars enviadas
		$campos=''; $valores=''; $c=0;
		foreach($_POST as $key => $value)
		{
			//restrições
			$show=true;
			if(in_array($key,$dontPost)) $show=false;
			if(!isset($_POST[$key])) $show=false;
			
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

		//imagens
		$arq = New Arquivo;
		$c=0; $valores_inicial = $valores; $campos_inicial = $campos; $update_inicial = $update;
		$campos_original = array('img1','img2','img3');
		if(count($_FILES['imagem']['name']) > 1 || empty($_POST['id'])) //se não vier com multiupload
		{
			foreach($_FILES['imagem']['name'] as $temp)
			{
				if(!empty($_FILES['imagem']['name'][$c]))
				{
					foreach ($campos_original as $v)
					{
						$img = $arq->Imagem($on,$_FILES['imagem']['name'][$c],$_FILES['imagem']['tmp_name'][$c],'',$tamanho_galeria[$v]);
						$valores .= ",'" . $img . "'";
						$campos .= "," . $v;
						$update .= "," . $v . "='" . $img . "'";			
					}
					mysql_query("INSERT INTO " . $db_table . "_imagens (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($this->alertMysql(mysql_error()));
					$id = mysql_insert_id();
					$this->saveLog('inseriu',"ID: " . $id);
					$valores = $valores_inicial; $campos = $campos_inicial; $update = $update_inicial;
					$c++;
				}
			}
		}else  //se for editar 
		{
			if(!empty($_FILES['imagem']['name']))
			{
				foreach ($campos_original as $v)
				{
					$img = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho_galeria[$v]);
					$valores .= ",'" . $img . "'";
					$campos .= "," . $v;
					$update .= "," . $v . "='" . $img . "'";			
				}
			}
			mysql_query("UPDATE " . $db_table . "_imagens SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($this->alertMysql(mysql_error()));
			$id = $_POST['id'];
			$this->saveLog('editou',"ID: " . $id);
		}			
		//redirecionando página
		header('Location: index.php?on=' . $on . '&in=galeria_fotos&id=' . $_POST['cid']);
	}

	public function Apagar_img($id)
	{
		global $admin, $admin_mods, $config, $on, $in, $db_table,$tamanho;

		if(empty($_POST['conf']))
		{
			$this->breadcrumbs();
			$this->pageTitle();
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_imagens WHERE id='" . $id . "'")) or die($this->alertMysql("O Registro não existe."));
			echo '
			<div class="row">
			  <form method="post" action="index.php?on=' . $on . '">
			  	<input type="hidden" name="in" value="' . $in . '" />
			  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
			  	<input type="hidden" name="conf" value="aham" />
			    <fieldset>
				  <legend>Tem certeza que deseja apagar este registro?</legend>
				  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>
				  <div class="row">
				    <div class="large-12 columns text-center">
				    	<img src="../img/' . $on . '/' . $arr['img1'] . '" />
					</div>
				  </div>
				  <div class="row">
				    <div class="large-12 columns text-center marginleft20 margintop20">
				    	<a href="index.php?on=' . $on . '&in=editar_img&id=' . $arr['id'] . '" class="button secondary">Cancelar</a>
				    	<button type="submit" class="alert marginleft20">Confirmar exclusão</a>
					</div>
				  </div>
				</fieldset>				
			  </form>
			</div>';
		}
		else
		{
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_imagens WHERE id='" . $_POST['id'] . "'")) or die($this->alertMysql("O Registro não existe."));
			mysql_query("DELETE FROM " . $db_table . "_imagens WHERE id='" . $_POST['id'] . "'") or die($this->alertMysql(mysql_error()));
			$this->saveLog('apagou imagem',"Nome: " . $arr['titulo'] . " / ID: " . $arr['id']);
			$admin->unLinkImgs($on,$db_table,$tamanho,$arr,false);

			//redirecionando página
			header('Location: index.php?on=' . $on . '&in=galeria_fotos&id=' . $arr['cid']);
		}	
	}

	public function updateordem_img()
	{
		global $admin, $on, $in, $db_table;
		$this->saveLog('alterou a ordem das imagens','');
		foreach ($_POST['neworder'] as $key => $value)
		{
			mysql_query("UPDATE " . $db_table . "_imagens SET ordem='" . $key . "' WHERE id='" . $value . "'");
		}
	}

	public function showCode_galeria($id)
	{
		global $on;
		echo '
			<div class="row">
				<div class="columns large-12">
					<a href="javascript:void(0);" class="open-code right"><i class="fa fa-code" aria-hidden="true"></i></a>
					<dl class="tabs show-code" data-tab>
					  <dd class="active"><a href="#panel2-1">PHP</a></dd>
					  <dd><a href="#panel2-2">HTML</a></dd>
					  <dd><a href="#panel2-3">CSS</a></dd>
					  <dd><a href="#panel2-4">JS</a></dd>
					</dl>
					<div class="tabs-content show-code">
					  <div class="content active" id="panel2-1">
					    <pre>
							<code>';
								$codigo = 
								'//galeria de ' . $on . "\r\n"
								.'$galeria_' . $id . 'id = \'\'; $c=0;'."\r\n"
								.'$rr = mysql_query("SELECT * FROM ' . $on . '_imagens WHERE cid=\'' . $id . '\' ORDER by ordem");'."\r\n"
								.'$total = mysql_num_rows($rr);'."\r\n"
								.'while($arr = mysql_fetch_array($rr))'."\r\n"
								.'{'."\r\n"
								.'  $c++; if($total == $c) $class="end";'."\r\n"
								.'  $galeria_' . $id . 'id .= \''."\r\n"
								.'  <div class="columns large-3 three \' . $class . \'">'."\r\n"
								.'    <div class="crop">'."\r\n"
								.'      <a href="img/' . $on . '/\' . $arr[\'img2\'] . \'" class="various" rel="galeria_' . $id . 'id">'."\r\n"
								.'        <img src="img/' . $on . '/\' . $arr[\'img1\'] . \'" alt="\' . $arr[\'titulo\'] . \'" title="\' . $arr[\'titulo\'] . \'" />'."\r\n"
								.'      </a>'."\r\n"
								.'    </div>'."\r\n"
								.'  </div>\';'."\r\n"
								.'}'."\r\n"
								.'Parser::__alloc("galeria_' . $id . 'id",$galeria_' . $id . 'id);';
								echo htmlentities($codigo) . '
							</code>
						</pre>
					  </div>
					  <div class="content" id="panel2-2">
					    <pre>
							<code>';
								$codigo = 
								'<!--galeria de fotos-->'."\r\n"
								.'<div id="galeria_' . $id . 'id" class="row">'."\r\n"
								.'  <var name="galeria_' . $id . 'id" />'."\r\n"
								.'</div>'."\r\n";
								echo htmlentities($codigo) . '
							</code>
						</pre>
					  </div>
					  <div class="content" id="panel2-3">
					    <pre>
							<code>';
								$codigo = 
								'#galeria_' . $id . 'id .crop'."\r\n"
								.'{'."\r\n"
								.'  height:200px; /* altura de acordo com o design */'."\r\n"
								.'  overflow:hidden;'."\r\n"
								.'}'."\r\n";
								echo htmlentities($codigo) . '
							</code>
						</pre>
					  </div>
					  <div class="content" id="panel2-4">
					    <p>Não é necessário, desde que haja a chamada do fancybox em /js/base.js, o que é padrão.</p>
					  </div>
					</div>
				</div>
			</div>';
	}

	public function checkMySql_img()
	{
		global $db_table;

		//_imagens
		if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "_imagens'")) == 0)
		{
			mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "_imagens` (
						  `id` int(5) NOT NULL AUTO_INCREMENT,
						  `cid` int(5) NOT NULL,
						  `titulo` varchar(255) NOT NULL,
						  `img1` varchar(255) NOT NULL,
						  `img2` varchar(255) NOT NULL,
						  `img3` varchar(255) NOT NULL,
						  `destaque` enum('Y','N') NOT NULL DEFAULT 'N',
						  `ordem` int(5) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
		}
	}

	public function categorias()
	{
		global $admin_mods, $config, $on, $in, $db_table, $_GET;
		$this->breadcrumbs();
		$this->pageTitle();
		$this->checkMySql_cat();
		
		$query = "SELECT * FROM " . $db_table . "_cat ORDER BY ordem";
		echo '
		<div class="row">
			<div class="columns large-12">
			<h3>Lista de categorias</h3>
			</div>
		</div>
		<div class="row">
			<div class="columns large-12">';
				if(mysql_num_rows(mysql_query($query)) > 0)
				{
					echo '
					<table id="tabela_menu" data-postFunc="updatemenu_cat" class="list-table">
						<thead>
							<tr>
								<th width="200">
								Título
								<span class="right disabled">Arraste as linhas para reordenar</span>
								</th>
							</tr>
						</thead>
						<tbody>';
						$rr = mysql_query($query);
						while ($arr = mysql_fetch_array($rr))
						{
							echo '
							<tr id="' . $arr['id'] . '" class="ui-state-default">
								<td>
									<a href="index.php?on=' . $on . '&in=editar_cat&id=' . $arr['id'] . '">' . $arr['titulo'] . '</a>
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

	public function Form_cat($id)
	{
		global $admin, $admin_mods, $config, $on, $in, $db_table;
		$this->breadcrumbs();
		$this->pageTitle();
		$this->checkMySql_cat();

		if($in == 'editar_cat')
		{
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $id . "' LIMIT 1"));
			$this->saveLog('visualizou',"Categoria: " . $arr['titulo'] . " / ID: " . $arr['id']);
		}else
		{
			$arr['tamanho1'] = '500';
			$arr['tamanho2'] = '1000';
		}
		echo '
		<div class="row">
		  <form method="post" action="index.php?on=' . $on . '">
		  	<input type="hidden" name="in" value="salvar_cat" />
		  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
			<fieldset>
			  <legend>Dados</legend>
			  <div class="row">
			    <div class="large-12 columns">
			      <label>Categoria
			        <input type="text" name="titulo" placeholder="Dê um nome para a categoria" value="' . $arr['titulo'] . '" />
			      </label>
			    </div>
			  </div>
			</fieldset>
			<div class="row ">
				<div class="large-12 columns margintop20 text-right">';
				 	if(!empty($arr['id'])) echo '<a href="index.php?on=' . $on . '&in=apagar_cat&id=' . $arr['id'] . '" class="button alert marginright10 left hide-for-small">Apagar</a>';
				 	echo '
					<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar</a>
			    	<button type="submit">' . $this->formButton() . '</button>
				</div>
			</div>
		  </form>
		</div>';
	}

	public function Salvar_cat()
	{
		global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $_FILES;
		//editando pre-vars
		$dontPost[] = 'permissoes';
		//tratando vars enviadas
		$campos=''; $valores=''; $c=0;
		foreach ($_POST as $key => $value)
		{
			//restrições
			$show=true;
			if(in_array($key,$dontPost)) $show=false;
			if(!isset($_POST[$key])) $show=false;
			
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
			mysql_query("INSERT INTO " . $db_table . "_cat (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($this->alertMysql(mysql_error()));
			$id = mysql_insert_id();
			$this->saveLog('inseriu',"Categoria ID: " . $id);
		}else
		{
			mysql_query("UPDATE " . $db_table . "_cat SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($this->alertMysql(mysql_error()));
			$id = $_POST['id'];
			$this->saveLog('editou',"Categoria ID: " . $id);
		}
		//redirecionando página
		header('Location: index.php?on=' . $on . '&filtro_' . $on . '=' . $id);
	}

	public function Apagar_cat($id)
	{
		global $admin, $admin_mods, $config, $on, $in, $db_table;
		if(empty($_POST['conf']))
		{
			$this->breadcrumbs();
			$this->pageTitle();
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $id . "'")) or die($this->alertMysql("O Registro não existe."));
			echo '
			<div class="row">
			  <form method="post" action="index.php?on=' . $on . '">
			  	<input type="hidden" name="in" value="apagar_cat" />
			  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
			  	<input type="hidden" name="conf" value="aham" />
			    <fieldset>
				  <legend>Tem certeza que deseja apagar esta categoria?</legend>
				  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>
				  <div class="row">
				    <div class="large-12 columns text-center">
				    	' . $arr['titulo'] . '
					</div>
				  </div>
				  <div class="row">
				    <div class="large-12 columns text-center marginleft20 margintop20">
				    	<a href="index.php?on=' . $on . '&in=editar_cat&id=' . $arr['id'] . '" class="button secondary">Cancelar</a>
				    	<button type="submit" class="alert marginleft20">Confirmar exclusão</a>
					</div>
				  </div>
				</fieldset>				
			  </form>
			</div>';
		}
		else
		{
			session_start();
			$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $_POST['id'] . "'")) or die($this->alertMysql("O Registro não existe."));
			$tamanho = array('img1','img2','img3');
			$rr2 = mysql_query("SELECT * FROM " . $db_table . " WHERE cid='" . $_POST['id'] . "'");
			while($arrItem = mysql_fetch_array($rr2))
			{
				foreach ($tamanho as $value) {
					if(file_exists(str_replace('/cms','',getcwd()) . '/img/' . $on . '/' . $arrItem[$value]))
					{
						if($zera_campos) mysql_query("UPDATE " . $db_table . " SET " . $value . "='' WHERE id='" . $arrItem['id'] . "'");
						unlink(str_replace('/cms','',getcwd()) . '/img/' . $on . '/' . $arrItem[$value]);
					}
				}
			}

			mysql_query("DELETE FROM " . $db_table . " WHERE cid='" . $_POST['id'] . "'") or die($this->alertMysql(mysql_error()));
			mysql_query("DELETE FROM " . $db_table . "_cat WHERE id='" . $_POST['id'] . "'") or die($this->alertMysql(mysql_error()));
			$this->saveLog('apagou',"Categoria: " . $arr['titulo'] . " / ID: " . $arr['id']);


			unset($_SESSION['filtro_' . $db_table]);
			//redirecionando página
			header('Location: index.php?on=' . $on);
		}	
	}

	public function unLinkImgs($on,$db_table,$tamanho,$arr,$zera_campos)
	{
		foreach ($tamanho as $key => $value) {
			if(file_exists(str_replace('/cms','',getcwd()) . '/img/' . $on . '/' . $arr[$key]))
			{
				if($zera_campos) mysql_query("UPDATE " . $db_table . " SET " . $key . "='' WHERE id='" . $arr['id'] . "'");
				unlink(str_replace('/cms','',getcwd()) . '/img/' . $on . '/' . $arr[$key]);
			}
		}
	}

	public function updatemenu_cat()
	{
		global $admin, $db_table, $on, $in;
		$this->saveLog('alterou a ordem','');
		foreach ($_POST['neworder'] as $key => $value)
		{
			mysql_query("UPDATE " . $db_table . "_cat SET ordem='" . $key . "' WHERE id='" . $value . "'");
		}
	}

	public function checkMySql_cat()
	{
		global $db_table;

		//_imagens
		if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "_cat'")) == 0)
		{
			mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "_cat` (
						  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
						  `titulo` varchar(255) NOT NULL DEFAULT '',
						  `tamanho1` int(5) NOT NULL,
						  `tamanho2` int(5) NOT NULL,
						  `ordem` int(5) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
		}
	}

}