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
	#	Night calculations
	#
	# 	A script that generates data for hourly apparent temperature tables
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
	
	//error_reporting(E_ALL);
	
	if(isset($_GET['var'])){
		$var = $_GET['var'];
	}
	else{
		die("No parameter specified.");
	}
	
	
	// assume valid
	$validFrom = true;
	$validTo = true;
	
	// check from/to and prevent SQL injection
	if(isset($_GET['from'])){
		if(!is_numeric($_GET['from'])){
			$validFrom = false;
		}
		else{
			if($_GET['from']<0 || $_GET['from']>23){
				$validFrom = false;
			}
		}
	}
	else{
		$validFrom = false;
	}
	if(isset($_GET['to'])){
		if(!is_numeric($_GET['to'])){
			$validTo = false;
		}
		else{
			if($_GET['to']<0 || $_GET['to']>23){
				$validTo = false;
			}
		}
	}
	else{
		$validTo = false;
	}
	// if not valid, set to default 19 and 7
	if(!$validFrom){
		$from = 19;
	}
	else{
		$from = trim($_GET['from']);
	}
	if(!$validTo){
		$to = 7;
	}
	else{
		$to = trim($_GET['to']);
	}
	
	$hourShift = $from - $to;
	
	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCols = array("T","Tmax","Tmin");
		$colors['min'] = "#4cd2ff";
		$colors['max'] = "#ff9673";
		$UoM = unitFormatter($displayTempUnits);
		$dp = 1;
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCols = array("A","A","A");
		$colors['min'] = "#4cd2ff";
		$colors['max'] = "#ff9673";
		$UoM = unitFormatter($displayTempUnits);
		$dp = 1;
	}
	if($var=="D"){
		$heading = lang("dew point",'c');
		$mySQLCols = array("D","D","D");
		$colors['min'] = "#4cd2ff";
		$colors['max'] = "#ff9673";
		$UoM = unitFormatter($displayTempUnits);
		$dp = 1;
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCols = array("H","H","H");
		$colors['min'] = "#ffd24c";
		$colors['max'] = "#4cff4c";
		$UoM = "%";
		$dp = 0;
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCols = array("P","P","P");
		$colors['min'] = "#ffa64c";
		$colors['max'] = "#dc73ff";
		$UoM = unitFormatter($displayPressUnits);
		if($displayPressUnits=="inhg"){
			$dp = 2;
		}
		else{
			$dp = 1;
		}
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCols = array("W","W","W");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#dc73ff";
		$UoM = unitFormatter($displayWindUnits);
		$dp = 1;
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCols = array("G","G","G");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#dc73ff";
		$UoM = unitFormatter($displayWindUnits);
		$dp = 1;
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCols = array("S","S","S");
		$colors['min'] = "#bbbbbb";
		$colors['max'] = "#ffff73";
		$UoM = "W/m2";
		$dp = 0;
	}
	
	
	// get daily stats
	$query = "
		SELECT AVG(".$mySQLCols[0]."), MAX(".$mySQLCols[1]."), MIN(".$mySQLCols[2]."), DATE_SUB(Datetime, INTERVAL ".$to." HOUR)
		FROM alldata
		WHERE HOUR(DATE_SUB(Datetime, INTERVAL ".$to." HOUR))>=".$hourShift."
		GROUP BY YEAR(DATE_SUB(Datetime, INTERVAL ".$to." HOUR)), MONTH(DATE_SUB(Datetime, INTERVAL ".$to." HOUR)), DAY(DATE_SUB(Datetime, INTERVAL ".$to." HOUR))
		ORDER BY Datetime
	";
	
	$firstDay = "";
	
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		if($firstDay==""){
			$firstDay = strtotime($row['DATE_SUB(Datetime, INTERVAL '.$to.' HOUR)']);
		}
		$temporaryDay = date("Y-m-d",strtotime($row['DATE_SUB(Datetime, INTERVAL '.$to.' HOUR)']));
		$dayStats[$temporaryDay]['avg'] = chooseConvertor($row['AVG('.$mySQLCols[0].')']);
		$dayStats[$temporaryDay]['max'] = chooseConvertor($row['MAX('.$mySQLCols[1].')']);
		$dayStats[$temporaryDay]['min'] = chooseConvertor($row['MIN('.$mySQLCols[2].')']);
		$dayStats[$temporaryDay]['date'] = $temporaryDay;
	}
	
	// check that first day is complete night
	$firstDayHours = array();
	$firstDayValid = true;
	$query = "
		SELECT DISTINCT HOUR(Datetime)
		FROM alldata
		WHERE YEAR(Datetime)=".date("Y",$firstDay)." AND MONTH(DateTime)=".date("m",$firstDay)." AND DAY(DateTime)=".date("d",$firstDay)."
	";
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		$firstDayHours[] = $row['HOUR(Datetime)'];
	}
	if(count($firstDayHours)==0){
		$firstDayValid = false;
	}
	else{
		if(min($firstDayHours)>$from){
			$firstDayValid = false;
		}
	}
	// if first night is not complete, delete it
	if(!$firstDayValid){
		unset($dayStats[date("Y-n-d",($firstDay))]);
	}
	
	// group by months and years
	foreach($dayStats as $dayDate=>$values){
		$temporaryDate = strtotime($dayDate);
		$y = date("Y",$temporaryDate);
		$m = date("n",$temporaryDate);
		$sortedStats[$y][$m][] = $values;
	}
	
	// all month stats
	foreach($sortedStats as $y=>$yearData){
		foreach($yearData as $m=>$monthData){
			$temporaryMaximum = -9999;
			$temporaryMinimum = 9999;
			$temporaryAvgMaximum = -9999;
			$temporaryAvgMinimum = 9999;
			$temporaryMaximumDates = array();
			$temporaryMinimumDates = array();
			$temporaryAvgMaximumDates = array();
			$temporaryAvgMinimumDates = array();
			$averageSum = 0;
			$dayCount = 0;
			foreach($monthData as $d=>$dayData){
				$averageSum += $dayData['avg'];
				$yearSums[$y][] = $dayData['avg'];
				$dayCount++;
				// maximum
				if($temporaryMaximum<$dayData['max']){
					$temporaryMaximum = $dayData['max'];
					$temporaryMaximumDates = array($dayData['date']);
				}
				else if($temporaryMaximum==$dayData['max']){
					$temporaryMaximumDates[] = $dayData['date'];
				}
				// minimum
				if($temporaryMinimum>$dayData['min']){
					$temporaryMinimum = $dayData['min'];
					$temporaryMinimumDates = array($dayData['date']);
				}
				else if($temporaryMinimum==$dayData['min']){
					$temporaryMinimumDates[] = $dayData['date'];
				}
				// average
				if($temporaryAvgMaximum<$dayData['avg']){
					$temporaryAvgMaximum = $dayData['avg'];
					$temporaryAvgMaximumDates = array($dayData['date']);
				}
				else if($temporaryAvgMaximum==$dayData['avg']){
					$temporaryAvgMaximumDates[] = $dayData['date'];
				}
				if($temporaryAvgMinimum>$dayData['avg']){
					$temporaryAvgMinimum = $dayData['avg'];
					$temporaryAvgMinimumDates = array($dayData['date']);
				}
				else if($temporaryAvgMinimum==$dayData['avg']){
					$temporaryAvgMinimumDates[] = $dayData['date'];
				}
			}
			$monthArray[$y][$m]['max'] = $temporaryMaximum;
			$monthArray[$y][$m]['maxDates'] = $temporaryMaximumDates;
			$monthArray[$y][$m]['min'] = $temporaryMinimum;
			$monthArray[$y][$m]['minDates'] = $temporaryMinimumDates;
			$monthArray[$y][$m]['avg'] = $averageSum/$dayCount;
			$monthArray[$y][$m]['maxAvg'] = $temporaryAvgMaximum;
			$monthArray[$y][$m]['maxAvgDates'] = $temporaryAvgMaximumDates;
			$monthArray[$y][$m]['minAvg'] = $temporaryAvgMinimum;
			$monthArray[$y][$m]['minAvgDates'] = $temporaryAvgMinimumDates;
		}
	}
	foreach($yearSums as $y=>$data){
		$yearArray[$y]['avg'] = array_sum($data)/count($data);
	}
	// all year stats
	foreach($monthArray as $y=>$yearData){
		$temporaryMaximum = -9999;
		$temporaryMinimum = 9999;
		$temporaryAvgMaximum = -9999;
		$temporaryAvgMinimum = 9999;
		$temporaryMaximumDates = array();
		$temporaryMinimumDates = array();
		$temporaryAvgMaximumDates = array();
		$temporaryAvgMinimumDates = array();
		foreach($yearData as $m=>$monthData){
			if($temporaryMaximum<$monthData['max']){
				$temporaryMaximum = $monthData['max'];
				$temporaryMaximumDates = $monthData['maxDates'];
			}
			else if($temporaryMaximum==$monthData['max']){
				$temporaryMaximumDates = array_merge($monthData['maxDates'],$temporaryMaximumDates);
			}
			if($temporaryMinimum>$monthData['min']){
				$temporaryMinimum = $monthData['min'];
				$temporaryMinimumDates = $monthData['minDates'];
			}
			else if($temporaryMinimum==$monthData['min']){
				$temporaryMinimumDates = array_merge($monthData['minDates'],$temporaryMinimumDates);
			}
			// average
			if($temporaryAvgMaximum<$monthData['maxAvg']){
				$temporaryAvgMaximum = $monthData['maxAvg'];
				$temporaryAvgMaximumDates = $monthData['maxAvgDates'];
			}
			else if($temporaryAvgMaximum==$monthData['maxAvg']){
				$temporaryAvgMaximumDates = array_merge($monthData['maxAvgDates'],$temporaryAvgMaximumDates);
			}
			if($temporaryAvgMinimum>$monthData['minAvg']){
				$temporaryAvgMinimum = $monthData['minAvg'];
				$temporaryAvgMinimumDates = $monthData['minAvgDates'];
			}
			else if($temporaryAvgMinimum==$monthData['minAvg']){
				$temporaryAvgMinimumDates[] = array_merge($monthData['minAvgDates'],$temporaryAvgMinimumDates);
			}
		}
		$yearArray[$y]['max'] = $temporaryMaximum;
		$yearArray[$y]['maxDates'] = $temporaryMaximumDates;
		$yearArray[$y]['min'] = $temporaryMinimum;
		$yearArray[$y]['minDates'] = $temporaryMinimumDates;
		$yearArray[$y]['maxAvg'] = $temporaryAvgMaximum;
		$yearArray[$y]['maxAvgDates'] = $temporaryAvgMaximumDates;
		$yearArray[$y]['minAvg'] = $temporaryAvgMinimum;
		$yearArray[$y]['minAvgDates'] = $temporaryAvgMinimumDates;
	}
	
	// all over stats
	$temporaryMaximum = -9999;
	$temporaryMinimum = 9999;
	$temporaryAvgMaximum = -9999;
	$temporaryAvgMinimum = 9999;
	$temporaryMaximumDates = array();
	$temporaryMinimumDates = array();
	$temporaryAvgMaximumDates = array();
	$temporaryAvgMinimumDates = array();
	$averageSum = 0;
	$yearCount = 0;
	foreach($yearArray as $y=>$yearData){
		$averageSum += $yearData['avg'];
		$yearCount++;
		if($temporaryMaximum<$yearData['max']){
			$temporaryMaximum = $yearData['max'];
			$temporaryMaximumDates = $yearData['maxDates'];
		}
		else if($temporaryMaximum==$yearData['max']){
			$temporaryMaximumDates = array_merge($yearData['maxDates'],$temporaryMaximumDates);
		}
		if($temporaryMinimum>$yearData['min']){
			$temporaryMinimum = $yearData['min'];
			$temporaryMinimumDates = $yearData['minDates'];
		}
		else if($temporaryMinimum==$yearData['min']){
			$temporaryMinimumDates = array_merge($yearData['minDates'],$temporaryMinimumDates);
		}
		if($temporaryAvgMaximum<$yearData['maxAvg']){
			$temporaryAvgMaximum = $yearData['maxAvg'];
			$temporaryAvgMaximumDates = $yearData['maxAvgDates'];
		}
		else if($temporaryAvgMaximum==$yearData['maxAvg']){
			$temporaryAvgMaximumDates = array_merge($yearData['maxAvgDates'],$temporaryAvgMaximumDates);
		}
		if($temporaryAvgMinimum>$yearData['minAvg']){
			$temporaryAvgMinimum = $yearData['minAvg'];
			$temporaryAvgMinimumDates = $yearData['minAvgDates'];
		}
		else if($temporaryAvgMinimum==$yearData['minAvg']){
			$temporaryAvgMinimumDates[] = array_merge($yearData['minAvgDates'],$temporaryAvgMinimumDates);
		}
	}
	$allArray['max'] = $temporaryMaximum;
	$allArray['maxDates'] = $temporaryMaximumDates;
	$allArray['min'] = $temporaryMinimum;
	$allArray['minDates'] = $temporaryMinimumDates;
	$allArray['avg'] = $averageSum/$yearCount;
	$allArray['maxAvg'] = $temporaryAvgMaximum;
	$allArray['maxAvgDates'] = $temporaryAvgMaximumDates;
	$allArray['minAvg'] = $temporaryAvgMinimum;
	$allArray['minAvgDates'] = $temporaryAvgMinimumDates;
	
	// month stats
	foreach($sortedStats as $y=>$yearData){
		foreach($yearData as $m=>$monthData){
			$m = $m*1;
			foreach($monthData as $d=>$dayData){
				$monthSums[$m][] = $dayData['avg'];
				if(!isset($allMonths[$m]['max'])){
					$allMonths[$m]['max'] = $dayData['max'];
					$allMonths[$m]['maxDates'] = array($dayData['date']);
				}
				else{
					if($allMonths[$m]['max']<$dayData['max']){
						$allMonths[$m]['max'] = $dayData['max'];
						$allMonths[$m]['maxDates'] = array($dayData['date']);
					}
					else if($allMonths[$m]['max']==$dayData['max']){
						$allMonths[$m]['maxDates'][] = $dayData['date'];
					}
				}
				if(!isset($allMonths[$m]['min'])){
					$allMonths[$m]['min'] = $dayData['min'];
					$allMonths[$m]['minDates'] = array($dayData['date']);
				}
				else{
					if($allMonths[$m]['min']>$dayData['min']){
						$allMonths[$m]['min'] = $dayData['min'];
						$allMonths[$m]['minDates'] = array($dayData['date']);
					}
					else if($allMonths[$m]['min']==$dayData['min']){
						$allMonths[$m]['minDates'][] = $dayData['date'];
					}
				}
				if(!isset($allMonths[$m]['maxAvg'])){
					$allMonths[$m]['maxAvg'] = $dayData['avg'];
					$allMonths[$m]['maxAvgDates'] = array($dayData['date']);
				}
				else{
					if($allMonths[$m]['maxAvg']<$dayData['avg']){
						$allMonths[$m]['maxAvg'] = $dayData['avg'];
						$allMonths[$m]['maxAvgDates'] = array($dayData['date']);
					}
					else if($allMonths[$m]['maxAvg']==$dayData['avg']){
						$allMonths[$m]['maxAvgDates'][] = $dayData['date'];
					}
				}
				if(!isset($allMonths[$m]['minAvg'])){
					$allMonths[$m]['minAvg'] = $dayData['avg'];
					$allMonths[$m]['minAvgDates'] = array($dayData['date']);
				}
				else{
					if($allMonths[$m]['minAvg']>$dayData['avg']){
						$allMonths[$m]['minAvg'] = $dayData['avg'];
						$allMonths[$m]['minAvgDates'] = array($dayData['date']);
					}
					else if($allMonths[$m]['minAvg']==$dayData['avg']){
						$allMonths[$m]['minAvgDates'][] = $dayData['date'];
					}
				}
			}
		}
	}
	
	foreach($monthSums as $m=>$sumValues){
		$allMonths[$m]['avg'] = array_sum($sumValues)/count($sumValues);
	}
	
	// create array with absolute values
	foreach($allMonths as $month=>$values){
		$monthAvgs[lang("month".$month,'c')] = $values['avg'];
	}
	$warmestMonth = array_keys($monthAvgs,max($monthAvgs));
	$coldestMonth = array_keys($monthAvgs,min($monthAvgs));
	
	// find absolute max/min months
	foreach($monthArray as $y=>$yearData){
		foreach($yearData as $m=>$monthData){
			$monthsFullArray[$y."/".$m] = $monthData['avg'];
		}
	}
	$absWarmestMonth = array_keys($monthsFullArray,max($monthsFullArray));
	$absColdestMonth = array_keys($monthsFullArray,min($monthsFullArray));
	
	// hourly stats
	$query = "
		SELECT AVG(".$mySQLCols[0]."), MAX(".$mySQLCols[1]."), MIN(".$mySQLCols[2]."), HOUR(DateTime)
		FROM alldata
		WHERE HOUR(DateTime)>=".$from." OR HOUR(DateTime)<".$to."
		GROUP BY HOUR(DateTime)
		ORDER BY Datetime
	";
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		$h = $row["HOUR(DateTime)"];
		$hourData['avg'][$h] = chooseConvertor($row['AVG('.$mySQLCols[0].')']);
		$hourData['max'][$h] = chooseConvertor($row['MAX('.$mySQLCols[1].')']);
		$hourData['min'][$h] = chooseConvertor($row['MIN('.$mySQLCols[2].')']);
	}
	// reorder data
	for($i=$from;$i<24;$i++){
		$theoretical[$i] = $i;
	}
	for($i=0;$i<$to;$i++){
		$theoretical[$i] = $i;
	}
	foreach($theoretical as $key=>$value){
		if(array_key_exists($key,$hourData['avg'])){
			$availableHours[] = $key;
		}
	}
	
	function dateChecker($arr,$exploder){
		global $dateFormat;
		if(count($arr)>6){
			return lang('more than 5 instances','l');
		}
		else{
			for($i=0;$i<count($arr);$i++){
				$formattedArr[] = date($dateFormat,strtotime($arr[$i]));
			}
			return implode($exploder,$formattedArr);
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
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<style>
			.periodSelector{
				cursor: pointer;
				font-weight: bold;
				font-variant: small-caps;
				padding: 5px;
				border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
				background: #<?php echo $color_schemes[$design2]['700']?>;
				border-radius: 10px;
				width: 50%;
				text-align:center;
			}
			.periodSelector:hover{
				background: #<?php echo $color_schemes[$design2]['400']?>;
			}
			.sectorDiv{
				width: 98%;
				padding-top: 10px;
				margin: 0 auto;
				display: none;
			}
			.table th{
				text-align: center;
			}
			#nightSelectorTable td{
				width:16.66%;
				text-align:center;
			}
		</style>
	</head>
	<body>		
		<table style="width:98%;margin:0 auto" id="nightSelectorTable">
			<tr>
				<td>
					<div class="periodSelector" id="selectorAlltime"><?php echo lang('alltime','c')?></div>
				</td>
				<td>
					<div class="periodSelector" id="selectorHours"><?php echo lang('hourly averages','c')?></div>
				</td>
				<td>
					<div class="periodSelector" id="selectorMonths"><?php echo lang('monthly averages','c')?></div>
				</td>
				<td>
					<div class="periodSelector" id="selectorYears"><?php echo lang('yearly averages','c')?></div>
				</td>
				<td>
					<div class="periodSelector" id="selectorAllMonths"><?php echo lang('all months','c')?></div>
				</td>
				<td>
					<div class="periodSelector" id="selectorDays"><?php echo lang('all values','c')?></div>
				</td>
			</tr>
		</table>
		<div id="alltimeDiv" class="sectorDiv">
			<br>
			<table class="table" style="width:100%; margin: 0 auto" cellpadding="2">
				<tbody>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('alltime average','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo number_format($allArray['avg'],($dp+1),".","")." ".$UoM;?></strong>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('alltime maximum night average','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo number_format($allArray['maxAvg'],($dp+1),".","")." ".($UoM);?></strong><br>
							<?php
								echo dateChecker($allArray['maxAvgDates'],", ");
							?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('alltime minimum night average','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo number_format($allArray['minAvg'],($dp+1),".","")." ".($UoM);?></strong><br>
							<?php
								echo dateChecker($allArray['minAvgDates'],", ");
							?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('absolute maximum','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo number_format($allArray['max'],($dp),".","")." ".($UoM);?></strong><br>
							<?php
								echo dateChecker($allArray['maxDates'],", ");
							?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('absolute minimum','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo number_format($allArray['min'],($dp),".","")." ".($UoM);?></strong><br>
							<?php
								echo dateChecker($allArray['minDates'],", ");
							?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('alltime maximum monthly average','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo implode(", ",$warmestMonth)?></strong><br>
							<?php echo number_format(max($monthAvgs),($dp+1),".","")." ".($UoM)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('alltime minimum monthly average','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo implode(", ",$coldestMonth)?></strong><br>
							<?php echo number_format(min($monthAvgs),($dp+1),".","")." ".($UoM)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left;font-variant:small-caps;font-weight:bold">
							<?php echo lang('absolute monthly maximum','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo implode(", ",$absWarmestMonth)?></strong><br>
							<?php echo number_format(max($monthsFullArray),2,".","")." ".($UoM)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:lef;font-variant:small-caps;font-weight:bold">
							<?php echo lang('absolute monthly minimum','c')?>
						</td>
						<td style="text-align:right">
							<strong><?php echo implode(", ",$absColdestMonth)?></strong><br>
							<?php echo number_format(min($monthsFullArray),($dp+1),".","")." ".($UoM)?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<div id="hoursDiv" class="sectorDiv">
			<div class="resizer">
				<div class="inner-resizer">
					<div id="hoursGraph" style="width:100%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br><br>
			<table style="width:98%;margin:0 auto" class="table">
				<thead>
					<tr>
						<th>
							<?php echo lang('hour','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="width:18%">
							<?php echo lang('average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('absolute maximum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('absolute minimum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($availableHours as $h=>$value){
					?>
							<tr>
								<td style="text-align:center;font-weight:bold">
									<?php echo $value?>
								</td>
								<td style="text-align:center">
									<?php echo number_format($hourData['avg'][$value],($dp+1),".","")?>
								</td>
								<td style="text-align:center">
									<?php echo number_format($hourData['max'][$value],($dp),".","")?>
								</td>
								<td style="text-align:center">
									<?php echo number_format($hourData['min'][$value],($dp),".","")?>
								</td>	
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<div id="monthsDiv" class="sectorDiv">
			<div class="resizer">
				<div class="inner-resizer">
					<div id="monthsGraph" style="width:100%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br><br>
			<table style="width:98%;margin:0 auto" class="table">
				<thead>
					<tr>
						<th>
							<?php echo lang('month','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="width:18%">
							<?php echo lang('average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('maximum night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('minimum night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute maximum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute minimum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($allMonths as $m=>$values){
					?>
							<tr>
								<td style="text-align:center">
									<?php echo $m?>
								</td>
								<td style="font-weight:bold;width:9%">
									<?php echo number_format($values['avg'],($dp+1),".","")?>
								</td>
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['maxAvg'],($dp+1),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['maxAvgDates'],"<br>")?>
								</td>	
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['minAvg'],($dp+1),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['minAvgDates'],"<br>")?>
								</td>
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['max'],($dp),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['maxDates'],"<br>")?>
								</td>	
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['min'],($dp),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['minDates'],"<br>")?>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<div id="yearsDiv" class="sectorDiv">
			<div class="resizer">
				<div class="inner-resizer">
					<div id="yearGraph" style="width:98%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br><br>
			<table style="width:98%;margin:0 auto" class="table">
				<thead>
					<tr>
						<th>
							<?php echo lang('year','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="width:18%">
							<?php echo lang('average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('maximum night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('minimium night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute maximum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute minimum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($yearArray as $y=>$values){
					?>
							<tr>
								<td style="text-align:center">
									<?php echo $y?>
								</td>
								<td style="font-weight:bold;width:9%">
									<?php echo number_format($values['avg'],($dp+1),".","")?>
								</td>
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['maxAvg'],($dp+1),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['maxAvgDates'],"<br>")?>
								</td>	
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['minAvg'],($dp+1),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['minAvgDates'],"<br>")?>
								</td>
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['max'],($dp),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['maxDates'],"<br>")?>
								</td>	
								<td style="text-align:right;font-weight:bold;width:9%">
									<?php echo number_format($values['min'],($dp),".","")?>
								</td>
								<td style="text-align:left;width:9%">
									<?php echo dateChecker($values['minDates'],"<br>")?>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<div id="allMonthsDiv" class="sectorDiv">
			<div class="resizer">
				<div class="inner-resizer">
					<div id="allMonthGraph" style="width:98%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="allMonthGraph2" style="width:98%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br><br>
			<table style="width:98%;margin:0 auto" class="table">
				<thead>
					<tr>
						<th>
							<?php echo lang('month','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="width:18%">
							<?php echo lang('average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('maximum night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('minimum night average','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute maximum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th colspan='2'>
							<?php echo lang('absolute minimum','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($monthArray as $y=>$values){
							foreach($values as $m=>$values2){
					?>
								<tr>
									<td style="text-align:center">
										<?php echo $y."/".$m?>
									</td>
									<td style="font-weight:bold;width:9%">
										<?php echo number_format($values2['avg'],($dp+1),".","")?>
									</td>
									<td style="text-align:right;font-weight:bold;width:9%">
										<?php echo number_format($values2['maxAvg'],($dp+1),".","")?>
									</td>
									<td style="text-align:left;width:9%">
										<?php echo dateChecker($values2['maxAvgDates'],"<br>")?>
									</td>	
									<td style="text-align:right;font-weight:bold;width:9%">
										<?php echo number_format($values2['minAvg'],($dp+1),".","")?>
									</td>
									<td style="text-align:left;width:9%">
										<?php echo dateChecker($values2['minAvgDates'],"<br>")?>
									</td>
									<td style="text-align:right;font-weight:bold;width:9%">
										<?php echo number_format($values2['max'],($dp),".","")?>
									</td>
									<td style="text-align:left;width:9%">
										<?php echo dateChecker($values2['maxDates'],"<br>")?>
									</td>	
									<td style="text-align:right;font-weight:bold;width:9%">
										<?php echo number_format($values2['min'],($dp),".","")?>
									</td>
									<td style="text-align:left;width:9%">
										<?php echo dateChecker($values2['minDates'],"<br>")?>
									</td>
								</tr>
					<?php
							}
						}
					?>
				</tbody>
			</table>
			<br>
		</div>
		<div id="daysDiv" class="sectorDiv">
			<div class="resizer">
				<div class="inner-resizer">
					<div id="daysGraph" style="width:98%;margin:0 auto;height:500px" class="resizerGraph"></div>
				</div>
			</div>
			<br><br>
			<table style="width:98%;margin:0 auto" class="table">
				<thead>
					<tr>
						<th>
							<?php echo lang('day','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="width:18%">
							<?php echo lang('averageAbbr','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('maximumAbbr','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('minimumAbbr','c')?><br><?php echo ($UoM)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($sortedStats as $y=>$values){
							foreach($values as $m=>$values2){
								foreach($values2 as $d=>$values3){
					?>
									<tr>
										<td style="text-align:left;padding-left:10px">
											<?php echo $y."/".$m."/".$d?>
										</td>
										<td style="text-align:center;font-weight:bold;width:25%">
											<?php echo number_format($values3['avg'],($dp+1),".","")?>
										</td>
										<td style="text-align:center;font-weight:bold;width:25%">
											<?php echo number_format($values3['max'],($dp),".","")?>
										</td>
										<td style="text-align:center;font-weight:bold;width:25%">
											<?php echo number_format($values3['min'],($dp),".","")?>
										</td>	
									</tr>
					<?php
								}
							}
						}
					?>
				</tbody>
			</table>
		</div>
	</body>
	<script>
		$(".table").tablesorter();
		$('.resizer').resizable({
			resize: function() {
				selectedDiv = $(this).find(".resizerGraph");
				chart = selectedDiv.highcharts();
				chart.setSize(
					this.offsetWidth - 50, 
					this.offsetHeight - 50,
					false
				);
			},
		});
		$("#selectorAlltime").click(function(){
			$(".sectorDiv").hide();
			$("#alltimeDiv").show();
		})
		$("#selectorHours").click(function(){
			$(".sectorDiv").hide();
			$("#hoursDiv").show();
			$('#hoursGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: '',
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				xAxis: {
					categories: [
						<?php 
							foreach($availableHours as $h=>$value){
								echo $value.",";
							}
						?>
					]
				},
				yAxis: {
					title: {
						text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
					},
				},
				tooltip: {
					valueSuffix: '<?php echo ($UoM)?>',
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('average','c')?>',
						color: 'white',
						data: [
							<?php 
								foreach($availableHours as $h=>$value){
									echo number_format($hourData['avg'][$value],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute maximum','c')?>',
						color: '<?php echo $colors['max']?>',
						data: [
							<?php 
								foreach($availableHours as $h=>$value){
									echo number_format($hourData['max'][$value],($dp),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute minimum','c')?>',
						color: '<?php echo $colors['min']?>',
						data: [
							<?php 
								foreach($availableHours as $h=>$value){
									echo number_format($hourData['min'][$value],($dp),'.','').",";
								}
							?>
						]
					}
				]
			});
		})
		$("#selectorMonths").click(function(){
			$(".sectorDiv").hide();
			$("#monthsDiv").show();
			$('#monthsGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: '',
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				xAxis: {
					categories: [
						<?php 
							foreach($allMonths as $m=>$values){
								echo "'".lang("month".$m,'c')."',";
							}
						?>
					]
				},
				yAxis: {
					title: {
						text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
					},
				},
				tooltip: {
					valueSuffix: '<?php echo ($UoM)?>',
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('average','c')?>',
						color: 'white',
						data: [
							<?php 
								foreach($allMonths as $m=>$values){
									echo number_format($values['avg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('maximum night average','c')?>',
						color: '<?php echo $colors['max']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($allMonths as $m=>$values){
									echo number_format($values['maxAvg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('minimum night average','c')?>',
						color: '<?php echo $colors['min']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($allMonths as $m=>$values){
									echo number_format($values['minAvg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute maximum','c')?>',
						color: '<?php echo $colors['max']?>',
						data: [
							<?php 
								foreach($allMonths as $m=>$values){
									echo number_format($values['max'],($dp),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute minimum','c')?>',
						color: '<?php echo $colors['min']?>',
						data: [
							<?php 
								foreach($allMonths as $m=>$values){
									echo number_format($values['min'],($dp),'.','').",";
								}
							?>
						]
					}
				]
			});
		})
		$("#selectorYears").click(function(){
			$(".sectorDiv").hide();
			$("#yearsDiv").show();
			$('#yearGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: '',
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				xAxis: {
					categories: [
						<?php 
							foreach($yearArray as $y=>$values){
								echo "'".$y."',";
							}
						?>
					]
				},
				yAxis: {
					title: {
						text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
					},
				},
				tooltip: {
					valueSuffix: '<?php echo ($UoM)?>',
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('average','c')?>',
						color: 'white',
						data: [
							<?php 
								foreach($yearArray as $y=>$values){
									echo number_format($values['avg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('maximum night average','c')?>',
						color: '<?php echo $colors['max']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($yearArray as $y=>$values){
									echo number_format($values['maxAvg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('minimum night average','c')?>',
						color: '<?php echo $colors['min']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($yearArray as $y=>$values){
									echo number_format($values['minAvg'],($dp+1),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute maximum','c')?>',
						color: '<?php echo $colors['max']?>',
						data: [
							<?php 
								foreach($yearArray as $y=>$values){
									echo number_format($values['max'],($dp),'.','').",";
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute minimum','c')?>',
						color: '<?php echo $colors['min']?>',
						data: [
							<?php 
								foreach($yearArray as $y=>$values){
									echo number_format($values['min'],($dp),'.','').",";
								}
							?>
						]
					}
				]
			});
		});	
		$("#selectorAllMonths").click(function(){
			$(".sectorDiv").hide();
			$("#allMonthsDiv").show();
			$('#allMonthGraph').highcharts({
				chart: {
					type: 'spline'
				},
				title: {
					text: '',
				},
				xAxis: {
					categories: [
						<?php 
							foreach($monthArray as $y=>$values){
								foreach($values as $m=>$values2){
									echo "'".$y."/".$m."',";
								}
							}
						?>
					]
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				plotOptions:{
					spline: {
						marker: {
							enabled: false
						}
					}
				},
				yAxis: {
					title: {
						text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
					},
				},
				tooltip: {
					valueSuffix: '<?php echo ($UoM)?>',
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('average','c')?>',
						color: 'white',
						data: [
							<?php 
								foreach($monthArray as $y=>$values){
									foreach($values as $m=>$values2){
										echo number_format($values2['avg'],($dp+1),'.','').",";
									}
								}
							?>
						]
					},
					{
						name: '<?php echo lang('maximum night average','c')?>',
						color: '<?php echo $colors['max']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($monthArray as $y=>$values){
									foreach($values as $m=>$values2){
										echo number_format($values2['maxAvg'],($dp+1),'.','').",";
									}
								}
							?>
						]
					},
					{
						name: '<?php echo lang('minimum night average','c')?>',
						color: '<?php echo $colors['min']?>',
						dashStyle: 'Dash',
						data: [
							<?php 
								foreach($monthArray as $y=>$values){
									foreach($values as $m=>$values2){
										echo number_format($values2['minAvg'],($dp+1),'.','').",";
									}
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute maximum','c')?>',
						color: '<?php echo $colors['max']?>',
						data: [
							<?php 
								foreach($monthArray as $y=>$values){
									foreach($values as $m=>$values2){
										echo number_format($values2['max'],($dp),'.','').",";
									}
								}
							?>
						]
					},
					{
						name: '<?php echo lang('absolute minimum','c')?>',
						color: '<?php echo $colors['min']?>',
						data: [
							<?php 
								foreach($monthArray as $y=>$values){
									foreach($values as $m=>$values2){
										echo number_format($values2['min'],($dp),'.','').",";
									}
								}
							?>
						]
					}
				]
			});
			$('#allMonthGraph2').highcharts({
				chart: {
					type: 'column'
				},
				title: {
					text: '',
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				xAxis: {
					categories: [
						<?php 
							for($i=1;$i<13;$i++){
								echo "'".lang("month".$i,"c")."',";
							}
						?>
					]
				},
				plotOptions:{
					spline: {
						marker: {
							enabled: false
						}
					}
				},
				yAxis: {
					title: {
						text: '<?php echo lang('average','c')." ".strtolower($heading)?> (<?php echo ($UoM)?>)'
					},
				},
				tooltip: {
					valueSuffix: '<?php echo ($UoM)?>',
					shared: true,
				},
				series: [
					<?php 
						foreach($monthArray as $y=>$values){
					?>
						{
							name: '<?php echo $y?>',
							data: [
								<?php
									for($i=1;$i<13;$i++){
										if(isset($monthArray[$y][$i])){
											echo number_format($monthArray[$y][$i]['avg'],($dp+1),'.','').",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
					<?php
						}
					?>
				]
			});
		})
		$("#selectorDays").click(function(){
			$(".sectorDiv").hide();
			$("#daysDiv").show();
			$('#daysGraph').highcharts('StockChart', {
				chart: {
					zoomType: 'x',
					type: 'spline'
				},
				credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
				rangeSelector: {
					buttons: [
					{
						type: 'day',
						count: 1,
						text: '1d'
					},
					{
						type: 'week',
						count: 1,
						text: '1w'
					}, {
						type: 'month',
						count: 1,
						text: '1m'
					},
					{
						type: 'month',
						count: 3,
						text: '3m'
					}, {
						type: 'month',
						count: 6,
						text: '6m'
					}, {
						type: 'year',
						count: 1,
						text: '1y'
					}, {
						type: 'all',
						text: 'All'
					}],
					selected: 3
				},
				xAxis: {
					type: 'datetime',
					
				},
				yAxis:{
						title: {
							text: '<?php echo $heading?> (<?php echo $UoM?>)'
						},
						opposite: false
				},
				title: {
					text: "<?php echo $heading?>"
				},
				
				series: [
					{
						name: '<?php echo $heading?>',
						color: '#fff',
						data: [
						<?php
							foreach($sortedStats as $y=>$yearData){
								foreach($yearData as $m=>$monthData){
									foreach($monthData as $d=>$dayData){
										$formatDate = date("Y",strtotime($dayData['date'])).",".(date("m",strtotime($dayData['date'])) - 1).",".date("d",strtotime($dayData['date']));
										echo "[Date.UTC(".$formatDate."),".number_format($dayData['avg'],($dp+1),".","")."],";
									}
								}
							}
						?>]
					},
					{
						name: '<?php echo lang('maximum','c')?> <?php echo strtolower($heading)?>',
						color: '<?php echo $colors['max']?>',
						data: [
						<?php
							foreach($sortedStats as $y=>$yearData){
								foreach($yearData as $m=>$monthData){
									foreach($monthData as $d=>$dayData){
										$formatDate = date("Y",strtotime($dayData['date'])).",".(date("m",strtotime($dayData['date'])) - 1).",".date("d",strtotime($dayData['date']));
										echo "[Date.UTC(".$formatDate."),".number_format($dayData['max'],($dp),".","")."],";
									}
								}
							}
						?>]
					},
					{
						name: '<?php echo lang('minimum','c')?> <?php echo strtolower($heading)?>',
						color: '<?php echo $colors['min']?>',
						data: [
						<?php
							foreach($sortedStats as $y=>$yearData){
								foreach($yearData as $m=>$monthData){
									foreach($monthData as $d=>$dayData){
										$formatDate = date("Y",strtotime($dayData['date'])).",".(date("m",strtotime($dayData['date'])) - 1).",".date("d",strtotime($dayData['date']));
										echo "[Date.UTC(".$formatDate."),".number_format($dayData['min'],($dp),".","")."],";
									}
								}
							}
						?>]
					}
				],
				tooltip: {
					dateTimeLabelFormats: {
						millisecond:"%A, %b %e",
						second:"%A, %b %e",
						minute:"%A, %b %e",
						hour:"%A, %b %e",
						day:"%A, %b %e, %Y",
						week:"Week from %A, %b %e, %Y",
						month:"%B %Y",
						year:"%Y"
					}
				},
			});
		});
	</script>
</html>