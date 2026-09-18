<?php

	// add new menu tab to the end

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$name = trim(urldecode($_GET['name']));
	$namespace = trim(urldecode($_GET['namespace']));
	$namespace = str_replace(" ","",$namespace); // remove spaces just in case
	$icon = trim(urldecode($_GET['tabIcon']));

	if(file_exists("menuItems.txt")){ // if some tabs exist load them
		$menuItems = json_decode(file_get_contents("menuItems.txt"),true);
	}
	else{ // no tabs yet, create blank
		$menuItems = array();
	}

	$newTab['name'] = $name;
	$newTab['namespace'] = $namespace;
	$newTab['tabIcon'] = $icon;
	$newTab['content'] = "";

	$menuItems[$namespace] = $newTab;

	if(file_exists("menuOrder.txt")){ // if some tabs exist load them
		$menuOrder = json_decode(file_get_contents("menuOrder.txt"),true);
	}
	else{ // no tabs yet, create blank
		$menuOrder = array();
	}

	$menuOrder[$namespace] = $namespace; // add new tab

	// write new files
	file_put_contents("menuItems.txt",json_encode($menuItems));
	file_put_contents("menuOrder.txt",json_encode($menuOrder));

	header("Location: menuTabs.php");
?>
