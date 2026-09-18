<?php
	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	
	error_reporting(E_ALL);
	
	// load user settings
	foreach($_GET as $key=>$value){
		$parameters[trim($key)] = trim(urldecode($value));
	}
	
	$saveString = "<?php";
	
	$saveString .= PHP_EOL;
	$saveString .= PHP_EOL;
	
	foreach($parameters as $key=>$value){
		$saveString .= "$".$key." = '".$value."';";
		$saveString .= PHP_EOL;
	}
	
	$saveString .= PHP_EOL;
	
	$saveString .= "?>".PHP_EOL;
	
	if(file_exists("updateSettings.php")){
		unlink("updateSettings.php");
	}
	file_put_contents("updateSettings.php",$saveString);
	chmod("updateSettings.php",0777);
	
	// check file exists
	
	if(!file_exists("updateSettings.php")){
		echo "<script>alert('Update settings file could not be created! Check that permissions for the update folder are set correctly to write files in there!');close();</script>";
	}
	else{
		print "<script>alert('Update settings file created/updated.');close();</script>";
	}