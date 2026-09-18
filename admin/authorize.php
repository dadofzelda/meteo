<?php

	require_once("../config.php");

	include("../scripts/functions.php");

	// determine IP
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$myIP = $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$myIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
		$myIP = $_SERVER['REMOTE_ADDR'];
	}

	// passwords match
	if($adminPassword==$_POST['password']){
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		// check if admin allowed 
		if(!isset($enableAdminIP)){ // user updated template and did not update setup file
			$enableAdminIP = false;
		}
		$allowedAccess = false;
		if($enableAdminIP){
			$allowedIPs = explode(",",$adminIPs);
			for($i=0;$i<count($allowedIPs);$i++){
				$thisIP = trim($allowedIPs[$i]);
				if (strpos($myIP, $thisIP) !== false) {
					$allowedAccess = true;
				}
			}
		}
		else{
			$allowedAccess = true;
		}
		if(!$allowedAccess){
			die("Unauthorized access");
		}
		$_SESSION['user'] = "admin";
		if(isset($_POST['keepLogged'])){
			if($_POST['keepLogged']=="on"){
				// we need PHP at least 5.4
				if(version_compare(PHP_VERSION, '5.3.7') >= 0) {
					// hash password
					require("hash.php");
					$password = $_POST['password'];
					$hash = password_hash($password, PASSWORD_BCRYPT);
					setcookie("meteotemplateAdmin", $hash, time() + 3600*24*365*10,'/'); // log in for 10 years ;)
				}
			}
		}
		header("Location: index.php");
	}

	// too bad ;)
	else{
		echo "Unauthorized access! Incorrect password specified.";
		die();
	}
?>
