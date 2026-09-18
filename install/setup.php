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
	#	Main setup
	#
	# 	Main template setup page.
	#
	############################################################################

	$templateVersionCurrent = 19.0;
	$versionNameCurrent = "Cranberry";

	session_start();

	clearstatcache();
	// check PHP version
	$phpVersionOK = true;
	if(PHP_MAJOR_VERSION<5){
		$phpVersionOK = false;
	}
	else if(PHP_MAJOR_VERSION==5 && PHP_MINOR_VERSION<4){
		$phpVersionOK = false;
	}
	else{}

	$phpVersionUsed = PHP_VERSION;

	if($phpVersionOK==false){
		echo "<span style='color:red;font-size:1.6em;font-weight:bold'>Meteotemplate requires PHP 5.4 or higher. Your server uses version ".$phpVersionUsed.". See if it is possible to upgrade to a higher version. Your version is very old and does not support some major functions used by the template. Recommended is using at least version 5.4, which has been around for many years already. Version 5.3 should also be ok, but if you can try using 5.4.</span><br><br>";
	}

	$mySQLHost = "";
	$mySQLUser = "";
	$mySQLPassword = "";
	$mySQLName = "";

	if(file_exists("googleMapsKey.txt")){
		$googleMapsAPI = trim(file_get_contents("googleMapsKey.txt"));
		if($googleMapsAPI=="XXXXXX" || $googleMapsAPI==""){
			echo "<span style='color:red;font-size:1.6em;font-weight:bold'>Before you start this setup, it is necessary to get the Google Maps API key.</span><br><br>";
			echo "Go to https://developers.google.com/maps/documentation/javascript/get-api-key and create a new Browser API key. Make sure that for the 'referrers' you specify your domain, for example:<br><br>For meteotemplate.com I added these two referrers:<br>- *meteotemplate.com/*<br>- www.meteotemplate.com/*<br><br>This is so that your key can only be used on your domain. Then open the file 'googleMapsKey.txt' in the same directory of the template as this setup.php. Replace the XXXXXXX with your API key, save changes, upload the saved text file to your server to this install directory and then run this setup again. Also remember that the changes might take up to 5 minutes to have effect.<br><br>";

		}
	}
	else{
		echo "<span style='color:red;font-size:1.6em;font-weight:bold'>Unable to find the googleMapsKey.txt file with google maps api key. Make sure this text file is in the Install directory, if you do not have it there, re-download the main Meteotemplate file and you will find it there.</span>";
		echo "<br><br>It is now recommended you close this setup without saving any changes and setup your Google Maps API key. Otherwise not all features of this setup file will work and none of your maps in the template will work later on.<br><br>.";
	}

	if(file_exists("../config.php")){
		// this is not the first time we are running this script so require password
		if($_SESSION['user']!="admin"){
			echo "Unauthorized access. Please log in as an administrator first";
			die();
		}


		if(!is_writable("../config.php")){
			echo "<span style='color:red;font-size:1.6em;font-weight:bold'>Your existing config.php is not writable! Any changes you make now will not be saved! Check the permissions of config.php in the root of your template and make sure the file is writable so that changes in template set up can be made.</span><br><br>NOTE: If you have already changed the permission of the config file, the information about it not being writable could be cached by your server, in which case you can then ignore this message.";
		}
		$doNotLoadCookie = true;
		include("../config.php");
		$configFile = fopen("../config.php","r");
			while(!feof($configFile)) {
				$line = fgets($configFile);
				if(strpos($line,"con = mysqli_connect")!== false || strpos($line,"con=mysqli_connect")!== false){ // old and new syntax
					if(isset($connectionVariable)){}
					else{
						$connectionVariable = $line;
					}
				}
			}
			preg_match("/\((.*)\);/",$connectionVariable,$matches);
			$connectionVariableClean = $matches[1];
			$conVarExp = explode(",",$connectionVariableClean);
			$mySQLHost = str_replace('"','',$conVarExp[0]);
			$mySQLUser = str_replace('"','',$conVarExp[1]);
			$mySQLPassword = str_replace('"','',$conVarExp[2]);
			$mySQLHost = str_replace("'",'',$mySQLHost);
			$mySQLUser = str_replace("'",'',$mySQLUser);
			$mySQLPassword = str_replace("'",'',$mySQLPassword);
			$mySQLName = $dbName;
		fclose($configFile);
		$myTimezone = $stationTZ;
	}

	else{
		// first setup, do not require password and auto login as administrator
		$_SESSION['user'] = "admin";
	}

	if(!isset($adminPassword)){
		$adminPassword = "12345";
	}

	if(!isset($updatePassword)){
		$updatePassword = $adminPassword;
	}

	if(!isset($path)){
		$path = $_SERVER['PHP_SELF'];
		$path = str_replace("install/setup.php","",$path);
	}

	if(!isset($myTimezone)){
		$myTimezone = date_default_timezone_get();
	}
	if(!isset($pageURL)){
		$pageURL = $_SERVER['SERVER_NAME'];
	}

	if(!isset($pageURL)){
		$lang = "gb";
	}

	if(!isset($dataTempUnits)){
		$dataTempUnits = "C";
	}
	if(!isset($dataRainUnits)){
		$dataRainUnits = "mm";
	}
	if(!isset($dataWindUnits)){
		$dataWindUnits = "kmh";
	}
	if(!isset($dataPressUnits)){
		$dataPressUnits = "hpa";
	}

	if(!isset($displayTempUnits)){
		$displayTempUnits = "C";
	}
	if(!isset($displayRainUnits)){
		$displayRainUnits = "mm";
	}
	if(!isset($displayWindUnits)){
		$displayWindUnits = "kmh";
	}
	if(!isset($displayPressUnits)){
		$displayPressUnits = "hpa";
	}
	if(!isset($displayCloudbaseUnits)){
		$displayCloudbaseUnits = "m";
	}
	if(!isset($displayVisibilityUnits)){
		$displayVisibilityUnits = "km";
	}

	if(!isset($limitTempMin)){
		$limitTempMin = -100;
	}
	if(!isset($limitTempMax)){
		$limitTempMax = 100;
	}
	if(!isset($limitHumidityMin)){
		$limitHumidityMin = 0;
	}
	if(!isset($limitHumidityMax)){
		$limitHumidityMax = 100;
	}
	if(!isset($limitPressureMin)){
		$limitPressureMin = 950;
	}
	if(!isset($limitPressureMax)){
		$limitPressureMax = 1100;
	}
	if(!isset($limitRainMin)){
		$limitRainMin = 0;
	}
	if(!isset($limitRainMax)){
		$limitRainMax = 200;
	}
	if(!isset($limitRainRateMin)){
		$limitRainRateMin = 0;
	}
	if(!isset($limitRainRateMax)){
		$limitRainRateMax = 500;
	}
	if(!isset($limitBearingMin)){
		$limitBearingMin = 0;
	}
	if(!isset($limitBearingMax)){
		$limitBearingMax = 360;
	}
	if(!isset($limitWindMin)){
		$limitWindMin = 0;
	}
	if(!isset($limitWindMax)){
		$limitWindMax = 200;
	}
	if(!isset($limitSolarMin)){
		$limitSolarMin = 0;
	}
	if(!isset($limitSolarMax)){
		$limitSolarMax = 2000;
	}

	if(!isset($stationMETAR)){
		$stationMETAR = "XXXX";
	}

	if(!isset($pageAuthor)){
		$pageAuthor = "Meteotemplate";
	}
	if(!isset($pageName)){
		$pageName = "Meteotemplate";
	}
	if(!isset($pageDesc)){
		$pageDesc = "My weather page.";
	}
	if(!isset($stationModel)){
		$stationModel = "";
	}

	if(!isset($stationLat)){
		$stationLat = 0;
	}
	if(!isset($stationLocation)){
		$stationLocation = "";
	}
	if(!isset($stationCountry)){
		$stationCountry = "";
	}
	if(!isset($stationLon)){
		$stationLon = 0;
	}

	if(!isset($design)){
		$design = 'grey';
	}
	if(!isset($design2)){
		$design2 = 'brown';
	}
	if(!isset($designFont)){
		$designFont = 'PT Sans';
	}
	if(!isset($designFont2)){
		$designFont2 = 'Roboto';
	}
	if(!isset($gradient)){
		$gradient = '0';
	}

	if(!isset($dateFormat)){
		$dateFormat = "Y-m-d";
	}
	if(!isset($timeFormat)){
		$timeFormat = "H.i";
	}
	if(!isset($dateTimeFormat)){
		$dateTimeFormat = "Y-m-d H.i";
	}
	if(!isset($prefferedDate)){
		$prefferedDate = "M";
	}
	if(!isset($prefferedTime)){
		$prefferedTime = "24h";
	}

	if(!isset($bannerID)){
		$bannerID = "XXXXXXX";
	}

	if(!isset($paypalButtonCode)){
		$paypalButtonCode = "";
	}
	if(!isset($wuStationID)){
		$wuStationID = "";
	}
	if(!isset($awekasID)){
		$awekasID = "";
	}
	if(!isset($cwopID)){
		$cwopID = "";
	}
	if(!isset($weathercloudID)){
		$weathercloudID = "";
	}
	if(!isset($WOWMetofficeID)){
		$WOWMetofficeID = "";
	}
	if(!isset($pwsID)){
		$pwsID = "";
	}

	if(!isset($tempGaugeMin)){
		$tempGaugeMin = -30;
	}
	if(!isset($tempGaugeMax)){
		$tempGaugeMax = 50;
	}
	if(!isset($pressureGaugeMin)){
		$pressureGaugeMin = 960;
	}
	if(!isset($pressureGaugeMax)){
		$pressureGaugeMax = 1040;
	}
	if(!isset($windGaugeMin)){
		$windGaugeMin = 0;
	}
	if(!isset($gustGaugeMin)){
		$gustGaugeMin = 0;
	}
	if(!isset($windGaugeMax)){
		$windGaugeMax = 80;
	}
	if(!isset($gustGaugeMax)){
		$gustGaugeMax = 120;
	}
	if(!isset($rainGaugeMin)){
		$rainGaugeMin = 0;
	}
	if(!isset($rainGaugeMax)){
		$rainGaugeMax = 50;
	}
	if(!isset($solarGaugeMin)){
		$solarGaugeMin = 0;
	}
	if(!isset($solarGaugeMax)){
		$solarGaugeMax = 1400;
	}

	if(!isset($climateID)){
		$climateID = "";
	}

	if(!isset($WWOApiKey)){
		$WWOApiKey = "";
	}
	if(!isset($WWOLocation)){
		$WWOLocation = "";
	}
	if(!isset($WWOCacheTime)){
		$WWOCacheTime = "";
	}

	if(!isset($aerisID)){
		$aerisID = "";
	}
	if(!isset($aerisSecret)){
		$aerisSecret = "";
	}
	if(!isset($aerisCacheTime)){
		$aerisCacheTime = "";
	}

	if(!isset($GAcode)){
		$GAcode = "";
	}

	if(!isset($userCustomColor)){
		$userCustomColor = true;
	}
	if(!isset($userCustomFont)){
		$userCustomFont = true;
	}
	if(!isset($userCustomUnits)){
		$userCustomUnits = true;
	}
	if(!isset($userCustomLang)){
		$userCustomLang = true;
	}


	$mySQLStatus = false;

	if(isset($con)){
		if (mysqli_connect_errno()){
			$mySQLStatus = false;
		}
		else{
			$mySQLStatus = true;
		}
	}

	if(!isset($customBgColor1)){
		$customBgColor1 = '#000';
	}
	if(!isset($customBgColor2)){
		$customBgColor2 = '';
	}
	if(!isset($customBgColor3)){
		$customBgColor3 = '';
	}
	if(!isset($customBgColor4)){
		$customBgColor4 = '';
	}
	if(!isset($customBgType)){
		$customBgType = 'vertical';
	}
	if(!isset($customBgImg)){
		$customBgImg = '';
	}
	if(!isset($customMaxWidth)){
		$customMaxWidth = '1600px';
	}
	if(!isset($customBlockRadius)){
		$customBlockRadius = '0px';
	}
	if(!isset($customBlockBevel)){
		$customBlockBevel = '0px';
	}
	if(!isset($customBlockBorderWidth)){
		$customBlockBorderWidth = '0px';
	}
	if(!isset($customHeadingShadow)){
		$customHeadingShadow = 'none';
	}
	if(!isset($customBodyTextShadow)){
		$customBodyTextShadow = 'none';
	}
	if(!isset($headerLeftImg)){
		$headerLeftImg = 'flag';
	}
	if(!isset($customHeaderLeftImg)){
		$customHeaderLeftImg = '';
	}
	if(!isset($headerImg)){
		$headerImg = 'interactive';
	}
	if(!isset($customHeaderImg)){
		$customHeaderImg = '';
	}
	if(!isset($headerTitleSelect)){
		$headerTitleSelect = 'show';
	}
	if(!isset($headerTitleText)){
		$headerTitleText = 'Meteotemplate';
	}
	if(!isset($headerSubtitleSelect)){
		$headerSubtitleSelect = 'show';
	}
	if(!isset($headerSubtitleText)){
		$headerSubtitleText = 'Brno';
	}
	if(!isset($menuType)){
		$menuType = 'fixed';
	}
	if(!isset($menuSpeed)){
		$menuSpeed = '400';
	}
	if(!isset($customGlobalFontSize)){
		$customGlobalFontSize = '1.0em';
	}
	if(!isset($customGraphFontSize)){
		$customGraphFontSize = '12px';
	}
	if(!isset($customFooterDisplay)){
		$customFooterDisplay = 'dateTime';
	}
	if(!isset($customFooterText)){
		$customFooterText = '';
	}

	if(!isset($activateDebug)){
		$activateDebug = 0;
	}

	if(!isset($defaultGraphInterval)){
		$defaultGraphInterval = "today";
	}

	if(!isset($defaultGraphParameter)){
		$defaultGraphParameter = "T";
	}

	// version 7 - new

	if(!isset($stationWarnings)){
		$stationWarnings = true;
	}
	if(!isset($stationWarningsInterval)){
		$stationWarningsInterval = "interval 30 minute";
	}
	if(!isset($warningHighT)){
		$warningHighT = 30;
	}
	if(!isset($warningLowT)){
		$warningLowT = -5;
	}
	if(!isset($warningHighW)){
		$warningHighW = 30;
	}
	if(!isset($warningHighR)){
		$warningHighR = 20;
	}
	if(!isset($warningHighS)){
		$warningHighS = 700;
	}

	// version 8 - new
	if(!isset($alertActive)){
		$alertActive = false;
	}
	if(!isset($minimumAlertInterval)){
		$minimumAlertInterval = 60;
	}
	if(!isset($alertEmail)){
		$alertEmail = "youremail@email.com";
	}

	if(!isset($redirectMobiles)){
		$redirectMobiles = true;
	}
	if(!isset($redirectTablets)){
		$redirectTablets = true;
	}
	if(!isset($maxWidthMobile)){
		$maxWidthMobile = 1200;
	}
	if(!isset($minWidthDesktop)){
		$minWidthDesktop = 900;
	}

	if(!isset($stationElevation)){
		$stationElevation = 240;
	}
	if(!isset($stationElevationUnits)){
		$stationElevationUnits = "m";
	}

	// version 10 - new
	if(!isset($mobileHeaderImg)){
		$mobileHeaderImg = "";
	}

	if(!isset($customPageSearch)){
		$customPageSearch = false;
	}

	if(!isset($searchCode)){
		$searchCode = "";
	}

	if(!isset($headerConditions)){
		$headerConditions = true;
	}

	if(!isset($headerConditionsInterval)){
		$headerConditionsInterval = 5;
	}

	if(!isset($fIOKey)){
		$fIOKey = "";
	}

	if(!isset($fIOLanguage)){
		$fIOLanguage = "en";
	}

	// version 13 - new
	if(!isset($stationState)){
		$stationState = "";
	}

	if(!isset($hideAdminEntrance)){
		$hideAdminEntrance = false;
	}

	if(!isset($highlightMenuHover)){
		$highlightMenuHover = false;
	}

	if(!isset($flagIconShape)){
		$flagIconShape = "flags";
	}

	if(!isset($hideHelpOpener)){
		$hideHelpOpener = false;
	}

	if(!isset($defaultPaperSize)){
		$defaultPaperSize = "A4";
	}

	if(!isset($stationIcon)){
		$stationIcon = "station";
	}

	if(!isset($hideMultipleBlockBorder)){
		$hideMultipleBlockBorder = false;
	}

	if(!isset($flatDesignDesktop)){
		$flatDesignDesktop = false;
	}
	if(!isset($flatDesignMobile)){
		$flatDesignMobile = false;
	}
	if(!isset($blockMaximizeDesktop)){
		$blockMaximizeDesktop = true;
	}
	if(!isset($blockMaximizeMobile)){
		$blockMaximizeMobile = true;
	}
	if(!isset($blockExportDesktop)){
		$blockExportDesktop = true;
	}
	if(!isset($blockExportMobile)){
		$blockExportMobile = true;
	}

	if(!isset($templateUpdateCheck)){
		$templateUpdateCheck = true;
	}

	if(!isset($titleSmallCaps)){
		$titleSmallCaps = true;
	}

	if(!isset($titleBoldText)){
		$titleBoldText = false;
	}

	if(!isset($subtitleSmallCaps)){
		$subtitleSmallCaps = true;
	}

	if(!isset($subtitleBoldText)){
		$subtitleBoldText = false;
	}

	if(!isset($menuLinksUpper)){
		$menuLinksUpper = true;
	}

	if($displayRainUnits=="cm"){
		$displayRainUnits = "mm";
	}

	if(!isset($addSharer)){
		$addSharer = true;
	}

	if(!isset($moreLinkHighlight)){
		$moreLinkHighlight = false;
	}

	if(!isset($footerSeasonImages)){
		$footerSeasonImages = true;
	}

	if(!isset($footerSeasonImagesType)){
		$footerSeasonImagesType = "astro";
	}

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

	// version 15
	if(!isset($graphTimeFormat)){
		$graphTimeFormat = "%H.%M";
	}
	if(!isset($graphDateFormat)){
		$graphDateFormat = "%d. %m";
	}
	if(!isset($enableAdminIP)){
		$enableAdminIP = false;
	}
	if(!isset($adminIPs)){
		$adminIPs = $myIP;
	}
	if(!isset($apiRRCalculation)){
		$apiRRCalculation = "avg";
	}
	if(!isset($showFooterStationStatus)){
		$showFooterStationStatus = false;
	}

	// version 16
	if(!isset($areaNormalsTUnits)){
		$areaNormalsTUnits = "C";
	}
	if(!isset($areaNormalsT)){
		$areaNormalsT = "-2.1;0.1;4.2;9.1;13.9;16.9;18.6;18.4;14.8;9.7;4;-0.3";
	}
	if(!isset($areaNormalsRUnits)){ 
		$areaNormalsRUnits = "mm";
	}
	if(!isset($areaNormalsR)){
		$areaNormalsR = "25;24;26;34;63;77;74;64;43;34;38;30";
	}
	if(!isset($enableKeyboard)){
		$enableKeyboard = false;
	}
	if(!isset($multiplierT)){
		$multiplierT = 0;
	}
	if(!isset($multiplierR)){
		$multiplierR = 1;
	}
	if(!isset($multiplierP)){
		$multiplierP = 0;
	}
	if(!isset($multiplierW)){
		$multiplierW = 1;
	}

	// version 17.2
	if(!isset($cookieNotice)){
		$cookieNotice = false;
	}
	if(!isset($wuStationAPI)){
		$wuStationAPI = "";
	}

	// previously enabled, check
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
		die("Access denied.");
	}
	

	include("../css/design.php");

	$availableTimezones = array("UTC","Africa/Abidjan","Africa/Accra","Africa/Addis_Ababa","Africa/Algiers","Africa/Asmara","Africa/Asmera","Africa/Bamako","Africa/Bangui","Africa/Banjul","Africa/Bissau","Africa/Blantyre","Africa/Brazzaville","Africa/Bujumbura","Africa/Cairo","Africa/Casablanca","Africa/Ceuta","Africa/Conakry","Africa/Dakar","Africa/Dar_es_Salaam","Africa/Djibouti","Africa/Douala","Africa/El_Aaiun","Africa/Freetown","Africa/Gaborone","Africa/Harare","Africa/Johannesburg","Africa/Juba","Africa/Kampala","Africa/Khartoum","Africa/Kigali","Africa/Kinshasa","Africa/Lagos","Africa/Libreville","Africa/Lome","Africa/Luanda","Africa/Lubumbashi","Africa/Lusaka","Africa/Malabo","Africa/Maputo","Africa/Maseru","Africa/Mbabane","Africa/Mogadishu","Africa/Monrovia","Africa/Nairobi","Africa/Ndjamena","Africa/Niamey","Africa/Nouakchott","Africa/Ouagadougou","Africa/Porto-Novo","Africa/Sao_Tome","Africa/Timbuktu","Africa/Tripoli","Africa/Tunis","Africa/Windhoek","America/Adak","America/Anchorage","America/Anguilla","America/Antigua","America/Araguaina","America/Argentina/Buenos_Aires","America/Argentina/Catamarca","America/Argentina/ComodRivadavia","America/Argentina/Cordoba","America/Argentina/Jujuy","America/Argentina/La_Rioja","America/Argentina/Mendoza","America/Argentina/Rio_Gallegos","America/Argentina/Salta","America/Argentina/San_Juan","America/Argentina/San_Luis","America/Argentina/Tucuman","America/Argentina/Ushuaia","America/Aruba","America/Asuncion","America/Atikokan","America/Atka","America/Bahia","America/Bahia_Banderas","America/Barbados","America/Belem","America/Belize","America/Blanc-Sablon","America/Boa_Vista","America/Bogota","America/Boise","America/Buenos_Aires","America/Cambridge_Bay","America/Campo_Grande","America/Cancun","America/Caracas","America/Catamarca","America/Cayenne","America/Cayman","America/Chicago","America/Chihuahua","America/Coral_Harbour","America/Cordoba","America/Costa_Rica","America/Creston","America/Cuiaba","America/Curacao","America/Danmarkshavn","America/Dawson","America/Dawson_Creek","America/Denver","America/Detroit","America/Dominica","America/Edmonton","America/Eirunepe","America/El_Salvador","America/Ensenada","America/Fort_Wayne","America/Fortaleza","America/Glace_Bay","America/Godthab","America/Goose_Bay","America/Grand_Turk","America/Grenada","America/Guadeloupe","America/Guatemala","America/Guayaquil","America/Guyana","America/Halifax","America/Havana","America/Hermosillo","America/Indiana/Indianapolis","America/Indiana/Knox","America/Indiana/Marengo","America/Indiana/Petersburg","America/Indiana/Tell_City","America/Indiana/Vevay","America/Indiana/Vincennes","America/Indiana/Winamac","America/Indianapolis","America/Inuvik","America/Iqaluit","America/Jamaica","America/Jujuy","America/Juneau","America/Kentucky/Louisville","America/Kentucky/Monticello","America/Knox_IN","America/Kralendijk","America/La_Paz","America/Lima","America/Los_Angeles","America/Louisville","America/Lower_Princes","America/Maceio","America/Managua","America/Manaus","America/Marigot","America/Martinique","America/Matamoros","America/Mazatlan","America/Mendoza","America/Menominee","America/Merida","America/Metlakatla","America/Mexico_City","America/Miquelon","America/Moncton","America/Monterrey","America/Montevideo","America/Montreal","America/Montserrat","America/Nassau","America/New_York","America/Nipigon","America/Nome","America/Noronha","America/North_Dakota/Beulah","America/North_Dakota/Center","America/North_Dakota/New_Salem","America/Ojinaga","America/Panama","America/Pangnirtung","America/Paramaribo","America/Phoenix","America/Port-au-Prince","America/Port_of_Spain","America/Porto_Acre","America/Porto_Velho","America/Puerto_Rico","America/Rainy_River","America/Rankin_Inlet","America/Recife","America/Regina","America/Resolute","America/Rio_Branco","America/Rosario","America/Santa_Isabel","America/Santarem","America/Santiago","America/Santo_Domingo","America/Sao_Paulo","America/Scoresbysund","America/Shiprock","America/Sitka","America/St_Barthelemy","America/St_Johns","America/St_Kitts","America/St_Lucia","America/St_Thomas","America/St_Vincent","America/Swift_Current","America/Tegucigalpa","America/Thule","America/Thunder_Bay","America/Tijuana","America/Toronto","America/Tortola","America/Vancouver","America/Virgin","America/Whitehorse","America/Winnipeg","America/Yakutat","America/Yellowknife","Asia/Aden","Asia/Almaty","Asia/Amman","Asia/Anadyr","Asia/Aqtau","Asia/Aqtobe","Asia/Ashgabat","Asia/Ashkhabad","Asia/Baghdad","Asia/Bahrain","Asia/Baku","Asia/Bangkok","Asia/Beirut","Asia/Bishkek","Asia/Brunei","Asia/Calcutta","Asia/Choibalsan","Asia/Chongqing","Asia/Chungking","Asia/Colombo","Asia/Dacca","Asia/Damascus","Asia/Dhaka","Asia/Dili","Asia/Dubai","Asia/Dushanbe","Asia/Gaza","Asia/Harbin","Asia/Hebron","Asia/Ho_Chi_Minh","Asia/Hong_Kong","Asia/Hovd","Asia/Irkutsk","Asia/Istanbul","Asia/Jakarta","Asia/Jayapura","Asia/Jerusalem","Asia/Kabul","Asia/Kamchatka","Asia/Karachi","Asia/Kashgar","Asia/Kathmandu","Asia/Katmandu","Asia/Khandyga","Asia/Kolkata","Asia/Krasnoyarsk","Asia/Kuala_Lumpur","Asia/Kuching","Asia/Kuwait","Asia/Macao","Asia/Macau","Asia/Magadan","Asia/Makassar","Asia/Manila","Asia/Muscat","Asia/Nicosia","Asia/Novokuznetsk","Asia/Novosibirsk","Asia/Omsk","Asia/Oral","Asia/Phnom_Penh","Asia/Pontianak","Asia/Pyongyang","Asia/Qatar","Asia/Qyzylorda","Asia/Rangoon","Asia/Riyadh","Asia/Saigon","Asia/Sakhalin","Asia/Samarkand","Asia/Seoul","Asia/Shanghai","Asia/Singapore","Asia/Taipei","Asia/Tashkent","Asia/Tbilisi","Asia/Tehran","Asia/Tel_Aviv","Asia/Thimbu","Asia/Thimphu","Asia/Tokyo","Asia/Ujung_Pandang","Asia/Ulaanbaatar","Asia/Ulan_Bator","Asia/Urumqi","Asia/Ust-Nera","Asia/Vientiane","Asia/Vladivostok","Asia/Yakutsk","Asia/Yekaterinburg","Asia/Yerevan","Atlantic/Azores","Atlantic/Bermuda","Atlantic/Canary","Atlantic/Cape_Verde","Atlantic/Faeroe","Atlantic/Faroe","Atlantic/Jan_Mayen","Atlantic/Madeira","Atlantic/Reykjavik","Atlantic/South_Georgia","Atlantic/St_Helena","Atlantic/Stanley","Australia/ACT","Australia/Adelaide","Australia/Brisbane","Australia/Broken_Hill","Australia/Canberra","Australia/Currie","Australia/Darwin","Australia/Eucla","Australia/Hobart","Australia/LHI","Australia/Lindeman","Australia/Lord_Howe","Australia/Melbourne","Australia/North","Australia/NSW","Australia/Perth","Australia/Queensland","Australia/South","Australia/Sydney","Australia/Tasmania","Australia/Victoria","Australia/West","Australia/Yancowinna","Europe/Amsterdam","Europe/Andorra","Europe/Athens","Europe/Belfast","Europe/Belgrade","Europe/Berlin","Europe/Bratislava","Europe/Brussels","Europe/Bucharest","Europe/Budapest","Europe/Busingen","Europe/Chisinau","Europe/Copenhagen","Europe/Dublin","Europe/Gibraltar","Europe/Guernsey","Europe/Helsinki","Europe/Isle_of_Man","Europe/Istanbul","Europe/Jersey","Europe/Kaliningrad","Europe/Kiev","Europe/Lisbon","Europe/Ljubljana","Europe/London","Europe/Luxembourg","Europe/Madrid","Europe/Malta","Europe/Mariehamn","Europe/Minsk","Europe/Monaco","Europe/Moscow","Europe/Nicosia","Europe/Oslo","Europe/Paris","Europe/Podgorica","Europe/Prague","Europe/Riga","Europe/Rome","Europe/Samara","Europe/San_Marino","Europe/Sarajevo","Europe/Simferopol","Europe/Skopje","Europe/Sofia","Europe/Stockholm","Europe/Tallinn","Europe/Tirane","Europe/Tiraspol","Europe/Uzhgorod","Europe/Vaduz","Europe/Vatican","Europe/Vienna","Europe/Vilnius","Europe/Volgograd","Europe/Warsaw","Europe/Zagreb","Europe/Zaporozhye","Europe/Zurich","Indian/Antananarivo","Indian/Chagos","Indian/Christmas","Indian/Cocos","Indian/Comoro","Indian/Kerguelen","Indian/Mahe","Indian/Maldives","Indian/Mauritius","Indian/Mayotte","Indian/Reunion","Pacific/Apia","Pacific/Auckland","Pacific/Chatham","Pacific/Chuuk","Pacific/Easter","Pacific/Efate","Pacific/Enderbury","Pacific/Fakaofo","Pacific/Fiji","Pacific/Funafuti","Pacific/Galapagos","Pacific/Gambier","Pacific/Guadalcanal","Pacific/Guam","Pacific/Honolulu","Pacific/Johnston","Pacific/Kiritimati","Pacific/Kosrae","Pacific/Kwajalein","Pacific/Majuro","Pacific/Marquesas","Pacific/Midway","Pacific/Nauru","Pacific/Niue","Pacific/Norfolk","Pacific/Noumea","Pacific/Pago_Pago","Pacific/Palau","Pacific/Pitcairn","Pacific/Pohnpei","Pacific/Ponape","Pacific/Port_Moresby","Pacific/Rarotonga","Pacific/Saipan","Pacific/Samoa","Pacific/Tahiti","Pacific/Tarawa","Pacific/Tongatapu","Pacific/Truk","Pacific/Wake","Pacific/Wallis","Pacific/Yap");

	sort($availableTimezones);

	$availableSchemes = array();
	foreach( array_keys( $color_schemes ) as $index=>$key ) {
		array_push($availableSchemes,$key);
	}

	$availableFonts = array("PT Sans","Roboto","Dosis","Ubuntu","Lobster","Kaushan Script","Open Sans","Play","Open Sans Condensed","Anton","Arial","Tahoma","Times New Roman","Helvetica","Inconsolata",'Righteous','Marck Script','Poiret One','Cutive Mono','Patrick Hand SC','Rubik','Lato','Raleway');

	$stationModels = array("Davis Vantage Pro 2","Davis Vantage Pro 2 Plus","Davis Vantage Vue","Oregon WMR968","Oregon WMR928","Oregon WMR200","Oregon WMR300","Oregon WMRS200","Oregon LW301","Oregon WMR180","Metron UWS 3000","Oregon WMR89","Oregon WMR88","Oregon WMR86","Meade TE923","Meade TE827","Meade TE821","Meade DV928","Ambient WS1090","Ambient WS2080","Ambient WS1400","Ambient WS1200","Ambient WS1001","Ambient 6357","Ambient 6250","Ambient 6251","Ambient 6322","Ambient 6152","Ambient WS2095","Ambient 6153","Ambient 6162","Ambient 6163","Peet Bros Ultimeter 100","Peet Bros Ultimeter 800","Peet Bros Ultimeter 2100","Peet Bros Ultimeter 500","Peet Bros Ultimeter 2000","Peet Bros Ultimeter 2100","Peet Bros Ultimeter II","Texas Instruments WR-25","Vaisala WXT510","Vaisala WXT520","","Rainwise MkIII","Lufft WS600","Lufft WS601","Acurite 1025","Acurite 1035","Acurite 1525","Acurite 1512","Acurite 1524","Acurite Pro 5-in-1","LaCrosse WS2300","LaCrosse WS300PC","LaCrosse WS444","LaCrosse WS500","LaCrosse WS550","LaCrosse WS777","LaCrosse WS888","LaCrosse WDC7000","LaCrosse WS1516","LaCrosse WS1517","LaCrosse WS1912","LaCrosse WS1913","LaCrosse WS2815","LaCrosse WS2810","LaCrosse WS2811","Fine Offset WH2310","Fine Offset WH2600","Fine Offset HP1000","Fine Offset HP1003","Fine Offset WH3081","Fine Offset WH3080","Fine Offset WS1080","Fine Offset WH1080","Fine Offset WH1081","Fine Offset WH1090","Fine Offset WH1091","Fine Offset WA2080","Fine Offset WA2081","Fine Offset WH2080","Fine Offset WH2081","Fine Offset WH5301","Fine Offset WH5300","Fine Offset WH1050","Fine Offset WG2071","Fine Offset WH2000","Fine Offset WH2001","Fine Offset WH2070","Fine Offset WH1070","NetAtmo");
	sort($stationModels);

	foreach(glob('../imgs/forecastImgs/weather_bg/*.*') as $file) {
		$availableBgs [] = $file;
	}

	// load langauges
	foreach(glob('../lang/*.*') as $file) {
		$availableLangs [] = substr($file,-6,2);
	}

	$stationLat = round($stationLat,5);
	$stationLon = round($stationLon,5);

	$colorThemeGradients = array();
	$colorThemeGradients[] = array("titanium","#283048","#859398","","","Titanium");
	$colorThemeGradients[] = array("midnight","#232526","#414345","","","Midnight");
	$colorThemeGradients[] = array("mystic","#757F9A","#D7DDE8","","","Mystic");
	$colorThemeGradients[] = array("moss","#134E5E","#71B280","","","");
	$colorThemeGradients[] = array("electricViolet","#4776E6","#8E54E9","","","Electric Violet");
	$colorThemeGradients[] = array("kashmir","#614385","#516395","","","Kashmir");
	$colorThemeGradients[] = array("steelGrey","#1F1C2C","#928DAB","","","Steel Grey");
	$colorThemeGradients[] = array("mirage","#16222A","#3A6073","","","Mirage");
	$colorThemeGradients[] = array("juicyOrange","#FF8008","#FFC837","","","Juicy Orange");
	$colorThemeGradients[] = array("mojito","#1D976C","#93F9B9","","","Mojito");
	$colorThemeGradients[] = array("cherry","#EB3349","#F45C43","","","Cherry");
	$colorThemeGradients[] = array("pinky","#DD5E89","#F7BB97","","","Pinky");
	$colorThemeGradients[] = array("sea","#4CB8C4","#3CD3AD","","","Sea");
	$colorThemeGradients[] = array("magicPurple","#1D2B64","#F8CDDA","","","Magic Purple");
	$colorThemeGradients[] = array("sunrise","#FF512F","#F09819","","","Sunrise");
	$colorThemeGradients[] = array("aquaMarine","#1A2980","#26D0CE","","","Aqua Marine");
	$colorThemeGradients[] = array("aubergine","#AA076B","#61045F","","","Aubergine");
	$colorThemeGradients[] = array("bloodyMary","#FF512F","#DD2476","","","Bloody Mary");
	$colorThemeGradients[] = array("mango","#F09819","#EDDE5D","","","Mango");
	$colorThemeGradients[] = array("mistyGrey","#403B4A","#E7E9BB","","","Misty Grey");
	$colorThemeGradients[] = array("roseWater","#E55D87","#5FC3E4","","","Rose Water");
	$colorThemeGradients[] = array("horizon","#003973","#E5E5BE","","","Horizon");
	$colorThemeGradients[] = array("lemonTwist","#3CA55C","#B5AC49","","","Lemon Twist");
	$colorThemeGradients[] = array("emeraldWater","#348F50","#56B4D3","","","Emerald Water");
	$colorThemeGradients[] = array("intuitive","#DA22FF","#9733EE","","","Intuitive");
	$colorThemeGradients[] = array("sunny","#EDE574","#E1F5C4","","","Sunny");
	$colorThemeGradients[] = array("harmonic","#16A085","#F4D03F","","","Harmonic");
	$colorThemeGradients[] = array("tranquil","#EECDA3","#EF629F","","","Tranquil");
	$colorThemeGradients[] = array("magicRed","#1D4350","#A43931","","","Magic Red");
	$colorThemeGradients[] = array("energy","#f7ff00","#db36a4","","","Energy");
	$colorThemeGradients[] = array("snowy","#E0EAFC","#CFDEF3","","","Snowy");
	$colorThemeGradients[] = array("decent","#4CA1AF","#C4E0E5","","","Decent");
	$colorThemeGradients[] = array("dark","#000000","#434343","","","Dark");
	$colorThemeGradients[] = array("darkSky","#4B79A1","#283E51","","","Dark Sky");
	$colorThemeGradients[] = array("superman","#0099F7","#F11712","","","Superman");
	$colorThemeGradients[] = array("nighthawk","#2980b9","#2c3e50","","","Nighthawk");
	$colorThemeGradients[] = array("forest","#5A3F37","#2C7744","","","Forest");
	$colorThemeGradients[] = array("paleWood","#eacda3","#d6ae7b","","","Pale Wood");
	$colorThemeGradients[] = array("blush","#B24592","#F15F79","","","Blush");
	$colorThemeGradients[] = array("poncho","#403A3E","#BE5869","","","Poncho");
	$colorThemeGradients[] = array("leaf","#76b852","#8DC26F","","","Leaf");
	$colorThemeGradients[] = array("sage","#CCCCB2","#757519","","","Sage");
	$colorThemeGradients[] = array("darkGreen","#6A9113","#141517","","","Dark Green");
	$colorThemeGradients[] = array("caramel","#D1913C","#FFD194","","","Caramel");
	$colorThemeGradients[] = array("turquoise","#136a8a","#267871","","","Turquoise");
	$colorThemeGradients[] = array("dawn","#FFA17F","#00223E","","","Dawn");
	$colorThemeGradients[] = array("magicViolet","#948E99","#2E1437","","","Magical Violet");
	$colorThemeGradients[] = array("shore","#70e1f5","#ffd194","","","Shore");
	$colorThemeGradients[] = array("amethyst","#9D50BB","#6E48AA","","","Amethyst");
	$colorThemeGradients[] = array("park","#ADD100","#7B920A","","","Park");
	$colorThemeGradients[] = array("cherryBlossom","#FBD3E9","#BB377D","","","Cherry Blossom");
	$colorThemeGradients[] = array("electricGreen","#008000","#ffff00","#008000","","Electric Green");
	$colorThemeGradients[] = array("fire","#ff0000","#ffff00","#ff0000","","Fire");
	$colorThemeGradients[] = array("darkRed","#4d0000","#ff0000","#4d0000","","Dark Red");
	$colorThemeGradients[] = array("silver","#000000","#ffffff","#000000","","Silver");
	$colorThemeGradients[] = array("energyRed","#000000","#ff0000","#000000","","Energy Red");
	$colorThemeGradients[] = array("chrome","#808080","#ffffff","#808080","","Chrome");

	function sorter($a, $b) {
		 return $a[5] > $b[5];
	}

	usort($colorThemeGradients, "sorter");

	$charactersPass = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHJKMNPQRSTUVWXYZ';
	for($ij=0;$ij<5;$ij++){
		$passLength = rand(8,15);
        $passGen = '';
        $maxPass = strlen($charactersPass) - 1;
        for ($i = 0; $i < $passLength; $i++) {
            $passGen .= $charactersPass[mt_rand(0, $maxPass)];
		}
		$passwordsGen[] = $passGen;
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Meteotemplate Setup</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="//jquery-ui.googlecode.com/svn/tags/1.8.2/themes/vader/jquery-ui.css">
		<script type="text/javascript" src="//maps.google.com/maps/api/js?sensor=false&key=<?php echo $googleMapsAPI?>"></script>
		<script type="text/javascript" src="../scripts/jquery.min.js"></script>
		<script type="text/javascript" src="../scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="../scripts/jquery.tooltipster.js"></script>
		<script src="../scripts/geolocation.js"></script>
		<script src="../scripts/jquery.geolocation.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/jquery.form-validator.min.js"></script>
		<script src="../scripts/colorpicker.js" type="text/javascript"></script>
		<link href="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.2.8/theme-default.min.css" rel="stylesheet" type="text/css" />
		<link href="../css/evol-colorpicker.min.css" rel="stylesheet" />
		<link rel="stylesheet" href="../css/font/styles.css">
		<link rel="stylesheet" href="../css/fontAwesome/css/font-awesome.min.css">
		<style>
			@import url(http://fonts.googleapis.com/css?family=Bree+Serif);
			@import url(http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic-ext,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin,cyrillic-ext,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Play:400,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Anton&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Inconsolata:400,700&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Righteous&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Marck+Script&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Poiret+One&subset=latin,latin-ext);
			@import url(http://fonts.googleapis.com/css?family=Cutive+Mono&subset=latin,latin-ext);
			@import url(https://fonts.googleapis.com/css?family=Patrick+Hand+SC&subset=latin-ext);
			body{
				background: black;
				color: white;
				font-family: 'PT Sans';
				padding: 5px;
			}
			h1{
				text-align:center;
			}
			#mapCanvas {
				width: 98%;
				height: 500px;
				margin: 0 auto;
				border: 1px solid white;
				border-radius: 10px;
			}
			.exampleIcon{
				width:30px;
				padding: 2px;
			}
			.exampleIconSmall{
				width: 15px;
				padding: 1px;
				padding-top:3px;
				padding-bottom:3px;
			}
			.langIcon{
				width: 30px;
				opacity: 0.7;
				cursor: pointer;
			}
			.langIcon:hover{
				opacity: 1;
			}
			.sectionDiv{
				background: #333333;
				padding: 20px;
				width:95%;
				max-width: 1600px;
				border-radius: 10px;
				margin: 0 auto;
				margin-bottom:10px;
				margin-top:10px;
				text-align: justify;
			}
			.exampleBlock{
				margin: 0 auto;
				width: 100%;
				text-align: center;
				background: #<?php echo $color_schemes[$design]['900']?>;
				<?php
					if($gradient){
				?>
						background-image: -ms-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
						background-image: -moz-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
						background-image: -o-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
						background-image: -webkit-gradient(linear, left top, left bottom, color-stop(0, #<?php echo $color_schemes[$design]['900']?>), color-stop(50, #<?php echo $color_schemes[$design]['800']?>), color-stop(100, #<?php echo $color_schemes[$design]['900']?>));
						background-image: -webkit-linear-gradient(top, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
						background-image: linear-gradient(to bottom, #<?php echo $color_schemes[$design]['900']?> 0%, #<?php echo $color_schemes[$design]['800']?> 50%, #<?php echo $color_schemes[$design]['900']?> 100%);
				<?php
					}
				?>
			}
			input{
				background:#666666;
				border: 0px solid white;
				padding: 3px;
				color: white;
			}
			select{
				background:#666666;
				border: 0px solid white;
				padding: 3px;
				color: white;
			}
			.term{
				font-weight: bold;
				font-size:0.8em;
				font-family: "Lucida Console", Monaco, monospace;
			}
			a {
				text-decoration: none;
				color:#ff9999;
				font-variant: small-caps;
			}
			a:link {
				color:#ff9999;
				font-variant: small-caps;
			}
			a:visited {
				color:#ff9999;
			}
			a:hover {
				-webkit-transition: all .5s ease;
				-moz-transition: all .5s ease;
				-o-transition: all .5s ease;
				transition: all .5s ease;
				color: #ff4c4d;
			}
			#submitBtn {
			  background: #d93434;
			  background-image: -webkit-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -moz-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -ms-linear-gradient(top, #d93434, #b82b2b);
			  background-image: -o-linear-gradient(top, #d93434, #b82b2b);
			  background-image: linear-gradient(to bottom, #d93434, #b82b2b);
			  -webkit-border-radius: 9;
			  -moz-border-radius: 9;
			  border-radius: 9px;
			  font-family: Arial;
			  color: #ffffff;
			  font-size: 20px;
			  padding: 10px 20px 10px 20px;
			  text-decoration: none;
			  cursor: pointer;
			}

			#submitBtn:hover {
			  background: #fc3c3c;
			  background-image: -webkit-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -moz-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -ms-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: -o-linear-gradient(top, #fc3c3c, #d93434);
			  background-image: linear-gradient(to bottom, #fc3c3c, #d93434);
			  text-decoration: none;
			}
			#testMySQL{
				opacity:0.8;
			}
			#testMySQL:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main">
			<form name="configForm" action="createConfig.php" method="post" id="configForm" onsubmit="return validateForm()" target="_blank">
			<div class="textDiv">
				<h1>Meteotemplate Settings</h1>
				<br>
				<div class="sectionDiv">
					<h3>Server Check</h3>
					<p>
						Click the button below to perform server check. This will check some of the crucial components required for using Meteotemplate. If you get an error for any of the checked items, you need to fix this otherwise the template will not work as expected or not work at all.<br><br>
						<input type="button" class="button" value="Server Check" onclick="window.open('serverCheck.php')"></input>
					</p>
				</div>
				<div class="sectionDiv">
					<h3>Admin and Update Password</h3>
					<p>
						You need to create a password for the template. Remember - this has nothing to do with any of your other passwords for FTP access or MySQL access. You can of course set the same password, but I do not recommend it. However, no matter what you choose here, make sure that this is then the password you will use when setting up the updates and it is also the password that will be used for accessing the administration section of the template. You will learn about this later in the set up process. For now just set a password and remember that this is the password referred to in the documentation as <span class="term">admin password</span>.
					</p>
					<p>
						The admin password is used to access a password-protected section of your webpage, which also includes this setup page, which is only accessible without a password the first time you set up your template and password. There is also a password required for the update of the database. Now, you might be wondering why there are two different passwords. Theoretically you can use the same password for both. However, the admin password is much better protected. Knowing this password would potentially allow someone to do basically anything with your site. The update password, however, is only to authorize the updates. Due to the way these updates work technically, it is not possible to protect this so well and for example in your CRON job or Meteobridge control panel, this password is visible in the URL in a normal unencrypted form. It might therefore be a good idea to make sure that especially your admin password is strong and different from the udpate password. By default, the update password is set to the same as your admin, so if this is first installation, also 12345.
					</p>
					<p>
						NOTE! If you have been using Meteotemplate in earlier versions, there was only one password. In such case, I highly recommend you change the admin password, not the udpate password. You can of course choose to change the update, or both, but it will be easier to change the admin password. IMPORTANT! - any time you change the update password you need to also make this change in the update URL in whatever means of update you use! So, for example, if you use a CRON job, you will need to change the password parameter in the URL for password. Likewise, if you are using Meteobridge, you will need to change this in your Meteobridge control panel. The update scripts always check the update password and if it does not match the one specified here, the update will not be performed.
					</p>
					<p>
						Only use alphanumeric symbols for the password. The password is case-sensitive, but do not use symbols such as ', ; etc.<br>
						<br>
						<input type="button" value="Generate Passwords" onclick="$('#passDiv').slideToggle()" style="padding:3px">
						<div style="width:100%;display:none" id="passDiv">
							Here are some passwords you can use, do not use the same password for both admin and update, this would create a major security vulnerability.<br>
							<?php echo implode("<br>", $passwordsGen)?>
						</div>
					</p>
					<table>
						<tr>
							<td>
								Current admin password
							</td>
							<td>
								<input type="password" id="adminPassword" name="adminPassword" value="<?php echo $adminPassword?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								New admin password
							</td>
							<td>
								<input type="password" id="adminPasswordNew" name="adminPasswordNew" value="" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Update password
							</td>
							<td>
								<input type="password" id="updatePassword" name="updatePassword" value="<?php echo $updatePassword?>" autocomplete="off">
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
					<h3>Google Maps API key</h3>
					<p>This key is loaded from the text file with the Google maps API (see wiki).</p>
					<table>
						<tr>
							<td>
								Google Maps API key
							</td>
							<td>
								<input id="googleMapsAPIKey" name="googleMapsAPIKey" value="<?php echo $googleMapsAPI?>" size="70" readonly>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
				<h3>MySQL Database</h3>
				<p>
					This section is absolutely crucial. These parameters are used for connecting to your MySQL database, which is necessary for both: being able to update your database and store new data by the updating scripts, as well as then all the other scripts in the template to be able to read from the database. These parameters should be provided to you by your webhosting provider or if you have your own server, they will depend on how you have set it up.
				</p>
				<p>
					After you have inserted all four MySQL connection parameters, click the Test button and make sure you get a message that the connection was established. If not, it will not work.
				</p>
					<table>
						<tr>
							<td>
								Host address
							</td>
							<td>
								<input id="mySQLHost" name="mySQLHost" value="<?php echo $mySQLHost?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								User
							</td>
							<td>
								<input id="mySQLUser" name="mySQLUser" value="<?php echo $mySQLUser?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Password
							</td>
							<td>
								<input id="mySQLPassword" name="mySQLPassword" value="<?php echo $mySQLPassword?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Database name
							</td>
							<td>
								<input id="mySQLName" name="mySQLName" value="<?php echo $mySQLName?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
					</table>
					<br>
					<input type="button" value="Test Connection" id="testMySQL" style="border:1px solid white;padding:6px;cursor:pointer" autocomplete="off">
					<?php
						if($mySQLStatus){
							echo "Your MySQL is currently set up correctly.";
						}
					?>
					<br>
					<p>
					If this is the very first time you are setting up the template, it is necessary to create the mySQL table in the database, to which all data will subsequently be saved. The Test Connection button will automatically create this table, if successful connection is made.
					<br>
				</div>
				<div class="sectionDiv">
					<h3>Paths</h3>
					<p>
						This section is again absolutely crucial for proper functioning of your template and must be set up correctly. They tell the script where to look for the files on your server.
					</p>
					<p>
						This is also the most problematic section of the whole <span class="mono">config.php</span>. The default configuration works for majority of servers, however, in some cases, especially if you have your own server with unusual structure, or especially if you are using a free hosting and your site is located on some subdomain, this might not work and in such case, probably the easiest thing to do is send me an email and I will try to figure out what the right setting is for your particular server.
					</p>
					<p>
						This setup script assumes that you have it saved in the install directory of the template already and so tries to pre-fill the values based on this, but you need to check this as it might not work in all cases.
					</p>
					<p>
						<span class="term">Path</span> is the path to the template root folder, relative to your server root folder. <span class="term">Page URL</span> is simply the top-level domain for your page.
					</p>
					<p>
						Example:
						<br>
						Your template is located in a folder "template" on your server, the address of the index is http:www.mysite.com/template/index.php. In this case, path would be "/template/" and Page URL would be "http://www.mysite.com".
					</p>
					<table>
						<tr>
							<td>
								Path
							</td>
							<td>
								<input id="templatePath" value="<?php echo $path?>" name="templatePath" size="<?php echo (strlen($path)+10)?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Page URL - make sure to include the http:// !!!
							</td>
							<td>
								<input id="pageURL" value="<?php echo $pageURL?>" name="pageURL" size="<?php echo (strlen($pageURL)+10)?>" autocomplete="off">
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
					<h3>Email</h3>
					<p>Some features of this template allow you to send emails - for example notifications, warnings etc. For this to work, your server must have mail server correctly configured. Here you can test if this is the case for your server. Fill in your email address and then click the Test Email link. If you receive an email, your mail server works correctly. If not, then you will be able to use the template without any problems, BUT keep in mind that anything that sends emails (notifications, station warnings etc.) will not work.</p>
					<table>
						<tr>
							<td>
								Your email address: <input id="testEmailAddress" value="@" name="testEmailAddress" size="20" autocomplete="off">
							</td>
							<td>
								<a href="#"><span id="testEmailSend">Test Email</span></a>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
					<h3>Station Location</h3>
					<table style="width:100%">
						<tr>
							<td style="width:30%">
								<p>
									Now you need to specify the exact location of your station. Built-in script tries to detect your location based on your computer IP address (if you clicked Yes and allowed the script to get your location), but you need to specify this further because the auto-location is not so accurate. Simply drag the marker on the map to the place you want. If you are entering your position manually aim for at least 3 decimal places, ideally 4, keep in mind that a difference of 0.01 can be +/- 2km.
								</p>
								<p>
									Once you have your latitude and longitude you need to specify your country. This is done by inputing the two-letter ISO2 code. Again, this script will try to detect this automatically based on the position of the marker, but double check this. The script also tried to guess your city, this is for example used as a subheading in the top banner and also for forecasts.
								</p>
								<table>
									<tr>
										<td>
											Coordinates
										</td>
										<td>
											<input id="stationLat" size="10" name="stationLat" value="<?php echo round($stationLat,4)?>" autocomplete="off">
											<br>
											<input id="stationLon" size="10" name="stationLon" value="<?php echo round($stationLon,4)?>" autocomplete="off">
										</td>
									</tr>
									<tr>
										<td>
											Country
										</td>
										<td>
											<input id="stationCountry" size="2" name="stationCountry" value="<?php echo $stationCountry?>" autocomplete="off">
										</td>
									</tr>
									<tr>
										<td>
											City
										</td>
										<td>
											<input id="stationCity" size="20" name="stationLocation" value="<?php echo $stationLocation?>" size="<?php echo (strlen($stationLocation)+10)?>" autocomplete="off">
										</td>
									</tr>
									<tr>
										<td>
											Station elevation
										</td>
										<td>
											<input id="stationElevation" size="2" name="stationElevation" value="<?php echo $stationElevation?>" autocomplete="off">
											<select id="stationElevationUnits" name="stationElevationUnits">
												<option value="m" <?php if($stationElevationUnits=="m"){echo "selected";}?>>
													m
												</option>
												<option value="ft" <?php if($stationElevationUnits=="ft"){echo "selected";}?>>
													ft
												</option>
											</select>
										</td>
									</tr>
									<tr>
										<td style='vertical-align:top'>
											State/province
										</td>
										<td>
											<input id="stationState" size="2" name="stationState" value="<?php echo $stationState?>" autocomplete="off"><br>
											only for users from the U.S. or Canada (2-letter code of your state/province in lowercase, eg. ny)
										</td>
									</tr>
								</table>
							</td>
							<td>
								<div id="mapCanvas"></div>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
					<h3>Timezone</h3>
					<p>
						In this section you need to specify your timezone, which again is important for calculations of sun rise, sun set etc. PHP has a built-in list of timezones to choose from, so simply select the appropriate one from the select box below.
					</p>
					<select id="stationTZ" name="stationTZ">
						<?php
							for($i=0;$i<count($availableTimezones);$i++){
								if($availableTimezones[$i]==$myTimezone){
									echo "<option value='".$availableTimezones[$i]."' selected>".$availableTimezones[$i]."</option>";
								}
								else{
									echo "<option value='".$availableTimezones[$i]."'>".$availableTimezones[$i]."</option>";
								}
							}
						?>
					</select>
				</div>
				<div class="sectionDiv">
				<h3>Language</h3>
					<p>
						There are currently over 15 languages available for the template. There is a language file for each language, which you can download from the Translations section at www.meteotemplate.com. The template download file already includes most of these files, but it is recommended you check the site if there are any new languages available or if an updated version is available. The Translations page also includes information about how to request a new language or how you can help translating yourself. All downloaded language files must be placed in the "lang" directory of the template.
					</p>
					<p>
						The flags below indicate which languages are currently available for your template - list of all language files currently in your lang directory. The one that is chosen to load by default is bigger. If you want to change the default language simply click the flag of the language you want. Also, if you enable this (later in this setup file), the users can change the language in which they see your site themselves, using the settings icon in the top right corner of the template.
					</p>
					<br>
					<?php
						foreach($availableLangs as $languageAvailable){
							if($languageAvailable!=$lang){
								echo "<img src='../imgs/flags/big/".strtolower($languageAvailable).".png' class='langIcon' onclick='langSelect(\"".$languageAvailable."\")' id='lang_".$languageAvailable."'>";
							}
							else{
								echo "<img src='../imgs/flags/big/".strtolower($languageAvailable).".png' class='langIcon' onclick='langSelect(\"".$languageAvailable."\")' id='lang_".$languageAvailable."' style='width:50px'>";
							}
						}
					?>
					<br>
					<input id="defaultLang" type="hidden" name="lang" value="<?php echo $lang?>" autocomplete="off">
				</div>
				<div class="sectionDiv">
					<h3>METAR</h3>
					<p>
						The template has a page, which allows you to view conditoins all around the world, using data reported by stations with a METAR code. Here you select the one that will be loaded by default. This is also used for generating the interactive banner.
					</p>
					Closest METAR station code
					<input size="4" id="stationMETAR" name="stationMETAR" value="<?php echo $stationMETAR?>" data-validation="length" data-validation-length="4" data-validation-error-msg-container="#stationMETARError" data-validation-error-msg="This is not a valid 4-letter METAR code." autocomplete="off">
					<br>
					<span id="stationMETARError"></span><br>
					(list available <a href="http://en.allmetsat.com/metar-taf" target="_blank">here</a>)
				</div>
				<div class="sectionDiv">
					<h3>General Info</h3>
					<p>
						In this section you give some general information. This is used for example as a heading for the banner, but also for meta tags, which are included in the HTML of your page. You don't actually see these tags, but they are analyzed for example by search engines and help with indexing and categorizing your page.
					</p>
					<table>
						<tr>
							<td>
								Page name
							</td>
							<td>
								<input id="pageName" name="pageName" value="<?php echo $pageName?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Author
							</td>
							<td>
								<input id="pageAuthor" name="pageAuthor" value="<?php echo $pageAuthor?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Short page description
							</td>
							<td>
								<input id="pageDesc" name="pageDesc" value="<?php echo $pageDesc?>" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td>
								Weather station model
							</td>
							<td>
								<input id="stationModel" name="stationModel" value="<?php echo $stationModel?>" data-validation="required" autocomplete="off">
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Weather station icon
							</td>
							<td>
								<br><br>
								<select id="stationIcon" class="button2" name="stationIcon">
									<option value="station" <?php if($stationIcon=="station"){ echo "selected";}?>>WH-1080</option>
									<option value="davispro2" <?php if($stationIcon=="davispro2"){ echo "selected";}?>>Davis Pro 2</option>
									<option value="davisvueblack" <?php if($stationIcon=="davisvueblack"){ echo "selected";}?>>Davis Vue</option>
									<option value="acuritevn1black" <?php if($stationIcon=="acuritevn1black"){ echo "selected";}?>>Acurite VN1</option>
									<option value="ambient1001black" <?php if($stationIcon=="ambient1001black"){ echo "selected";}?>>Ambient 1001</option>
									<option value="netatmoblack" <?php if($stationIcon=="netatmoblack"){ echo "selected";}?>>NetAtmo</option>
									<option value="raspberryblack" <?php if($stationIcon=="raspberryblack"){ echo "selected";}?>>Raspberry Pi</option>
									<option value="wmr90ablack" <?php if($stationIcon=="wmr90ablack"){ echo "selected";}?>>WMR-90</option>
									<option value="wmr200black" <?php if($stationIcon=="wmr200black"){ echo "selected";}?>>WMR-200</option>
									<option value="ws5300black" <?php if($stationIcon=="ws5300black"){ echo "selected";}?>>WS-5300</option>
								</select>
								&nbsp;Choose an icon that best reflects your station model
								<br>
								<table style="width:100%;table-layout:fixed">
									<tr>
										<td style="text-align:center">
											<span class="mticon-station" style="font-size:4em"></span><br>WH-1080
										</td>
										<td style="text-align:center">
											<span class="mticon-davispro2" style="font-size:4em"></span><br>Davis Pro 2
										</td>
										<td style="text-align:center">
											<span class="mticon-davisvueblack" style="font-size:4em"></span><br>Davis Vue
										</td>
										<td style="text-align:center">
											<span class="mticon-acuritevn1black" style="font-size:4em"></span><br>Acurite VN1
										</td>
										<td style="text-align:center">
											<span class="mticon-ambient1001black" style="font-size:4em"></span><br>Ambient 1001
										</td>
									</tr>
									<tr>
										<td style="text-align:center">
											<span class="mticon-netatmoblack" style="font-size:4em"></span><br>NetAtmo
										</td>
										<td style="text-align:center">
											<span class="mticon-raspberryblack" style="font-size:4em"></span><br>Raspberry Pi
										</td>
										<td style="text-align:center">
											<span class="mticon-wmr90ablack" style="font-size:4em"></span><br>WMR-90
										</td>
										<td style="text-align:center">
											<span class="mticon-wmr200black" style="font-size:4em"></span><br>WMR-200
										</td>
										<td style="text-align:center">
											<span class="mticon-ws5300black" style="font-size:4em"></span><br>WS-5300
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
					<h3>Solar sensor</h3>
					<p>
						Meteotemplate supports solar sensor. However, since this is something not every station has, you can disable it by setting the following parameter to No. In such case, all the statistics, graphs and tables that deal with solar radiation, as well as legends and other features of the template, will be hidden. However, your database will still include a column for solar radiation data, so if you subsequently get one, you can easily set this to true and start logging solar radiation as well. In some cases, the type of parameters displayed will also differ based on this settings. For example, if there is no solar sensor available, then in some cases, the space on the web, where solar radiation data is otherwise displayed, might show alternative parameters or content.
					</p>
					<select id="solarSensor" name="solarSensor">
						<?php
							if(!isset($solarSensor)){
								$solarSensor = true;
							}
							if($solarSensor){
						?>
								<option value="1" selected>Yes</option>
								<option value="0">No</option>
						<?php
							}
							else{
						?>
								<option value="1">Yes</option>
								<option value="0" selected>No</option>
						<?php
							}
						?>
					</select>
				</div>
				<div class="sectionDiv">
				<h3>Design</h3>
				<p>
					You can choose from many color themes for your template. First thing to note is that all the pages and plugins always use a combination of two color themes. Technically your template will work absolutely fine even if you specify the same color scheme for both parameters. However, it is not recommended because then some texts or graphs could be difficult to read, if the colors are too similar.
				</p>
				<p>
					For each color theme, there are 9 hues of that color. So if for example you choose dark_red as one of the colors, all the hues of that theme will be used throughout the template. The way this works is that for example there might be a graph, which will be shown in the color you specified - in this example dark red - and for the background, the darkest hue of that combination will be used and for the font, the lightest etc. I tried to make sure that I do not use similar hues even when using different combinations, but as mentioned above, try using different colors for both parameters. The first color is the main color, the second color parameter is the secondary color. In case of the fonts, the first one is the main page font, the second one is used especially for headings, menu etc.
				</p>
				<p>
					The template uses the so-called "material design". Main characteristics of this design philosophy are simplicity and cleanness. Current web design trends tend not to use things such as shadows, 3D graphics, gradients etc. However, design is something very subjective. If you really think you would rather have gradient colors, then set this parameter gradient to Yes. In such case, gradients will be applied to certain parts of the website (using the same colors you specified previously), including for example table headings, menu, backgrounds etc. In some cases however, the non-gradient design looks much better, so it is recommended to keep this option as No.
				</p>
				<p>
					Below you can test how your page would look with the colors/fonts you choose. This example uses the dark theme and the live preview takes into account your color and font settings and also uses the information you specified above. The top interactive banner randomly chooses a weather condition for illustration purposes.
				</p>
				<table>
					<tr>
						<td>
							Color 1
						</td>
						<td>
							<select id="color1" name="design">
								<?php
									$i=0;
									foreach($color_schemes as $scheme){
										if($availableSchemes[$i]==$design){
											echo "<option value='".strtolower($availableSchemes[$i])."' selected>".ucfirst($availableSchemes[$i])."</option>";$i++;
										}
										else{
											echo "<option value='".strtolower($availableSchemes[$i])."'>".ucfirst($availableSchemes[$i])."</option>";$i++;
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Color 2
						</td>
						<td>
							<select id="color2" name="design2">
								<?php
									$i=0;
									foreach($color_schemes as $scheme){
										if($availableSchemes[$i]==$design2){
											echo "<option value='".strtolower($availableSchemes[$i])."' selected>".ucfirst($availableSchemes[$i])."</option>";$i++;
										}
										else{
											echo "<option value='".strtolower($availableSchemes[$i])."'>".ucfirst($availableSchemes[$i])."</option>";$i++;
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Font 1
						</td>
						<td>
							<select id="font1" name="designFont">
								<?php
									for($i=0;$i<count($availableFonts);$i++){
										if($availableFonts[$i]==$designFont){
										echo "<option value='".$availableFonts[$i]."' selected>".ucfirst($availableFonts[$i])."</option>";
										}
										else{
											echo "<option value='".$availableFonts[$i]."'>".ucfirst($availableFonts[$i])."</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Font 2
						</td>
						<td>
							<select id="font2" name="designFont2">
								<?php
									for($i=0;$i<count($availableFonts);$i++){
										if($availableFonts[$i]==$designFont2){
											echo "<option value='".$availableFonts[$i]."' selected>".ucfirst($availableFonts[$i])."</option>";
										}
										else{
											echo "<option value='".$availableFonts[$i]."'>".ucfirst($availableFonts[$i])."</option>";
										}
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Gradient colors
						</td>
						<td>
							<select id="gradient" name="gradient">
								<option value="1" <?php if($gradient=="1"){echo "selected";}?>>Yes</option>
								<option value="0" <?php if($gradient=="0"){echo "selected";}?>>No</option>
							</select>
						</td>
					</tr>
				</table>
				<table style="width:98%;margin:0 auto">
					<tr>
						<td style="width:40%">
							<table style="width:100%">
								<?php
									$i=0;
									foreach($color_schemes as $scheme){
								?>
									<tr>
										<th style="text-align:left">
											<?php echo ucfirst($availableSchemes[$i])?>
										</th>
										<td style="width:7%;background-color:#<?php echo $scheme['100']?>;color:#<?php echo $scheme['font100']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['200']?>;color:#<?php echo $scheme['font200']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['300']?>;color:#<?php echo $scheme['font300']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['400']?>;color:#<?php echo $scheme['font400']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['500']?>;color:#<?php echo $scheme['font500']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['600']?>;color:#<?php echo $scheme['font600']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['700']?>;color:#<?php echo $scheme['font700']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['800']?>;color:#<?php echo $scheme['font800']?>">
											abc
										</td>
										<td style="width:7%;background-color:#<?php echo $scheme['900']?>;color:#<?php echo $scheme['font900']?>">
											abc
										</td>
									</tr>
								<?php
										$i++;
									}
								?>
							</table>
						</td>
						<td style="vertical-align:top">
							<div style="width:100%;text-align:center;font-size:1.2em;font-weight:bold">Live Preview of Dark Theme</div>
							<table style="width:100%;margin:0 auto;border-spacing:0px;background:black" id="designTable">
								<tr>
									<td style="font-size:1.3em;text-align:center;padding:5px;padding-top:20px;padding-bottom:20px;background-image:url('../imgs/forecastImgs/weather_bg/cloudy3.jpg');background-repeat: no-repeat;background-size: 100% 100%;" colspan="3" id="exampleTopBanner">
										<table style="width:98%;margin:0 auto">
											<tr>
												<td style="width:20%;text-align:left;padding:0px" id="exampleFlag">
												</td>
												<td style="text-align:center;font-family:'<?php echo $availableFonts[0]?>';text-shadow: 1px 1px #222222;" id="exampleHeading">
													METEOTEMPLATE
													<br>
													<span id="exampleSubtitle" style="font-size:0.85em;font-variant:small-caps">
													</span>
												</td>
												<td style="width:20%">
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td style="text-align:left;font-size:0.7em;font-variant:small-caps;padding:7px;font-family:'<?php echo $availableFonts[0]?>'" colspan="3" id="designExampleMenu">
										Weather station&nbsp&nbsp&nbspWeather&nbsp&nbsp&nbspClimate&nbsp&nbsp&nbspAstronomy&nbsp&nbsp&nbspOther&nbsp&nbsp&nbspInfo
									</td>
								</tr>
								<tr>
									<td style="width:25%;padding:5px" colspan="3">
										<div class="exampleBlock" style="height:100px">
											<br><br>Block 1
										</div>
									</td>
								</tr>
								<tr>
									<td style="padding:5px" colspan="2">
										<div class="exampleBlock" style="height:150px">
											<br><br>Block 2
										</div>
									</td>
									<td style="padding:5px">
										<div class="exampleBlock" style="height:150px">
											<br><br>Block 3
										</div>
									</td>
								</tr>
								<tr>
									<td style="width:25%;padding:5px">
										<div class="exampleBlock" style="height:100px">
											<br><br>Block 4
										</div>
									</td>
									<td style="width:50%;padding:5px">
										<div class="exampleBlock" style="height:100px">
											<br><br>Block 5
										</div>
									</td>
									<td style="width:25%;padding:5px">
										<div class="exampleBlock" style="height:100px">
											<br><br>Block 6
										</div>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;font-size:0.7em;font-variant:small-caps;padding:6px;margin-top:5px" colspan="3" id="designExampleFooter">
										meteotemplate <?php echo date("Y")?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Units</h3>
				<p>
					Now you have to specify the units. There are two sets of units that must be specified:
				</p>
				<ul>
					<li>
						<span class="term">database units</span> - these are units that tell the template, in which units are the numbers you import/update in the database. These must be set prior to the actual import and updates. This is very important because otherwise your dewpoint and apparent temperature will not be correctly calculated.
					</li>
					<li>
						<span class="term">display units</span> - these are units in which data will be presented on your site. These can be changed and can be different from the units in your database. It is however very important that you therefore set the database units correctly, so that the correct conversion is performed. If you enable this, you can also let the user to change the display units. For example, you might have your database temperature units set to degrees Fahrenheit. However, the user is not familiar with these and so they change it in the settings dialog (if enabled) to degrees Celsius. In this situation, your numbers in the database will of course not be changed, but when the scripts get the data from the database, they will check if the display units are the same as the database units, and if not, it will make the corresponding conversion and you will see numbers in the units specified. The display units set here is what you will see by default. You can also change the display units any time in the future (unlike the database units, which must remain the same. Remember that by changing any of these values, both database and display units, you are not making any change to the actual numbers in the database, you are only telling the scripts what the units are to perform the right conversions and calculations of dew point and apparent temperature).
					</li>
				</ul>
				<h4>Database units</h4>
				<table style="width:50%">
					<tr>
						<td style="text-align:center;width:25%">
							<span class="mticon-temp" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:25%">
							<span class="mticon-wind" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:25%">
							<span class="mticon-pressure" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:25%">
							<span class="mticon-rain" style="font-size:3em"></span>
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							<select id="dataTempUnits" name="dataTempUnits">
								<option value="C" <?php if($dataTempUnits=="C"){echo "selected";}?>>
									C
								</option>
								<option value="F" <?php if($dataTempUnits=="F"){echo "selected";}?>>
									F
								</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="dataWindUnits" name="dataWindUnits">
								<option value="ms" <?php if($dataWindUnits=="ms"){echo "selected";}?>>
									m/s
								</option>
								<option value="kmh" <?php if($dataWindUnits=="kmh"){echo "selected";}?>>
									km/h
								</option>
								<option value="mph" <?php if($dataWindUnits=="mph"){echo "selected";}?>>
									mph
								</option>
								<option value="kt" <?php if($dataWindUnits=="kt"){echo "selected";}?>>
									knots
								</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="dataPressUnits" name="dataPressUnits">
								<option value="hpa" <?php if($dataPressUnits=="hpa"){echo "selected";}?>>hPa</option>
								<option value="mmhg" <?php if($dataPressUnits=="mmhg"){echo "selected";}?>>mmHg</option>
								<option value="inhg" <?php if($dataPressUnits=="inhg"){echo "selected";}?>>inHg</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="dataRainUnits" name="dataRainUnits">
								<option value="mm" <?php if($dataRainUnits=="mm"){echo "selected";}?>>mm</option>
								<option value="in" <?php if($dataRainUnits=="in"){echo "selected";}?>>in</option>
							</select>
						</td>
					</tr>
				</table>
				<h4>Webpage default displayed units</h4>
				<table style="width:75%">
					<tr>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-temp" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-wind" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-pressure" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-rain" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-cloudbase" style="font-size:3em"></span>
						</td>
						<td style="text-align:center;width:16.66%">
							<span class="mticon-visibility" style="font-size:3em"></span>
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							<select id="displayTempUnits" name="displayTempUnits">
								<option value="C" <?php if($displayTempUnits=="C"){echo "selected";}?>>
									C
								</option>
								<option value="F" <?php if($displayTempUnits=="F"){echo "selected";}?>>
									F
								</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="displayWindUnits" name="displayWindUnits">
								<option value="ms" <?php if($displayWindUnits=="ms"){echo "selected";}?>>m/s</option>
								<option value="kmh" <?php if($displayWindUnits=="kmh"){echo "selected";}?>>km/h</option>
								<option value="mph" <?php if($displayWindUnits=="mph"){echo "selected";}?>>mph</option>
								<option value="kt" <?php if($displayWindUnits=="kt"){echo "selected";}?>>knots</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="displayPressUnits" name="displayPressUnits">
								<option value="hpa" <?php if($displayPressUnits=="hpa"){echo "selected";}?>>hPa</option>
								<option value="mmhg" <?php if($displayPressUnits=="mmhg"){echo "selected";}?>>mmHg</option>
								<option value="inhg" <?php if($displayPressUnits=="inhg"){echo "selected";}?>>inHg</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="displayRainUnits" name="displayRainUnits">
								<option value="mm" <?php if($displayRainUnits=="mm"){echo "selected";}?>>mm</option>
								<option value="in" <?php if($displayRainUnits=="in"){echo "selected";}?>>in</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="displayCloudbaseUnits" name="displayCloudbaseUnits">
								<option value="m" <?php if($displayCloudbaseUnits=="m"){echo "selected";}?>>m</option>
								<option value="ft" <?php if($displayCloudbaseUnits=="ft"){echo "selected";}?>>ft</option>
							</select>
						</td>
						<td style="text-align:center">
							<select id="displayVisibilityUnits" name="displayVisibilityUnits">
								<option value="km" <?php if($displayVisibilityUnits=="km"){echo "selected";}?>>km</option>
								<option value="mi" <?php if($displayVisibilityUnits=="mi"){echo "selected";}?>>mi</option>
								<option value="m" <?php if($displayVisibilityUnits=="m"){echo "selected";}?>>m</option>
							</select>
						</td>
					</tr>
				</table>
				<p>
					The data saved to the database are aggregated values from the cached api updates. In case of the rain rate you can choose what exactly you want to save to the database. Either you can choose average, in which case the value saved to the database will be the average rain rate from the 5min interval. This is the statistically correct way. However, in case you would rather see the 5min maximum, then change this to maximum.<br>
					<select id="apiRRCalculation" name="apiRRCalculation">
						<option value="avg" <?php if($apiRRCalculation=="avg"){echo "selected";}?>>Average</option>
						<option value="max" <?php if($apiRRCalculation=="max"){echo "selected";}?>>Maximum</option>
					</select>
				</p>
				</div>
				<div class="sectionDiv">
				<h3>Limits</h3>
				<p>
					This section is also very important and useful and incorrect settings can result in your data not being properly imported or updated. In summary, here you specify the range of values, which are possible for the particular parameter. If then, the script performing the database update, or during import of history data, encounters a value which is outside of this interval, it will reject it and simply save a blank value for that record, which means it will show in your template as blank, it will not be included in the statistics, graphs etc. The point of this is to prevent potential spikes in data, or for example if your station "goes crazy" and just reports some unrealistic value, it will be saved, if it was, it could completely bias all your station extremes, statistics etc. It is of course possible to subsequently delete it manually from the database, but it is always easier to prevent such thing from happening in the first place.
				</p>
				<p>
					Make sure that these ranges correspond to the units in the database! This is very important, the displayed units in this case are irrelevant. Be particularly careful about for example the pressure range. The default values in the config are set for hPa, where the pressure usually ranges between values around 1000. If you are using inHg, then your numbers are likely to be around 30, so if you specify your database pressure units as inhg and then do not change the limits, your pressure will not be saved in the database!
				</p>
				<table style="width:100%">
					<tr>
						<td colspan='2' style="width:12.5%;text-align:center">
							Temperature
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Humidity
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Pressure
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Daily precipitation
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Rain rate (per hour)
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Wind Speed
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Wind Direction
						</td>
						<td colspan='2' style="width:12.5%;text-align:center">
							Solar Radiation
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
						<td style="text-align:center">
							Min
						</td>
						<td style="text-align:center">
							Max
						</td>
					</tr>
					<tr>
						<td style="text-align:center">
							<input id="limitTempMin" size="4" name="limitTempMin" value=<?php echo $limitTempMin?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitTempMax" size="4" name="limitTempMax" value=<?php echo $limitTempMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitHumidityMin"  size="3" name="limitHumidityMin" value=<?php echo $limitHumidityMin?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitHumidityMax"  size="3" name="limitHumidityMax" value=<?php echo $limitHumidityMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitPressureMin" size="6" name="limitPressureMin" value=<?php echo $limitPressureMin?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitPressureMax" size="6" name="limitPressureMax" value=<?php echo $limitPressureMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitRainMin" size="6"  name="limitRainMin" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitRainMax" size="6" name="limitRainMax" value=<?php echo $limitRainMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitRainRateMin" size="6"  name="limitRainRateMin" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitRainRateMax" size="6" name="limitRainRateMax" value=<?php echo $limitRainRateMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitWindMin" size="4" name="limitWindMin" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitWindMax" size="4" name="limitWindMax" value=<?php echo $limitWindMax?> style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitBearingMin" size="3" name="limitBearingMin" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitBearingMax" size="3" name="limitBearingMax" value=360 readonly data-validation="number" data-validation-allowing="range[360;360]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitSolarMin" size="4" name="limitSolarMin" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
						<td style="text-align:center">
							<input id="limitSolarMax" size="4" name="limitSolarMax" value=<?php echo $limitSolarMax?> style="text-align:center">
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Adjustments</h3>
				<p>
					This section allows you to set adjustments. This means that each corresponding value sent to the API will be adjusted by this factor. Be VERY CAREFUL about this. If you do not know how to use it, leave all the fields to defaults! In most cases this is the right value.
				</p>
				<p>
					Also note, that the adjustments have to be in the same units as used by the API! This means - temperature (deg Celsius), precipitation (mm), pressure (hPa) and wind speed (km/h). Make sure that the multiplication factor is based on these units, regardless of what the display or database units are!
				</p>
				<p>
					For precipitation and wind speed, the adjustments are multipliers (the value will be multiplied by the provided number). For temperature and pressure, the adjustment is addition. If you want to subtract a number simply provide negative number.
				</p>
				<table style="width:100%">
					<tr>
						<td>
							Temperature
						</td>
						<td>
							Pressure
						</td>
						<td>
							Daily precipitation
						</td>
						<td>
							Wind Speed
						</td>
					</tr>
					<tr>
						<td>
							+ <input id="multiplierT" size="6" name="multiplierT" value=<?php echo $multiplierT?> style="text-align:center">&deg;C
						</td>
						<td>
							+ <input id="multiplierP" size="6" name="multiplierP" value=<?php echo $multiplierP?> style="text-align:center">hPa
						</td>
						<td>
							* <input id="multiplierR" size="6" name="multiplierR" value=<?php echo $multiplierR?> style="text-align:center">mm
						</td>
						<td>
							* <input id="multiplierW" size="6" name="multiplierW" value=<?php echo $multiplierW?> style="text-align:center">km/h
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Formats</h3>
				<p>
					In this section you can specify how you want the date and time to be displayed in the template (in graphs, tables, headings etc.). There are three parameters for this: one for situations where only date is displayed, one for situations when only time is displayed and one for situations where both date and time are shown.
				</p>
				<table>
					<tr>
						<td>
							Date
						</td>
						<td>
							<select id="dateFormat" name="dateFormat">
								<option value="Y-m-d" <?php if($dateFormat=="Y-m-d"){echo "selected";}?>>2015-09-01</option>
								<option value="y-m-d" <?php if($dateFormat=="y-m-d"){echo "selected";}?>>15-09-01</option>
								<option value="m-d-Y" <?php if($dateFormat=="m-d-Y"){echo "selected";}?>>09-01-2015</option>
								<option value="m-d-y" <?php if($dateFormat=="m-d-y"){echo "selected";}?>>09-01-15</option>
								<option value="M d, Y" <?php if($dateFormat=="M d, Y"){echo "selected";}?>>Sep 01, 2015</option>
								<option value="M j, Y" <?php if($dateFormat=="M j, Y"){echo "selected";}?>>Sep 1, 2015</option>
								<option value="M d, y" <?php if($dateFormat=="M d, y"){echo "selected";}?>>Sep 01, 15</option>
								<option value="M j, y" <?php if($dateFormat=="M j, y"){echo "selected";}?>>Sep 1, 15</option>
								<option value="Y-m-j" <?php if($dateFormat=="Y-m-j"){echo "selected";}?>>2015-09-1</option>
								<option value="y-m-j" <?php if($dateFormat=="y-m-j"){echo "selected";}?>>15-09-1</option>
								<option value="d.m.Y" <?php if($dateFormat=="d.m.Y"){echo "selected";}?>>01.09.2015</option>
								<option value="d-m-Y" <?php if($dateFormat=="d-m-Y"){echo "selected";}?>>01-09-2015</option>
								<option value="j.n.Y" <?php if($dateFormat=="j.n.Y"){echo "selected";}?>>1.9.2015</option>
								<option value="d.m.y" <?php if($dateFormat=="d.m.y"){echo "selected";}?>>01.09.15</option>
								<option value="Y/m/d" <?php if($dateFormat=="Y/m/d"){echo "selected";}?>>2015/09/01</option>
								<option value="y/m/d" <?php if($dateFormat=="y/m/d"){echo "selected";}?>>15/09/01</option>
								<option value="m/d/Y" <?php if($dateFormat=="m/d/Y"){echo "selected";}?>>09/01/2015</option>
								<option value="m/d/y" <?php if($dateFormat=="m/d/y"){echo "selected";}?>>09/01/15</option>
								<option value="d/m/y" <?php if($dateFormat=="d/m/y"){echo "selected";}?>>01/09/15</option>
								<option value="d/m/Y" <?php if($dateFormat=="d/m/Y"){echo "selected";}?>>01/09/2015</option>
								<option value="Y/d/m" <?php if($dateFormat=="Y/d/m"){echo "selected";}?>>2015/01/09</option>
								<option value="y/d/m" <?php if($dateFormat=="y/d/m"){echo "selected";}?>>15/01/09</option>
								<option value="j.m.Y" <?php if($dateFormat=="j.m.Y"){echo "selected";}?>>1.09.2016</option>
								<option value="j-n-Y" <?php if($dateFormat=="j-n-Y"){echo "selected";}?>>1-9-2016</option>
								<option value="jS F Y" <?php if($dateFormat=="jS F Y"){echo "selected";}?>>1st September 2015</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Time
						</td>
						<td>
							<select id="timeFormat" name="timeFormat">
								<option value="H:i" <?php if($timeFormat=="H:i"){echo "selected";}?>>09:50 (24h format)</option>
								<option value="G:i" <?php if($timeFormat=="G:i"){echo "selected";}?>>9:50 (24h format)</option>
								<option value="h:i A" <?php if($timeFormat=="h:i A"){echo "selected";}?>>09:50 AM</option>
								<option value="g:i A" <?php if($timeFormat=="g:i A"){echo "selected";}?>>9:50 AM</option>
								<option value="h:i a" <?php if($timeFormat=="h:i a"){echo "selected";}?>>09:50 am</option>
								<option value="g:i a" <?php if($timeFormat=="g:i a"){echo "selected";}?>>9:50 am</option>
								<option value="H.i" <?php if($timeFormat=="H.i"){echo "selected";}?>>09.50 (24h format)</option>
								<option value="G.i" <?php if($timeFormat=="G.i"){echo "selected";}?>>9.50 (24h format)</option>
								<option value="h.i A" <?php if($timeFormat=="h.i A"){echo "selected";}?>>09.50 AM</option>
								<option value="g.i A" <?php if($timeFormat=="g.i A"){echo "selected";}?>>9.50 AM</option>
								<option value="h.i a" <?php if($timeFormat=="h.i a"){echo "selected";}?>>09.50 am</option>
								<option value="g.i a" <?php if($timeFormat=="g.i a"){echo "selected";}?>>9.50 am</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Full date
						</td>
						<td>
							<select id="dateTimeFormat" name="dateTimeFormat">
								<option value="Y-m-d H.i" <?php if($dateTimeFormat=="Y-m-d H.i"){echo "selected";}?>>2015-09-01 09.50 (24h format)</option>
								<option value="Y-m-d H:i" <?php if($dateTimeFormat=="Y-m-d H:i"){echo "selected";}?>>2015-09-01 09:50 (24h format)</option>
								<option value="Y-m-d G.i" <?php if($dateTimeFormat=="Y-m-d G.i"){echo "selected";}?>>2015-09-01 9.50 (24h format)</option>
								<option value="Y-m-d G:i" <?php if($dateTimeFormat=="Y-m-d G:i"){echo "selected";}?>>2015-09-01 9:50 (24h format)</option>
								<option value="Y-m-d h.i A" <?php if($dateTimeFormat=="Y-m-d h.i A"){echo "selected";}?>>2015-09-01 09.50 AM</option>
								<option value="Y-m-d h.i a" <?php if($dateTimeFormat=="Y-m-d h.i a"){echo "selected";}?>>2015-09-01 09.50 am</option>
								<option value="Y-m-d h:i A" <?php if($dateTimeFormat=="Y-m-d h:i A"){echo "selected";}?>>2015-09-01 09:50 AM</option>
								<option value="Y-m-d h:i a" <?php if($dateTimeFormat=="Y-m-d h:i a"){echo "selected";}?>>2015-09-01 09:50 am</option>
								<option value="Y-m-d g.i A" <?php if($dateTimeFormat=="Y-m-d g.i A"){echo "selected";}?>>2015-09-01 9.50 AM</option>
								<option value="Y-m-d g.i a" <?php if($dateTimeFormat=="Y-m-d g.i a"){echo "selected";}?>>2015-09-01 9.50 am</option>
								<option value="Y-m-d g:i A" <?php if($dateTimeFormat=="Y-m-d g:i A"){echo "selected";}?>>2015-09-01 9:50 AM</option>
								<option value="Y-m-d g:i a" <?php if($dateTimeFormat=="Y-m-d g:i a"){echo "selected";}?>>2015-09-01 9:50 am</option>

								<option value="d.m.Y H.i" <?php if($dateTimeFormat=="d.m.Y H.i"){echo "selected";}?>>01.09.2015 09.50 (24h format)</option>
								<option value="d.m.Y H:i" <?php if($dateTimeFormat=="d.m.Y H:i"){echo "selected";}?>>01.09.2015 09:50 (24h format)</option>
								<option value="d.m.Y G.i" <?php if($dateTimeFormat=="d.m.Y G.i"){echo "selected";}?>>01.09.2015 9.50 (24h format)</option>
								<option value="d.m.Y G:i" <?php if($dateTimeFormat=="d.m.Y G:i"){echo "selected";}?>>01.09.2015 9:50 (24h format)</option>
								<option value="j.m.Y H.i" <?php if($dateTimeFormat=="j.m.Y H.i"){echo "selected";}?>>1.09.2015 09.50 (24h format)</option>
								<option value="j.m.Y H:i" <?php if($dateTimeFormat=="j.m.Y H:i"){echo "selected";}?>>1.09.2015 09:50 (24h format)</option>
								<option value="j.m.Y G.i" <?php if($dateTimeFormat=="j.m.Y G.i"){echo "selected";}?>>1.09.2015 9.50 (24h format)</option>
								<option value="j.m.Y G:i" <?php if($dateTimeFormat=="j.m.Y G:i"){echo "selected";}?>>1.09.2015 9:50 (24h format)</option>
								<option value="j.n.Y H.i" <?php if($dateTimeFormat=="j.n.Y H.i"){echo "selected";}?>>1.9.2015 09.50 (24h format)</option>
								<option value="j.n.Y H:i" <?php if($dateTimeFormat=="j.n.Y H:i"){echo "selected";}?>>1.9.2015 09:50 (24h format)</option>
								<option value="j.n.Y G.i" <?php if($dateTimeFormat=="j.n.Y G.i"){echo "selected";}?>>1.9.2015 9.50 (24h format)</option>
								<option value="j.n.Y G:i" <?php if($dateTimeFormat=="j.n.Y G:i"){echo "selected";}?>>1.9.2015 9:50 (24h format)</option>
								<option value="d.m.Y h.i A" <?php if($dateTimeFormat=="d.m.Y h.i A"){echo "selected";}?>>01.09.2015 09.50 AM</option>
								<option value="d.m.Y h.i a" <?php if($dateTimeFormat=="d.m.Y h.i a"){echo "selected";}?>>01.09.2015 09.50 am</option>
								<option value="d.m.Y h:i A" <?php if($dateTimeFormat=="d.m.Y h:i A"){echo "selected";}?>>01.09.2015 09:50 AM</option>
								<option value="d.m.Y H:i a" <?php if($dateTimeFormat=="d.m.Y H:i a"){echo "selected";}?>>01.09.2015 09:50 am</option>
								<option value="j.m.Y h.i A" <?php if($dateTimeFormat=="j.m.Y h.i A"){echo "selected";}?>>1.09.2015 09.50 AM</option>
								<option value="j.m.Y h.i a" <?php if($dateTimeFormat=="j.m.Y h.i a"){echo "selected";}?>>1.09.2015 09.50 am</option>
								<option value="j.m.Y h:i A" <?php if($dateTimeFormat=="j.m.Y h:i A"){echo "selected";}?>>1.09.2015 09:50 AM</option>
								<option value="j.m.Y H:i a" <?php if($dateTimeFormat=="j.m.Y H:i a"){echo "selected";}?>>1.09.2015 09:50 am</option>
								<option value="j.n.Y h.i A" <?php if($dateTimeFormat=="j.n.Y h.i A"){echo "selected";}?>>1.9.2015 09.50 AM</option>
								<option value="j.n.Y h.i a" <?php if($dateTimeFormat=="j.n.Y h.i a"){echo "selected";}?>>1.9.2015 09.50 am</option>
								<option value="j.n.Y h:i A" <?php if($dateTimeFormat=="j.n.Y h:i A"){echo "selected";}?>>1.9.2015 09:50 AM</option>
								<option value="j.n.Y H:i a" <?php if($dateTimeFormat=="j.n.Y H:i a"){echo "selected";}?>>1.9.2015 09:50 am</option>

								<option value="M j, Y H.i" <?php if($dateTimeFormat=="M j, Y H.i"){echo "selected";}?>>Sep 1, 2015 09.50 (24h format)</option>
								<option value="M j, Y H:i" <?php if($dateTimeFormat=="M j, Y H:i"){echo "selected";}?>>Sep 1, 2015 09:50 (24h format)</option>
								<option value="M j, Y G.i" <?php if($dateTimeFormat=="M j, Y G.i"){echo "selected";}?>>Sep 1, 2015 9.50 (24h format)</option>
								<option value="M j, Y G:i" <?php if($dateTimeFormat=="M j, Y G:i"){echo "selected";}?>>Sep 1, 2015 9:50 (24h format)</option>
								<option value="M j, Y h.i A" <?php if($dateTimeFormat=="M j, Y h.i A"){echo "selected";}?>>Sep 1, 2015 09.50 AM</option>
								<option value="M j, Y h.i a" <?php if($dateTimeFormat=="M j, Y h.i a"){echo "selected";}?>>Sep 1, 2015 09.50 am</option>
								<option value="M j, Y h:i A" <?php if($dateTimeFormat=="M j, Y h:i A"){echo "selected";}?>>Sep 1, 2015 09:50 AM</option>
								<option value="M j, Y H:i a" <?php if($dateTimeFormat=="M j, Y H:i a"){echo "selected";}?>>Sep 1, 2015 09:50 am</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Preferred date format
						</td>
						<td>
							<select id="prefferedDate" name="prefferedDate">
								<option value="M" <?php if($prefferedDate=="M"){echo "selected";}?>>Metric</option>
								<option value="US" <?php if($prefferedDate=="US"){echo "selected";}?>>Imperial</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Preferred time format
						</td>
						<td>
							<select id="prefferedTime" name="prefferedTime">
								<option value="24h" <?php if($prefferedTime=="24h"){echo "selected";}?>>24h</option>
								<option value="12h" <?php if($prefferedTime=="12h"){echo "selected";}?>>12h (AM/PM)</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							First day of the week
						</td>
						<td>
							<select id="firstWeekday" name="firstWeekday">
								<option value="0" <?php if($firstWeekday=="0"){echo "selected";}?>>Sunday</option>
								<option value="1" <?php if($firstWeekday=="1"){echo "selected";}?>>Monday</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Default graph interval
						</td>
						<td>
							<select id="defaultGraphInterval" name="defaultGraphInterval">
								<option value="today" <?php if($defaultGraphInterval=="today"){echo "selected";}?>>Today</option>
								<option value="1h" <?php if($defaultGraphInterval=="1h"){echo "selected";}?>>Last hour</option>
								<option value="24h" <?php if($defaultGraphInterval=="24h"){echo "selected";}?>>Last 24 hours</option>
								<option value="yesterday" <?php if($defaultGraphInterval=="yesterday"){echo "selected";}?>>Yesterday</option>
								<option value="thisweek" <?php if($defaultGraphInterval=="thisweek"){echo "selected";}?>>This week</option>
								<option value="lastweek" <?php if($defaultGraphInterval=="lastweek"){echo "selected";}?>>Last week</option>
								<option value="thismonth" <?php if($defaultGraphInterval=="thismonth"){echo "selected";}?>>This month</option>
								<option value="lastmonth" <?php if($defaultGraphInterval=="lastmonth"){echo "selected";}?>>Last month</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Default graph parameter
						</td>
						<td>
							<select id="defaultGraphParameter" name="defaultGraphParameter">
								<option value="T" <?php if($defaultGraphParameter=="T"){echo "selected";}?> >Temperature</option>
								<option value="H" <?php if($defaultGraphParameter=="H"){echo "selected";}?>>Humidity</option>
								<option value="P" <?php if($defaultGraphParameter=="P"){echo "selected";}?>>Pressure</option>
								<option value="R" <?php if($defaultGraphParameter=="R"){echo "selected";}?>>Rain</option>
								<option value="W" <?php if($defaultGraphParameter=="W"){echo "selected";}?>>Wind/Gust</option>
								<option value="S" <?php if($defaultGraphParameter=="S"){echo "selected";}?>>Solar radiation</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Graph date format
						</td>
						<td>
							<input id="graphDateFormat" size="6" name="graphDateFormat" value="<?php echo $graphDateFormat?>">
							<br>This is the Highcharts default date format - see http://php.net/manual/en/function.strftime.php
						</td>
					</tr>
					<tr>
						<td>
							Graph time format
						</td>
						<td>
							<input id="graphTimeFormat" size="6" name="graphTimeFormat" value="<?php echo $graphTimeFormat?>">
							<br>This is the Highcharts default date format - see http://php.net/manual/en/function.strftime.php
						</td>
					</tr>
					<tr>
						<td>
							Paper size for PDFs
						</td>
						<td>
							<select id="defaultPaperSize" name="defaultPaperSize">
								<option value="A4" <?php if($defaultPaperSize=="A4"){echo "selected";}?> >A4</option>
								<option value="letter" <?php if($defaultPaperSize=="letter"){echo "selected";}?>>Letter (US)</option>
							</select>
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>User options</h3>
				<p>
					In this section you can specify what level of customization you want to allow your users to have. If you look closely at the interactive banner at the top, you will see there is a gear icon in the very top right corner. Clicking this opens a dialog, where the user can choose their preferred color combination, font combination, language and units. This setting is then saved locally to their PC as a cookie and overrides your default settings. It is however possible for you as a webmaster, to disable certain customization possibilities, or disable the whole settings dialog altogether, in which case the page will always be displayed to the user using the default settings you specified here.
				</p>
				<table style="border-spacing:4px">
					<tr>
						<td>
							<?php
								if($userCustomColor){
									echo '<input type="checkbox" id="userCustomColor" name="userCustomColor" checked>';
								}
								else{
									echo '<input type="checkbox" id="userCustomColor" name="userCustomColor">';
								}
							?>
						</td>
						<td>
							allow user to select custom design color combination - if set to false, template will always use your default colors set above
						</td>
					</td>
					<tr>
						<td>
							<?php
								if($userCustomFont){
									echo '<input type="checkbox" id="userCustomFont" name="userCustomFont" checked>';
								}
								else{
									echo '<input type="checkbox" id="userCustomFont" name="userCustomFont">';
								}
							?>
						</td>
						<td>
							allow user to select custom font combination - if set to false, template will always use your default fonts set above
						</td>
					</td>
					<tr>
						<td>
							<?php
								if($userCustomUnits){
									echo '<input type="checkbox" id="userCustomUnits" name="userCustomUnits" checked>';
								}
								else{
									echo '<input type="checkbox" id="userCustomUnits" name="userCustomUnits">';
								}
							?>
						</td>
						<td>
							allow user to select custom units - if set to false, template will always use your default units set above
						</td>
					</td>
					<tr>
						<td>
							<?php
								if($userCustomLang){
									echo '<input type="checkbox" id="userCustomLang" name="userCustomLang" checked>';
								}
								else{
									echo '<input type="checkbox" id="userCustomLang" name="userCustomLang">';
								}
							?>
						</td>
						<td>
							allow user to select language - if set to false, template will always use your default language set above
						</td>
					</td>
				</table>
				</div>
				<div class="sectionDiv">
					<h3>Additional Security</h3>
					<p>
						Here you can further increase the security of the template by limiting the possible IPs which are allowed to log in as admin.<br>
						PLEASE NOTE: If you enable this, you will only be able to login and manage your template from the IPs specified. Also, in case you ever need help from me and I ask you for the admin password to try diagnose and solve problems, you would need to temporarily disable this.
					</p>
					<p>
						Also, do NOT use this if you know that your internet provider often changes your IP address, in such case you would also not be able to login.
					</p>
					<p>
						If you want to specify more then one IP, separate them by a comma. If you want to specify an IP range, simply use the common part. The script will grant access to any IP, which includes the string specified here.<br>
						Examples:<br>
						<ul>
							<li>
								111.111.111.111 - will only grant access to 111.111.111.111 IP address
							</li>
							<li>
								123.123.123 - will grant access to IPs like 123.123.123.0, 123.123.123.55, but also 192.123.123.123
							</li>
							<li>
								123 - will grant access to any IP, which contains the number 123
							</li>
							<li>
								111.111.111,123.123.123 - will grant access to IPs, which contain "111.111.111" or "123.123.123"
							</li>
						</ul>
						BE VERY CAREFUL - if you enable this make absolutely sure that you include the IP you are currently using, otherwise you will be logged out and not be able to log back in to change this! It is therefore also not a good idea to use this if your IP changes very often.<br>
						Your current IP: <?php echo $myIP?>
					</p>
					<select id="enableAdminIP" name="enableAdminIP">
						<?php
							if(!isset($enableAdminIP)){
								$enableAdminIP = true;
							}
							if($enableAdminIP){
						?>
								<option value="1" selected>Yes</option>
								<option value="0">No</option>
						<?php
							}
							else{
						?>
								<option value="1">Yes</option>
								<option value="0" selected>No</option>
						<?php
							}
						?>
					</select><br>
					IPs allowed logging in as admin:
					<input id="adminIPs" size="150" name="adminIPs" value="<?php echo $adminIPs?>" >
					<br>
					<table>
						<tr>
							<td>
								Show cookie consent
							</td>
							<td>
								<select id="cookieNotice" name="cookieNotice">
									<?php
										if(!isset($cookieNotice)){
											$cookieNotice = false;
										}
										if($cookieNotice){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
				<h3>Climate ID</h3>
				<p>
					The default version of the template also includes a page about climate. This page uses data I have put together myself from over 30 different sources. The problem is that I had to make my own data table for this because each service/page reports the data in different format, different parameters etc etc. The table used by the template I made for my original personal website and took over a month to do. The important thing however, is that I obviously had to make a compromise and could not include every single city there is on Earth. There is currently approximately 3500 locations available in this table. If your location is not included and there is not even some nearby city that could be used, let me know and I will see if I can find data for your place.
				</p>
				<p>
					To determine the code for your location, go to <a href="http://www.meteotemplate.com/template/pages/climate/map.php" target="_blank">this page</a> and here you will see a map. Zoom-in and find the point that best represents your location. Then click on it and in the popup dialog click <span class="mono">Select</span>. You will be redirected to the climate page of that particular location. Now look at the URL and you will see it has a number at the end. This is the number you must insert for this parameter below. It should be a number between 1 and 5000.
				</p>
				<input id="climateID" size="4" name="climateID" value="<?php echo $climateID?>" data-validation="number" data-validation-allowing="range[0;10000]" data-validation-error-msg-container="#climateIDError" data-validation-error-msg="This is not a valid climate ID.">
				<br>
				<span id="climateIDError"></span>
				</div>
				<div class="sectionDiv">
				<h3>Normals</h3>
				<p>
					For some sensible comparisons it is essential to have long-term series of data, ideally one normal period (in meteteorology a normal is a 30-year period, a strict normal is a particular 30-year period - 1961-1990). Assuming you do not have 30-year series of measurement it is necessary you provide normals for your region using data from the closest available station with such series. Ideally try to find data from some nearby professional station. 
				</p>
				<p>
					<span class="mticon-temp" style="font-size:1.7em"></span>&nbsp;<strong>Temperature normals</strong><br>
					Units:&nbsp;
					<select id="areaNormalsTUnits" name="areaNormalsTUnits">
						<option value="C" <?php if($areaNormalsTUnits=="C"){echo "selected";}?>>&deg;C</option>
						<option value="F" <?php if($areaNormalsTUnits=="F"){echo "selected";}?>>&deg;F</option>
					</select>
					<br>
					If enabled, specify the monthly normals below (use a period (.) as decimal separator and semi-colon (;) as monthly values separator). Make sure the string has 12 values for all months, starting from January:<br>
					<input id="areaNormalsT" size="100" name="areaNormalsT" value="<?php echo $areaNormalsT?>">
				</p>
				<p>
					<span class="mticon-rain" style="font-size:1.7em"></span>&nbsp;<strong>Precipitation normals</strong><br>
					Units:&nbsp;
					<select id="areaNormalsRUnits" name="areaNormalsRUnits">
						<option value="mm" <?php if($areaNormalsRUnits=="C"){echo "selected";}?>>mm</option>
						<option value="in" <?php if($areaNormalsRUnits=="F"){echo "selected";}?>>in</option>
					</select>
					<br>
					Specify the monthly normals below (use a period (.) as decimal separator and semi-colon (;) as monthly values separator). Make sure the string has 12 values for all months, starting from January:<br>
					<input id="areaNormalsR" size="100" name="areaNormalsR" value="<?php echo $areaNormalsR?>">
				</p>
				</div>
				<div class="sectionDiv">
				<h3>Station Warnings</h3>
				<p>
					In this section you specify the station warning thresholds. First choose if you want to activate this feature at all. If you do, then what this does is that once you get above/below the specified value, a warning symbol will be shown in the header above the menu bar, showing the warning type and on hovering also the actual value.
				</p>
				<p>
					<strong>IMPORTANT!!!</strong> The thresholds must be specified in the database units! This is very important because remember that the displayed units can be changed by the user so we want to make sure the thresholds are applied to the correct numbers.
				</p>
				<p>
					In addition to the actual values you must also specify the interval in minutes. We want to have some interval to the past to look at. So, if for example you set this to 30 minutes, then a warning symbol for wind would be displayed if there was a wind gust equal to or higher than the specified value in the last 30 minutes. For the rain, the number represents the rain rate - we do not look at daily cumulative total, if it rained a lot in the morning, we do not want the rain warning to be shown in the evening. In your database, the rain rate is logged as a particular value per hour. So if you set this to eg. 30 minutes and the value to 20 then a warning symbol would be displayed if in the last 30 miunutes, during at least one database update the rain rate was equal to or above 20 mm/h (assuming you have database rain units set to mm, if you use inches, you would obviously specify a lower number). Same goes for solar radiation (if you previously set solar sensor to false simply ignore this, it won't have any effect).
				</p>
				<p>
					In case you want to enable only certain warnings, for example temperature, then simply set the threshold for that value really high or low. Example - you want to disable the warnings for just wind. Then set up all the others, enable warnings and for wind put a threshold of for example 999, there obviously will never be such high wind speed, so the warning would not have any effect and never show up.
				</p>
				<table>
					<tr>
						<td>
							Enable warnings
						</td>
						<td>
							<select id="stationWarnings" name="stationWarnings">
								<?php
									if(!isset($stationWarnings)){
										$stationWarnings = true;
									}
									if($stationWarnings){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Warnings interval
						</td>
						<td>
							<select id="stationWarningsInterval" name="stationWarningsInterval">
								<option value="interval 10 minute" <?php if($stationWarningsInterval=="interval 10 minute"){echo "selected";}?>>10 minutes</option>
								<option value="interval 20 minute" <?php if($stationWarningsInterval=="interval 20 minute"){echo "selected";}?>>20 minutes</option>
								<option value="interval 30 minute" <?php if($stationWarningsInterval=="interval 30 minute"){echo "selected";}?>>30 minutes</option>
								<option value="interval 45 minute" <?php if($stationWarningsInterval=="interval 45 minute"){echo "selected";}?>>45 minutes</option>
								<option value="interval 1 hour" <?php if($stationWarningsInterval=="interval 1 hour"){echo "selected";}?>>1 hour</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							High temperature warning
						</td>
						<td>
							<input id="warningHighT" size="5" name="warningHighT" value="<?php echo $warningHighT?>">
						</td>
					</tr>
					<tr>
						<td>
							Low temperature warning
						</td>
						<td>
							<input id="warningLowT" size="5" name="warningLowT" value="<?php echo $warningLowT?>">
						</td>
					</tr>
					<tr>
						<td>
							High wind speed warning
						</td>
						<td>
							<input id="warningHighW" size="5" name="warningHighW" value="<?php echo $warningHighW?>">
						</td>
					</tr>
					<tr>
						<td>
							Heavy rain warning
						</td>
						<td>
							<input id="warningHighR" size="5" name="warningHighR" value="<?php echo $warningHighR?>">
						</td>
					</tr>
					<tr>
						<td>
							High solar radiation
						</td>
						<td>
							<input id="warningHighS" size="5" name="warningHighS" value="<?php echo $warningHighS?>">
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<!--<h3>Station Alert</h3>
					<p>
						It is possible to activate a script, which will send you an email if your station hasn't reported any data for the time specified below. If you want to activate this function, simply enter your email address where you want the email to be sent and also the time interval in minutes.
					</p>
					<p>
						When such situation occurs, an email is sent to the email address specified. That also creates a temporary file that informs the template that the email has already been sent to you - this is to prevent emailing you with every subsequent update until your station is back online. In other words, you will only get the email once, however, this also means, once your station is online, you must go to your Admin section of the template and there you will see a button that will reset the alerts. Pressing it will delete the temporary file and re-activate the alerts. Unless you do this, even if your station is again offline, the script would still think the email has already been sent.
					</p>
					<table>
						<tr>
							<td>
								Enable email alerts
							</td>
							<td>
								<select id="alertActive" name="alertActive">
									<?php
										if($alertActive){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								Interval
							</td>
							<td>
								
							</td>
						</tr>
						<tr>
							<td>
								Email address
							</td>
							<td>
								
							</td>
						</tr>
					</table>
				</div>
				-->
				<input id="minimumAlertInterval" size="5" name="minimumAlertInterval" value="30" type="hidden">
				<input id="alertEmail" size="40" name="alertEmail" value="" type="hidden">
				<div class="sectionDiv">
				<h3>Services</h3>
				<table>
					<tr>
						<td>
							PayPal
						</td>
						<td>
							<input id="paypalButtonCode" size="15" name="paypalButtonCode" value="<?php echo $paypalButtonCode?>">
						</td>
					</tr>
					<tr>
						<td>
							DarkSky.net
						</td>
						<td>
							<input id="fIOKey" size="40" name="fIOKey" value="<?php echo $fIOKey?>"> necessary for Forecast page, get your free key <a href="https://darksky.net/dev/register" target="_blank">here</a>.
							<br>
							Language for the textual forecast:
							<select name="fIOLanguage" class="button2">
								<option value="ar" <?php if($fIOLanguage=="el"){echo "selected";}?>>Arabic</option>
								<option value="az" <?php if($fIOLanguage=="az"){echo "selected";}?>>Azerbaijani</option>
								<option value="be" <?php if($fIOLanguage=="be"){echo "selected";}?>>Belarusian</option>
								<option value="bs" <?php if($fIOLanguage=="bs"){echo "selected";}?>>Bosnian</option>
								<option value="zh" <?php if($fIOLanguage=="zh"){echo "selected";}?>>Chinese - simplified</option>
								<option value="zh-tw" <?php if($fIOLanguage=="zh-tw"){echo "selected";}?>>Chinese - traditional</option>
								<option value="kw" <?php if($fIOLanguage=="kw"){echo "selected";}?>>Cornish</option>
								<option value="cs" <?php if($fIOLanguage=="cs"){echo "selected";}?>>Czech</option>
								<option value="hr" <?php if($fIOLanguage=="hr"){echo "selected";}?>>Croatian</option>
								<option value="nl" <?php if($fIOLanguage=="nl"){echo "selected";}?>>Dutch</option>
								<option value="de" <?php if($fIOLanguage=="de"){echo "selected";}?>>German</option>
								<option value="el" <?php if($fIOLanguage=="el"){echo "selected";}?>>Greek</option>
								<option value="en" <?php if($fIOLanguage=="en"){echo "selected";}?>>English</option>
								<option value="fr" <?php if($fIOLanguage=="fr"){echo "selected";}?>>French</option>
								<option value="hu" <?php if($fIOLanguage=="hu"){echo "selected";}?>>Hungarian</option>
								<option value="id" <?php if($fIOLanguage=="id"){echo "selected";}?>>Indonesian</option>
								<option value="it" <?php if($fIOLanguage=="it"){echo "selected";}?>>Italian</option>
								<option value="is" <?php if($fIOLanguage=="is"){echo "selected";}?>>Icelandic</option>
								<option value="nb" <?php if($fIOLanguage=="nb"){echo "selected";}?>>Norwegian Bokmål</option>
								<option value="pl" <?php if($fIOLanguage=="pl"){echo "selected";}?>>Polish</option>
								<option value="pt" <?php if($fIOLanguage=="pt"){echo "selected";}?>>Portuguese</option>
								<option value="ru" <?php if($fIOLanguage=="ru"){echo "selected";}?>>Russian</option>
								<option value="sk" <?php if($fIOLanguage=="sk"){echo "selected";}?>>Slovak</option>
								<option value="sr" <?php if($fIOLanguage=="sr"){echo "selected";}?>>Serbian</option>
								<option value="es" <?php if($fIOLanguage=="es"){echo "selected";}?>>Spanish</option>
								<option value="sv" <?php if($fIOLanguage=="sv"){echo "selected";}?>>Swedish</option>
								<option value="tet" <?php if($fIOLanguage=="tet"){echo "selected";}?>>Tetum</option>
								<option value="tr" <?php if($fIOLanguage=="tr"){echo "selected";}?>>Turkish</option>
								<option value="uk" <?php if($fIOLanguage=="uk"){echo "selected";}?>>Ukrainian</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							WU ID
						</td>
						<td>
							<input id="wuStationID" size="15" name="wuStationID" value="<?php echo $wuStationID?>">
						</td>
					</tr>
					<tr>
						<td>
							WU API key
						</td>
						<td>
							<input id="wuStationAPI" size="15" name="wuStationAPI" value="<?php echo $wuStationAPI?>">
						</td>
					</tr>
					<tr>
						<td>
							Awekas
						</td>
						<td>
							<input id="awekasID" size="15" name="awekasID" value="<?php echo $awekasID?>">
						</td>
					</tr>
					<tr>
						<td>
							CWOP
						</td>
						<td>
							<input id="cwopID" size="15" name="cwopID" value="<?php echo $cwopID?>">
						</td>
					</tr>
					<tr>
						<td>
							WeatherCloud
						</td>
						<td>
							<input id="weathercloudID" size="15" name="weathercloudID" value="<?php echo $weathercloudID?>">
						</td>
					</tr>
					<tr>
						<td>
							WOW Metoffice
						</td>
						<td>
							<input id="WOWMetofficeID" size="15" name="WOWMetofficeID" value="<?php echo $WOWMetofficeID?>">
						</td>
					</tr>
					<tr>
						<td>
							PWS
						</td>
						<td>
							<input id="pwsID" size="15" name="pwsID" value="<?php echo $pwsID?>">
						</td>
					</tr>
					<tr>
						<td>
							Aeris
						</td>
						<td>
							<table>
								<tr>
									<td>
										Aeris ID
									</td>
									<td>
										<input id="aerisID" name="aerisID" value="<?php echo $aerisID?>">
									</td>
								</tr>
								<tr>
									<td>
										Aeris Secret
									</td>
									<td>
										<input id="aerisSecret" name="aerisSecret" value="<?php echo $aerisSecret?>">
									</td>
								</tr>
								<tr>
									<td>
										Aeris cache time
									</td>
									<td>
										<input id="aerisCacheTime" size="3" value="60" name="aerisCacheTime" value="<?php echo $aerisCacheTime?>">
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							World Weather Online
						</td>
						<td>
							<table>
								<tr>
									<td>
										WWO Api key
									</td>
									<td>
										<input id="WWOApiKey" name="WWOApiKey" value="<?php echo $WWOApiKey?>">
									</td>
								</tr>
								<tr>
									<td>
										WWO Location
									</td>
									<td>
										<input id="WWOLocation" name="WWOLocation" value="<?php echo $WWOLocation?>">
									</td>
								</tr>
								<tr>
									<td>
										WWO cache time
									</td>
									<td>
										<input id="WWOCacheTime" size="3" value="60" name="WWOCacheTime" value="<?php echo $WWOCacheTime?>">
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Gauge Limits</h3>
				<p>
					There is a page which shows gauges with current conditions. This is part of the main template file (if you want there are also "live gauges" available as a plugin, which offer further functionality and realtime updates). In this section you can define the lower and upper limits for these gauges.
				</p>
				<table>
					<tr>
						<td></td>
						<td>Max</td>
						<td>Min</td>
					</tr>
					<tr>
						<td>
							Temp
						</td>
						<td>
							<input id="tempGaugeMax" name="tempGaugeMax" size="4" value=<?php echo $tempGaugeMax?> data-validation="number" data-validation-allowing="range[-100;150],float,negative" data-validation-error-msg-container="#tempGaugeMaxError" data-validation-error-msg="This is not a reasonable temperature." style="text-align:center">
							<br>
							<span id="tempGaugeMaxError"></span>
						</td>
						<td>
							<input id="tempGaugeMin" name="tempGaugeMin" size="4" value=<?php echo $tempGaugeMin?> data-validation="number" data-validation-allowing="range[-100;150],float,negative" data-validation-error-msg-container="#tempGaugeMinError" data-validation-error-msg="This is not a reasonable temperature." style="text-align:center">
							<br>
							<span id="tempGaugeMinError"></span>
						</td>
					</tr>
					<tr>
						<td>
							Pressure
						</td>
						<td>
							<input id="pressureGaugeMax" name="pressureGaugeMax" size="4" value=<?php echo $pressureGaugeMax?> data-validation="number" data-validation-allowing="range[20;1100]" data-validation-error-msg-container="#pressureGaugeMaxError" data-validation-error-msg="This is not a reasonable pressure." style="text-align:center">
							<br>
							<span id="pressureGaugeMaxError"></span>
						</td>
						<td>
							<input id="pressureGaugeMin" name="pressureGaugeMin" size="4" value=<?php echo $pressureGaugeMin?> data-validation="number" data-validation-allowing="range[20;1100]" data-validation-error-msg-container="#pressureGaugeMinError" data-validation-error-msg="This is not a reasonable pressure." style="text-align:center">
							<br>
							<span id="pressureGaugeMinError"></span>
						</td>
					</tr>
					<tr>
						<td>
							Wind
						</td>
						<td>
							<input id="windGaugeMax" name="windGaugeMax" size="4" value=<?php echo $windGaugeMax?> data-validation="number" data-validation-allowing="range[0;300]" data-validation-error-msg-container="#windGaugeMaxError" data-validation-error-msg="This is not a reasonable wind speed." style="text-align:center">
							<br>
							<span id="windGaugeMaxError"></span>
						</td>
						<td>
							<input id="windGaugeMin" name="windGaugeMin" size="4" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
					</tr>
					<tr>
						<td>
							Gust
						</td>
						<td>
							<input id="gustGaugeMax" name="gustGaugeMax" size="4" value=<?php echo $gustGaugeMax?> data-validation="number" data-validation-allowing="range[0;300]" data-validation-error-msg-container="#gustGaugeMaxError" data-validation-error-msg="This is not a reasonable wind speed." style="text-align:center">
							<br>
							<span id="gustGaugeMaxError"></span>
						</td>
						<td>
							<input id="gustGaugeMin" name="gustGaugeMin" size="4" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
					</tr>
					<tr>
						<td>
							Rain
						</td>
						<td>
							<input id="rainGaugeMax" name="rainGaugeMax" size="4" value=<?php echo $rainGaugeMax?> data-validation="number" data-validation-allowing="range[0;500]" data-validation-error-msg-container="#rainGaugeMaxError" data-validation-error-msg="This is not a reasonable daily precipitation." style="text-align:center">
							<br>
							<span id="rainGaugeMaxError"></span>
						</td>
						<td>
							<input id="rainGaugeMin" name="rainGaugeMin" size="4" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
							<br>
							<span id="rainGaugeMinError"></span>
						</td>
					</tr>
					<tr>
						<td>
							Solar
						</td>
						<td>
							<input id="solarGaugeMax" name="solarGaugeMax" size="4" value=<?php echo $solarGaugeMax?> data-validation="number" data-validation-allowing="range[0;1500]" data-validation-error-msg-container="#solarGaugeMaxError" data-validation-error-msg="This is not a reasonable solar radiation." style="text-align:center">
							<br>
							<span id="solarGaugeMaxError"></span>
						</td>
						<td>
							<input id="solarGaugeMin" name="solarGaugeMin" size="4" value=0 readonly data-validation="number" data-validation-allowing="range[0;0]" style="text-align:center">
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
					<h3>Google Analytics</h3>
					<p>
						Meteotemplate has a built-in support for Google Analytics - a tool for tracking your website traffic. If you use Google Analytics, you were assigned a unique code by Google, which if you insert here, will automatically start tracking all the template sites and it will be included in your Google Analytics overviews. The code should look something like "UA-...." or similar.
					</p>
					GA code:
					<input id="GAcode" name="GAcode" value="<?php echo $GAcode?>" style="text-align:center" autocomplete="off">
					<br>
					<span id="GAcodeError"></span>
				</div>
				<div class="sectionDiv">
					<h3>Page Search</h3>
					<p>
						Meteotemplate allows you to place a search box in the footer which will allow the user to search your page. This features uses the Google Custom search. You will need to set this up via your Google Developer console. There you will specify the domain you want to search etc. There are also options to set the appearance etc., but I highly recommend you leave everything to default, the template applies its own styling to the search box, so you do not have to worry about it. The only thing you will need is a code for the search, which you will be provided by Google on the developer's page, similarly to how you got your Google Maps api key. When you generate the code for the custom search, you will see a unique ID, which is what you have to place here. In the actual JavaScript code you will see it as var "cx" and it will be a long alphanumeric string.
					</p>
					Enable custom page search
					<select id="customPageSearch" name="customPageSearch">
						<?php
							if($customPageSearch){
						?>
								<option value="1" selected>Yes</option>
								<option value="0">No</option>
						<?php
							}
							else{
						?>
								<option value="1">Yes</option>
								<option value="0" selected>No</option>
						<?php
							}
						?>
					</select>
					Custom search code:
					<input id="searchCode" name="searchCode" value="<?php echo $searchCode?>" style="text-align:center" size="30" autocomplete="off">
				</div>
				<div class="sectionDiv">
					<h3>Share buttons</h3>
					<p>
						If you enable this, there will be an extra "share" icon in the footer, which when clicked will show Share buttons to Facebook, Google+ and Twitter. Clicking this will share the URL at which the user currently is to the corresponding service. The scripts are loaded completely separately to make sure this will in no way slow down your page or crash it in case any of the services is not available.
					</p>
					Enable share buttons
					<select id="addSharer" name="addSharer">
						<?php
							if($addSharer){
						?>
								<option value="1" selected>Yes</option>
								<option value="0">No</option>
						<?php
							}
							else{
						?>
								<option value="1">Yes</option>
								<option value="0" selected>No</option>
						<?php
							}
						?>
					</select>
				</div>
				<div class="sectionDiv">
					<h3>Update Check</h3>
					<p>
						In this section you can activate auto update check. This function will automatically check if there are block or plugin updates or a new template version available by connecting to meteotemplate.com and checking the latest versions. The new versions file is then checked once per day and cached. If a new block version or plugin version is detected and you access the homepage as an admin (in other words, you must be logged in), then you will see a notice at the top above the header telling you which blocks and plugins have updates available.
					</p>
					<table>
						<tr>
							<td>
								Enable update check
							</td>
							<td>
								<select id="templateUpdateCheck" name="templateUpdateCheck">
									<?php
										if($templateUpdateCheck){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv">
				<h3>Desktop and Mobile homepage</h3>
				<p>
					Your template has two homepages, one for mobile devices, which only has certain pages and a different menu, header etc. and a normal desktop homepage. This is where you set up which homepage you want to use and how to decide whether you want to redirect the user to the desktop or mobile version. Inside this file, you will first set up, if in general you want to redirect mobiles and if also tablets. This can be overriden by the setting below, where you also specify specific widths where one or the other homepage will be used.<br>Note - it is always possible to go to the other version, link to the mobile version is in the footer of the desktop, link to the desktop is in the footer of the mobile homepage.
				</p>
				<table>
					<tr>
						<td style="width:20%">
							Redirect mobile phones:
						</td>
						<td>
							<select id="redirectMobiles" name="redirectMobiles">
								<?php
									if($redirectMobiles){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
							 this means smartphones will be redirected to the mobile version (can be overridden by widths specified below)
						</td>
					</tr>
					<tr>
						<td>
							Redirect tablets:
						</td>
						<td>
							<select id="redirectTablets" name="redirectTablets">
								<?php
									if($redirectTablets){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
							 this means tablets will be redirected to the mobile version (can be overridden by widths specified below)
						</td>
					</tr>
					<tr>
						<td>
							High-resolution devices:
						</td>
						<td>
							<input id="maxWidthMobile" name="maxWidthMobile" value="<?php echo $maxWidthMobile?>" style="text-align:center"> px<br>
							 here you can specify a certain screen width in pixels, above which even mobile devices would be redirected to the normal desktop version. If you for example set this to 1200, then a tablet with a Full HD resolution would be redirected to the desktop version even if you set tablets to be redirected to mobile homepage above.
						</td>
					</tr>
					<tr>
						<td>
							Low resolution computers:
						</td>
						<td>
							<input id="minWidthDesktop" name="minWidthDesktop" value="<?php echo $minWidthDesktop?>" style="text-align:center"> px<br>
							 this is the exact opposite of the above. Here you can specify a certain minimum width in pixels, and if the monitor screen width is smaller (this can also happen if for example the user does not have the browser maximized and so it is narrow) then even a PC or laptop would be redirected to the mobile version of the page.<br><br>
						</td>
					</tr>
					<tr>
						<td>
							Mobile homepage
						</td>
						<td>
							<select id="mobileHomepageType" name="mobileHomepageType">
								<option value="customizable" <?php if($mobileHomepageType=="customizable"){echo "selected";}?>>Customizable</option>
								<option value="responsive" <?php if($mobileHomepageType=="responsive"){echo "selected";}?>>Responsive (fixed, Bootstrap)</option>
							</select>
							<br>
							 You have a choice. Meteotemplate includes two versions of mobile homepage. First, the original one, can be set up in a similar fashion to the desktop homepage. It includes many of the desktop scripts and allows you to build your homepage from blocks. The problem with this set up is that because you have so much flexibility, it is impossible to optimize this page and make it 100% responsive. Simply because as a developer I have no idea which blocks and in which order/layout you will use. If you prefer simplicity and 100% responsiveness over customization then you can use the alternative homepage which has a fixed layout and includes the most important features - current conditions, forecast, weather map and station statistics. This page has a fixed layout and uses Bootstrap for 100% responsiveness and looks good on any device.
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Further template customization</h3>
				<table>
					<tr>
						<td style="min-width:150px">
							Template background
						</td>
						<td>
							<p>Here you can specify the page background. This depends on the page width you set below. If for example you set the page width to 80%, there will be 10% on each side of the screen filled with the below specified background. If you set page width to 100%, there will not be any background visible.</p>
							<p>You can set the background as either a single color, a color gradient or your own background image (using a specific URL). Below you can adjust this. There are 4 fields for the colors, simply fill as many as you like and create the gradient. If you want just one color, simply fill in one field with the particular color and leave all the other fields blank.</p>
							<p>In addition you can use the select box to choose some predefined gradient combinations.</p>
							<table>
								<tr>
									<td>
										Background type
									</td>
									<td>
										<select id="customBgType" name="customBgType">
											<option value="vertical" <?php if($customBgType=="vertical"){echo "selected";}?>>Vertical gradient</option>
											<option value="horizontal" <?php if($customBgType=="horizontal"){echo "selected";}?>>Horizontal gradient</option>
											<option value="radial" <?php if($customBgType=="radial"){echo "selected";}?>>Radial gradient</option>
											<option value="image" <?php if($customBgType=="image"){echo "selected";}?>>Background image</option>
										</select>
										<span id="customBgImgDiv"><input id="customBgImg" name="customBgImg" value="<?php echo $customBgImg?>"> (custom image URL)</span>
									</td>
								</tr>
								<tr>
									<td>
										Gradient Colors
									</td>
									<td>
										<table>
											<tr>
												<td>
													<input id="customBgColor1" name="customBgColor1" value="<?php echo $customBgColor1?>" size="7" class="colorPicker">
												</td>
												<td>
													<input id="customBgColor2" name="customBgColor2" value="<?php echo $customBgColor2?>" size="7" class="colorPicker">
												</td>
												<td>
													<input id="customBgColor3" name="customBgColor3" value="<?php echo $customBgColor3?>" size="7" class="colorPicker">
												</td>
												<td>
													<input id="customBgColor4" name="customBgColor4" value="<?php echo $customBgColor4?>" size="7" class="colorPicker">
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>

									</td>
									<td>
										Preview<br>
										<div id="backgroundPreview" style="width:200px;height:100px;display:inline-block"></div>
										<div style="display:inline-block;vertical-align:top">
											<select id="predefinedBG">
												<option value="" selected>Predefined combinations</option>
												<?php
													for($i=0;$i<count($colorThemeGradients);$i++){
												?>
														<option value="<?php echo $colorThemeGradients[$i][0]?>"><?php echo $colorThemeGradients[$i][5]?></option>
												<?php
													}
												?>
											</select>
										</div>
									</td>
								</tr>
							</table>

						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Custom page width
						</td>
						<td>
							The page width, can be specified either in px or as a percentage of screen width. The remaining space will be filled in with the background.
							<br>
							<input id="customMaxWidth" name="customMaxWidth" value="<?php echo $customMaxWidth?>" style="margin-bottom:15px">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Block radius
						</td>
						<td>
							Here you can specify the block border radius. If you leave this as "0px", the blocks will have absolutely sharp corners. The higher the radius, the more oval the borders.
							<br>
							<input id="customBlockRadius" name="customBlockRadius" value="<?php echo $customBlockRadius?>" style="margin-bottom:15px">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Block bevel effect
						</td>
						<td>
							Here you can specify the bevel effect. This can be as a value in px ("3px","0px" etc) or as a percentage ("1%", "0.8%" etc.). The default recommended value is 0px. However, if you wish to ad a 3D effect to the blocks, then set this to some higher number. I recommend not using anything above 15px. You can immediately see the effect in the preview of the block below.
							<br>
							<input id="customBlockBevel" name="customBlockBevel" value="<?php echo $customBlockBevel?>" style="margin-bottom:15px">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Block border width
						</td>
						<td>
							Set the block border width. "0px" means the blocks will have no borders.
							<br>
							<input id="customBlockBorderWidth" name="customBlockBorderWidth" value="<?php echo $customBlockBorderWidth?>" style="margin-bottom:15px">
						</td>
					</tr>
					<tr>
						<td></td>
						<td>
							Block preview
							<br>
							<div id="blockExampleDiv" style="width:200px;height:100px;border:<?php echo $customBlockBorderWidth?> solid #<?php echo $color_schemes[$design2]['700']?>;background:#<?php echo $color_schemes[$design2]['900']?>;-moz-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);-webkit-box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);box-shadow: inset <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4), inset -<?php echo $customBlockBevel?> -<?php echo $customBlockBevel?> <?php echo $customBlockBevel?> rgba(0, 0, 0, .4);"></div>
						</td>
					</tr>
					<tr>
						<td>
							Hide multiple block borders
						</td>
						<td>
							<p>You will learn about what a multiple block is later. If you do not know what it is, just leave this to No and later when you create your homepage you will understand what this does and be able to go back to this Setup and change this should you wish to do so. By enabling this the multiple blocks will blend into one block and there will not be a border between them, instead a border will be drawn around the entire multiple block.</p>
							<select id="hideMultipleBlockBorder" name="hideMultipleBlockBorder">
								<?php
									if($hideMultipleBlockBorder){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Full-screen blocks
						</td>
						<td>
							<p>It is possible to automatically append a small link at the bottom of each block, which when clicked will show the block in a popup dialog in full screen mode. You can enable or disable this feature for desktop and mobile homepage separately.</p>
							Desktop homepage:
							<select id="blockMaximizeDesktop" name="blockMaximizeDesktop">
								<?php
									if($blockMaximizeDesktop){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
							<br>
							Mobile homepage: 
							<select id="blockMaximizeMobile" name="blockMaximizeMobile">
								<?php
									if($blockMaximizeMobile){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Block image export
						</td>
						<td>
							<p>It is possible to append an icon that allows the user to export any block as an image file. You can enable this separately for the desktop and mobile version</p>
							Desktop homepage:
							<select id="blockExportDesktop" name="blockExportDesktop">
								<?php
									if($blockExportDesktop){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
							<br>
							Mobile homepage: 
							<select id="blockExportMobile" name="blockExportMobile">
								<?php
									if($blockExportMobile){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Flat Design
						</td>
						<td>
							<p>It is possible to completely hide block borders and make the entire homepage look as one piece (flat). I personally prefer using the standard block version, which in my opinion is easier to use, but if you prefer this flat look, feel free to test this. You can of course always change this setting anytime in the future.</p>
							Desktop homepage:
							<select id="flatDesignDesktop" name="flatDesignDesktop">
								<?php
									if($flatDesignDesktop){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
							<br>
							Mobile homepage: 
							<select id="flatDesignMobile" name="flatDesignMobile">
								<?php
									if($flatDesignMobile){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Headings shadow</p>
						</td>
						<td>
							<p>This parameter specifies if you want your headings to have a shadow.</p>
							<select id="customHeadingShadow" name="customHeadingShadow"  style="margin-bottom:15px">
								<option value="none" <?php if($customHeadingShadow=="none"){echo "selected";}?>>No shadow</option>
								<option value="1px 1px #aaaaaa" <?php if($customHeadingShadow=="1px 1px #aaaaaa"){echo "selected";}?>>Small</option>
								<option value="2px 2px #aaaaaa" <?php if($customHeadingShadow=="2px 2px #aaaaaa"){echo "selected";}?>>Big</option>
							</select>
							<span id="headingExample" style="text-shadow: <?php echo $customHeadingShadow?>;padding-left:25px;font-size:1.5em;font-variant:small-caps;font-weight:bold">Example</span>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Body text shadow</p>
						</td>
						<td>
							<p>This parameter specifies if you want all the text on your pages to have a shadow.</p>
							<select id="customBodyTextShadow" name="customBodyTextShadow" style="margin-bottom:15px">
								<option value="none" <?php if($customBodyTextShadow=="none"){echo "selected";}?>>No shadow</option>
								<option value="1px 1px #aaaaaa" <?php if($customBodyTextShadow=="1px 1px #aaaaaa"){echo "selected";}?>>Small</option>
								<option value="2px 2px #aaaaaa" <?php if($customBodyTextShadow=="2px 2px #aaaaaa"){echo "selected";}?>>Big</option>
							</select>
							<span id="bodyTextExample" style="text-shadow: <?php echo $customHeadingShadow?>;padding-left:25px;font-size:1em">Example</span>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header left image</p>
						</td>
						<td>
							<p>The default setting is to show a flag of your country on the left side in the header of all pages. You can however change this if you wish, by specifying a URL for your custom image, or simply just select "Country flag" if you want to use this and the template will automatically insert an image of the flag of your country.</p>
							<select id="headerLeftImg" name="headerLeftImg" style="margin-bottom:15px">
								<option value="flag" <?php if($headerLeftImg=="flag"){echo "selected";}?>>Country flag</option>
								<option value="custom" <?php if($headerLeftImg=="custom"){echo "selected";}?>>Custom</option>
							</select>
							<span id="customHeaderLeftImgDiv"><input id="customHeaderLeftImg" name="customHeaderLeftImg" value="<?php echo $customHeaderLeftImg?>"> (custom image URL)</span>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header flag shape</p>
						</td>
						<td>
							<select id="flagIconShape" name="flagIconShape" style="margin-bottom:15px">
								<option value="flags" <?php if($flagIconShape=="flags"){echo "selected";}?>>Rounded</option>
								<option value="flagsSquare" <?php if($flagIconShape=="flagsSquare"){echo "selected";}?>>Squared</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header image</p>
						</td>
						<td>
							<p>The template uses a so-called "Interactive banner". This banner takes current conditions from your nearby METAR station specified above. It is in this case not possible to use your station data, because it is not possible to deduce for example the cloud cover, differentiate between rain and snow etc. The script then looks at whether it is night or day, and also determines the current Moon phase. Then based on this it will create the dynamic interactive banner. It will randomly choose a background image that corresponds to current conditions. If it is raining, it will add water droplets and set their angle based on the current wind speed taken from the database from your data. Also the intensity of the rain will be adjusted based on the METAR report. If it is snowing, snowflakes will appear. And if it is night time and it is not overcast, an icon of the Moon, corresponding to its current phase, will appear in the header top right corner.</p>
							<p>If however, you do not wish to use the interactive banner and want to use your own static image instead, simply select this below and specify the URL of this image.</p>
							<select id="headerImg" name="headerImg" style="margin-bottom:15px">
								<option value="interactive" <?php if($headerImg=="interactive"){echo "selected";}?>>Interactive Banner</option>
								<option value="custom" <?php if($headerImg=="custom"){echo "selected";}?>>Custom</option>
							</select>
							<span id="customHeaderImgDiv"><input id="customHeaderImg" name="customHeaderImg" value="<?php echo $customHeaderImg?>"> (custom image URL)</span>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header title</p>
						</td>
						<td>
							<p>This is the main title shown in the page header.</p>
							<select id="headerTitleSelect" name="headerTitleSelect" style="margin-bottom:15px">
								<option value="show" <?php if($headerTitleSelect=="show"){echo "selected";}?>>Show</option>
								<option value="hide" <?php if($headerTitleSelect=="hide"){echo "selected";}?>>Hide</option>
							</select>
							<input id="headerTitleText" name="headerTitleText" value="<?php echo $headerTitleText?>">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header subtitle</p>
						</td>
						<td>
							<p>This is the subtitle shown below the title in the header.</p>
							<select id="headerSubtitleSelect" name="headerSubtitleSelect" style="margin-bottom:15px">
								<option value="show" <?php if($headerSubtitleSelect=="show"){echo "selected";}?>>Show</option>
								<option value="hide" <?php if($headerSubtitleSelect=="hide"){echo "selected";}?>>Hide</option>
							</select>
							<input id="headerSubtitleText" name="headerSubtitleText" value="<?php echo $headerSubtitleText?>">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Mobile header</p>
						</td>
						<td>
							<p>The header of your mobile page does not include the interactive header because the mobile devices might have problems with power to render it or screen size issues. By default, your mobile section pages will have a unicolor header based on your design theme selection. Alternatively you can set an image backgound. If you want to use the default setting, simply leave this field blank. If you want to use an image, then insert the URL of it (http://..).</p>
							<input id="mobileHeaderImg" name="mobileHeaderImg" value="<?php echo $mobileHeaderImg?>" size="40">
							<br><br>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Header conditions</p>
						</td>
						<td>
							<p>It is possible to display the latest database values in the header. The values periodically switch. Here you can enable/disable this and also set the switch interval (in seconds).</p>
							<select id="headerConditions" name="headerConditions" style="margin-bottom:15px">
								<option value="1" <?php if($headerConditions=="1"){echo "selected";}?>>Show</option>
								<option value="0" <?php if($headerConditions=="0"){echo "selected";}?>>Hide</option>
							</select>&nbsp;&nbsp;
							Interval (s): <input id="headerConditionsInterval" name="headerConditionsInterval" value="<?php echo $headerConditionsInterval?>" size="3">
							<br><br>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Menu display</p>
						</td>
						<td>
							<p>There are two possible ways how the menu of the desktop version of the page can be displayed.</p>
							<ul>
								<li>sticky - this means that once the user starts scrolling down, the menu will remain on top of the page in a fixed position.</li>
								<li>fixed - this means that the menu will behave normally and once the user scrolls down, it will remain at the top of the page and will no longer be visible.</li>
							</ul>
							<select id="menuType" name="menuType" style="margin-bottom:15px">
								<option value="sticky" <?php if($menuType=="sticky"){echo "selected";}?>>Sticky</option>
								<option value="fixed" <?php if($menuType=="fixed"){echo "selected";}?>>Fixed</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Menu speed</p>
						</td>
						<td>
							<p>Here you can set the speed for the menu slide down/ slide up effect.</p>
							<select id="menuSpeed" name="menuSpeed" style="margin-bottom:15px">
								<option value="1" <?php if($menuSpeed=="1"){echo "selected";}?>>Instantaneous</option>
								<option value="150" <?php if($menuSpeed=="150"){echo "selected";}?>>Very fast</option>
								<option value="250" <?php if($menuSpeed=="250"){echo "selected";}?>>Fast</option>
								<option value="400" <?php if($menuSpeed=="400"){echo "selected";}?>>Normal</option>
								<option value="600" <?php if($menuSpeed=="600"){echo "selected";}?>>Slow</option>
								<option value="1000" <?php if($menuSpeed=="1000"){echo "selected";}?>>Very Slow</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Menu highlight</p>
						</td>
						<td>
							<p>It is possible to have the menu items highlighted when hovering over them. If you set this to No the text will be highlighted when hovered over. If it is set to true, in addition to the text a highlight background will be drawn on the items in the menu.</p>
							<select id="highlightMenuHover" name="highlightMenuHover">
								<?php
									if($highlightMenuHover){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							<p>Footer</p>
						</td>
						<td>
							<p>The footer contains three fields. The one on the right will show an icon to enter your admin section and also the alternative (mobile/desktop) site. The middle section is for credits and link for Meteotemplate. Do not remove this section, the template is available for free but the license includes that you must keep this part untouched. The left section is where you can enter your own content. Below you can either select to choose the current date, time or both, or your own custom text.</p>
							<select id="customFooterDisplay" name="customFooterDisplay" style="margin-bottom:15px">
								<option value="dateTime" <?php if($customFooterDisplay=="dateTime"){echo "selected";}?>>Show current date and time</option>
								<option value="date" <?php if($customFooterDisplay=="date"){echo "selected";}?>>Show current date</option>
								<option value="time" <?php if($customFooterDisplay=="time"){echo "selected";}?>>Show current time</option>
								<option value="custom" <?php if($customFooterDisplay=="custom"){echo "selected";}?>>Custom text</option>
							</select>
							<input id="customFooterText" name="customFooterText" value="<?php echo $customFooterText?>">
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Global font size
						</td>
						<td>
							<select id="customGlobalFontSize" name="customGlobalFontSize" style="margin-bottom:15px">
								<option value="1.0em" <?php if($customGlobalFontSize=="1.0em"){echo "selected";}?>>Normal</option>
								<option value="0.8em" <?php if($customGlobalFontSize=="0.8em"){echo "selected";}?>>Very small</option>
								<option value="0.9em" <?php if($customGlobalFontSize=="0.9em"){echo "selected";}?>>Small</option>
								<option value="1.1em" <?php if($customGlobalFontSize=="1.1em"){echo "selected";}?>>Large</option>
								<option value="1.2em" <?php if($customGlobalFontSize=="1.2em"){echo "selected";}?>>Very large</option>
							</select>
						</td>
					</tr>
					<tr>
						<td style="vertical-align:top">
							Graph font size
						</td>
						<td>
							<select id="customGraphFontSize" name="customGraphFontSize" style="margin-bottom:15px">
								<option value="12px" <?php if($customGraphFontSize=="12px"){echo "selected";}?>>Normal</option>
								<option value="9px" <?php if($customGraphFontSize=="9px"){echo "selected";}?>>Very small</option>
								<option value="11px" <?php if($customGraphFontSize=="11px"){echo "selected";}?>>Small</option>
								<option value="15px" <?php if($customGraphFontSize=="15px"){echo "selected";}?>>Large</option>
								<option value="18px" <?php if($customGraphFontSize=="18px"){echo "selected";}?>>Very large</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Hide admin icon
						</td>
						<td>
							<p>Meteotemplate is administred via the Meteotemplate control panel. This control panel is only accessible using the admin password you specified above. Normally the control panel is entered by clicking a "lock" icon in the footer on the right. If however you want this to make slightly less visible you can disable this icon here. If you do this, you will be able to access the control panel by clicking on the Czech flag you have in the footer.</p>
							<select id="hideAdminEntrance" name="hideAdminEntrance">
								<?php
									if($hideAdminEntrance){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							Hide help button
						</td>
						<td>
							<p>In the top right corner of the header is a help button. Clicking this button shows the user descriptions of icons and a text, which you can specify in the Info pages setting in the Meteotemplate control panel - i.e. you can give any information you think might be useful to the visitor to use the page as such. Here you can disable this button. If you disable it, the button will disappear from the header and won't be possible to click.</p>
							<select id="hideHelpOpener" name="hideHelpOpener">
								<?php
									if($hideHelpOpener){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td style="text-align:left">
							Enable keyboard shortcuts
						</td>
						<td>
							<p>Some pages can use keyboard shortcuts, which override the default browser shortcuts. The list of where and how you can use these is provided in the wiki.</p>
							<select id="enableKeyboard" name="enableKeyboard">
								<?php
									if($enableKeyboard){
								?>
										<option value="1" selected>Yes</option>
										<option value="0">No</option>
								<?php
									}
									else{
								?>
										<option value="1">Yes</option>
										<option value="0" selected>No</option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				</div>
				<div class="sectionDiv">
				<h3>Fine Tuning</h3>
					<p>
						You can skip this section and leave it to default. If however you want to do some fine modifications to the design, feel free to change the defaults. 
					</p>
					<table>
						<tr>
							<td style="vertical-align:top;width:15%">
								Main page title
							</td>
							<td>
								<br>
								Use <span style='font-variant:small-caps'>small-caps</span>:&nbsp;&nbsp;
								<select id="titleSmallCaps" name="titleSmallCaps">
									<?php
										if($titleSmallCaps){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br>
								Use <b>bold</b> text:&nbsp;&nbsp;
								<select id="titleBoldText" name="titleBoldText">
									<?php
										if($titleBoldText){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br> 
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Main page subtitle
							</td>
							<td>
								<br>
								Use <span style='font-variant:small-caps'>small-caps</span>:&nbsp;&nbsp;
								<select id="subtitleSmallCaps" name="subtitleSmallCaps">
									<?php
										if($subtitleSmallCaps){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br>
								Use <b>bold</b> text:&nbsp;&nbsp;
								<select id="subtitleBoldText" name="subtitleBoldText">
									<?php
										if($subtitleBoldText){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br> 
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Menu links
							</td>
							<td>
								<br>
								Use upper case:&nbsp;&nbsp;
								<select id="menuLinksUpper" name="menuLinksUpper">
									<?php
										if($menuLinksUpper){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Block "more" link
							</td>
							<td>
								Some blocks have a so-called "more" section to show more details. This section is opened by clicking a "more" link. By default this link looks like a regular link. If you want it to really stand out and be highlighted you can enable this here. By default this feature is disabled because in my opinion it looks better off, but feel free to experiment...
								<br><br>
								Highlight "more" links:&nbsp;
								<select id="moreLinkHighlight" name="moreLinkHighlight">
									<?php
										if($moreLinkHighlight){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br><br>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Footer station status
							</td>
							<td>
								Show station status (online/offline) in footer
								<br><br>
								<select id="showFooterStationStatus" name="showFooterStationStatus">
									<?php
										if($showFooterStationStatus){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select><br><br>
							</td>
						</tr>
						<tr>
							<td style="vertical-align:top">
								Footer season images
							</td>
							<td>
								Enabling this will insert a little icon representing current season in the footer on the left. This will take into account your hemisphere.
								<br>
								<select id="footerSeasonImages" name="footerSeasonImages">
									<?php
										if($footerSeasonImages){
									?>
											<option value="1" selected>Yes</option>
											<option value="0">No</option>
									<?php
										}
										else{
									?>
											<option value="1">Yes</option>
											<option value="0" selected>No</option>
									<?php
										}
									?>
								</select>
								<br>
								If the above is enabled, also specify how you want to define season. Meteorological seasons always begin on the 1st of a particular month (March, June, September, December). If you select astronomical, the image will be based on astronomical seasons, which are defined by solstice and equinox. In such case the image will switch at exactly the second of solstice/equinox.<br>
								<select id="footerSeasonImagesType" name="footerSeasonImagesType" style="margin-bottom:15px">
									<option value="astro" <?php if($footerSeasonImagesType=="astro"){echo "selected";}?>>Astronomical seasons</option>
									<option value="meteo" <?php if($footerSeasonImagesType=="meteo"){echo "selected";}?>>Meteorological seasons</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="sectionDiv" style="text-align:center">
					<input type="submit" value="Save" id="submitBtn">
				</div>
			</div>
		</div>
				<input type="hidden" name="templateVersion" value="<?php echo $templateVersionCurrent?>">
				<input type="hidden" name="versionName" value="<?php echo $versionNameCurrent?>">
			</form>
		<script type="text/javascript">
			function validateForm(){
				// check for http in path
				path = $("#pageURL").val();
				pathCheck = path.includes("http");
				if(pathCheck==false){
					alert("Check your paths, make sure it contains http:// as specified in the wiki!");
					return false;
				}

				// check station country code
				countryCode = $("#stationCountry").val();
				if(countryCode.length!=2){
					alert("Check your ISO-2 country code. It needs to be a 2-letter abbreviation of your country. For example: 'cz', 'us', 'es', 'ca' etc.");
					return false;
				}
			}
			previewDesign();
			var geocoder = new google.maps.Geocoder();
			function geocodePosition(pos) {
				geocoder.geocode({
					latLng: pos
				}, function(responses) {
					if (responses && responses.length > 0){
						country = getCountry(responses[0].address_components);
						city = getCity(responses[0].address_components);
						$("#stationCountry").val(country);
						$("#exampleFlag").html("<img src='../imgs/flags/big/"+country.toLowerCase()+".png' style='width:40px'>");
						$("#stationCity").val(city);
						$("#exampleSubtitle").html(city);
					}
					else{
						$("#stationCountry").val();
						$("#exampleFlag").html("");
						$("#stationCity").val();
						$("#exampleSubtitle").html();
					}
				});
			}
			$("#dateFormatSelector").change(function(){
				current = $("#dateFormat").val();
				$("#dateFormat").val(current + $("#dateFormatSelector").val());
			});
			$("#timeFormatSelector").change(function(){
				current = $("#timeFormat").val();
				$("#timeFormat").val(current + $("#timeFormatSelector").val());
			});
			$("#dateTimeFormatSelector").change(function(){
				current = $("#dateTimeFormat").val();
				$("#dateTimeFormat").val(current + $("#dateTimeFormatSelector").val());
			});
			function updateMarkerPosition(latLng) {
				latRounded = Math.round(latLng.lat()*10000)/10000;
				$("#stationLat").val(latRounded);
				lonRounded = Math.round(latLng.lng()*10000)/10000;
				$("#stationLon").val(lonRounded);
			}
			<?php
				if(!file_exists("../config.php")){
			?>
					function initialize() {
						var latLng = new google.maps.LatLng($("#stationLat").val(), $("#stationLon").val());
						var map = new google.maps.Map(document.getElementById('mapCanvas'), {
							zoom: 12,
							center: latLng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});
						var marker = new google.maps.Marker({
							position: latLng,
							title: 'Station',
							map: map,
							draggable: true
						});
						updateMarkerPosition(latLng);
						geocodePosition(latLng);
						google.maps.event.addListener(marker, 'drag', function() {
							updateMarkerPosition(marker.getPosition());
						});
						google.maps.event.addListener(marker, 'dragend', function() {
							geocodePosition(marker.getPosition());
						});
					}
			<?php
				}
				else{
			?>
					function initialize() {
						var latLng = new google.maps.LatLng(<?php echo $stationLat?>, <?php echo $stationLon?>);
						var map = new google.maps.Map(document.getElementById('mapCanvas'), {
							zoom: 12,
							center: latLng,
							mapTypeId: google.maps.MapTypeId.ROADMAP
						});
						var marker = new google.maps.Marker({
							position: latLng,
							title: 'Station',
							map: map,
							draggable: true
						});
						updateMarkerPosition(latLng);
						//geocodePosition(latLng);
						google.maps.event.addListener(marker, 'drag', function() {
							updateMarkerPosition(marker.getPosition());
						});
						google.maps.event.addListener(marker, 'dragend', function() {
							geocodePosition(marker.getPosition());
						});
						country = $("#stationCountry").val();
						city = $("#stationCity").val();
						$("#exampleFlag").html("<img src='../imgs/flags/big/"+country.toLowerCase()+".png' style='width:40px'>");
						$("#exampleSubtitle").html(city);
					}
			<?php
				}
			?>
			function getCountry(addrComponents) {
				for (var i = 0; i < addrComponents.length; i++) {
					if (addrComponents[i].types[0] == "country") {
						return addrComponents[i].short_name;
					}
					if (addrComponents[i].types.length == 2) {
						if (addrComponents[i].types[0] == "political") {
							return addrComponents[i].short_name;
						}
					}
				}
				return false;
			}
			function getCity(addrComponents) {
				for (var i = 0; i < addrComponents.length; i++) {
					if (addrComponents[i].types[0] == "locality") {
						return addrComponents[i].short_name;
					}
					if (addrComponents[i].types.length == 2) {
						if (addrComponents[i].types[0] == "political") {
							return addrComponents[i].short_name;
						}
					}
				}
				return false;
			}
			$.geolocation.get({win: positionOK, fail: noPosition});
			function positionOK(position){
				var lat = Math.round(position.coords.latitude * 10000)/10000;
				var lon = Math.round(position.coords.longitude * 10000)/10000;
				$("#stationLat").val(lat);
				$("#stationLon").val(lon);
				initialize();
			}
			function noPosition(){
				$("#stationLat").val(0);
				$("#stationLon").val(0);
				initialize();
			}
			function langSelect(lang){
				$("#defaultLang").val(lang);
			}
			$(".langIcon").click(function(){
				var id = $(this).attr('id');
				$(".langIcon").css("width","30px");
				$("#"+id).css("width","50px");
			})
			$("#color1").change(function(){
				previewDesign();
				changeBg();
			});
			$("#color2").change(function(){
				previewDesign();
				changeBg();
			});
			$("#country").change(function(){
				country = $("#country").val();
				changeBg();
				$("#exampleFlag").html("<img src='../imgs/flags/big/"+country.toLowerCase()+".png' style='width:70px'>");
			});
			if($("#headerLeftImg").val()=="flag"){
				$("#customHeaderLeftImgDiv").hide();
			}
			$("#headerLeftImg").change(function(){
				if($("#headerLeftImg").val()=="flag"){
					$("#customHeaderLeftImg").val("");
					$("#customHeaderLeftImgDiv").hide();
				}
				if($("#headerLeftImg").val()=="custom"){
					$("#customHeaderLeftImg").val("");
					$("#customHeaderLeftImgDiv").show();
				}
			})
			if($("#customBgType").val()!="image"){
				$("#customBgImgDiv").hide();
			}
			$("#customBgType").change(function(){
				if($("#customBgType").val()=="image"){
					$("#customBgImg").val("");
					$("#customBgImgDiv").show();
				}
				else{
					$("#customBgImgDiv").hide();
				}
			})
			if($("#headerImg").val()=="interactive"){
				$("#customHeaderImgDiv").hide();
			}
			$("#headerImg").change(function(){
				if($("#headerImg").val()=="interactive"){
					$("#customHeaderImg").val("");
					$("#customHeaderImgDiv").hide();
				}
				if($("#headerImg").val()=="custom"){
					$("#customHeaderImg").val("");
					$("#customHeaderImgDiv").show();
				}
			})
			if($("#headerTitleSelect").val()=="hide"){
				$("#headerTitleText").hide();
			}
			$("#headerTitleSelect").change(function(){
				if($("#headerTitleSelect").val()=="hide"){
					$("#headerTitleText").val("");
					$("#headerTitleText").hide();
				}
				if($("#headerTitleSelect").val()=="show"){
					$("#headerTitleText").val("Title");
					$("#headerTitleText").show();
				}
			})
			if($("#headerSubtitleSelect").val()=="hide"){
				$("#headerSubtitleText").hide();
			}
			if($("#customFooterDisplay").val()!="custom"){
				$("#customFooterText").hide();
			}
			$("#headerSubtitleSelect").change(function(){
				if($("#headerSubtitleSelect").val()=="hide"){
					$("#headerSubtitleText").val("");
					$("#headerSubtitleText").hide();
				}
				if($("#headerSubtitleSelect").val()=="show"){
					$("#headerSubtitleText").val("Subtitle");
					$("#headerSubtitleText").show();
				}
			})
			$("#customFooterDisplay").change(function(){
				if($("#customFooterDisplay").val()!="custom"){
					$("#customFooterText").val("");
					$("#customFooterText").hide();
				}
				if($("#customFooterDisplay").val()=="custom"){
					$("#customFooterText").val("");
					$("#customFooterText").show();
				}
			})
			backgrounds = [
				<?php
					for($i=0;$i<count($availableBgs);$i++){
						echo "'".$availableBgs[$i]."',";
					}
				?>
			];
			function previewDesign(){
				colorArray = [];
				<?php
					$i=0;
					foreach($color_schemes as $scheme){
				?>
						colorArray['<?php echo strtolower($availableSchemes[$i])?>100'] = "#<?php echo $scheme['100']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>200'] = "#<?php echo $scheme['200']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>300'] = "#<?php echo $scheme['300']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>400'] = "#<?php echo $scheme['400']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>500'] = "#<?php echo $scheme['500']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>600'] = "#<?php echo $scheme['600']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>700'] = "#<?php echo $scheme['700']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>800'] = "#<?php echo $scheme['800']?>";
						colorArray['<?php echo strtolower($availableSchemes[$i])?>900'] = "#<?php echo $scheme['900']?>";

				<?php
					$i++;
					}
				?>
				color1 = $("#color1").val();
				color2 = $("#color2").val();
				gradient = $("#gradient").val();
				$("#designExampleBanner").css("background-color",colorArray[color1+"900"]);
				$("#designExampleMenu").css("background-color",colorArray[color2+"900"]);
				$("#designExampleFooter").css("background-color",colorArray[color2+"900"]);
				if(gradient==1){
					$(".exampleBlock").css("background-image","-ms-linear-gradient(top, "+colorArray[color1+"900"]+" 0%, "+colorArray[color1+"800"]+" 50%, "+colorArray[color1+"900"]+" 100%)");
					$(".exampleBlock").css("background-image","-moz-linear-gradient(top, "+colorArray[color1+"900"]+" 0%, "+colorArray[color1+"800"]+" 50%, "+colorArray[color1+"900"]+" 100%)");
					$(".exampleBlock").css("background-image","-o-linear-gradient(top, "+colorArray[color1+"900"]+" 0%, "+colorArray[color1+"800"]+" 50%, "+colorArray[color1+"900"]+" 100%)");
					$(".exampleBlock").css("background-image","-webkit-gradient(linear, left top, left bottom, color-stop(0, "+colorArray[color1+"900"]+"), color-stop(50, "+colorArray[color1+"800"]+", color-stop(100, "+colorArray[color1+"900"]+"))");
					$(".exampleBlock").css("background-image","-webkit-linear-gradient(top, "+colorArray[color1+"900"]+" 0%, "+colorArray[color1+"800"]+" 50%, "+colorArray[color1+"900"]+" 100%)");

					$(".exampleBlock").css("background-image","linear-gradient(to bottom, "+colorArray[color1+"900"]+" 0%, "+colorArray[color1+"800"]+" 50%, "+colorArray[color1+"900"]+" 100%)");
				}
				else{
					$(".exampleBlock").css("background-image","none");
					$(".exampleBlock").css("background-color",colorArray[color1+"900"]);
				}
			}
			$("#designTable").css("font-family","'"+$("#font1").val()+"'");
			$("#gradient").change(function(){
				previewDesign();
			});
			$("#font1").change(function(){
				$("#designTable").css("font-family","'"+$("#font1").val()+"'");
				changeBg();
			});
			$("#font2").change(function(){
				changeBg();
				$("#designExampleMenu").css("font-family","'"+$("#font2").val()+"'");
				$("#exampleHeading").css("font-family","'"+$("#font2").val()+"'");
			});
			$("#pageName").on('keyup change',function(){
				name = $("#pageName").val();
				$("#exampleHeading").html(name.toUpperCase());
				changeBg();
			});
			$("#stationCity").on('keyup change',function(){
				name = $("#stationCity").val();
				$("#exampleSubtitle").html(name.toUpperCase());
				changeBg();
			});
			function setBlockExample(){
				$("#blockExampleDiv").css('border-radius',$("#customBlockRadius").val());
				$("#blockExampleDiv").css('border-width',$("#customBlockBorderWidth").val());
				$("#blockExampleDiv").css('-moz-box-shadow','inset '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4), inset -'+ $("#customBlockBevel").val() +' -'+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4)');
				$("#blockExampleDiv").css('-webkit-box-shadow','inset '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4), inset -'+ $("#customBlockBevel").val() +' -'+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4)');
				$("#blockExampleDiv").css('box-shadow','inset '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4), inset -'+ $("#customBlockBevel").val() +' -'+ $("#customBlockBevel").val() +' '+ $("#customBlockBevel").val() +' rgba(0, 0, 0, .4)');
			}
			$("#customBlockRadius").change(function(){
				setBlockExample();
			});
			$("#customBlockBorderWidth").change(function(){
				setBlockExample();
			});
			$("#customBlockBevel").change(function(){
				setBlockExample();
			});
			$("#customHeadingShadow").change(function(){
				$("#headingExample").css('text-shadow',$("#customHeadingShadow").val());
			});
			$("#customBodyTextShadow").change(function(){
				$("#bodyTextExample").css('text-shadow',$("#customBodyTextShadow").val());
			});
			changeBg();
			function changeBg(){
				count = backgrounds.length;
				random = Math.floor((Math.random() * count));
				imgUrl = backgrounds[random];
				$("#exampleTopBanner").css("background-image","url('"+imgUrl+"')");
			}
			function post(path, params, method) {
				method = method || "post";
				var form = document.createElement("form");
				form.setAttribute("method", method);
				form.setAttribute("action", path);
				form.setAttribute("target", "_blank");

				for(var key in params) {
					if(params.hasOwnProperty(key)) {
						var hiddenField = document.createElement("input");
						hiddenField.setAttribute("type", "hidden");
						hiddenField.setAttribute("name", key);
						hiddenField.setAttribute("value", params[key]);

						form.appendChild(hiddenField);
					 }
				}

				document.body.appendChild(form);
				form.submit();
			}
			$("#testMySQL").click(function(){
				host = $("#mySQLHost").val();
				user = $("#mySQLUser").val();
				pass = $("#mySQLPassword").val();
				name = $("#mySQLName").val();
				post('testMySQL.php', {host: host, user: user, pass: pass, name: name});
			})
		</script>
		<script>
			var errors = [];
			/*$.validate({
				form : '#configForm',
				onError : function($form) {
				  //alert('Validation of form '+$form.attr('id')+' failed!');
				},
				onSuccess : function($form) {
				  //alert('The form '+$form.attr('id')+' is valid!');
				},
				onElementValidate : function(valid, $el, $form, errorMess) {
				 if( !valid ) {
				  errors.push({el: $el, error: errorMess});
				  alert(errorMess);
				 }
			  }
			});*/
			$(document).ready(function() {
				//$("#stationLat").validate();
				//$("#stationLon").validate();
				//$("#stationMETAR").validate();
				previewBackground();
				$(".colorPicker").colorpicker({
					defaultPalette: 'web',
					history: false,
					 displayIndicator: false
				});
				$(".colorPicker").change(function(){
					previewBackground();
				})
				$("#customBgImg").change(function(){
					url = $("#customBgImg").val();
					$("#backgroundPreview").css("background-size","100% 100%");
					$("#backgroundPreview").css("background-repeat","no-repeat");
					$("#backgroundPreview").css("background","url("+url+")");
				})
				$("#customBgType").change(function(){
					previewBackground();
				})
				$("#predefinedBG").change(function(){
					value = $("#predefinedBG").val();
					<?php
						for($i=0;$i<count($colorThemeGradients);$i++){
					?>
							if(value=="<?php echo $colorThemeGradients[$i][0]?>"){
								$("#customBgColor1").val("<?php echo $colorThemeGradients[$i][1]?>");
								$("#customBgColor2").val("<?php echo $colorThemeGradients[$i][2]?>");
								$("#customBgColor3").val("<?php echo $colorThemeGradients[$i][3]?>");
								$("#customBgColor4").val("<?php echo $colorThemeGradients[$i][4]?>");
							}
					<?php
						}
					?>
					previewBackground();
				})
			});
			function previewBackground(){
				colors = [];
				customColor1 = $("#customBgColor1").val();
				customColor2 = $("#customBgColor2").val();
				customColor3 = $("#customBgColor3").val();
				customColor4 = $("#customBgColor4").val();
				if(customColor1!=""){
					colors.push(customColor1);
				}
				if(customColor2!=""){
					colors.push(customColor2);
				}
				if(customColor3!=""){
					colors.push(customColor3);
				}
				if(customColor4!=""){
					colors.push(customColor4);
				}
				type = $("#customBgType").val();
				if(type=="vertical"){
					$("#backgroundPreview").css("background","-webkit-linear-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","-o-linear-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","-moz-linear-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","linear-gradient("+colors.join()+")");
				}
				if(type=="horizontal"){
					$("#backgroundPreview").css("background","-webkit-linear-gradient(left,"+colors.join()+")");
					$("#backgroundPreview").css("background","-o-linear-gradient(right,"+colors.join()+")");
					$("#backgroundPreview").css("background","-moz-linear-gradient(right,"+colors.join()+")");
					$("#backgroundPreview").css("background","linear-gradient(to right,"+colors.join()+")");
				}
				if(type=="radial"){
					$("#backgroundPreview").css("background","-webkit-radial-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","-o-radial-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","-moz-radial-gradient("+colors.join()+")");
					$("#backgroundPreview").css("background","radial-gradient("+colors.join()+")");
				}
				if(type=="image"){
					$("#backgroundPreview").css("background","black");
				}
			}
			$("#testEmailSend").click(function(){
				mailAddress = $("#testEmailAddress").val();
				window.open("testEmail.php?address="+encodeURIComponent(mailAddress));
			}) 
		</script>
	</body>
</html>
