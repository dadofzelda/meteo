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
	#	Database add record
	#
	############################################################################

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	// get configuration
	include("../config.php");


	// get data
    // check which sensors available
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbName' AND TABLE_NAME = 'alldataExtra'";
    $x = mysqli_query($con, $query);
    while($row = mysqli_fetch_array($x)){
        if($row['COLUMN_NAME'] != "DateTime"){
            $extraCols[] = $row['COLUMN_NAME'];
        }
    }

    for($i=0;$i<count($extraCols);$i++){
	    ${$extraCols[$i]} = $_GET[$extraCols[$i]];
    }
	
	$year = $_GET["y"];
	$month = $_GET["m"];
	$day = $_GET["d"];
	$hours = $_GET["h"];
	$minutes = $_GET["i"];
	
	$date = $year."-".$month."-".$day." ".$hours.":".$minutes.":00";
	
	// create string for data upload to MySQL
	$parameters = array();
	$values = array();
	
	array_push($parameters,"DateTime");
	array_push($values,"'".$date."'");

    for($i=0;$i<count($extraCols);$i++){	
        array_push($parameters,$extraCols[$i]);
        array_push($values,${$extraCols[$i]});
    }
	
	
	$query = "INSERT INTO alldataExtra (".implode(',',$parameters).") values (".implode(',',$values).")";
	echo $query;
	mysqli_query($con,$query);
	
	echo "<script type='text/javascript'>";
	echo "window.close();";
	echo "</script>";

?>