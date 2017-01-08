<?php
global $admin;

//set default table
$db_table = $admin->getTabelaModulo($_GET['on']);

//set images sizes
$tamanho = array('img1'=>'300','img2'=>'600','img3'=>'0');
$tamanho_galeria = array('img1'=>'500','img2'=>'1000','img3'=>'0');

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	
	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou','');
	checkMySql();

	if($admin->isDeveloper())
	{
		showCode($id);
	}
	
	$query = "SELECT * FROM " . $db_table . " ORDER BY ordem";
	echo '
	<div class="row">
		<div class="columns large-12">';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table id="tabela_menu" class="list-table">
					<thead>
						<tr>
							<th width="200">
							Nome
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
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">
									' . $arr['titulo'] . '
								</a>';
								if($admin->ModuloOptionsCheck($on,'star')){ 
									echo '<img src="img/star-2.png" class="star set '; if($arr['star'] != 'Y') echo 'disabled'; else echo 'active'; echo ' right" />';
								}
								echo '
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

function Form($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	$admin->breadcrumbs();
	$admin->pageTitle();
	if($in == 'editar')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "' LIMIT 1"));
		$admin->saveLog('visualizou',"Página: " . $arr['nome'] . " / ID: " . $arr['id']);
		$_SESSION['last_gallery_id'] = $id;

		if($admin->ModuloOptionsCheck($on,'galeria'))
		{
			echo '
			<div class="row">
			  <div class="columns large-1 right">
			  	<a href="index.php?on=' . $on . '&in=galeria_fotos&id=' . $arr['id'] . '">
					<img id="open-galeria" data-tooltip src="img/icon-gallery.png" alt="Ver galeria de fotos" class="has-tip tip-left" title="Ver galeria de fotos" />
			  	</a>
			  </div>
			</div>';
		}	
	}
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
	  	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>
		  <legend>Dados Gerais</legend>';
		  if($admin->ModuloOptionsCheck($on,'imagem'))
		  {
		  	echo '
			  <div class="row">
			    <div class="large-6 columns image-box end">'
			    . $admin->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
			    . '<br/><br/>
			    </div>
			  </div>';
		  }
		  echo '
		    <div class="row">
		    	<div class="large-12 columns">
			      <label>Título (exatamente igual ao módulo)
			        <input type="text" name="titulo" placeholder="Digite o título para exibição" value="' . $arr['titulo'] . '" required />
			      </label>
			    </div>
			</div>';
			/*<div class="large-3 columns">
		  		<label>Largura da View
			        <select name="largura_view">
				        	<option value="1000"'; if($arr['largura_view'] == '1000') echo ' selected'; echo '>1000px (Foundation)</option>
				        	<option value="screen"'; if($arr['largura_view'] == 'screen') echo ' selected'; echo '>Largura da Tela</option>
				    </select>
			    </label>
		  	</div>
		  	<div class="large-3 columns">
		  		<label>Altura de cada ítem
		  			<input type="text" name="altura_itens" value="' . $arr['altura_itens'] . '" />
			    </label>
		  	</div>
		  	<div class="large-3 columns">
		  		<label>Qtd de ítens que trocam por click
		  			<select name="scroll_itens">
				        	<option value="1"'; if($arr['scroll_itens'] == '1') echo ' selected'; echo '>1</option>
				        	<option value="todos"'; if($arr['scroll_itens'] == 'todos') echo ' selected'; echo '>Todos</option>
				    </select>
			    </label>
		  	</div>*/
		  echo '
		  <div class="row">
		  	<div class="large-3 columns">
		  		<label>Nro de ítens visíveis na tela
		  			<input type="number" name="itens" value="' . $arr['itens'] . '" />
			    </label>
		  	</div>
		  </div>
		</fieldset>';
		if($in == 'editar'){
		echo'
		<fieldset>
			<div class="row">
				<div class="large-12 columns">
					<textarea name="" id="" cols="30" rows="10">';
							$crsl_table = $arr['titulo'];
							$crslName = "crsl-".$crsl_table;
							$codigo = 
								'$crsl_table="'.$crsl_table.'";'."\r\n"
								.'$crslName="'.$crslName.'";'."\r\n" 
								.'$sql = "SELECT * FROM carousel WHERE titulo=\''.$crsl_table.'\'";'."\r\n" 
								.'$query = mysql_query($sql);'."\r\n"
								.'$columns = mysql_num_fields($query);'."\r\n"
								.'for($i = 1; $i < $columns; $i++) {'."\r\n"
								.'$fieldName = mysql_field_name($query,$i);'."\r\n"
								.'while($row = mysql_fetch_assoc($query,$i)){'."\r\n"
								.'foreach($row as $column=>$value) {'."\r\n"
      							.'if($column != "id" && $column != "titulo" && $column != "modulo_ref" && $column != "ordem"){'."\r\n"
        						.'$crsl_options.="data-".$column."=\'".$value."\'";'."\r\n"
      							.'}'."\r\n"
      							.'}'."\r\n" 
  								.'}'."\r\n"
								.'}'."\r\n"  
								.'$crsl1_parser .= "<div class=\'crsl-wrap\'>";'."\r\n"  
								.'$crsl1_parser .= "<ul id=\''.$crslName.'\' class=\'hasCarousel\' \'".$crsl_options."\'>";//PARA ITEM CIRCULAR, ADD CLASS "round"'."\r\n"  
								.'$c=0;'."\r\n"
								.'$rr = mysql_query("SELECT * FROM ".$crsl_table." ORDER by ordem");'."\r\n"
								.'$total = mysql_num_rows($rr);'."\r\n"
								.'while($arr = mysql_fetch_array($rr))'."\r\n"
								.'{'."\r\n"
								.'$crsl1_parser .= "'."\r\n"
								.'<li>'."\r\n"
								.'<img src=\'img/'.$crsl_table.'/" . $arr[\'img1\'] . " \'alt=\'". $arr[\'titulo\'] . "\'/>'."\r\n"
								.'</li>";'."\r\n"
								.'}'."\r\n"
								.'$crsl1_parser.="</ul>";'."\r\n"
								.'$crsl1_parser.=$Layout->geraNavs($crslName, \'center\'); //center = navs no centro da tela; side = navs nas laterais '."\r\n"
								.'$crsl1_parser.="</div>";'."\r\n";
								echo htmlentities($codigo);
							echo'
					</textarea>
				</div>
			</div>
		</fieldset>';
	}
	echo'
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">';
			 	if(!empty($arr['id'])) echo '<a href="index.php?on=' . $on . '&in=apagar&id=' . $arr['id'] . '" class="button alert marginright10 left hide-for-small">Apagar <i class="fa fa-trash-o" aria-hidden="true"></i></a>';
			 	echo '
				<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar <i class="fa fa-times" aria-hidden="true"></i></a>
		    	<button type="submit">' . $admin->formButton() . '</button>
			</div>
		</div>
	  </form>
	</div>';
}

