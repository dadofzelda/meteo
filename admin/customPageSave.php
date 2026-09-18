<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    $savedData = json_decode(file_get_contents("customPages.txt"),true);

    $nameSpace = trim($_POST['namespace']);

	$savedData[$nameSpace] = $_POST['htmlCode'];
	
	file_put_contents("customPages.txt",json_encode($savedData));

    header("Location: customPages.php");

?>