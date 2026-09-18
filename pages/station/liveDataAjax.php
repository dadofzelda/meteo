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
	#	Current Conditions
	#
	# 	A script showing current conditions and some past data for all 
	#	parameters.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	$parameter = $_GET['parameter'];
	
	if($parameter=="T"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['todayMax'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['todayMin'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(convertTStddev($row['stddev(T)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['yesterdayMax'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['yesterdayMin'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(convertTStddev($row['stddev(T)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['last24Max'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['last24Min'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(convertTStddev($row['stddev(T)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['monthMax'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['monthMin'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(convertTStddev($row['stddev(T)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['yearMax'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['yearMin'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(convertTStddev($row['stddev(T)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(Tmax), min(Tmin), avg(T), stddev(T)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertT($row['avg(T)']),2,".","");
			$data['allMax'] = number_format(convertT($row['max(Tmax)']),1,".","");
			$data['allMin'] = number_format(convertT($row['min(Tmin)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(convertTStddev($row['stddev(T)']),2,".","");
		}
		$units = unitFormatter($displayTempUnits);
	}
	if($parameter=="A"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['todayMax'] = number_format(convertT($row['max(A)']),1,".","");
			$data['todayMin'] = number_format(convertT($row['min(A)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(convertTStddev($row['stddev(A)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['yesterdayMax'] = number_format(convertT($row['max(A)']),1,".","");
			$data['yesterdayMin'] = number_format(convertT($row['min(A)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(convertTStddev($row['stddev(A)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['last24Max'] = number_format(convertT($row['max(A)']),1,".","");
			$data['last24Min'] = number_format(convertT($row['min(A)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(convertTStddev($row['stddev(A)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['monthMax'] = number_format(convertT($row['max(A)']),1,".","");
			$data['monthMin'] = number_format(convertT($row['min(A)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(convertTStddev($row['stddev(A)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['yearMax'] = number_format(convertT($row['max(A)']),1,".","");
			$data['yearMin'] = number_format(convertT($row['min(A)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(convertTStddev($row['stddev(A)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(A), min(A), avg(A), stddev(A)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertT($row['avg(A)']),2,".","");
			$data['allMax'] = number_format(convertT($row['max(A)']),1,".","");
			$data['allMin'] = number_format(convertT($row['min(A)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(convertTStddev($row['stddev(A)']),2,".","");
		}
		$units = unitFormatter($displayTempUnits);
	}
	if($parameter=="D"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['todayMax'] = number_format(convertT($row['max(D)']),1,".","");
			$data['todayMin'] = number_format(convertT($row['min(D)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(convertTStddev($row['stddev(D)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['yesterdayMax'] = number_format(convertT($row['max(D)']),1,".","");
			$data['yesterdayMin'] = number_format(convertT($row['min(D)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(convertTStddev($row['stddev(D)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['last24Max'] = number_format(convertT($row['max(D)']),1,".","");
			$data['last24Min'] = number_format(convertT($row['min(D)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(convertTStddev($row['stddev(D)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['monthMax'] = number_format(convertT($row['max(D)']),1,".","");
			$data['monthMin'] = number_format(convertT($row['min(D)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(convertTStddev($row['stddev(D)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['yearMax'] = number_format(convertT($row['max(D)']),1,".","");
			$data['yearMin'] = number_format(convertT($row['min(D)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(convertTStddev($row['stddev(D)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(D), min(D), avg(D), stddev(D)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertT($row['avg(D)']),2,".","");
			$data['allMax'] = number_format(convertT($row['max(D)']),1,".","");
			$data['allMin'] = number_format(convertT($row['min(D)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(convertTStddev($row['stddev(D)']),2,".","");
		}
		$units = unitFormatter($displayTempUnits);
	}
	if($parameter=="W"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['todayMax'] = number_format(convertW($row['max(W)']),1,".","");
			$data['todayMin'] = number_format(convertW($row['min(W)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(convertW($row['stddev(W)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['yesterdayMax'] = number_format(convertW($row['max(W)']),1,".","");
			$data['yesterdayMin'] = number_format(convertW($row['min(W)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(convertW($row['stddev(W)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['last24Max'] = number_format(convertW($row['max(W)']),1,".","");
			$data['last24Min'] = number_format(convertW($row['min(W)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(convertW($row['stddev(W)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['monthMax'] = number_format(convertW($row['max(W)']),1,".","");
			$data['monthMin'] = number_format(convertW($row['min(W)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(convertW($row['stddev(W)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['yearMax'] = number_format(convertW($row['max(W)']),1,".","");
			$data['yearMin'] = number_format(convertW($row['min(W)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(convertW($row['stddev(W)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(W), min(W), avg(W), stddev(W)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertW($row['avg(W)']),2,".","");
			$data['allMax'] = number_format(convertW($row['max(W)']),1,".","");
			$data['allMin'] = number_format(convertW($row['min(W)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(convertW($row['stddev(W)']),2,".","");
		}
		$units = unitFormatter($displayWindUnits);
	}
	if($parameter=="G"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['todayMax'] = number_format(convertW($row['max(G)']),1,".","");
			$data['todayMin'] = number_format(convertW($row['min(G)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(convertW($row['stddev(G)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['yesterdayMax'] = number_format(convertW($row['max(G)']),1,".","");
			$data['yesterdayMin'] = number_format(convertW($row['min(G)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(convertW($row['stddev(G)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['last24Max'] = number_format(convertW($row['max(G)']),1,".","");
			$data['last24Min'] = number_format(convertW($row['min(G)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(convertW($row['stddev(G)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['monthMax'] = number_format(convertW($row['max(G)']),1,".","");
			$data['monthMin'] = number_format(convertW($row['min(G)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(convertW($row['stddev(G)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['yearMax'] = number_format(convertW($row['max(G)']),1,".","");
			$data['yearMin'] = number_format(convertW($row['min(G)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(convertW($row['stddev(G)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(G), min(G), avg(G), stddev(G)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertW($row['avg(G)']),2,".","");
			$data['allMax'] = number_format(convertW($row['max(G)']),1,".","");
			$data['allMin'] = number_format(convertW($row['min(G)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(convertW($row['stddev(G)']),2,".","");
		}
		$units = unitFormatter($displayWindUnits);
	}
	if($parameter=="P"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['todayMax'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['todayMin'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],$decimalsP,".","");
			$data['todaySD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['yesterdayMax'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['yesterdayMin'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],$decimalsP,".","");
			$data['yesterdaySD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['last24Max'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['last24Min'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],$decimalsP,".","");
			$data['last24SD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['monthMax'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['monthMin'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],$decimalsP,".","");
			$data['monthSD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['yearMax'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['yearMin'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],$decimalsP,".","");
			$data['yearSD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(P), min(P), avg(P), stddev(P)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(convertP($row['avg(P)']),($decimalsP+1),".","");
			$data['allMax'] = number_format(convertP($row['max(P)']),$decimalsP,".","");
			$data['allMin'] = number_format(convertP($row['min(P)']),$decimalsP,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],$decimalsP,".","");
			$data['allSD'] = number_format(convertP($row['stddev(P)']),($decimalsP+1),".","");
		}
		$units = unitFormatter($displayPressUnits);
	}
	if($parameter=="H"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(($row['avg(H)']),2,".","");
			$data['todayMax'] = number_format(($row['max(H)']),1,".","");
			$data['todayMin'] = number_format(($row['min(H)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(($row['stddev(T)']),2,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(($row['avg(H)']),2,".","");
			$data['yesterdayMax'] = number_format(($row['max(H)']),1,".","");
			$data['yesterdayMin'] = number_format(($row['min(H)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(($row['stddev(H)']),2,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(($row['avg(H)']),2,".","");
			$data['last24Max'] = number_format(($row['max(H)']),1,".","");
			$data['last24Min'] = number_format(($row['min(H)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(($row['stddev(H)']),2,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(($row['avg(H)']),2,".","");
			$data['monthMax'] = number_format(($row['max(H)']),1,".","");
			$data['monthMin'] = number_format(($row['min(H)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(($row['stddev(H)']),2,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(($row['avg(H)']),2,".","");
			$data['yearMax'] = number_format(($row['max(H)']),1,".","");
			$data['yearMin'] = number_format(($row['min(H)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(($row['stddev(H)']),2,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(H), min(H), avg(H), stddev(H)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(($row['avg(H)']),2,".","");
			$data['allMax'] = number_format(($row['max(H)']),1,".","");
			$data['allMin'] = number_format(($row['min(H)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(($row['stddev(H)']),2,".","");
		}
		$units = "%";
	}
	if($parameter=="S"){
		// Today
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE()
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayAvg'] = number_format(($row['avg(S)']),1,".","");
			$data['todayMax'] = number_format(($row['max(S)']),1,".","");
			$data['todayMin'] = number_format(($row['min(S)']),1,".","");
			$data['todayRange'] = number_format($data['todayMax'] - $data['todayMin'],1,".","");
			$data['todaySD'] = number_format(($row['stddev(T)']),1,".","");
		}
		// Yesterday
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayAvg'] = number_format(($row['avg(S)']),1,".","");
			$data['yesterdayMax'] = number_format(($row['max(S)']),1,".","");
			$data['yesterdayMin'] = number_format(($row['min(S)']),1,".","");
			$data['yesterdayRange'] = number_format($data['yesterdayMax'] - $data['yesterdayMin'],1,".","");
			$data['yesterdaySD'] = number_format(($row['stddev(S)']),1,".",""); 
		}
		// Last 24h
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				WHERE DateTime >= now() - interval 24 hour
				ORDER BY DateTime 
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['last24Avg'] = number_format(($row['avg(S)']),1,".","");
			$data['last24Max'] = number_format(($row['max(S)']),1,".","");
			$data['last24Min'] = number_format(($row['min(S)']),1,".","");
			$data['last24Range'] = number_format($data['last24Max'] - $data['last24Min'],1,".","");
			$data['last24SD'] = number_format(($row['stddev(S)']),1,".","");
		}
		// This month
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['monthAvg'] = number_format(($row['avg(S)']),1,".","");
			$data['monthMax'] = number_format(($row['max(S)']),1,".","");
			$data['monthMin'] = number_format(($row['min(S)']),1,".","");
			$data['monthRange'] = number_format($data['monthMax'] - $data['monthMin'],1,".","");
			$data['monthSD'] = number_format(($row['stddev(S)']),1,".","");
		}
		// This year
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yearAvg'] = number_format(($row['avg(S)']),1,".","");
			$data['yearMax'] = number_format(($row['max(S)']),1,".","");
			$data['yearMin'] = number_format(($row['min(S)']),1,".","");
			$data['yearRange'] = number_format($data['yearMax'] - $data['yearMin'],1,".","");
			$data['yearSD'] = number_format(($row['stddev(S)']),1,".","");
		}
		// All time
		$result = mysqli_query($con, "
				SELECT  max(S), min(S), avg(S), stddev(S)
				FROM  alldata
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['allAvg'] = number_format(($row['avg(S)']),1,".","");
			$data['allMax'] = number_format(($row['max(S)']),1,".","");
			$data['allMin'] = number_format(($row['min(S)']),1,".","");
			$data['allRange'] = number_format($data['allMax'] - $data['allMin'],1,".","");
			$data['allSD'] = number_format(($row['stddev(S)']),1,".","");
		}
		$units = "W/m<sup>2</sup>";
	}
	
	if($parameter=="R"){
		$result = mysqli_query($con, "
			SELECT max(R)
			FROM  alldata
			WHERE DATE(DateTime) = CURDATE()
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['todayR'] = convertR($row['max(R)']);
		}
		
		$result = mysqli_query($con, "
			SELECT max(R)
			FROM  alldata
			WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['yesterdayR'] = convertR($row['max(R)']);
		}
		
		$result = mysqli_query($con, "
			SELECT R
			FROM  alldata
			WHERE DateTime >= now() - interval 24 hour
			ORDER BY DateTime 
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$data['temporaryRain'] = convertR($row['R']); // for later calculation of last 24 h rain
		}
		
		// Calculate precipitation last 24 h - use todays rain plus yesterdays value minus the value already counted 24h ago
		$data['last24R'] = $data['todayR'] + ($data['yesterdayR'] - $data['temporaryRain']);
		
		// Calculate precipitation this month
		$data['monthRains'] = array();
		$result = mysqli_query($con, "
				SELECT  max(R)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($data['monthRains'], convertR($row['max(R)']));		
		}
		if(isset($data['monthRains'])){
			$data['monthAvgR'] = array_sum($data['monthRains'])/count($data['monthRains']);
			$data['monthMaxR'] = max($data['monthRains']);
		}
		$data['monthR'] = array_sum($data['monthRains']);
		
		// Calculate precipitation this year
		$data['yearRains'] = array();
		$result = mysqli_query($con, "
				SELECT  max(R)
				FROM  alldata
				WHERE YEAR(DateTime) = YEAR(CURDATE())
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($data['yearRains'], convertR($row['max(R)']));		
		}
		if(empty($data['yearRains'])===false){
			$data['yearAvgR'] = array_sum($data['yearRains'])/count($data['yearRains']);
			$data['yearMaxR'] = max($data['yearRains']);
		}
		$data['yearR'] = array_sum($data['yearRains']);
		
		// Calculate precipitation all time
		$data['allRains'] = array();
		$result = mysqli_query($con, "
				SELECT  max(R)
				FROM  alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
		);
		while ($row = mysqli_fetch_array($result)) {
			array_push($data['allRains'], convertR($row['max(R)']));		
		}
		if(empty($data['allRains'])===false){
			$data['allAvgR'] = array_sum($data['allRains'])/count($data['allRains']);
			$data['allMaxR'] = max($data['allRains']);
		}
		$data['allR'] = array_sum($data['allRains']);	
	}

?>
	<?php
		if($parameter!="R"){
	?>
		<table class="table" style="text-align:center;width:100%">
			<thead>
				<tr>
					<th>
					</th>
					<th class="dataCell">
						<?php echo lang('avgAbbr','c')?>
					</th>
					<th class="dataCell">
						<?php echo lang('maximumAbbr','c')?>
					</th>
					<th class="dataCell">
						<?php echo lang('minimumAbbr','c')?>
					</th>
					<th class="dataCell">
						<?php echo lang('range','c')?>
					</th>
					<th class="dataCell">
						<?php echo lang("sdAbbr",'')?>
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("today",'c')?>
					</td>
					<td>
						<?php echo $data['todayAvg']." ".$units?>
					</td>
					<td>
						<?php echo $data['todayMax']." ".$units?>
					</td>
					<td>
						<?php echo $data['todayMin']." ".$units?>
					</td>
					<td>
						<?php echo $data['todayRange']." ".$units?>
					</td>
					<td>
						<?php echo $data['todaySD']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("yesterday",'c')?>
					</td>
					<td>
						<?php echo $data['yesterdayAvg']." ".$units?>
					</td>
					<td>
						<?php echo $data['yesterdayMax']." ".$units?>
					</td>
					<td>
						<?php echo $data['yesterdayMin']." ".$units?>
					</td>
					<td>
						<?php echo $data['yesterdayRange']." ".$units?>
					</td>
					<td>
						<?php echo $data['yesterdaySD']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("last 24h",'c')?>
					</td>
					<td>
						<?php echo $data['last24Avg']." ".$units?>
					</td>
					<td>
						<?php echo $data['last24Max']." ".$units?>
					</td>
					<td>
						<?php echo $data['last24Min']." ".$units?>
					</td>
					<td>
						<?php echo $data['last24Range']." ".$units?>
					</td>
					<td>
						<?php echo $data['last24SD']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("this month","c")?>
					</td>
					<td>
						<?php echo $data['monthAvg']." ".$units?>
					</td>
					<td>
						<?php echo $data['monthMax']." ".$units?>
					</td>
					<td>
						<?php echo $data['monthMin']." ".$units?>
					</td>
					<td>
						<?php echo $data['monthRange']." ".$units?>
					</td>
					<td>
						<?php echo $data['monthSD']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("this year","c")?>
					</td>
					<td>
						<?php echo $data['yearAvg']." ".$units?>
					</td>
					<td>
						<?php echo $data['yearMax']." ".$units?>
					</td>
					<td>
						<?php echo $data['yearMin']." ".$units?>
					</td>
					<td>
						<?php echo $data['yearRange']." ".$units?>
					</td>
					<td>
						<?php echo $data['yearSD']?>
					</td>
				</tr>
				<tr>
					<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
						<?php echo lang("all time","c")?>
					</td>
					<td>
						<?php echo $data['allAvg']." ".$units?>
					</td>
					<td>
						<?php echo $data['allMax']." ".$units?>
					</td>
					<td>
						<?php echo $data['allMin']." ".$units?>
					</td>
					<td>
						<?php echo $data['allRange']." ".$units?>
					</td>
					<td>
						<?php echo $data['allSD']?>
					</td>
				</tr>
			</tbody>
		</table>
	<?php
		}
	?>
	<?php
		if($parameter=="R"){
	?>
			<table class="table" style="text-align:center;width:100%">
				<thead>
					<tr>
						<th>
						</th>
						<th>
							<?php echo lang("avgAbbr",'c')."/".lang('day','l')?>
						</th>
						<th>
							<?php echo lang("maximumAbbr",'c')."/".lang('day','l')?>
						</th>
						<th>
							<?php echo lang('total','c')?>
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("today",'c')?>
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
						<td>
							<?php echo number_format($data['todayR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("yesterday",'c')?>
						</td>
						<td>
							
						</td>
						<td>
							
						</td>
						<td>
							<?php echo number_format($data['yesterdayR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("last 24h","c")?>
						</td>
						<td>
						</td>
						<td>
						</td>
						<td>
							<?php echo number_format($data['last24R'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("this month","c")?>
						</td>
						<td>
							<?php echo number_format($data['monthAvgR'],($decimalsR+1),".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['monthMaxR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['monthR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("this year","c")?>
						</td>
						<td>
							<?php echo number_format($data['yearAvgR'],($decimalsR + 1),".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['yearMaxR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['yearR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left!important; background: #<?php echo $color_schemes[$design2]['900']?>">
							<?php echo lang("all time","c")?>
						</td>
						<td>
							<?php echo number_format($data['allAvgR'],($decimalsR+1),".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['allMaxR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
						<td>
							<?php echo number_format($data['allR'],$decimalsR,".","")." ".unitFormatter($displayRainUnits)?>
						</td>
					</tr>
				</tbody>
			</table>
	<?php 
		}
	?>
	
