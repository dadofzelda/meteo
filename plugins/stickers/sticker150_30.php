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
	#	Sticker 150x30px
	#
	# 	Script for generating sticker.
	#
	############################################################################
	#	Version and change log
	#
	# 	v1.0 	2015-07-23	Initial release
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."scripts/functions.php");
	header('Content-type: image/png');
	
	$parameter = strtoupper($_GET['parameter']);
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
		$G = convertW($row['G']);
		$B = $row['B'];
		$A = convertT($row['A']);
		$D = convertT($row['D']);
		$R = convertR($row['R']);
		$S = $row['S'];
	}
	
	$width = 150;
	$height = 30;
  
	$png_image = imagecreatetruecolor($width, $height);
	imagealphablending( $png_image, true );
	imagesavealpha( $png_image, true );
	$bg = imagecolorallocate($png_image, hex2rgb($color_schemes[$design]['900'])[0], hex2rgb($color_schemes[$design]['900'])[1], hex2rgb($color_schemes[$design]['900'])[2]);
	$color = imagecolorallocate($png_image, hex2rgb($color_schemes[$design2]['700'])[0], hex2rgb($color_schemes[$design2]['700'])[1], hex2rgb($color_schemes[$design2]['700'])[2]);
	$color2 = imagecolorallocate($png_image, hex2rgb($color_schemes[$design2]['100'])[0], hex2rgb($color_schemes[$design2]['100'])[1], hex2rgb($color_schemes[$design2]['100'])[2]);
  
  
	roundRect($png_image, 0, 0, $width, $height, 0,  $bg);
	
	$white = imagecolorallocate($png_image, 255, 255, 255);
	$black = imagecolorallocate($png_image, 0, 0, 0);
	
	if($parameter=="T"){
		textCenter($png_image,($width/2 + 3), 23, $T." °".$displayTempUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $T." °".$displayTempUnits, 4, 12, $color2, 0);
	}
	if($parameter=="H"){
		textCenter($png_image,($width/2 + 3), 23, $H." %", 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $H." %", 4, 12, $color2, 0);
	}
	if($parameter=="P"){
		textCenter($png_image,($width/2 + 3), 23, $P." ".$displayPressUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $P." ".$displayPressUnits, 4, 12, $color2, 0);
	}
	if($parameter=="W"){
		textCenter($png_image,($width/2 + 3), 23, $W." ".$displayWindUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $W." ".$displayWindUnits, 4, 12, $color2, 0);
	}
	if($parameter=="G"){
		textCenter($png_image,($width/2 + 3), 23, $G." °".$displayWindUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $G." °".$displayWindUnits, 4, 12, $color2, 0);
	}
	if($parameter=="B"){
		textCenter($png_image,($width/2 + 3), 23, $B." °", 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $B." °", 4, 12, $color2, 0);
	}
	if($parameter=="A"){
		textCenter($png_image,($width/2 + 3), 23, $A." °".$displayTempUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $A." °".$displayTempUnits, 4, 12, $color2, 0);
	}
	if($parameter=="D"){
		textCenter($png_image,($width/2 + 3), 23, $D." °".$displayTempUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $D." °".$displayTempUnits, 4, 12, $color2, 0);
	}
	if($parameter=="R"){
		textCenter($png_image,($width/2 + 3), 23, $R." °".$displayTempUnits, 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $R." °".$displayTempUnits, 4, 12, $color2, 0);
	}
	if($parameter=="S"){
		textCenter($png_image,($width/2 + 3), 23, $S." W/m2", 4, 12, $black, 0);  
		textCenter($png_image,($width/2 + 3), 20, $S." W/m2", 4, 12, $color2, 0);
	}

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