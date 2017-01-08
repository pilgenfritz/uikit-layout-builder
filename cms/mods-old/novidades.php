<?php
global $admin;

//set default table
$db_table = $admin->getTabelaModulo($_GET['on']);

//set images sizes
$tamanho = array('img1'=>'500','img2'=>'1000','img3'=>'0');
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
	
	$query = "SELECT * FROM " . $db_table . " ORDER BY data DESC";
	echo '
	<div class="row">
		<div class="columns large-12">';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<thead>
						<tr>';
							if($admin->ModuloOptionsCheck($on,'data')) echo '<th width="150">Data</th>';
							echo '
							<th>Título</th>
							<th width="100"></th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr class="'; if($arr['active'] != 'Y') echo ' inactive'; echo '">';
							if($admin->ModuloOptionsCheck($on,'data')) echo '
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">
									' . date('d/m/Y',strtotime($arr['data'])) . '
								</a>
							</td>';

							echo '
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">
									' . $arr['titulo'] . '
								</a>
							</td>
							<td class="text-right options">';
								if($admin->ModuloOptionsCheck($on,'active')){ 
									echo '
									<i class="fa fa-toggle-'; if($arr['active'] == 'Y') echo 'on'; else echo 'off';echo ' set-active" aria-hidden="true" data-id="' . $arr['id'] . '"></i>';
								}
								if($admin->ModuloOptionsCheck($on,'star')){ 
									echo '
									<i class="fa fa-star'; if($arr['star'] != 'Y') echo '-o'; echo ' set-star" aria-hidden="true" data-id="' . $arr['id'] . '"></i>';
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
		$data = date('d/m/Y',strtotime($arr['data']));
	}else
	{
		$data = date('d/m/Y');
	}
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
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
	  	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>
		  <legend>Dados Gerais</legend>
		  <div class="row">
		    <div class="large-6 columns image-box end">'
		    . $admin->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
		    . '<br/><br/>
		    </div>
		  </div>';
		  if($admin->ModuloOptionsCheck($on,'data'))
		  {
			  echo '
			  <div class="row">
			    <div class="large-2 columns end">
			      <label>Data
			        <input type="text" name="data" placeholder="Digite uma data" value="' . $data . '" class="datepicker" required />
			      </label>
			    </div>
			  </div>';
		  }
		  echo '
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Título
		        <input type="text" name="titulo" placeholder="Digite o nome para exibição" value="' . $arr['titulo'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Texto
		        <textarea id="texto" name="texto" cols="80" rows="10" class="ckeditor">' . $arr['texto'] . '</textarea>
		      </label>
		    </div>
		  </div>';
		  if($admin->ModuloOptionsCheck($on,'tags'))
		  {

			  $tags = ''; $c=0;
			  $r2 = mysql_query("SELECT * FROM " . $db_table . "_vinculos WHERE pid='" . $arr['id'] . "' ORDER by id");
			  while($ar2 = mysql_fetch_array($r2))
			  {
			  	$c++;
			    if($c > 1) $tags .= ',';
			    list($titulo) = mysql_fetch_row(mysql_query("SELECT titulo FROM " . $db_table . "_tags WHERE id='" . $ar2['tid'] . "'"));
			    $tags .= $titulo;
			  }
			  echo '
			  <div class="row">
			    <div class="large-12 columns">
			    <br/ >
			      <label>Tags
			      <div class="control-group">
						<input type="text" name="tags" id="input-tags" class="input-tags demo-default" value="' . $tags . '">
					</div>
			      </label>
			    </div>
			  </div>';
		   }
		   echo '		  
		</fieldset>
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
	global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $tamanho, $dados;
	//editando pre-vars
	$dontPost[] = 'MAX_FILE_SIZE'; $dontPost[] = 'imagem'; $dontPost[] = 'tags';
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
			if($key=='data') $valores .= "'" . $dados->formadata($value,'1') . "'"; else $valores .= "'$value'";
			if(!empty($_POST['id'])) //se vier do form editar, add na var update
			{
				if($key=='data') $update .= " $key='" . $dados->formadata($value,'1') . "'";
				else $update .= " $key='$value'";
			}
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
	//tags
	delete_vinculos($id);
	$tags = explode(',',$_POST['tags']);
	foreach ($tags as $v)
	{
		if(!tag_exists($v))
		{
			$tid = create_tag($v);
		}else{
			$tid = getid_tag($v);
		}
		create_vinculo($id,$tid);
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
}

