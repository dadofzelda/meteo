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
	#	Monthly average tables generation
	#
	# 	A script that generates data for monthly average temperature tables
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
	
	if($dataTempUnits=="C"){
		$freezingThreshold = 0;
	}
	if($dataTempUnits=="F"){
		$freezingThreshold = 32;
	}

	// summer days - Number of summer days: Annual count of days when TX (daily maximum temperature) > 25 C.
	$summerDays = array();
	if($dataTempUnits=="C"){
		$summerThreshold=25;
	}
	else{
		$summerThreshold=77;
	}
	// tropical days - Days with maximum temperature >= 30 C
	$tropicalDays = array();
	if($dataTempUnits=="C"){
		$tropicalDayThreshold=30;
	}
	else{
		$tropicalDayThreshold=86;
	}

	// tropical nights - Annual count of days when TN (daily minimum temperature) > 20 C.
	$tropicalNights = array();
	if($dataTempUnits=="C"){
		$tropicalNightThreshold=20;
	}
	else{
		$tropicalNightThreshold=68;
	}
	
	// check cache
	if(file_exists("climateCache.php")){ // does cached file exist?
		if (time()-filemtime("climateCache.php") > 60 * 60) { // if it exists, if it is older than user defined time, delete it
			unlink("climateCache.php");
		}
	}
	// check if cached file still exists, if so, retrieve data from it, otherwise get new data and write to cache
	if(file_exists("climateCache.php")){
		
		$data = unserialize(file_get_contents("climateCache.php"));
		
		$frostDays = $data['frostDays'];
		$icingDays = $data['icingDays'];
		$summerDays = $data['summerDays'];
		$tropicalDays = $data['tropicalDays'];
		$tropicalNights = $data['tropicalNights'];
		$monthNormals = $data['monthNormals'];
		$firstFrosts = $data['firstFrosts'];
		$lastFrosts = $data['lastFrosts'];
		$months = $data['months'];
		$wetDays = $data['wetDays'];
		$dayNumbers = $data['dayNumbers'];
		$years = $data['years'];
	}
	
	else { // if no cache or cache was too old, receive new data
	
		//get all years in db and number of days for each
		$years = array();
		$result = mysqli_query($con, "
			SELECT DISTINCT YEAR(DateTime)
			FROM alldata
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$years [] = $row['YEAR(DateTime)'];
		}
		
		//get number of months available for each
		$result = mysqli_query($con, "
			SELECT YEAR(DateTime), MONTH(DateTime)
			FROM alldata
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$months[$row['MONTH(DateTime)']] = $months[$row['MONTH(DateTime)']] + 1;
		}
		
		$dayNumbers = array();
		$result = mysqli_query($con, "
			SELECT YEAR(DateTime) 
			FROM alldata
			GROUP BY YEAR( DateTime ) , MONTH( DateTime ) , DAY( DateTime ) 
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$dayNumbers[$row['YEAR(DateTime)']] = $dayNumbers[$row['YEAR(DateTime)']] + 1;
		}
		
		// get frost days - Annual count of days when TN (daily minimum temperature) < 0 C.
		$frostDays = array();
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
			FROM (
				SELECT DATETIME, MIN(Tmin) AS DailyMinT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyMinT<=$freezingThreshold
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$frostDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $frostDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
		}
		
		
		
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME), DailyMaxT
			FROM (
				SELECT DATETIME, MAX(Tmax) AS DailyMaxT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyMaxT>=$summerThreshold
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$summerDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $summerDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
			if($row['DailyMaxT']>=$tropicalDayThreshold){
				$tropicalDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $tropicalDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
			}
		}
		
		// get icing days - Annual count of days when TX (daily maximum temperature) < 0 C.
		$icingDays = array();
		
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
			FROM (
				SELECT DATETIME, MAX(Tmax) AS DailyMaxT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyMaxT<$freezingThreshold
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$icingDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $icingDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
		}
		
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
			FROM (
				SELECT DATETIME, MIN(Tmin) AS DailyMinT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyMinT>=$tropicalNightThreshold
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$tropicalNights[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $tropicalNights[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
		}
		
		// wet days - Days with precipitation > 0 mm.
		$wetDays = array();
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyR
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyR>0
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$wetDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $wetDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
		}
		
		// first frost
		$firstFrosts = array();
		$lastFrosts = array();
		if($stationLat>0){
			$result = mysqli_query($con, "
				SELECT DateTime, YEAR(DateTime)
				FROM alldata
				WHERE MONTH(DateTime)>6 AND Tmin<=$freezingThreshold
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while ($row = mysqli_fetch_array($result)) {
				if($firstFrosts[$row['YEAR(DateTime)']]==""){
					$firstFrosts[$row['YEAR(DateTime)']] = $row['DateTime'];
				}
			}
			
			// last frost
			$result = mysqli_query($con, "
				SELECT DateTime, YEAR(DateTime)
				FROM alldata
				WHERE MONTH(DateTime)<6 AND Tmin<=$freezingThreshold
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while ($row = mysqli_fetch_array($result)) {
				$lastFrosts[$row['YEAR(DateTime)']] = $row['DateTime'];
			}
		}
		else{
			$result = mysqli_query($con, "
				SELECT DateTime, YEAR(DateTime)
				FROM alldata
				WHERE MONTH(DateTime)>1 AND Tmin<=$freezingThreshold
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while ($row = mysqli_fetch_array($result)) {
				if($firstFrosts[$row['YEAR(DateTime)']]==""){
					$firstFrosts[$row['YEAR(DateTime)']] = $row['DateTime'];
				}
				$lastFrosts[$row['YEAR(DateTime)']] = $row['DateTime'];
			}
		}
		
		// monthly normals
		$monthNormals = array();
		$result = mysqli_query($con, "
			SELECT Month(DateTime), max(Tmax), min(Tmin), avg(T)
			FROM alldata
			GROUP BY MONTH(DateTime)
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$monthNormals[$row['Month(DateTime)']]['avgT'] = number_format(convertT($row['avg(T)']),1);
			$monthNormals[$row['Month(DateTime)']]['minT'] = number_format(convertT($row['min(Tmin)']),1);
			$monthNormals[$row['Month(DateTime)']]['maxT'] = number_format(convertT($row['max(Tmax)']),1);
		}
		
		$result = mysqli_query($con,
			"
			SELECT YEAR(DATETIME), MONTH(DATETIME), AVG(DailyRain), MAX(DailyRain), SUM(DailyRain), AVG(DailyMaxT), COUNT(DailyRain), AVG(DailyMinT)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain, MAX(Tmax) AS DailyMaxT, MIN(Tmin) AS DailyMinT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			GROUP BY MONTH(DATETIME)
			ORDER BY DATETIME
			"
		);
		while($row = mysqli_fetch_array($result)){
			$month = $row['MONTH(DATETIME)'];
			$monthDayNumber = date("t",strtotime("2015-".$month."-1"));
			$monthNormals[$row['MONTH(DATETIME)']]['rainAvg'] = convertR(round($row['SUM(DailyRain)']/$row['COUNT(DailyRain)']*$monthDayNumber,2));
			$monthNormals[$row['MONTH(DATETIME)']]['rainDailyMax'] = convertR($row['MAX(DailyRain)']);
			$monthNormals[$row['MONTH(DATETIME)']]['avgDailyMax'] = convertT(round($row['AVG(DailyMaxT)'],1));
			$monthNormals[$row['MONTH(DATETIME)']]['avgDailyMin'] = convertT(round($row['AVG(DailyMinT)'],1));
		}
		
		//prepare complete dataset
		$dataStr ['frostDays'] = $frostDays;
		$dataStr['icingDays'] = $icingDays;
		$dataStr['summerDays'] = $summerDays;
		$dataStr['tropicalDays'] = $tropicalDays;
		$dataStr['tropicalNights'] = $tropicalNights;
		$dataStr['monthNormals'] = $monthNormals;
		$dataStr['firstFrosts'] = $firstFrosts;
		$dataStr['lastFrosts'] = $lastFrosts;
		$dataStr['months'] = $months;
		$dataStr['years'] = $years;
		$dataStr['wetDays'] = $wetDays;
		$dataStr['dayNumbers'] = $dayNumbers;
		
		file_put_contents("climateCache.php", serialize($dataStr));
	}
	
	foreach($monthNormals as $month){
		$avgTs[] = $month['avgT'];
		$minTs[] = $month['minT'];
		$maxTs[] = $month['maxT'];
	}
	$minAvgT = min($avgTs);
	$maxAvgT = max($avgTs);
	$minMinT = min($minTs);
	$maxMinT = max($minTs);
	$minMaxT = min($maxTs);
	$maxMaxT = max($maxTs);
	
	// average first frost
	if(count($firstFrosts)>0){
		foreach($firstFrosts as $start){
			$firstFrostDays[] = date("z",strtotime($start));
		}
		$temporary = round(array_sum($firstFrostDays)/count($firstFrostDays));
		$averageFirstFrost = DateTime::createFromFormat('z', $temporary);
		$averageFirstFrostDay = $averageFirstFrost->format('j');
		$averageFirstFrostMonth = $averageFirstFrost->format('n');
		$averageFirstFrost = $averageFirstFrostDay." ".lang('month'.$averageFirstFrostMonth,'c');
	}
	if(count($lastFrosts)>0){
		foreach($lastFrosts as $start){
			$lastFrostDays[] = date("z",strtotime($start));
		}
		$temporary = round(array_sum($lastFrostDays)/count($lastFrostDays));
		$averageLastFrost = DateTime::createFromFormat('z', $temporary);
		$averageLastFrostDay = $averageLastFrost->format('j');
		$averageLastFrostMonth = $averageLastFrost->format('n');
		$averageLastFrost = $averageLastFrostDay." ".lang('month'.$averageLastFrostMonth,'c');
	}
	
	function doConv($number){
		global $displayTempUnits;
		global $dataTempUnits;
		return number_format(convertor($number,$dataTempUnits,$displayTempUnits),0,".","");
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Climate</title>
		<?php metaHeader()?>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<style>
			
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<div style="margin:0 auto;width:98%">
				<h1><?php echo lang('climate','c')?></h1>
				<br>
				<h2><?php echo lang('monthly','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th><?php echo lang('month','c')?></th>
								<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('temperature','c')." - ".lang('average','l')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgT']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgT']."</div></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('average','c')." ".lang('maximumAbbr','l')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgDailyMax']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgDailyMax']."</div></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('average','c')." ".lang('minimumAbbr','l')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgDailyMin']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgDailyMin']."</div></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('frost days','c')?><br><span class="mticon-temp"></span><?php echo lang('minimumAbbr','l')?> < <?php echo doConv($freezingThreshold).unitFormatter($displayTempUnits)?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$frostDays)){
										foreach($frostDays[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphFrostDays[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('ice days','c')?><br><span class="mticon-temp"></span><?php echo lang('maximumAbbr','l')?> < <?php echo doConv($freezingThreshold).unitFormatter($displayTempUnits)?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$icingDays)){
										foreach($icingDays[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphIcingDays[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('summer days','c')?><br><span class="mticon-temp"></span><?php echo lang('maximumAbbr','l')?> > <?php echo doConv($summerThreshold).unitFormatter($displayTempUnits)?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$summerDays)){
										foreach($summerDays[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphSummerDays[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('tropical days','c')?><br><span class="mticon-temp"></span><?php echo lang('maximumAbbr','l')?> > <?php echo doConv($tropicalDayThreshold).unitFormatter($displayTempUnits)?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$tropicalDays)){
										foreach($tropicalDays[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphTropicalDays[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('tropical nights','c')?><br><span class="mticon-temp"></span><?php echo lang('minimumAbbr','l')?> > <?php echo doConv($tropicalNightThreshold).unitFormatter($displayTempUnits)?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$tropicalNights)){
										foreach($tropicalNights[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphTropicalNights[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('absolute maximum','c')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['maxT']."</span><br><div class='tempGaugeMax'>".$monthNormals[$i]['maxT']."</div></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('absolute minimum','c')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['minT']."</span><br><div class='tempGaugeMin'>".$monthNormals[$i]['minT']."</div></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('average first frost','c')?></td>
							<td colspan="12"><?php echo $averageFirstFrost?></td>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('average last frost','c')?></td>
							<td colspan="12"><?php echo $averageLastFrost?></td>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('average','c')." ".lang('precipitation','l')?></td>
							<?php
								for($i=1;$i<13;$i++){
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($monthNormals[$i]['rainAvg'],1)."</span></td>";
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'><?php echo lang('wetdays','c')?></td>
							<?php
								for($i=1;$i<13;$i++){
									$avg = 0;
									if(array_key_exists($i,$wetDays)){
										foreach($wetDays[$i] as $year){
											$avg = $avg + $year;
										}
										$avg = round($avg/$months[$i],1);
									}
									$graphWetDays[$i] = $avg;
									echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($avg,1)."</span></td>";
								}
							?>
						</tr>
					</tbody>
				</table>
				<br>
				<div id="summaryTemperatureGraph" style="height:400px"></div>
				<br>
				<div id="summaryDays" style="height:400px"></div>
				<br>
				<h2><?php echo lang('frost days','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$frostDays)){
										if(array_key_exists($years[$i],$frostDays[$a])){
											$temporaryTotal = $temporaryTotal + $frostDays[$a][$years[$i]];
											echo "<td>".$frostDays[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $frostDays[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('ice days','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$icingDays)){
										if(array_key_exists($years[$i],$icingDays[$a])){
											$temporaryTotal = $temporaryTotal + $icingDays[$a][$years[$i]];
											echo "<td>".$icingDays[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $icingDays[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('summer days','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$summerDays)){
										if(array_key_exists($years[$i],$summerDays[$a])){
											$temporaryTotal = $temporaryTotal + $summerDays[$a][$years[$i]];
											echo "<td>".$summerDays[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $summerDays[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('tropical days','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$tropicalDays)){
										if(array_key_exists($years[$i],$tropicalDays[$a])){
											$temporaryTotal = $temporaryTotal + $tropicalDays[$a][$years[$i]];
											echo "<td>".$tropicalDays[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $tropicalDays[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('tropical nights','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$tropicalNights)){
										if(array_key_exists($years[$i],$tropicalNights[$a])){
											$temporaryTotal = $temporaryTotal + $tropicalNights[$a][$years[$i]];
											echo "<td>".$tropicalNights[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $tropicalNights[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('wetdays','c')?></h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th><?php echo lang('total','c')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($years);$i++){
								$temporaryTotal = 0;
								echo "<tr><td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
								for($a=1;$a<13;$a++){	
									if(array_key_exists($a,$wetDays)){
										if(array_key_exists($years[$i],$wetDays[$a])){
											$temporaryTotal = $temporaryTotal + $wetDays[$a][$years[$i]];
											echo "<td>".$wetDays[$a][$years[$i]]."</td>";
										}
										else{
											echo "<td>0</td>";
										}
									}
									else{
										echo "<td>0</td>";
									}
								}
								echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$temporaryTotal."</td>";
								echo "</tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr style="background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold;font-size:0.9em">
							<td><?php echo lang('total','c')?></td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										$temporaryTotal = $temporaryTotal + $wetDays[$i][$years[$a]];
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2><?php echo lang('first and last frost','c')?></h2>
				<table class="table">
					<tr>
						<th></th>
						<th><?php echo lang('first frost','c')?></th>
						<th><?php echo lang('last frost','c')?></th>
					</tr>
						<?php
						for($i=0;$i<count($years);$i++){
							echo "<tr>";
							echo "<td style='background:#".$color_schemes[$design2]['900'].";font-variant:small-caps; font-weight:bold;font-size:0.9em'>".$years[$i]."</td>";
							if(array_key_exists($years[$i],$firstFrosts)){
								$temporary = date($dateTimeFormat,strtotime($firstFrosts[$years[$i]]));
								echo "<td>".$temporary."</td>";
							}
							else{
								echo "<td>-</td>";
							}
							if(array_key_exists($years[$i],$lastFrosts)){
								$temporary = date($dateTimeFormat,strtotime($lastFrosts[$years[$i]]));
								echo "<td>".$temporary."</td>";
							}
							else{
								echo "<td>-</td>";
							}
							echo "</tr>";
						}
					?>
					</table>
					<br><br>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
	<script>
		(function($){
			$.fn.tempGauge = function(options) {
				var opts = $.extend({}, $.fn.tempGauge.defaults, options),
				padding = opts.borderWidth;			
				
				var gauges = [];
				
				this.each(function(idx, item){
					gauges.push(createTempGauge(item));
				});
				
				return $(gauges);
				
				function createTempGauge(gauge){
					
					var canvas = document.createElement("canvas"),
						ctx = canvas.getContext("2d"),
						currentTempText = $(gauge).text(),
						currentTemp = parseFloat(currentTempText) || opts.defaultTemp;

					canvas.width = opts.width;
					canvas.height = opts.width * 2 + opts.labelSize;
					
					$(gauge).replaceWith(canvas);
					
					var percentage = calculatePercentage(currentTemp, opts.minTemp, opts.maxTemp - opts.minTemp);

					ctx.lineWidth = opts.borderWidth;
					ctx.strokeStyle = opts.borderColor;
					//ctx.fillStyle = opts.fillColor;
					if(currentTemp<=<?php echo $freezingThreshold?>){
						ctx.fillStyle = "blue";
					}
					else{
						ctx.fillStyle = "red";
					}
					ctx.font = "bold " + opts.labelSize + "px Arial ";
					ctx.textAlign = "center";
					
					fillTempGauge(ctx, 0, padding/2, opts.width,  opts.width * 2 - padding, percentage);
					strokeTempGauge(ctx, 0, padding/2,  opts.width,  opts.width * 2 -padding);
					if(opts.showLabel){
						drawLabel(ctx, canvas.width/2, canvas.height - opts.labelSize/5 , currentTempText);
					}
				}	
				
				function calculatePercentage(temp, mintemp, length){
					var percentage = (temp - mintemp)/ length;
					percentage = percentage > 1 ? 1 : percentage;
					percentage = percentage < 0 ? 0 : percentage;
					return percentage;
				}
				
				function drawTemperatureGauge(ctx, x, y, width, height, spacing, fillPercent){
					
					var wholeCircle = Math.PI * 2;
					var smallRadius = width / 3 / 2 - spacing;
					var xSmall = x + width / 2 ;
					var ySmall = y + smallRadius + spacing;
					
					var bigRadius = height / 6 - spacing;
					var xBig = x + width / 2 ;
					var yBig = y + height / 6 * 5 ;
					
					var offSet = Math.sqrt((Math.pow(bigRadius,2) - Math.pow(smallRadius/2,2)),2);
					var twoThirdsLength = height / 6 * 5 - offSet - width / 3 / 2;
					
					var gauge = twoThirdsLength * fillPercent;
					
					var yBox = yBig - offSet - gauge;
					var xBox = xBig - width / 6 + spacing;
					var sRad = Math.asin(smallRadius/bigRadius);
					
					ctx.beginPath();
					ctx.arc(xSmall, yBox, smallRadius, 0, wholeCircle * -0.5,  true);
					ctx.arc(xBig, yBig, bigRadius, wholeCircle * 0.75 - sRad, wholeCircle * -0.25 + sRad, true);
					ctx.closePath();
				}
				
				function strokeTempGauge(ctx, x, y, width, height){
					drawTemperatureGauge(ctx, x, y, width, height, 0, 1);
					ctx.stroke();
				}
				
				function fillTempGauge(ctx, x, y, width, height, percent){
					drawTemperatureGauge(ctx, x, y, width, height,  opts.borderWidth, percent);
					ctx.fill();
				}
				
				function drawLabel(ctx, x, y, text){
					ctx.fillStyle = opts.labelColor;
					ctx.fillText(text, x , y );
				}
			};
			
			$.fn.tempGauge.defaults = {
				borderColor: "black",
				borderWidth: 4,
				defaultTemp: 26,
				fillColor: "red",
				labelSize: 12,
				labelColor: "black",
				maxTemp: 40,
				minTemp: -10,
				showLabel:false,
				width: 100
			};
				
		})(jQuery);
		$(".tempGauge").tempGauge({
			borderColor: "white",
			borderWidth: 1,
			fillColor: "red",
			labelSize: 12,
			labelColor: "white",
			maxTemp: <?php echo $maxAvgT?>,
			minTemp: <?php echo $minAvgT?>,
			showLabel:false,
			width: 20
		});
		$(".tempGaugeMax").tempGauge({
			borderColor: "white",
			borderWidth: 1,
			fillColor: "red",
			labelSize: 12,
			labelColor: "white",
			maxTemp: <?php echo $maxMaxT?>,
			minTemp: <?php echo $minMinT?>,
			showLabel:false,
			width: 20
		});
		$(".tempGaugeMin").tempGauge({
			borderColor: "white",
			borderWidth: 1,
			fillColor: "red",
			labelSize: 12,
			labelColor: "white",
			maxTemp: <?php echo $maxMaxT?>,
			minTemp: <?php echo $minMinT?>,
			showLabel:false,
			width: 20
		});
	</script>
	<?php include("../../css/highcharts.php")?>
	<script>
		$(function () {
			$('#summaryTemperatureGraph').highcharts({
				chart: {
					zoomType: 'xy',
				},
				title: {
					text:  '<?php echo lang('average','c')?>'
				},
				xAxis: {
					categories: [<?php for($i=1;$i<13;$i++){echo "'".lang('month'.$i.'short','c')."',";}?>],
					title: {
						text: null
					},
				},
				yAxis: [
					{
						title: {
							text: '<?php echo lang('temperature','c')?>'
						},
						labels: {
							format: '{value} °<?php echo $displayTempUnits?>'
						}
					},
					{
						title: {
							text: '<?php echo lang('precipitation','c')?>'
						},
						labels: {
							format: '{value} <?php echo $displayRainUnits?>'
						},
						opposite: true,
						min: 0
					}
				],
				tooltip: {
					shared: true,
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
							duration: 5000
						},
						marker: {
							enabled: false
						},
					},					
				},	
				series: [
					{
						type: 'spline',
						name: '<?php echo lang("temperature","c")?>',
						color: '#FFF',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($monthNormals[$i]['avgT'],1).",";
							}
						?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('maximum','c')." ".lang("temperature","l")?>',
						color: '#<?php echo $color_schemes[$design2]['300']?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($monthNormals[$i]['avgDailyMax'],1).",";
							}
						?>
						]
					},
					{
						type: 'spline',
						name: '<?php echo lang('minimum','c')." ".lang("temperature","l")?>',
						color: '#<?php echo $color_schemes[$design2]['200']?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($monthNormals[$i]['avgDailyMin'],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('precipitation','c')?>',
						color: '#<?php echo $color_schemes[$design]['500']?>',
						yAxis: 1,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($monthNormals[$i]['rainAvg'],1).",";
							}
						?>
						]
					},
				]
			});	
			$('#summaryDays').highcharts({
				chart: {
					zoomType: 'xy',
				},
				title: {
					text:  '<?php echo lang('average','c')?>'
				},
				xAxis: {
					categories: [<?php for($i=1;$i<13;$i++){echo "'".lang('month'.$i.'short','c')."',";}?>],
					title: {
						text: null
					},
				},
				yAxis: [
					{
						title: {
							text: '<?php echo lang('days','c')?>'
						},
					},
				],
				tooltip: {
					shared: true,
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
							duration: 5000
						},
						marker: {
							enabled: false
						},
					},					
				},	
				series: [
					{
						type: 'column',
						name: '<?php echo lang('frost days','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphFrostDays[$i],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('ice days','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphIcingDays[$i],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('summer days','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphSummerDays[$i],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('tropical days','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphTropicalDays[$i],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('tropical nights','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphTropicalNights[$i],1).",";
							}
						?>
						]
					},
					{
						type: 'column',
						name: '<?php echo lang('wetdays','c')?>',
						zIndex:5,
						data: [
						<?php
							for($i=1;$i<13;$i++){
								echo round($graphWetDays[$i],1).",";
							}
						?>
						]
					},
				]
			});	
		});
	</script>
</html>
	