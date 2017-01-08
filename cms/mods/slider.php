<?php
$db_table='slider';
$tamanho = array('img1'=>'500','img2'=>'1360','img3'=>'0',
				'img_mobile1'=>'500','img_mobile2'=>'1000','img_mobile3'=>'0', 'img_logo1' => '0', 'img_capa' => '0'
				);

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	if($admin->isDeveloper())
	{
		//showCode();
	}
	list($queryAux) = mysql_fetch_row(mysql_query("SELECT valor FROM " . $db_table . "_option WHERE id=1"));
	$conteudo = '';
	$mostra='';
	if($queryAux == 'I')
	{
		$query = "SELECT * FROM " . $db_table . " WHERE tipo='T' OR tipo='I' ORDER BY ordem";
		$conteudo = 'Imagens';
	}else
	{
		$query = "SELECT * FROM " . $db_table . " WHERE tipo='V' ORDER BY ordem";
		$conteudo = 'Videos';
	}
	$admin->breadcrumbs();
	$admin->pageTitle($conteudo);
	$admin->saveLog('acessou','');
	checkMySql();

	echo '
	<div class="row">
		<div class="columns large-12">
			<div class="switchTipo">
				<div class="switch">
				  <input type="hidden" name="' . $arr['chave'] . '" value="N" />
				  <input id="checkTipo" name="checkTipo" type="checkbox" value="' . $conteudo . '"'; 
				  if($queryAux == 'I' && $conteudo == 'Imagens' || $queryAux == 'V' && $conteudo == 'Videos') echo ' checked';
				  echo '>
				  <label for="checkTipo"></label>
				</div>
			</div>';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table id="tabela_menu" class="list-table">
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr id="' . $arr['id'] . '" class="ui-state-default">
							<td class="'; if($arr['active'] != 'Y') echo ' inactive'; echo '">
								<div class="slider right options">
									<i class="fa fa-toggle-';
										 if($arr['active'] == 'Y') echo 'on'; else echo 'off'; echo '
									 set-active right" aria-hidden="true" data-id="' . $arr['id'] . '"></i>
								</div>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">';
									$queryAux != 'I'? $m = '_capa' : $m = '1';
									echo '<img src="../img/' . $on . '/' . $arr['img' . $m] . '" style="width:300px;" />
								</a>
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

/*<div class="row">
	      		<div class="large-3 columns">
			    	<input name="altura" id="altura1" type="radio" value="F"';
			    	if($arr['altura'] == 'F' || $arr['altura'] == '') echo ' checked';
			    	echo '><label for="altura1"><strong>Altura da tela</strong></label>
		        </div>
		        <div class="large-3 columns end">
			    	<input name="altura" id="altura2" type="radio" value="E"';
			    	if($arr['altura'] == 'E') echo ' checked';
			    	echo '><label for="tipo2"><strong>Altura Específica</strong></label>
		        </div>
	      </div>
	      <div class="row altura-especifica">
	      	<div class="large-6 columns">
	      		<input type="text" id="alturaEspecifica" name="alturaEspecifica" placeholder="Defina a altura do slider ex: 500" value="'. $arr['alturaPx'] .'"/>
	      	</div>
	      </div>*/

