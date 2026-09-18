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
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	include("../../../scripts/stats.php");
	
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
		$currentR = convertR($row['R']);
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
		$hourR = convertR($row['R']);
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
		$yesterdayR = convertR($row['R']);
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
		$weekR = convertR($row['R']);
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
		$monthR = convertR($row['R']);
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
		$yearR = convertR($row['R']);
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
			$oneYear['R'] = convertR($row['R']);
			$oneYear['S'] = $row['S'];
			array_push($yearsData,$oneYear);
		}
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("history",'c')?></title>
		<?php metaHeader()?>
		<style>
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['500']?>;
				color: #<?php echo $color_schemes[$design2]['font500']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['600']?>;
				color: #<?php echo $color_schemes[$design2]['font600']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			.table tfoot tr{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.headerIcon{
				max-width: 30px;
				padding-top: 5px;
				padding-bottom: 5px;
				opacity: 0.8;
			}
			.trend{
				text-align: center;
			}
			.value{
				text-align: left;
				opacity: 0.8;
			}
			.trendIcon{
				max-width: 25px;
				opacity: 0.8;
			}
			.current{
				font-weight: bold;
				text-align: center!important;
				opacity: 1!important;
			}
			.interval{
				opacity:0.8;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
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
					<th>
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/gust.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/rain.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="headerIcon" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="headerIcon" alt=''>
					</th>
					<?php 
						if($solarSensor){
					?>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/sun.png" class="headerIcon" alt=''>
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
					<td class="value current">
						<?php echo number_format($currentT,1)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentH,0)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentP,2,".","")?>
					</td>
					<td class="value current">
						<?php echo number_format($currentW,1)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentG,1)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentR,2)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentA,1)?>
					</td>
					<td class="value current">
						<?php echo number_format($currentD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="value current">
							<?php echo number_format($currentS,0)?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("hour ago",'c')?>
					</th>
					<td class="trend">
						<?php
							if($hourT<$currentT){
								$trend = "Up";
							}
							if($hourT==$currentT){
								$trend = "Neutral";
							}
							if($hourT>$currentT){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourT,1)?>
					</td>
					<td class="trend">
						<?php
							if($hourH<$currentH){
								$trend = "Up";
							}
							if($hourH==$currentH){
								$trend = "Neutral";
							}
							if($hourH>$currentH){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourH,0)?>
					</td>
					<td class="trend">
						<?php
							if($hourP<$currentP){
								$trend = "Up";
							}
							if($hourP==$currentP){
								$trend = "Neutral";
							}
							if($hourP>$currentP){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourP,2,".","")?>
					</td>
					<td class="trend">
						<?php
							if($hourW<$currentW){
								$trend = "Up";
							}
							if($hourW==$currentW){
								$trend = "Neutral";
							}
							if($hourW>$currentW){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourW,1)?>
					</td>
					<td class="trend">
						<?php
							if($hourG<$currentG){
								$trend = "Up";
							}
							if($hourG==$currentG){
								$trend = "Neutral";
							}
							if($hourG>$currentG){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourG,1)?>
					</td>
					<td class="trend">
						<?php
							if($hourR<$currentR){
								$trend = "Up";
							}
							if($hourR==$currentR){
								$trend = "Neutral";
							}
							if($hourR>$currentR){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourR,2)?>
					</td>
					<td class="trend">
						<?php
							if($hourA<$currentA){
								$trend = "Up";
							}
							if($hourA==$currentA){
								$trend = "Neutral";
							}
							if($hourA>$currentA){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourA,1)?>
					</td>
					<td class="trend">
						<?php
							if($hourD<$currentD){
								$trend = "Up";
							}
							if($hourD==$currentD){
								$trend = "Neutral";
							}
							if($hourD>$currentD){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($hourD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="trend">
							<?php
								if($hourS<$currentS){
									$trend = "Up";
								}
								if($hourS==$currentS){
									$trend = "Neutral";
								}
								if($hourS>$currentS){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($hourS,0)?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("yesterday",'c')?>
					</th>
					<td class="trend">
						<?php
							if($yesterdayT<$currentT){
								$trend = "Up";
							}
							if($yesterdayT==$currentT){
								$trend = "Neutral";
							}
							if($yesterdayT>$currentT){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($yesterdayT,1)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayH<$currentH){
								$trend = "Up";
							}
							if($yesterdayH==$currentH){
								$trend = "Neutral";
							}
							if($yesterdayH>$currentH){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($yesterdayH,0)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayP<$currentP){
								$trend = "Up";
							}
							if($yesterdayP==$currentP){
								$trend = "Neutral";
							}
							if($yesterdayP>$currentP){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($yesterdayP,2,".","")?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayW<$currentW){
								$trend = "Up";
							}
							if($yesterdayW==$currentW){
								$trend = "Neutral";
							}
							if($yesterdayW>$currentW){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($yesterdayW,1)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayG<$currentG){
								$trend = "Up";
							}
							if($yesterdayG==$currentG){
								$trend = "Neutral";
							}
							if($yesterdayG>$currentG){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yesterdayG,1)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayR<$currentR){
								$trend = "Up";
							}
							if($yesterdayR==$currentR){
								$trend = "Neutral";
							}
							if($yesterdayR>$currentR){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($yesterdayR,2)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayA<$currentA){
								$trend = "Up";
							}
							if($yesterdayA==$currentA){
								$trend = "Neutral";
							}
							if($yesterdayA>$currentA){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yesterdayA,1)?>
					</td>
					<td class="trend">
						<?php
							if($yesterdayD<$currentD){
								$trend = "Up";
							}
							if($yesterdayD==$currentD){
								$trend = "Neutral";
							}
							if($yesterdayD>$currentD){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yesterdayD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="trend">
							<?php
								if($yesterdayS<$currentS){
									$trend = "Up";
								}
								if($yesterdayS==$currentS){
									$trend = "Neutral";
								}
								if($yesterdayS>$currentS){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($yesterdayS,0)?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th  class="interval">
						<?php echo lang("week ago",'c')?>
					</th>
					<td class="trend">
						<?php
							if($weekT<$currentT){
								$trend = "Up";
							}
							if($weekT==$currentT){
								$trend = "Neutral";
							}
							if($weekT>$currentT){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						<?php echo number_format($weekT,1)?>
					</td>
					<td class="trend">
						<?php
							if($weekH<$currentH){
								$trend = "Up";
							}
							if($weekH==$currentH){
								$trend = "Neutral";
							}
							if($weekH>$currentH){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekH,0)?>
					</td>
					<td class="trend">
						<?php
							if($weekP<$currentP){
								$trend = "Up";
							}
							if($weekP==$currentP){
								$trend = "Neutral";
							}
							if($weekP>$currentP){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekP,2,".","")?>
					</td>
					<td class="trend">
						<?php
							if($weekW<$currentW){
								$trend = "Up";
							}
							if($weekW==$currentW){
								$trend = "Neutral";
							}
							if($weekW>$currentW){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekW,1)?>
					</td>
					<td class="trend">
						<?php
							if($weekG<$currentG){
								$trend = "Up";
							}
							if($weekG==$currentG){
								$trend = "Neutral";
							}
							if($weekG>$currentG){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekG,1)?>
					</td>
					<td class="trend">
						<?php
							if($weekR<$currentR){
								$trend = "Up";
							}
							if($weekR==$currentR){
								$trend = "Neutral";
							}
							if($weekR>$currentR){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekR,2)?>
					</td>
					<td class="trend">
						<?php
							if($weekA<$currentA){
								$trend = "Up";
							}
							if($weekA==$currentA){
								$trend = "Neutral";
							}
							if($weekA>$currentA){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekA,1)?>
					</td>
					<td class="trend">
						<?php
							if($weekD<$currentD){
								$trend = "Up";
							}
							if($weekD==$currentD){
								$trend = "Neutral";
							}
							if($weekD>$currentD){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($weekD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="trend">
							<?php
								if($weekS<$currentS){
									$trend = "Up";
								}
								if($weekS==$currentS){
									$trend = "Neutral";
								}
								if($weekS>$currentS){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($weekS,0)?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th class="interval">
						<?php echo lang("month ago",'c')?>
					</th>
					<td class="trend">
						<?php
							if($monthT<$currentT){
								$trend = "Up";
							}
							if($monthT==$currentT){
								$trend = "Neutral";
							}
							if($monthT>$currentT){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthT,1)?>
					</td>
					<td class="trend">
						<?php
							if($monthH<$currentH){
								$trend = "Up";
							}
							if($monthH==$currentH){
								$trend = "Neutral";
							}
							if($monthH>$currentH){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthH,0)?>
					</td>
					<td class="trend">
						<?php
							if($monthP<$currentP){
								$trend = "Up";
							}
							if($monthP==$currentP){
								$trend = "Neutral";
							}
							if($monthP>$currentP){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthP,2,".","")?>
					</td>
					<td class="trend">
						<?php
							if($monthW<$currentW){
								$trend = "Up";
							}
							if($monthW==$currentW){
								$trend = "Neutral";
							}
							if($monthW>$currentW){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthW,1)?>
					</td>
					<td class="trend">
						<?php
							if($monthG<$currentG){
								$trend = "Up";
							}
							if($monthG==$currentG){
								$trend = "Neutral";
							}
							if($monthG>$currentG){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthG,1)?>
					</td>
					<td class="trend">
						<?php
							if($monthR<$currentR){
								$trend = "Up";
							}
							if($monthR==$currentR){
								$trend = "Neutral";
							}
							if($monthR>$currentR){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthR,2)?>
					</td>
					<td class="trend">
						<?php
							if($monthA<$currentA){
								$trend = "Up";
							}
							if($monthA==$currentA){
								$trend = "Neutral";
							}
							if($monthA>$currentA){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthA,1)?>
					</td>
					<td class="trend">
						<?php
							if($monthD<$currentD){
								$trend = "Up";
							}
							if($monthD==$currentD){
								$trend = "Neutral";
							}
							if($monthD>$currentD){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($monthD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="trend">
							<?php
								if($monthS<$currentS){
									$trend = "Up";
								}
								if($monthS==$currentS){
									$trend = "Neutral";
								}
								if($monthS>$currentS){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($monthS,0)?>
						</td>
					<?php
						}
					?>
				</tr>
				<tr>
					<th class="interval">
						<?php echo lang("year ago",'c')?>
					</th>
					<td class="trend">
						<?php
							if($yearT<$currentT){
								$trend = "Up";
							}
							if($yearT==$currentT){
								$trend = "Neutral";
							}
							if($yearT>$currentT){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearT,1)?>
					</td>
					<td class="trend">
						<?php
							if($yearH<$currentH){
								$trend = "Up";
							}
							if($yearH==$currentH){
								$trend = "Neutral";
							}
							if($yearH>$currentH){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearH,0)?>
					</td>
					<td class="trend">
						<?php
							if($yearP<$currentP){
								$trend = "Up";
							}
							if($yearP==$currentP){
								$trend = "Neutral";
							}
							if($yearP>$currentP){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearP,2,".","")?>
					</td>
					<td class="trend">
						<?php
							if($yearW<$currentW){
								$trend = "Up";
							}
							if($yearW==$currentW){
								$trend = "Neutral";
							}
							if($yearW>$currentW){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearW,1)?>
					</td>
					<td class="trend">
						<?php
							if($yearG<$currentG){
								$trend = "Up";
							}
							if($yearG==$currentG){
								$trend = "Neutral";
							}
							if($yearG>$currentG){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearG,1)?>
					</td>
					<td class="trend">
						<?php
							if($yearR<$currentR){
								$trend = "Up";
							}
							if($yearR==$currentR){
								$trend = "Neutral";
							}
							if($yearR>$currentR){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearR,2)?>
					</td>
					<td class="trend">
						<?php
							if($yearA<$currentA){
								$trend = "Up";
							}
							if($yearA==$currentA){
								$trend = "Neutral";
							}
							if($yearA>$currentA){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearA,1)?>
					</td>
					<td class="trend">
						<?php
							if($yearD<$currentD){
								$trend = "Up";
							}
							if($yearD==$currentD){
								$trend = "Neutral";
							}
							if($yearD>$currentD){
								$trend = "Down";
							}
						?>
						<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
						<?php echo number_format($yearD,1)?>
					</td>
					<?php 
						if($solarSensor){
					?>
						<td class="trend">
							<?php
								if($yearS<$currentS){
									$trend = "Up";
								}
								if($yearS==$currentS){
									$trend = "Neutral";
								}
								if($yearS>$currentS){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($yearS,0)?>
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
						<td class="trend">
							<?php
								if($yearsData[$i]['T']<$currentT){
									$trend = "Up";
								}
								if($yearsData[$i]['T']==$currentT){
									$trend = "Neutral";
								}
								if($yearsData[$i]['T']>$currentT){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							<?php echo number_format($yearsData[$i]['T'],1)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['H']<$currentH){
									$trend = "Up";
								}
								if($yearsData[$i]['H']==$currentH){
									$trend = "Neutral";
								}
								if($yearsData[$i]['H']>$currentH){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['H'],0)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['P']<$currentP){
									$trend = "Up";
								}
								if($yearsData[$i]['P']==$currentP){
									$trend = "Neutral";
								}
								if($yearsData[$i]['P']>$currentP){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['P'],2,".","")?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['W']<$currentW){
									$trend = "Up";
								}
								if($yearsData[$i]['W']==$currentW){
									$trend = "Neutral";
								}
								if($yearsData[$i]['W']>$currentW){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['W'],1)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['G']<$currentG){
									$trend = "Up";
								}
								if($yearsData[$i]['G']==$currentG){
									$trend = "Neutral";
								}
								if($yearsData[$i]['G']>$currentG){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['G'],1)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['R']<$currentR){
									$trend = "Up";
								}
								if($yearsData[$i]['R']==$currentR){
									$trend = "Neutral";
								}
								if($yearsData[$i]['R']>$currentR){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['R'],2)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['A']<$currentA){
									$trend = "Up";
								}
								if($yearsData[$i]['A']==$currentA){
									$trend = "Neutral";
								}
								if($yearsData[$i]['A']>$currentA){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
						
							<?php echo number_format($yearsData[$i]['A'],1)?>
						</td>
						<td class="trend">
							<?php
								if($yearsData[$i]['D']<$currentD){
									$trend = "Up";
								}
								if($yearsData[$i]['D']==$currentD){
									$trend = "Neutral";
								}
								if($yearsData[$i]['D']>$currentD){
									$trend = "Down";
								}
							?>
							<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
					
							<?php echo number_format($yearsData[$i]['D'],1)?>
						</td>
						<?php 
							if($solarSensor){
						?>
							<td class="trend">
								<?php
									if($yearsData[$i]['S']<$currentS){
										$trend = "Up";
									}
									if($yearsData[$i]['S']==$currentS){
										$trend = "Neutral";
									}
									if($yearsData[$i]['S']>$currentS){
										$trend = "Down";
									}
								?>
								<img src="<?php echo $pageURL.$path?>icons/trend<?php echo $trend?>.png" class="trendIcon" alt=''><br>
							
								<?php echo number_format($yearsData[$i]['S'],0)?>
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
		Data available since: <?php echo date($dateFormat." ".$timeFormat, $firstDate)?>
	</div></div>
	<?php include("../../footer.php")?>
	</body>
</html>
