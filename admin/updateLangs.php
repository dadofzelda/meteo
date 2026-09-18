<?php

    session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    require("../config.php");
    require("../scripts/functions.php");
    
    // get available langs
    $langData = loadContent($meteotemplateURL."/web/translations/updateLang.php",20);
    $langData = json_decode($langData, true);

    if(count($langData) < 2){
        die("Unable to load data from meteotemplate.com");
    }
    echo "<style>body{color:white!important}</style>";
    foreach($langData as $lang=>$data){
        echo "Updating language: " . $lang . "<br>";
        echo "Strings available: " . count($langData[$lang]) . "<br>";
        echo "Updating file...<br>";
        file_put_contents("../lang/".strtolower($lang).".php", json_encode($data));
        if(file_exists("../lang/".strtolower($lang).".php")){
            echo "Updated.<br><br>";
        }
    }
    