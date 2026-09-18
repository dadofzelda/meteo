<?php

	require("../config.php");
	$password = $_GET['password'];

	if($password!=$updatePassword){
		die("Incorrect password.");
	}

	// find all crons

	$files = glob($baseURL."load/crons/*.php");

	
	if(count($files)>0){
		foreach($files as $file){
			include($file); // load them
			echo "Loaded CRON: ".$file."<br>";
		}
	}



?>
