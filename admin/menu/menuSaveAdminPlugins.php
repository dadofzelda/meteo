<?php 
	//error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$adminPlugins = $_GET['plugins'];
	
	file_put_contents("adminPlugins.txt",$adminPlugins);
	
	header("Location: menuTabs.php");
?>
	