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
	#	Main functions
	#
	# 	A script which contains the main functions used by the template.
	#
	############################################################################
	#
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	if (function_exists('mb_internal_encoding')) {
		mb_internal_encoding('UTF-8');
		mb_http_output('UTF-8');
		mb_http_input();
		mb_language('uni');
		mb_regex_encoding('UTF-8');
	}
	function encodeUTF8(string $s): string{
		$s .= $s;
		$len = \strlen($s);

		for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
			switch (true) {
				case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
				case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
				default: $s[$j] = "\xC3"; $s[++$j] = \chr(\ord($s[$i]) - 64); break;
			}
		}

		return substr($s, 0, $j);
	}
	function decodeUTF8(string $string): string {
		$s = (string) $string;
		$len = \strlen($s);
	
		for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
			switch ($s[$i] & "\xF0") {
				case "\xC0":
				case "\xD0":
					$c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
					$s[$j] = $c < 256 ? \chr($c) : '?';
					break;
	
				case "\xF0":
					++$i;
					// no break
	
				case "\xE0":
					$s[$j] = '?';
					$i += 2;
					break;
	
				default:
					$s[$j] = $s[$i];
			}
		}
	
		return substr($s, 0, $j);
	}
	function lang($string,$case="-"){
		global $language;
		global $lang;
		if(is_array($language)){
			// check if the word exists in the main Meteotemplate translation files and there is translation for it
			if (array_key_exists($string, $language[$lang])&&($language[$lang][$string]!="")) {
				$str = encodeUTF8($language[$lang][$string]);
				if (function_exists('mb_internal_encoding')) {
					if($case=="l"){
						$str = encodeUTF8(mb_strtolower(decodeUTF8($str)));
					}
					if($case=="u"){
						$str = encodeUTF8(mb_strtoupper(decodeUTF8($str)));
					}
					if($case=="c"){
						$str = ucfirst($str);
					}
					if($case=="w"){
						$str = ucwords($str);
					}
				}
				else{
					if($case=="l"){
						$str = encodeUTF8(strtolower(decodeUTF8($str)));
					}
					if($case=="u"){
						$str = encodeUTF8(strtoupper(decodeUTF8($str)));
					}
					if($case=="c"){
						$str = ucfirst($str);
					}
					if($case=="w"){
						$str = ucwords($str);
					}
					if($case=="-"){
						$str = ($str);
					}
				}
			}

			else{
				if(is_array($language['us'])){
					if (array_key_exists($string, $language['us'])) { // string exists, but is not yet translated, we return english equivalent
						$str = $language['us'][$string];
						if($case=="l"){
							$str = strtolower($str);
						}
						if($case=="u"){
							$str = strtoupper($str);
						}
						if($case=="c"){
							$str = ucfirst($str);
						}
						if($case=="w"){
							$str = ucwords($str);
						}
						if($case=="-"){
							$str = ($str);
						}
					}
					else{ // if it is customized translation we just format and return
						$str = $string;
						if($case=="l"){
							$str = strtolower($str);
						}
						if($case=="u"){
							$str = strtoupper($str);
						}
						if($case=="c"){
							$str = ucfirst($str);
						}
						if($case=="w"){
							$str = ucwords($str);
						}
						if($case=="-"){
							$str = ($str);
						}
					}
				}
				else{ // if it is customized translation we just format and return
					$str = $string;
					if($case=="l"){
						$str = strtolower($str);
					}
					if($case=="u"){
						$str = strtoupper($str);
					}
					if($case=="c"){
						$str = ucfirst($str);
					}
					if($case=="w"){
						$str = ucwords($str);
					}
					if($case=="-"){
						$str = ($str);
					}
				}
			}
			return decodeUTF8($str);
		}
		else{
			return $string;
		}
	}
	function loadLangs(){
		global $baseURL;
		global $lang;
		$languageRaw = file_get_contents($baseURL."lang/gb.php");
		$language['gb'] = json_decode($languageRaw,true);
		// Om $lang är ogiltig/skadad (t.ex. en trasig cookie) finns ingen
		// lang/<tomt>.php eller lang/<konstigt>.php-fil, file_get_contents()
		// ger false, och json_decode(false) ger null - vilket kraschade
		// lang() längre ner (array_key_exists() mot null). Faller nu tillbaka
		// på redan inladdade gb istället för att krascha hela sidan.
		$language[$lang] = $language['gb']; // säker fallback, skrivs över nedan om filen är giltig
		if(is_string($lang) && $lang!="" && file_exists($baseURL."lang/".$lang.".php")){
			$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
			$decoded = json_decode($languageRaw,true);
			if(is_array($decoded)){
				$language[$lang] = $decoded;
			}
		}
		return $language;
	}
	function mySQLGET($query){
		global $con;
		$thisQuery = mysqli_query($con, $query);
		if(error_reporting() != 0){
			if(!$thisQuery){
				echo("Meteotemplate MySQL Error: " . mysqli_error($con));
			}
		}
		if($thisQuery){
			return mysqli_fetch_array($thisQuery);
		}
	}
	function isHTTPS() {
		return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
	}
	function convertor($n,$unit1,$unit2){
		// prepare input
		$unit1 = trim(strtolower($unit1));
		$unit2 = trim(strtolower($unit2));
		$unit1 = str_replace("/","",$unit1);
		$unit2 = str_replace("/","",$unit2);
		$unit1 = str_replace("kts","kt",$unit1);
		$unit2 = str_replace("kts","kt",$unit2);
		$unit1 = str_replace("knots","kt",$unit1);
		$unit2 = str_replace("knots","kt",$unit2);
		$unit1 = str_replace("kph","kmh",$unit1);
		$unit2 = str_replace("kph","kmh",$unit2);
		$unit1 = str_replace("mb","hpa",$unit1);
		$unit2 = str_replace("mb","hpa",$unit2);
		$unit1 = str_replace("miles","mi",$unit1);
		$unit2 = str_replace("miles","mi",$unit2);
		$unit1 = str_replace("feet","ft",$unit1);
		$unit2 = str_replace("feet","ft",$unit2);
		$unit1 = str_replace("foot","ft",$unit1);
		$unit2 = str_replace("foot","ft",$unit2);

		// return same units
		if($unit1==$unit2){
			return $n;
		}

		// temperature
		else if($unit1=="c" && $unit2=="f"){
			return $n*1.8 + 32;
		}
		else if($unit1=="f" && $unit2=="c"){
			return ($n - 32)/1.8;
		}

		// wind speed
		else if($unit1=="ms" && $unit2=="kmh"){
			return $n * 3.6;
		}
		else if($unit1=="ms" && $unit2=="mph"){
			return $n * 2.23694;
		}
		else if($unit1=="ms" && $unit2=="kt"){
			return $n * 1.943844;
		}
		else if($unit1=="kmh" && $unit2=="ms"){
			return $n / 3.6;
		}
		else if($unit1=="kmh" && $unit2=="mph"){
			return $n * 0.621371;
		}
		else if($unit1=="kmh" && $unit2=="kt"){
			return $n * 0.539957;
		}
		else if($unit1=="mph" && $unit2=="ms"){
			return $n * 0.44704;
		}
		else if($unit1=="mph" && $unit2=="kmh"){
			return $n * 1.609344;
		}
		else if($unit1=="mph" && $unit2=="kt"){
			return $n * 0.868976;
		}
		else if($unit1=="kt" && $unit2=="ms"){
			return $n * 0.514444;
		}
		else if($unit1=="kt" && $unit2=="kmh"){
			return $n * 1.852;
		}
		else if($unit1=="kt" && $unit2=="mph"){
			return $n * 1.150779;
		}

		// pressure
		else if($unit1=="hpa" && $unit2=="inhg"){
			return $n * 0.02952998;
		}
		else if($unit1=="hpa" && $unit2=="mmhg"){
			return $n * 0.750063755;
		}
		else if($unit1=="inhg" && $unit2=="hpa"){
			return $n * 33.863881;
		}
		else if($unit1=="inhg" && $unit2=="mmhg"){
			return $n * 25.400069;
		}
		else if($unit1=="mmhg" && $unit2=="hpa"){
			return $n * 1.3332239;
		}
		else if($unit1=="mmhg" && $unit2=="inhg"){
			return $n * 0.03937;
		}

		// precipitation
		else if($unit1=="mm" && $unit2=="in"){
			return $n * 0.0393701;
		}
		else if($unit1=="in" && $unit2=="mm"){
			return $n * 25.4;
		}

		else if($unit1=="mm" && $unit2=="cm"){
			return $n * 0.1;
		}
		else if($unit1=="cm" && $unit2=="mm"){
			return $n * 10;
		}

		// distance
		else if($unit1=="km" && $unit2=="mi"){
			return $n * 0.621371;
		}
		else if($unit1=="mi" && $unit2=="km"){
			return $n * 1.60934;
		}
		else if($unit1=="km" && $unit2=="ft"){
			return $n * 3280.84;
		}
		else if($unit1=="ft" && $unit2=="km"){
			return $n * 0.0003048;
		}
		else if($unit1=="m" && $unit2=="ft"){
			return $n * 3.28084;
		}
		else if($unit1=="ft" && $unit2=="m"){
			return $n * 0.3048;
		}
	}
	function convertT($value){
		global $dataTempUnits;
		global $displayTempUnits;
		$formats = $dataTempUnits.$displayTempUnits;
		if($formats=="CC"){
			return $value;
		}
		if($formats=="CF"){
			return round((($value*1.8)+32),3);
		}
		if($formats=="FC"){
			return round((($value-32)/1.8),3);
		}
		if($formats=="FF"){
			return $value;
		}
	}
	function convertTStddev($value){
		global $dataTempUnits;
		global $displayTempUnits;
		$formats = $dataTempUnits.$displayTempUnits;
		if($formats=="CC"){
			return $value;
		}
		if($formats=="CF"){
			return $value*1.8;
		}
		if($formats=="FC"){
			return $value/1.8;
		}
		if($formats=="FF"){
			return $value;
		}
	}
	function convertW($value){
		global $dataWindUnits;
		global $displayWindUnits;
		$formats = $dataWindUnits.$displayWindUnits;
		if($dataWindUnits==$displayWindUnits){
			return $value;
		}
		if($formats=="mskmh"){
			return round(($value*3.6),3);
		}
		if($formats=="msmph"){
			return round(($value*2.23694),3);
		}
		if($formats=="kmhms"){
			return round(($value/3.6),3);
		}
		if($formats=="kmhmph"){
			return round(($value*0.621371),3);
		}
		if($formats=="mphms"){
			return round(($value*0.44704),3);
		}
		if($formats=="mphkmh"){
			return round(($value*1.609344),3);
		}
		if($formats=="mskt"){
			return round(($value*1.943844),3);
		}
		if($formats=="kmhkt"){
			return round(($value*0.539957),3);
		}
		if($formats=="mphkt"){
			return round(($value*0.868976),3);
		}
		if($formats=="ktms"){
			return round(($value*0.514444),3);
		}
		if($formats=="ktkmh"){
			return round(($value*1.852),3);
		}
		if($formats=="ktmph"){
			return round(($value*1.150779),3);
		}
	}
	function convertP($value){
		global $dataPressUnits;
		global $displayPressUnits;
		$formats = $dataPressUnits.$displayPressUnits;
		if($dataPressUnits==$displayPressUnits){
			return $value;
		}
		if($formats=="hpammhg"){
			return round(($value*0.75006375541921),3);
		}
		if($formats=="hpainhg"){
			return round(($value*0.0295299830714),3);
		}
		if($formats=="mmhghpa"){
			return round(($value*1.3332239),3);
		}
		if($formats=="mmhginhg"){
			return round(($value*0.03937),3);
		}
		if($formats=="inhghpa"){
			return round(($value*33.863881579),3);
		}
		if($formats=="inhgmmhg"){
			return round(($value*25.400069),3);
		}
	}
	function convertR($value){
		global $dataRainUnits;
		global $displayRainUnits;
		$formats = $dataRainUnits.$displayRainUnits;
		if($formats=="mmmm"){
			return $value;
		}
		if($formats=="mmin"){
			return round(($value*0.0393701),3);
		}
		if($formats=="inmm"){
			return round(($value*25.4),3);
		}
		if($formats=="inin"){
			return $value;
		}
	}
	function unitFormatter($unit){
		$unit = str_replace('kmh','km/h',$unit);
		$unit = str_replace('ms','m/s',$unit);
		$unit = str_replace('inhg','inHg',$unit);
		$unit = str_replace('hpa','hPa',$unit);
		$unit = str_replace('mmhg','mmHg',$unit);
		$unit = str_replace('C','°C',$unit);
		$unit = str_replace('F','°F',$unit);
		return $unit;
	}
	function cleanHTML($html){
		$html = preg_replace("/\n/", "", $html);
		$html = preg_replace('/\s+/', ' ', $html);
		return $html;
	}
	function createCacheDir(){
		if(!is_dir("cache")){
			mkdir("cache");
		}
	}
	function showLog($errorLog){
		foreach($errorLog as $entry){
			if($entry[0]=="e"){
				echo "<script>console.error('Meteotemplate Error: ".$entry[1]."');</script>";
			}
			else if($entry[0]=="w"){
				echo "<script>console.warn('Meteotemplate Warning: ".$entry[1]."');</script>";
			}
			else{
				echo "<script>console.log('Meteotemplate: ".$entry[1]."');</script>";
			}
		}
	}
	function getColorHue($colorString,$type){
		if($type=="hex"){
			$rgb = convertColor($colorString);
			if ($rgb['r'] + $rgb['g'] + $rgb['b'] > 382){
				return "light";
			}
			else{
				return "dark";
			}
		}
		else{
			$colorBreakDown = str_replace("rgb(","",$colorString);
			$colorBreakDown = str_replace(")","",$colorBreakDown);
			$colorBreakDown = explode(",",$colorBreakDown);
			if ($colorBreakDown[0] + $colorBreakDown[1] + $colorBreakDown[2] > 382){
				return "light";
			}
			else{
				return "dark";
			}
		}   
    }
	function convertColor($hex, $alpha = false) {
		$hex      = str_replace('#', '', $hex);
		$length   = strlen($hex);
		$rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
		$rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
		$rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
		if ( $alpha ) {
			$rgb['a'] = $alpha;
		}
		return $rgb;
	}
	function avgWind($directions) { // based on http://en.wikipedia.org/wiki/Yamartino_method
	  $sinSum = 0;
	  $cosSum = 0;
	  foreach ($directions as $value) {
		$sinSum += sin(deg2rad($value));
		$cosSum += cos(deg2rad($value));
	  }
	  return ((rad2deg(atan2($sinSum, $cosSum)) + 360) % 360);
	}
	function windAbb($value){
		if($value<=11.25){
			return "N";
		}
		if($value>11.25 && $value<=33.75){
			return "NNE";
		}
		if($value>33.75 && $value<=56.25){
			return "NE";
		}
		if($value>56.25 && $value<=78.75){
			return "ENE";
		}
		if($value>78.75 && $value<=101.25){
			return "E";
		}
		if($value>101.25 && $value<=123.75){
			return "ESE";
		}
		if($value>123.75 && $value<=146.25){
			return "SE";
		}
		if($value>146.25 && $value<=168.75){
			return "SSE";
		}
		if($value>168.75 && $value<=191.25){
			return "S";
		}
		if($value>191.25 && $value<=213.75){
			return "SSW";
		}
		if($value>213.75 && $value<=236.25){
			return "SW";
		}
		if($value>236.25 && $value<=258.75){
			return "WSW";
		}
		if($value>258.75 && $value<=281.25){
			return "W";
		}
		if($value>281.25 && $value<=303.75){
			return "WNW";
		}
		if($value>303.75 && $value<=326.25){
			return "NW";
		}
		if($value>326.25 && $value<=348.75){
			return "NNW";
		}
		if($value>348.75){
			return "N";
		}
	}
	function curlMain($url,$timeout){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; rv:19.0) Gecko/20100101 Firefox/19.0");
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	function loadContent($url,$timeout){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36");
		$data = curl_exec($ch);
		curl_close($ch);

		if($data==""){
			$data = file_get_contents($url);
		}

		return $data;
	}
	function solarMax($time,$lat,$lon,$elevation,$elevationUnits){

		if($elevationUnits=="ft"){
			$z = 0.3048 * $elevation;
		}
		else{
			$z = $elevation;
		}
		
		$atc = 0.8;

		$lw  = (M_PI / 180) * -$lon;
		$phi = (M_PI / 180) * $lat;
		$d   = ($time / (60 * 60 * 24) - 0.5 + 2440588) - 2451545;

		$M = (M_PI / 180) * (357.5291 + 0.98560028 * $d);
		$L = ((M_PI / 180) * (1.9148 * sin($M) + 0.02 * sin(2 * $M) + 0.0003 * sin(3 * $M))) + $M + (M_PI / 180) * 102.9372 + M_PI;
		$declin = asin(sin(0) * cos(((M_PI / 180) * 23.4397)) + cos(0) * sin(((M_PI / 180) * 23.4397)) * sin($L));
		$rightAsc = atan2(sin($L) * cos(((M_PI / 180) * 23.4397)) - tan(0) * sin(((M_PI / 180) * 23.4397)), cos($L));

		$H   = ((M_PI / 180) * (280.16 + 360.9856235 * $d) - $lw) - $rightAsc;
		$altitude = asin(sin($phi) * sin($declin) + cos($phi) * cos($declin) * cos($H));
		$altitude = $altitude * 180 / M_PI;

		$JD = $time / (60 * 60 * 24) - 0.5 + 2440588;

		$dayTime = ($time - strtotime(date("Y",$time)."-".date("m",$time)."-".date('d',$time)." 00:00"))/(24*60*60*1000);

		$t = ($JD + $dayTime -2451545.0)/36525;
		$eccentricity = 0.01678634 + 0.000042037*$t + 0.0000001267*$t*$t;

		$mean_anomaly =  357.52911+ 35999.05029*$t + 0.0001537*$t*$t;
		if($mean_anomaly<0){
			$mean_anomaly=$mean_anomaly%360+360;
		}
		if($mean_anomaly>360){
			$mean_anomaly=$mean_anomaly%360;
		}

		$c = (1.914602 - 0.004817*$t + 0.000014*$t*$t)*sin($mean_anomaly*(M_PI / 180));
		$c =$c+ (0.019993 - 0.000101*$t)*sin(2*$mean_anomaly*(M_PI / 180));
		$c =$c+ 0.000289 *sin(3*$mean_anomaly*(M_PI / 180));

		$true_anomary = $mean_anomaly + $c;

		$radius = (1.000001018*(1-$eccentricity*$eccentricity))/(1 + $eccentricity*cos($true_anomary*(M_PI / 180)));
		$distance= $radius * 149598000;

		$el = $altitude;
		$R = $radius; 

		$nrel = 1367.0;
		$sinal = sin(deg2rad($el));
		if($sinal >= 0){
			$rm = pow((288.0 - 0.0065 * $z) / 288.0, 5.256) / ($sinal + 0.15 * pow($el + 3.885, -1.253));
			$toa = $nrel * $sinal / ($R * $R);
			$sr = $toa * pow($atc, $rm);
		}
		else{
			$sr = 0;
		}

		return $sr;
	}

	/* ############ COLOR FILL ######################## */
	function fill($value,$stops,$colors) {

		global $fromR, $fromG, $fromB;
		global $toR, $toG, $toB;

		for ($i = 0; $i < count($stops); $i++) {
			if ($value > $stops[$i] && $value <= $stops[$i + 1]) {
				$pos = ($value - $stops[$i]) * (1 / ($stops[$i + 1] - $stops[$i]));

				$minimum_color = $colors[$i];
				$maximum_color = $colors[$i + 1];

				$minimum_color = str_replace("#", "", $minimum_color);
				if (strlen($minimum_color) == 3) {
					$fromR = hexdec(substr($minimum_color, 0, 1) . substr($minimum_color, 0, 1));
					$fromG = hexdec(substr($minimum_color, 1, 1) . substr($minimum_color, 1, 1));
					$fromB = hexdec(substr($minimum_color, 2, 1) . substr($minimum_color, 2, 1));
				}
				else {
					$fromR = hexdec(substr($minimum_color, 0, 2));
					$fromG = hexdec(substr($minimum_color, 2, 2));
					$fromB = hexdec(substr($minimum_color, 4, 2));
				}

				$maximum_color = str_replace("#", "", $maximum_color);
				if (strlen($maximum_color) == 3) {
					$toR = hexdec(substr($maximum_color, 0, 1) . substr($maximum_color, 0, 1));
					$toG = hexdec(substr($maximum_color, 1, 1) . substr($maximum_color, 1, 1));
					$toB = hexdec(substr($maximum_color, 2, 1) . substr($maximum_color, 2, 1));
				}
				else {
					$toR = hexdec(substr($maximum_color, 0, 2));
					$toG = hexdec(substr($maximum_color, 2, 2));
					$toB = hexdec(substr($maximum_color, 4, 2));
				}

				$colorR = round($toR + ($fromR - $toR) * (1 - $pos));
				$colorG = round($toG + ($fromG - $toG) * (1 - $pos));
				$colorB = round($toB + ($fromB - $toB) * (1 - $pos));

				$color = "rgb(" . $colorR . "," . $colorG . "," . $colorB . ")";
			}
		}
		return $color;
	}
	function print_r_tree($data){
		// capture the output of print_r
		$out = print_r($data, true);

		// replace something like '[element] => <newline> (' with <a href="javascript:toggleDisplay('...');">...</a><div id="..." style="display: none;">
		$out = preg_replace('/([ \t]*)(\[[^\]]+\][ \t]*\=\>[ \t]*[a-z0-9 \t_]+)\n[ \t]*\(/iUe',"'\\1<a href=\"javascript:toggleDisplay(\''.(\$id = substr(md5(rand().'\\0'), 0, 7)).'\');\">\\2</a><div id=\"'.\$id.'\" style=\"display: none;\">'", $out);

		// replace ')' on its own on a new line (surrounded by whitespace is ok) with '</div>
		$out = preg_replace('/^\s*\)\s*$/m', '</div>', $out);

		// print the javascript function toggleDisplay() and then the transformed output
		echo '<script language="Javascript">function toggleDisplay(id) { document.getElementById(id).style.display = (document.getElementById(id).style.display == "block") ? "none" : "block"; }</script>'."\n$out";
	}
	/* ############ MOON FUNCTIONS #################### */
	// Moon rise/set
	class MoonRiSet {
		const RADEG = 57.29577951308232;
		const DEGRAD = 0.01745329251994;
		const ARC = 206264.8;
		const SIN_EPS = 0.39768; // sin+cos obliquity ecliptic (23d26m)
		const COS_EPS = 0.91752;
		const PREC = 18;    // precision

		private $_sinEarthLatitude, $_cosEarthLatitude;
		private $_data = array();

		public function __construct($earthLatitude = false, $earthLongitude = false, $earthTimezone = false) {
			if ($earthLatitude === false)
				$this->earthLatitude = ini_get('date.default_latitude');
			else
				$this->earthLatitude = $earthLatitude;

			if ($earthLongitude === false)
				$this->earthLongitude = ini_get('date.default_longitude');
			else
				$this->earthLongitude = $earthLongitude;

			if ($earthTimezone === false)
				$this->earthTimezone = ini_get('date.timezone');
			else
				$this->earthTimezone = $earthTimezone;
			// set current day
			$this->setDate(date("Y", time()), date("n", time()), date("j", time()));
		}
		// set day
		public function setDate($year, $month, $day) {
			if ($year < 1583 or $year > 2500) return(false);

			$old_timezone = date_default_timezone_get();
			date_default_timezone_set($this->earthTimezone);

			// calculation day's table, begin+end time
			$t = $tb = mktime(0, 0, 0, $month, $day, $year);
			$te = mktime(24, 0, 0, $month, $day, $year);
			$this->tdiff = ($te - $tb) / self::PREC;
			$this->_sinEarthLatitude = $this->dsin($this->earthLatitude);
			$this->_cosEarthLatitude = $this->dcos($this->earthLatitude);

			$i = 0;
			while ($i <= self::PREC) {
				$this->_data[$i]["timestamp"] = $t;
				$jd = $this->getJulianDate($t);
				$LST = $this->getLST($jd); // Local Sidereal Time
				$this->_data[$i]["LST"] = $LST;
				list($RA, $de) = $this->miniMoon(($jd - 2451545.0) / 36525.0);
				$this->_data[$i]["RA"] = $RA;
				$HA = $LST - $RA;    // hour angle
				if ($HA > 12) $HA -= 24;
				$this->_data[$i]["HA"] = $HA;
				$this->_data[$i]["sAlt"] = $this->_sinEarthLatitude * $this->dsin($de) + $this->_cosEarthLatitude * $this->dcos($de) * $this->dcos(15*$this->_data[$i]["HA"]); // sinus Altitude
				$t += $this->tdiff;
				$i++;
			}
			// Moon transit
			list($this->transit["timestamp"], $this->transit["hhmm"], $this->transit["hh:mm"]) = $this->getTransit("HA");
			// Moon's rise and set
			list(
				$this->rise["timestamp"], $this->rise["hhmm"], $this->rise["hh:mm"],
				$this->set["timestamp"], $this->set["hhmm"], $this->set["hh:mm"],
				$this->rise2["timestamp"], $this->rise2["hhmm"], $this->rise2["hh:mm"],
				$this->set2["timestamp"], $this->set2["hhmm"], $this->set2["hh:mm"]
			) = $this->getRiSet("sAlt", $this->dsin(0.125));

			date_default_timezone_set($old_timezone);
			return(true);
		}
		// PRIVATE //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		private function getTransit($o) {
			$fT = false;
			for ($i = 1; $i < self::PREC && !$fT; $i += 2) {
				if (($this->_data[$i-1][$o] < 0 && $this->_data[$i][$o] >= 0) || ($this->_data[$i][$o] <= 0 && $this->_data[$i+1][$o] > 0)) {
					list($nz, $z1, $z2, $xe, $ye) = $this->quad($this->_data[$i-1][$o], $this->_data[$i][$o], $this->_data[$i+1][$o]);
					$fT = true;
					$oTran = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
				}
			}
			if ($fT)
				list($transit["timestamp"], $transit["hhmm"], $transit["hh:mm"]) = $this->formatTime($oTran);
			else {
				$transit["timestamp"] = false;
				$transit["hhmm"] = "    ";
				$transit["hh:mm"] = "     ";
			}
			return array($transit["timestamp"], $transit["hhmm"], $transit["hh:mm"]);
		}
		private function getRiSet($o, $alt) {

			$fRise = $fSet = false;
			$fAbove = false;
			$oRise2 = $oSet2 = false;

			if ($this->_data[0][$o] > $alt)
				$fAbove = true;
			for ($i = 1; $i < self::PREC; $i += 2) {
				list($nz, $z1, $z2, $xe, $ye) = $this->quad($this->_data[$i-1][$o]-$alt, $this->_data[$i][$o]-$alt, $this->_data[$i+1][$o]-$alt);
				if ($nz == 1) {
					if ($this->_data[$i-1][$o] < $alt) {
						if ($fRise === true)
							$oRise2 = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
						else {
							$oRise = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
							$fRise = true;
						}
					}
					else {
						if ($fSet === true)
							$oSet2 = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
						else {
							$oSet = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
							$fSet = true;
						}
					}
				}
				elseif ($nz == 2) {
					if ($ye < 0.0) {
						$oRise = $this->_data[$i]["timestamp"] + $this->tdiff*$z2;
						$oSet = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
					}
					else {
						$oRise = $this->_data[$i]["timestamp"] + $this->tdiff*$z1;
						$oSet = $this->_data[$i]["timestamp"] + $this->tdiff*$z2;
					}
					$fRise = $fSet = true;
				}
			}
			// output
			if ($fRise === true || $fSet === true ) {
				if ($fRise === true) {
					list($rise["timestamp"], $rise["hhmm"], $rise["hh:mm"]) = $this->formatTime($oRise);
					list($rise2["timestamp"], $rise2["hhmm"], $rise2["hh:mm"]) = $this->formatTime($oRise2);
				}
				else {
					$rise["timestamp"] = false;
					$rise["hhmm"] = "    ";
					$rise["hh:mm"] = "     ";
				}
				if ($fSet === true) {
					list($set["timestamp"], $set["hhmm"], $set["hh:mm"]) = $this->formatTime($oSet);
					list($set2["timestamp"], $set2["hhmm"], $set2["hh:mm"]) = $this->formatTime($oSet2);
				}
				else {
					$set["timestamp"] = true;
					$set["hhmm"] = "    ";
					$set["hh:mm"] = "     ";
				}
			}
			else {
				if ($fAbove === true) { // continuously above horizon
					$rise["timestamp"] = $set["timestamp"] = true;
					$rise["hhmm"] = $set["hhmm"] = "****";
					$rise["hh:mm"] = $set["hh:mm"] = "**:**";
				}
				else { // continuously below horizon
					$rise["timestamp"] = $set["timestamp"] = false;
					$rise["hhmm"] = $set["hhmm"] = "----";
					$rise["hh:mm"] = $set["hh:mm"] = "--:--";
				}
			}
			// return
			if ($oRise2 !== false)
				return array(
					$rise["timestamp"], $rise["hhmm"], $rise["hh:mm"],
					$set["timestamp"], $set["hhmm"], $set["hh:mm"],
					$rise2["timestamp"], $rise2["hhmm"], $rise2["hh:mm"],
					false, false, false);
			elseif ($oSet2 !== false)
				return array(
					$rise["timestamp"], $rise["hhmm"], $rise["hh:mm"],
					$set["timestamp"], $set["hhmm"], $set["hh:mm"],
					false, false, false,
					$set2["timestamp"], $set2["hhmm"], $set2["hh:mm"]);
			else
				return array(
					$rise["timestamp"], $rise["hhmm"], $rise["hh:mm"],
					$set["timestamp"], $set["hhmm"], $set["hh:mm"],
					false, false, false,
					false, false, false);
		}

		// Low precision formulae for planetary position, Flandern & Pulkkinen
		// returns ra and dec of Moon to 5 arc min (ra) and 1 arc min (dec)
		// for a few centuries either side of J2000.0
		// Predicts rise and set times to within minutes for about 500 years
		private function miniMoon($T) {

			$l0 = $this->frac(0.606433 + 1336.855225 * $T);
			$l = 2*M_PI * $this->frac(0.374897 + 1325.552410 * $T);
			$ls = 2*M_PI * $this->frac(0.993133 + 99.997361 * $T);
			$d = 2*M_PI * $this->frac(0.827361 + 1236.853086 * $T);
			$f = 2*M_PI * $this->frac(0.259086 + 1342.227825 * $T);

			// perturbation
			$dl =  22640 * sin($l);
			$dl += -4586 * sin($l - 2*$d);
			$dl += +2370 * sin(2*$d);
			$dl +=  +769 * sin(2*$l);
			$dl +=  -668 * sin($ls);
			$dl +=  -412 * sin(2*$f);
			$dl +=  -212 * sin(2*$l - 2*$d);
			$dl +=  -206 * sin($l + $ls - 2*$d);
			$dl +=  +192 * sin($l + 2*$d);
			$dl +=  -165 * sin($ls - 2*$d);
			$dl +=  -125 * sin($d);
			$dl +=  -110 * sin($l + $ls);
			$dl +=  +148 * sin($l - $ls);
			$dl +=   -55 * sin(2*$f - 2*$d);

			$s = $f + ($dl + 412 * sin(2*$f) + 541*sin($ls)) / self::ARC;
			$h = $f - 2*$d;

			$n =   -526 * sin($h);
			$n +=   +44 * sin($l + $h);
			$n +=   -31 * sin(-$l + $h);
			$n +=   -23 * sin($ls + $h);
			$n +=   +11 * sin(-$ls + $h);
			$n +=   -25 * sin(-2*$l + $f);
			$n +=   +21 * sin(-$l + $f);

			$l_moon = 2 * M_PI * $this->frac($l0 + $dl / 1296000);
			$b_moon = (18520.0 * sin($s) + $n) / self::ARC;

			// convert to equatorial coords using a fixed ecliptic
			$cb = cos($b_moon);
			$x = $cb * cos($l_moon);
			$v = $cb * sin($l_moon);
			$w = sin($b_moon);
			$y = self::COS_EPS * $v - self::SIN_EPS * $w;
			$z = self::SIN_EPS * $v + self::COS_EPS * $w;
			$rho = sqrt(1.0 - $z*$z);
			$de = (180/M_PI) * atan($z / $rho);
			$RA = (24/M_PI) * atan($y / ($x + $rho));
			if ($RA < 0) $RA += 24;
			return(array($RA, $de));
		}
		// finds the parabola through the three points (-1,ym), (0,yz), (1, yp) and returns
		// the coordinates of the values of x where the parabola crosses zero (roots of the quadratic)
		// and the number of roots (0, 1 or 2) within the interval [-1, 1]
		private function quad($ym, $yz, $yp) {
			$z1 = $z2 = 0;
			$nz = 0;
			$a = 0.5 * ($ym + $yp) - $yz;
			$b = 0.5 * ($yp - $ym);
			$c = $yz;
			$xe = -$b / (2 * $a);
			$ye = ($a * $xe + $b) * $xe + $c;
			$dis = $b * $b - 4.0 * $a * $c;
			if ($dis > 0) {
				$dx = 0.5 * sqrt($dis) / abs($a);
				$z1 = $xe - $dx;
				$z2 = $xe + $dx;
				if (abs($z1) <= 1.0) $nz += 1;
				if (abs($z2) <= 1.0) $nz += 1;
				if ($z1 < -1.0) $z1 = $z2;
			}
			return(array($nz, $z1, $z2, $xe, $ye));
		}
		private function getJulianDate($t) {
			//return $t/86400 + 2440587.5; // only for 64bit && year > 1582
			$jd = gregoriantojd(gmdate("n", $t) ,gmdate("j", $t) , gmdate("Y", $t)) - 0.5;
			$jd += gmdate("H", $t)/24 + gmdate("i", $t)/1440 + gmdate("s", $t)/86400;
			return($jd);
		}
		// returns the local sidereal time (degree)
		private function getLST($jd) {
			$mjd = $jd - 2451545.0;
			$lst = $this->range(280.46061837 + 360.98564736629 * $mjd);
			return ($lst + $this->earthLongitude)/15;
		}
		// round time, return array(timestamp, "hhmm", "hh:mm")
		private function formatTime($t) {
			$t0 = 60*(int)($t/60+0.5);
			if (date("j", $t) == date("j", $t0))
				$t = $t0;
			return array($t, date("Hi", $t), date("H:i", $t));
		}

		private function frac($x) {
			return($x - floor($x));
		}
		private function range($x) {
			return($x - 360.0 * (Floor($x / 360.0)));
		}
		private function dsin($x) {
			return(sin($x * self::DEGRAD));
		}
		private function dcos($x) {
			return(cos($x * self::DEGRAD));
		}
	}

	function special(){
		global $pageURL;
		global $path;
		global $stationLat;
		global $stationCountry;

		$timestampNow = time();

		$icon = "fa fa-home";

		if(date('m',$timestampNow)==1 && date('d',$timestampNow)==1){
			$icon = "fa fa-glass";
		}
		if(date('m',$timestampNow)==12 && date('d',$timestampNow)==31){
			$icon = "fa fa-hourglass-end";
		}
		if(date('m',$timestampNow)==12 && date('d',$timestampNow)==24){
			$icon = "fa fa-gift";
		}
		if(date('m',$timestampNow)==12 && date('d',$timestampNow)==25){
			$icon = "fa fa-gift";
		}
		if(date('m',$timestampNow)==12 && date('d',$timestampNow)>=20 && date('d',$timestampNow)<24){
			$icon = "fa fa-tree";
		}
		if(date('m',$timestampNow)==3 && date('d',$timestampNow)==21){
			if($stationLat>0){
				$icon = "mticon-spring";
			}
			else{
				$icon = "mticon-autumn";
			}
		}
		if(date('m',$timestampNow)==6 && date('d',$timestampNow)==21){
			if($stationLat>0){
				$icon = "mticon-summer";
			}
			else{
				$icon = "mticon-winter";
			}
		}
		if(date('m',$timestampNow)==9 && date('d',$timestampNow)==23){
			if($stationLat>0){
				$icon = "mticon-autumn";
			}
			else{
				$icon = "mticon-spring";
			}
		}
		if(date('m',$timestampNow)==12 && date('d',$timestampNow)==21){
			if($stationLat>0){
				$icon = "mticon-winter";
			}
			else{
				$icon = "mticon-summer";
			}
		}
		if($stationCountry=="us"){
			if(date('Y',$timestampNow)==2017){
				if(date('m',$timestampNow)==11 && date('d',$timestampNow)==23){
					$icon = "fa fa-cutlery";
				}
			}
			if(date('Y',$timestampNow)==2018){
				if(date('m',$timestampNow)==11 && date('d',$timestampNow)==22){
					$icon = "fa fa-cutlery";
				}
			}
			if(date('Y',$timestampNow)==2019){
				if(date('m',$timestampNow)==11 && date('d',$timestampNow)==28){
					$icon = "fa fa-cutlery";
				}
			}
		}
		if($stationCountry=="ca"){
			if(date('Y',$timestampNow)==2017){
				if(date('m',$timestampNow)==10 && date('d',$timestampNow)==9){
					$icon = "fa fa-cutlery";
				}
			}
			if(date('Y',$timestampNow)==2018){
				if(date('m',$timestampNow)==10 && date('d',$timestampNow)==8){
					$icon = "fa fa-cutlery";
				}
			}
			if(date('Y',$timestampNow)==2019){
				if(date('m',$timestampNow)==10 && date('d',$timestampNow)==14){
					$icon = "fa fa-cutlery";
				}
			}
		}
		if(date('m',$timestampNow)==10 && date('d',$timestampNow)==31){
			$icon = "fa fa-meh-o";
		}
		if(date('Y',$timestampNow)==2017){
			if(date('m',$timestampNow)==4 && date('d',$timestampNow)==16){
				$icon = "fa fa-home";
			}
		}
		if(date('Y',$timestampNow)==2018){
			if(date('m',$timestampNow)==4 && date('d',$timestampNow)==1){
				$icon = "fa fa-home";
			}
		}
		if(date('Y',$timestampNow)==2019){
			if(date('m',$timestampNow)==4 && date('d',$timestampNow)==21){
				$icon = "fa fa-home";
			}
		}

		return $icon;
	}

	$fooCont = file_get_contents($baseURL."/footer.php");
	if (strpos(strtolower($fooCont), 'meteotemplate.com') == false) {
		echo "<!-- Error -->Error, contact the developer.";
		die();
	}

	// Moonphase
	define('MP_NEW_MOON_NAME','New moon');
	define('MP_NEW_MOON_ID',0);
	define('MP_WAXING_CRESCENT_NAME','Waxing crescent');
	define('MP_WAXING_CRESCENT_ID',1);
	define('MP_FIRST_QUARTER_NAME','First quarter');
	define('MP_FIRST_QUARTER_ID',2);
	define('MP_WAXING_GIBBOUS_NAME','Waxing gibbous');
	define('MP_WAXING_GIBBOUS_ID',3);
	define('MP_FULL_MOON_NAME','Full');
	define('MP_FULL_MOON_ID',4);
	define('MP_WANING_GIBBOUS_NAME','Waning gibbous');
	define('MP_WANING_GIBBOUS_ID',5);
	define('MP_THIRD_QUARTER_MOON_NAME','Third quarter');
	define('MP_THIRD_QUARTER_MOON_ID',6);
	define('MP_WANING_CRESCENT_NAME','Waning crescent');
	define('MP_WANING_CRESCENT_ID',7);
	define('MP_DAY_IN_SECONDS', 60 * 60 * 24);

	class moonPhase {
		var $allMoonPhases = array();
		var $dateAsTimeStamp;
		var $moonPhaseIDforDate;
		var $moonPhaseNameForDate;
		var $periodInDays = 29.53058867; // == complete moon cycle
		var $periodInSeconds = -1; // gets set when you ask for it
		var $someFullMoonDate;
		// $timestamp (int) date of which to calculate a moon phase and relative phases for
		// PHP4-stilens "samma namn som klassen"-konstruktor tas inte längre emot
		// som konstruktor i PHP8 - utan __construct() kördes aldrig setDate(),
		// vilket gjorde att alla anrop returnerade en fryst, felaktig månfas
		// (position 0, 100% belyst, ingen fasnamn) oavsett verkligt datum.
		function __construct($timeStamp = -1) {
			$this->allMoonPhases = array(
				MP_NEW_MOON_NAME,
				MP_WAXING_CRESCENT_NAME,
				MP_FIRST_QUARTER_NAME,
				MP_WAXING_GIBBOUS_NAME,
				MP_FULL_MOON_NAME,
				MP_WANING_GIBBOUS_NAME,
				MP_THIRD_QUARTER_MOON_NAME,
				MP_WANING_CRESCENT_NAME);
			$this->someFullMoonDate = strtotime("March 5 2015 18:05 UTC");
			if($timeStamp == '' or $timeStamp == -1) $timeStamp = time();
			$this->setDate($timeStamp);
		}
		function calcMoonPhase() {
			$position = $this->getPositionInCycle();
			if($position >= 0.474 && $position <= 0.53)
				$phaseInfoForCurrentDate = array(MP_NEW_MOON_ID, MP_NEW_MOON_NAME);
			else if ($position >= 0.53 && $position <= 0.724)
				$phaseInfoForCurrentDate = array(MP_WAXING_CRESCENT_ID, MP_WAXING_CRESCENT_NAME);
			else if ($position >= 0.724 && $position <= 0.776)
				$phaseInfoForCurrentDate = array(MP_FIRST_QUARTER_ID, MP_FIRST_QUARTER_NAME);
			else if ($position >= 0.776 && $position <= 0.974)
				$phaseInfoForCurrentDate = array(MP_WAXING_GIBBOUS_ID, MP_WAXING_GIBBOUS_NAME);
			else if ($position >= 0.974 || $position <= 0.026)
				$phaseInfoForCurrentDate = array(MP_FULL_MOON_ID, MP_FULL_MOON_NAME);
			else if ($position >= 0.026 && $position <= 0.234)
				$phaseInfoForCurrentDate = array(MP_WANING_GIBBOUS_ID, MP_WANING_GIBBOUS_NAME);
			else if ($position >= 0.234 && $position <= 0.295)
				$phaseInfoForCurrentDate = array(MP_THIRD_QUARTER_MOON_ID, MP_THIRD_QUARTER_MOON_NAME);
			else if ($position >= 0.295 && $position <= 0.4739)
				$phaseInfoForCurrentDate = array(MP_WANING_CRESCENT_ID, MP_WANING_CRESCENT_NAME);
			list($this->moonPhaseIDforDate,$this->moonPhaseNameForDate) = $phaseInfoForCurrentDate;
		}

		function getAllMoonPhases() {
			return $this->allMoonPhases;
		}
		function getBaseFullMoonDate() {
			return $this->someFullMoonDate;
		}
		function getDateAsTimeStamp() {
			return $this->dateAsTimeStamp;
		}
		function getDaysUntilNextFullMoon() {
			$position = $this->getPositionInCycle();
			return round((1 - $position) * $this->getPeriodInDays(), 7);
		}
		function getDaysUntilNextLastQuarterMoon() {
			$days = 0;
			$position = $this->getPositionInCycle();
			if ($position < 0.25)
				$days = (0.25 - $position) * $this->getPeriodInDays();
			else if ($position >= 0.25)
				$days = (1.25 - $position) * $this->getPeriodInDays();
			return round($days, 7);
		}
		function getDaysUntilNextFirstQuarterMoon() {
			$days = 0;
			$position = $this->getPositionInCycle();
			if ($position < 0.75)
				$days = (0.75 - $position) * $this->getPeriodInDays();
			else if ($position >= 0.75)
				$days = (1.75 - $position) * $this->getPeriodInDays();
			return round($days,7);
		}
		function getDaysUntilNextNewMoon() {
			$days = 0;
			$position = $this->getPositionInCycle();
			if ($position < 0.5)
				$days = (0.5 - $position) * $this->getPeriodInDays();
			else if ($position >= 0.5)
				$days = (1.5 - $position) * $this->getPeriodInDays();
			return round($days, 7);
		}
		function getPercentOfIllumination() {
			$percentage = (1.0 + cos(2.0 * M_PI * $this->getPositionInCycle())) / 2.0;
			$percentage *= 100;
			$percentage = round($percentage,1) . '%';
			return $percentage;
		}
		function getPeriodInDays() {
			return $this->periodInDays;
		}
		function getPeriodInSeconds() {
			if($this->periodInSeconds > -1) return $this->periodInSeconds; // in case it was cached
			$this->periodInSeconds = $this->getPeriodInDays() * MP_DAY_IN_SECONDS;
			return $this->periodInSeconds;
		}
		function getPhaseID() {
			return $this->moonPhaseIDforDate;
		}
		function getPhaseName($ID = -1) {
			if($ID <= -1)
				return $this->moonPhaseNameForDate; // get name for this current date
			return $this->allMoonPhases[$ID]; // or.. get name for a specific ID
		}
		function getPositionInCycle() {
			$diff = $this->getDateAsTimeStamp() - $this->getBaseFullMoonDate();
			$periodInSeconds = $this->getPeriodInSeconds();
			$position = (($diff) % floor($periodInSeconds));
			$position = $position / $periodInSeconds;
			if ($position < 0)
				$position = 1 + $position;
			return $position;
		}
		function getUpcomingWeekArray($newStartingDateAsTimeStamp = -1) {
			$newStartingDateAsTimeStamp = ($newStartingDateAsTimeStamp > -1)
				? $newStartingDateAsTimeStamp
				: $this->getDateAsTimeStamp();
			$moonPhaseObj = get_class($this);
			$weeklyPhase = new $moonPhaseObj($newStartingDateAsTimeStamp);
			$upcomingWeekArray = array();
			for(	$day = 0, $thisTimeStamp = $weeklyPhase->getDateAsTimeStamp();
						$day < 7; $day++, $thisTimeStamp += MP_DAY_IN_SECONDS) {
				$weeklyPhase->setDate($thisTimeStamp);
				$upcomingWeekArray[$thisTimeStamp] = $weeklyPhase->getPhaseID();
			} // END for($day = 0; $day < 7; $day++) {
			unset($weeklyPhase);
			return $upcomingWeekArray;
		}
		function setDate($timeStamp = -1) {
			if($timeStamp == '' or $timeStamp == -1) $timeStamp = time();
			$this->dateAsTimeStamp = $timeStamp;
			$this->calcMoonPhase();
		}
	}

	############################################################################
	#	SMHI weather warnings banner
	############################################################################

	// Returns active SMHI warnings for the counties in $weatherWarningsCounties,
	// sorted highest severity first. Cached to disk (see interactiveBanner.txt
	// above for the same pattern) so we don't hit SMHI on every page load.
	// De 21 svenska länen, exakt som SMHI stavar dem (verifierat mot
	// SMHIs metadata/area.json, code SWE_COUNTIES). Delad mellan
	// getActiveWeatherWarnings() och inställningsdialogens kryssrutor.
	function getSwedishCounties(){
		return array(
			"Blekinge län","Dalarnas län","Gotlands län","Gävleborgs län",
			"Hallands län","Jämtlands län","Jönköpings län","Kalmar län",
			"Kronobergs län","Norrbottens län","Skåne län","Stockholms län",
			"Södermanlands län","Uppsala län","Värmlands län","Västerbottens län",
			"Västernorrlands län","Västmanlands län","Västra Götalands län",
			"Örebro län","Östergötlands län"
		);
	}

	// Besökaren kan välja egna län via inställningsdialogen (cookie
	// weatherWarningsPrefs, satt av userWeatherPrefs.php). Faller
	// tillbaka på adminens $weatherWarningsCounties om ingen cookie finns
	// eller om den är tom.
	// Vissa externa källor (t.ex. SMHI) levererar text på flera språk
	// själva, till skillnad från t.ex. NOAA som bara ger engelska. Den
	// här funktionen svarar "vilket språkfält ska vi plocka från en sån
	// källa" - matchar samma "engelska som fallback"-princip som lang()
	// redan använder för sajtens egna översatta texter. Källor med bara
	// ETT språk (NOAA m.fl.) ska inte anropa den här alls - visa bara
	// källans enda språk rakt av, ingen låtsas-översättning.
	function getSourceLanguage(){
		global $lang;
		return ($lang=="se") ? "sv" : "en";
	}

	function getWeatherWarningsCounties(){
		global $weatherWarningsCounties;

		if(isset($_COOKIE['weatherWarningsPrefs'])){
			$prefs = json_decode($_COOKIE['weatherWarningsPrefs'],true);
			if(is_array($prefs) && isset($prefs['counties']) && is_array($prefs['counties']) && count($prefs['counties']) > 0){
				return $prefs['counties'];
			}
		}
		return $weatherWarningsCounties;
	}

	function getActiveWeatherWarnings(){
		global $baseURL;
		$weatherWarningsCounties = getWeatherWarningsCounties();

		$cacheFile = $baseURL."cache/vaderVarningar.json";
		$maxAge = 15 * 60; // 15 minuter - matchar HA-larmets pollingintervall

		if(file_exists($cacheFile)){
			if(time() - filemtime($cacheFile) > $maxAge){
				unlink($cacheFile);
			}
		}
		if(file_exists($cacheFile)){
			$rawWarnings = file_get_contents($cacheFile);
		}
		else{
			$warningsURL = "https://opendata-download-warnings.smhi.se/ibww/api/version/1/warning.json";
			$rawWarnings = curlMain($warningsURL,5);
			if($rawWarnings==""){
				$rawWarnings = file_get_contents($warningsURL);
			}
			if($rawWarnings!=""){
				file_put_contents($cacheFile,$rawWarnings);
			}
		}

		if($rawWarnings==""){
			return array();
		}

		$warnings = json_decode($rawWarnings,true);
		if(!is_array($warnings)){
			return array();
		}

		$levelRank = array("MESSAGE"=>0,"YELLOW"=>1,"ORANGE"=>2,"RED"=>3);
		$levelColor = array("MESSAGE"=>"grey","YELLOW"=>"yellow","ORANGE"=>"orange","RED"=>"red");
		$sourceLang = getSourceLanguage(); // "sv" eller "en" beroende på besökarens språkval

		$active = array();
		foreach($warnings as $warning){
			if(!isset($warning['warningAreas'])){
				continue;
			}
			foreach($warning['warningAreas'] as $area){
				$counties = array();
				if(isset($area['affectedAreas'])){
					foreach($area['affectedAreas'] as $affected){
						if(isset($affected['sv'])){
							$counties[] = $affected['sv'];
						}
					}
				}
				$match = false;
				foreach($weatherWarningsCounties as $wantedCounty){
					if(in_array($wantedCounty,$counties)){
						$match = true;
						break;
					}
				}
				if(!$match){
					continue;
				}
				$levelCode = isset($area['warningLevel']['code']) ? $area['warningLevel']['code'] : "MESSAGE";
				$description = "";
				if(isset($area['descriptions']) && is_array($area['descriptions'])){
					foreach($area['descriptions'] as $desc){
						if(isset($desc['text'][$sourceLang]) && $desc['text'][$sourceLang]!=""){
							$description .= $desc['text'][$sourceLang]." ";
						}
						else if(isset($desc['text']['sv'])){
							$description .= $desc['text']['sv']." "; // källan saknade det efterfrågade språket, visa svenska hellre än inget alls
						}
					}
				}
				$active[] = array(
					"event" => isset($warning['event'][$sourceLang]) ? $warning['event'][$sourceLang] : (isset($warning['event']['sv']) ? $warning['event']['sv'] : ""),
					"levelCode" => $levelCode,
					"levelName" => isset($area['warningLevel'][$sourceLang]) ? $area['warningLevel'][$sourceLang] : (isset($area['warningLevel']['sv']) ? $area['warningLevel']['sv'] : $levelCode),
					"levelRank" => isset($levelRank[$levelCode]) ? $levelRank[$levelCode] : 0,
					"levelColor" => isset($levelColor[$levelCode]) ? $levelColor[$levelCode] : "grey",
					// länsnamn hålls medvetet på svenska oavsett besökarspråk - egennamn/
					// ortnamn, samma princip som att "Stockholm" inte översätts på en engelsk sida
					"county" => implode(", ",$counties),
					"description" => trim($description)
				);
			}
		}

		usort($active,function($a,$b){
			return $b['levelRank'] - $a['levelRank'];
		});

		return $active;
	}

	// Norrskenschans - NOAA:s planetära Kp-index (geomagnetisk aktivitet 0-9),
	// samma fetch+cache-mönster som getActiveWeatherWarnings(). Kp-indexet
	// uppdateras hos NOAA var 3:e timme, så 30 min cache är gott om marginal.
	function getAuroraForecast(){
		global $baseURL;

		$cacheFile = $baseURL."cache/auroraKp.json";
		$maxAge = 30 * 60;

		if(file_exists($cacheFile)){
			if(time() - filemtime($cacheFile) > $maxAge){
				unlink($cacheFile);
			}
		}
		if(file_exists($cacheFile)){
			$rawKp = file_get_contents($cacheFile);
		}
		else{
			$kpURL = "https://services.swpc.noaa.gov/products/noaa-planetary-k-index.json";
			$rawKp = curlMain($kpURL,5);
			if($rawKp==""){
				$rawKp = file_get_contents($kpURL);
			}
			if($rawKp!=""){
				file_put_contents($cacheFile,$rawKp);
			}
		}

		if($rawKp==""){
			return array();
		}
		$rows = json_decode($rawKp,true);
		if(!is_array($rows) || count($rows)<1){
			return array();
		}
		// varje rad är ett objekt {"time_tag":...,"Kp":...,"a_running":...,"station_count":...},
		// verifierat live 2026-09-23 - sista posten är senaste avläsningen
		$latest = end($rows);
		if(!isset($latest['time_tag']) || !isset($latest['Kp'])){
			return array();
		}

		return array(
			"time" => $latest['time_tag'],
			"kp" => floatval($latest['Kp'])
		);
	}

	// Norrskenschans-nivå utifrån Kp - grov approximation för stationens
	// breddgrad (~58°N). Molntäcke/mörker spelar minst lika stor roll, se
	// tooltip-texten där denna används. Delad mellan sky-blocket och
	// den fullständiga norrskenssidan/blocket.
	function getAuroraTier($kp){
		if($kp>=8){
			return array("label"=>"very good chance","shade"=>"800");
		}
		else if($kp>=6){
			return array("label"=>"good chance","shade"=>"600");
		}
		else if($kp>=4){
			return array("label"=>"possible under a dark sky","shade"=>"400");
		}
		return array("label"=>"low chance","shade"=>"200");
	}

	// NOAA:s OVATION-modell - global norrskenssannolikhetsgrid (1° upplösning,
	// ~65000 punkter, [lon 0-359, lat -90..90, sannolikhet 0-100]). Samma
	// fetch+cache-mönster som getAuroraForecast()/getActiveWeatherWarnings().
	// Filtrering till ett relevant kartutsnitt görs av anroparen (se
	// pages/astronomy/auroraOvationAjax.php), inte här - den här funktionen
	// returnerar hela den råa strukturen.
	function getAuroraOvationGrid(){
		global $baseURL;

		$cacheFile = $baseURL."cache/auroraOvation.json";
		$maxAge = 30 * 60;

		if(file_exists($cacheFile)){
			if(time() - filemtime($cacheFile) > $maxAge){
				unlink($cacheFile);
			}
		}
		if(file_exists($cacheFile)){
			$rawGrid = file_get_contents($cacheFile);
		}
		else{
			$gridURL = "https://services.swpc.noaa.gov/json/ovation_aurora_latest.json";
			$rawGrid = curlMain($gridURL,8);
			if($rawGrid==""){
				$rawGrid = file_get_contents($gridURL);
			}
			if($rawGrid!=""){
				file_put_contents($cacheFile,$rawGrid);
			}
		}

		if($rawGrid==""){
			return array();
		}
		$data = json_decode($rawGrid,true);
		if(!is_array($data) || !isset($data['coordinates'])){
			return array();
		}
		return $data;
	}

?>
