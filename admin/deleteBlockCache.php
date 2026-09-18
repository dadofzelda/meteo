<?php 

    session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

    $cacheToDelete = $_GET['block'];

    $filesToDelete = glob("../homepage/blocks/".$cacheToDelete."/cache/*");

    if(count($filesToDelete) > 0){
        foreach($filesToDelete as $deleteFile){
            echo $deleteFile."<br>";
            unlink($deleteFile);
        }
    }

    echo "Deleted ".count($filesToDelete). " file(s).";