function Salvar()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost,$tamanho;
	//editando pre-vars
	$dontPost[] = 'MAX_FILE_SIZE'; $dontPost[] = 'imagem'; $dontPost[] = 'pdf';
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

	//imagens
	$arq = New Arquivo;
	if (!empty($_FILES['imagem']['name']))
	{
		$campo_original = array('img1','img2','img3');
		foreach ($campo_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";
		}
	}	

	//gravando informações no banco
	if(empty($_POST['id'])) //se vier de um form de inclusão
	{
		mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
		$id = mysql_insert_id();
		$admin->saveLog('inseriu',"Nome: " . $_POST['nome'] . " / ID: " . $id);
	}else
	{
		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$id = $_POST['id'];
		$admin->saveLog('editou',"Nome: " . $_POST['nome'] . " / ID: " . $id);
	}
	//redirecionando página
	header('Location: index.php?on=' . $on);
}

function Apagar($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table,$tamanho;
	if(empty($_POST['conf']))
	{
		$admin->breadcrumbs();
		$admin->pageTitle();
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));
		echo '
		<div class="row">
		  <form method="post" action="index.php?on=' . $on . '">
		  	<input type="hidden" name="in" value="apagar" />
		  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
		  	<input type="hidden" name="conf" value="aham" />
		    <fieldset>
			  <legend>Tem certeza que deseja apagar este registro?</legend>
			  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>
			  <div class="row">
			    <div class="large-12 columns text-center">
			    	<h4>' . $arr['titulo'] . '</h4>
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
		mysql_query("DELETE FROM " . $db_table . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$admin->saveLog('apagou',"Nome: " . $arr['nome'] . " / ID: " . $arr['id']);
		$admin->unLinkImgs($on,$db_table,$tamanho,$arr,false);
		//redirecionando página
		header('Location: index.php?on=' . $on);
	}	
}

