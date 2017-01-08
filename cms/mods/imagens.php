<?php
global $admin;

//set default table
$db_table = $admin->getTabelaModulo($_GET['on']);

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	
	if($_GET['filtro_imagens'] != ''){ $filtro_imagens = $_GET['filtro_imagens']; }
	elseif(isset($_GET['filtro_imagens']) && empty($_GET['filtro_imagens']) ||
		   empty($_GET['filtro_imagens']) && empty($_SESSION['filtro_imagens'])){
			list($filtro_imagens) = mysql_fetch_row(mysql_query("SELECT id FROM imagens_cat ORDER BY titulo LIMIT 1"));
	}
	elseif($_SESSION['filtro_imagens'] != ''){ $filtro_imagens = $_SESSION['filtro_imagens']; }
	$_SESSION['filtro_imagens'] = $filtro_imagens;
	
	if($admin->isDeveloper())
	{
		showCode($filtro_imagens);
	}
	
	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou','');
	checkMySql();
	
	
	echo '
	<div class="row">
		<div class="columns large-8 text-right margintop10 marginbottom10">Categoria</div>
		<div class="columns large-4 filtro-settings">
			<select id="filterPages" name="filtro_imagens" required>';
			$c=0;
	        $r2 = mysql_query("SELECT * FROM imagens_cat ORDER by titulo");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	        	$c++;
	        	echo '
	        	<option value="' . $ar2['id'] . '"'; if($filtro_imagens == $ar2['id']) echo ' selected'; echo '>' . $ar2['titulo'] . '</option>';
	        }
	        echo '
	        </select>
			<a href="index.php?on=' . $on . '&in=categorias">
				<i class="fa fa-pencil-square-o settings right" aria-hidden="true"></i>
			</a>
		</div>
	</div>';	
	
	if(!empty($filtro_imagens)) $asqw=" WHERE cid='" . $filtro_imagens . "'";
	$query = "SELECT * FROM " . $db_table .  $asqw . " ORDER BY ordem";
	echo '
	<div class="row">
		<div class="columns large-12">';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table id="tabela_menu_img" class="list-table">
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
									<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">
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
		$admin->saveLog('visualizou',"Página: " . $arr['titulo'] . " / ID: " . $arr['id']);
		list($tamanho1,$tamanho2) = mysql_fetch_row(mysql_query("SELECT tamanho1,tamanho2 FROM " . $db_table . "_cat WHERE id='" . $arr['cid'] . "'"));
		$tamanho['img1'] = $tamanho1;
		$tamanho['img2'] = $tamanho2;
		$tamanho['img3'] = '0';
	}else
	{
		$arr['cid'] = $_SESSION['filtro_imagens'];
	}
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '" enctype="multipart/form-data">
	  	<input type="hidden" name="MAX_FILE_SIZE" value="10000000">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>
		  <legend>Imagem</legend>';
		  
		  if($in == 'novo')
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
			    <div class="large-6 columns image-box end">'
			    . $admin->inputImageWOptions('Imagem','imagem','titulo','img1,img2,img3',$db_table,$arr,$tamanho)
			    . '<br/><br/>
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
	  			<label>Categoria
		            <select name="cid">';
		            $r2 = mysql_query("SELECT * FROM imagens_cat ORDER by titulo");
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
	global $admin, $admin_mods, $config, $on, $in, $db_table, $dontPost, $_FILES;
	//editando pre-vars
	$dontPost[] = 'permissoes'; $dontPost[] = 'imagem';
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
	$c=0; $valores_inicial = $valores; $campos_inicial = $campos; $update_inicial = $update;
	$campos_original = array('img1','img2','img3');
	list($tamanho1,$tamanho2) = mysql_fetch_row(mysql_query("SELECT tamanho1,tamanho2 FROM imagens_cat WHERE id='" . $_POST['cid'] . "'"));
	$tamanho_galeria['img1'] = $tamanho1;
	$tamanho_galeria['img2'] = $tamanho2;
	$tamanho_galeria['img3'] = '0';
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
				mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
				$id = mysql_insert_id();
				$admin->saveLog('inseriu',"ID: " . $id);
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
		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$id = $_POST['id'];
		$admin->saveLog('editou',"ID: " . $id);
	}	
	//imagens
	/*$arq = New Arquivo;
	if (!empty($_FILES['imagem']['name']))
	{
		list($tamanho1,$tamanho2) = mysql_fetch_row(mysql_query("SELECT tamanho1,tamanho2 FROM imagens_cat WHERE id='" . $_POST['cid'] . "'"));
		$img1 = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho1);
		$img2 = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'',$tamanho2);
		$img3 = $arq->Imagem($on,$_FILES['imagem']['name'],$_FILES['imagem']['tmp_name'],'','0');
		$campos .= ",img1,img2,img3";
		$valores .= ",'" . $img1 . "','" . $img2 . "','" . $img3 . "'";
		$update .= ",img1='" . $img1 . "', img2='" . $img2 . "', img3='" . $img3 . "'";
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
	}*/
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

