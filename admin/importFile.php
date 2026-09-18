<?php

    ############################################################################
    #
    #   Meteotemplate
    #   http://www.meteotemplate.com
    #   Free website template for weather enthusiasts
    #   Author: Jachym
    #           Brno, Czech Republic
    #   First release: 2015
    #
    ############################################################################
    #           
    #   v2.0    2017-04-20
    #   v2.1    2017-05-02   
    #
    ############################################################################

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	
	// load user settings
	foreach($_GET as $key=>$value){
		$parameters[trim($key)] = trim(urldecode($value));
	}
	
	// override main settings solar sensor settings
	if($parameters["solarData"]==1){
		$solarSensor = true;
	}
	else{
		$solarSensor = false;
	}
	
	echo "Importing data from CSV/text file...<br><br>";
	echo "<span style='color:red'>Errors will be highlighted in red...</span><br><br>";
	
	// try loading file
	echo "Loading CSV/text file from <i>".$parameters['path']."</i><br><br>";
    $log = file($parameters['path']);
	if($log != ""){
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
	echo "The imported file has ".count($log)." data sets.<br>";
	$counter = count($log);
	
	// parse
	echo "<br>";
	echo "Import type: ".$parameters['importType'];
	echo "<br>";
	echo "Starting to parse data.<br><hr>";
	
	$errors = array();
	
	for($n=$header;$n<$counter;$n++){
		$invalidRecord = false;
		$sqlParameters = array();
		$sqlValues = array();
		$row = $log[$n];
		$row = trim($row);
		
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

		for($n2=0;$n2<count($fields);$n2++){
			$fields[$n2] = trim($fields[$n2]);
			if($parameters['separator']=="comma"){
				$fields[$n2] = str_replace(",",".",$fields[$n2]);
			}
		}
		$dateTimeType = $parameters['dateformat'];
		if($dateTimeType=="single"){
			$dateTimeRaw = $fields[$parameters['dtSingleField']];
			$dateTime = date_create_from_format($parameters['dtSingleFormat'],$dateTimeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
		}
		if($dateTimeType=="double"){
			$dateRaw = $fields[$parameters['dtDoubleDateField']];
			$timeRaw = $fields[$parameters['dtDoubleTimeField']];
			$dateTime = date_create_from_format($parameters['dtDoubleDateFormat']." ".$parameters['dtDoubleTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
		}
		if($dateTimeType=="separateDate"){
			$timeRaw = $fields[$parameters['dtSeparateDateTimeField']];
			$Y = $fields[$parameters['dtSeparateDateYearField']];
			$m = $fields[$parameters['dtSeparateDateMonthField']];
			$d = $fields[$parameters['dtSeparateDateDayField']];
			$dateRaw = $Y."-".$m."-".$d;
			$dateTime = date_create_from_format("Y-m-d ".$parameters['dtSeparateDateTimeFormat'],$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
		}
		if($dateTimeType=="separateTime"){
			$dateRaw = $fields[$parameters['dtSeparateTimeDateField']];
			$HH = $fields[$parameters['dtSeparateTimeHourField']];
			$i = $fields[$parameters['dtSeparateTimeMinuteField']];
			$timeRaw = $HH.":".$i;
			$dateTime = date_create_from_format($parameters['dtSeparateTimeDateFormat']." H:i",$dateRaw." ".$timeRaw);
			$dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
		}
		if($dateTimeType=="separate"){
			$Y = $fields[$parameters['dtSeparateYearField']];
			$m = $fields[$parameters['dtSeparateMonthField']];
			$d = $fields[$parameters['dtSeparateDayField']];
			$HH = $fields[$parameters['dtSeparateHourField']];
			$i = $fields[$parameters['dtSeparateMinuteField']];
			if($HH<10){
				$HH = $HH * 1;
				$HH = "0".$HH;
			}
			if($i<10){
				$i = $i * 1;
				$i = "0".$i;
			}
			$dateTimeFormatted = $Y."-".$m."-".$d." ".$HH.":".$i;
		}

		$Traw = $fields[$parameters['fieldT']];
		if($Traw==="" || $Traw===null || !is_numeric($Traw)){
			$nullT = true;
		}
		else{
			$nullT = false;
		}
		$T = convertor($Traw,$parameters['unitsT'],$dataTempUnits);
		$T = number_format($T,1,".","");

		$H = $fields[$parameters['fieldH']];
		if($H==="" || $H===null || !is_numeric($H)){
			$nullH = true;
		}
		else{
			$nullH = false;
		}
	
		$Praw = $fields[$parameters['fieldP']];
		if($Praw==="" || $Praw===null || !is_numeric($Praw)){
			$nullP = true;
		}
		else{
			$nullP = false;
		}
		$P = convertor($Praw,$parameters['unitsP'],$dataPressUnits);
		$P = number_format($P,2,".","");
		
		$Wraw = $fields[$parameters['fieldW']];
		if($Wraw==="" || $Wraw===null || !is_numeric($Wraw)){
			$nullW = true;
		}
		else{
			$nullW = false;
		}
		$W = convertor($Wraw,$parameters['unitsW'],$dataWindUnits);
		$W = number_format($W,1,".","");

		$Graw = $fields[$parameters['fieldG']];
		if($Graw==="" || $Graw===null || !is_numeric($Graw)){
			$nullG = true;
		}
		else{
			$nullG = false;
		}
		$G = convertor($Graw,$parameters['unitsG'],$dataWindUnits);
		$G = number_format($G,1,".","");

		$Rraw = $fields[$parameters['fieldR']];
		if($Rraw==="" || $Rraw===null || !is_numeric($Rraw)){
			$nullR = true;
		}
		else{
			$nullR = false;
		}
		$R = convertor($Rraw,$parameters['unitsR'],$dataRainUnits);
		$R = number_format($R,2,".","");

		$RRraw = $fields[$parameters['fieldRR']];
		if($RRraw==="" || $RRraw===null || !is_numeric($RRraw)){
			$nullRR = true;
		}
		else{
			$nullRR = false;
		}
		if($parameters['unitsR']=="mmmin"){
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "mm";
		}
		if($parameters['unitsR']=="inmin"){
			$RRraw = $RRraw * 60;
			$parameters['unitsR'] = "in";
		}
		if($parameters['unitsR']=="mm10min"){
			$RRraw = $RRraw * 6;
			$parameters['unitsR'] = "mm";
		}
		if($parameters['unitsR']=="in10min"){
			$RRraw = $RRraw * 6;
			$parameters['unitsR'] = "in";
		}
		$RR = convertor($RRraw,$parameters['unitsR'],$dataRainUnits);
		$RR = number_format($RR,2,".","");
		
		$B = $fields[$parameters['fieldB']];
		if($B==="" || $B===null || !is_numeric($B)){
			$nullB = true;
		}
		else{
			$nullB = false;
		}
		
		if(!$solarSensor){
			$nullS = true;
		}
		else{
			$S = $fields[$parameters['fieldS']];
			if($S==="" || $S===null || !is_numeric($S)){
				$nullS = true;
			}
			else{
				$nullS = false;
			}
		}
		
		
		// validate
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
		$validS = false;
		
		// checking date/time
		$minimumDate = strtotime("1980-01-01");
		$checkedDate = strtotime($dateTimeFormatted);
		if($checkedDate>$minimumDate && $checkedDate<time()){
			$validDate = true;
			$sqlParameters[] = "DateTime";
			$sqlValues[] = "'".$dateTimeFormatted."'";
		}
		else{
			$invalidRecord = true;
		}
		
		// temperature
		if($T<=$limitTempMax && $T>=$limitTempMin && !$nullT){
			$validT = true;
			$sqlParameters[] = "T";
			$sqlParameters[] = "Tmax";
			$sqlParameters[] = "Tmin";
			$sqlValues[] = $T;
			$sqlValues[] = $T;
			$sqlValues[] = $T;
		}
		else{
			$sqlParameters[] = "T";
			$sqlParameters[] = "Tmax";
			$sqlParameters[] = "Tmin";
			$sqlValues[] = "null";
			$sqlValues[] = "null";
			$sqlValues[] = "null";
		}
		
		// humidity
		if($H<=$limitHumidityMax && $H>=$limitHumidityMin && !$nullH){
			$validH = true;
			$sqlParameters[] = "H";
			$sqlValues[] = $H;
		}
		else{
			$sqlParameters[] = "H";
			$sqlValues[] = "null";
		}
		
		// pressure
		if($P<=$limitPressureMax && $P>=$limitPressureMin && !$nullP){
			$validP = true;
			$sqlParameters[] = "P";
			$sqlValues[] = $P;
		}
		else{
			$sqlParameters[] = "P";
			$sqlValues[] = "null";
		}
		
		// wind speed
		if($W<=$limitWindMax && $W>=$limitWindMin && !$nullW){
			$validW = true;
			$sqlParameters[] = "W";
			$sqlValues[] = $W;
		}
		else{
			$sqlParameters[] = "W";
			$sqlValues[] = "null";
		}
		
		// wind gust
		if($G<=$limitWindMax && $G>=$limitWindMin && !$nullG){
			$validG = true;
			$sqlParameters[] = "G";
			$sqlValues[] = $G;
		}
		else{
			$sqlParameters[] = "G";
			$sqlValues[] = "null";
		}
		
		// precipitation
		if($R<=$limitRainMax && $R>=$limitRainMin && !$nullR){
			$validR = true;
			$sqlParameters[] = "R";
			$sqlValues[] = $R;
		}
		else{
			$sqlParameters[] = "R";
			$sqlValues[] = "null";
		}
		
		// rain rate
		if($RR<=$limitRainRateMax && $RR>=$limitRainRateMin && !$nullRR){
			$validRR = true;
			$sqlParameters[] = "RR";
			$sqlValues[] = $RR;
		}
		else{
			$sqlParameters[] = "RR";
			$sqlValues[] = "null";
		}
		
		// wind direction
		if($B<=$limitBearingMax && $B>=$limitBearingMin && !$nullB){
			$validB = true;
			$sqlParameters[] = "B";
			$sqlValues[] = $B;
		}
		else{
			$sqlParameters[] = "B";
			$sqlValues[] = "null";
		}
		
		if($solarSensor){
			// solar radiation
			if($S<=$limitSolarMax && $S>=$limitSolarMin && !$nullS){
				$validS = true;
				$sqlParameters[] = "S";
				$sqlValues[] = $S;
			}
			else{
				$sqlParameters[] = "S";
				$sqlValues[] = "null";
			}
		}
		else{
			$sqlParameters[] = "S";
			$sqlValues[] = "null";
		}
		
		// calculate dew point and apparent temperature
		if($validT && $validH && !$nullT && !$nullH){
			$T_C = convertor($T,$dataTempUnits,"C");
			$D = dewpoint($T_C,$H);
			// convert back
			$D = convertor($D,"C",$dataTempUnits);
			$validD = true;
			$sqlParameters[] = "D";
			$sqlValues[] = $D;
		}
		else{
			$sqlParameters[] = "D";
			$sqlValues[] = "null";
		}
		
		if($validT && $validH && $validW && !$nullT && !$nullH && !$nullW){
			$T_C = convertor($T,$dataTempUnits,"C");
			$W_MS = convertor($W,$dataWindUnits,"ms");
			$A = apparent($T_C,$H,$W_MS);
			$A = convertor($A,"C",$dataTempUnits);
			$validA = true;
			$sqlParameters[] = "A";
			$sqlValues[] = $A;
		}
		else{
			$sqlParameters[] = "A";
			$sqlValues[] = "null";
		}


		if(!$validDate){
			echo "<span style='color:red'>Record number ".$n." has incorrect date/time format, this data row is completely ignored...</span style='color:red'><br>";
			echo "Date parsed as: ".$dateTimeFormatted."<br>Skipping...<hr>";
		}
		else{

			$query = "INSERT INTO alldata (".implode(',',$sqlParameters).") values (".implode(',',$sqlValues).")";
			
			if($invalidRecord){
				echo "<br><span style='color:red'>The following record has some errors - some values are missing or out of the limits defined in Main settings. Not all parameters were imported for this row. The query to database for this record was:</span style='color:red'><br>".$query."<hr>";
			}
			else{
				if($parameters['importType']=="skip"){
					echo "All ok, imported (skipped if exists): ".$dateTimeFormatted."<br>".$query."<hr>";
					mysqli_query($con,$query);
				}
				if($parameters['importType']=="overwrite"){
					echo "All ok, imported (overwritten if exists): ".$dateTimeFormatted."<br>".$query."<hr>";
					$queryDel = "DELETE FROM alldata WHERE DateTime='".$dateTimeFormatted."'";
					mysqli_query($con,$queryDel);
					mysqli_query($con,$query);
				}
			}
		}
	}
	//echo "<br><br>--- END ----";
	
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