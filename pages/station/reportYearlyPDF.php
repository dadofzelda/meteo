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
	
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y'])){
		echo "Invalid date";
		die();
	}
	
	$chosenYear = $_GET['y'];

	// check id data cache is available
	if(file_exists("cache/annual".$chosenYear.".txt")){
		$data = json_decode(file_get_contents("cache/annual".$chosenYear.".txt"),true);
	}
	else{
	
		$span = "Year(DateTime) = ".$chosenYear;
		
		// get available months
		$result = mysqli_query($con,"
			SELECT DISTINCT month(DateTime)
			FROM alldata 
			WHERE $span
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['availableMonths'][] = $row['month(DateTime)'];
		}
		/* #############################################################################*/
		// Calculate annual average, max, min, sd, range
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), max(T), max(Tmax), max(H), max(P), max(D), max(W), max(G), max(A), max(S), min(Tmin), min(H), min(P), min(D), min(W), min(G), min(A), min(S), stddev(T), stddev(H), stddev(P), stddev(D), stddev(W), stddev(G), stddev(A), stddev(S), max(RR)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			// annual averages
			$data['annualAvgT'] = ($row['avg(T)']);
			$data['annualAvgH'] = $row['avg(H)'];
			$data['annualAvgP'] = ($row['avg(P)']);
			$data['annualAvgD'] = ($row['avg(D)']);
			$data['annualAvgW'] = ($row['avg(W)']);
			$data['annualAvgG'] = ($row['avg(G)']);
			$data['annualAvgA'] = ($row['avg(A)']);
			$data['annualAvgS'] = $row['avg(S)'];
			// annual maxima
			$data['annualMaxTmax'] = ($row['max(Tmax)']);
			$data['annualMaxH'] = $row['max(H)'];
			$data['annualMaxP'] = ($row['max(P)']);
			$data['annualMaxD'] = ($row['max(D)']);
			$data['annualMaxW'] = ($row['max(W)']);
			$data['annualMaxG'] = ($row['max(G)']);
			$data['annualMaxA'] = ($row['max(A)']);
			$data['annualMaxS'] = $row['max(S)'];
			$data['annualMaxRR'] = ($row['max(RR)']);
			// annual minima
			$data['annualMinTmin'] = ($row['min(Tmin)']);
			$data['annualMinH'] = $row['min(H)'];
			$data['annualMinP'] = ($row['min(P)']);
			$data['annualMinD'] = ($row['min(D)']);
			$data['annualMinW'] = ($row['min(W)']);
			$data['annualMinG'] = ($row['min(G)']);
			$data['annualMinA'] = ($row['min(A)']);
			$data['annualMinS'] = $row['min(S)'];
			// annual ranges
			$data['annualRangeT'] = $data['annualMaxTmax'] - $data['annualMinTmin'];
			$data['annualRangeH'] = $data['annualMaxH'] - $data['annualMinH'];
			$data['annualRangeP'] = $data['annualMaxP'] - $data['annualMinP'];
			$data['annualRangeD'] = $data['annualMaxD'] - $data['annualMinD'];
			$data['annualRangeW'] = $data['annualMaxW'] - $data['annualMinW'];
			$data['annualRangeG'] = $data['annualMaxG'] - $data['annualMinG'];
			$data['annualRangeA'] = $data['annualMaxA'] - $data['annualMinA'];
			$data['annualRangeS'] = $data['annualMaxS'] - $data['annualMinS'];
			// annual standard deviations
			$data['annualStddevT'] = ($row['stddev(T)']);
			$data['annualStddevH'] = $row['stddev(H)'];
			$data['annualStddevP'] = ($row['stddev(P)']);
			$data['annualStddevD'] = ($row['stddev(D)']);
			$data['annualStddevW'] = ($row['stddev(W)']);
			$data['annualStddevG'] = ($row['stddev(G)']);
			$data['annualStddevA'] = ($row['stddev(A)']);
			$data['annualStddevS'] = $row['stddev(S)'];
			
		}
		
		// Calculate dates and times when max and min measured
		$data['annualMaxTmaxDate'] = array();
		$data['annualMinTminDate'] = array();
		$data['annualMaxHDate'] = array();
		$data['annualMinHDate'] = array();
		$data['annualMaxPDate'] = array();
		$data['annualMinPDate'] = array();
		$data['annualMaxWDate'] = array();
		$data['annualMinWDate'] = array();
		$data['annualMaxGDate'] = array();
		$data['annualMinGDate'] = array();
		$data['annualMaxADate'] = array();
		$data['annualMinADate'] = array();
		$data['annualMaxDDate'] = array();
		$data['annualMinDDate'] = array();
		$data['annualMaxSDate'] = array();
		$data['annualMinSDate'] = array();
		$data['annualMaxRRDate'] = array();
		
		// new method, faster

		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND Tmax=".$data['annualMaxTmax']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxTmaxDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND Tmin=".$data['annualMinTmin']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinTminDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND A=".$data['annualMaxA']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxADate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND A=".$data['annualMinA']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinADate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND D=".$data['annualMaxD']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxDDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND D=".$data['annualMinD']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinDDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND H=".$data['annualMaxH']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxHDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND H=".$data['annualMinH']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinHDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND P=".$data['annualMaxP']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxPDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND P=".$data['annualMinP']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinPDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND W=".$data['annualMaxW']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxWDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND W=".$data['annualMinW']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinWDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND G=".$data['annualMaxG']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxGDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND G=".$data['annualMinG']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMinGDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND RR=".$data['annualMaxRR']." LIMIT 11");
		while($row = mysqli_fetch_array($result)){
			$date_temporary = strtotime($row['DateTime']);
			array_push($data['annualMaxRRDate'],date($timeFormat." ".$dateFormat, $date_temporary));
		}
		if($solarSensor){
			$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND S=".$data['annualMaxS']." LIMIT 11");
			while($row = mysqli_fetch_array($result)){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['annualMaxSDate'],date($timeFormat." ".$dateFormat, $date_temporary));
			}
			$result = mysqli_query($con,"SELECT DateTime FROM alldata WHERE $span AND S=".$data['annualMinS']." LIMIT 11");
			while($row = mysqli_fetch_array($result)){
				$date_temporary = strtotime($row['DateTime']);
				array_push($data['annualMinSDate'],date($timeFormat." ".$dateFormat, $date_temporary));
			}
		}
		if(count($data['annualMaxHDate'])>10){
			$data['annualMaxHDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinHDate'])>10){
			$data['annualMinHDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxTmaxDate'])>10){
			$data['annualMaxTmaxDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinTminDate'])>10){
			$data['annualMinTminDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxADate'])>10){
			$data['annualMaxADate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinADate'])>10){
			$data['annualMinADate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxDDate'])>10){
			$data['annualMaxDDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinDDate'])>10){
			$data['annualMinDDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxPDate'])>10){
			$data['annualMaxPDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinPDate'])>10){
			$data['annualMinPDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxSDate'])>10){
			$data['annualMaxSDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinSDate'])>10){
			$data['annualMinSDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxWDate'])>10){
			$data['annualMaxWDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinWDate'])>10){
			$data['annualMinWDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxGDate'])>10){
			$data['annualMaxGDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMinGDate'])>10){
			$data['annualMinGDate'] = array(lang('more than 10<br>instances','l'));
		}
		if(count($data['annualMaxRRDate'])>10){
			$data['annualMaxRRDate'] = array(lang('more than 10<br>instances','l'));
		}
		
		$data['annualRTotal'] = 0;
		$data['daysNumber'] = 0;
		// Calculate annual precipitation
		$result = mysqli_query($con,"
			SELECT max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['annualRTotal'] += ($row['max(R)']);
			$data['daysNumber']++;
		}
		
		// Calculate annual windrun

		if($dataWindUnits=="kmh"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'];
			$data['annualWindRunUnits'] = "km";
		}
		if($dataWindUnits=="ms"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'] * 3.6;
			$data['annualWindRunUnits'] = "km";
		}
		if($dataWindUnits=="mph"){
			$data['annualWindRun'] = $data['annualAvgW'] * 24 * $data['daysNumber'];
			$data['annualWindRunUnits'] = "mi";
		}
		
		// Calculate average wind direction
		$data['annualBValues'] = array();
		$result = mysqli_query($con,"
			SELECT B
			FROM alldata 
			WHERE $span
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['annualBValues'],$row['B']);
		}
		$data['annualAvgB'] = avgWind($data['annualBValues']);
		$data['annualBValues'] = ""; // delete it so that it is not all cached
		
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/annual".$chosenYear.".txt",json_encode($data));
		}
	}
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S)
		FROM alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		$stationAverageT = convertT($row['avg(T)']);
		$stationAverageA = convertT($row['avg(A)']);
		$stationAverageD = convertT($row['avg(D)']);
		$stationAverageH = ($row['avg(H)']);
		$stationAverageP = convertP($row['avg(P)']);
		$stationAverageW = convertW($row['avg(W)']);
		$stationAverageG = convertW($row['avg(G)']);
		$stationAverageS = ($row['avg(S)']);
	}
	
	function deviationFormat($n){
		if($n>0){
			return "+".$n;
		}
		else{
			return $n;
		}
	}

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
    $mpdf->SetTitle(lang("annual report",'w'));
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
                    <h1 style="font-size:1.5em;color:white">'.lang("annual report",'w').' - '.lang('summary','c').'</h1>
					<h2 style="font-size:1.2em;color:white">'.$chosenYear.'</h2>
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
						'.lang("sdAbbr",'u').'
					</th>
                    <th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang("station average",'c').'
					</th>
                    <th style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
						'.lang("deviation",'c').'
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("temperature",'c')." (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualAvgT']),2).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['monthlyMaxTmax']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxTmaxDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualMinTmin']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinTminDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['annualRangeT']),1).'
					</td>
					<td>
						'.number_format(convertTStddev($data['annualStddevT']),2).'
					</td>
                    <td>
						'.number_format($stationAverageT,2).'
					</td>
                    <td>
						'.deviationFormat(number_format(convertT($data['annualAvgT']) - $stationAverageT,2,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("apparent temperature",'c'). " (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualAvgA']),2).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualMaxA']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxADate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualMinA']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinADate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['annualRangeA']),1).'
					</td>
					<td>
						'.number_format(convertTStddev($data['annualStddevA']),2).'
					</td>
                    <td>
						'.number_format($stationAverageA,2,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(convertT($data['annualAvgA']) - $stationAverageA,2,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("dew point",'c'). " (".unitFormatter($displayTempUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualAvgD']),2).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualMaxD']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxDDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertT($data['annualMinD']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinDDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertTStddev($data['annualRangeD']),1).'
					</td>
					<td>
						'.number_format(convertTStddev($data['annualStddevD']),2).'
					</td>
                    <td>
						'.number_format($stationAverageD,2,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(convertT($data['annualAvgD']) - $stationAverageD,2,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("humidity",'c'). " (%)".' 
					</td>
					<td>
						<strong>'.number_format($data['annualAvgH'],2).'</strong>
					</td>
					<td>
						<strong>'.number_format($data['annualMaxH'],1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxHDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format($data['annualMinH'],1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinHDate']) .'
						</div>
					</td>
					<td>
						'.number_format($data['annualRangeH'],1).'
					</td>
					<td>
						'.number_format($data['annualStddevH'],2).'
					</td>
                    <td>
						'.number_format($stationAverageH,1,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(($data['annualAvgH']) - $stationAverageH,1,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("pressure",'c'). " (".unitFormatter($displayPressUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertP($data['annualAvgP']),$decimalsP,".","").'</strong>
					</td>
					<td>
						<strong>'.number_format(convertP($data['annualMaxP']),$decimalsP,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxPDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertP($data['annualMinP']),$decimalsP,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinPDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertP($data['annualRangeP']),$decimalsP,".","").'
					</td>
					<td>
						'.number_format(convertP($data['annualRangeP']),$decimalsP+1,".","").'
					</td>
                    <td>
						'.number_format($stationAverageP,$decimalsP,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(convertP($data['annualAvgP']) - $stationAverageP,$decimalsP,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("wind speed",'c'). " (".unitFormatter($displayWindUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualAvgW']),2).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualMaxW']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxWDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualMinW']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinWDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertW($data['annualRangeW']),1).'
					</td>
					<td>
						'.number_format(convertW($data['annualStddevW']),2).'
					</td>
                    <td>
						'.number_format($stationAverageW,2,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(convertW($data['annualAvgW']) - $stationAverageW,2,".","")).'
					</td>
				</tr>
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("wind gust",'c'). " (".unitFormatter($displayWindUnits).")".' 
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualAvgG']),2).'</strong>
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualMaxG']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxGDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format(convertW($data['annualMinG']),1).'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinGDate']) .'
						</div>
					</td>
					<td>
						'.number_format(convertW($data['annualRangeG']),1).'
					</td>
					<td>
						'.number_format(convertW($data['annualStddevG']),2).'
					</td>
				</tr>');

	if($solarSensor){
		$mpdf->WriteHTML('
				<tr>
					<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px"> 
						'.lang("solar radiation",'c'). " (W/m<sup>2</sup>)".'
					</td>
					<td>
						<strong>'.number_format($data['annualAvgS'],1,".","").'</strong>
					</td>
					<td>
						<strong>'.number_format($data['annualMaxS'],0,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMaxSDate']) .'
						</div>
					</td>
					<td>
						<strong>'.number_format($data['annualMinS'],0,".","").'</strong>
						<div style="font-size:10pt">
							'.checkSize($data['annualMinSDate']) .'
						</div>
					</td>
					<td>
						'.number_format($data['annualRangeS'],0,".","").'
					</td>
					<td>
						'.number_format($data['annualStddevS'],1,".","").'
					</td>
                    <td>
						'.number_format($stationAverageS,1,".","").'
					</td>
					<td>
						'.deviationFormat(number_format(($data['annualAvgS']) - $stationAverageS,0,".","")).'
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
					<strong>'.number_format(convertR($data['annualMaxRR']),$decimalsR).'</strong>
					<div style="font-size:10pt">
						'.checkSize($data['annualMaxRRDate']) .'
					</div>
				</td>
				<td colspan="5">
				</td>
			</tr>
			<tr>
				<td style="background:#'.$color_schemes[$design2]['700'].'">
				</td>
				<td colspan="7" style="text-align:center;background:#'.$color_schemes[$design2]['700'].';color:white">
					'.lang('total','c').'
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px">
					'.lang("precipitation",'c'). " (".unitFormatter($displayRainUnits).")".' 
				</td>
				<td colspan="7">
					<strong>'.number_format(convertR($data['annualRTotal']),$decimalsR).'</strong>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold;vertical-align:middle;padding-left:10px">
					'.lang("wind run",'c'). " (".$data['annualWindRunUnits'].")".' 
				</td>
				<td colspan="7">
					<strong>'.number_format($data['annualWindRun'],1,".","") .'</strong>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="width:100%;text-align:right;font-size:10px">
		* '.lang('more than one').'
	</div>
	');
	
	$mpdf->WriteHTML('<pagebreak />');

    if(file_exists("cache/".$chosenYear."_1.txt")){
		$data = json_decode(file_get_contents("cache/".$chosenYear."_1.txt"),true);
	}
	else{
		$span = "Year(DateTime) = ".$chosenYear;
		
		$data['monthlyAveragesT'] = array();
		$data['monthlyAveragesH'] = array();
		$data['monthlyAveragesP'] = array();
		$data['monthlyAveragesW'] = array();
		$data['monthlyAveragesG'] = array();
		$data['monthlyAveragesS'] = array();
		$data['monthlyAveragesA'] = array();
		$data['monthlyAveragesD'] = array();
		$data['monthlyAveragesDates'] = array();
		
		$result = mysqli_query($con,"
			SELECT DateTime, avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['monthlyAveragesT'],($row['avg(T)']));
			array_push($data['monthlyAveragesH'],$row['avg(H)']);
			array_push($data['monthlyAveragesP'],($row['avg(P)']));
			array_push($data['monthlyAveragesW'],($row['avg(W)']));
			array_push($data['monthlyAveragesG'],($row['avg(G)']));
			array_push($data['monthlyAveragesS'],$row['avg(S)']);
			array_push($data['monthlyAveragesD'],($row['avg(D)']));
			array_push($data['monthlyAveragesA'],($row['avg(A)']));
			array_push($data['monthlyAveragesDates'],strtotime($row['DateTime']));
		}
		
		$data['dailyAveragesT'] = array();
		$data['dailyAveragesH'] = array();
		$data['dailyAveragesP'] = array();
		$data['dailyAveragesW'] = array();
		$data['dailyAveragesG'] = array();
		$data['dailyAveragesS'] = array();
		$data['dailyAveragesA'] = array();
		$data['dailyAveragesD'] = array();
		$data['dailyR'] = array();
		$data['monthlyR'] = array();
		$data['dailyAveragesDates'] = array();
		
		$result = mysqli_query($con,"
			SELECT DateTime, avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(R), MONTH(DateTime)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['dailyAveragesT'],($row['avg(T)']));
			array_push($data['dailyAveragesH'],$row['avg(H)']);
			array_push($data['dailyAveragesP'],($row['avg(P)']));
			array_push($data['dailyAveragesW'],($row['avg(W)']));
			array_push($data['dailyAveragesG'],($row['avg(G)']));
			array_push($data['dailyAveragesS'],$row['avg(S)']);
			array_push($data['dailyAveragesD'],($row['avg(D)']));
			array_push($data['dailyAveragesA'],($row['avg(A)']));
			array_push($data['dailyR'],($row['max(R)']));
			$data['monthlyR'][$row['MONTH(DateTime)']] = $data['monthlyR'][$row['MONTH(DateTime)']] + ($row['max(R)']);
			array_push($data['dailyAveragesDates'],strtotime($row['DateTime']));
		}
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/".$chosenYear."_1.txt",json_encode($data));
		}
	}

    $mpdf->WriteHTML('
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/temp.png" class="headingImg" ><br>'.lang('temperature','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("warmest month","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesT']), $data['monthlyAveragesT'])])),"c").'
				</td>
				<td class="value cellSecond">
					'.number_format(convertT(max($data['monthlyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("coldest month","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesT']), $data['monthlyAveragesT'])])),"c").'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['monthlyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("warmest day","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesT']), $data['dailyAveragesT'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(max($data['dailyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("coldest day","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesT']), $data['dailyAveragesT'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['dailyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits).'
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
					'. lang("month with highest average apparent temperature","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesA']), $data['monthlyAveragesA'])])),"c").'
				</td>
				<td class="value cellSecond">
					'.number_format(convertT(max($data['monthlyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average apparent temperature","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesA']), $data['monthlyAveragesA'])])),"c").'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['monthlyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average apparent temperature","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesA']), $data['dailyAveragesA'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(max($data['dailyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average apparent temperature","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesA']), $data['dailyAveragesA'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['dailyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits).'
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
					'. lang("month with highest average dew point","").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesD']), $data['monthlyAveragesD'])])),"c").'
				</td>
				<td class="value cellSecond">
					'.number_format(convertT(max($data['monthlyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average dew point","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesD']), $data['monthlyAveragesD'])])),"c").'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['monthlyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average dew point","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesD']), $data['dailyAveragesD'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(max($data['dailyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average dew point","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesD']), $data['dailyAveragesD'])]).'
				</td>
				<td class="value">
					'.number_format(convertT(min($data['dailyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits).'
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
					'. lang("month with highest average humidity","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesH']), $data['monthlyAveragesH'])])),'c').'
				</td>
				<td class="value cellSecond">
					'.number_format(max($data['monthlyAveragesH']),2,".","")." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average humidity","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesH']), $monthlyAveragesH)])),"c").'
				</td>
				<td class="value">
					'.number_format(min($data['monthlyAveragesH']),2,".","")." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average humidity","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesH']), $data['dailyAveragesH'])]).'
				</td>
				<td class="value">
					'.number_format(max($data['dailyAveragesH']),2,".","")." %".'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average humidity","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesH']), $data['dailyAveragesH'])]).'
				</td>
				<td class="value">
					'.number_format(min($data['dailyAveragesH']),2,".","")." %".'
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
					'. lang("month with highest average pressure","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesP']), $data['monthlyAveragesP'])])),'c').'
				</td>
				<td class="value cellSecond">
					'.number_format(convertP(max($data['monthlyAveragesP'])),$decimalsP+1,".","")." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average pressure","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesP']), $data['monthlyAveragesP'])])),"c").'
				</td>
				<td class="value">
					'.number_format(convertP(min($data['monthlyAveragesP'])),$decimalsP+1,".","")." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average pressure","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesP']), $data['dailyAveragesP'])]).'
				</td>
				<td class="value">
					'.number_format(convertP(max($data['dailyAveragesP'])),$decimalsP+1,".","")." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average pressure","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesP']), $data['dailyAveragesP'])]).'
				</td>
				<td class="value">
					'.number_format(convertP(min($data['dailyAveragesP'])),$decimalsP,".","")." ".unitFormatter($displayPressUnits).'
				</td>
			</tr>
		</table>
		<pagebreak>
		<table class="table" cellspacing="0" cellpadding="2">
			<tr>
				<th colspan="3" class="cellHeading">
					<img src="../../icons/wind.png" class="headingImg" ><br>'. lang('wind speed','c').'
				</th>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with highest average wind speed","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesW']), $data['monthlyAveragesW'])])),'c').'
				</td>
				<td class="value cellSecond">
					'.number_format(convertW(max($data['monthlyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average wind speed","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesW']), $data['monthlyAveragesW'])])),"c").'
				</td>
				<td class="value">
					'.number_format(convertW(min($data['monthlyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average wind speed","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesW']), $data['dailyAveragesW'])]).'
				</td>
				<td class="value">
					'.number_format(convertW(max($data['dailyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average wind speed","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesW']), $data['dailyAveragesW'])]).'
				</td>
				<td class="value">
					'.number_format(convertW(min($data['dailyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits).'
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
					'. lang("month with highest average gust","c").'
				</th>
				<td class="cellSecond">
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesG']), $data['monthlyAveragesG'])])),'c').'
				</td>
				<td class="value cellSecond">
					'.number_format(convertW(max($data['monthlyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest average gust","c").'
				</th>
				<td>
					'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesG']), $monthlyAveragesG)])),"c").'
				</td>
				<td class="value">
					'.number_format(convertW(min($data['monthlyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest average gust","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesG']), $data['dailyAveragesG'])]).'
				</td>
				<td class="value">
					'.number_format(convertW(max($data['dailyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with lowest average gust","c").'
				</th>
				<td>
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesG']), $data['dailyAveragesG'])]).'
				</td>
				<td class="value">
					'.number_format(convertW(min($data['dailyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits).'
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
						'. lang("month with highest average solar radiation","").'
					</th>
					<td class="cellSecond">
						'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesS']), $data['monthlyAveragesS'])])),"c").'
					</td>
					<td class="value cellSecond">
						'.number_format(max($data['monthlyAveragesS']),2,".","")." W/m2".'
					</td>
				</tr>
				<tr>
					<th class="cellFirst">
						'. lang("month with lowest average solar radiation","c").'
					</th>
					<td>
						'.lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesS']), $data['monthlyAveragesS'])])),"c").'
					</td>
					<td class="value">
						'.number_format(min($data['monthlyAveragesS']),2,".","")." W/m2".'
					</td>
				</tr>
				<tr>
					<th class="cellFirst">
						'. lang("day with highest average solar radiation","c").'
					</th>
					<td>
						'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesS']), $data['dailyAveragesS'])]).'
					</td>
					<td class="value">
						'.number_format(max($data['dailyAveragesS']),2,".","")." W/m2".'
					</td>
				</tr>
                <tr>
					<th class="cellFirst">
						'. lang("day with lowest average solar radiation","c").'
					</th>
					<td>
						'.date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesS']), $data['dailyAveragesS'])]).'
					</td>
					<td class="value">
						'.number_format(min($data['dailyAveragesS']),2,".","")." W/m2".'
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
					'. lang("month with highest precipitation","c").'
				</th>
				<td class="cellSecond">
					'.lang("month".array_search(max($data['monthlyR']), $data['monthlyR']),"c").'
				</td>
				<td class="cellSecond">
					'.number_format(convertR(max($data['monthlyR'])),$decimalsR,".","")." ".unitFormatter($displayRainUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("month with lowest precipitation","c").'
				</th>
				<td class="cellSecond">
					'.lang("month".array_search(min($data['monthlyR']), $data['monthlyR']),"c").'
				</td>
				<td class="cellSecond">
					'.number_format(convertR(min($data['monthlyR'])),$decimalsR,".","")." ".unitFormatter($displayRainUnits).'
				</td>
			</tr>
			<tr>
				<th class="cellFirst">
					'. lang("day with highest precipitation","c").'
				</th>
				<td class="cellSecond">
					'.date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyR']), $data['dailyR'])]).'
				</td>
				<td class="value cellSecond">
					'.number_format(convertR(max($data['dailyR'])),$decimalsR,".","")." ".unitFormatter($displayRainUnits).'
				</td>
			</tr>
		</table>
	');

	$mpdf->Output('monthlyReport_'.$chosenYear.'.pdf', 'I');
    exit;
	?>
