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

    // load api settings
    $extraAPIRaw = json_decode(file_get_contents("../update/apiSettings.txt"),true);
    foreach($extraAPIRaw as $extraParam=>$extraValue){
        if($extraValue==1){
            $extraSensors[] = $extraParam;
        }
    }
	
	echo "Testing CSV/text file...<br><br>";
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
    $counter = count($log);
	echo "The tested file has ".$counter." data sets. This import test will load only the first line.<br>";
	

	if($header==0){
		$limit = 1;
	}
	else{
		$limit = 2;
	}
	
	// parse
	echo "<br>";
	echo "<hr><br>";
	echo "Starting to parse data.<br><br>";
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
		if($parameters['separator']=="comma"){
			echo "Decimal separator is a comma, replacing with a period for correct parsing by PHP.<br>";
		}
		else{
			echo "Decimal separator is a period, no need to do anything, can be parsed by PHP.<br>";
		}
			
		for($n2=0;$n2<count($fields);$n2++){
			$fields[$n2] = trim($fields[$n2]);
			if($parameters['separator']=="comma"){
				$fields[$n2] = str_replace(",",".",$fields[$n2]);
			}
		}
        $nFields = count($fields);
        echo "The data row contains ".$nFields." fields.<br>";
		// load date and time
		echo "<br>Parsing date and time...";
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
		echo "<br>Parsing variables...<br>";
		echo "<br>";
		
		foreach($extraSensors as $extraSensor){

            if($extraSensor=="TIN"){
                $sensor['name'] = "Indoor temperature";
                $sensor['dbUnits'] = "C";
                $sensor['limits'] = array(1,50);
                $sensor['decimals'] = 1;
            }
            if($extraSensor=="HIN"){
                $sensor['name'] = "Indoor humidity";
                $sensor['dbUnits'] = "%";
                $sensor['limits'] = array(0.01,100);
                $sensor['decimals'] = 1;
            }
            if($extraSensor=="UV"){
                $sensor['name'] = "UV";
                $sensor['dbUnits'] = "-";
                $sensor['limits'] = array(0,40);
                $sensor['decimals'] = 1;
            }
            if($extraSensor=="SN"){
                $sensor['name'] = "Snowfall";
                $sensor['dbUnits'] = "mm";
                $sensor['limits'] = array(0,5000);
                $sensor['decimals'] = 0;
            }
            if($extraSensor=="SD"){
                $sensor['name'] = "Snow depth";
                $sensor['dbUnits'] = "mm";
                $sensor['limits'] = array(0,10000);
                $sensor['decimals'] = 0;
            }
            if($extraSensor=="NL"){
                $sensor['name'] = "Noise level";
                $sensor['dbUnits'] = "dB";
                $sensor['limits'] = array(5,200);
                $sensor['decimals'] = 0;
            }
            if($extraSensor=="L"){
                $sensor['name'] = "Lightning";
                $sensor['dbUnits'] = "-";
                $sensor['limits'] = array(0,1000);
                $sensor['decimals'] = 0;
            }
            if($extraSensor=="SS"){
                $sensor['name'] = "Sunshine";
                $sensor['dbUnits'] = "h";
                $sensor['limits'] = array(0,24);
                $sensor['decimals'] = 1;
            }
            for($g=1;$g<=4;$g++){
                if($extraSensor == "T".$g){
                    $sensor['name'] = "extra temperature sensor ".$g;
                    $sensor['limits'] = array(-60,60);
                    $sensor['dbUnits'] = "C";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "H".$g){
                    $sensor['name'] = "extra humidity sensor ".$g;
                    $sensor['limits'] = array(0.01,100);
                    $sensor['dbUnits'] = "%";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "TS".$g){
                    $sensor['name'] = "soil temperature sensor ".$g;
                    $sensor['limits'] = array(-60,80);
                    $sensor['dbUnits'] = "C";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "SM".$g){
                    $sensor['name'] = "soil moisture sensor ".$g;
                    $sensor['limits'] = array(0.01,100);
                    $sensor['dbUnits'] = "-";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "LT".$g){
                    $sensor['name'] = "leaf temperature sensor ".$g;
                    $sensor['limits'] = array(-60,80);
                    $sensor['dbUnits'] = "C";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "LW".$g){
                    $sensor['name'] = "leaf wetness sensor ".$g;
                    $sensor['limits'] = array(0,15);
                    $sensor['dbUnits'] = "-";
                    $sensor['decimals'] = 1;
                }
                if($extraSensor == "CO2_".$g){
                    $sensor['name'] = "CO2 sensor ".$g;
                    $sensor['limits'] = array(300,600);
                    $sensor['dbUnits'] = "ppm";
                    $sensor['decimals'] = 0;
                }
                if($extraSensor == "CO_".$g){
                    $sensor['name'] = "CO sensor ".$g;
                    $sensor['limits'] = array(0.1,50);
                    $sensor['dbUnits'] = "ppm";
                    $sensor['decimals'] = 0;
                }
                if($extraSensor == "NO2_".$g){
                    $sensor['name'] = "NO2 sensor ".$g;
                    $sensor['limits'] = array(0,10);
                    $thisSensorUnits = "ppm";
                    $sensor['decimals'] = 0;
                }
                if($extraSensor == "SO2_".$g){
                    $sensor['name'] = "SO2 sensor ".$g;
                    $sensor['limits'] = array(0,1000);
                    $sensor['dbUnits'] = "ppb";
                    $sensor['decimals'] = 0;
                }
                if($extraSensor == "O3_".$g){
                    $sensor['name'] = "O3 sensor ".$g;
                    $sensor['limits'] = array(0,100);
                    $sensor['dbUnits'] = "ppb";
                    $sensor['decimals'] = 0;
                }
                if($extraSensor == "PP".$g){
                    $sensor['name'] = "particulate pollution ".$g;
                    $sensor['limits'] = array(0,1000);
                    $sensor['dbUnits'] = "ug/m3";
                    $sensor['decimals'] = 0;
                }
            }
            echo $sensor['name']."<br>";
            $rawValue = $fields[$parameters['field'.$extraSensor]];
            echo "Raw value: ".$rawValue."<br>";
            echo "Database standardized units: ".$sensor['dbUnits']."<br>";
            echo "File units: ".$parameters['units'.$extraSensor]."<br>";
            if($sensor['dbUnits'] == $parameters['units'.$extraSensor]){
                echo "Same units, no need to do any conversion.<br>";
                $val = $rawValue;
            }
            else{
                echo "Converting...<br>";
                $val = convertor($rawValue,$parameters['units'.$extraSensor],$sensor['dbUnits']);
            }
            $val = number_format($val,$sensor['decimals'],".","");
            echo "Final value: ".$val." ".$sensor['dbUnits']."<br>";
            echo "Checking limits...<br>";
            $validity = checkValidity($val,$sensor['limits']);
            if($validity){
                echo "Value within acceptable range...<br>";
                $extraQueryParams[] = $extraSensor;
                $extraQueryValues[] = number_format($val, $sensor['decimals'], ".", "");
                echo "Added to query<br><br>";
            }
            else{
                echo "<span style='color:red'>This sensor value is outside acceptable range of values.</span><br>";
            }
        }
		
		echo "<br>Parsing line ".$n." finished.<br>";
		
		
		// validate date
		echo "Checking validity of date...<br>";
		$validDate = false;
		
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
		
		// build query
		if(!$validDate){
			echo "<br><br><span style='color:red'>There is a problem with the date and time, nothing would be imported. Check the date/time format you specified and the actual values in the file.</span><br>";
		}
		else{
            echo "<h2>Summary</h2>";
			echo "The following would be imported to database:<br>";
			echo "<ul>";
			echo "<li>Date/time: ".$dateTimeFormatted."</li>";
			for($i=0;$i<count($extraQueryParams);$i++){
                echo "<li>".$extraQueryParams[$i].": ".$extraQueryValues[$i]."</li>";
            }
		}
		echo "<br><hr><br>";
	}
	
	echo "<br><br>--- END ----";
	function checkValidity($val,$limits){
        if($val >= $limits[0] && $val <= $limits[1] && is_numeric($val)){
            return true;
        }
        else{
            return false;
        }
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
        else if($unit1=="mm" && $unit2=="cm"){
			return $x * 0.1;
		}
        else if($unit1=="cm" && $unit2=="mm"){
			return $x * 10;
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