function Form($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table,$tamanho;
	$admin->breadcrumbs();
	$admin->pageTitle();
	list($queryAux) = mysql_fetch_row(mysql_query("SELECT valor FROM " . $db_table . "_option WHERE id=1"));
	if($in == 'editar')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "' LIMIT 1"));
		$admin->saveLog('visualizou',"Página: " . $arr['titulo'] . " / ID: " . $arr['id']);
	}
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
	  	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	  	<fieldset'; if($queryAux != 'I') echo ' style="display:none;"'; echo '>
		  <legend>Conteúdo</legend>
		  <div class="row">
			    <div class="large-3 columns">
			    	<input name="tipo" id="tipo1" type="radio" value="I"';
			    	if($arr['tipo'] == 'I' || $arr['tipo'] == '' && $queryAux == 'I') echo ' checked';
			    	echo '><label for="tipo1"><strong>Somente Imagem</strong></label>
		        </div>
		        <div class="large-3 columns end">
			    	<input name="tipo" id="tipo2" type="radio" value="T"';
			    	if($arr['tipo'] == 'T') echo ' checked';
			    	echo '><label for="tipo2"><strong>Imagem + Texto</strong></label>
		        </div>
		        <div class="large-3 columns end"'; if($queryAux == 'I') echo ' style="display:none;"'; echo '>
			    	<input name="tipo" id="tipo3" type="radio" value="V"';
			    	if($arr['tipo'] == 'V' || $queryAux != 'I') echo ' checked';
			    	echo '><label for="tipo3"><strong>Vídeo</strong></label>
		        </div>
	      </div>
		  <div class="row texto-options">
		    <div class="large-12 columns">
		      <label>Título
		        <input type="text" name="titulo" placeholder="Título" value="' . $arr['titulo'] . '" />
		      </label>
		    </div>
		    <div class="large-12 columns">
		      <label>Descrição
		        <textarea type="text" name="texto" placeholder="Texto">' . $arr['texto'] . '</textarea>
		      </label>
		    </div>
		    <div class="large-3 columns">
		      <label><i class="fa fa-eyedropper" aria-hidden="true"></i> Cor do texto
		        <input type="text" name="cor" placeholder="Cor do texto" value="' . $arr['cor'] . '" class="picker" />
		      </label>
		    </div>
			<div class="large-3 columns">
	  			<label><i class="fa fa-align-left" aria-hidden="true"></i> Alinhamento do texto
		            <select name="alinhamento">
			          <option value="text-center"'; if($arr['alinhamento'] == 'text-center' || $arr['alinhamento'] == '') echo ' selected'; echo '>Centralizado</option>
			          <option value="text-left"'; if($arr['alinhamento'] == 'text-left') echo ' selected'; echo '>Alinhado à Esquerda</option>
			          <option value="text-right"'; if($arr['alinhamento'] == 'text-right') echo ' selected'; echo '>Alinhado à Direita</option>
			        </select>
			    </label>
	        </div>
	        <div class="large-3 columns">
	  			<label><i class="fa fa-arrows-v" aria-hidden="true"></i> Box texto vertical
		            <select name="caixa_vertical">
			          <option value="vert-center"'; if($arr['caixa_vertical'] == 'vert-center' || $arr['caixa_vertical'] == '') echo ' selected'; echo '>Centro</option>
			          <option value="vert-top"'; if($arr['caixa_vertical'] == 'vert-top') echo ' selected'; echo '>Superior</option>
			          <option value="vert-bottom"'; if($arr['caixa_vertical'] == 'vert-bottom') echo ' selected'; echo '>Inferior</option>
			        </select>
			    </label>
	        </div>
	        <div class="large-3 columns">
	  			<label><i class="fa fa-arrows-h" aria-hidden="true"></i> Box texto horizontal
		            <select name="caixa_horizontal">
			          <option value="hor-center"'; if($arr['caixa_horizontal'] == 'hor-center' || $arr['caixa_horizontal'] == '') echo ' selected'; echo '>Centro</option>
			          <option value="hor-left"'; if($arr['caixa_horizontal'] == 'hor-left') echo ' selected'; echo '>Esquerda</option>
			          <option value="hor-right"'; if($arr['caixa_horizontal'] == 'hor-right') echo ' selected'; echo '>Direita</option>
			        </select>
			    </label>
	        </div>
	        
		  </div>
		</fieldset>
		<section id="fs-video"'; if($queryAux == 'I') echo ' style="display:none;"'; echo '>
			<fieldset>
			  <legend>Vídeo</legend>
			  <div class="row">
			    <div class="large-6 columns file-box">'
			    	. $admin->inputFileWOptions('Vídeo .mp4','video_mp4','titulo',$db_table,$arr)
			    	. '
			    </div>
			    <div class="large-6 columns file-box">'
			    	. $admin->inputFileWOptions('Vídeo .ogv','video_ogv','titulo',$db_table,$arr)
			    	. '
			    </div>
			    <div class="large-6 columns file-box">'
			    	. $admin->inputFileWOptions('Vídeo .webm','video_webm','titulo',$db_table,$arr)
			    	. '
			    </div>
			    <div class="large-6 columns image-box">'
			    . $admin->inputImageWOptions('Imagem de Capa','imagem_capa','titulo','img_capa',$db_table,$arr,$tamanho)
			    . '
			    </div>
			  </div>
			</fieldset>
			<fieldset>
				<legend>Frase</legend>
				<div class="row">
					<div class="large-12 columns">
		      			<label>Texto
		      				<textarea id="frase" name="frase" cols="80" rows="10" class="ckeditor">' . $arr['frase'] . '</textarea>
		      			</label>
		    </div>
		    	</div>
			</fieldset>
		</section>
		<section id="fs-imagem"'; if($queryAux != 'I') echo ' style="display:none;"'; echo '>
	    	<fieldset>
			  <legend>Imagens</legend>
			  <div class="row">
			    <div class="large-6 columns image-box">'
			    . $admin->inputImageWOptions('<i class="fa fa-desktop" aria-hidden="true"></i> Imagem Desktop & Tablet','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
			    . '
			    </div>
			    <div class="large-6 columns image-box">'
			    . $admin->inputImageWOptions('<i class="fa fa-mobile" aria-hidden="true"></i> Imagem Smartphone','imagem_mobile','titulo','img_mobile1,img_mobile2,img_mobile3',$db_table,$arr,$tamanho)
			    . '
			    </div>
			    <!--<div class="large-6 columns image-box">'
			    . $admin->inputImageWOptions('Imagem logo','imagem_logo','titulo','img_logo1',$db_table,$arr,$tamanho)
			    . '
			    </div>-->
			  </div>
			</fieldset>
			<fieldset>
			  <legend><i class="fa fa-external-link" aria-hidden="true"></i> Link</legend>
			  <div class="row">
				    <div class="large-3 columns">
				    	<input name="do_link" id="do_link" type="checkbox" value="Y"';
				    	if($arr['do_link'] == 'Y') echo ' checked';
				    	echo '><label for="do_link"><strong>Ativo</strong></label>
			      </div>
	    	</div>
			  <div class="row do_link">
			    <div class="large-9 columns">
			      <label>Link
			        <input type="text" name="link" placeholder="Link" value="' . $arr['link'] . '" />
			      </label>
			    </div>
	  			<div class="small-3 columns">
	  				<label>Abrir em
			            <select name="target">
				          <option value="_top"'; if($arr['target'] == '_top') echo ' selected'; echo '>Mesma aba</option>
				          <option value="_blank"'; if($arr['target'] == '_blank') echo ' selected'; echo '>Nova aba</option>
				        </select>
				    </label>
	    	    </div>
			  </div>
			  <div class="row do_link">
			    <div class="large-9 columns">
			      <label>Texto do Botão
			        <input type="text" name="textoLink" placeholder="Texto do Botão" value="' . $arr['textoLink'] . '" />
			      </label>
			    </div>
			  </div>
			</fieldset>
		</section>
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
	global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $_FILES,$tamanho;
	//editando pre-vars
	$dontPost[] = 'permissoes'; $dontPost[] = 'imagem'; $dontPost[] = 'imagem_mobile'; $dontPost[] = 'video_mp4'; $dontPost[] = 'video_ogv'; $dontPost[] = 'video_webm';
	if(empty($_POST['tipo'])) $_POST['tipo'] = 'I';
	if(empty($_POST['do_link'])) $_POST['do_link'] = 'N';
	//tratando vars enviadas
	$campos=''; $valores=''; $c=0;
	foreach ($_POST as $key => $value)
	{
		//restrições
		$show=true;
		if(in_array($key,$dontPost)) $show=false;
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
		$campos_original = array('img1','img2','img3');
		foreach ($campos_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";			
		}
	}
	if (!empty($_FILES['imagem_mobile']['name']))
	{
		$campos_original = array('img_mobile1','img_mobile2','img_mobile3');
		foreach ($campos_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem_mobile']['name'],$_FILES['imagem_mobile']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";			
		}
	}
	if (!empty($_FILES['imagem_capa']['name']))
	{
		$campos_original = array('img_capa');
		foreach ($campos_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem_capa']['name'],$_FILES['imagem_capa']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";			
		}
	}
	if (!empty($_FILES['imagem_logo']['name']))
	{
		$campos_original = array('img_logo1');
		foreach ($campos_original as $v)
		{
			$img = $arq->Imagem($on,$_FILES['imagem_logo']['name'],$_FILES['imagem_logo']['tmp_name'],'',$tamanho[$v]);
			$valores .= ",'" . $img . "'";
			$campos .= "," . $v;
			$update .= "," . $v . "='" . $img . "'";			
		}
	}
	if (!empty($_FILES['video_mp4']['name']))
	{
		$pdf = $arq->Upload($on,$_FILES['video_mp4']['name'],$_FILES['video_mp4']['tmp_name']);
		$campos .= ",video_mp4";
		$valores .= ",'" . $pdf . "'";
		$update .= ",video_mp4='" . $pdf . "'";
	}
	if (!empty($_FILES['video_ogv']['name']))
	{
		$pdf = $arq->Upload($on,$_FILES['video_ogv']['name'],$_FILES['video_ogv']['tmp_name']);
		$campos .= ",video_ogv";
		$valores .= ",'" . $pdf . "'";
		$update .= ",video_ogv='" . $pdf . "'";
	}
	if (!empty($_FILES['video_webm']['name']))
	{
		$pdf = $arq->Upload($on,$_FILES['video_webm']['name'],$_FILES['video_webm']['tmp_name']);
		$campos .= ",video_webm";
		$valores .= ",'" . $pdf . "'";
		$update .= ",video_webm='" . $pdf . "'";
	}
	//gravando informações no banco
	if(empty($_POST['id'])) //se vier de um form de inclusão
	{
		mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
		$id = mysql_insert_id();
		$admin->saveLog('inseriu',"ID: " . $id);
	}else
	{
		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$id = $_POST['id'];
		$admin->saveLog('editou',"ID: " . $id);
	}
	//redirecionando página
	header('Location: index.php?on=' . $on);
}

