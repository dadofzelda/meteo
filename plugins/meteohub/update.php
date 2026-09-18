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
	#	Database update from Meteohub
	#
	# 	A script which updates and reads data from Meteohub
	#
	############################################################################
	#	Version and change log
	#
	# 	v1.0 - Jun 17, 2017	
    #       - initial release
    # 	v1.1 - Jun 21, 2017	
    #       - bug fixes
    # 	v2.0 - Sep 11, 2017	
    #       - added support for indoor temperature and humidity
	#
	############################################################################

	$base = "../../";
    
    // load main info
    require($base."config.php");

    // load dependencies
    require($base."scripts/functions.php");
    
    // check acces authorization
	$password = $_GET['pass'];
    
    // check if password is correct
    if($password!=$updatePassword){
        if($password==$adminPassword){ // if admin password provided accept, but notify
            echo "Authorized via admin password";
        }
        else{
            die("Unauthorized");
        }
    }
    
    ########### PARSE DATA TO API FORMAT ############
    
    $updateLog = array();
    $rawUpdate = array();
    
    // load settings
	if(!file_exists("settings.php")){
		die("The settings file does not exist! Please go to your control panel and set up the wlIP plugin.");
	}
	include("settings.php"); 

    // get data
    $rawData = trim(file_get_contents($clientrawURL));
    $rawData = explode(" ",$rawData);

    $rawUpdate['T'] = $rawData[4];
    $rawUpdate['H'] = $rawData[5];
    $rawUpdate['P'] = $rawData[6];
    $rawUpdate['B'] = $rawData[3];
    $rawUpdate['W'] = number_format(convertor($rawData[1],"kt","kmh"),1,".","");
    $rawUpdate['G'] = number_format(convertor($rawData[2],"kt","kmh"),1,".","");
    $rawUpdate['R'] = $rawData[7];
    $rawUpdate['RR'] = $rawData[10] * 60;
    if($rawData[34]!="-"){
        $rawUpdate['S'] = $rawData[34];
    }
    if($rawData[79]!="-"){
        $rawUpdate['UV'] = $rawData[79];
    }
    $rawUpdate['TIN'] = $rawData[12];
    $rawUpdate['HIN'] = $rawData[13];
    
    $dateField = explode("/",$rawData[74]);

    $rawUpdate["U"] = strtotime($dateField[2]."-".$dateField[1]."-".$dateField[0]." ".$rawData[29].":".$rawData[30].":".$rawData[31]);
    
    // call api.php
    $apiUpdate = "Meteohub";

    include_once($base."api.php");
?>