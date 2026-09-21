<?php 
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../../config.php");
    include("../../scripts/functions.php");
	
	$tab = $_POST['tab'];
	$content = $_POST['content'];
	$link = $_POST['link'];
	
	$menuItems = json_decode(file_get_contents("menuItems.txt"),true);
	
	$menuItems[$tab]['content'] = $content;
	$menuItems[$tab]['link'] = $link;
	
	file_put_contents("menuItems.txt",json_encode($menuItems));

	header("Location: menuTabs.php");
?>
	