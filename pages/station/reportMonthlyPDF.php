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
	
	//error_reporting(E_ALL);
	
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	createCacheDir();
	
	$bearingNames = array(lang("directionN",""),lang("directionNNE",""),lang("directionENE",""),lang("directionE",""),lang("directionESE",""),lang("directionSE",""),lang("directionSSE",""),lang("directionS",""),lang("directionSSW",""),lang("directionSW",""),lang("directionWSW",""),lang("directionW",""),lang("directionWNW",""),lang("directionNW",""),lang("directionNNW",""));
	
	if($_GET['m']<1 || $_GET['m']>12){
		echo "Invalid date";
		die();
	}
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y']) || !is_numeric($_GET['m'])){
		echo "Invalid date";
		die();
	}
	
	$chosenYear = $_GET['y'];
	$chosenMonth = $_GET['m'];

	// jweyn: a bunch of number formatters.
	if($displayPressUnits=="inhg"){
		$decimalP = 2;
	}
	else{
		$decimalP = 1;
	}
	if($displayRainUnits=="in"){
		$decimalR = 2;
	}
	else{
		$decimalR = 1;
	}
	$decimalW = 1;
	$decimalT = 1;
	$decimalH = 1;
	$decimalS = 0;
	
	if(file_exists("cache/monthly".$chosenYear."_".$chosenMonth.".txt")){
		$data = json_decode(file_get_contents("cache/monthly".$chosenYear."_".$chosenMonth.".txt"),true);
	}
	else{
	
		$span = "Month(DateTime) = " .$chosenMonth. " AND Year(DateTime) = ".$chosenYear;
		
		
		/* #############################################################################*/
		// Calculate monthly average, max, min, sd, range
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(Tmin), avg(Tmax), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(T), max(Tmin), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(T), min(Tmin), min(Tmax), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(Tmin), stddev(Tmax), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			// monthly averages
			$data['monthlyAvgT'] = ($row['avg(T)']);
			$data['monthlyAvgTmin'] = ($row['avg(Tmin)']);
			$data['monthlyAvgTmax'] = ($row['avg(Tmax)']);
			$data['monthlyAvgH'] = $row['avg(H)'];
			$data['monthlyAvgP'] = ($row['avg(P)']);
			$data['monthlyAvgD'] = ($row['avg(D)']);
			$data['monthlyAvgW'] = ($row['avg(W)']);
			$data['monthlyAvgG'] = ($row['avg(G)']);
			$data['monthlyAvgA'] = ($row['avg(A)']);
			$data['monthlyAvgS'] = $row['avg(S)'];
			
			// monthly maxima
			$data['monthlyMaxT'] = ($row['max(T)']);
			$data['monthlyMaxTmin'] = ($row['max(Tmin)']);
			$data['monthlyMaxTmax'] = ($row['max(Tmax)']);
			$data['monthlyMaxH'] = $row['max(H)'];
			$data['monthlyMaxP'] = ($row['max(P)']);
			$data['monthlyMaxD'] = ($row['max(D)']);
			$data['monthlyMaxW'] = ($row['max(W)']);
			$data['monthlyMaxG'] = ($row['max(G)']);
			$data['monthlyMaxA'] = ($row['max(A)']);
			$data['monthlyMaxS'] = $row['max(S)'];
			
			// monthly minima
			$data['monthlyMinT'] = ($row['min(T)']);
			$data['monthlyMinTmin'] = ($row['min(Tmin)']);
			$data['monthlyMinTmax'] = ($row['min(Tmax)']);
			$data['monthlyMinH'] = $row['min(H)'];
			$data['monthlyMinP'] = ($row['min(P)']);
			$data['monthlyMinD'] = ($row['min(D)']);
			$data['monthlyMinW'] = ($row['min(W)']);
			$data['monthlyMinG'] = ($row['min(G)']);
			$data['monthlyMinA'] = ($row['min(A)']);
			$data['monthlyMinS'] = $row['min(S)'];
			
			// monthly ranges
			$data['monthlyRangeT'] = $data['monthlyMaxT'] - $data['monthlyMinT'];
			$data['monthlyRangeTmin'] = $data['monthlyMaxTmin'] - $data['monthlyMinTmin'];
			$data['monthlyRangeTmax'] = $data['monthlyMaxTmax'] - $data['monthlyMinTmax'];
			$data['monthlyRangeH'] = $data['monthlyMaxH'] - $data['monthlyMinH'];
			$data['monthlyRangeP'] = $data['monthlyMaxP'] - $data['monthlyMinP'];
			$data['monthlyRangeD'] = $data['monthlyMaxD'] - $data['monthlyMinD'];
			$data['monthlyRangeW'] = $data['monthlyMaxW'] - $data['monthlyMinW'];
			$data['monthlyRangeG'] = $data['monthlyMaxG'] - $data['monthlyMinG'];
			$data['monthlyRangeA'] = $data['monthlyMaxA'] - $data['monthlyMinA'];
			$data['monthlyRangeS'] = $data['monthlyMaxS'] - $data['monthlyMinS'];
			
			// monthly standard deviations
			$data['monthlyStddevT'] = ($row['stddev(T)']);
			$data['monthlyStddevTmin'] = ($row['stddev(Tmin)']);
			$data['monthlyStddevTmax'] = ($row['stddev(Tmax)']);
			$data['monthlyStddevH'] = $row['stddev(H)'];
			$data['monthlyStddevP'] = ($row['stddev(P)']);
			$data['monthlyStddevD'] = ($row['stddev(D)']);
			$data['monthlyStddevW'] = ($row['stddev(W)']);
			$data['monthlyStddevG'] = ($row['stddev(G)']);
			$data['monthlyStddevA'] = ($row['stddev(A)']);
			$data['monthlyStddevS'] = $row['stddev(S)'];
			$data['monthlyMaxRR'] = ($row['max(RR)']);
		}
		
		// Calculate dates and times when max and min measured
		$data['monthlyMaxTDate'] = array();
		$data['onthlyMinTDate'] = array();
		$data['monthlyMaxTmaxDate'] = array();
		$data['monthlyMinTmaxDate'] = array();
		$data['monthlyMaxTminDate'] = array();
		$data['monthlyMinTminDate'] = array();
		$data['monthlyMaxHDate'] = array();
		$data['monthlyMinHDate'] = array();
		$data['monthlyMaxPDate'] = array();
		$data['monthlyMinPDate'] = array();
		$data['monthlyMaxWDate'] = array();
		$data['monthlyMinWDate'] = array();
		$data['monthlyMaxGDate'] = array();
		$data['monthlyMinGDate'] = array();
		$data['monthlyMaxADate'] = array();
		$data['monthlyMinADate'] = array();
		$data['monthlyMaxDDate'] = array();
		$data['monthlyMinDDate'] = array();
		$data['monthlyMaxSDate'] = array();
		$data['monthlyMinSDate'] = array();
		$data['monthlyMaxRRDate'] = array();
		
		$result = mysqli_query($con,"
			SELECT *
			FROM alldata 
			WHERE $span
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			if(($row['T'])==$data['monthlyMaxT']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxTDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['T'])==$data['monthlyMinT']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinTDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['Tmax'])==$data['monthlyMaxTmax']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxTmaxDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['Tmax'])==$data['monthlyMinTmax']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinTmaxDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['Tmin'])==$data['monthlyMaxTmin']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxTminDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['Tmin'])==$data['monthlyMinTmin']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinTminDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if($row['H']==$data['monthlyMaxH']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxHDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if($row['H']==$data['monthlyMinH']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinHDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['P'])==$data['monthlyMaxP']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxPDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['P'])==$data['monthlyMinP']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinPDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['W'])==$data['monthlyMaxW']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxWDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['W'])==$data['monthlyMinW']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinWDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['G'])==$data['monthlyMaxG']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxGDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['G'])==$data['monthlyMinG']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinGDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['A'])==$data['monthlyMaxA']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxADate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['A'])==$data['monthlyMinA']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinADate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['D'])==$data['monthlyMaxD']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxDDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if(($row['D'])==$data['monthlyMinD']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinDDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if($row['S']==$data['monthlyMaxS']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxSDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if($row['S']==$data['monthlyMinS']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMinSDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
			if($row['RR']==$data['monthlyMaxRR']){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['monthlyMaxRRDate'],date($timeFormat, $date_temporary)."<br>".date($dateFormat, $date_temporary));
			}
		}
		
		// limit dates
		$data['monthlyMaxTDate'] = limitDates($data['monthlyMaxTDate'],15);
		$data['monthlyMinTDate'] = limitDates($data['monthlyMinTDate'],15);
		$data['monthlyMaxTmaxDate'] = limitDates($data['monthlyMaxTmaxDate'],15);
		$data['monthlyMinTmaxDate'] = limitDates($data['monthlyMinTmaxDate'],15);
		$data['monthlyMaxTminDate'] = limitDates($data['monthlyMaxTminDate'],15);
		$data['monthlyMinTminDate'] = limitDates($data['monthlyMinTminDate'],15);
		$data['monthlyMaxHDate'] = limitDates($data['monthlyMaxHDate'],15);
		$data['monthlyMinHDate'] = limitDates($data['monthlyMinHDate'],15);
		$data['monthlyMaxPDate'] = limitDates($data['monthlyMaxPDate'],15);
		$data['monthlyMinPDate'] = limitDates($data['monthlyMinPDate'],15);
		$data['monthlyMaxWDate'] = limitDates($data['monthlyMaxWDate'],15);
		$data['monthlyMinWDate'] = limitDates($data['monthlyMinWDate'],15);
		$data['monthlyMaxGDate'] = limitDates($data['monthlyMaxGDate'],15);
		$data['monthlyMinGDate'] = limitDates($data['monthlyMinGDate'],15);
		$data['monthlyMaxDDate'] = limitDates($data['monthlyMaxDDate'],15);
		$data['monthlyMinDDate'] = limitDates($data['monthlyMinDDate'],15);
		$data['monthlyMaxADate'] = limitDates($data['monthlyMaxADate'],15);
		$data['monthlyMinADate'] = limitDates($data['monthlyMinADate'],15);
		$data['monthlyMaxRRDate'] = limitDates($data['monthlyMaxRRDate'],15);
		$data['monthlyMaxSDate'] = limitDates($data['monthlyMaxSDate'],15);
		$data['monthlyMinSDate'] = limitDates($data['monthlyMinSDate'],15);
		
		// Calculate monthly precipitation
		$result = mysqli_query($con,"
			SELECT max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['monthlyRTotal'] += convertR($row['max(R)']);
			$data['daysNumber']++;
		}

		// Calculate monthly wind run
		if($displayWindUnits=="kmh"){
			$data['monthlyWindRun'] = convertW($data['monthlyAvgW']) * 24 * $data['daysNumber'];
			$data['monthlyWindRunUnits'] = "km";
		}
		if($displayWindUnits=="ms"){
			$data['monthlyWindRun'] = convertW($data['monthlyAvgW']) * 24 * $data['daysNumber'] * 3.6;
			$data['monthlyWindRunUnits'] = "km";
		}
		if($displayWindUnits=="mph"){
			$data['monthlyWindRun'] = convertW($data['monthlyAvgW']) * 24 * $data['daysNumber'];
			$data['monthlyWindRunUnits'] = "mi";
		}
		if($displayWindUnits=="kt"){
			$data['monthlyWindRun'] = convertW($data['monthlyAvgW']) * 24 * $data['daysNumber'];
			$data['monthlyWindRunUnits'] = "nm";
		}
		
		// Calculate daily avg, min, max, range and sd
		$data['dailyAvgT'] = array();
		$data['dailyMaxT'] = array();
		$data['dailyMinT'] = array();
		$data['dailyRangeT'] = array();
		$data['dailyStddevT'] = array();
		
		$data['dailyAvgTmax'] = array();
		$data['dailyMaxTmax'] = array();
		$data['dailyMinTmax'] = array();
		$data['dailyRangeTmax'] = array();
		$data['dailyStddevTmax'] = array();
		
		$data['dailyAvgTmin'] = array();
		$data['dailyMaxTmin'] = array();
		$data['dailyMinTmin'] = array();
		$data['dailyRangeTmin'] = array();
		$data['dailyStddevTmin'] = array();
		
		$data['dailyAvgD'] = array();
		$data['dailyMaxD'] = array();
		$data['dailyMinD'] = array();
		$data['dailyRangeD'] = array();
		$data['dailyStddevD'] = array();
		
		$data['dailyAvgA'] = array();
		$data['dailyMaxA'] = array();
		$data['dailyMinA'] = array();
		$data['dailyRangeA'] = array();
		$data['dailyStddevA'] = array();
		
		$data['dailyAvgH'] = array();
		$data['dailyMaxH'] = array();
		$data['dailyMinH'] = array();
		$data['dailyRangeH'] = array();
		$data['dailyStddevH'] = array();
		
		$data['dailyAvgP'] = array();
		$data['dailyMaxP'] = array();
		$data['dailyMinP'] = array();
		$data['dailyRangeP'] = array();
		$data['dailyStddevP'] = array();
		
		$data['dailyAvgW'] = array();
		$data['dailyMaxW'] = array();
		$data['dailyMinW'] = array();
		$data['dailyRangeW'] = array();
		$data['dailyStddevW'] = array();
		
		$data['dailyWindRun'] = array();
		
		$data['dailyAvgG'] = array();
		$data['dailyMaxG'] = array();
		$data['dailyMinG'] = array();
		$data['dailyRangeG'] = array();
		$data['dailyStddevG'] = array();
		
		$data['dailyAvgS'] = array();
		$data['dailyMaxS'] = array();
		$data['dailyMinS'] = array();
		$data['dailyRangeS'] = array();
		$data['dailyStddevS'] = array();
		
		$data['dailyAvgRR'] = array();
		$data['dailyMaxRR'] = array();
		$data['dailyMinRR'] = array();
		$data['dailyRangeRR'] = array();
		$data['dailyStddevRR'] = array();
		
		$data['dailyR'] = array();
		$data['dailyCumulativeR'] = array();
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(Tmin), avg(Tmax), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(T), max(Tmin), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(T), min(Tmin), min(Tmax), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(Tmin), stddev(Tmax), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR), max(R), min(R), min(RR), avg(RR), stddev(RR)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['dailyAvgT'],($row['avg(T)']));
			array_push($data['dailyMaxT'],($row['max(T)']));
			array_push($data['dailyMinT'],($row['min(T)']));
			array_push($data['dailyRangeT'],(($row['max(T)'])-($row['min(T)'])));
			array_push($data['dailyStddevT'],($row['stddev(T)']));
			
			array_push($data['dailyAvgTmax'],($row['avg(Tmax)']));
			array_push($data['dailyMaxTmax'],($row['max(Tmax)']));
			array_push($data['dailyMinTmax'],($row['min(Tmax)']));
			array_push($data['dailyRangeTmax'],(($row['max(Tmax)'])-($row['min(Tmax)'])));
			array_push($data['dailyStddevTmax'],($row['stddev(Tmax)']));
			
			array_push($data['dailyAvgTmin'],($row['avg(Tmin)']));
			array_push($data['dailyMaxTmin'],($row['max(Tmin)']));
			array_push($data['dailyMinTmin'],($row['min(Tmin)']));
			array_push($data['dailyRangeTmin'],(($row['max(Tmin)'])-($row['min(Tmin)'])));
			array_push($data['dailyStddevTmin'],($row['stddev(Tmin)']));
			
			array_push($data['dailyAvgD'],($row['avg(D)']));
			array_push($data['dailyMaxD'],($row['max(D)']));
			array_push($data['dailyMinD'],($row['min(D)']));
			array_push($data['dailyRangeD'],(($row['max(D)'])-($row['min(D)'])));
			array_push($data['dailyStddevD'],($row['stddev(D)']));
			
			array_push($data['dailyAvgA'],($row['avg(A)']));
			array_push($data['dailyMaxA'],($row['max(A)']));
			array_push($data['dailyMinA'],($row['min(A)']));
			array_push($data['dailyRangeA'],(($row['max(A)'])-($row['min(A)'])));
			array_push($data['dailyStddevA'],($row['stddev(A)']));
			
			array_push($data['dailyAvgH'],$row['avg(H)']);
			array_push($data['dailyMaxH'],$row['max(H)']);
			array_push($data['dailyMinH'],$row['min(H)']);
			array_push($data['dailyRangeH'],$row['max(H)']-$row['min(H)']);
			array_push($data['dailyStddevH'],$row['stddev(H)']);
			
			array_push($data['dailyAvgP'],($row['avg(P)']));
			array_push($data['dailyMaxP'],($row['max(P)']));
			array_push($data['dailyMinP'],($row['min(P)']));
			array_push($data['dailyRangeP'],(($row['max(P)'])-($row['min(P)'])));
			array_push($data['dailyStddevP'],($row['stddev(P)']));
			
			array_push($data['dailyAvgW'],($row['avg(W)']));
			array_push($data['dailyMaxW'],($row['max(W)']));
			array_push($data['dailyMinW'],($row['min(W)']));
			array_push($data['dailyRangeW'],(($row['max(W)'])-($row['min(W)'])));
			array_push($data['dailyStddevW'],($row['stddev(W)']));
			
			array_push($data['dailyAvgG'],($row['avg(G)']));
			array_push($data['dailyMaxG'],($row['max(G)']));
			array_push($data['dailyMinG'],($row['min(G)']));
			array_push($data['dailyRangeG'],(($row['max(G)'])-($row['min(G)'])));
			array_push($data['dailyStddevG'],($row['stddev(G)']));
			
			array_push($data['dailyAvgRR'],($row['avg(RR)']));
			array_push($data['dailyMaxRR'],($row['max(RR)']));
			array_push($data['dailyMinRR'],($row['min(RR)']));
			array_push($data['dailyRangeRR'],(($row['max(RR)'])-($row['min(RR)'])));
			array_push($data['dailyStddevRR'],($row['stddev(RR)']));
			
			array_push($data['dailyAvgS'],$row['avg(S)']);
			array_push($data['dailyMaxS'],$row['max(S)']);
			array_push($data['dailyMinS'],$row['min(S)']);
			array_push($data['dailyRangeS'],$row['max(S)']-$row['min(S)']);
			array_push($data['dailyStddevS'],$row['stddev(S)']);
			
			array_push($data['dailyR'], ($row['max(R)']));
			$data['dailyCumulativeRTemporary'] += ($row['max(R)']);
			array_push($data['dailyCumulativeR'], $data['dailyCumulativeRTemporary']);
		}
		
		// Calculate daily windrun

		if($displayWindUnits=="kmh"){
			for($i=0;$i<$data['daysNumber'];$i++){
				array_push($data['dailyWindRun'],$data['dailyAvgW'][$i]*24);
			}
			$data['dailyWindRunUnits'] = "km";
		}
		if($displayWindUnits=="ms"){
			for($i=0;$i<$data['daysNumber'];$i++){
				array_push($data['dailyWindRun'],$data['dailyAvgW'][$i]*24*3.6);
			}
			$data['dailyWindRunUnits'] = "km";
		}
		if($displayWindUnits=="mph"){
			for($i=0;$i<$data['daysNumber'];$i++){
				array_push($data['dailyWindRun'],$data['dailyAvgW'][$i]*24);
			}
			$data['dailyWindRunUnits'] = "mi";
		}
		// Calculate hourly avg, min, max, range and sd
		$data['hourlyAvgT'] = array();
		$data['hourlyMaxT'] = array();
		$data['hourlyMinT'] = array();
		$data['hourlyRangeT'] = array();
		$data['hourlyStddevT'] = array();
		
		$data['hourlyAvgTmax'] = array();
		$data['hourlyMaxTmax'] = array();
		$data['hourlyMinTmax'] = array();
		$data['hourlyRangeTmax'] = array();
		$data['hourlyStddevTmax'] = array();
		
		$data['hourlyAvgTmin'] = array();
		$data['hourlyMaxTmin'] = array();
		$data['hourlyMinTmin'] = array();
		$data['hourlyRangeTmin'] = array();
		$data['hourlyStddevTmin'] = array();
		
		$data['hourlyAvgD'] = array();
		$data['hourlyMaxD'] = array();
		$data['hourlyMinD'] = array();
		$data['hourlyRangeD'] = array();
		$data['hourlyStddevD'] = array();
		
		$data['hourlyAvgA'] = array();
		$data['hourlyMaxA'] = array();
		$data['hourlyMinA'] = array();
		$data['hourlyRangeA'] = array();
		$data['hourlyStddevA'] = array();
		
		$data['hourlyAvgH'] = array();
		$data['hourlyMaxH'] = array();
		$data['hourlyMinH'] = array();
		$data['hourlyRangeH'] = array();
		$data['hourlyStddevH'] = array();
		
		$data['hourlyAvgP'] = array();
		$data['hourlyMaxP'] = array();
		$data['hourlyMinP'] = array();
		$data['hourlyRangeP'] = array();
		$data['hourlyStddevP'] = array();
		
		$data['hourlyAvgW'] = array();
		$data['hourlyMaxW'] = array();
		$data['hourlyMinW'] = array();
		$data['hourlyRangeW'] = array();
		$data['hourlyStddevW'] = array();
		
		$data['hourlyWindRun'] = array();
		
		$data['hourlyAvgG'] = array();
		$data['hourlyMaxG'] = array();
		$data['hourlyMinG'] = array();
		$data['hourlyRangeG'] = array();
		$data['hourlyStddevG'] = array();
		
		$data['hourlyAvgS'] = array();
		$data['hourlyMaxS'] = array();
		$data['hourlyMinS'] = array();
		$data['hourlyRangeS'] = array();
		$data['hourlyStddevS'] = array();
		
		$data['hourlyAvgRR'] = array();
		$data['hourlyMaxRR'] = array();
		$data['hourlyMinRR'] = array();
		$data['hourlyRangeRR'] = array();
		$data['hourlyStddevRR'] = array();
		
		$hourlyR = array();
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(Tmin), avg(Tmax), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(T), max(Tmin), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(T), min(Tmin), min(Tmax), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(Tmin), stddev(Tmax), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR), min(RR), avg(RR), stddev(RR)
			FROM alldata 
			WHERE $span
			GROUP BY HOUR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['hourlyAvgT'],($row['avg(T)']));
			array_push($data['hourlyMaxT'],($row['max(T)']));
			array_push($data['hourlyMinT'],($row['min(T)']));
			array_push($data['hourlyRangeT'],(($row['max(T)'])-($row['min(T)'])));
			array_push($data['hourlyStddevT'],($row['stddev(T)']));
			
			array_push($data['hourlyAvgTmax'],($row['avg(Tmax)']));
			array_push($data['hourlyMaxTmax'],($row['max(Tmax)']));
			array_push($data['hourlyMinTmax'],($row['min(Tmax)']));
			array_push($data['hourlyRangeTmax'],(($row['max(Tmax)'])-($row['min(Tmax)'])));
			array_push($data['hourlyStddevTmax'],($row['stddev(Tmax)']));
			
			array_push($data['hourlyAvgTmin'],($row['avg(Tmin)']));
			array_push($data['hourlyMaxTmin'],($row['max(Tmin)']));
			array_push($data['hourlyMinTmin'],($row['min(Tmin)']));
			array_push($data['hourlyRangeTmin'],(($row['max(Tmin)'])-($row['min(Tmin)'])));
			array_push($data['hourlyStddevTmin'],($row['stddev(Tmin)']));
			
			array_push($data['hourlyAvgD'],($row['avg(D)']));
			array_push($data['hourlyMaxD'],($row['max(D)']));
			array_push($data['hourlyMinD'],($row['min(D)']));
			array_push($data['hourlyRangeD'],(($row['max(D)'])-($row['min(D)'])));
			array_push($data['hourlyStddevD'],($row['stddev(D)']));
			
			array_push($data['hourlyAvgA'],($row['avg(A)']));
			array_push($data['hourlyMaxA'],($row['max(A)']));
			array_push($data['hourlyMinA'],($row['min(A)']));
			array_push($data['hourlyRangeA'],(($row['max(A)'])-($row['min(A)'])));
			array_push($data['hourlyStddevA'],($row['stddev(A)']));
			
			array_push($data['hourlyAvgH'],$row['avg(H)']);
			array_push($data['hourlyMaxH'],$row['max(H)']);
			array_push($data['hourlyMinH'],$row['min(H)']);
			array_push($data['hourlyRangeH'],$row['max(H)']-$row['min(H)']);
			array_push($data['hourlyStddevH'],$row['stddev(H)']);
			
			array_push($data['hourlyAvgP'],($row['avg(P)']));
			array_push($data['hourlyMaxP'],($row['max(P)']));
			array_push($data['hourlyMinP'],($row['min(P)']));
			array_push($data['hourlyRangeP'],(($row['max(P)'])-($row['min(P)'])));
			array_push($data['hourlyStddevP'],($row['stddev(P)']));
			
			array_push($data['hourlyAvgW'],($row['avg(W)']));
			array_push($data['hourlyMaxW'],($row['max(W)']));
			array_push($data['hourlyMinW'],($row['min(W)']));
			array_push($data['hourlyRangeW'],(($row['max(W)'])-($row['min(W)'])));
			array_push($data['hourlyStddevW'],($row['stddev(W)']));
			
			array_push($data['hourlyAvgG'],($row['avg(G)']));
			array_push($data['hourlyMaxG'],($row['max(G)']));
			array_push($data['hourlyMinG'],($row['min(G)']));
			array_push($data['hourlyRangeG'],(($row['max(G)'])-($row['min(G)'])));
			array_push($data['hourlyStddevG'],($row['stddev(G)']));
			
			array_push($data['hourlyAvgRR'],($row['avg(RR)']));
			array_push($data['hourlyMaxRR'],($row['max(RR)']));
			array_push($data['hourlyMinRR'],($row['min(RR)']));
			array_push($data['hourlyRangeRR'],(($row['max(RR)'])-($row['min(RR)'])));
			array_push($data['hourlyStddevRR'],($row['stddev(RR)']));
			
			array_push($data['hourlyAvgS'],$row['avg(S)']);
			array_push($data['hourlyMaxS'],$row['max(S)']);
			array_push($data['hourlyMinS'],$row['min(S)']);
			array_push($data['hourlyRangeS'],$row['max(S)']-$row['min(S)']);
			array_push($data['hourlyStddevS'],$row['stddev(S)']);
		}
		
		$data['previous'] = 0;
		$data['dayR'] = 1;
		
		$result = mysqli_query($con,"
			SELECT R, DAY(DateTime), HOUR(DateTime)
			FROM alldata 
			WHERE $span
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			if($data['dayR']==$row['DAY(DateTime)']){
				if(isset($data[${"hourlyR".$row['HOUR(DateTime)']}])){
					$data[${"hourlyR".$row['HOUR(DateTime)']}] = $data[${"hourlyR".$row['HOUR(DateTime)']}] + ($row['R']) - $data['previous'];
				}
				else{
					$data[${"hourlyR".$row['HOUR(DateTime)']}] = ($row['R']) - $data['previous'];
				}
			}
			if($data['dayR']!=$row['DAY(DateTime)']){
				$data['dayR'] = $row['DAY(DateTime)'];
				$data['previous'] = 0;
				$data[${"hourlyR".$row['HOUR(DateTime)']}] = $data[${"hourlyR".$row['HOUR(DateTime)']}] + ($row['R']) - $data['previous'];
			}
			$data['previous'] = ($row['R']);
		}
		$data['hourlyR'] = array($data['hourlyR0'],$data['hourlyR1'],$data['hourlyR2'],$data['hourlyR3'],$data['hourlyR4'],$data['hourlyR5'],$data['hourlyR6'],$data['hourlyR7'],$data['hourlyR8'],$data['hourlyR9'],$data['hourlyR10'],$data['hourlyR11'],$data['hourlyR12'],$data['hourlyR13'],$data['hourlyR14'],$data['hourlyR15'],$data['hourlyR16'],$data['hourlyR17'],$data['hourlyR18'],$data['hourlyR19'],$data['hourlyR20'],$data['hourlyR21'],$data['hourlyR22'],$data['hourlyR23']);
		
		// --------------------------- STATS ------------------------------- 
		$data['highestTHourValue'] = max($data['hourlyAvgT']);
		$data['highestTHour'] = array_search(max($data['hourlyAvgT']), $data['hourlyAvgT']);
		$data['lowestTHourValue'] = min($data['hourlyAvgT']);
		$data['lowestTHour'] = array_search(min($data['hourlyAvgT']), $data['hourlyAvgT']);
		$data['highestTDayValue'] = max($data['dailyAvgT']);
		$data['day'] = array_search(max($data['dailyAvgT']), $data['dailyAvgT']) + 1;
		$data['highestTDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestTDayValue'] = min($data['dailyAvgT']);
		$data['day'] = array_search(min($data['dailyAvgT']), $data['dailyAvgT']) + 1;
		$data['lowestTDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestHHourValue'] = max($data['hourlyAvgH']);
		$data['highestHHour'] = array_search(max($data['hourlyAvgH']), $data['hourlyAvgH']);
		$data['lowestHHourValue'] = min($data['hourlyAvgH']);
		$data['lowestHHour'] = array_search(min($data['hourlyAvgH']), $data['hourlyAvgH']);
		$data['highestHDayValue'] = max($data['dailyAvgH']);
		$data['day'] = array_search(max($data['dailyAvgH']), $data['dailyAvgH']) + 1;
		$data['highestHDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestHDayValue'] = min($data['dailyAvgH']);
		$data['day'] = array_search(min($data['dailyAvgH']), $data['dailyAvgH']) + 1;
		$data['lowestHDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestPHourValue'] = max($data['hourlyAvgP']);
		$data['highestPHour'] = array_search(max($data['hourlyAvgP']), $data['hourlyAvgP']);
		$data['lowestPHourValue'] = min($data['hourlyAvgP']);
		$data['lowestPHour'] = array_search(min($data['hourlyAvgP']), $data['hourlyAvgP']);
		$data['highestPDayValue'] = max($data['dailyAvgP']);
		$data['day'] = array_search(max($data['dailyAvgP']), $data['dailyAvgP']) + 1;
		$data['highestPDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestPDayValue'] = min($data['dailyAvgP']);
		$data['day'] = array_search(min($data['dailyAvgP']), $data['dailyAvgP']) + 1;
		$data['lowestPDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestDHourValue'] = max($data['hourlyAvgD']);
		$data['highestDHour'] = array_search(max($data['hourlyAvgD']), $data['hourlyAvgD']);
		$data['lowestDHourValue'] = min($data['hourlyAvgD']);
		$data['lowestDHour'] = array_search(min($data['hourlyAvgD']), $data['hourlyAvgD']);
		$data['highestDDayValue'] = max($data['dailyAvgD']);
		$data['day'] = array_search(max($data['dailyAvgD']), $data['dailyAvgD']) + 1;
		$data['highestDDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestDDayValue'] = min($data['dailyAvgD']);
		$data['day'] = array_search(min($data['dailyAvgD']), $data['dailyAvgD']) + 1;
		$data['lowestDDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestAHourValue'] = max($data['hourlyAvgA']);
		$data['highestAHour'] = array_search(max($data['hourlyAvgA']), $data['hourlyAvgA']);
		$data['lowestAHourValue'] = min($data['hourlyAvgA']);
		$data['lowestAHour'] = array_search(min($data['hourlyAvgA']), $data['hourlyAvgA']);
		$data['highestADayValue'] = max($data['dailyAvgA']);
		$data['day'] = array_search(max($data['dailyAvgA']), $data['dailyAvgA']) + 1;
		$data['highestADay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestADayValue'] = min($data['dailyAvgA']);
		$data['day'] = array_search(min($data['dailyAvgA']), $data['dailyAvgA']) + 1;
		$data['lowestADay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestWHourValue'] = max($data['hourlyAvgW']);
		$data['highestWHour'] = array_search(max($data['hourlyAvgW']), $data['hourlyAvgW']);
		$data['lowestWHourValue'] = min($data['hourlyAvgW']);
		$data['lowestWHour'] = array_search(min($data['hourlyAvgW']), $data['hourlyAvgW']);
		$data['highestWDayValue'] = max($data['dailyAvgW']);
		$data['day'] = array_search(max($data['dailyAvgW']), $data['dailyAvgW']) + 1;
		$data['highestWDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestWDayValue'] = min($data['dailyAvgW']);
		$data['day'] = array_search(min($data['dailyAvgW']), $data['dailyAvgW']) + 1;
		$data['lowestWDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestGHourValue'] = max($data['hourlyAvgG']);
		$data['highestGHour'] = array_search(max($data['hourlyAvgG']), $data['hourlyAvgG']);
		$data['lowestGHourValue'] = min($data['hourlyAvgG']);
		$data['lowestGHour'] = array_search(min($data['hourlyAvgG']), $data['hourlyAvgG']);
		$data['highestGDayValue'] = max($data['dailyAvgG']);
		$data['day'] = array_search(max($data['dailyAvgG']), $data['dailyAvgG']) + 1;
		$data['highestGDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestGDayValue'] = min($data['dailyAvgG']);
		$data['day'] = array_search(min($data['dailyAvgG']), $data['dailyAvgG']) + 1;
		$data['lowestGDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestSHourValue'] = max($data['hourlyAvgS']);
		$data['highestSHour'] = array_search(max($data['hourlyAvgS']), $data['hourlyAvgS']);
		$data['lowestSHourValue'] = min($data['hourlyAvgS']);
		$data['lowestSHour'] = array_search(min($data['hourlyAvgS']), $data['hourlyAvgS']);
		$data['highestSDayValue'] = max($data['dailyAvgS']);
		$data['day'] = array_search(max($data['dailyAvgS']), $data['dailyAvgS']) + 1;
		$data['highestSDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		$data['lowestSDayValue'] = min($data['dailyAvgS']);
		$data['day'] = array_search(min($data['dailyAvgS']), $data['dailyAvgS']) + 1;
		$data['lowestSDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$data['highestRDayValue'] = max($data['dailyR']);
		$data['day'] = array_search(max($data['dailyR']), $data['dailyR']) + 1;
		$data['highestRDay'] = strtotime($chosenYear."-".$chosenMonth."-".$data['day']);
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(S), avg(D), avg(A), avg(W), avg(G), DAY(DateTime), HOUR(DateTime)
			FROM alldata 
			WHERE $span
			GROUP BY DAY(DateTime), HOUR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['string'] = $row['HOUR(DateTime)']." ".lang("hAbbr",'').", ".lang("day","c")." ".$row['DAY(DateTime)'];
			$data['temporaryT'][$data['string']] = ($row['avg(T)']);
			$data['temporaryH'][$data['string']] = $row['avg(H)'];
			$data['temporaryP'][$data['string']] = ($row['avg(P)']);
			$data['temporaryS'][$data['string']] = $row['avg(S)'];
			$data['temporaryD'][$data['string']] = ($row['avg(D)']);
			$data['temporaryA'][$data['string']] = ($row['avg(A)']);
			$data['temporaryW'][$data['string']] = ($row['avg(W)']);
			$data['temporaryG'][$data['string']] = ($row['avg(G)']);
		}
		$data['highestTHourValueAbs'] = max($data['temporaryT']);
		$data['highestTHourAbs'] = array_search(max($data['temporaryT']), $data['temporaryT']);
		$data['lowestTHourValueAbs'] = min($data['temporaryT']);
		$data['lowestTHourAbs'] = array_search(min($data['temporaryT']), $data['temporaryT']);
		
		$data['highestHHourValueAbs'] = max($data['temporaryH']);
		$data['highestHHourAbs'] = array_search(max($data['temporaryH']), $data['temporaryH']);
		$data['lowestHHourValueAbs'] = min($data['temporaryH']);
		$data['lowestHHourAbs'] = array_search(min($data['temporaryH']), $data['temporaryH']);
		
		$data['highestPHourValueAbs'] = max($data['temporaryP']);
		$data['highestPHourAbs'] = array_search(max($data['temporaryP']), $data['temporaryP']);
		$data['lowestPHourValueAbs'] = min($data['temporaryP']);
		$data['lowestPHourAbs'] = array_search(min($data['temporaryP']), $data['temporaryP']);
		
		$data['highestAHourValueAbs'] = max($data['temporaryA']);
		$data['highestAHourAbs'] = array_search(max($data['temporaryA']), $data['temporaryA']);
		$data['lowestAHourValueAbs'] = min($data['temporaryA']);
		$data['lowestAHourAbs'] = array_search(min($data['temporaryA']), $data['temporaryA']);
		
		$data['highestDHourValueAbs'] = max($data['temporaryD']);
		$data['highestDHourAbs'] = array_search(max($data['temporaryD']), $data['temporaryD']);
		$data['lowestDHourValueAbs'] = min($data['temporaryD']);
		$data['lowestDHourAbs'] = array_search(min($data['temporaryD']), $data['temporaryD']);
		
		$data['highestWHourValueAbs'] = max($data['temporaryW']);
		$data['highestWHourAbs'] = array_search(max($data['temporaryW']), $data['temporaryW']);
		$data['lowestWHourValueAbs'] = min($data['temporaryW']);
		$data['lowestWHourAbs'] = array_search(min($data['temporaryW']), $data['temporaryW']);
		
		$data['highestGHourValueAbs'] = max($data['temporaryG']);
		$data['highestGHourAbs'] = array_search(max($data['temporaryG']), $data['temporaryG']);
		$data['lowestGHourValueAbs'] = min($data['temporaryG']);
		$data['lowestGHourAbs'] = array_search(min($data['temporaryG']), $data['temporaryG']);
		
		$data['highestSHourValueAbs'] = max($data['temporaryS']);
		$data['highestSHourAbs'] = array_search(max($data['temporaryS']), $data['temporaryS']);
		$data['lowestSHourValueAbs'] = min($data['temporaryS']);
		$data['lowestSHourAbs'] = array_search(min($data['temporaryS']), $data['temporaryS']);
		
		$data['wetDays'] = array();
		
		$result = mysqli_query($con,"
			SELECT DAY(DateTime), DateTime
			FROM alldata 
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime) 
			HAVING Max(R)!=0 AND YEAR(DateTime)=$chosenYear AND MONTH(DateTime)=$chosenMonth
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['wetDays'],$row['DAY(DateTime)']);
		}

		if(($chosenYear.$chosenMonth)!=(date("Y").date('n'))){
			file_put_contents("cache/monthly".$chosenYear."_".$chosenMonth.".txt",json_encode($data));
		}
		
	} // end else
	
	
	function limitDates($array,$maxElement){
		for($i=0;$i<$maxElement;$i++){
			if(array_key_exists($i,$array)){
				$resultArr[] = $array[$i];
			}
		}
		return $resultArr;
	}
	
	function checkCount($array){
		if(count($array)>10){
			return lang('more than 10<br>instances','l');
		}
		else{
			return implode("<br>",$array);
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

	// PDF
    include($baseURL."scripts/mpdf60/mpdf.php");
    if($defaultPaperSize=="letter"){
        $mpdf = new mPDF('','Letter');
    }
    else{
        $mpdf = new mPDF();
    }
    $mpdf->SetTitle(lang("monthly report",'w'));
    $mpdf->SetAuthor("Meteotemplate");
    $mpdf->SetCreator("Meteotemplate");

    $mpdf->setFooter('<span style="color:black;font-style:normal;font-size:0.9em">'.$pageURL.$path.'</span>||<span style="color:black;font-style:normal">Meteotemplate</span>');

	$mpdf->WriteHTML('
		<style>
			.cellHeading{
				text-align: center!important;
				background: #'.$color_schemes[$design2]['900'].';
				color:white;
				padding-bottom: 5px;
			}
			.cellFirst{
				width: 60%;
				padding-left:5px;
				text-align:left;
				font-size: 10pt;
			}
			.cellSecond{
				width: 25%;
			}
			.headingImg{
				width: 30px;
				padding: 5px;
			}
			#summaryTable td{
				text-align:center;
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
			.table{
				width: 100%;
			}
			.table tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['200'].';
			}
			.table tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
			.value{
				text-align:right;
				padding-right:2px;
			}
		</style>
	');
	$mpdf->WriteHTML('<body style="font-family:Helvetica">');

    $mpdf->WriteHTML('
        <table style="width:100%" cellspacing="0">
			<tr>
                <td style="text-align:center;background: #'.$color_schemes[$design2]['900'].';color:white">
                    <h1 style="font-size:1.5em;color:white">'.lang("monthly report",'w').' - '.lang('summary','c').'</h1>
					<h2 style="font-size:1.2em;color:white">'.lang("month".($chosenMonth*1),"c")." ".$chosenYear.'</h2>
                </td>
            </tr>
		</table>
		<br>
	');
	$mpdf->WriteHTML('
		<table style="width:100%" id="summaryTable" cellspacing="0">
			<thead>
				<tr>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						
					</th>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang('avgAbbr','c').'
					</th>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang('maximumAbbr','c').'
					</th>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang('minimumAbbr','c').'
					</th>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang('range','c').'
					</th>
					<th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang("sdAbbr",'').'
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("temperature",'c')." (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyAvgT']),$decimalT).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMaxTmax']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxTmaxDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMinTmin']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinTminDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyRangeT']),$decimalT).'
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyStddevT']),$decimalT+1).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("apparent temperature",'c'). " (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyAvgA']),$decimalT).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMaxA']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxADate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMinA']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinADate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyRangeA']),$decimalT).'
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyStddevA']),$decimalT+1).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("dew point",'c'). " (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyAvgD']),$decimalT).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMaxD']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxDDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMinD']),$decimalT).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinDDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyRangeD']),$decimalT).'
					</td>
					<td>
						'.number_format(convertTStddev($data['monthlyStddevD']),$decimalT+1).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("humidity",'c'). " (%)".' 
					</td>
					<td>
						<strong>'.number_format($data['monthlyAvgH'],$decimalH).'</strong>
					</td>
					<td>
						<strong>'.number_format($data['monthlyMaxH'],$decimalH).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxHDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format($data['monthlyMinH'],$decimalH).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinHDate']) .'
						</div>
					</td>
					<td>
						'.number_format($data['monthlyRangeH'],$decimalH).'
					</td>
					<td>
						'.number_format($data['monthlyStddevH'],$decimalH+1).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("pressure",'c'). " (".unitFormatter($displayPressUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertP($data['monthlyAvgP']),$decimalP,".","").'</strong>
					</td>
					<td>
						<strong>'.number_format(convertP($data['monthlyMaxP']),$decimalP,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxPDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertP($data['monthlyMinP']),$decimalP,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinPDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertP($data['monthlyRangeP']),$decimalP,".","").'
					</td>
					<td>
						'.number_format(convertP($data['monthlyStddevP']),$decimalP+1,".","").'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("wind speed",'c'). " (".unitFormatter($displayWindUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyAvgW']),$decimalW).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyMaxW']),$decimalW).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxWDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyMinW']),$decimalW).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinWDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertW($data['monthlyRangeW']),$decimalW).'
					</td>
					<td>
						'.number_format(convertW($data['monthlyStddevW']),$decimalW+1).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("wind gust",'c'). " (".unitFormatter($displayWindUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyAvgG']),$decimalW).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyMaxG']),$decimalW).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxGDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertW($data['monthlyMinG']),$decimalW).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinGDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertW($data['monthlyRangeG']),$decimalW).'
					</td>
					<td>
						'.number_format(convertW($data['monthlyStddevG']),$decimalW+1).'
					</td>
				</tr>');

	if($solarSensor){
		$mpdf->WriteHTML('
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("solar radiation",'c'). " (W/m<sup>2</sup>)".'
					</td>
					<td>
						<strong>'.number_format($data['monthlyAvgS'],$decimalS,".","").'</strong>
					</td>
					<td>
						<strong>'.number_format($data['monthlyMaxS'],$decimalS,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMaxSDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format($data['monthlyMinS'],$decimalS,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['monthlyMinSDate']) .'
						</div>
					</td>
					<td>
						'.number_format($data['monthlyRangeS'],$decimalS,".","").'
					</td>
					<td>
						'.number_format($data['monthlyStddevS'],$decimalS+1,".","").'
					</td>
				</tr>
		');
	}
	$mpdf->WriteHTML('
			<tr>
				<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
					'.lang("rain rate",'c'). " (".unitFormatter($displayRainUnits).")".' 
				</td>
				<td>
					
				</td>
				<td>
					<strong>'.number_format(convertR($data['monthlyMaxRR']),$decimalR).'</strong>
					<div style="font-size:10pt">
						'.checkSize($data['monthlyMaxRRDate']) .'
					</div>
				</td>
				<td colspan="3">
				</td>
			</tr>
			<tr>
				<td style="background:#'.$color_schemes[$design2]['700'].'">
				</td>
				<td colspan="5" style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
					'.lang('total','c').'
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px">
					'.lang("precipitation",'c'). " (".unitFormatter($displayRainUnits).")".' 
				</td>
				<td colspan="5">
					<strong>'.number_format(convertR($data['monthlyRTotal']),$decimalR).'</strong>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px">
					'.lang("wind run",'c'). " (".$data['monthlyWindRunUnits'].")".' 
				</td>
				<td colspan="5">
					<strong>'.number_format($data['monthlyWindRun'],1,".","") .'</strong>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="width:100%;text-align:right;font-size:10px">
		* '.lang('more than one').'
	</div>
	');
	
	$mpdf->WriteHTML('<pagebreak />');

	$mpdf->WriteHTML('
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/temp.png" class="headingImg" ><br>'.lang('temperature','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("warmest hour","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestTHour']." ".lang("hAbbr",'l').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertT($data['highestTHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("absolute warmest hour","c").'
				</th>
				<td>
					'. $data['highestTHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestTHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("coldest hour","c").'
				</th>
				<td>
					'. $data['lowestTHour']." ".lang("hAbbr",'l').'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestTHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("absolute coldest hour","c").'
				</th>
				<td>
					'. $data['lowestTHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestTHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("warmest day","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestTDay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestTDayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("coldest day","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestTDay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestTDayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/apparent.png" class="headingImg" ><br>'. lang('apparent temperature','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average apparent temperature","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestAHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertT($data['highestAHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average apparent temperature","c").'
				</th>
				<td>
					'. $data['highestAHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestAHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average apparent temperature","c").'
				</th>
				<td>
					'. $data['lowestAHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestAHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average apparent temperature","c").'
				</th>
				<td>
					'. $data['lowestAHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestAHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average apparent temperature","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestADay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestADayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average apparent temperature","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestADay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestADayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/dewpoint.png" class="headingImg" ><br>'. lang('dewpoint','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average dew point","").'
				</th>
				<td class="cellSecond">
					'. $data['highestDHour']." ".lang("hAbbr",'l').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertT($data['highestDHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average dew point","c").'
				</th>
				<td>
					'. $data['highestDHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestDHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average dew point","c").'
				</th>
				<td>
					'. $data['lowestDHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestDHourValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average dew point","c").'
				</th>
				<td>
					'. $data['lowestDHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestDHourValueAbs']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average dew point","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestDDay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['highestDDayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average dew point","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestDDay']).'
				</td>
				<td class="value">
					'. number_format(convertT($data['lowestDDayValue']),$decimalT,'.','')." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/humidity.png" class="headingImg" ><br>'. lang('humidity','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average humidity","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestHHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value cellSecond">
					'. number_format($data['highestHHourValue'],$decimalH,'.','')." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average humidity","c").'
				</th>
				<td>
					'. $data['highestHHourAbs'].'
				</td>
				<td class="value">
					'. number_format($data['highestHHourValueAbs'],$decimalH,'.','')." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average humidity","c").'
				</th>
				<td>
					'. $data['lowestHHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format($data['lowestHHourValue'],$decimalH,'.','')." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average humidity","c").'
				</th>
				<td>
					'. $data['lowestHHourAbs'].'
				</td>
				<td class="value">
					'. number_format($data['lowestHHourValueAbs'],$decimalH,'.','')." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average humidity","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestHDay']).'
				</td>
				<td class="value">
					'. number_format($data['highestHDayValue'],$decimalH,'.','')." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average humidity","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestHDay']).'
				</td>
				<td class="value">
					'. number_format($data['lowestHDayValue'],$decimalH,'.','')." %".'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/pressure.png" class="headingImg" ><br>'. lang('pressure','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average pressure","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestPHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertP($data['highestPHourValue']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average pressure","c").'
				</th>
				<td>
					'. $data['highestPHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertP($data['highestPHourValueAbs']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average pressure","c").'
				</th>
				<td>
					'. $data['lowestPHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format(convertP($data['lowestPHourValue']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average pressure","c").'
				</th>
				<td>
					'. $data['lowestPHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertP($data['lowestPHourValueAbs']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average pressure","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestPDay']).'
				</td>
				<td class="value">
					'. number_format(convertP($data['highestPDayValue']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average pressure","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestPDay']).'
				</td>
				<td class="value">
					'. number_format(convertP($data['lowestPDayValue']),$decimalP,'.','')." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/wind.png" class="headingImg" ><br>'. lang('wind speed','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average wind speed","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestWHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertW($data['highestWHourValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average wind speed","c").'
				</th>
				<td>
					'. $data['highestWHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertW($data['highestWHourValueAbs']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average wind speed","c").'
				</th>
				<td>
					'. $data['lowestWHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestWHourValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average wind speed","c").'
				</th>
				<td>
					'. $data['lowestWHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestWHourValueAbs']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average wind speed","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestWDay']).'
				</td>
				<td class="value">
					'. number_format(convertW($data['highestWDayValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average wind speed","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestWDay']).'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestWDayValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
		</table>
		<br>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/gust.png" class="headingImg" ><br>'. lang('wind gust','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with highest average wind gust","c").'
				</th>
				<td class="cellSecond">
					'. $data['highestGHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value cellSecond">
					'. number_format(convertW($data['highestGHourValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute highest average wind gust","c").'
				</th>
				<td>
					'. $data['highestGHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertW($data['highestGHourValueAbs']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with lowest average gust","c").'
				</th>
				<td>
					'. $data['lowestGHour']." ".lang("hAbbr",'').'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestGHourValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("hour with absolute lowest average gust","c").'
				</th>
				<td>
					'. $data['lowestGHourAbs'].'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestGHourValueAbs']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average gust","c").'
				</th>
				<td>
					'. date($dateFormat,$data['highestGDay']).'
				</td>
				<td class="value">
					'. number_format(convertW($data['highestGDayValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average gust","c").'
				</th>
				<td>
					'. date($dateFormat,$data['lowestGDay']).'
				</td>
				<td class="value">
					'. number_format(convertW($data['lowestGDayValue']),$decimalW,'.','')." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
		</table>
		<br>
	');
	if($solarSensor){
		$mpdf->WriteHTML('
			<table class="table" cellspacing="0" cellpadding="2">
				<tr>
					<th colspan="3" class="cellHeading">
						<img src="../../icons/sun.png" class="headingImg" ><br>'. lang('solar radiation','c').'
					</th>
				</tr>
				<tr>
					<th class="cellFirst">
						'. lang("hour with highest average solar radiation","").'
					</th>
					<td class="cellSecond">
						'. $data['highestSHour']." ".lang("hAbbr",'l').'
					</td>
					<td class="value cellSecond">
						'. number_format($data['highestSHourValue'],$decimalS,'.','')." W/m2".'
					</td>
				</tr>
				<tr>
					<th class="cellFirst">
						'. lang("hour with absolute highest average solar radiation","c").'
					</th>
					<td>
						'. $data['highestSHourAbs'].'
					</td>
					<td class="value">
						'. number_format($data['highestSHourValueAbs'],$decimalS,'.','')." W/m2".'
					</td>
				</tr>
				<tr>
					<th class="cellFirst">
						'. lang("day with highest average solar radiation","c").'
					</th>
					<td>
						'. date($dateFormat,$data['highestSDay']).'
					</td>
					<td class="value">
						'. number_format($data['highestSDayValue'],$decimalS,'.','')." W/m2".'
					</td>
				</tr>
			</table>
			<br>
		');
	}
	$mpdf->WriteHTML('
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/rain.png" class="headingImg" ><br>'. lang('precipitation','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst" >
					'. lang("days with precipitation","c").'
				</th>
				<td class="cellSecond">
					'. count($data['wetDays']).'
				</td>
				<td class="cellSecond" style="text-align:center">
					'. lang("days",'c')."<br>".implode(', ',$data['wetDays']).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("days without precipitation","c").'
				</th>
				<td class="cellSecond">
					'. ($data['daysNumber']-count($data['wetDays'])).'
				</td>
				<td class="cellSecond" style="text-align:center">
					
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("highest daily precipitation","c").'
				</th>
				<td class="cellSecond">
					'. number_format(convertR($data['highestRDayValue']),$decimalR)." ".$displayRainUnits.'
				</td>
				<td class="value cellSecond">
					'. date($dateFormat,$data['highestRDay']).'
				</td>
			</tr>
		</table>
	');

	$mpdf->Output('monthlyReport_'.$chosenYear."-".$chosenMonth.'.pdf', 'I');
    exit;
	?>
