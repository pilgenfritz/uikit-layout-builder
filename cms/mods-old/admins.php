<?php

function Main()
{
	global $admin, $admin_mods, $config, $on, $in;
	
	$query = "SELECT * FROM admins ORDER BY nome";
	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou','');
	
	echo '
	<div class="row">
		<div class="columns large-12">
			<table class="list-table">
				<thead>
					<tr>
						<th width="200">nome</th>
						<th class="hide-for-small">e-mail</th>
					</tr>
				</thead>
				<tbody>';
				$rr = mysql_query($query);
				while ($arr = mysql_fetch_array($rr))
				{
					echo '
					<tr>
						<td>
							<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['aid'] . '">' . $arr['nome'] . '</a>
						</td>
						<td class="hide-for-small">
							<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['aid'] . '">' . $arr['anome'] . '</a>
						</td>
					</tr>';
				}
				echo '
				</tbody>
			</table>
		</div>
	</div>';
}

function Form($id)
{
	global $admin, $admin_mods, $config, $on, $in;

	$admin->breadcrumbs();
	$admin->pageTitle();

	if($in == 'editar')
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM admins WHERE aid='" . $id . "' LIMIT 1"));
		$admin->saveLog('visualizou',"Nome: " . $arr['nome'] . " / ID: " . $arr['aid']);
	}

	echo '
	<div class="row">
	  <form method="post" action="index.php?on=' . $on . '">
	  	<input type="hidden" name="in" value="salvar" />
	  	<input type="hidden" name="aid" value="' . $arr['aid'] . '" />
	    <fieldset>
		  <legend>Dados Pessoais</legend>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>Nome Completo
		        <input type="text" name="nome" placeholder="Nome Completo" value="' . $arr['nome'] . '" required />
		      </label>
		    </div>
		  </div>
		  <div class="row">
		    <div class="large-12 columns">
		      <label>E-mail
		        <input type="text" name="anome" placeholder="E-mail" value="' . $arr['anome'] . '" required />
		      </label>
		    </div>
		  </div>
		</fieldset>';
		if($admin->super)
		{
			echo '
			<fieldset>
			  <legend>Permissões</legend>
			  <div class="row marginbottom20">
			    <div class="large-12 columns">

			    	<input id="super" name="super" type="checkbox" value="Y"';
			    	if($arr['super'] == 'Y') echo ' checked';
			    	echo '><label for="super"><strong>SuperUsuário</strong></label>

				    </div>
			    </div>

			  <div id="permissoes" class="row">
			    <div class="large-12 columns">';

			    $nomes = array(
			    				'user' => 'Módulos',
			    				'config' => 'Configurações',
			    				'dev' => 'Programador'
			    				);

			    foreach ($admin_mods as $key => $value)
			    {
			    	$list_mods[$admin_mods[$key]['tipo']][] = $key;
			    }

			    echo '<div class="row">';
			    foreach ($nomes as $k => $v)
			    {
			    	if($k == 'dev' && (!$admin->isDeveloper($arr['aid']) || $arr['aid'] == '')) break;

			    	echo '
			    		<div class="large-4 columns end">
					    	<span class="label secondary marginbottom10">' . $v . '</span>
					    	<div class="row">';
					    	foreach ($list_mods[$k] as $key => $value)
					    	{
							    echo '
							    <div class="large-12 columns">
							    	<input name="permissoes[]" id="' . $value . '" type="checkbox" value="' . $value . '"';
							    	if(mysql_num_rows(mysql_query("SELECT * FROM admins_permissoes WHERE aid='" . $arr['aid'] . "' && modulo='" . $value . "' && permissao='Y' LIMIT 1"))) echo ' checked';
							    	echo '><label for="' . $value . '">' . $admin_mods[$value]['titulo'] . '</label>
							    </div>';
					    	}
					    	echo '
					    	</div>
					    </div>';
			    }
			    echo '
			    	</div>
			    </div>
			  </div>
			</fieldset>';
		}
		echo '
		<fieldset>
		  <legend>Atualização de Senha</legend>
		  <div class="row">
		    <div class="large-4 columns">
		      <label>Senha
		        <input type="password" name="senha" placeholder="Senha" />
		    	<span class="secondary radius label margintop-10">Preencher somente caso queira editar a senha</span>
		      </label>
		    </div>
		    <div class="large-4 columns end">
		      <label>Repetir a Senha
		        <input type="password" name="senha" placeholder="Repetir a Senha" />
		      </label>
		    </div>
		  </div>
		</fieldset>';
		  if($admin->super)
		  {
		  	echo '
		  	</fieldset>
			<fieldset>
			  	<legend>Observações</legend>
			  	<div class="row">
				    <div class="large-12 columns">
				      <label>
				        <textarea name="obs" placeholder="Observações">' . $arr['obs'] . '</textarea>
				        <span class="secondary radius label margintop-10">Pode ser visualizada por todos os SuperUsuários</span>
				      </label>
				    </div>
				  </div>
			</fieldset>';
		  }
		  echo '
		<div class="row ">
			<div class="large-12 columns margintop20 text-right">';
			 	if(!empty($arr['aid'])) echo '<a href="index.php?on=' . $on . '&in=apagar&id=' . $arr['aid'] . '" class="button alert marginright10 left hide-for-small">Apagar <i class="fa fa-trash-o" aria-hidden="true"></i></a>';
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
	global $admin, $admin_mods, $config, $on, $in, $dontPost;

	//editando pre-vars
	$dontPost[] = 'permissoes';
	if(empty($_POST['super'])) $_POST['super'] = 'N';

	//tratando vars enviadas
	$campos=''; $valores=''; $c=0;
	foreach ($_POST as $key => $value)
	{
		//restrições
		$show=true;
		if(in_array($key,$dontPost)) $show=false;
		if($key=='senha' && empty($value)) $show=false;
		
		//se tudo ok, adiciona campo na lista
		if($show)
		{
			$c++;
			if($c>1){ $campos .= ','; $valores .= ','; $update .= ','; } //add virgulas antes dos campos

			$campos .= $key;
			
			if($key == 'senha' && !empty($value)) //se for senha, coloca em md5
			{
				$valores .= "'" . md5($value) . "'";
				if(!empty($_POST['aid'])) $update .= " $key='" . md5($value) . "'"; //se vier do form editar, add na var update
			}
			else
			{
				$valores .= "'$value'";
				if(!empty($_POST['aid'])) $update .= " $key='$value'"; //se vier do form editar, add na var update
			}
		}
	}

	//gravando informações no banco
	if(empty($_POST['aid'])) //se vier de um form de inclusão
	{
		mysql_query("INSERT INTO admins (aid," . $campos . ") VALUES (NULL," . $valores . ") ") or die($admin->alertMysql(mysql_error()));
		$aid = mysql_insert_id();
		$admin->saveLog('inseriu',"Nome: " . $_POST['nome'] . " / ID: " . $aid);
	}else
	{
		mysql_query("UPDATE admins SET " . $update . " WHERE aid='" . $_POST['aid'] . "'") or die($admin->alertMysql(mysql_error()));
		$aid = $_POST['aid'];
		$admin->saveLog('editou',"Nome: " . $_POST['nome'] . " / ID: " . $aid);
	}

	//alterando permisões
	mysql_query("DELETE FROM admins_permissoes WHERE aid='" . $aid . "'") or die($admin->alertMysql(mysql_error()));  //deleta todas as permissões atuais
	foreach ($_POST['permissoes'] as $value)
	{
		mysql_query("INSERT INTO admins_permissoes (id,aid,modulo,permissao) VALUES (NULL,'" . $aid . "','" . $value . "','Y')") or die($admin->alertMysql(mysql_error()));
	}

	//redirecionando página
	header('Location: index.php?on=' . $on);
}

