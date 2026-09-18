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
	#	Database table delete
	#
	# 	AJAX script that performs database delete of record.
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	

	$date = $_GET['date'];
	$sql = "DELETE FROM alldataExtra WHERE YEAR(DateTime)=".date('Y',$date)." AND MONTH(DateTime)=".date('m',$date)." AND DAY(DateTime)=".date('d',$date)." AND HOUR(DateTime)=".date('H',$date)." AND MINUTE(DateTime)=".date('i',$date);
	mysqli_query($con, $sql);
	echo "Deleted.";

?>