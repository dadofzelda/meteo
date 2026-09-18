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
	#	Equisol and Solstice calculation
	#
	# 	An ajax script to calculate equinox and solstice tied with the
	#	equisol.php script.
	#
	############################################################################
	#
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."header.php");

	$y = $_GET['y'];
	
	if($y==""){
		$y = date('Y');
	}
	
	$tz = date_default_timezone_get();
	$myTZ  = new DateTimeZone($stationTZ);
	
	if($y<1000){
		$y = $y/1000;
		$ME_JDE = 1721139.29189 + 365242.13740*$y + 0.06134*($y*$y) + 0.00111*($y*$y*$y) - 0.00071*($y*$y*$y*$y);
		$JS_JDE = 1721233.25401 + 365241.72562*$y - 0.05323*$y*$y + 0.00907*$y*$y*$y + 0.00025*$y*$y*$y*$y;
		$SE_JDE = 1721325.70455 + 365242.49558*$y - 0.11677*$y*$y - 0.00297*$y*$y*$y + 0.00074*$y*$y*$y*$y;
		$DS_JDE = 1721414.39987 + 365242.88257*$y - 0.00769*$y*$y - 0.00933*$y*$y*$y - 0.00006*$y*$y*$y*$y;
	}

	if($y>1000){
			$y = ($y-2000)/1000;
			$ME_JDE = 2451623.80984 + 365242.37404*$y + 0.05169*pow($y,2) - 0.00411*pow($y,3) - 0.00057*pow($y,4);
			$JS_JDE = 2451716.56767 + 365241.62603*$y + 0.00325*($y*$y) + 0.00888*($y*$y*$y) - 0.00030*($y*$y*$y*$y);
			$SE_JDE = 2451810.21715 + 365242.01767*$y - 0.11575*($y*$y) + 0.00337*($y*$y*$y) + 0.00078*($y*$y*$y*$y);
			$DS_JDE = 2451900.05952 + 365242.74049*$y - 0.06223*($y*$y) - 0.00823*($y*$y*$y) + 0.00032*($y*$y*$y*$y);
	}

	$ME_T = ($ME_JDE - 2451545)/36525;
	$ME_W = 35999.373*$ME_T - 2.47;
	$ME_delta = 1 + 0.0334*(cos($ME_W*pi()/180)) + 0.007*(cos(2*$ME_W*pi()/180));

	$JS_T = ($JS_JDE - 2451545)/36525;
	$JS_W = 35999.373*$JS_T - 2.47;
	$JS_delta = 1 + 0.0334*(cos($JS_W*pi()/180)) + 0.007*(cos(2*$JS_W*pi()/180));

	$SE_T = ($SE_JDE - 2451545)/36525;
	$SE_W = 35999.373*$SE_T - 2.47;
	$SE_delta = 1 + 0.0334*(cos($SE_W*pi()/180)) + 0.007*(cos(2*$SE_W*pi()/180));

	$DS_T = ($DS_JDE - 2451545)/36525;
	$DS_W = 35999.373*$ME_T - 2.47;
	$DS_delta = 1 + 0.0334*(cos($DS_W*pi()/180)) + 0.007*(cos(2*$DS_W*pi()/180));

	$array_A = array(485,203,199,182,156,136,77,74,70,58,52,50,45,44,29,18,17,16,14,12,12,12,9,8);
	$array_B = array(324.96,337.23,342.08,27.85,73.14,171.52,222.54,296.72,243.58,119.81,297.17,21.02,247.54,325.15,60.93,155.12,288.79,198.04,199.76,95.39,287.11,320.81,227.73,15.45);
	$array_C = array(1934.136,32964.467,20.186,445267.112,45036.886,22518.443,65928.934,3034.906,9037.513,33718.147,150.678,2281.226,29929.562,31555.956,4443.417,67555.328,4562.452,62894.029,31436.921,14577.848,31931.756,34777.259,1222.114,16859.074);

	for($i=0;$i<24;$i++){
			$ME_S = $ME_S + ($array_A[$i]*cos(($array_B[$i]+($array_C[$i])*$ME_T)*pi()/180));
			$SE_S = $SE_S + ($array_A[$i]*cos(($array_B[$i]+($array_C[$i])*$SE_T)*pi()/180));
			$JS_S = $JS_S + ($array_A[$i]*cos(($array_B[$i]+($array_C[$i])*$JS_T)*pi()/180));
			$DS_S = $DS_S + ($array_A[$i]*cos(($array_B[$i]+($array_C[$i])*$DS_T)*pi()/180));
	}

	$ME = $ME_JDE + ((0.00001*$ME_S)/$ME_delta);
	$ME = conversion($ME);
	$ME_UTC = date_create($ME,timezone_open("UTC"));
	$ME_local = $ME_UTC;
	$ME_local = $ME_local->setTimezone($myTZ);
	$ME_UTC = date_create($ME,timezone_open("UTC"));

	$JS = $JS_JDE + ((0.00001*$JS_S)/$JS_delta);
	$JS = conversion($JS);
	$JS_UTC = date_create($JS,timezone_open("UTC"));
	$JS_local = $JS_UTC;
	$JS_local = $JS_local->setTimezone($myTZ);
	$JS_UTC = date_create($JS,timezone_open("UTC"));

	$SE = $SE_JDE + ((0.00001*$SE_S)/$SE_delta);
	$SE = conversion($SE);
	$SE_UTC = date_create($SE,timezone_open("UTC"));
	$SE_local = $SE_UTC;
	$SE_local = $SE_local->setTimezone($myTZ);
	$SE_UTC = date_create($SE,timezone_open("UTC"));

	$DS = $DS_JDE + ((0.00001*$DS_S)/$DS_delta);
	$DS = conversion($DS);
	$DS_UTC = date_create($DS,timezone_open("UTC"));
	$DS_local = $DS_UTC;
	$DS_local = $DS_local->setTimezone($myTZ);
	$DS_UTC = date_create($DS,timezone_open("UTC"));

	function conversion($JD) { 
		$JD = $JD + 0.5;
		$Z = floor($JD);
		$F = $JD - $Z;
		
		if($Z < 2299161){
			$A = $Z;
		}
		else{
			$alpha = floor(($Z-1867216.25)/36524.25);
			$A = $Z + 1 + $alpha - floor($alpha/4);
		}
		$B = $A + 1524;
		$C = floor(($B-122.1)/365.25);
		$D = floor(365.25*$C);
		$E = floor(($B-$D)/30.6001);
		
		$day = $B - $D - floor(30.6001*$E) + $F;
		if($E<14){
			$month = $E-1;
		}
		else{
			$month = $E-13;
		}
		if($month>2){
			$year = $C - 4716;
		}
		else{
			$year = $C-4715;
		}
		$daypart = $day-floor($day);
		$daypart_min = $daypart*1440;
		$hour = floor($daypart_min/60);
		$minute = round($daypart_min-($hour*60));
		if($minute == 60){
			$minute = 59;
		}
		
		$date = $year."-".$month."-".floor($day)." ".$hour.":".$minute; 
		return $date;
	} 
	
	$final['spring'] = date_format($ME_UTC, $dateTimeFormat)." (UTC)<br>".date_format($ME_local, $dateTimeFormat)." (".lang('local time','l').")";
	$final['summer'] = date_format($JS_UTC, $dateTimeFormat)." (UTC)<br>".date_format($JS_local, $dateTimeFormat)." (".lang('local time','l').")";
	$final['winter'] = date_format($DS_UTC, $dateTimeFormat)." (UTC)<br>".date_format($DS_local, $dateTimeFormat)." (".lang('local time','l').")";
	$final['autumn'] = date_format($SE_UTC, $dateTimeFormat)." (UTC)<br>".date_format($SE_local, $dateTimeFormat)." (".lang('local time','l').")";
	
	print json_encode($final, JSON_NUMERIC_CHECK);
?>