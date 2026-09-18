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
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('climate','c')?></title>
		<?php metaHeader()?>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo $googleMapsAPIKey?>"></script>
		<script src="<?php echo $pageURL.$path?>scripts/infobox.js"></script>
		<script src="climate_json.php"></script>
		<script src="<?php echo $pageURL.$path?>scripts/markercluster.js"></script>
		<script src="<?php echo $pageURL.$path?>scripts/datatable.js"></script>
		
		<style>
			#map {
				width: 100%;
				height: 800px;
			}
			.item {
				margin-left: 20px;
			}
			.info {
				color: black;
				text-align: center;
			}
			.infoBox {
				opacity:1;
				min-width: 400px;
				border-radius: 20px 20px 20px 20px;
				padding-top: 10px;
				padding-bottom: 15px;
				padding-left: 10px;
				padding-right: 10px;
				margin-left: 10px;
				margin-bottom: 15px;
				z-Index:9999;
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
			<?php include($baseURL."menu.php");?>
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
			infobox = new InfoBox({	
				content: document.getElementById("infobox"),
				disableAutoPan: false,
				alignBottom: false,
				zIndex: 10,
				maxWidth: 0,
				boxStyle: {
					opacity: 0.9,
					background: '#000000',
					color: '#ffffff',
				},
				infoBoxClearance: new google.maps.Size(3, 3),
				enableEventPropagation: false
			});
			 
			var styles = [];
			var gmarkers = [];

			var markerClusterer = null;
			var map = null;
			var imageUrl = '//chart.apis.google.com/chart?cht=mm&chs=24x32&' +'chco=FFFFFF,008CFF,000000&ext=.png';

			function refreshMap() {
				var markers = [];

				for (var i = 0; i < json.length; ++i) {
					var latLng = new google.maps.LatLng(json[i].lat,json[i].lon);
					icon = {
						path: google.maps.SymbolPath.CIRCLE,
						scale: 10,
						fillOpacity: 0.8,
						fillColor: "#000000",
						strokeOpacity: 0.7,
						strokeColor: '#FFFFFF',
						strokeWeight: 4,
					}
					var marker = new google.maps.Marker({
						position: latLng,
						title: json[i].name,
						icon: icon
					});
					google.maps.event.addListener(marker, 'click', (function(marker, i) {
						return function() {		
							infobox.open(map, marker);
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
							content += "<table><tr><td width='20px' rowspan='2'></td><td><span class='mticon-lat' style='font-size:2.2em'></span></td><td>"+latitude_text+"</td><td style='padding-left:20px'><span class='mticon-lon' style='font-size:2.2em'></span></td><td>"+longitude_text+"</td></tr><tr><td><span class='mticon-elevation' style='font-size:2.2em'></span></td><td>"+elevation+" <?php echo $displayCloudbaseUnits?></td><td style='padding-left:20px'><span class='mticon-weather' style='font-size:2.2em'></span></td><td>"+koppen+" / "+trewartha+"</td></tr></table>";
							content += "<center><table cellspacing='10' cellpadding='2'><tr>";
							if(temp!=-9999){
								content += "<td align='center'><span class='mticon-temp' style='font-size:3.7em'></span><br>";
								content += temp+"<br>";
								if(mintemp!=-9999){
									content += "<font color='#73B9FF'>"+mintemp+"</font> / ";
								}
								if(maxtemp!=-9999){
									content += "<font color='#FF4C4C'>"+maxtemp+"</font> °<?php echo $displayTempUnits?></td>";
								}
							}
							if(humidity!=-9999){
								content += "<td align='center'><span class='mticon-humidity' style='font-size:3.7em'></span><br><br>"+humidity+" %</td>";
							}
							if(rain!=-9999){
								content += "<td align='center'><span class='mticon-rain' style='font-size:3.7em'></span><br>"+rain+"<br><?php echo $displayRainUnits?>/<?php echo lang('year','l')?></td>";
							}
							if(sunlight!=-9999){
								content += "<td align='center'><span class='mticon-sun' style='font-size:3.7em'></span><br>"+sunlight+"<br><?php echo lang('minAbbr','l')?>/<?php echo lang('day','l')?></td>";
							}
							content += "</tr></table><table cellspacing='10' cellpadding='2'><tr>";
							if(wetdays!=-9999){
								content += "<td align='center'><span class='mticon-wetdays' style='font-size:3.7em'></span><br><br>"+wetdays+"</td>";
							}
							if(daylength!=-9999){
								content += "<td align='center'><span class='mticon-daylength' style='font-size:3.7em'></span><br><br>"+daylength+" <?php echo lang('minAbbr','l')?></td>";
							}
							if(temprange!=-9999){
								content += "<td align='center'><span class='mticon-temp-range' style='font-size:3.7em'></span><br><br>"+temprange+" °<?php echo $displayTempUnits?></td>";
							}
							if(rainrange!=-9999){
								content += "<td align='center'><span class='mticon-rain-range' style='font-size:3.7em'></span><br><br>"+rainrange+" <?php echo $displayRainUnits?></td>";
							}
							if(humidityrange!=-9999){
								content += "<td align='center'><span class='mticon-humidity-range' style='font-size:3.7em'></span><br><br>"+humidityrange+" %</td>";
							}
							content += "</tr></table></center>";
							content += "<div style='text-align:center;width:100%;font-size:14px;'><a href='index.php?climateID="+id+"'><input type='button' class='button' value='<?php echo lang("select",'c')?>'></a></div>";
							infobox.setContent(content);
							map.panTo(marker.getPosition());
							}
						}
					)(marker, i)); 
				  markers.push(marker);
				}
				$('#overlay').hide();
				markerClusterer = new MarkerClusterer(map, markers, {
					maxZoom: 8,
					gridSize: 40,
					styles: [
						{
							textColor: 'black',
							height: 53,
							url: "cluster1.png",
							width: 53
						},
						{
							textColor: 'black',
							height: 56,
							url: "cluster2.png",
							width: 56
						},
						{
							textColor: 'black',
							height: 66,
							url: "cluster3.png",
							width: 66
						},
						{
							textColor: 'black',
							height: 78,
							url: "cluster4.png",
							width: 78
						},
						{
							textColor: 'black',
							height: 90,
							url: "cluster5.png",
							width: 90
						}
					],
					minimumClusterSize:3,
				});
			}

			function initialize() {
				map = new google.maps.Map(document.getElementById('map'), {
					zoom: 2,
					center: new google.maps.LatLng(20, 0),
					mapTypeId: google.maps.MapTypeId.HYBRID
				});
				refreshMap();
			}

			function clearClusters(e) {
				e.preventDefault();
				e.stopPropagation();
				markerClusterer.clearMarkers();
			}

			google.maps.event.addDomListener(window, 'load', initialize);
			</script>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
	