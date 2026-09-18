<?php

	############################################################################
	# 	
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Trends
	#
	# 	A script which generates the page showing weather condition trends.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCol = "T";
		$dp = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCol = "H";
		$dp = 1;
		$UoM = "%";
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCol = "P";
		$dp = 2;
		$UoM = unitFormatter($displayPressUnits);
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCol = "W";
		$dp = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCol = "G";
		$dp = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCol = "A";
		$dp = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="D"){
		$heading = lang("dewpoint",'c');
		$mySQLCol = "D";
		$dp = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCol = "S";
		$dp = 1;
		$UoM = "W/m2";
	}
	
	$trendUpColor = $color_schemes[$design2]['700'];
	$trendNeutralColor = $color_schemes[$design2]['800'];
	$trendDownColor = $color_schemes[$design2]['900'];

	$result = mysqli_query($con, "
			SELECT  DateTime, ".$mySQLCol."
			FROM  alldata
			WHERE DateTime >= now() - interval 3 month
			ORDER BY DateTime
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$data['3m'][] = chooseConvertor($row[$mySQLCol]);
		if(strtotime($row['DateTime']) >= strtotime('-1 month')){
			$data['1m'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-10 Days')){
			$data['10d'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-3 Days')){
			$data['3d'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-2 weeks')){
			$data['2w'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-1 week')){
			$data['1w'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-48 hours')){
			$data['48h'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-24 hours')){
			$data['24h'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-6 hours')){
			$data['6h'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-3 hours')){
			$data['3h'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-12 hours')){
			$data['12h'][] = chooseConvertor($row[$mySQLCol]);
		}
		if(strtotime($row['DateTime']) >= strtotime('-60 minutes')){
			$data['1h'][] = chooseConvertor($row[$mySQLCol]);
		}
	}
	
	// check we have data
	$intervals = array('3m','1m','2w','10d','1w','3d','48h','24h','12h','6h','3h','1h');
	$intervals = array_reverse($intervals);
	
	foreach($intervals as $interval){
		if(!array_key_exists($interval,$data)){
			$enable[$interval] = false;
		}
		else{
			if(count($data[$interval])<2){
				$enable[$interval] = false;
			}
			else{
				$enable[$interval] = true;
			}
		}
	}
	
	if($enable['3m']){
		$trend['3m'] = regression($data['3m']);
		$maximum['3m'] = max($data['3m']);
		$minimum['3m'] = min($data['3m']);
		$average['3m'] = average($data['3m']);
		if(count($data['3m'])>100){
			$data['3m'] = smoothen($data['3m'],100);
		}
	}
	if($enable['1m']){
		$trend['1m'] = regression($data['1m']);
		$maximum['1m'] = max($data['1m']);
		$minimum['1m'] = min($data['1m']);
		$average['1m'] = average($data['1m']);
		if(count($data['1m'])>100){
			$data['1m'] = smoothen($data['1m'],100);
		}
	}
	if($enable['2w']){
		$trend['2w'] = regression($data['2w']);
		$maximum['2w'] = max($data['2w']);
		$minimum['2w'] = min($data['2w']);
		$average['2w'] = average($data['2w']);
		if(count($data['2w'])>100){
			$data['2w'] = smoothen($data['2w'],100);
		}
	}
	if($enable['10d']){
		$trend['10d'] = regression($data['10d']);
		$maximum['10d'] = max($data['10d']);
		$minimum['10d'] = min($data['10d']);
		$average['10d'] = average($data['10d']);
		if(count($data['10d'])>100){
			$data['10d'] = smoothen($data['10d'],100);
		}
	}
	if($enable['3d']){
		$trend['3d'] = regression($data['3d']);
		$maximum['3d'] = max($data['3d']);
		$minimum['3d'] = min($data['3d']);
		$average['3d'] = average($data['3d']);
		if(count($data['3d'])>100){
			$data['3d'] = smoothen($data['3d'],100);
		}
	}
	if($enable['1w']){
		$trend['1w'] = regression($data['1w']);
		$maximum['1w'] = max($data['1w']);
		$minimum['1w'] = min($data['1w']);
		$average['1w'] = average($data['1w']);
		if(count($data['1w'])>100){
			$data['1w'] = smoothen($data['1w'],100);
		}
	}
	if($enable['48h']){
		$trend['48h'] = regression($data['48h']);
		$maximum['48h'] = max($data['48h']);
		$minimum['48h'] = min($data['48h']);
		$average['48h'] = average($data['48h']);
		if(count($data['48h'])>100){
			$data['48h'] = smoothen($data['48h'],100);
		}
	}
	if($enable['24h']){
		$trend['24h'] = regression($data['24h']);
		$maximum['24h'] = max($data['24h']);
		$minimum['24h'] = min($data['24h']);
		$average['24h'] = average($data['24h']);
		if(count($data['24h'])>100){
			$data['24h'] = smoothen($data['24h'],100);
		}
	}
	if($enable['12h']){
		$trend['12h'] = regression($data['12h']);
		$maximum['12h'] = max($data['12h']);
		$minimum['12h'] = min($data['12h']);
		$average['12h'] = average($data['12h']);
		if(count($data['12h'])>100){
			$data['12h'] = smoothen($data['12h'],100);
		}
	}
	if($enable['6h']){
		$trend['6h'] = regression($data['6h']);
		$maximum['6h'] = max($data['6h']);
		$minimum['6h'] = min($data['6h']);
		$average['6h'] = average($data['6h']);
		if(count($data['6h'])>100){
			$data['6h'] = smoothen($data['6h'],100);
		}
	}
	if($enable['3h']){
		$trend['3h'] = regression($data['3h']);
		$maximum['3h'] = max($data['3h']);
		$minimum['3h'] = min($data['3h']);
		$average['3h'] = average($data['3h']);
		if(count($data['3h'])>100){
			$data['3h'] = smoothen($data['3h'],100);
		}
	}
	if($enable['1h']){
		$trend['1h'] = regression($data['1h']);
		$maximum['1h'] = max($data['1h']);
		$minimum['1h'] = min($data['1h']);
		$average['1h'] = average($data['1h']);
		if(count($data['1h'])>100){
			$data['1h'] = smoothen($data['1h'],100);
		}
	}
	
	function chooseConvertor($value){
		global $var;
		if($var=="T" || $var=="A" || $var=="D"){
			return convertT($value);
		}
		if($var=="H"){
			return ($value);
		}
		if($var=="P"){
			return convertP($value);
		}
		if($var=="W" || $var=="G"){
			return convertW($value);
		}
		if($var=="S"){
			return ($value);
		}
		if($var=="R"){
			return convertR($value);
		}
	}
	
	function positive($value){
		if($value>0){
			$value="+".$value;
		}
		return $value;
	}

	function average($arr){
		return array_sum($arr)/count($arr);
	}

	function sumrain($arr){
		return array_sum($arr);
	}

	function smoothen($arr,$x){
		$arr = array_chunk($arr, count($arr) / $x);
		$arr = array_map('average', $arr);
		return $arr;
	}				

	function smoothen_rain($arr,$x){
		$arr = array_chunk($arr, count($arr) / $x);
		$arr = array_map('sumrain', $arr);
		return $arr;
	}

	function regression($y){
		if(count($y)<2){
			$result = "";
		}
		else{
			$n = count($y);
			$x = array();
			for($i=0;$i<$n;$i++){
				array_push($x,$i);
			}
			$xy = array();
			$x2 = array();
			
			for($i=0;$i<count($x);$i++){
				array_push($xy,($x[$i]*$y[$i]));
				array_push($x2,($x[$i]*$x[$i]));
			}
			
			$sx = array_sum($x);
			$sy = array_sum($y);
			$sxy = array_sum($xy);
			$sx2 = array_sum($x2);
			
			$slope = ($n*$sxy-$sx*$sy)/($n*$sx2-$sx*$sx);
			$intercept = ($sy - $slope*$sx)/$n;
			
			$result = $slope;
		}
		$count = count($y);
		return array($result,$count);
	}
	
	
	
	function trendDirection($value,$trendUpColor,$trendNeutralColor,$trendDownColor){
		if($value<0){
			$color = $trendDownColor;
		}
		if($value>0){
			$color = $trendUpColor;
		}
		if($value==0){
			$color = $trendNeutralColor;
		}
		return $color;
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $heading." ".lang("trends",'c')?></title>
		<?php metaHeader()?>
		<style>
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['600']?>;
				color: #<?php echo $color_schemes[$design2]['font600']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['700']?>;
				color: #<?php echo $color_schemes[$design2]['font700']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			.icon{
				width: 24px;
				opacity: 0.8;
				cursor: hand;
			}
			.icon:hover{
				opacity: 0.9;
			}
			.parameters{
				width: 24px;
			}
			.imgHeader{
				width: 40px;
				padding-left: 5px;
			}
			.descriptions{
				width:auto;
			}
			.arrow{
				width: 25px;
			}
			.trend{
				width: 20px;
			}
			.trendValue{
				text-align: center;
			}
			.graphIcon{
				width: 30px;
				opacity: 0.6;
				cursor: pointer;
			}
			.graphIcon:hover{
				opacity: 0.9;
			}
			.ui-widget-content {
				border: 1px solid #<?php echo $color_schemes[$design2]['900']?>;
				background: #<?php echo $color_schemes[$design2]['800']?>;
				background-color: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.ui-widget-header {
				border: 0px solid #000;
				background: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.ui-widget {
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
				font-size: 1.1em;
			}
			.ui-tabs .ui-tabs-panel {
				background: #<?php echo $color_schemes[$design2]['900']?>;
				border: 1px solid #<?php echo $color_schemes[$design]['900']?>;
				border-radius: 5px;
				padding: 0em 0em; 
			}
			.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
				border: 0px solid #000;
				background: none;
			}
			.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
				border: 1px solid #<?php echo $color_schemes[$design]['900']?>;
				background: #<?php echo $color_schemes[$design2]['900']?>;
			}
			.ui-tabs .ui-tabs-nav {
				margin: 0;
				padding: 0em 0em 0;
			}
		</style>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.sparkline.min.js"></script>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
		<h1><?php echo $heading." ".lang("trends",'l')?></h1>
		<br>
		<div style="width:100%;text-align:center;position:absolute;top:50;left:0;z-index:10" id="spinner">
			<br><br>
			<img src="<?php echo $pageURL.$path?>icons/logo.png" style="width:80px" class="mtSpinner">
			<br><br>
			<?php echo lang("generating graphs and tables",'c')?>...
		</div>
		</div>
		
			<div id="tabs-1">
				<table class="table" style="width:98%;margin:0 auto">
					<thead>
						<tr>
							<th>
							</th>
							<th>
							</th>
							<th>
								<?php echo lang("trend",'c')?><br><?php echo $UoM?>
							</th>
							<th colspan="2">						
							</th>
							<th>
								<?php echo lang('maximumAbbr','c')?><br><?php echo $UoM?>
							</th>
							<th>
								<?php echo lang('minimumAbbr','c')?><br><?php echo $UoM?>
							</th>
							<th>
								<?php echo lang('avgAbbr','c')?><br><?php echo $UoM?>
							</th>
							<th>
								<?php echo lang('range','c')?><br><?php echo $UoM?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							$icons['1h'] = '1h';
							$icons['3h'] = '3h';
							$icons['6h'] = '6h';
							$icons['12h'] = '12h';
							$icons['24h'] = '24h';
							$icons['48h'] = '48h';
							$icons['3d'] = '3days';
							$icons['1w'] = 'week';
							$icons['10d'] = '10days';
							$icons['2w'] = '2weeks';
							$icons['1m'] = 'calendar-month';
							$icons['3m'] = '3months';
							
							foreach($intervals as $interval){
						?>
								<?php 
									if($enable[$interval]){
								?>
								<tr>
									<td>
										<span class='mticon-<?php echo $icons[$interval]?> imgHeader' style='font-size:3em'></span>
									</td>
									<td class="trend">
										<?php
											if($trend[$interval][0]>0){ 
												echo "<span class='mticon-trendup' style='font-size:1.7em'></span>";
											}
											if($trend[$interval][0]<0){ 
												echo "<span class='mticon-trenddown' style='font-size:1.7em'></span>";
											}
											if($trend[$interval][0]==0){ 
												echo "<span class='mticon-trendneutral' style='font-size:1.7em'></span>";
											}
										?>
									</td>
									<td class="trendValue">
										<?php
											echo positive(number_format($trend[$interval][0]*$trend[$interval][1],2)); 
										?>
									</td>
									<td>
										<div class="sparklines<?php echo $var?>">
										<?php 
											$arr = $data[$interval];
											for($i=0;$i<count($arr);$i++){
												$values .= $arr[$i].",";
											}
											$values = substr($values, 0, -1);
											echo $values;
											$values = "";
										?>
										</div>
									</td>
									<td>
										<span class='mticon-graph graphIcon' style='font-size:1.7em' onclick="loadgraph('<?php echo $var?>','<?php echo $interval?>')"></span>
									</td>
									<td>
										<?php echo number_format($maximum[$interval],$dp);?>
									</td>
									<td>
										<?php echo number_format($minimum[$interval],$dp);?>
									</td>
									<td>
										<?php echo number_format($average[$interval],$dp);?>
									</td>
									<td>
										<?php echo number_format($maximum[$interval]-$minimum[$interval],$dp);?>
									</td>
								</tr>
								<?php 
									}
								?>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<br><br>
		</div>

		<script>
		$(document).ready(function() {
			$('#spinner').hide();
			$("#tabs").show();
		});
		</script>
		<script type="text/javascript">
		$(function() {
			$('.sparklinesT').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1.5,
					spotColor: false,
					spotRadius: 3,
					minSpotColor: '#56aaff',
					maxSpotColor: '#ff0000',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesA').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1.5,
					spotColor: false,
					spotRadius: 3,
					minSpotColor: '#56aaff',
					maxSpotColor: '#ff0000',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesD').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1.5,
					spotColor: false,
					spotRadius: 3,
					minSpotColor: '#56aaff',
					maxSpotColor: '#ff0000',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesH').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1,
					spotColor: false,
					spotRadius: 2.5,
					minSpotColor: '#BFFFBF',
					maxSpotColor: '#2DB300',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesW').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1,
					spotColor: false,
					spotRadius: 2.5,
					minSpotColor: '#FFCFBF',
					maxSpotColor: '#D93600',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesG').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1,
					spotColor: false,
					spotRadius: 2.5,
					minSpotColor: '#FFCFBF',
					maxSpotColor: '#D93600',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesP').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1,
					spotColor: false,
					spotRadius: 2.5,
					minSpotColor: '#FFBFFF',
					maxSpotColor: '#8600B3',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
			$('.sparklinesS').sparkline('html',
				{
					type: 'line',
					width: '200px',
					height: '30px',
					lineColor: '#ffffff',
					fillColor: null,
					lineWidth: 1,
					spotColor: false,
					spotRadius: 4,
					minSpotColor: '#FFFFFF',
					maxSpotColor: '#000000',
					highlightSpotColor: null,
					highlightLineColor: null,
					drawNormalOnTop: false,
					disableInteraction: true,
				}
			);
		});
		</script>
		<script>
			$(function() {
				$( "#tabs" ).tabs();
			});
		</script>
		<script>
		function loadgraph(type,interval){
			var left = (screen.width/2)-(850/2);
			var top = (screen.height/2)-(600/2);
			url = ('trendsGraph.php?interval='+interval+'&type='+type);
			window.open(url,'<?php echo lang("graph",'c')?>','toolbar=no,location=no,directories=no,status=no, menubar=no,scrollbars=no,resizable=no,width=850,height=600,titlebar=no, toolbar=no, top='+top+', left='+left); 
		}
		</script>
		<?php include($baseURL."footer.php");?>
	</body>
</html>