function Apagar_Imagem($id,$campos)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table,$tamanho;
	
	$campos = explode(',',$_GET['campos']);
	if(empty($_POST['conf']))
	{
		$admin->breadcrumbs();
		$admin->pageTitle();
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));
		echo '
		<div class="row">
		  <form method="post" action="index.php?on=' . $on . '">
		  	<input type="hidden" name="in" value="apagar_imagem" />
		  	<input type="hidden" name="campos" value="' . $_GET['campos'] . '" />
		  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
		  	<input type="hidden" name="conf" value="aham" />
		    <fieldset>
			  <legend>Tem certeza que deseja apagar esta imagem?</legend>
			  <span class="label secondary radius margintop-10">Não será possível desfazer esta ação.</span>
			  <div class="row">
			    <div class="large-12 columns text-center">
			    	<img src="../img/' . $on . '/' . $arr[$campos[1]] . '" />
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
		$admin->saveLog('apagou',"Imagens: " . $_POST['campos'] . " / ID: " . $arr['id']);
		$admin->unLinkImgs($on,$db_table,$tamanho,$arr,true);
		
		//redirecionando página
		header('Location: index.php?on=' . $on . '&in=editar&id=' . $_POST['id']);
	}	
}

function updatemenu()
{
	global $admin, $db_table, $on, $in;
	$admin->saveLog('alterou a ordem','');
	foreach ($_POST['neworder'] as $key => $value)
	{
		mysql_query("UPDATE " . $db_table . " SET ordem='" . $key . "' WHERE id='" . $value . "'");
	}
	echo "UPDATE " . $db_table . " SET ordem='" . $key . "' WHERE id='" . $value . "'";
}