function Apagar($id)
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
		  	<input type="hidden" name="in" value="apagar" />
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
		$admin->saveLog('apagou',"Nome: " . $arr['titulo'] . " / ID: " . $arr['id']);
		unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr['img1']);
		unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr['img2']);
		unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr['img3']);
		unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr['img_mobile1']);
		unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr['img_mobile2']);
		//redirecionando página
		header('Location: index.php?on=' . $on);
	}	
}

function Apagar_Imagem($id,$campos)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	
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
		$campos = explode(',',$_POST['campos']);
		foreach ($campos as $key => $value)
		{
			mysql_query("UPDATE " . $db_table . " SET " . $value . "='' WHERE id='" . $_POST['id'] . "'");
			unlink($config['site-raiz'] . 'img/' . $on . '/' . $arr[$value]);
		}
		
		//redirecionando página
		header('Location: index.php?on=' . $on . '&in=editar&id=' . $_POST['id']);
	}	
}

function updateTipo()
{
	global $admin, $on, $in;
	
	isset($_GET['newTipo']) ? $atual = $_GET['newTipo'] : $atual = 'V';

	echo $atual;
	echo "UPDATE slider_option SET valor='".$atual."' WHERE id=1";
	mysql_query("UPDATE slider_option SET valor='".$atual."' WHERE id='1'");
}

