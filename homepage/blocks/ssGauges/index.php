<?php

include("../../../config.php");
include($baseURL."css/design.php");
include($baseURL."scripts/functions.php");
//include($baseURL."header.php");

include("../../../plugins/steelSeries/settings.php");

$language = loadLangs();

$theme = $_GET['theme'];
$bodyText = $theme=="dark" ? "white" : "black";

$gaugeSize = $_GET['size'];

include("settings.php");


?>
<!DOCTYPE html>
<html>
<head>
    <title>Gauges</title>
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/gauges-ss.css">
	<link rel="stylesheet"  href="<?php echo $pageURL.$path?>css/main.php?v=<?php echo $timestampCSS?>" media="all" title="screen">
	<link rel="stylesheet" type="text/css" href="<?php echo $pageURL.$path?>css/tooltipster.css" media="all" title="screen">
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css">
	<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/font/styles.css">
	<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/fontAwesome/css/font-awesome.min.css">
    <style>
		.ddimgtooltip{
		  z-index: 2000;
		  border-radius: 3px;
		  display: none;
		  position: absolute;
		  border: 1px solid #<?php echo $color_schemes[$design2]['200']?>!important;
		  background: #<?php echo $color_schemes[$design2]['900']?>!important;
		  color: white!important;
		  padding: 0 7px 3px 7px;
		}
		.tipinfo{
		  text-align: left;
		  padding: 3px 0 3px 2px;
		}
		.tipimg{
		  width: 438px;
		  height: 175px;
		}
		.termWindow{
			font-size: 1.3em;
			cursor: pointer;
			opacity: 0.7;
		}
		.termWindow:hover{
			opacity: 1.0;
		}
		.ui-dialog .ui-dialog-content{
			padding: 0px;
		}
		.gaugeSizeStd{
			width: <?php echo $gaugeSize?>px;
			height: <?php echo $gaugeSize?>px;
		}
		.timeValue{
			font-size:0.8em;
		}
		.actualValue{
			font-weight: bold;
			font-size: 1.2em;
		}
		<?php
			if($gaugeTooltipsTrigger=="click"){
		?>
				.gaugeSizeStd{
					cursor: pointer;
				}
		<?php
			}
		?>
		.feelLikeGaugeSelector{
			font-size: 1.5em;
			cursor: pointer;
			opacity: 0.7;
		}
		.feelLikeGaugeSelector:hover{
			opacity: 1;
		}
    </style>
</head>
<body style='background:none;overflow:hidden;color:<?php echo $bodyText?>'>
	<div style="width:98%;margin:0 auto;font-weight:bold;font-size:1.3em;font-variant:small-caps;text-align:center;position:absolute;top:10px" id="loadingDiv">
		<?php echo lang('loading','c')?>...
	</div>
	<div style="width:100%;margin;text-align:center">
		<?php 
			if($gaugeShowClock){
		?>
				<div id="gaugeSSTime" style="width:98%;margin:0 auto;text-align:center;font-weight:bold;font-size:1.5em"><span style="color:#<?php echo $color_schemes[$design]['900']?>">0:00:00</span></div>
		<?php 
			}
		?>
		<?php
			$gaugeOrderExp = explode(",",$gaugeOrder);
			foreach($gaugeOrderExp as $thisGauge){
		?>
				<?php
					if($thisGauge=="T" && $showT){
				?>
						<div class="gauge">
							<div id="tip_0">
								<canvas id="canvas_temp" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="A" && $showA){
				?>
						<div class="gauge">
							<div id="tip_1">
								<canvas id="canvas_dew" class="gaugeSizeStd"></canvas>
							</div>
							<span class="mticon-dewpoint tooltip feelLikeGaugeSelector" id="ssGaugesSetD" title="<?php echo lang('dew point','c')?>"></span>
							<span class="mticon-apparent tooltip feelLikeGaugeSelector" id="ssGaugesSetA" title="<?php echo lang('apparent temperature','c')?>"></span>
							<div style="display:none">
							<input id="rad_dew1" type="radio" name="rad_dew" value="dew" onclick="gauges.doDew(this);">
							<label id="lab_dew1" for="rad_dew1"><?php echo lang('dewpoint','c')?></label>
							<input id="rad_dew2" type="radio" name="rad_dew" value="app" checked onclick="gauges.doDew(this);">
							<label id="lab_dew2" for="rad_dew2"><?php echo lang('apparent temperature','c')?></label>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="H" && $showH){
				?>
						<div class="gauge">
							<div id="tip_4">
								<canvas id="canvas_hum" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="W" && $showW){
				?>
						<div class="gauge">
							<div id="tip_6">
								<canvas id="canvas_wind" class="gaugeSizeStd"></canvas>
							</div>
						</div>

				<?php
					}
				?>
				<?php
					if($thisGauge=="B" && $showB){
				?>
						<div id="tip_7" class="gauge">
							<canvas id="canvas_dir" class="gaugeSizeStd"></canvas>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="WindRose" && $showWindRose){
				?>
						<div id="tip_10" class="gauge">
							<canvas id="canvas_rose" class="gaugeSizeStd"></canvas>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="P" && $showP){
				?>
						<div class="gauge">
							<div id="tip_5">
								<canvas id="canvas_baro" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="R" && $showR){
				?>
						<div class="gauge">
							<div id="tip_2">
								<canvas id="canvas_rain" class="gaugeSizeStd"></canvas>
							</div>
						</div>
						<div class="gauge">
							<div id="tip_3" class="gauge">
								<canvas id="canvas_rrate" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="S" && $showS){
				?>
						<div class="gauge">
							<div id="tip_9">
								<canvas id="canvas_solar" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
				<?php
					if($thisGauge=="UV" && $showUV){
				?>
						<div class="gauge">
							<div id="tip_8" class="gauge">
								<canvas id="canvas_uv" class="gaugeSizeStd"></canvas>
							</div>
						</div>
				<?php
					}
				?>
		<?php
			}
		?>
		<div id="tip_11" class="gauge">
			<canvas id="canvas_cloud" class="gaugeSizeStd"></canvas>
		</div>
	</div>
    <script src="scripts/steelseries_tween.php"></script>
    <script src="scripts/language.php"></script>
    <script src="scripts/gauges.php"></script>

    <script src="scripts/RGraph.common.core.min.js"></script>
    <script src="scripts/RGraph.rose.min.js"></script>
	<script>
		$(document).ready(function(){
			$("#ssGaugesSetD").click(function(){
				$("#rad_dew1").click();
			});
			$("#ssGaugesSetA").click(function(){
				$("#rad_dew2").click();
			});
		})
	</script>
	<br>
		<div style='width:98%;margin:0 auto;text-align:center;font-size:1.1em;font-variant:normal;padding-top:10px;color: orange'>
	<?php echo lang('hover over the gauges to see more details', 'c') ?>
	<br><br>
	<!--
	<iframe src="https://www.lemimi.fr/template/vigilance/bandeau.php"
        title="Bandeau" width="95%" height="40">
</iframe>
-->	
</div>
</body>
</html>
