<?php

    $var = $_GET['var'];

    include("../../../config.php");
    include("../../../scripts/functions.php");
    include("functions.php");

    if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "First you need to go to your admin panel and use the blocks settings section to create the settings file.";
		die;
	}

    $updateType = trim(strtolower($updateType));

    if($updateType=="api"){
        $newData = file_get_contents("../../../meteotemplateLive.txt");
        $newData = json_decode($newData,true);

        if($var=="T" || $var=="A" || $var=="D"){
            $point = convertor($newData[$var],"C",$displayTempUnits);
        }
        else if($var=="W" || $var=="G"){
            $point = convertor($newData[$var],"km/h",$displayWindUnits);
        }
        else if($var=="P"){
            $point = convertor($newData[$var],"hpa",$displayPressUnits);
        }
        else{
            $point = $newData[$var];
        }
		
		// send as UTC 
		$this_tz_str = date_default_timezone_get();
		$this_tz = new DateTimeZone($this_tz_str);
		$now = new DateTime("now", $this_tz);
		$offset = $this_tz->getOffset($now);

		$U = $newData['U'] + $offset;

    }
    if($updateType=="cumulus"){
        $realtime = file_get_contents($updateFile);
		$realtimeData = explode(" ",$realtime);
		$dataTempUnits = $realtimeData[14];
		$unitsW = $realtimeData[13];
		$dataRainUnits = $realtimeData[16];
		$unitsP = $realtimeData[15];
		// convert to Meteotemplate format
		if($unitsW=="m/s"){
			$dataWindUnits = "ms";
		}
		else if($unitsW=="km/h"){
			$dataWindUnits = "kmh";
		}
		else if($unitsW=="kts"){
			$dataWindUnits = "kt";
		}
		else{
			$dataWindUnits = "mph";
		}
		if($unitsP=="mb"){
			$dataPressUnits = "hpa";
		}
		else if($unitsP=="hPa"){
			$dataPressUnits = "hpa";
		}
		else{
			$dataPressUnits = "inhg";
		}

		$current['T'] = number_format(convertT($realtimeData[2]),1,".","");
		$current['H'] = number_format($realtimeData[3],1,".","");
		if($displayPressUnits=="hpa"){
			$current['P'] = number_format(convertP($realtimeData[10]),1,".","");
		}
		else{
			$current['P'] = number_format(convertP($realtimeData[10]),2,".","");
		}
		$current['W'] = number_format(convertW($realtimeData[6]),1,".","");
		$current['G'] = number_format(convertW($realtimeData[40]),1,".","");
		if($displayRainUnits=="mm"){
			$current['R'] = number_format(convertR($realtimeData[9]),1,".","");
		}
		else{
			$current['R'] = number_format(convertR($realtimeData[9]),2,".","");
		}
		$current['D'] = number_format(convertT($realtimeData[4]),1,".","");
		$current['A'] = number_format(convertT($realtimeData[54]),1,".","");
		$current['B'] = $realtimeData[7];
		$current['S'] = $realtimeData[45];
		$current['UV'] = $realtimeData[43];
		$current['Timestamp'] = date($updateTimeFormat,strtotime("2015-1-1 ".$realtimeData[1]));
		$dateSplit = explode("-",$realtimeData[0]);
		if(count($dateSplit)<2){
			$dateSplit = explode("/",$realtimeData[0]);
		}
        if(count($dateSplit)<2){
			$dateSplit = explode(".",$realtimeData[0]);
		}
		$current['DateTime'] = strtotime(($dateSplit[2]+2000)."-".$dateSplit[1]."-".$dateSplit[0]." ".$realtimeData[1]);

        $point = $current[$var];
        $U = $current['DateTime']*1000;
    }

    $output['point'] = number_format($point,$dp,".","");
	$output['U'] = $U * 1000;
    echo json_encode($output,JSON_NUMERIC_CHECK);