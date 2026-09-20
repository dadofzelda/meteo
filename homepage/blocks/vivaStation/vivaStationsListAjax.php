<?php

	############################################################################
	#
	#	ViVa Station block - station list proxy
	#
	#	Fetches the full list of ~185 Sjofartsverket ViVa stations (ID, name,
	#	coordinates) and caches it for 24h - this list changes very rarely.
	#
	############################################################################

	header("Content-Type: application/json; charset=utf-8");

	if(!is_dir("cache")){
		mkdir("cache");
	}

	$cacheFile = "cache/stationList.json";
	$cacheMaxAge = 86400; // 24 hours

	if(file_exists($cacheFile) && (time()-filemtime($cacheFile))<$cacheMaxAge){
		echo file_get_contents($cacheFile);
		die();
	}

	$url = "https://services.viva.sjofartsverket.se/output/vivaoutputservice.svc/vivastation/";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_USERAGENT, "Meteotemplate weather.sollebrunn.net");
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($response===false || $httpCode!=200){
		if(file_exists($cacheFile)){
			echo file_get_contents($cacheFile);
			die();
		}
		echo json_encode(array("error"=>"Could not reach ViVa service"));
		die();
	}

	file_put_contents($cacheFile,$response);
	echo $response;

?>
