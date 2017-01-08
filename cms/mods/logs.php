<?php
$db_table = 'admins_acessos';

function Main()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	
	
	$query = "SELECT * FROM " . $db_table . " ORDER BY datacad DESC";
	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou',"Seção: " . $admin_mods[$on]['submenu'][$in]);
	
	echo '
	<div class="row">
		<div class="columns large-12">';
		if($admin->isDeveloper()) echo '<button id="zerar-banco">Zerar todos os Logs</button>';
		echo '
			<table class="list-table">
				<thead>
					<tr>
						<th width="120">Data</th>
						<th width="100">Horário</th>
						<th>Usuário</th>
						<th width="150" class="hide-for-small">IP</th>
					</tr>
				</thead>
				<tbody>';
				
				$max='50'; $pagIni = $admin->PaginacaoInit($query,$max,$_GET['pg']);
				$rr = mysql_query($query . ' LIMIT ' . $pagIni['c'] . ',' . $max);
				while ($arr = mysql_fetch_array($rr))
				{
					list($nome) = mysql_fetch_row(mysql_query("SELECT nome FROM admins WHERE aid='" . $arr['aid'] . "'"));
					echo '
					<tr>
						<td>' . date("d/m/Y", strtotime($arr['datacad'])) . '</td>
						<td>' . date("H:i:s", strtotime($arr['datacad'])) . '</td>
						<td>' . $nome . '</td>
						<td class="hide-for-small">' . $arr['ip'] . '</td>
					</tr>';
				}
				echo '
				</tbody>
			</table>
		</div>
	</div>';
	$admin->Paginacao($pagIni['nr'],$_GET['pg'],$pagIni['np'],'');
}

