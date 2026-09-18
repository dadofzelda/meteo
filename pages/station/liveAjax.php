<?php
	
	############################################################################
	# 	
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Gauges Data Update
	#
	# 	A script that updates the gauges.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."header.php");
	
	
	$resultSQL = mysqli_query($con,"
		SELECT *
		FROM alldata 
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($resultSQL)){
		$temperature = convertT($row['T']);
		$humidity = $row['H'];
		$wind = convertW($row['W']);
		$gust = convertW($row['G']);
		$dew = convertT($row['D']);
		$apparent = convertT($row['A']);
		$solar = $row['S'];
		$pressure = convertP($row['P']);
		$rain = convertR($row['R']);
		$direction = $row['B'];
	}
	
	$resultAjax['temperature'] = round($temperature,1);
	$resultAjax['humidity'] = $humidity;
	$resultAjax['wind'] = round($wind,1);
	$resultAjax['gust'] = round($gust,1);
	$resultAjax['dew'] = round($dew,1);
	$resultAjax['apparent'] = round($apparent,1);
	$resultAjax['solar'] = $solar;
	$resultAjax['pressure'] = round($pressure,2);
	$resultAjax['rain'] = round($rain,2);
	$resultAjax['direction'] = $direction;
	
	print json_encode($resultAjax, JSON_NUMERIC_CHECK);
?>