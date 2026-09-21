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
	#	Global climate map
	#
	# 	A script showing a world map with markers loaded from climate info 
	#	database and allowing user to show information about climatic conditions
	#	for any place.
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('climate','c')?></title>
		<?php metaHeader()?>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" integrity="sha512-h9FcoyWjHcOcmEVkxOfTLnmZFWIH0iZhZT1H2TbOq55xssQGEJHEaIm+PgoUaZbRvQTNTluNOEfb1ZRy6D3BOw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.min.css" integrity="sha512-ENrTWqddXrLJsQS2A86QmvA17PkJ0GVm1bqj5aTgpeMAfDKN2+SIOLpKG8R/6KkimnhTb+VW5qqUHB/r1zaRgg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.min.css" integrity="sha512-fYyZwU1wU0QWB4Yutd/Pvhy5J1oWAwFXun1pt+Bps04WSe4Aq6tyHlT4+MHSJhD8JlLfgLuC4CbCnX5KHSjyCg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" integrity="sha512-puJW3E/qXDqYp9IfhAI54BJEaWIfloJ7JWs7OeD5i6ruC9JZL1gERT1wjtwXFlh7CjE7ZJ+/vcRZRkIYIb6p4g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.min.js" integrity="sha512-TiMWaqipFi2Vqt4ugRzsF8oRoGFlFFuqIi30FFxEPNw58Ov9mOy6LgC05ysfkxwLE0xVeZtmr92wVg9siAFRWA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="climate_json.php"></script>
		<script src="../../../scripts/datatable.js"></script>

		<style>
			#map {
				width: 100%;
				height: 500px;
			}
			.item {
				margin-left: 20px;
			}
			.info {
				color: black;
				text-align: center;
			}
			.climate-marker {
				width: 20px;
				height: 20px;
				border-radius: 50%;
				background: #000000;
				opacity: 0.8;
				border: 4px solid #ffffff;
				box-sizing: border-box;
			}
			.leaflet-popup-content {
				min-width: 380px;
			}
			.boxtitle {
				font-size:16pt; 
				color:#B30000; 
				text-align: center;
				font-family:"<?php echo $designFont?>","Arial Narrow",Arial,Helvetica,sans-serif;
				font-weight:bold;
				text-shadow: white 0.02em 0.02em 0.02em;
			}

			#overlay {
				position : fixed;
				width : 100%;
				top:0px;
				left:0px;
				height : 100%;
				background-color : #000000;
				z-index: 9998;
				opacity: 0.95;
				text-align:center;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div id="overlay">
				<div style="margin-left:auto;margin-right:auto;margin-top:300px">
					<span style="font-size: 40px;font-variant:small-caps;font-weight:bold;">LOADING...</span>
				</div>
			</div>
			<div id="map"></div>
		</div>
		<script>
			var map = null;
			var markerClusterGroup = null;
			var climateIcon = L.divIcon({className: 'climate-marker', iconSize: [20, 20]});

			function refreshMap() {
				markerClusterGroup = L.markerClusterGroup({
					maxClusterRadius: 40,
					disableClusteringAtZoom: 8
				});

				for (var i = 0; i < json.length; ++i) {
					var marker = L.marker([json[i].lat, json[i].lon], {icon: climateIcon, title: json[i].name});

					longitude = json[i].lon;
							latitude = json[i].lat;
							if(latitude>=0){
								latitude_text = Math.round(latitude*100)/100 + " <?php echo lang('coordN','u')?>";
							}
							if(latitude<0){
								latitude_text = Math.round(latitude*100)/100 + " <?php echo lang('coordS','u')?>";
							}
							if(longitude>=0){
								longitude_text = Math.round(longitude*100)/100 + " <?php echo lang('coordE','u')?>";
							}
							if(longitude<0){
								longitude_text = Math.round(longitude*100)/100 + " <?php echo lang('coordW','u')?>";
							}
							elevation = json[i].elevation;
							<?php
								if($displayCloudbaseUnits=="ft"){
									echo "elevation = Math.round(elevation * 3.28084);";
								}
							?>
							id = json[i].id;
							name = json[i].name;
							region = json[i].region;
							country = json[i].country;
							countrycode = json[i].countrycode;
							temp = eval(json[i].temp);
							<?php
								if($displayTempUnits=="F"){
									echo "temp = Math.round(temp * 9/5 + 32);";
								}
							?>
							mintemp = eval(json[i].mintemp);
							<?php
								if($displayTempUnits=="F"){
									echo "mintemp = Math.round(mintemp * 9/5 + 32);";
								}
							?>
							maxtemp = eval(json[i].maxtemp);
							<?php
								if($displayTempUnits=="F"){
									echo "maxtemp = Math.round(maxtemp * 9/5 + 32);";
								}
							?>
							humidity = eval(json[i].humidity);
							rain = eval(json[i].rain);
							<?php
								if($displayRainUnits=="in"){
									echo "rain = Math.round(rain * 3.93701)/100;";
								}
								if($displayRainUnits=="cm"){
									echo "rain = Math.round(rain / 10);";
								}
							?>
							sunlight = eval(json[i].sunlight);
							wetdays = json[i].wetdays;
							daylength = json[i].daylength;
							temprange = json[i].temprange;
							<?php
								if($displayTempUnits=="F"){
									echo "temprange = Math.round(temprange * 9/5 + 32);";
								}
							?>
							rainrange = json[i].rainrange;
							<?php
								if($displayRainUnits=="in"){
									echo "rainrange = Math.round(rainrange * 3.93701)/100;";
								}
								if($displayRainUnits=="cm"){
									echo "rainrange = Math.round(rainrange / 10);";
								}
							?>
							humidityrange = json[i].humidityrange;
							koppen = json[i].koppen;
							trewartha = json[i].trewartha;
							flag = countrycode+".png";
							if(region==""){
								content = "<table><tr><td><img src='<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/"+flag+"' width='60px'></td><td><span class='infobox_title'>"+name+"</span><center></td></tr></table>";
							}
							if(region!=""){
								content = "<table><tr><td><img src='<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/"+flag+"' width='40px'></td><td><span class='infobox_title'>"+name+", "+region+"</span><center></td></tr></table>";
							}
							content += "<table><tr><td width='20px' rowspan='2'></td><td><img src='<?php echo $pageURL.$path?>imgs/climateImgs/lat.png' width='20px'></td><td>"+latitude_text+"</td><td style='padding-left:20px'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/lon.png' width='20px'></td><td>"+longitude_text+"</td></tr><tr><td><img src='<?php echo $pageURL.$path?>imgs/climateImgs/elevation.png' width='20px'></td><td>"+elevation+" <?php echo $displayCloudbaseUnits?></td><td style='padding-left:20px'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/surroundings.png' width='20px'></td><td>"+koppen+" / "+trewartha+"</td></tr></table>";
							content += "<center><table cellspacing='10' cellpadding='2'><tr>";
							if(temp!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/temp.png' style='width:40px'><br>";
								content += temp+"<br>";
								if(mintemp!=-9999){
									content += "<font color='#73B9FF'>"+mintemp+"</font> / ";
								}
								if(maxtemp!=-9999){
									content += "<font color='#FF4C4C'>"+maxtemp+"</font> °<?php echo $displayTempUnits?></td>";
								}
							}
							if(humidity!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/humidity.png' style='width:40px'><br><br>"+humidity+" %</td>";
							}
							if(rain!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/rain.png' style='width:40px'><br>"+rain+"<br><?php echo $displayRainUnits?>/<?php echo lang('year','l')?></td>";
							}
							if(sunlight!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/sun.png' style='width:40px'><br>"+sunlight+"<br><?php echo lang('minAbbr','l')?>/<?php echo lang('day','l')?></td>";
							}
							content += "</tr></table><table cellspacing='10' cellpadding='2'><tr>";
							if(wetdays!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/wetdays.png' style='width:40px'><br><br>"+wetdays+"</td>";
							}
							if(daylength!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/daylength.png' style='width:40px'><br><br>"+daylength+" <?php echo lang('minAbbr','l')?></td>";
							}
							if(temprange!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/temp_range.png' style='width:40px'><br><br>"+temprange+" °<?php echo $displayTempUnits?></td>";
							}
							if(rainrange!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/rain_range.png' style='width:40px'><br><br>"+rainrange+" <?php echo $displayRainUnits?></td>";
							}
							if(humidityrange!=-9999){
								content += "<td align='center'><img src='<?php echo $pageURL.$path?>imgs/climateImgs/humidity_range.png' style='width:40px'><br><br>"+humidityrange+" %</td>";
							}
							content += "</tr></table></center>";
							content += "<div style='text-align:center;width:100%;font-size:14px;'><a href='index.php?q="+id+"'><input type='button' class='button' value='<?php echo lang("select",'c')?>'></a></div>";

					marker.bindPopup(content);
					markerClusterGroup.addLayer(marker);
				}
				map.addLayer(markerClusterGroup);
				$('#overlay').hide();
			}

			function initialize() {
				map = L.map('map').setView([20, 0], 2);
				L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
					maxZoom: 19
				}).addTo(map);
				refreshMap();
			}

			$(function() {
				initialize();
			});
			</script>
		<?php include("../../footer.php")?>
	</body>
</html>
	