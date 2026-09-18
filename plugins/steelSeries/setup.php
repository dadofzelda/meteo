<?php

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	// load core files
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	// check if settings already exists and if so, load it, otherwise set parameters to default values
	if(file_exists("settings.php")){
		include("settings.php");
	}

	// declare defaults
	if(!isset($showT)){
		$showT = true;
	}
	if(!isset($showA)){
		$showA = true;
	}
	if(!isset($showH)){
		$showH = true;
	}
	if(!isset($showP)){
		$showP = true;
	}
	if(!isset($showW)){
		$showW = true;
	}
	if(!isset($showB)){
		$showB = true;
	}
	if(!isset($showWindRose)){
		$showWindRose = true;
	}
	if(!isset($showR)){
		$showR = true;
	}
	if(!isset($showS)){
		$showS = true;
	}
	if(!isset($showUV)){
		$showUV = false;
	}
	if(!isset($gaugeTypeT)){
		$gaugeTypeT = "TYPE4";
	}
	if(!isset($gaugeTypeA)){
		$gaugeTypeA = "TYPE4";
	}
	if(!isset($gaugeTypeH)){
		$gaugeTypeH = "TYPE4";
	}
	if(!isset($gaugeTypeP)){
		$gaugeTypeP = "TYPE4";
	}
	if(!isset($gaugeTypeW)){
		$gaugeTypeW = "TYPE4";
	}
	if(!isset($gaugeTypeR)){
		$gaugeTypeR = "TYPE4";
	}
	if(!isset($gaugeTypeRR)){
		$gaugeTypeRR = "TYPE4";
	}
	if(!isset($gaugeTypeS)){
		$gaugeTypeS = "TYPE4";
	}
	if(!isset($gaugeOrder)){
		$gaugeOrder = "orderT;orderA;orderH;orderW;orderB;orderWindRose;orderP;orderR;orderS;orderUV";
	}
	if(!isset($gaugeAInitial)){
		$gaugeAInitial = "app";
	}
	if(!isset($gaugeTTrend)){
		$gaugeTTrend = true;
	}
	if(!isset($gaugePTrend)){
		$gaugePTrend = true;
	}
	if(!isset($gaugeUVDecimals)){
		$gaugeUVDecimals = true;
	}
	if(!isset($gaugePointerSpeed)){
		$gaugePointerSpeed = "4";
	}
	if(!isset($gaugeTooltips)){
		$gaugeTooltips = true;
	}
	if(!isset($gaugeTooltipsTrigger)){
		$gaugeTooltipsTrigger = "mouseenter";
	}
	if(!isset($gaugeTooltipDelay)){
		$gaugeTooltipDelay = "7";
	}
	if(!isset($gaugeTooltipFadeInSpeed)){
		$gaugeTooltipFadeInSpeed = "400";
	}
	if(!isset($gaugeTooltipFadeOutSpeed)){
		$gaugeTooltipFadeOutSpeed = "400";
	}
	if(!isset($showStatusLED)){
		$showStatusLED = true;
	}
	if(!isset($showStatus)){
		$showStatus = true;
	}
	if(!isset($showNextUpdate)){
		$showNextUpdate = true;
	}
	if(!isset($updateInterval)){
		$updateInterval = 30;
	}
	if(!isset($offlineInterval)){
		$offlineInterval = 11;
	}
	if(!isset($gaugeLimitTempCMin)){
		$gaugeLimitTempCMin = -30;
	}
	if(!isset($gaugeLimitTempCMax)){
		$gaugeLimitTempCMax = 40;
	}
	if(!isset($gaugeLimitTempFMin)){
		$gaugeLimitTempFMin = 0;
	}
	if(!isset($gaugeLimitTempFMax)){
		$gaugeLimitTempFMax = 120;
	}
	if(!isset($gaugeLimitPressHPaMin)){
		$gaugeLimitPressHPaMin = 980;
	}
	if(!isset($gaugeLimitPressHPaMax)){
		$gaugeLimitPressHPaMax = 1040;
	}
	if(!isset($gaugeLimitPressInHgMin)){
		$gaugeLimitPressInHgMin = 28;
	}
	if(!isset($gaugeLimitPressInHgMax)){
		$gaugeLimitPressInHgMax = 32;
	}
	if(!isset($gaugeLimitWindKmh)){
		$gaugeLimitWindKmh = 80;
	}
	if(!isset($gaugeLimitWindMs)){
		$gaugeLimitWindMs = 30;
	}
	if(!isset($gaugeLimitWindMph)){
		$gaugeLimitWindMph = 60;
	}
	if(!isset($gaugeLimitWindKts)){
		$gaugeLimitWindKts = 60;
	}
	if(!isset($gaugeLimitRainMM)){
		$gaugeLimitRainMM = 25;
	}
	if(!isset($gaugeLimitRainIN)){
		$gaugeLimitRainIN = 2;
	}
	if(!isset($gaugeLimitRainRateMM)){
		$gaugeLimitRainRateMM = 100;
	}
	if(!isset($gaugeLimitRainRateIN)){
		$gaugeLimitRainRateIN = 8;
	}
	if(!isset($gaugeLimitUV)){
		$gaugeLimitUV = 12;
	}
	if(!isset($gaugeLimitSolar)){
		$gaugeLimitSolar = 1440;
	}
	if(!isset($gaugeShadow)){
		$gaugeShadow = false;
	}
	if(!isset($gaugeShadowColor)){
		$gaugeShadowColor = "rgb(220,220,220)";
	}
	if(!isset($gaugeShadowOpacity)){
		$gaugeShadowOpacity = "0.3";
	}
	if(!isset($gaugeShadowSize)){
		$gaugeShadowSize = "0.01";
	}
	if(!isset($gaugeMinMaxColor)){
		$gaugeMinMaxColor = "rgb(212,132,134)";
	}
	if(!isset($gaugeMinMaxOpacity)){
		$gaugeMinMaxOpacity = "0.6";
	}
	if(!isset($gaugeWindRangeColor)){
		$gaugeWindRangeColor = "rgb(132,212,134)";
	}
	if(!isset($gaugeWindRangeOpacity)){
		$gaugeWindRangeOpacity = "0.6";
	}
	if(!isset($gaugeOdo)){
		$gaugeOdo = true;
	}
	if(!isset($gaugeOdoDigits)){
		$gaugeOdoDigits = "5";
	}
	if(!isset($gaugeOdoSize)){
		$gaugeOdoSize = "0.08";
	}
	if(!isset($gaugeOdoForeground)){
		$gaugeOdoForeground = "rgb(255, 255, 255)";
	}
	if(!isset($gaugeOdoBackground)){
		$gaugeOdoBackground = "rgb(0, 0, 0)";
	}
	if(!isset($gaugeOdoForegroundDecimals)){
		$gaugeOdoForegroundDecimals = "rgb(255, 0, 0)";
	}
	if(!isset($gaugeOdoBackgroundDecimals)){
		$gaugeOdoBackgroundDecimals = "rgb(255, 255, 255)";
	}
	if(!isset($gaugeDigitalFont)){
		$gaugeDigitalFont = false;
	}
	if(!isset($gaugeStatusScroll)){
		$gaugeStatusScroll = false;
	}
	if(!isset($gaugeKnobType)){
		$gaugeKnobType = "stdBlack";
	}
	if(!isset($gaugeFrameDesign)){
		$gaugeFrameDesign = "ANTHRACITE";
	}
	if(!isset($gaugeFrameBackground)){
		$gaugeFrameBackground = "BRUSHED_STAINLESS";
	}
	if(!isset($gaugeFramePointerColor)){
		$gaugeFramePointerColor = "RED";
	}
	if(!isset($gaugeFramePointerType)){
		$gaugeFramePointerType = "TYPE11";
	}
	if(!isset($gaugeFrameLCDColor)){
		$gaugeFrameLCDColor = "GRAY";
	}
	if(!isset($gaugeFrameLEDColor)){
		$gaugeFrameLEDColor = "RED_LED";
	}
	if(!isset($gaugeFrameForeground)){
		$gaugeFrameForeground = "TYPE2";
	}
	if(!isset($gaugeKnobType)){
		$gaugeKnobType = "STANDARD_KNOB";
	}
	if(!isset($gaugeKnobColor1)){
		$gaugeKnobColor1 = "rgb(232, 117, 117)";
	}
	if(!isset($gaugeKnobColor2)){
		$gaugeKnobColor2 = "rgb(149, 24, 24)";
	}
	if(!isset($gaugeSize)){
		$gaugeSize = "220";
	}
	if(!isset($gaugeRainColor)){
		$gaugeRainColor = "BLUE";
	}
	if(!isset($pageUpdateLimit)){
		$pageUpdateLimit = 20;
	}

	$rainGaugeColors['RED'] =  array("82, 0, 0", "158, 0, 19", "213, 0, 25", "240, 82, 88", "255, 171, 173", "255, 217, 218");
	$rainGaugeColors['GREEN'] =  array("8, 54, 4", "0, 107, 14", "15, 148, 0", "121, 186, 37", "190, 231, 141", "234, 247, 218");
	$rainGaugeColors['BLUE'] =  array("0, 11, 68", "0, 73, 135", "0, 108, 201", "0, 141, 242", "122, 200, 255", "204, 236, 255");
	$rainGaugeColors['BLUE2'] = array("73,155,234", "73,155,234","73,155,234", "20,84,153","20,84,153","20,84,153");
	$rainGaugeColors['BLUE3'] =  array("179,220,237", "179,220,237","41,184,229", "41,184,229","188,224,238","188,224,238");
	$rainGaugeColors['BLUE4'] =  array("17,84,208", "17,84,208","17,84,208", "14,62,175","14,62,175","14,62,175");
	$rainGaugeColors['BLUE5'] =  array("198,217,250", "198,217,250","17,84,208", "14,62,175","14,62,175","14,62,175");
	$rainGaugeColors['ORANGE'] =  array("118, 83, 30", "215, 67, 0", "240, 117, 0", "255, 166, 0", "255, 255, 128", "255, 247, 194");
	$rainGaugeColors['YELLOW'] =  array("41, 41, 0", "102, 102, 0", "177, 165, 0", "255, 242, 0", "255, 250, 153", "255, 252, 204");
	$rainGaugeColors['CYAN'] =  array("15, 109, 109", "0, 109, 144", "0, 144, 191", "0, 174, 239", "153, 223, 249", "204, 239, 252");
	$rainGaugeColors['MAGENTA'] =  array("98, 0, 114", "128, 24, 72", "191, 36, 107", "255, 48, 143", "255, 172, 210", "255, 214, 23");
	$rainGaugeColors['WHITE'] =  array("210, 210, 210", "220, 220, 220", "235, 235, 235", "255, 255, 255", "255, 255, 255", "255, 255, 255");
	$rainGaugeColors['GRAY'] =  array("25, 25, 25", "51, 51, 51", "76, 76, 76", "128, 128, 128", "204, 204, 204", "243, 243, 243");
	$rainGaugeColors['BLACK'] =  array("0, 0, 0", "5, 5, 5", "10, 10, 10", "15, 15, 15", "20, 20, 20", "25, 25, 25");
	$rainGaugeColors['RAITH'] =  array("0, 32, 65", "0, 65, 125", "0, 106, 172", "130, 180, 214", "148, 203, 242", "191, 229, 255");
	$rainGaugeColors['GREEN_LCD'] =  array("0, 55, 45", "15, 109, 93", "0, 185, 165", "48, 255, 204", "153, 255, 227", "204, 255, 241");
	$rainGaugeColors['JUG_GREEN'] =  array("0, 56, 0", "32, 69, 36", "50, 161, 0", "129, 206, 0", "190, 231, 141", "234, 247, 218");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<script src="scripts/colpick.js" type="text/javascript"></script>
		<script src="scripts/steelseries_tween.min.js"></script>
		<script src="scripts/steelseries-min.js"></script>
		<link rel="stylesheet" href="css/colpick.css" type="text/css"/>
		<style>
			.setupSection{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				width: 94%;
				margin: 0 auto;
				padding: 1%;
				border-radius: 10px;
				border: 1px solid #<?php echo $color_schemes[$design2]['400']?>;
				margin-top: 20px;
				text-align: justify;
			}
			.colorPickers {
				margin-right: auto;
				margin-left: auto;
				width:30px;
				height:30px;
				margin:5px;
				border: 2px solid <?php echo $color_schemes[$design2]['200']?>;
				cursor: pointer;
				background: #00008C;
				opacity: 0.85;
			}
			.colorPickers:hover{
				opacity: 1;
			}
			.showGauge{
				font-size: 2em;
			}
			#sortableList {
				list-style-type: none;
				margin: 0;
				padding: 0;
				cursor: move;
			}
			#sortableList li {
				margin: 3px 3px 3px 3px;
				padding: 8px;
				padding-left: 10px;
				padding-right: 10px;
				min-width: 50px;
				text-align: center;
				font-size: 1.5em;
				color: white;
				font-weight: bold;
				float:left;
				background: #<?php echo $color_schemes[$design]['800']?>;
			}
			#gaugeSizeExample{
				width: <?php echo $gaugeSize?>px;
				height: <?php echo $gaugeSize?>px;
				background: #<?php echo $color_schemes[$design]['700']?>;
				border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
				border-radius: 50%;
			}
			<?php
				foreach($rainGaugeColors as $code=>$rainColor){
			?>
					.rainGauge<?php echo $code?>{
						background: rgba(<?php echo $rainColor[0]?>,1);
						background: -moz-linear-gradient(left, rgba(<?php echo $rainColor[0]?>,1) 0%, rgba(<?php echo $rainColor[1]?>,1) 31%, rgba(<?php echo $rainColor[2]?>,1) 46%, rgba(<?php echo $rainColor[3]?>,1) 60%, rgba(<?php echo $rainColor[4]?>,1) 79%, rgba(<?php echo $rainColor[5]?>,1) 100%);
						background: -webkit-gradient(left top, right top, color-stop(0%, rgba(<?php echo $rainColor[0]?>,1)), color-stop(31%, rgba(<?php echo $rainColor[1]?>,1)), color-stop(46%, rgba(<?php echo $rainColor[2]?>,1)), color-stop(60%, rgba(<?php echo $rainColor[3]?>,1)), color-stop(79%, rgba(<?php echo $rainColor[4]?>,1)), color-stop(100%, rgba(<?php echo $rainColor[5]?>,1)));
						background: -webkit-linear-gradient(left, rgba(<?php echo $rainColor[0]?>,1) 0%, rgba(<?php echo $rainColor[1]?>,1) 31%, rgba(<?php echo $rainColor[2]?>,1) 46%, rgba(<?php echo $rainColor[3]?>,1) 60%, rgba(<?php echo $rainColor[4]?>,1) 79%, rgba(<?php echo $rainColor[5]?>,1) 100%);
						background: -o-linear-gradient(left, rgba(<?php echo $rainColor[0]?>,1) 0%, rgba(<?php echo $rainColor[1]?>,1) 31%, rgba(<?php echo $rainColor[2]?>,1) 46%, rgba(<?php echo $rainColor[3]?>,1) 60%, rgba(<?php echo $rainColor[4]?>,1) 79%, rgba(<?php echo $rainColor[5]?>,1) 100%);
						background: -ms-linear-gradient(left, rgba(<?php echo $rainColor[0]?>,1) 0%, rgba(<?php echo $rainColor[1]?>,1) 31%, rgba(<?php echo $rainColor[2]?>,1) 46%, rgba(<?php echo $rainColor[3]?>,1) 60%, rgba(<?php echo $rainColor[4]?>,1) 79%, rgba(<?php echo $rainColor[5]?>,1) 100%);
						background: linear-gradient(to right, rgba(<?php echo $rainColor[0]?>,1) 0%, rgba(<?php echo $rainColor[1]?>,1) 31%, rgba(<?php echo $rainColor[2]?>,1) 46%, rgba(<?php echo $rainColor[3]?>,1) 60%, rgba(<?php echo $rainColor[4]?>,1) 79%, rgba(<?php echo $rainColor[5]?>,1) 100%);"
					}
			<?php
				}
			?>
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader()?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<h1>Steel Series - Setup</h1>
			<form method="POST" action="saveSettings.php" target="_blank">
				<div class="setupSection">
					<h3>Displayed Gauges</h3>
					<p>
						Select which gauges you want to see. Also select their type (see the gauge examples at the very bottom of this page, each gauge has type number).
					</p>
					<br />
					<table style="width:98%;margin:0 auto">
						<tr>
						<td style="text-align:left">
							<table style="width:98%;margin: 0 auto;table-layout: fixed;">
								<tr>
									<td style="vertical-align:top">
										<span class="mticon-temp showGauge"></span><br /><select name="showT" class="button">
											<option value="true" <?php if($showT){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showT){echo "selected";}?>>No</option>
										</select>
										<br />
										<select name="gaugeTypeT" class="button">
											<option value="TYPE1" <?php if($gaugeTypeT=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeT=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeT=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeT=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-apparent showGauge"></span><span class="mticon-dewpoint showGauge"></span><br /><select name="showA" class="button">
											<option value="true" <?php if($showA){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showA){echo "selected";}?>>No</option>
										</select>
										<br />
										<select name="gaugeTypeA" class="button">
											<option value="TYPE1" <?php if($gaugeTypeA=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeA=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeA=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeA=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-humidity showGauge"></span><br /><select name="showH" class="button">
											<option value="true" <?php if($showH){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showH){echo "selected";}?>>No</option>
										</select>
										<br />
										<select name="gaugeTypeH" class="button">
											<option value="TYPE1" <?php if($gaugeTypeH=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeH=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeH=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeH=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-wind showGauge"></span><span class="mticon-gust showGauge"></span><br /><select name="showW" class="button">
											<option value="true" <?php if($showW){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showW){echo "selected";}?>>No</option>
										</select>
										<select name="gaugeTypeW" class="button">
											<option value="TYPE1" <?php if($gaugeTypeW=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeW=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeW=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeW=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-winddirection showGauge"></span><br /><select name="showB" class="button">
											<option value="true" <?php if($showB){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showB){echo "selected";}?>>No</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-bearing showGauge"></span><br /><select name="showWindRose" class="button">
											<option value="true" <?php if($showWindRose){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showWindRose){echo "selected";}?>>No</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-pressure showGauge"></span><br /><select name="showP" class="button">
											<option value="true" <?php if($showP){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showP){echo "selected";}?>>No</option>
										</select>
										<br />
										<select name="gaugeTypeP" class="button">
											<option value="TYPE1" <?php if($gaugeTypeP=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeP=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeP=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeP=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-rain showGauge"></span><br /><select name="showR" class="button">
											<option value="true" <?php if($showR){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showR){echo "selected";}?>>No</option>
										</select>
										<br />
										Rain<br />
										<select name="gaugeTypeR" class="button">
											<option value="TYPE1" <?php if($gaugeTypeR=="TYPE1"){echo "selected";}?>>Type 5</option>
											<option value="TYPE2" <?php if($gaugeTypeR=="TYPE2"){echo "selected";}?>>Type 6</option>
											<option value="TYPE3" <?php if($gaugeTypeR=="TYPE3"){echo "selected";}?>>Type 7</option>
											<option value="TYPE4" <?php if($gaugeTypeR=="TYPE4"){echo "selected";}?>>Type 8</option>
										</select>
										<br />
										Rain rate<br />
										<select name="gaugeTypeRR" class="button">
											<option value="TYPE1" <?php if($gaugeTypeRR=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeRR=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeRR=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeRR=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-sun showGauge"></span><br /><select name="showS" class="button">
											<option value="true" <?php if($showS){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showS){echo "selected";}?>>No</option>
										</select>
										<br />
										<select name="gaugeTypeS" class="button">
											<option value="TYPE1" <?php if($gaugeTypeS=="TYPE1"){echo "selected";}?>>Type 1</option>
											<option value="TYPE2" <?php if($gaugeTypeS=="TYPE2"){echo "selected";}?>>Type 2</option>
											<option value="TYPE3" <?php if($gaugeTypeS=="TYPE3"){echo "selected";}?>>Type 3</option>
											<option value="TYPE4" <?php if($gaugeTypeS=="TYPE4"){echo "selected";}?>>Type 4</option>
										</select>
									</td>
									<td style="vertical-align:top">
										<span class="mticon-uv showGauge"></span><br /><select name="showUV" class="button">
											<option value="true" <?php if($showUV){echo "selected";}?>>Yes</option>
											<option value="false" <?php if(!$showUV){echo "selected";}?>>No</option>
										</select>
										<select name="gaugeTypeUV" class="button">
											<option value="TYPE1" <?php if($gaugeTypeUV=="TYPE1"){echo "selected";}?>>Type 5</option>
											<option value="TYPE2" <?php if($gaugeTypeUV=="TYPE2"){echo "selected";}?>>Type 6</option>
											<option value="TYPE3" <?php if($gaugeTypeUV=="TYPE3"){echo "selected";}?>>Type 7</option>
											<option value="TYPE4" <?php if($gaugeTypeUV=="TYPE4"){echo "selected";}?>>Type 8</option>
										</select>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Gauge Order</h3>
					<p>
						Drag and drop the boxes to specify the order of the gauges you want to see on your page. If you disabled some gauges above then you can just put them anywhere in this order, they will be ignored anyway.
					</p>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left">
								<br />
								<ul id="sortableList">
									<?php
										$gaugeOrderExp = explode(";",$gaugeOrder);
										foreach($gaugeOrderExp as $thisGauge){
											if($thisGauge=="orderT"){
												echo '<li class="ui-state-default" id="orderT"><span class="mticon-temp"></span></li>';
											}
											if($thisGauge=="orderA"){
												echo '<li class="ui-state-default" id="orderA"><span class="mticon-apparent"></span></li>';
											}
											if($thisGauge=="orderH"){
												echo '<li class="ui-state-default" id="orderH"><span class="mticon-humidity"></span></li>';
											}
											if($thisGauge=="orderW"){
												echo '<li class="ui-state-default" id="orderW"><span class="mticon-wind"></span></li>';
											}
											if($thisGauge=="orderB"){
												echo '<li class="ui-state-default" id="orderB"><span class="mticon-winddirection"></span></li>';
											}
											if($thisGauge=="orderWindRose"){
												echo '<li class="ui-state-default" id="orderWindRose"><span class="mticon-bearing"></span></li>';
											}
											if($thisGauge=="orderP"){
												echo '<li class="ui-state-default" id="orderP"><span class="mticon-pressure"></span></li>';
											}
											if($thisGauge=="orderR"){
												echo '<li class="ui-state-default" id="orderR"><span class="mticon-rain"></span></li>';
											}
											if($thisGauge=="orderS"){
												echo '<li class="ui-state-default" id="orderS"><span class="mticon-sun"></span></li>';
											}
											if($thisGauge=="orderUV"){
												echo '<li class="ui-state-default" id="orderUV"><span class="mticon-uv"></span></li>';
											}
										}
									?>
								</ul>
								<input type="hidden" id="gaugeOrder" name="gaugeOrder" value="<?php echo $gaugeOrder?>">
								<br />
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Status</h3>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left;vertical-align:top">
								Update interval
							</td>
							<td style="text-align:left">
								<input name="updateInterval" class="button" value="<?php echo $updateInterval?>" size="3"> seconds
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Update limit
							</td>
							<td style="text-align:left">
								<input name="pageUpdateLimit" class="button" value="<?php echo $pageUpdateLimit?>" size="4"> minutes &nbsp;(max time for which the gauges will be updating - if someone left the homepage/plugin opened for longer, the gauges will automatically stop updating after this time to save server resources)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Station offline
							</td>
							<td style="text-align:left">
								<input name="offlineInterval" class="button" value="<?php echo $offlineInterval?>" size="3"> minutes
								&nbsp;(max interval for station to be considered online)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Show status LED
							</td>
							<td style="text-align:left">
								<select name="showStatusLED" class="button">
									<option value="true" <?php if($showStatusLED){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$showStatusLED){echo "selected";}?>>No</option>
								</select>
								&nbsp;show station status LED (online = green, offline = red)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Show status box
							</td>
							<td style="text-align:left">
								<select name="showStatus" class="button">
									<option value="true" <?php if($showStatus){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$showStatus){echo "selected";}?>>No</option>
								</select>
								&nbsp;show a box with last update time or a notice that station is online
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Show next update
							</td>
							<td style="text-align:left">
								<select name="showNextUpdate" class="button">
									<option value="true" <?php if($showNextUpdate){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$showNextUpdate){echo "selected";}?>>No</option>
								</select>
								&nbsp;show a box with countdown until next update
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Always scroll status
							</td>
							<td style="text-align:left">
								<select name="gaugeStatusScroll" class="button">
									<option value="true" <?php if($gaugeStatusScroll){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugeStatusScroll){echo "selected";}?>>No</option>
								</select>
								&nbsp;if the station status box is enabled you can select if you want the text to be scrolling from right to left
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Tooltips</h3>
					<p>
						Specify if you want to see tooltips for the gauges. Tooltips are boxes showing max/min daily values and in addition to enabling/disabling them, you can specify several options how you want them to be displayed.
					</p>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left;vertical-align:top">
								Enable tooltips
							</td>
							<td style="text-align:left">
								<select name="gaugeTooltips" class="button">
									<option value="true" <?php if($gaugeTooltips){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugeTooltips){echo "selected";}?>>No</option>
								</select>
								&nbsp;show tooltips (with details such as daily min/max etc.)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Tooltip trigger
							</td>
							<td style="text-align:left">
								<select name="gaugeTooltipsTrigger" class="button">
									<option value="mouseenter" <?php if($gaugeTooltipsTrigger=="mouseenter"){echo "selected";}?>>Hover</option>
									<option value="click" <?php if($gaugeTooltipsTrigger=="click"){echo "selected";}?>>Click</option>
								</select>
								&nbsp;specify if you want the tooltip to be shown when you mouse over the gauge, or click it (if using click, the cursor will change to hand, if mouseover, you will see standard cursor)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Display delay
							</td>
							<td style="text-align:left">
								<select name="gaugeTooltipDelay" class="button">
									<option value="0" <?php if($gaugeTooltipDelay=="0"){echo "selected";}?>>No delay</option>
									<option value="2" <?php if($gaugeTooltipDelay=="2"){echo "selected";}?>>Fast</option>
									<option value="7" <?php if($gaugeTooltipDelay=="7"){echo "selected";}?>>Normal</option>
									<option value="10" <?php if($gaugeTooltipDelay=="10"){echo "selected";}?>>Slow</option>
									<option value="15" <?php if($gaugeTooltipDelay=="15"){echo "selected";}?>>Very slow</option>
								</select>
								&nbsp;if tooltips enabled, specify the delay for the tooltip (the interval between the moment you hover over the gauge and the moment the tooltip is displayed)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Tooltip fade-in speed
							</td>
							<td style="text-align:left">
								<select name="gaugeTooltipFadeInSpeed" class="button">
									<option value="0" <?php if($gaugeTooltipFadeInSpeed=="0"){echo "selected";}?>>No delay</option>
									<option value="200" <?php if($gaugeTooltipFadeInSpeed=="200"){echo "selected";}?>>Fast</option>
									<option value="400" <?php if($gaugeTooltipFadeInSpeed=="400"){echo "selected";}?>>Normal</option>
									<option value="800" <?php if($gaugeTooltipFadeInSpeed=="800"){echo "selected";}?>>Slow</option>
									<option value="1000" <?php if($gaugeTooltipFadeInSpeed=="1000"){echo "selected";}?>>Very slow</option>
								</select>
								&nbsp;specify the speed of the fade in effect for showing the tooltip (when using mouse over effect)
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Tooltip fade-out speed
							</td>
							<td style="text-align:left">
								<select name="gaugeTooltipFadeOutSpeed" class="button">
									<option value="0" <?php if($gaugeTooltipFadeOutSpeed=="0"){echo "selected";}?>>No delay</option>
									<option value="200" <?php if($gaugeTooltipFadeOutSpeed=="200"){echo "selected";}?>>Fast</option>
									<option value="400" <?php if($gaugeTooltipFadeOutSpeed=="400"){echo "selected";}?>>Normal</option>
									<option value="800" <?php if($gaugeTooltipFadeOutSpeed=="800"){echo "selected";}?>>Slow</option>
									<option value="1000" <?php if($gaugeTooltipFadeOutSpeed=="1000"){echo "selected";}?>>Very slow</option>
								</select>
								&nbsp;specify the speed of the fade out effect for showing the tooltip (when using mouse over effect)
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Limits</h3>
					<p>
						Each gauge has a minimum and maximum value and this value will obviously also depend on the units selected. Specify the limits below based on what can be expected at your location. Also note that the values will be rounded to make them nicer - for example, if you set your temperature maximum to 38&deg;C, on the actual gauge you would see 40 (it will never be rounded down, so you will always see at least the interval you specified). Tip: Set Rain and RainRate max values to 10 (mm) or 4 (inch). The scale will expand automatic when needed and so you will have the maximum resolution.
					</p>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left;vertical-align:top">
								<span class="mticon-temp" style="font-size:2em"></span>
							</td>
							<td style="text-align:left">
								<table style="width:98%;margin:0 auto;table-layout: fixed;">
									<tr>
										<td>
											Min<br />(&deg;C)<br />
											<select name="gaugeLimitTempCMin" class="button">
												<option value="10" <?php if($gaugeLimitTempCMin=="10"){echo "selected";}?>>10</option>
												<option value="0" <?php if($gaugeLimitTempCMin=="0"){echo "selected";}?>>0</option>
												<option value="-10" <?php if($gaugeLimitTempCMin=="-10"){echo "selected";}?>>-10</option>
												<option value="-20" <?php if($gaugeLimitTempCMin=="-20"){echo "selected";}?>>-20</option>
												<option value="-30" <?php if($gaugeLimitTempCMin=="-30"){echo "selected";}?>>-30</option>
												<option value="-40" <?php if($gaugeLimitTempCMin=="-40"){echo "selected";}?>>-40</option>
												<option value="-50" <?php if($gaugeLimitTempCMin=="-50"){echo "selected";}?>>-50</option>
											</select>
										</td>
										<td>
											Max<br />(&deg;C)<br />
											<select name="gaugeLimitTempCMax" class="button">
												<option value="10" <?php if($gaugeLimitTempCMax=="10"){echo "selected";}?>>10</option>
												<option value="20" <?php if($gaugeLimitTempCMax=="20"){echo "selected";}?>>20</option>
												<option value="30" <?php if($gaugeLimitTempCMax=="30"){echo "selected";}?>>30</option>
												<option value="40" <?php if($gaugeLimitTempCMax=="40"){echo "selected";}?>>40</option>
												<option value="50" <?php if($gaugeLimitTempCMax=="50"){echo "selected";}?>>50</option>
												<option value="60" <?php if($gaugeLimitTempCMax=="60"){echo "selected";}?>>60</option>
											</select>
										</td>
										<td>
											Min<br />(&deg;F)<br />
											<select name="gaugeLimitTempFMin" class="button">
												<option value="40" <?php if($gaugeLimitTempFMin=="40"){echo "selected";}?>>40</option>
												<option value="20" <?php if($gaugeLimitTempFMin=="20"){echo "selected";}?>>20</option>
												<option value="0" <?php if($gaugeLimitTempFMin=="0"){echo "selected";}?>>0</option>
												<option value="-20" <?php if($gaugeLimitTempFMin=="-20"){echo "selected";}?>>-20</option>
												<option value="-40" <?php if($gaugeLimitTempFMin=="-40"){echo "selected";}?>>-40</option>
												<option value="-60" <?php if($gaugeLimitTempFMin=="-60"){echo "selected";}?>>-60</option>
											</select>
										</td>
										<td>
											Max<br />(&deg;F)<br />
											<select name="gaugeLimitTempFMax" class="button">
												<option value="40" <?php if($gaugeLimitTempFMax=="40"){echo "selected";}?>>40</option>
												<option value="60" <?php if($gaugeLimitTempFMax=="60"){echo "selected";}?>>60</option>
												<option value="80" <?php if($gaugeLimitTempFMax=="80"){echo "selected";}?>>80</option>
												<option value="100" <?php if($gaugeLimitTempFMax=="100"){echo "selected";}?>>100</option>
												<option value="120" <?php if($gaugeLimitTempFMax=="120"){echo "selected";}?>>120</option>
												<option value="140" <?php if($gaugeLimitTempFMax=="140"){echo "selected";}?>>140</option>
											</select>
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								<span class="mticon-pressure" style="font-size:2em"></span>
							</td>
							<td style="text-align:left">
								<table style="width:98%;margin:0 auto;table-layout: fixed;">
									<tr>
										<td>
											Min<br />(hPa)<br />
											<input name="gaugeLimitPressHPaMin" class="button" value="<?php echo $gaugeLimitPressHPaMin?>" size="5">
										</td>
										<td>
											Max<br />(hPa)<br />
											<input name="gaugeLimitPressHPaMax" class="button" value="<?php echo $gaugeLimitPressHPaMax?>" size="5">
										</td>
										<td>
											Min<br />(inHg)<br />
											<input name="gaugeLimitPressInHgMin" class="button" value="<?php echo $gaugeLimitPressInHgMin?>" size="5">
										</td>
										<td>
											Max<br />(inHg)<br />
											<input name="gaugeLimitPressInHgMax" class="button" value="<?php echo $gaugeLimitPressInHgMax?>" size="5">
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								<span class="mticon-wind" style="font-size:2em"></span>
							</td>
							<td style="text-align:left">
								<table style="width:98%;margin:0 auto;table-layout: fixed;">
									<tr>
										<td>
											Max<br />(km/h)<br />
											<input name="gaugeLimitWindKmh" class="button" value="<?php echo $gaugeLimitWindKmh?>" size="3">
										</td>
										<td>
											Max<br />(m/s)<br />
											<input name="gaugeLimitWindMs" class="button" value="<?php echo $gaugeLimitWindMs?>" size="3">
										</td>
										<td>
											Max<br />(mph)<br />
											<input name="gaugeLimitWindMph" class="button" value="<?php echo $gaugeLimitWindMph?>" size="3">
										</td>
										<td>
											Max<br />(kts)<br />
											<input name="gaugeLimitWindKts" class="button" value="<?php echo $gaugeLimitWindKts?>" size="3">
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								<span class="mticon-rain" style="font-size:2em"></span>
							</td>
							<td style="text-align:left">
								<table style="width:98%;margin:0 auto;table-layout: fixed;">
									<tr>
										<td>
											Max Daily<br />(mm)<br />
											<input name="gaugeLimitRainMM" class="button" value="<?php echo $gaugeLimitRainMM?>" size="3">
										</td>
										<td>
											Max Daily<br />(in)<br />
											<input name="gaugeLimitRainIN" class="button" value="<?php echo $gaugeLimitRainIN?>" size="3">
										</td>
										<td>
											Max rate/h<br />(mm)<br />
											<input name="gaugeLimitRainRateMM" class="button" value="<?php echo $gaugeLimitRainRateMM?>" size="3">
										</td>
										<td>
											Max rate/h<br />(in)<br />
											<input name="gaugeLimitRainRateIN" class="button" value="<?php echo $gaugeLimitRainRateIN?>" size="3">
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								<span class="mticon-sun" style="font-size:2em"></span>
							</td>
							<td style="text-align:left">
								<table style="width:98%;margin:0 auto;table-layout: fixed;">
									<tr>
										<td style="vertical-align:top">
											UV max<br />
											<input name="gaugeLimitUV" class="button" value="<?php echo $gaugeLimitUV?>" size="2">
										</td>
										<td>
											Max Daily<br />(W/m2)<br />
											<input name="gaugeLimitSolar" class="button" value="<?php echo $gaugeLimitSolar?>" size="4">
										</td>
									</tr>
								</table>
								<br />
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Gauge Details</h3>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left;vertical-align:top">
								Apparent/dewpoint
							</td>
							<td style="text-align:left">
								<select name="gaugeAInitial" class="button">
									<option value="app" <?php if($gaugeAInitial=="app"){echo "selected";}?>><?php echo lang("apparent temperature","c")?></option>
									<option value="dew" <?php if($gaugeAInitial=="dew"){echo "selected";}?>><?php echo lang("dewpoint","c")?></option>
								</select>
								&nbsp;if apparent temperature/dew point gauge enabled, set which one is shown by default
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Temperature trend
							</td>
							<td style="text-align:left">
								<select name="gaugeTTrend" class="button">
									<option value="true" <?php if($gaugeTTrend){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugeTTrend){echo "selected";}?>>No</option>
								</select>
								&nbsp;if temperature gauge enabled, specify if you want to display trend indicator
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Pressure trend
							</td>
							<td style="text-align:left">
								<select name="gaugePTrend" class="button">
									<option value="true" <?php if($gaugePTrend){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugePTrend){echo "selected";}?>>No</option>
								</select>
								&nbsp;if pressure gauge enabled, specify if you want to display trend indicator
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								UV decimals
							</td>
							<td style="text-align:left">
								<select name="gaugeUVDecimals" class="button">
									<option value="true" <?php if($gaugeUVDecimals){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugeUVDecimals){echo "selected";}?>>No</option>
								</select>
								&nbsp;if UV gauge enabled, specify if you want to show decimal values
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Pointer speed
							</td>
							<td style="text-align:left">
								<select name="gaugePointerSpeed" class="button">
									<option value="1" <?php if($gaugePointerSpeed=="1"){echo "selected";}?>>Very fast</option>
									<option value="2" <?php if($gaugePointerSpeed=="2"){echo "selected";}?>>Fast</option>
									<option value="4" <?php if($gaugePointerSpeed=="4"){echo "selected";}?>>Normal</option>
									<option value="6" <?php if($gaugePointerSpeed=="6"){echo "selected";}?>>Slow</option>
									<option value="8" <?php if($gaugePointerSpeed=="8"){echo "selected";}?>>Very slow</option>
								</select>
								&nbsp;select speed of the pointer movement
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<h3>Design</h3>
					<p>
						Now you can specify the look and feel of the gauges.
					</p>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td style="text-align:left;vertical-align:top">
								Size
							</td>
							<td style="text-align:left">
								<table>
									<tr>
										<td>
											<div id="gaugeSizeSlider" style="margin-right:30px"></div>
										</td>
										<td>
											<div id="gaugeSizeExample"></div>
											<input name="gaugeSize" type="hidden" value="<?php echo $gaugeSize?>" id="gaugeSize">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:left;width:200px;vertical-align:top">
								Gauge shadow
							</td>
							<td style="text-align:left;vertical-align:top">
								<table style="min-width:50%;table-layout:fixed">
									<tr>
										<td style="vertical-align:middle">
											<select name="gaugeShadow" class="button">
												<option value="true" <?php if($gaugeShadow){echo "selected";}?>>Yes</option>
												<option value="false" <?php if(!$gaugeShadow){echo "selected";}?>>No</option>
											</select>
										</td>
										<td style="vertical-align:top">
											Shadow color<br>
											<div id="gaugeShadowColorSelector" style="display:inline-block;background:<?php echo $gaugeShadowColor?>" class="colorPickers"></div><input type="hidden" name="gaugeShadowColor" id="gaugeShadowColor" value="<?php echo $gaugeShadowColor?>">
										</td>
										<td style="vertical-align:top">
											Shadow transparency<br>
											<select name="gaugeShadowOpacity" class="button">
												<option value="0.1" <?php if($gaugeShadowOpacity==0.1){echo "selected";}?>>10%</option>
												<option value="0.2" <?php if($gaugeShadowOpacity==0.2){echo "selected";}?>>20%</option>
												<option value="0.3" <?php if($gaugeShadowOpacity==0.3){echo "selected";}?>>30%</option>
												<option value="0.4" <?php if($gaugeShadowOpacity==0.4){echo "selected";}?>>40%</option>
												<option value="0.5" <?php if($gaugeShadowOpacity==0.5){echo "selected";}?>>50%</option>
												<option value="0.6" <?php if($gaugeShadowOpacity==0.6){echo "selected";}?>>60%</option>
												<option value="0.7" <?php if($gaugeShadowOpacity==0.7){echo "selected";}?>>70%</option>
												<option value="0.8" <?php if($gaugeShadowOpacity==0.8){echo "selected";}?>>80%</option>
												<option value="0.9" <?php if($gaugeShadowOpacity==0.9){echo "selected";}?>>90%</option>
												<option value="1" <?php if($gaugeShadowOpacity==1){echo "selected";}?>>100%</option>
											</select>
										</td>
										<td style="vertical-align:top">
											Shadow size<br>
											<select name="gaugeShadowSize" class="button">
												<option value="0.005" <?php if($gaugeShadowSize==0.005){echo "selected";}?>>Small</option>
												<option value="0.01" <?php if($gaugeShadowSize==0.01){echo "selected";}?>>Normal</option>
												<option value="0.015" <?php if($gaugeShadowSize==0.015){echo "selected";}?>>Big</option>
											</select>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:left;width:200px;vertical-align:top">
								Min/max range highlight
							</td>
							<td style="text-align:left;vertical-align:top">
								<table>
									<tr>
										<td style="vertical-align:top">
											Color<br>
											<div id="gaugeMinMaxColorSelector" style="display:inline-block;background:<?php echo $gaugeMinMaxColor?>" class="colorPickers"></div><input type="hidden" name="gaugeMinMaxColor" id="gaugeMinMaxColor" value="<?php echo $gaugeMinMaxColor?>">
										</td>
										<td style="vertical-align:top">
											Transparency<br>
											<select name="gaugeMinMaxOpacity" class="button">
												<option value="0.0" <?php if($gaugeMinMaxOpacity==0.0){echo "selected";}?>>0% (disabled)</option>
												<option value="0.1" <?php if($gaugeMinMaxOpacity==0.1){echo "selected";}?>>10%</option>
												<option value="0.2" <?php if($gaugeMinMaxOpacity==0.2){echo "selected";}?>>20%</option>
												<option value="0.3" <?php if($gaugeMinMaxOpacity==0.3){echo "selected";}?>>30%</option>
												<option value="0.4" <?php if($gaugeMinMaxOpacity==0.4){echo "selected";}?>>40%</option>
												<option value="0.5" <?php if($gaugeMinMaxOpacity==0.5){echo "selected";}?>>50%</option>
												<option value="0.6" <?php if($gaugeMinMaxOpacity==0.6){echo "selected";}?>>60%</option>
												<option value="0.7" <?php if($gaugeMinMaxOpacity==0.7){echo "selected";}?>>70%</option>
												<option value="0.8" <?php if($gaugeMinMaxOpacity==0.8){echo "selected";}?>>80%</option>
												<option value="0.9" <?php if($gaugeMinMaxOpacity==0.9){echo "selected";}?>>90%</option>
												<option value="1" <?php if($gaugeMinMaxOpacity==1){echo "selected";}?>>100%</option>
											</select>
										</td>
										<td style="padding-left:10px;text-align:left">
											the gauge will highlight the interval of today's min/max value. If you want to disable the highlight set the transparency to 0%. Click the color box and use the color picker to select the color you want for the highlighted area.
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:left;width:200px;vertical-align:top">
								Wind speed range highlight
							</td>
							<td style="text-align:left;vertical-align:top">
								<table style="min-width:50%;table-layout:fixed">
									<tr>
										<td style="vertical-align:top">
											Color<br>
											<div id="gaugeWindRangeColorSelector" style="display:inline-block;background:<?php echo $gaugeMinMaxColor?>" class="colorPickers"></div><input type="hidden" name="gaugeWindRangeColor" id="gaugeWindRangeColor" value="<?php echo $gaugeMinMaxColor?>">
										</td>
										<td style="vertical-align:top">
											Transparency<br>
											<select name="gaugeWindRangeOpacity" class="button">
												<option value="0.0" <?php if($gaugeWindRangeOpacity==0.0){echo "selected";}?>>0% (disabled)</option>
												<option value="0.1" <?php if($gaugeWindRangeOpacity==0.1){echo "selected";}?>>10%</option>
												<option value="0.2" <?php if($gaugeWindRangeOpacity==0.2){echo "selected";}?>>20%</option>
												<option value="0.3" <?php if($gaugeWindRangeOpacity==0.3){echo "selected";}?>>30%</option>
												<option value="0.4" <?php if($gaugeWindRangeOpacity==0.4){echo "selected";}?>>40%</option>
												<option value="0.5" <?php if($gaugeWindRangeOpacity==0.5){echo "selected";}?>>50%</option>
												<option value="0.6" <?php if($gaugeWindRangeOpacity==0.6){echo "selected";}?>>60%</option>
												<option value="0.7" <?php if($gaugeWindRangeOpacity==0.7){echo "selected";}?>>70%</option>
												<option value="0.8" <?php if($gaugeWindRangeOpacity==0.8){echo "selected";}?>>80%</option>
												<option value="0.9" <?php if($gaugeWindRangeOpacity==0.9){echo "selected";}?>>90%</option>
												<option value="1" <?php if($gaugeWindRangeOpacity==1){echo "selected";}?>>100%</option>
											</select>
										</td>
										<td style="padding-left:10px;text-align:left">
											the gauge will highlight the interval of today's min/max value. If you want to disable the highlight set the transparency to 0%. Click the color box and use the color picker to select the color you want for the highlighted area.
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Rain gauge bar color
							</td>
							<td style="text-align:left">
								<select name="gaugeRainColor" class="button" id="gaugeRainColor">
									<option value="BLUE" <?php if($gaugeRainColor=="BLUE"){echo "selected";}?>>Blue</option>
									<option value="BLUE2" <?php if($gaugeRainColor=="BLUE2"){echo "selected";}?>>Blue 2</option>
									<option value="BLUE3" <?php if($gaugeRainColor=="BLUE3"){echo "selected";}?>>Blue 3</option>
									<option value="BLUE4" <?php if($gaugeRainColor=="BLUE4"){echo "selected";}?>>Blue 4</option>
									<option value="BLUE5" <?php if($gaugeRainColor=="BLUE5"){echo "selected";}?>>Blue 5</option>
									<option value="RED" <?php if($gaugeRainColor=="RED"){echo "selected";}?>>Red</option>
									<option value="GREEN" <?php if($gaugeRainColor=="GREEN"){echo "selected";}?>>Green</option>
									<option value="ORANGE" <?php if($gaugeRainColor=="ORANGE"){echo "selected";}?>>Orange</option>
									<option value="YELLOW" <?php if($gaugeRainColor=="YELLOW"){echo "selected";}?>>Yellow</option>
									<option value="CYAN" <?php if($gaugeRainColor=="CYAN"){echo "selected";}?>>Cyan</option>
									<option value="MAGENTA" <?php if($gaugeRainColor=="MAGENTA"){echo "selected";}?>>Magenta</option>
									<option value="WHITE" <?php if($gaugeRainColor=="WHITE"){echo "selected";}?>>White</option>
									<option value="GRAY" <?php if($gaugeRainColor=="GRAY"){echo "selected";}?>>Gray</option>
									<option value="BLACK" <?php if($gaugeRainColor=="BLACK"){echo "selected";}?>>Black</option>
									<option value="RAITH" <?php if($gaugeRainColor=="RAITH"){echo "selected";}?>>Raith</option>
									<option value="GREEN_LCD" <?php if($gaugeRainColor=="GREEN_LCD"){echo "selected";}?>>Bright green</option>
									<option value="JUG_GREEN" <?php if($gaugeRainColor=="JUG_GREEN"){echo "selected";}?>>Green 2</option>
								</select>
								<br /><br />
								<div id="rainGaugeColorsExample" style="width:50%;height:15px;border-radius:10px"></div>
								<br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Odometer
							</td>
							<td style="text-align:left">
								<p>
									Odometer is inserted inside the wind rose and displays the daily wind run. You can enable/disable it and also use the color pickers to specify the foreground and background of the odometer - different color is used for the decimals. The value will always be given to once decimal point so if you select for example 5 digits, you will have 4 digits and 1 decimal digit. Also specify the size of the odometer inside the gauge.
								</p>
								<table cellspacing="5" cellpadding="5">
									<tr>
										<td style="text-align:left" colspan="4">
											<select name="gaugeOdo" class="button">
												<option value="true" <?php if($gaugeOdo){echo "selected";}?>>Enable</option>
												<option value="false" <?php if(!$gaugeOdo){echo "selected";}?>>Disable</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>
											Digits
											<br />
											<select name="gaugeOdoDigits" class="button">
												<option value="3" <?php if($gaugeOdoDigits=="3"){echo "selected";}?>>3</option>
												<option value="4" <?php if($gaugeOdoDigits=="4"){echo "selected";}?>>4</option>
												<option value="5" <?php if($gaugeOdoDigits=="5"){echo "selected";}?>>5</option>
												<option value="6" <?php if($gaugeOdoDigits=="6"){echo "selected";}?>>6</option>
												<option value="7" <?php if($gaugeOdoDigits=="7"){echo "selected";}?>>7</option>
											</select>
										</td>
										<td>
											Size
											<br />
											<select name="gaugeOdoSize" class="button">
												<option value="0.06" <?php if($gaugeOdoSize=="0.06"){echo "selected";}?>>Small</option>
												<option value="0.08" <?php if($gaugeOdoSize=="0.08"){echo "selected";}?>>Normal</option>
												<option value="0.1" <?php if($gaugeOdoSize=="0.1"){echo "selected";}?>>Big</option>
											</select>
										</td>
										<td>
										</td>
										<td>
										</td>
									</tr>
									<tr>
										<td>
											Foreground
										</td>
										<td>
											Background
										</td>
										<td>
											Decimals foreground
										</td>
										<td>
											Decimals background
										</td>
									</tr>
									<tr>
										<td>
											<div id="gaugeOdoForegroundSelector" style="display:inline-block;background:<?php echo $gaugeOdoForeground?>" class="colorPickers"></div><input type="hidden" name="gaugeOdoForeground" id="gaugeOdoForeground" value="<?php echo $gaugeOdoForeground?>">
										</td>
										<td>
											<div id="gaugeOdoBackgroundSelector" style="display:inline-block;background:<?php echo $gaugeOdoBackground?>" class="colorPickers"></div><input type="hidden" name="gaugeOdoBackground" id="gaugeOdoBackground" value="<?php echo $gaugeOdoBackground?>">
										</td>
										<td>
											<div id="gaugeOdoForegroundDecimalsSelector" style="display:inline-block;background:<?php echo $gaugeOdoForegroundDecimals?>" class="colorPickers"></div><input type="hidden" name="gaugeOdoForegroundDecimals" id="gaugeOdoForegroundDecimals" value="<?php echo $gaugeOdoForegroundDecimals?>">
										</td>
										<td>
											<div id="gaugeOdoBackgroundDecimalsSelector" style="display:inline-block;background:<?php echo $gaugeOdoBackgroundDecimals?>" class="colorPickers"></div><input type="hidden" name="gaugeOdoBackgroundDecimals" id="gaugeOdoBackgroundDecimals" value="<?php echo $gaugeOdoBackgroundDecimals?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Digital font
							</td>
							<td style="text-align:left">
								<select name="gaugeDigitalFont" class="button">
									<option value="true" <?php if($gaugeDigitalFont){echo "selected";}?>>Yes</option>
									<option value="false" <?php if(!$gaugeDigitalFont){echo "selected";}?>>No</option>
								</select> &nbsp;enabling this option means the gauge values will use "digital font" (typically used on older LCD screens)
								<br /><br />
							</td>
						</tr>
						<tr>
							<td style="text-align:left;vertical-align:top">
								Knob type
							</td>
							<td style="text-align:left">
								Here you can select the knob type (the middle part of the gauge). Either select one of the predefined, or you can use your custom color.
							</td>
						</tr>
						<tr>
							<td></td>
							<td style="text-align:left">
								<table cellspacing="5" cellpadding="3" style="min-width:60%;table-layout:fixed">
									<tr>
										<td>
											Standard Silver<br /><img src="images/knobStdSilver.png" /><br /><input type="radio" name="gaugeKnobType" value="stdSilver" <?php if($gaugeKnobType=="stdSilver"){ echo "checked"; }?>/>
										</td>
										<td>
											Metal Silver<br /><img src="images/knobMetalSilver.png" /><br /><input type="radio" name="gaugeKnobType" value="metalSilver" <?php if($gaugeKnobType=="metalSilver"){ echo "checked"; }?>/>
										</td>
										<td>
											Standard Black<br /><img src="images/knobStdBlack.png" /><br /><input type="radio" name="gaugeKnobType" value="stdBlack" <?php if($gaugeKnobType=="stdBlack"){ echo "checked"; }?>/>
										</td>
										<td>
											Metal Black<br /><img src="images/knobMetalBlack.png" /><br /><input type="radio" name="gaugeKnobType" value="metalBlack" <?php if($gaugeKnobType=="metalBlack"){ echo "checked"; }?>/>
										</td>
										<td>
											Standard Brass<br /><img src="images/knobStdBrass.png" /><br /><input type="radio" name="gaugeKnobType" value="stdBrass" <?php if($gaugeKnobType=="stdBrass"){ echo "checked"; }?>/>
										</td>
										<td>
											Metal Brass<br /><img src="images/knobMetalBrass.png" /><br /><input type="radio" name="gaugeKnobType" value="metalBrass" <?php if($gaugeKnobType=="metalBrass"){ echo "checked"; }?>/>
										</td>
										<td>
											Custom<br /><input type="radio" name="gaugeKnobType" value="custom" <?php if($gaugeKnobType=="custom"){ echo "checked"; }?>/><br /><select name="knobCustomType" class="button"><option value="standard">Standard</option><option value="standard">Metal</option></select><br /><div id="gaugeKnobColor1Selector" style="display:inline-block;background:<?php echo $gaugeKnobColor1?>" class="colorPickers"></div><input type="hidden" name="gaugeKnobColor1" id="gaugeKnobColor1" value="<?php echo $gaugeKnobColor1?>"><div id="gaugeKnobColor2Selector" style="display:inline-block;background:<?php echo $gaugeKnobColor2?>" class="colorPickers"></div><input type="hidden" name="gaugeKnobColor2" id="gaugeKnobColor2" value="<?php echo $gaugeKnobColor2?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="setupSection">
					<table style="margin:0 auto;width:98%;table-layout:fixed">
						<tbody>
							<tr>
								<td>
									Frame<br />
									<select id="comboFrame" onchange="setFrameDesign(this);" name="gaugeFrameDesign" class="button">
										<option value="BLACK_METAL" <?php if($gaugeFrameDesign=="BLACK_METAL"){ echo "selected";}?>>Black metal</option>
										<option value="METAL" <?php if($gaugeFrameDesign=="METAL"){ echo "selected";}?>>Metal</option>
										<option value="SHINY_METAL" <?php if($gaugeFrameDesign=="SHINY_METAL"){ echo "selected";}?>>Shiny metal</option>
										<option value="BRASS" <?php if($gaugeFrameDesign=="BRASS"){ echo "selected";}?>>Brass</option>
										<option value="STEEL" <?php if($gaugeFrameDesign=="STEEL"){ echo "selected";}?>>Steel</option>
										<option value="CHROME" <?php if($gaugeFrameDesign=="CHROME"){ echo "selected";}?>>Chrome</option>
										<option value="GOLD" <?php if($gaugeFrameDesign=="GOLD"){ echo "selected";}?>>Gold</option>
										<option value="ANTHRACITE" <?php if($gaugeFrameDesign=="ANTHRACITE"){ echo "selected";}?>>Anthracite</option>
										<option value="TILTED_GRAY" <?php if($gaugeFrameDesign=="TILTED_GRAY"){ echo "selected";}?>>Tilted gray</option>
										<option value="TILTED_BLACK" <?php if($gaugeFrameDesign=="TILTED_BLACK"){ echo "selected";}?>>Tilted black</option>
										<option value="GLOSSY_METAL" <?php if($gaugeFrameDesign=="GLOSSY_METAL"){ echo "selected";}?>>Glossy metal</option>
									</select>
								</td>
								<td>
									Background<br />
									<select id="comboBackground" onchange="setBackgroundColor(this);" name="gaugeFrameBackground" class="button">
										<option value="DARK_GRAY" <?php if($gaugeFrameBackground=="DARK_GRAY"){ echo "selected";}?>>Dark gray</option>
										<option value="SATIN_GRAY" <?php if($gaugeFrameBackground=="SATIN_GRAY"){ echo "selected";}?>>Satin gray</option>
										<option value="LIGHT_GRAY" <?php if($gaugeFrameBackground=="LIGHT_GRAY"){ echo "selected";}?>>Light gray</option>
										<option value="WHITE" <?php if($gaugeFrameBackground=="WHITE"){ echo "selected";}?>>White</option>
										<option value="BLACK" <?php if($gaugeFrameBackground=="BLACK"){ echo "selected";}?>>Black</option>
										<option value="BEIGE" <?php if($gaugeFrameBackground=="BEIGE"){ echo "selected";}?>>Beige</option>
										<option value="BROWN" <?php if($gaugeFrameBackground=="BROWN"){ echo "selected";}?>>Brown</option>
										<option value="RED" <?php if($gaugeFrameBackground=="RED"){ echo "selected";}?>>Red</option>
										<option value="GREEN" <?php if($gaugeFrameBackground=="GREEN"){ echo "selected";}?>>Green</option>
										<option value="BLUE" <?php if($gaugeFrameBackground=="BLUE"){ echo "selected";}?>>Blue</option>
										<option value="ANTHRACITE" <?php if($gaugeFrameBackground=="ANTHRACITE"){ echo "selected";}?>>Anthracite</option>
										<option value="MUD" <?php if($gaugeFrameBackground=="MUD"){ echo "selected";}?>>Mud</option>
										<option value="PUNCHED_SHEET" <?php if($gaugeFrameBackground=="PUNCHED_SHEET"){ echo "selected";}?>>Punched sheet</option>
										<option value="CARBON" <?php if($gaugeFrameBackground=="CARBON"){ echo "selected";}?>>Carbon</option>
										<option value="STAINLESS" <?php if($gaugeFrameBackground=="STAINLESS"){ echo "selected";}?>>Stainless</option>
										<option value="BRUSHED_METAL" <?php if($gaugeFrameBackground=="BRUSHED_METAL"){ echo "selected";}?>>Brushed metal</option>
										<option value="BRUSHED_STAINLESS" <?php if($gaugeFrameBackground==""){ echo "selected";}?>>Brushed stainless</option>
										<option value="TURNED" <?php if($gaugeFrameBackground=="TURNED"){ echo "selected";}?>>Turned</option>
									</select>
								</td>
								<td>
									Pointer Color<br />
									<select id="comboPointerColor" onchange="setPointerColor(this);" name="gaugeFramePointerColor" class="button">
										<option value="RED" <?php if($gaugeFramePointerColor=="RED"){ echo "selected";}?>>Red</option>
										<option value="GREEN" <?php if($gaugeFramePointerColor=="GREEN"){ echo "selected";}?>>Green</option>
										<option value="BLUE" <?php if($gaugeFramePointerColor=="BLUE"){ echo "selected";}?>>Blue</option>
										<option value="ORANGE" <?php if($gaugeFramePointerColor=="ORANGE"){ echo "selected";}?>>Orange</option>
										<option value="YELLOW" <?php if($gaugeFramePointerColor=="YELLOW"){ echo "selected";}?>>Yellow</option>
										<option value="CYAN" <?php if($gaugeFramePointerColor=="CYAN"){ echo "selected";}?>>Cyan</option>
										<option value="MAGENTA" <?php if($gaugeFramePointerColor=="MAGENTA"){ echo "selected";}?>>Magenta</option>
										<option value="WHITE" <?php if($gaugeFramePointerColor=="WHITE"){ echo "selected";}?>>White</option>
										<option value="GRAY" <?php if($gaugeFramePointerColor=="GRAY"){ echo "selected";}?>>Gray</option>
										<option value="BLACK" <?php if($gaugeFramePointerColor=="BLACK"){ echo "selected";}?>>Black</option>
										<option value="RAITH" <?php if($gaugeFramePointerColor=="RAITH"){ echo "selected";}?>>Raith</option>
										<option value="GREEN_LCD" <?php if($gaugeFramePointerColor=="GREEN_LCD"){ echo "selected";}?>>Green LCD</option>
										<option value="JUG_GREEN" <?php if($gaugeFramePointerColor=="JUG_GREEN"){ echo "selected";}?>>JUG Green</option>
									</select>
								</td>
								<td>
									Pointer type<br />
									<select id="comboPointerType" onchange="setPointerType(this);" name="gaugeFramePointerType" class="button">
										<option value="TYPE1" <?php if($gaugeFramePointerType=="TYPE1"){ echo "selected";}?>>Type1</option>
										<option value="TYPE2" <?php if($gaugeFramePointerType=="TYPE2"){ echo "selected";}?>>Type2</option>
										<option value="TYPE3" <?php if($gaugeFramePointerType=="TYPE3"){ echo "selected";}?>>Type3</option>
										<option value="TYPE4" <?php if($gaugeFramePointerType=="TYPE4"){ echo "selected";}?>>Type4</option>
										<option value="TYPE5" <?php if($gaugeFramePointerType=="TYPE5"){ echo "selected";}?>>Type5</option>
										<option value="TYPE6" <?php if($gaugeFramePointerType=="TYPE6"){ echo "selected";}?>>Type6</option>
										<option value="TYPE7" <?php if($gaugeFramePointerType=="TYPE7"){ echo "selected";}?>>Type7</option>
										<option value="TYPE8" <?php if($gaugeFramePointerType=="TYPE8"){ echo "selected";}?>>Type8</option>
										<option value="TYPE9" <?php if($gaugeFramePointerType=="TYPE9"){ echo "selected";}?>>Type9</option>
										<option value="TYPE10" <?php if($gaugeFramePointerType=="TYPE10"){ echo "selected";}?>>Type10</option>
										<option value="TYPE11" <?php if($gaugeFramePointerType=="TYPE11"){ echo "selected";}?>>Type11</option>
										<option value="TYPE12" <?php if($gaugeFramePointerType=="TYPE12"){ echo "selected";}?>>Type12</option>
										<option value="TYPE13" <?php if($gaugeFramePointerType=="TYPE13"){ echo "selected";}?>>Type13</option>
										<option value="TYPE14" <?php if($gaugeFramePointerType=="TYPE14"){ echo "selected";}?>>Type14</option>
										<option value="TYPE15" <?php if($gaugeFramePointerType=="TYPE15"){ echo "selected";}?>>Type15</option>
										<option value="TYPE16" <?php if($gaugeFramePointerType=="TYPE16"){ echo "selected";}?>>Type16</option>
									</select>
								</td>
								<td>
									LCD color<br />
									<select id="comboLcdColor" onchange="setLcdColor(this);" name="gaugeFrameLCDColor" class="button">
										<option value="BEIGE" <?php if($gaugeFrameLCDColor=="BEIGE"){ echo "selected";}?>>Beige</option>
										<option value="BLUE" <?php if($gaugeFrameLCDColor=="BLUE"){ echo "selected";}?>>Blue</option>
										<option value="ORANGE" <?php if($gaugeFrameLCDColor=="ORANGE"){ echo "selected";}?>>Orange</option>
										<option value="RED" <?php if($gaugeFrameLCDColor=="RED"){ echo "selected";}?>>Red</option>
										<option value="YELLOW" <?php if($gaugeFrameLCDColor=="YELLOW"){ echo "selected";}?>>Yellow</option>
										<option value="WHITE" <?php if($gaugeFrameLCDColor=="WHITE"){ echo "selected";}?>>White</option>
										<option value="GRAY" <?php if($gaugeFrameLCDColor=="GRAY"){ echo "selected";}?>>Gray</option>
										<option value="BLACK" <?php if($gaugeFrameLCDColor=="BLACK"){ echo "selected";}?>>Black</option>
										<option value="GREEN" <?php if($gaugeFrameLCDColor=="GREEN"){ echo "selected";}?>>Green</option>
										<option value="BLUE2" <?php if($gaugeFrameLCDColor=="BLUE2"){ echo "selected";}?>>Blue2</option>
										<option value="BLUE_BLACK" <?php if($gaugeFrameLCDColor=="BLUE_BLACK"){ echo "selected";}?>>Blue / Black</option>
										<option value="BLUE_DARKBLUE" <?php if($gaugeFrameLCDColor=="BLUE_DARKBLUE"){ echo "selected";}?>>Blue / Dark blue</option>
										<option value="BLUE_GRAY" <?php if($gaugeFrameLCDColor=="BLUE_GRAY"){ echo "selected";}?>>Blue / Gray</option>
										<option value="STANDARD" <?php if($gaugeFrameLCDColor=="STANDARD"){ echo "selected";}?>>Standard</option>
										<option value="STANDARD_GREEN" <?php if($gaugeFrameLCDColor=="STANDARD_GREEN"){ echo "selected";}?>>Standard green</option>
										<option value="BLUE_BLUE" <?php if($gaugeFrameLCDColor=="BLUE_BLUE"){ echo "selected";}?>>Blue / Blue</option>
										<option value="RED_DARKRED" <?php if($gaugeFrameLCDColor=="RED_DARKRED"){ echo "selected";}?>>Red / DarkRed</option>
										<option value="DARKBLUE" <?php if($gaugeFrameLCDColor=="DARKBLUE"){ echo "selected";}?>>Dark blue</option>
										<option value="LILA" <?php if($gaugeFrameLCDColor=="LILA"){ echo "selected";}?>>Lila</option>
										<option value="BLACKRED" <?php if($gaugeFrameLCDColor=="BLACKRED"){ echo "selected";}?>>Black / Red</option>
										<option value="DARKGREEN" <?php if($gaugeFrameLCDColor=="DARKGREEN"){ echo "selected";}?>>Dark green</option>
										<option value="AMBER" <?php if($gaugeFrameLCDColor=="AMBER"){ echo "selected";}?>>Amber</option>
										<option value="LIGHTBLUE" <?php if($gaugeFrameLCDColor=="LIGHTBLUE"){ echo "selected";}?>>Light blue</option>
									</select>
								</td>
								<td>
									Foreground type<br />
									<select id="comboForeground" onchange="setForegroundType(this);" name="gaugeFrameForeground" class="button">
										<option value="TYPE1" <?php if($gaugeFrameForeground=="TYPE1"){ echo "selected";}?>>Type1</option>
										<option value="TYPE2" <?php if($gaugeFrameForeground=="TYPE2"){ echo "selected";}?>>Type2</option>
										<option value="TYPE3" <?php if($gaugeFrameForeground=="TYPE3"){ echo "selected";}?>>Type3</option>
										<option value="TYPE4" <?php if($gaugeFrameForeground=="TYPE4"){ echo "selected";}?>>Type4</option>
										<option value="TYPE5" <?php if($gaugeFrameForeground=="TYPE5"){ echo "selected";}?>>Type5</option>
									</select>
								</td>
							</tr>
						</table>
						<br />
						<table style="width:98%;margin: 0 auto">
							<tr>
								<td width="100%">
									<canvas id="canvasRadial1" width="201" height="201">No canvas in your browser...sorry...</canvas>
									<canvas id="canvasRadial2" width="201" height="201"></canvas>
									<canvas id="canvasRadial3" width="201" height="201"></canvas>
									<canvas id="canvasRadial4" width="201" height="201"></canvas>
									<br />
									<canvas id="canvasRadial5" width="201" height="201"></canvas>
									<canvas id="canvasRadial6" width="201" height="201"></canvas>
									<canvas id="canvasRadial7" width="201" height="201"></canvas>
									<canvas id="canvasRadial8" width="201" height="201" ></canvas>
								</td>
							</tr>
						</tbody>
					</table>
				</div>

				<br>
				<div style="width:50%;text-align:center;margin:0 auto">
					<input type="submit" value="Save" class="button2">
				</div>
				<br><br>
			</form>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$(document).ready(
			function(){
				$("#sortableList").sortable({
					stop: function(){
						newOrder = ( $("#sortableList").sortable( "toArray" ));
						newOrderFinal = encodeURI(newOrder.join(";"));
						$("#gaugeOrder").val(newOrderFinal);
					}
				});
				$("#sortableList").disableSelection();
				$('#gaugeShadowColorSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeShadowColor").val($("#gaugeShadowColorSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeShadowColor").val($("#gaugeShadowColorSelector").css('backgroundColor'));
				});

				$('#gaugeMinMaxColorSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeMinMaxColor").val($("#gaugeMinMaxColorSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeMinMaxColor").val($("#gaugeMinMaxColorSelector").css('backgroundColor'));
				});

				$('#gaugeWindRangeColorSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeWindRangeColor").val($("#gaugeWindRangeColorSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeWindRangeColor").val($("#gaugeWindRangeColorSelector").css('backgroundColor'));
				});

				$('#gaugeKnobColor1Selector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeKnobColor1").val($("#gaugeKnobColor1Selector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeKnobColor1").val($("#gaugeKnobColor1Selector").css('backgroundColor'));
				});

				$('#gaugeKnobColor2Selector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeKnobColor2").val($("#gaugeKnobColor2Selector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeKnobColor2").val($("#gaugeKnobColor2Selector").css('backgroundColor'));
				});

				$('#gaugeOdoForegroundSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeOdoForeground").val($("#gaugeOdoForegroundSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeOdoForeground").val($("#gaugeOdoForegroundSelector").css('backgroundColor'));
				});

				$('#gaugeOdoBackgroundSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeOdoBackground").val($("#gaugeOdoBackgroundSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeOdoBackground").val($("#gaugeOdoBackgroundSelector").css('backgroundColor'));
				});

				$('#gaugeOdoForegroundDecimalsSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeOdoForegroundDecimals").val($("#gaugeOdoForegroundDecimalsSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeOdoForegroundDecimals").val($("#gaugeOdoForegroundDecimalsSelector").css('backgroundColor'));
				});

				$('#gaugeOdoBackgroundDecimalsSelector').colpick({
					layout:'hex',
					submit:0,
					colorScheme:'dark',
					onChange:function(hsb,hex,rgb,el,bySetColor) {
						$(el).css('background','#'+hex);
						if(!bySetColor) $(el).val(hex);
						$("#gaugeOdoBackgroundDecimals").val($("#gaugeOdoBackgroundDecimalsSelector").css('backgroundColor'));
					}
				}).keyup(function(){
					$(this).colpickSetColor(this.value);
					$("#gaugeOdoBackgroundDecimals").val($("#gaugeOdoBackgroundDecimalsSelector").css('backgroundColor'));
				});

				gaugeSliderSizeConfig = {
					min: 100,
					max: 500,
					orientation: "vertical",
					value: <?php echo $gaugeSize?>,
					step: 1,
					slide: function( event, ui ) {
						sizeSelected = ui.value;
						$("#gaugeSizeExample").css("width",sizeSelected);
						$("#gaugeSizeExample").css("height",sizeSelected);
						$("#gaugeSize").val(sizeSelected);
					},
				};
				$('#gaugeSizeSlider').slider(gaugeSliderSizeConfig);

				$("#rainGaugeColorsExample").addClass("rainGauge<?php echo $gaugeRainColor?>");
				$("#gaugeRainColor").change(function(){
					color = $("#gaugeRainColor").val();
					$("#rainGaugeColorsExample").removeAttr('class');
					$("#rainGaugeColorsExample").addClass("rainGauge" + color);
				})


				init();

				$("#comboFrame").change();
				$("#comboBackground").change();
				$("#comboPointerColor").change();
				$("#comboPointerType").change();
				$("#comboLcdColor").change();
				$("#comboForeground").change();
			});
		</script>
		<script src="scripts/designOptions.js"></script>
	</body>
</html>
