<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$type = $_GET['type'];

    $newWidths = explode(",",trim($_POST['newColumnWidths']));

    if($type=="desktop" && file_exists("homepageLayoutDesktop.txt")){
		$homepageData = json_decode(file_get_contents("homepageLayoutDesktop.txt"),true);
		$homepageData['desktop']['columnWidths'] = $newWidths;
        file_put_contents("homepageLayoutDesktop.txt",json_encode($homepageData));
        echo "<script>alert('Column widths updated.');location = 'createHomepage.php?type=".$type."';</script>";
	}
	else if($type=="mobile" && file_exists("homepageLayoutMobile.txt")){
        $homepageData = json_decode(file_get_contents("homepageLayoutMobile.txt"),true);
        $homepageData['mobile']['columnWidths'] = $newWidths;
        file_put_contents("homepageLayoutMobile.txt",json_encode($homepageData));
        echo "<script>alert('Column widths updated.');location = 'createHomepage.php?type=".$type."';</script>";
	}
	else{
	}

?>