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

	// try updating the menu
	$updatedMenu = file_get_contents($meteotemplateURL."/template/menu.txt");
    if($updatedMenu==""){
        $updatedMenu = curlMain($meteotemplateURL."/template/menu.txt",5);
    }
	if($updatedMenu!=""){     
        if(is_writable("../../menu.php")){
            file_put_contents("../../menu.php",$updatedMenu);
        }
    }
	
	header("Location: menuTabs.php");
?>
	