function showCode($filtro_imagens)
{
	global $admin, $db_table, $dados, $on;
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'"));
	echo '
		<div class="row">
			<div class="columns large-12">
				<a href="javascript:void(0);" class="open-code right"><img src="img/icon-code.png" alt="Mostrar código" class="absolute" /></a>
				<dl class="tabs show-code" data-tab>
				  <dd class="active"><a href="#panel2-1">Opções</a></dd>
				  <dd><a href="#panel2-2">PHP</a></dd>
				  <dd><a href="#panel2-3">HTML</a></dd>
				  <dd><a href="#panel2-4">CSS</a></dd>
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
				  	<button id="zerar-banco">Zerar</button>
					<div class="mod-options">
						<label for="imagem">
							<input type="checkbox" name="imagem" id="imagem" value="Y" '; if($admin->ModuloOptionsCheck($on,'imagem')) echo ' checked'; echo '/> Imagem
						</label>
						<label for="star">
							<input type="checkbox" name="star" id="star" value="Y" '; if($admin->ModuloOptionsCheck($on,'star')) echo ' checked'; echo '/> Estrela
						</label>
						<label for="cidade">
							<input type="checkbox" name="cidade" id="cidade" value="Y" '; if($admin->ModuloOptionsCheck($on,'cidade')) echo ' checked'; echo '/> Cidade
						</label>
						<label for="email">
							<input type="checkbox" name="email" id="email" value="Y" '; if($admin->ModuloOptionsCheck($on,'email')) echo ' checked'; echo '/> E-mail
						</label>
						<label for="site">
							<input type="checkbox" name="site" id="site" value="Y" '; if($admin->ModuloOptionsCheck($on,'site')) echo ' checked'; echo '/> Site
						</label>
						<label for="linkedin">
							<input type="checkbox" name="linkedin" id="linkedin" value="Y" '; if($admin->ModuloOptionsCheck($on,'linkedin')) echo ' checked'; echo '/> Linkedin
						</label>
						<label for="facebook">
							<input type="checkbox" name="facebook" id="facebook" value="Y" '; if($admin->ModuloOptionsCheck($on,'facebook')) echo ' checked'; echo '/> Facebook
						</label>
						<label for="twitter">
							<input type="checkbox" name="twitter" id="twitter" value="Y" '; if($admin->ModuloOptionsCheck($on,'twitter')) echo ' checked'; echo '/> Twitter
						</label>
						<label for="texto">
							<input type="checkbox" name="texto" id="texto" value="Y" '; if($admin->ModuloOptionsCheck($on,'texto')) echo ' checked'; echo '/> Texto
						</label>
						<label for="galeria">
							<input type="checkbox" name="galeria" id="galeria" value="Y" '; if($admin->ModuloOptionsCheck($on,'galeria')) echo ' checked'; echo '/> Galeria
						</label>
					</div>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>';
							$codigo = 
							'//' . $on ."\r\n"
							.'$' . $on . ' = \'\'; $c=0;'."\r\n"
							.'$rr = mysql_query("SELECT * FROM ' . $db_table . ' ORDER by ordem");'."\r\n"
							.'$total = mysql_num_rows($rr);'."\r\n"
							.'while($arr = mysql_fetch_array($rr))'."\r\n"
							.'{'."\r\n"
							.'  $c++; if($total == $c) $class="end";'."\r\n"
							.'  $' . $on . ' .= \''."\r\n"
							.'  <div class="columns large-4 four \' . $class . \'">'."\r\n"
							.'    <div class="crop">'."\r\n"
							.'      <a href="img/' . $on . '/\' . $arr[\'img2\'] . \'" class="various" rel="' . $on . '">'."\r\n"
							.'        <img src="img/' . $on . '/\' . $arr[\'img1\'] . \'" alt="\' . $arr[\'titulo\'] . \'" />'."\r\n"
							.'      </a>'."\r\n"
							.'    </div>'."\r\n"
							.'  </div>\';'."\r\n"
							.'}'."\r\n"
							.'Parser::__alloc("' . $on . '",$' . $on . ');';
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-3">
				    <pre>
						<code>';
							$codigo = 
							'<!--' . $on . '-->'."\r\n"
							.'<div id="' . $on . '" class="row">'."\r\n"
							.'  <var name="' . $on . '" />'."\r\n"
							.'</div>'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-4">
				    <pre>
						<code>';
							$codigo = 
							'#' . $on . ' .crop'."\r\n"
							.'{'."\r\n"
							.'  height:200px; /* altura de acordo com o design */'."\r\n"
							.'  overflow:hidden;'."\r\n"
							.'}'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				</div>
			</div>
		</div>';
}

function checkMySql()
{
	global $db_table;

	//principal
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
					  `titulo` varchar(180) NOT NULL DEFAULT '',
					  `texto` text NOT NULL,
					  `site` varchar(255) NOT NULL,
					  `linkedin` varchar(255) NOT NULL,
					  `facebook` varchar(255) NOT NULL,
					  `twitter` varchar(255) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `cidade` varchar(255) NOT NULL,
					  `img1` varchar(255) NOT NULL,
					  `img2` varchar(255) NOT NULL,
					  `img3` varchar(255) NOT NULL,
					  `ordem` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
}

function truncate()
{
	global $db_table, $on;
	mysql_query("TRUNCATE " . $db_table);
	mysql_query("TRUNCATE " . $db_table . "_imagens");
	header('Location: index.php?on=' . $on);
}

switch($in)
{
	default;
	Main();
	break;
	
	case "novo";
	case "editar";
	Form($id);
	break;

	case "inserir";
	case "salvar";
	Salvar();
	break;
	
	case "apagar";
	Apagar($id);
	break;	
	
	case "apagar_imagem";
	Apagar_Imagem($id);
	break;

	case "apagar_arquivo";
	$arquivo = New Arquivo;
	$arquivo->apagar_arquivo($_GET['id']);
	break;

	case "truncate";
	truncate($id);
	break;
	
	case "updatemenu";
	updatemenu();
	break;
	
	//funções de galeria de fotos, classe $admin
	case "galeria_fotos";
		$admin->galeria_fotos($id);
	break;

	case "nova_img"; case "editar_img";
		$admin->Form_img($id);
	break;

	case "inserir_img"; case "salvar_img";
		$admin->Salvar_img();
	break;
	
	case "apagar_img";
		$admin->Apagar_img($id);
	break;
	
	case "updateordem_img";
		$admin->updateordem_img();
	break;		
}