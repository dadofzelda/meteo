<?php

	############################################################################
	#
	#	ViVa Station block - current values proxy
	#
	#	Fetches ViVaStationWithDirection/{id} from Sjofartsverket's ViVa service
	#	(https://viva.sjofartsverket.se/) and caches the result locally for a
	#	few minutes so we don't hammer their API on every page view. Their own
	#	response says "cache-control: public, max-age=10", but 10 seconds is
	#	too aggressive for a shared cache serving many visitors - 5 minutes is
	#	plenty for a marine conditions display.
	#
	############################################################################

	$id = isset($_GET['id']) ? preg_replace('/[^0-9]/','',$_GET['id']) : '';

	header("Content-Type: application/json; charset=utf-8");

	if($id==''){
		echo json_encode(array("error"=>"No station id provided"));
		die();
	}

	if(!is_dir("cache")){
		mkdir("cache");
	}

	$cacheFile = "cache/current_".$id.".json";
	$cacheMaxAge = 300; // 5 minutes

	if(file_exists($cacheFile) && (time()-filemtime($cacheFile))<$cacheMaxAge){
		echo file_get_contents($cacheFile);
		die();
	}

	$url = "https://services.viva.sjofartsverket.se/output/vivaoutputservice.svc/ViVaStationWithDirection/".$id."?isMVY=false";

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 8);
	curl_setopt($ch, CURLOPT_USERAGENT, "Meteotemplate weather.sollebrunn.net");
	$response = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if($response===false || $httpCode!=200){
		// serve stale cache rather than nothing, if we have one
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
