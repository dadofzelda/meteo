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

	$blockToUpdate = $_GET['id'];

    $blockNameArr = explode("_",$blockToUpdate);
    $blockName = $blockNameArr[0];
    $blockVersion = $blockNameArr[1];

    // get the zip file
    $fileURL = $meteotemplateURL."/web/downloadRequestUpdate.php?file=block&block=".$blockName."&version=".$blockVersion."&referrer=".urlencode($pageURL.$path)."&gps=".urlencode($stationLat.",".$stationLon);;

    if(!is_dir("blockTemporary")){
        mkdir("blockTemporary");
    }

    $file = loadContent($fileURL,10);

    if($file == ""){
        die("Unable to load file from meteotemplate.com.");
    }

    file_put_contents("blockTemporary/".$blockToUpdate.".zip", $file);

    
    $zip = new ZipArchive;
    if ($zip->open("blockTemporary/".$blockToUpdate.".zip") === TRUE) {
        $zip->extractTo("../homepage/blocks/");
        $zip->close();
    } 
    else {
        die('Failed to extract block ZIP file');
    }
    /*
    // list all files to update
    $filesToUpdate = getDirContents("blockTemporary/".$blockName);

    $fullPath = str_replace("admin/blockInstallProcess.php", "", realpath("blockInstallProcess.php"));
    
    
    foreach($filesToUpdate as $fileToUpdate){
        // check if in dir
        if(stripos($fileToUpdate,"/") !== false){
            $dirTree = explode("/", $fileToUpdate);
            unset($dirTree[count($dirTree) - 1]);
            $thisTree = implode("/", $dirTree);
            if(stripos($thisTree,"/") !== false){
                $dirTree2 = explode("/", $thisTree);
                unset($dirTree2[count($dirTree2) - 1]);
                $thisTree2 = implode("/", $dirTree2);
                // create dir
                if(!is_dir($fullPath."homepage/blocks/".$blockName."/".$thisTree)){
                    //echo $thisTree."<br>";
                    mkdir($fullPath."homepage/blocks/".$blockName."/".$thisTree);
                }
            }
            else{
                // create dir
                if(!is_dir($fullPath."homepage/blocks/".$blockName."/".$thisTree)){
                    //echo $thisTree."<br>";
                    mkdir($fullPath."homepage/blocks/".$blockName."/".$thisTree);
                }
            }
        }
    }

    // update files
    foreach($filesToUpdate as $fileToUpdate){
        copy("blockTemporary/".$blockName."/".$fileToUpdate, "../homepage/blocks/".$blockName."/".$fileToUpdate);
    }
    */
    // delete temporary dir
    $dir = "blockTemporary/".$blockName;
    rrmdir($dir);


    //header("Location: http://www.meteotemplate.com/web/downloadedBlock.php?update=1&block=".$blockName);

    function getDirContents($dir, &$results = array()){
        $files = scandir($dir);
        foreach($files as $key => $value){
            $path = ($dir.DIRECTORY_SEPARATOR.$value);
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

    echo "Block installed";

?>

<script>
    window.open("<?php echo $meteotemplateURL?>/web/downloadedBlock.php?update=2&block=<?php echo $blockName?>");
</script>
