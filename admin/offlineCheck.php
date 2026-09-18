<?php 
    session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    // check folder permissions - write and delete file 

    file_put_contents("../cache/stationOffline.txt", time());

    if(!file_exists("../cache/stationOffline.txt")){
        die("Folder not writable. Make sure the template cache folder is writable.");
    }

    unlink("../cache/stationOffline.txt");

    if(file_exists("../cache/stationOffline.txt")){
        die("Unable to delete files from the Cache folder. Check permissions.");
    }

    echo "Everything OK :)";
