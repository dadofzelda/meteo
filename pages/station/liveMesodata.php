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
	#   liveMesodata.php script by Ken True - Saratoga-weather.org based on
	#     the liveAjax.php script by Jachym and used to provide the Global
	#     Affiliated Weather Networks with a 'stickertags' type data for
	#     conditions from a Meteotemplate site.
	#     n.b.: missing a means to produce text for Barometer Trend currently
	#
	############################################################################
	#
	#	Gauges Data Update
	#
	# 	A script that updates the gauges.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."header.php");
	
	
	$resultSQL = mysqli_query($con,"
		SELECT *
		FROM alldata 
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($resultSQL)){
		list($date,$time) = explode(' ',$row['DateTime']);
		$temperature = convertT($row['T']);
		$humidity = round($row['H'],0);
		$wind = convertW($row['W']);
		$gust = convertW($row['G']);
		$dew = convertT($row['D']);
		$apparent = convertT($row['A']);
		$solar = $row['S'];
		$pressure = convertP($row['P']);
		$rain = convertR($row['R']);
		$direction = $row['B'];
	}
	$temperature = round($temperature,1);
	$wind = round($wind,1);
	$gust = round($gust,1);
	$dew = round($dew,1);
	$apparent = round($apparent,1);
	$pressure = round($pressure,2);
	$rain = round($rain,2);
	$direction = deg2dir($direction);
	list($sunrise,$sunset) = getSuntimes("$date $time",$stationLat,$stationLon);
	$sunrise = date('H:i',$sunrise);
	$sunset  = date('H:i',$sunset);
	$heatidx = calcHeatIndex($temperature,$humidity,$displayTempUnits);
	$windch  = calcWindChill($temperature,$wind,$displayTempUnits,$displayWindUnits);

	// determine barotrend
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
	
	$pressureDifference = $pressure - $pressure3h;
	
	if($displayPressUnits=='hpa'){
		if(abs($pressureDifference)<=0.6){
			$barTrend = "Steady";
		}
		else if($pressureDifference>0.6 && $pressureDifference<2){
			$barTrend = "Rising Slowly";
		}
		else if($pressureDifference >= 2){
			$barTrend = "Rising Rapidly";
		}
		else if($pressureDifference<-0.6 && $pressureDifference>-2){
			$barTrend = "Falling Slowly";
		}
		else if($pressureDifference <= -2){
			$barTrend = "Falling Rapidly";
		}
		else{
		}
	}
	if($displayPressUnits=='inhg'){
		if(abs($pressureDifference)<=0.02){
			$barTrend = "Steady";
		}
		else if($pressureDifference>0.02 && $pressureDifference<0.06){
			$barTrend = "Rising Slowly";
		}
		else if($pressureDifference >= 0.06){
			$barTrend = "Rising Rapidly";
		}
		else if($pressureDifference<-0.02 && $pressureDifference>-0.06){
			$barTrend = "Falling Slowly";
		}
		else if($pressureDifference <= -0.06){
			$barTrend = "Falling Rapidly";
		}
		else{
		}
	}
	if($displayPressUnits=='mmhg'){
		if(abs($pressureDifference)<=0.5){
			$barTrend = "Steady";
		}
		else if($pressureDifference>0.5 && $pressureDifference<1.5){
			$barTrend = "Rising Slowly";
		}
		else if($pressureDifference >= 1.5){
			$barTrend = "Rising Rapidly";
		}
		else if($pressureDifference<-0.5 && $pressureDifference>-1.5){
			$barTrend = "Falling Slowly";
		}
		else if($pressureDifference <= -1.5){
			$barTrend = "Falling Rapidly";
		}
		else{
		}
	}
	
	
	
/*  desired output contents are:
<!--stationTime-->,
<!--stationDate-->,
<!--outsideTemp-->,
<!--outsideHeatIndex-->,
<!--windChill-->,
<!--outsideHumidity-->,
<!--outsideDewPt-->,
<!--barometer-->,
<!--BarTrend-->,
<!--wind10Avg-->,
<!--windDirection-->,
<!--dailyRain-->,
,
<!--sunriseTime-->,
<!--sunsetTime-->,
<!--windAvg10-->,
<!--windHigh10-->,
<!--tempUnit-->|<!--windUnit-->|<!--barUnit-->|<!--rainUnit-->
*/

	header("Content-type: text/plain,charset=ISO-8859-1");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s",strtotime("$date $time")) . " GMT");
	$expires = 60*3;
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires);
	header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$expires) . ' GMT');

	print "$time,$date,$temperature,$heatidx,$windch,$humidity,$dew,$pressure,$barTrend,";
	print "$wind,$direction,$rain,,$sunrise,$sunset,$wind,$gust,";
	print "$displayTempUnits|".cUnits($displayWindUnits)."|".
	      cUnits($displayPressUnits)."|$displayRainUnits\n";
	
