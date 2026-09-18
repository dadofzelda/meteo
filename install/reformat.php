<?php

############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Table reformating script
	#
	# 	A script to change time interval of main table and create a backup of 
	#	original table.
	#
	############################################################################

	
	include("../config.php");
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "You are not logged in as an administrator.";
		die();
	}
	
	$interval = 300; // new interval in seconds
	
	$query = "CREATE TABLE new SELECT DateTime, avg(T), max(Tmax), min(Tmin), avg(H), avg(D), avg(W),max(G),B,avg(RR),max(R),avg(P),max(S),avg(A) FROM alldata GROUP BY round(UNIX_TIMESTAMP(DateTime) DIV ".$interval.")";

	mysqli_query($con,$query);

	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(T)` `T` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `max(Tmax)` `Tmax` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `min(Tmin)` `Tmin` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(H)` `H` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(D)` `D` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(W)` `W` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `max(G)` `G` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(RR)` `RR` DECIMAL( 7, 3 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `max(R)` `R` DECIMAL( 7, 3 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(P)` `P` DECIMAL( 7, 3 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `max(S)` `S` DECIMAL( 5, 1 ) NULL DEFAULT NULL");
	mysqli_query($con,"ALTER TABLE  new CHANGE  `avg(A)` `A` DECIMAL( 4, 1 ) NULL DEFAULT NULL");

	mysqli_query($con,"RENAME TABLE  ".$dbName.".alldata TO  ".$dbName.".alldata_bkp;");
	mysqli_query($con,"RENAME TABLE  ".$dbName.".new TO  ".$dbName.".alldata;");

	mysqli_query($con,"ALTER TABLE `alldata` ADD PRIMARY KEY(`DateTime`)");

?>
<html>
	<head>
		<title>Database Reformating</title>
		<style>
			body{
				background-color: black;
				color: white;
				width: 80%;
				margin-left: auto;
				margin-right: auto;
				padding: 15px;
				margin-top: 10px;
				margin-bottom: 10px;
				text-align: center;
			}	
		</style>
	</head>

	<body>
		<h1> DATABASE REFORMATING...</H1>
		<H2> DONE!</H2>
	</body>
<html>