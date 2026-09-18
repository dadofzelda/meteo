<?php

	
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	require_once("../config.php");

	$blockNameSpace = $_POST['id'];
	$parameters = explode(',',$_POST['parameters']);

	$string = "<?php".PHP_EOL;

	$string .= "// ".$blockNameSpace." settings file" .PHP_EOL;
	$string .= "// Version: ".number_format($_POST['version'],1,".","") .PHP_EOL;
	$string .= "// Created: ".date("Y-m-d H:i:s",time());

	$string .= PHP_EOL;
	$string .= PHP_EOL;

	foreach($parameters as $parameter){
		if(trim($_POST[$parameter])=="true" || trim($_POST[$parameter])=="false"){
			$string .= "$".$parameter." = ".$_POST[$parameter].";".PHP_EOL;
		}
		else{
			$string .= "$".$parameter." = '".$_POST[$parameter]."';".PHP_EOL;
		}
	}

	$string .= PHP_EOL;
	$string .= PHP_EOL;

	file_put_contents('../homepage/blocks/'.$blockNameSpace.'/settings.php',$string);

	if(file_exists('../homepage/blocks/'.$blockNameSpace.'/settings.php')){
		echo "<script>alert('Settings saved.');document.location = 'blockSetup.php';</script>";
	}
	else{
		echo "<script>alert('Settings could not be saved! Please check that the block folder has correct permissions to write the settings file.');document.location = 'blockSetup.php';</script>";
	}
	
?>