function updatemenu()
{
	global $admin, $on, $in;
	$admin->saveLog('alterou a ordem do menu','');
	foreach ($_POST['neworder'] as $key => $value)
	{
		mysql_query("UPDATE slider SET ordem='" . $key . "' WHERE id='" . $value . "'");
	}
}

function checkMySql()
{
	global $db_table;
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
					  `titulo` varchar(180) NOT NULL DEFAULT '',
					  `texto` text NOT NULL,
					  `lado` enum('L','R') NOT NULL,
					  `cor` varchar(100) NOT NULL,
					  `do_link` enum('Y','N') NOT NULL,
					  `link` varchar(30) NOT NULL,
					  `target` enum('_top','_blank') NOT NULL,
					  `tipo` enum('T','I') NOT NULL,
					  `status` enum('A','I') NOT NULL,
					  `img1` varchar(255) NOT NULL,
					  `img2` varchar(255) NOT NULL,
					  `img3` varchar(255) NOT NULL,
					  `img_mobile1` varchar(255) NOT NULL,
					  `img_mobile2` varchar(255) NOT NULL,
					  `img_mobile3` varchar(255) NOT NULL,
					  `ordem` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
	}
}

function showCode($id)
{
	echo '
		<div class="row show-code">
			<div class="columns large-12">
				<a href="javascript:void(0);" class="open-code right"><i class="fa fa-code" aria-hidden="true"></i>
</a>
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
							'//slider'."\r\n"
							.'$slider = \'\'; $c=0;'."\r\n"
							.'$rr = mysql_query("SELECT * FROM slider ORDER by ordem");'."\r\n"
							.'$total = mysql_num_rows($rr);'."\r\n"
							.'while($arr = mysql_fetch_array($rr))'."\r\n"
							.'{'."\r\n"
							.'  $c++; if($total == $c) $class="end";'."\r\n"
							.'  $slider .= \''."\r\n"
							.'  <li>'."\r\n"
							.'    <a href="img/' . $on . '/\' . $arr[\'img2\'] . \'" class="fancybox" rel="slider_id' . $id . '">'."\r\n"
							.'        <img src="img/' . $on . '/\' . $arr[\'img1\'] . \'" alt="\' . $arr[\'titulo\'] . \'" />'."\r\n"
							.'      </a>'."\r\n"
							.'    </div>'."\r\n"
							.'  </li>\';'."\r\n"
							.'}'."\r\n"
							.'Parser::__alloc("slider",$slider);';
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>';
							$codigo = 
							'<!--slider-->'."\r\n"
							.'<ul class="orbit_slider" data-orbit>'."\r\n"
							.'  <var name="orbit_slider" />'."\r\n"
							.'</ul>'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-3">
				    <pre>
						<code>';
							$codigo = 
							'#galeria_id' . $filtro_imagens . ' .crop'."\r\n"
							.'{'."\r\n"
							.'  height:200px; /* altura de acordo com o design */'."\r\n"
							.'  overflow:hidden;'."\r\n"
							.'}'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-4">
				    <p>Não é necessário, desde que haja a chamada dentro do /js/events.js, o que é padrão.</p>
				  </div>
				</div>
			</div>
		</div>';
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
	case "updatemenu";
	updatemenu();
	break;

	case "updateTipo";
	updateTipo();
	break;
}