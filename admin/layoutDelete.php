<?php
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$layout = trim($_GET['layout']);
    $type = trim($_GET['type']);

	if(file_exists("layouts/".$type."/".$layout.".txt")){
		unlink("layouts/".$type."/".$layout.".txt");
	}

	header("Location: manageLayouts.php");
?>
