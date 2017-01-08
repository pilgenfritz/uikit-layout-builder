<?php
$db_table = 'frases';

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	if($_GET['filtro'] != ''){ $filtro = $_GET['filtro']; }
	elseif(isset($_GET['filtro']) && empty($_GET['filtro'])){ $filtro = ''; }
	elseif($_SESSION['filtro'] != ''){ $filtro = $_SESSION['filtro']; }
	$_SESSION['filtro'] = $filtro;
	
	if(!empty($filtro)) $asqw=" WHERE pagina='" . $filtro . "'";
	$query = "SELECT * FROM " . $db_table .  $asqw . " ORDER BY local";
	$admin->breadcrumbs();
	$admin->pageTitle();
	checkMySql();
	
	echo '
	<div class="row">
		<div class="columns large-8 text-right margintop10 marginbottom10">Filtrar por página</div>
		<div class="columns large-4">
			<select id="filterPages" name="filtro" required>';
	        if(empty($arr['pagina']))
	        {
	        	echo '
	          	<option value="">-</option>';
	        }
	        $r2 = mysql_query("SELECT * FROM config_pages WHERE ativa='Y' ORDER by nome");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	          echo '
	          <option value="' . $ar2['page'] . '"'; if($filtro == $ar2['page']) echo ' selected'; echo '>' . $ar2['nome'] . '</option>';
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
					<tbody>';
					$c=0;
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						$c++;
						$localAnt = $local[0];
						$local = explode(':',$arr['local']);

						if($local[0] != $localAnt || $c == 1)
						{
							echo thead($local[0],$filtro);
						}

						echo '
						<tr>';
							if(empty($filtro))
							{
								echo '
								<td>
									<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['pagina'] . '</a>
								</td>';
							}
							echo '
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . str_replace($local[0] . ': ','',$arr['local']) . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['frase'] . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['frase_en'] . '</a>
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
		        $r2 = mysql_query("SELECT * FROM config_pages WHERE ativa='Y' ORDER by nome");
		        while($ar2 = mysql_fetch_array($r2))
		        {
		        	if(empty($arr['pagina'])) $select = $_SESSION['filtro']; else $select = $arr['pagina'];
		          echo '
		          <option value="' . $ar2['page'] . '"'; if($select == $ar2['page']) echo ' selected'; echo '>' . $ar2['nome'] . '</option>';
		        }
		        echo '
		        </select>
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Local
		        <input type="text" name="local" placeholder="Local" value="' . $arr['local'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Frase (Português)
		        <input type="text" name="frase" placeholder="Frase" value="' . $arr['frase'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Frase (Inglês)
		        <input type="text" name="frase_en" placeholder="Frase" value="' . $arr['frase_en'] . '" required />
		      </label>
		    </div>
		  </div>
		</fieldset>
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">
				<a href="index.php?on=' . $on . '" class="button secondary marginright10">Cancelar</a>
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
				<a href="javascript:void(0);" class="open-code right"><img src="../img/cms/icon-code.png" alt="Mostrar código" class="absolute" /></a>
				<dl class="tabs show-code" data-tab>
				  <dd class="active"><a href="#panel2-1">PHP</a></dd>
				  <dd><a href="#panel2-2">HTML</a></dd>
				</dl>
				<div class="tabs-content show-code">
				  <div class="content active" id="panel2-1">
				    <pre>
						<code>';
							$codigo = 
							'//Frase: ' . utf8_decode($arr['frase'])."\r\n"
							.'$frase_' . $dados->create_slug_(trim($arr['frase'])) . ' = $Dados->getFrase(' . $id . ');'."\r\n"
							.'Parser::__alloc("frase_' . $dados->create_slug_(trim($arr['frase'])) . '",$frase_' . $dados->create_slug_(trim($arr['frase'])) . ');';
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

function thead($name,$filtro)
{
	$thead = '</tbody>
			  <thead>
			  	<tr><td colspan="4"><h2>'.$name.'</h2></td></tr>
				<tr>';
					if(empty($filtro))
					{
						$thead .= '
						<th width="100">Página</th>';
					}
					$thead .= '
					<th width="200">Local</th>
					<th>Português</th>
					<th>Inglês</th>
				</tr>
			  </thead>
			  <tbody>';

	return $thead;
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
}