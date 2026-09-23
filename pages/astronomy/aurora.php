<?php

	############################################################################
	#
	#	Norrskensprognos
	#	Namespace:		aurora
	#	Meteotemplate-sida (custom, not from meteotemplate.com)
	#
	#	v1.0 - Sep 23, 2026
	#		- Kp-index-baserad chansnivå + Leaflet/OVATION-heatmap
	#	v2.0 - Sep 23, 2026
	#		- ersatte den egna Leaflet/OVATION-heatmapen med NOAA:s egen
	#		  färdiga prognosbild (bäddas in direkt). Egen karta sa inte
	#		  så mycket vid låg aktivitet, NOAA:s bild är tydligare och
	#		  uppdateras av dem själva - ingen egen kartlogik kvar.
	#
	############################################################################

	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	$aurora = getAuroraForecast();
	$auroraTier = null;
	if(isset($aurora['kp'])){
		$auroraTier = getAuroraTier($aurora['kp']);
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('aurora forecast','c')?></title>
		<?php metaHeader()?>

		<style>
			.auroraBadge {
				display: inline-block;
				margin: 10px auto 20px auto;
				padding: 10px 20px;
				border-radius: 6px;
				text-align: center;
				<?php if($auroraTier!==null){?>
					background: #<?php echo $color_schemes['green'][$auroraTier['shade']]?>;
					color: #<?php echo $color_schemes['green']['font'.$auroraTier['shade']]?>;
				<?php }?>
			}
			.auroraBadge .auroraBadgeValue {
				font-weight: bold;
				font-size: 1.3em;
			}
			.auroraBadge .auroraBadgeKp {
				font-size: 0.85em;
				opacity: 0.85;
			}
			.auroraImageWrap {
				text-align: center;
			}
			.auroraImageWrap img {
				max-width: 800px;
				width: 100%;
				border-radius: 6px;
			}
			.auroraInfo {
				max-width: 800px;
				margin: 20px auto;
				text-align: justify;
				font-size: 0.9em;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div style="text-align:center">
				<?php if($auroraTier!==null){?>
					<div class="auroraBadge">
						<div class="auroraBadgeValue"><?php echo lang($auroraTier['label'],'c')?></div>
						<div class="auroraBadgeKp">Kp <?php echo number_format($aurora['kp'],1,".","")?></div>
					</div>
				<?php }?>
			</div>
			<div class="auroraImageWrap">
				<img src="https://services.swpc.noaa.gov/images/aurora-forecast-northern-hemisphere.jpg" alt="<?php echo lang('aurora forecast','c')?>">
			</div>
			<div class="auroraInfo">
				<p><?php echo lang('the kp-index measures geomagnetic activity on a scale from 0 to 9. higher values increase the chance of seeing the aurora further south. cloud cover and darkness still matter a lot - this is only a rough indicator.','c')?></p>
				<p><?php echo lang('data from noaa\'s ovation model, showing the probability of visible aurora across the sky, not just at the horizon. actual visibility still depends on cloud cover and darkness.','c')?></p>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
