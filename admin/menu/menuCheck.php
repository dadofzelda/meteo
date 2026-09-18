<?php

	// check which installed pages are missing in current menu

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	// first load the current menu data
	if(file_exists("menuItems.txt")){
		$userMenuFile = file_get_contents("menuItems.txt");
	}
	else{ // checking non-existent menu
		die("No menu file exists. Before doing menu check you need to create a menu.");
	}

	// now load the default pages, which should be included in core template
	$defaultMenuFile = file_get_contents("defaultItems.txt");

	// let's iterate the files and prepare arrays of items
	$defaultItemsRaw = json_decode($defaultMenuFile,true);
	foreach($defaultItemsRaw as $defaultRow){
		$currentTabContent = $defaultRow['content'];
		$tabContent = str_replace("\n\r","\n",$currentTabContent);
		$tabContent = str_replace("\r\n","\n",$tabContent);
		$tabContent = str_replace("\r","\n",$tabContent);
		$tabContent = str_replace("(new)","",$tabContent); // remove the "new" word
		$tabRows = explode("\n",$tabContent);
		for($a=0;$a<count($tabRows);$a++){
			if(substr($tabRows[$a],0,8)=="#default"){
				$thisPage = str_replace("#default(","",$tabRows[$a]);
				$thisPage = str_replace(")","",$thisPage);
				$thisPage = trim($thisPage);
				$defaultItems[] = $thisPage;
			}
		}
	}

	// now check what user has
	$userItems = array();
	$userPlugins = array();
	$userItemsRaw  = json_decode($userMenuFile,true);
	if(count($userItemsRaw)>0){
		foreach($userItemsRaw as $userRow){
			$currentTabContent = $userRow['content'];
			$tabContent = str_replace("\n\r","\n",$currentTabContent);
			$tabContent = str_replace("\r\n","\n",$tabContent);
			$tabContent = str_replace("\r","\n",$tabContent);
			$tabContent = str_replace("(new)","",$tabContent); // remove the "new" word
			$tabRows = explode("\n",$tabContent);
			for($a=0;$a<count($tabRows);$a++){
				if(substr($tabRows[$a],0,8)=="#default"){
					$thisPage = str_replace("#default(","",$tabRows[$a]);
					$thisPage = str_replace(")","",$thisPage);
					$userItems[] = $thisPage;
				}
				if(substr($tabRows[$a],0,7)=="#plugin"){
					$thisPlugin = str_replace("#plugin(","",$tabRows[$a]);
					$thisPlugin = str_replace(")","",$thisPlugin);
					$userPlugins[] = $thisPlugin;
				}
			}
		}
	}
	else{
		echo "Your menu file is empty!";
	}

	// now check which plugins the user has
	$dirs = array_filter(glob('../../plugins/*'), 'is_dir');
	if(count($dirs)>0){
		foreach($dirs as $dir){
			$dir = str_replace("../../plugins/","",$dir);
			$pluginList[] = $dir;
		}
	}

	// now compare
	$missingItems = array();
	for($i=0;$i<count($defaultItems);$i++){
		$currentItem = $defaultItems[$i];
		if(!in_array($currentItem,$userItems)){
			$currentItemClean = str_replace("#default(","",$currentItem);
			$currentItemClean = str_replace(")","",$currentItemClean);
			$missingItems[] = $currentItemClean;
		}
	}

	$missingPlugins = array();
	for($i=0;$i<count($pluginList);$i++){
		$currentPlugin = $pluginList[$i];
		if(!in_array($currentPlugin,$userPlugins)){
			$currentItemClean = str_replace("#plugin(","",$currentPlugin);
			$currentItemClean = str_replace(")","",$currentItemClean);
			// ignore some plugins
			if($currentItemClean!="stickers" && $currentItemClean!="netAtmo" && $currentItemClean!="weatherCat" && $currentItemClean!="wuUpload" && $currentItemClean!="wlIP" && $currentItemClean!="bloomSky" && $currentItemClean!="notifications"){
				$missingPlugins[] = $currentItemClean;
			}
		}
	}

	echo "<h2>Default Pages</h2>";
	if(count($missingItems)==0){
		echo "All default pages are included in your menu :)";
	}
	else{
		echo "Your menu is missing the following default pages:";
		echo "<ul>";
		foreach($missingItems as $missingItem){
			echo "<li>".$missingItem."</li>";
		}
		echo "</ul>";
	}

	echo "<h2>Installed plugins</h2>";
	if(count($missingPlugins)==0){
		echo "All installed plugins are included in your menu :)";
	}
	else{
		echo "Your menu is missing the following installed plugins:";
		echo "<ul>";
		foreach($missingPlugins as $missingPlugin){
			echo "<li>".$missingPlugin."</li>";
		}
		echo "</ul>";
	}
?>
