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
	#	Daily report
	#
	# 	A script which generates the daily report for user specified day.
	#
	############################################################################
	#	
	#
	# 	v10.0 Banana 2016-10-28
	#
	############################################################################
	
	include("../../../config.php");
	
	//error_reporting(E_ALL);
	
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");
	
	
	if($_GET['d']<0 || $_GET['d']>31){
		echo "Invalid date";
		die();
	}
	if($_GET['m']<1 || $_GET['m']>12){
		echo "Invalid date";
		die();
	}
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y']) || !is_numeric($_GET['m']) || !is_numeric($_GET['d'])){
		echo "Invalid date";
		die();
	}
	
	$dateY = $_GET['y'];
	$dateM = $_GET['m'];
	$dateD = $_GET['d'];
	
	$result = mysqli_query($con,"
		SELECT *
		FROM alldata 
		WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$time = strtotime($row['DateTime']);
		$T[$time] = convertT($row['T']);
		$A[$time] = convertT($row['A']);
		$D[$time] = convertT($row['D']);
		$H[$time] = $row['H'];
		$P[$time] = convertP($row['P']);
		$W[$time] = convertW($row['W']);
		$G[$time] = convertW($row['G']);
		$R[$time] = convertR($row['R']);
		$S[$time] = $row['S'];	
	}
	
?>
	<div class="resizer">
		<div class="inner-resizer">
			<div id="varGraph" class="varGraphs" style="height:700px;margin:0 auto;width:100%"></div>
		</div>
	</div>
	
	<script>
		$(document).ready(function() {
			$('.resizer').resizable({
				resize: function() {
					selectedDiv = $(this).find(".varGraphs");
					chart = selectedDiv.highcharts();
					chart.setSize(
						this.offsetWidth - 50, 
						this.offsetHeight - 50,
						false
					);
				},
			});
			Highcharts.setOptions({
				global: {
					useUTC: true
				},
				lang: {
					months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
					shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
					weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
					resetZoom: ['<?php echo lang('default zoom','c')?>']
				}
			});
			$('#varGraph').highcharts({
				chart: {
					zoomType: 'x',
					alignTicks: false,
				},
				title: {
					text:  ''
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				xAxis: {
					type: 'datetime',
					title: {
						text: null
					},
					dateTimeLabelFormats: {
						millisecond: '%H:%M:%S.%L',
						second: '%H:%M:%S',
						minute: '%H:%M',
						hour: '<?php echo $graphTimeFormat ?>',
						day: '<?php echo $graphDateFormat ?>',
						week: '<?php echo $graphDateFormat ?>',
						month: '%b / %y',
						year: '%Y'
					}
				},
				yAxis:[
					{
						title: {
							text: '<?php echo unitFormatter($displayTempUnits)?>'
						},
					},
					{
						title: {
							text: '%'
						},
						max: 100
					},
					{
						title: {
							text: '<?php echo unitFormatter($displayPressUnits)?>'
						},
						opposite: true
					},
					{
						title: {
							text: '<?php echo unitFormatter($displayWindUnits)?>'
						},
					},
					{
						title: {
							text: '<?php echo unitFormatter($displayRainUnits)?>'
						},
						opposite: true
					},
				],
				tooltip: {
					shared: true
				},
				navigation: {
					buttonOptions: {
						enabled: false
					}
				},
				credits: {
					enabled: false
				},
				legend: {
					enabled: true
				},
				plotOptions: {
					series: {
						animation: {
							duration: 7000
						},
						marker: {
							enabled: false
						}
					},
					areaspline:{
						fillOpacity: 0.4
					}					
				},	
				series: [		
					{
						type: 'spline',
						name: '<?php echo lang('average','c')." ".lang('temperature','l')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#ff7373',
						data: [
							<?php
								foreach($T as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('apparent temperature','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#ff7373',
						dashStyle: 'ShortDot',
						data: [
							<?php
								foreach($A as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('dewpoint','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#ff7373',
						dashStyle: 'Dash',
						data: [
							<?php
								foreach($D as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('humidity','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#6cd900',
						yAxis: 1,
						data: [
							<?php
								foreach($H as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('pressure','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#ffa64c',
						yAxis: 2,
						data: [
							<?php
								foreach($P as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('wind speed','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#cc99ff',
						yAxis: 3,
						data: [
							<?php
								foreach($W as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('wind gust','c')?>',
						zIndex: 10,
						marker: {
							enabled: false
						},
						color: '#cc99ff',
						dashStyle: 'ShortDot',
						yAxis: 3,
						data: [
							<?php
								foreach($G as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('precipitation','c')?>',
						zIndex: 5,
						marker: {
							enabled: false
						},
						color: '#2693ff',
						yAxis: 4,
						data: [
							<?php
								foreach($R as $time=>$value){
									$y = date("Y",$time);
									$m = date("m",$time) - 1; // Javascript
									$d = date("d",$time);
									$h = date("H",$time);
									$min = date("i",$time);
									echo "[Date.UTC(".$y.",".$m.",".$d.",".$h.",".$min."),".round($value,3)."],";
								}
							?>
						]
					},
				]
			});
		});
	</script>