function usuarios_ativos()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	
	$query = "SELECT * FROM admins_sessao ORDER BY stempo DESC";
	$admin->breadcrumbs();
	$admin->pageTitle();
	$admin->saveLog('acessou',"Seção: " . $admin_mods[$on]['submenu'][$in]);
	
	echo '
	<div class="row">
		<div class="columns large-12">
			<table class="list-table">
				<thead>
					<tr>
						<th width="190">Última ação</th>
						<th>Usuário</th>
						<th width="150">IP</th>
					</tr>
				</thead>
				<tbody>';
				
				$max='50'; $pagIni = $admin->PaginacaoInit($query,$max,$_GET['pg']);
				$rr = mysql_query($query . ' LIMIT ' . $pagIni['c'] . ',' . $max);
				while ($arr = mysql_fetch_array($rr))
				{
					list($nome) = mysql_fetch_row(mysql_query("SELECT nome FROM admins WHERE aid='" . $arr['aid'] . "'"));
					echo '
					<tr>
						<td>' . date("d/m/Y", strtotime($arr['stempo'])) . ' às ' . date("H:i:s", strtotime($arr['stempo'])) . 'h </td>
						<td>' . $nome . '</td>
						<td>' . $arr['ip'] . '</td>
					</tr>';
				}
				echo '
				</tbody>
			</table>
		</div>
	</div>';
	$admin->Paginacao($pagIni['nr'],$_GET['pg'],$pagIni['np'],'');
}
function Alteracoes()
{
	global $admin, $admin_mods, $config, $on, $in, $db_table, $_GET;
	$in='alteracoes';
	$admin->saveLog('acessou',"Seção: " . $admin_mods[$on]['submenu'][$in]);
	
	unset($asqw);
	if(!empty($_GET['acao']))
	{
		if(!isset($asqw)) $asqw="WHERE "; else $asqw.=" && ";
		$asqw.=" acao='" . $_GET['acao'] . "'";
	}
	if(!empty($_GET['aid']))
	{
		if(!isset($asqw)) $asqw="WHERE "; else $asqw.=" && ";
		$asqw.=" aid='" . $_GET['aid'] . "'";
	}
	if(!empty($_GET['modulo']))
	{
		if(!isset($asqw)) $asqw="WHERE "; else $asqw.=" && ";
		$asqw.=" modulo='" . $_GET['modulo'] . "'";
	}
	$query = "SELECT * FROM admins_logs " . $asqw . " ORDER BY datacad DESC";
	$admin->breadcrumbs();
	$admin->pageTitle();
	
	echo '
	<div class="row panel filtros">
		<span class="label">Filtros</span>
		<div class="columns large-1 margintop10 text-right">Usuário</div>
		<div class="columns large-3">
			<select id="aid" name="aid" required>';
	        if(empty($arr['aid'])){ echo '<option value="">-</option>'; }
	        $array_aid = array();
	        $r2 = mysql_query("SELECT * FROM admins_logs ORDER by aid");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	        	if(!in_array($ar2['aid'], $array_aid))
	        	{
	        		$array_aid[] = $ar2['aid'];
	        		list($nome) = mysql_fetch_row(mysql_query("SELECT nome FROM admins WHERE aid='" . $ar2['aid'] . "'"));
			        echo '
			        <option value="' . $ar2['aid'] . '"'; if($_GET['aid'] == $ar2['aid']) echo ' selected'; echo '>' . $nome . '</option>';
	        	}
	        }
	        echo '
	        </select>
		</div>
		<div class="columns large-1 margintop10 text-right">Ação</div>
		<div class="columns large-3">
			<select id="acao" name="acao" required>';
	        if(empty($arr['acao'])){ echo '<option value="">-</option>'; }
	        $array_acao = array();
	        $r2 = mysql_query("SELECT * FROM admins_logs ORDER by acao");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	        	if(!in_array($ar2['acao'], $array_acao))
	        	{
	        		$array_acao[] = $ar2['acao'];
			        echo '
			        <option value="' . $ar2['acao'] . '"'; if($_GET['acao'] == $ar2['acao']) echo ' selected'; echo '>' . str_replace('-',' ',$ar2['acao']) . '</option>';
	        	}
	        }
	        echo '
	        </select>
		</div>
		<div class="columns large-1 margintop10 text-right">Módulo</div>
		<div class="columns large-3">
			<select id="modulo" name="modulo" required>';
	        if(empty($arr['modulo'])){ echo '<option value="">-</option>'; }
	        $array_modulo = array();
	        $r2 = mysql_query("SELECT * FROM admins_logs ORDER by modulo");
	        while($ar2 = mysql_fetch_array($r2))
	        {
	        	if(!in_array($ar2['modulo'], $array_modulo) && !empty($admin_mods[$ar2['modulo']]['titulo']))
	        	{
	        		$array_modulo[] = $ar2['modulo'];
			        echo '
			        <option value="' . $ar2['modulo'] . '"'; if($_GET['modulo'] == $ar2['modulo']) echo ' selected'; echo '>' . $admin_mods[$ar2['modulo']]['titulo'] . '</option>';
	        	}
	        }
	        echo '
	        </select>
		</div>
	</div>
	<div class="row">
		<div class="columns graficos-div-control large-2 text-right right margintop-10">
			<a class="secondary radius mostrar primeiro">mostrar gráfico</a>
			<a class="secondary radius ocultar hide">ocultar gráfico</a>
		</div>
	</div>
	<div class="row panel graficos hide">
		<span class="label">Gráficos</span>';
		echo "
		<script type='text/javascript'>
	      google.load('visualization', '1', {packages:['corechart']});
	      function drawChart() {
	        var data = google.visualization.arrayToDataTable([";
	        	$meses = array('', 'jan', 'fev', 'mar', 'abr', 'mai', 'jun', 'jul', 'ago', 'set', 'out', 'nov', 'dez');
	        	for ($i = 1; $i <= 12; $i++){ $last12Months[] = date("Y-m", strtotime( "next month -$i months")); }
	        	$last12Months = array_reverse($last12Months);
		        echo "['Mês'"; foreach ($array_acao as $value){  echo ",'" . str_replace('-',' ',$value) . "'"; } echo "],\n\r";
	        	foreach ($last12Months as $mes)
	        	{
	        		$nome_mes=explode('-',$mes);
	        		echo "['" . $meses[ltrim($nome_mes[1],'0')] . "/" . substr($nome_mes[0], 2) . "'";
	        		foreach ($array_acao as $acao)
	        		{
	        			//echo "SELECT id FROM admins_logs WHERE acao='" . $acao . "' && datacad LIKE '" . $mes . "%'";
	        			$total = mysql_num_rows(mysql_query("SELECT id FROM admins_logs WHERE acao='" . $acao . "' && datacad LIKE '" . $mes . "%'"));
	        			echo ',' . $total; unset($total);
	        		}
	        		echo "],\n\r";
	        	}
	        	echo "
	        ]);
	        var options = {
	          title: 'Alterações ao longo do tempo'
	        };
	        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
	        chart.draw(data, options);
	      }
	    </script>";
	    echo '
	    <div id="chart_div" style="width: 900px; height: 500px;"></div>
	</div>
	<br /><br />
	<div class="row">
		<div class="columns large-12">';
		$query_rows = explode('ORDER BY',$query);
		if(mysql_num_rows(mysql_query($query)) > 0)
		{
			echo '
			<table class="list-table">
				<thead>
					<tr>
						<th width="120">Data</th>
						<th width="100" class="hide-for-small">Horário</th>
						<th>Atividade</th>
					</tr>
				</thead>
				<tbody>';
				
				$max='50'; $pagIni = $admin->PaginacaoInit($query,$max,$_GET['pg']);
				$rr = mysql_query($query . ' LIMIT ' . $pagIni['c'] . ',' . $max);
				while ($arr = mysql_fetch_array($rr))
				{
					list($nome) = mysql_fetch_row(mysql_query("SELECT nome FROM admins WHERE aid='" . $arr['aid'] . "'"));
					echo '
					<tr>
						<td>' . date("d/m/Y", strtotime($arr['datacad'])) . ' <p class="show-for-small">' . date("H:i:s", strtotime($arr['datacad'])) . '</p></td>
						<td class="hide-for-small">' . date("H:i:s", strtotime($arr['datacad'])) . '</td>
						<td class="has-tip" data-tooltip title="IP: ' . $arr['ip'] . '" style="border:0; font-weight:normal;">' . $nome . ' ' . str_replace('-',' ',$arr['acao']) . ' no módulo ' . $admin_mods[$arr['modulo']]['titulo']; if(!empty($arr['log'])) echo ' - ' . $arr['log']; echo '.</td>
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
	$admin->Paginacao($pagIni['nr'],$_GET['pg'],$pagIni['np'],'&modulo=' . $_GET['modulo'] . '&aid=' . $_GET['aid'] . '&acao=' . $_GET['acao']);
}

function truncate()
{
	global $db_table, $on;
	mysql_query("TRUNCATE admins_sessao");
	mysql_query("TRUNCATE admins_acessos");
	mysql_query("TRUNCATE admins_logs");
	header('Location: index.php?on=' . $on);
}

switch($in)
{
	default;
	Alteracoes();
	break;

	case "truncate";
	truncate();
	break;
	
	case "acessos";
	Main();
	break;
	
	case "usuarios_ativos";
	usuarios_ativos();
	break;
}