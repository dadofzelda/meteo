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
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	
	
	
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");
	
	//error_reporting(E_ALL);

	// Get date
	$chosenDay = $_GET['d'];
	$chosenMonth = $_GET["m"];
	$chosenYear = $_GET["y"];
	
	// validate date and prevent SQL injection
	if($chosenDay<0 || $chosenDay>31){
		echo "Invalid date";
		die();
	}
	if($chosenMonth<1 || $chosenMonth>12){
		echo "Invalid date";
		die();
	}
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($chosenDay) || !is_numeric($chosenMonth) || !is_numeric($chosenYear)){
		echo "Invalid date";
		die();
	}
	
	$day = strtotime($chosenYear."-".$chosenMonth."-".$chosenDay);

	$dayYesterdayURL = "reportDaily.php?d=".(date("d",$day - 60 * 60 * 24))."%26m=".(date("m",$day - 60 * 60 * 24))."%26y=".(date("Y",$day - 60 * 60 * 24));
	if(($day+60*60*24)<=time()){ // prevent showing tomorrow if current day is today
		$dayTomorrowURL = "reportDaily.php?d=".(date("d",$day + 60 * 60 * 24))."%26m=".(date("m",$day + 60 * 60 * 24))."%26y=".(date("Y",$day + 60 * 60 * 24));
	}
	
	$Y = $chosenYear;
	$M = $chosenMonth;
	$D = $chosenDay;
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}
	
	
	/* #############################################################################*/
	// Calculate daily average, max, min, sd, range
	
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(Tmin), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(Tmin), stddev(Tmax), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR), max(R)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$dailyAvgT = convertT($row['avg(T)']);
		$dailyAvgH = $row['avg(H)'];
		$dailyAvgP = convertP($row['avg(P)']);
		$dailyAvgD = convertT($row['avg(D)']);
		$dailyAvgW = convertW($row['avg(W)']);
		$dailyAvgG = convertW($row['avg(G)']);
		$dailyAvgA = convertT($row['avg(A)']);
		$dailyAvgS = $row['avg(S)'];
		// daily maxima
		$dailyMaxT = convertT($row['max(Tmax)']);
		$dailyMaxH = $row['max(H)'];
		$dailyMaxP = convertP($row['max(P)']);
		$dailyMaxD = convertT($row['max(D)']);
		$dailyMaxW = convertW($row['max(W)']);
		$dailyMaxG = convertW($row['max(G)']);
		$dailyMaxA = convertT($row['max(A)']);
		$dailyMaxS = $row['max(S)'];
		// daily minima
		$dailyMinT = convertT($row['min(Tmin)']);
		$dailyMinH = $row['min(H)'];
		$dailyMinP = convertP($row['min(P)']);
		$dailyMinD = convertT($row['min(D)']);
		$dailyMinW = convertW($row['min(W)']);
		$dailyMinG = convertW($row['min(G)']);
		$dailyMinA = convertT($row['min(A)']);
		$dailyMinS = $row['min(S)'];
		// daily ranges
		$dailyRangeT = $dailyMaxT - $dailyMinT;
		$dailyRangeH = $dailyMaxH - $dailyMinH;
		$dailyRangeP = $dailyMaxP - $dailyMinP;
		$dailyRangeD = $dailyMaxD - $dailyMinD;
		$dailyRangeW = $dailyMaxW - $dailyMinW;
		$dailyRangeG = $dailyMaxG - $dailyMinG;
		$dailyRangeA = $dailyMaxA - $dailyMinA;
		$dailyRangeS = $dailyMaxS - $dailyMinS;
		// daily standard deviations
		$dailyStddevT = convertTStddev($row['stddev(T)']);
		$dailyStddevH = $row['stddev(H)'];
		$dailyStddevP = convertP($row['stddev(P)']);
		$dailyStddevD = convertTStddev($row['stddev(D)']);
		$dailyStddevW = convertW($row['stddev(W)']);
		$dailyStddevG = convertW($row['stddev(G)']);
		$dailyStddevA = convertTStddev($row['stddev(A)']);
		$dailyStddevS = $row['stddev(S)'];
		// daily rain total and maximum rain rate
		$dailyRTotal = convertR($row['max(R)']);
		$dailyMaxRR = convertR($row['max(RR)']);
	}
	
	// Calculate dates and times when max and min measured
	$result = mysqli_query($con,"
		SELECT *
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		"
	);
	while($row = mysqli_fetch_array($result)){
		if(convertT($row['T'])==$dailyMaxT){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxTDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['T'])==$dailyMinT){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinTDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['Tmax'])==$dailyMaxTmax){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxTmaxDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['Tmax'])==$dailyMinTmax){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinTmaxDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['Tmin'])==$dailyMaxTmin){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxTminDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['Tmin'])==$dailyMinTmin){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinTminDate[] = date($timeFormat, $date_temporary);
		}
		if($row['H']==$dailyMaxH){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxHDate[] = date($timeFormat, $date_temporary);
		}
		if($row['H']==$dailyMinH){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinHDate[] = date($timeFormat, $date_temporary);
		}
		if(convertP($row['P'])==$dailyMaxP){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxPDate[] = date($timeFormat, $date_temporary);
		}
		if(convertP($row['P'])==$dailyMinP){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinPDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['W'])==$dailyMaxW){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxWDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['W'])==$dailyMinW){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinWDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['G'])==$dailyMaxG){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxGDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['G'])==$dailyMinG){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinGDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['A'])==$dailyMaxA){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxADate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['A'])==$dailyMinA){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinADate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['D'])==$dailyMaxD){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxDDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['D'])==$dailyMinD){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinDDate[] = date($timeFormat, $date_temporary);
		}
		if($row['S']==$dailyMaxS){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxSDate[] = date($timeFormat, $date_temporary);
		}
		if($row['S']==$dailyMinS){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinSDate[] = date($timeFormat, $date_temporary);
		}
		if($row['RR']==$dailyMaxRR){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxRRDate[] = date($timeFormat, $date_temporary);
		}
	}
	
	// Separate calculation of average wind bearing
	$result = mysqli_query($con,"
		SELECT B, Hour(DateTime)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		"
	);
	while($row = mysqli_fetch_array($result)){
		$bearings[] = $row['B'];
		$hour = $row['Hour(DateTime)'];
		${"bearings".$hour}[] = $row['B'];
	}
	$dailyAvgB = avgWind($bearings);
	for($i=0;$i<24;$i++){
		if(isset(${"bearings".$i})){
			${"bearings".$i."avg"} = avgWind(${"bearings".$i});
		}
	}

	// Calculate daily wind run
	if($displayWindUnits=="kmh"){
		$dailyWindRun = round($dailyAvgW * 24,1);
		$dailyWindRunUnits = "km";
	}
	if($displayWindUnits=="ms"){
		$dailyWindRun = round($dailyAvgW * 3.6 * 24,1);
		$dailyWindRunUnits = "km";
	}
	if($displayWindUnits=="mph"){
		$dailyWindRun = round($dailyAvgW * 24,1);
		$dailyWindRunUnits = "mi";
	}
	if($displayWindUnits=="kt"){
		$tempW = convertor($dailyAvgW,"kt","mph");
		$dailyWindRun = round($tempW * 24,1);
		$dailyWindRunUnits = "mi";
	}
	
	// Calculate all year averages
	$result = mysqli_query($con,"
		SELECT T,H,P,D,W,G,A,S,DateTime,Tmax,Tmin
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M."
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$time = date($dateTimeFormat,strtotime($row['DateTime']));
		$Y = date("Y",strtotime($row['DateTime']));
		$overallAvgT[$time] = convertT($row['T']);
		$overallYT[$Y][] = convertT($row['T']);
		$overallMaxT[$time] = convertT($row['Tmax']);
		$overallMinT[$time] = convertT($row['Tmin']);
		$overallH[$time] = $row['H'];
		$overallYH[$Y][] = $row['H'];
		$overallP[$time] = convertP($row['P']);
		$overallYP[$Y][] = convertP($row['P']);
		$overallD[$time] = convertT($row['D']);
		$overallYD[$Y][] = convertT($row['D']);
		$overallW[$time] = convertW($row['W']);
		$overallYW[$Y][] = convertW($row['W']);
		$overallG[$time] = convertW($row['G']);
		$overallYG[$Y][] = convertW($row['G']);
		$overallA[$time] = convertT($row['A']);
		$overallYA[$Y][] = convertT($row['A']);
		$overallS[$time] = $row['S'];
		$overallYS[$Y][] = $row['S'];
		$overallRR[$time] = convertR($row['RR']);
		$overallYRR[$Y][] = convertR($row['RR']);
	}
	
	$result = mysqli_query($con,"
		SELECT max(R),YEAR(DateTime)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M."
		GROUP BY YEAR(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$time = $row['YEAR(DateTime)'];
		$overallR[$time] = convertR($row['max(R)']);
	}
	
	// group by years
	foreach($overallYT as $Y=>$yearValues){
		$annualT[$Y] = avg($yearValues);
	}
	foreach($overallYA as $Y=>$yearValues){
		$annualA[$Y] = avg($yearValues);
	}
	foreach($overallYD as $Y=>$yearValues){
		$annualD[$Y] = avg($yearValues);
	}
	foreach($overallYH as $Y=>$yearValues){
		$annualH[$Y] = avg($yearValues);
	}
	foreach($overallYP as $Y=>$yearValues){
		$annualP[$Y] = avg($yearValues);
	}
	foreach($overallYW as $Y=>$yearValues){
		$annualW[$Y] = avg($yearValues);
	}
	foreach($overallYG as $Y=>$yearValues){
		$annualG[$Y] = avg($yearValues);
	}
	foreach($overallYS as $Y=>$yearValues){
		$annualS[$Y] = avg($yearValues);
	}
	foreach($overallYRR as $Y=>$yearValues){
		$annualRR[$Y] = avg($yearValues);
	}
	
	function avg($arr){
		if(count($arr)>0){
			return array_sum($arr)/count($arr);
		}
		else{
			return "";
		}
	}
	function deviation($n1,$n2,$dp){
		$deviation = $n1 - $n2;
		$deviation = number_format($deviation,$dp,".","");
		if($deviation>0){
			return "+".$deviation;
		}
		else{
			return $deviation;
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("day report",'w')?></title>
		<?php metaHeader()?>

		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
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
			.clock{
				width: 20px;
				cursor: pointer;
				opacity: 0.8;
			}
			.showtimes:hover, .clock:hover, .sort:hover{
				opacity: 1;
			}
			.astro{
				width: 60px;
				opacity: 0.8;
			}
			<?php
				if($stationLat<0){ // invert Moon image if Southern hemisphere
			?>
				#moonImage{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
			<?php
				}
			?>	
			.inner-resizer {
				padding: 10px;
			}
			.resizer {   
				margin: 0 auto;
				width: 98%;	
			}
			.table th{
				text-align: center;
			}
			.tableFirstIcon{
				width: 25px;
				padding-left: 5px;
				padding-right: 5px;
			}
			.unitCell{
				text-align: left;
				font-variant: small-caps;
			}
			#summaryTable th{
				width: 8%;
			}
			.times{
				display:none;
			}
			.varSelectorIcon{
				width:40px;
				cursor: pointer;
				opacity: 0.8;
			}
			.varSelectorIcon:hover{
				opacity: 1;
			}
			#mtImage {
				width: 80px;
				-webkit-animation: rotation 2s infinite linear;
			}

			@-webkit-keyframes rotation {
				from {-webkit-transform: rotate(0deg);}
				to   {-webkit-transform: rotate(359deg);}
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
					<td style="width:5%">

					</td>
					<td style='text-align:right;padding-top:10px'>
						<a href="<?php echo $dayYesterdayURL?>"><input type="button" class="button2" style="font-weight:bold;font-variant:small-caps" value="<<< <?php echo date($dateFormat,$day-60*60*24)?>"></a>
					</td>
					<td style='text-align:center;vertical-align:top'>
						<h2><?php echo date($dateFormat,$day)?></h2>
					</td>
					<td style='text-align:left'>
						<?php 
							if(isset($dayTomorrowURL)){
						?>
								<a href="<?php echo $dayTomorrowURL?>"><input type="button" class="button2" style="font-weight:bold;font-variant:small-caps" value="<?php echo date($dateFormat,$day+60*60*24)?> >>>"></a>
						<?php
							}
						?>
					</td>
					<td style="width:5%;text-align:right">
						<a href="<?php echo $pageURL.$path?>/pages/station/reportDailyPDF.php?y=<?php echo $Y?>&m=<?php echo $M?>&d=<?php echo $D?>" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>
					</td>
				</tr>
			</table>
			<br>
			<div id="summary">
				<table class="table" id="summaryTable">
					<thead>
						<tr>
							<th colspan="2">								
							</th>
							<th>
								<?php echo lang('avgAbbr','c')?>
							</th>
							<th>
								<?php echo lang('maximumAbbr','c')?>
							</th>
							<th>
								<?php echo lang('minimumAbbr','c')?>
							</th>
							<th>
								<?php echo lang('range','c')?>
							</th>
							<th>
								<?php echo lang("sdAbbr",'')?>
							</th>
							<th>
								<?php echo lang("long-term",'')?>
							</th>
							<th>
								<?php echo lang("deviation",'')?>
							</th>
							<th>
								<?php echo lang("avgAbbr",'c')." ".lang('maximumAbbr','l')?>
							</th>
							<th>
								<?php echo lang("avgAbbr",'c')." ".lang('minimumAbbr','l')?>
							</th>
							<th>
								<?php echo lang("absolute",'c')." ".lang('maximumAbbr','l')?>
							</th>
							<th>
								<?php echo lang("absolute",'c')." ".lang('minimumAbbr','l')?>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 30px"> 
								<img src="<?php echo $pageURL.$path?>icons/temp.png" class="tableFirstIcon tooltip" title="<?php echo lang("temperature",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayTempUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgT,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxT,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxTDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinT,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinTDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeT,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevT,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallAvgT),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgT,avg($overallAvgT),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualT),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualT,max($annualT))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualT),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualT,min($annualT))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallMaxT),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallMaxT,max($overallMaxT))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallMinT),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallMinT,min($overallMinT))) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="tableFirstIcon tooltip" title="<?php echo lang("apparent temperature",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayTempUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgA,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxA,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxADate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinA,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinADate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeA,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevA,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallA),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgA,avg($overallA),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualA),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualA,max($annualA))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualA),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualA,min($annualA))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallA),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallA,max($overallA))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallA),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallA,min($overallA))) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="tableFirstIcon tooltip" title="<?php echo lang("dew point",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayTempUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgD,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxD,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxDDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinD,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinDDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeD,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevD,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallD),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgD,avg($overallD),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualD),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualD,max($annualD))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualD),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualD,min($annualD))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallD),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallD,max($overallD))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallD),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallD,min($overallD))) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="tableFirstIcon tooltip" title="<?php echo lang("humidity",'c')?>">
							</td>
							<td class="unitCell">
								%
							</td>
							<td>
								<?php echo round($dailyAvgH,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxH,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxHDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinH,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinHDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeH,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevH,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallH),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgH,avg($overallH),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualH),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualH,max($annualH))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualH),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualH,min($annualH))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallH),0,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallH,max($overallH))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallH),0,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallH,min($overallH))) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="tableFirstIcon tooltip" title="<?php echo lang("pressure",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayPressUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgP,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxP,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxPDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinP,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinPDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeP,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevP,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallP),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgP,avg($overallP),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualP),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualP,max($annualP))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualP),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualP,min($annualP))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallP),$decimalsP,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallP,max($overallP))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallP),$decimalsP,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallP,min($overallP))) ?>
								</div>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/wind.png" class="tableFirstIcon tooltip" title="<?php echo lang("wind speed",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayWindUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgW,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxW,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxWDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinW,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinWDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeW,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevW,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallW),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgW,avg($overallW),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualW),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualW,max($annualW))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualW),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualW,min($annualW))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallW),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallW,max($overallW))) ?>
								</div>
							</td>
							<td>

							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/gust.png" class="tableFirstIcon tooltip" title="<?php echo lang("wind gust",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayWindUnits)?>
							</td>
							<td>
								<?php echo round($dailyAvgG,2)?>
							</td>
							<td>
								<?php echo round($dailyMaxG,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxGDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyMinG,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMinGDate) ?>
								</div>
							</td>
							<td>
								<?php echo round($dailyRangeG,1)?>
							</td>
							<td>
								<?php echo round($dailyStddevG,2)?>
							</td>
							<td>
								<?php echo number_format(avg($overallG),2,".","") ?>
							</td>
							<td>
								<?php echo deviation($dailyAvgG,avg($overallG),2) ?>
							</td>
							<td>
								<?php echo number_format(max($annualG),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualG,max($annualG))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualG),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualG,min($annualG))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallG),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallG,max($overallG))) ?>
								</div>
							</td>
							<td>

							</td>
						</tr>
						<tr>
							<td>
							</td>
							<td>
								
							</td>
							<td>
								<?php echo round($dailyAvgB)?>°
							</td>
							<td colspan="10">
							</td>
						</tr>
						<?php if($solarSensor){?>
							<tr>
								<td> 
									<img src="<?php echo $pageURL.$path?>icons/sun.png" class="tableFirstIcon tooltip" title="<?php echo lang("solar radiation",'c')?>">
								</td>
								<td class="unitCell">
									W/m<sup>2</sup>
								</td>
								<td>
									<?php echo round($dailyAvgS,2)?>
								</td>
								<td>
									<?php echo round($dailyMaxS,1)?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times">
										<br><?php echo implode("<br>",$dailyMaxSDate) ?>
									</div>
								</td>
								<td>
									<?php echo round($dailyMinS,1)?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times">
										<br><?php echo implode("<br>",$dailyMinSDate) ?>
									</div>
								</td>
								<td>
									<?php echo round($dailyRangeS,1)?>
								</td>
								<td>
									<?php echo round($dailyStddevS,2)?>
								</td>
								<td>
									<?php echo number_format(avg($overallS),1,".","") ?>
								</td>
								<td>
									<?php echo deviation($dailyAvgS,avg($overallS),1) ?>
								</td>
								<td>
									<?php echo number_format(max($annualS),1,".","") ?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times" style="font-size:0.8">
										<?php echo implode("<br>",array_keys($annualS,max($annualS))) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(min($annualS),1,".","") ?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times" style="font-size:0.8">
										<?php echo implode("<br>",array_keys($annualS,min($annualS))) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(max($overallS),0,".","") ?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times" style="font-size:0.8">
										<?php echo implode("<br>",array_keys($overallS,max($overallS))) ?>
									</div>
								</td>
								<td>
									<?php echo number_format(min($overallS),0,".","") ?>
									<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
									<div class="times" style="font-size:0.8">
										<?php echo implode("<br>",array_keys($overallS,min($overallS))) ?>
									</div>
								</td>
							</tr>
						<?php } ?>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/rain.png" class="tableFirstIcon tooltip" title="<?php echo lang("rain rate",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
							</td>
							<td>
								
							</td>
							<td>
								<?php echo round($dailyMaxRR,1)?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times">
									<br><?php echo implode("<br>",$dailyMaxRRDate) ?>
								</div>
							</td>
							<td colspan="5">
							</td>
							<td>
								<?php echo number_format(max($annualRR),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualRR,max($annualRR))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($annualRR),1,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($annualRR,min($annualRR))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(max($overallRR),2,".","") ?>
							</td>
							<td></td>
						</tr>
						<tr>
							<td colspan="13" style="text-align:center;font-varian:small-caps;border-bottom:white;font-weight:bold">
								<?php echo lang('total','c')?>
							</td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/rain.png" class="tableFirstIcon tooltip" title="<?php echo lang("precipitation",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo unitformatter($displayRainUnits)?>
							</td>
							<td colspan="5">
								<?php echo $dailyRTotal ?>
							</td>
							<td>
								<?php echo number_format(avg($overallR),$decimalsR,".","")?>
							</td>
							<td>
								<?php echo deviation($dailyRTotal,avg($overallR),$decimalsR)?>
							</td>
							<td>
								<?php echo number_format(max($overallR),$decimalsR,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallR,max($overallR))) ?>
								</div>
							</td>
							<td>
								<?php echo number_format(min($overallR),$decimalsR,".","") ?>
								<img src="<?php echo $pageURL.$path?>icons/time.png" class="showtimes" alt=''>
								<div class="times" style="font-size:0.8">
									<?php echo implode("<br>",array_keys($overallR,min($overallR))) ?>
								</div>
							</td>
							<td colspan="2"></td>
						</tr>
						<tr>
							<td> 
								<img src="<?php echo $pageURL.$path?>icons/wind.png" class="tableFirstIcon tooltip" title="<?php echo lang("w",'c')?>">
							</td>
							<td class="unitCell">
								<?php echo $dailyWindRunUnits?>
							</td>
							<td colspan="5">
								<?php echo round($dailyWindRun,2) ?>
							</td>
							<td colspan="6"></td>
						</tr>
					</tbody>
				</table>
				<br>
			</div>
			<div style="width:96%;padding:2%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['700']?>;border-radius:10px">
				<table style="width:100%">
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/daynight.png" class="varSelectorIcon tooltip" id="varSelectorAstro" title="<?php echo lang('astronomy','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/graph.png" class="varSelectorIcon tooltip" id="varSelectorGraph" title="<?php echo lang('summary graph','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="varSelectorIcon tooltip" id="varSelectorT" title="<?php echo lang('temperature','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="varSelectorIcon tooltip" id="varSelectorA" title="<?php echo lang('apparent temperature','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="varSelectorIcon tooltip" id="varSelectorD" title="<?php echo lang('dewpoint','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('humidity','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="varSelectorIcon tooltip" id="varSelectorP" title="<?php echo lang('pressure','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="varSelectorIcon tooltip" id="varSelectorW" title="<?php echo lang('wind speed','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/gust.png" class="varSelectorIcon tooltip" id="varSelectorG" title="<?php echo lang('wind gust','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="varSelectorIcon tooltip" id="varSelectorR" title="<?php echo lang('precipitation','c')?>">
						</td>
						<?php 
							if($solarSensor){
						?>
								<td>
									<img src="<?php echo $pageURL.$path?>icons/sun.png" class="varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('solar radiation','c')?>">
								</td>
						<?php 
							}
						?>
					</tr>
				</table>
			</div>
			<br>
			<div id="varDiv" style="width:98%;margin:0 auto;text-align:center"></div>
			</div>
			<br><br>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".varSelectorIcon").click(function(){
					$("#varDiv").html("<br><br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' id='mtImage'></img>");
					id = $(this).attr("id");
					id = id.replace("varSelector","");
					if(id!="Astro" && id!="Graph"){
						$("#varDiv").load("reportDailyLoad.php?var="+id+"&y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>&d=<?php echo $chosenDay?>");
					}
					if(id=="Astro"){
						$("#varDiv").load("reportDailyAstro.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>&d=<?php echo $chosenDay?>");
					}
					if(id=="Graph"){
						$("#varDiv").load("reportDailyGraph.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>&d=<?php echo $chosenDay?>");
					}
				})
				$(".showtimes").click(function(){
					$(this).next(".times").slideToggle(800);
				});
			});
		</script>
		<?php include("../../../css/highcharts.php");?>
		<?php include("../../footer.php");?>
	</body>
</html>