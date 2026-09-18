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
    #   Update
    #
    #   v2.0    2017-04-20  Modified for handling by api.php
    #   v2.1    2017-04-20  Modified for handling by api.php
    #           
    #
    ############################################################################

    $base = "../";

    // load main info
    require($base."config.php");

    // load dependencies
    require($base."scripts/functions.php");

    // check acces authorization
    if(isset($_GET['PASS'])){
        $password = $_GET['PASS'];
    }
    else if(isset($_GET['password'])){ 
        $password = $_GET['password'];
    }
    else{
        die("No password provided");
    }
    
    // check if password is correct
    if($password!=$updatePassword){
        if($password==$adminPassword){ // if admin password provided accept, but notify
            echo "Authorized via admin password";
        }
        else{
            die("Unauthorized");
        }
    }

    ########### PARSE DATA TO API FORMAT ############
    
    $updateLog = array();
    $rawUpdate = array();
    
    $updateLog['info'][] = "Authorized access.";
    $updateLog['info'][] = "Current date/time: ".date("Y-m-d H:i:s");
    $updateLog['info'][] = "logdata from update/update.php";
    
    // load settings
    if(!file_exists($base."update/updateSettings.php")){
        $updateLog['error'][] = "The update settings file does not exist";
        generateUpdateLog();
        die("The update settings file does not exist! Please go to your control panel and set up the update process.");
    }
    else{
        $originalPath = $path;
        include($base."update/updateSettings.php");
        $filePath = $path;
        $path = $originalPath;
    }

    ########## CUMULUS #############
    if($type=="cumulus"){
        $updateLog['info'][] = "Parsing cumulus data.";
        // http://wiki.sandaysoft.com/a/Realtime.txt
        $updateLog['info'][] = "Parsing Cumulus realtime.txt to match API format";
        $updateLog['info'][] = "Loading realtime.txt from ".$filePath;
        $fileRaw = file_get_contents($filePath);
        if($fileRaw==""){
            // try cURL 
            $updateLog['info'][] = "No data loaded, trying alternative method";
            $fileRaw = curlMain($filePath,5);
        }
        if($fileRaw==""){
            $updateLog['error'][] = "Unable to load data for realtime.txt. Either file does not exist, or the permissions are not correct";
            generateUpdateLog();
            die("Unable to load data for realtime.txt");
        }
        // clean up 
        $fileRaw = preg_replace('/^[ \t]*[\r\n]+/m', '', $fileRaw);
        $updateLog['info'][] = "Loaded realtime.txt data:\r\n".$fileRaw."\r\n";
        $fileSplit = explode(" ",$fileRaw);
        
        // realtime should be 58 fields long, check that it is at least 50 
        $updateLog['info'][] = "Number of fields in text file: ".count($fileSplit);
        if(count($fileSplit) < 50){ // at least 50 fields should be in the file
            $updateLog['error'][] = "Realtime.txt is incomplete. Number of fields: ".count($fileSplit);
            die("Realtime.txt is incomplete. Number of fields: ".count($fileSplit));
        }

        array_map('trim', $fileSplit);

        $rawUpdate['SW'] = "Cumulus";

        // Date has inconsistent separator, find it 
        $fileRawDate = $fileSplit[0];
        $fileDate['D'] = substr($fileRawDate,0,2); // year in Cumulus given as two figures
        $fileDate['M'] = substr($fileRawDate,3,2);
        $fileDate['Y'] = substr($fileRawDate,6,2) + 2000;

        // Time
        $fileTime = $fileSplit[1];

        $rawUpdate['U'] = strtotime($fileDate['Y']."-".$fileDate['M']."-".$fileDate['D']." ".$fileTime);

        // Cumulus units not standardized grrrr.... 
        $realtimeUnits['T'] = $fileSplit[14];
        $realtimeUnits['W'] = $fileSplit[13];
        $realtimeUnits['R'] = $fileSplit[16];
        $realtimeUnits['P'] = $fileSplit[15];

        // outside temperature - default API units: deg C
        if($realtimeUnits['T']=="C"){
            $rawUpdate['T'] = $fileSplit[2];
        }
        else if($realtimeUnits['T']=="F"){
            $rawUpdate['T'] = convertor($fileSplit[2],"F","C");
        }
        else{
            $rawUpdate['T'] = $fileSplit[2];
        }

        // relative humidity
        $rawUpdate['H'] = $fileSplit[3];

        // average wind speed - default API units: km/h 
        if($realtimeUnits['W']=="km/h"){
            $rawUpdate['W'] = $fileSplit[5];
        }
        else if($realtimeUnits['W']=="m/s"){
            $rawUpdate['W'] = convertor($fileSplit[5],"ms","kmh");
        }
        else if($realtimeUnits['W']=="mph"){
            $rawUpdate['W'] = convertor($fileSplit[5],"mph","kmh");
        }
        else if($realtimeUnits['W']=="kts"){
            $rawUpdate['W'] = convertor($fileSplit[5],"kt","kmh");
        }
        else{
             $rawUpdate['W'] = $fileSplit[5];
        }

        // wind gust - default API units: km/h 
        if($realtimeUnits['W']=="km/h"){
            $rawUpdate['G'] = $fileSplit[40];
        }
        else if($realtimeUnits['W']=="m/s"){
            $rawUpdate['G'] = convertor($fileSplit[40],"ms","kmh");
        }
        else if($realtimeUnits['W']=="mph"){
            $rawUpdate['G'] = convertor($fileSplit[40],"mph","kmh");
        }
        else if($realtimeUnits['W']=="kts"){
            $rawUpdate['G'] = convertor($fileSplit[40],"kt","kmh");
        }
        else{
             $rawUpdate['G'] = $fileSplit[40];
        }

        // wind direction 
        $rawUpdate['B'] = $fileSplit[7];
        
        // pressure - default API units: hPa 
        if($realtimeUnits['P']=="hPa"){
            $rawUpdate['P'] = $fileSplit[10];
        }
        else if($realtimeUnits['P']=="mb"){
            $rawUpdate['P'] = $fileSplit[10];
        }
        else if($realtimeUnits['P']=="in"){
            $rawUpdate['P'] = convertor($fileSplit[10],"inhg","hpa");
        }
        else{
            $rawUpdate['P'] = $fileSplit[10];
        }

        // precipitation - default API units: mm 
        if($realtimeUnits['R']=="mm"){
            $rawUpdate['R'] = $fileSplit[9];
        }
        else if($realtimeUnits['R']=="in"){
            $rawUpdate['R'] = convertor($fileSplit[9],"in","mm");
        }
        else{
            $rawUpdate['R'] = $fileSplit[9];
        }

        // rain rate - default API units: mm 
        if($realtimeUnits['R']=="mm"){
            $rawUpdate['RR'] = $fileSplit[8];
        }
        else if($realtimeUnits['R']=="in"){
            $rawUpdate['RR'] = convertor($fileSplit[8],"in","mm");
        }
        else{
            $rawUpdate['RR'] = $fileSplit[8];
        }

        // indoor temperature - default API units: deg C
        if($realtimeUnits['T']=="C"){
            $rawUpdate['TIN'] = $fileSplit[22];
        }
        else if($realtimeUnits['T']=="F"){
            $rawUpdate['TIN'] = convertor($fileSplit[22],"F","C");
        }
        else{
            $rawUpdate['TIN'] = $fileSplit[22];
        }

        // indoor humidity
        $rawUpdate['HIN'] = $fileSplit[23];

        // UV
        $rawUpdate['UV'] = $fileSplit[43];

        // solar radiation
        $rawUpdate['S'] = $fileSplit[45];

        // sun shine 
        $rawUpdate['SS'] = $fileSplit[55];

    } // end Cumulus

    ########### WU ############
    elseif($type=="wu"){
        $updateLog['info'][] = "Parsing WU data.";
        $wuID = strtoupper(trim($WUID));
        $updateLog['info'][] = "Parsing WU input to match API format";
        $updateLog['info'][] = "Specified WU ID: ".$wuID;
        $wuURL = "https://api.weather.com/v2/pws/observations/current?stationId=".$wuID."&format=json&units=m&apiKey=".$wuStationAPI."&numericPrecision=decimal";
        $updateLog['info'][] = "Loading data from ".$wuURL;

        $fileRaw = file_get_contents($wuURL);
        $logRaw = $fileRaw;
        $updateLog['info'][] = "Data received from WU";
        if($fileRaw==""){
            // try cURL 
            $updateLog['info'][] = "No data loaded, trying alternative method";
            $fileRaw = curlMain($wuURL,5);
        }
        if($fileRaw==""){
            $updateLog['error'][] = "Unable to load data from WU.";
            generateUpdateLog();
            die("Unable to load data from WU");
        }
        $fileRaw = json_decode($fileRaw, true);
        // print_r($xmlWU);
        $updateLog['info'][] = "JSON data received from WU";
        // get latest observation
        $rawUpdate['SW'] = "WU";
        $updateLog['info'][] = "Loaded data items from WU = ".$logRaw;
        $rawUpdate['U'] = $fileRaw['observations'][0]['epoch'];
        $rawUpdate['T'] = $fileRaw['observations'][0]['metric']['temp'];
        $rawUpdate['H'] = $fileRaw['observations'][0]['humidity'];
        $rawUpdate['P'] = $fileRaw['observations'][0]['metric']['pressure'];
        $rawUpdate['W'] = $fileRaw['observations'][0]['metric']['windSpeed'];
        $rawUpdate['G'] = $fileRaw['observations'][0]['metric']['windGust'];
        $rawUpdate['R'] = $fileRaw['observations'][0]['metric']['precipTotal'];
        $rawUpdate['RR'] = $fileRaw['observations'][0]['metric']['precipRate'];
        $rawUpdate['B'] = $fileRaw['observations'][0]['winddir'];
        $rawUpdate['S'] = $fileRaw['observations'][0]['solarRadiation'];
        
    } // end WU

    ########### CUSTOM ############
    elseif($type == "custom"){
        $updateLog['info'][] = "Parsing custom data.";
        $rawUpdate['SW'] = "Custom";
        $updateLog['info'][] = "Parsing custom file to match API format";
        $updateLog['info'][] = "Loading realtime data from ".$filePath;
        $fileRaw = file_get_contents($filePath);
        if($fileRaw==""){
            // try cURL 
            $updateLog['info'][] = "No data loaded, trying alternative method";
            $fileRaw = curlMain($filePath,5);
        }
        if($fileRaw==""){
            $updateLog['error'][] = "Unable to load data for custom text file. Either file does not exist, or the permissions are not correct";
            generateUpdateLog();
            die("Unable to load data for custom text file");
        }

        $fileRaw = preg_replace('/^[ \t]*[\r\n]+/m', '', $fileRaw);
        $updateLog['info'][] = "Loaded custom text file data: ".$fileRaw;

        // delimiter
        if($delimiter=="semicolon"){
            $fileSplit = explode(";",$fileRaw);
        }
        if($delimiter=="comma"){
            $fileSplit = explode(",",$fileRaw);
        }
        if($delimiter=="space"){
            $fileSplit = explode(" ",$fileRaw);
        }
        if($delimiter=="tab"){
            $fileSplit = preg_split('/\s+/', $fileRaw);
        }
        if($delimiter=="colon"){
            $fileSplit = explode(":",$fileRaw);
        }
        if($delimiter=="vertical"){
            $fileSplit = explode("|",$fileRaw);
        }
        
        $updateLog['info'][] = "Number of fields in text file: ".count($fileSplit);
        if(count($fileSplit) < 5){ // at least 5 fields should be in the file
            $updateLog['error'][] = "Custom text file is incomplete. Number of fields: ".count($fileSplit);
            generateUpdateLog();
            die("Custom text file is incomplete. Number of fields: ".count($fileSplit));
        }

        array_map('trim', $fileSplit);

        // replace separator "," by "." used in PHP
        for($n=0;$n<count($fileSplit);$n++){
            if($separator=="comma"){
                $fileSplit[$n] = str_replace(",",".",$fileSplit[$n]);
            }
        }

        // Date and time
        $dateTimeType = $dateformat;
        if($dateTimeType=="single"){
            $updateLog['info'][] = "Date and time in a single field";
            $dateTimeRaw = $fileSplit[$dtSingleField];
            $dateTime = date_create_from_format($dtSingleFormat,$dateTimeRaw);
            $dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
            $rawUpdate['U'] = date_format($dateTime, "U");
            $updateLog['info'][] = "dateTimeRaw=".$dateTimeRaw.", dateTimeFormatted=".$dateTimeFormatted.", rawUpdate['U']=|".$rawUpdate['U'];
            $updateLog['info'][] = "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted;
        }
        if($dateTimeType=="double"){
            $updateLog['info'][] = "Date in one field, time in a different one";
            $dateRaw = $fileSplit[$dtDoubleDateField];
            $timeRaw = $fileSplit[$dtDoubleTimeField];
            $dateTime = date_create_from_format($dtDoubleDateFormat." ".$dtDoubleTimeFormat,$dateRaw." ".$timeRaw);
            $dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
            $rawUpdate['U'] = date_format($dateTime, "U");
            $updateLog['info'][] = "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted;
        }
        if($dateTimeType=="separateDate"){
            $updateLog['info'][] = "Date in separate fields, time in one field";
            $timeRaw = $fileSplit[$dtSeparateDateTimeField];
            $Y = $fileSplit[$dtSeparateDateYearField];
            $m = $fileSplit[$dtSeparateDateMonthField];
            $d = $fileSplit[$dtSeparateDateDayField];
            $dateRaw = $Y."-".$m."-".$d;
            $dateTime = date_create_from_format("Y-m-d ".$dtSeparateDateTimeFormat,$dateRaw." ".$timeRaw);
            $dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
            $rawUpdate['U'] = date_format($dateTime, "U");
            $updateLog['info'][] = "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted;
        }
        if($dateTimeType=="separateTime"){
            $updateLog['info'][] = "Time in separate fields, date in one field";
            $dateRaw = $fileSplit[$dtSeparateTimeDateField];
            $HH = $fileSplit[$dtSeparateTimeHourField];
            $i = $fileSplit[$dtSeparateTimeMinuteField];
            $timeRaw = $HH.":".$i;
            $dateTime = date_create_from_format($dtSeparateTimeDateFormat." H:i",$dateRaw." ".$timeRaw);
            $dateTimeFormatted = date_format($dateTime, "Y-m-d H:i");
            $rawUpdate['U'] = strtotime($dateTimeFormatted);
            $updateLog['info'][] = "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted;
        }
        if($dateTimeType=="separate"){
            $updateLog['info'][] = "Date and time parameters all in separate fields";
            $Y = $fileSplit[$dtSeparateYearField];
            $m = $fileSplit[$dtSeparateMonthField];
            $d = $fileSplit[$dtSeparateDayField];
            $HH = $fileSplit[$dtSeparateHourField];
            $i = $fileSplit[$dtSeparateMinuteField];
            if($HH<10){
				$HH = $HH * 1;
				$HH = "0".$HH;
			}
			if($i<10){
				$i = $i * 1;
				$i = "0".$i;
			}
            $dateTimeFormatted = $Y."-".$m."-".$d." ".$HH.":".$i;
            $rawUpdate['U'] = strtotime($Y."-".$m."-".$d." ".$HH.":".$i);
            $updateLog['info'][] = "MySQL accepts date and time in format YYYY-MM-DD HH:MM, the parsed date and time is: ".$dateTimeFormatted;
        }

        // temperature
        $Traw = $fileSplit[$fieldT];
        $rawUpdate['T'] = convertor($Traw,$unitsT,"C");

        // humidity
        $rawUpdate['H'] = $fileSplit[$fieldH];

        // pressure
        $Praw = $fileSplit[$fieldP];
        $rawUpdate['P'] = convertor($Praw,$unitsP,"hpa");

        // wind speed
        $Wraw = $fileSplit[$fieldW];
        $rawUpdate['W'] = convertor($Wraw,$unitsW,"kmh");

        // wind gust
        $Graw = $fileSplit[$fieldG];
        $rawUpdate['G'] = convertor($Graw,$unitsG,"kmh");

        // precipitation
        $Rraw = $fileSplit[$fieldR];
        $rawUpdate['R'] = convertor($Rraw,$unitsR,"mm");

        // rain rate
        $RRraw = $fileSplit[$fieldRR];
        if($unitsR=="mmmin"){
            $RRraw = $RRraw * 60;
            $unitsR = "mm";
        }
        if($unitsR=="inmin"){
            $RRraw = $RRraw * 60;
            $unitsR = "in";
        }
        $rawUpdate['RR'] = convertor($RRraw,$unitsR,"mm");

        // wind direction
        $rawUpdate['B'] = $fileSplit[$fieldB];

        // solar radiation
        $rawUpdate['S'] = $fileSplit[$fieldS];

    }
    else{
        // no valid type
        $updateLog['error'][] = "apiUpdate: no valid type given in updateSettings.php.";
        generateUpdateLog();
        die("update: no valid type given in updateSettings.phpt");
    }
    ############################################################################
    // end of Update section
    ############################################################################
    
    generateUpdateLog();
    
    // call api.php
    $apiUpdate = $type;
    include_once($base."api.php");
    
    function generateUpdateLog(){
        global $updateLog;
        global $base;
        // create log and exit update script 
        $updateLog['info'][] = "Generating log file cache/updateLog.txt";
        $updateTxt = "";
        foreach($updateLog['info'] as $info){
            $updateTxt .= $info."\n\r";
        }
        $updateTxt .= "\n\rERRORS:\n\r";
        if(isset($updateLog['error'])){
            foreach($updateLog['error'] as $error){
                $updateTxt .= $error."\n\r";
            }
        }
        file_put_contents($base."cache/updateLog.txt",$updateTxt);
    }

