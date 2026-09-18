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
	#	Annual report graphs
	#
	# 	A script that generates data for annual report graphs.
	#
	############################################################################
	#	
	#
	# 	v10.0 Banana 2016-10-28
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");

	$bearingNames = array(lang("directionN",""),lang("directionNNE",""),lang("directionENE",""),lang("directionE",""),lang("directionESE",""),lang("directionSE",""),lang("directionSSE",""),lang("directionS",""),lang("directionSSW",""),lang("directionSW",""),lang("directionSW",""),lang("directionWSW",""),lang("directionW",""),lang("directionWNW",""),lang("directionNW",""),lang("directionNNW",""));
	
	// Get date
	$chosenYear = $_GET['y'];
	
	// validate year
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	
	$q = $_GET['q'];
	$interval = $_GET['interval'];
	$span = "Year(DateTime) = ".$chosenYear;
	
	if($interval=="m"){
		$monthlyAvgT = array();
		$monthlyAvgH = array();
		$monthlyAvgP = array();
		$monthlyAvgW = array();
		$monthlyAvgG = array();
		$monthlyAvgS = array();
		$monthlyAvgA = array();
		$monthlyAvgD = array();
		
		$monthlyMaxT = array();
		$monthlyMaxH = array();
		$monthlyMaxP = array();
		$monthlyMaxW = array();
		$monthlyMaxG = array();
		$monthlyMaxS = array();
		$monthlyMaxA = array();
		$monthlyMaxD = array();
		
		$monthlyMinT = array();
		$monthlyMinH = array();
		$monthlyMinP = array();
		$monthlyMinW = array();
		$monthlyMinG = array();
		$monthlyMinS = array();
		$monthlyMinA = array();
		$monthlyMinD = array();

		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(Tmax), max(H), max(P), max(W), max(G), max(S), max(A), max(D), min(Tmin), min(H), min(P), min(W), min(G), min(S), min(A), min(D)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($monthlyAvgT,convertT($row['avg(T)']));
			array_push($monthlyAvgH,$row['avg(H)']);
			array_push($monthlyAvgP,convertP($row['avg(P)']));
			array_push($monthlyAvgW,convertW($row['avg(W)']));
			array_push($monthlyAvgG,convertW($row['avg(G)']));
			array_push($monthlyAvgS,$row['avg(S)']);
			array_push($monthlyAvgD,convertT($row['avg(D)']));
			array_push($monthlyAvgA,convertT($row['avg(A)']));
			
			array_push($monthlyMaxT,convertT($row['max(Tmax)']));
			array_push($monthlyMaxH,$row['max(H)']);
			array_push($monthlyMaxP,convertP($row['max(P)']));
			array_push($monthlyMaxW,convertW($row['max(W)']));
			array_push($monthlyMaxG,convertW($row['max(G)']));
			array_push($monthlyMaxS,$row['max(S)']);
			array_push($monthlyMaxD,convertT($row['max(D)']));
			array_push($monthlyMaxA,convertT($row['max(A)']));
			
			array_push($monthlyMinT,convertT($row['min(Tmin)']));
			array_push($monthlyMinH,$row['min(H)']);
			array_push($monthlyMinP,convertP($row['min(P)']));
			array_push($monthlyMinW,convertW($row['min(W)']));
			array_push($monthlyMinG,convertW($row['min(G)']));
			array_push($monthlyMinS,$row['min(S)']);
			array_push($monthlyMinD,convertT($row['min(D)']));
			array_push($monthlyMinA,convertT($row['min(A)']));		
		}
		
		$monthlyR = array();
		
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			if(array_key_exists($row['MONTH(DateTime)'],$monthlyR)){
				$monthlyR[$row['MONTH(DateTime)']] = $monthlyR[$row['MONTH(DateTime)']] + convertR($row['max(R)']);
			}
			else{
				$monthlyR[$row['MONTH(DateTime)']] =  convertR($row['max(R)']);
			}
		}
		
		$cumRain = 0;
		
		$monthlyCumulativeR = array();
		for($i=1;$i<=count($monthlyR);$i++){
			$cumRain = $cumRain + $monthlyR[$i];
			array_push($monthlyCumulativeR,$cumRain);
		}
		
		if($q == "T"){
			$values = array();
			$values2 = array();
			$values3 = array();
			for($i=0;$i<count($monthlyAvgT);$i++){
				array_push($values,round($monthlyAvgT[$i],2));
				array_push($values2,round($monthlyAvgA[$i],2));
				$temporary = array(round($monthlyMinT[$i],2),round($monthlyMaxT[$i],2));
				array_push($values3,$temporary);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
			$final['data3'] = $values3;
		}
		if($q == "H"){
			$values = array();
			$values2 = array();
			for($i=0;$i<count($monthlyAvgH);$i++){
				array_push($values,round($monthlyAvgH[$i],2));
				$temporary = array(round($monthlyMinH[$i],2),round($monthlyMaxH[$i],2));
				array_push($values2,$temporary);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
		}
		if($q == "P"){
			$values = array();
			$values2 = array();
			for($i=0;$i<count($monthlyAvgP);$i++){
				array_push($values,round($monthlyAvgP[$i],2));
				$temporary = array(round($monthlyMinP[$i],2),round($monthlyMaxP[$i],2));
				array_push($values2,$temporary);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
		}
		if($q == "W"){
			$values = array();
			$values2 = array();
			$values3 = array();
			for($i=0;$i<count($monthlyAvgW);$i++){
				array_push($values,round($monthlyAvgW[$i],2));
				array_push($values2,round($monthlyAvgG[$i],2));
				array_push($values3,round($monthlyMaxG[$i],2));
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
			$final['data3'] = $values3;
		}
		if($q == "D"){
			$values = array();
			$values2 = array();
			for($i=0;$i<count($monthlyAvgD);$i++){
				array_push($values,round($monthlyAvgD[$i],2));
				$temporary = array(round($monthlyMinD[$i],2),round($monthlyMaxD[$i],2));
				array_push($values2,$temporary);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
		}
		if($q == "R"){
			$values = array();
			$values2 = array();
			for($i=1;$i<=count($monthlyR);$i++){
				array_push($values,$monthlyR[$i]);
				array_push($values2,$monthlyCumulativeR[($i-1)]);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
		}
		if($q == "S"){
			$values = array();
			$values2 = array();
			for($i=0;$i<count($monthlyAvgS);$i++){
				array_push($values,round($monthlyAvgS[$i],2));
				array_push($values2,$monthlyMaxS[$i]);
			}
			$final['data1'] = $values;
			$final['data2'] = $values2;
		}
	}
	if($interval=="d"){
		$dailyAvgT = array();
		$dailyAvgH = array();
		$dailyAvgP = array();
		$dailyAvgW = array();
		$dailyAvgG = array();
		$dailyAvgS = array();
		$dailyAvgA = array();
		$dailyAvgD = array();
		
		$dailyMaxT = array();
		$dailyMaxH = array();
		$dailyMaxP = array();
		$dailyMaxW = array();
		$dailyMaxG = array();
		$dailyMaxS = array();
		$dailyMaxA = array();
		$dailyMaxD = array();
		
		$dailyMinT = array();
		$dailyMinH = array();
		$dailyMinP = array();
		$dailyMinW = array();
		$dailyMinG = array();
		$dailyMinS = array();
		$dailyMinA = array();
		$dailyMinD = array();
		
		$dailyR = array();
		
		$dailyDates = array();

		
		$result = mysqli_query($con,"
			SELECT DateTime, avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(Tmax), max(H), max(P), max(W), max(G), max(S), max(A), max(D), min(Tmin), min(H), min(P), min(W), min(G), min(S), min(A), min(D), max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($dailyAvgT,convertT($row['avg(T)']));
			array_push($dailyAvgH,$row['avg(H)']);
			array_push($dailyAvgP,convertP($row['avg(P)']));
			array_push($dailyAvgW,convertW($row['avg(W)']));
			array_push($dailyAvgG,convertW($row['avg(G)']));
			array_push($dailyAvgS,$row['avg(S)']);
			array_push($dailyAvgD,convertT($row['avg(D)']));
			array_push($dailyAvgA,convertT($row['avg(A)']));
			
			array_push($dailyMaxT,convertT($row['max(Tmax)']));
			array_push($dailyMaxH,$row['max(H)']);
			array_push($dailyMaxP,convertP($row['max(P)']));
			array_push($dailyMaxW,convertW($row['max(W)']));
			array_push($dailyMaxG,convertW($row['max(G)']));
			array_push($dailyMaxS,$row['max(S)']);
			array_push($dailyMaxD,convertT($row['max(D)']));
			array_push($dailyMaxA,convertT($row['max(A)']));
			
			array_push($dailyMinT,convertT($row['min(Tmin)']));
			array_push($dailyMinH,$row['min(H)']);
			array_push($dailyMinP,convertP($row['min(P)']));
			array_push($dailyMinW,convertW($row['min(W)']));
			array_push($dailyMinG,convertW($row['min(G)']));
			array_push($dailyMinS,$row['min(S)']);
			array_push($dailyMinD,convertT($row['min(D)']));
			array_push($dailyMinA,convertT($row['min(A)']));		
			
			array_push($dailyDates,strtotime($row['DateTime']));
			
			array_push($dailyR,convertR($row['max(R)']));
		}
		$dailyCumulativeR = array();
		for($i=0;$i<count($dailyR);$i++){
			$cumRain = $cumRain + $dailyR[$i];
			array_push($dailyCumulativeR,$cumRain);
		}
		
		if($q == "T"){
			for($i=0;$i<count($dailyAvgT);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				$result3 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgT[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,round($dailyAvgA[$i],2));
				$final['data2'][] = $result2;
				array_push($result3,($date1*1000));
				array_push($result3,$dailyMinT[$i]);
				array_push($result3,$dailyMaxT[$i]);
				$final['data3'][] = $result3;
			}
		}
		if($q == "H"){
			for($i=0;$i<count($dailyAvgH);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgH[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,$dailyMinH[$i]);
				array_push($result2,$dailyMaxH[$i]);
				$final['data2'][] = $result2;
			}
		}
		if($q == "P"){
			for($i=0;$i<count($dailyAvgP);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgP[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,$dailyMinP[$i]);
				array_push($result2,$dailyMaxP[$i]);
				$final['data2'][] = $result2;
			}
		}
		if($q == "W"){
			for($i=0;$i<count($dailyAvgW);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				$result3 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgW[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,round($dailyAvgG[$i],2));
				$final['data2'][] = $result2;
				array_push($result3,($date1*1000));
				array_push($result3,0);
				array_push($result3,$dailyMaxG[$i]);
				$final['data3'][] = $result3;
			}
		}
		if($q == "D"){
			for($i=0;$i<count($dailyAvgD);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgD[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,$dailyMinD[$i]);
				array_push($result2,$dailyMaxD[$i]);
				$final['data2'][] = $result2;
			}
		}
		if($q == "R"){
			for($i=0;$i<count($dailyR);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				array_push($result1,($date1*1000));
				array_push($result1,$dailyR[$i]);
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,$dailyCumulativeR[$i]);
				$final['data2'][] = $result2;
			}
		}
		if($q == "S"){
			for($i=0;$i<count($dailyAvgS);$i++){
				$date1 = date('U',$dailyDates[$i]);
				$result1 = array();
				$result2 = array();
				array_push($result1,($date1*1000));
				array_push($result1,round($dailyAvgS[$i],2));
				$final['data1'][] = $result1;
				array_push($result2,($date1*1000));
				array_push($result2,$dailyMaxS[$i]);
				$final['data2'][] = $result2;
			}
		}
	}
	print json_encode($final, JSON_NUMERIC_CHECK);
?>