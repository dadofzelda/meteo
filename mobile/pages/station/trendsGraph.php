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
	#	Trend graphs
	#
	# 	A script which generates trend graph of user specified parameter and 
	#	time span.
	#
	############################################################################
	#	
	#
	# 	v10.0 Banana 2016-10-28
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");
	
	$interval = $_GET['interval'];
	$type = $_GET['type'];
	
	// validate type
	$type = substr($type,0,1);

	$arrData = array();
	$arrDates = array();

	function regression($y){

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
		return $result;
	}

	function average($arr){
		return array_sum($arr)/count($arr);
	}

	if($interval=="1h"){$interval= "60 minute";}
	else if($interval=="3h"){$interval= "3 hour";}
	else if($interval=="6h"){$interval= "6 hour";}
	else if($interval=="12h"){$interval= "12 hour";}
	else if($interval=="24h"){$interval= "24 hour";}
	else if($interval=="48h"){$interval= "48 hour";}
	else if($interval=="3d"){$interval= "3 day";}
	else if($interval=="1w"){$interval= "7 day";}
	else if($interval=="10d"){$interval= "10 day";}
	else if($interval=="2w"){$interval= "14 day";}
	else if($interval=="1m"){$interval= "1 month";}
	else if($interval=="3m"){$interval= "3 month";}
	else{
		die();
	}

	$result = mysqli_query($con, 
		"
		SELECT  DateTime, $type
		FROM  alldata
		WHERE DateTime >= now() - interval $interval
		ORDER BY DateTime
		"
	);
	while ($row = mysqli_fetch_array($result)) {
		if($type=="T" || $type=="A" || $type=="D"){
			array_push($arrData,convertT($row[$type]));
		}
		else if($type=="W" || $type=="G"){
			array_push($arrData,convertW($row[$type]));
		}
		else if($type=="P"){
			array_push($arrData,convertP($row[$type]));
		}
		else{
			array_push($arrData,$row[$type]);
		}
		array_push($arrDates,strtotime($row['DateTime'])*1000);
	}
	for($i=0;$i<count($arrData);$i++){
		$dataStr = $dataStr."[".$arrDates[$i].",".$arrData[$i]."],";
	}
	$trend = array();
	$trend = regression($arrData);
	$intercept = $arrData[0];	

	for($i=0;$i<count($arrData);$i++){
		$dataStr2 = $dataStr2."[".$arrDates[$i].",".$intercept."],";
		$intercept += $trend;
	}

	if($type=="T"){
		$name = lang("temperature",'c');
		$unit = "°".$displayTempUnits;
	}
	if($type=="H"){
		$name = lang("humidity",'c');
		$unit = "%";
		$additional = "max:100";
	}
	if($type=="P"){
		$name = lang("pressure",'c');
		$unit = unitFormatter($displayPressUnits);
	}
	if($type=="W"){
		$name = lang("wind speed",'c');
		$unit = unitFormatter($displayWindUnits);
		$additional = "min:0";
	}
	if($type=="G"){
		$name = lang("wind gust",'c');
		$unit = unitFormatter($displayWindUnits);
		$additional = "min:0";
	}
	if($type=="A"){
		$name = lang("apparent temperature",'c');
		$unit = "°".$displayTempUnits;
	}
	if($type=="D"){
		$name = lang("dew point",'c');
		$unit = "°".$displayTempUnits;
	}
	if($type=="S"){
		$name = lang("solar radiation",'c');
		$unit = "W/m2";
	}
?>
<html>
	<head>
		<?php metaHeader()?>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
	</head>
	<body>
		<div id="container" height="500px" width="700px"></div>
		<div style="text-align:center">
			<br><br>
			<input type="button" class="button" onclick="window.close()" value="<?php echo lang("close",'c')?>">
		</div>
		<script>
			Highcharts.setOptions({                                          
				global : {
					  useUTC : false
				 }
			});
			$(function () {
				chart = new Highcharts.Chart({
				chart: {
					zoomType: 'xy',
					renderTo: 'container'
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				title: {
					text: '<?php echo $name?>'
				},
				xAxis: {
					type: 'datetime',
				},
				yAxis: {
					title: {
						text: ''
					},
					labels: {
						format: '{value} <?php echo $unit?>'
					},
					<?php echo $additional ?>
				},
				series: [
					{
						data: [<?php echo $dataStr ?>],
						type: 'spline',
						name: '<?php echo $name ?>',
						marker: {
							enabled: false
						},
						color: '#ffffff'
					},
					{
						data: [<?php echo $dataStr2 ?>],
						type: 'spline',
						name: "Trend",
						marker: {
							enabled: false
						},
						color: '#ffffff',
						dashStyle: 'ShortDot',
					},
				],
				});
			});
		</script>
		<?php include("../../../css/highcharts.php");?>
	</body>
</html>