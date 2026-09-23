<?php

	############################################################################
	#
	#	Norrskensprognos
	#	Namespace:		aurora
	#	Meteotemplate-sida (custom, not from meteotemplate.com)
	#
	#	v1.0 - Sep 23, 2026
	#		- Kp-index-baserad chansnivå (delad med homepage/blocks/sky
	#		  och homepage/blocks/aurora via getAuroraTier())
	#		- Leaflet-karta med NOAA OVATION-modellens
	#		  norrskenssannolikhet som heatmap (leaflet.heat-plugin)
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
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" integrity="sha512-h9FcoyWjHcOcmEVkxOfTLnmZFWIH0iZhZT1H2TbOq55xssQGEJHEaIm+PgoUaZbRvQTNTluNOEfb1ZRy6D3BOw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" integrity="sha512-puJW3E/qXDqYp9IfhAI54BJEaWIfloJ7JWs7OeD5i6ruC9JZL1gERT1wjtwXFlh7CjE7ZJ+/vcRZRkIYIb6p4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.heat/0.2.0/leaflet-heat.js" integrity="sha512-KhIBJeCI4oTEeqOmRi2gDJ7m+JARImhUYgXWiOTIp9qqySpFUAJs09erGKem4E5IPuxxSTjavuurvBitBmwE0w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

		<style>
			#map {
				width: 100%;
				height: 600px;
			}
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
			.auroraStationValue {
				text-align: center;
				margin-bottom: 20px;
			}
			.auroraInfo {
				max-width: 800px;
				margin: 20px auto;
				text-align: justify;
				font-size: 0.9em;
			}
			.auroraUpdated {
				text-align: center;
				opacity: 0.7;
				font-size: 0.85em;
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
			<div class="auroraStationValue" id="auroraStationValue"></div>
			<div id="map"></div>
			<div class="auroraUpdated" id="auroraUpdated"></div>
			<div class="auroraInfo">
				<p><?php echo lang('the kp-index measures geomagnetic activity on a scale from 0 to 9. higher values increase the chance of seeing the aurora further south. cloud cover and darkness still matter a lot - this is only a rough indicator.','c')?></p>
				<p><?php echo lang('data from noaa\'s ovation model, showing the probability of visible aurora across the sky, not just at the horizon. actual visibility still depends on cloud cover and darkness.','c')?></p>
			</div>
		</div>
		<script>
			var map = L.map('map').setView([<?php echo $stationLat?>, <?php echo $stationLon?>], 4);
			L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
				maxZoom: 19
			}).addTo(map);
			L.marker([<?php echo $stationLat?>, <?php echo $stationLon?>]).addTo(map);

			$.getJSON("auroraOvationAjax.php", function(data){
				if(data.points && data.points.length>0){
					L.heatLayer(data.points, {radius: 18, blur: 22, max: 100}).addTo(map);
				}
				if(data.stationValue!==null){
					$('#auroraStationValue').html(
						"<b>" + data.stationValue + "%</b> <?php echo lang('probability')?> - <?php echo lang('chance at your location')?> (<?php echo $stationLocation?>)"
					);
				}
				if(data.observationTime){
					var updatedText = "<?php echo lang('updated','c')?> (UTC): " + data.observationTime.replace("T"," ").replace("Z","");
					if(data.forecastTime){
						updatedText += " - <?php echo lang('valid until')?> " + data.forecastTime.replace("T"," ").replace("Z","") + " UTC";
					}
					$('#auroraUpdated').text(updatedText);
				}
			});
		</script>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
