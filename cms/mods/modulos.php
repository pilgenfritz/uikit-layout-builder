<?php
$db_table = 'admins_mods';

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	
	$query = "SELECT * FROM " . $db_table . " WHERE ativo='Y' ORDER BY tipo, nome";
	$admin->breadcrumbs();
	$admin->pageTitle();
	
	echo '
	<div class="row">
		<div class="columns large-12">
			<h4>Módulos ativos</h4>';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<thead>
						<tr>
							<th width="300">Nome</th>
							<th class="hide-for-small">Descrição</th>
							<th width="100">Menu</th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr>
							<td>
								<a data-tooltip href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '" class="has-tip" title="/cms/mods/' . $arr['modulo'] . '.php">'
								 . $arr['nome'] . '
								</a>';
								if(empty($arr['copia_de']))
								{
									echo '
									<a href="index.php?on=' . $on . '&in=duplicar&id=' . $arr['id'] . '" class="label active radius right">duplicar</a>';
								}else
								{
									echo '
								 	<span class="label secondary radius right" style="margin-left:15px;">Cópia de ' . $arr['copia_de'] . '.</span>';
								}
							echo '
							</td>
							<td class="hide-for-small">
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['descricao'] . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['tipo'] . '</a>
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
	echo '
	<div class="row">
		<div class="columns large-12">
			<h4>Módulos inativos</h4>';
			$query = str_replace("'Y'","'N'",$query);
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<thead>
						<tr>
							<th width="300">Nome</th>
							<th class="hide-for-small">Descrição</th>
							<th width="100">Menu</th>
						</tr>
					</thead>
					<tbody>';
					$rr = mysql_query($query);
					while ($arr = mysql_fetch_array($rr))
					{
						echo '
						<tr>
							<td>
								<a data-tooltip href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '" class="has-tip" title="/cms/mods/' . $arr['modulo'] . '.php">' . $arr['nome'] . '</a>
								<a href="index.php?on=' . $on . '&in=activateMod&mod=' . $arr['modulo'] . '&print=Y" class="label active radius right">ativar</a>
							</td>
							<td class="hide-for-small">
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['descricao'] . '</a>
							</td>
							<td>
								<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['id'] . '">' . $arr['tipo'] . '</a>
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
			echo '</div>
	</div>';
}

function Form($id)
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;
	$admin->breadcrumbs();
	$admin->pageTitle();
	if($in != 'novo')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM " . $db_table . " WHERE id='" . $id . "' LIMIT 1"));
		if($in == 'editar')
		{
			$inputId = '
			<input type="hidden" name="id" value="' . $arr['id'] . '" />';
		}
		elseif($in == 'duplicar')
		{
			$inputId .= '
			<input type="hidden" name="descricao" value="' . $arr['descricao'] . '" />
			<input type="hidden" name="copia_de" value="' . $arr['modulo'] . '" />';
			unset($arr['nome']); unset($arr['modulo']); unset($arr['tabela']);
		}
	}
	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '">
	  	<input type="hidden" name="in" value="salvar" />
	  	' . $inputId . '
	    <fieldset>
		  <legend>Formulário</legend>
		  <div class="row">
			  <div class="large-6 columns end">
			      <br /><label for="ativo" class="margintop10">
			      <input type="checkbox" name="ativo" id="ativo" value="Y"'; if($arr['ativo'] == 'Y') echo ' checked'; echo ' />
			      Módulo Ativo <span class="disabled"> (Visível no menu do CMS)</span>
			      </label>
			  </div>
			  <div class="large-6 columns">
		        <label>Menu
		          <select name="tipo">
		            <option value="user"'; if($arr['tipo'] == 'user') echo ' selected'; echo '>Módulo (Usuários)</option>
		            <option value="config"'; if($arr['tipo'] == 'config') echo ' selected'; echo '>Configuração</option>
		            <option value="dev"'; if($arr['tipo'] == 'dev') echo ' selected'; echo '>Desenvolvedor</option>
		          </select>
		        </label>
		      </div>
		  </div>
		  <div class="row">
		  	  <div class="large-6 columns">
		        <label>Nome
		          <input type="text" name="nome" id="nome" placeholder="Nome" value="' . $arr['nome'] . '" required />
		        </label>
		      </div>
		      <div class="large-6 columns">
			      <label>Submenu
			        <input type="text" name="submenu" placeholder="Links Submenu" value="' . $arr['submenu'] . '" required />
			      </label>
			    </div>
		      
		  </div>
		  <div class="row">
			  <div class="large-6 columns">
		  		<label>Arquivo do módulo
				  <div class="row collapse">
				    <div class="small-4 large-4 columns">
				      <span class="prefix">/cms/mods/</span>
				    </div>
				    <div class="small-5 large-6 columns">
				      <input type="text" name="modulo" id="modulo" placeholder="Arquivo" value="' . $arr['modulo'] . '" required '; if(!empty($arr['modulo']) && ($arr['tipo']=='dev' || $arr['tipo']=='config')) echo ' disabled'; if(empty($arr['modulo'])) echo ' class="vazio"'; echo '/>
				    </div>
				    <div class="small-3 large-2 columns end">
				      <span class="postfix">.php</span>
				    </div>
				  </div>
				</label>
		      </div>
		    <div class="large-6 columns end">
			      <label>Ícone (<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>)
			        <input type="text" name="faicon" id="faicon" placeholder="Ícone da Font Awesome" value="' . $arr['faicon'] . '" required'; if(empty($arr['modulo'])) echo ' class="vazio"'; echo '/>
			      </label>
			    </div>
		    
		  </div>
		  <div class="row">
		    <div class="large-6 columns">
		      <label>Tabela MySQL
		        <input type="text" name="tabela" id="tabela" placeholder="Tabela do Banco de Dados" value="' . $arr['tabela'] . '" required'; if(empty($arr['modulo'])) echo ' class="vazio"'; echo '/>
		      </label>
		    </div>
		    <div class="large-6 columns">
		      <label>Descrição
		        <input type="text" name="descricao" placeholder="Descrição do módulo" value="' . $arr['descricao'] . '" required'; if(!empty($arr['copia_de']) || $in == 'duplicar') echo ' disabled'; echo ' />
		      </label>
		    </div>
		  </div>
		</fieldset>
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">';
			 	if(!empty($arr['id']) && !empty($arr['copia_de'])) echo '<a href="index.php?on=' . $on . '&in=apagar&id=' . $arr['id'] . '" class="button alert marginright10 left hide-for-small">Apagar <i class="fa fa-trash-o" aria-hidden="true"></i></a>';
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
	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;

	if(empty($_POST['ativo'])) $_POST['ativo'] = 'N';
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
			if(!empty($_POST['id'])) $update .= " $key='" . $value . "'"; //se vier do form editar, add na var update
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

