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
	#	Astronomy calendar
	#
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

	//error_reporting(E_ALL);

	// prepare day array

	if(isset($_GET['m'])){
		$m = $_GET['m'];
	}
	else{
		$m = date("m");
	}

	if(isset($_GET['y'])){
		$y = $_GET['y'];
	}
	else{
		$y = date("Y");
	}

	$daysInMonth = date("t",strtotime($y."-".$m."-15"));

	$days = array();

	for($i=1;$i<=$daysInMonth;$i++){
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($i."-".$m."-".$y), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime))/3600;
		$days[$i]['date'] = strtotime($y."-".$m."-".$i);
		$days[$i]['sunRise'] = date_sunrise($days[$i]['date'], SUNFUNCS_RET_STRING, $stationLat, $stationLon, 90.5, $offset);
		$days[$i]['sunSet'] = date_sunset($days[$i]['date'], SUNFUNCS_RET_STRING,$stationLat,$stationLon,90.5,$offset);

		$sunRiseTimestamp=date_sunrise($days[$i]['date'],SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);
		$sunSetTimestamp=date_sunset($days[$i]['date'],SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,$offset);

		$days[$i]['sunRiseTS'] = $sunRiseTimestamp;
		$days[$i]['sunSetTS'] = $sunSetTimestamp;

		/*Nautical*/
		$days[$i]['twilightNauticalRise'] = date_sunrise($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,102,$offset);
		$days[$i]['twilightNauticalSet'] = date_sunset($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,102,$offset);
		/*Astronomical*/
		$days[$i]['twilightAstronomicalRise'] = date_sunrise($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,108,$offset);
		$days[$i]['twilightAstronomicalSet'] = date_sunset($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,108,$offset);
		/*Civil*/
		$days[$i]['twilightCivilRise'] = date_sunrise($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,96,$offset);
		$days[$i]['twilightCivilSet'] = date_sunset($days[$i]['date'],SUNFUNCS_RET_STRING,$stationLat,$stationLon,96,$offset);

		$diff = ($sunSetTimestamp-$sunRiseTimestamp)/60;
		$dayLengthHours = floor($diff/60);
		$dayLengthMins = round(($diff - ($dayLengthHours*60)),0);
		$days[$i]['dayPercentage'] = number_format(($dayLengthHours*60+$dayLengthMins)/1440*100,1,".","");
		$days[$i]['nightPercentage'] = number_format(100 - $days[$i]['dayPercentage'],1,".","");
		$days[$i]['dayLength'] = $dayLengthHours." ".lang("hAbbr",'')." ".$dayLengthMins." ".lang("minAbbr",'l');
		$days[$i]['nightLength'] = floor(24-$dayLengthHours-1)." ".lang("hAbbr",'')." ".(60-$dayLengthMins)." ".lang("minAbbr",'l');

		$mp = new moonPhase($days[$i]['date'] + 60*60*12); // get average moon phase, i.e. add 12 hours
		$imgPhase = $mp->getPositionInCycle();
		$intervals = 118;
		$days[$i]['moonIcon'] = round(($imgPhase/(1/$intervals)));
		$days[$i]['moonPercentage'] = $mp->getPercentOfIllumination();
	}


	// Moon dates
	$moonResults = array();
	$moonFile = file("files/moons.txt");
	foreach($moonFile as $moonRow){
		$moonData = explode(",",$moonRow);
		foreach($days as $key=>$day){
			$temporaryDate = $day['date'];
			if(date("Ymd",$day['date'])==date("Ymd",$moonData[5])){
				$days[$key]['moon'] = $moonData;
			}
		}
	}

	// Season dates
	$seasonResults = array();
	$seasonFile = file("files/seasons.txt");
	foreach($seasonFile as $seasonRow){
		$checkedSeasonDate = strtotime(trim($seasonRow));
		$tD = date("d",$checkedSeasonDate);
		$tM = date("m",$checkedSeasonDate);
		$tY = date("Y",$checkedSeasonDate);
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime));
		$checkedSeasonDate = $checkedSeasonDate + $offset;
		foreach($days as $key=>$day){
			$temporaryDate = $day['date'];
			if(date("Ymd",$day['date'])==date("Ymd",$checkedSeasonDate)){
				$days[$key]['season'] = $checkedSeasonDate;
			}
		}
	}

	// Lunar Eclipses
	$lunarResults = array();
	$lunarFile = file("files/lunarEclipses.txt");
	foreach($lunarFile as $lunarRow){
		$lunarData = explode(";",$lunarRow);
		$checkedLunarDate = strtotime($lunarData[1]."-".$lunarData[2]."-".$lunarData[3]." ".$lunarData[4]);
		$tD = date("d",$checkedLunarDate);
		$tM = date("m",$checkedLunarDate);
		$tY = date("Y",$checkedLunarDate);
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime));
		$checkedLunarDate = $checkedLunarDate + $offset;
		foreach($days as $key=>$day){
			$temporaryDate = $day['date'];
			if(date("Ymd",$day['date'])==date("Ymd",$checkedLunarDate)){
				$days[$key]['lunar'] = $lunarRow;
			}
		}
	}

	// Solar Eclipses
	$solarResults = array();
	$solarFile = file("files/solarEclipses.txt");
	foreach($solarFile as $solarRow){
		$solarData = explode(";",$solarRow);
		$checkedSolarDate = strtotime($solarData[2]."-".$solarData[3]."-".$solarData[4]." ".$solarData[5]);
		$tD = date("d",$checkedSolarDate);
		$tM = date("m",$checkedSolarDate);
		$tY = date("Y",$checkedSolarDate);
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime));
		$checkedSolarDate = $checkedSolarDate + $offset;
		foreach($days as $key=>$day){
			$temporaryDate = $day['date'];
			if(date("Ymd",$day['date'])==date("Ymd",$checkedSolarDate)){
				$days[$key]['solar'] = $solarRow;
			}
		}
	}

	// meteor showers
	foreach($days as $key=>$day){
		$temporaryDate = $day['date'];
		if(date("m",$temporaryDate)==1 && (date("d",$temporaryDate)==3 || date("d",$temporaryDate)==4)){
			$days[$key]['meteors'] = lang("Quadrantids",'c');
		}
		if(date("m",$temporaryDate)==4 && (date("d",$temporaryDate)==22 || date("d",$temporaryDate)==23)){
			$days[$key]['meteors'] = lang("Lyrids",'c');
		}
		if(date("m",$temporaryDate)==5 && (date("d",$temporaryDate)==6 || date("d",$temporaryDate)==7)){
			$days[$key]['meteors'] = lang("Eta Aquarids",'c');
		}
		if(date("m",$temporaryDate)==7 && (date("d",$temporaryDate)==28 || date("d",$temporaryDate)==29)){
			$days[$key]['meteors'] = lang("Delta Aquarids",'c');
		}
		if(date("m",$temporaryDate)==7 && (date("d",$temporaryDate)==27 || date("d",$temporaryDate)==28)){
			$days[$key]['meteors'] = lang("Alpha Capricornids",'c');
		}
		if(date("m",$temporaryDate)==8 && (date("d",$temporaryDate)==12 || date("d",$temporaryDate)==13)){
			$days[$key]['meteors'] = lang("Perseids",'c');
		}
		if(date("m",$temporaryDate)==10 && (date("d",$temporaryDate)==21 || date("d",$temporaryDate)==22)){
			$days[$key]['meteors'] = lang("Orionids",'c');
		}
		if(date("m",$temporaryDate)==11 && (date("d",$temporaryDate)==5 || date("d",$temporaryDate)==6)){
			$days[$key]['meteors'] = lang("South Taurids",'c');
		}
		if(date("m",$temporaryDate)==11 && (date("d",$temporaryDate)==11 || date("d",$temporaryDate)==12)){
			$days[$key]['meteors'] = lang("North Taurids",'c');
		}
		if(date("m",$temporaryDate)==11 && (date("d",$temporaryDate)==17 || date("d",$temporaryDate)==18)){
			$days[$key]['meteors'] = lang("Leonids",'c');
		}
		if(date("m",$temporaryDate)==12 && (date("d",$temporaryDate)==13 || date("d",$temporaryDate)==14)){
			$days[$key]['meteors'] = lang("Geminids",'c');
		}
		if(date("m",$temporaryDate)==12 && (date("d",$temporaryDate)==21 || date("d",$temporaryDate)==22)){
			$days[$key]['meteors'] = lang("Ursids",'c');
		}
	}

	// prepare formatted string
	foreach($days as $key=>$day){
		$morning = $day['sunRiseTS'] - strtotime(date("Y",$day['sunRiseTS'])."-".date("m",$day['sunRiseTS'])."-".date("d",$day['sunRiseTS'])." 00:00");
		$dayTime = $day['sunSetTS'] - $day['sunRiseTS'];
		$daySeconds = 60 * 60 * 24;
		$evening = $daySeconds - $dayTime - $morning;
		$morningPerc = round($morning/$daySeconds,2)*100;
		$dayPerc = round($dayTime/$daySeconds,2)*100;
		$eveningPerc = round($evening/$daySeconds,2)*100;
		$dateTimeZone = new DateTimeZone($stationTZ);
		$dateTime = new DateTime(($key."-".$m."-".$y), $dateTimeZone);
		$offset = ($dateTimeZone->getOffset($dateTime));
		$temporaryDay = strtotime($y."-".$m."-".$key);
		$currentZodiac = getZodiac($day['date']);
		if(date("L",$temporaryDay)==1){
			$remainingDays = 366 - date("z",$temporaryDay);
		}
		else{
			$remainingDays = 365 - date("z",$temporaryDay);
		}
		$str = "<table style='width:98%;margin:0 auto'>";
			$str .= "<tr>";
				$str .= "<td style='text-align:left;padding-left:3px;font-weight:bold;font-size:3em'>".$key."</td>";
				$str .= "<td style='text-align:right;padding-right:2px;vertical-align:top:font-size:0.8em;font-weight:bold'>".date("z",$temporaryDay)."<br>".$remainingDays."</td>";
			$str .= "</tr>";
			$str .= "<tr>";
				$str .= "<td colspan='2' style='text-align:right;padding-right:2px;vertical-align:top:font-size:0.8em;font-weight:bold'><img src='".$pageURL.$path."icons/zodiac/".$currentZodiac.".png' style='width:20px'></td>";
			$str .= "</tr>";
			$str .= "<tr>";
				$str .= "<td style='width:50%;text-align:center'><img src='".$pageURL.$path."icons/sunrise.png' style='width:20px'><br>".$day['sunRise']."</td>";
				$str .= "<td style='width:50%;text-align:center'><img src='".$pageURL.$path."icons/sunset.png' style='width:20px'><br>".$day['sunSet']."</td>";
			$str .= "</tr>";
			$str .= "<tr>";
				$str .= "<td style='width:50%;text-align:center'><img src='".$pageURL.$path."icons/sun.png' style='width:20px'><br>".$day['dayLength']."</td>";
				$str .= "<td style='width:50%;text-align:center'><img src='".$pageURL.$path."icons/moon.png' style='width:20px' class='moonIcon'><br>".$day['nightLength']."</td>";
			$str .= "</tr>";
			$str .= "<tr>";
				$str .= "<td colspan='2'>";
					$str .= "<table style='width:80%;margin:0 auto;margin-top:10px' cellspacing='0' cellpadding='0'>";
						$str .= "<tr>";
							$str .= "<td style='height:10px;background:black;border:1px solid #".$color_schemes[$design]['600'].";border-right:0px;width:".$morningPerc."%;border-radius:20px 0px 0px 20px'></td>";
							$str .= "<td style='height:10px;background:#4ca6ff;border:1px solid #".$color_schemes[$design]['600'].";border-right:0px;border-left:0px;width:".$dayPerc."%'></td>";
							$str .= "<td style='height:10px;background:black;border:1px solid #".$color_schemes[$design]['600'].";border-left:0px;width:".$eveningPerc."%;border-radius:0px 20px 20px 0px'></td>";
						$str .= "</tr>";
					$str .= "</table>";
				$str .= "</td>";
			$str.= "</tr>";
			$str .= "<tr>";
				$str .= "<td colspan='2' style='text-align:center'><img src='".$pageURL.$path."imgs/moon/".$day['moonIcon'].".png' style='width:60px'></td>";
			$str .= "</tr>";
			if(array_key_exists("moon",$day)){
				$phaseRaw = $day['moon'][3];
				if($phaseRaw==1){
					$phase = lang("new moon",'c');
				}
				else if($phaseRaw==2){
					$phase = lang("first quarter",'c');
				}
				else if($phaseRaw==3){
					$phase = lang("full moon",'c');
				}
				else{
					$phase = lang("last quarter",'c');
				}
				$phaseTime = date($timeFormat,$day['moon'][5]);

				$str .= "<tr>";
					$str .= "<td colspan='2' style='text-align:left;font-variant:small-caps'>".$phase." (".$phaseTime.")</td>";
				$str .= "</tr>";
			}
			if(array_key_exists("season",$day)){
				if(date("n",$day['season'])==3){
					if($stationLat>=0){
						$seasonType = lang('spring','c');
						$seasonImage = "spring";
					}
					else{
						$seasonType = lang('autumn','c');
						$seasonImage = "autumn";
					}
				}
				else if(date("n",$day['season'])==6){
					if($stationLat>=0){
						$seasonType = lang('summer','c');
						$seasonImage = "summer";
					}
					else{
						$seasonType = lang('winter','c');
						$seasonImage = "winter";
					}
				}
				else if(date("n",$day['season'])==9){
					if($stationLat>=0){
						$seasonType = lang('autumn','c');
						$seasonImage = "autumn";
					}
					else{
						$seasonType = lang('spring','c');
						$seasonImage = "spring";
					}
				}
				else{
					if($stationLat>=0){
						$seasonType = lang('winter','c');
						$seasonImage = "winter";
					}
					else{
						$seasonType = lang('summer','c');
						$seasonImage = "summer";
					}
				}
				$str .= "<tr>";
					$str .= "<td style='text-align:left;font-variant:small-caps' colspan='2'><img src='".$pageURL.$path."icons/".$seasonImage.".png' style='width:30px;padding-right:3px'>".$seasonType." (".date($timeFormat,$day['season']).")</td>";
				$str .= "</tr>";
			}
			if(array_key_exists("meteors",$day)){
				$str .= "<tr>";
					$str .= "<td style='text-align:left;font-variant:small-caps' colspan='2'><img src='".$pageURL.$path."icons/meteor.png' style='width:30px;padding-right:3px'>".$day['meteors']."</td>";
				$str .= "</tr>";
			}
			if(array_key_exists("lunar",$day)){
				$lunarRow = explode(';',$day['lunar']);
				$checkedLunarDate = strtotime($lunarRow[1]."-".$lunarRow[2]."-".$lunarRow[3]." ".$lunarRow[4]);
				$tD = date("d",$checkedLunarDate);
				$tM = date("m",$checkedLunarDate);
				$tY = date("Y",$checkedLunarDate);
				$dateTimeZone = new DateTimeZone($stationTZ);
				$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
				$offset = ($dateTimeZone->getOffset($dateTime));
				$checkedLunarDate = $checkedLunarDate + $offset;
				$dateLunar = date($timeFormat,$checkedLunarDate);
				$type = $lunarRow[8];
				if($type=="T"){
					$type = lang('total eclipse','c');
				}
				if($type=="P"){
					$type = lang('partial eclipse','c');
				}
				if($type=="N"){
					$type = lang('penumbral eclipse','c');
				}
				if($type=="Tm"){
					$type = lang('total eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
				}
				if($type=="Pm"){
					$type = lang('partial eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
				}
				if($type=="Nm"){
					$type = lang('penumbral eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
				}
				if($type=="T+"){
					$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes north of shadow axis",'c');
				}
				if($type=="T-"){
					$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes south of shadow axis",'c');
				}
				if($type=="Tx"){
					$type = lang('total eclipse','c')."<br>".lang("total penumbral lunar eclipse",'l');
				}
				if($type=="Nb"){
					$type = lang('penumbral eclipse','c')."<br>".lang("first penumbral eclipse in series",'l');
				}
				if($type=="Ne"){
					$type = lang('penumbral eclipse','c')."<br>".lang("last penumbral eclipse in series",'l');
				}
				if($type[0]=="T"){
					$typeImg = "totalL.png";
				}
				if($type[0]=="P"){
					$typeImg = "partialL.png";
				}
				if($type[0]=="N"){
					$typeImg = "penumbralL.png";
				}
				$str .= "<tr>";
					$str .= "<td style='text-align:left;font-variant:small-caps' colspan='2'><img src='".$pageURL.$path."icons/lunar.png' style='width:30px;padding-right:3px'>".$type." (".$dateLunar.")</td>";
				$str .= "</tr>";
			}
			if(array_key_exists("solar",$day)){
				$solarData = explode(';',$day['solar']);
				$checkedSolarDate = strtotime($solarData[2]."-".$solarData[3]."-".$solarData[4]." ".$solarData[5]);
				$tD = date("d",$checkedSolarDate);
				$tM = date("m",$checkedSolarDate);
				$tY = date("Y",$checkedSolarDate);
				$dateTimeZone = new DateTimeZone($stationTZ);
				$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
				$offset = ($dateTimeZone->getOffset($dateTime));
				$checkedSolarDate = $checkedSolarDate + $offset;
				$dateSolar = date($timeFormat,$checkedSolarDate);
				$type = $solarData[9];
				if($type[0]=="P"){
					$type = lang('partial eclipse','c');
					$typeImg = "partial.png";
				}
				if($type[0]=="A"){
					$type = lang('annular eclipse','c');
					$typeImg = "annular.png";
				}
				if($type[0]=="T"){
					$type = lang('total eclipse','c');
					$typeImg = "total.png";
				}
				if($type[0]=="H"){
					$type = lang('hybrid eclipse','c');
					$typeImg = "hybrid";
				}
				if(strlen($type)>1){
					if(substr($type,1,1)=="m"){
						$type = $type."<br>".lang('middle eclipse of Saros series','l');
					}
					if(substr($type,1,1)=="n"){
						$type = $type."<br>".lang('central eclipse with no northern limit','l');
					}
					if(substr($type,1,1)=="s"){
						$type = $type."<br>".lang('central eclipse with no southern limit','l');
					}
					if(substr($type,1,1)=="+"){
						$type = $type."<br>".lang("non-central eclipse with no northern limit",'l');
					}
					if(substr($type,1,1)=="-"){
						$type = $type."<br>".lang("non-central eclipse with no southern limit",'l');
					}
					if(substr($type,1,1)=="2"){
						$type = $type."<br>".lang("hybrid path begins total and ends annular",'l');
					}
					if(substr($type,1,1)=="3"){
						$type = $type."<br>".lang("hybrid path begins annular and ends total",'l');
					}
					if(substr($type,1,1)=="b"){
						$type = $type."<br>".lang("first eclipse in series",'l');
					}
					if(substr($type,1,1)=="e"){
						$type = $type."<br>".lang("last eclipse in series",'l');
					}
				}
				$durationRaw = $solarData[17];
				$durationM = substr($durationRaw,0,2);
				$durationS = substr($durationRaw,3,2);
				$duration = $durationM * 60 + $durationS;
				$str .= "<tr>";
					$str .= "<td style='text-align:left;font-variant:small-caps' colspan='2'><img src='".$pageURL.$path."icons/solar.png' style='width:30px;padding-right:3px'>".$type." (".$dateSolar.", ".$duration."s)</td>";
				$str .= "</tr>";
			}
		$str .= "</table>";

		$dayString[$key] = $str;
	}

	$calendarString = "";
	if($firstWeekday == 1){
		$first=date('w',mktime(0,0,0,$m,1,$y));
		$calendarTotal=date('t',mktime(0,0,0,$m,1,$y));
		if ($first==0) $first=7;
		$calendarString .=  '<table style="text-align:center;width:98%;margin:0 auto" id="astroTable">';
		$calendarString .=  '<tr style="color:black;font-size:1.3em;font-variant:small-caps"><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('mondayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('tuesdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('wednesdayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('thursdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('fridayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('saturdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('sundayAbbr','c').'</b></td></tr>';
		$calendarString .=  '<tr>';
		$i=1;
		while ($i<$first) {
			$calendarString .=  '<td> </td>';
			$i++;
		}
		$i=1;
		while ($i<=$calendarTotal) {
			$rest=($i+$first-1)%7;
			if ($rest==6) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['800'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $dayString[$i];
			} else if ($rest==0) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['900'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $dayString[$i];
			} else {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['700'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $dayString[$i];
			}
			$calendarString .=  "</td>\n";
			if ($rest==0) $calendarString .=  "</tr>\n<tr>\n";
			$i++;
		}
		$calendarString .=  '</tr>';
		$calendarString .=  '</table>';
	}

	else{
		$first=date('w',mktime(0,0,0,$m,1,$y));
		$calendarTotal=date('t',mktime(0,0,0,$m,1,$y));
		$calendarString .=  '<table style="text-align:center;width:98%;margin:0 auto" id="astroTable">';
		$calendarString .=  '<tr style="color:black;font-size:1.3em;font-variant:small-caps"><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('sundayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('mondayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('tuesdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('wednesdayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('thursdayAbbr','c').'</b></td>';
		$calendarString .=  '<td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('fridayAbbr','c').'</b></td><td style="background:#'.$color_schemes[$design2]['300'].'"><br><b>'.lang('saturdayAbbr','c').'</b></td>';
		$calendarString .=  '<tr>';
		$i=0;
		while ($i<$first) {
			$calendarString .=  '<td> </td>';
			$i++;
		}
		$i=1;
		while ($i<=$calendarTotal) {
			$rest=($i+$first)%7;
			if ($rest==0) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['900'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $i;
			} else if ($rest==1) {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['800'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $i;
			} else {
				$calendarString .=  '<td style="font-size:90%; text-align:center;width:14.3%;background:#'.$color_schemes[$design2]['700'].';border:1px solid #'.$color_schemes[$design2]['200'].'" class="dayClass" data-id="'.$i.'">';
				$calendarString .=  $i;
			}
			$calendarString .=  "</td>\n";
			if ($rest==0) $calendarString .=  "</tr>\n<tr>\n";
			$i++;
		}
		$calendarString .=  '</tr>';
		$calendarString .=  '</table>';
	}

	function getZodiac($date){
		$month = date("m",$date);
		$day = date("d",$date);
		if(($month == 3 && $day > 20 ) || ( $month == 4 && $day < 20)){
			$zodiac = "aries";
		}
		else if(($month == 4 && $day > 19 ) || ( $month == 5 && $day < 21)){
			$zodiac = "taurus";
		}
		else if(($month == 5 && $day > 20 ) || ( $month == 6 && $day < 21)){
			$zodiac = "gemini";
		}
		else if(($month == 6 && $day > 20 ) || ( $month == 7 && $day < 23)){
			$zodiac = "cancer";
		}
		else if(($month == 7 && $day > 22 ) || ( $month == 8 && $day < 23)){
				$zodiac = "leo";
		}
		else if(($month == 8 && $day > 22 ) || ( $month == 9 && $day < 23)){
			$zodiac = "virgo";
		}
		else if(($month == 9 && $day > 22 ) || ( $month == 10 && $day < 23)){
			$zodiac = "libra";
		}
		else if(($month == 10 && $day > 22 ) || ( $month == 11 && $day < 22)){
			$zodiac = "scorpio";
		}
		else if(($month == 11 && $day > 21 ) || ( $month == 12 && $day < 22)){
			$zodiac = "sagittarius";
		}
		else if(($month == 12 && $day > 21 ) || ( $month == 1 && $day < 20)){
			$zodiac = "capricorn";
		}
		else if(($month == 1 && $day > 19 ) || ( $month == 2 && $day < 19)){
			$zodiac = "aquarius";
		}
		else if(($month == 2 && $day > 18 ) || ( $month == 3 && $day < 21)){
			$zodiac = "pisces";
		}
		else{
			$zodiac = "";
		}
		return $zodiac;
	}

	$pdfURLMonth = "../../../pages/astronomy/astroCalendarPDF.php?y=".$y."&m=".$m;
	$pdfURLYear = "../../../pages/astronomy/astroCalendarPDFYear.php?y=".$y;
	$pdfURLText = urlencode(lang("annual calendar is being prepared",'c'));
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("moon","c")?></title>
		<?php metaHeader()?>
		<style>
			.dayClass{
				vertical-align: top;
				opacity: 0.92;
				cursor: pointer;
			}
			.dayClass:hover{
				opacity:1;
				background: #<?php echo $color_schemes[$design2]['600']?>;
			}
			<?php
				if($stationLat<0){
			?>
				.moonIcon{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
			<?php
				}
			?>
			.pdfLink{
				font-size:3em;
				cursor: pointer;
				opacity: 0.8;
				padding-bottom:10px;
				padding-top: 10px;
			}
			.pdfLink:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1><?php echo lang('astronomy calendar','w')?></h1>
				<div style="width:98%;margin:0 auto;text-align:center">
					<table style="width:100%;table-layout:fixed">
						<tr>
							<td style="width:5%;text-align:left;font-size:0.8em">
								<a href="<?php echo $pdfURLMonth?>" target="_blank"><span class="fa fa-file-pdf-o tooltip pdfLink" title="PDF"></span><br><?php echo lang("month".($m*1),"c")." ".$y?></a>
							</td>
							<td style='text-align:center;vertical-align:top'>
								<select id="astroMonth" class="button2">
									<?php
										for($i=1;$i<13;$i++){
									?>
											<option value="<?php echo $i?>" <?php if($m==$i){ echo "selected"; }?>><?php echo lang('month'.$i,'c')?></option>
									<?php 
										}
									?>
								</select>
								<select id="astroYear" class="button2">
									<?php
										for($i=1990;$i<2030;$i++){
									?>
											<option value="<?php echo $i?>" <?php if($y==$i){ echo "selected"; }?>><?php echo $i?></option>
									<?php 
										}
									?>
								</select><br>
								<input type="button" class="button2" id="astroShow" value="<?php echo lang('show','c')?>">
							</td>
							<td style="width:5%;text-align:right;font-size:0.8em">
								<!--<a href="<?php echo $pdfURLYear?>&text=<?php echo $pdfURLText?>" target="_blank"><span class="fa fa-file-pdf-o tooltip pdfLink" title="PDF"></span><br><?php echo $y?></a>-->
							</td>
						</tr>
					</table>
				</div>
				<br>
				<h2><?php echo lang("month".$m,"c")." ".$y?></h2>
				<br>
				<?php
					echo $calendarString;
				?>
			</div>
		</div>
		<?php
			foreach($days as $key=>$values){
				$morning = $day['sunRiseTS'] - strtotime(date("Y",$day['sunRiseTS'])."-".date("m",$day['sunRiseTS'])."-".date("d",$day['sunRiseTS'])." 00:00");
				$dayTime = $day['sunSetTS'] - $day['sunRiseTS'];
				$daySeconds = 60 * 60 * 24;
				$evening = $daySeconds - $dayTime - $morning;
				$morningPerc = round($morning/$daySeconds,2)*100;
				$dayPerc = round($dayTime/$daySeconds,2)*100;
				$eveningPerc = round($evening/$daySeconds,2)*100;
		?>
				<div id="dayDetailDiv<?php echo $key?>">
					<h3><?php echo date($dateFormat,$values['date'])?></h3>
					<br>
					<table style="width:98%;margin:0 auto;border-radius:15px" cellspacing="0">
						<tr>
							<td style="width:50%;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px" colspan="3">
								<img src="<?php echo $pageURL.$path?>icons/sunrise.png" style="width:60px"><br><?php echo $values['sunRise']?>
							</td>
							<td style="width:50%;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px" colspan="3">
								<img src="<?php echo $pageURL.$path?>icons/sunset.png" style="width:60px"><br><?php echo $values['sunSet']?>
							</td>
						</tr>
						<tr>
							<td colspan="2" style="width:33%;font-variant:small-caps;font-weight:bold;font-size:1.1em;background:#<?php echo $color_schemes[$design2]['600']?>">
								<?php echo lang("astronomical",'c')?>
							</td>
							<td colspan="2" style="width:33%;font-variant:small-caps;font-weight:bold;font-size:1.1em;background:#<?php echo $color_schemes[$design2]['600']?>">
								<?php echo lang("nautical",'c')?>
							</td>
							<td colspan="2" style="width:33%;font-variant:small-caps;font-weight:bold;font-size:1.1em;background:#<?php echo $color_schemes[$design2]['600']?>">
								<?php echo lang("civil",'c')?>
							</td>
						</tr>
						<tr>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunrise.png" style="width:20px"><br><?php echo $values['twilightAstronomicalRise']?>
							</td>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunset.png" style="width:20px"><br><?php echo $values['twilightAstronomicalSet']?>
							</td>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunrise.png" style="width:20px"><br><?php echo $values['twilightNauticalRise']?>
							</td>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunset.png" style="width:20px"><br><?php echo $values['twilightNauticalSet']?>
							</td>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunrise.png" style="width:20px"><br><?php echo $values['twilightCivilRise']?>
							</td>
							<td style="width:16.6%;background:#<?php echo $color_schemes[$design2]['600']?>">
								<img src="<?php echo $pageURL.$path?>icons/sunset.png" style="width:20px"><br><?php echo $values['twilightCivilSet']?>
							</td>
						</tr>
						<tr>
							<td style="width:50%;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px" colspan="3">
								<img src="<?php echo $pageURL.$path?>icons/sun.png" style="width:60px"><br><?php echo $values['dayLength']?><br><?php echo $values['dayPercentage']?> %<br>
							</td>
							<td style="width:50%;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px" colspan="3">
								<img src="<?php echo $pageURL.$path?>icons/moon.png" style="width:60px"><br><?php echo $values['nightLength']?><br><?php echo $values['nightPercentage']?> %<br>
							</td>
						</tr>
						<tr>
							<td colspan='6' style="background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
								<table style='width:80%;margin:0 auto;margin-top:10px' cellspacing='0' cellpadding='0'>
									<tr>
										<td style='height:10px;background:black;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-right:0px;width:<?php echo $morningPerc?>%;border-radius:20px 0px 0px 20px'></td>
										<td style='height:10px;background:#4ca6ff;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-right:0px;border-left:0px;width:<?php echo $dayPerc?>%'></td>
										<td style='height:10px;background:black;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-left:0px;width:<?php echo $eveningPerc?>%;border-radius:0px 20px 20px 0px'></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan='6' style="background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
								<img src='<?php echo $pageURL.$path?>imgs/moon/<?php echo $values['moonIcon']?>.png' style='width:130px'>
							</td>
						</tr>
						<?php
							if(array_key_exists("moon",$values)){
								$phaseRaw = $values['moon'][3];
								if($phaseRaw==1){
									$phase = lang("new moon",'c');
								}
								else if($phaseRaw==2){
									$phase = lang("first quarter",'c');
								}
								else if($phaseRaw==3){
									$phase = lang("full moon",'c');
								}
								else{
									$phase = lang("last quarter",'c');
								}
								$phaseTime = date($timeFormat,$values['moon'][5]);

						?>
									<tr>
										<td colspan='6' style="background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
											<?php echo $phase." (".$phaseTime.")";?>
										</td>
									</tr>
						<?php
							}
						?>
						<?php
							if(array_key_exists("season",$day)){
								if(date("n",$values['season'])==3){
									if($stationLat>=0){
										$seasonType = lang('spring','c');
										$seasonImage = "spring";
									}
									else{
										$seasonType = lang('autumn','c');
										$seasonImage = "autumn";
									}
								}
								else if(date("n",$values['season'])==6){
									if($stationLat>=0){
										$seasonType = lang('summer','c');
										$seasonImage = "summer";
									}
									else{
										$seasonType = lang('winter','c');
										$seasonImage = "winter";
									}
								}
								else if(date("n",$values['season'])==9){
									if($stationLat>=0){
										$seasonType = lang('autumn','c');
										$seasonImage = "autumn";
									}
									else{
										$seasonType = lang('spring','c');
										$seasonImage = "spring";
									}
								}
								else{
									if($stationLat>=0){
										$seasonType = lang('winter','c');
										$seasonImage = "winter";
									}
									else{
										$seasonType = lang('summer','c');
										$seasonImage = "summer";
									}
								}
							?>
									<tr>
										<td colspan='6' style="padding-left:10px;text-align:left;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
											<img src='<?php echo $pageURL.$path?>icons/<?php echo $seasonImage?>.png' style='width:30px;padding-right:3px'><?php echo $seasonType?> (<?php echo date($timeFormat,$values['season'])?>)
										</td>
									</tr>
							<?php
								}
							?>
							<?php
								if(array_key_exists("meteors",$values)){
							?>
									<tr>
										<td colspan='6' style="padding-left:10px;text-align:left;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
											<img src='<?php echo $pageURL.$path?>icons/meteor.png' style='width:30px;padding-right:3px'><?php echo $values['meteors']?>
										</td>
									</tr>
							<?php
								}
								if(array_key_exists("lunar",$values)){
									$lunarRow = explode(';',$values['lunar']);
									$checkedLunarDate = strtotime($lunarRow[1]."-".$lunarRow[2]."-".$lunarRow[3]." ".$lunarRow[4]);
									$tD = date("d",$checkedLunarDate);
									$tM = date("m",$checkedLunarDate);
									$tY = date("Y",$checkedLunarDate);
									$dateTimeZone = new DateTimeZone($stationTZ);
									$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
									$offset = ($dateTimeZone->getOffset($dateTime));
									$checkedLunarDate = $checkedLunarDate + $offset;
									$dateLunar = date($timeFormat,$checkedLunarDate);
									$type = $lunarRow[8];
									if($type=="T"){
										$type = lang('total eclipse','c');
									}
									if($type=="P"){
										$type = lang('partial eclipse','c');
									}
									if($type=="N"){
										$type = lang('penumbral eclipse','c');
									}
									if($type=="Tm"){
										$type = lang('total eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
									}
									if($type=="Pm"){
										$type = lang('partial eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
									}
									if($type=="Nm"){
										$type = lang('penumbral eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
									}
									if($type=="T+"){
										$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes north of shadow axis",'c');
									}
									if($type=="T-"){
										$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes south of shadow axis",'c');
									}
									if($type=="Tx"){
										$type = lang('total eclipse','c')."<br>".lang("total penumbral lunar eclipse",'l');
									}
									if($type=="Nb"){
										$type = lang('penumbral eclipse','c')."<br>".lang("first penumbral eclipse in series",'l');
									}
									if($type=="Ne"){
										$type = lang('penumbral eclipse','c')."<br>".lang("last penumbral eclipse in series",'l');
									}
									if($type[0]=="T"){
										$typeImg = "totalL.png";
									}
									if($type[0]=="P"){
										$typeImg = "partialL.png";
									}
									if($type[0]=="N"){
										$typeImg = "penumbralL.png";
									}
							?>
									<tr>
										<td colspan='6' style="padding-left:10px;text-align:left;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
											<img src='<?php echo $pageURL.$path?>icons/lunar.png' style='width:30px;padding-right:3px'><?php echo $type." (".$dateLunar.")"?>
										</td>
									</tr>
							<?php
								}
								if(array_key_exists("solar",$values)){
									$solarData = explode(';',$values['solar']);
									$checkedSolarDate = strtotime($solarData[2]."-".$solarData[3]."-".$solarData[4]." ".$solarData[5]);
									$tD = date("d",$checkedSolarDate);
									$tM = date("m",$checkedSolarDate);
									$tY = date("Y",$checkedSolarDate);
									$dateTimeZone = new DateTimeZone($stationTZ);
									$dateTime = new DateTime(($tD."-".$tM."-".$tY), $dateTimeZone);
									$offset = ($dateTimeZone->getOffset($dateTime));
									$checkedSolarDate = $checkedSolarDate + $offset;
									$dateSolar = date($timeFormat,$checkedSolarDate);
									$type = $solarData[9];
									if($type[0]=="P"){
										$type = lang('partial eclipse','c');
										$typeImg = "partial.png";
									}
									if($type[0]=="A"){
										$type = lang('annular eclipse','c');
										$typeImg = "annular.png";
									}
									if($type[0]=="T"){
										$type = lang('total eclipse','c');
										$typeImg = "total.png";
									}
									if($type[0]=="H"){
										$type = lang('hybrid eclipse','c');
										$typeImg = "hybrid";
									}
									if(strlen($type)>1){
										if(substr($type,1,1)=="m"){
											$type = $type."<br>".lang('middle eclipse of Saros series','l');
										}
										if(substr($type,1,1)=="n"){
											$type = $type."<br>".lang('central eclipse with no northern limit','l');
										}
										if(substr($type,1,1)=="s"){
											$type = $type."<br>".lang('central eclipse with no southern limit','l');
										}
										if(substr($type,1,1)=="+"){
											$type = $type."<br>".lang("non-central eclipse with no northern limit",'l');
										}
										if(substr($type,1,1)=="-"){
											$type = $type."<br>".lang("non-central eclipse with no southern limit",'l');
										}
										if(substr($type,1,1)=="2"){
											$type = $type."<br>".lang("hybrid path begins total and ends annular",'l');
										}
										if(substr($type,1,1)=="3"){
											$type = $type."<br>".lang("hybrid path begins annular and ends total",'l');
										}
										if(substr($type,1,1)=="b"){
											$type = $type."<br>".lang("first eclipse in series",'l');
										}
										if(substr($type,1,1)=="e"){
											$type = $type."<br>".lang("last eclipse in series",'l');
										}
									}
									$durationRaw = $solarData[17];
									$durationM = substr($durationRaw,0,2);
									$durationS = substr($durationRaw,3,2);
									$duration = $durationM * 60 + $durationS;
						?>
									<tr>
										<td colspan='6' style="padding-left:10px;text-align:left;background:#<?php echo $color_schemes[$design2]['800']?>;padding-top:10px;padding-bottom:10px">
											<img src='<?php echo $pageURL.$path?>icons/solar.png' style='width:30px;padding-right:3px'><?php echo $type." (".$dateSolar.", ".$duration."s)"?>
										</td>
									</tr>
						<?php
							}
						?>
						<?php
							$currentZodiac = getZodiac($values['date']);
							$zodiac = lang("zodiac".ucwords($currentZodiac),"c");
						?>
						<tr>
							<td colspan='6' style="padding-left:10px;text-align:left;background:#<?php echo $color_schemes[$design2]['600']?>;padding-top:10px;padding-bottom:10px">
								<img src='<?php echo $pageURL.$path?>icons/zodiac/<?php echo $currentZodiac?>.png' style='width:30px;padding-right:3px'> <?php echo $zodiac?>
							</td>
						</tr>
					</table>
				</div>
				<script>
					dialogHeight = screen.height*0.8;
					dialogWidth = screen.width*0.6;
					$(".dayClass").click(function(){
						id = $(this).attr('data-id');
						$("#dayDetailDiv"+id).dialog('open');
					})
					$("#dayDetailDiv<?php echo $key?>").dialog({
						modal: true,
						autoOpen: false,
						height: dialogHeight,
						width: dialogWidth
					});
					$("#astroShow").click(function(){
						m = $("#astroMonth").val();
						y = $("#astroYear").val();
						window.location = "astroCalendar.php?y="+y+"&m="+m;
					})
				</script>
		<?php
			}
		?>
		<?php include("../../footer.php")?>
	</body>
</html>
