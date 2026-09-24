<?php

	# 		Station Data
	# 		Namespace:		stationData
	#		Meteotemplate Block

	# 		v1.1 - Jan 29, 2016
	#			- added responsiveness
	#			- bug fixes
	# 		v2.0 - May 23, 2016
	#			- optimized db loading
	#			- added dates and times
	#			- design tweaks
	#			- CSS loading spinner bug fix
	# 		v2.1 - May 30, 2016
	#			- bug fix - max solar radiation time
	# 		v3.0 - Aug 15, 2016
	#			- added last 365 days option
	#			- CSS tweaks
	#		v3.1 - Dec 16, 2016
	#			- correct unit formatting
	#			- optimization
	#		v3.2 - Jan 7, 2017
	#			- alltime rain changed to annual average rather than overall sum
	#		v4.0 - Jan 25, 2017
	#			- added 1h interval
	#			- added possibility to show/hide block heading
	#			- added possibility to set default interval
	#			- CSS tweaks
	#		v4.1 - Feb 10, 2017
	#			- minor language tweaks
	#		v4.2 - Feb 14, 2017
	#			- minor language tweaks
	#		v5.0 - Feb 22, 2017
	#			- added Beaufort divs
	#		v5.1 - Feb 23, 2017
	#			- added possibility to enable/disable Beuafort separately for wind speed and wind gust
	#		v6.0 - Feb 25, 2017
	#			- added more information for precipitation
	#		v7.0 - Mar 9, 2017
	#			- added max/day dates
	#		v7.1 - Apr 7, 2017
	#			- fix: rain in last 24h
	#			- conversion to SVG icons
	#		v7.2 - Apr 9, 2017
	#			- fix: rain in last 1h
	#		v8.0 - Jul 19, 2017
	#			- added current/avg option
	# 		v8.1 - Jul 21, 2017
	# 			- auto station icon selection
	# 			- optimization
	# 		v8.2 - Jul 27, 2017
	# 			- design tweaks
	# 		v9.0 - Aug 22, 2017
	# 			- added rain rate
	# 		v10.0 - Sep 18, 2017
	# 			- added last 7 days option

	$thisBlockVersion = "10.0";

	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$blockStartTime = $time;

	$errorLog[] = array("","%c Loading Block Station Data ".$thisBlockVersion."','font-weight:bold;color:green");

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

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$language = loadLangs();

