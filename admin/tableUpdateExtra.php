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
	#	Database table update
	#
	# 	AJAX script that performs database table update.
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");

    // check which sensors available
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbName' AND TABLE_NAME = 'alldataExtra'";
    $x = mysqli_query($con, $query);
    while($row = mysqli_fetch_array($x)){
        if($row['COLUMN_NAME'] != "DateTime"){
            $extraCols[] = $row['COLUMN_NAME'];
        }
    }

    $date = $_GET['date'];
    for($i=0;$i<count($extraCols);$i++){    
        $params[] = $extraCols[$i]."=".$_GET[$extraCols[$i]];
    }
    $sql = "UPDATE alldataExtra SET ";
    $sql .= implode(",",$params);
    $sql .= " WHERE DateTime='$date'";
    mysqli_query($con, $sql);
    echo "Saved.";

		

?>