function categorias()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	$admin->breadcrumbs();
	$admin->pageTitle();
	
	$query = "SELECT * FROM " . $db_table . "_cat ORDER BY titulo";

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
				<table class="list-table">
					<thead>
						<tr>
							<th>Categorias</th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr>
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

function Form_cat($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	$admin->breadcrumbs();
	$admin->pageTitle();
	if($in == 'editar_cat')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $id . "' LIMIT 1"));
		$admin->saveLog('visualizou',"Categoria: " . $arr['titulo'] . " / ID: " . $arr['id']);
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
		      <label>Título
		        <input type="text" name="titulo" placeholder="Dê um nome para a categoria" value="' . $arr['titulo'] . '" />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		      <div class="large-3 columns">
		          <div class="row collapse">
				      <label>Tamanho da imagem menor</label>
				    <div class="small-9 columns">
				        <input type="text" name="tamanho1" placeholder="Digite um número" value="' . $arr['tamanho1'] . '" />
				    </div>
				    <div class="small-3 columns end">
				      <span class="postfix">pixels</span>
				    </div>
				  </div>
			  </div>
		  </div>
		  <div class="row">
		      <div class="large-3 columns">
		          <div class="row collapse">
				      <label>Tamanho da imagem maior</label>
				    <div class="small-9 columns">
				        <input type="text" name="tamanho2" placeholder="Digite um número" value="' . $arr['tamanho2'] . '" />
				    </div>
				    <div class="small-3 columns end">
				      <span class="postfix">pixels</span>
				    </div>
				  </div>
			  </div>
		  </div>
		</fieldset>
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">';
			 	if(!empty($arr['id'])) echo '<a href="index.php?on=' . $on . '&in=apagar_cat&id=' . $arr['id'] . '" class="button alert marginright10 left hide-for-small">Apagar <i class="fa fa-trash-o" aria-hidden="true"></i></a>';
			 	echo '
				<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar <i class="fa fa-times" aria-hidden="true"></i></a>
		    	<button type="submit">' . $admin->formButton() . '</button>
			</div>
		</div>
	  </form>
	</div>';
}

function Salvar_cat()
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
		mysql_query("INSERT INTO " . $db_table . "_cat (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
		$id = mysql_insert_id();
		$admin->saveLog('inseriu',"Categoria ID: " . $id);
	}else
	{
		mysql_query("UPDATE " . $db_table . "_cat SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$id = $_POST['id'];
		$admin->saveLog('editou',"Categoria ID: " . $id);
	}
	//redirecionando página
	header('Location: index.php?on=' . $on);
}

function Apagar_cat($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	if(empty($_POST['conf']))
	{
		$admin->breadcrumbs();
		$admin->pageTitle();
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));
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
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . "_cat WHERE id='" . $_POST['id'] . "'")) or die($admin->alertMysql("O Registro não existe."));
		mysql_query("DELETE FROM " . $db_table . "_cat WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$admin->saveLog('apagou',"Categoria: " . $arr['titulo'] . " / ID: " . $arr['id']);
		unset($_SESSION['filtro_imagens']);
		//redirecionando página
		header('Location: index.php?on=' . $on);
	}	
}

