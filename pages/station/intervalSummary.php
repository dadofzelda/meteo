<?php
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	if(isset($_GET['from'])){
		$fromRaw = explode("_",$_GET['from']);
		$fromY = $fromRaw[0];
		if(!is_numeric($fromY) || $fromY<1900 || $fromY>date("Y")){
			$fromY = date("Y");
		}
		$fromM = $fromRaw[1];
		if(!is_numeric($fromM) || $fromM<1 || $fromM>12){
			$fromM = date("m");
		}
		$fromD = $fromRaw[2];
		if(!is_numeric($fromD) || $fromD<1 || $fromD>31){
			$fromD = date("d");
		}
		$fromH = $fromRaw[3];
		if(!is_numeric($fromH) || $fromH<0 || $fromH>24){
			$fromH = date("H");
		}
		$fromMin = $fromRaw[4];
		if(!is_numeric($fromMin) || $fromMin<0 || $fromMin>59){
			$fromMin = date("i");
		}
		$from = $fromY."-".$fromM."-".$fromD." ".$fromH.":".$fromMin;
	}
	else{
		$from = date("Y-m-d H:i",strtotime('last month'));
	}
	
	if(isset($_GET['to'])){
		$toRaw = explode("_",$_GET['to']);
		$toY = $toRaw[0];
		if(!is_numeric($toY) || $toY<1900 || $toY>date("Y")){
			$toY = date("Y");
		}
		$toM = $toRaw[1];
		if(!is_numeric($toM) || $toM<1 || $toM>12){
			$toM = date("m");
		}
		$toD = $toRaw[2];
		if(!is_numeric($toD) || $toD<1 || $toD>31){
			$toD = date("d");
		}
		$toH = $toRaw[3];
		if(!is_numeric($toH) || $toH<0 || $toH>24){
			$toH = date("H");
		}
		$toMin = $toRaw[4];
		if(!is_numeric($toMin) || $toMin<0 || $toMin>59){
			$toMin = date("i");
		}
		$to = $toY."-".$toM."-".$toD." ".$toH.":".$toMin;
	}
	else{
		$to = date("Y-m-d H:i",strtotime('last month'));
	}
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	
	// check interval size
	$oneYear = 60 * 60 * 24 * 365;
	$fromTimestamp = strtotime($from);
	$toTimestamp = strtotime($to);
	$intervalLength = $toTimestamp - $fromTimestamp;
	$intervalType = "normal";
	if($intervalLength>$oneYear){
		$intervalType = "hour";
	}
	if($intervalLength>=($oneYear*3)){
		$intervalType = "day";
	}
	
	// OVERALL STATS
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(W), avg(G), avg(A), avg(D), avg(S), max(Tmax), max(H), max(P), max(W), max(G), max(A), max(D), max(S), min(Tmin), min(H), min(P), min(W), min(G), min(A), min(D), min(S)
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to'
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['avgT'] = convertT($row['avg(T)']);
		$overall['avgH'] = ($row['avg(H)']);
		$overall['avgP'] = convertP($row['avg(P)']);
		$overall['avgW'] = convertW($row['avg(W)']);
		$overall['avgG'] = convertW($row['avg(G)']);
		$overall['avgA'] = convertT($row['avg(A)']);
		$overall['avgD'] = convertT($row['avg(D)']);
		$overall['avgS'] = ($row['avg(S)']);
		
		$overall['maxT'] = convertT($row['max(Tmax)']);
		$overallRawTmax = $row['max(Tmax)'];
		$overall['maxH'] = ($row['max(H)']);
		$overallRawHmax = $row['max(H)'];
		$overall['maxP'] = convertP($row['max(P)']);
		$overallRawPmax = $row['max(P)'];
		$overall['maxW'] = convertW($row['max(W)']);
		$overallRawWmax = $row['max(W)'];
		$overall['maxG'] = convertW($row['max(G)']);
		$overallRawGmax = $row['max(G)'];
		$overall['maxA'] = convertT($row['max(A)']);
		$overallRawAmax = $row['max(A)'];
		$overall['maxD'] = convertT($row['max(D)']);
		$overallRawDmax = $row['max(D)'];
		if($solarSensor){
			$overall['maxS'] = ($row['max(S)']);
			$overallRawSmax = $row['max(S)'];
		}
		
		$overall['minT'] = convertT($row['min(Tmin)']);
		$overallRawTmin = $row['min(Tmin)'];
		$overall['minH'] = ($row['min(H)']);
		$overallRawHmin = $row['min(H)'];
		$overall['minP'] = convertP($row['min(P)']);
		$overallRawPmin = $row['min(P)'];
		$overall['minW'] = convertW($row['min(W)']);
		$overallRawWmin = $row['min(W)'];
		$overall['minG'] = convertW($row['min(G)']);
		$overallRawGmin = $row['min(G)'];
		$overall['minA'] = convertT($row['min(A)']);
		$overallRawAmin = $row['min(A)'];
		$overall['minD'] = convertT($row['min(D)']);
		$overallRawDmin = $row['min(D)'];
		if($solarSensor){
			$overall['minS'] = ($row['min(S)']);
			$overallRawSmin = $row['min(S)'];
		}
	}
	
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND Tmax=$overallRawTmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxTDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND Tmin=$overallRawTmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minTDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND H=$overallRawHmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxHDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND H=$overallRawHmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minHDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND P=$overallRawPmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxPDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND P=$overallRawPmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minPDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND W=$overallRawWmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxWDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND W=$overallRawWmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minWDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND G=$overallRawGmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxGDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND G=$overallRawGmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minGDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND A=$overallRawAmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxADate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND A=$overallRawAmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minADate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND D=$overallRawDmax
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['maxDDate'][] = strtotime($row['DateTime']);
	}
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE DateTime>='$from' AND DateTime<='$to' AND D=$overallRawDmin
		"
	);
	while($row = mysqli_fetch_array($result)){
		$overall['minDDate'][] = strtotime($row['DateTime']);
	}
	
	if($solarSensor){
		$result = mysqli_query($con,"
			SELECT DateTime
			FROM alldata 
			WHERE DateTime>='$from' AND DateTime<='$to' AND S=$overallRawSmax
			"
		);
		while($row = mysqli_fetch_array($result)){
			$overall['maxSDate'][] = strtotime($row['DateTime']);
		}
		$result = mysqli_query($con,"
			SELECT DateTime
			FROM alldata 
			WHERE DateTime>='$from' AND DateTime<='$to' AND S=$overallRawSmin
			"
		);
		while($row = mysqli_fetch_array($result)){
			$overall['minSDate'][] = strtotime($row['DateTime']);
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("interval summary","c")?></title>
		<?php metaHeader()?>
		<style>
			.inner-resizer {
				padding: 10px;
			}
			.resizer {
				border-bottom: 1px solid white;   
				margin: 0 auto;
				width: 98%;
			}
			.overallStatValue{
				font-weight: bold;
				font-size: 1.2em;
			}
			.overallStatDate{
				font-variant:small-caps;
			}
			.spinnerSmall {
				position: relative;
				color: #fff;
				display: inline-block;
				width:  1em; 
				height: 1em; 
				font-size: 40px; 
				border-bottom: 3px solid; 
				vertical-align: middle;
				overflow: hidden;  
				text-indent: 100%;
				-webkit-animation: 0.9s spinnerSmall linear infinite;
				   -moz-animation: 0.9s spinnerSmall linear infinite;
						animation: 0.9s spinnerSmall linear infinite;
				&,
				&:after {
					border-radius: 100%;
				}       
				&:after {
					content: "";
					position: absolute;
					top:    0;
					right:  0;
					bottom: 0;
					left:   0;
					border: 1px solid; 
					opacity: 0.5;
				}
			}
			@-webkit-keyframes spinnerSmall {
				to {
					-webkit-transform: rotate(360deg);
				}
			}
			@-moz-keyframes spinnerSmall {
				to {
					-moz-transform: rotate(360deg);
				}
			}
			@keyframes spinnerSmall {

				to {
					transform: rotate(360deg);
				}
			}
			.spinnerSmall, .spinnerSmall:after {
			  border-radius: 100%;
			}
			.tableIconSmall{
				width: 30px;
			}
		</style>
		<script src="//code.highcharts.com/stock/highstock.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<h1><?php echo lang('interval summary','c')?></h1>
			<h2 style="font-size:0.8em"><?php echo date($dateTimeFormat,strtotime($from))." - ".date($dateTimeFormat,strtotime($to))?></h2>
			<br>
			<table class="table" style="width:96%;margin:0 auto">
				<thead>
					<tr>
						<th>
						
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
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="tableIconSmall tooltip" title="<?php echo lang('temperature','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgT'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxT'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxTDate'])<=5){
									foreach($overall['maxTDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minT'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minTDate'])<=5){
									foreach($overall['minTDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxT']-$overall['minT']),1,".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="tableIconSmall tooltip" title="<?php echo lang('humidity','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgH'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxH'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxHDate'])<=5){
									foreach($overall['maxHDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minH'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minHDate'])<=5){
									foreach($overall['minHDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxH']-$overall['minH']),1,".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="tableIconSmall tooltip" title="<?php echo lang('pressure','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgP'],($decimalsP+1),".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxP'],($decimalsP),".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxPDate'])<=5){
									foreach($overall['maxPDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minP'],($decimalsP),".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minPDate'])<=5){
									foreach($overall['minPDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxP']-$overall['minP']),($decimalsP),".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="tableIconSmall tooltip" title="<?php echo lang('wind speed','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgW'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxW'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxWDate'])<=5){
									foreach($overall['maxWDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minW'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minWDate'])<=5){
									foreach($overall['minWDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxW']-$overall['minW']),1,".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/gust.png" class="tableIconSmall tooltip" title="<?php echo lang('wind gust','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgG'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxG'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxGDate'])<=5){
									foreach($overall['maxGDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minG'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minGDate'])<=5){
									foreach($overall['minGDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxG']-$overall['minG']),1,".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="tableIconSmall tooltip" title="<?php echo lang('apparent temperature','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgA'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxA'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxADate'])<=5){
									foreach($overall['maxADate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minA'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minADate'])<=5){
									foreach($overall['minADate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxA']-$overall['minA']),1,".","");?></span>
						</td>
					</tr>
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="tableIconSmall tooltip" title="<?php echo lang('dewpoint','c')?>">
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['avgD'],2,".","");?></span>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['maxD'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['maxDDate'])<=5){
									foreach($overall['maxDDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format($overall['minD'],1,".","");?></span>
							<br>
							<div class="overallStatDate">
							<?php
								if(count($overall['minDDate'])<=5){
									foreach($overall['minDDate'] as $dateValue){ 
										echo date($dateTimeFormat,$dateValue)."<br>";
									}
								}
								else{
									echo lang('more than 5 instances','l');
								}
							?>
							</div>
						</td>
						<td>
							<span class="overallStatValue"><?php echo number_format(($overall['maxD']-$overall['minD']),1,".","");?></span>
						</td>
					</tr>
					<?php
						if($solarSensor){
					?>
							<tr>
								<td>
									<img src="<?php echo $pageURL.$path?>icons/sun.png" class="tableIconSmall tooltip" title="<?php echo lang('solar radiation','c')?>">
								</td>
								<td>
									<span class="overallStatValue"><?php echo number_format($overall['avgS'],1,".","");?></span>
								</td>
								<td>
									<span class="overallStatValue"><?php echo number_format($overall['maxS'],0,".","");?></span>
									<br>
									<div class="overallStatDate">
									<?php
										if(count($overall['maxSDate'])<=5){
											foreach($overall['maxSDate'] as $dateValue){ 
												echo date($dateTimeFormat,$dateValue)."<br>";
											}
										}
										else{
											echo lang('more than 5 instances','l');
										}
									?>
									</div>
								</td>
								<td>
									<span class="overallStatValue"><?php echo number_format($overall['minS'],0,".","");?></span>
									<br>
									<div class="overallStatDate">
									<?php
										if(count($overall['minSDate'])<=5){
											foreach($overall['minSDate'] as $dateValue){ 
												echo date($dateTimeFormat,$dateValue)."<br>";
											}
										}
										else{
											echo lang('more than 5 instances','l');
										}
									?>
									</div>
								</td>
								<td>
									<span class="overallStatValue"><?php echo number_format(($overall['maxS']-$overall['minS']),0,".","");?></span>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br><br>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="containerT" class="container" style="width:98%;margin:0 auto;height:400px;">
						<span class="spinnerSmall">Loading…</span>
					</div>
				</div>
			</div>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="containerH" class="container" style="width:98%;margin:0 auto;height:400px;">
						<span class="spinnerSmall">Loading…</span>
					</div>
				</div>
			</div>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="containerP" class="container" style="width:98%;margin:0 auto;height:400px;">
						<span class="spinnerSmall">Loading…</span>
					</div>
				</div>
			</div>
			<div class="resizer">
				<div class="inner-resizer">
					<div id="containerW" class="container" style="width:98%;margin:0 auto;height:400px;">
						<span class="spinnerSmall">Loading…</span>
					</div>
				</div>
			</div>
			<br><br>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$(function () {
				$.ajax({
					url : "intervalSummaryLoad.php?from=<?php echo $_GET['from']?>&to=<?php echo $_GET['to']?>&parameter=T",
					dataType : 'json',
					success : function (data) {	
						showGraphT(data);
					}
				});
				$.ajax({
					url : "intervalSummaryLoad.php?from=<?php echo $_GET['from']?>&to=<?php echo $_GET['to']?>&parameter=H",
					dataType : 'json',
					success : function (data) {	
						showGraphH(data);
					}
				});
				$.ajax({
					url : "intervalSummaryLoad.php?from=<?php echo $_GET['from']?>&to=<?php echo $_GET['to']?>&parameter=P",
					dataType : 'json',
					success : function (data) {	
						showGraphP(data);
					}
				});
				$.ajax({
					url : "intervalSummaryLoad.php?from=<?php echo $_GET['from']?>&to=<?php echo $_GET['to']?>&parameter=W",
					dataType : 'json',
					success : function (data) {	
						showGraphW(data);
					}
				});
				Highcharts.setOptions({
					global: {
						useUTC: false,
					},
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>']
					}		
				});
					function showGraphT(data){
						$('#containerT').highcharts('StockChart', {
							chart: {
								zoomType: 'x'
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
										text: '<?php echo lang('temperature','c')?> (°<?php echo $displayTempUnits?>)'
									},
									opposite: false
							},
							title: {
								text: "<?php echo lang('temperature','c')?>"
							},
							
							series: [
								{
									name: '<?php echo lang('temperature','c')?>',
									data: data['T'],
									tooltip: {
										valueDecimals: 1,
										valueSuffix: '°<?php echo $displayTempUnits?>'
									},
								}
							],
							tooltip: {
								<?php 
									if($intervalType=="normal"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H:%M",
									second:"%A, %b %e, %H:%M",
									minute:"%A, %b %e, %H:%M",
									hour:"%A, %b %e, %H:%M",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="hour"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									second:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									minute:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									hour:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="day"){
								?>
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
								<?php 
									}
								?>
							},
						});
					}
					function showGraphH(data){
					$('#containerH').highcharts('StockChart', {
						chart: {
							zoomType: 'x'
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
									text: '<?php echo lang('humidity','c')?> (%)'
								},
								opposite: false,
								max: 100
						},
						title: {
							text: "<?php echo lang('humidity','c')?>"
						},

						series: [
						{
							name: '<?php echo lang('humidity','c')?>',
							data: data['H'],
							tooltip: {
								valueDecimals: 1,
								valueSuffix: '%'
							}
						}
						
						],
						tooltip: {
								<?php 
									if($intervalType=="normal"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H:%M",
									second:"%A, %b %e, %H:%M",
									minute:"%A, %b %e, %H:%M",
									hour:"%A, %b %e, %H:%M",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="hour"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									second:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									minute:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									hour:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="day"){
								?>
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
								<?php 
									}
								?>
							},
					});	
					}
					function showGraphP(data){
					$('#containerP').highcharts('StockChart', {
						chart: {
							zoomType: 'x'
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
									text: '<?php echo lang('pressure','c')?> (<?php echo $displayPressUnits?>)'
								},
								opposite: false
						},
						title: {
							text: "<?php echo lang('pressure','c')?>"
						},

						series: [
						{
							name: '<?php echo lang('pressure','c')?>',
							data: data['P'],
							tooltip: {
								valueDecimals: 1,
								valueSuffix: '<?php echo unitFormatter($displayPressUnits)?>'
							},
							yAxis: 0
						}
						],
							tooltip: {
								<?php 
									if($intervalType=="normal"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H:%M",
									second:"%A, %b %e, %H:%M",
									minute:"%A, %b %e, %H:%M",
									hour:"%A, %b %e, %H:%M",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="hour"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									second:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									minute:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									hour:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="day"){
								?>
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
								<?php 
									}
								?>
							},
					});
					}
					function showGraphW(data){
						$('#containerW').highcharts('StockChart', {
							chart: {
								zoomType: 'x'
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
										text: '<?php echo lang('wind speed','c')?> (<?php echo unitFormatter($displayWindUnits)?>)'
									},
									opposite: false
							},
							title: {
								text: "<?php echo lang('wind speed','c')?>"
							},
							series: [
								{
									name: '<?php echo lang('wind speed','c')?>',
									data: data['W'],
									tooltip: {
										valueDecimals: 1,
										valueSuffix: '<?php echo unitFormatter($displayWindUnits)?>'
									},
									yAxis: 0
								}
							],
							tooltip: {
								<?php 
									if($intervalType=="normal"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H:%M",
									second:"%A, %b %e, %H:%M",
									minute:"%A, %b %e, %H:%M",
									hour:"%A, %b %e, %H:%M",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="hour"){
								?>
								dateTimeLabelFormats: {
									millisecond:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									second:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									minute:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									hour:"%A, %b %e, %H <?php echo lang('hAbbr','l')?>",
									day:"%A, %b %e, %Y",
									week:"Week from %A, %b %e, %Y",
									month:"%B %Y",
									year:"%Y"
								}
								<?php 
									}
								?>
								<?php 
									if($intervalType=="day"){
								?>
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
								<?php 
									}
								?>
							},
						});
					}
				$('.resizer').resizable({
					resize: function() {
						selectedDiv = $(this).find(".container");
						chart = selectedDiv.highcharts();
						chart.setSize(
							this.offsetWidth - 50, 
							this.offsetHeight - 50,
							false
						);
					}
				});
			});
		</script>
		<?php include("../../css/highcharts.php");?>
	</body>
</html>