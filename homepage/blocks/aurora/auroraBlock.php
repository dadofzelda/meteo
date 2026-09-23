<?php

	# 		Norrsken
	# 		Namespace:		aurora
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 23, 2026
	#			- initial release
	#			- kompakt Kp-badge + liten karta (NOAA OVATION-heatmap),
	#			  länkar till den fullständiga norrskenssidan
	#			  (pages/astronomy/aurora.php) för mer detalj.

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
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" integrity="sha512-h9FcoyWjHcOcmEVkxOfTLnmZFWIH0iZhZT1H2TbOq55xssQGEJHEaIm+PgoUaZbRvQTNTluNOEfb1ZRy6D3BOw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" integrity="sha512-puJW3E/qXDqYp9IfhAI54BJEaWIfloJ7JWs7OeD5i6ruC9JZL1gERT1wjtwXFlh7CjE7ZJ+/vcRZRkIYIb6p4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js" integrity="sha512-KhIBJeCI4oTEeqOmRi2gDJ7m+JARImhUYgXWiOTIp9qqySpFUAJs09erGKem4E5IPuxxSTjavuurvBitBmwE0w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
		#<?php echo $blockUID?> .auroraMiniMap{
			width: 100%;
			height: 180px;
			border-radius: 6px;
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
		<div class="auroraMiniMap" id="<?php echo $blockUID?>_map"></div>
		<a class="auroraLink" href="pages/astronomy/aurora.php"><?php echo lang('see full aurora forecast','c')?></a>
	</div>
	<script>
		(function(){
			var map = L.map('<?php echo $blockUID?>_map', {
				zoomControl: false,
				attributionControl: false,
				dragging: false,
				scrollWheelZoom: false
			}).setView([<?php echo $stationLat?>, <?php echo $stationLon?>], 3);
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19
			}).addTo(map);
			L.marker([<?php echo $stationLat?>, <?php echo $stationLon?>]).addTo(map);

			$.getJSON("pages/astronomy/auroraOvationAjax.php", function(data){
				if(data.points && data.points.length>0){
					L.heatLayer(data.points, {radius: 14, blur: 18, max: 100}).addTo(map);
				}
			});
		})();
	</script>
