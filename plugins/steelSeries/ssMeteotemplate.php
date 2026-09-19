<?php
    include("../../config.php");
    include("../../scripts/functions.php");

    if(!file_exists("settings.php")){
		echo "Missing settings file, create one using the plugin setup.";
		die();
	}
	else{
		include("settings.php");
	}

    // error_reporting(E_ALL);

	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	$directions = array(0,11.25,33.75,56.25,78.75,101.25,123.75,146.25,168.75,191.25,213.75,236.75,258.75,281.25,303.75,326.25,348.75,361);
	$bearingNames = array(lang("directionN",""),lang("directionNNE",""),lang("directionNE",""),lang("directionENE",""),lang("directionE",""),lang("directionESE",""),lang("directionSE",""),lang("directionSSE",""),lang("directionS",""),lang("directionSSW",""),lang("directionSW",""),lang("directionWSW",""),lang("directionW",""),lang("directionWNW",""),lang("directionNW",""),lang("directionNNW",""),"N2");

    if($prefferedTime=="12h"){
		$updateTimeFormat = "g:i:s A";
	}
	else{
		$updateTimeFormat = "G:i:s";
	}

    if($displayPressUnits=="mmhg"){
        $displayPressUnits = "hpa";
    }

    $apiData = file_get_contents("../../meteotemplateLive.txt");
    $apiData = json_decode($apiData,true);
    $current['T'] = number_format(convertor($apiData['T'],"C",$displayTempUnits),1,".","");
    $current['H'] = number_format($apiData['H'],1,".","");
    if($displayPressUnits=="hpa"){
        $current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),1,".","");
    }
    else{
        $current['P'] = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),2,".","");
    }
    $current['W'] = number_format(convertor($apiData['W'],"kmh",$displayWindUnits),1,".","");
    $current['G'] = number_format(convertor($apiData['G'],"kmh",$displayWindUnits),1,".","");
    if($displayRainUnits=="mm"){
        $current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),1,".","");
        $current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),1,".","");
    }
    else{
        $current['R'] = number_format(convertor($apiData['R'],"mm",$displayRainUnits),2,".","");
        $current['RR'] = number_format(convertor($apiData['RR'],"mm",$displayRainUnits),2,".","");
    }
    $current['D'] = number_format(convertor($apiData['D'],"C",$displayTempUnits),1,".","");
    $current['A'] = number_format(convertor($apiData['A'],"C",$displayTempUnits),1,".","");
    $current['B'] = $apiData['B'];
    $current['S'] = $apiData['S'];
    if(isset($apiData['UV'])){
        $current['UV'] = $apiData['UV'];
    }
    else{
        $current['UV'] = "";
    }
    $current['Timestamp'] = date($updateTimeFormat,$apiData['U']);
    $current['UNIX'] = $apiData['U'];

	// todays data  - 5min cache
    if(!is_dir("cache")){
		mkdir("cache");
	}

    $yesterdayDate = date("Ymd",strtotime("yesterday"));
    if(file_exists("cache/yesterday.txt")){
        $cachedDay = json_decode(file_get_contents("cache/yesterday.txt"),true);
        $cachedDayDate = $cachedDay['date'];
        if($cachedDayDate!=$yesterdayDate){
            unlink("cache/yesterday.txt");
        }
    }

    if(file_exists("cache/yesterday.txt")){
        $yesterday = json_decode(file_get_contents("cache/yesterday.txt"),true);
    }
    else{
        $result = mysqli_query($con,"
    		SELECT *
    		FROM alldata
    		WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
            ORDER BY DateTime
    	"
    	);
    	$windsYesterday = array();
    	$bearingsYesterday = array();
    	$directionYesterday = array();
    	while($row = mysqli_fetch_array($result)){
            $thisTime = date($timeFormat,strtotime($row['DateTime']));
            // max T
            if(isset($yesterday['maxT'])){
                if($yesterday['maxT']<=($row['Tmax'])){
                    $yesterday['maxT'] = ($row['Tmax']);
                    $yesterday['maxTTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxT'] = ($row['Tmax']);
                $yesterday['maxTTime'] = $thisTime;
            }
            // min T
            if(isset($yesterday['minT'])){
                if($yesterday['minT']>=($row['Tmin'])){
                    $yesterday['minT'] = ($row['Tmin']);
                    $yesterday['minTTime'] = $thisTime;
                }
            }
            else{
                $yesterday['minT'] = ($row['Tmin']);
                $yesterday['minTTime'] = $thisTime;
            }
            // max H
            if(isset($yesterday['maxH'])){
                if($yesterday['maxH']<=($row['H'])){
                    $yesterday['maxH'] = ($row['H']);
                    $yesterday['maxHTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxH'] = ($row['H']);
                $yesterday['maxHTime'] = $thisTime;
            }
            // min H
            if(isset($yesterday['minH'])){
                if($yesterday['minH']>=($row['H'])){
                    $yesterday['minH'] = ($row['H']);
                    $yesterday['minHTime'] = $thisTime;
                }
            }
            else{
                $yesterday['minH'] = ($row['H']);
                $yesterday['minHTime'] = $thisTime;
            }
            // max P
            if(isset($yesterday['maxP'])){
                if($yesterday['maxP']<=($row['P'])){
                    $yesterday['maxP'] = ($row['P']);
                    $yesterday['maxPTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxP'] = ($row['P']);
                $yesterday['maxPTime'] = $thisTime;
            }
            // min P
            if(isset($yesterday['minP'])){
                if($yesterday['minP']>=($row['P'])){
                    $yesterday['minP'] = ($row['P']);
                    $yesterday['minPTime'] = $thisTime;
                }
            }
            else{
                $yesterday['minP'] = ($row['P']);
                $yesterday['minPTime'] = $thisTime;
            }
            // max A
            if(isset($yesterday['maxA'])){
                if($yesterday['maxA']<=($row['A'])){
                    $yesterday['maxA'] = ($row['A']);
                    $yesterday['maxATime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxA'] = ($row['A']);
                $yesterday['maxATime'] = $thisTime;
            }
            // min T
            if(isset($yesterday['minA'])){
                if($yesterday['minA']>=($row['A'])){
                    $yesterday['minA'] = ($row['A']);
                    $yesterday['minATime'] = $thisTime;
                }
            }
            else{
                $yesterday['minA'] = ($row['A']);
                $yesterday['minATime'] = $thisTime;
            }
            // max D
            if(isset($yesterday['maxD'])){
                if($yesterday['maxD']<=($row['D'])){
                    $yesterday['maxD'] = ($row['D']);
                    $yesterday['maxDTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxD'] = ($row['D']);
                $yesterday['maxDTime'] = $thisTime;
            }
            // min D
            if(isset($yesterday['minD'])){
                if($yesterday['minD']>=($row['D'])){
                    $yesterday['minD'] = ($row['D']);
                    $yesterday['minDTime'] = $thisTime;
                }
            }
            else{
                $yesterday['minD'] = ($row['D']);
                $yesterday['minDTime'] = $thisTime;
            }
            // max W
            if(isset($yesterday['maxW'])){
                if($yesterday['maxW']<=($row['W'])){
                    $yesterday['maxW'] = ($row['W']);
                    $yesterday['maxWTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxW'] = ($row['W']);
                $yesterday['maxWTime'] = $thisTime;
            }
            // max G
            if(isset($yesterday['maxG'])){
                if($yesterday['maxG']<=($row['G'])){
                    $yesterday['maxG'] = ($row['G']);
                    $yesterday['maxGTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxG'] = ($row['G']);
                $yesterday['maxGRawTime'] = $thisTime;
            }
            // max RR
            if(isset($yesterday['maxRR'])){
                if($yesterday['maxRR']<=($row['RR'])){
                    $yesterday['maxRR'] = ($row['RR']);
                    $yesterday['maxRRTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxRR'] = ($row['RR']);
                $yesterday['maxRRTime'] = $thisTime;
            }
            // max S
            if(isset($yesterday['maxS'])){
                if($yesterday['maxS']<=($row['S'])){
                    $yesterday['maxS'] = ($row['S']);
                    $yesterday['maxSTime'] = $thisTime;
                }
            }
            else{
                $yesterday['maxS'] = ($row['S']);
                $yesterday['maxSTime'] = $thisTime;
            }

            $bearingsYesterday[] = $row['B'];
            $windsYesterday[] = ($row['W']);

            $currentDirection = windAbb($row['B']);
            $directionYesterday[$currentDirection] = $directionYesterday[$currentDirection] + 1;

            $yesterday['R'] = $row['R'];
        }
        $yesterday['avgW'] = count($windsYesterday)>0 ? array_sum($windsYesterday)/count($windsYesterday) : 0;
        $yesterday['avgB'] = avgWind($bearingsYesterday);
        $yesterday['dominantWind'] = windAbb($yesterday['avgB']);
        $yesterday['direction'] = $directionYesterday;

        $toCache['date'] = $yesterdayDate;
        $toCache['data'] = $yesterday;
        file_put_contents("cache/yesterday.txt",json_encode($toCache));
    }

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
    // use currently delected units
    $yesterday['data']['maxT'] = number_format(convertT($yesterday['data']['maxT']),1,".","");
    $yesterday['data']['minT'] = number_format(convertT($yesterday['data']['minT']),1,".","");
    $yesterday['data']['maxA'] = number_format(convertT($yesterday['data']['maxA']),1,".","");
    $yesterday['data']['minA'] = number_format(convertT($yesterday['data']['minA']),1,".","");
    $yesterday['data']['maxD'] = number_format(convertT($yesterday['data']['maxD']),1,".","");
    $yesterday['data']['minD'] = number_format(convertT($yesterday['data']['minD']),1,".","");
    $yesterday['data']['maxP'] = number_format(convertP($yesterday['data']['maxP']),$decimalsP,".","");
    $yesterday['data']['minP'] = number_format(convertP($yesterday['data']['minP']),$decimalsP,".","");
    $yesterday['data']['avgW'] = number_format(convertW($yesterday['data']['avgW']),1,".","");
    $yesterday['data']['maxW'] = number_format(convertW($yesterday['data']['maxW']),1,".","");
    $yesterday['data']['maxG'] = number_format(convertW($yesterday['data']['maxG']),1,".","");
    $yesterday['data']['R'] = number_format(convertR($yesterday['data']['R']),$decimalsR,".","");
    $yesterday['data']['maxRR'] = number_format(convertR($yesterday['data']['maxRR']),$decimalsR,".","");

    $result = mysqli_query($con,"
		SELECT *
		FROM alldata
		WHERE DATE(DateTime) = CURDATE()
        ORDER BY DateTime
	"
	);
	while($row = mysqli_fetch_array($result)){
        $thisTime = date($timeFormat,strtotime($row['DateTime']));
        // max T
        if(isset($today['maxT'])){
            if($today['maxT']<=convertT($row['Tmax'])){
                $today['maxT'] = convertT($row['Tmax']);
                $today['maxTTime'] = $thisTime;
            }
        }
        else{
            $today['maxT'] = convertT($row['Tmax']);
            $today['maxTTime'] = $thisTime;
        }
        // min T
        if(isset($today['minT'])){
            if($today['minT']>=convertT($row['Tmin'])){
                $today['minT'] = convertT($row['Tmin']);
                $today['minTTime'] = $thisTime;
            }
        }
        else{
            $today['minT'] = convertT($row['Tmin']);
            $today['minTTime'] = $thisTime;
        }
        // max H
        if(isset($today['maxH'])){
            if($today['maxH']<=($row['H'])){
                $today['maxH'] = ($row['H']);
                $today['maxHTime'] = $thisTime;
            }
        }
        else{
            $today['maxH'] = ($row['H']);
            $today['maxHTime'] = $thisTime;
        }
        // min H
        if(isset($today['minH'])){
            if($today['minH']>=($row['H'])){
                $today['minH'] = ($row['H']);
                $today['minHTime'] = $thisTime;
            }
        }
        else{
            $today['minH'] = ($row['H']);
            $today['minHTime'] = $thisTime;
        }
        // max P
        if(isset($today['maxP'])){
            if($today['maxP']<=convertP($row['P'])){
                $today['maxP'] = convertP($row['P']);
                $today['maxPTime'] = $thisTime;
            }
        }
        else{
            $today['maxP'] = convertP($row['P']);
            $today['maxPTime'] = $thisTime;
        }
        // min P
        if(isset($today['minP'])){
            if($today['minP']>=convertP($row['P'])){
                $today['minP'] = convertP($row['P']);
                $today['minPTime'] = $thisTime;
            }
        }
        else{
            $today['minP'] = convertP($row['P']);
            $today['minPTime'] = $thisTime;
        }
        // max A
        if(isset($today['maxA'])){
            if($today['maxA']<=convertT($row['A'])){
                $today['maxA'] = convertT($row['A']);
                $today['maxATime'] = $thisTime;
            }
        }
        else{
            $today['maxA'] = convertT($row['A']);
            $today['maxATime'] = $thisTime;
        }
        // min T
        if(isset($today['minA'])){
            if($today['minA']>=convertT($row['A'])){
                $today['minA'] = convertT($row['A']);
                $today['minATime'] = $thisTime;
            }
        }
        else{
            $today['minA'] = convertT($row['A']);
            $today['minATime'] = $thisTime;
        }
        // max D
        if(isset($today['maxD'])){
            if($today['maxD']<=convertT($row['D'])){
                $today['maxD'] = convertT($row['D']);
                $today['maxDTime'] = $thisTime;
            }
        }
        else{
            $today['maxD'] = convertT($row['D']);
            $today['maxDTime'] = $thisTime;
        }
        // min D
        if(isset($today['minD'])){
            if($today['minD']>=convertT($row['D'])){
                $today['minD'] = convertT($row['D']);
                $today['minDTime'] = $thisTime;
            }
        }
        else{
            $today['minD'] = convertT($row['D']);
            $today['minDTime'] = $thisTime;
        }
        // max W
        if(isset($today['maxW'])){
            if($today['maxW']<=convertW($row['W'])){
                $today['maxW'] = convertW($row['W']);
                $today['maxWTime'] = $thisTime;
            }
        }
        else{
            $today['maxW'] = convertW($row['W']);
            $today['maxWTime'] = $thisTime;
        }
        // max G
        if( !isset($type) || ($type!="cumulus") ){   // ...type is undefined
            if(isset($today['maxG'])){
                if($today['maxG']<=convertW($row['G'])){
                    $today['maxG'] = convertW($row['G']);
                    $today['maxGTime'] = $thisTime;
                }
            }
            else{
                $today['maxG'] = convertW($row['G']);
                $today['maxGTime'] = $thisTime;
            }
        }
        // max RR
        if(isset($today['maxRR'])){
            if($today['maxRR']<=convertR($row['RR'])){
                $today['maxRR'] = convertR($row['RR']);
                $today['maxRRTime'] = $thisTime;
            }
        }
        else{
            $today['maxRR'] = convertR($row['RR']);
            $today['maxRRTime'] = $thisTime;
        }
        // max S
        if(isset($today['maxS'])){
            if($today['maxS']<=($row['S'])){
                $today['maxS'] = ($row['S']);
                $today['maxSTime'] = $thisTime;
            }
        }
        else{
            $today['maxS'] = ($row['S']);
            $today['maxSTime'] = $thisTime;
        }

        $bearings[] = $row['B'];
        $winds[] = convertW($row['W']);

        $currentDirection = windAbb($row['B']);
        $direction[$currentDirection] = (isset($direction[$currentDirection]) )?$direction[$currentDirection] + 1:1;
    }



    $result = mysqli_query($con,"
		SELECT T, P
		FROM alldata
		WHERE DateTime > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ORDER BY DateTime
        LIMIT 1
	"
	);
	while($row = mysqli_fetch_array($result)){
        $trend['T'] = $current['T'] - convertT($row['T']);
        $trend['P'] = $current['P'] - convertP($row['P']);
    }

    $today['avgW'] = array_sum($winds)/count($winds);
    $today['avgB'] = avgWind($bearings);
    $today['dominantWind'] = windAbb($today['avgB']);
    $today['direction'] = $direction;

    // check if the record value was not yet logged in db
    if($current['T']>=$today['maxT']){
        $today['maxT'] = $current['T'];
        $today['maxTTime'] = $current['Timestamp'];
    }
    if($current['T']<=$today['minT']){
        $today['minT'] = $current['T'];
        $today['minTTime'] = $current['Timestamp'];
    }
    if($current['A']>=$today['maxA']){
        $today['maxA'] = $current['A'];
        $today['maxATime'] = $current['Timestamp'];
    }
    if($current['A']<=$today['minA']){
        $today['minA'] = $current['A'];
        $today['minATime'] = $current['Timestamp'];
    }
    if($current['D']>=$today['maxD']){
        $today['maxD'] = $current['D'];
        $today['maxDTime'] = $current['Timestamp'];
    }
    if($current['D']<=$today['minD']){
        $today['minD'] = $current['D'];
        $today['minDTime'] = $current['Timestamp'];
    }
    if($current['H']>=$today['maxH']){
        $today['maxH'] = $current['H'];
        $today['maxHTime'] = $current['Timestamp'];
    }
    if($current['H']<=$today['minH']){
        $today['minH'] = $current['H'];
        $today['minHTime'] = $current['Timestamp'];
    }
    if($current['P']>=$today['maxP']){
        $today['maxP'] = $current['P'];
        $today['maxPTime'] = $current['Timestamp'];
    }
    if($current['P']<=$today['minP']){
        $today['minP'] = $current['P'];
        $today['minPTime'] = $current['Timestamp'];
    }  
    if($current['G']>=$today['maxG']){
        $today['maxG'] = $current['G'];
        $today['maxGTime'] = $current['Timestamp'];
    }
    if($current['S']>=$today['maxS']){
        $today['maxS'] = $current['S'];
        $today['maxSTime'] = $current['Timestamp'];
    }
    if($current['RR']>=$today['maxRR']){
        $today['maxRR'] = $current['RR'];
        $today['maxRRTime'] = $current['Timestamp'];
    }

    $windNames = array("N","NNE","NE","ENE","E","ESE","SE","SSE","S","SSW","SW","WSW","W","WNW","NW","NNW");
    $windRose = array();
    foreach($windNames as $windName){
        if(isset($today['direction'][$windName])){
            $windRose[] = $today['direction'][$windName];
        }
        else{
            $windRose[] = 0;
        }
    }

    $todayElapsed = strtotime(date("Y")."-".date('m')."-".date("d")." ".$current['Timestamp']) - strtotime(date("Y")."-".date('m')."-".date("d"));

    $windRun = ($today['avgW'])*($todayElapsed/(60*60));
    if($displayWindUnits=="ms"){
        $windRun = $windRun * 3.6;
    }

	function apparent($T,$H,$W){
		$T = ($T-32)/1.8;
		$e = ($H/100)*6.105*pow(2.71828, ((17.27*$T)/(237.7+$T)));
		$A = round(($T + 0.33*$e-0.7*$W-4),1);
		$A = ($A*1.8)+32;
		return $A;
	}
	function apparentWeatherCat($T,$H,$W){
		$e = ($H/100)*6.105*pow(2.71828, ((17.27*$T)/(237.7+$T)));
		$A = round(($T + 0.33*$e-0.7*$W-4),1);
		return $A;
	}
	function apparentWD($T,$H,$W){
		$e = ($H/100)*6.105*pow(2.71828, ((17.27*$T)/(237.7+$T)));
		$A = round(($T + 0.33*$e-0.7*$W-4),1);
		return $A;
	}
	function dewpointWeatherCat($T,$H){
		$D = round(((pow(($H/100), 0.125))*(112+0.9*$T)+(0.1*$T)-112),1);
		return $D;
	}
	function dewpoint($T,$H){
		$D = round(((pow(($H/100), 0.125))*(112+0.9*$T)+(0.1*$T)-112),1);
		return $D;
	}
    function steelUnits($unit){
        if($unit=="inhg"){
            return "inHg";
        }
        if($unit=="hpa"){
            return "hPa";
        }
        if($unit=="ms"){
            return "m/s";
        }
        if($unit=="km/h"){
            return "kmh";
        }
        if($unit=="kt"){
            return "kts";
        }
        else{
            return $unit;
        }
    }

?>
{"date":"<?php echo $current['Timestamp']?>",
"dateFormat":"m/d/y",
"temp":"<?php echo number_format($current['T'],1,".","")?>",
"tempTL":"<?php echo number_format($today['minT'],1,".","")?>",
"tempTH":"<?php echo number_format($today['maxT'],1,".","")?>",
"dew":"<?php echo number_format($current['D'],1,".","")?>",
"dewpointTL":"<?php echo number_format($today['minD'],1,".","")?>",
"dewpointTH":"<?php echo number_format($today['maxD'],1,".","")?>",
"apptemp":"<?php echo number_format($current['A'],1,".","")?>",
"apptempTL":"<?php echo number_format($today['minA'],1,".","")?>",
"apptempTH":"<?php echo number_format($today['maxA'],1,".","")?>",
"wlatest":"<?php echo number_format($current['W'],1,".","")?>",
"wspeed":"<?php echo number_format($today['avgW'],1,".","")?>",
"wgust":"<?php echo number_format($current['G'],1,".","")?>",
"wgustTM":"<?php echo number_format($today['maxG'],1,".","")?>",
"bearing":"<?php echo number_format($current['B'],0,".","")?>",
"avgbearing":"<?php echo number_format($today['avgB'],1,".","")?>",
"press":"<?php echo number_format($current['P'],$decimalsP,".","")?>",
"pressTL":"<?php echo number_format($today['minP'],$decimalsP,".","")?>",
"pressTH":"<?php echo number_format($today['maxP'],$decimalsP,".","")?>",
"pressL":"<?php echo number_format($today['minP'],$decimalsP,".","")?>",
"pressH":"<?php echo number_format($today['maxP'],$decimalsP,".","")?>",
"rfall":"<?php echo number_format($current['R'],$decimalsR,".","")?>",
"rrate":"<?php echo number_format($current['RR'],$decimalsR,".","")?>",
"rrateTM":"<?php echo number_format($today['maxRR'],$decimalsR,".","")?>",
"hum":"<?php echo number_format($current['H'],1,".","")?>",
"humTL":"<?php echo number_format($today['minH'],1,".","")?>",
"humTH":"<?php echo number_format($today['maxH'],1,".","")?>",
"SensorContactLost":"0",
"tempunit":"<?php echo $displayTempUnits?>",
"windunit":"<?php echo steelUnits($displayWindUnits)?>",
"pressunit":"<?php echo steelUnits($displayPressUnits)?>",
"rainunit":"<?php echo $displayRainUnits?>",
"temptrend":"<?php echo number_format($trend['T'],1,".","")?>",
"TtempTL":"<?php echo $today['minTTime']?>",
"TtempTH":"<?php echo $today['maxTTime']?>",
"TdewpointTL":"<?php echo $today['minDTime']?>",
"TdewpointTH":"<?php echo $today['maxDTime']?>",
"TapptempTL":"<?php echo $today['minATime']?>",
"TapptempTH":"<?php echo $today['maxATime']?>",
"TrrateTM":"<?php echo $today['maxRRTime']?>",
"ThourlyrainTH":"00:00",
"LastRainTipISO":"<?php echo number_format($current['R'],$decimalsR,".","")?>",
"hourlyrainTH":"0.0",
"ThumTL":"<?php echo $today['minHTime']?>",
"ThumTH":"<?php echo $today['maxHTime']?>",
"TpressTL":"<?php echo $today['minPTime']?>",
"TpressTH":"<?php echo $today['maxPTime']?>",
"presstrendval":"<?php echo number_format($trend['P'],$decimalsP,".","")?>",
"Tbeaufort":"F2",
"TwgustTM":"<?php echo $today['maxGTime']?>",
"TwindTM":"<?php echo $today['maxWTime']?>",
"windTM":"<?php echo number_format($today['maxW'],1,".","")?>",
"bearingTM":"<?php echo number_format($today['avgB'],1,".","")?>",
"timeUTC":"<?php echo gmdate("Y,m,d,H,i,s",$current['UNIX'])?>",
"BearingRangeFrom10":"359",
"BearingRangeTo10":"0",
"UV":"<?php echo number_format($current['UV'],1,".","")?>",
"UVTH":"--",
"SolarRad":"<?php echo number_format($current['S'],0,".","")?>",
"CurrentSolarMax":"N/A",
"SolarTM":"<?php echo number_format($today['maxS'],0,".","")?>",
"TSolarTM":"<?php echo $today['maxSTime']?>",
"domwinddir":"<?php echo $today['dominantWind']?>",
"WindRoseData":[<?php echo implode(",",$windRose)?>],
"windrun":"<?php echo number_format($windRun,1,".","")?>",
"forecast":"",
"version":"3.1",
"build":"10736",
"ver":"10",
"yesterday":<?php echo json_encode($yesterday)?>
}
