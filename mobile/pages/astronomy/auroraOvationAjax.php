<?php

	############################################################################
	#
	#	Norrskenskarta - AJAX-data (mobil)
	#	Namespace:		aurora
	#	Meteotemplate-sida (custom, not from meteotemplate.com)
	#
	#	v1.0 - Sep 23, 2026
	#		- identisk logik som desktop-versionen, se
	#		  pages/astronomy/auroraOvationAjax.php för fullständig kommentar
	#
	############################################################################

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

	$grid = getAuroraOvationGrid();

	$points = array();
	$stationValue = null;
	$stationDist = null;

	if(isset($grid['coordinates']) && is_array($grid['coordinates'])){
		foreach($grid['coordinates'] as $c){
			$lon = $c[0];
			$lat = $c[1];
			$val = $c[2];

			if($lon>180){
				$lon = $lon - 360;
			}

			if($lat<35 || $lon<-40 || $lon>70){
				continue;
			}

			$dist = abs($lat-$stationLat) + abs($lon-$stationLon);
			if($stationDist===null || $dist<$stationDist){
				$stationDist = $dist;
				$stationValue = $val;
			}

			if($val>0){
				$points[] = array($lat,$lon,$val);
			}
		}
	}

	$result = array(
		"points" => $points,
		"stationValue" => $stationValue,
		"observationTime" => isset($grid['Observation Time']) ? $grid['Observation Time'] : null,
		"forecastTime" => isset($grid['Forecast Time']) ? $grid['Forecast Time'] : null
	);

	header('Content-Type: application/json');
	print json_encode($result);

?>
