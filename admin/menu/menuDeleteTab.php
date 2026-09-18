<?php
	error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$tab = trim($_GET['tab']);

	if(file_exists("menuItems.txt")){
		$menuItems = json_decode(file_get_contents("menuItems.txt"),true);
	}
	else{
		$menuItems = array();
	}

	unset($menuItems[$tab]);

	file_put_contents("menuItems.txt",json_encode($menuItems));

	if(file_exists("menuOrder.txt")){
		$menuOrder = json_decode(file_get_contents("menuOrder.txt"),true);
	}
	else{
		$menuOrder = array();
	}

	unset($menuOrder[$tab]);

	file_put_contents("menuItems.txt",json_encode($menuItems));
	file_put_contents("menuOrder.txt",json_encode($menuOrder));

	header("Location: menuTabs.php");
?>
