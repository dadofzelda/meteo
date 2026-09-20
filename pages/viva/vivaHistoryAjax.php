<?php

	############################################################################
	#
	#	ViVa page - history proxy
	#
	#	Fetches ViVaStationHistory/{parameter}/{id} - "parameter" must match
	#	exactly one of the Sample "Name" values returned for that station by
	#	ViVaStationWithDirection (e.g. "Medelvind", "Vattenstand", "Vattentemp"
	#	- these vary station to station, see 08-viva-block-och-sida.md in the
	#	vault for the full list found during research).
	#
	############################################################################

	$id = isset($_GET['id']) ? preg_replace('/[^0-9]/','',$_GET['id']) : '';
	$param = isset($_GET['param']) ? $_GET['param'] : '';

	header("Content-Type: application/json; charset=utf-8");

	if($id=='' || $param==''){
		echo json_encode(array("error"=>"Missing id or param"));
		die();
	}

	if(!is_dir("cache")){
		mkdir("cache");
	}

	$cacheKey = md5($id."_".$param);
	$cacheFile = "cache/history_".$cacheKey.".json";
	$cacheMaxAge = 600; // 10 minutes - history changes less urgently than current conditions

	if(file_exists($cacheFile) && (time()-filemtime($cacheFile))<$cacheMaxAge){
		echo file_get_contents($cacheFile);
		die();
	}

	$url = "https://services.viva.sjofartsverket.se/output/vivaoutputservice.svc/ViVaStationHistory/".rawurlencode($param)."/".$id."?isMVY=false";

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
