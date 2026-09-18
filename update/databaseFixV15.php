<?php

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	require("../config.php");
	
    $query = "ALTER TABLE  `alldataExtra` CHANGE  `UV`  `UV` DECIMAL( 3, 1 ) NOT NULL";

    mysqli_query($con,$query);
   
    echo "Finished databaseFixV15.php; all OK";