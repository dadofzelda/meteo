<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$text['weatherStation'] = $_POST['weatherStation'];
	$text['webpage'] = $_POST['webpage'];
	$text['location'] = $_POST['location'];
	$text['help'] = $_POST['help'];
	$text['links'] = $_POST['links'];
	
	file_put_contents("infoPages.txt",json_encode($text));
	
	if(file_exists("infoPages.txt")){
		echo "<script>alert('Your text was saved successfully!');close();</script>";
	}
	else{
		echo "<script>alert('The text could not be saved. Make sure the admin folder has correct permissions to save files.');close();</script>";
	}

?>