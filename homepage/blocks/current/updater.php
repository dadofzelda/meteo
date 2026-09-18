<?php
	include("../../../config.php");
	include("../../../scripts/functions.php");

	$type = $_GET['type'];
	$path = $_GET['path'];
	$maxInterval = $_GET['interval'];
	$path = urldecode($path);

	include("settings.php");

	if(!$hideSeconds){
		if($prefferedTime=="12h"){
			$updateTimeFormat = "g:i:s A";
		}
		else{
			$updateTimeFormat = "G:i:s";
		}
	}
	else{
		if($prefferedTime=="12h"){
			$updateTimeFormat = "g:i A";
		}
		else{
			$updateTimeFormat = "G:i";
		}
	}

	// Main API data
	$apiData = file_get_contents("../../../meteotemplateLive.txt");
	$apiData = json_decode($apiData,true);
	if(isset($apiData['T'])){
		$current['T'] = number_format(convertor($apiData['T'],"C",$displayTempUnits),1,".","");
	}
	else{
		$current['T'] = "--";
	}
	if(isset($apiData['H'])){
		$current['H'] = number_format($apiData['H'],1,".","");
	}
	else{
		$current['H'] = "--";
	}
	if(isset($apiData['P'])){
		if($displayPressUnits=="hpa"){
			$current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),1,".","");
		}
		else{
			$current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),2,".","");
		}
	}
	else{
		$current['P'] = "--";
	}
	if(isset($apiData['W'])){
		$current['W'] = number_format(convertor($apiData['W'],"kmh",$displayWindUnits),1,".","");
	}
	else{
		$current['W'] = "--";
	}
	if(isset($apiData['G'])){
		$current['G'] = number_format(convertor($apiData['G'],"kmh",$displayWindUnits),1,".","");
	}
	else{
		$current['G'] = "--";
	}
	if(isset($apiData['R'])){
		if($displayRainUnits=="mm"){
			$current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),1,".","");
		}
		else{
			$current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),2,".","");
		}
	}
	else{
		$current['R'] = "--";
	}
	if(isset($apiData['RR'])){
		if($displayRainUnits=="mm"){
			$current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),1,".","");
		}
		else{
			$current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),2,".","");
		}
	}
	else{
		$current['RR'] = "--";
	}
	if(isset($apiData['D'])){
		$current['D'] = number_format(convertor($apiData['D'],"C",$displayTempUnits),1,".","");
	}
	else{
		$current['D'] = "--";
	}
	if(isset($apiData['A'])){
		$current['A'] = number_format(convertor($apiData['A'],"C",$displayTempUnits),1,".","");
	}
	else{
		$current['A'] = "--";
	}
	if(isset($apiData['B'])){
		$current['B'] = $apiData['B'];
	}
	else{
		$current['B'] = "--";
	}
	if(isset($apiData['S'])){
		$current['S'] = number_format($apiData['S'],0,".","");
	}
	else{
		$current['S'] = "--";
	}

	// DB data - cache
	createCacheDir();
	# WIND RUN - today
	if(file_exists("cache/windRunToday.txt")){
		if (time()-filemtime("cache/windRunToday.txt") > 60 * 10) {
			unlink("cache/windRunToday.txt");
		}
	}
	// also delete if after midnight
	if(file_exists("cache/windRunTodayDate.txt")){
		$wrDate = file_get_contents("cache/windRunTodayDate.txt");
		if($wrDate!=date("Ymd")){
			unlink("cache/windRunToday.txt");
		}
	}
	if(file_exists("cache/windRunToday.txt")){
		$wrToday = file_get_contents("cache/windRunToday.txt"); // in m
	}
	else{
		$result = mysqli_query($con, "
			SELECT avg(W)
			FROM  alldata
			WHERE DATE(DateTime) = CURDATE()
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$avgW = convertor($row['avg(W)'],$dataWindUnits,"ms");
		}
		// get seconds from midnight
		$todayMidnight = strtotime(date("Y")."-".date("m")."-".date("d")." 00:00");
		$elapsedS = time() - $todayMidnight;
		$wrToday = $elapsedS * $avgW;
		file_put_contents("cache/windRunToday.txt",$wrToday);
		file_put_contents("cache/windRunTodayDate.txt",date("Ymd"));
	}
	if($displayVisibilityUnits=="mi"){
		$wrToday = $wrToday * 0.000621371;
		$current['wrToday'] = number_format($wrToday,2,".","");
	}
	else{
		$wrToday = $wrToday * 0.001;
		$current['wrToday'] = number_format($wrToday,2,".","");
	}
	
	# MAX RAIN RATE
	if(file_exists("cache/maxRRToday.txt")){
		if (time()-filemtime("cache/maxRRToday.txt") > 60 * 10) {
			unlink("cache/maxRRToday.txt");
		}
	}
	// also delete if after midnight
	if(file_exists("cache/maxRRTodayDate.txt")){
		$rrDate = file_get_contents("cache/maxRRTodayDate.txt");
		if($rrDate!=date("Ymd")){
			unlink("cache/maxRRToday.txt");
		}
	}
	if(file_exists("cache/maxRRToday.txt")){
		$maxRR = file_get_contents("cache/maxRRToday.txt"); // in mm
	}
	else{
		$result = mysqli_query($con, "
			SELECT max(RR)
			FROM  alldata
			WHERE DATE(DateTime) = CURDATE()
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$maxRR = convertor($row['max(RR)'],$dataRainUnits,"mm");
		}
		file_put_contents("cache/maxRRToday.txt",$maxRR);
		file_put_contents("cache/maxRRTodayDate.txt",date("Ymd"));
	}
	if($displayRainUnits=="mm"){
		$current['maxRR'] = number_format($maxRR,1,".","");
	}
	else{
		$maxRR = convertor($maxRR,"mm",$displayRainUnits);
		$current['maxRR'] = number_format($maxRR,2,".","");
	}
	// fallback if current rain rate is higher than cached
	if(is_numeric($current['RR'])){
		if($current['RR'] > $current['maxRR']){
			$current['maxRR'] = $current['RR'];
		}
	}

	if($solarSensor){
		# MAX SOLAR
		if(file_exists("cache/maxSToday.txt")){
			if (time()-filemtime("cache/maxSToday.txt") > 60 * 10) {
				unlink("cache/maxSToday.txt");
			}
		}
		// also delete if after midnight
		if(file_exists("cache/maxSTodayDate.txt")){
			$sDate = file_get_contents("cache/maxSTodayDate.txt");
			if($sDate!=date("Ymd")){
				unlink("cache/maxSToday.txt");
			}
		}
		if(file_exists("cache/maxSToday.txt")){
			$maxS = file_get_contents("cache/maxSToday.txt");
		}
		else{
			$result = mysqli_query($con, "
				SELECT max(S)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
			);
			while ($row = mysqli_fetch_array($result)) {
				$maxS = $row['max(S)'];
			}
			file_put_contents("cache/maxSToday.txt",$maxS);
			file_put_contents("cache/maxSTodayDate.txt",date("Ymd"));
		}
		$current['maxS'] = number_format($maxS,0,".","");

		// fallback if current rain rate is higher than cached
		if(is_numeric($current['S'])){
			if($current['S'] > $current['maxS']){
				$current['maxS'] = $current['S'];
			}
		}
	}

	

	// CALCULATED
	# HUMIDEX
	if(is_numeric($current['T']) && is_numeric($current['D'])){
		$current['HX'] = getHX($apiData['T'],$apiData['D']);
	}
	else{
		$current['HX'] = "--";
	}
	# HEAT INDEX
	if(is_numeric($current['T']) && is_numeric($current['H'])){
		$current['HI'] = getHI($apiData['T'],$apiData['H']);
	}
	else{
		$current['HI'] = "--";
	}
	# WIND CHILL
	if(is_numeric($current['T']) && is_numeric($current['W'])){
		$current['WCh'] = getWCh($apiData['T'],$apiData['W']);
	}
	else{
		$current['WCh'] = "--";
	}
	# WET-BULB
	if(is_numeric($current['T']) && is_numeric($current['H'])){
		$current['WB'] = getWB($apiData['T'],$apiData['H']);
	}
	else{
		$current['WB'] = "--";
	}
	# VAPOR PRESSURE
	if(is_numeric($current['T'])){
		$current['VP'] = getVP($apiData['T']);
	}
	else{
		$current['VP'] = "--";
	}
	# SATURATED WAPOR PRESSURE
	if(is_numeric($current['T'])){
		$current['SVP'] = getSVP($apiData['T']);
	}
	else{
		$current['SVP'] = "--";
	}
	# ABSOLUTE HUMIDITY
	if(is_numeric($current['T']) && is_numeric($current['H'])){
		$current['AH'] = getAH($apiData['T'],$apiData['H']);
	}
	else{
		$current['AH'] = "--";
	}
	# STATION PRESSURE
	if(is_numeric($current['T']) && is_numeric($current['H']) && is_numeric($current['P'])){
		$current['Pst'] = getP($apiData['T'], $apiData['H'], $apiData['P']);
	}
	else{
		$current['Pst'] = "--";
	}

	// extra parameters
	if(isset($apiData['TIN'])){
		$current['TIN'] = number_format(convertor($apiData['TIN'],"C",$displayTempUnits),1,".","");
	}
	if(isset($apiData['HIN'])){
		$current['HIN'] = number_format($apiData['HIN'],1,".","");
	}
	if(isset($apiData['UV'])){
		$current['UV'] = number_format($apiData['UV'],1,".","");
	}

	if(file_exists("../../../update/apiSettings.txt")){
		$apiSetup = file_get_contents("../../../update/apiSettings.txt");
		$apiSetup = json_decode($apiSetup,1);
		if($apiSetup['UV'] == 1){
			$result = mysqli_query($con, "
				SELECT max(UV)
				FROM  alldataExtra
				WHERE DATE(DateTime) = CURDATE()
				"
			);
			while ($row = mysqli_fetch_array($result)) {
				$current['maxUV'] = $row['max(UV)'];
				$current['maxUV'] = $current['maxUV'] < $current['UV'] ? $current['UV'] : $current['maxUV'];
			}
		}
	}

	if(!isset($current['maxUV'])){
		$current['maxUV'] = "--";
	}
	

	// date/time
	$current['DateTime'] = $apiData['U'];
	$current['Timestamp'] = date($updateTimeFormat,$apiData['U']);

	// Beaufort
	$temporaryBft = getBft(convertor($current['W'],$displayWindUnits,'ms'));
	$current['WBft'] = $temporaryBft[0];
	$current['WBftBg'] = $temporaryBft[1];
	$current['WBftColor'] = $temporaryBft[2];

	$temporaryBft = getBft(convertor($current['G'],$displayWindUnits,'ms'));
	$current['GBft'] = $temporaryBft[0];
	$current['GBftBg'] = $temporaryBft[1];
	$current['GBftColor'] = $temporaryBft[2];

	// trends
	if($currentShowTrends){
		$result = mysqli_query($con, "
			SELECT  T, H, P, W, G, A, D
			FROM  alldata
			WHERE DateTime >= now() - interval ".$currentTrendInterval." hour
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$trendTs[] = convertT($row['T']);
			$trendHs[] = ($row['H']);
			$trendPs[] = convertP($row['P']);
			$trendWs[] = convertW($row['W']);
			$trendGs[] = convertW($row['G']);
			$trendAs[] = convertT($row['A']);
			$trendDs[] = convertT($row['D']);
		}

		// temperature, humidity, pressure, feels like, dew point - compare latest with hour ago
		if(isset($trendTs)){
			$trendT = $trendTs[(count($trendTs)-1)] - $trendTs[0];
		}
		if(isset($trendHs)){
			$trendH = $trendHs[(count($trendHs)-1)] - $trendHs[0];
		}
		if(isset($trendPs)){
			$trendP = $trendPs[(count($trendPs)-1)] - $trendPs[0];
		}
		if(isset($trendAs)){
			$trendA = $trendAs[(count($trendAs)-1)] - $trendAs[0];
		}
		if(isset($trendDs)){
			$trendD = $trendDs[(count($trendDs)-1)] - $trendDs[0];
		}

		// wind - calculate average from first half hour, compare with avg from latest half hour
		if(isset($trendWs)){
			$trendIntervals = floor(count($trendWs)/2);
			for($i=0;$i<$trendIntervals;$i++){
				$trendW1[] = $trendWs[$i];
				$trendG1[] = $trendGs[$i];
			}
			for($i=$trendIntervals;$i<count($trendWs);$i++){
				$trendW2[] = $trendWs[$i];
				$trendG2[] = $trendGs[$i];
			}

			$trendW = array_sum($trendW2)/count($trendW2) - array_sum($trendW1)/count($trendW1);
			$trendG = array_sum($trendG2)/count($trendG2) - array_sum($trendG1)/count($trendG1);
		}
		if(isset($trendT)){
			$current['trendT'] = getTrend($trendT,"T");
		}
		else{
			$current['trendT'] = "";
		}
		if(isset($trendH)){
			$current['trendH'] = getTrend($trendH,"H");
		}
		else{
			$current['trendH'] = "";
		}
		if(isset($trendP)){
			$current['trendP'] = getTrend($trendP,"P");
		}
		else{
			$current['trendP'] = "";
		}
		if(isset($trendW)){
			$current['trendW'] = getTrend($trendW,"W");
		}
		else{
			$current['trendW'] = "";
		}
		if(isset($trendG)){
			$current['trendG'] = getTrend($trendG,"G");
		}
		else{
			$current['trendG'] = "";
		}
		if(isset($trendA)){
			$current['trendA'] = getTrend($trendA,"A");
		}
		else{
			$current['trendA'] = "";
		}
		if(isset($trendD)){
			$current['trendD'] = getTrend($trendD,"D");
		}
		else{
			$current['trendD'] = "";
		}
	}

	// backgrounds
	# load cached general extremes
	if(file_exists("cache/extremes.txt")){
		if (time()-filemtime("cache/extremes.txt") > 60 * 60 * 24) {
			unlink("cache/extremes.txt");
		}
	}
	if(file_exists("cache/extremes.txt")){
		$extremes = json_decode(file_get_contents("cache/extremes.txt"), true);
	}
	else{
		$result = mysqli_query($con, "
			SELECT max(Tmax), min(Tmin), min(H), max(P), min(P), max(G), max(S)
			FROM  alldata
		");
		while ($row = mysqli_fetch_array($result)) {
			$extremes['maxT'] = convertor($row['max(Tmax)'],$dataTempUnits,"C");
			$extremes['minT'] = convertor($row['min(Tmin)'],$dataTempUnits,"C");
			$extremes['minH'] = $row['min(H)'];
			$extremes['maxP'] = convertor($row['max(P)'],$dataPressUnits,"hpa");
			$extremes['minP'] = convertor($row['min(P)'],$dataPressUnits,"hpa");
			$extremes['maxG'] = convertor($row['max(G)'],$dataWindUnits,"kmh");
			$extremes['maxS'] = $row['max(S)'];
		}
		$result = mysqli_query($con, "
			SELECT max(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyMaxTable
		");
		while ($row = mysqli_fetch_array($result)) {
			$extremes['maxR'] = convertor($row['max(DailyRain)'],$dataRainUnits,"mm");
		}
		file_put_contents("cache/extremes.txt", json_encode($extremes));
	}
	// fallback if currently extreme
	$extremes['maxT'] = $extremes['maxT'] < $apiData['T'] ? $apiData['T'] : $extremes['maxT'];
	$extremes['minT'] = $extremes['minT'] > $apiData['T'] ? $apiData['T'] : $extremes['minT'];
	$extremes['minH'] = $extremes['minH'] > $apiData['H'] ? $apiData['H'] : $extremes['minH'];
	$extremes['maxP'] = $extremes['maxP'] < $apiData['P'] ? $apiData['P'] : $extremes['maxP'];
	$extremes['minP'] = $extremes['minP'] > $apiData['P'] ? $apiData['P'] : $extremes['minP'];
	$extremes['maxG'] = $extremes['maxG'] < $apiData['G'] ? $apiData['G'] : $extremes['maxG'];
	$extremes['maxS'] = $extremes['maxS'] < $apiData['S'] ? $apiData['S'] : $extremes['maxS'];
	$extremes['maxR'] = $extremes['maxR'] < $apiData['R'] ? $apiData['R'] : $extremes['maxR'];
	$extremes['maxUV'] = 14 < $apiData['UV'] ? $apiData['UV'] : 14;
	// fixed
	$extremes['maxH'] = 100;
	$extremes['minG'] = 0;
	$extremes['minS'] = 0;
	$extremes['minR'] = 0;

	if(!isset($apiData['T'])){
		$apiData['T'] = $extremes['minT'] + ($extremes['maxT'] - $extremes['minT']);
	}
	$current['colorT1'] = fill($apiData['T'],array($extremes['minT']-0.001,$extremes['maxT']+0.001),array("#0040ff","#b30000"));
	$current['colorT2'] = fill($apiData['T'],array($extremes['minT']-0.001,$extremes['maxT']+0.001),array("#002eba","#6d0000"));
	$current['colorTFont'] = fontColor($current['colorT1']);

	if(!isset($apiData['H'])){
		$apiData['H'] = $extremes['minH'] + ($extremes['maxH'] - $extremes['minH']);
	}
	$current['colorH1'] = fill($apiData['H'],array($extremes['minH']-0.001,$extremes['maxH']+0.001),array("#96600a","#159b2e"));
	$current['colorH2'] = fill($apiData['H'],array($extremes['minH']-0.001,$extremes['maxH']+0.001),array("#7f4e01","#006813"));
	$current['colorHFont'] = fontColor($current['colorH1']);

	if(!isset($apiData['P'])){
		$apiData['P'] = $extremes['minP'] + ($extremes['maxP'] - $extremes['minP']);
	}
	$current['colorP1'] = fill($apiData['P'],array($extremes['minP']-0.001,$extremes['maxP']+0.001),array("#c18100","#7901c4"));
	$current['colorP2'] = fill($apiData['P'],array($extremes['minP']-0.001,$extremes['maxP']+0.001),array("#875a00","#41006b"));
	$current['colorPFont'] = fontColor($current['colorP1']);

	if(!isset($apiData['W'])){
		$apiData['W'] = $extremes['minW'] + ($extremes['maxW'] - $extremes['minW']);
	}
	$current['colorW1'] = fill($apiData['G'],array($extremes['minG']-0.001,$extremes['maxG']+0.001),array("#aaaaaa","#7901c4"));
	$current['colorW2'] = fill($apiData['G'],array($extremes['minG']-0.001,$extremes['maxG']+0.001),array("#919191","#540089"));
	$current['colorWFont'] = fontColor($current['colorW1']);

	if(!isset($apiData['R'])){
		$apiData['R'] = $extremes['minR'] + ($extremes['maxR'] - $extremes['minR']);
	}
	$current['colorR1'] = fill($apiData['R'],array($extremes['minR']-0.001,$extremes['maxR']+0.001),array("#a5c7ff","#003fa5"));
	$current['colorR2'] = fill($apiData['R'],array($extremes['minR']-0.001,$extremes['maxR']+0.001),array("#7fadf9","#003182"));
	if($apiData['R']==0){
		$current['colorR1'] = "rgb(201, 201, 201)";
		$current['colorR2'] = "rgb(165, 165, 165)";
	}
	$current['colorRFont'] = fontColor($current['colorR1']);


	if($solarSensor){
		$current['colorS1'] = fill($current['S'],array($extremes['minS']-0.001,$extremes['maxS']+0.001),array("#aaaaaa","#ffed7a"));
		$current['colorS2'] = fill($current['S'],array($extremes['minS']-0.001,$extremes['maxS']+0.001),array("#919191","#ffe228"));
		$current['colorSFont'] = fontColor($current['colorS1']);
	}
	if(isset($apiData['UV'])){
		$current['colorUV1'] = fill($apiData['UV'],array(0-0.001,$extremes['maxUV']+0.001),array("#aaaaaa","#f94343"));
		$current['colorUV2'] = fill($apiData['UV'],array(0-0.001,$extremes['maxUV']+0.001),array("#919191","#fc2020"));
		$current['colorUVFont'] = fontColor($current['colorUV1']);
	}

	// online/offline
	$offlineTime = time() - $maxInterval*60;
	if($offlineTime>$current['DateTime']){
		$current['offline'] = 1;
	}
	else{
		$current['offline'] = 0;
	}
	$current['dt'] = date($dateTimeFormat,$current['DateTime']);
	$current = json_encode($current);

	echo $current; // OUTPUT

	function getBft($input){
		if($input<0.3){
			return array(0,"#ffffff","#000");
		}
		if($input>=0.3 && $input<1.5){
			return array(1,"#CCFFFF","#000");
		}
		if($input>=1.5 && $input<3.3){
			return array(2,"#99FFCC","#000");
		}
		if($input>=3.3 && $input<5.5){
			return array(3,"#99FF99","#000");
		}
		if($input>=5.5 && $input<8){
			return array(4,"#99FF66","#000");
		}
		if($input>=8 && $input<10.8){
			return array(5,"#99FF00","#000");
		}
		if($input>=10.8 && $input<13.9){
			return array(6,"#CCFF00","#000");
		}
		if($input>=13.9 && $input<17.2){
			return array(7,"#FFFF00","#000");
		}
		if($input>=17.2 && $input<20.7){
			return array(8,"#FFCC00","#000");
		}
		if($input>=20.7 && $input<24.5){
			return array(9,"#FF9900","#000");
		}
		if($input>=24.5 && $input<28.4){
			return array(10,"#FF6600","#000");
		}
		if($input>=28.4 && $input<32.6){
			return array(11,"#FF3300","#000");
		}
		if($input>=32.6){
			return array(12,"#FF0000","#000");
		}
	}
	function getTrend($trend,$parameter){
		global $displayWindUnits;
		global $displayPressUnits;
		if($parameter=="T" || $parameter=="A" || $parameter=="D"){
			if($trend<-0.5){
				return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
			}
			else if($trend>0.5){
				return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
			}
			else{
				return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
			}
		}
		if($parameter=="H"){
			if($trend<-3){
				return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
			}
			else if($trend>3){
				return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
			}
			else{
				return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
			}
		}
		if($parameter=="W" || $parameter=="G"){
			if($displayWindUnits=="ms"){
				if($trend<-1.5){
					return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
				}
				else if($trend>1.5){
					return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
				}
				else{
					return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
				}
			}
			else{
				if($trend<-5){
					return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
				}
				else if($trend>5){
					return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
				}
				else{
					return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
				}
			}
		}
		if($parameter=="P"){
			if($displayPressUnits=="hpa"){
				if($trend<-0.5){
					return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
				}
				else if($trend>0.5){
					return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
				}
				else{
					return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
				}
			}
			else{
				if($trend<-0.05){
					return "<span class='mticon-trenddown' style='font-size:1.3em'></span>";
				}
				else if($trend>0.05){
					return "<span class='mticon-trendup' style='font-size:1.3em'></span>";
				}
				else{
					return "<span class='mticon-trendneutral' style='font-size:1.3em'></span>";
				}
			}
		}
	}
	// Humidex (Canada) - https://en.wikipedia.org/wiki/Humidex
	function getHX($HX_T,$HX_D){
		$constKelvin = 273.16;
		$dewpointK = $HX_D + $constKelvin;
		$HX = $HX_T + 0.5555*(6.11*pow(exp(1),(5417.7530*((1/273.16)-(1/$dewpointK))))-10); 
		return number_format($HX,1,".","");
	}
	// Heat Index (U.S.) - https://en.wikipedia.org/wiki/Heat_index
	function getHI($HI_T,$HI_H){
		global $displayTempUnits;
		if($HI_T < 26.7){
			return "--";
		}
		else{
			$HI_T = convertor($HI_T,"C","F");
			$c1 = -42.379;
			$c2 = 2.04901523;
			$c3 = 10.14333127;
			$c4 = -0.22475541;
			$c5 = -6.83783 * pow(10, -3);
			$c6 = -5.481717 * pow(10, -2);
			$c7 = 1.22874 * pow(10, -3);
			$c8 = 8.5282 * pow(10, -4);
			$c9 = -1.99 * pow(10, -6);
			$HI = $c1 + $c2 * $HI_T + $c3 * $HI_H + $c4 * $HI_T * $HI_H + $c5 * $HI_T * $HI_T + $c6 * $HI_H * $HI_H + $c7 * $HI_T * $HI_T * $HI_H + $c8 * $HI_T * $HI_H * $HI_H + $c9 * $HI_T * $HI_T * $HI_H * $HI_H;
			$HI = convertor($HI,"F",$displayTempUnits);
			return number_format($HI,1,".","");
		}
	}
	// Wind Chill - https://en.wikipedia.org/wiki/Wind_chill
	function getWCh($WCh_T,$WCh_W){
		global $displayTempUnits;
		// wind chill only valid for T<=10C and W>4.8km/h
		if($WCh_T <= 10 && $WCh_W > 4.8){
			$WCh = 13.12 + 0.6215 * $WCh_T - 11.37 * pow($WCh_W,0.16) + 0.3965 * $WCh_T * pow($WCh_W,0.16);
			$WCh = convertor($WCh,"C",$displayTempUnits);
			return number_format($WCh,1,".","");
		}
		else{
			return "-- ";
		}
	}
	// Wet-bulb - https://en.wikipedia.org/wiki/Wet-bulb_temperature
	function getWB($WB_T,$WB_H){
		global $displayTempUnits;
		$e = $WB_H / 100 * 6.105 * exp(17.27 * $WB_T / (237.7 + $WB_T));
		$WB = 0.567 * $WB_T + 0.393 * $e + 3.94;
		$WB = convertor($WB,"C",$displayTempUnits);
		return number_format($WB,1,".","");
	}
	// Vapor-pressure of water - https://en.wikipedia.org/wiki/Vapour_pressure_of_water
	function getVP($VP_T){
		// using Buck equation - most accurate
		$A = 18.678 - $VP_T / 234.5;
		$B = $VP_T / (257.14 + $VP_T);
		$VP = 0.61121 * exp($A * $B); // in kPa
		$VP = $VP * 0.00986923; // in atm
		return number_format($VP,4,".","");
	}
	// Saturated Water Vapor Pressure - https://www.weather.gov/media/epz/wxcalc/vaporPressure.pdf
	function getSVP($SVP_T){
		global $displayPressUnits;
		$decimalsP = $displayPressUnits == "inhg" ? 2 : 1;
		$exponent = (7.5 * $SVP_T) / (237.3 + $SVP_T);
		$SVP = 6.11 * pow(10,$exponent);
		$SVP = convertor($SVP, "hpa", $displayPressUnits);
		return number_format($SVP,$decimalsP,".","");
	}
	// Absolute humidity - https://carnotcycle.wordpress.com/2012/08/04/how-to-convert-relative-humidity-to-absolute-humidity/
	function getAH($AH_T,$AH_H){
		$A = (17.67 * $AH_T) / ($AH_T + 243.5);
		$B = 6.112 * exp($A) * $AH_H * 2.1674;
		$AH = $B / (273.15 + $AH_T);
		return number_format($AH,2,".",""); // in g/m3
	}
	// Station pressure 
	function getP($P_T, $P_H, $P_P){
		global $displayPressUnits;
		global $stationElevation;
		global $stationElevationUnits;
		$decimalsP = $displayPressUnits == "inhg" ? 2 : 1;

		// convert elevation to m
		if($stationElevationUnits=="ft"){
			$alt = $stationElevation * 0.3048;
		}
		else{
			$alt = $stationElevation;
		}

		$T_K = $P_T + 273.15;
		$g = 9.80665; // Earth-surface gravitational acceleration
		$M = 0.0289644; // molar mass of dry air
		$R = 8.31447; // universal gas constant 
		$A = ($g * $M * $alt) / ($R * $T_K);
		$A = $A * (-1);
		$P = $P_P * exp($A);
		$P = convertor($P,"hpa",$displayPressUnits);
		return number_format($P,$decimalsP,".","");
	}
	function fontColor($color){
		$tone = getColorHue($color,"rgb");
		return $tone=="light" ? "black" : "white";
	}
?>