# functions -------------------------------------------------------------

function getSuntimes($stationtime,$stationlatitude,$stationlongitude) {
	
	$tstamp = strtotime($stationtime);
	if(function_exists('date_sun_info')) {
	  $info = date_sun_info($tstamp,$stationlatitude,$stationlongitude);
	  $t = $info['sunrise'] . ' ' . $info['sunset'];
	} else {
	  $t = 'n/a n/a';
	}
	
	return(explode(' ',$t));
	
} // end getSuntimes
	
function deg2dir ($degrees) {
// figure out a text value for compass direction
// Given the wind direction, return the text label
// for that value.  16 point compass
   $winddir = $degrees;
   if ($winddir == "--") { return($winddir); }

  if (!isset($winddir)) {
    return "---";
  }
  if (!is_numeric($winddir)) {
	return($winddir);
  }
  $windlabel = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
	 "SSW","SW", "WSW", "W", "WNW", "NW", "NNW");
  $dir = $windlabel[ fmod((($winddir + 11) / 22.5),16) ];
  return($dir);

} // end function deg2dir

function calcHeatIndex ($temp,$humidity,$useunit) {
// Calculate Heat Index from temperature and humidity
// Source of calculation: http://woody.cowpi.com/phpscripts/getwx.php.txt	
  if(preg_match('|C|i',$useunit)) {
    $tempF = round(1.8 * $temp + 32,1);
  } else {
	$tempF = round($temp,1);
  }
  $rh = $humidity;
  
  
  // Calculate Heat Index based on temperature in F and relative humidity (65 = 65%)
  if ($tempF > 79 && $rh > 39) {
	  $hiF = -42.379 + 2.04901523 * $tempF + 10.14333127 * $rh - 0.22475541 * $tempF * $rh;
	  $hiF += -0.00683783 * pow($tempF, 2) - 0.05481717 * pow($rh, 2);
	  $hiF += 0.00122874 * pow($tempF, 2) * $rh + 0.00085282 * $tempF * pow($rh, 2);
	  $hiF += -0.00000199 * pow($tempF, 2) * pow($rh, 2);
	  $hiF = round($hiF,1);
	  $hiC = round(($hiF - 32) / 1.8,1);
  } else {
	  $hiF = $tempF;
	  $hiC = round(($hiF - 32) / 1.8,1);
  }
  if(preg_match('|F|i',$useunit)) {
     $heatIndex = $hiF;	  
  } else {
	 $heatIndex = $hiC;
  }
  return($heatIndex);	
}

function calcWindChill($temp,$wind,$tempUOM,$windUOM) {
  // Calculate Wind Chill Temperature based on temperature in F and
  // wind speed in miles per hour
  // Source of calculation: http://woody.cowpi.com/phpscripts/getwx.php.txt	
  if(preg_match('|C|i',$tempUOM)) {
    $tempF = round(1.8 * $temp + 32,1);
  } else {
	$tempF = round($temp,1);
  }
  // ms, kmh, kt or mph 
  if(preg_match('|mph|i',$windUOM)) {
	 $windspeed = round($wind,0);
  } elseif (preg_match('|km|i',$windUOM)) {
	 $windspeed = round(0.621371 * $wind,0);
   } elseif (preg_match('|kt|i',$windUOM)) {
	 $windspeed = round(1.15078 * $wind,0);
  } elseif (preg_match('|ms|i',$windUOM)) {
	 $windspeed = round(2.23694 * $wind,0);
  } else {
	 $windspeed = 0;
  }
  
  
  if ($tempF < 51 && $windspeed > 3) {
		  $chillF = 35.74 + 0.6215 * $tempF - 35.75 * pow($windspeed, 0.16) + 0.4275 * $tempF * pow($windspeed, 0.16);
		  $chillF = round($chillF);
		  $chillC = round(($chillF - 32) / 1.8);
		  $wxInfo['WIND CHILL'] = "$chillF&deg;F ($chillC&deg;C)";
  } else {
	  return($temp);
  }

  if(preg_match('|C|i',$tempUOM)) {
	  $chill = $chillC;
  } else {
	  $chill = $chillF;
  }
  return($chill);
	
}

function cUnits ($in) {
	$U = array(
	'kmh' => 'km/h',
	'ms'  => 'm/s',
	'kt'  => 'kts',
	'hpa' => 'hPa',
	'inhg' => 'inHg',
	);
	
	if(isset($U[$in])) { 
	  return($U[$in]); 
	} else {
	  return($in);
	}
}

?>