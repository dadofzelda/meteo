<?php
	//error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	file_put_contents("menuItems.txt","default");
	file_put_contents("menuOrder.txt","default");

	header("Location: menuTabs.php");
?>
