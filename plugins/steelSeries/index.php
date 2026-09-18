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
#	Version and change log
#
# 	v1.0 - Jan 4, 2017
#		- initial release
#	v1.1 - Jan 4, 2017
#		- fixed WD and Cumulus date and units
#		- fixed temperature gauge display
# 	v1.2 - Jan 9, 2017
#		- fixed zeroing of temp/dew gauge upon each update
# 	v1.3 - Jan 11, 2017
#		- fixed Cumulus wind/gust values
# 	v1.4 - Jan 14, 2017
#		- WD date parse fix
# 	v1.5 - Feb 24, 2017
#		- rain rate limit bug fix
# 	v1.6 - Mar 6, 2017
#		- fix for UV gauge decimal places
# 	v2.0 - Mar 13, 2017
#		- added support for Meteotemplate API
# 	v3.0 - Mar 16, 2017
#		- added possibility to set gauge timeout
# 	v3.1 - Apr 10, 2017
#		- bug fixes
# 	v3.2 - May 18, 2017
#		- bug fixes
# 	v4.0 - Jul 13, 2017
#		- API implementation
# 		- improved rain rate calculation
# 	v4.0a - Jul 18, 2021
# 			- fixed browser error: MIME type mismatch (X-Content-Type-Options: nosniff)
# 	v4.1 - Jul 18, 2022
# 			- fixed php 8.0 compatibility issue ( in case selected time format is am/pm )
#
############################################################################

include("../../config.php");
include($baseURL."css/design.php");
include($baseURL."header.php");

if(!file_exists("settings.php")){
	echo "Missing settings file, create one using the plugin setup.";
	die();
}
else{
	include("settings.php");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageName?></title>
    <?php metaHeader();?>
    <link rel="stylesheet" href="css/gauges-ss.css">

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
		#summary{
			margin: 0 auto;
		}
		.timeValue{
			font-size:0.8em;
		}
		.actualValue{
			font-weight: bold;
			font-size: 1.2em;
		}
		#summaryOpener{
			cursor: pointer;
			opacity: 0.7;
		}
		#summaryOpener:hover{
			opacity: 1.0;
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
    </style>
