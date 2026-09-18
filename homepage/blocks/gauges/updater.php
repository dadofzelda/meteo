<?php
	include("../../../config.php");
	include("../../../scripts/functions.php");

	if($prefferedTime=="12h"){
		$updateTimeFormat = "g:i:s A";
	}
	else{
		$updateTimeFormat = "G:i:s";
	}

	// use API
	$apiData = file_get_contents("../../../meteotemplateLive.txt");
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
	$current['DateTime'] = $apiData['U'];
	$current['Timestamp'] = date($updateTimeFormat,$apiData['U']);

	if(!isset($apiData['U']) || ($apiData['U'] + 60 * 60) < time()){
		$current['offline'] = 1;
	}
	else{
		$current['offline'] = 0;
	}

	if($displayTempUnits=="F"){
		$TC = (($current['T']-32)/1.8);
	}
	else{
		$TC = $current['T'];
	}
	$H = $current['H'];
	$CBI = (((110 - 1.373*$H) - 0.54 * (10.20 - $TC)) * (124 * pow(10,(-0.0142*$H))))/60;
	$current['CBI'] = round($CBI);

	if($CBI<0){
		$CBI=0;
	}

	if($CBI<50){
		$current['FD'] = "0";
	}
	else if($CBI>=50 && $CBI<75){
		$current['FD'] = 1;
	}
	else if($CBI>=75 && $CBI<90){
		$current['FD'] = 2;
	}
	else if($CBI>=90 && $CBI<97.5){
		$current['FD'] = 3;
	}
	else{
		$current['FD'] = 4;
	}

	// calculate wetbulb
	$WB_T = $apiData['T'];
	$WB_H = $apiData['H'];

	$WB = $WB_T * atan(0.151977 * sqrt($WB_H + 8.313659)) + atan($WB_T + $WB_H) - atan($WB_H - 1.676331) + 0.00391838 * ($WB_H^(3/2)) * atan(0.023101 * $WB_H) - 4.686035;

	if($displayTempUnits=="F"){ 
		$WB = convertor($WB,"C","F");
	}

	$current['WB'] = number_format($WB,1,".","");

	$current = json_encode($current);
	echo $current;
?>
