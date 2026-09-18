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
	#	Sticker 200x120px
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
	
	$width = 200;
	$height = 120;
  
	$png_image = imagecreatetruecolor($width, $height);
	imagealphablending( $png_image, true );
	imagesavealpha( $png_image, true );
	$bg = imagecolorallocate($png_image, hex2rgb($color_schemes[$design]['900'])[0], hex2rgb($color_schemes[$design]['900'])[1], hex2rgb($color_schemes[$design]['900'])[2]);
	$color = imagecolorallocate($png_image, hex2rgb($color_schemes[$design2]['700'])[0], hex2rgb($color_schemes[$design2]['700'])[1], hex2rgb($color_schemes[$design2]['700'])[2]);
	$color2 = imagecolorallocate($png_image, hex2rgb($color_schemes[$design2]['100'])[0], hex2rgb($color_schemes[$design2]['100'])[1], hex2rgb($color_schemes[$design2]['100'])[2]);
  
  
	roundRect($png_image, 0, 0, $width, $height, 0,  $bg);
	roundRect($png_image, 20, 20, ($width-20), ($height-20), 10,  $color);
	
	$white = imagecolorallocate($png_image, 255, 255, 255);
	$black = imagecolorallocate($png_image, 0, 0, 0);
	
	if($parameter=="T"){
		$icon1 = imagecreatefrompng('icons/temp.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $T." °".$displayTempUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $T." °".$displayTempUnits, 6, 18, $white, 0);
	}
	if($parameter=="H"){
		$icon1 = imagecreatefrompng('icons/humidity.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $H." %", 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $H." %", 6, 18, $color2, 0);
	}
	if($parameter=="P"){
		$icon1 = imagecreatefrompng('icons/pressure.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $P." ".$displayPressUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $P." ".$displayPressUnits, 6, 18, $color2, 0);
	}
	if($parameter=="W"){
		$icon1 = imagecreatefrompng('icons/wind.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $W." ".$displayWindUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $W." ".$displayWindUnits, 6, 18, $color2, 0);
	}
	if($parameter=="G"){
		$icon1 = imagecreatefrompng('icons/gust.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $G." °".$displayWindUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $G." °".$displayWindUnits, 6, 18, $color2, 0);
	}
	if($parameter=="B"){
		$icon1 = imagecreatefrompng('icons/wind.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $B." °", 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $B." °", 6, 18, $color2, 0);
	}
	if($parameter=="A"){
		$icon1 = imagecreatefrompng('icons/apparent.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $A." °".$displayTempUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $A." °".$displayTempUnits, 6, 18, $color2, 0);
	}
	if($parameter=="D"){
		$icon1 = imagecreatefrompng('icons/dewpoint.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $D." °".$displayTempUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $D." °".$displayTempUnits, 6, 18, $color2, 0);
	}
	if($parameter=="R"){
		$icon1 = imagecreatefrompng('icons/rain.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $R." ".$displayRainUnits, 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $R." ".$displayRainUnits, 6, 18, $color2, 0);
	}
	if($parameter=="S"){
		$icon1 = imagecreatefrompng('icons/sun.png');
		imagealphablending( $icon1, true );
		imagesavealpha( $icon1, true );
		imagecolorallocatealpha($icon1, 255, 255, 255, 75);
		imagecopy($png_image, $icon1, $width/2 -20, 28, 0, 0, 30, 30);
		textCenter($png_image,($width/2 + 3), 91, $S." W/m2", 6, 18, $black, 0);  
		textCenter($png_image,($width/2 + 3), 90, $S." W/m2", 6, 18, $color2, 0);
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