?>
	<style>
		.stationDataIcon{
			max-width: 30px;
			width: 100%;
			min-width:10px;
			font-size: 1.9em;
		}
		#stationDataTable td{
			padding:5px;
		}
		#stationDataBlock{
			padding-top:0px;
		}
		#recordsHeading{
			font-variant: small-caps;
			font-size: 1.3em;
			font-weight:bold;
		}
		.stationDataBeaufortW{
			width: 30%;
			margin: 0 auto;
			margin-top:2px;
			border-radius: 5px;
			font-size: 0.8em;
			font-weight:bold;
			<?php 
				if(!$stationDataShowBftW){
			?>
 display:none;
			<?php
				}
			?>
		}
		.stationDataBeaufortG{
			width: 30%;
			margin: 0 auto;
			margin-top:2px;
			border-radius: 5px;
			font-size: 0.8em;
			font-weight:bold;
			<?php 
				if(!$stationDataShowBftG){
			?>
 display:none;
			<?php
				}
			?>
		}
	</style>
	<table style="width:100%;margin:0 auto;border-spacing:0px" id="stationDataTable">
		<?php
			if($showStationDataHeading){
		?>
				<tr>
					<td>

					</td>
					<td colspan="3">
						<h2 style="margin-bottom:0px"><?php echo lang('station data','c')?></h2>
					</td>
				</tr>
		<?php
			}
		?>
		<tr>
			<td>
				<span class="mticon-<?php echo $stationIcon?>" style="font-size:1.8em"></span>
			</td>
			<td colspan="3">
				<span id="recordsHeading"><?php echo lang('today','c')?></span>
			</td>
		</tr>
		<tr>
			<td style="width:10%">
			</td>
			<td style="width:30%;font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
				<?php echo !$showNow ? lang('avgAbbr','c') : lang('now','c')?>
			</td>
			<td style="width:30%;font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
				<?php echo lang('maximumAbbr','c')?>
			</td>
			<td style="width:30%;font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
				<?php echo lang('minimumAbbr','c')?>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-temp stationDataIcon tooltip" title="<?php echo lang('temperature','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsTAvg" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsTMax" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsTMin" class="records"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-humidity stationDataIcon tooltip" title="<?php echo lang('humidity','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsHAvg" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsHMax" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsHMin" class="records"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-pressure stationDataIcon tooltip" title="<?php echo lang('pressure','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsPAvg" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsPMax" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsPMin" class="records"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-wind stationDataIcon tooltip" title="<?php echo lang('wind speed','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsWAvg" class="records"></span>
				<div id="recordsWAvgBft" class="stationDataBeaufortW"></div>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsWMax" class="records"></span>
				<div id="recordsWMaxBft" class="stationDataBeaufortW"></div>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsWMin" class="records"></span>
				<div id="recordsWMinBft" class="stationDataBeaufortW"></div>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-gust stationDataIcon tooltip" title="<?php echo lang('wind gust','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsGAvg" class="records"></span>
				<div id="recordsGAvgBft" class="stationDataBeaufortG"></div>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsGMax" class="records"></span>
				<div id="recordsGMaxBft" class="stationDataBeaufortG"></div>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
				<span id="recordsGMin" class="records"></span>
				<div id="recordsGMinBft" class="stationDataBeaufortG"></div>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-rain stationDataIcon tooltip" title="<?php echo lang('precipitation','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
				<span style="font-size:0.9em;font-variant:small-caps;font-weight:bold"><?php echo lang('total','c')?></span><br>
				<span id="recordsR" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
				<span id="recordsR2Label" style="font-size:0.9em;font-variant:small-caps;font-weight:bold"></span><br>
				<span id="recordsR2" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
				<span id="recordsR3Label" style="font-size:0.9em;font-variant:small-caps;font-weight:bold"></span><br>
				<span id="recordsR3" class="records"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-apparent stationDataIcon tooltip" title="<?php echo lang('apparent temperature','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsAAvg" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsAMax" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsAMin" class="records"></span>
			</td>
		</tr>
		<tr>
			<td>
				<span class="mticon-dewpoint stationDataIcon tooltip" title="<?php echo lang('dew point','c')?>">
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsDAvg" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsDMax" class="records"></span>
			</td>
			<td style="border-bottom:1px solid #9b9b8c">
				<span id="recordsDMin" class="records"></span>
			</td>
		</tr>
		<?php
			if($solarSensor){
		?>
				<tr>
					<td>
						<span class="mticon-sun stationDataIcon tooltip" title="<?php echo lang('solar radiation','c')?>">
					</td>
					<td >
						<span id="recordsSAvg" class="records"></span>
					</td>
					<td >
						<span id="recordsSMax" class="records"></span>
					</td>
					<td >
						<span id="recordsSMin" class="records"></span>
					</td>
				</tr>
		<?php
			}
		?>
		<tr>
			<td>
			</td>
			<td colspan="3">
				<select id="recordsSelector" class="button" style="margin-top:8px">
					<option value="today" selected>
						<?php echo lang('today','c')?>
					</option>
					<option value="yesterday">
						<?php echo lang('yesterday','c')?>
					</option>
					<option value="1h">
						1<?php echo " ".lang('hAbbr','l')?>
					</option>
					<option value="24h">
						24<?php echo " ".lang('hAbbr','l')?>
					</option>
					<option value="thisMonth">
						<?php echo lang("this month",'c');?>
					</option>
					<option value="thisYear">
						<?php echo lang("this year",'c');?>
					</option>
					<option value="last365">
						<?php echo lang("last",'c')." 365 ".lang("days",'l');?>
					</option>
					<option value="last7">
						<?php echo lang("last",'c')." 7 ".lang("days",'l');?>
					</option>
					<option value="alltime">
						<?php echo lang('all time','c')?>
					</option>
					<option value="normal">
						<?php echo date('d')." ".lang('month'.date('n'),'c')?>
					</option>
				</select>
			</td>
		</tr>
	</table>
	<script>
		stationData('<?php echo $defaultStats?>');
		function stationData(period){
			//$(".records").html("");
			$("#recordsHeading").html("<span><?php echo lang('loading','c')?>...</span>");


			$.ajax({
				url : "homepage/blocks/stationData/stationDataAjax.php?period="+period,
				dataType : 'json',
				success : function (json) {
					if(period=="today"){
						$("#recordsHeading").html("<?php echo lang('today','c')?>");
					}
					if(period=="yesterday"){
						$("#recordsHeading").html("<?php echo lang('yesterday','c')?>");
					}
					if(period=="1h"){
						$("#recordsHeading").html("1<?php echo " ".lang('hAbbr','l')?>");
					}
					if(period=="24h"){
						$("#recordsHeading").html("24<?php echo " ".lang('hAbbr','l')?>");
					}
					if(period=="thisMonth"){
						$("#recordsHeading").html("<?php echo lang("this month",'c');?>");
					}
					if(period=="thisYear"){
						$("#recordsHeading").html("<?php echo lang("this year",'c');?>");
					}
					if(period=="last365"){
						$("#recordsHeading").html("<?php echo lang("last",'c')." 365 ".lang("days",'l');?>");
					}
					if(period=="last7"){
						$("#recordsHeading").html("<?php echo lang("last",'c')." 7 ".lang("days",'l');?>");
					}
					if(period=="alltime"){
						$("#recordsHeading").html("<?php echo lang('all time','c')?>");
					}
					if(period=="normal"){
						$("#recordsHeading").html("<?php echo date('d')." ".lang('month'.date('n'),'c')?>");
					}
					$("#recordsTAvg").html("<strong>"+json['avgT']+"</strong>");
					$("#recordsTMax").html("<strong>"+json['maxT']+"</strong><br><span style='font-size:0.8em'>"+json['maxTtime']+"</span>");
					$("#recordsTMin").html("<strong>"+json['minT']+"</strong><br><span style='font-size:0.8em'>"+json['minTtime']+"</span>");

					$("#recordsHAvg").html("<strong>"+json['avgH']+"</strong>");
					$("#recordsHMax").html("<strong>"+json['maxH']+"</strong><br><span style='font-size:0.8em'>"+json['maxHtime']+"</span>");
					$("#recordsHMin").html("<strong>"+json['minH']+"</strong><br><span style='font-size:0.8em'>"+json['minHtime']+"</span>");

					$("#recordsPAvg").html("<strong>"+json['avgP']+"</strong>");
					$("#recordsPMax").html("<strong>"+json['maxP']+"</strong><br><span style='font-size:0.8em'>"+json['maxPtime']+"</span>");
					$("#recordsPMin").html("<strong>"+json['minP']+"</strong><br><span style='font-size:0.8em'>"+json['minPtime']+"</span>");

					$("#recordsWAvg").html("<strong>"+json['avgW']+"</strong>");
					$("#recordsWAvgBft").html(json['avgWBft']);
					$("#recordsWAvgBft").css("background",json['avgWBftBg']);
					$("#recordsWAvgBft").css("color",json['avgWBftColor']);
					$("#recordsWMax").html("<strong>"+json['maxW']+"</strong><br><span style='font-size:0.8em'>"+json['maxWtime']+"</span>");
					$("#recordsWMaxBft").html(json['maxWBft']);
					$("#recordsWMaxBft").css("background",json['maxWBftBg']);
					$("#recordsWMaxBft").css("color",json['maxWBftColor']);
					$("#recordsWMin").html(json['minW']);
					$("#recordsWMinBft").html(json['minWBft']);
					$("#recordsWMinBft").css("background",json['minWBftBg']);
					$("#recordsWMinBft").css("color",json['minWBftColor']);

					$("#recordsGAvg").html("<strong>"+json['avgG']+"</strong>");
					$("#recordsGAvgBft").html(json['avgGBft']);
					$("#recordsGAvgBft").css("background",json['avgGBftBg']);
					$("#recordsGAvgBft").css("color",json['avgGBftColor']);
					$("#recordsGMax").html("<strong>"+json['maxG']+"</strong><br><span style='font-size:0.8em'>"+json['maxGtime']+"</span>");
					$("#recordsGMaxBft").html(json['maxGBft']);
					$("#recordsGMaxBft").css("background",json['maxGBftBg']);
					$("#recordsGMaxBft").css("color",json['maxGBftColor']);
					$("#recordsGMin").html(json['minG']);
					$("#recordsGMinBft").html(json['minGBft']);
					$("#recordsGMinBft").css("background",json['minGBftBg']);
					$("#recordsGMinBft").css("color",json['minGBftColor']);

					$("#recordsDAvg").html("<strong>"+json['avgD']+"</strong>");
					$("#recordsDMax").html("<strong>"+json['maxD']+"</strong><br><span style='font-size:0.8em'>"+json['maxDtime']+"</span>");
					$("#recordsDMin").html("<strong>"+json['minD']+"</strong><br><span style='font-size:0.8em'>"+json['minDtime']+"</span>");

					$("#recordsAAvg").html("<strong>"+json['avgA']+"</strong>");
					$("#recordsAMax").html("<strong>"+json['maxA']+"</strong><br><span style='font-size:0.8em'>"+json['maxAtime']+"</span>");
					$("#recordsAMin").html("<strong>"+json['minA']+"</strong><br><span style='font-size:0.8em'>"+json['minAtime']+"</span>");

					$("#recordsR").html("<strong>"+json['totalR']+"</strong>");
					$("#recordsR2").html(json['R2']);
					$("#recordsR3").html(json['R3']);
					$("#recordsR2Label").text(json['R2Label']);
					$("#recordsR3Label").text(json['R3Label']);
					<?php
						if($solarSensor){
					?>
						$("#recordsSAvg").html("<strong>"+json['avgS']+"</strong>");
						$("#recordsSMax").html("<strong>"+json['maxS']+"</strong><br><span style='font-size:0.8em'>"+json['maxStime']+"</span>");
						$("#recordsSMin").html(json['minS']);
					<?php
						}
					?>
				},
			});
		}
		$("#recordsSelector").change(function(){
			period = $(this).val();
			stationData(period);
		});
		$("#recordsSelector").val('<?php echo $defaultStats?>');
	</script>

	<?php
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$blockEndTime = $time;
		$blockLoadTime = $blockEndTime - $blockStartTime;
		$blockLoadTime = round($blockLoadTime,4);
		$errorLog[] = array("","Block loaded in ".$blockLoadTime." s.");
		showLog($errorLog);
	?>
