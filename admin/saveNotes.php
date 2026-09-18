<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$text = $_POST['notes'];
	$text = urlencode($text);
	$text = "<?php \$text='".$text."';?>";
	
	file_put_contents("adminNotesText.php",$text);
	
	if(file_exists("adminNotesText.php")){
		echo "<script>alert('Your text was saved successfully!');close();</script>";
	}
	else{
		echo "<script>alert('The text could not be saved. Make sure the admin folder has correct permissions to save files.');close();</script>";
	}

?>