function showCode($filtro_imagens)
{
	global $on;
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
				  <dd><a href="#panel2-5">Opções</a></dd>
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
				    <pre>
						<code>';
							$codigo = 
							'//galeria de fotos'."\r\n"
							.'$galeria_' . $filtro_imagens . 'id = \'\'; $c=0;'."\r\n"
							.'$rr = mysql_query("SELECT * FROM imagens WHERE cid=\'' . $filtro_imagens . '\' ORDER by ordem");'."\r\n"
							.'$total = mysql_num_rows($rr);'."\r\n"
							.'while($arr = mysql_fetch_array($rr))'."\r\n"
							.'{'."\r\n"
							.'  $c++; if($total == $c) $class="end";'."\r\n"
							.'  $galeria_' . $filtro_imagens . 'id .= \''."\r\n"
							.'  <div class="columns large-4 four \' . $class . \'">'."\r\n"
							.'    <div class="crop">'."\r\n"
							.'      <a href="img/' . $on . '/\' . $arr[\'img2\'] . \'" class="various" rel="galeria_id' . $filtro_imagens . '">'."\r\n"
							.'        <img src="img/' . $on . '/\' . $arr[\'img1\'] . \'" alt="\' . $arr[\'titulo\'] . \'" title="\' . $arr[\'titulo\'] . \'"/>'."\r\n"
							.'      </a>'."\r\n"
							.'    </div>'."\r\n"
							.'  </div>\';'."\r\n"
							.'}'."\r\n"
							.'Parser::__alloc("galeria_' . $filtro_imagens . 'id",$galeria_' . $filtro_imagens . 'id);';
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>';
							$codigo = 
							'<!--galeria de fotos-->'."\r\n"
							.'<div id="galeria_' . $filtro_imagens . 'id" class="row">'."\r\n"
							.'  <var name="galeria_' . $filtro_imagens . 'id" />'."\r\n"
							.'</div>'."\r\n";
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-3">
				    <pre>
						<code>';
							$codigo = 
							'#galeria_' . $filtro_imagens . 'id .crop'."\r\n"
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
				  <div class="content" id="panel2-5">
				  	<button id="zerar-banco" style="float:left;">Zerar</button>
				  </div>
				</div>
			</div>
		</div>';
}

function updatemenu()
{
	global $admin, $on, $in;
	$admin->saveLog('alterou a ordem das imagens','');
	foreach ($_POST['neworder'] as $key => $value)
	{
		mysql_query("UPDATE imagens SET ordem='" . $key . "' WHERE id='" . $value . "'");
	}
}

function checkMySql()
{
	global $db_table;

	//cats
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` int(5) NOT NULL AUTO_INCREMENT,
					  `cid` int(5) NOT NULL,
					  `titulo` varchar(255) NOT NULL,
					  `img1` varchar(255) NOT NULL,
					  `img2` varchar(255) NOT NULL,
					  `img3` varchar(255) NOT NULL,
					  `destaque` enum('Y','N') NOT NULL DEFAULT 'N',
					  `ordem` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}

	//_imagens
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "_cat'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "_cat` (
					  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
					  `titulo` varchar(255) NOT NULL DEFAULT '',
					  `tamanho1` int(5) NOT NULL,
					  `tamanho2` int(5) NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
	}
}

function truncate()
{
	global $db_table, $on;
	mysql_query("TRUNCATE " . $db_table);
	mysql_query("TRUNCATE " . $db_table . "_cat");
	header('Location: index.php?on=' . $on);
}

switch($in)
{
	default;
	Main();
	break;

	case "truncate";
	truncate();
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
	case "categorias";
	categorias();
	break;
	case "nova_cat";
	case "editar_cat";
	Form_cat($id);
	break;
	case "inserir_cat";
	case "salvar_cat";
	Salvar_cat();
	break;
	case "apagar_cat";
	Apagar_cat($id);
	break;	
}