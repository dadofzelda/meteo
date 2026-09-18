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
	#	Daily report
	#
	# 	A script which generates the daily report for user specified day.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	
	//error_reporting(E_ALL);
	
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	
	if($_GET['d']<0 || $_GET['d']>31){
		echo "Invalid date";
		die();
	}
	if($_GET['m']<1 || $_GET['m']>12){
		echo "Invalid date";
		die();
	}
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y']) || !is_numeric($_GET['m']) || !is_numeric($_GET['d'])){
		echo "Invalid date";
		die();
	}
	
	$dateY = $_GET['y'];
	$dateM = $_GET['m'];
	$dateD = $_GET['d'];
	
	$day = strtotime($dateY."-".$dateM."-".$dateD);
	
	// Sunrise/sunset
	$dateTimeZone = new DateTimeZone($stationTZ);
	$dateTime = new DateTime(($dateD."-".$dateM."-".$dateY), $dateTimeZone);
	$offset = ($dateTimeZone->getOffset($dateTime))/3600;

	$sunRise = date_sunrise($day, SUNFUNCS_RET_STRING, $stationLat, $stationLon, 90.5, $offset);
	$sunSet = date_sunset($day, SUNFUNCS_RET_STRING,$stationLat,$stationLon,90.5,$offset);
	
	$sunRiseNautical=date_sunrise($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,102,$offset);
	$sunSetNautical=date_sunset($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,102,$offset);
	$sunRiseAstronomical=date_sunrise($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,108,$offset);
	$sunSetAstronomical=date_sunset($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,108,$offset);
	$sunRiseCivil=date_sunrise($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,96,$offset);
	$sunSetCivil=date_sunset($day,SUNFUNCS_RET_STRING,$stationLat,$stationLon,96,$offset);
	
	$temporary = date_sun_info($day, $stationLat, $stationLon);
	$sunTransitTimestamp=$temporary['transit'] + ($offset*3600);
	$sunTransit=gmdate("H:i", $sunTransitTimestamp);
	
	// Moonrise/moonset
	$moonTimes = new MoonRiSet($stationLat, $stationLon, $stationTZ);
	$moonTimes->setDate($dateY, $dateM, $dateD);
	$moonRise = $moonTimes->rise["hh:mm"];
	$moonSet = $moonTimes->set["hh:mm"];
	
	// Daylength
	$sunRiseTimestamp=date_sunrise($day,SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);
	$sunSetTimestamp=date_sunset($day,SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);

	$diff = ($sunSetTimestamp-$sunRiseTimestamp)/60;
	$dayLengthHours = floor($diff/60);
	$dayLengthMins = round(($diff - ($dayLengthHours*60)),0);
	$dayLength = $dayLengthHours." ".lang("hAbbr",'')." ".$dayLengthMins." ".lang("minAbbr",'l');
	$nightLength = floor(24-$dayLengthHours-1)." ".lang("hAbbr",'')." ".(60-$dayLengthMins)." ".lang("minAbbr",'l');
	
	// Calendar functions
	$julianDate = cal_to_jd(CAL_GREGORIAN,$dateM,$dateD,$dateY);
	
	$mp = new moonPhase($day);
	$imgPhase = $mp->getPositionInCycle();
	$intervals = 118;
	$moonIcon = round(($imgPhase/(1/$intervals)));
	$moonPercentage = $mp->getPercentOfIllumination();
?>
	<table style="width:100%;margin: 0 auto;background:#<?php echo $color_schemes[$design2]['900']?>">
		<tr>
			<td style="text-align:left;padding-left:10px">
				<?php echo lang("julian date",'c')?>: <?php echo $julianDate?><br>
				<?php echo lang("day of the year",'c')?>: <?php echo date("z",strtotime($dateY."-".$dateM."-".$dateD))?><br>
			</td>
		</tr>
		<tr>
			<td style="width:100%; text-align:center">
				<table style="width:100%">
					<tr style="margin-top:20px">
						<td style="text-align:center" colspan="3">
							<span class="mticon-sun" style="font-size:2.5em;opacity:0.8"></span>
						</td>
						<td style="text-align:center" colspan="3">
							<span class="mticon-night" style="font-size:2.5em;opacity:0.8"></span>
						</td>
					</tr>
					<tr>
						<td style="text-align:center" colspan="3">
							<?php echo $dayLength?>
						</td>
						<td style="text-align:center" colspan="3">
							<?php echo $nightLength?>
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							<span class="mticon-sunrise" style="font-size:4.5em;opacity:0.8"></span>
						</td>
						<td style="text-align:center">
						</td>
						<td style="text-align:center">
							<span class="mticon-sunset" style="font-size:4.5em;opacity:0.8"></span>
						</td>
						<td style="text-align:center">
							<span class="mticon-moonrise" style="font-size:4.5em;opacity:0.8"></span>
						</td>
						<td style="text-align:center" rowspan="2">
							<div id="mainMoonImage"></div><div id="illuminatedPerc"></div>
						</td>
						<td style="text-align:center">
							<span class="mticon-moonset" style="font-size:4.5em;opacity:0.8"></span>
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							<?php echo $sunRise?>
						</td>
						<td style="text-align:center">
							<?php echo $sunTransit?>
						</td>
						<td style="text-align:center">
							<?php echo $sunSet?>
						</td>
						<td style="text-align:center">
							<?php echo $moonRise?>
						</td>
						<td style="text-align:center">
							<?php echo $moonSet?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table style="width:100%;margin-top:0px;background:#<?php echo $color_schemes[$design2]['900']?>;table-layout:fixed">
		<thead>
			<tr>
				<td colspan="2" style="text-align:center">
					<?php echo lang("astronomical","c")?>
				</td>
				<td colspan="2" style="text-align:center">
					<?php echo lang("nautical","c")?>
				</td>
				<td colspan="2" style="text-align:center">
					<?php echo lang("civil","c")?>
				</td>
				<td style="width:50%"></td>
			</tr>
			<tr>
				<td style="text-align:center">
					<span class="mticon-sunrise" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td>
					<span class="mticon-sunset" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td>
					<span class="mticon-sunrise" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td>
					<span class="mticon-sunset" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td>
					<span class="mticon-sunrise" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td>
					<span class="mticon-sunset" style="font-size:2.5em;opacity:0.8"></span>
				</td>
				<td></td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="text-align:center">
					<?php echo $sunRiseAstronomical?>
				</td>
				<td style="text-align:center">
					<?php echo $sunSetAstronomical?>
				</td>
				<td style="text-align:center">
					<?php echo $sunRiseNautical?>
				</td>
				<td style="text-align:center">
					<?php echo $sunSetNautical?>
				</td>
				<td style="text-align:center">
					<?php echo $sunRiseCivil?>
				</td>
				<td style="text-align:center">
					<?php echo $sunSetCivil?>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
				</td>
			</tr>
		</tfoot>
	</table>
	<script>
		$('#mainMoonImage').html("<img src='<?php echo $pageURL.$path?>imgs/moon/<?php echo $moonIcon?>.png' style='width:70px;opacity:0.8' id='moonImage' alt=''>");
		$('#illuminatedPerc').html('<?php echo $moonPercentage;?>');
	</script>