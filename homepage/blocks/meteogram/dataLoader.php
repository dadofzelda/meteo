<?php
	require("../../../config.php");
	include("settings.php");

	if (strpos($meteogramLocations, ';') !== false) {
		$meteogramLocations = explode(";",$meteogramLocations);
		foreach($meteogramLocations as $meteogramLocation){
			$meteogramIDs[] = explode(",",$meteogramLocation);
		}
	}
	else{
		$meteogramIDs[0] = explode(",",$meteogramLocations);
	}

	# read meteogram data from yr.No
	$yrNoLocation = 'https://api.met.no/weatherapi/locationforecast/2.0/compact?lat='.$stationLat.'&lon='.$stationLon;

	if(file_exists("cache/yrNo.txt")){ 
		if (time()-filemtime("cache/yrNo.txt") > 60 * 180) {
			unlink("cache/yrNo.txt");
		}
	}
	if(file_exists("cache/yrNo.txt")){
		$cached = fopen("cache/yrNo.txt", "r");
		$response = fread($cached,filesize("cache/yrNo.txt"));
		fclose($cached);
	}
	else { 
		$response = loadContent($yrNoLocation,5);
		$cached = fopen("cache/yrNo.txt", "w");
		fwrite($cached, $response);
		fclose($cached);
	}  

	$callback = (string)$_GET['callback'];
	if (!$callback) $callback = 'callback';
	$json = file_get_contents("cache/yrNo.txt");
	header('Content-Type: text/javascript');
	echo "$callback($json);";

	function loadContent($url,$timeout){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36");
		$data = curl_exec($ch);
		curl_close($ch);

		if($data==""){
			$data = file_get_contents($url);
		}

		return $data;
	}
?>