<?php 
	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	} 
	include("../../config.php");
    include("../../scripts/functions.php");
    //error_reporting(E_ALL);
	$updatedMenu = file_get_contents($meteotemplateURL."/template/menu.txt");
    if($updatedMenu==""){
        $updatedMenu = curlMain($meteotemplateURL."/template/menu.txt",5);
    }
	if($updatedMenu!=""){     
        if(is_writable("../../menu.php")){
            file_put_contents("../../menu.php",$updatedMenu);
            echo "<script>alert('Menu updated!');location='".$pageURL.$path."admin/menu/menuTabs.php';</script>";
        }
        else{
            echo "<script>alert('Make sure the users.txt is writable.');location='".$pageURL.$path."admin/menu/menuTabs.php';</script>";
        }
    }
    else{
        echo "<script>alert('Menu data N/A.');location='".$pageURL.$path."admin/menu/menuTabs.php';</script>";
    }
	
?>