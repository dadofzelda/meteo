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
	
	$query = "CREATE TABLE newExtra SELECT DateTime, avg(T1), avg(UV) FROM alldataExtra GROUP BY round(UNIX_TIMESTAMP(DateTime) DIV ".$interval.")";

	mysqli_query($con,$query);

	mysqli_query($con,"ALTER TABLE  newExtra CHANGE  `avg(T1)` `T1` DECIMAL( 4, 1 ) NULL DEFAULT NULL");
    mysqli_query($con,"ALTER TABLE  newExtra CHANGE  `avg(UV)` `UV` DECIMAL( 4, 1 ) NULL DEFAULT NULL");

	mysqli_query($con,"RENAME TABLE  ".$dbName.".alldataExtra TO  ".$dbName.".alldataExtra_bkp;");
	mysqli_query($con,"RENAME TABLE  ".$dbName.".newExtra TO  ".$dbName.".alldataExtra;");

	mysqli_query($con,"ALTER TABLE `alldataExtra` ADD PRIMARY KEY(`DateTime`)");

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