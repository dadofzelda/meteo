<?php

	# 		Himlen
	# 		Namespace:		sky
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 23, 2026
	# 			- initial release
	#			- soluppgång/nedgång, dagslängd + jämförelse mot gårdagen,
	#			  månfas. Inspirerad av borgabo.se/borgafjalls-vadersida.
	#			  Använder date_sun_info() istället för deprecated
	#			  date_sunrise()/date_sunset().
	#		v1.1 - Sep 23, 2026
	#			- norrskenschans flyttad till ett eget block
	#			  (homepage/blocks/aurora/) - dubblerade annars samma
	#			  Kp-badge på förstasidan

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$language = loadLangs();

	$blockUID = "sky_".substr(md5(uniqid()),0,8);

	date_default_timezone_set($stationTZ);

	// Sol - date_sun_info() ersätter deprecated date_sunrise()/date_sunset()
	$sunToday = date_sun_info(time(),$stationLat,$stationLon);
	$sunYesterday = date_sun_info(strtotime('yesterday'),$stationLat,$stationLon);

	$sunrise = is_numeric($sunToday['sunrise']) ? $sunToday['sunrise'] : null;
	$sunset = is_numeric($sunToday['sunset']) ? $sunToday['sunset'] : null;

	$dayLengthMin = null;
	$dayLengthDeltaMin = null;
	if($sunrise!==null && $sunset!==null){
		$dayLengthMin = round(($sunset-$sunrise)/60);
		if(is_numeric($sunYesterday['sunrise']) && is_numeric($sunYesterday['sunset'])){
			$dayLengthYestMin = round(($sunYesterday['sunset']-$sunYesterday['sunrise'])/60);
			$dayLengthDeltaMin = $dayLengthMin - $dayLengthYestMin;
		}
	}

	// Måne - moonPhase-klassen (functions.php) räknar ut fas/belysning från datum+tid
	$mp = new moonPhase();
	$moonIconIndex = round(($mp->getPositionInCycle())/(1/118));
	$moonPercent = $mp->getPercentOfIllumination();
	$moonPhaseName = $mp->getPhaseName();
	$moonPhaseID = $mp->getPhaseID();
	if($moonPhaseID===MP_NEW_MOON_ID){
		$moonIconClass = "mticon-newmoon";
	}
	else if($moonPhaseID===MP_FULL_MOON_ID){
		$moonIconClass = "mticon-fullmoon";
	}
	else{
		$moonIconClass = "mticon-halfmoon";
	}

?>
	<style>
		#<?php echo $blockUID?> .skyTiles{
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 10px;
		}
		#<?php echo $blockUID?> .skyTile{
			min-width: 110px;
			padding: 8px 12px;
			text-align: center;
			border-radius: 6px;
			background: rgba(128,128,128,0.12);
		}
		#<?php echo $blockUID?> .skyTile .skyTileIcon{
			font-size: 1.4em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .skyTile .skyTileValue{
			font-weight: bold;
			font-size: 1.1em;
		}
		#<?php echo $blockUID?> .skyTile .skyTileDelta{
			font-size: 0.75em;
			opacity: 0.75;
		}
		#<?php echo $blockUID?> .skyTile .skyTileLabel{
			font-size: 0.8em;
			opacity: 0.8;
		}
	</style>
	<div id="<?php echo $blockUID?>">
		<div class="skyTiles">
			<?php if($sunrise!==null){?>
				<div class="skyTile">
					<div class="skyTileIcon"><span class="mticon-sunrise"></span></div>
					<div class="skyTileValue"><?php echo date("H:i",$sunrise)?></div>
					<div class="skyTileLabel"><?php echo lang('sunrise','c')?></div>
				</div>
			<?php }?>
			<?php if($sunset!==null){?>
				<div class="skyTile">
					<div class="skyTileIcon"><span class="mticon-sunset"></span></div>
					<div class="skyTileValue"><?php echo date("H:i",$sunset)?></div>
					<div class="skyTileLabel"><?php echo lang('sunset','c')?></div>
				</div>
			<?php }?>
			<?php if($dayLengthMin!==null){?>
				<div class="skyTile">
					<div class="skyTileIcon"><span class="mticon-daynight"></span></div>
					<div class="skyTileValue"><?php echo floor($dayLengthMin/60)?> <?php echo lang('hAbbr')?> <?php echo $dayLengthMin%60?> <?php echo lang('minAbbr','l')?></div>
					<?php if($dayLengthDeltaMin!==null){?>
						<?php if($dayLengthDeltaMin>0){?>
							<div class="skyTileDelta"><?php echo $dayLengthDeltaMin?> <?php echo lang('minAbbr','l')?> <?php echo lang('longer than yesterday')?></div>
						<?php }
						else if($dayLengthDeltaMin<0){?>
							<div class="skyTileDelta"><?php echo abs($dayLengthDeltaMin)?> <?php echo lang('minAbbr','l')?> <?php echo lang('shorter than yesterday')?></div>
						<?php }
						else{?>
							<div class="skyTileDelta"><?php echo lang('same length as yesterday')?></div>
						<?php }?>
					<?php }?>
					<div class="skyTileLabel"><?php echo lang('day length','c')?></div>
				</div>
			<?php }?>
			<div class="skyTile">
				<div class="skyTileIcon"><span class="<?php echo $moonIconClass?>"></span></div>
				<div class="skyTileValue"><?php echo $moonPercent?></div>
				<div class="skyTileDelta"><?php echo lang('illuminated')?></div>
				<div class="skyTileLabel"><?php echo lang($moonPhaseName,'c')?></div>
			</div>
		</div>
	</div>
