<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    $savedData = json_decode(file_get_contents("customPages.txt"),true);

    unset($savedData[$_GET['page']]);
	
	file_put_contents("customPages.txt",json_encode($savedData));

    header("Location: customPages.php");

?>