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
	
	include("../../config.php");
	
	//error_reporting(E_ALL);
	
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	if(isset($_GET['var'])){
		$var = $_GET['var'];
	}
	else{
		die("No parameter specified.");
	}
	
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
	
	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCols = array("T","Tmax","Tmin");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCols = array("A","A","A");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="D"){
		$heading = lang("dew point",'c');
		$mySQLCols = array("D","D","D");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCols = array("H","H","H");
		$colors['min'] = "#d9a300";
		$colors['max'] = "#00b300";
		$deviationsDecimals = 1;
		$UoM = "%";
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCols = array("P","P","P");
		$colors['min'] = "#ffa64c";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = $decimalsP + 2;
		$UoM = unitFormatter($displayPressUnits);
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCols = array("W","W","W");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCols = array("G","G","G");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCols = array("S","S","S");
		$colors['min'] = "#222222";
		$colors['max'] = "#ffd24c";
		$deviationsDecimals = 0;
		$UoM = "W/m2";
	}
	if($var=="R"){
		$heading = lang("precipitation",'c');
		$colors['min'] = "#999999";
		$colors['max'] = "#006cd9";
		$UoM = unitFormatter($displayRainUnits);
	}
	
	
	// get hourly data	
	if($var!="R"){ // rain must be treated differently - data in db is cumulative
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), stddev(".$mySQLCols[0]."), HOUR(DateTime)
			FROM alldata 
			WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$h = $row['HOUR(DateTime)'];
			$hourlyAvg[$h] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$hourlyMax[$h] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$hourlyMin[$h] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$hourlyRange[$h] = (chooseConvertor($row['max('.$mySQLCols[1].')'])-chooseConvertor($row['min('.$mySQLCols[2].')']));
			if($var=="T" || $var=="A" || $var=="D"){
				$hourlyStddev[$h] = convertTStddev($row['stddev('.$mySQLCols[0].')']);
			}
			else{
				$hourlyStddev[$h] = chooseConvertor($row['stddev('.$mySQLCols[0].')']);
			}
		}
		
		// long-term
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), HOUR(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM."
			GROUP BY HOUR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$H = $row["HOUR(DateTime)"];
			$overallAvg[$H] = chooseConvertor($row["avg(".$mySQLCols[0].")"]);
			$overallMax[$H] = chooseConvertor($row["max(".$mySQLCols[1].")"]);
			$overallMin[$H] = chooseConvertor($row["min(".$mySQLCols[2].")"]);
		}
		// calculate deviations
		for($i=0;$i<24;$i++){
			if(array_key_exists($i,$hourlyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($hourlyAvg[$i],$overallAvg[$i],$deviationsDecimals);
			}
		}
	}
	if($var=="R"){ // cumulative = for rain the value is hourly max minus hourly min
		$result = mysqli_query($con,"
			SELECT max(R), min(R), HOUR(DateTime)
			FROM alldata 
			WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$h = $row['HOUR(DateTime)'];
			$hourlyAvg[$h] = convertR($row['max(R)']) - convertR($row['min(R)']);
		}
		// long-term
		$result = mysqli_query($con,"
			SELECT max(R), min(R), HOUR(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM."
			GROUP BY HOUR(DateTime), YEAR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$h = $row["HOUR(DateTime)"];
			$overallAvg[$h][] = convertR($row['max(R)']) - convertR($row['min(R)']);
			$overalAll[] = convertR($row['max(R)']) - convertR($row['min(R)']);
		}	
		// calculate deviations
		for($i=0;$i<24;$i++){
			if(array_key_exists($i,$hourlyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($hourlyAvg[$i],avg($overallAvg[$i]),$decimalsR);
			}
		}
	}
	
	if($var=="W" || $var=="G"){
		$result = mysqli_query($con,"
			SELECT B, Hour(DateTime)
			FROM alldata 
			WHERE DAY(DateTime)=".$dateD." AND MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			"
		);
		while($row = mysqli_fetch_array($result)){
			$bearings[] = $row['B'];
			$h = $row['Hour(DateTime)'];
			${"bearings".$h}[] = $row['B'];
		}
		for($i=0;$i<24;$i++){
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
	<h2><?php echo $heading?></h2>
	<div style="width:98%;margin:0 auto;text-align:center">
		<?php echo $UoM?>
	</div>
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
							<span class="fa fa-clock-o"></span><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang('avgAbbr','c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang('maximumAbbr','c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang('minimumAbbr','c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang('range','c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("sdAbbr",'u')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("station average",'l')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("deviation",'')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('maximum','l')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('minimum','l')?><br><span class="fa fa-unsorted sort"></span>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i=0;$i<24;$i++){		
					?>
						<tr>
							<td style="width: 50px;text-align:center">
								<?php echo $i?>
							</td>
							<?php 
								if(array_key_exists($i,$hourlyAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($hourlyAvg[$i],array((min($hourlyMin)-0.001),(max($hourlyMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($hourlyAvg[$i],2)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$hourlyMax)){
							?>
							<td style="font-weight:bold;color:white;background:<?php echo fill($hourlyMax[$i],array((min($hourlyMin)-0.001),(max($hourlyMax)+0.001)),array($colors['min'], $colors['max']))?>">
								<?php echo round($hourlyMax[$i],1)?>
							</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$hourlyMin)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($hourlyMin[$i],array((min($hourlyMin)-0.001),(max($hourlyMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($hourlyMin[$i],1)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$hourlyRange)){
							?>
								<td style="font-weight:bold;color:black;background:<?php echo fill($hourlyRange[$i],array((min($hourlyRange)-0.001),(max($hourlyRange)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400']))?>">
									<?php echo round($hourlyRange[$i],1)?>
								</td>
							<?php
								}
								else echo "<td></td>";
							?>
							<?php 
								if(array_key_exists($i,$hourlyStddev)){
							?>
								<td style="font-weight:bold;color:black;background:<?php echo fill($hourlyStddev[$i],array((min($hourlyStddev)-0.001),(max($hourlyStddev)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400']))?>">
									<?php echo round($hourlyStddev[$i],2)?>
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
							<img src="<?php echo $pageURL.$path?>icons/time.png" class="clock" alt='' style="width:15px"><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang('total','c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("station average",'c')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("deviation",'')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('maximum','l')?><br><span class="fa fa-unsorted sort"></span>
						</th>
						<th>
							<?php echo lang("absolute",'c')." ".lang('minimum','l')?><br><span class="fa fa-unsorted sort"></span>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						for($i=0;$i<24;$i++){		
					?>
						<tr>
							<td style="width: 50px;text-align:center">
								<?php echo $i?>
							</td>
							<?php 
								if(array_key_exists($i,$hourlyAvg)){
							?>
								<td style="font-weight:bold;color:white;background:<?php echo fill($hourlyAvg[$i],array((min($hourlyAvg)-0.001),(max($hourlyAvg)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo round($hourlyAvg[$i],$decimalsR)?>
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
						title: {
							text:  ''
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: ['0-1','1-2','2-3','3-4','4-5','5-6','6-7','7-8','8-9','9-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-0'],
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
									max: 100
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
												for($i=0;$i<24;$i++){
													if(array_key_exists($i,$hourlyAvg)){
														echo round($hourlyAvg[$i],2).",";
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
												for($i=0;$i<24;$i++){
													if(array_key_exists($i,$hourlyAvg)){
														$icon = strtolower(windAbb(${"bearings".$i."avg"}));
														echo "{ y:".round($hourlyAvg[$i],2).",marker:{symbol:'url(".$pageURL.$path."icons/winddir/".$icon.".png)'}},";
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
								name: '<?php echo lang("station average",'c')?>',
								zIndex: 10,
								color: '#bbbbbb',
								data: [
									<?php
										for($i=0;$i<24;$i++){
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
										for($i=0;$i<24;$i++){
											if(array_key_exists($i,$hourlyMin)){
												echo "[".round($hourlyMin[$i],2).",".round($hourlyMax[$i],2)."],";
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
										for($i=0;$i<24;$i++){
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
						title: {
							text:  ''
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: ['0-1','1-2','2-3','3-4','4-5','5-6','6-7','7-8','8-9','9-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-0'],
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
										for($i=0;$i<24;$i++){
											if(array_key_exists($i,$hourlyAvg)){
												echo round($hourlyAvg[$i],2).",";
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
								name: '<?php echo lang("station average",'c')?>',
								zIndex: 10,
								color: '#265cff',
								data: [
									<?php
										for($i=0;$i<24;$i++){
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
							categories: ['0-1','1-2','2-3','3-4','4-5','5-6','6-7','7-8','8-9','9-10','10-11','11-12','12-13','13-14','14-15','15-16','16-17','17-18','18-19','19-20','20-21','21-22','22-23','23-0'],
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
									max: 100
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
										for($i=0;$i<24;$i++){
											if(array_key_exists($i,$hourlyMin)){
												echo "[".$hourlyMin[$i].",".$hourlyMax[$i]."],";
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