<?php

	# 		Current Conditions - Big Display
	# 		Namespace:		currentBig
	#		Meteotemplate Block

	# 		v1.0 - Jan 11, 2017
	#			- initial release
	# 		v2.0 - Mar 15, 2017
	#			- added support for Meteotemplate API
	# 		v3.0 - Mar 27, 2017
	#			- added possibility to add timestamp
	# 		v4.0 - Apr 18, 2017
	#			- added update highlight
	# 		v5.0 - May 21, 2017
	#			- added 'condition' parameter using DarkSky
	# 		v6.0 - Nov 18, 2017
	#   		- added support for UV
	# 			- optimization
	# 			- added full support for API


	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	include("../../../config.php");
	include("../../../scripts/functions.php");
	include("../../../css/design.php");

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	if($fontSize<1){
		$fontSize = 1;
	}
	if($fontSize>5){
		$fontSize = 5;
	}
	$showParameter = trim(strtoupper($showParameter));

	if($showParameter=="T"){
		$iconText = "temp";
	}
	if($showParameter=="H"){
		$iconText = "humidity";
	}
	if($showParameter=="P"){
		$iconText = "pressure";
	}
	if($showParameter=="W"){
		$iconText = "wind";
	}
	if($showParameter=="G"){
		$iconText = "gust";
	}
	if($showParameter=="S"){
		$iconText = "sun";
	}
	if($showParameter=="R"){
		$iconText = "rain";
	}
	if($showParameter=="RR"){
		$iconText = "rain";
	}
	if($showParameter=="A"){
		$iconText = "apparent";
	}
	if($showParameter=="D"){
		$iconText = "dewpoint";
	}

	if($showParameter=="CONDITION"){
		$fIOURL = "https://api.darksky.net/forecast/".$fIOKey."/".$stationLat.",".$stationLon."?units=si&lang=".$fIOLanguage;
	
		if(file_exists("../../../pages/forecast/cache/current.txt")){ 
			if (time()-filemtime("../../../pages/forecast/cache/current.txt") > 60 * 15) { // cache every 15 mins
				unlink("../../../pages/forecast/cache/current.txt");
			}
		}
		if(file_exists("../../../pages/forecast/cache/current.txt")){
			$rawData = file_get_contents("../../../pages/forecast/cache/current.txt");
			$forecastLoadedTime = filemtime("../../../pages/forecast/cache/current.txt");
		}
		else{
			// get contents
			$rawData = file_get_contents($fIOURL);
			if($rawData!=""){
				file_put_contents("../../../pages/forecast/cache/current.txt",$rawData);
			}
			$forecastLoadedTime = time();
		}
		
		$dataString = json_decode($rawData,true);

		$iconsAvailable = array("clear-day","clear-night","rain","snow","sleet","wind","fog","cloudy","partly-cloudy-day","partly-cloudy-night","thunderstorm");

		$conditionAvailable = false;

		$current['icon'] = $dataString['currently']['icon'];

		if(in_array($current['icon'],$iconsAvailable)){
			$conditionAvailable = true;
		}
	}

?>
	<style>

	</style>
	<?php 
		if($showParameter!="CONDITION"){
	?>
			<div style="width:98%;margin: 0 auto;font-size:2em;text-align:center">
				<span class="mticon-<?php echo $iconText?>"></span>
			</div>
	<?php 
		}
		else{
	?>
			<img src="homepage/blocks/currentBig/icons/<?php echo $theme?>/<?php echo $current['icon']?>.png" style="width:100px">
	<?php
		}
	?>
	<?php 
		if($showTime){ 
	?>
		<div style="width:98%;margin: 0 auto;font-size:1em;text-align:center" id="currentConditionsBigTime">

		</div>
	<?php
		}
	?>
	<?php 
		if($showParameter!="CONDITION"){
	?>
			<div id="currentConditionsBigDiv"></div>
			<input type="hidden" id="currentBigHelperInput">
			<script>
				updaterConditionsBig();
				setInterval(function(){ updaterConditionsBig(); }, (<?php echo $updateInterval?>*1000));
				function updaterConditionsBig(){
					//$("#currentTimestampValue").html("<span class='spinner' id='updateSpinner'>Loading…</span>");
					$.ajax({
						url : "homepage/blocks/currentBig/updater.php",
						dataType : 'json',
						success : function (json) {
							if(json[0]!=$("#currentBigHelperInput").val()){
								changeBigParameter = true;
							}
							else{
								changeBigParameter = false;
							}
							$("#currentBigHelperInput").val(json[0]);
							$("#currentConditionsBigDiv").html("<span style='font-weight:bold;font-size:<?php echo $fontSize?>em'>"+json[0]+"</span><span style='font-size:<?php echo ($fontSize/2)?>em'>"+json[1]+"</span>");
							if(json[2]!=$("#currentConditionsBigTime").html()){
								changeBigTime = true;
							}
							else{
								changeBigTime = false;
							}
							$("#currentConditionsBigTime").html(json[2]);
							<?php 
								if($currentBigHighlightUpdate){
							?>
									updateBigElements = [];
									if(changeBigParameter){
										updateBigElements.push("Value");
									}
									if(changeBigTime){
										updateBigElements.push("Date");
									}
									currentBigHighlightUpdate(updateBigElements);
							<?php 
								}
							?>
						},
					});
				}
				function currentBigHighlightUpdate(updateBigElements) {
					var originalBigColor = $('#currentConditionsBigTime').css("color");
					if($.inArray("Date", updateBigElements) !== -1){
						$('#currentConditionsBigTime').css("color",'#<?php echo $theme=="dark" ? $color_schemes[$design2]['200'] : $color_schemes[$design2]['600']?>');
					}
					if($.inArray("Value", updateBigElements) !== -1){
						$('#currentConditionsBigDiv').css("color",'#<?php echo $theme=="dark" ? $color_schemes[$design2]['200'] : $color_schemes[$design2]['600']?>');
					}
					window.setTimeout(function() 
						{ 
							if($.inArray("Date", updateBigElements) !== -1){
								$('#currentConditionsBigTime').css("color",originalBigColor);
							}
							if($.inArray("Value", updateBigElements) !== -1){
								$('#currentConditionsBigDiv').css("color",originalBigColor);
							}
						}, 
						<?php echo ($currentBigHighlightInterval*1000)?>
					);
				}
			</script>
	<?php 
		}
	?>
