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
	#	Annual report
	#
	# 	A script that generates the annual report.
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
	
	// Get date
	$chosenYear = $_GET['y'];
	
	// validate year
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	// check id data cache is available
	if(file_exists("cache/annual".$chosenYear.".txt")){
		$data = json_decode(file_get_contents("cache/annual".$chosenYear.".txt"),true);
	}
	else{
	
		$span = "Year(DateTime) = ".$chosenYear;
		
		// get available months
		$result = mysqli_query($con,"
			SELECT DISTINCT month(DateTime)
			FROM alldata 
			WHERE $span
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['availableMonths'][] = $row['month(DateTime)'];
		}
		/* #############################################################################*/
		// Calculate annual average, max, min, sd, range
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(T), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(Tmin), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			// annual averages
			$data['annualAvgT'] = ($row['avg(T)']);
			$data['annualAvgH'] = $row['avg(H)'];
			$data['annualAvgP'] = ($row['avg(P)']);
			$data['annualAvgD'] = ($row['avg(D)']);
			$data['annualAvgW'] = ($row['avg(W)']);
			$data['annualAvgG'] = ($row['avg(G)']);
			$data['annualAvgA'] = ($row['avg(A)']);
			$data['annualAvgS'] = $row['avg(S)'];
			// annual maxima
			$data['annualMaxTmax'] = ($row['max(Tmax)']);
			$data['annualMaxH'] = $row['max(H)'];
			$data['annualMaxP'] = ($row['max(P)']);
			$data['annualMaxD'] = ($row['max(D)']);
			$data['annualMaxW'] = ($row['max(W)']);
			$data['annualMaxG'] = ($row['max(G)']);
			$data['annualMaxA'] = ($row['max(A)']);
			$data['annualMaxS'] = $row['max(S)'];
			$data['annualMaxRR'] = ($row['max(RR)']);
			// annual minima
			$data['annualMinTmin'] = ($row['min(Tmin)']);
			$data['annualMinH'] = $row['min(H)'];
			$data['annualMinP'] = ($row['min(P)']);
			$data['annualMinD'] = ($row['min(D)']);
			$data['annualMinW'] = ($row['min(W)']);
			$data['annualMinG'] = ($row['min(G)']);
			$data['annualMinA'] = ($row['min(A)']);
			$data['annualMinS'] = $row['min(S)'];
			// annual ranges
			$data['annualRangeT'] = $data['annualMaxTmax'] - $data['annualMinTmin'];
			$data['annualRangeH'] = $data['annualMaxH'] - $data['annualMinH'];
			$data['annualRangeP'] = $data['annualMaxP'] - $data['annualMinP'];
			$data['annualRangeD'] = $data['annualMaxD'] - $data['annualMinD'];
			$data['annualRangeW'] = $data['annualMaxW'] - $data['annualMinW'];
			$data['annualRangeG'] = $data['annualMaxG'] - $data['annualMinG'];
			$data['annualRangeA'] = $data['annualMaxA'] - $data['annualMinA'];
			$data['annualRangeS'] = $data['annualMaxS'] - $data['annualMinS'];
			// annual standard deviations
			$data['annualStddevT'] = ($row['stddev(T)']);
			$data['annualStddevH'] = $row['stddev(H)'];
			$data['annualStddevP'] = ($row['stddev(P)']);
			$data['annualStddevD'] = ($row['stddev(D)']);
			$data['annualStddevW'] = ($row['stddev(W)']);
			$data['annualStddevG'] = ($row['stddev(G)']);
			$data['annualStddevA'] = ($row['stddev(A)']);
			$data['annualStddevS'] = $row['stddev(S)'];
			
		}
		
		// Calculate dates and times when max and min measured
		$data['annualMaxTmaxDate'] = array();
		$data['annualMinTminDate'] = array();
		$data['annualMaxHDate'] = array();
		$data['annualMinHDate'] = array();
		$data['annualMaxPDate'] = array();
		$data['annualMinPDate'] = array();
		$data['annualMaxWDate'] = array();
		$data['annualMinWDate'] = array();
		$data['annualMaxGDate'] = array();
		$data['annualMinGDate'] = array();
		$data['annualMaxADate'] = array();
		$data['annualMinADate'] = array();
		$data['annualMaxDDate'] = array();
		$data['annualMinDDate'] = array();
		$data['annualMaxSDate'] = array();
		$data['annualMinSDate'] = array();
		$data['annualMaxRRDate'] = array();
		
		// new method, faster

		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND Tmax=".$data['annualMaxTmax']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxTmaxDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND Tmin=".$data['annualMinTmin']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinTminDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND A=".$data['annualMaxA']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxADate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND A=".$data['annualMinA']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinADate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND D=".$data['annualMaxD']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxDDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND D=".$data['annualMinD']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinDDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND H=".$data['annualMaxH']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxHDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND H=".$data['annualMinH']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinHDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND P=".$data['annualMaxP']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxPDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND P=".$data['annualMinP']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinPDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND W=".$data['annualMaxW']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxWDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND W=".$data['annualMinW']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinWDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND G=".$data['annualMaxG']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxGDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND G=".$data['annualMinG']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinGDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND RR=".$data['annualMaxRR']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxRRDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		if($solarSensor){
			$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND S=".$data['annualMaxS']." LIMIT 11");
			while($row = mysqli_fetch_array($result)){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['annualMaxSDate'],date($timeFormat." ".$dateFormat, $date_temporary));
			}
			$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND S=".$data['annualMinS']." LIMIT 11");
			while($row = mysqli_fetch_array($result)){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['annualMinSDate'],date($timeFormat." ".$dateFormat, $date_temporary));
			}
		}
		if(count($data['annualMaxHDate'])>10){
			$data['annualMaxHDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinHDate'])>10){
			$data['annualMinHDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxTmaxDate'])>10){
			$data['annualMaxTmaxDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinTminDate'])>10){
			$data['annualMinTminDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxADate'])>10){
			$data['annualMaxADate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinADate'])>10){
			$data['annualMinADate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxDDate'])>10){
			$data['annualMaxDDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinDDate'])>10){
			$data['annualMinDDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxPDate'])>10){
			$data['annualMaxPDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinPDate'])>10){
			$data['annualMinPDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxSDate'])>10){
			$data['annualMaxSDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinSDate'])>10){
			$data['annualMinSDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxWDate'])>10){
			$data['annualMaxWDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinWDate'])>10){
			$data['annualMinWDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxGDate'])>10){
			$data['annualMaxGDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinGDate'])>10){
			$data['annualMinGDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxRRDate'])>10){
			$data['annualMaxRRDate'] = array(lang('more than 10<br>instances','l'));
		}
		
		$data['annualRTotal'] = 0;
		$data['daysNumber'] = 0;
		// Calculate annual precipitation
		$result = mysqli_query($con,"
			SELECT max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['annualRTotal'] += ($row['max(R)']);
			$data['daysNumber']++;
		}
		
		// Calculate annual windrun

		if($dataWindUnits=="kmh"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'];
			$data['annualWindRunUnits'] = "km";
		}
		if($dataWindUnits=="ms"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'] * 3.6;
			$data['annualWindRunUnits'] = "km";
		}
		if($dataWindUnits=="mph"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'];
			$data['annualWindRunUnits'] = "mi";
		}
		
		// Calculate average wind direction
		$data['annualBValues'] = array();
		$result = mysqli_query($con,"
			SELECT B
			FROM alldata 
			WHERE $span
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['annualBValues'],$row['B']);
		}
		$data['annualAvgB'] = avgWind($data['annualBValues']);
		$data['annualBValues'] = ""; // delete it so that it is not all cached
		
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/annual".$chosenYear.".txt",json_encode($data));
		}
	}
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S)
		FROM alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		$stationAverageT = convertT($row['avg(T)']);
		$stationAverageA = convertT($row['avg(A)']);
		$stationAverageD = convertT($row['avg(D)']);
		$stationAverageH = ($row['avg(H)']);
		$stationAverageP = convertP($row['avg(P)']);
		$stationAverageW = convertW($row['avg(W)']);
		$stationAverageG = convertW($row['avg(G)']);
		$stationAverageS = ($row['avg(S)']);
	}

	// normals
	$normalsTRaw = explode(";",$areaNormalsT);
	for($i=0;$i<count($normalsTRaw);$i++){
		$monthNormalsT[$i+1] = number_format(convertor($normalsTRaw[$i],$areaNormalsTUnits,$displayTempUnits),1,".","");
	}
	$normalsRRaw = explode(";",$areaNormalsR);
	for($i=0;$i<count($normalsRRaw);$i++){
		$monthNormalsR[$i+1] = number_format(convertor($normalsRRaw[$i],$areaNormalsRUnits,$displayRainUnits),$decimalR,".","");
	}
	
	function deviationFormat($n){
		if($n>0){
			return "+".$n;
		}
		else{
			return $n;
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("annual report",'c')?></title>
		<?php metaHeader()?>
		<style>
			.showtimes{
				width: 13px;
				opacity: 0.8;
				cursor: pointer;
				padding-left: 2px;
			}
			.sort{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.sort:hover{
				opacity:1;
			}
			.clock{
				width: 20px;
				cursor: pointer;
				opacity: 0.8;
			}
			.showtimes:hover, .clock:hover, .sort:hover{
				opacity: 1;
			}
			.value{
				text-align: right;
			}
			#tabs{
				min-height: 200px;
			}
			.parameters{
				width: 24px;
			}
			.imgHeader{
				width: 40px;
			}
			.imgSubHeader{
				width: 22px;
				opacity: 0.8;
				cursor: pointer;
				font-size: 0.8em;
				border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
				border-radius: 3px;
				padding-left: 12px;
				padding-right: 12px;
				padding-top: 2px;
				padding-bottom: 2px;
				background-color: #<?php echo $color_schemes[$design2]['600']?>;
				margin: 1px;
			}
			.imgSubHeader:hover{
				opacity: 1;
				background-color: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.descriptions{
				width:auto;
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
				padding: 0.5em; 
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
			.ui-tabs .ui-tabs-nav .ui-tabs-anchor {
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table th{
				text-align:center;
			}
			.times{
				font-size:0.8em;
			}
			#summaryTable{
				table-layout: fixed;
			}
			#pdfLink{
				font-size:3em;
				cursor: pointer;
				opacity: 0.8;
				padding-bottom:10px;
				padding-top: 10px;
			}
			#pdfLink:hover{
				opacity: 1;
			}
		</style>
		<script type="text/javascript" src="//code.highcharts.com/highcharts.js"></script>
		<script type="text/javascript" src="//code.highcharts.com/highcharts-more.js"></script>
		<script type="text/javascript" src="//code.highcharts.com/modules/heatmap.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
	</head>
	<body>
	<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
		<table style="width:100%;table-layout:fixed">
				<tr>
					<td style="width:5%;text-align:right">
					</td>
					<td style='text-align:center;vertical-align:top'>
						<h2><?php echo lang("annual report","c")." ".$chosenYear?></h2>
					</td>
					<td style="width:5%;text-align:right">
						<a href="<?php echo $pageURL.$path?>pages/station/reportYearlyPDF.php?y=<?php echo $chosenYear?>" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>
					</td>
				</tr>
			</table>
		<br>
		<div id="tabs" class="centered">
			<ul style="align:center">
				<li>
					<a href="#tabs1"><?php echo lang("summary",'c')?></a>
				</li>
				<li>
					<a href="reportYearlyExtremes.php?y=<?php echo $chosenYear?>"><?php echo lang("extremes",'c')?></a>
				</li>
				<li>
					<a href="reportYearlyMonths.php?y=<?php echo $chosenYear?>"><?php echo lang("month values",'c')?></a>
				</li>
				<li>
					<a href="reportYearlyDays.php?y=<?php echo $chosenYear?>"><?php echo lang("day values",'c')?></a>
				</li>
				<li>
					<a href="#tabs4"><span onclick="graph('T','m')"><?php echo lang("graphs",'c')?></span></a>
				</li>
				<li>
					<a href="#tabs5"><span onclick="visual('Tavg')"><?php echo lang("visualizations",'c')?></span></a>
				</li>
			</ul>
			<div id="tabs1">
				<table class="table" id="summaryTable">
						<thead>
							<tr>
								<th>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang('avgAbbr','c')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang('range','c')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang("sdAbbr",'')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang("station",'c')." ".lang("average",'l')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang("deviation",'c')?>
								</th>
								<th class="summaryTableHeading">
									<?php echo lang("normal",'c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td> 
									<?php echo lang("temperature",'c')." (".unitFormatter($displayTempUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertT($data['annualAvgT']),2,'.','')?>
								</td>
								<td> 
									<?php echo number_format(convertT($data['annualMaxTmax']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span> 
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxTmaxDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertT($data['annualMinTmin']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinTminDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualRangeT']),1,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualStddevT']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageT,2,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertT($data['annualAvgT']) - $stationAverageT,2,".",""))?>
								</td>
								<td>
									<?php echo number_format(array_sum($monthNormalsT)/count($monthNormalsR),1,".","")?>
								</td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("apparent temperature",'c'). " (".unitFormatter($displayTempUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertT($data['annualAvgA']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertT($data['annualMaxA']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxADate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertT($data['annualMinA']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinADate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualRangeA']),1,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualStddevA']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageA,2,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertT($data['annualAvgA']) - $stationAverageA,2,".",""))?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("dew point",'c'). " (".unitFormatter($displayTempUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertT($data['annualAvgD']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($data['annualMaxD'],1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxDDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertT($data['annualMinD']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinDDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualRangeD']),1,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertTStddev($data['annualStddevD']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageD,2,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertT($data['annualAvgD']) - $stationAverageD,2,".",""))?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("humidity",'c'). " (%)"?> 
								</td>
								<td>
									<?php echo number_format($data['annualAvgH'],2,'.','')?>
								</td>
								<td>
									<?php echo number_format($data['annualMaxH'],1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxHDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format($data['annualMinH'],1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinHDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format($data['annualRangeH'],1,'.','')?>
								</td>
								<td>
									<?php echo number_format($data['annualStddevH'],2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageH,1,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(($data['annualAvgH']) - $stationAverageH,1,".",""))?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("pressure",'c'). " (".unitFormatter($displayPressUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertP($data['annualAvgP']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertP($data['annualMaxP']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxPDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertP($data['annualMinP']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinPDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertP($data['annualRangeP']),$decimalsP,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertP($data['annualStddevP']),$decimalsP+1,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageP,$decimalsP+1,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertP($data['annualAvgP']) - $stationAverageP,$decimalsP+1,".",""))?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("wind speed",'c'). " (".unitFormatter($displayWindUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertW($data['annualAvgW']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualMaxW']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxWDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualMinW']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinWDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualRangeW']),1,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualStddevW']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageW,2,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertW($data['annualAvgW']) - $stationAverageW,2,".",""))?>
								</td>
								<td></td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("wind gust",'c'). " (".unitFormatter($displayWindUnits).")"?> 
								</td>
								<td>
									<?php echo number_format(convertW($data['annualAvgG']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualMaxG']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxGDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualMinG']),1,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMinGDate']) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualRangeG']),1,'.','')?>
								</td>
								<td>
									<?php echo number_format(convertW($data['annualStddevG']),2,'.','')?>
								</td>
								<td>
									<?php echo number_format($stationAverageG,2,".","")?>
								</td>
								<td>
									<?php echo deviationFormat(number_format(convertW($data['annualAvgG']) - $stationAverageG,2,".",""))?>
								</td>
								<td></td>
							</tr>
							<?php if($solarSensor){?>
								<tr>
									<td> 
										<?php echo lang("solar radiation",'c'). " (W/m<sup>2</sup>)"?> 
									</td>
									<td>
										<?php echo number_format($data['annualAvgS'],1,'.','')?>
									</td>
									<td>
										<?php echo number_format($data['annualMaxS'],0,'.','')?>
										<span class="fa fa-clock-o showtimes"></span>
										<div class="times">
											<br><?php echo implode("<br>",$data['annualMaxSDate']) ?>
										</div>
									</td>
									<td>
										<?php echo round($data['annualMinS'],0)?>
										<span class="fa fa-clock-o showtimes"></span>
										<div class="times">
											<br><?php echo implode("<br>",$data['annualMinSDate']) ?>
										</div>
									</td>
									<td>
										<?php echo round($data['annualRangeS'],0)?>
									</td>
									<td>
										<?php echo number_format($data['annualStddevS'],1,'.','')?>
									</td>
									<td>
										<?php echo number_format($stationAverageS,1,".","")?>
									</td>
									<td>
										<?php echo deviationFormat(number_format(($data['annualAvgS']) - $stationAverageS,0,".",""))?>
									</td>
									<td></td>
								</tr>
							<?php 
								} 
							?>
							<tr>
								<td> 
									<?php echo lang("rain rate",'c'). " (".unitFormatter($displayRainUnits).")"?> 
								</td>
								<td>
									
								</td>
								<td>
									<?php echo number_format(convertR($data['annualMaxRR']),$decimalsR,'.','')?>
									<span class="fa fa-clock-o showtimes"></span>
									<div class="times">
										<br><?php echo implode("<br>",$data['annualMaxRRDate']) ?>
									</div>
								</td>
								<td colspan="6">							
								</td>
							</tr>
							<tr>
								<td> 
									<?php echo lang("wind direction",'c')?> 
								</td>
								<td>
									<?php echo round($data['annualAvgB'])?>°
								</td>
								<td colspan="7">
								</td>
							</tr>
							<tr>
								<td style="background:#<?php echo $color_schemes[$design2]['900']?>">
								</td>
								<td colspan="7" style="text-align:center;font-variant:small-caps;background:#<?php echo $color_schemes[$design2]['900']?>;color:white">
									<?php echo lang('total','c')?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo lang("precipitation",'c'). " (".unitFormatter($displayRainUnits).")"?> 
								</td>
								<td colspan="7">
									<?php echo number_format(convertR($data['annualRTotal']),$decimalsR,'.','') ?>
								</td>
								<td>
									<?php echo number_format(array_sum($monthNormalsR),$decimalsR,".","");?>
								</td>
							</tr>
							<tr>
								<td>
									<?php echo lang("wind run",'c'). " (".$data['annualWindRunUnits'].")"?> 
								</td>
								<td colspan="7">
									<?php echo number_format($data['annualWindRun'],1,'.','') ?>
								</td>
								<td>
								</td>
							</tr>
						</tbody>
					</table>
				<br><br>
			</div>
			<div id="tabs4">
				<table id="mainTable4" class="table tableSpacing2Padding2">
					<tr>
						<th style="text-align:center!important" colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="imgHeader" alt=''>
						</th>
						<th colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="imgHeader" alt=''>
						</th>
						<th colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="imgHeader" alt=''>
						</th>
						<th colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="imgHeader" alt=''>
						</th>
						<th colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="imgHeader" alt=''>
						</th>
						<th colspan="2">
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="imgHeader" alt=''>
						</th>
						<?php
							if($solarSensor){
						?>
							<th colspan="2">
								<img src="<?php echo $pageURL.$path?>icons/sun.png" class="imgHeader" alt=''>
							</th>
						<?php
							}
						?>
					</tr>
					<tr>
						<th style="text-align:center!important">
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('T','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('T','m')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('H','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('H','m')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('P','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('P','m')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('W','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('W','m')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('D','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('D','m')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('R','d')" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('R','m')" alt=''>
						</th>
						<?php
							if($solarSensor){
						?>
							<th>
								<img src="<?php echo $pageURL.$path?>icons/calendar_day.png" class="imgSubHeader" onclick="graph('S','d')" alt=''>
							</th>
							<th>
								<img src="<?php echo $pageURL.$path?>icons/calendar_month.png" class="imgSubHeader" onclick="graph('S','m')" alt=''>
							</th>
						<?php
							}
						?>
					</tr>
				</table>
				<div id="graph" style="height:400px;margin-left:auto;margin-right:auto;width:95%"></div>
			</div>
			<div id="tabs5">
				<table id="mainTable5" class="table tableSpacing2Padding2">
					<tr>
						<th style="text-align:center!important">
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/gust.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="imgHeader" alt=''>
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="imgHeader" alt=''>
						</th>
						<?php
							if($solarSensor){
						?>
							<th>
								<img src="<?php echo $pageURL.$path?>icons/sun.png" class="imgHeader" alt=''>
							</th>
						<?php
							}
						?>
					</tr>
					<tr>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Tavg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<br>
							<span class="imgSubHeader" onclick="visual('Tmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Tmin')">
								<?php echo lang('minimumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Havg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<br>
							<span class="imgSubHeader" onclick="visual('Hmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Hmin')">
								<?php echo lang('minimumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Pavg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<br>
							<span class="imgSubHeader" onclick="visual('Pmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Pmin')">
								<?php echo lang('minimumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Wavg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Wmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Gavg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Gmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Davg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<br>
							<span class="imgSubHeader" onclick="visual('Dmax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Dmin')">
								<?php echo lang('minimumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('Aavg')">
								<?php echo lang('avgAbbr','c')?>
							</span>
							<br>
							<span class="imgSubHeader" onclick="visual('Amax')">
								<?php echo lang('maximumAbbr','c')?>
							</span>
							<span class="imgSubHeader" onclick="visual('Amin')">
								<?php echo lang('minimumAbbr','c')?>
							</span>
						</th>
						<th style="text-align:center!important">
							<span class="imgSubHeader" onclick="visual('R')">
								<?php echo lang('total','c')?>
							</span>
						</th>
						<?php
							if($solarSensor){
						?>
							<th style="text-align:center!important">
								<span class="imgSubHeader" onclick="visual('Savg')">
									<?php echo lang('avgAbbr','c')?>
								</span>
								<span class="imgSubHeader" onclick="visual('Smax')">
									<?php echo lang('maximumAbbr','c')?>
								</span>
							</th>
						<?php
							}
						?>
					</tr>
				</table>
				<div id="visualization" style="height:400px;margin-left:auto;margin-right:auto;width:90%">
				</div>
				<div style="width:100%;text-align:center">
					<input id="visualValues" value="<?php echo lang("show values",'c')?>" type="button" class="button2" style="font-size: 0.6em;font-family:'PT-Sans'">
				</div>
			</div>
		</div>
		</div>
		</div>
		
		<script>
			$(document).ready(function() {
				$(".showtimes").click(function(){
					$(this).next(".times").slideToggle(800);
				});
				$(".times").hide();
				$( "#tabs" ).tabs();
				$( "#tabs" ).tabs( "load", "reportYearlyExtremes.php?y=<?php echo $chosenYear?>" );
				$( "#tabs" ).tabs( "load", "reportYearlyMonths.php?y=<?php echo $chosenYear?>" );
				$( "#tabs" ).tabs( "load", "reportYearlyDays.php?y=<?php echo $chosenYear?>" );
			})
		</script>
		<script>
			function graph(parameter,interval){
				Highcharts.setOptions({
					global: {
						useUTC: false
					},
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>'],
					}	
				});
				optionsTmonth = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					title: {
						text:  '<?php echo lang('temperature','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('temperature','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayTempUnits) ?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average temperature",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{
							name: '<?php echo lang("apparent temperature",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot',
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang("range",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsTday = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					title: {
						text:  '<?php echo lang('temperature','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('temperature','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayTempUnits) ?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average temperature",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{
							name: '<?php echo lang("apparent temperature",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot',
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang("range",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsHmonth = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('humidity','c') ?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('humidity','c') ?>'
						},
						labels: {
							format: '{value} %'
						},
						max: 100,
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average humidity",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang("range",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsHday = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('humidity','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('humidity','c') ?>'
						},
						labels: {
							format: '{value} %'
						},
						max: 100,
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average humidity",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang("range",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsPmonth = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('pressure','c') ?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('pressure','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayPressUnits)?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average pressure",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsPday = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('pressure','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('pressure','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayPressUnits)?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average pressure",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsWmonth = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('wind','c') ?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('wind','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayWindUnits) ?>'
						},
						min: 0,
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average wind speed",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{
							name: '<?php echo lang("average wind gust",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot',
						},
						{
							name: '<?php echo lang("peak wind gust",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'Dash',
						},
					]
				};
				optionsWday = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('wind','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('wind','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayWindUnits) ?>'
						},
						min: 0,
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average wind speed",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{
							name: '<?php echo lang("average wind gust",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot',
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang("peak wind gust",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsDmonth = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('dew point','c') ?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('dew point','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayTempUnits)?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average dew point",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsDday = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('dew point','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: {
						title: {
							text: '<?php echo lang('dew point','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayTempUnits)?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("average dew point",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						},
					]
				};
				optionsRmonth = {
					chart : {
						renderTo : 'graph',
						type : 'column',
						zoomType: 'x',
					},
					title: {
						text:  '<?php echo lang('precipitation','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						type: 'xy',
						categories: [
							<?php
								foreach($data['availableMonths'] as $month){
									echo "'".lang("month".$month,'c')."',";
								}
							?>
						],
						title: {
							text: null
						},
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang('precipitation','c') ?>'
							},
							labels: {
								format: '{value} <?php echo unitFormatter($displayRainUnits)?>'
							},
						},
						{
							title: {
								text: '<?php echo lang('cumulative precipitation','c') ?>'
							},
							labels: {
								format: '{value} <?php echo unitFormatter($displayRainUnits)?>'
							},
							opposite: true,
						},
					],
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("precipitation",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areaspline',
							name: '<?php echo lang("cumulative precipitation",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: [],
							yAxis: 1
						},
					]
				};
				optionsRday = {
					chart : {
						renderTo : 'graph',
						type : 'column',
						zoomType: 'x',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('precipitation','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},	
						title: {
							text: null
						},
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang('precipitation','c') ?>'
							},
							labels: {
								format: '{value} <?php echo unitFormatter($displayRainUnits)?>'
							},
						},
						{
							title: {
								text: '<?php echo lang('cumulative precipitation','c') ?>'
							},
							labels: {
								format: '{value} <?php echo unitFormatter($displayRainUnits)?>'
							},
							opposite: true,
						},
					],
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							},
						},
						areasplinerange:{
							fillOpacity: 0.5,
						}								
					},
					tooltip: {
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang("precipitation",'c')?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
						},
						{	
							type: 'areaspline',
							name: '<?php echo lang("cumulative precipitation",'c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: [],
							yAxis: 1
						},
					]
				};
				<?php
					if($solarSensor){
				?>
						optionsSmonth = {
							chart : {
								renderTo : 'graph',
								type : 'spline',
								zoomType: 'x',
							},
							title: {
								text:  '<?php echo lang('solar radiation','c') ?>'
							},
							credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
							xAxis: {
								type: 'xy',
								categories: [
									<?php
										foreach($data['availableMonths'] as $month){
											echo "'".lang("month".$month,'c')."',";
										}
									?>
								],
								title: {
									text: null
								},
							},
							yAxis: {
								title: {
									text: '<?php echo lang('solar radiation','c') ?>'
								},
								labels: {
									format: '{value} W/m2'
								},
							},
							plotOptions: {
								series: {
									animation: {
										duration: 3000
									},
									marker: {
										enabled: false
									},
								},
								areasplinerange:{
									fillOpacity: 0.5,
								}								
							},
							tooltip: {
								shared: true,
							},
							series: [
								{
									name: '<?php echo lang("average solar radiation",'c')?>',
									data: [],
									color: "#<?php echo $color_schemes[$design]['font700']?>",
								},
								{
									name: '<?php echo lang("maxAbbr",'c')." ".lang("solar radiation",'c')?>',
									data: [],
									color: "#<?php echo $color_schemes[$design]['font700']?>",
									dashStyle: 'ShortDot',
								},
							]
						};
						optionsSday = {
							chart : {
								renderTo : 'graph',
								type : 'spline',
								zoomType: 'x',
							},
							credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
							title: {
								text:  '<?php echo lang('solar radiation','c') ?>'
							},
							xAxis: {
								type: 'datetime',
								dateTimeLabelFormats: {
									millisecond: '%H:%M:%S.%L',
									second: '%H:%M:%S',
									minute: '%H:%M',
									hour: '<?php echo $graphTimeFormat ?>',
									day: '<?php echo $graphDateFormat ?>',
									week: '<?php echo $graphDateFormat ?>',
									month: '%b / %y',
									year: '%Y'
								},	
								title: {
									text: null
								},
							},
							yAxis: {
								title: {
									text: '<?php echo lang('solar radiation','c') ?>'
								},
								labels: {
									format: '{value} W/m2'
								},
							},
							plotOptions: {
								series: {
									animation: {
										duration: 3000
									},
									marker: {
										enabled: false
									},
								},
								areasplinerange:{
									fillOpacity: 0.5,
								}								
							},
							tooltip: {
								shared: true,
							},
							series: [
								{
									name: '<?php echo lang("average solar radiation",'c')?>',
									data: [],
									color: "#<?php echo $color_schemes[$design]['font700']?>",
								},
								{
									name: '<?php echo lang("maxAbbr",'c')." ".lang("solar radiation",'c')?>',
									data: [],
									color: "#<?php echo $color_schemes[$design]['font700']?>",
									dashStyle: 'ShortDot',
								},
							]
						};
				<?php 
					}
				?>
				if(parameter=="T"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsTmonth.series[0].data = json['data1'];
								optionsTmonth.series[1].data = json['data2'];
								optionsTmonth.series[2].data = json['data3'];
								chart = new Highcharts.Chart(optionsTmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsTday.series[0].data = json['data1'];
								optionsTday.series[1].data = json['data2'];
								optionsTday.series[2].data = json['data3'];
								chart = new Highcharts.Chart(optionsTday);
							},
						});
					}
				}
				if(parameter=="H"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsHmonth.series[0].data = json['data1'];
								optionsHmonth.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsHmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsHday.series[0].data = json['data1'];
								optionsHday.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsHday);
							},
						});
					}
				}
				if(parameter=="P"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsPmonth.series[0].data = json['data1'];
								optionsPmonth.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsPmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsPday.series[0].data = json['data1'];
								optionsPday.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsPday);
							},
						});
					}
				}
				if(parameter=="W"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsWmonth.series[0].data = json['data1'];
								optionsWmonth.series[1].data = json['data2'];
								optionsWmonth.series[2].data = json['data3'];
								chart = new Highcharts.Chart(optionsWmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsWday.series[0].data = json['data1'];
								optionsWday.series[1].data = json['data2'];
								optionsWday.series[2].data = json['data3'];
								chart = new Highcharts.Chart(optionsWday);
							},
						});
					}
				}
				if(parameter=="D"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsDmonth.series[0].data = json['data1'];
								optionsDmonth.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsDmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsDday.series[0].data = json['data1'];
								optionsDday.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsDday);
							},
						});
					}
				}
				if(parameter=="R"){
					if(interval=="m"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsRmonth.series[0].data = json['data1'];
								optionsRmonth.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsRmonth);
							},
						});
					}
					if(interval=="d"){
						$.ajax({
							url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
							dataType : 'json',
							success : function (json) {
								optionsRday.series[0].data = json['data1'];
								optionsRday.series[1].data = json['data2'];
								chart = new Highcharts.Chart(optionsRday);
							},
						});
					}
				}
				<?php
					if($solarSensor){
				?>
						if(parameter=="S"){
							if(interval=="m"){
								$.ajax({
									url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
									dataType : 'json',
									success : function (json) {
										optionsSmonth.series[0].data = json['data1'];
										optionsSmonth.series[1].data = json['data2'];
										chart = new Highcharts.Chart(optionsSmonth);
									},
								});
							}
							if(interval=="d"){
								$.ajax({
									url : "reportYearlyGraphs.php?y=<?php echo $chosenYear?>&q="+parameter+"&interval="+interval,
									dataType : 'json',
									success : function (json) {
										optionsSday.series[0].data = json['data1'];
										optionsSday.series[1].data = json['data2'];
										chart = new Highcharts.Chart(optionsSday);
									},
								});
							}
						}
				<?php 
					}
				?>
			}
			function visual(parameter){
				Highcharts.setOptions({
					global: {
						useUTC: false
					},
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>'],
					}
				});
				optionsVisual = {
					chart : {
						renderTo : 'visualization',
						type : 'heatmap',
					},
					title: {
						text:  '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								for($i=1;$i<=31;$i++){
									echo "'".$i."',";
								}
							?>
						],
						title: {
							text: ''
						},
					},
					yAxis: {
						categories: [	
							<?php
								for($i=1;$i<13;$i++){
									echo "'".lang("month".$i,'c')."',";
								}
							?>
						],
						reversed: true,
						title: {
							text: ''
						},
					},
					colorAxis: {
						minColor: '',
						maxColor: '',
					},
					tooltip: {
						formatter: function () {
							x = this.point.x + 1;
							y = this.point.y + 1;
							return '<?php echo lang("month",'c')?>: '+y+'<br><?php echo lang("day",'c')?>: '+x+'<br>'+this.point.value;
						}
					},
					legend: {
						enabled: false,
					},
					series: [{
						name: '',
						borderWidth: 1,
						data: [],
						dataLabels: {
							enabled: false,
							color: '#FFFFFF'
						}
					}]
				};

				$.ajax({
					url : "reportYearlyVisualizations.php?y=<?php echo $chosenYear?>&q="+parameter,
					dataType : 'json',
					success : function (json) {
						optionsVisual.series[0].data = json['data1'];
						optionsVisual.series[0].name = json['name1'];
						optionsVisual.title.text = json['title1'];
						optionsVisual.colorAxis.minColor = json['mincolor1'];
						optionsVisual.colorAxis.maxColor = json['maxcolor1'];
						optionsVisual.series[0].dataLabels.color = json['labels1'];
						chart2 = new Highcharts.Chart(optionsVisual);
					},
				});
				var showValues = true;
				$('#visualValues').click(function() {
					if(showValues){
						$('#visualValues').val("<?php echo lang("hide values",'c')?>");
					}
					else{
						$('#visualValues').val("<?php echo lang("show values",'c')?>");
					}
					chart2.series[0].update({
						dataLabels: {
							enabled: showValues,
						}
					});
					showValues = !showValues;
				});				
			}
		</script>
		<?php include("../../../css/highcharts.php");?>
		<?php include("../../footer.php");?>
	</body>
</html>