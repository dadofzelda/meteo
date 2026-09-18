<?php
	
	session_start();
	
	session_unset(); 
	session_destroy(); 
	if(isset($_COOKIE["meteotemplateAdmin"])) {
		setcookie("meteotemplateAdmin", "", time() - 3600,'/');
	}
	header("Location: ../indexDesktop.php");
?>