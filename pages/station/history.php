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
	#	History
	#
	# 	A script which shows past weather conditions (hour ago, day ago, etc.).
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
	include($baseURL."scripts/stats.php");
	
	$yearsData = array();
	
	$yearInitial = 2;
	$oneYear = array();
	
	//current
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$currentT = convertT($row['T']);
		$currentH = $row['H'];
		$currentP = convertP($row['P']);
		$currentW = convertW($row['W']);
		$currentG = convertW($row['G']);
		$currentA = convertT($row['A']);
		$currentD = convertT($row['D']);
		$currentS = $row['S'];
	}
	// hour ago
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		WHERE DateTime <= now() - interval 1 hour
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$hourT = convertT($row['T']);
		$hourH = $row['H'];
		$hourP = convertP($row['P']);
		$hourW = convertW($row['W']);
		$hourG = convertW($row['G']);
		$hourA = convertT($row['A']);
		$hourD = convertT($row['D']);
		$hourS = $row['S'];
	}
	// day ago
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		WHERE DateTime <= now() - interval 1 day
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$yesterdayT = convertT($row['T']);
		$yesterdayH = $row['H'];
		$yesterdayP = convertP($row['P']);
		$yesterdayW = convertW($row['W']);
		$yesterdayG = convertW($row['G']);
		$yesterdayA = convertT($row['A']);
		$yesterdayD = convertT($row['D']);
		$yesterdayS = $row['S'];
	}
	// week ago
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		WHERE DateTime <= now() - interval 1 week
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$weekT = convertT($row['T']);
		$weekH = $row['H'];
		$weekP = convertP($row['P']);
		$weekW = convertW($row['W']);
		$weekG = convertW($row['G']);
		$weekA = convertT($row['A']);
		$weekD = convertT($row['D']);
		$weekS = $row['S'];
	}
	// month ago
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		WHERE DateTime <= now() - interval 1 month
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$monthT = convertT($row['T']);
		$monthH = $row['H'];
		$monthP = convertP($row['P']);
		$monthW = convertW($row['W']);
		$monthG = convertW($row['G']);
		$monthA = convertT($row['A']);
		$monthD = convertT($row['D']);
		$monthS = $row['S'];
	}
	// year ago
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		WHERE DateTime <= now() - interval 1 year
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$yearT = convertT($row['T']);
		$yearH = $row['H'];
		$yearP = convertP($row['P']);
		$yearW = convertW($row['W']);
		$yearG = convertW($row['G']);
		$yearA = convertT($row['A']);
		$yearD = convertT($row['D']);
		$yearS = $row['S'];
	}
			
	$yearsData = array();
	$oneYear = array();
	
	// get first date in db and number of years
	$result = mysqli_query($con,"
		SELECT  DateTime, Year(DateTime)
		FROM  alldata
		ORDER BY DateTime 
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$firstDate = strtotime($row['DateTime']);
		$firstYear = $row['Year(DateTime)'];
	}
	
	$yearsNumber = date('Y')-$firstYear;
	
	// get data for each year
	for($i=2;$i<=$yearsNumber;$i++){
		$result = mysqli_query($con,"
			SELECT  *
			FROM  alldata
			WHERE DateTime <= now() - interval ".$i." year
			ORDER BY DateTime DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){	
			$oneYear['T'] = convertT($row['T']);
			$oneYear['H'] = $row['H'];
			$oneYear['P'] = convertP($row['P']);
			$oneYear['W'] = convertW($row['W']);
			$oneYear['G'] = convertW($row['G']);
			$oneYear['A'] = convertT($row['A']);
			$oneYear['D'] = convertT($row['D']);
			$oneYear['S'] = $row['S'];
			array_push($yearsData,$oneYear);
		}
	}

	function diffCalc($val,$val2,$dp=1){
		$diff = $val - $val2;
		$diff = number_format($diff,$dp,".","");
		if($diff>0){
			$diff = "+".$diff;
		}
		return $diff;
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("history",'c')?></title>
		<?php metaHeader()?>
		<style>
			.trend{
				text-align: center;
			}
			.value{
				text-align: center;
			}
			.trendIcon{
				width: 25px;
			}
			.current{
				font-weight: bold;
				font-size: 1.25em;
				text-align: center!important;
				opacity: 1!important;
			}
			.comparisonSpan{
				font-size:0.8em;
			}
			.valueSpan{
				font-weight: bold;
				font-size:1.2em;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
		<h1><?php echo lang("history","c")?></h1>
		<br>
		<table class="table">
			<thead>
				<tr>
					<th>
						
					</th>
					<th style="text-align:center">
						<span class="mticon-temp tooltip" style="font-size:1.8em" title="<?php echo lang('temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?>
					</th>
					<th style="text-align:center">
						<span class="mticon-humidity tooltip" style="font-size:1.8em" title="<?php echo lang('humidity','c')?>"></span><br>%
					</th>
					<th style="text-align:center">
						<span class="mticon-pressure tooltip" style="font-size:1.8em" title="<?php echo lang('pressure','c')?>"></span><br><?php echo unitFormatter($displayPressUnits)?>
					</th>
					<th style="text-align:center">
						<span class="mticon-wind tooltip" style="font-size:1.8em" title="<?php echo lang('wind speed','c')?>"></span><br><?php echo unitFormatter($displayWindUnits)?>
					</th>
					<th style="text-align:center">
						<span class="mticon-gust tooltip" style="font-size:1.8em" title="<?php echo lang('wind gust','c')?>"></span><br><?php echo unitFormatter($displayWindUnits)?>
					</th>
					<th style="text-align:center">
						<span class="mticon-apparent tooltip" style="font-size:1.8em" title="<?php echo lang('apparent temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?>
					</th>
					<th style="text-align:center">
						<span class="mticon-dewpoint tooltip" style="font-size:1.8em" title="<?php echo lang('dew point','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?>
					</th>
					<?php 
						if($solarSensor){
					?>
						<th style="text-align:center">
							<span class="mticon-sun tooltip" style="font-size:1.8em" title="<?php echo lang('solar radiation','c')?>"></span><br>W/m<sup>2</sup>
						</th>
					<?php
						}
					?>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th>
						<?php echo lang("current","c")?>
					</th>
					<th class="value current">
						<?php echo number_format($currentT,1)?>
					</th>
					<th class="value current">
						<?php echo number_format($currentH,1)?>
					</th>
					<th class="value current">
						<?php echo number_format($currentP,$decimalsP,".","")?>
					</th>
					<th class="value current">
						<?php echo number_format($currentW,1)?>
					</th>
					<th class="value current">
						<?php echo number_format($currentG,1)?>
					</th>
					<th class="value current">
						<?php echo number_format($currentA,1)?>
					</th>
					<th class="value current">
						<?php echo number_format($currentD,1)?>
					</th>
					<?php 
						if($solarSensor){
					?>
						<th class="value current">
							<?php echo number_format($currentS,0)?>
						</th>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("hour ago",'c')?>
					</th>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourT,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourT,$currentT,1)?></span><br>
						<?php
							if($hourT<$currentT){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourT==$currentT){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourT>$currentT){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourH,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourH,$currentH,1)?></span><br>
						<?php
							if($hourH<$currentH){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourH==$currentH){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourH>$currentH){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourP,$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourP,$currentP,$decimalsP)?></span><br>
						<?php
							if($hourP<$currentP){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourT==$currentP){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourP>$currentP){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourW,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourW,$currentW,1)?></span><br>
						<?php
							if($hourW<$currentW){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourW==$currentW){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourW>$currentW){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourG,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourG,$currentG,1)?></span><br>
						<?php
							if($hourG<$currentG){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourG==$currentG){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourG>$currentG){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourA,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourA,$currentA,1)?></span><br>
						<?php
							if($hourA<$currentA){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourA==$currentA){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourA>$currentA){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($hourD,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourD,$currentD,1)?></span><br>
						<?php
							if($hourD<$currentD){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($hourD==$currentD){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($hourD>$currentD){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($hourS,0)?></span><br><span class="comparisonSpan"><?php echo diffCalc($hourS,$currentS,0)?></span><br>
							<?php
								if($hourS<$currentS){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($hourS==$currentS){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($hourS>$currentS){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("yesterday",'c')?>
					</th>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayT,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayT,$currentT,1)?></span><br>
						<?php
							if($yesterdayT<$currentT){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayT==$currentT){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayT>$currentT){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayH,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayH,$currentH,1)?></span><br>
						<?php
							if($yesterdayH<$currentH){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayH==$currentH){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayH>$currentH){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayP,$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayP,$currentP,$decimalsP)?></span><br>
						<?php
							if($yesterdayP<$currentP){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayP==$currentP){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayP>$currentP){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayW,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayW,$currentW,1)?></span><br>
						<?php
							if($yesterdayW<$currentW){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayW==$currentW){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayW>$currentW){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayG,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayG,$currentG,1)?></span><br>
						<?php
							if($yesterdayG<$currentG){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayG==$currentG){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayG>$currentG){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayA,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayA,$currentA,1)?></span><br>
						<?php
							if($yesterdayA<$currentA){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayA==$currentA){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayA>$currentA){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yesterdayD,1)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayD,$currentD,1)?></span><br>
						<?php
							if($yesterdayD<$currentD){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yesterdayD==$currentD){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yesterdayD>$currentD){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yesterdayS,0)?></span><br><span class="comparisonSpan"><?php echo diffCalc($yesterdayS,$currentS,0)?></span><br>
							<?php
								if($yesterdayS<$currentS){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yesterdayS==$currentS){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yesterdayS>$currentS){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("week ago",'c')?>
					</th>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekT,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekT,$currentT,1)?></span><br>
						<?php
							if($weekT<$currentT){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekT==$currentT){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekT>$currentT){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekH,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekH,$currentH,1)?></span><br>
						<?php
							if($weekH<$currentH){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekH==$currentH){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekH>$currentH){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekP,$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekP,$currentP,$decimalsP)?></span><br>
						<?php
							if($weekP<$currentP){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekP==$currentP){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekP>$currentP){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekW,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekW,$currentW,1)?></span><br>
						<?php
							if($weekW<$currentW){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekW==$currentW){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekW>$currentW){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekG,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekG,$currentG,1)?></span><br>
						<?php
							if($weekG<$currentG){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekG==$currentG){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekG>$currentG){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekA,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekA,$currentA,1)?></span><br>
						<?php
							if($weekA<$currentA){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekA==$currentA){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekA>$currentA){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($weekD,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekD,$currentD,1)?></span><br>
						<?php
							if($weekD<$currentD){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($weekD==$currentD){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($weekD>$currentD){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($weekS,0,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($weekS,$currentS,0)?></span><br>
							<?php
								if($weekS<$currentS){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($weekS==$currentS){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($weekS>$currentS){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th class="interval">
						<?php echo lang("month ago",'c')?>
					</th>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthT,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthT,$currentT,1)?></span><br>
						<?php
							if($monthT<$currentT){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthT==$currentT){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthT>$currentT){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthH,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthH,$currentH,1)?></span><br>
						<?php
							if($monthH<$currentH){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthH==$currentH){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthH>$currentH){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthP,$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthP,$currentP,$decimalsP)?></span><br>
						<?php
							if($monthP<$currentP){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthP==$currentP){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthP>$currentP){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthW,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthW,$currentW,1)?></span><br>
						<?php
							if($monthW<$currentW){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthW==$currentW){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthW>$currentW){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthG,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthG,$currentG,1)?></span><br>
						<?php
							if($monthG<$currentG){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthG==$currentG){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthG>$currentG){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthA,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthA,$currentA,1)?></span><br>
						<?php
							if($monthA<$currentA){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthA==$currentA){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthA>$currentA){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($monthD,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthD,$currentD,1)?></span><br>
						<?php
							if($monthD<$currentD){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($monthD==$currentD){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($monthD>$currentD){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($monthS,0,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($monthS,$currentS,0)?></span><br>
							<?php
								if($monthS<$currentS){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($monthS==$currentS){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($monthS>$currentS){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th class="interval">
						<?php echo lang("year ago",'c')?>
					</th>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearT,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearT,$currentT,1)?></span><br>
						<?php
							if($yearT<$currentT){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearT==$currentT){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearT>$currentT){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearH,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearH,$currentH,1)?></span><br>
						<?php
							if($yearH<$currentH){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearH==$currentH){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearH>$currentH){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearP,$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearP,$currentP,$decimalsP)?></span><br>
						<?php
							if($yearP<$currentP){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearP==$currentP){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearP>$currentP){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearW,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearW,$currentW,1)?></span><br>
						<?php
							if($yearW<$currentW){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearW==$currentW){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearW>$currentW){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearG,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearG,$currentG,1)?></span><br>
						<?php
							if($yearG<$currentG){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearG==$currentG){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearG>$currentG){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearA,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearA,$currentA,1)?></span><br>
						<?php
							if($yearA<$currentA){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearA==$currentA){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearA>$currentA){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<td class="value">
						<span class="valueSpan"><?php echo number_format($yearD,1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearD,$currentD,1)?></span><br>
						<?php
							if($yearD<$currentD){
								echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
							}
							if($yearD==$currentD){
								echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
							}
							if($yearD>$currentD){
								echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
							}
						?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearS,0,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearS,$currentS,0)?></span><br>
							<?php
								if($yearS<$currentS){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearS==$currentS){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearS>$currentS){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
					<?php
						}
					?>
				</tr>
				<?php 
					for($i=0;$i<count($yearsData);$i++){
				?>			
					<tr>
						<th  class="interval">
							<?php echo ($i+2)?> <?php echo lang("years ago",'c')?>
						</th>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['T'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['T'],$currentT,1)?></span><br>
							<?php
								if($yearsData[$i]['T']<$currentT){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['T']==$currentT){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['T']>$currentT){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['H'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['H'],$currentH,1)?></span><br>
							<?php
								if($yearsData[$i]['H']<$currentH){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['H']==$currentH){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['H']>$currentH){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['P'],$decimalsP,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['P'],$currentP,$decimalsP)?></span><br>
							<?php
								if($yearsData[$i]['P']<$currentP){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['P']==$currentP){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['P']>$currentP){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['W'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['W'],$currentW,1)?></span><br>
							<?php
								if($yearsData[$i]['W']<$currentW){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['W']==$currentW){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['W']>$currentW){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['G'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['G'],$currentG,1)?></span><br>
							<?php
								if($yearsData[$i]['G']<$currentG){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['G']==$currentG){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['G']>$currentG){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['A'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['A'],$currentA,1)?></span><br>
							<?php
								if($yearsData[$i]['A']<$currentA){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['A']==$currentA){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['A']>$currentA){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<td class="value">
							<span class="valueSpan"><?php echo number_format($yearsData[$i]['D'],1,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['D'],$currentD,1)?></span><br>
							<?php
								if($yearsData[$i]['D']<$currentD){
									echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['D']==$currentD){
									echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
								}
								if($yearsData[$i]['D']>$currentD){
									echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
								}
							?>
						</td>
						<?php 
							if($solarSensor){
						?>
							<td class="value">
								<span class="valueSpan"><?php echo number_format($yearsData[$i]['S'],0,".","")?></span><br><span class="comparisonSpan"><?php echo diffCalc($yearsData[$i]['S'],$currentS,0)?></span><br>
								<?php
									if($yearsData[$i]['S']<$currentS){
										echo '<span class="mticon-trendup" style="font-size:1.2em"></span>';
									}
									if($yearsData[$i]['S']>$currentS){
										echo '<span class="mticon-trenddown" style="font-size:1.2em"></span>';
									}
									if($yearsData[$i]['S']==$currentS){
										echo '<span class="mticon-trendneutral" style="font-size:1.2em"></span>';
									}
								?>
							</td>
						<?php
							}
						?>
					</tr>
				<?php			
				}
				?>
			</tbody>
		</table>
		<br>
		<?php echo lang('data available since','c')?>: <?php echo date($dateFormat." ".$timeFormat, $firstDate)?>
		<br><br>
	</div></div>
	<?php include($baseURL."footer.php");?>
	</body>
</html>
