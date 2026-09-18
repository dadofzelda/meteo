<?php
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."scripts/functions.php");
	header('Content-type: image/png');

	// check if we can use API
	if(file_exists("../../meteotemplateLive.txt")){
		$apiData = json_decode(file_get_contents("../../meteotemplateLive.txt"),true);
		$T = number_format(convertor($apiData['T'],"C",$displayTempUnits),1,".","");
		$H = number_format($apiData['H'],1,".","");
		$W = number_format(convertor($apiData['W'],"kmh",$displayWindUnits),1,".","");
		$P = convertor($apiData['P'],"hpa",$displayPressUnits);
			if($displayPressUnits=="hpa"){
				$P = number_format($P,1,".","");
			}
			else{
				$P = number_format($P,2,".","");
			}
		$G = number_format(convertor($apiData['G'],"kmh",$displayWindUnits),1,".","");
		$B = $apiData['B'];
		$A = number_format(convertor($apiData['A'],"C",$displayTempUnits),1,".","");
		$D = number_format(convertor($apiData['D'],"C",$displayTempUnits),1,".","");
		$R = convertor($apiData['R'],"mm",$displayRainUnits);
		$RR = convertor($apiData['RR'],"mm",$displayRainUnits);
			if($displayRainUnits=="mm"){
				$R = number_format($R,1,".","");
				$decimalsR = 1;
			}
			else{
				$R = number_format($R,2,".","");
				$decimalsR = 2;
			}
		$S = $apiData['S'];

		$time = date($timeFormat,$apiData['U']);
		$date = date($dateFormat,$apiData['U']); 
	}
	else{ // API N/A, use db
		$a = mysqli_query($con,"
			SELECT *
			FROM alldata 
			ORDER BY DateTime 
			DESC LIMIT 1
		");

		while($row = mysqli_fetch_array($a)){
			$T = number_format(convertT($row['T']),1,".","");
			$H = number_format($row['H'],1,".","");
			$W = number_format(convertW($row['W']),1,".","");
			$P = convertP($row['P']);
				if($displayPressUnits=="hpa"){
					$P = number_format($P,1,".","");
				}
				else{
					$P = number_format($P,2,".","");
				}
			$G = number_format(convertW($row['G']),1,".","");
			$B = $row['B'];
			$A = convertT($row['A']);
			$D = convertT($row['D']);
			$R = convertR($row['R']);
			$RR = convertR($row['RR']);
				if($displayRainUnits=="mm"){
					$R = number_format($R,1,".","");
					$decimalsR = 1;
				}
				else{
					$R = number_format($R,2,".","");
					$decimalsR = 2;
				}
			$S = $row['S'];

			$time = date($timeFormat,strtotime($row['DateTime']));
			$date = date($dateFormat,strtotime($row['DateTime']));
		}
	}
	
	$a = mysqli_query($con,"
		SELECT max(Tmax),min(Tmin), max(G), min(H), max(H)
		FROM alldata 
		WHERE DATE(DateTime) = CURDATE()
		GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
	");

	while($row = mysqli_fetch_array($a)){
		$dailyMaxT = number_format(convertT($row['max(Tmax)']),1,".","");
		$dailyMinT = number_format(convertT($row['min(Tmin)']),1,".","");
		$dailyMaxG = number_format(convertW($row['max(G)']),1,".","");
		$dailyMaxH = number_format(($row['max(H)']),1,".","");
		$dailyMinH = number_format(($row['min(H)']),1,".","");
	}
	
	$monthlyRains = array();
	$result = mysqli_query($con, "
			SELECT  max(R)
			FROM  alldata
			WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		array_push($monthlyRains, convertR($row['max(R)']));		
	}
	if(empty($monthlyRains)===false){
		$monthlyAvgR = array_sum($monthlyRains)/count($monthlyRains);
		$monthlyMaxR = max($monthlyRains);
	}
	$rainMonth = number_format(array_sum($monthlyRains),$decimalsR,".","");
	
	$annualRains = array();
	$result = mysqli_query($con, "
			SELECT  max(R)
			FROM  alldata
			WHERE YEAR(DateTime) = YEAR(CURDATE())
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		array_push($annualRains, convertR($row['max(R)']));		
	}
	if(empty($annualRains)===false){
		$annualAvgR = array_sum($annualRains)/count($annualRains);
		$annualMaxR = max($annualRains);
	}
	$rainYear = number_format(array_sum($annualRains),$decimalsR,".","");
	
	$resultSQLBaro = mysqli_query($con, "
			SELECT  DateTime, P
			FROM  alldata
			WHERE DateTime <= now() - interval 3 hour
			ORDER BY DateTime DESC
			LIMIT 1
			"
	);
	while($row = mysqli_fetch_array($resultSQLBaro)){
		$pressure3h = convertP($row['P']);
	}
	
	
	
	if($displayPressUnits=="inhg"){
		$pressUnits = "inHg";
	}
	if($displayPressUnits=="hpa"){
		$pressUnits = "hPa";
	}
	if($displayWindUnits=="kmh"){
		$windUnits = "km/h";
	}
	if($displayWindUnits=="ms"){
		$windUnits = "m/s";
	}
	if($displayWindUnits=="mph"){
		$windUnits = "mph";
	}
	
	$stationTimezone = new DateTimeZone($stationTZ);
	$stationOffset  = $stationTimezone->getOffset(new DateTime)/3600;
	
	
	// get sunrise and sunset times
	$sunRiseTS = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
	$sunSetTS = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
	
	// current time
	$currentTime = time();
	
	// before sunrise
	if($currentTime<($sunRiseTS-30*60)){
		$dayTime = "night";
	}
	// after sunset
	else if($currentTime>($sunSetTS+30*60)){
		$dayTime = "night";
	}
	// hour within sunrise
	else if($currentTime>=($sunRiseTS-30*60) && $currentTime<=($sunRiseTS+30*60)){
		$dayTime = "sunrise";
	}
	// hour within sunset
	else if($currentTime>=($sunSetTS-30*60) && $currentTime<=($sunSetTS+30*60)){
		$dayTime = "sunset";
	}
	// else must be day
	else{
		$dayTime = "day";
	}
	
	// get parameters
	
	if(isset($_GET['text'])){
		$text = $_GET['text'];
	}
	else{
		$text = "Meteotemplate";
	}
	if(isset($_GET['font'])){
		$fontFace = "./fonts/".$_GET['font'].".ttf";
	}
	else{
		$fontFace = "./fonts/Ubuntu-Regular.ttf";
	}
    if(isset($_GET['realpath'])) {$fontFace = realpath($fontFace);}
	if(isset($_GET['bg'])){
		$bgURL = 'bgs/'.$_GET['bg'].'.jpg';
		$bgNumber = $_GET['bg'];
	}
	else{
		$bgURL = 'bgs/10.jpg';
		$bgNumber = 10;
	}
	if(isset($_GET['border'])){
		$border = $_GET['border'];
	}
	else{
		$border = 5;
	}
	if(isset($_GET['bgColor'])){
		$bgColor = hex2rgb($_GET['bgColor']);
	}
	else{
		$bgColor = hex2rgb('000');
	}
	if(isset($_GET['shadow'])){
		if($_GET['shadow']==0){
			$shadow = false;
		}
		else{
			$shadow = true;
		}
	}
	else{
		$shadow = false;
	}
	
	if(isset($_GET['color'])){
		$stickerColor = $_GET['color'];
	}
	else{
		$stickerColor = "white";
	}
	
	
	if($stickerColor=="white"){
		$shadowColor = "black";
	}
	else{
		$shadowColor = "white";
	}
	
	$trendP = $P - $pressure3h;
	
	if($stickerColor=="black"){
		if($trendP>0){
			$imageP = "icons/trendUpBlack.png";
		}
		if($trendP==0){
			$imageP = "icons/trendNeutralBlack.png";
		}
		if($trendP<0){
			$imageP = "icons/trendDownBlack.png";
		}
	}
	else{
		if($trendP>0){
			$imageP = "icons/trendUp.png";
		}
		if($trendP==0){
			$imageP = "icons/trendNeutral.png";
		}
		if($trendP<0){
			$imageP = "icons/trendDown.png";
		}
	}
	
	// get type
	if(isset($_GET['type'])){
		if($_GET['type']=="random"){
			$bgImagesAvailable = array_filter(glob('bgs/*.jpg'));
			foreach($bgImagesAvailable as $availableImage){
				$bgNames [] = $availableImage;
			}
			$bgNumber = rand(0,(count($bgNames)-1));
			$bgURL = $bgNames[$bgNumber];
			$shadow = 1;
		}
		if($_GET['type']=="interactive"){
			$shadow = 1;
			if($dayTime=='night'){
				$items = array(27,68,145,146,40,38);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'white';
				$shadowColor = 'black';
			}
			if($dayTime=='day'){
				$items = array(13,139,140,141,171,172);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'white';
				$shadowColor = 'black';
			}
			if($dayTime=='sunrise'){
				$items = array(17,51,16,142,151,152);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'white';
				$shadowColor = 'black';
			}
			if($dayTime=='sunset'){
				$items = array(17,51,16,137,138,151,152,177);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'white';
				$shadowColor = 'black';
			}
			if($RR>0){
				$items = array(65,143,144,150);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'white';
				$shadowColor = 'black';
			}
			if($S>800){
				$items = array(23);
				$bgNumber = $items[array_rand($items)];
				$bgURL = 'bgs/'.$bgNumber.'.jpg';
				$stickerColor = 'black';
				$shadowColor = 'white';
			}
		}
		if($_GET['type']=="christmas"){
			$shadow = 1;
			$items = array(182,183,184,185,186,187,188,189,190,191,192,193,194,195,196,197,198,199);
			$bgNumber = $items[array_rand($items)];
			$bgURL = 'bgs/'.$bgNumber.'.jpg';
			$stickerColor = 'white';
			$shadowColor = 'black';
		}
	}
	
	$widthShift = 0;
	
	if(isset($_GET['image'])){
		if($_GET['image']!="random"){
			$thumbNail = true;
			$widthShift = 175;
			$customImage = imagecreatefrompng('imgs/'.$_GET['image'].".png");
		}
		else if($_GET['image']=="random"){
			$thumbNail = true;
			$widthShift = 175;
			$customImagesAvailable = array_filter(glob('imgs/*'));
			foreach($customImagesAvailable as $availableCustomImage){
				$customNames [] = $availableCustomImage;
			}
			$customImageNumber = rand(0,(count($customNames)-1));
			$customImage = imagecreatefrompng($customImagesAvailable[$customImageNumber]);
		}
		else{
			$thumbNail = false;
		}
	}
	else{
		$thumbNail = false;
	}
	
	$width = 800;
	if($thumbNail){
		$widthImage = $width + $widthShift;
	}
	else{
		$widthImage = $width;
	}
	$height = 170;
  
	$png_image = imagecreatetruecolor($widthImage, $height);
	
	imagealphablending( $png_image, true );
	imagesavealpha( $png_image, true );
	$color900 = hex2rgb($color_schemes[$design]['900']);
	$color800 = hex2rgb($color_schemes[$design]['500']);
	$color700 = hex2rgb($color_schemes[$design2]['700']);
	$color100 = hex2rgb($color_schemes[$design2]['100']);
	$colorWhite = hex2rgb("#660000");
	
	
	$white = imagecolorallocate($png_image, 255, 255, 255);
	$black = imagecolorallocate($png_image, 0, 0, 0);
	
	$bgColorFinal = imagecolorallocate($png_image, $bgColor[0], $bgColor[1], $bgColor[2]);
	
	
	imagefill ( $png_image , 0 ,0 , $bgColorFinal );
	
	if($stickerColor=="black"){
		$mainColor = $black;
		$shadowColor = $white;
	}
	else{
		$mainColor = $white;
		$shadowColor = $black;
	}

	
	$bgImage = imagecreatefromjpeg($bgURL);
	imagecopy($png_image, $bgImage, $widthShift+$border, $border, 0, 0, (800-$border*2), (170-$border*2));
  
  
	//roundRect($png_image, 0, 0, $width, $height, 0,  $bg);
	//roundRect($png_image, 19, 19, ($width-19), ($height-19), 10,  $color3);
	//roundRect($png_image, 20, 20, ($width-20), ($height-20), 10,  $color);
  
	$icon1 = imagecreatefrompng('../../imgs/flags/big/'.$stationCountry.'.png');
	imagealphablending( $icon1, true );
	imagesavealpha( $icon1, true );
	imagecopy($png_image, $icon1, 2+$widthShift, 2, 0, 0, 80, 80);
	
	if($thumbNail){
		imagealphablending($customImage, true );
		imagesavealpha( $customImage, true );
		imagecopy($png_image, $customImage, $border, $border, 0, 0, 170, 170-$border*2);
	}
	
	if(!$shadow){
		if($stickerColor=="black"){
			$icon2 = imagecreatefrompng('icons/logoSmallBlack.png');
		}
		else{
			$icon2 = imagecreatefrompng('icons/logoSmall.png');
		}
		imagealphablending( $icon2, true );
		imagesavealpha( $icon2, true );
		imagecopy($png_image, $icon2, 748+$widthShift, 112, 0, 0, 50, 50);
	}
	else{
		if($stickerColor=="black"){
			$icon2 = imagecreatefrompng('icons/logoSmallBlack.png');
			$icon2shadow = imagecreatefrompng('icons/logoSmall.png');
		}
		else{
			$icon2 = imagecreatefrompng('icons/logoSmall.png');
			$icon2shadow = imagecreatefrompng('icons/logoSmallBlack.png');
		}
		imagealphablending( $icon2shadow, true );
		imagesavealpha( $icon2shadow, true );
		imagecopy($png_image, $icon2shadow, 749+$widthShift, 113, 0, 0, 50, 50);
		imagealphablending( $icon2, true );
		imagesavealpha( $icon2, true );
		imagecopy($png_image, $icon2, 748+$widthShift, 112, 0, 0, 50, 50);
	}
	
	// use both units?
	if($_GET['dualUnits']==1){
		$dualUnits = true;
	}
	else{
		$dualUnits = false;
	}

	if(!$dualUnits){
			$pressTrendTopHeight1 = 96;
		}
		else{
			$pressTrendTopHeight1 = 108;
		}
	
	$pressureIcon = imagecreatefrompng($imageP);
	imagealphablending( $pressureIcon, true );
	imagesavealpha( $pressureIcon, true );
	imagecopy($png_image, $pressureIcon, ($width/6 * 3 - 15)+$widthShift, $pressTrendTopHeight1, 0, 0, 30, 30);
	
	if(!$shadow){
		if($stickerColor=="black"){
			$stationIcon = imagecreatefrompng('icons/stationBlack.png');
		}
		else{
			$stationIcon = imagecreatefrompng('icons/station.png');
		}
		imagealphablending( $stationIcon, true );
		imagesavealpha( $stationIcon, true );
		imagecopy($png_image, $stationIcon, 17+$widthShift, 100, 0, 0, 50, 50);
	}
	else{
		if($stickerColor=="black"){
			$stationIcon = imagecreatefrompng('icons/stationBlack.png');
			$stationIconShadow = imagecreatefrompng('icons/station.png');
		}
		else{
			$stationIcon = imagecreatefrompng('icons/station.png');
			$stationIconShadow = imagecreatefrompng('icons/stationBlack.png');
		}
		imagealphablending( $stationIconShadow, true );
		imagesavealpha( $stationIconShadow, true );
		imagecopy($png_image, $stationIconShadow, 18+$widthShift, 101, 0, 0, 50, 50);
		imagealphablending( $stationIcon, true );
		imagesavealpha( $stationIcon, true );
		imagecopy($png_image, $stationIcon, 17+$widthShift, 100, 0, 0, 50, 50);
	}

	
	if(!$shadow){
		textLeft($png_image,90+$widthShift, 36, $text, 7, 17, $mainColor, 0);
		
		if($prefferedTime=="12h"){
			textCenter($png_image,738+$widthShift, 40, $time, 8, 11, $mainColor, 0);
		}
		else{
			textCenter($png_image,752+$widthShift, 40, $time, 8, 11, $mainColor, 0);
		}
		if($prefferedDate=="US"){
			textCenter($png_image,734+$widthShift, 22, $date, 8, 10, $mainColor, 0);
		}
		else{
			textCenter($png_image,734+$widthShift, 22, $date, 8, 10, $mainColor, 0);
		}
		textCenter($png_image,($width/6 + 1)+$widthShift, 75, $T, 7, 22, $mainColor, 0);  
		textCenter($png_image,($width/6 * 2 + 1)+$widthShift, 75, $H, 7, 22, $mainColor, 0);  
		textCenter($png_image,($width/6 * 3 + 1)+$widthShift, 75, $P, 7, 22, $mainColor, 0);  
		textCenter($png_image,($width/6 * 4 + 1)+$widthShift, 75, $W, 7, 22, $mainColor, 0);  
		textCenter($png_image,($width/6 * 5 + 1)+$widthShift, 75, $R, 7, 22, $mainColor, 0);  

		if($dualUnits){
			if($displayTempUnits=="C"){
				$alternativeT = number_format(convertor($T,"C","F"),1,".","")."°F";
			}
			else{
				$alternativeT = number_format(convertor($T,"F","C"),1,".","")."°C";
			}
			if($displayPressUnits=="hpa"){
				$alternativeP = number_format(convertor($P,"hpa","inhg"),2,".","")." inHg";
			}
			if($displayPressUnits=="mmhg"){
				$alternativeP = number_format(convertor($P,"mmhg","inhg"),2,".","")." inHg";
			}
			if($displayPressUnits=="inhg"){
				$alternativeP = number_format(convertor($P,"inhg","hpa"),1,".","")." hPa";
			}
			if($displayRainUnits=="mm"){
				$alternativeR = number_format(convertor($R,"mm","in"),2,".","")." in";
			}
			if($displayRainUnits=="in"){
				$alternativeR = number_format(convertor($R,"in","mm"),1,".","")." mm";
			}
			if($displayWindUnits=="kmh"){
				$alternativeW = number_format(convertor($W,"kmh","mph"),1,".","")." mph";
			}
			if($displayWindUnits=="ms"){
				$alternativeW = number_format(convertor($W,"ms","mph"),1,".","")." mph";
			}
			if($displayWindUnits=="kt"){
				$alternativeW = number_format(convertor($W,"kt","kmh"),1,".","")." km/h";
			}
			if($displayWindUnits=="mph"){
				$alternativeW = number_format(convertor($W,"mph","kmh"),1,".","")." km/h";
			} 
			textCenter($png_image,($width/6)+$widthShift, 91, $alternativeT, 3.5, 11, $mainColor, 0);
			textCenter($png_image,($width/6 * 3)+$widthShift, 91, $alternativeP, 3.5, 11, $mainColor, 0);
			textCenter($png_image,($width/6 * 4)+$widthShift, 91, $alternativeW, 3.5, 11, $mainColor, 0);    
			textCenter($png_image,($width/6 * 5)+$widthShift, 91, $alternativeR, 3.5, 11, $mainColor, 0); 
		}
	
		if(!$dualUnits){
			$recordsTopHeight1 = 96;
			$recordsTopHeight2 = 116;
			$recordsTopHeight3 = 140; 
			$unitsFontSize = 6;
		}
		else{
			$recordsTopHeight1 = 108;
			$recordsTopHeight2 = 130; 
			$recordsTopHeight3 = 147;
			$unitsFontSize = 5;
		}

		textCenter($png_image,($width/6)+$widthShift, $recordsTopHeight1, ("TODAY"), 3, 8, $mainColor, 0); 
		textCenter($png_image,($width/6 * 2)+$widthShift, $recordsTopHeight1, ("TODAY"), 3, 8, $mainColor, 0);
		textCenter($png_image,($width/6 * 3)+$widthShift, $recordsTopHeight1, ("3 H"), 3, 8, $mainColor, 0); 
		textCenter($png_image,($width/6 * 4)+$widthShift, $recordsTopHeight1, ("MAX TODAY"), 3, 8, $mainColor, 0); 
		textCenter($png_image,($width/6 * 5)+$widthShift, $recordsTopHeight1, ("M / Y"), 3, 8, $mainColor, 0); 
	
		textCenter($png_image,($width/6)+$widthShift, $recordsTopHeight2, ($dailyMaxT." / ".$dailyMinT), 5, 12, $mainColor, 0);
		textCenter($png_image,($width/6 * 2)+$widthShift, $recordsTopHeight2, ($dailyMaxH." / ".$dailyMinH), 5, 12, $mainColor, 0);
		textCenter($png_image,($width/6 * 4)+$widthShift, $recordsTopHeight2, ($dailyMaxG), 5, 12, $mainColor, 0);
		textCenter($png_image,($width/6 * 5)+$widthShift, $recordsTopHeight2, ($rainMonth." / ".$rainYear), 5, 12, $mainColor, 0);
	 

		textCenter($png_image,($width/6)+$widthShift, $recordsTopHeight3, "°".$displayTempUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 2)+$widthShift, $recordsTopHeight3, '%', $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);  
		textCenter($png_image,($width/6 * 3)+$widthShift, $recordsTopHeight3, $pressUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 4)+$widthShift, $recordsTopHeight3, $windUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0); 
		textCenter($png_image,($width/6 * 5)+$widthShift, $recordsTopHeight3, $displayRainUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0); 

		textCenter($png_image,($width/2)+$widthShift, 160, $pageURL.$path, 3, 8, $mainColor, 0);
		textCenter($png_image,710+$widthShift, 160, "Meteotemplate", 3, 8, $mainColor, 0);
	}
	else{
		textLeft($png_image,92+$widthShift, 39, $text, 7, 17, $shadowColor, 0);
		textLeft($png_image,90+$widthShift, 37, $text, 7, 17, $mainColor, 0);
		
		if($prefferedTime=="12h"){
			textCenter($png_image,740+$widthShift, 42, $time, 8, 11, $shadowColor, 0);
			textCenter($png_image,738+$widthShift, 40, $time, 8, 11, $mainColor, 0);
		}
		else{
			textCenter($png_image,754+$widthShift, 42, $time, 8, 11, $shadowColor, 0);
			textCenter($png_image,752+$widthShift, 40, $time, 8, 11, $mainColor, 0);
		}
		if($prefferedDate=="US"){
			textCenter($png_image,736+$widthShift, 25, $date, 8, 10, $shadowColor, 0);
			textCenter($png_image,734+$widthShift, 23, $date, 8, 10, $mainColor, 0);
		}
		else{
			textCenter($png_image,736+$widthShift, 25, $date, 8, 10, $shadowColor, 0);
			textCenter($png_image,734+$widthShift, 23, $date, 8, 10, $mainColor, 0);
		}
		textCenter($png_image,($width/6 + 2)+$widthShift, 75, $T, 7, 22, $shadowColor, 0);  
		textCenter($png_image,($width/6)+$widthShift, 74, $T, 7, 22, $mainColor, 0);
		textCenter($png_image,($width/6 * 2 + 2)+$widthShift, 75, $H, 7, 22, $shadowColor, 0);  
		textCenter($png_image,($width/6 * 2)+$widthShift, 74, $H, 7, 22, $mainColor, 0);
		textCenter($png_image,($width/6 * 3 + 2)+$widthShift, 75, $P, 7, 22, $shadowColor, 0);  
		textCenter($png_image,($width/6 * 3)+$widthShift, 74, $P, 7, 22, $mainColor, 0);
		textCenter($png_image,($width/6 * 4 + 2)+$widthShift, 75, $W, 7, 22, $shadowColor, 0);  
		textCenter($png_image,($width/6 * 4)+$widthShift, 74, $W, 7, 22, $mainColor, 0);    
		textCenter($png_image,($width/6 * 5 + 2)+$widthShift, 75, $R, 7, 22, $shadowColor, 0);  
		textCenter($png_image,($width/6 * 5)+$widthShift, 74, $R, 7, 22, $mainColor, 0); 

		if($dualUnits){
			if($displayTempUnits=="C"){
				$alternativeT = number_format(convertor($T,"C","F"),1,".","")."°F";
			}
			else{
				$alternativeT = number_format(convertor($T,"F","C"),1,".","")."°C";
			}
			if($displayPressUnits=="hpa"){
				$alternativeP = number_format(convertor($P,"hpa","inhg"),2,".","")." inHg";
			}
			if($displayPressUnits=="mmhg"){
				$alternativeP = number_format(convertor($P,"mmhg","inhg"),2,".","")." inHg";
			}
			if($displayPressUnits=="inhg"){
				$alternativeP = number_format(convertor($P,"inhg","hpa"),1,".","")." hPa";
			}
			if($displayRainUnits=="mm"){
				$alternativeR = number_format(convertor($R,"mm","in"),2,".","")." in";
			}
			if($displayRainUnits=="in"){
				$alternativeR = number_format(convertor($R,"in","mm"),1,".","")." mm";
			}
			if($displayWindUnits=="kmh"){
				$alternativeW = number_format(convertor($W,"kmh","mph"),1,".","")." mph";
			}
			if($displayWindUnits=="ms"){
				$alternativeW = number_format(convertor($W,"ms","mph"),1,".","")." mph";
			}
			if($displayWindUnits=="kt"){
				$alternativeW = number_format(convertor($W,"kt","kmh"),1,".","")." km/h";
			}
			if($displayWindUnits=="mph"){
				$alternativeW = number_format(convertor($W,"mph","kmh"),1,".","")." km/h";
			}
			textCenter($png_image,($width/6 + 2)+$widthShift, 92, $alternativeT, 3.5, 11, $shadowColor, 0);  
			textCenter($png_image,($width/6)+$widthShift, 91, $alternativeT, 3.5, 11, $mainColor, 0);
			textCenter($png_image,($width/6 * 3 + 2)+$widthShift, 92, $alternativeP, 3.5, 11, $shadowColor, 0);  
			textCenter($png_image,($width/6 * 3)+$widthShift, 91, $alternativeP, 3.5, 11, $mainColor, 0);
			textCenter($png_image,($width/6 * 4 + 2)+$widthShift, 92, $alternativeW, 3.5, 11, $shadowColor, 0);  
			textCenter($png_image,($width/6 * 4)+$widthShift, 91, $alternativeW, 3.5, 11, $mainColor, 0);    
			textCenter($png_image,($width/6 * 5 + 2)+$widthShift, 92, $alternativeR, 7, 11, $shadowColor, 0);  
			textCenter($png_image,($width/6 * 5)+$widthShift, 91, $alternativeR, 3.5, 11, $mainColor, 0); 
		}


		if(!$dualUnits){
			$recordsTopHeight1 = 96;
			$recordsTopHeight2 = 116;
			$recordsTopHeight3 = 140; 
			$unitsFontSize = 6;
		}
		else{
			$recordsTopHeight1 = 108;
			$recordsTopHeight2 = 130; 
			$recordsTopHeight3 = 147;
			$unitsFontSize = 5;
		}
		
		textCenter($png_image,($width/6 + 1)+$widthShift, ($recordsTopHeight1+1), ("TODAY"), 3, 8, $shadowColor, 0);
		textCenter($png_image,($width/6)+$widthShift, $recordsTopHeight1, ("TODAY"), 3, 8, $mainColor, 0); 
		textCenter($png_image,($width/6 * 2 + 1)+$widthShift, ($recordsTopHeight1+1), ("TODAY"), 3, 8, $shadowColor, 0);
		textCenter($png_image,($width/6 * 2)+$widthShift, $recordsTopHeight1, ("TODAY"), 3, 8, $mainColor, 0);
		textCenter($png_image,($width/6 * 3 + 1)+$widthShift, ($recordsTopHeight1+1), ("3 H"), 3, 8, $shadowColor, 0);
		textCenter($png_image,($width/6 * 3)+$widthShift, $recordsTopHeight1, ("3 H"), 3, 8, $mainColor, 0); 		
		textCenter($png_image,($width/6 * 4 + 1)+$widthShift, ($recordsTopHeight1+1), ("MAX TODAY"), 3, 8, $shadowColor, 0);
		textCenter($png_image,($width/6 * 4)+$widthShift, $recordsTopHeight1, ("MAX TODAY"), 3, 8, $mainColor, 0);
		textCenter($png_image,($width/6 * 5 + 1)+$widthShift, ($recordsTopHeight1+1), ("M / Y"), 3, 8, $shadowColor, 0); 
		textCenter($png_image,($width/6 * 5)+$widthShift, $recordsTopHeight1, ("M / Y"), 3, 8, $mainColor, 0);
		
		textCenter($png_image,($width/6)+$widthShift, ($recordsTopHeight2+2), ($dailyMaxT." / ".$dailyMinT), 5, 12, $shadowColor, 0);
		textCenter($png_image,($width/6 + 2)+$widthShift, $recordsTopHeight2, ($dailyMaxT." / ".$dailyMinT), 5, 12, $mainColor, 0); 
		textCenter($png_image,($width/6 * 2)+$widthShift, ($recordsTopHeight2+2), ($dailyMaxH." / ".$dailyMinH), 5, 12, $shadowColor, 0);
		textCenter($png_image,($width/6 * 2 + 2)+$widthShift, $recordsTopHeight2, ($dailyMaxH." / ".$dailyMinH), 5, 12, $mainColor, 0); 
		textCenter($png_image,($width/6 * 4)+$widthShift, ($recordsTopHeight2+2), ($dailyMaxG), 5, 12, $shadowColor, 0);
		textCenter($png_image,($width/6 * 4 + 2)+$widthShift, $recordsTopHeight2, ($dailyMaxG), 5, 12, $mainColor, 0);
		textCenter($png_image,($width/6 * 5)+$widthShift, ($recordsTopHeight2+2), ($rainMonth." / ".$rainYear), 5, 12, $shadowColor, 0);
		textCenter($png_image,($width/6 * 5 + 2)+$widthShift, $recordsTopHeight2, ($rainMonth." / ".$rainYear), 5, 12, $mainColor, 0); 
		
		textCenter($png_image,($width/6 + 1)+$widthShift, ($recordsTopHeight3+2), "°".$displayTempUnits, $unitsFontSize, ($unitsFontSize*2), $shadowColor, 0);  
		textCenter($png_image,($width/6)+$widthShift, $recordsTopHeight3, "°".$displayTempUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 2 + 2)+$widthShift, ($recordsTopHeight3+2), '%', $unitsFontSize, ($unitsFontSize*2), $shadowColor, 0);  
		textCenter($png_image,($width/6 * 2)+$widthShift, $recordsTopHeight3, '%', $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 3 + 2)+$widthShift, ($recordsTopHeight3+2), $pressUnits, $unitsFontSize, ($unitsFontSize*2), $shadowColor, 0);  
		textCenter($png_image,($width/6 * 3)+$widthShift, $recordsTopHeight3, $pressUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 4 + 2)+$widthShift, ($recordsTopHeight3+2), $windUnits, $unitsFontSize, ($unitsFontSize*2), $shadowColor, 0);  
		textCenter($png_image,($width/6 * 4)+$widthShift, $recordsTopHeight3, $windUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0);
		textCenter($png_image,($width/6 * 5 + 2)+$widthShift, ($recordsTopHeight3+2), $displayRainUnits, $unitsFontSize, ($unitsFontSize*2), $shadowColor, 0);  
		textCenter($png_image,($width/6 * 5)+$widthShift, $recordsTopHeight3, $displayRainUnits, $unitsFontSize, ($unitsFontSize*2), $mainColor, 0); 

		textCenter($png_image,($width/2 + 1)+$widthShift, 166, $pageURL.$path, 3, 8, $shadowColor, 0);
		textCenter($png_image,($width/2)+$widthShift, 165, $pageURL.$path, 3, 8, $mainColor, 0);
		textCenter($png_image,711+$widthShift, 166, "Meteotemplate", 3, 8, $shadowColor, 0);
		textCenter($png_image,710+$widthShift, 165, "Meteotemplate", 3, 8, $mainColor, 0);
	}

	imagepng($png_image);

	function textCenter($img, $x, $y, $text, $size, $ttfsize, $color, $angle) {
		global $fontFace;
		$gsSupport = gd_info();
		if ($gsSupport["FreeType Support"] == 0){
		   $x -= (imagefontwidth($size) * strlen($text)) / 2;
		   $y -= (imagefontheight($size)) / 2;
		   imagestring($img, $size, $x, $y - 3, $text, $color);
		}
		else {
			$box = imagettfbbox ($ttfsize, $angle, $fontFace, $text);
			$x = intval($x - ($box[2] - $box[0]) / 2);
			$y = intval($y - ($box[3] - $box[1]) / 2);
			imagettftext ($img, $ttfsize, $angle, $x, $y, $color, $fontFace, $text);
		}

	}
	function textLeft($img, $x, $y, $text, $size, $ttfsize, $color, $angle) {
		global $fontFace;
		$gsSupport = gd_info();
		if ($gsSupport["FreeType Support"] == 0){
		   $x -= (imagefontwidth($size) * strlen($text));
		   $y -= (imagefontheight($size)) / 2;
		   imagestring($img, $size, $x, $y - 3, $text, $color);
		}
		else {
			$box = imagettfbbox ($ttfsize, $angle, $fontFace, $text);
			$x = intval($x - ($box[4] - $box[2])) ;
			$y = intval($y - ($box[3] - $box[1]) / 2);
			imagettftext ($img, $ttfsize, $angle, $x, $y, $color, $fontFace, $text);
		}

	}
	function hex2rgb($hex){
		$hex = str_replace("#", "", $hex);
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} 
		else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb;
	}
	function roundRect($im,$x,$y,$cx,$cy,$rad,$col){
		imagefilledrectangle($im,$x,$y+$rad,$cx,$cy-$rad,$col);
		imagefilledrectangle($im,$x+$rad,$y,$cx-$rad,$cy,$col);
		$dia = $rad*2;
		imagefilledellipse($im, $x+$rad, $y+$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $x+$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $cy-$rad, $rad*2, $dia, $col);
		imagefilledellipse($im, $cx-$rad, $y+$rad, $rad*2, $dia, $col);
	}
?>