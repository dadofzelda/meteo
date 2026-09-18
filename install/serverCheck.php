<?php

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$analysisResult = array();

	// start checking functions needed by the template.

	// PHP version
	$phpVersionUsed = PHP_VERSION;
	if(PHP_MAJOR_VERSION<5){
		$analysisResult[] = array("error","PHP version","You are using PHP version ".$phpVersionUsed.", this version is NOT compatible with Meteotemplate. You need to be using at least version 5.4, which is the lowest supported PHP version. Please update your server PHP on your server or in your hosting control panel.");
	}
	else if(PHP_MAJOR_VERSION==5 && PHP_MINOR_VERSION<4){
		$analysisResult[] = array("error","PHP version","You are using PHP version ".$phpVersionUsed.", this version is NOT compatible with Meteotemplate. You need to be using at least version 5.4, which is the lowest supported PHP version. Please update your server PHP on your server or in your hosting control panel.");
	}
	else{
		$analysisResult[] = array("ok","PHP version","You are using PHP version ".$phpVersionUsed.", which is OK.");
	}

	// Curl function
	if(function_exists("curl_init")){
		$analysisResult[] = array("ok","CURL extension","CURL extension is enabled on your server.");
	}
	else{
		$analysisResult[] = array("error","CURL extension","Your server does not have the CURL extension installed/enabled. Please install it or enable it in your hosting control panel. The CURL function is essential for retrieving data from external websites and APIs.");
	}

	// file_get_contents enabled
	if( ini_get('allow_url_fopen') ) {
		$analysisResult[] = array("ok","url_fopen","The url_fopen is enabled on your server.");
	}
	else{
		$analysisResult[] = array("error","url_fopen","The url_fopen property is disabled in your PHP settings file. This needs to be enabled in order to be able to read files.");
	}

	echo "<div style='width:80%;margin:0 auto'><h1 style='text-align:center'>Server Check</h1>";

	foreach($analysisResult as $result){
		$status = $result[0];
		if($status=="ok"){
			$colorStatus = "green";
		}
		else{
			$colorStatus = "red";
		}
		$parameter = $result[1];
		$details = $result[2];

		echo "<h2 style='font-weight:bold'>".$parameter."</h2>";
		echo "<p style='color:".$colorStatus.";font-weight:bold'>".$details."</p><br>";
	}

	echo "</div>";
