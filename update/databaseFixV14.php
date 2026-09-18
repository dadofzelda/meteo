<?php

    ############################################################################
    #
    #   Meteotemplate
    #   http://www.meteotemplate.com
    #   Free website template for weather enthusiasts
    #   Author: Jachym
    #           Brno, Czech Republic
    #   First release: 2015
    #
    ############################################################################
    #           
    #   v1.0    2017-04-24  
    #
    ############################################################################

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	
    $query = "SELECT DateTime, R FROM alldata WHERE YEAR(DateTime)>=2017 AND HOUR(DateTime)=23 AND MINUTE(DateTime)=59 AND SECOND(DateTime)=59";

    $result = mysqli_query($con,$query);
    while($row = mysqli_fetch_array($result)){
        $thisDate = strtotime($row['DateTime']);
        $thisR = $row['R'];
        $previousR = $thisR;
        $subQuery = "SELECT DateTime, R FROM alldata  WHERE DateTime='".date("Y-m-d",$thisDate)." 23:55:00'";
        $result2 = mysqli_query($con,$subQuery);
        while($row2 = mysqli_fetch_array($result2)){
            $previousR = $row2['R'];
        }
        $diff = $thisR - $previousR;
        if($diff>0){
            $newRain = $diff+$previousR;
        }
        else{
            $newRain = $previousR;
        }
        $newDate = $thisDate + 1;
        $newQuery = "UPDATE alldata SET R=".$newRain." WHERE DateTime='".date("Y-m-d",$thisDate)." 23:55:00'";
        echo "The database will now be updated with the following query: ".$newQuery."<br>";
        mysqli_query($con,$newQuery);
        $newQuery = "UPDATE alldata SET DateTime='".date("Y-m-d",$newDate)." 00:00:00', R=0 WHERE DateTime='".date("Y-m-d",$thisDate)." 23:59:59'";
        echo "The database will now be updated with the following query: ".$newQuery."<br>";
        mysqli_query($con,$newQuery);
    }
    echo "Finished databaseFixV14.php; all OK";