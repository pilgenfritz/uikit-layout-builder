<?php
$db_table = 'config_general';

function Form()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table;

	if(empty($in)) $in = 'header';
	
	$query = "SELECT * FROM " . $db_table . " WHERE tipo='layout' && chave LIKE '%" . $in . "%' ORDER BY ordem";
	$admin->breadcrumbs();
	$admin->pageTitle();

	echo '
	<form id="layout-options" method="post" action="index.php?on=' . $on . '&in=salvar">
	<input type="hidden" name="in_from" value="' . $in . '">';
	$rr = mysql_query($query);
	$nr = mysql_num_rows($rr);
	while ($arr = mysql_fetch_array($rr))
	{
		/*if(is_html($arr['valor'])) $arr['valor']='<span class="disabled">[Conteúdo HTML]</span>';
		if(empty($arr['valor'])) $arr['valor']='<span class="disabled">(vazio)</span>';*/
		
		echo '
		<div class="row layout-options ' . $arr['chave'] . '">
			<div class="columns large-6">' . $arr['descricao'] . '</div>
			<div class="columns large-6">';
			if($arr['campo'] == 'checkbox')
			{
				echo '
				<div class="switch">
				  <input type="hidden" name="' . $arr['chave'] . '" value="N" />
				  <input id="' . $arr['chave'] . '" name="' . $arr['chave'] . '" type="checkbox" value="Y"'; if($arr['valor'] == 'Y') echo ' checked'; echo '>
				  <label for="' . $arr['chave'] . '"></label>
				</div>';
			}
			elseif($arr['campo'] == 'radio_img')
			{
				$arr['campo_options'] = explode('|', $arr['campo_options']);
				foreach ($arr['campo_options'] as $key => $value)
				{
					$data_this = explode(',',$value);
					echo '
					<label for="' . $data_this[0] . '" class="radio-img text-center">
					  <input id="' . $data_this[0] . '" name="' . $arr['chave'] . '" type="radio" value="' . $data_this[0] . '"'; if($arr['valor'] == $data_this[0]) echo ' checked'; echo '>
					  <img src="img/modelos/header_' . $data_this[0] . '.png" alt="">
				 	  <br/><span>' . $data_this[1] . '</span>
					</label>';
				}
			}
			elseif($arr['campo'] == 'radio')
			{
				$arr['campo_options'] = explode('|', $arr['campo_options']);
				foreach ($arr['campo_options'] as $key => $value)
				{
					$data_this = explode(',',$value);
					echo '
					<label for="' . $data_this[0] . '" class="radio-img">
					  <input type="radio" class="effeckt-rdio-ios7" id="' . $data_this[0] . '" name="' . $arr['chave'] . '" value="' . $data_this[0] . '"'; if($arr['valor'] == $data_this[0]) echo ' checked'; echo '>
				 	  <br/><span>' . $data_this[1] . '</span>
					</label>';
				}
			}
			elseif($arr['campo'] == 'color_picker')
			{
				echo '
				<input type="text" id="' . $arr['chave'] . '" name="' . $arr['chave'] . '" value="' . $arr['valor'] . '" class="picker" />';
			}
			elseif($arr['campo'] == 'input')
			{
				echo '
				<input type="text" id="' . $arr['chave'] . '" name="' . $arr['chave'] . '" value="' . $arr['valor'] . '" />';
			}
			elseif($arr['campo'] == 'number')
			{
				echo '
				<input type="number" id="' . $arr['chave'] . '" name="' . $arr['chave'] . '" value="' . $arr['valor'] . '" style="width:120px;" />';
			}
			elseif($arr['campo'] == 'range')
			{
				echo '
				<div class="range-slider" data-slider="' . $arr['valor'] . '" data-options="start: 0; end: 10;">
				  <span class="range-slider-handle" role="slider" tabindex="0"></span>
				  <span class="range-slider-active-segment"></span>
				  <input type="hidden" id="' . $arr['chave'] . '" name="' . $arr['chave'] . '" value="' . $arr['valor'] . '">
				</div>';
			}
			echo '
			</div>
		</div>';
	}
	if($nr == 0)
	{
		echo '
		<div class="row">
			<div class="columns large-12 text-center">
				<p>Nenhuma configuração para esta seção foi encontrada.</p>
			</div>
		</div>';
	}else
	{
		echo '
		<div class="row">
			<div class="columns large-12 text-right">
				<br/><button>Salvar alterações</button>
			</div>
		</div>';
	}
	echo '
	</form>';
}

function Salvar()
{
	global $admin, $admin_mods, $config, $on, $in, $dontPost, $db_table;

	//alterando no banco
	foreach ($_POST as $key => $value)
	{
		mysql_query("UPDATE config_general SET valor='" . $value . "' WHERE chave='" . $key . "'") or die($admin->alertMysql(mysql_error()));
	}

	//redirecionando página
	header('Location: index.php?on=' . $on . '&in_from=' . $_POST['in_from'] . '&confirm=update');
}

switch($in)
{
	default;
	Form();
	break;

	case "salvar";
	Salvar();
	break;

}