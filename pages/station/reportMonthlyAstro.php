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
	#	Monthly report
	#
	# 	A script which generates the monthly report for user specified month.
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
	include($baseURL."scripts/stats.php");
	
	$monthsArray=array(lang('january','c'),lang('february','c'),lang('march','c'),lang('april','c'),lang('may','c'),lang('june','c'),lang('july','c'),lang('august','c'),lang('september','c'),lang('october','c'),lang('november','c'),lang('december','c'));
	
	// this month
	
	$calendarString = "";
	
	// Get date
	$loadedMonth = $_GET["m"];
	$loadedYear = $_GET["y"];
	
	// validate date and prevent SQL injection
	if($loadedMonth<1 || $loadedMonth>12){
		echo "Invalid date";
		die();
	}
	if($loadedYear<1900 || $loadedYear>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($loadedMonth) || !is_numeric($loadedYear)){
		echo "Invalid date";
		die();
	}
	
	$monthDays = date("t",strtotime($loadedYear."-".$loadedMonth."-15"));
	
	
	// Sunrise/sunset
	
	
	for($i=1;$i<=$monthDays;$i++){
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($i."-".$loadedMonth."-".$loadedYear), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime))/3600;
		$day = strtotime($loadedYear."-".$loadedMonth."-".$i);
		$sunRises[$i] = date_sunrise($day, SUNFUNCS_RET_STRING, $stationLat, $stationLon, 90.5, $offset);
		$sunSets[$i] = date_sunset($day, SUNFUNCS_RET_STRING,$stationLat,$stationLon,90.5,$offset);
		
		$sunRiseTimestamp=date_sunrise($day,SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);
		$sunSetTimestamp=date_sunset($day,SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);
		
		$diff = ($sunSetTimestamp-$sunRiseTimestamp)/60;
		$dayLengthHours = floor($diff/60);
		$dayLengthMins = round(($diff - ($dayLengthHours*60)),0);
		$dayLengths[$i] = $dayLengthHours." ".lang("hAbbr",'')." ".$dayLengthMins." ".lang("minAbbr",'l');
		$nightLengths[$i] = floor(24-$dayLengthHours-1)." ".lang("hAbbr",'')." ".(60-$dayLengthMins)." ".lang("minAbbr",'l');
		
		$mp = new moonPhase($day);
		$imgPhase = $mp->getPositionInCycle();
		$intervals = 118;
		$moonIcons[$i] = round(($imgPhase/(1/$intervals)));
		$moonPercentages[$i] = $mp->getPercentOfIllumination();
	}
	

	if($firstWeekday == 1){
		$erster=date('w',mktime(0,0,0,$loadedMonth,1,$loadedYear));
		$calendarTotal=date('t',mktime(0,0,0,$loadedMonth,1,$loadedYear));
		if ($erster==0) $erster=7;
		$calendarString .=  '<table style="text-align:center;width:98%;margin:0 auto">';
		$calendarString .=  '<tr style="color:black"><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('mondayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('tuesdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('wednesdayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('thursdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('fridayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('saturdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('sundayAbbr','c').'</b></td></tr>';
		$calendarString .=  '<tr>';
		$i=1;
		while ($i<$erster) {
			$calendarString .=  '<td> </td>';
			$i++;
		}
		$i=1;
		while ($i<=$calendarTotal) {
			$rest=($i+$erster-1)%7;		
			if ($rest==6) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['800'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			} else if ($rest==0) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['900'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			} else {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['700'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img class="moonPhaseImgs" src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			}
			$calendarString .=  "</td>\n";
			if ($rest==0) $calendarString .=  "</tr>\n<tr>\n";
			$i++;
		}
		$calendarString .=  '</tr>';
		$calendarString .=  '</table>';
	}
	
	else{
		$erster=date('w',mktime(0,0,0,$loadedMonth,1,$loadedYear));
		$calendarTotal=date('t',mktime(0,0,0,$loadedMonth,1,$loadedYear));
		$calendarString .=  '<table style="text-align:center;width:98%;margin:0 auto">';
		$calendarString .=  '<tr style="color:black"><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('sundayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('mondayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('tuesdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('wednesdayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('thursdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('fridayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><b>'.lang('saturdayAbbr','c').'</b></td>';
		$calendarString .=  '<tr>';
		$i=0;
		while ($i<$erster) {
			$calendarString .=  '<td> </td>';
			$i++;
		}
		$i=1;
		while ($i<=$calendarTotal) {
			$rest=($i+$erster)%7;			
			if ($rest==0) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['900'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			} else if ($rest==1) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['800'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			} else {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['700'].';border:1px solid #'.$color_schemes[$design2]['200'].'">';
				$calendarString .=  '<span style="font-size:2em;font-weight:bold">'.$i.'</span><table style="width:100%"><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sunrise.png" style="width:20px"><br>'.$sunRises[$i].'</td><td><img src="'.$pageURL.$path.'icons/sunset.png" style="width:20px"><br>'.$sunSets[$i].'</td></tr><tr><td style="width:50%"><img src="'.$pageURL.$path.'icons/sun.png" style="width:20px"><br>'.$dayLengths[$i].'</td><td><img src="'.$pageURL.$path.'icons/moon.png" style="width:20px"><br>'.$nightLengths[$i].'</td></tr><tr><td colspan="2"><img src="'.$pageURL.$path.'imgs/moon/'.$moonIcons[$i].'.png" style="width:60px"><br>'.$moonPercentages[$i].'</td></tr></table>';
			}
			$calendarString .=  "</td>\n";
			if ($rest==0) $calendarString .=  "</tr>\n<tr>\n";
			$i++;
		}
		$calendarString .=  '</tr>';
		$calendarString .=  '</table>';
	}
	
?>
	<style>
		<?php
			if($stationLat<0){ // invert Moon image if Southern hemisphere
		?>
				.moonPhaseImgs{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
		<?php
			}
		?>	
	</style>
	<?php
		echo $calendarString;
	?>