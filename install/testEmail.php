<?php 

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$emailTo = urldecode($_GET['address']);
	mail($emailTo,"Meteotemplate Test Email","Mail working :)");
	
	echo "Test email has been sent. Close this tab and wait if it arrives.";
?>