<?php
	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	include($base."scripts/functions.php");
	
	//error_reporting(E_ALL);
	
	// load user settings
	foreach($_GET as $key=>$value){
		$parameters[trim($key)] = trim(urldecode($value));
	}
	
	echo "<h2>Update Test</h2>";
	
	// parse Weather Display
	if($parameters['type'] == "wd"){
		echo "Update type tested: Weather Display<br><br>";
		$path = $parameters['path'];
		echo "Loading clientraw.txt from ".$path."...<br><br>";
		$dataRaw = file_get_contents($path);
		$dataRaw = preg_replace('/^[ \t]*[\r\n]+/m', '', $dataRaw);
		
		if($dataRaw==""){
			die("Unable to load conditions from the path specified. Make sure the file has 'read' permissions and also check the URL directly from the browser. If you cannot get this to work you will need to use alternative URL - use relative path if you specified URL or use URL if you tried relative path. If even this does not help, contact me (Jachym).");
		}
		else{
			echo "Raw clientraw.txt data: <br><br>".$dataRaw."<br><br>";
		}
		$data = explode(" ",$dataRaw);
		echo "Parsing data...<br><br>";
		 
		// use clientraw standardized field numbers and units
		$dateTimeFormatted = strtotime($data[141]."-".$data[36]."-".$data[35]." ".$data[29].":".$data[30].":".$data[31]);
		$Traw = $data[4];
		$Hraw = $data[5];
		$Praw = $data[6];
		$Wraw = $data[1];
		$Graw = $data[140];
		$Rraw = $data[7];
		$RRraw = $data[10]*60;
		$Braw = $data[3];
		if($solarSensor){
			$Sraw = $data[127];
		}
		
		// convert
		echo "Checking units and performing conversion if necessary...<br><br>";
		echo "Temperature<br>";
		if($dataTempUnits=="C"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$T = convertor($Traw,"C",$dataTempUnits);
		$T = number_format($T,1,".","");
		echo "Final temperature value: ".$T." ".$dataTempUnits."<br><br>";
		
		// humidity
		echo "Humidity<br>";
		$H = $Hraw;
		echo "Final humidity value: ".$H." %<br><br>";
		
		// pressure
		echo "Pressure<br>";
		echo "Raw value: ".$Praw."<br>";
		echo "Database pressure units: ".$dataPressUnits."<br>";
		echo "File pressure units: hPa<br>";
		if($dataPressUnits=="hpa"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$P = convertor($Praw,"hpa",$dataPressUnits);
		$P = number_format($P,2,".","");
		echo "Final pressure value: ".$P." ".$dataPressUnits."<br><br>";
		
		// wind speed
		echo "Wind speed<br>";
		echo "Raw value: ".$Wraw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind speed units: knots<br>";
		if($dataWindUnits=="kt"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$W = convertor($Wraw,"kt",$dataWindUnits);
		$W = number_format($W,1,".","");
		echo "Final wind speed value: ".$W." ".$dataWindUnits."<br><br>";
		
		// wind gust
		echo "Wind gust<br>";
		echo "Raw value: ".$Graw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind gust units: knots<br>";
		if($dataWindUnits=="kt"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$G = convertor($Graw,"kt",$dataWindUnits);
		$G = number_format($G,1,".","");
		echo "Final wind speed value: ".$G." ".$dataWindUnits."<br><br>";
		
		// precipitation
		echo "Precipitation<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."<br>";
		echo "File precipitation units: mm<br>";
		if($dataRainUnits=="mm"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$R = convertor($Rraw,"mm",$dataRainUnits);
		$R = number_format($R,2,".","");
		echo "Final precipitation value: ".$R." ".$dataRainUnits."<br><br>";
		
		// rain rate
		echo "Rain rate<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."/h<br>";
		echo "File precipitation units: mm/h<br>";
		if($dataRainUnits=="mm"){
			echo "Same units, no need to do any conversion, only convert to hour rate.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$RR = convertor($RRraw,"mm",$dataRainUnits);
		$RR = number_format($RR,2,".","");
		echo "Final rain rate value: ".$RR." ".$dataRainUnits."/h<br><br>";
		
		// wind direction
		echo "Wind direction<br>";
		$B = $Braw;
		echo "Final wind direction value: ".$B." degrees<br><br>";
		
		// solar radiation
		if(!$solarSensor){
			echo "Solar sensor disabled in main settings, ignoring.<br>";
		}
		else{
			echo "Solar sensor enabled in main settings, reading data...<br>";
			$S = $Sraw;
			echo "Final solar radiation value: ".$S." W/m2<br><br>";
		}
		
		// for A and D
		$T_C = $Traw;
		$W_MS = convertor($Wraw,"kt","ms");
	}
	
	// parse Cumulus
	if($parameters['type'] == "cumulus"){
		echo "Update type tested: Cumulus<br><br>";
		$path = $parameters['path'];
		echo "Loading realtime.txt from ".$path."...<br><br>";
		$dataRaw = file_get_contents($path);
		$dataRaw = preg_replace('/^[ \t]*[\r\n]+/m', '', $dataRaw);
		
		if($dataRaw==""){
			die("Unable to load conditions from the path specified. Make sure the file has 'read' permissions and also check the URL directly from the browser. If you cannot get this to work you will need to use alternative URL - use relative path if you specified URL or use URL if you tried relative path. If even this does not help, contact me (Jachym).");
		}
		else{
			echo "Raw realtime.txt data: <br><br>".$dataRaw."<br><br>";
		}
		$data = explode(" ",$dataRaw);
		echo "Parsing data...<br><br>";
		 
		// use clientraw standardized field numbers and units
		$dateRaw = explode($parameters['separator'],$data[0]);
		$dateY = ($dateRaw[2]+2000);
		$dateM = ($dateRaw[1]);
		$dateD = ($dateRaw[0]);
		$dateTimeFormatted = strtotime($dateY."-".$dateM."-".$dateD." ".$data[1]);
		$Traw = $data[2];
		$Hraw = $data[3];
		$Praw = $data[10];
		$Wraw = $data[5];
		$Graw = $data[6];
		$Rraw = $data[9];
		$RRraw = $data[8];
		$Braw = $data[7];
		if($solarSensor){
			$Sraw = $data[45];
		}
		
		// convert
		echo "Checking units and performing conversion if necessary...<br><br>";
		echo "Temperature<br>";
		if($dataTempUnits==$data[14]){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$T = convertor($Traw,$data[14],$dataTempUnits);
		$T = number_format($T,1,".","");
		echo "Final temperature value: ".$T." ".$dataTempUnits."<br><br>";
		
		// humidity
		echo "Humidity<br>";
		$H = $Hraw;
		echo "Final humidity value: ".$H." %<br><br>";
		
		// pressure
		echo "Pressure<br>";
		echo "Raw value: ".$Praw."<br>";
		$unitsP = strtolower($data[15]);
		if($unitsP=="in"){
			$unitsP = "inhg";
		}
		echo "Database pressure units: ".$dataPressUnits."<br>";
		echo "File pressure units: ".$unitsP."<br>";
		if($dataPressUnits==$unitsP){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$P = convertor($Praw,$unitsP,$dataPressUnits);
		$P = number_format($P,2,".","");
		echo "Final pressure value: ".$P." ".$dataPressUnits."<br><br>";
		
		// wind speed
		echo "Wind speed<br>";
		echo "Raw value: ".$Wraw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		$unitsW = strtolower($data[13]);
		echo "File wind speed units: ".$unitsW."<br>";
		if($dataWindUnits==$unitsW){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$W = convertor($Wraw,$unitsW,$dataWindUnits);
		$W = number_format($W,1,".","");
		echo "Final wind speed value: ".$W." ".$dataWindUnits."<br><br>";
		
		// wind gust
		echo "Wind gust<br>";
		echo "Raw value: ".$Graw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind gust units: ".$unitsW."<br>";
		if($dataWindUnits==$unitsW){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$G = convertor($Graw,$unitsW,$dataWindUnits);
		$G = number_format($G,1,".","");
		echo "Final wind speed value: ".$G." ".$dataWindUnits."<br><br>";
		
		// precipitation
		echo "Precipitation<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."<br>";
		$unitsR = strtolower($data[16]);
		echo "File precipitation units: ".$unitsR."<br>";
		if($dataRainUnits==$unitsR){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$R = convertor($Rraw,$unitsR,$dataRainUnits);
		$R = number_format($R,2,".","");
		echo "Final precipitation value: ".$R." ".$dataRainUnits."<br><br>";
		
		// rain rate
		echo "Rain rate<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."/h<br>";
		echo "File precipitation units: ".$unitsR."/h<br>";
		if($dataRainUnits==$unitsR){
			echo "Same units, no need to do any conversion, only convert to hour rate.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$RR = convertor($RRraw,$unitsR,$dataRainUnits);
		$RR = number_format($RR,2,".","");
		echo "Final rain rate value: ".$RR." ".$dataRainUnits."/h<br><br>";
		
		// wind direction
		echo "Wind direction<br>";
		$B = $Braw;
		echo "Final wind direction value: ".$B." degrees<br><br>";
		
		// solar radiation
		if(!$solarSensor){
			echo "Solar sensor disabled in main settings, ignoring.<br>";
		}
		else{
			echo "Solar sensor enabled in main settings, reading data...<br>";
			$S = $Sraw;
			echo "Final solar radiation value: ".$S." W/m2<br><br>";
		}
		
		// for A and D
		$T_C = convertor($T,$dataTempUnits,"C");
		$W_MS = convertor($W,$dataWindUnits,"ms");
	}
	
	// parse WU
	if($parameters['type'] == "wu"){
		$WUID = $_GET['WUID'];
		if(strlen($WUID) < 5){
			die("When using WU you need to provide WU API key in the Main setup.");
		}
		echo "Update type tested: Weather Underground<br><br>";
		echo "Loading data from weatherunderground.com for station: ".$WUID."...<br><br>";
		$wuURL = "https://api.weather.com/v2/pws/observations/current?stationId=".$WUID."&format=json&units=m&apiKey=".$wuStationAPI;
		$fileRaw = file_get_contents($wuURL);
		if($fileRaw==""){
            $fileRaw = curlMain($wuURL,5);
        }
		$fileRaw = json_decode($fileRaw, true);

		if($fileRaw==""){
			die("Unable to load conditions from weatherunderground.com. Contact me (Jachym).");
		}

		$dateTimeFormatted = $fileRaw['observations'][0]['epoch'];
		
		echo "Parsing data...<br><br>";
		 
		// use standardized field numbers and units
		$Traw = $fileRaw['observations'][0]['metric']['temp']; // in C
		$Hraw = $fileRaw['observations'][0]['humidity'];
		$Praw = $fileRaw['observations'][0]['metric']['pressure'];
		$Wraw = $fileRaw['observations'][0]['metric']['windSpeed'];
		$Graw = $fileRaw['observations'][0]['metric']['windGust'];
		$Rraw = $fileRaw['observations'][0]['metric']['precipTotal'];
		$RRraw = $fileRaw['observations'][0]['metric']['precipRate'];
		$Braw = $fileRaw['observations'][0]['winddir'];
		if($solarSensor){
			$Sraw = $fileRaw['observations'][0]['solarRadiation'];
		}
		
		// convert
		echo "Checking units and performing conversion if necessary...<br><br>";
		echo "Temperature<br>";
		echo "Raw value: ".$Traw."<br>";
		if($dataTempUnits=="C"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$T = convertor($Traw,"C",$dataTempUnits);
		$T = number_format($T,1,".","");
		echo "Final temperature value: ".$T." ".$dataTempUnits."<br><br>";
		
		// humidity
		echo "Humidity<br>";
		$H = $Hraw;
		echo "Final humidity value: ".$H." %<br><br>";
		
		// pressure
		echo "Pressure<br>";
		echo "Raw value: ".$Praw."<br>";
		echo "Database pressure units: ".$dataPressUnits."<br>";
		echo "File pressure units: hPa<br>";
		if($dataPressUnits=="hpa"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$P = convertor($Praw,"hpa",$dataPressUnits);
		$P = number_format($P,2,".","");
		echo "Final pressure value: ".$P." ".$dataPressUnits."<br><br>";
		
		// wind speed
		echo "Wind speed<br>";
		echo "Raw value: ".$Wraw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind speed units: kmh<br>";
		if($dataWindUnits=="kmh"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$W = convertor($Wraw,"kmh",$dataWindUnits);
		$W = number_format($W,1,".","");
		echo "Final wind speed value: ".$W." ".$dataWindUnits."<br><br>";
		
		// wind gust
		echo "Wind gust<br>";
		echo "Raw value: ".$Graw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind gust units: kmh<br>";
		if($dataWindUnits=="kmh"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$G = convertor($Graw,"kmh",$dataWindUnits);
		$G = number_format($G,1,".","");
		echo "Final wind speed value: ".$G." ".$dataWindUnits."<br><br>";
		
		// precipitation
		echo "Precipitation<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."<br>";
		echo "File precipitation units: mm<br>";
		if($dataRainUnits=="mm"){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$R = convertor($Rraw,"mm",$dataRainUnits);
		$R = number_format($R,2,".","");
		echo "Final precipitation value: ".$R." ".$dataRainUnits."<br><br>";
		
		// rain rate
		echo "Rain rate<br>";
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."/h<br>";
		echo "File precipitation units: mm/h<br>";
		if($dataRainUnits=="mm"){
			echo "Same units, no need to do any conversion, only convert to hour rate.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$RR = convertor($RRraw,"mm",$dataRainUnits);
		$RR = number_format($RR,2,".","");
		echo "Final rain rate value: ".$RR." ".$dataRainUnits."/h<br><br>";
		
		// wind direction
		echo "Wind direction<br>";
		$B = $Braw;
		echo "Final wind direction value: ".$B." degrees<br><br>";
		
		// solar radiation
		if(!$solarSensor){
			echo "Solar sensor disabled in main settings, ignoring.<br>";
		}
		else{
			echo "Solar sensor enabled in main settings, reading data...<br>";
			$S = $Sraw;
			echo "Final solar radiation value: ".$S." W/m2<br><br>";
		}
		
		// for A and D
		$T_C = $Traw;
		$W_MS = convertor($Wraw,"mph","ms");
	} // end wu
	
	// parse custom
	if($parameters['type'] == "custom"){
		echo "Update type tested: Custom file<br><br>";
		$path = $parameters['path'];
		echo "Loading custom text file from ".$path."...<br><br>";
		$dataRaw = file_get_contents($path);
		$dataRaw = preg_replace('/^[ \t]*[\r\n]+/m', '', $dataRaw);
		
		if($dataRaw==""){
			die("Unable to load conditions from the path specified. Make sure the file has 'read' permissions and also check the URL directly from the browser. If you cannot get this to work you will need to use alternative URL - use relative path if you specified URL or use URL if you tried relative path. If even this does not help, contact me (Jachym).");
		}
		else{
			echo "Raw text file data: <br><br>".$dataRaw."<br><br>";
		}
		$data = explode(" ",$dataRaw);
		echo "Parsing data...<br><br>";
		 
		echo "Separating fields...<br>";
		$delimiter = $parameters['delimiter'];
		if($delimiter=="semicolon"){
			$fields = explode(";",$dataRaw);
		}
		if($delimiter=="comma"){
			$fields = explode(",",$dataRaw);
		}
		if($delimiter=="space"){
			$fields = explode(" ",$dataRaw);
		}
		if($delimiter=="tab"){
			$fields = preg_split('/\s+/', $dataRaw);
		}
		if($delimiter=="colon"){
			$fields = explode(":",$dataRaw);
		}
		if($delimiter=="vertical"){
			$fields = explode("|",$dataRaw);
		}
		if($parameters['decimalSeparator']=="comma"){
			echo "Decimal separator is a comma, replacing with a period for correct parsing by PHP.<br>";
		}
		else{
			echo "Decimal separator is a period, no need to do anything, can be parsed by PHP.<br>";
		}
			
		for($n=0;$n<count($fields);$n++){
			$fields[$n] = trim($fields[$n]);
			if($parameters['separator']=="comma"){
				$fields[$n] = str_replace(",",".",$fields[$n]);
			}
		}
		
		echo "<br><br>Parsing date and time...";
		$dateTimeType = $parameters['dateformat'];
		if($dateTimeType=="single"){
			echo "Date and time in a single field.<br>";
			$dateTimeRaw = $fields[$parameters['dtSingleField']];
			$dateTime = date_create_from_format($parameters['dtSingleFormat'],$dateTimeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="double"){
			echo "Date in one field, time in a different one.<br>";
			$dateRaw = $fields[$parameters['dtDoubleDateField']];
			$timeRaw = $fields[$parameters['dtDoubleTimeField']];
			$dateTime = date_create_from_format($parameters['dtDoubleDateFormat']." ".$parameters['dtDoubleTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separateDate"){
			echo "Date in separate fields, time in one field.<br>";
			$timeRaw = $fields[$parameters['dtSeparateDateTimeField']];
			$Y = $fields[$parameters['dtSeparateDateYearField']];
			$m = $fields[$parameters['dtSeparateDateMonthField']];
			$d = $fields[$parameters['dtSeparateDateDayField']];
			$dateRaw = $Y."-".$m."-".$d;
			$dateTime = date_create_from_format("Y-m-d ".$parameters['dtSeparateDateTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separateTime"){
			echo "Time in separate fields, date in one field.<br>";
			$dateRaw = $fields[$parameters['dtSeparateTimeDateField']];
			$H = $fields[$parameters['dtSeparateTimeHourField']];
			$i = $fields[$parameters['dtSeparateTimeMinuteField']];
			$timeRaw = $H.":".$i;
			$dateTime = date_create_from_format($parameters['dtSeparateTimeDateFormat']." H:i",$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separate"){
			echo "Date and time parameters all in separate fields.<br>";
			$Y = $fields[$parameters['dtSeparateYearField']];
			$m = $fields[$parameters['dtSeparateMonthField']];
			$d = $fields[$parameters['dtSeparateDayField']];
			$H = $fields[$parameters['dtSeparateHourField']];
			$i = $fields[$parameters['dtSeparateMinuteField']];
			if($H<10){
				$H = "0".$H;
			}
			if($i<10){
				$i = "0".$i;
			}
			$dateTimeFormatted = $Y."-".$m."-".$d." ".$H.":".$i;
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		echo "<br>";
		
		// parse variables
		echo "<br><br>Parsing variables...<br>";
		echo "<br>";
		
		// temperature
		echo "Temperature<br>";
		$Traw = $fields[$parameters['fieldT']];
		echo "Raw value: ".$Traw."<br>";
		echo "Database temperature units: ".$dataTempUnits."<br>";
		echo "File temperature units: ".$parameters['unitsT']."<br>";
		if($dataTempUnits==$parameters['unitsT']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$T = convertor($Traw,$parameters['unitsT'],$dataTempUnits);
		$T = number_format($T,1,".","");
		echo "Final temperature value: ".$T." ".$dataTempUnits."<br><br>";
		
		// humidity
		echo "Humidity<br>";
		$H = $fields[$parameters['fieldH']];
		echo "Final humidity value: ".$H." %<br><br>";
		
		// pressure
		echo "Pressure<br>";
		$Praw = $fields[$parameters['fieldP']];
		echo "Raw value: ".$Praw."<br>";
		echo "Database pressure units: ".$dataPressUnits."<br>";
		echo "File pressure units: ".$parameters['unitsP']."<br>";
		if($dataPressUnits==$parameters['unitsP']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$P = convertor($Praw,$parameters['unitsP'],$dataPressUnits);
		$P = number_format($P,2,".","");
		echo "Final pressure value: ".$P." ".$dataPressUnits."<br><br>";
		
		// wind speed
		echo "Wind speed<br>";
		$Wraw = $fields[$parameters['fieldW']];
		echo "Raw value: ".$Wraw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind speed units: ".$parameters['unitsW']."<br>";
		if($dataWindUnits==$parameters['unitsW']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$W = convertor($Wraw,$parameters['unitsW'],$dataWindUnits);
		$W = number_format($W,1,".","");
		echo "Final wind speed value: ".$W." ".$dataWindUnits."<br><br>";
		
		// wind gust
		echo "Wind gust<br>";
		$Graw = $fields[$parameters['fieldG']];
		echo "Raw value: ".$Graw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind gust units: ".$parameters['unitsG']."<br>";
		if($dataWindUnits==$parameters['unitsG']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$G = convertor($Graw,$parameters['unitsG'],$dataWindUnits);
		$G = number_format($G,1,".","");
		echo "Final wind speed value: ".$G." ".$dataWindUnits."<br><br>";
		
		// precipitation
		echo "Precipitation<br>";
		$Rraw = $fields[$parameters['fieldR']];
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."<br>";
		echo "File precipitation units: ".$parameters['unitsR']."<br>";
		if($dataRainUnits==$parameters['unitsR']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$R = convertor($Rraw,$parameters['unitsR'],$dataRainUnits);
		$R = number_format($R,2,".","");
		echo "Final precipitation value: ".$R." ".$dataRainUnits."<br><br>";
		
		// rain rate
		echo "Rain rate<br>";
		$RRraw = $fields[$parameters['fieldRR']];
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."/h<br>";
		echo "Rain rate is saved in the database in units per hour.<br>";
		if($parameters['unitsR']=="mmmin"){
			echo "Rain rate in file in mm/min, converting to m/h...<br>";
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "mm";
		}
		else if($parameters['unitsR']=="inmin"){
			echo "Rain rate in file in in/min, converting to in/h...<br>";
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "in";
		}
		else{
			echo "File rain rate units: ".$parameters['unitsR']."<br>";
		}
		if($dataRainUnits==$parameters['unitsR']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$RR = convertor($RRraw,$parameters['unitsR'],$dataRainUnits);
		$RR = number_format($RR,2,".","");
		echo "Final rain rate value: ".$RR." ".$dataRainUnits."/h<br><br>";
		
		// wind direction
		echo "Wind direction<br>";
		$B = $fields[$parameters['fieldB']];
		echo "Final wind direction value: ".$B." degrees<br><br>";
		
		// solar radiation
		if(!$solarSensor){
			echo "Solar sensor disabled in main settings, ignoring.<br>";
		}
		else{
			echo "Solar sensor enabled in main settings, reading data...<br>";
			echo "Solar radiation<br>";
			$S = $fields[$parameters['fieldS']];
			echo "Final solar radiation value: ".$S." W/m2<br><br>";
		}
		
		// for A and D
		$T_C = convertor($T,$dataTempUnits,"C");
		$W_MS = convertor($W,$dataWindUnits,"ms");

		$dateTimeFormatted = strtotime($dateTimeFormatted);
	}
	
	// GENERAL PART - same for all
	#########################################################################################################
	// validate
	echo "Checking validity of data based on your limits in Main settings...<br><br>";
	$validDate = false;
	$validT = false;
	$validH = false;
	$validP = false;
	$validW = false;
	$validG = false;
	$validR = false;
	$validRR = false;
	$validB = false;
	$validD = false;
	$validA = false;
	if($solarSensor){
		$validS = false;
	}
	
	// checking date/time
	echo "Checking date and time...<br>";
	$minimumDate = strtotime("1980-01-01");
	$checkedDate = ($dateTimeFormatted);
	echo "The parsed date is ".date($dateTimeFormat,$dateTimeFormatted).", current time is ".date($dateTimeFormat)."<br>";
	if($checkedDate>$minimumDate && $checkedDate<time()){
		echo "Date/time seems sensible.<br>";
		$validDate = true;
	}
	else{
		echo "<span style='color:red'>There seems to be a problem with the date and time. Either it has not been parsed correctly or it is a date/time in the future! Check the format you specified and also the actual values.</span><br>";
	}
	
	// temperature 
	echo "Minimum temperature allowed: ".$limitTempMin." ".$dataTempUnits."<br>";
	echo "Maximum temperature allowed: ".$limitTempMax." ".$dataTempUnits."<br>";
	if($T<=$limitTempMax && $T>=$limitTempMin){
		$validT = true;
		echo "Temperature OK.<br>";
	}
	else{
		echo "<span style='color:red'>Temperature is outside the allowed limits! Temperature value will be ignored!</span><br>";
	}
	
	// humidity
	echo "Minimum humidity allowed: ".$limitHumidityMin." %<br>";
	echo "Maximum humidity allowed: ".$limitHumidityMax." %<br>";
	if($H<=$limitHumidityMax && $H>=$limitHumidityMin){
		$validH = true;
		echo "Humidity OK.<br>";
	}
	else{
		echo "<span style='color:red'>Humidity is outside the allowed limits! Humidity value will be ignored!</span><br>";
	}
	
	// pressure
	echo "Minimum pressure allowed: ".$limitPressureMin." ".$dataPressUnits."<br>";
	echo "Maximum pressure allowed: ".$limitPressureMax." ".$dataPressUnits."<br>";
	if($P<=$limitPressureMax && $P>=$limitPressureMin){
		$validP = true;
		echo "Pressure OK.<br>";
	}
	else{
		echo "<span style='color:red'>Pressure is outside the allowed limits! Pressure value will be ignored!</span><br>";
	}
	
	// wind speed
	echo "Minimum wind speed allowed: ".$limitWindMin." ".$dataWindUnits."<br>";
	echo "Maximum wind speed allowed: ".$limitWindMax." ".$dataWindUnits."<br>";
	if($W<=$limitWindMax && $W>=$limitWindMin){
		$validW = true;
		echo "Wind speed OK.<br>";
	}
	else{
		echo "<span style='color:red'>Wind speed is outside the allowed limits! Wind speed value will be ignored!</span><br>";
	}
	
	// wind gust
	echo "Minimum wind gust allowed: ".$limitWindMin." ".$dataWindUnits."<br>";
	echo "Maximum wind gust allowed: ".$limitWindMax." ".$dataWindUnits."<br>";
	if($G<=$limitWindMax && $G>=$limitWindMin){
		$validG = true;
		echo "Wind gust OK.<br>";
	}
	else{
		echo "<span style='color:red'>Wind gust is outside the allowed limits! Wind gust value will be ignored!</span><br>";
	}
	
	// precipitation
	echo "Minimum daily precipitation allowed: ".$limitRainMin." ".$dataRainUnits."<br>";
	echo "Maximum daily precipitation allowed: ".$limitRainMax." ".$dataRainUnits."<br>";
	if($R<=$limitRainMax && $R>=$limitRainMin){
		$validR = true;
		echo "Daily precipitation OK.<br>";
	}
	else{
		echo "<span style='color:red'>Daily precipitation is outside the allowed limits! Daily precipitation value will be ignored!</span><br>";
	}
	
	// rain rate
	echo "Minimum rain rate allowed: ".$limitRainRateMin." ".$dataRainUnits."/h<br>";
	echo "Maximum rain rate allowed: ".$limitRainRateMax." ".$dataRainUnits."/h<br>";
	if($RR<=$limitRainRateMax && $RR>=$limitRainRateMin){
		$validRR = true;
		echo "Rain rate OK.<br>";
	}
	else{
		echo "<span style='color:red'>Rain rate is outside the allowed limits! Rain rate value will be ignored!</span><br>";
	}
	
	// wind direction
	echo "Minimum wind direction allowed: ".$limitBearingMin." degrees<br>";
	echo "Maximum wind direction allowed: ".$limitBearingMax." degrees<br>";
	if($B<=$limitBearingMax && $B>=$limitBearingMin){
		$validB = true;
		echo "Wind direction OK.<br>";
	}
	else{
		echo "<span style='color:red'>Wind direction is outside the allowed limits! Wind direction value will be ignored!</span><br>";
	}
	
	if($solarSensor){
		// solar radiation
		echo "Minimum solar radiation allowed: ".$limitSolarMin." W/m2<br>";
		echo "Maximum solar radiation allowed: ".$limitSolarMax." W/m2<br>";
		if($S<=$limitSolarMax && $S>=$limitSolarMin){
			$validS = true;
			echo "Solar radiation OK.<br>";
		}
		else{
			echo "<span style='color:red'>Solar radiation is outside the allowed limits! Solar radiation value will be ignored!</span><br>";
		}
	}
	
	// Calculating apparent and dew point
	echo "<br>";
	echo "Calculating dew point based on parsed values...<br>";
	if($validT && $validH){
		echo "Temperature and humidity both valid... converting and calculating dew point....<br>";
		$D = dewpoint($T_C,$H);
		// convert back
		$D = convertor($D,"C",$dataTempUnits);
		$validD = true;
		echo "Dew point calculated: ".$D." ".$dataTempUnits;
	}
	else{
		echo "<span style='color:red'>Temperature or humidity or both are not valid. Not possible to calculate dew point.</span><br>";
	}
	echo "<br>Calculating apparent temperature based on parsed values...<br>";
	if($validT && $validH && $validW){
		echo "Temperature, humidity, and wind speed all valid... converting and calculating apparent temperature....<br>";
		$A = apparent($T_C,$H,$W_MS);
		// convert back
		$A = convertor($A,"C",$dataTempUnits);
		$validA = true;
		echo "Apparent temperature calculated: ".$A." ".$dataTempUnits;
	}
	else{
		echo "<span style='color:red'>One or more parameters from the following: temperature, humidity, wind speed, are not valid. Impossible to calculate apparent temperature.</span><br>";
	}
	
	// build query
	echo "<br><br><strong>Summary</strong><br>";
	if(!$validDate){
		echo "<span style='color:red'>There is a problem with the date and time, nothing would be imported. Check the date/time format you specified and the actual values in the file.</span><br>";
	}
	else{
		echo "The following would be imported to database:<br>";
		echo "<ul>";
		echo "<li>Date/time: ".date("Y-m-d H:i",$dateTimeFormatted)."</li>";
		echo "<li>Temperature: ";
		if($validT){
			echo $T." ".$displayTempUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Dewpoint: ";
		if($validD){
			echo $D." ".$displayTempUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Apparent Temperature: ";
		if($validA){
			echo $A." ".$displayTempUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Humidity: ";
		if($validH){
			echo $H." %";
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Pressure: ";
		if($validP){
			echo $P." ".$displayPressUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Wind speed: ";
		if($validW){
			echo $W." ".$displayWindUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Wind gust: ";
		if($validG){
			echo $G." ".$displayWindUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Precipitation: ";
		if($validR){
			echo $R." ".$displayRainUnits;
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Rain rate: ";
		if($validRR){
			echo $RR." ".$displayRainUnits."/h";
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		echo "<li>Wind direction: ";
		if($validB){
			echo $B." degrees";
		}
		else{
			echo "<span style='color:red'>ERROR - see above</span>";
		}
		echo "</li>";
		if($solarSensor){
			echo "<li>Solar radiation: ";
			if($validS){
				echo $S." W/m2";
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
		}
		else{
			echo "<li>Solar radiation: No solar sensor - ignored.";
		}
	}
	echo "<br><hr><br>";
	
	echo "<br><br>--- END ----";
	
	
	
	
	
	
	
	
	
	
	
	/*
	echo "Testing CSV/text file...<br><br>";
	echo "<span style='color:red'>Errors will be highlighted in red...</span><br><br>";
	
	// try loading file
	echo "Loading CSV/text file from <i>".$parameters['path']."</i><br><br>";
	$log = file($parameters['path']);
	if(count($log)>0){
		echo "File loaded successfully.<br>";
	}
	else{
		echo "<span style='color:red'>Cannot find the file specified. Check it has the right permissions and double check the path. If you are using URL try using relative path or vice versa.</span><br>";
		die();
	}
	
	// check header
	if($parameters['fileHeader']==1){
		$header = 1;
		echo "Header row in the file, skipping first line.<br>";
	}
	else{
		$header = 0;
		echo "No header, reading from first line.<br>";
	}
	echo "<br>";
	
	// limit lines
	echo "The tested file has ".count($log)." data sets. This import test will load only the first line.<br>";
	$counter = count($log);

	if($header==0){
		$limit = 1;
	}
	else{
		$limit = 2;
	}
	
	// parse
	echo "<br>";
	echo "<hr><br>";
	echo "<br>Starting to parse data.<br><br>";
	for($n=$header;$n<$limit;$n++){
		echo "Loading line ".$n."...<br>";
		$row = $log[$n];
		$row = trim($row);
		
		// split fields
		echo "Separating fields...<br>";
		$delimiter = $parameters['delimiter'];
		if($delimiter=="semicolon"){
			$fields = explode(";",$row);
		}
		if($delimiter=="comma"){
			$fields = explode(",",$row);
		}
		if($delimiter=="space"){
			$fields = explode(" ",$row);
		}
		if($delimiter=="tab"){
			$fields = preg_split('/\s+/', $row);
		}
		if($delimiter=="colon"){
			$fields = explode(":",$row);
		}
		if($delimiter=="vertical"){
			$fields = explode("|",$row);
		}
		if($parameters['decimalSeparator']=="comma"){
			echo "Decimal separator is a comma, replacing with a period for correct parsing by PHP.<br>";
		}
		else{
			echo "Decimal separator is a period, no need to do anything, can be parsed by PHP.<br>";
		}
			
		for($n2=0;$n2<count($fields);$n2++){
			$fields[$n2] = trim($fields[$n2]);
			if($parameters['decimalSeparator']=="comma"){
				$fields[$n2] = str_replace(",",".",$fields[$n2]);
			}
		}
		
		// load date and time
		echo "<br><br>Parsing date and time...";
		$dateTimeType = $parameters['dateformat'];
		if($dateTimeType=="single"){
			echo "Date and time in a single field.<br>";
			$dateTimeRaw = $fields[$parameters['dtSingleField']];
			$dateTime = date_create_from_format($parameters['dtSingleFormat'],$dateTimeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="double"){
			echo "Date in one field, time in a different one.<br>";
			$dateRaw = $fields[$parameters['dtDoubleDateField']];
			$timeRaw = $fields[$parameters['dtDoubleTimeField']];
			$dateTime = date_create_from_format($parameters['dtDoubleDateFormat']." ".$parameters['dtDoubleTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separateDate"){
			echo "Date in separate fields, time in one field.<br>";
			$timeRaw = $fields[$parameters['dtSeparateDateTimeField']];
			$Y = $fields[$parameters['dtSeparateDateYearField']];
			$m = $fields[$parameters['dtSeparateDateMonthField']];
			$d = $fields[$parameters['dtSeparateDateDayField']];
			$dateRaw = $Y."-".$m."-".$d;
			$dateTime = date_create_from_format("Y-m-d ".$parameters['dtSeparateDateTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separateTime"){
			echo "Time in separate fields, date in one field.<br>";
			$dateRaw = $fields[$parameters['dtSeparateTimeDateField']];
			$H = $fields[$parameters['dtSeparateTimeHourField']];
			$i = $fields[$parameters['dtSeparateTimeMinuteField']];
			$timeRaw = $H.":".$i;
			$dateTime = date_create_from_format($parameters['dtSeparateTimeDateFormat']." H:i",$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		if($dateTimeType=="separate"){
			echo "Date and time parameters all in separate fields.<br>";
			$Y = $fields[$parameters['dtSeparateYearField']];
			$m = $fields[$parameters['dtSeparateMonthField']];
			$d = $fields[$parameters['dtSeparateDayField']];
			$H = $fields[$parameters['dtSeparateHourField']];
			$i = $fields[$parameters['dtSeparateMinuteField']];
			if($H<10){
				$H = "0".$H;
			}
			if($i<10){
				$i = "0".$i;
			}
			$dateTimeFormatted = $Y."-".$m."-".$d." ".$H.":".$i;
			echo "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted."<br>";
		}
		echo "<br>";
		
		// parse variables
		echo "<br><br>Parsing variables...<br>";
		echo "<br>";
		
		// temperature
		echo "Temperature<br>";
		$Traw = $fields[$parameters['fieldT']];
		echo "Raw value: ".$Traw."<br>";
		echo "Database temperature units: ".$dataTempUnits."<br>";
		echo "File temperature units: ".$parameters['unitsT']."<br>";
		if($dataTempUnits==$parameters['unitsT']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$T = convertor($Traw,$parameters['unitsT'],$dataTempUnits);
		$T = number_format($T,1,".","");
		echo "Final temperature value: ".$T." ".$dataTempUnits."<br><br>";
		
		// humidity
		echo "Humidity<br>";
		$H = $fields[$parameters['fieldH']];
		echo "Final humidity value: ".$H." %<br><br>";
		
		// pressure
		echo "Pressure<br>";
		$Praw = $fields[$parameters['fieldP']];
		echo "Raw value: ".$Praw."<br>";
		echo "Database pressure units: ".$dataPressUnits."<br>";
		echo "File pressure units: ".$parameters['unitsP']."<br>";
		if($dataPressUnits==$parameters['unitsP']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$P = convertor($Praw,$parameters['unitsP'],$dataPressUnits);
		$P = number_format($P,2,".","");
		echo "Final pressure value: ".$P." ".$dataPressUnits."<br><br>";
		
		// wind speed
		echo "Wind speed<br>";
		$Wraw = $fields[$parameters['fieldW']];
		echo "Raw value: ".$Wraw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind speed units: ".$parameters['unitsW']."<br>";
		if($dataWindUnits==$parameters['unitsW']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$W = convertor($Wraw,$parameters['unitsW'],$dataWindUnits);
		$W = number_format($W,1,".","");
		echo "Final wind speed value: ".$W." ".$dataWindUnits."<br><br>";
		
		// wind gust
		echo "Wind gust<br>";
		$Graw = $fields[$parameters['fieldG']];
		echo "Raw value: ".$Graw."<br>";
		echo "Database wind speed units: ".$dataWindUnits."<br>";
		echo "File wind gust units: ".$parameters['unitsG']."<br>";
		if($dataWindUnits==$parameters['unitsG']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$G = convertor($Graw,$parameters['unitsG'],$dataWindUnits);
		$G = number_format($G,1,".","");
		echo "Final wind speed value: ".$G." ".$dataWindUnits."<br><br>";
		
		// precipitation
		echo "Precipitation<br>";
		$Rraw = $fields[$parameters['fieldR']];
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."<br>";
		echo "File precipitation units: ".$parameters['unitsR']."<br>";
		if($dataRainUnits==$parameters['unitsR']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$R = convertor($Rraw,$parameters['unitsR'],$dataRainUnits);
		$R = number_format($R,2,".","");
		echo "Final precipitation value: ".$R." ".$dataRainUnits."<br><br>";
		
		// rain rate
		echo "Rain rate<br>";
		$RRraw = $fields[$parameters['fieldRR']];
		echo "Raw value: ".$Rraw."<br>";
		echo "Database precipitation units: ".$dataRainUnits."/h<br>";
		echo "Rain rate is saved in the database in units per hour.<br>";
		if($parameters['unitsR']=="mmmin"){
			echo "Rain rate in file in mm/min, converting to m/h...<br>";
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "mm";
		}
		else if($parameters['unitsR']=="inmin"){
			echo "Rain rate in file in in/min, converting to in/h...<br>";
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "in";
		}
		else{
			echo "File rain rate units: ".$parameters['unitsR']."<br>";
		}
		if($dataRainUnits==$parameters['unitsR']){
			echo "Same units, no need to do any conversion.<br>";
		}
		else{
			echo "Converting...<br>";
		}
		$RR = convertor($RRraw,$parameters['unitsR'],$dataRainUnits);
		$RR = number_format($RR,2,".","");
		echo "Final rain rate value: ".$RR." ".$dataRainUnits."/h<br><br>";
		
		// wind direction
		echo "Wind direction<br>";
		$B = $fields[$parameters['fieldB']];
		echo "Final wind direction value: ".$B." degrees<br><br>";
		
		// solar radiation
		if(!$solarSensor){
			echo "Solar sensor disabled in main settings, ignoring.<br>";
		}
		else{
			echo "Solar sensor enabled in main settings, reading data...<br>";
			echo "Wind direction<br>";
			$S = $fields[$parameters['fieldS']];
			echo "Final solar radiation value: ".$S." W/m2<br><br>";
		}
		
		echo "<br>Parsing line ".$n." finished.<br>";
		
		
		// validate
		echo "Checking validity of data based on your limits in Main settings...<br>";
		$validDate = false;
		$validT = false;
		$validH = false;
		$validP = false;
		$validW = false;
		$validG = false;
		$validR = false;
		$validRR = false;
		$validB = false;
		$validD = false;
		$validA = false;
		if($solarSensor){
			$validS = false;
		}
		
		// checking date/time
		echo "Checking date and time...<br>";
		$minimumDate = strtotime("1980-01-01");
		$checkedDate = strtotime($dateTimeFormatted);
		if($checkedDate>$minimumDate && $checkedDate<time()){
			echo "Date/time seems sensible.<br>";
			$validDate = true;
		}
		else{
			echo "<span style='color:red'>There seems to be a problem with the date and time. Either it has not been parsed correctly or it is a date/time in the future! Check the format you specified and also the actual values.</span><br>";
		}
		
		// temperature 
		echo "Minimum temperature allowed: ".$limitTempMin." ".$dataTempUnits."<br>";
		echo "Maximum temperature allowed: ".$limitTempMax." ".$dataTempUnits."<br>";
		if($T<=$limitTempMax && $T>=$limitTempMin){
			$validT = true;
			echo "Temperature OK.<br>";
		}
		else{
			echo "<span style='color:red'>Temperature is outside the allowed limits! Temperature value will be ignored!</span><br>";
		}
		
		// humidity
		echo "Minimum humidity allowed: ".$limitHumidityMin." %<br>";
		echo "Maximum humidity allowed: ".$limitHumidityMax." %<br>";
		if($H<=$limitHumidityMax && $H>=$limitHumidityMin){
			$validH = true;
			echo "Humidity OK.<br>";
		}
		else{
			echo "<span style='color:red'>Humidity is outside the allowed limits! Humidity value will be ignored!</span><br>";
		}
		
		// pressure
		echo "Minimum pressure allowed: ".$limitPressureMin." ".$dataPressUnits."<br>";
		echo "Maximum pressure allowed: ".$limitPressureMax." ".$dataPressUnits."<br>";
		if($P<=$limitPressureMax && $P>=$limitPressureMin){
			$validP = true;
			echo "Pressure OK.<br>";
		}
		else{
			echo "<span style='color:red'>Pressure is outside the allowed limits! Pressure value will be ignored!</span><br>";
		}
		
		// wind speed
		echo "Minimum wind speed allowed: ".$limitWindMin." ".$dataWindUnits."<br>";
		echo "Maximum wind speed allowed: ".$limitWindMax." ".$dataWindUnits."<br>";
		if($W<=$limitWindMax && $W>=$limitWindMin){
			$validW = true;
			echo "Wind speed OK.<br>";
		}
		else{
			echo "<span style='color:red'>Wind speed is outside the allowed limits! Wind speed value will be ignored!</span><br>";
		}
		
		// wind gust
		echo "Minimum wind gust allowed: ".$limitWindMin." ".$dataWindUnits."<br>";
		echo "Maximum wind gust allowed: ".$limitWindMax." ".$dataWindUnits."<br>";
		if($G<=$limitWindMax && $G>=$limitWindMin){
			$validG = true;
			echo "Wind gust OK.<br>";
		}
		else{
			echo "<span style='color:red'>Wind gust is outside the allowed limits! Wind gust value will be ignored!</span><br>";
		}
		
		// precipitation
		echo "Minimum daily precipitation allowed: ".$limitRainMin." ".$dataRainUnits."<br>";
		echo "Maximum daily precipitation allowed: ".$limitRainMax." ".$dataRainUnits."<br>";
		if($R<=$limitRainMax && $R>=$limitRainMin){
			$validR = true;
			echo "Daily precipitation OK.<br>";
		}
		else{
			echo "<span style='color:red'>Daily precipitation is outside the allowed limits! Daily precipitation value will be ignored!</span><br>";
		}
		
		// rain rate
		echo "Minimum rain rate allowed: ".$limitRainRateMin." ".$dataRainUnits."/h<br>";
		echo "Maximum rain rate allowed: ".$limitRainRateMax." ".$dataRainUnits."/h<br>";
		if($RR<=$limitRainRateMax && $RR>=$limitRainRateMin){
			$validRR = true;
			echo "Rain rate OK.<br>";
		}
		else{
			echo "<span style='color:red'>Rain rate is outside the allowed limits! Rain rate value will be ignored!</span><br>";
		}
		
		// wind direction
		echo "Minimum wind direction allowed: ".$limitBearingMin." degrees<br>";
		echo "Maximum wind direction allowed: ".$limitBearingMax." degrees<br>";
		if($B<=$limitBearingMax && $B>=$limitBearingMin){
			$validB = true;
			echo "Wind direction OK.<br>";
		}
		else{
			echo "<span style='color:red'>Wind direction is outside the allowed limits! Wind direction value will be ignored!</span><br>";
		}
		
		if($solarSensor){
			// solar radiation
			echo "Minimum solar radiation allowed: ".$limitSolarMin." W/m2<br>";
			echo "Maximum solar radiation allowed: ".$limitSolarMax." W/m2<br>";
			if($S<=$limitSolarMax && $S>=$limitSolarMin){
				$validS = true;
				echo "Solar radiation OK.<br>";
			}
			else{
				echo "<span style='color:red'>Solar radiation is outside the allowed limits! Solar radiation value will be ignored!</span><br>";
			}
		}
		
		// calculate dew point and apparent temperature
		echo "<br>";
		echo "Calculating dew point based on parsed values...<br>";
		if($validT && $validH){
			echo "Temperature and humidity both valid... converting and calculating dew point....<br>";
			// get T in Celsius
			$T_C = convertor($T,$dataTempUnits,"C");
			$D = dewpoint($T_C,$H);
			// convert back
			$D = convertor($D,"C",$dataTempUnits);
			$validD = true;
			echo "Dew point calculated: ".$D." ".$dataTempUnits;
		}
		else{
			echo "<span style='color:red'>Temperature or humidity or both are not valid. Not possible to calculate dew point.</span><br>";
		}
		echo "<br>Calculating apparent temperature based on parsed values...<br>";
		if($validT && $validH && $validW){
			echo "Temperature, humidity, and wind speed all valid... converting and calculating apparent temperature....<br>";
			// get T in Celsius
			$T_C = convertor($T,$dataTempUnits,"C");
			// get wind speed in m/s
			$W_MS = convertor($W,$dataWindUnits,"ms");
			$A = apparent($T_C,$H,$W_MS);
			// convert back
			$A = convertor($A,"C",$dataTempUnits);
			$validA = true;
			echo "Apparent temperature calculated: ".$A." ".$dataTempUnits;
		}
		else{
			echo "<span style='color:red'>One or more parameters from the following: temperature, humidity, wind speed, are not valid. Impossible to calculate apparent temperature.</span><br>";
		}
		
		// build query
		echo "<br><br><strong>Summary</strong><br>";
		if(!$validDate){
			echo "<span style='color:red'>There is a problem with the date and time, nothing would be imported. Check the date/time format you specified and the actual values in the file.</span><br>";
		}
		else{
			echo "The following would be imported to database:<br>";
			echo "<ul>";
			echo "<li>Date/time: ".$dateTimeFormatted."</li>";
			echo "<li>Temperature: ";
			if($validT){
				echo $T." ".$displayTempUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Dewpoint: ";
			if($validD){
				echo $D." ".$displayTempUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Apparent Temperature: ";
			if($validA){
				echo $A." ".$displayTempUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Humidity: ";
			if($validH){
				echo $H." %";
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Pressure: ";
			if($validP){
				echo $P." ".$displayPressUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Wind speed: ";
			if($validW){
				echo $W." ".$displayWindUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Wind gust: ";
			if($validG){
				echo $G." ".$displayWindUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Precipitation: ";
			if($validR){
				echo $R." ".$displayRainUnits;
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Rain rate: ";
			if($validRR){
				echo $RR." ".$displayRainUnits."/h";
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			echo "<li>Wind direction: ";
			if($validB){
				echo $B." degrees";
			}
			else{
				echo "<span style='color:red'>ERROR - see above</span>";
			}
			echo "</li>";
			if($solarSensor){
				echo "<li>Solar radiation: ";
				if($validS){
					echo $S." W/m2";
				}
				else{
					echo "<span style='color:red'>ERROR - see above</span>";
				}
				echo "</li>";
			}
			else{
				echo "<li>Solar radiation: No solar sensor - ignored.";
			}
		}
		echo "<br><hr><br>";
	}
	
	echo "<br><br>--- END ----";
	*/
	function dewpoint($T,$H){
		$D = round(((pow(($H/100), 0.125))*(112+0.9*$T)+(0.1*$T)-112),1);
		return $D;
	}
	
	function apparent($T,$H,$W){
		$e = ($H/100)*6.105*pow(2.71828, ((17.27*$T)/(237.7+$T)));
		$A = round(($T + 0.33*$e-0.7*$W-4),1);
		return $A;
	}
	
	function convertor($x,$unit1,$unit2){
		// prepare input
		$unit1 = trim(strtolower($unit1));
		$unit2 = trim(strtolower($unit2));
		$unit1 = str_replace("/","",$unit1);
		$unit2 = str_replace("/","",$unit2);
		$unit1 = str_replace("kts","kt",$unit1);
		$unit2 = str_replace("kts","kt",$unit2);
		$unit1 = str_replace("knots","kt",$unit1);
		$unit2 = str_replace("knots","kt",$unit2);
		$unit1 = str_replace("kph","kmh",$unit1);
		$unit2 = str_replace("kph","kmh",$unit2);
		$unit1 = str_replace("mb","hpa",$unit1);
		$unit2 = str_replace("mb","hpa",$unit2);
		$unit1 = str_replace("miles","mi",$unit1);
		$unit2 = str_replace("miles","mi",$unit2);
		$unit1 = str_replace("feet","ft",$unit1);
		$unit2 = str_replace("feet","ft",$unit2);
		$unit1 = str_replace("foot","ft",$unit1);
		$unit2 = str_replace("foot","ft",$unit2);

		// return same units
		if($unit1==$unit2){
			return $x;
		}
		
		// temperature
		else if($unit1=="c" && $unit2=="f"){
			return $x*1.8 + 32;
		}
		else if($unit1=="f" && $unit2=="c"){
			return ($x - 32)/1.8;
		}
		
		// wind speed
		else if($unit1=="ms" && $unit2=="kmh"){
			return $x * 3.6;
		}
		else if($unit1=="ms" && $unit2=="mph"){
			return $x * 2.23694;
		}
		else if($unit1=="ms" && $unit2=="kt"){
			return $x * 1.943844;
		}
		else if($unit1=="kmh" && $unit2=="ms"){
			return $x / 3.6;
		}
		else if($unit1=="kmh" && $unit2=="mph"){
			return $x * 0.621371;
		}
		else if($unit1=="kmh" && $unit2=="kt"){
			return $x * 0.539957;
		}
		else if($unit1=="mph" && $unit2=="ms"){
			return $x * 0.44704;
		}
		else if($unit1=="mph" && $unit2=="kmh"){
			return $x * 1.609344;
		}
		else if($unit1=="mph" && $unit2=="kt"){
			return $x * 0.868976;
		}
		else if($unit1=="kt" && $unit2=="ms"){
			return $x * 0.514444;
		}
		else if($unit1=="kt" && $unit2=="kmh"){
			return $x * 1.852;
		}
		else if($unit1=="kt" && $unit2=="mph"){
			return $x * 1.150779;
		}
		
		// pressure
		else if($unit1=="hpa" && $unit2=="inhg"){
			return $x * 0.02952998;
		}
		else if($unit1=="hpa" && $unit2=="mmhg"){
			return $x * 0.750063755;
		}
		else if($unit1=="inhg" && $unit2=="hpa"){
			return $x * 33.863881;
		}
		else if($unit1=="inhg" && $unit2=="mmhg"){
			return $x * 25.400069;
		}
		else if($unit1=="mmhg" && $unit2=="hpa"){
			return $x * 1.3332239;
		}
		else if($unit1=="mmhg" && $unit2=="inhg"){
			return $x * 0.03937;
		}
		
		// precipitation
		else if($unit1=="mm" && $unit2=="in"){
			return $x * 0.0393701;
		}
		else if($unit1=="in" && $unit2=="mm"){
			return $x * 25.4;
		}
		
		// distance
		else if($unit1=="km" && $unit2=="mi"){
			return $x * 0.621371;
		}
		else if($unit1=="mi" && $unit2=="km"){
			return $x * 1.60934;
		}
		else if($unit1=="km" && $unit2=="ft"){
			return $x * 3280.84;
		}
		else if($unit1=="ft" && $unit2=="km"){
			return $x * 0.0003048;
		}
		else if($unit1=="m" && $unit2=="ft"){
			return $x * 3.28084;
		}
		else if($unit1=="ft" && $unit2=="m"){
			return $x * 0.3048;
		}
		else{
			return "N/A";
		}
	}
?>