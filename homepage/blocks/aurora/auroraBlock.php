<?php

	# 		Norrsken
	# 		Namespace:		aurora
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 23, 2026
	#			- initial release
	#			- kompakt Kp-badge + egen Leaflet-heatmap (NOAA OVATION)
	#		v2.0 - Sep 23, 2026
	#			- ersatte den egna heatmapen med NOAA:s egen färdiga
	#			  prognosbild (bäddas in direkt, uppdateras av NOAA själva,
	#			  ingen egen kartlogik kvar). Användaren tyckte NOAA:s
	#			  bild var tydligare och att vår egen karta inte sa så
	#			  mycket vid låg aktivitet.

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$language = loadLangs();

	$blockUID = "aurora_".substr(md5(uniqid()),0,8);

	$aurora = getAuroraForecast();
	$auroraTier = null;
	if(isset($aurora['kp'])){
		$auroraTier = getAuroraTier($aurora['kp']);
	}

?>
	<style>
		#<?php echo $blockUID?> .auroraBadge{
			text-align: center;
			padding: 6px 0;
			border-radius: 6px;
			margin-bottom: 8px;
			<?php if($auroraTier!==null){?>
				background: #<?php echo $color_schemes['green'][$auroraTier['shade']]?>;
				color: #<?php echo $color_schemes['green']['font'.$auroraTier['shade']]?>;
			<?php }?>
		}
		#<?php echo $blockUID?> .auroraBadge .auroraBadgeValue{
			font-weight: bold;
		}
		#<?php echo $blockUID?> .auroraBadge .auroraBadgeKp{
			font-size: 0.8em;
			opacity: 0.85;
		}
		#<?php echo $blockUID?> .auroraImage{
			width: 100%;
			border-radius: 6px;
			display: block;
		}
		#<?php echo $blockUID?> .auroraLink{
			display: block;
			text-align: center;
			margin-top: 8px;
			font-size: 0.85em;
		}
	</style>
	<div id="<?php echo $blockUID?>">
		<?php if($auroraTier!==null){?>
			<div class="auroraBadge">
				<div class="auroraBadgeValue"><?php echo lang($auroraTier['label'],'c')?></div>
				<div class="auroraBadgeKp">Kp <?php echo number_format($aurora['kp'],1,".","")?></div>
			</div>
		<?php }?>
		<a href="pages/astronomy/aurora.php">
			<img class="auroraImage" src="https://services.swpc.noaa.gov/images/aurora-forecast-northern-hemisphere.jpg" alt="<?php echo lang('aurora forecast','c')?>">
		</a>
		<a class="auroraLink" href="pages/astronomy/aurora.php"><?php echo lang('see full aurora forecast','c')?></a>
	</div>