function checkMySql()
{
	global $db_table;

	//main
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
					  `data` date NOT NULL,
					  `titulo` varchar(180) NOT NULL DEFAULT '',
					  `texto` text NOT NULL,
					  `img1` varchar(255) NOT NULL,
					  `img2` varchar(255) NOT NULL,
					  `img3` varchar(255) NOT NULL,
					  `ordem` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	}

	//_tags
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "_tags'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "_tags` (
					  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
					  `titulo` varchar(180) NOT NULL DEFAULT '',
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	}

	//_vinculos
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "_vinculos'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "_vinculos` (
					  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
					  `pid` int(5) NOT NULL,
					  `tid` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	}
}

function tag_exists($titulo)
{
	global $db_table;
	if(mysql_num_rows(mysql_query("SELECT id FROM " . $db_table . "_tags WHERE titulo='" . $titulo . "' LIMIT 1")) > 0) return true;
	return false;
}

function create_tag($titulo)
{
	global $db_table;
	
	mysql_query("INSERT INTO " . $db_table . "_tags VALUES(NULL,'" . $titulo . "')");
	$novaTag = mysql_insert_id();
	return $novaTag;
}

function getid_tag($titulo)
{
	global $db_table;
	list($id) = mysql_fetch_row(mysql_query("SELECT id FROM " . $db_table . "_tags WHERE titulo='" . $titulo . "'"));
	
	return $id;
}

function create_vinculo($pid,$tid)
{
	global $db_table;
	mysql_query("INSERT INTO " . $db_table . "_vinculos VALUES(NULL,'" . $pid . "','" . $tid . "')");
}

function has_vinculo($pid,$tid)
{
	global $db_table;
	
	if(mysql_num_rows(mysql_query("SELECT id FROM " . $db_table . "_vinculos WHERE pid='" . $pid . "' && tid='" . $tid . "'")) > 0) return true;
	return false;
}

function delete_vinculos($pid)
{
	global $db_table;
	
	mysql_query("DELETE FROM " . $db_table . "_vinculos WHERE pid='" . $pid . "'");
}

function showCode($id)
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
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
					<button id="zerar-banco">Zerar</button>
					<div class="mod-options">
						<label for="active">
							<input type="checkbox" name="active" id="active" value="Y" '; if($admin->ModuloOptionsCheck($on,'active')) echo ' checked'; echo '/> Ativo/Inativo
						</label>
						<label for="star">
							<input type="checkbox" name="star" id="star" value="Y" '; if($admin->ModuloOptionsCheck($on,'star')) echo ' checked'; echo '/> Estrela
						</label>
						<label for="tags">
							<input type="checkbox" name="tags" id="tags" value="Y" '; if($admin->ModuloOptionsCheck($on,'tags')) echo ' checked'; echo '/> Tags
						</label>
						<label for="data">
							<input type="checkbox" name="data" id="data" value="Y" '; if($admin->ModuloOptionsCheck($on,'data')) echo ' checked'; echo '/> Data
						</label>
						<label for="galeria">
							<input type="checkbox" name="galeria" id="galeria" value="Y" '; if($admin->ModuloOptionsCheck($on,'galeria')) echo ' checked'; echo '/> Galeria
						</label>
					</div>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>
							Deve ser utilizado o modelo e classe do Blog. 
						</code>
					</pre>
				  </div>
				</div>
			</div>
		</div>';
}

function truncate()
{
	global $db_table, $on;
	mysql_query("TRUNCATE " . $db_table);
	mysql_query("TRUNCATE " . $db_table . "_imagens");
	mysql_query("TRUNCATE " . $db_table . "_tags");
	mysql_query("TRUNCATE " . $db_table . "_vinculos");
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
	
	case "updatemenu";
	updatemenu();
	break;	
	
	case "apagar_imagem";
	Apagar_Imagem($id);
	break;
	
	case "truncate";
	truncate($id);
	break;
	
	case "apagar_arquivo";
	$arquivo = New Arquivo;
	$arquivo->apagar_arquivo($_GET['id']);
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