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
	#	Saving user settings
	#
	# 	A script which saves the user specified units, colors, fonts
	# 	etc., all as a cookie.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	if($_GET['reset']!=1){
		$cookieFields = array();

		array_push($cookieFields,$_GET['design']);
		array_push($cookieFields,$_GET['design2']);
		
		$tempUnits = $_GET['tempUnits'];
		array_push($cookieFields,$tempUnits);
		$rainUnits = $_GET['rainUnits'];
		array_push($cookieFields,$rainUnits);
		$windUnits = $_GET['windUnits'];
		array_push($cookieFields,$windUnits);
		$pressureUnits = $_GET['pressureUnits'];
		array_push($cookieFields,$pressureUnits);
		$cloudbaseUnits = $_GET['cloudbaseUnits'];
		array_push($cookieFields,$cloudbaseUnits);
		$visibilityUnits = $_GET['visibilityUnits'];
		array_push($cookieFields,$visibilityUnits);
		
		$font = $designFont;
		array_push($cookieFields,$_GET['designFont']);
		$font2 = $designFont2;
		array_push($cookieFields,$_GET['designFont2']);
		
		array_push($cookieFields,$_GET['userLang']);

		$cookieText = implode(";",$cookieFields);

		setcookie('weatherTemplate', $cookieText, time() + (86400 * 30), "/");
	}
	else{
		unset($_COOKIE['weatherTemplate']);
		setcookie('weatherTemplate', null, -1, '/');
	}
?>