function Apagar($id)
{
	global $admin, $admin_mods, $config, $on, $in;


	if(empty($_POST['conf']))
	{
		$admin->breadcrumbs();
		$admin->pageTitle();

		$arr = mysql_fetch_array(mysql_query("SELECT * FROM admins WHERE aid='" . $id . "'")) or die($admin->alertMysql("O Registro não existe."));

		echo '
		<div class="row">
		  <form method="post" action="index.php?on=' . $on . '">
		  	<input type="hidden" name="in" value="apagar" />
		  	<input type="hidden" name="id" value="' . $arr['aid'] . '" />
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
			    	<a href="index.php?on=' . $on . '&in=editar&id=' . $arr['aid'] . '" class="button secondary">Cancelar</a>
			    	<button type="submit" class="alert marginleft20">Confirmar exclusão</a>
				</div>
			  </div>
			</fieldset>				
		  </form>
		</div>';
	}
	else
	{
		$arr = mysql_fetch_array(mysql_query("SELECT * FROM admins WHERE aid='" . $_POST['id'] . "'")) or die($admin->alertMysql("O Registro não existe."));
		mysql_query("DELETE FROM admins WHERE aid='" . $_POST['id'] . "'") or die($admin->alertMysql(mysql_error()));
		$admin->saveLog('apagou',"Nome: " . $arr['nome'] . " / ID: " . $arr['aid']);

		//redirecionando página
		header('Location: index.php?on=' . $on);
	}	
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