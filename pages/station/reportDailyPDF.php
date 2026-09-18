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

    $languageRaw = file_get_contents($baseURL."lang/gb.php");
    $language['gb'] = json_decode($languageRaw,true);
    $languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
    $language[$lang] = json_decode($languageRaw,true);

    $monthsArray = array("",lang('january','c'),lang('february','c'),lang('march','c'),lang('april','c'),lang('may','c'),lang('june','c'),lang('july','c'),lang('august','c'),lang('september','c'),lang('october','c'),lang('november','c'),lang('december','c'));

    $weekDays = array(lang('sundayAbbr','c'),lang('mondayAbbr','c'),lang('tuesdayAbbr','c'),lang('wednesdayAbbr','c'),lang('thursdayAbbr','c'),lang('fridayAbbr','c'),lang('saturdayAbbr','c'));

    // Get date
	$chosenDay = $_GET['d'];
	$chosenMonth = $_GET["m"];
	$chosenYear = $_GET["y"];
	
	// validate date and prevent SQL injection
	if($chosenDay<0 || $chosenDay>31){
		$chosenDay = date("d","yesterday");
	}
	if($chosenMonth<1 || $chosenMonth>12){
		$chosenMonth = date("m","yesterday");
	}
	if($chosenYear<1900 || $chosenYear>2100){
		$chosenYear = date("Y","yesterday");
	}
	
	if(!is_numeric($chosenDay) || !is_numeric($chosenMonth) || !is_numeric($chosenYear)){
		echo "Invalid date";
		die();
	}
	
	$day = strtotime($chosenYear."-".$chosenMonth."-".$chosenDay);

    $Y = $chosenYear;
	$M = $chosenMonth;
	$D = $chosenDay;
    $dateY = $chosenYear;
	$dateM = $chosenMonth;
	$dateD = $chosenDay;
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}

    /* #############################################################################*/
	// Calculate daily average, max, min, sd, range
	
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(Tmin), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(Tmin), stddev(Tmax), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR), max(R)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$dailyAvgT = convertT($row['avg(T)']);
		$dailyAvgH = $row['avg(H)'];
		$dailyAvgP = convertP($row['avg(P)']);
		$dailyAvgD = convertT($row['avg(D)']);
		$dailyAvgW = convertW($row['avg(W)']);
		$dailyAvgG = convertW($row['avg(G)']);
		$dailyAvgA = convertT($row['avg(A)']);
		$dailyAvgS = $row['avg(S)'];
		// daily maxima
		$dailyMaxT = convertT($row['max(Tmax)']);
		$dailyMaxH = $row['max(H)'];
		$dailyMaxP = convertP($row['max(P)']);
		$dailyMaxD = convertT($row['max(D)']);
		$dailyMaxW = convertW($row['max(W)']);
		$dailyMaxG = convertW($row['max(G)']);
		$dailyMaxA = convertT($row['max(A)']);
		$dailyMaxS = $row['max(S)'];
		// daily minima
		$dailyMinT = convertT($row['min(Tmin)']);
		$dailyMinH = $row['min(H)'];
		$dailyMinP = convertP($row['min(P)']);
		$dailyMinD = convertT($row['min(D)']);
		$dailyMinW = convertW($row['min(W)']);
		$dailyMinG = convertW($row['min(G)']);
		$dailyMinA = convertT($row['min(A)']);
		$dailyMinS = $row['min(S)'];
		// daily ranges
		$dailyRangeT = $dailyMaxT - $dailyMinT;
		$dailyRangeH = $dailyMaxH - $dailyMinH;
		$dailyRangeP = $dailyMaxP - $dailyMinP;
		$dailyRangeD = $dailyMaxD - $dailyMinD;
		$dailyRangeW = $dailyMaxW - $dailyMinW;
		$dailyRangeG = $dailyMaxG - $dailyMinG;
		$dailyRangeA = $dailyMaxA - $dailyMinA;
		$dailyRangeS = $dailyMaxS - $dailyMinS;
		// daily standard deviations
		$dailyStddevT = convertTStddev($row['stddev(T)']);
		$dailyStddevH = $row['stddev(H)'];
		$dailyStddevP = convertP($row['stddev(P)']);
		$dailyStddevD = convertTStddev($row['stddev(D)']);
		$dailyStddevW = convertW($row['stddev(W)']);
		$dailyStddevG = convertW($row['stddev(G)']);
		$dailyStddevA = convertTStddev($row['stddev(A)']);
		$dailyStddevS = $row['stddev(S)'];
		// daily rain total and maximum rain rate
		$dailyRTotal = convertR($row['max(R)']);
		$dailyMaxRR = convertR($row['max(RR)']);
	}
	
	// Calculate dates and times when max and min measured
	$result = mysqli_query($con,"
		SELECT *
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		"
	);
	while($row = mysqli_fetch_array($result)){
		if(convertT($row['Tmax'])==$dailyMaxT){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxTDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['Tmin'])==$dailyMinT){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinTDate[] = date($timeFormat, $date_temporary);
		}
		if($row['H']==$dailyMaxH){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxHDate[] = date($timeFormat, $date_temporary);
		}
		if($row['H']==$dailyMinH){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinHDate[] = date($timeFormat, $date_temporary);
		}
		if(convertP($row['P'])==$dailyMaxP){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxPDate[] = date($timeFormat, $date_temporary);
		}
		if(convertP($row['P'])==$dailyMinP){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinPDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['W'])==$dailyMaxW){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxWDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['W'])==$dailyMinW){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinWDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['G'])==$dailyMaxG){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxGDate[] = date($timeFormat, $date_temporary);
		}
		if(convertW($row['G'])==$dailyMinG){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinGDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['A'])==$dailyMaxA){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxADate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['A'])==$dailyMinA){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinADate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['D'])==$dailyMaxD){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxDDate[] = date($timeFormat, $date_temporary);
		}
		if(convertT($row['D'])==$dailyMinD){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinDDate[] = date($timeFormat, $date_temporary);
		}
		if($row['S']==$dailyMaxS){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxSDate[] = date($timeFormat, $date_temporary);
		}
		if($row['S']==$dailyMinS){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMinSDate[] = date($timeFormat, $date_temporary);
		}
		if($row['RR']==$dailyMaxRR){
			$date_temporary = strtotime($row['DateTime']);
			$dailyMaxRRDate[] = date($timeFormat, $date_temporary);
		}
	}
	
	// Separate calculation of average wind bearing
	$result = mysqli_query($con,"
		SELECT B, Hour(DateTime)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M." AND YEAR(DateTime)=".$Y."
		"
	);
	while($row = mysqli_fetch_array($result)){
		$bearings[] = $row['B'];
		$hour = $row['Hour(DateTime)'];
		${"bearings".$hour}[] = $row['B'];
	}
	$dailyAvgB = avgWind($bearings);
	for($i=0;$i<24;$i++){
		if(isset(${"bearings".$i})){
			${"bearings".$i."avg"} = avgWind(${"bearings".$i});
		}
	}

	// Calculate daily wind run
	if($displayWindUnits=="kmh"){
		$dailyWindRun = round($dailyAvgW * 24,1);
		$dailyWindRunUnits = "km";
	}
	if($displayWindUnits=="ms"){
		$dailyWindRun = round($dailyAvgW * 3.6 * 24,1);
		$dailyWindRunUnits = "km";
	}
	if($displayWindUnits=="mph"){
		$dailyWindRun = round($dailyAvgW * 24,1);
		$dailyWindRunUnits = "mi";
	}
	if($displayWindUnits=="kt"){
		$tempW = convertor($dailyAvgW,"kt","mph");
		$dailyWindRun = round($tempW * 24,1);
		$dailyWindRunUnits = "mi";
	}
	
	// Calculate all year averages
	$result = mysqli_query($con,"
		SELECT T,H,P,D,W,G,A,S,DateTime,Tmax,Tmin
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M."
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$time = date($dateTimeFormat,strtotime($row['DateTime']));
		$Y = date("Y",strtotime($row['DateTime']));
		$overallAvgT[$time] = convertT($row['T']);
		$overallYT[$Y][] = convertT($row['T']);
		$overallMaxT[$time] = convertT($row['Tmax']);
		$overallMinT[$time] = convertT($row['Tmin']);
		$overallH[$time] = $row['H'];
		$overallYH[$Y][] = $row['H'];
		$overallP[$time] = convertP($row['P']);
		$overallYP[$Y][] = convertP($row['P']);
		$overallD[$time] = convertT($row['D']);
		$overallYD[$Y][] = convertT($row['D']);
		$overallW[$time] = convertW($row['W']);
		$overallYW[$Y][] = convertW($row['W']);
		$overallG[$time] = convertW($row['G']);
		$overallYG[$Y][] = convertW($row['G']);
		$overallA[$time] = convertT($row['A']);
		$overallYA[$Y][] = convertT($row['A']);
		$overallS[$time] = $row['S'];
		$overallYS[$Y][] = $row['S'];
		if(isset($row['RR'])){
			$overallRR[$time] = convertR($row['RR']);
			$overallYRR[$Y][] = convertR($row['RR']);
		}
	}
	
	$result = mysqli_query($con,"
		SELECT max(R),YEAR(DateTime)
		FROM alldata 
		WHERE DAY(DateTime)=".$D." AND MONTH(DateTime)=".$M."
		GROUP BY YEAR(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		// daily averages
		$time = $row['YEAR(DateTime)'];
		$overallR[$time] = convertR($row['max(R)']);
	}
	
	// group by years
	foreach($overallYT as $Y=>$yearValues){
		$annualT[$Y] = avg($yearValues);
	}
	foreach($overallYA as $Y=>$yearValues){
		$annualA[$Y] = avg($yearValues);
	}
	foreach($overallYD as $Y=>$yearValues){
		$annualD[$Y] = avg($yearValues);
	}
	foreach($overallYH as $Y=>$yearValues){
		$annualH[$Y] = avg($yearValues);
	}
	foreach($overallYP as $Y=>$yearValues){
		$annualP[$Y] = avg($yearValues);
	}
	foreach($overallYW as $Y=>$yearValues){
		$annualW[$Y] = avg($yearValues);
	}
	foreach($overallYG as $Y=>$yearValues){
		$annualG[$Y] = avg($yearValues);
	}
	foreach($overallYS as $Y=>$yearValues){
		$annualS[$Y] = avg($yearValues);
	}
	foreach($overallYRR as $Y=>$yearValues){
		$annualRR[$Y] = avg($yearValues);
	}
	
	function avg($arr){
		if(count($arr)>0){
			return array_sum($arr)/count($arr);
		}
		else{
			return "";
		}
	}
	function deviation($n1,$n2,$dp){
		$deviation = $n1 - $n2;
		$deviation = number_format($deviation,$dp,".","");
		if($deviation>0){
			return "+".$deviation;
		}
		else{
			return $deviation;
		}
	}
    function checkSize($arr){
        if(count($arr)>1){
            return $arr[0]."*";
        }
        else{
            return implode(", ",$arr);
        }
    }

    // Sunrise/sunset
	$dateTimeZone = new DateTimeZone($stationTZ);
	$dateTime = new DateTime(($dateD."-".$dateM."-".$dateY), $dateTimeZone);
	$offset = ($dateTimeZone->getOffset($dateTime))/3600;

	$sun = date_sun_info(time(),$stationLat, $stationLon);

	$sunRise = date("H:i:s", $sun['sunrise']);
	$sunSet = date("H:i:s", $sun['sunset']);
	
	$sunRiseNautical= date("H:i:s", $sun['nautical_twilight_begin']);
	$sunSetNautical= date("H:i:s", $sun['nautical_twilight_end']);
	$sunRiseAstronomical= date("H:i:s", $sun['astronomical_twilight_begin']);
	$sunSetAstronomical= date("H:i:s", $sun['astronomical_twilight_end']);
	$sunRiseCivil= date("H:i:s", $sun['civil_twilight_begin']);
	$sunSetCivil= date("H:i:s", $sun['civil_twilight_end']);
	
	$temporary = date_sun_info($day, $stationLat, $stationLon);
	$sunTransitTimestamp=$temporary['transit'] + ($offset*3600);
	$sunTransit=gmdate("H:i", $sunTransitTimestamp);
	
	// Moonrise/moonset
	$moonTimes = new MoonRiSet($stationLat, $stationLon, $stationTZ);
	$moonTimes->setDate($dateY, $dateM, $dateD);
	$moonRise = $moonTimes->rise["hh:mm"];
	$moonSet = $moonTimes->set["hh:mm"];
	
	// Daylength
	$sunRiseTimestamp= $sun['sunrise'];
	$sunSetTimestamp= $sun['sunset'];

	$diff = ($sunSetTimestamp-$sunRiseTimestamp)/60;
	$dayLengthHours = floor($diff/60);
	$dayLengthMins = round(($diff - ($dayLengthHours*60)),0);
	$dayLength = $dayLengthHours." ".lang("hAbbr",'')." ".$dayLengthMins." ".lang("minAbbr",'l');
	$nightLength = floor(24-$dayLengthHours-1)." ".lang("hAbbr",'')." ".(60-$dayLengthMins)." ".lang("minAbbr",'l');

    // Calendar functions
	$julianDate = cal_to_jd(CAL_GREGORIAN,$dateM,$dateD,$dateY);


    // PDF
    include($baseURL."scripts/mpdf60/mpdf.php");
    if($defaultPaperSize=="letter"){
        $mpdf = new mPDF('','Letter');
    }
    else{
        $mpdf = new mPDF();
    }
    $mpdf->SetTitle(lang("day report",'w'));
    $mpdf->SetAuthor("Meteotemplate");
    $mpdf->SetCreator("Meteotemplate");

    $mpdf->setFooter('<span style="color:black;font-style:normal;font-size:0.9em">'.$pageURL.$path.'</span>||<span style="color:black;font-style:normal">Meteotemplate</span>');

    $mpdf->WriteHTML('
        <head>
            <style>
                #summaryTable td{
                    vertical-align:top;
                }
                #summaryTable tr:nth-child(even) {
                    background: #'.$color_schemes[$design2]['200'].';
                }
                #summaryTable tr:nth-child(odd) {
                    background: #'.$color_schemes[$design2]['100'].';
                }
                .unitCell{
                    
                    text-align: center;
                }
            </style>
        </head>
    ');
    $mpdf->WriteHTML('<body style="font-family:Helvetica">');

    $mpdf->WriteHTML('
        <table style="width:100%;margin:0 auto" cellspacing="0">
			<tr>
                <td style="text-align:center;background: #'.$color_schemes[$design2]['900'].';color:white">
                    <h1 style="font-size:1.5em;color:white">'.lang("day report",'w').'</h1>
					<h2 style="font-size:1.2em;color:white">'.date($dateFormat,$day).'</h2>
                </td>
            </tr>
            <tr>
                <td style="text-align:right;background: #'.$color_schemes[$design2]['900'].';color:white;padding-right:3px;padding-bottom:20px">
                    '.lang("day of the year",'c').': '.date("z",strtotime($dateY."-".$dateM."-".$dateD)).'<br><br>
                </td>
            </tr>
		</table>
		<table style="width:100%;margin:0 auto" cellspacing="0" cellpadding="2">
			<tr style="padding-top:5px">
				<td style="text-align:center;background: #'.$color_schemes[$design2]['200'].'" colspan="3">
					<img src="../../icons/pdf/sun.png" style="width:40px">
				</td>
				<td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';" colspan="3">
					<img src="../../icons/pdf/night.png" style="width:40px">
				</td>
			</tr>
			<tr>
				<td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';" colspan="3">
					'.$dayLength.'
				</td>
				<td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';" colspan="3">
					'.$nightLength.'
				</td>
			</tr>
			<tr style="padding-top:5px;">
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';width:20%">
					<img src="../../icons/sunrise.png" style="width:30px">
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';width:20%">
					<img src="../../icons/sunset.png" style="width:30px">
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';width:20%">
					<img src="../../icons/moonrise.png" style="width:30px">
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';width:20%">
					<img src="../../icons/moonset.png" style="width:30px">
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';width:20%">
					<img src="../../icons/suntransit.png" style="width:30px">
				</td>
			</tr>
			<tr style="padding-bottom:5px">
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';">
					'.$sunRise.'
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';">
					'.$sunTransit.'
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';">
					'.$sunSet.'
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';">
					'.$moonRise.'
				</td>
				<td style="text-align:center;color:white;text-align:center;background: #'.$color_schemes[$design2]['500'].';">
					'.$moonSet.'
				</td>
			</tr>
		</table>
        <table style="width:100%;margin:0 auto" cellspacing="0">
            <thead>
                <tr>
                    <td colspan="2" style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.lang("astronomical","c").'
                    </td>
                    <td colspan="2" style="text-align:center;background: #'.$color_schemes[$design2]['100'].';">
                        '.lang("nautical","c").'
                    </td>
                    <td colspan="2" style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.lang("civil","c").'
                    </td>
                </tr>
                <tr style="padding-bottom:5px">
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';width:16.66%">
                        <img src="../../icons/pdf/sunrise.png" style="width:25px" >
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';width:16.66%">
                        <img src="../../icons/pdf/sunset.png" style="width:25px" >
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['100'].';width:16.66%">
                        <img src="../../icons/pdf/sunrise.png" style="width:25px" >
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['100'].';width:16.66%">
                        <img src="../../icons/pdf/sunset.png" style="width:25px" >
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';width:16.66%">
                        <img src="../../icons/pdf/sunrise.png" style="width:25px" >
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';width:16.66%">
                        <img src="../../icons/pdf/sunset.png" style="width:25px" >
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.$sunRiseAstronomical.'
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.$sunSetAstronomical.'
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['100'].';">
                        '.$sunRiseNautical.'
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['100'].';">
                       '.$sunSetNautical.'
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.$sunRiseCivil.'
                    </td>
                    <td style="text-align:center;background: #'.$color_schemes[$design2]['200'].';">
                        '.$sunSetCivil.'
                    </td>
                </tr>
            </tbody>
        </table>
		<br>
    ');

    $mpdf->WriteHTML('<div id="summary">');
		$mpdf->WriteHTML('<table style="table-layout:fixed" cellspacing="0" cellpadding="2" id="summaryTable">');
		    $mpdf->WriteHTML('
                <thead>
                    <tr>
                        <th colspan="2" style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">								
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang('avgAbbr','c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang('maximumAbbr','c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang('minimumAbbr','c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang('range','c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("sdAbbr",'u').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("long-term",'c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("deviation",'c').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("avgAbbr",'c')."<br>".lang('maximumAbbr','l').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("avgAbbr",'c')."<br>".lang('minimumAbbr','l').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                            '.lang("absolute",'c')."<br>".lang('maximumAbbr','l').'
                        </th>
                        <th style="text-align:center;font-size:10pt;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'">
                           '.lang("absolute",'c')."<br>".lang('minimumAbbr','l').'
                        </th>
					</tr>
					</thead>
					<tbody>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/temp.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayTempUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgT,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxT,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxTDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinT,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinTDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeT,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevT,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallAvgT),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgT,avg($overallAvgT),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualT),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualT,max($annualT))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualT),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualT,min($annualT))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallMaxT),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallMaxT,max($overallMaxT))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallMinT),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallMinT,min($overallMinT))) .'</div>
							</td>
						</tr>
                        <tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/apparent.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayTempUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgA,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxA,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxADate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinA,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinADate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeA,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevA,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallA),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgA,avg($overallA),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualA),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualA,max($annualA))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualA),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualA,min($annualA))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallA),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallA,max($overallA))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallA),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallA,min($overallA))) .'</div>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/dewpoint.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayTempUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgD,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxD,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxDDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinD,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinDDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeD,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevD,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallD),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgD,avg($overallD),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualD),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualD,max($annualD))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualD),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualD,min($annualD))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallD),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallD,max($overallD))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallD),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallD,min($overallD))) .'</div>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/humidity.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								%
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgH,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxH,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxHDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinH,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinHDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeH,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevH,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallH),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgH,avg($overallH),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualH),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualH,max($annualH))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualH),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualH,min($annualH))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallH),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallH,max($overallH))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallH),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallH,min($overallH))) .'</div>
							</td>
						</tr>
                        <tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/pressure.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayPressUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgP,($decimalsP+1)).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxP,($decimalsP)).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxPDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinP,$decimalsP).'</strong><div style="font-size:10pt">'.checkSize($dailyMinPDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeP,$decimalsP).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevP,($decimalsP+1)).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallP),($decimalsP+1),".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgP,avg($overallP),$decimalsP+1).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualP),$decimalsP,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualP,max($annualP))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualP),$decimalsP,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualP,min($annualP))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallP),$decimalsP,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallP,max($overallP))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallP),$decimalsP,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallP,min($overallP))) .'</div>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/wind.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayWindUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgW,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxW,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxWDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinW,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinWDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeW,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevW,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallW),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgW,avg($overallW),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualW),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualW,max($annualW))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualW),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualW,min($annualW))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallW),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallW,max($overallW))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallW),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallW,min($overallW))) .'</div>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/gust.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'.unitformatter($displayWindUnits).'
							</td>
							<td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgG,2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMaxG,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxGDate).'</div>
							</td>
                            <td style="font-size:13pt;text-align:center">
                                <strong>'.round($dailyMinG,1).'</strong><div style="font-size:10pt">'.checkSize($dailyMinGDate).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyRangeG,1).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.round($dailyStddevG,2).'
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.number_format(avg($overallG),2,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'.deviation($dailyAvgG,avg($overallG),2).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualG),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualG,max($annualG))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualG),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualG,min($annualG))) .'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallG),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallG,max($overallG))).'</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallG),1,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallG,min($overallG))) .'</div>
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle">
                                <img src="../../icons/pdf/wind.png" style="width:30px">
							</td>
							<td>
								
							</td>
							<td style="font-size:13pt;text-align:center">
								'. round($dailyAvgB).'°
							</td>
							<td colspan="10">
							</td>
						</tr>');

						if($solarSensor){
							$mpdf->WriteHTML('
                                <tr>
                                    <td style="width: 30px;vertical-align:middle"> 
                                        <img src="../../icons/pdf/sun.png" style="width:30px">
                                    </td>
                                    <td style="vertical-align: middle;text-align:center">
                                        W/m2
                                    </td>
                                    <td style="font-size:13pt;text-align:center"><strong>'.round($dailyAvgS,1).'</strong>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.round($dailyMaxS,0).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxSDate).'</div>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.round($dailyMinS,0).'</strong><div style="font-size:10pt">'.checkSize($dailyMinSDate).'</div>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.round($dailyRangeS,0).'
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.round($dailyStddevS,1).'
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.number_format(avg($overallS),1,".","").'</strong>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'.deviation($dailyAvgS,avg($overallS),1).'</strong>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'. number_format(max($annualS),0,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualS,max($annualS))).'</div>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'. number_format(min($annualS),0,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualS,min($annualS))) .'</div>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'. number_format(max($overallS),0,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallS,max($overallS))).'</div>
                                    </td>
                                    <td style="font-size:13pt;text-align:center">
                                        <strong>'. number_format(min($overallS),0,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($overallS,min($overallS))) .'</div>
                                    </td>
                                </tr>
                            ');
                        }
						$mpdf->WriteHTML('
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/rain.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'. unitformatter($displayRainUnits).'/'. lang('hAbbr','l').'
							</td>
							<td>
								
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. round($dailyMaxRR,$decimalsR).'</strong><div style="font-size:10pt">'.checkSize($dailyMaxRRDate) .'</div>
							</td>
							<td colspan="5">
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($annualRR),$decimalsR,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualRR,max($annualRR))) .'
								</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($annualRR),$decimalsR,".","") .'</strong><div style="font-size:10pt">'.checkSize(array_keys($annualRR,min($annualRR))) .'
								</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallRR),$decimalsR+1,".","") .'</strong>
							</td>
							<td colspan="5"></td>
						</tr>
						<tr>
							<td colspan="13" style="text-align:center;font-varian:small-caps;border-bottom:white;font-weight:bold">
								'. lang('total','c').'
							</td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/rain.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'. unitformatter($displayRainUnits).'
							</td>
							<td colspan="5" style="font-size:13pt;text-align:center">
								<strong>'. $dailyRTotal .'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(avg($overallR),$decimalsR,".","").'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. deviation($dailyRTotal,avg($overallR),$decimalsR).'</strong>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(max($overallR),$decimalsR,".","") .'</strong><div style="font-size:10pt">
									'. checkSize(array_keys($overallR,max($overallR))) .'</strong>
								</div>
							</td>
							<td style="font-size:13pt;text-align:center">
								<strong>'. number_format(min($overallR),$decimalsR,".","") .'</strong><div style="font-size:10pt">
									'. checkSize(array_keys($overallR,min($overallR)) ).'
								</div>
							</td>
							<td colspan="4"></td>
						</tr>
						<tr>
							<td style="width: 30px;vertical-align:middle"> 
								<img src="../../icons/pdf/wind.png" style="width:30px">
							</td>
							<td style="vertical-align: middle;text-align:center">
								'. $dailyWindRunUnits.'
							</td>
							<td colspan="5" style="font-size:13pt;text-align:center">
								<strong>'. round($dailyWindRun,2) .'</strong>
							</td>
							<td colspan="6"></td>
						</tr>
					</tbody>
				</table>
				<div style="width:100%;text-align:right;font-size:10px">
					* '.lang('more than one').'
				</div>
				<br>
			</div>');
    
    //$mpdf->WriteHTML('<pagebreak />');

    $mpdf->Output('dayReport_'.date('Y-m-d',$day).'.pdf', 'I');
    exit;
?>