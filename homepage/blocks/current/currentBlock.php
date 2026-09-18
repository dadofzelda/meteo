<?php

	# 		Current Conditions Block
	# 		Namespace:		current
	#		Meteotemplate Block

	# 		v1.1 - Jan 29, 2016
	#			- added responsiveness
	# 		v2.0 - Feb 2, 2016
	#			- added UV sensor data
	# 		v2.1 - Feb 8, 2016
	#			- added UV support for Weather Link
	# 		v2.2 - Feb 19, 2016
	#			- added UV support for Meteobridge
	# 		v2.3 - Mar 1, 2016
	#			- added support for WS1001
	# 		v2.4 - Mar 5, 2016
	#			-  bug fix for Cumulus wind/gust
	# 		v3.0 - Mar 19, 2016
	#			-  added option to display wind direction as direction icon and text or degrees
	# 		v3.1 - Mar 30, 2016
	#			-  Weather Display apparent temperature bug fix
	# 		v3.2 - Apr 1, 2016
	#			-  CSS tweaks
	#		v3.3 - Apr 25, 2016
	#			-  proper unit name formatting
	#			-  bug fixes
	#		v4.0 - Jul 04, 2016
	#			- added rain rate
	#			- format wind units and pressure units
	#		v5.0 - Aug 09, 2016
	#			- possibility to add title
	#		v6.0 - Sep 30, 2016
	#			- added support for WeatherCat
	#		v6.1 - Jan 11, 2017
	#			- minor bug fixes for Cumulus data parsing
	#		v7.0 - Jan 26, 2017
	#			- added possibility to show warnings
	#			- added feature to show station as offline
	#			- CSS tweaks
	#			- converted icons to SVG objects
	# 		v8.0 - Feb 22, 2017
	# 			- added Beaufort scale
	# 		v9.0 - Mar 10, 2017
	# 			- added trends
	# 		v10.0 - Mar 13, 2017
	# 			- added support for Meteotemplate API
	# 		v10.1 - Mar 14, 2017
	# 			- minor tweaks
	# 		v11.0 - Mar 19, 2017
	# 			- added option to highlight time when data updated
	# 		v12.0 - Apr 14, 2017
	# 			- added option to highlight changed values upon update
	# 			- tweaked decimal places display
	# 		v12.1 - Apr 17, 2017
	# 			- fix for correct realtime parsing (inconsistant Cumulus...)
	# 		v13.0 - May 19, 2017
	# 			- added option to show current conditions from DarkSky
	# 		v13.1 - Jun 17, 2017
	# 			- CSS tweaks
	# 		v14.0 - Jun 25, 2017
	# 			- current block now uses API exclusively
	# 			- improved rain rate accuracy
	# 			- UV sensor auto enabled when available]
	# 			- CSS tweaks
	# 		v14.1 - Jun 25, 2017
	# 			- bug fix for rain rate when units set to in
	# 		v15.0 - Jul 11, 2017
	# 			- added parameter table + new parameters
	# 			- optimization
	# 		v15.1 - Jul 12, 2017
	# 			- added option to hide indoor data 
	# 			- bug fixes
	# 		v16.0 - Aug 16, 2017
	# 			- improved missing data handling
	# 			- bug fixes
	# 		v17.0 - Aug 21, 2017
	# 			- added daily UV max
	# 		v18.0 - Aug 23, 2017
	# 			- added auto-open feature
	# 		v18.1 - Sep 13, 2017
	# 			- heat-index bug fix
	# 		v19.0 - Oct 22, 2017
	# 			- added option to hide seconds
	# 		v20.0 - Nov 27, 2017
	# 			- added option to hide top section
	# 		v21.0 - Jan 4, 2018
	# 			- added option to show labels
	# 		v22.0 - Jan 24, 2018
	# 			- added option to specify color of the highlight
	# 		v23.0 - Feb 17, 2018
	# 			- added option to specify which parameters to show
	# 			- bug fixes
	# 		v24.0 - Feb 24, 2018
	#  			- added dual units option
	#		v24.1 - Feb 28, 2018
	# 			- bug fixes

	include("../../../config.php");
	include("../../../scripts/functions.php");
	include("../../../css/design.php");

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];

	$language = loadLangs();

	$filePath = urlencode($filePath);

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

	if(in_array($current['icon'],$iconsAvailable) && $conditionEnable){
		$conditionAvailable = true;
	}

	if(!file_exists("../../../meteotemplateLive.txt")){
		die("<br><br>API file not found.<br><br>");
	}

	$apiData = file_get_contents("../../../meteotemplateLive.txt");
	$apiData = json_decode($apiData,true);
	if(isset($apiData['UV'])){
		$UVsensor = true;
	}
	else{
		$UVsensor = false;
	}

	$wrUnits = $displayVisibilityUnits=="mi" ? "mi" : "km";

	if(!$autoOpenCurrent){
		$textInitial = lang('more','c');
	}
	else{
		$textInitial = lang('hide','c');
	}
	if(!isset($hideMainSection)){
		$hideMainSection = false;
	}
	if(!isset($showLabels)){
		$showLabels = false;
	}

	$colorHighlight = trim(strtolower($colorHighlight));

	if($colorHighlight == "auto"){
		$colorHighlight = $theme == "dark" ? $color_schemes[$design2]['200'] : $color_schemes[$design2]['800'];
		$colorHighlight = "#".$colorHighlight;
	}

	$showDivs = strtoupper($showDivs);
	$showDivs = explode(",", $showDivs);
	$showDivs = array_map("trim", $showDivs);

	if(!isset($dualUnits)){
		$dualUnits = false;
	}

	if($displayTempUnits=="C"){
		$alternativeT = "F";
	}
	else{
		$alternativeT = "C";
	}

	if($displayRainUnits=="mm"){
		$alternativeR = "in";
	}
	else{
		$alternativeR = "mm";
	}
	
