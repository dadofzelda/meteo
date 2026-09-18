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
	#	Reset alerting
	#
	# 	This page resets alerts so they are again active.
	#
	############################################################################

	if(session_status() == PHP_SESSION_NONE){
		session_start();
	}

	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	if(file_exists("../update/alertSent.txt")){
		unlink("../update/alertSent.txt");
	}

	header('Location: index.php');

?>
