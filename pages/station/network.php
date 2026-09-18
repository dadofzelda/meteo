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
	#	Meteotemplate network
	#
	# 	A script that creates output for the Meteotemplate users network
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include("../../scripts/functions.php");
	
	// override units
	$displayTempUnits = "C";
	$displayPressUnits = "hpa";
	$displayWindUnits = "ms";
	$displayRainUnits = "mm";
	
	$conditions = array();
	$conditions24 = array();

	$result = mysqli_query($con, "
		SELECT  max(DateTime), avg(T), max(Tmax), min(Tmin), avg(P), avg(H), avg(W), max(G), avg(S), avg(D), avg(A), max(R)
		FROM  alldata
		WHERE DateTime >= now() - interval 1 hour
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$conditions['Tavg'] = convertT($row['avg(T)']);
		$conditions['Tmax'] = convertT($row['max(Tmax)']);
		$conditions['Tmin'] = convertT($row['min(Tmin)']);
		$conditions['A'] = convertT($row['avg(A)']);
		$conditions['D'] = convertT($row['avg(D)']);
		$conditions['H'] = $row['avg(H)'];
		$conditions['S'] = $row['avg(S)'];
		$conditions['P'] = convertP($row['avg(P)']);
		$conditions['W'] = convertW($row['avg(W)']);
		$conditions['G'] = convertW($row['max(G)']);
		$conditions['R'] = convertR($row['max(R)']);	
		$conditions['timestamp'] = strtotime($row['max(DateTime)']);
		$conditions['tz'] = $stationTZ;
	}

	$result = mysqli_query($con, "
		SELECT  min(DateTime), avg(T), max(Tmax), min(Tmin), avg(P), avg(H), avg(W), max(G), avg(S), avg(D), avg(A), max(R)
		FROM  alldata
		WHERE DateTime >= now() - interval 24 hour
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$conditions24['Tavg'] = convertT($row['avg(T)']);
		$conditions24['Tmax'] = convertT($row['max(Tmax)']);
		$conditions24['Tmin'] = convertT($row['min(Tmin)']);
		$conditions24['A'] = convertT($row['avg(A)']);
		$conditions24['D'] = convertT($row['avg(D)']);
		$conditions24['H'] = $row['avg(H)'];
		$conditions24['S'] = $row['avg(S)'];
		$conditions24['P'] = convertP($row['avg(P)']);
		$conditions24['W'] = convertW($row['avg(W)']);
		$conditions24['G'] = convertW($row['max(G)']);
		$conditions24['R'] = convertR($row['max(R)']);	
		$conditions24['timestamp'] = strtotime($row['min(DateTime)']);
	}	
	$final['conditions1h'] = $conditions;
	$final['conditions24h'] = $conditions24;
	print json_encode($final, JSON_NUMERIC_CHECK);
?>