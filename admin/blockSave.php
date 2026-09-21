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
	if(!preg_match('/^[A-Za-z0-9_]+$/', $blockNameSpace)){
		echo "<script>alert('Invalid block name.');document.location = 'blockSetup.php';</script>";
		die();
	}
	$parameters = explode(',',$_POST['parameters']);

	$string = "<?php".PHP_EOL;

	$string .= "// ".$blockNameSpace." settings file" .PHP_EOL;
	$string .= "// Version: ".number_format($_POST['version'],1,".","") .PHP_EOL;
	$string .= "// Created: ".date("Y-m-d H:i:s",time());

	$string .= PHP_EOL;
	$string .= PHP_EOL;

	foreach($parameters as $parameter){
		// only allow valid PHP variable-name characters - this token gets
		// written into generated code unquoted, right after a bare "$"
		if(!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $parameter)){
			continue;
		}
		$value = $_POST[$parameter];
		if(trim($value)=="true" || trim($value)=="false"){
			$string .= "$".$parameter." = ".trim($value).";".PHP_EOL;
		}
		else{
			// var_export() safely escapes the value for use as a PHP string
			// literal - the previous manual '...' concatenation let a value
			// containing a single quote break out and inject arbitrary code
			$string .= "$".$parameter." = ".var_export($value, true).";".PHP_EOL;
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
