<?php
	//error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$tab = trim($_GET['tab']);
	$name = trim($_GET['name']);
	$namespace = trim($_GET['namespace']);
	$tabIcon = trim($_GET['tabIcon']);

	if(file_exists("menuItems.txt")){
		$menuItems = json_decode(file_get_contents("menuItems.txt"),true);
	}

	foreach($menuItems as $menuItem){
		$thisTabName = $menuItem['name'];
		$thisTabNamespace = $menuItem['namespace'];
		$thisTabContent = $menuItem['content'];
		$thisTabIcon = $menuItem['tabIcon'];
		$thisTabLink = $menuItem['link'];
		if($thisTabNamespace==$tab){
			if($namespace!="null"){
				$thisTabNamespace = $namespace;
			}
			if($name!="null"){
				$thisTabName = $name;
			}
			if($tabIcon!="null"){
				$thisTabIcon = $tabIcon;
			}
		}
		$newMenuItems[$thisTabNamespace] = array("name"=>$thisTabName,"namespace"=>$thisTabNamespace,"tabIcon"=>$thisTabIcon,"content"=>$thisTabContent,"link"=>$thisTabLink);
	}

	file_put_contents("menuItems.txt",json_encode($newMenuItems));

	if(file_exists("menuOrder.txt")){
		$menuOrder = json_decode(file_get_contents("menuOrder.txt"),true);
	}
	foreach($menuOrder as $item){
		if($item==$tab){
			if($namespace!="null"){
				$item = $namespace;
			}
		}
		$newMenuOrder[$item] = $item;
	}

	file_put_contents("menuOrder.txt",json_encode($newMenuOrder));

	header("Location: menuTabs.php");
?>
