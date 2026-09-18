<?php
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$layout = trim($_GET['layout']);
    $type = trim($_GET['type']);

    if($type=="desktop"){
        $data = file_get_contents("layouts/".$type."/".$layout.".txt");
        if($data!=""){
            file_put_contents("homepageLayoutDesktop.txt",$data);
        }
        header("Location: createHomepage.php?type=desktop");
    }
    else{
        $data = file_get_contents("layouts/".$type."/".$layout.".txt");
        if($data!=""){
            file_put_contents("homepageLayoutMobile.txt",$data);
        }
        header("Location: createHomepage.php?type=mobile");
    }


	
?>
