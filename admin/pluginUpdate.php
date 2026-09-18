<?php

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	require_once("../config.php");
    require("../scripts/functions.php");

	$pluginToUpdate = $_GET['id'];

    $pluginNameArr = explode("_",$pluginToUpdate);
    $pluginName = $pluginNameArr[0];
    $pluginVersion = $pluginNameArr[1];

    // get the zip file
    $fileURL = $meteotemplateURL."/web/downloadRequestUpdate.php?file=".$pluginToUpdate."&referrer=".urlencode($pageURL.$path)."&gps=".urlencode($stationLat.",".$stationLon);

    if(!is_dir("pluginTemporary")){
        mkdir("pluginTemporary");
    }

    $file = loadContent($fileURL,10);

    if($file == ""){
        die("Unable to load file from meteotemplate.com.");
    }

    file_put_contents("pluginTemporary/".$pluginToUpdate.".zip", $file);
    
    $zip = new ZipArchive;
    if ($zip->open("pluginTemporary/".$pluginToUpdate.".zip") === TRUE) {
        $zip->extractTo("pluginTemporary");
        $zip->close();
    } 
    else {
        die('Failed to extract plugin ZIP file');
    }

    // list all files to update
    $filesToUpdate = getDirContents("pluginTemporary/".$pluginName);

    $fullPath = str_replace("admin/pluginUpdate.php", "", realpath("pluginUpdate.php"));
    
    // create dirs
    foreach($filesToUpdate as $fileToUpdate){
        // check if in dir
        if(stripos($fileToUpdate,"/") !== false){
            $dirTree = explode("/", $fileToUpdate);
            unset($dirTree[count($dirTree) - 1]);
            $thisTree = implode("/", $dirTree);
            // create dir
            if(!is_dir($fullPath."plugins/".$pluginName."/".$thisTree)){
                echo $thisTree."<br>";
                mkdir($fullPath."plugins/".$pluginName."/".$thisTree);
            }
        }
    }

    // update files
    foreach($filesToUpdate as $fileToUpdate){
        copy("pluginTemporary/".$pluginName."/".$fileToUpdate, "../plugins/".$pluginName."/".$fileToUpdate);
    }

    // delete temporary dir
    $dir = "pluginTemporary/".$pluginName;
    rrmdir($dir);

    function getDirContents($dir, &$results = array()){
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = ($dir."/".$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }
        foreach($results as $result){
            if(stripos($result, ".") !== false){
                $finalResults[] = str_replace($dir."/","",$result);
            }
        }
        return $finalResults;
    }

    function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") 
                    rrmdir($dir."/".$object); 
                    else unlink   ($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    echo "Plugin updated.";

?>

<script>
    window.open("<?php echo $meteotemplateURL?>/web/downloadedPlugin.php?update=1&plugin=<?php echo $pluginName?>");
</script>