function push_options()
{
	if($_GET['mostrar'] == 'Y')
	{
		mysql_query("INSERT INTO admins_mods_options (id, modulo, campo, mostrar) VALUES (NULL,'" . $_GET['modulo'] . "','" . $_GET['campo'] . "','Y') ") or die($admin->alertMysql(mysql_error()));
	}else
	{
		mysql_query("DELETE FROM admins_mods_options WHERE modulo='" . $_GET['modulo'] . "' && campo='" . $_GET['campo'] . "'") or die($admin->alertMysql(mysql_error()));
	}
}

function disableMod()
{
	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;
	mysql_query("UPDATE " . $db_table . " SET ativo='N' WHERE modulo='" . $_GET['mod'] . "'") or die($admin->alertMysql(mysql_error()));
}

function activateMod()
{
	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;
	mysql_query("UPDATE " . $db_table . " SET ativo='Y' WHERE modulo='" . $_GET['mod'] . "'") or die($admin->alertMysql(mysql_error()));

	//redirecionando página
	header('Location: index.php?on=' . $on);
}

function useless_tables()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	
	$query = "SHOW TABLES FROM dev_base";
	$admin->breadcrumbs();
	$admin->pageTitle();

	//listando tabelas ativas
	$active_tables = array();
	$rr = mysql_query("SELECT * FROM admins_mods WHERE ativo='Y'");
	while ($arr = mysql_fetch_array($rr))
	{
		if(empty($arr['tabela'])) $active_tables[] = $arr['modulo'];
		else $active_tables[] = $arr['tabela'];
	}
	
	echo '
	<div class="row">
		<div class="columns large-12">';
			if(mysql_num_rows(mysql_query($query)) > 0)
			{
				echo '
				<table class="list-table">
					<tbody>';
					$c=0;
					$rr = mysql_query($query);
					while($arr = mysql_fetch_array($rr))
					{
						$show = true;
						if(ereg('admins',$arr[0]) || ereg('config_',$arr[0])) $show = false;

						if(ereg('_',$arr[0])){
							$nome_temp = explode('_',$arr[0]);
							if(in_array($nome_temp[0],$active_tables)) $show = false;
						}
						elseif(in_array($arr[0],$active_tables)) $show = false;


						if($show)
						{
							$c++;
							echo '
							<tr>
								<td>' . $arr[0] . '</td>
							</tr>';
						}
					}
					echo '
					</tbody>
				</table>';
			}
			if($c == 0)
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

switch($in)
{
	default;
	Main();
	break;
	
	case "novo";
	case "duplicar";
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
	
	case "useless_tables";
	useless_tables();
	break;	

	case "push_options";
	push_options();
	break;	

	case "disableMod";
	disableMod();
	break;	

	case "activateMod";
	activateMod();
	break;	
}