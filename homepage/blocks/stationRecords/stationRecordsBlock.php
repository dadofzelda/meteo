<?php

	# 		Stationsrekord
	# 		Namespace:		stationRecords
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 23, 2026
	# 			- initial release
	#			- högsta/lägsta temperatur, kraftigaste vindby och våtaste
	#			  dygn ur stationens hela mätserie (inte en tidsbegränsad
	#			  period). Inspirerad av borgabo.se/borgafjalls-vadersida.

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$language = loadLangs();

	$blockUID = "stationRecords_".substr(md5(uniqid()),0,8);

	$records = array();
	// DateTime > 2000-01-01 filtrerar bort gamla nolldatum-artefakter
	// (0000-00-00 00:00:00) som annars gör att "sedan"-datumet blir 1970
	$result = mysqli_query($con,"
		SELECT max(Tmax) AS maxT, min(Tmin) AS minT, max(G) AS maxG, min(DateTime) AS sinceDate
		FROM alldata
		WHERE DateTime > '2000-01-01 00:00:00'
	");
	if($result){
		while($row = mysqli_fetch_array($result)){
			$records['maxT'] = $row['maxT'];
			$records['minT'] = $row['minT'];
			$records['maxG'] = $row['maxG'];
			$records['sinceDate'] = $row['sinceDate'];
		}
	}

	// våtaste dygnet - samma mönster som climate.php redan använder för
	// dygnsnederbörd (MAX(R) per dygn, eftersom R är en stigande räknare
	// som nollställs varje dygn hos den här stationstypen)
	$maxDailyRain = null;
	$rainResult = mysqli_query($con,"
		SELECT max(DailyRain) AS maxDailyRain FROM (
			SELECT max(R) AS DailyRain
			FROM alldata
			WHERE DateTime > '2000-01-01 00:00:00'
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
		) AS daily
	");
	if($rainResult){
		while($row = mysqli_fetch_array($rainResult)){
			$maxDailyRain = $row['maxDailyRain'];
		}
	}

?>
	<style>
		#<?php echo $blockUID?> .recordsTiles{
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 10px;
		}
		#<?php echo $blockUID?> .recordsTile{
			min-width: 110px;
			padding: 8px 12px;
			text-align: center;
			border-radius: 6px;
			background: rgba(128,128,128,0.12);
		}
		#<?php echo $blockUID?> .recordsTile .recordsTileIcon{
			font-size: 1.4em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .recordsTile .recordsTileValue{
			font-weight: bold;
			font-size: 1.1em;
		}
		#<?php echo $blockUID?> .recordsTile .recordsTileLabel{
			font-size: 0.8em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .recordsSince{
			text-align: center;
			opacity: 0.7;
			font-size: 0.85em;
			margin-top: 10px;
		}
	</style>
	<div id="<?php echo $blockUID?>">
		<div class="recordsTiles">
			<?php if(isset($records['maxT'])){?>
				<div class="recordsTile">
					<div class="recordsTileIcon"><span class="mticon-temp"></span></div>
					<div class="recordsTileValue"><?php echo number_format(convertT($records["maxT"]),1,".","")?><?php echo unitFormatter($displayTempUnits)?></div>
					<div class="recordsTileLabel"><?php echo lang('highest','c')?></div>
				</div>
			<?php }?>
			<?php if(isset($records['minT'])){?>
				<div class="recordsTile">
					<div class="recordsTileIcon"><span class="mticon-temp"></span></div>
					<div class="recordsTileValue"><?php echo number_format(convertT($records["minT"]),1,".","")?><?php echo unitFormatter($displayTempUnits)?></div>
					<div class="recordsTileLabel"><?php echo lang('lowest','c')?></div>
				</div>
			<?php }?>
			<?php if(isset($records['maxG'])){?>
				<div class="recordsTile">
					<div class="recordsTileIcon"><span class="mticon-gust"></span></div>
					<div class="recordsTileValue"><?php echo number_format(convertW($records['maxG']),1,".","")?> <?php echo unitFormatter($displayWindUnits)?></div>
					<div class="recordsTileLabel"><?php echo lang('wind gust','c')?></div>
				</div>
			<?php }?>
			<?php if($maxDailyRain!==null){?>
				<div class="recordsTile">
					<div class="recordsTileIcon"><span class="mticon-rain"></span></div>
					<div class="recordsTileValue"><?php echo number_format(convertR($maxDailyRain),1,".","")?> <?php echo unitFormatter($displayRainUnits)?></div>
					<div class="recordsTileLabel"><?php echo lang('wettest day','c')?></div>
				</div>
			<?php }?>
		</div>
		<?php if(isset($records['sinceDate'])){?>
			<div class="recordsSince"><?php echo lang('records since','c')?> <?php echo date("Y-m-d",strtotime($records['sinceDate']))?></div>
		<?php }?>
	</div>