</head>
<body>
    <div id="main_top">
        <?php bodyHeader();?>
        <?php include($baseURL."menu.php");?>
    </div>
    <div id="main" style="text-align:center">
		<h1><?php echo lang('current conditions','c')?></h1>
		<?php
			if($showStatusLED || $showStatus || $showNextUpdate){
		?>
				<div class="row">
					<?php
						if($showStatusLED){
					?>
				    		<canvas id="canvas_led" style="width:25px;height:30px;padding:10px"></canvas>
					<?php
						}
					?>
					<?php
						if($showStatus){
					?>
				    		<canvas id="canvas_status" style="width:250px;height:30px;padding:10px"></canvas>
					<?php
						}
					?>
					<?php
						if($showNextUpdate){
					?>
				    		<canvas id="canvas_timer" style="width:80px;height:30px;padding:10px"></canvas>
					<?php
						}
					?>
		  		</div>
		<?php
			}
		?>
		<div style="width:98%;margin: 0 auto;">
			<span class="mticon-summary" style="font-size:2.1em" id="summaryOpener"></span>
		</div>
		<div style="width:98%;margin: 0 auto">
			<?php
				$gaugeOrderExp = explode(";",$gaugeOrder);
				foreach($gaugeOrderExp as $thisGauge){
			?>
					<?php
						if($thisGauge=="orderT" && $showT){
					?>
							<div class="gauge">
								<div id="tip_0">
									<canvas id="canvas_temp" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=T','<?php echo lang("temperature","c")?>')"></span>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderA" && $showA){
					?>
							<div class="gauge">
								<div id="tip_1">
									<canvas id="canvas_dew" class="gaugeSizeStd"></canvas>
								</div>
								<br />
								<input id="rad_dew1" type="radio" name="rad_dew" value="dew" onclick="gauges.doDew(this);">
								<label id="lab_dew1" for="rad_dew1"><?php echo lang('dewpoint','c')?></label>
								<input id="rad_dew2" type="radio" name="rad_dew" value="app" checked onclick="gauges.doDew(this);">
								<label id="lab_dew2" for="rad_dew2"><?php echo lang('apparent temperature','c')?></label>
								<br>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=D','<?php echo lang("dew point","c")?>')"></span>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderH" && $showH){
					?>
							<div class="gauge">
								<div id="tip_4">
									<canvas id="canvas_hum" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=H','<?php echo lang("humidity","c")?>')"></span>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderW" && $showW){
					?>
							<div class="gauge">
								<div id="tip_6">
									<canvas id="canvas_wind" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=W','<?php echo lang("wind speed","c")?>')"></span>
							</div>

					<?php
						}
					?>
					<?php
						if($thisGauge=="orderB" && $showB){
					?>
							<div id="tip_7" class="gauge">
							  	<canvas id="canvas_dir" class="gaugeSizeStd"></canvas>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderWindRose" && $showWindRose){
					?>
							<div id="tip_10" class="gauge">
							  	<canvas id="canvas_rose" class="gaugeSizeStd"></canvas>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderP" && $showP){
					?>
							<div class="gauge">
								<div id="tip_5">
									<canvas id="canvas_baro" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=P','<?php echo lang("pressure","c")?>')"></span>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderR" && $showR){
					?>
							<div class="gauge">
								<div id="tip_2">
									<canvas id="canvas_rain" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=R','<?php echo lang("precipitation","c")?>')"></span>
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
						if($thisGauge=="orderS" && $showS){
					?>
							<div class="gauge">
								<div id="tip_9">
									<canvas id="canvas_solar" class="gaugeSizeStd"></canvas>
								</div>
								<span class="mticon-graph termWindow" onclick="openWindow('graph.php?var=S','<?php echo lang("solar radiation","c")?>')"></span>
							</div>
					<?php
						}
					?>
					<?php
						if($thisGauge=="orderUV" && $showUV){
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
		<div id="summary">
			<h1><?php echo lang('summary','c')?></h1>
			<table cellspacing="4" cellpadding="4" class="table" style="width:98%;margin: 0 auto;table-layout:fixed;border-radius:10px">
				<thead>
					<tr>
						<th rowspan="2">

						</th>
						<th rowspan="2" style="text-align:center">
							Current
						</th>
						<th colspan="2" style="text-align:center">
							Today
						</th>
						<th colspan="2" style="text-align:center">
							Yesterday
						</th>
					</tr>
					<tr>
						<th style="text-align:center">
							Max
						</th>
						<th>
							Min
						</th>
						<th>
							Max
						</th>
						<th>
							Min
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
							<span class="mticon-temp" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentT"></div>
						</td>
						<td>
							<div id="todayMaxT"></div>
						</td>
						<td>
							<div id="todayMinT"></div>
						</td>
						<td>
							<div id="yesterdayMaxT"></div>
						</td>
						<td>
							<div id="yesterdayMinT"></div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-apparent" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentA"></div>
						</td>
						<td>
							<div id="todayMaxA"></div>
						</td>
						<td>
							<div id="todayMinA"></div>
						</td>
						<td>
							<div id="yesterdayMaxA"></div>
						</td>
						<td>
							<div id="yesterdayMinA"></div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-dewpoint" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentD"></div>
						</td>
						<td>
							<div id="todayMaxD"></div>
						</td>
						<td>
							<div id="todayMinD"></div>
						</td>
						<td>
							<div id="yesterdayMaxD"></div>
						</td>
						<td>
							<div id="yesterdayMinD"></div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-humidity" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentH"></div>
						</td>
						<td>
							<div id="todayMaxH"></div>
						</td>
						<td>
							<div id="todayMinH"></div>
						</td>
						<td>
							<div id="yesterdayMaxH"></div>
						</td>
						<td>
							<div id="yesterdayMinH"></div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-pressure" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentP"></div>
						</td>
						<td>
							<div id="todayMaxP"></div>
						</td>
						<td>
							<div id="todayMinP"></div>
						</td>
						<td>
							<div id="yesterdayMaxP"></div>
						</td>
						<td>
							<div id="yesterdayMinP"></div>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-wind" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentW"></div>
						</td>
						<td>
							<div id="todayMaxW"></div>
						</td>
						<td>
						</td>
						<td>
							<div id="yesterdayMaxW"></div>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-gust" style="font-size:1.8em"></span>
						</td>
						<td>
							<div id="currentG"></div>
						</td>
						<td>
							<div id="todayMaxG"></div>
						</td>
						<td>
						</td>
						<td>
							<div id="yesterdayMaxG"></div>
						</td>
						<td>
						</td>
					</tr>
					<tr>
						<td>
							<span class="mticon-rain" style="font-size:1.8em"></span>
						</td>
						<td>
						</td>
						<td colspan="2">
							<div id="todayR"></div>
						</td>
						<td colspan="2">
							<div id="yesterdayR"></div>
						</td>
					</tr>
					<?php
						if($solarSensor){
					?>
							<tr>
								<td>
									<span class="mticon-sun" style="font-size:1.8em"></span>
								</td>
								<td>
									<div id="currentS"></div>
								</td>
								<td>
									<div id="todayMaxS"></div>
								</td>
								<td>
								</td>
								<td>
									<div id="yesterdayMaxS"></div>
								</td>
								<td>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br />
		</div>
		<br />
		<div style="width:98%;margin: 0 auto;text-align:center;font-weight:0.8em">
			<span style="cursor:pointer;font-variant:small-caps" onclick="$('#creditsDiv').slideToggle()">Info</span>
			<div id="creditsDiv" style="width:98%;margin: 0 auto;text-align:center;display:none">
				HTML 5 Weather Gauges based on the SteelSeries gauges from HansSolo and Mark Crossley, modified for Meteotemplate (GUI setup, additional parameters, data source, graphs, summary).
			</div>
		</div>
		<br />
    </div>
    <?php include($baseURL."footer.php");?>
    <script src="scripts/steelseries_tween.php"></script>
    <script src="scripts/language.php"></script>
    <script src="scripts/gauges.php"></script>

    <script src="scripts/RGraph.common.core.min.js"></script>
    <script src="scripts/RGraph.rose.min.js"></script>
	<script>
		dialogHeight = screen.height*0.7;
		dialogWidth = screen.width*0.8;
		$("#summary").dialog({
			autoOpen: false,
			modal: true,
			height: dialogHeight,
			width: dialogWidth,
			title: '<?php echo lang('summary','c')?>',
			show: {
				effect: "fade",
				duration: 400
			},
			hide: {
				effect: "fade",
				duration: 800
			}
		});
		$("#summaryOpener").click(function(){
			$("#summary").dialog('open');
		});
		function openWindow(url,title){
			dialogHeight = screen.height*0.8;
			dialogWidth = screen.width*0.8;
			var $dialog = $('<div style="overflow:hidden"></div>')
				.html('<iframe style="border: 0px;background:#<?php echo $color_schemes[$design]['900']?> " src="' + url + '" width="100%" height="100%"></iframe>')
				.dialog({
					autoOpen: false,
					modal: true,
					height: dialogHeight,
					width: dialogWidth,
					title: title,
					show: {
						effect: "fade",
						duration: 400
					},
					hide: {
						effect: "fade",
						duration: 800
					}
				});
			$dialog.dialog('open');
		}
	</script>
</body>
</html>
