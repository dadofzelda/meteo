<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	foreach($_POST as $parameter=>$value){
        if($parameter!=""){
            $importData[$parameter] = $value;
        }
    }
    file_put_contents("importExtraSettings.txt",json_encode($importData));
    echo "<script>alert('Settings saved.');window.close();</script>";

?>