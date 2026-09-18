<?php

	############################################################################
	# 	
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Sticker 800x150px
	#
	# 	Script for generating sticker.
	#
	############################################################################
	#	Version and change log
	#
	# 	v1.0 	2015-07-23	Initial release
	# 	v2.0	2016-03-06	Units clean up, rounding, addition of date/time, new
	#			sticker 800x170 with detailed info
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."scripts/functions.php");
	header('Content-type: image/png');
	
	$text = $_GET['text'];
	$design = $_GET['color1'];
	$design2 = $_GET['color2'];
	
	$a = mysqli_query($con,"
		SELECT *
		FROM alldata 
		ORDER BY DateTime 
		DESC LIMIT 1
	");

	while($row = mysqli_fetch_array($a)){
		$T = convertT($row['T']);
		$H = $row['H'];
		$W = convertW($row['W']);
		$P = convertP($row['P']);
			if($displayPressUnits=="hpa"){
				$P = number_format($P,1,".","");
			}
			else{
				$P = number_format($P,2,".","");
			}
		$G = convertW($row['G']);
		$B = $row['B'];
		$A = convertT($row['A']);
		$D = convertT($row['D']);
		$R = convertR($row['R']);
			if($displayRainUnits=="mm"){
				$R = number_format($R,1,".","");
			}
			else{
				$R = number_format($R,2,".","");
			}
		$S = $row['S'];
		if($prefferedTime=="12h"){			
			$time = date("h:i A",strtotime($row['DateTime']));
		}
		else{
			$time = date("H:i",strtotime($row['DateTime']));
		}
		if($prefferedDate=="US"){			
			$date = date("M j, Y",strtotime($row['DateTime']));
		}
		else{
			$date = date("Y-m-d",strtotime($row['DateTime']));
		}
		
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
	
	
	$width = 800;
	$height = 150;
  
	$png_image = imagecreatetruecolor($width, $height);
	imagealphablending( $png_image, true );
	imagesavealpha( $png_image, true );
	$color900 = hex2rgb($color_schemes[$design]['900']);
	$color800 = hex2rgb($color_schemes[$design]['500']);
	$color700 = hex2rgb($color_schemes[$design2]['700']);
	$color100 = hex2rgb($color_schemes[$design2]['100']);
	$colorWhite = hex2rgb("ffffff");
	$bg = imagecolorallocate($png_image, $color900[0], $color900[1], $color900[2]);
	$color = imagecolorallocate($png_image, $color700[0], $color700[1], $color700[2]);
	$color2 = imagecolorallocate($png_image, $color100[0], $color100[1], $color100[2]);
	$color3 = imagecolorallocate($png_image, $color800[0], $color800[1], $color800[2]);
  
  
	roundRect($png_image, 0, 0, $width, $height, 0,  $bg);
	roundRect($png_image, 19, 19, ($width-19), ($height-19), 10,  $color3);
	roundRect($png_image, 20, 20, ($width-20), ($height-20), 10,  $color);
  
	$icon1 = imagecreatefrompng('../../imgs/flags/big/'.$stationCountry.'.png');
	imagealphablending( $icon1, true );
	imagesavealpha( $icon1, true );
	imagecopy($png_image, $icon1, 4, 4, 0, 0, 80, 80);
	
	$icon2 = imagecreatefrompng('icons/logoSmall.png');
	imagealphablending( $icon2, true );
	imagesavealpha( $icon2, true );
	imagecopy($png_image, $icon2, 748, 92, 0, 0, 50, 50);
	
	$icon3 = imagecreatefrompng('icons/logoSmall.png');
	imagealphablending( $icon3, true );
	imagesavealpha( $icon3, true );
	imagecopy($png_image, $icon3, 748.5, 92.5, 0, 0, 50, 50);
	
	$white = imagecolorallocate($png_image, 255, 255, 255);
	$black = imagecolorallocate($png_image, 0, 0, 0);
  
	textCenter($png_image,($width/2 + 3), 52, $text, 8, 22, $black, 0);
	textCenter($png_image,($width/2), 50, $text, 8, 22, $white, 0);
	
	
	if($prefferedTime=="12h"){
		textCenter($png_image,738, 58, $time, 8, 11, $white, 0);
	}
	else{
		textCenter($png_image,752, 58, $time, 8, 11, $white, 0);
	}
	if($prefferedDate=="US"){
		textCenter($png_image,734, 40, $date, 8, 10, $white, 0);
	}
	else{
		textCenter($png_image,734, 40, $date, 8, 10, $white, 0);
	}
	
	textCenter($png_image,($width/6 + 1), 91, $T, 9, 24, $black, 0);  
	textCenter($png_image,($width/6), 90, $T, 9, 24, $white, 0);
	textCenter($png_image,($width/6 * 2 + 1), 91, $H, 9, 24, $black, 0);  
	textCenter($png_image,($width/6 * 2), 90, $H, 9, 24, $white, 0);
	textCenter($png_image,($width/6 * 3 + 1), 91, $P, 9, 24, $black, 0);  
	textCenter($png_image,($width/6 * 3), 90, $P, 9, 24, $white, 0);
	textCenter($png_image,($width/6 * 4 + 1), 91, $W, 9, 24, $black, 0);  
	textCenter($png_image,($width/6 * 4), 90, $W, 9, 24, $white, 0);    
	textCenter($png_image,($width/6 * 5 + 1), 91, $R, 9, 24, $black, 0);  
	textCenter($png_image,($width/6 * 5), 90, $R, 9, 24, $white, 0); 

	textCenter($png_image,($width/6 + 1), 121, "°".$displayTempUnits, 6, 12, $black, 0);  
	textCenter($png_image,($width/6), 120, "°".$displayTempUnits, 6, 12, $color2, 0);
	textCenter($png_image,($width/6 * 2 + 1), 121, '%', 6, 12, $black, 0);  
	textCenter($png_image,($width/6 * 2), 120, '%', 6, 12, $color2, 0);
	textCenter($png_image,($width/6 * 3 + 1), 121, $pressUnits, 6, 12, $black, 0);  
	textCenter($png_image,($width/6 * 3), 120, $pressUnits, 6, 12, $color2, 0);
	textCenter($png_image,($width/6 * 4 + 1), 121, $windUnits, 6, 12, $black, 0);  
	textCenter($png_image,($width/6 * 4), 120, $windUnits, 6, 12, $color2, 0);    
	textCenter($png_image,($width/6 * 5 + 1), 121, $displayRainUnits, 6, 12, $black, 0);  
	textCenter($png_image,($width/6 * 5), 120, $displayRainUnits, 6, 12, $color2, 0); 

	textCenter($png_image,($width/2), 145, $pageURL, 3, 8, $white, 0);
	textCenter($png_image,710, 145, "Meteotemplate", 3, 8, $white, 0);

	imagepng($png_image);

	function textCenter($img, $x, $y, $text, $size, $ttfsize, $color, $angle) {
		$gsSupport = gd_info();
		if ($gsSupport["FreeType Support"] == 0){
		   $x -= (imagefontwidth($size) * strlen($text)) / 2;
		   $y -= (imagefontheight($size)) / 2;
		   imagestring($img, $size, $x, $y - 3, $text, $color);
		}
		else {
			$box = imagettfbbox ($ttfsize, $angle, 'fonts/Roboto-Bold.ttf', $text);
			$x -= ($box[2] - $box[0]) / 2;
			$y -= ($box[3] - $box[1]) / 2;
			imagettftext ($img, $ttfsize, $angle, $x, $y, $color, 'fonts/Roboto-Bold.ttf', $text);
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