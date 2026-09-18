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
	#	Monthly report
	#
	# 	A script which generates the monthly report for user specified month.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	
	//error_reporting(E_ALL);
	
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");
	
	if(isset($_GET['var'])){
		$var = $_GET['var'];
	}
	else{
		die("No parameter specified.");
	}
	
	if($_GET['m']<1 || $_GET['m']>12){
		echo "Invalid date";
		die();
	}
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y']) || !is_numeric($_GET['m'])){
		echo "Invalid date";
		die();
	}
	
	$dateY = $_GET['y'];
	$dateM = $_GET['m'];
	
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
	
	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCols = array("T","Tmax","Tmin");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCols = array("A","A","A");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="D"){
		$heading = lang("dew point",'c');
		$mySQLCols = array("D","D","D");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCols = array("H","H","H");
		$colors['min'] = "#d9a300";
		$colors['max'] = "#00b300";
		$deviationsDecimals = 1;
		$dp = 1;
		$UoM = "%";
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCols = array("P","P","P");
		$colors['min'] = "#ffa64c";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = $decimalsP + 2;
		$dp = $decimalsP;
		$UoM = unitFormatter($displayPressUnits);
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCols = array("W","W","W");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCols = array("G","G","G");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCols = array("S","S","S");
		$colors['min'] = "#222222";
		$colors['max'] = "#ffd24c";
		$deviationsDecimals = 0;
		$dp = 0;
		$UoM = "W/m2";
	}
	if($var=="R"){
		$heading = lang("precipitation",'c');
		$colors['min'] = "#999999";
		$colors['max'] = "#006cd9";
		if($displayRainUnits=="in"){
			$dp = 2;
		}
		else{
			$dp = 1;
		}
		$UoM = unitFormatter($displayRainUnits);
	}
	
	$numberDays = date("t",strtotime($dateY."-".$dateM."-15"));
	
	// get monthly data	
	if($var!="R"){ // rain must be treated differently - data in db is cumulative
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), stddev(".$mySQLCols[0]."), DAY(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row['DAY(DateTime)'];
			$dailyAvg[$d] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$dailyMax[$d] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$dailyMaxRaw[$d] = ($row['max('.$mySQLCols[1].')']);
			$dailyMin[$d] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$dailyMinRaw[$d] = ($row['min('.$mySQLCols[2].')']);
			$dailyRange[$d] = (chooseConvertor($row['max('.$mySQLCols[1].')'])-chooseConvertor($row['min('.$mySQLCols[2].')']));
			if($var=="T" || $var=="A" || $var=="D"){
				$dailyStddev[$d] = convertTStddev($row['stddev('.$mySQLCols[0].')']);
			}
			else{
				$dailyStddev[$d] = chooseConvertor($row['stddev('.$mySQLCols[0].')']);
			}
		}
		
		$monthAvg = avg($dailyAvg);
		$monthMax = max($dailyMax);
		$monthMin = min($dailyMin);
		$result = mysqli_query($con,"
			SELECT max(".$mySQLCols[1]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY." AND ".$mySQLCols[1]."=".max($dailyMaxRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthMaxDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		$result = mysqli_query($con,"
			SELECT min(".$mySQLCols[2]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY." AND ".$mySQLCols[2]."=".min($dailyMinRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthMinDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		
		// long-term
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), DAY(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM."
			GROUP BY DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row["DAY(DateTime)"];
			$overallAvg[$d] = chooseConvertor($row["avg(".$mySQLCols[0].")"]);
			$overallMax[$d] = chooseConvertor($row["max(".$mySQLCols[1].")"]);
			$overallMaxRaw[] = ($row["max(".$mySQLCols[1].")"]);
			$overallMin[$d] = chooseConvertor($row["min(".$mySQLCols[2].")"]);
			$overallMinRaw[$d] = ($row["min(".$mySQLCols[2].")"]);
		}
		// calculate deviations
		for($i=1;$i<32;$i++){
			if(array_key_exists($i,$dailyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($dailyAvg[$i],$overallAvg[$i],$deviationsDecimals);
			}
		}
		
		$absoluteAvg = avg($overallAvg);
		$absoluteMax = max($overallMax);
		$absoluteMin = min($overallMin);
		$result = mysqli_query($con,"
			SELECT max(".$mySQLCols[1]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND ".$mySQLCols[1]."=".max($overallMaxRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$absoluteMaxDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		$result = mysqli_query($con,"
			SELECT min(".$mySQLCols[2]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND ".$mySQLCols[2]."=".min($overallMinRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$absoluteMinDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
	}
	if($var=="R"){
		$result = mysqli_query($con,"
			SELECT max(R), DAY(DateTime),DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row['DAY(DateTime)'];
			$dateTemporary = date($dateFormat,strtotime($row['DateTime']));
			$dailyAvg[$d] = convertR($row['max(R)']);
			$dailyAvgRaw[$dateTemporary] = convertR($row['max(R)']);
		}
		// long-term
		$result = mysqli_query($con,"
			SELECT max(R), DAY(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM."
			GROUP BY DAY(DateTime), YEAR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row["DAY(DateTime)"];
			$overallAvg[$d][] = convertR($row['max(R)']);
			$overallYData[$row['YEAR(DateTime)']][] = convertR($row['max(R)']);
			$overalAll[] = convertR($row['max(R)']);
		}	
		
		$monthTotal = array_sum($dailyAvg);
		$monthMax = max($dailyAvg);
		$monthMin = min($dailyAvg);
		$monthMaxDate = array_keys($dailyAvgRaw,max($dailyAvg));
		$monthMinDate = array_keys($dailyAvgRaw,min($dailyAvg));
		
		foreach($overallYData as $temporaryYear => $annualData){
			$annualRains[$temporaryYear] = array_sum($annualData);
		}
		$absoluteAvg = avg($annualRains);
		$absoluteMax = max($annualRains);
		$absoluteMin = min($annualRains);
		$absoluteMaxDate = array_keys($annualRains,max($annualRains));
		$absoluteMinDate = array_keys($annualRains,min($annualRains));
		
		// calculate deviations
		for($i=1;$i<32;$i++){
			if(array_key_exists($i,$dailyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($dailyAvg[$i],avg($overallAvg[$i]),$decimalsR);
			}
		}
	}
	
	if($var=="W" || $var=="G"){
		$result = mysqli_query($con,"
			SELECT B, DAY(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			"
		);
		while($row = mysqli_fetch_array($result)){
			$bearings[] = $row['B'];
			$h = $row['DAY(DateTime)'];
			${"bearings".$h}[] = $row['B'];
		}
		for($i=1;$i<32;$i++){
			if(isset(${"bearings".$i})){
				${"bearings".$i."avg"} = avgWind(${"bearings".$i});
			}
		}
	}
	
	// enable interval graphs
	if($var=="T" || $var=="A" || $var=="D" || $var=="H" || $var=="P" || $var=="S"){
		$intervalGraphs = true;
	}
	else{ // no point for wind, precipitation etc. where minimum is almost always zero
		$intervalGraphs = false;
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
	<style>
		<?php
			if($var!="R"){
		?>
				#tableHourly th{
					width: 10.5%;
				}
		<?php 
			}
			else{
		?>
				#tableHourly th{
					width: 18.5%;
				}
		<?php 
			}
		?>
	</style>
	<tr>
		<td style="width:5%;text-align:right">
		</td>
		<td style='text-align:center;vertical-align:top'>
			<h2><?php echo $heading?></h2>
		</td>
		<td style="width:5%;text-align:right">
			<!--a href="<?php echo $pageURL.$path?>pages/station/reportMonthlyParamPDF.php?y=<?php echo $dateY?>&m=<?php echo $dateM?>&var=<?php echo $_GET['var']?>" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>-->
		</td>
	</tr>
	<div style="width:98%;margin:0 auto;text-align:center">
		<?php echo $UoM?>
	</div>
	<br>
	<?php 
		if($var!="R"){
	?>
			<div style="width:96%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['900']?>;border-radius:10px;padding:1%">
				<table style="width:98%;margin:0 auto">
					<tr>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("average",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("maximum",'c')?><br>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("minimum",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime average",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("deviation",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime maximum",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime minimum",'c')?>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthAvg,($dp+1),".","")." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthMax,$dp,".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($monthMaxDate)<=5){
										for($i=0;$i<count($monthMaxDate);$i++){
											echo $monthMaxDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthMin,$dp,".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($monthMinDate)<=5){
										for($i=0;$i<count($monthMinDate);$i++){
											echo $monthMinDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteAvg,($dp+1),".","")." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo deviation($monthAvg,$absoluteAvg,($dp+1))." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteMax,($dp),".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($absoluteMaxDate)<=5){
										for($i=0;$i<count($absoluteMaxDate);$i++){
											echo $absoluteMaxDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteMin,($dp),".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($absoluteMinDate)<=5){
										for($i=0;$i<count($absoluteMinDate);$i++){
											echo $absoluteMinDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
	<?php
		}
		else{
	?>
			<div style="width:96%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['900']?>;border-radius:10px;padding:1%">
				<table style="width:98%;margin:0 auto">
					<tr>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("total",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("maximum daily total",'c')?><br>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("minimum daily total",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime average",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("deviation",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime maximum",'c')?>
						</td>
						<td style="text-align:center;font-weight:bold;font-size:1.2em;font-variant:small-caps">
							<?php echo lang("alltime minimum",'c')?>
						</td>
					</tr>
					<tr>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthTotal,($dp+1),".","")." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthMax,$dp,".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($monthMaxDate)<=5){
										for($i=0;$i<count($monthMaxDate);$i++){
											echo $monthMaxDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($monthMin,$dp,".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($monthMinDate)<=5){
										for($i=0;$i<count($monthMinDate);$i++){
											echo $monthMinDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteAvg,($dp+1),".","")." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo deviation($monthTotal,$absoluteAvg,($dp+1))." ".$UoM?></strong>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteMax,($dp),".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($absoluteMaxDate)<=5){
										for($i=0;$i<count($absoluteMaxDate);$i++){
											echo $absoluteMaxDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
						<td style="text-align:center;font-size:1.0em;font-variant:small-caps">
							<strong><?php echo number_format($absoluteMin,($dp),".","")." ".$UoM?></strong>
							<div style="font-size:1em">
								<?php
									if(count($absoluteMinDate)<=5){
										for($i=0;$i<count($absoluteMinDate);$i++){
											echo $absoluteMinDate[$i]."<br>";
										}
									}
									else{
										echo lang("more than 5 instances",'c');
									}
								?>
							</div>
						</td>
					</tr>
				</table>
			</div>
	<?php 
		}
	?>
	<br>
	<div class="resizer">
		<div class="inner-resizer">
			<div id="varGraph" class="varGraphs" style="height:400px;margin:0 auto;width:100%"></div>
		</div>
	</div>
	<?php
		if($var=="H"){
	?>
			<input type="button" class="button2" value="<?php echo lang("whole scale","c") ?>" id="humidityMinSetWhole">
			<input type="button" class="button2" value="<?php echo lang("adjusted scale","c") ?>" id="humidityMinSetAdjusted">
	<?php
		}
	?>
	<?php
		if($var=="W" || $var=="G"){
	?>
			<input type="button" class="button2" value="<?php echo lang("wind direction",'c') ?>" id="windSetDirection">
	<?php
		}
	?>
	<br>
	<br>
	<?php 
		if($intervalGraphs){
	?>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="varGraphInterval" class="varGraphs" style="height:400px;margin:0 auto;width:100%"></div>
				</div>
			</div>
			<input type="button" class="button2" value="<?php echo lang("labels","c") ?>" id="rangeGraphLabels">
	<?php 
		}
	?>
	<br>
	<div class="exportDiv">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','csv')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','txt')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','excel')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','sql')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','doc')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','png')">
		<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport('tableHourly','json')">
	</div>
	<?php 
		if($var!="R"){
	?>
			<table class="table tablesorter" id="tableHourly">
				<thead>
					<tr>
						<th style="width: 50px;text-align:center">
							<?php echo lang('day','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("sdAbbr",'u')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("long-term",'c')." ".lang('avgAbbr','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("deviation",'')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('maximum','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('minimum','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i=1;$i<=$numberDays;$i++){		
					?>
						<tr>
							<td style="width: 50px;text-align:center">
								<?php echo $i?>
							</td>
							<?php 
								if(array_key_exists($i,$dailyAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($dailyAvg[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($dailyAvg[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$dailyMax)){
							?>
							<td style="font-weight:bold;color:white;background:<?php echo fill($dailyMax[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max']))?>">
								<?php echo round($dailyMax[$i],1)?>
							</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$dailyMin)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($dailyMin[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($dailyMin[$i],1)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$dailyRange)){
							?>
								<td style="font-weight:bold;color:black;background:<?php echo fill($dailyRange[$i],array((min($dailyRange)-0.001),(max($dailyRange)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400']))?>">
									<?php echo round($dailyRange[$i],1)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$dailyStddev)){
							?>
								<td style="font-weight:bold;color:black;background:<?php echo fill($dailyStddev[$i],array((min($dailyStddev)-0.001),(max($dailyStddev)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400']))?>">
									<?php echo round($dailyStddev[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($overallAvg[$i],array((min($overallAvg)-0.001),(max($overallAvg)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($overallAvg[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$deviationsAvg)){
									if($deviationsAvg[$i]<0){
										$colorTemporary = fill($deviationsAvg[$i],array((min($deviationsAvg)-0.1),0),array($colors['min'], "#999999"));
									}
									if($deviationsAvg[$i]>0){
										$colorTemporary = fill($deviationsAvg[$i],array(0,(max($deviationsAvg)+0.1)),array("#999999",$colors['max']));
									}
									if($deviationsAvg[$i]==0){
										$colorTemporary = "#999999";
									}
							?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo $deviationsAvg[$i]?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallMax)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($overallMax[$i],array((min($overallMax)-0.001),(max($overallMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($overallMax[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallMin)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($overallMin[$i],array((min($overallMin)-0.001),(max($overallMin)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($overallMin[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>
	<?php
		}
		else{
	?>
			<table class="table tablesorter" id="tableHourly">
				<thead>
					<tr>
						<th style="width: 50px;text-align:center">
							<?php echo lang('day','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang('total','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("long-term",'c')." ".lang('avgAbbr','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("deviation",'')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('maximum','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('minimum','l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i=1;$i<=$numberDays;$i++){		
					?>
						<tr>
							<td style="width: 50px;text-align:center">
								<?php echo $i?>
							</td>
							<?php 
								if(array_key_exists($i,$dailyAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($dailyAvg[$i],array((min($dailyAvg)-0.001),(max($dailyAvg)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($dailyAvg[$i],$decimalsR)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill(avg($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round(avg($overallAvg[$i]),$decimalsR)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$deviationsAvg)){
									if($deviationsAvg[$i]<0){
										$colorTemporary = fill($deviationsAvg[$i],array((min($deviationsAvg)-0.1),0),array($colors['min'], "#999999"));
									}
									if($deviationsAvg[$i]>0){
										$colorTemporary = fill($deviationsAvg[$i],array(0,(max($deviationsAvg)+0.1)),array("#999999",$colors['max']));
									}
									if($deviationsAvg[$i]==0){
										$colorTemporary = "#999999";
									}
							?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo $deviationsAvg[$i]?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill(max($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round(max($overallAvg[$i]),$decimalsR)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$overallAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill(min($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round(min($overallAvg[$i]),$decimalsR)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>
	<?php
		}
	?>
	<script>
		$(document).ready(function() {
			$(".table").tablesorter();
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
				lang: {
					months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
					shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
					weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
					resetZoom: ['<?php echo lang('default zoom','c')?>']
				}
			});
			<?php
				if($var!="R"){
			?>
					$('#varGraph').highcharts({
						chart: {
							zoomType: 'x'
						},
						credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
						title: {
							text:  ''
						},
						xAxis: {
							categories: [<?php for($i=1;$i<=$numberDays;$i++){ echo $i.","; }?>],
							title: {
								text: null
							}
						},
						yAxis: {
							title: {
								text: null
							},
							labels: {
								format: '{value} <?php echo $UoM?>'
							},
							<?php 
								if($var=="H"){
							?>
									max: 100,
							<?php
								}
							?>
							<?php 
								if($var=="W" || $var=="G" || $var=="S"){
							?>
									min: 0,
							<?php
								}
							?>
						},
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
							areasplinerange:{
								fillOpacity: 0.6
							}					
						},	
						series: [
							<?php
								if($var!="W" && $var!="G"){
							?>
									{
										type: 'spline',
										name: '<?php echo $dateY." ".lang('average','l')?>',
										zIndex: 10,
										color: '#fff',
										data: [
											<?php
												for($i=1;$i<=$numberDays;$i++){
													if(array_key_exists($i,$dailyAvg)){
														echo round($dailyAvg[$i],2).",";
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
								else{
							?>
									{
										type: 'spline',
										name: '<?php echo $dateY." ".lang('average','l')?>',
										zIndex: 10,
										marker: {
											enabled: false
										},
										color: '#fff',
										data: [
											<?php
												for($i=1;$i<=$numberDays;$i++){
													if(array_key_exists($i,$dailyAvg)){
														$icon = strtolower(windAbb(${"bearings".$i."avg"}));
														echo "{ y:".round($dailyAvg[$i],2).",marker:{symbol:'url(".$pageURL.$path."icons/winddir/".$icon.".png)'}},";
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
							{
								type: 'spline',
								name: '<?php echo lang("long-term",'c')." ".lang('average','l')?>',
								zIndex: 10,
								color: '#bbbbbb',
								data: [
									<?php
										for($i=1;$i<=$numberDays;$i++){
											if(array_key_exists($i,$overallAvg)){
												echo round($overallAvg[$i],2).",";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							},
							{	
								type: 'areasplinerange',
								name: '<?php echo $dateY." ".lang('range','l')?>',
								zIndex: 5,
								color: '#<?php echo $color_schemes[$design2]['200']?>',
								data: [
									<?php
										for($i=1;$i<=$numberDays;$i++){
											if(array_key_exists($i,$dailyMin)){
												echo "[".round($dailyMin[$i],2).",".round($dailyMax[$i],2)."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							},
							{	
								type: 'areasplinerange',
								name: '<?php echo lang("absolute",'c')." ".lang('range','l')?>',
								zIndex: 5,
								color: '#<?php echo $color_schemes[$design2]['800']?>',
								data: [
									<?php
										for($i=1;$i<=$numberDays;$i++){
											if(array_key_exists($i,$overallMax)){
												echo "[".round($overallMin[$i],2).",".round($overallMax[$i],2)."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							}
						]
					});
			<?php 
				}
				if($var=="R"){
			?>
					$('#varGraph').highcharts({
						chart: {
							zoomType: 'x'
						},
						credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
						title: {
							text:  ''
						},
						xAxis: {
							categories: [<?php for($i=1;$i<=$numberDays;$i++){ echo $i.","; }?>],
							title: {
								text: null
							}
						},
						yAxis: {
							title: {
								text: null
							},
							labels: {
								format: '{value} <?php echo $UoM?>'
							}
						},
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
							areasplinerange:{
								fillOpacity: 0.6
							}					
						},	
						series: [
							{
								type: 'column',
								name: '<?php echo $dateY." ".lang('total','l')?>',
								zIndex: 10,
								color: '#fff',
								data: [
									<?php
										for($i=1;$i<$numberDays;$i++){
											if(array_key_exists($i,$dailyAvg)){
												echo round($dailyAvg[$i],2).",";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							},
							{
								type: 'column',
								name: '<?php echo lang("long-term",'c')." ".lang('average','l')?>',
								zIndex: 10,
								color: '#265cff',
								data: [
									<?php
										for($i=1;$i<=$numberDays;$i++){
											if(array_key_exists($i,$overallAvg)){
												echo round(avg($overallAvg[$i]),$decimalsR).",";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							}
						]
					});
			<?php
				}
			?>
			<?php 
				if($intervalGraphs){
			?>
					$('#varGraphInterval').highcharts({
						chart: {
							zoomType: 'x',
							type: 'columnrange',
							inverted: true
						},
						title: {
							text:  ''
						},
						credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
						xAxis: {
							categories: [<?php for($i=1;$i<=$numberDays;$i++){ echo $i.","; }?>],
							title: {
								text: null
							}
						},
						yAxis: {
							title: {
								text: ''
							},
							labels: {
								format: '{value} <?php echo $UoM?>'
							},
							<?php 
								if($var=="H"){
							?>
									max: 100,
							<?php
								}
							?>
							<?php 
								if($var=="W" || $var=="G" || $var=="S"){
							?>
									min: 0,
							<?php
								}
							?>
						},
						tooltip: {
							shared: true,
							xDateFormat: '<?php echo $graphTimeFormat?>, <?php echo $graphDateFormat?>'
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
								dataLabels: {
									color: 'white',
									style: {
										textShadow: '0px'
									}
								}
							}
						},	
						series: [
							{
								type: 'columnrange',
								name: '<?php echo $heading." ".lang('range','l')?>',
								zIndex: 10,
								color: '<?php echo $colors['max']?>',
								data: [
									<?php
										for($i=1;$i<$numberDays;$i++){
											if(array_key_exists($i,$dailyMin)){
												echo "[".$dailyMin[$i].",".$dailyMax[$i]."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								]
							}
						]
					});
			<?php 
				}
			?>
			<?php
				if($var=="H"){
			?>
					var humidityChart = $('#varGraph').highcharts();
					$('#humidityMinSetWhole').click(function() {
						humidityChart.yAxis[0].update({
							min: 0
						});
					});
					$('#humidityMinSetAdjusted').click(function() {
						humidityChart.yAxis[0].update({
							min: null
						});
					});
			<?php
				}
			?>
			<?php
				if($var=="W" || $var=="G"){
			?>
					var windChart = $('#varGraph').highcharts();
					var showDirectionW = true;
					$('#windSetDirection').click(function() {
						windChart.series[0].update({
							marker: {
								enabled: showDirectionW
							}
						});
						showDirectionW = !showDirectionW;
					});
			<?php
				}
			?>
			<?php 
				if($intervalGraphs){
			?>
					var rangeChart = $('#varGraphInterval').highcharts();
					var enableDataLabels = true;
					$('#rangeGraphLabels').click(function() {
						rangeChart.series[0].update({
							dataLabels: {
								enabled: enableDataLabels
							}
						});
						enableDataLabels = !enableDataLabels;
					});
			<?php
				}
			?>
		})
	</script>