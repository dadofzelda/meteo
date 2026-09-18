<?php

include ("phpmyeasyweather.inc.php");
$myfile = fopen("stationsFilter.txt", "r") or die("Unable to open file!");
	$myfile2 = fopen("stationsCorrect.txt", "w") or die("Unable to open file!");
		while(!feof($myfile)) {
			$string = fgets($myfile);
			$code = substr($string,20,4);
			if ($input = getMetarFromWWW ($code)) {
			   $txt = $string;  
			   fwrite($myfile2, $txt);
			} else {
			}
		}
		fclose($myfile);
		fclose($myfile2);

?>