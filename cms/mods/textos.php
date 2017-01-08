<?php
$db_table = 'textos';

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	if($_GET['filtro'] != ''){ $filtro = $_GET['filtro']; }
	elseif(isset($_GET['filtro']) && empty($_GET['filtro'])){ $filtro = ''; }
	elseif($_SESSION['filtro'] != ''){ $filtro = $_SESSION['filtro']; }
	$_SESSION['filtro'] = $filtro;
	
	if(!empty($filtro)) $asqw=" WHERE pagina='" . $filtro . "'";
	$query = "SELECT * FROM " . $db_table .  $asqw . " ORDER BY";
	if($filtro == '') $query .= " pagina, caminho"; else $query .= " caminho";
	$admin->breadcrumbs();
	$admin->pageTitle();
	checkMySql();
	
	echo '
	<div class="row">
		<div class="columns large-2 text-left">';
		if($admin->isDeveloper()) echo '<button id="zerar-banco" style="float:left;">Zerar</button>';
		echo '</div>
		<div class="columns large-6 text-right margintop10 marginbottom10">Filtrar por página</div>
		<div class="columns large-4">
			<select id="filterPages" name="filtro" required>';
	        if(empty($arr['pagina']))
	        {
	        	echo '
	          	<option value="">-</option>';
	        }
	        $r2 = mysql_query("SELECT * FROM config_pages WHERE ativa='Y' && link='Y' ORDER by nome");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	          echo '
	          <option value="' . $ar2['page'] . '"'; if($filtro == $ar2['page']) echo ' selected'; echo '>' . $ar2['nome'] . ' ' . $ar2['subnome'] . '</option>';
	        }
	        echo '
	        </select>
		</div>
	</div>
	<div class="row">
		<div class="columns large-12">';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<thead>
						<tr>';
							if($filtro == '') echo '
							<th style="width:200px;">Página</th>';
							echo '
							<th>Título</th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr>';
							if($filtro == '') 
							{
								list($pagina_nome) = mysql_fetch_row(mysql_query("SELECT nome FROM config_pages WHERE page='" . $arr['pagina'] . "' LIMIT 1"));
								echo '
								<td>
									<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $pagina_nome . '</a>
								</td>';
							}
							echo '
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['caminho'] . '</a>
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
		if($admin->isDeveloper())
		{
			showCode($id);
		}
	}
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="id" value="' . $arr['id'] . '" />
	    <fieldset>
		  <legend>Formulário</legend>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Página
		        <select name="pagina" required>';
		        if(empty($arr['pagina']))
		        {
		        	echo '
		          	<option value="">[Selecione uma página]</option>';
		        }
		        $r2 = mysql_query("SELECT * FROM config_pages WHERE ativa='Y' && link='Y' ORDER by nome");
		        while($ar2 = mysql_fetch_array($r2))
		        {
		        	if(empty($arr['pagina'])) $select = $_SESSION['filtro']; else $select = $arr['pagina'];
		          echo '
		          <option value="' . $ar2['page'] . '"'; if($select == $ar2['page']) echo ' selected'; echo '>' . $ar2['nome'] . ' ' . $ar2['subnome'] . '</option>';
		        }
		        echo '
		        </select>
		      </label>
		    </div>
		  </div>';
			  if($arr['caminho'] != '[disabled]')
			  {
				  echo '
				  <div class="row">
				    <div class="large-12 columns">
				      <label>Índice';
				      	if($admin->isDeveloper() && $in == 'editar')
					    {
					      	echo '
					        <a class="disableField" href="index.php?on=textos&in=disableField&id=' . $arr['id'] . '&field=caminho&print=Y"><i class="fa fa-trash" aria-hidden="true"></i></a>';
					    }
					    echo '
				        <input type="text" name="caminho" placeholder="Local (Conteúdo)" value="' . $arr['caminho'] . '" required />
				      </label>
				    </div>
				  </div>';
			  }
		  if($arr['titulo'] != '[disabled]')
		  {
			  echo '
			  <div class="row">
			    <div class="large-12 columns">
			      <label>Título';
			      if($admin->isDeveloper() && $in == 'editar')
			      {
			      	echo '
			        <a class="disableField" href="index.php?on=textos&in=disableField&id=' . $arr['id'] . '&field=titulo&print=Y"><i class="fa fa-trash" aria-hidden="true"></i></a>';
			      }
			        echo '<input type="text" name="titulo" placeholder="Título" value="' . $arr['titulo'] . '" required />
			      </label>
			    </div>
			  </div>';
		  }
		  if($arr['texto'] != '[disabled]')
		  {
			  echo '
			  <div class="row">
			    <div class="large-12 columns">
			      <label>Texto';
			      	if($admin->isDeveloper() && $in == 'editar')
				    {
				      	echo '
				        <a class="disableField" href="index.php?on=textos&in=disableField&id=' . $arr['id'] . '&field=texto&print=Y"><i class="fa fa-trash" aria-hidden="true"></i></a>';
				    }
				    echo '
			      	<textarea id="texto" name="texto" cols="80" rows="10" class="ckeditor">' . $arr['texto'] . '</textarea>
			      </label>
			    </div>
			  </div>';
		  }
		  if($admin->isDeveloper())
		  {
		  	  echo '<br/>
			  <div class="row">
			    <div class="large-12 columns">
			      <label>Template de preenchimento para o usuário
			      	<textarea id="instrucoes" name="instrucoes" cols="80" rows="10" class="ckeditor">' . $arr['instrucoes'] . '</textarea>
			      </label>
			    </div>
			  </div>';
		  }else
		  {
		  	if(!empty($arr['instrucoes']))
		  	{
		  	  echo '
		  	  <br/>
			  <div class="row instrucoes">
			    <div class="large-12 columns">
			      <div class="panel">
			      	<span class="right fechar">&times;</span>
			      	<p class="titulo">Modelo para preenchimento</p>
			      	' . $arr['instrucoes'] . '
			      </div>
			    </div>
			  </div>';
		  	}
		  }
		  echo '
		</fieldset>
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">
				<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar <i class="fa fa-times" aria-hidden="true"></i></a>
		    	<button type="submit">' . $admin->formButton() . '</button>
			</div>
		</div>
	  </form>
	</div>';
}

