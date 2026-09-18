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
	#	World cities current weather conditions
	#
	# 	A script showing current weather conditions in user specified cities.
	#
	############################################################################
	#	Version and change log
	#
	# 	v1.0 	2015-07-15	Initial release
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	
	if($dataTempUnits=="C"){
		$freezingThreshold = 0;
	}
	if($dataTempUnits=="F"){
		$freezingThreshold = 32;
	}
	
	$firstFrostDays = array();
	$lastFrostDays = array();
	
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
			if(isset($months[$row['MONTH(DateTime)']])){
				$months[$row['MONTH(DateTime)']] = $months[$row['MONTH(DateTime)']] + 1;
			}
			else{
				$months[$row['MONTH(DateTime)']] = 1;
			}
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
		
		// get summer days - Number of summer days: Annual count of days when TX (daily maximum temperature) > 25 C.
		$summerDays = array();
		if($dataTempUnits=="C"){
			$summerThreshold=25;
		}
		else{
			$summerThreshold=77;
		}
		
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
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
		
		// tropical nights - Annual count of days when TN (daily minimum temperature) > 20 C.
		$tropicalNights = array();
		if($dataTempUnits=="C"){
			$tropicalNightThreshold=20;
		}
		else{
			$tropicalNightThreshold=68;
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
		
		// tropical days - Days with maximum temperature >= 30 C
		$tropicalDays = array();
		if($dataTempUnits=="C"){
			$tropicalDayThreshold=30;
		}
		else{
			$tropicalDayThreshold=86;
		}
		$result = mysqli_query($con, "
			SELECT YEAR(DATETIME), MONTH(DATETIME)
			FROM (
				SELECT DATETIME, MAX(Tmax) AS DailyMaxT
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyTable
			WHERE DailyMaxT>=$tropicalDayThreshold
			ORDER BY DATETIME
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$tropicalDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] = $tropicalDays[$row['MONTH(DATETIME)']][$row['YEAR(DATETIME)']] + 1;
		}
		
		// first frost
		$firstFrosts = array();
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
		$lastFrosts = array();
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
	if(isset($avgTs)){
		$minAvgT = min($avgTs);
		$maxAvgT = max($avgTs);
		$minMinT = min($minTs);
		$maxMinT = max($minTs);
		$minMaxT = min($maxTs);
		$maxMaxT = max($maxTs);
	}
	
	
	// average first frost
	foreach($firstFrosts as $start){
		$firstFrostDays[] = date("z",strtotime($start));
	}
	if(count($firstFrostDays)>0){
		$temporary = round(array_sum($firstFrostDays)/count($firstFrostDays));
		$averageFirstFrost = DateTime::createFromFormat('z', $temporary);
		$averageFirstFrostDay = $averageFirstFrost->format('j');
		$averageFirstFrostMonth = $averageFirstFrost->format('n');
		$averageFirstFrost = $averageFirstFrostDay." ".lang('month'.$averageFirstFrostMonth,'c');
	}
	
	foreach($lastFrosts as $start){
		$lastFrostDays[] = date("z",strtotime($start));
	}
	if(count($lastFrostDays)>0){
		$temporary = round(array_sum($lastFrostDays)/count($lastFrostDays));
		$averageLastFrost = DateTime::createFromFormat('z', $temporary);
		$averageLastFrostDay = $averageLastFrost->format('j');
		$averageLastFrostMonth = $averageLastFrost->format('n');
		$averageLastFrost = $averageLastFrostDay." ".lang('month'.$averageLastFrostMonth,'c');
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('climate','c')?></title>
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
			<?php include("../../menu.php")?>
		</div>
		<div id="main" style="text-align:center">
			<div style="margin:0 auto;width:98%">
				<h1><?php echo lang('climate','c')?></h1>
				<br>
				<h2>Monthly normals</h2>
				<table class="table">
					<thead>
						<tr>
							<th>Month</th>
								<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average temperature</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['avgT'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgT']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgT']."</div></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average max</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['avgDailyMax'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgDailyMax']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgDailyMax']."</div></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average min</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['avgDailyMin'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['avgDailyMin']."</span><br><div class='tempGauge'>".$monthNormals[$i]['avgDailyMin']."</div></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Frost days</td>
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
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Icing days</td>
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
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Summer days</td>
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
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Tropical days</td>
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
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Tropical nights</td>
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
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Absolute maximum</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['maxT'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['maxT']."</span><br><div class='tempGaugeMax'>".$monthNormals[$i]['maxT']."</div></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Absolute minimum</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['minT'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".$monthNormals[$i]['minT']."</span><br><div class='tempGaugeMin'>".$monthNormals[$i]['minT']."</div></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average first frost</td>
							<td colspan="12"><?php echo $averageFirstFrost?></td>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average last frost</td>
							<td colspan="12"><?php echo $averageLastFrost?></td>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Average precipitation</td>
							<?php
								for($i=1;$i<13;$i++){
									if(isset($monthNormals[$i]['rainAvg'])){
										echo "<td><span style='font-size:0.9em;font-weight:bold'>".number_format($monthNormals[$i]['rainAvg'],1)."</span></td>";
									}
									else{
										echo "<td></td>";
									}
								}
							?>
						</tr>
						<tr>
							<td style='background:#<?php echo $color_schemes[$design2]['900']?>;font-variant:small-caps; font-weight:bold'>Wet days</td>
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
				<h2>Frost days</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($frostDays[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $frostDays[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>Icing days</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($icingDays[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $icingDays[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>Summer days</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($summerDays[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $summerDays[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>Tropical days</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($tropicalDays[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $tropicalDays[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>Tropical nights</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($tropicalNights[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $tropicalNights[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>Wet days</h2>
				<table class="table">
					<thead>
						<tr>
							<th></th>
							<?php for($i=1;$i<13;$i++){echo "<th style='width:7%'>".lang('month'.$i.'short','c')."</th>";}?>
							<th>Total</th>
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
							<td>Total</td>
							<?php 
								for($i=1;$i<13;$i++){
									$temporaryTotal  = 0;
									for($a=0;$a<count($years);$a++){
										if(isset($wetDays[$i][$years[$a]])){
											$temporaryTotal = $temporaryTotal + $wetDays[$i][$years[$a]];
										}
									}
									echo "<td>".$temporaryTotal."</td>";
								}
							?>
							<td></td>
						</tr>
					</tfoot>
				</table>
				<br>
				<h2>First and last frosts</h2>
				<table class="table">
					<tr>
						<th></th>
						<th>First frost</th>
						<th>Last frost</th>
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
			</div>
		</div>
		<?php include("../../footer.php")?>
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
	<script>
		Highcharts.createElement('link', {
			href: 'https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);

		Highcharts.theme = {
			colors: ["#BBBBBB", "#90ee7e", "#f45b5b", "#7798BF", "#aaeeee", "#ff0066", "#eeaaee",
				"#55BF3B", "#DF5353", "#7798BF", "#aaeeee"],
			chart: {
				backgroundColor: "#<?php echo $color_schemes[$design2]['700']?>",
				color: "#<?php echo $color_schemes[$design2]['font900']?>",
				style: {
					fontFamily: "'<?php echo $designFont?> Narrow', sans-serif"
				},
				plotBorderColor: '#606063'
			},
			title: {
				style: {
					color: "#<?php echo $color_schemes[$design2]['font900']?>",
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				labels: {
					style: {
						color: '#<?php echo $color_schemes[$design2]['font900']?>'
					}
				},
				lineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				minorGridLineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				tickColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				title: {
					style: {
						color: '#<?php echo $color_schemes[$design2]['font900']?>'

					}
				}
			},
			yAxis: {
				gridLineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				gridLineWidth: 0,
				labels: {
					style: {
						color: '#<?php echo $color_schemes[$design2]['font900']?>'
					}
				},
				lineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				minorGridLineColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				tickColor: '#<?php echo $color_schemes[$design2]['font900']?>',
				tickWidth: 1,
				title: {
					style: {
						color: '#<?php echo $color_schemes[$design2]['font900']?>'
					}
				}
			},
			tooltip: {
				backgroundColor: '#<?php echo $color_schemes[$design2]['700']?>',
				style: {
					color: '#<?php echo $color_schemes[$design2]['font700']?>'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: '#<?php echo $color_schemes[$design2]['900']?>'
					},
					marker: {
						lineColor: '#333'
					}
				},
				boxplot: {
					fillColor: '#505053'
				},
				candlestick: {
					lineColor: 'white'
				},
				errorbar: {
					color: 'white'
				}
			},
			legend: {
				itemStyle: {
					color: '#<?php echo $color_schemes[$design2]['200']?>'
				},
				itemHoverStyle: {
					color: '#<?php echo $color_schemes[$design2]['300']?>'
				},
				itemHiddenStyle: {
					color: '#<?php echo $color_schemes[$design2]['100']?>'
				}
			},
			credits: {
				style: {
					color: '#666'
				}
			},
			labels: {
				style: {
					color: '#707073'
				}
			},

			drilldown: {
				activeAxisLabelStyle: {
					color: '#F0F0F3'
				},
				activeDataLabelStyle: {
					color: '#F0F0F3'
				}
			},

			navigation: {
				buttonOptions: {
					symbolStroke: '#DDDDDD',
					theme: {
						fill: '#505053'
					}
				}
			},

			// scroll charts
			rangeSelector: {
				buttonTheme: {
					fill: '#505053',
					stroke: '#000000',
					style: {
						color: '#CCC'
					},
					states: {
						hover: {
							fill: '#707073',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						},
						select: {
							fill: '#000003',
							stroke: '#000000',
							style: {
								color: 'white'
							}
						}
					}
				},
				inputBoxBorderColor: '#505053',
				inputStyle: {
					backgroundColor: '#333',
					color: 'silver'
				},
				labelStyle: {
					color: 'silver'
				}
			},

			navigator: {
				handles: {
					backgroundColor: '#666',
					borderColor: '#AAA'
				},
				outlineColor: '#CCC',
				maskFill: 'rgba(255,255,255,0.1)',
				series: {
					color: '#7798BF',
					lineColor: '#A6C7ED'
				},
				xAxis: {
					gridLineColor: '#505053'
				}
			},

			scrollbar: {
				barBackgroundColor: '#808083',
				barBorderColor: '#808083',
				buttonArrowColor: '#CCC',
				buttonBackgroundColor: '#606063',
				buttonBorderColor: '#606063',
				rifleColor: '#FFF',
				trackBackgroundColor: '#404043',
				trackBorderColor: '#404043'
			},

			// special colors for some of the
			legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
			background2: '#505053',
			dataLabelsColor: '#B0B0B3',
			textColor: '#C0C0C0',
			contrastTextColor: '#F0F0F3',
			maskColor: 'rgba(255,255,255,0.3)'
		};

		// Apply the theme
		Highcharts.setOptions(Highcharts.theme);
	</script>
	<script>
		$(function () {
			$('#summaryTemperatureGraph').highcharts({
				chart: {
					zoomType: 'xy',
				},
				title: {
					text:  'Average'
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
							text: 'Temperature'
						},
						labels: {
							format: '{value} °<?php echo $displayTempUnits?>'
						}
					},
					{
						title: {
							text: 'Precipitation'
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
					text:  'Average'
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
							text: 'Days'
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
						name: 'Frost days',
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
						name: 'Icing days',
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
						name: 'Summer days',
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
						name: 'Tropical days',
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
						name: 'Tropical nights',
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
						name: 'Wet days',
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
	