<?php 
	//error_reporting(E_ALL);
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$order = urldecode($_GET['order']);
	$order = explode(";",$order);
	
	for($i=0;$i<count($order);$i++){
		$newOrder[$order[$i]] = $order[$i];
	}
	
	file_put_contents("menuOrder.txt",json_encode($newOrder));
	
	header("Location: menuTabs.php");
?>
	