function Salvar()
{
	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;
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
	//gravando informações no banco
	if(empty($_POST['id'])) //se vier de um form de inclusão
	{
		mysql_query("INSERT INTO " . $db_table . " (id," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
		$id = mysql_insert_id();
	}else
	{
		mysql_query("UPDATE " . $db_table . " SET " . $update . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$id = $_POST['id'];
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
			    	<h4>' . $arr['nome'] . '</h4>
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
		mysql_query("DELETE FROM " . $db_table . " WHERE id='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		//redirecionando página
		header('Location: index.php?on=' . $on);
	}	
}

function checkMySql()
{
	global $db_table;
	if(mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $db_table . "'")) == 0)
	{
		mysql_query("CREATE TABLE IF NOT EXISTS `" . $db_table . "` (
					  `id` int(5) NOT NULL AUTO_INCREMENT,
					  `titulo` varchar(255) NOT NULL,
					  `texto` text NOT NULL,
					  `pagina` varchar(255) NOT NULL,
					  `instrucoes` text NOT NULL,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
	}
}

function showCode($id)
{
	global $db_table, $dados;
	$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "'"));
	echo '
		<div class="row show-code">
			<div class="columns large-12">
				<a href="javascript:void(0);" class="open-code right"><i class="fa fa-code" aria-hidden="true"></i>
</a>
				<dl class="tabs show-code" data-tab>
				  <dd class="active"><a href="#panel2-1">PHP</a></dd>
				  <dd><a href="#panel2-2">HTML</a></dd>
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
				    <pre>
						<code>';
							$codigo = 
							'//Texto: ' . utf8_decode($arr['titulo'])."\r\n"
							.'list($texto_' . $dados->create_slug_(trim($arr['titulo'])) . ') = mysql_fetch_row(mysql_query("SELECT texto FROM ' . $db_table . ' WHERE id=\'' . $id . '\'"));'."\r\n"
							.'Parser::__alloc("texto_' . $dados->create_slug_(trim($arr['titulo'])) . '",$texto_' . $dados->create_slug_(trim($arr['titulo'])) . ');';
							echo htmlentities($codigo) . '
						</code>
					</pre>
				  </div>
				  <div class="content" id="panel2-2">
				    <pre>
						<code>';
							$codigo = 
							'<!--Texto: ' . $arr['titulo']."-->\r\n"
							.'<div class="row">'."\r\n"
							.'  <div class="columns large-12 twelve">'."\r\n"
							.'    <var name="texto_' . $dados->create_slug_($arr['titulo']) . '" />'."\r\n"
							.'  </div>'."\r\n"
							.'</div>'."\r\n";
							echo htmlentities($codigo) . '
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
	header('Location: index.php?on=' . $on);
}

function disableField()
{
	global $db_table, $on, $admin;
	
	mysql_query("UPDATE " . $db_table . " SET " . $_GET['field'] . "='[disabled]' WHERE id='" . $_GET['id'] . "'") or die($admin->alertMysql(mysql_error()));

	header('Location: index.php?on=textos&in=editar&id=' . $_GET['id']);
}

switch($in)
{
	default;
	Main();
	break;

	case "truncate";
	truncate();
	break;

	case "disableField";
	disableField();
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
}