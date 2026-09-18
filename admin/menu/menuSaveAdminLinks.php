<?php 
	//error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$adminLinks = urldecode($_GET['links']);
	
	file_put_contents("adminLinks.txt",$adminLinks);
	
	header("Location: menuTabs.php");
?>
	