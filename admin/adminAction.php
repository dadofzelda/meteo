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
	#	Administration Ajax page
	#
	# 	This page is called by Ajax from the admininstration page and performs
	#  	desired action.
	#
	############################################################################

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../config.php");

	$action = $_GET['action'];

	// delete METAR cache
	if($action=="metarCache"){
		$files = glob('../pages/metar/buffer/metar/*');
		if(count($files)>0){
			foreach($files as $file){
			  if(is_file($file))
				unlink($file);
			}
		}
	}

	// delete forecast cache
	else if($action=="forecastCache"){
		if(file_exists("../pages/forecast/wwoCache.txt")){
			unlink("../pages/forecast/wwoCache.txt");
		}
	}

	// delete station cache
	else if($action=="stationCache"){
		$files = glob('../pages/station/cache/*');
		if(count($files)>0){
			foreach($files as $file){
			  	if(is_file($file)){
					unlink($file);
				}
			}
		}
	}

	// backup database
	else if($action=="dbBackup"){
		// create backup directory if necessary
		if (!is_dir('../backup')) {
			mkdir('../backup', 0777, true);
		}
		if (!is_dir('../backup/'.date("Ymd").'db')) {
			mkdir('../backup/'.date("Ymd").'db', 0777, true);
		}

		$backupYear = trim($_GET['backupYear']);

		if($backupYear=="all"){
			$result = mysqli_query($con,"
				SELECT *
				FROM alldata
				ORDER BY DateTime
				"
			);
			$fp = fopen('../backup/'.date("Ymd").'db/alldata.csv', 'w');
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				fputcsv($fp, $row);
			}
			fclose($fp);

			// create ZIP file
			$urlBackup = $pageURL.$path.'backup/'.date("Ymd").'db/alldata.csv';
			echo $urlBackup;
		}
		else{
			$result = mysqli_query($con,"
				SELECT *
				FROM alldata
				WHERE YEAR(DateTime)=$backupYear
				ORDER BY DateTime
				"
			);
			$fp = fopen('../backup/'.date("Ymd").'db/alldata'.$backupYear.'.csv', 'w');
			while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
				fputcsv($fp, $row);
			}
			fclose($fp);

			// create ZIP file
			$urlBackup = $pageURL.$path.'backup/'.date("Ymd").'db/alldata'.$backupYear.'.csv';
			echo $urlBackup;
		}
	}

	// backup files
	else if($action=="filesBackup"){
		// create backup directory if necessary
		if (!is_dir('../backup/'.date("Ymd")."files")) {
			mkdir('../backup/'.date("Ymd")."files", 0777, true);
		}
		$files = array();

		if(file_exists("../config.php")){
			$files[] = array("../config.php","config.php");
		}
		$files[] = array("../header.php","header.php");
		if(file_exists("homepageLayoutDesktop.txt")){
			$files[] = array("homepageLayoutDesktop.txt","homepageLayoutDesktop.txt");
		}
		if(file_exists("homepageLayoutMobile.txt")){
			$files[] = array("homepageLayoutMobile.txt","homepageLayoutMobile.txt");
		}
		if(file_exists("../index.php")){
			$files[] = array("../index.php","index.php");
		}
		if(file_exists("../menu.php")){
			$files[] = array("../menu.php","menu.php");
		}
		if(file_exists("../update/wd.php")){
			$files[] = array("../update/wd.php","wdUpdate.php");
		}
		if(file_exists("../update/cumulus.php")){
			$files[] = array("../update/cumulus.php","cumulusUpdate.php");
		}
		if(file_exists("../update/meteobridge.php")){
			$files[] = array("../update/meteobridge.php","meteobridgeUpdate.php");
		}
		if(file_exists("../update/wu.php")){
			$files[] = array("../update/wu.php","wuUpdate.php");
		}
		if(file_exists("../update/wview.php")){
			$files[] = array("../update/wview.php","wviewUpdate.php");
		}
		if(file_exists("menu/menuItems.txt")){
			$files[] = array("menu/menuItems.txt","menuItems.txt");
		}
		if(file_exists("menu/menuOrder.txt")){
			$files[] = array("menu/menuOrder.txt","menuOrder.txt");
		}


		// backup all block settings
		$dirs = array_filter(glob('../homepage/blocks/*'), 'is_dir');
		$installedBlocks = array();
		if(count($dirs)>0){
			foreach($dirs as $dir){
				$blockNamespace = str_replace("../homepage/blocks/","",$dir);
				if(file_exists('../homepage/blocks/'.$blockNamespace.'/settings.php')){
					$files[] = array('../homepage/blocks/'.$blockNamespace.'/settings.php',"blockSettings".$blockNamespace.".php");
				}
			}
		}

		// backup all plugin settings
		$dirs = array_filter(glob('../plugins/*'), 'is_dir');
		$installedPlugins = array();
		if(count($dirs)>0){
			foreach($dirs as $dir){
				$pluginNamespace = str_replace("../plugins/","",$dir);
				if(file_exists('../plugins/'.$pluginNamespace.'/settings.php')){
					$files[] = array('../plugins/'.$pluginNamespace.'/settings.php',"pluginSettings".$pluginNamespace.".php");
				}
			}
		}

		foreach($files as $file){
			$fileContent = file_get_contents($file[0]);
			file_put_contents("../backup/".date("Ymd")."files/".$file[1],$fileContent);
		}
	}

	$dirs = array_filter(glob('../backup/*'), 'is_dir');
	if(count($dirs)>0){
		foreach($dirs as $dir){
			$files = glob($dir."/allFiles.zip");
			foreach($files as $file){
				unlink($file);
			}
		}
	}

	// show PHP configuration
	if($action=="phpInfo"){
		echo phpinfo();
	}

	// show installed blocks
	else if($action=="showBlocks"){
		$dirs = array_filter(glob('../homepage/blocks/*'), 'is_dir');
		$installedBlocks = array();
		if(count($dirs)>0){
			foreach($dirs as $dir){
				$blockNamespace = str_replace("../homepage/blocks/","",$dir);
				if(file_exists('../homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt')){
					$blockInfo = json_decode(file_get_contents('../homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt'),true);
					$version = $blockInfo['version'];
					$name = $blockInfo['name'];
					if(is_array($blockInfo)){
						if(array_key_exists('variables',$blockInfo)){
							$setup = "<a href='blockSettings.php?id=".$blockNamespace."'>Settings</a>";
						}
						else{
							$setup = "";
						}
						$installedBlocks[] = array($blockNamespace,1,$version,$setup,$name);
					}
				}
				else{
					$installedBlocks[] = array($blockNamespace,0);
				}
			}
		}
		echo "<style>body{color:white}</style>";
		echo "<h2>Installed Blocks</h2>";
		echo "<table>";
		if(count($installedBlocks)>0){
			foreach($installedBlocks as $block){
				if(array_key_exists(4,$block)){
					echo "<tr><td>".$block[4]." ".number_format($block[2],1,".","")."</td><td>".$block[0]."</td></tr>";
				}
				else{
					echo "<tr><td>".$block[0]."</td></tr>";
				}
			}
		}
		echo "</table>";
	}

	// show installed plugins
	else if($action=="showPlugins"){
		$dirs = array_filter(glob('../plugins/*'), 'is_dir');
		echo "<style>body{color:white}</style>";
		echo "<h2>Installed Plugins</h2>";
		echo "<ul>";
		if(count($dirs)>0){
			foreach($dirs as $dir){
				$dir = str_replace("../plugins/","",$dir);
				echo "<li>".$dir."</li>";
			}
		}
		echo "</ul>";
	}

	// reset desktop homepage
	else if($action=="resetDesktop"){
		if(file_exists("homepageLayoutDesktop.txt")){
			unlink("homepageLayoutDesktop.txt");
		}
	}

	// reset mobile homepage
	else if($action=="resetMobile"){
		if(file_exists("homepageLayoutMobile.txt")){
			unlink("homepageLayoutMobile.txt");
		}
	}

	// register user
	else if($action=="registerUser"){
		$emailAddress = $_GET['emailAddress'];
		$name = $_GET['name'];
		$message = "Hi this is ".$name.", my email is ".$emailAddress." please register my page to Meteotemplate users. My station model is ".$stationModel." and my coordinates are ".$stationLat.",".$stationLon.". The webpage URL is ".$pageURL.$path.".";
		$to = "jachymcz@gmail.com";
		$subject = "User Registration";
		$headers = "From: ".$emailAddress;
		file_put_contents("templateRegistered.txt",date("Y-m-d H:i"));
		mail($to,$subject,$message,$headers);
		$registerURL = $meteotemplateURL."/web/registration.php?lat=".$stationLat."&lon=".$stationLon."&station=".urlencode($stationType)."&url=".urlencode($pageURL.$path)."&location=".urlencode($stationLocation)."&author=".urlencode($pageAuthor)."&name=".urlencode($pageName)."&country=".$stationCountry."&myName=".urlencode($name)."&email=".urlencode($emailAddress);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $registerURL);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
		$registrationCurl = curl_exec($ch);
		curl_close($ch);
	}

	else{
	}

	function create_zip($files = array(),$destination = '',$overwrite = false) {
		if(file_exists($destination) && !$overwrite) { return false; }
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}
			}
		}
		if(count($valid_files)) {
			$zip = new ZipArchive();
			if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
				return false;
			}
			foreach($valid_files as $file) {
				$zip->addFile($file,$file);
			}
			$zip->close();
			return file_exists($destination);
		}
		else{
			return false;
		}
	}
?>
