<?php

	$sun = date_sun_info(time(),$stationLat, $stationLon);
	$themeSunrise = $sun['sunrise'];
	$themeSunset = $sun['sunset'];
	if($theme=="sun"){
		if((time()>$themeSunrise)&&(time()<$themeSunset)){
			$theme = "light";
		}
		else{
			$theme = "dark";
		}
	}
	if($theme=="variable"){
		if((date("H")>=$morningSwitch)&&(date("H")<$eveningSwitch)){
			$theme = "light";
		}
		else{
			$theme = "dark";
		}
	}
	$designTheme['theme'] = $theme;
	$designTheme['blockColor'] = $blockColor;
	$designTheme = json_encode($designTheme);
	$themeFile = file_put_contents("homepage/css/theme.txt",$designTheme);

	if(file_exists('cache/version.txt')){}
	else{
		echo "<img src='http://www.meteotemplate.com/web/latestVersion.php?lat=".$stationLat."&lon=".$stationLon."' style='height:0px' alt=''>";
		if($stationLat!=0 && $stationLon!=0){
			file_put_contents("cache/version.txt","11.0");
		}
	}

?>
