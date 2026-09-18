<?php
	include("../config.php");

	$parameter = $_GET['parameter'];
	$value = $_GET['value'];
	$value = str_replace('-',' ',$value);
	
	$cookieFields = array();
	
	$cookieFields['design'] = $design;
	$cookieFields['design2'] = $design2;
	$cookieFields['designFont'] = $designFont;
	$cookieFields['designFont2'] = $designFont2;
	
	$cookieFields['displayTempUnits'] = $displayTempUnits;
	$cookieFields['displayWindUnits'] = $displayWindUnits;
	$cookieFields['displayRainUnits'] = $displayRainUnits;
	$cookieFields['displayPressUnits'] = $displayPressUnits;
	$cookieFields['displayCloudbaseUnits'] = $displayCloudbaseUnits;
	$cookieFields['displayVisibilityUnits'] = $displayVisibilityUnits;
	
	$cookieFields[$parameter] = $value;
	
	$cookieNew = array();
	
	array_push($cookieNew,$cookieFields['design']);
	array_push($cookieNew,$cookieFields['design2']);
	array_push($cookieNew,$cookieFields['displayTempUnits']);
	array_push($cookieNew,$cookieFields['displayRainUnits']);
	array_push($cookieNew,$cookieFields['displayWindUnits']);
	array_push($cookieNew,$cookieFields['displayPressUnits']);
	array_push($cookieNew,$cookieFields['displayCloudbaseUnits']);
	array_push($cookieNew,$cookieFields['displayVisibilityUnits']);
	array_push($cookieNew,$cookieFields['designFont']);
	array_push($cookieNew,$cookieFields['designFont2']);
	
	array_push($cookieNew,$lang);
	
	$cookieText = implode(";",$cookieNew);
	
	setcookie('weatherTemplate', $cookieText, time() + (86400 * 30), "/");
?>