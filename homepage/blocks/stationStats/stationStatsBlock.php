<?php

	# 		Stationen har mätt
	# 		Namespace:		stationStats
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 23, 2026
	# 			- initial release
	#			- totalt antal mätvärden, antal dygn med mätningar och hur
	#			  länge sedan senaste avläsningen. Inspirerad av
	#			  borgabo.se/borgafjalls-vadersida ("Stationen har mätt").

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$language = loadLangs();

	$blockUID = "stationStats_".substr(md5(uniqid()),0,8);

	$stats = array();
	// DateTime > 2000-01-01 filtrerar bort gamla nolldatum-artefakter
	// (0000-00-00 00:00:00) som annars gör att "sedan"-datumet blir 1970
	$result = mysqli_query($con,"
		SELECT count(*) AS totalReadings,
		       min(DateTime) AS firstDate,
		       max(DateTime) AS lastDate,
		       count(DISTINCT DATE(DateTime)) AS daysMeasured
		FROM alldata
		WHERE DateTime > '2000-01-01 00:00:00'
	");
	if($result){
		while($row = mysqli_fetch_array($result)){
			$stats['totalReadings'] = $row['totalReadings'];
			$stats['firstDate'] = $row['firstDate'];
			$stats['lastDate'] = $row['lastDate'];
			$stats['daysMeasured'] = $row['daysMeasured'];
		}
	}

	function secondsToAgoText($seconds){
		if($seconds < 60){
			return $seconds." s";
		}
		if($seconds < 3600){
			return round($seconds/60)." min";
		}
		if($seconds < 86400){
			return round($seconds/3600,1)." h";
		}
		return round($seconds/86400)." "."d";
	}

?>
	<style>
		#<?php echo $blockUID?> .statsTiles{
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 10px;
		}
		#<?php echo $blockUID?> .statsTile{
			min-width: 110px;
			padding: 8px 12px;
			text-align: center;
			border-radius: 6px;
			background: rgba(128,128,128,0.12);
		}
		#<?php echo $blockUID?> .statsTile .statsTileIcon{
			font-size: 1.4em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .statsTile .statsTileValue{
			font-weight: bold;
			font-size: 1.1em;
		}
		#<?php echo $blockUID?> .statsTile .statsTileLabel{
			font-size: 0.8em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .statsSince{
			text-align: center;
			opacity: 0.7;
			font-size: 0.85em;
			margin-top: 10px;
		}
	</style>
	<div id="<?php echo $blockUID?>">
		<div class="statsTiles">
			<?php if(isset($stats['totalReadings'])){?>
				<div class="statsTile">
					<div class="statsTileIcon"><span class="fa fa-database"></span></div>
					<div class="statsTileValue"><?php echo number_format($stats['totalReadings'],0,"."," ")?></div>
					<div class="statsTileLabel"><?php echo lang('measurements','c')?></div>
				</div>
			<?php }?>
			<?php if(isset($stats['daysMeasured'])){?>
				<div class="statsTile">
					<div class="statsTileIcon"><span class="fa fa-calendar"></span></div>
					<div class="statsTileValue"><?php echo number_format($stats['daysMeasured'],0,"."," ")?></div>
					<div class="statsTileLabel"><?php echo lang('days','c')?></div>
				</div>
			<?php }?>
			<?php if(isset($stats['lastDate'])){?>
				<div class="statsTile">
					<div class="statsTileIcon"><span class="fa fa-clock-o"></span></div>
					<div class="statsTileValue"><?php echo secondsToAgoText(time()-strtotime($stats['lastDate']))?></div>
					<div class="statsTileLabel"><?php echo lang('ago','c')?></div>
				</div>
			<?php }?>
		</div>
		<?php if(isset($stats['firstDate'])){?>
			<div class="statsSince"><?php echo lang('since','c')?> <?php echo date("Y-m-d",strtotime($stats['firstDate']))?></div>
		<?php }?>
	</div>