?>
	<style>
		.conditionsIcon{
			font-size: 2em;
		}
		.currentBlockDivs{
			display: inline-block;
			width: 70px;
			padding: 3px;
			vertical-align: top;
		}
		.currentBlockWarning{
			font-size: 1.2em;
			display: none;
			padding-top:3px;
		}
		@-webkit-keyframes currentBlockWarningAnimation {
		    0% {
				-webkit-transform: scale(0.7, 0.7); opacity: 0.6;
			}
		    50% {
				opacity: 1.0;
				-webkit-transform: scale(1.2, 1.2);
			}
		    100% {
				-webkit-transform: scale(0.7, 0.7); opacity: 0.6;
			}
		}
		@keyframes currentBlockWarningAnimation {
		    0% {
				-webkit-transform: scale(0.7, 0.7); opacity: 0.6;
				transform: scale(0.7, 0.7); opacity: 0.6;
			}
		    50% {
				opacity: 1.0;
				-webkit-transform: scale(1.2, 1.2);
				transform: scale(1.2, 1.2);
			}
		    100% {
				-webkit-transform: scale(0.7, 0.7); opacity: 0.6;
				transform: scale(0.7, 0.7); opacity: 0.6;
			}
		}
		.currentBlockWarningAnimationClass{
			-webkit-animation: currentBlockWarningAnimation 5s ease-out;
	    	-webkit-animation-iteration-count: infinite;
			animation: currentBlockWarningAnimation 5s ease-out;
	    	animation-iteration-count: infinite;
		}
		.currentBeaufort{
			width: 30%;
			margin: 0 auto;
			margin-top:2px;
			border-radius: 5px;
			font-size: 0.8em;
			font-weight:bold;
		}
		#currentWBft{
			<?php echo $currentShowBftW ? "" : "display:none;"?>
		}
		#currentGBft{
			<?php echo $currentShowBftG ? "" : "display:none;"?>
		}
		#currentBlockTrend{
			<?php echo $currentShowTrends ? "" : "display:none;"?>
		}
		.currentDetailsHeading{
			font-weight: bold;
			font-variant: small-caps;
			font-size: 1.3em;
		}
		.currentDetailsParameter{
			text-align: left;
		}
		.currentDetailsValue{
			text-align: right;
		}
		.currentDetailsHeadingIcon{
			font-size: 1.5em;
		}
		.currentDetailsDivClass{
			width:98%;
			margin: 0 auto;
			border-radius: 10px;
			border: 1px solid <?php echo $theme=="dark" ? "white" : "black";?>;
		}
		.currentDetailsDivInner{
			width: 98%;
			margin: 0 auto;
			padding: 1%;
		}
		.currentLabel{
			font-size:0.9em;
			font-variant: small-caps;
		}
	</style>
	<?php
		if($showTitle){
	?>
			<div style="text-align:center;width:100%;font-weight:bold;font-size:0.9em;font-variant:small-caps">
				<?php echo lang('current conditions','w')?>
			</div>
	<?php
		}
	?>

	<table style="width:98%;margin:0 auto;text-align:center">
		<tr>
			<td style="width:80px">

			</td>
			<td>
				<span id="currentTimestampValue" style="font-size:2em"></span>
			</td>
			<td style="width:80px">
				<?php 
					if($conditionAvailable){
				?>
						<img src="homepage/blocks/current/icons/<?php echo $theme?>/<?php echo $current['icon']?>.png" style="width:50px">
				<?php
					}
				?>
			</td>
		</tr>
	</table>
	<div style="display:inline-block;vertical-align:top;margin:0 auto;width:98%<?php if($hideMainSection){ echo ';display:none';}?>" id="currentBlockMainDiv">
		<?php
			if(in_array("T", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivT">
					<span class="mticon-temp conditionsIcon tooltip" alt='' title="<?php echo lang('temperature','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('temperature','c')?></span><br><?php } ?><span id="currentTValue"></span><br>°<?php echo $displayTempUnits?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentTValueAlt"></span><br>°<?php echo $alternativeT?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendT"></div><div class="currentBlockWarning" id="currentBlockWarningT"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("H", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivH">
					<span class="mticon-humidity conditionsIcon tooltip" alt='' title="<?php echo lang('humidity','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('humidity','c')?></span><br><?php } ?><span id="currentHValue"></span><br>%<div class="currentBlockTrend" id="currentBlockTrendH"></div><div class="currentBlockWarning" id="currentBlockWarningH"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("P", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivP">
					<span class="mticon-pressure conditionsIcon tooltip" alt='' title="<?php echo lang('pressure','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('pressure','c')?></span><br><?php } ?><span id="currentPValue"></span><br><?php echo unitFormatter($displayPressUnits)?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentPValueAlt"></span><br><?php echo unitFormatter($alternativeP)?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendP"></div><div class="currentBlockWarning" id="currentBlockWarningP"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("W", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivW">
					<span class="mticon-wind conditionsIcon tooltip" alt='' title="<?php echo lang('wind speed','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('wind','c')?></span><br><?php } ?><span id="currentWValue"></span><br><?php echo unitFormatter($displayWindUnits)?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentWValueAlt"></span><br><?php echo unitFormatter($alternativeW)?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendW"></div><div id="currentWBft" class="currentBeaufort"></div><span id="currentBValue"></span><div class="currentBlockWarning" id="currentBlockWarningW"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("G", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivG">
					<span class="mticon-gust conditionsIcon tooltip" alt='' title="<?php echo lang('wind gust','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('gust','c')?></span><br><?php } ?><span id="currentGValue"></span><br><?php echo unitFormatter($displayWindUnits)?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentGValueAlt"></span><br><?php echo unitFormatter($alternativeW)?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendG"></div><div id="currentGBft" class="currentBeaufort"></div><div class="currentBlockWarning" id="currentBlockWarningG"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("R", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivR">
					<span class="mticon-rain conditionsIcon tooltip" alt='' title="<?php echo lang('precipitation','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('precipitation','c')?></span><br><?php } ?><span id="currentRValue" style="font-size:1em"></span><span style="font-size:1em">&nbsp;<?php echo $displayRainUnits?></span><br><span style="font-size:1em" id="currentRRValue"></span><span style="font-size:1em">&nbsp;<?php echo $displayRainUnits."/".lang('hAbbr','l')?></span>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentRValueAlt"></span><br><?php echo unitFormatter($alternativeR)?></div>
					<?php
						}
					?>
					<div class="currentBlockWarning" id="currentBlockWarningR"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("D", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivD">
					<span class="mticon-dewpoint conditionsIcon tooltip" alt='' title="<?php echo lang('dew point','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('dew point','c')?></span><br><?php } ?><span id="currentDValue"></span><br>°<?php echo $displayTempUnits?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentDValueAlt"></span><br><?php echo unitFormatter($alternativeT)?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendD"></div><div class="currentBlockWarning" id="currentBlockWarningD"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("A", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivA">
					<span class="mticon-apparent conditionsIcon tooltip" alt='' title="<?php echo lang('apparent temperature','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('apparent','c')?></span><br><?php } ?><span id="currentAValue"></span><br>°<?php echo $displayTempUnits?>
					<?php 
						if($dualUnits){
					?>
							<div style="font-size:0.8em"><span id="currentAValueAlt"></span><br><?php echo unitFormatter($alternativeT)?></div>
					<?php
						}
					?>
					<div class="currentBlockTrend" id="currentBlockTrendA"></div><div class="currentBlockWarning" id="currentBlockWarningA"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php 
			}
		?>
		<?php
			if(in_array("S", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivS">
					<span class="mticon-sun conditionsIcon tooltip" alt='' title="<?php echo lang('solar radiation','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('solar','c')?></span><br><?php } ?><span id="currentSValue"></span><br><span style="font-size:0.8em">W/m<sup>2</sup></span><div class="currentBlockWarning" id="currentBlockWarningS"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php
			}
		?>
		<?php
			if(in_array("UV", $showDivs)){
		?>
				<div class="currentBlockDivs" id="currentBlockDivUV">
					<span class="mticon-uv conditionsIcon tooltip" alt='' title="<?php echo lang('UV','c')?>"></span><br><?php if($showLabels){?><span class='currentLabel'><?php echo lang('uv','c')?></span><br><?php } ?><span id="currentUVValue"></span><div class="currentBlockWarning" id="currentBlockWarningUV"><span class="mticon-warninggeneral"></span></div>
				</div>
		<?php
			}
		?>
	</div>
	<div id="currentBlockDetails" class="details" style="width:98%;margin:0 auto">
		<h2><?php echo lang('current conditions','c')?></h2>
		<table style="width:100%;table-layout:fixed" id="currentDetailsTable" cellspacing="8">
			<tr>
				<?php 
					if(!$solarSensor && !isset($apiData['UV'])){
				?>
					<td style="vertical-align:top">
						<div id="currentDetailsDivT" class="currentDetailsDivClass">
							<div class="currentDetailsDivInner">
								<span class="mticon-temp currentDetailsHeadingIcon"></span><br>
								<span class="currentDetailsHeading"><?php echo lang('temperature','c')?></span>
								<table style="width:98%;margin:0 auto">
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('temperature','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueT"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('apparent temperature','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueA"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('dew point','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueD"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('heat index','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueHI"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											Humidex
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueHX"></span>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('windchill','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueWCh"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('wet-bulb','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueWB"></span><?php echo unitFormatter($displayTempUnits)?>
										</td>
									</tr>
									<?php 
										if(isset($apiData['TIN']) && $currentShowIndoor){		
									?>
											<tr>
												<td class="currentDetailsParameter">
													<?php echo lang('indoorAbbr','c')?>
												</td>
												<td class="currentDetailsValue">
													<span id="currentDetailsValueTIN"></span><?php echo unitFormatter($displayTempUnits)?>
												</td>
											</tr>
									<?php 
										}
										else{
											echo "<tr><td>&nbsp;</td><td></td></tr>";
										}
									?>
								</table>
							</div>
						</div>
						<div style="height:20px"></div>
						<div id="currentDetailsDivH" class="currentDetailsDivClass">
							<div class="currentDetailsDivInner">
								<span class="mticon-humidity currentDetailsHeadingIcon"></span><br>
								<span class="currentDetailsHeading"><?php echo lang('humidity','c')?></span>
								<table style="width:98%;margin:0 auto">
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('relative humidity','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueH"></span>%
										</td>
									</tr>
									<?php 
										if(isset($apiData['HIN']) && $currentShowIndoor){		
									?>
											<tr>
												<td class="currentDetailsParameter">
													<?php echo lang('indoorAbbr','c')?>
												</td>
												<td class="currentDetailsValue">
													<span id="currentDetailsValueHIN"></span>%
												</td>
											</tr>
									<?php 
										}
										else{
											echo "<tr><td>&nbsp;</td><td></td></tr>";
										}
									?>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('absolute humidity','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueAH"></span> g/m<sup>3</sup>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('saturated vapor pressure','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueSVP"></span> <?php echo unitFormatter($displayPressUnits)?>
										</td>
									</tr>
								</table>
							</div>
						</div>
					</td>
					<td style="vertical-align:top">
						<div id="currentDetailsDivR" class="currentDetailsDivClass">
							<div class="currentDetailsDivInner">
								<span class="mticon-rain currentDetailsHeadingIcon"></span><br>
								<span class="currentDetailsHeading"><?php echo lang('precipitation','c')?></span>
								<table style="width:98%;margin:0 auto">
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('rain rate','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('today','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueR"></span> <?php echo unitFormatter($displayRainUnits)?>
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueMaxRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div style="height:10px"></div>
						<div id="currentDetailsDivW" class="currentDetailsDivClass">
							<div class="currentDetailsDivInner">
								<span class="mticon-wind currentDetailsHeadingIcon"></span><br>
								<span class="currentDetailsHeading"><?php echo lang('wind speed','c')?></span>
								<table style="width:98%;margin:0 auto">
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('wind speed','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueW"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueWBft"></span> Bft)
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('wind gust','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueG"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueGBft"></span> Bft)
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('wind direction','c')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueB"></span>&deg; (<span id="currentDetailsValueBDir"></span>)
										</td>
									</tr>
									<tr>
										<td class="currentDetailsParameter">
											<?php echo lang('wind run','c')?> - <?php echo lang('today','l')?>
										</td>
										<td class="currentDetailsValue">
											<span id="currentDetailsValueWrToday"></span> <?php echo $wrUnits?>
										</td>
									</tr>
								</table>
							</div>
						</div>
						<div style="height:10px"></div>
						<div id="currentDetailsDivP" class="currentDetailsDivClass">
							<div class="currentDetailsDivInner">	
							<span class="mticon-pressure currentDetailsHeadingIcon"></span><br>
							<span class="currentDetailsHeading"><?php echo lang('pressure','c')?></span>
							<table style="width:98%;margin:0 auto">
								<tr>
									<td class="currentDetailsParameter">
										<?php echo lang('sea-level','c')?>
									</td>
									<td class="currentDetailsValue">
										<span id="currentDetailsValueP"></span> <?php echo unitFormatter($displayPressUnits)?>
									</td>
								</tr>
								<tr>
									<td class="currentDetailsParameter">
										<?php echo lang('weather station','c')?>
									</td>
									<td class="currentDetailsValue">
										<span id="currentDetailsValuePst"></span> <?php echo unitFormatter($displayPressUnits)?>
									</td>
								</tr>
							</table>
							</div>
						</div>
					</td>
				<?php 
					}
				?>
				<?php 
					if(!$solarSensor && isset($apiData['UV'])){
				?>
						<td style="vertical-align:top">
							<div id="currentDetailsDivT" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-temp currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('temperature','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueT"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('apparent temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueA"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('dew point','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueD"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('heat index','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHI"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												Humidex
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHX"></span>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('windchill','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWCh"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wet-bulb','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWB"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<?php 
											if(isset($apiData['TIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueTIN"></span><?php echo unitFormatter($displayTempUnits)?>
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivH" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-humidity currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('humidity','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('relative humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueH"></span>%
											</td>
										</tr>
										<?php 
											if(isset($apiData['HIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueHIN"></span>%
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('absolute humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueAH"></span> g/m<sup>3</sup>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('saturated vapor pressure','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueSVP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivR" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-rain currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('precipitation','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('rain rate','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('today','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueR"></span> <?php echo unitFormatter($displayRainUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueMaxRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivW" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-wind currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('wind speed','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind speed','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueW"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueWBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind gust','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueG"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueGBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind direction','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueB"></span>&deg; (<span id="currentDetailsValueBDir"></span>)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind run','c')?> - <?php echo lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWrToday"></span> <?php echo $wrUnits?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivP" class="currentDetailsDivClass">	
								<div class="currentDetailsDivInner">
									<span class="mticon-pressure currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('pressure','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('sea-level','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('weather station','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValuePst"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivUV" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-uv currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading">UV</span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												UV
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueUV"></span>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueUVMax"></span>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
				<?php
					}
				?>
				<?php 
					if($solarSensor && !isset($apiData['UV'])){
				?>
						<td style="vertical-align:top">
							<div id="currentDetailsDivT" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-temp currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('temperature','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueT"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('apparent temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueA"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('dew point','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueD"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('heat index','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHI"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												Humidex
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHX"></span>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('windchill','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWCh"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wet-bulb','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWB"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<?php 
											if(isset($apiData['TIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueTIN"></span><?php echo unitFormatter($displayTempUnits)?>
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivH" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-humidity currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('humidity','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('relative humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueH"></span>%
											</td>
										</tr>
										<?php 
											if(isset($apiData['HIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueHIN"></span>%
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('absolute humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueAH"></span> g/m<sup>3</sup>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('saturated vapor pressure','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueSVP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivR" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-rain currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('precipitation','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('rain rate','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('today','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueR"></span> <?php echo unitFormatter($displayRainUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueMaxRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivW" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-wind currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('wind speed','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind speed','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueW"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueWBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind gust','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueG"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueGBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind direction','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueB"></span>&deg; (<span id="currentDetailsValueBDir"></span>)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind run','c')?> - <?php echo lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWrToday"></span> <?php echo $wrUnits?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivP" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">	
									<span class="mticon-pressure currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('pressure','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('sea-level','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('weather station','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValuePst"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivS" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-sun currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('solar radiation','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('solar radiation','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueS"></span> W/m<sup>2</sup>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueMaxS"></span> W/m<sup>2</sup>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
				<?php
					}
				?>
				<?php 
					if($solarSensor && isset($apiData['UV'])){
				?>
						<td style="vertical-align:top">
							<div id="currentDetailsDivT" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-temp currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('temperature','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueT"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('apparent temperature','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueA"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('dew point','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueD"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('heat index','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHI"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												Humidex
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueHX"></span>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('windchill','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWCh"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wet-bulb','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWB"></span><?php echo unitFormatter($displayTempUnits)?>
											</td>
										</tr>
										<?php 
											if(isset($apiData['TIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueTIN"></span><?php echo unitFormatter($displayTempUnits)?>
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
									</table>
								</div>
							</div>
							<div style="height:20px"></div>
							<div id="currentDetailsDivH" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-humidity currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('humidity','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('relative humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueH"></span>%
											</td>
										</tr>
										<?php 
											if(isset($apiData['HIN']) && $currentShowIndoor){		
										?>
												<tr>
													<td class="currentDetailsParameter">
														<?php echo lang('indoorAbbr','c')?>
													</td>
													<td class="currentDetailsValue">
														<span id="currentDetailsValueHIN"></span>%
													</td>
												</tr>
										<?php 
											}
											else{
												echo "<tr><td>&nbsp;</td><td></td></tr>";
											}
										?>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('absolute humidity','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueAH"></span> g/m<sup>3</sup>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('saturated vapor pressure','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueSVP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivR" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-rain currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('precipitation','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('rain rate','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('today','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueR"></span> <?php echo unitFormatter($displayRainUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueMaxRR"></span> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:20px"></div>
							<div id="currentDetailsDivW" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-wind currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('wind speed','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind speed','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueW"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueWBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind gust','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueG"></span> <?php echo unitFormatter($displayWindUnits)?> (<span id="currentDetailsValueGBft"></span> Bft)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind direction','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueB"></span>&deg; (<span id="currentDetailsValueBDir"></span>)
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('wind run','c')?> - <?php echo lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueWrToday"></span> <?php echo $wrUnits?>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
						<td style="vertical-align:top">
							<div id="currentDetailsDivP" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-pressure currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('pressure','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('sea-level','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueP"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('weather station','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValuePst"></span> <?php echo unitFormatter($displayPressUnits)?>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivS" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-sun currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading"><?php echo lang('solar radiation','c')?></span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('solar radiation','c')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueS"></span> W/m<sup>2</sup>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueMaxS"></span> W/m<sup>2</sup>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<div style="height:10px"></div>
							<div id="currentDetailsDivUV" class="currentDetailsDivClass">
								<div class="currentDetailsDivInner">
									<span class="mticon-uv currentDetailsHeadingIcon"></span><br>
									<span class="currentDetailsHeading">UV</span>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td class="currentDetailsParameter">
												UV
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueUV"></span>
											</td>
										</tr>
										<tr>
											<td class="currentDetailsParameter">
												<?php echo lang('maximumAbbr','c')." - ".lang('today','l')?>
											</td>
											<td class="currentDetailsValue">
												<span id="currentDetailsValueUVMax"></span>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</td>
				<?php 
					}
				?>
			</tr>
		</table>
	</div>
	<?php
		if(!$hideMainSection){
	?>
			<div style="width:98%;margin:0 auto">
				<span id="currentBlockMoreOption" class="more" onclick="txt = $('#currentBlockDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('hide','l')?>';$('#currentBlockDetails').slideToggle(800);$(this).text(txt);resizeCurrentDivs()">
					<?php echo $textInitial?>
				</span>
			</div>
	<?php 
		}
	?>
	<script>
		updater();
		setInterval(function(){ updater(); }, (<?php echo $updateInterval?>*1000));
		<?php 
			if($hideMainSection){
		?>
				$("#currentBlockDetails").show();
				$("#currentBlockMainDiv").hide();
		<?php
			}
		?>
		function updater(){
			$.ajax({
				url : "homepage/blocks/current/updater.php?interval=<?php echo $currentMaxInterval?>",
				dataType : 'json',
				success : function (json) {
					if(json['offline']==0){
						<?php 
							if(!$hideMainSection){
						?>
								$("#currentBlockMainDiv").show();
						<?php 
							}
							else{
						?>
								$("#currentBlockMainDiv").hide();
						<?php 
							}
						?>
						$("#currentDetailsValueT").html(json['T']);
						$("#currentDetailsValueA").html(json['A']);
						$("#currentDetailsValueD").html(json['D']);
						$("#currentDetailsValueHX").html(json['HX']);
						$("#currentDetailsValueHI").html(json['HI']);
						$("#currentDetailsValueWB").html(json['WB']);
						$("#currentDetailsValueWCh").html(json['WCh']);
						$("#currentDetailsValueTIN").html(json['TIN']);

						$("#currentDetailsValueH").html(json['H']);
						$("#currentDetailsValueHIN").html(json['HIN']);
						$("#currentDetailsValueAH").html(json['AH']);
						$("#currentDetailsValueSVP").html(json['SVP']);

						$("#currentDetailsValueP").html(json['P']);
						$("#currentDetailsValuePst").html(json['Pst']);

						$("#currentDetailsValueW").html(json['W']);
						$("#currentDetailsValueG").html(json['G']);
						$("#currentDetailsValueWBft").html(json['WBft']);
						$("#currentDetailsValueGBft").html(json['GBft']);
						$("#currentDetailsValueB").html(json['B']);
						$("#currentDetailsValueWrToday").html(json['wrToday']);

						$("#currentDetailsValueR").html(json['R']);
						$("#currentDetailsValueRR").html(json['RR']);
						$("#currentDetailsValueMaxRR").html(json['maxRR']);

						$("#currentDetailsValueS").html(json['S']);
						$("#currentDetailsValueMaxS").html(json['maxS']);
						
						$("#currentDetailsValueUV").html(json['UV']);
						$("#currentDetailsValueUVMax").html(json['maxUV']);

						// bgs
						$("#currentDetailsDivT").css("background",json['colorT1']);
						$("#currentDetailsDivT").css("background","-moz-linear-gradient(top," + json['colorT1'] + " 0%, " + json['colorT2'] + " 100%)");
						$("#currentDetailsDivT").css("background","-webkit-linear-gradient(top," + json['colorT1'] +  " 0%, " + json['colorT2'] + " 100%)");
						$("#currentDetailsDivT").css("background","linear-gradient(to bottom," + json['colorT1'] +  " 0%, " + json['colorT2'] + " 100%)");
						$("#currentDetailsDivT").css("color",json['colorTFont']);
						$("#currentDetailsDivH").css("background",json['colorH1']);
						$("#currentDetailsDivH").css("background","-moz-linear-gradient(top," + json['colorH1'] + " 0%, " + json['colorH2'] + " 100%)");
						$("#currentDetailsDivH").css("background","-webkit-linear-gradient(top," + json['colorH1'] +  " 0%, " + json['colorH2'] + " 100%)");
						$("#currentDetailsDivH").css("background","linear-gradient(to bottom," + json['colorH1'] +  " 0%, " + json['colorH2'] + " 100%)");
						$("#currentDetailsDivH").css("color",json['colorHFont']);
						$("#currentDetailsDivP").css("background",json['colorP1']);
						$("#currentDetailsDivP").css("background","-moz-linear-gradient(top," + json['colorP1'] + " 0%, " + json['colorP2'] + " 100%)");
						$("#currentDetailsDivP").css("background","-webkit-linear-gradient(top," + json['colorP1'] +  " 0%, " + json['colorP2'] + " 100%)");
						$("#currentDetailsDivP").css("background","linear-gradient(to bottom," + json['colorP1'] +  " 0%, " + json['colorP2'] + " 100%)");
						$("#currentDetailsDivP").css("color",json['colorPFont']);
						$("#currentDetailsDivW").css("background",json['colorP1']);
						$("#currentDetailsDivW").css("background","-moz-linear-gradient(top," + json['colorW1'] + " 0%, " + json['colorW2'] + " 100%)");
						$("#currentDetailsDivW").css("background","-webkit-linear-gradient(top," + json['colorW1'] +  " 0%, " + json['colorW2'] + " 100%)");
						$("#currentDetailsDivW").css("background","linear-gradient(to bottom," + json['colorW1'] +  " 0%, " + json['colorW2'] + " 100%)");
						$("#currentDetailsDivW").css("color",json['colorWFont']);
						$("#currentDetailsDivR").css("background",json['colorR1']);
						$("#currentDetailsDivR").css("background","-moz-linear-gradient(top," + json['colorR1'] + " 0%, " + json['colorR2'] + " 100%)");
						$("#currentDetailsDivR").css("background","-webkit-linear-gradient(top," + json['colorR1'] +  " 0%, " + json['colorR2'] + " 100%)");
						$("#currentDetailsDivR").css("background","linear-gradient(to bottom," + json['colorR1'] +  " 0%, " + json['colorR2'] + " 100%)");
						$("#currentDetailsDivR").css("color",json['colorRFont']);
						$("#currentDetailsDivS").css("background",json['colorS1']);
						$("#currentDetailsDivS").css("background","-moz-linear-gradient(top," + json['colorS1'] + " 0%, " + json['colorS2'] + " 100%)");
						$("#currentDetailsDivS").css("background","-webkit-linear-gradient(top," + json['colorS1'] +  " 0%, " + json['colorS2'] + " 100%)");
						$("#currentDetailsDivS").css("background","linear-gradient(to bottom," + json['colorS1'] +  " 0%, " + json['colorS2'] + " 100%)");
						$("#currentDetailsDivS").css("color",json['colorSFont']);
						$("#currentDetailsDivUV").css("background",json['colorUV1']);
						$("#currentDetailsDivUV").css("background","-moz-linear-gradient(top," + json['colorUV1'] + " 0%, " + json['colorUV2'] + " 100%)");
						$("#currentDetailsDivUV").css("background","-webkit-linear-gradient(top," + json['colorUV1'] +  " 0%, " + json['colorUV2'] + " 100%)");
						$("#currentDetailsDivUV").css("background","linear-gradient(to bottom," + json['colorUV1'] +  " 0%, " + json['colorUV2'] + " 100%)");
						$("#currentDetailsDivUV").css("color",json['colorUVFont']);

						if(json['T']!=$("#currentTValue").text()){
							changeT = true;
						}
						else{
							changeT = false;
						}
						$("#currentTValue").text(json['T']);
						<?php 
							if($dualUnits){
						?>
								if(json['T']!="--"){
									<?php 
										if($displayTempUnits=="C"){
									?>
											altT = eval(json['T']) * 1.8 + 32;
											altT = altT.toFixed(1);
									<?php 
										}
										else{
									?>
											altT = (eval(json['T']) - 32) / 1.8;
											altT = altT.toFixed(1);
									<?php
										}
									?>	
								}
								$("#currentTValueAlt").text(altT);
						<?php
							}
						?>
						if(json['H']!=$("#currentHValue").text()){
							changeH = true;
						}
						else{
							changeH = false;
						}
						$("#currentHValue").text(json['H']);
						if(json['P']!=$("#currentPValue").text()){
							changeP = true;
						}
						else{
							changeP = false;
						}
						$("#currentPValue").text(json['P']);
						<?php 
							if($dualUnits){
						?>
								if(json['P']!="--"){
									<?php 
										if($displayPressUnits=="hpa" && $alternativeP == "inhg"){
									?>
											altP = eval(json['P']) * 0.02952;
											altP = altP.toFixed(2);
									<?php 
										}
									?>
									<?php
										if($displayPressUnits=="inhg" && $alternativeP == "hpa"){
									?>
											altP = (eval(json['P']) * 33.8638;
											altP = altP.toFixed(1);
									<?php
										}
									?>
									<?php
										if($displayPressUnits=="hpa" && $alternativeP == "mmhg"){
									?>
											altP = (eval(json['P']) * 0.75006;
											altP = altP.toFixed(1);
									<?php
										}
									?>
									<?php
										if($displayPressUnits=="mmhg" && $alternativeP == "hpa"){
									?>
											altP = (eval(json['P']) * 1.3332;
											altP = altP.toFixed(1);
									<?php
										}
									?>
									<?php
										if($displayPressUnits=="inhg" && $alternativeP == "mmhg"){
									?>
											altP = (eval(json['P']) * 25.3999;
											altP = altP.toFixed(1);
									<?php
										}
									?>
									<?php
										if($displayPressUnits=="mmhg" && $alternativeP == "inhg"){
									?>
											altP = (eval(json['P']) * 0.03937;
											altP = altP.toFixed(2);
									<?php
										}
									?>
									<?php 
										if($displayPressUnits==$alternativeP){
									?>
											altP = json['P'];
									<?php 
										}
									?>
								}
								$("#currentPValueAlt").text(altP);
						<?php
							}
						?>
						if(json['W']!=$("#currentWValue").text()){
							changeW = true;
						}
						else{
							changeW = false;
						}
						$("#currentWValue").text(json['W']);
						<?php 
							if($dualUnits){
						?>
								if(json['W']!="--"){
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "ms"){
									?>
											altW = eval(json['W']) / 3.6;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "kmh"){
									?>
											altW = eval(json['W']) * 3.6;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "mph"){
									?>
											altW = eval(json['W']) * 0.621371;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "kmh"){
									?>
											altW = eval(json['W']) * 1.60934;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "ms"){
									?>
											altW = eval(json['W']) * 0.44704;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "mph"){
									?>
											altW = eval(json['W']) * 2.23694;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "kt"){
									?>
											altW = eval(json['W']) * 0.539957;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "kmh"){
									?>
											altW = eval(json['W']) * 1.852;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "kt"){
									?>
											altW = eval(json['W']) * 0.868976;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "mph"){
									?>
											altW = eval(json['W']) * 1.15078;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "kt"){
									?>
											altW = eval(json['W']) * 1.94384;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "ms"){
									?>
											altW = eval(json['W']) * 0.514444;
											altW = altW.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits==$alternativeW){
									?>
											altW = json['W'];
									<?php 
										}
									?>
								}
								$("#currentWValueAlt").text(altW);
						<?php
							}
						?>
						$("#currentWBft").text(json['WBft']);
						$("#currentWBft").css("background",json['WBftBg']);
						$("#currentWBft").css("color",json['WBftColor']);
						if(json['G']!=$("#currentGValue").text()){
							changeG = true;
						}
						else{
							changeG = false;
						}
						$("#currentGValue").text(json['G']);
						<?php 
							if($dualUnits){
						?>
								if(json['G']!="--"){
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "ms"){
									?>
											altG = eval(json['G']) / 3.6;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "kmh"){
									?>
											altG = eval(json['G']) * 3.6;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "mph"){
									?>
											altG = eval(json['G']) * 0.621371;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "kmh"){
									?>
											altG = eval(json['G']) * 1.60934;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "ms"){
									?>
											altG = eval(json['G']) * 0.44704;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "mph"){
									?>
											altG = eval(json['G']) * 2.23694;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kmh" && $alternativeW == "kt"){
									?>
											altG = eval(json['G']) * 0.539957;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "kmh"){
									?>
											altG = eval(json['G']) * 1.852;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="mph" && $alternativeW == "kt"){
									?>
											altG = eval(json['G']) * 0.868976;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "mph"){
									?>
											altG = eval(json['G']) * 1.15078;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="ms" && $alternativeW == "kt"){
									?>
											altG = eval(json['G']) * 1.94384;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits=="kt" && $alternativeW == "ms"){
									?>
											altG = eval(json['G']) * 0.514444;
											altG = altG.toFixed(1);
									<?php 
										}
									?>
									<?php 
										if($displayWindUnits==$alternativeW){
									?>
											altG = json['G'];
									<?php 
										}
									?>
								}
								$("#currentGValueAlt").text(altG);
						<?php
							}
						?>
						$("#currentGBft").text(json['GBft']);
						$("#currentGBft").css("background",json['GBftBg']);
						$("#currentGBft").css("color",json['GBftColor']);
						if(json['A']!=$("#currentAValue").text()){
							changeA = true;
						}
						else{
							changeA = false;
						}
						$("#currentAValue").text(json['A']);
						<?php 
							if($dualUnits){
						?>
								if(json['A']!="--"){
									<?php 
										if($displayTempUnits=="C"){
									?>
											altA = eval(json['A']) * 1.8 + 32;
											altA = altA.toFixed(1);
									<?php 
										}
										else{
									?>
											altA = (eval(json['A']) - 32) / 1.8;
											altA = altA.toFixed(1);
									<?php
										}
									?>	
								}
								$("#currentAValueAlt").text(altA);
						<?php
							}
						?>
						if(json['D']!=$("#currentDValue").text()){
							changeD = true;
						}
						else{
							changeD = false;
						}
						$("#currentDValue").text(json['D']);
						<?php 
							if($dualUnits){
						?>
								if(json['D']!="--"){
									<?php 
										if($displayTempUnits=="C"){
									?>
											altD = eval(json['D']) * 1.8 + 32;
											altD = altD.toFixed(1);
									<?php 
										}
										else{
									?>
											altD = (eval(json['D']) - 32) / 1.8;
											altD = altD.toFixed(1);
									<?php
										}
									?>	
								}
								$("#currentDValueAlt").text(altD);
						<?php
							}
						?>
						if(json['S']!=$("#currentSValue").text()){
							changeS = true;
						}
						else{
							changeS = false;
						}
						$("#currentSValue").text(json['S']);
						if($("#currentSValue").text()=="null"){
							$("#currentSValue").text("");
						}
						if(json['UV']!=$("#currentUVValue").text()){
							changeUV = true;
						}
						else{
							changeUV = false;
						}
						$("#currentUVValue").text(json['UV']);
						if($("#currentUVValue").text()=="null"){
							$("#currentUVValue").text("");
						}
						if(json['R']!=$("#currentRValue").text()){
							changeR = true;
						}
						else{
							changeR = false;
						}
						$("#currentRValue").text(json['R']);
						<?php 
							if($dualUnits){
						?>
								if(json['R']!="--"){
									<?php 
										if($displayRainUnits=="mm"){
									?>
											altR = eval(json['R']) * 0.0393701;
											altR = altR.toFixed(2);
									<?php 
										}
										else{
									?>
											altR = eval(json['R']) * 25.4;
											altR = altR.toFixed(1);
									<?php
										}
									?>	
								}
								$("#currentRValueAlt").text(altR);
						<?php
							}
						?>
						if(json['RR']!=$("#currentRRValue").text()){
							changeRR = true;
						}
						else{
							changeRR = false;
						}
						$("#currentRRValue").text(json['RR']);
						<?php
							if($windDirectionView=="degrees"){
						?>
								if(json['B']!=$("#currentBValue").text()+"°"){
									changeB = true;
								}
								else{
									changeB = false;
								}
								$("#currentBValue").text(json['B']+"°");
						<?php
							}
							else{
						?>
								if(json['B']!="--"){
									windBearing = eval(json['B']);
									if(windBearing<=11.25){ dirIcon = "n";dirText = "<?php echo lang('directionN','u')?>";}
									if(windBearing>11.25 && windBearing<=33.75){
										dirIcon = "nne";dirText = "<?php echo lang('directionNNE','u')?>";
									}
									if(windBearing>33.75 && windBearing<=56.25){
										dirIcon = "ne";dirText = "<?php echo lang('directionNE','u')?>";
									}
									if(windBearing>56.25 && windBearing<=78.75){
										dirIcon = "ene";dirText = "<?php echo lang('directionENE','u')?>";
									}
									if(windBearing>78.75 && windBearing<=101.25){
										dirIcon = "e";dirText = "<?php echo lang('directionE','u')?>";
									}
									if(windBearing>101.25 && windBearing<=123.75){
										dirIcon = "ese";dirText = "<?php echo lang('directionESE','u')?>";
									}
									if(windBearing>123.75 && windBearing<=146.25){
										dirIcon = "se";dirText = "<?php echo lang('directionSE','u')?>";
									}
									if(windBearing>146.25 && windBearing<=168.75){
										dirIcon = "sse";dirText = "<?php echo lang('directionSSE','u')?>";
									}
									if(windBearing>168.75 && windBearing<=191.25){
										dirIcon = "s";dirText = "<?php echo lang('directionS','u')?>";
									}
									if(windBearing>191.25 && windBearing<=213.75){
										dirIcon = "ssw";dirText = "<?php echo lang('directionSSW','u')?>";
									}
									if(windBearing>213.75 && windBearing<=236.25){
										dirIcon = "sw";dirText = "<?php echo lang('directionSW','u')?>";
									}
									if(windBearing>236.25 && windBearing<=258.75){
										dirIcon = "wsw";dirText = "<?php echo lang('directionWSW','u')?>";
									}
									if(windBearing>258.75 && windBearing<=281.25){
										dirIcon = "w";dirText = "<?php echo lang('directionW','u')?>";
									}
									if(windBearing>281.25 && windBearing<=303.75){
										dirIcon = "wnw";dirText = "<?php echo lang('directionWNW','u')?>";
									}
									if(windBearing>303.75 && windBearing<=326.25){
										dirIcon = "nw";dirText = "<?php echo lang('directionNW','u')?>";
									}
									if(windBearing>326.25 && windBearing<=348.75){
										dirIcon = "nnw";dirText = "<?php echo lang('directionNNW','u')?>";
									}
									if(windBearing>348.75){
										dirIcon = "n";dirText = "<?php echo lang('directionN','u')?>";
									}
									bearingString = '<span class="mticon-' + dirIcon + '"></span>' + dirText;
									if(bearingString!=$("#currentBValue").html()){
										changeB = true;
									}
									else{
										changeB = false;
									}
								}
								else{
									changeB = false;
								}
								$("#currentBValue").html(bearingString);
								$("#currentDetailsValueBDir").html(bearingString);
								
						<?php
							}
						?>
						if(json['Timestamp']!=$("#currentTimestampValue").text()){
							changeDate = true;
						}
						else{
							changeDate = false;
						}
						$("#currentTimestampValue").text(json['Timestamp']);
						<?php 
							if($currentHighlightUpdate){
						?>
								updateElements = [];
								if(changeDate){
									updateElements.push("Date");
								}
								if(changeT){
									updateElements.push("T");
								}
								if(changeH){
									updateElements.push("H");
								}
								if(changeP){
									updateElements.push("P");
								}
								if(changeW){
									updateElements.push("W");
								}
								if(changeG){
									updateElements.push("G");
								}
								if(changeR){
									updateElements.push("R");
								}
								if(changeRR){
									updateElements.push("RR");
								}
								if(changeA){
									updateElements.push("A");
								}
								if(changeD){
									updateElements.push("D");
								}
								if(changeA){
									updateElements.push("A");
								}
								if(changeUV){
									updateElements.push("UV");
								}
								if(changeS){
									updateElements.push("S");
								}
								currentHighlightUpdate(updateElements);
						<?php 
							}
						?>
						$(".currentBlockDivs").css("color","<?php echo $theme=="dark" ? '#fff' : '#000';?>");
						$(".currentBlockDivs").css("text-shadow","0px");
						$(".currentBlockWarning").hide();
						$(".currentBlockWarning").removeClass("currentBlockWarningAnimationClass");

						<?php
							if($showCurrentWarnings){
						?>
								if(json['T']!="--"){
									if(eval(json['T'])>=<?php echo convertT($currentThresholdHighT)?>){
										$("#currentBlockWarningT").show();
										$("#currentBlockWarningT").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningT").css("color","<?php echo $currentWarningColorHighT?>");
										$("#currentBlockWarningT").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
									if(eval(json['T'])<=<?php echo convertT($currentThresholdLowT)?>){
										$("#currentBlockWarningT").show();
										$("#currentBlockWarningT").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningT").css("color","<?php echo $currentWarningColorLowT?>");
										$("#currentBlockWarningT").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['A']!="--"){
									if(eval(json['A'])>=<?php echo convertT($currentThresholdHighA)?>){
										$("#currentBlockWarningA").show();
										$("#currentBlockWarningA").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningA").css("color","<?php echo $currentWarningColorHighT?>");
										$("#currentBlockWarningA").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
									if(eval(json['A'])<=<?php echo convertT($currentThresholdLowA)?>){
										$("#currentBlockWarningA").show();
										$("#currentBlockWarningA").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningA").css("color","<?php echo $currentWarningColorLowT?>");
										$("#currentBlockWarningA").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['D']!="--"){
									if(eval(json['D'])>=<?php echo convertT($currentThresholdHighD)?>){
										$("#currentBlockWarningD").show();
										$("#currentBlockWarningD").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningD").css("color","<?php echo $currentWarningColorHighT?>");
										$("#currentBlockWarningD").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
									if(eval(json['D'])<=<?php echo convertT($currentThresholdLowD)?>){
										$("#currentBlockWarningD").show();
										$("#currentBlockWarningD").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningD").css("color","<?php echo $currentWarningColorLowT?>");
										$("#currentBlockWarningD").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['H']!="--"){															
									if(eval(json['H'])>=<?php echo ($currentThresholdHighH)?>){
										$("#currentBlockWarningH").show();
										$("#currentBlockWarningH").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningH").css("color","<?php echo $currentWarningColorHighH?>");
										$("#currentBlockWarningH").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
									if(eval(json['H'])<=<?php echo ($currentThresholdLowH)?>){
										$("#currentBlockWarningH").show();
										$("#currentBlockWarningH").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningH").css("color","<?php echo $currentWarningColorLowH?>");
										$("#currentBlockWarningH").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['P']!="--"){
									if(eval(json['P'])>=<?php echo convertP($currentThresholdHighP)?>){
										$("#currentBlockWarningP").show();
										$("#currentBlockWarningP").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningP").css("color","<?php echo $currentWarningColorHighP?>");
										$("#currentBlockWarningP").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
									if(eval(json['P'])<=<?php echo convertP($currentThresholdLowP)?>){
										$("#currentBlockWarningP").show();
										$("#currentBlockWarningP").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningP").css("color","<?php echo $currentWarningColorLowP?>");
										$("#currentBlockWarningP").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['W']!="--"){
									if(eval(json['W'])>=<?php echo convertW($currentThresholdHighW)?>){
										$("#currentBlockWarningW").show();
										$("#currentBlockWarningW").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningW").css("color","<?php echo $currentWarningColorHighW?>");
										$("#currentBlockWarningW").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['G']!="--"){
									if(eval(json['G'])>=<?php echo convertW($currentThresholdHighG)?>){
										$("#currentBlockWarningG").show();
										$("#currentBlockWarningG").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningG").css("color","<?php echo $currentWarningColorHighW?>");
										$("#currentBlockWarningG").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								if(json['RR']!="--"){
									if(eval(json['RR'])>=<?php echo convertR($currentThresholdHighR)?>){
										$("#currentBlockWarningR").show();
										$("#currentBlockWarningR").addClass("currentBlockWarningAnimationClass");
										$("#currentBlockWarningR").css("color","<?php echo $currentWarningColorHighR?>");
										$("#currentBlockWarningR").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
									}
								}
								<?php
									if($solarSensor){
								?>
										if(json['S']!="--"){
											if(eval(json['S'])>=<?php echo ($currentThresholdHighS)?>){
												$("#currentBlockWarningS").show();
												$("#currentBlockWarningS").addClass("currentBlockWarningAnimationClass");
												$("#currentBlockWarningS").css("color","<?php echo $currentWarningColorHighS?>");
												$("#currentBlockWarningS").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
											}
										}
								<?php
									}
								?>
								<?php
									if($UVsensor){
								?>
										if(json['UV']!="--"){
											if(eval(json['UV'])>=<?php echo ($currentThresholdHighUV)?>){
												$("#currentBlockWarningUV").show();
												$("#currentBlockWarningUV").addClass("currentBlockWarningAnimationClass");
												$("#currentBlockWarningUV").css("color","<?php echo $currentWarningColorHighUV?>");
												$("#currentBlockWarningUV").css("text-shadow","1px 2px 2px <?php echo $theme=="dark" ? '#fff' : '#000';?>");
											}
										}
								<?php
									}
								?>
						<?php
							}
						?>
						<?php 
							if($currentShowTrends){
						?>
								$("#currentBlockTrendT").html(json['trendT']);
								$("#currentBlockTrendH").html(json['trendH']);
								$("#currentBlockTrendP").html(json['trendP']);
								$("#currentBlockTrendW").html(json['trendW']);
								$("#currentBlockTrendG").html(json['trendG']);
								$("#currentBlockTrendA").html(json['trendA']);
								$("#currentBlockTrendD").html(json['trendD']);
						<?php 
							}
						?>
					}
					else{
						$("#currentBlockMainDiv").hide();
						$("#currentTimestampValue").html('<span style="font-variant:small-caps"><?php echo lang('offline','c')?></span><br /><span style="font-size:0.7em"><?php echo lang("last update","c")?>: ' + json['dt']) + '</span>';
					}

				},
			});
		}
		function currentHighlightUpdate(elements) {
			var original = $('#currentTimestampValue').css("color");
			if($.inArray("Date", elements) !== -1){
				$('#currentTimestampValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("T", elements) !== -1){
				$('#currentTValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("H", elements) !== -1){
				$('#currentHValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("P", elements) !== -1){
				$('#currentPValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("W", elements) !== -1){
				$('#currentWValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("G", elements) !== -1){
				$('#currentGValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("R", elements) !== -1){
				$('#currentRValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("RR", elements) !== -1){
				$('#currentRRValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("S", elements) !== -1){
				$('#currentSValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("UV", elements) !== -1){
				$('#currentUVValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("A", elements) !== -1){
				$('#currentAValue').css("color",'<?php echo $colorHighlight?>');
			}
			if($.inArray("D", elements) !== -1){
				$('#currentDValue').css("color",'<?php echo $colorHighlight?>');
			}
			window.setTimeout(function() 
				{ 
					if($.inArray("Date", elements) !== -1){
						$('#currentTimestampValue').css("color",original);
					}
					if($.inArray("T", elements) !== -1){
						$('#currentTValue').css("color",original);
					}
					if($.inArray("H", elements) !== -1){
						$('#currentHValue').css("color",original);
					}
					if($.inArray("P", elements) !== -1){
						$('#currentPValue').css("color",original);
					}
					if($.inArray("W", elements) !== -1){
						$('#currentWValue').css("color",original);
					}
					if($.inArray("G", elements) !== -1){
						$('#currentGValue').css("color",original);
					}
					if($.inArray("R", elements) !== -1){
						$('#currentRValue').css("color",original);
					}
					if($.inArray("RR", elements) !== -1){
						$('#currentRRValue').css("color",original);
					}
					if($.inArray("S", elements) !== -1){
						$('#currentSValue').css("color",original);
					}
					if($.inArray("UV", elements) !== -1){
						$('#currentUVValue').css("color",original);
					}
					if($.inArray("A", elements) !== -1){
						$('#currentAValue').css("color",original);
					}
					if($.inArray("D", elements) !== -1){
						$('#currentDValue').css("color",original);
					}
				}, 
				<?php echo ($currentHighlightInterval*1000)?>
			);
		}
		function resizeCurrentDivs(){
			<?php 
				if(!$solarSensor && !isset($apiData['UV'])){
			?>
					divHeight = $("#currentDetailsTable").innerHeight() + 50;
					$("#currentDetailsDivT").height(divHeight * 0.5);
					$("#currentDetailsDivH").height(divHeight * 0.5);
					$("#currentDetailsDivR").height(divHeight * 0.3333);
					$("#currentDetailsDivW").height(divHeight * 0.3333);
					$("#currentDetailsDivP").height(divHeight * 0.3333);
			<?php 
				}
			?>
			<?php 
				if($solarSensor && !isset($apiData['UV'])){
			?>
					divHeight = $("#currentDetailsTable").innerHeight() + 50;
					$("#currentDetailsDivT").height(divHeight * 0.5);
					$("#currentDetailsDivH").height(divHeight * 0.5);
					$("#currentDetailsDivR").height(divHeight * 0.5);
					$("#currentDetailsDivW").height(divHeight * 0.5);
					$("#currentDetailsDivP").height(divHeight * 0.5);
					$("#currentDetailsDivS").height(divHeight * 0.5);
			<?php 
				}
			?>
			<?php 
				if(!$solarSensor && isset($apiData['UV'])){
			?>
					divHeight = $("#currentDetailsTable").innerHeight() + 50;
					$("#currentDetailsDivT").height(divHeight * 0.5);
					$("#currentDetailsDivH").height(divHeight * 0.5);
					$("#currentDetailsDivR").height(divHeight * 0.5);
					$("#currentDetailsDivW").height(divHeight * 0.5);
					$("#currentDetailsDivP").height(divHeight * 0.5);
					$("#currentDetailsDivUV").height(divHeight * 0.5);
			<?php 
				}
			?>
			<?php 
				if($solarSensor && isset($apiData['UV'])){
			?>
					divHeight = $("#currentDetailsTable").innerHeight() + 50;
					$("#currentDetailsDivT").height(divHeight * 0.5);
					$("#currentDetailsDivH").height(divHeight * 0.5);
					$("#currentDetailsDivR").height(divHeight * 0.5);
					$("#currentDetailsDivW").height(divHeight * 0.5);
					$("#currentDetailsDivP").height(divHeight * 0.3333);
					$("#currentDetailsDivUV").height(divHeight * 0.3333);
					$("#currentDetailsDivS").height(divHeight * 0.3333);
			<?php 
				}
			?>
		}
		<?php
			if($autoOpenCurrent){
		?>

				$('#currentBlockDetails').slideToggle(800);
				$("#currentBlockMoreOption").text(<?php echo lang('hide','c')?>)
		<?php
			}
		?>
	</script>
