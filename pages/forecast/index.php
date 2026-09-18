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
	#	Outlook
	#
	# 	Weather outlook for the upcoming days - forecast, warnings, averages, 
	#	extremes etc.
	#
	############################################################################
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	if($fIOKey==""){
		die("No forecast IO key specified, please get your free API key at darksky.net and insert it in the Main settings.");
	}
	
	if(!is_dir("cache")){
		mkdir("cache");
	}
	
	//error_reporting(E_ALL);
	
	if(isset($_GET['reloadCache'])){
		if(file_exists("cache/stationCache.txt")){
			unlink("cache/stationCache.txt");
		}
	}
	
	$weekDays = array(lang('sundayAbbr','c'),lang('mondayAbbr','c'),lang('tuesdayAbbr','c'),lang('wednesdayAbbr','c'),lang('thursdayAbbr','c'),lang('fridayAbbr','c'),lang('saturdayAbbr','c'));
	$monthNames = array(0,lang('janAbbr','c'), lang('febAbbr','c'), lang('marAbbr','c'), lang('aprAbbr','c'), lang('mayAbbr','c'), lang('junAbbr','c'), lang('julAbbr','c'), lang('augAbbr','c'), lang('sepAbbr','c'),  lang('octAbbr','c'), lang('novAbbr','c'), lang('decAbbr','c'));
	
	$dateTimeZone = new DateTimeZone($stationTZ);
	$dateTime = new DateTime("now", $dateTimeZone);
	$offset = $dateTimeZone->getOffset($dateTime);
	
	// build URL
	$fIOURL = "https://api.darksky.net/forecast/".$fIOKey."/".$stationLat.",".$stationLon."?units=si&lang=".$fIOLanguage;
	
	if(file_exists("cache/current.txt")){ 
		if (time()-filemtime("cache/current.txt") > 60 * 15) { // cache every 15 mins
			unlink("cache/current.txt");
		}
	}
	if(file_exists("cache/current.txt")){
		$rawData = file_get_contents("cache/current.txt");
		$forecastLoadedTime = filemtime("cache/current.txt");
	}
	else{
		// get contents
		$rawData = file_get_contents($fIOURL);
		if($rawData!=""){
			file_put_contents("cache/current.txt",$rawData);
		}
		$forecastLoadedTime = time();
	}
	
	$dataString = json_decode($rawData,true);
	
	// SET GENERAL PARAMETERS
	// position
	$lat = round($stationLat,3);
	$lon = round($stationLon,3);
	if($lat>=0){
		$coord1 = $lat." ".lang('coordN','u');
	}
	if($lat<0){
		$coord1 = ($lat*-1)." ".lang('coordS','u');
	}
	if($lon>=0){
		$coord2 = $lon." ".lang('coordE','u');
	}
	if($lon<0){
		$coord2 = ($lon*-1)." ".lang('coordW','u');
	}
	
	$iconsAvailable = array("clear-day","clear-night","rain","snow","sleet","wind","fog","cloudy","partly-cloudy-day","partly-cloudy-night","thunderstorm");
	
	// CURRENT CONDITIONS
	$current = array();
	$currentAvailable = false;
	
	if(array_key_exists("currently",$dataString)){
		if(array_key_exists("time",$dataString['currently'])){
			$current['time'] = $dataString['currently']['time'];
		}
		if(array_key_exists("summary",$dataString['currently'])){
			$current['summary'] = $dataString['currently']['summary'];
		}
		if(array_key_exists("icon",$dataString['currently'])){
			$current['icon'] = $dataString['currently']['icon'];
		}
		if(array_key_exists("precipIntensity",$dataString['currently'])){
			$current['precipIntensity'] = $dataString['currently']['precipIntensity'];
		}
		if(array_key_exists("precipProbability",$dataString['currently'])){
			$current['precipProbability'] = $dataString['currently']['precipProbability'];
		}
		if(array_key_exists("temperature",$dataString['currently'])){
			$current['temperature'] = $dataString['currently']['temperature'];
		}
		if(array_key_exists("apparentTemperature",$dataString['currently'])){
			$current['apparentTemperature'] = $dataString['currently']['apparentTemperature'];
		}
		if(array_key_exists("dewPoint",$dataString['currently'])){
			$current['dewPoint'] = $dataString['currently']['dewPoint'];
		}
		if(array_key_exists("humidity",$dataString['currently'])){
			$current['humidity'] = $dataString['currently']['humidity']*100;
		}
		if(array_key_exists("windSpeed",$dataString['currently'])){
			$current['windSpeed'] = $dataString['currently']['windSpeed'];
		}
		if(array_key_exists("windBearing",$dataString['currently'])){
			$current['windBearing'] = $dataString['currently']['windBearing'];
		}
		if(array_key_exists("visibility",$dataString['currently'])){
			$current['visibility'] = $dataString['currently']['visibility'];
			if($displayVisibilityUnits=="mi"){
				$current['visibility'] = $current['visibility']*0.621371;
			}
			if($displayVisibilityUnits=="m"){
				$current['visibility'] = $current['visibility']*1000;
			}
		}
		if(array_key_exists("cloudCover",$dataString['currently'])){
			$current['cloudCover'] = $dataString['currently']['cloudCover'];
		}
		if(array_key_exists("pressure",$dataString['currently'])){
			$current['pressure'] = $dataString['currently']['pressure'];
		}
		if(array_key_exists("ozone",$dataString['currently'])){
			$current['ozone'] = $dataString['currently']['ozone'];
		}
		if(array_key_exists("nearestStormDistance",$dataString['currently'])){
			$current['nearestStormDistance'] = $dataString['currently']['nearestStormDistance'];
		}
		if(array_key_exists("precipType",$dataString['currently'])){
			$current['precipType'] = $dataString['currently']['precipType'];
		}
		
		// do conversions
		if($displayTempUnits=="F"){
			if(array_key_exists("temperature",$current)){
				$current['temperature'] = $current['temperature']*1.8 + 32;
			}
			if(array_key_exists("dewPoint",$current)){
				$current['dewPoint'] = $current['dewPoint']*1.8 + 32;
			}
			if(array_key_exists("apparentTemperature",$current)){
				$current['apparentTemperature'] = $current['apparentTemperature']*1.8 + 32;
			}
		}
		if($displayPressUnits=="inhg"){
			if(array_key_exists("pressure",$current)){
				$current['pressure'] = $current['pressure'] * 0.02953;
			}
		}
		if($displayPressUnits=="mmhg"){
			if(array_key_exists("pressure",$current)){
				$current['pressure'] = $current['pressure'] * 0.75006;
			}
		}
		if($displayWindUnits=="kmh"){
			if(array_key_exists("windSpeed",$current)){
				$current['windSpeed'] = $current['windSpeed'] * 3.6;
			}
		}
		if($displayWindUnits=="mph"){
			if(array_key_exists("windSpeed",$current)){
				$current['windSpeed'] = $current['windSpeed'] * 2.23694;
			}
		}
		if($displayWindUnits=="kt"){
			if(array_key_exists("windSpeed",$current)){
				$current['windSpeed'] = $current['windSpeed'] * 1.94384;
			}
		}
		if($displayRainUnits=="in"){
			if(array_key_exists("precipIntensity",$current)){
				$current['precipIntensity'] = $current['precipIntensity'] / 25.4;
			}
		}
	}
	if(count($current)>0){
		$currentAvailable = true;
	}
	
	// DAY HOURLY FORECAST
	$hourly = array();
	$hourlyAvailable = false;
	
	if(array_key_exists("hourly",$dataString)){
		if(array_key_exists("summary",$dataString['hourly'])){
			$hourly['summary'] = $dataString['hourly']['summary'];
		}
		if(array_key_exists("icon",$dataString['hourly'])){
			$hourly['icon'] = $dataString['hourly']['icon'];
			if(!in_array($hourly['icon'],$iconsAvailable)){
				$hourly['icon'] = "na";
			}
		}
		foreach($dataString['hourly']['data'] as $hour){
			$temporary = array();
			if(array_key_exists("time",$hour)){
				$temporary['time'] = $hour['time'];
			}
			if(array_key_exists("summary",$hour)){
				$temporary['summary'] = $hour['summary'];
			}
			if(array_key_exists("icon",$hour)){
				$temporary['icon'] = $hour['icon'];
				if(!in_array($hour['icon'],$iconsAvailable)){
					$temporary['icon'] = "na";
				}
			}
			if(array_key_exists("precipIntensity",$hour)){
				$temporary['precipIntensity'] = $hour['precipIntensity'];
				if($displayRainUnits=="in"){
					$temporary['precipIntensity'] = $temporary['precipIntensity'] / 25.4;
				}
			}
			if(array_key_exists("precipProbability",$hour)){
				$temporary['precipProbability'] = $hour['precipProbability'];
			}
			if(array_key_exists("precipType",$hour)){
				$temporary['precipType'] = $hour['precipType'];
			}
			if(array_key_exists("temperature",$hour)){
				$temporary['temperature'] = $hour['temperature'];
				if($displayTempUnits=="F"){
					$temporary['temperature'] = $temporary['temperature']*1.8 + 32;
				}
			}
			if(array_key_exists("apparentTemperature",$hour)){
				$temporary['apparentTemperature'] = $hour['apparentTemperature'];
				if($displayTempUnits=="F"){
					$temporary['apparentTemperature'] = $temporary['apparentTemperature']*1.8 + 32;
				}
			}
			if(array_key_exists("dewPoint",$hour)){
				$temporary['dewPoint'] = $hour['dewPoint'];
				if($displayTempUnits=="F"){
					$temporary['dewPoint'] = $temporary['dewPoint']*1.8 + 32;
				}
			}
			if(array_key_exists("humidity",$hour)){
				$temporary['humidity'] = $hour['humidity']*100;
			}
			if(array_key_exists("windSpeed",$hour)){
				$temporary['windSpeed'] = $hour['windSpeed'];
				if($displayWindUnits=="kmh"){
					$temporary['windSpeed'] = $temporary['windSpeed']*3.6;
				}
				if($displayWindUnits=="mph"){
					$temporary['windSpeed'] = $temporary['windSpeed']*2.23694;
				}
				if($displayWindUnits=="kt"){
					$temporary['windSpeed'] = $temporary['windSpeed']*1.94384;
				}
			}
			if(array_key_exists("windBearing",$hour)){
				$temporary['windBearing'] = $hour['windBearing'];
			}
			if(array_key_exists("visibility",$hour)){
				$temporary['visibility'] = $hour['visibility'];
				if($displayVisibilityUnits=="mi"){
					$temporary['visibility'] = $temporary['visibility']*0.621371;
				}
				if($displayVisibilityUnits=="m"){
					$temporary['visibility'] = $temporary['visibility']*1000;
				}
			}
			if(array_key_exists("cloudCover",$hour)){
				$temporary['cloudCover'] = $hour['cloudCover'];
			}
			if(array_key_exists("pressure",$hour)){
				$temporary['pressure'] = $hour['pressure'];
				if($displayPressUnits=="inhg"){
					$temporary['pressure'] = $temporary['pressure']*0.02953;
				}
				if($displayPressUnits=="mmhg"){
					$temporary['pressure'] = $temporary['pressure']*0.75006;
				}
			}
			if(array_key_exists("ozone",$hour)){
				$temporary['ozone'] = $hour['ozone'];
			}
			if(date("d",$temporary['time']) == date("d")){
				$hourly['data']['today'][] = $temporary;
			}
			else if(date("d",$temporary['time']) == date("d",strtotime('tomorrow'))){
				$hourly['data']['tomorrow'][] = $temporary;
			}
			else if(date("d",$temporary['time']) == date("d",strtotime('+2 days'))){
				$hourly['data']['dayAfterTomorrow'][] = $temporary;
			}
			else{}
		}
	}
	if(count($hourly)>0){
		$hourlyAvailable = true;
	}
	
	// DAILY FORECAST
	$daily = array();
	$dailyAvailable = false;
	
	if(array_key_exists("daily",$dataString)){
		if(array_key_exists("summary",$dataString['daily'])){
			$daily['summmary'] = $dataString['daily']['summary'];
		}
		foreach($dataString['daily']['data'] as $day){
			$temporary = array();
			if(array_key_exists("time",$day)){
				$temporary['time'] = $day['time'];
			}
			if(array_key_exists("summary",$day)){
				$temporary['summary'] = $day['summary'];
			}
			if(array_key_exists("icon",$day)){
				$temporary['icon'] = $day['icon'];
				if(!in_array($day['icon'],$iconsAvailable)){
					$temporary['icon'] = "na";
				}
			}
			if(array_key_exists("sunriseTime",$day)){
				$temporary['sunriseTime'] = $day['sunriseTime'];
			}
			if(array_key_exists("sunsetTime",$day)){
				$temporary['sunsetTime'] = $day['sunsetTime'];
			}
			if(array_key_exists("moonPhase",$day)){
				$temporary['moonPhase'] = $day['moonPhase'];
			}
			if(array_key_exists("precipIntensity",$day)){
				$temporary['precipIntensity'] = $day['precipIntensity'];
				if($displayRainUnits=="in"){
					$temporary['precipIntensity'] = $temporary['precipIntensity'] / 25.4;
				}
			}
			if(array_key_exists("precipIntensityMax",$day)){
				$temporary['precipIntensityMax'] = $day['precipIntensityMax'];
				if($displayRainUnits=="in"){
					$temporary['precipIntensityMax'] = $temporary['precipIntensityMax'] / 25.4;
				}
			}
			if(array_key_exists("precipIntensityMaxTime",$day)){
				$temporary['precipIntensityMaxTime'] = $day['precipIntensityMaxTime'];
			}
			if(array_key_exists("precipProbability",$day)){
				$temporary['precipProbability'] = $day['precipProbability'];
			}
			if(array_key_exists("precipType",$day)){
				$temporary['precipType'] = $day['precipType'];
			}
			if(array_key_exists("temperatureMin",$day)){
				$temporary['temperatureMin'] = $day['temperatureMin'];
				if($displayTempUnits=="F"){
					$temporary['temperatureMin'] = $temporary['temperatureMin']*1.8 + 32;
				}
			}
			if(array_key_exists("temperatureMinTime",$day)){
				$temporary['temperatureMinTime'] = $day['temperatureMinTime'];
			}
			if(array_key_exists("temperatureMax",$day)){
				$temporary['temperatureMax'] = $day['temperatureMax'];
				if($displayTempUnits=="F"){
					$temporary['temperatureMax'] = $temporary['temperatureMax']*1.8 + 32;
				}
			}
			if(array_key_exists("temperatureMaxTime",$day)){
				$temporary['temperatureMaxTime'] = $day['temperatureMaxTime'];
			}
			if(array_key_exists("apparentTemperatureMin",$day)){
				$temporary['apparentTemperatureMin'] = $day['apparentTemperatureMin'];
				if($displayTempUnits=="F"){
					$temporary['apparentTemperatureMin'] = $temporary['apparentTemperatureMin']*1.8 + 32;
				}
			}
			if(array_key_exists("apparentTemperatureMinTime",$day)){
				$temporary['apparentTemperatureMinTime'] = $day['apparentTemperatureMinTime'];
			}
			if(array_key_exists("apparentTemperatureMax",$day)){
				$temporary['apparentTemperatureMax'] = $day['apparentTemperatureMax'];
				if($displayTempUnits=="F"){
					$temporary['apparentTemperatureMax'] = $temporary['apparentTemperatureMax']*1.8 + 32;
				}
			}
			if(array_key_exists("apparentTemperatureMaxTime",$day)){
				$temporary['apparentTemperatureMaxTime'] = $day['apparentTemperatureMaxTime'];
			}
			if(array_key_exists("dewPoint",$day)){
				$temporary['dewPoint'] = $day['dewPoint'];
				if($displayTempUnits=="F"){
					$temporary['dewPoint'] = $temporary['dewPoint']*1.8 + 32;
				}
			}
			if(array_key_exists("humidity",$day)){
				$temporary['humidity'] = $day['humidity']*100;
			}
			if(array_key_exists("windSpeed",$day)){
				$temporary['windSpeed'] = $day['windSpeed'];
				if($displayWindUnits=="kmh"){
					$temporary['windSpeed'] = $temporary['windSpeed']*3.6;
				}
				if($displayWindUnits=="mph"){
					$temporary['windSpeed'] = $temporary['windSpeed']*2.23694;
				}
				if($displayWindUnits=="kt"){
					$temporary['windSpeed'] = $temporary['windSpeed']*1.94384;
				}
			}
			if(array_key_exists("windBearing",$day)){
				$temporary['windBearing'] = $day['windBearing'];
			}
			if(array_key_exists("visibility",$day)){
				$temporary['visibility'] = $day['visibility'];
				if($displayVisibilityUnits=="mi"){
					$temporary['visibility'] = $temporary['visibility']*0.621371;
				}
				if($displayVisibilityUnits=="m"){
					$temporary['visibility'] = $temporary['visibility']*1000;
				}
			}
			if(array_key_exists("cloudCover",$day)){
				$temporary['cloudCover'] = $day['cloudCover'];
			}
			if(array_key_exists("pressure",$day)){
				$temporary['pressure'] = $day['pressure'];
				if($displayPressUnits=="inhg"){
					$temporary['pressure'] = $temporary['pressure']*0.02953;
				}
				if($displayPressUnits=="mmhg"){
					$temporary['pressure'] = $temporary['pressure']*0.75006;
				}
			}
			if(array_key_exists("ozone",$day)){
				$temporary['ozone'] = $day['ozone'];
			}
			
			$daily['data'][] = $temporary;
		}
	}
	if(count($daily)>0){
		$dailyAvailable = true;
	}
	
	// ALERTS
	$alerts = array();
	$alertsActive = false;
	if(array_key_exists("alerts",$dataString)){
		foreach($dataString['alerts'] as $alert){
			$temporary = array();
			$temporary['title'] = $alert['title'];
			$temporary['time'] = $alert['time'];
			$temporary['expires'] = $alert['expires'];
			$temporary['description'] = $alert['description'];
			$alerts[] = $temporary;
		}
	}
	if(count($alerts)>0){
		$alertsActive = true;
	}
	
	$sources = "";
	$metarStations = "";
	if(array_key_exists("flags",$dataString)){
		$sources = implode(", ",($dataString['flags']['sources']));
		$metarStations = implode(", ",($dataString['flags']['madis-stations']));
	}
	
	$result = mysqli_query($con,"
		SELECT DateTime,T,H,P,W,A,D,RR,B
		FROM alldata 
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$station['T'] = number_format(convertT($row['T']),1,".","");
		$station['H'] = number_format($row['H'],1,".","");
		$station['P'] = number_format(convertP($row['P']),$decimalsP,".","");
		$station['W'] = number_format(convertW($row['W']),1,".","");
		$station['R'] = number_format(convertR($row['RR']),$decimalsR,".","");
		$station['D'] = number_format(convertT($row['D']),1,".","");
		$station['A'] = number_format(convertT($row['A']),1,".","");
		$station['B'] = $row['B'];
	}
	
	$result = mysqli_query($con,"
		SELECT DISTINCT YEAR(DateTime)
		FROM alldata 
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$yearsArr[] = $row['YEAR(DateTime)'];
	}
	
	// station averages
	$weekDatesArray = array(strtotime('yesterday'),strtotime('today'),strtotime('tomorrow'),strtotime('+2 days'),strtotime('+3 days'),strtotime('+4 days'),strtotime('+5 days'),strtotime('+6 days'),strtotime('+7 days'));
	
	if(file_exists("cache/stationCache.txt")){
		if (time()-filemtime("cache/stationCache.txt") > 60 * 720) {
			unlink("cache/stationCache.txt");
		}
	}

	if(file_exists("cache/stationCache.txt")){
		$stationData = json_decode(file_get_contents("cache/stationCache.txt"),true);
		$stationDataLoadedTime = filemtime("cache/stationCache.txt");
	}
	else { 
		$stationDataLoadedTime = time();
		foreach($weekDatesArray as $stationDay){
			$temporaryTs = array();
			$temporaryAs = array();
			$temporaryDs = array();
			$temporaryRs = array();
			
			$maximumT = array();
			$minimumT = array();
			$maximumA = array();
			$minimumA = array();
			$maximumD = array();
			$minimumD = array();
			$maximumH = array();
			$minimumH = array();
			$maximumP = array();
			$minimumP = array();
			$maximumW = array();
			$maximumG = array();
			$maximumR = array();
				
			$result = mysqli_query($con,"
				SELECT DateTime, avg(H), avg(P), avg(W)
				FROM alldata 
				WHERE MONTH(DateTime)=".date('m',$stationDay)." AND DAY(DateTime)=".date('d',$stationDay)
			);
			while($row = mysqli_fetch_array($result)){
				$stationData[$stationDay]['H'] = number_format($row['avg(H)'],1,".","");
				$stationData[$stationDay]['P'] = number_format(($row['avg(P)']),$decimalsP,".","");
				$stationData[$stationDay]['W'] = number_format(($row['avg(W)']),1,".","");
			}
			
			$result = mysqli_query($con,"
				SELECT DateTime, avg(T), avg(A), avg(D)
				FROM alldata 
				WHERE MONTH(DateTime)=".date('m',$stationDay)." AND DAY(DateTime)=".date('d',$stationDay)."
				GROUP BY HOUR(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$temporaryTs[] = ($row['avg(T)']);
				$temporaryAs[] = ($row['avg(A)']);
				$temporaryDs[] = ($row['avg(D)']);
			}
			$stationData[$stationDay]['maxT'] = number_format(max($temporaryTs),1,".","");
			$stationData[$stationDay]['minT'] = number_format(min($temporaryTs),1,".","");
			$stationData[$stationDay]['maxA'] = number_format(max($temporaryAs),1,".","");
			$stationData[$stationDay]['minA'] = number_format(min($temporaryAs),1,".","");
			$stationData[$stationDay]['maxD'] = number_format(max($temporaryDs),1,".","");
			$stationData[$stationDay]['minD'] = number_format(min($temporaryDs),1,".","");
			$result = mysqli_query($con,"
				SELECT DateTime, max(R)
				FROM alldata 
				WHERE MONTH(DateTime)=".date('m',$stationDay)." AND DAY(DateTime)=".date('d',$stationDay)."
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$temporaryRs[] = ($row['max(R)']);
			}
			$stationData[$stationDay]['R'] = number_format(array_sum($temporaryRs)/count($temporaryRs),$decimalsR,".","");
			
			// EXTREMES
			$maximumTemperatures = array();
			$result = mysqli_query($con,"
				SELECT YEAR(DateTime), max(Tmax), min(Tmin), max(H), min(H), max(P), min(P), max(W), max(G), max(R), min(D), max(D), max(A), min(A)
				FROM alldata 
				WHERE MONTH(DateTime)=".date('m',$stationDay)." AND DAY(DateTime)=".date('d',$stationDay)."
				GROUP BY YEAR(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$maximumT[$row['YEAR(DateTime)']] = $row['max(Tmax)'];
				$minimumT[$row['YEAR(DateTime)']] = $row['min(Tmin)'];
				$maximumA[$row['YEAR(DateTime)']] = $row['max(A)'];
				$minimumA[$row['YEAR(DateTime)']] = $row['min(A)'];
				$maximumD[$row['YEAR(DateTime)']] = $row['max(D)'];
				$minimumD[$row['YEAR(DateTime)']] = $row['min(D)'];
				$maximumH[$row['YEAR(DateTime)']] = $row['max(H)'];
				$minimumH[$row['YEAR(DateTime)']] = $row['min(H)'];
				$maximumP[$row['YEAR(DateTime)']] = $row['max(P)'];
				$minimumP[$row['YEAR(DateTime)']] = $row['min(P)'];
				$maximumW[$row['YEAR(DateTime)']] = $row['max(W)'];
				$maximumG[$row['YEAR(DateTime)']] = $row['max(G)'];
				$maximumR[$row['YEAR(DateTime)']] = $row['max(R)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxT'] = $row['max(Tmax)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['minT'] = $row['min(Tmin)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxA'] = $row['max(A)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['minA'] = $row['min(A)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxD'] = $row['max(D)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['minD'] = $row['min(D)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxH'] = $row['max(H)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['minH'] = $row['min(H)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxP'] = $row['max(P)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['minP'] = $row['min(P)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxW'] = $row['max(W)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxG'] = $row['max(G)'];
				$stationData[$stationDay]['recordsArray'][$row['YEAR(DateTime)']]['maxR'] = $row['max(R)'];
			}
			
			
			if(count($maximumT)>0){
				$stationData[$stationDay]['recordTmax'] = max($maximumT);
				foreach($maximumT as $key=>$value){
					if($value==max($maximumT)){
						$stationData[$stationDay]['recordTmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumA)>0){
				$stationData[$stationDay]['recordAmax'] = max($maximumA);
				foreach($maximumA as $key=>$value){
					if($value==max($maximumA)){
						$stationData[$stationDay]['recordAmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumD)>0){
				$stationData[$stationDay]['recordDmax'] = max($maximumD);
				foreach($maximumD as $key=>$value){
					if($value==max($maximumD)){
						$stationData[$stationDay]['recordDmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumH)>0){
				$stationData[$stationDay]['recordHmax'] = max($maximumH);
				foreach($maximumH as $key=>$value){
					if($value==max($maximumH)){
						$stationData[$stationDay]['recordHmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumP)>0){
				$stationData[$stationDay]['recordPmax'] = max($maximumP);
				foreach($maximumP as $key=>$value){
					if($value==max($maximumP)){
						$stationData[$stationDay]['recordPmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumW)>0){
				$stationData[$stationDay]['recordWmax'] = max($maximumW);
				foreach($maximumW as $key=>$value){
					if($value==max($maximumW)){
						$stationData[$stationDay]['recordWmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumG)>0){
				$stationData[$stationDay]['recordGmax'] = max($maximumG);
				foreach($maximumG as $key=>$value){
					if($value==max($maximumG)){
						$stationData[$stationDay]['recordGmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($maximumR)>0){
				$stationData[$stationDay]['recordRmax'] = max($maximumR);
				foreach($maximumR as $key=>$value){
					if($value==max($maximumR)){
						$stationData[$stationDay]['recordRmaxY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($minimumT)>0){
				$stationData[$stationDay]['recordTmin'] = min($minimumT);
				foreach($minimumT as $key=>$value){
					if($value==min($minimumT)){
						$stationData[$stationDay]['recordTminY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($minimumA)>0){
				$stationData[$stationDay]['recordAmin'] = min($minimumA);
				foreach($minimumA as $key=>$value){
					if($value==min($minimumA)){
						$stationData[$stationDay]['recordAminY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($minimumD)>0){
				$stationData[$stationDay]['recordDmin'] = min($minimumD);
				foreach($minimumD as $key=>$value){
					if($value==min($minimumD)){
						$stationData[$stationDay]['recordDminY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($minimumH)>0){
				$stationData[$stationDay]['recordHmin'] = min($minimumH);
				foreach($minimumH as $key=>$value){
					if($value==min($minimumH)){
						$stationData[$stationDay]['recordHminY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
			if(count($minimumP)>0){
				$stationData[$stationDay]['recordPmin'] = min($minimumP);
				foreach($minimumP as $key=>$value){
					if($value==min($minimumP)){
						$stationData[$stationDay]['recordPminY'][] = "<span class='yearSpan year".$key."'>".$key."</span>";
					}
				}
			}
		}
		file_put_contents("cache/stationCache.txt",json_encode($stationData));
	}
	
	
	
	// Moon dates
	$moonResults = array();
	$moonFile = file("../astronomy/files/moons.txt");
	foreach($moonFile as $moonRow){
		$moonData = explode(",",$moonRow);
		for($i=0;$i<count($weekDatesArray);$i++){
			if(date("Ymd",$weekDatesArray[$i])==date("Ymd",$moonData[5])){
				$moonResults[] = $moonData;
			}
		}
	}
	
	// Season dates
	$seasonResults = array();
	$seasonFile = file("../astronomy/files/seasons.txt");
	foreach($seasonFile as $seasonRow){
		$checkedSeasonDate = strtotime(trim($seasonRow)) + $offset;
		for($i=0;$i<count($weekDatesArray);$i++){
			if(date("Ymd",$weekDatesArray[$i])==date("Ymd",$checkedSeasonDate)){
				$seasonResults[] = $checkedSeasonDate;
			}
		}
	}
	
	// Lunar Eclipses
	$lunarResults = array();
	$lunarFile = file("../astronomy/files/lunarEclipses.txt");
	foreach($lunarFile as $lunarRow){
		$lunarData = explode(";",$lunarRow);
		$checkedLunarDate = strtotime($lunarData[1]."-".$lunarData[2]."-".$lunarData[3]." ".$lunarData[4]) + $offset;
		for($i=0;$i<count($weekDatesArray);$i++){
			if(date("Ymd",$weekDatesArray[$i])==date("Ymd",$checkedLunarDate)){
				$lunarResults[] = $lunarRow;
			}
		}
	}
	
	// Solar Eclipses
	$solarResults = array();
	$solarFile = file("../astronomy/files/solarEclipses.txt");
	foreach($solarFile as $solarRow){
		$solarData = explode(";",$solarRow);
		$checkedSolarDate = strtotime($solarData[2]."-".$solarData[3]."-".$solarData[4]." ".$solarData[5]) + $offset;
		for($i=0;$i<count($weekDatesArray);$i++){
			if(date("Ymd",$weekDatesArray[$i])==date("Ymd",$checkedSolarDate)){
				$solarResults[] = $solarRow;
			}
		}
	}
	
	function cloudCoverImg($cover){
		$cover = $cover*100;
		if($cover==0){
			$coverImg = "clouds0";
		}
		if($cover>0 && $cover<12.5){
			$coverImg = "clouds1";
		}
		else if($cover>=12.5 && $cover<25){
			$coverImg = "clouds2";
		}
		else if($cover>=25 && $cover<37.5){
			$coverImg = "clouds3";
		}
		else if($cover>=37.5 && $cover<50){
			$coverImg = "clouds4";
		}
		else if($cover>=50 && $cover<62.5){
			$coverImg = "clouds5";
		}
		else if($cover>=62.5 && $cover<75){
			$coverImg = "clouds6";
		}
		else if($cover>=75 && $cover<87.5){
			$coverImg = "clouds7";
		}
		else if($cover>=78.5 && $cover<=100){
			$coverImg = "clouds8";
		}
		else{
			$coverImg = "clouds0";
		}
		return $coverImg;
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.sparkline.min.js"></script>
		<style>
			.sectionDiv{
				border: 1px solid #<?php echo $color_schemes[$design2]['400']?>;
				border-radius: 10px;
				width:98%;
				margin:0 auto;
				padding-top:10px;
				padding-bottom:20px;
				background: #<?php echo $color_schemes[$design2]['900']?>;
				text-align:center;
				/*display: none;*/
			}
			.currentTD{
				font-weight: bold;
				font-variant: small-caps;
				width: 8%;
			}
			.currentIcon{
				font-size: 2.7em;
			}
			.hourDivToday, .hourDivTomorrow, .hourDivLongTerm, .hourDivAstro, .hourDivStation{
				display: inline-block;
				padding: 5px;
				text-align: center;
				background: #<?php echo $color_schemes[$design]['900']?>;
				border-radius: 3px;
				border: 0.5px solid #<?php echo $color_schemes[$design2]['700']?>;
				width: 250px;
			}
			.hourTD{
				text-align: center;
				padding: 2px;
			}
			.hourTDContent{
				text-align: right;
				padding: 2px;
			}
			.hourIcon{
				width: 20px;
				font-size: 1.3em;
			}
			.more{
				cursor: pointer;
				opacity: 0.8;
			}
			.more:hover{
				opacity: 1;
			}
			<?php
				if($stationLat<0){
			?>
					#moonImg{
						-webkit-transform: rotate(-180deg);
						-moz-transform: rotate(-180deg);
						-ms-transform: rotate(-180deg);
						-o-transform: rotate(-180deg);
						filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
					}
			<?php
				}
			?>
			.inner-resizer {
				padding: 10px;
			}
			.resizer {   
				margin: 0 auto;
				width: 90%;
			}
			.sectionDivOpener{
				cursor: pointer;
				opacity: 0.8;
				color: #<?php echo $color_schemes[$design2]['100']?>;
			}
			.sectionDivOpener:hover{
				opacity: 1;
			}
			.headingIconSmall{
				width: 100px;
			}
			.sort{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.sort:hover{
				opacity: 1;
			}
			.table th{
				background: black;
			}
			.table tfoot td{
				background: black;
			}
			.updateIcon{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.updateIcon:hover{
				opacity: 1;
			}
			#pdfLink{
				font-size:3em;
				cursor: pointer;
				opacity: 0.8;
				padding-bottom:10px;
				padding-top: 10px;
			}
			#pdfLink:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<img src="icons/outlook.png" style="width:125px">
			<table style="width:98%;margin: 0 auto">
				<tr>
					<td style="width:5%">

					</td>
					<td style='text-align:center;vertical-align:top'>
						<h1><?php echo lang("outlook",'c')?></h1>
					</td>
					<td style="width:5%;text-align:right">
						<a href="forecastPDF.php" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>
					</td>
				</tr>
			</table>	
			<table style="margin:0 auto;font-variant:small-caps;font-size:1.1em" cellpadding="5" cellspacing="5">
				<tr>
					<td class="sectionDivOpener" data-id="current">
						<img src="icons/current.png" class="headingIconSmall" alt=""><br><?php echo lang('current conditions','c')?>
					</td>
					<?php 
						if($alertsActive){
					?>
							<td class="sectionDivOpener" data-id="alerts">
								<img src="<?php echo $pageURL.$path?>icons/warning.png" class="headingIconSmall" alt=""><br><?php echo lang('alerts','c')?>
							</td>
					<?php 
						}
					?>
					<td class="sectionDivOpener" data-id="shortTerm">
						<img src="icons/shortTerm.png" class="headingIconSmall" alt=""><br><?php echo lang('short-term forecast','c')?>
					</td>
					<td class="sectionDivOpener" data-id="longTerm">
						<img src="icons/longTerm.png" class="headingIconSmall" alt=""><br><?php echo lang('long-term forecast','c')?>
					</td>
					<td class="sectionDivOpener" data-id="forecastSummary">
						<img src="<?php echo $pageURL.$path?>icons/table.png" class="headingIconSmall" alt=""><br><?php echo lang('forecast summary','c')?>
					</td>
					<td class="sectionDivOpener" data-id="station">
						<img src="icons/stationAverages.png" class="headingIconSmall" alt=""><br><?php echo lang('station averages','c')?>
					</td>
					<td class="sectionDivOpener" data-id="stationExtremes">
						<img src="icons/stationExtremes.png" class="headingIconSmall" alt=""><br><?php echo lang('station extremes','c')?>
					</td>
					<td class="sectionDivOpener" data-id="astro">
						<img src="icons/astronomy.png" class="headingIconSmall" alt=""><br><?php echo lang('almanac','c')?>
					</td>
				</tr>
			</table>
			<br>
			<div id="currentDiv" class="sectionDiv">
				<h1><?php echo lang('current conditions','c')?></h1>
				<br>
				<table style="width:98%;margin:0 auto" id="currentTable">
					<tr>
						<td style="text-align:center;width:15%;font-weight:bold;font-size:1.4em;font-variant:small-caps" rowspan="4">
							<?php
								if(array_key_exists("icon",$current)){
							?>
									<img src="icons/<?php echo $current['icon']?>.png" style="width:90%;max-width:100px"><?php } else{?><img src="icons/na.png" style="width:90%;max-width:80px"><?php }?><?php if(array_key_exists("summary",$current)){?><br><?php echo $current['summary']?>
							<?php
								}
							?>
						</td>
						<td colspan="11"></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<span class="mticon-temp currentIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
						</td>
						<td>
							<span class="mticon-humidity currentIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
						</td>
						<td>
							<span class="mticon-pressure currentIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
						</td>
						<td>
							<span class="mticon-wind currentIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
						</td>
						<td>
							<span class="mticon-rain currentIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
						</td>
						<td>
							<span class="mticon-apparent currentIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
						</td>
						<td>
							<span class="mticon-dewpoint currentIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
						</td>
						<td>
							<span class="mticon-visibility currentIcon tooltip" title="<?php echo lang('visibility','c')?>"></span>
						</td>
						<td>
							<span class="mticon-o3 currentIcon tooltip" title="<?php echo lang('ozone','c')?>"></span>
						</td>
					</tr>
					<tr>
						<td style="width:5%;vertical-align:top">
							<span class="fa fa-plane fa-2x tooltip" title="<?php echo lang('airport','c')?>"></span>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("temperature",$current)){
									echo number_format($current['temperature'],1,".","")." ".unitformatter($displayTempUnits);
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("humidity",$current)){
									echo number_format($current['humidity'],1,".","")." %";
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("pressure",$current)){
									echo number_format($current['pressure'],$decimalsP,".","")." ".unitformatter($displayPressUnits);
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("windSpeed",$current)){
									echo number_format($current['windSpeed'],1,".","")." ".unitformatter($displayWindUnits);
									if(array_key_exists("windBearing",$current)){
										echo " (".windAbb($current['windBearing']).")";
									}
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("precipIntensity",$current)){
									echo number_format($current['precipIntensity'],$decimalsR,".","")." ".unitformatter($displayRainUnits)."/".lang("hAbbr",'l');
									if(array_key_exists("precipType",$current)){
										echo "<span style='font-size:0.7em'>(".ucWords($current['precipType']).")</span>";
									}
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("apparentTemperature",$current)){
									echo number_format($current['apparentTemperature'],1,".","")." ".unitformatter($displayTempUnits);
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("dewPoint",$current)){
									echo number_format($current['dewPoint'],1,".","")." ".unitformatter($displayTempUnits);
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("visibility",$current)){
									echo number_format($current['visibility'],1,".","")." ".unitformatter($displayVisibilityUnits);
								}
							?>
						</td>
						<td class="currentTD">
							<?php
								if(array_key_exists("ozone",$current)){
									echo number_format($current['ozone'],$decimalsP,".","")." DU";
								}
							?>
						</td>
						<td rowspan="3">
							<?php
								if(array_key_exists("cloudCover",$current)){
							?>
									<img src="<?php echo $pageURL.$path?>icons/<?php echo cloudCoverImg($current['cloudCover'])?>.png" class="currentIcon tooltip" title="<?php echo lang('cloud cover','c')?>" style="max-width:50px">
							<?php
								}
							?>
						</td>
					</tr>
					<tr>
						<td style="width:5%;vertical-align:top">
							<span class="mticon-<?php echo $stationIcon?>" style="font-size:2em"></span>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['T'],1,".","")." ".unitformatter($displayTempUnits);?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['H'],1,".","")." %";?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['P'],$decimalsP,".","")." ".unitformatter($displayPressUnits);?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['W'],1,".","")." ".unitformatter($displayWindUnits)." (".windAbb($station['B']).")";?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['RR'],$decimalsR,".","")." ".unitformatter($displayRainUnits)."/".lang('hAbbr','l');?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['A'],1,".","")." ".unitformatter($displayTempUnits);?>
						</td>
						<td class="currentTD">
							<?php echo number_format($station['D'],1,".","")." ".unitformatter($displayTempUnits);?>
						</td>
						<td></td>
						<td></td>
					</tr>
				</table>
				<br>
			</div>	
			<?php 
				if($alertsActive){
			?>
					<br>
					<div id="alertsDiv" class="sectionDiv">
						<img src="<?php echo $pageURL.$path?>icons/warning.png" style="width:50px"><br>
						
						<h3 style='text-align:center'>
						<?php 
							if(count($alerts)==1){
								echo "1 ".lang('alert','l');
							}
							else{
								echo count($alerts)." ".lang('alerts','l');
							}
						?>
						</h3>
						<div id="alertDetails" style="width:90%;margin:0 auto;text-align:left;display:none">
							<?php 
								foreach($alerts as $alert){
							?>
									<h3><?php echo $alert['title']?></h3>
									<span style="font-weight:bold;font-variant:small-caps;font-size:1.2em"><?php echo lang('expires','c')?></span>: <?php echo date($dateTimeFormat,$alert['expires'])?>
									<br>
									<?php echo $alert['description']?>
									<br>
							<?php 
								}
							?>
						</div>
						<div style="width:100%;text-align:center;font-weight:bold;font-variant:small-caps;font-size:1.6em">
							<span class="more" onclick="txt = $('#alertDetails').is(':visible') ? '<?php echo lang('show','l')?>' : '<?php echo lang('hide','l')?>';$('#alertDetails').slideToggle(800);$(this).text(txt)">
								<?php echo lang('show','l')?>
							</span>
						</div>
					</div>
			<?php 
				}
			?>
			
			<br>
			<div id="shortTermDiv" class="sectionDiv">
				<h1><?php echo lang("short-term forecast",'c')?></h1>
				<?php
					if(array_key_exists("icon",$hourly)){
				?>
						<img src="icons/<?php echo $hourly['icon']?>.png" style="width:90%;max-width:80px"><?php } else{?><img src="icons/na.png" style="width:90%;max-width:80px"><?php }?><?php if(array_key_exists("summary",$hourly)){?><br><span style="font-weight:bold;font-size:1.3em;font-variant:small-caps"><?php echo $hourly['summary']?></span>
				<?php
					}
				?>
				<?php 
					if(count($hourly['data']['today'])>0){
				?>
						<h2 style="text-align:center"><?php echo lang('today')?></h2><br>
						<div class="resizer">
							<div class="inner-resizer">
								<div id="todayGraph" style="width:100%;margin:0 auto;height:400px" class="graphDiv"></div>
							</div>
						</div>
						<br>
						<div id="todayDetails" style="width:100%">
						<br>
						<?php 
							foreach($hourly['data']['today'] as $hour){
						?>
								<div class="hourDivToday">
									<table style="width:98%;margin:0 auto" cellpadding="3">
										<tr>
											<td colspan="2" style="text-align:center;">
												<?php
													if(array_key_exists("time",$hour)){
												?>
														<span style="font-weight:bold;font-size:1.2em">
															<?php
																if($prefferedTime=="12h"){
																	echo date("g A",$hour['time']);
																}
																else{
																	echo date("G",$hour['time'])." ".lang('hAbbr','l');
																}
															?>
														</span>
												<?php
													}
												?>
											</td>
										</tr>
										<tr>
											<td style="text-align:center;padding-bottom:5px">
												<?php
													if(array_key_exists("icon",$hour)){
												?>
														<img src="icons/<?php echo $hour['icon']?>.png" style="width:90%;max-width:30px">
												<?php 
													} 
													else{
												?>
														<img src="icons/na.png" style="width:90%;max-width:30px">
												<?php 
													}
												?>
											</td>
											<td style='text-align:left;padding-bottom:5px'>
												<?php
													if(array_key_exists("summary",$hour)){
												?>
														<span style="font-weight:bold;font-variant:small-caps"><?php echo $hour['summary']?></span>
												<?php 
													} 
												?>
											</td>
										</tr>
										<?php
											if(array_key_exists("temperature",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['temperature'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("humidity",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['humidity'],0,".","")." %"?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("pressure",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['pressure'],$decimalsP,".","")." ".unitformatter($displayPressUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("windSpeed",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['windSpeed'],1,".","")." ".unitformatter($displayWindUnits)?>
														<?php
															if(array_key_exists("windBearing",$hour)){
														?>
																<?php echo " (".windAbb($hour['windBearing']).")"?>
														<?php
															}
														?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("precipIntensity",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['precipIntensity'],$decimalsR,".","")." ".unitformatter($displayRainUnits)."/".lang('hAbbr','l')?>
														<?php
															if(array_key_exists("precipType",$hour)){
														?>
																<br><?php echo ucWords($hour['precipType'])?>
														<?php
															}
														?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("apparentTemperature",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['apparentTemperature'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("dewPoint",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['dewPoint'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("visibility",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-visibility hourIcon tooltip" title="<?php echo lang('visibility','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['visibility'],1,".","")." ".($displayVisibilityUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("ozone",$hour)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($hour['ozone'],2,".","")." DU"?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("cloudCover",$hour)){	
										?>
												<tr>
													<td class="hourTD" colspan="2">
														<img src="<?php echo $pageURL.$path?>icons/<?php echo cloudCoverImg($hour['cloudCover'])?>.png" class="hourIcon tooltip" title="<?php echo lang('cloud cover','c')?>">
													</td>
												</tr>
										<?php
											}
										?>
									</table>
								</div>
						<?php
							}
						?>
						</div>
						<div style="width:100%;text-align:center;font-weight:bold;font-variant:small-caps;font-size:1.6em">
							<span class="more" onclick="txt = $('#todayDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('less','l')?>';$('#todayDetails').slideToggle(800);$(this).text(txt)">
								<?php echo lang('more','l')?>
							</span>
						</div>
						<br>
				<?php 
					}
				?>
				<?php 
					if(count($hourly['data']['tomorrow'])>0){
				?>
						<h2 style="text-align:center"><?php echo lang('tomorrow')?></h2><br>
						<div class="resizer">
							<div class="inner-resizer">
								<div id="tomorrowGraph" style="width:100%;margin:0 auto;height:400px" class="graphDiv"></div>
							</div>
						</div>
						<br>	
						<div id="tomorrowDetails" style="width:100%">
							<?php 
								foreach($hourly['data']['tomorrow'] as $hour){
							?>
									<div class="hourDivTomorrow">
										<table style="width:98%;margin:0 auto" cellpadding="3">
											<tr>
												<td colspan="2">
													<?php
														if(array_key_exists("time",$hour)){
													?>
															<span style="font-weight:bold;font-size:1.2em">
																<?php
																	if($prefferedTime=="12h"){
																		echo date("g A",$hour['time']);
																	}
																	else{
																		echo date("G",$hour['time'])." ".lang('hAbbr','l');
																	}
																?>
															</span>
													<?php
														}
													?>
												</td>
											</tr>
											<tr>
												<td style="text-align:center;padding-bottom:5px">
													<?php
														if(array_key_exists("icon",$hour)){
													?>
															<img src="icons/<?php echo $hour['icon']?>.png" style="width:90%;max-width:30px">
													<?php 
														} 
														else{
													?>
															<img src="icons/na.png" style="width:90%;max-width:30px">
													<?php 
														}
													?>
												</td>
												<td style='text-align:left;padding-bottom:5px'>
													<?php
														if(array_key_exists("summary",$hour)){
													?>
															<span style="font-weight:bold;font-variant:small-caps"><?php echo $hour['summary']?></span>
													<?php 
														} 
													?>
												</td>
											</tr>
											<?php
												if(array_key_exists("temperature",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['temperature'],1,".","").unitformatter($displayTempUnits)?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("humidity",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['humidity'],0,".","")." %"?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("pressure",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['pressure'],$decimalsP,".","")." ".unitformatter($displayPressUnits)?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("windSpeed",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['windSpeed'],1,".","")." ".unitformatter($displayWindUnits)?>
															<?php
																if(array_key_exists("windBearing",$hour)){
															?>
																	<?php echo " (".windAbb($hour['windBearing']).")"?>
															<?php
																}
															?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("precipIntensity",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['precipIntensity'],$decimalsR,".","")." ".unitformatter($displayRainUnits)."/".lang('hAbbr','l')?>
															<?php
																if(array_key_exists("precipType",$hour)){
															?>
																	<br><?php echo ucWords($hour['precipType'])?>
															<?php
																}
															?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("apparentTemperature",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['apparentTemperature'],1,".","").unitformatter($displayTempUnits)?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("dewPoint",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['dewPoint'],1,".","").unitformatter($displayTempUnits)?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("visibility",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-visibility hourIcon tooltip" title="<?php echo lang('visibility','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['visibility'],1,".","")." ".($displayVisibilityUnits)?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("ozone",$hour)){
											?>
													<tr>
														<td class="hourTD">
															<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span>
														</td>
														<td class="hourTDContent">
															<?php echo number_format($hour['ozone'],2,".","")." DU"?>
														</td>
													</tr>
											<?php
												}
											?>
											<?php
												if(array_key_exists("cloudCover",$hour)){	
											?>
													<tr>
														<td class="hourTD" colspan="2">
															<img src="<?php echo $pageURL.$path?>icons/<?php echo cloudCoverImg($hour['cloudCover'])?>.png" class="hourIcon tooltip" title="<?php echo lang('cloud cover','c')?>">
														</td>
													</tr>
											<?php
												}
											?>
										</table>
									</div>
							<?php
								}
							?>
						</div>
						<div style="width:100%;text-align:center;font-weight:bold;font-variant:small-caps;font-size:1.6em">
							<span class="more" onclick="txt = $('#tomorrowDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('less','l')?>';$('#tomorrowDetails').slideToggle(800);$(this).text(txt)">
								<?php echo lang('more','l')?>
							</span>
						</div>
				<?php 
					}
				?>
			</div>
			<br>
			<div id="longTermDiv" class="sectionDiv">
				<h1><?php echo lang("long-term forecast",'c')?></h1>
				<div class="resizer">
							<div class="inner-resizer">
								<div id="longtermGraph" style="width:100%;margin:0 auto;height:400px" class="graphDiv"></div>
							</div>
						</div>
						<br>
				<div id="longtermDetails" style="width:100%">
				<?php 
					if(count($daily['data'])>0){
				?>
						<?php 
							foreach($daily['data'] as $day){
						?>
								<div class="hourDivLongTerm">
									<table style="width:98%;margin:0 auto" cellpadding="3">
										<tr>
											<td colspan="2" style="text-align:center;">
												<?php
													if(array_key_exists("time",$day)){
												?>
														<span style="font-weight:bold;font-size:1.2em">
															<?php
																echo $weekDays[date("w",$day['time'])];
															?>
														</span>
												<?php
													}
												?>
											</td>
										</tr>
										<tr>
											<td style="text-align:center" colspan="2">
												<?php
													if(array_key_exists("icon",$day)){
												?>
														<img src="icons/<?php echo $day['icon']?>.png" style="width:90%;max-width:70px">
												<?php 
													} 
													else{
												?>
														<img src="icons/na.png" style="width:90%;max-width:30px">
												<?php 
													}
												?>
											</td>
										</tr>
										<tr>
											<td style='text-align:left;padding-bottom:5px' colspan="2">
												<?php
													if(array_key_exists("summary",$day)){
												?>
														<span style="font-weight:bold;font-variant:small-caps"><?php echo $day['summary']?></span>
												<?php 
													} 
												?>
											</td>
										</tr>
										<?php
											if(array_key_exists("temperatureMax",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['temperatureMax'],1,".","")." / ".number_format($day['temperatureMin'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("humidity",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['humidity'],0,".","")." %"?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("pressure",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['pressure'],$decimalsP,".","")." ".unitformatter($displayPressUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("windSpeed",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['windSpeed'],1,".","")." ".unitformatter($displayWindUnits)?>
														<?php
															if(array_key_exists("windBearing",$day)){
														?>
																<?php echo " (".windAbb($day['windBearing']).")"?>
														<?php
															}
														?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("precipIntensity",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['precipIntensity']*24,$decimalsR,".","")." ".unitformatter($displayRainUnits)?>
														<?php
															if(array_key_exists("precipType",$day)){
														?>
																<br><?php echo ucWords($day['precipType'])?>
														<?php
															}
														?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("apparentTemperature",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['apparentTemperature'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("dewPoint",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['dewPoint'],1,".","").unitformatter($displayTempUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("visibility",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-visibility hourIcon tooltip" title="<?php echo lang('visibility','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['visibility'],1,".","")." ".($displayVisibilityUnits)?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("ozone",$day)){
										?>
												<tr>
													<td class="hourTD">
														<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span>
													</td>
													<td class="hourTDContent">
														<?php echo number_format($day['ozone'],2,".","")." DU"?>
													</td>
												</tr>
										<?php
											}
										?>
										<?php
											if(array_key_exists("cloudCover",$day)){	
										?>
												<tr>
													<td class="hourTD" colspan="2">
														<img src="<?php echo $pageURL.$path?>icons/<?php echo cloudCoverImg($day['cloudCover'])?>.png" class="hourIcon tooltip" title="<?php echo lang('cloud cover','c')?>">
													</td>
												</tr>
										<?php
											}
										?>
									</table>
								</div>
						<?php
							}
						?>
						</div>
						<br>
						<div style="width:100%;text-align:center;font-weight:bold;font-variant:small-caps;font-size:1.6em">
							<span class="more" onclick="txt = $('#longtermDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('less','l')?>';$('#longtermDetails').slideToggle(800);$(this).text(txt)">
								<?php echo lang('more','l')?>
							</span>
						</div>
				<?php 
					}
				?>
			</div>
			<br>
			<div id="forecastSummaryDiv" class="sectionDiv">
				<h1><?php echo lang('forecast summary','c')?></h1>
				<h3 style="padding-left:1%"><?php echo lang("today","c")?></h3>
				<?php 
					if(count($hourly['data']['today'])>0){
				?>
						<table class="table" style="width:98%;margin:0 auto">
							<thead>
								<tr>
									<th style="text-align:center;width:5%">
										<?php echo lang('hour','c');?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:right;width:3%">
										
									</th>
									<th style="text-align:left;width:10%">
										
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span><br>%<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span><br><?php echo unitFormatter($displayPressUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span><br><?php echo unitFormatter($displayWindUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span><br><?php echo unitFormatter($displayRainUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span><br>DU<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($hourly['data']['today'] as $hour){
								?>
										<tr>
											<td style="text-align:center;font-weight:bold">
												<?php
													if(array_key_exists("time",$hour)){
												?>
														<span style="font-weight:bold;font-size:1.2em">
															<?php
																if($prefferedTime=="12h"){
																	echo date("g A",$hour['time']);
																}
																else{
																	echo date("G",$hour['time']);
																}
															?>
														</span>
												<?php
													}
												?>
											</td>
											<td style="text-align:right">
												<?php
													if(array_key_exists("icon",$hour)){
												?>
														<img src="icons/<?php echo $hour['icon']?>.png" style="width:90%;max-width:30px">
												<?php 
													} 
													else{
												?>
														<img src="icons/na.png" style="width:90%;max-width:30px">
												<?php 
													}
												?>
											</td>
											<td style='text-align:left'>
												<?php
													if(array_key_exists("summary",$hour)){
												?>
														<span style="font-weight:bold;font-variant:small-caps"><?php echo $hour['summary']?></span>
												<?php 
													} 
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("temperature",$hour)){
														echo number_format($hour['temperature'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("apparentTemperature",$hour)){
														echo number_format($hour['apparentTemperature'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("dewPoint",$hour)){
														echo number_format($hour['dewPoint'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("humidity",$hour)){
														echo number_format($hour['humidity'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("pressure",$hour)){
														echo number_format($hour['pressure'],$decimalsP,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("windSpeed",$hour)){
														echo number_format($hour['windSpeed'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("precipIntensity",$hour)){
														echo number_format($hour['precipIntensity'],$decimalsR,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("ozone",$hour)){
														echo number_format($hour['ozone'],1,".","");
													}
												?>
											</td>
										</tr>
								<?php
									}
								?>
							</tbody>
						</table>
				<?php 
					}
				?>
				<br>
				<h3 style="padding-left:1%"><?php echo lang("tomorrow","c")?></h3>
				<?php 
					if(count($hourly['data']['tomorrow'])>0){
				?>
						<table class="table" style="width:98%;margin:0 auto">
							<thead>
								<tr>
									<th style="text-align:center;width:5%">
										<?php echo lang('hour','c');?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:right;width:3%">
										
									</th>
									<th style="text-align:left;width:10%">
										
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span><br>%<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span><br><?php echo unitFormatter($displayPressUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span><br><?php echo unitFormatter($displayWindUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span><br><?php echo unitFormatter($displayRainUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span><br>DU<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($hourly['data']['tomorrow'] as $hour){
								?>
										<tr>
											<td style="text-align:center;">
												<?php
													if(array_key_exists("time",$hour)){
												?>
														<span style="font-weight:bold;font-size:1.2em">
															<?php
																if($prefferedTime=="12h"){
																	echo date("g A",$hour['time']);
																}
																else{
																	echo date("G",$hour['time']);
																}
															?>
														</span>
												<?php
													}
												?>
											</td>
											<td style="text-align:right">
												<?php
													if(array_key_exists("icon",$hour)){
												?>
														<img src="icons/<?php echo $hour['icon']?>.png" style="width:90%;max-width:30px">
												<?php 
													} 
													else{
												?>
														<img src="icons/na.png" style="width:90%;max-width:30px">
												<?php 
													}
												?>
											</td>
											<td style='text-align:left'>
												<?php
													if(array_key_exists("summary",$hour)){
												?>
														<span style="font-weight:bold;font-variant:small-caps"><?php echo $hour['summary']?></span>
												<?php 
													} 
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("temperature",$hour)){
														echo number_format($hour['temperature'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("apparentTemperature",$hour)){
														echo number_format($hour['apparentTemperature'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("dewPoint",$hour)){
														echo number_format($hour['dewPoint'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("humidity",$hour)){
														echo number_format($hour['humidity'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("pressure",$hour)){
														echo number_format($hour['pressure'],$decimalsP,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("windSpeed",$hour)){
														echo number_format($hour['windSpeed'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("precipIntensity",$hour)){
														echo number_format($hour['precipIntensity'],$decimalsR,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("ozone",$hour)){
														echo number_format($hour['ozone'],1,".","");
													}
												?>
											</td>
										</tr>
								<?php
									}
								?>
							</tbody>
						</table>
				<?php 
					}
				?>
				<br>
				<h3 style="padding-left:1%"><?php echo lang("long-term forecast","c")?></h3>
				<?php 
					if(count($daily['data'])>0){
				?>
						<table class="table" style="width:98%;margin:0 auto">
							<thead>
								<tr>
									<th style="text-align:center;width:5%">
										
									</th>
									<th style="text-align:right;width:3%">
										
									</th>
									<th style="text-align:left;width:10%">
										
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span><br><?php echo lang('maximumAbbr','l')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span><br><?php echo lang('minimumAbbr','l')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span><br>%<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span><br><?php echo unitFormatter($displayPressUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span><br><?php echo unitFormatter($displayWindUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span><br><?php echo unitFormatter($displayRainUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th style="text-align:center;width:5%">
										<span class="mticon-o3 hourIcon tooltip" title="<?php echo lang('ozone','c')?>"></span><br>DU<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									foreach($daily['data'] as $day){
								?>
										<tr>
											<td style="text-align:center;">
												<?php
													if(array_key_exists("time",$day)){
												?>
														<span style="font-weight:bold;font-size:1.2em">
															<?php
																echo $weekDays[date("w",$day['time'])];
															?>
														</span>
												<?php
													}
												?>
											</td>
											<td style="text-align:right">
												<?php
													if(array_key_exists("icon",$day)){
												?>
														<img src="icons/<?php echo $day['icon']?>.png" style="width:90%;max-width:30px">
												<?php 
													} 
													else{
												?>
														<img src="icons/na.png" style="width:90%;max-width:30px">
												<?php 
													}
												?>
											</td>
											<td style='text-align:left'>
												<?php
													if(array_key_exists("summary",$day)){
												?>
														<span style="font-weight:bold;font-variant:small-caps"><?php echo $day['summary']?></span>
												<?php 
													} 
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("temperatureMax",$day)){
														echo number_format($day['temperatureMax'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("temperatureMin",$day)){
														echo number_format($day['temperatureMin'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("dewPoint",$day)){
														echo number_format($day['dewPoint'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("humidity",$hour)){
														echo number_format($hour['humidity'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("pressure",$day)){
														echo number_format($day['pressure'],$decimalsP,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("windSpeed",$day)){
														echo number_format($day['windSpeed'],1,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("precipIntensity",$hour)){
														echo number_format($hour['precipIntensity'],$decimalsR,".","");
													}
												?>
											</td>
											<td>
												<?php
													if(array_key_exists("ozone",$day)){
														echo number_format($day['ozone'],1,".","");
													}
												?>
											</td>
										</tr>
								<?php
									}
								?>
							</tbody>
						</table>
				<?php 
					}
				?>
			</div>
			<div id="stationDiv" class="sectionDiv">
				<h1><?php echo lang('station averages','c')?></h1>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="stationGraph" style="width:100%;margin:0 auto;height:400px" class="graphDiv"></div>
					</div>
				</div>
				<br>
				<div style="width:100%;text-align:center;font-weight:bold;font-variant:small-caps;font-size:1.6em">
					<span class="more" onclick="txt = $('#stationDetailsDiv').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('less','l')?>';$('#stationDetailsDiv').slideToggle(800);$(this).text(txt)">
						<?php echo lang('more','l')?>
					</span>
				</div>
				<br>
				<div id="stationDetailsDiv" style="width:100%">
					<?php 
						foreach($stationData as $key=>$value){
					?>
							<div class="hourDivAstro" style="width:150px">
								<?php 
									echo "<span style='font-size:1.2em;font-weight:bold;font-variant:small-caps'>".date("j",$key)." ".$monthNames[date('n',$key)]."</span>";
								?>
								<table style="width:90%;margin:0 auto">
									<tr>
										<td>
											<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertT($value['maxT']),1,".","")." / ".number_format(convertT($value['minT']),1,".","")." ".unitFormatter($displayTempUnits);?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo $value['H']." %";?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertP($value['P']),$decimalsP,".","")." ".unitFormatter($displayPressUnits);?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertW($value['W']),1,".","")." ".unitFormatter($displayWindUnits);?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertR($value['R']),$decimalsR,".","")." ".$displayRainUnits;?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertT($value['maxA']),1,".","")." / ".number_format(convertT($value['minA']),1,".","")." ".unitFormatter($displayTempUnits);?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
										</td>
										<td style="text-align:right">
											<?php echo number_format(convertT($value['maxD']),1,".","")." / ".number_format(convertT($value['minD']),1,".","")." ".unitFormatter($displayTempUnits);?>
										</td>
									</tr>
								</table>
							</div>
					<?php 
						}
					?>
				</div>
			</div>
			<br>
			<div id="stationExtremesDiv" class="sectionDiv">
				<h1><?php echo lang('station extremes','c')?></h1>
				<br>
				<?php 
					foreach($yearsArr as $oneYear){
				?>
						<input type="button" class="button2 yearHighlighter" value="<?php echo $oneYear?>" style="margin-right: 10px;margin-left: 10px">
				<?php 
					}			
				?>
				<br><br>
					<?php 
						foreach($stationData as $key=>$value){
					?>
							<div class="hourDivStation" style="width:300px">
								<?php 
									echo "<span style='font-size:1.2em;font-weight:bold;font-variant:small-caps'>".$weekDays[date('w',$key)]."<br>".date("d",$key)." ".$monthNames[date('n',$key)]."</span>";
								?>
								<table style="width:90%;margin:0 auto">
									<tr>
										<td rowspan="2">
											<span class="mticon-temp hourIcon tooltip" title="<?php echo lang('temperature','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordTmax",$value)){
													echo number_format(convertT($value['recordTmax']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordTmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td style='font-variant:small-caps'>
											<?php echo lang("minimumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordTmin",$value)){
													echo number_format(convertT($value['recordTmin']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordTminY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td rowspan="2">
											<span class="mticon-humidity hourIcon tooltip" title="<?php echo lang('humidity','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordHmax",$value)){
													echo number_format(($value['recordHmax']),0,".","")." % (".implode(", ",$value['recordHmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td style='font-variant:small-caps'>
											<?php echo lang("minimumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordHmin",$value)){
													echo number_format(($value['recordHmin']),0,".","")." % (".implode(", ",$value['recordHminY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td rowspan="2">
											<span class="mticon-pressure hourIcon tooltip" title="<?php echo lang('pressure','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordPmax",$value)){
													echo number_format(convertP($value['recordPmax']),$decimalsP,".","")." ".unitFormatter($displayPressUnits)." (".implode(", ",$value['recordPmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td style='font-variant:small-caps'>
											<?php echo lang("minimumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordPmin",$value)){
													echo number_format(convertP($value['recordPmin']),$decimalsP,".","")." ".unitFormatter($displayPressUnits)." (".implode(", ",$value['recordPminY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-wind hourIcon tooltip" title="<?php echo lang('wind speed','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordWmax",$value)){
													echo number_format(convertW($value['recordWmax']),1,".","")." ".unitFormatter($displayWindUnits)." (".implode(", ",$value['recordWmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-gust hourIcon tooltip" title="<?php echo lang('wind gust','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordGmax",$value)){
													echo number_format(convertW($value['recordGmax']),1,".","")." ".unitFormatter($displayWindUnits)." (".implode(", ",$value['recordGmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td>
											<span class="mticon-rain hourIcon tooltip" title="<?php echo lang('precipitation','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordRmax",$value)){
													echo number_format(convertR($value['recordRmax']),$decimalsR,".","")." ".unitFormatter($displayRainUnits)." (".implode(", ",$value['recordRmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td rowspan="2">
											<span class="mticon-apparent hourIcon tooltip" title="<?php echo lang('apparent temperature','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordAmax",$value)){
													echo number_format(convertT($value['recordAmax']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordAmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td style='font-variant:small-caps'>
											<?php echo lang("minimumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordAmin",$value)){
													echo number_format(convertT($value['recordAmin']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordAminY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td rowspan="2">
											<span class="mticon-dewpoint hourIcon tooltip" title="<?php echo lang('dew point','c')?>"></span>
										</td>
										<td style='font-variant:small-caps'>
											<?php echo lang("maximumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordDmax",$value)){
													echo number_format(convertT($value['recordDmax']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordDmaxY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
									<tr>
										<td style='font-variant:small-caps'>
											<?php echo lang("minimumAbbr",'c')?>
										</td>
										<td style="text-align:right">
											<?php
												if(array_key_exists("recordDmin",$value)){
													echo number_format(convertT($value['recordDmin']),1,".","")." ".unitFormatter($displayTempUnits)." (".implode(", ",$value['recordDminY']).")";
												}
												else{
													echo "-";
												}
											?>
										</td>
									</tr>
								</table>
							</div>
					<?php 
						}
					?>
				<br>
					<?php 
						foreach($stationData as $key=>$value){
					?>
							<div style="width:98%;margin:0 auto;padding-top:10px;padding-bottom:10px">
								<h3><?php echo date("j",$key)." ".$monthNames[date('n',$key)]?></h3>
							</div>
							<table style="width:98%;margin:0 auto" class="table">
								<thead>
									<tr>
										<th></th>
										<th colspan="2">
											<img src="<?php echo $pageURL.$path?>icons/temp.png" style="width:30px" class="tooltip" title="<?php echo lang('temperature','c')?>">
										</th>
										<th colspan="2">
											<img src="<?php echo $pageURL.$path?>icons/humidity.png" style="width:30px" class="tooltip" title="<?php echo lang('humidity','c')?>">
										</th>
										<th colspan="2">
											<img src="<?php echo $pageURL.$path?>icons/pressure.png" style="width:30px" class="tooltip" title="<?php echo lang('pressure','c')?>">
										</th>
										<th>
											<img src="<?php echo $pageURL.$path?>icons/wind.png" style="width:30px" class="tooltip" title="<?php echo lang('wind speed','c')?>">
										</th>
										<th>
											<img src="<?php echo $pageURL.$path?>icons/gust.png" style="width:30px" class="tooltip" title="<?php echo lang('wind gust','c')?>">
										</th>
										<th>
											<img src="<?php echo $pageURL.$path?>icons/rain.png" style="width:30px" class="tooltip" title="<?php echo lang('precipitation','c')?>">
										</th>
										<th colspan="2">
											<img src="<?php echo $pageURL.$path?>icons/apparent.png" style="width:30px" class="tooltip" title="<?php echo lang('apparent temperature','c')?>">
										</th>
										<th colspan="2">
											<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" style="width:30px" class="tooltip" title="<?php echo lang('dewpoint','c')?>">
										</th>
									</tr>
									<tr>
										<th><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
										<th style="width:7%" id="oneRecordTD">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("minimumAbbr",'c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br>%<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("minimumAbbr",'c')?><br>%<br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayPressUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("minimumAbbr",'c')?><br><?php echo unitFormatter($displayPressUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayWindUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("maximumAbbr",'c')?><br><?php echo unitFormatter($displayWindUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayRainUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("minimumAbbr",'c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang('maximumAbbr','c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
										<th style="width:7%">
											<?php echo lang("minimumAbbr",'c')?><br><?php echo unitFormatter($displayTempUnits)?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										foreach($value['recordsArray'] as $currentYear=>$record){
									?>
											<tr>
												<td>
													<?php echo $currentYear?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxT",$record)){
															echo number_format(convertT($record['maxT']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("minT",$record)){
															echo number_format(convertT($record['minT']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxH",$record)){
															echo number_format(($record['maxH']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("minH",$record)){
															echo number_format(($record['minH']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxP",$record)){
															echo number_format(convertP($record['maxP']),$decimalsP,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("minP",$record)){
															echo number_format(convertP($record['minP']),$decimalsP,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxW",$record)){
															echo number_format(convertW($record['maxW']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxG",$record)){
															echo number_format(convertW($record['maxG']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxR",$record)){
															echo number_format(convertR($record['maxR']),$decimalsR,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxA",$record)){
															echo number_format(convertT($record['maxA']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("minA",$record)){
															echo number_format(convertT($record['minA']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("maxD",$record)){
															echo number_format(convertT($record['maxD']),1,".","");
														}
													?>
												</td>
												<td>
													<?php
														if(array_key_exists("minD",$record)){
															echo number_format(convertT($record['minD']),1,".","");
														}
													?>
												</td>
											</tr>
									<?php
										}
									?>
								</tbody>
								<tfoot>
									<tr>
										<td></td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxT",$record)){
															$temporaryArr[] = number_format(convertT($record['maxT']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("minT",$record)){
															$temporaryArr[] = number_format(convertT($record['minT']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesH">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxH",$record)){
															$temporaryArr[] = number_format(($record['maxH']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesH">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("minH",$record)){
															$temporaryArr[] = number_format(($record['minH']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesP">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxP",$record)){
															$temporaryArr[] = number_format(convertT($record['maxP']),$decimalsP,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesP">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("minP",$record)){
															$temporaryArr[] = number_format(convertT($record['minP']),$decimalsP,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesW">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxW",$record)){
															$temporaryArr[] = number_format(convertW($record['maxW']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesW">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxG",$record)){
															$temporaryArr[] = number_format(convertW($record['maxG']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesR">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxR",$record)){
															$temporaryArr[] = number_format(convertR($record['maxR']),$decimalsR,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxA",$record)){
															$temporaryArr[] = number_format(convertT($record['maxA']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("minA",$record)){
															$temporaryArr[] = number_format(convertT($record['minA']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("maxD",$record)){
															$temporaryArr[] = number_format(convertT($record['maxD']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
										<td>
											<div class="sparklinesT">
												<?php 
													$temporaryArr = array();
													foreach($value['recordsArray'] as $currentYear=>$record){
														if(array_key_exists("minD",$record)){
															$temporaryArr[] = number_format(convertT($record['minD']),1,".","");
														}
													}
													echo implode(", ",$temporaryArr);
												?>
											</div>
										</td>
									</tr>
								</tfoot>
							</table>
							<br>
					<?php
						}
					?>
			</div>
			<br>
			<div id="astroDiv" class="sectionDiv">
				<?php
					foreach($daily['data'] as $day){
				?>
						<div class="hourDivAstro" style="width:150px">
							<?php
								if(array_key_exists("time",$day)){
							?>
									<span style="font-weight:bold;font-size:1.2em">
										<?php
											echo $weekDays[date("w",$day['time'])];
										?>
									</span>
							<?php
								}
							?>
							<table style='width:98%;margin:0 auto'>
								<tr>
									<td style="width:50%">
										<img src="<?php echo $pageURL.$path?>icons/sunrise.png" style="width:25px">
									</td>
									<td style="width:50%">
										<img src="<?php echo $pageURL.$path?>icons/sunset.png" style="width:25px">
									</td>
								</tr>
								<tr>
									<td style="width:50%">
										<?php
											if(array_key_exists("sunriseTime",$day)){
												echo date($timeFormat,$day['sunriseTime']);
											}
										?>
									</td>
									<td style="width:50%">
										<?php
											if(array_key_exists("sunsetTime",$day)){
												echo date($timeFormat,$day['sunsetTime']);
											}
										?>
									</td>
								</tr>
								<?php
									if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
										$morning = $day['sunriseTime'] - strtotime(date("Y",$day['sunriseTime'])."-".date("m",$day['sunriseTime'])."-".date("d",$day['sunriseTime'])." 00:00");
										$dayTime = $day['sunsetTime'] - $day['sunriseTime'];
										$daySeconds = 60 * 60 * 24;
										$evening = $daySeconds - $dayTime - $morning;
										$morningPerc = round($morning/$daySeconds,2)*100;
										$dayPerc = round($dayTime/$daySeconds,2)*100;
										$eveningPerc = round($evening/$daySeconds,2)*100;
								?>
										<tr>
											<td colspan="2">
												<div style="height:10px"></div>
												<table style="width:80%;margin: 0 auto" cellspacing="0" cellpadding="0">
													<tr>
														<td>
															<img src="<?php echo $pageURL.$path?>icons/moon.png" style="width:12px;opacity:0.7">
														</td>
														<td>
															<img src="<?php echo $pageURL.$path?>icons/sun.png" style="width:12px;opacity:0.9">
														</td>
														<td>
															<img src="<?php echo $pageURL.$path?>icons/moon.png" style="width:12px;opacity:0.7">
														</td>
													</tr>
													<tr>
														<td style="height:7px;background:black;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-right:0px;width:<?php echo $morningPerc?>%"></td>
														<td style="height:7px;background:#4ca6ff;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-right:0px;border-left:0px;width:<?php echo $dayPerc?>%"></td>
														<td style="height:7px;background:black;border:1px solid #<?php echo $color_schemes[$design]['600']?>;border-left:0px;width:<?php echo $eveningPerc?>%"></td>
													</tr>
												</table>
											</td>
										</tr>
								<?php 
									}
								?>
								<tr>
									<td colspan="2">
										<?php
											if(array_key_exists("moonPhase",$day)){
												$phase = $day['moonPhase'];
												if($phase<0.5){
													$phase = $phase + 0.5;
												}
												else{
													$phase = $phase - 0.5;
												}
												$moonIcon = round(($phase/(1/118)));
										?>
												<img src='<?php echo $pageURL.$path?>imgs/moonImgs/<?php echo $moonIcon?>.png' style='width:70px' id='moonImg'>
										<?php
											}
										?>
									</td>
								</tr>
								<tr>
									<td>
										<img src="<?php echo $pageURL.$path?>icons/sun.png" style="width:25px;opacity:0.9">
									</td>
									<td>
										<img src="<?php echo $pageURL.$path?>icons/night.png" style="width:25px;opacity:0.9">
									</td>									
								</tr>
								<tr>
									<td style="font-size:0.9em">
										<?php
											$dayLengthS = 60 * 60 * 24;
											if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
												$dayMin = ($day['sunsetTime'] - $day['sunriseTime'])/60;
												$nightMin = 1440 - $dayMin;
												echo round($dayMin,0)." ".lang("minAbbr",'l');
											}
										?>
									</td>
									<td style="font-size:0.9em">
										<?php
											if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
												echo round($nightMin,0)." ".lang("minAbbr",'l');
											}
										?>
									</td>
								</tr>
							</table>
						</div>
				<?php
					}
				?>
				<br><br>
				<table style="width:90%;margin: 0 auto">
					<tr>
					<?php 
						if(count($moonResults)>0){
							for($i=0;$i<count($moonResults);$i++){
								$phaseRaw = $moonResults[$i][3];
								if($phaseRaw==1){
									$phase = lang("new moon",'c');
									$phaseImg = 59;
								}
								else if($phaseRaw==2){
									$phase = lang("first quarter",'c');
									if($stationLat>=0){
										$phaseImg = 93;
									}
									else{
										$phaseImg = 22;
									}
								}
								else if($phaseRaw==3){
									$phase = lang("full moon",'c');
									$phaseImg = 118;
								}
								else{
									$phase = lang("last quarter",'c');
									if($stationLat>=0){
										$phaseImg = 22;
									}
									else{
										$phaseImg = 93;
									}
								}
						?>
								<td >
									<table style="width:98%;margin:0 auto">
										<tr>
											<td style="width:100px">
												<img src='<?php echo $pageURL.$path?>imgs/moonImgs/<?php echo $phaseImg?>.png' style='width:90px'>
											</td>
											<td>
												<span style="font-weight:bold;font-size:1.3;font-variant:small-caps"><?php echo $phase?></span>
												<br>
												<?php echo date($dateTimeFormat,$moonResults[$i][5]);?>
											</td>
										</tr>
									</table>
								</td>
						<?php
							}
						} // end moon results
						if(count($seasonResults)>0){
							for($i=0;$i<count($seasonResults);$i++){
								if(date("n",$seasonResults[$i])==3){
									if($stationLat>=0){
										$seasonType = lang('spring','c');
										$seasonImage = "spring";
									}
									else{
										$seasonType = lang('autumn','c');
										$seasonImage = "autumn";
									}
								}
								else if(date("n",$seasonResults[$i])==6){
									if($stationLat>=0){
										$seasonType = lang('summer','c');
										$seasonImage = "summer";
									}
									else{
										$seasonType = lang('winter','c');
										$seasonImage = "winter";
									}
								}
								else if(date("n",$seasonResults[$i])==9){
									if($stationLat>=0){
										$seasonType = lang('autumn','c');
										$seasonImage = "autumn";
									}
									else{
										$seasonType = lang('spring','c');
										$seasonImage = "spring";
									}
								}
								else{
									if($stationLat>=0){
										$seasonType = lang('winter','c');
										$seasonImage = "winter";
									}
									else{
										$seasonType = lang('summer','c');
										$seasonImage = "summer";
									}
								}
							?>
								<td>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td>
												<img src='icons/<?php echo $seasonImage?>.png' style='width:90px'>
											</td>
											<td>
												<span style="font-weight:bold;font-size:1.3;font-variant:small-caps"><?php echo $seasonType?></span>
												<br>
												<?php echo date($dateTimeFormat,$seasonResults[$i]);?>
											</td>
										</tr>
									</table>
								</td>
						<?php 
							}
						} // end season results
						if(count($lunarResults)>0){
							for($i=0;$i<count($lunarResults);$i++){
								$lunarRow = explode(';',$lunarResults[$i]);
								$checkedLunarDate = strtotime($lunarRow[1]."-".$lunarRow[2]."-".$lunarRow[3]." ".$lunarRow[4]) + $offset;
								$dateLunar = date($dateTimeFormat,$checkedLunarDate);
								$type = $lunarRow[8];
								if($type=="T"){
									$type = lang('total eclipse','c');
								}
								if($type=="P"){
									$type = lang('partial eclipse','c');
								}
								if($type=="N"){
									$type = lang('penumbral eclipse','c');
								}
								if($type=="Tm"){
									$type = lang('total eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
								}
								if($type=="Pm"){
									$type = lang('partial eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
								}
								if($type=="Nm"){
									$type = lang('penumbral eclipse','c')."<br>".lang("middle eclipse of Saros series",'l');
								}
								if($type=="T+"){
									$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes north of shadow axis",'c');
								}
								if($type=="T-"){
									$type = lang('total eclipse','c')."<br>".lang('central total eclipse','l')."<br>".lang("Moon center passes south of shadow axis",'c');
								}
								if($type=="Tx"){
									$type = lang('total eclipse','c')."<br>".lang("total penumbral lunar eclipse",'l');
								}
								if($type=="Nb"){
									$type = lang('penumbral eclipse','c')."<br>".lang("first penumbral eclipse in series",'l');
								}
								if($type=="Ne"){
									$type = lang('penumbral eclipse','c')."<br>".lang("last penumbral eclipse in series",'l');
								}
								if($type[0]=="T"){
									$typeImg = "totalL.png";
								}
								if($type[0]=="P"){
									$typeImg = "partialL.png";
								}
								if($type[0]=="N"){
									$typeImg = "penumbralL.png";
								}
							?>
								<td>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td>
												<img src='icons/<?php echo $typeImg?>' style='width:90px'>
											</td>
											<td>
												<h3 style="text-align:center"><?php echo lang('lunar eclipse','c')?></h3>
												<span style="font-weight:bold;font-size:1.3;font-variant:small-caps"><?php echo $type?></span>
												<br>
												<?php echo $dateLunar;?>
											</td>
										</tr>
									</table>
								</td>
							<?php
							}
						}
						if(count($solarResults)>0){
							for($i=0;$i<count($solarResults);$i++){
								$solarData = explode(";",$solarResults[$i]);
								$checkedSolarDate = strtotime($solarData[2]."-".$solarData[3]."-".$solarData[4]." ".$solarData[5]) + $offset;
								$dateSolar = date($dateTimeFormat,$checkedSolarDate);
								$type = $solarData[9];
								if($type[0]=="P"){
									$type = lang('partial eclipse','c');
									$typeImg = "partial.png";
								}
								if($type[0]=="A"){
									$type = lang('annular eclipse','c');
									$typeImg = "annular.png";
								}
								if($type[0]=="T"){
									$type = lang('total eclipse','c');
									$typeImg = "total.png";
								}
								if($type[0]=="H"){
									$type = lang('hybrid eclipse','c');
									$typeImg = "hybrid";
								}
								if(strlen($type)>1){
									if(substr($type,1,1)=="m"){
										$type = $type."<br>".lang('middle eclipse of Saros series','l');
									}
									if(substr($type,1,1)=="n"){
										$type = $type."<br>".lang('central eclipse with no northern limit','l');
									}
									if(substr($type,1,1)=="s"){
										$type = $type."<br>".lang('central eclipse with no southern limit','l');
									}
									if(substr($type,1,1)=="+"){
										$type = $type."<br>".lang("non-central eclipse with no northern limit",'l');
									}
									if(substr($type,1,1)=="-"){
										$type = $type."<br>".lang("non-central eclipse with no southern limit",'l');
									}
									if(substr($type,1,1)=="2"){
										$type = $type."<br>".lang("hybrid path begins total and ends annular",'l');
									}
									if(substr($type,1,1)=="3"){
										$type = $type."<br>".lang("hybrid path begins annular and ends total",'l');
									}
									if(substr($type,1,1)=="b"){
										$type = $type."<br>".lang("first eclipse in series",'l');
									}
									if(substr($type,1,1)=="e"){
										$type = $type."<br>".lang("last eclipse in series",'l');
									}
								}
								$durationRaw = $solarData[17];
								$durationM = substr($durationRaw,0,2);
								$durationS = substr($durationRaw,3,2);
								$duration = $durationM * 60 + $durationS;
							?>
								<td>
									<table style="width:98%;margin:0 auto">
										<tr>
											<td>
												<img src='icons/<?php echo $typeImg?>' style='width:90px'>
											</td>
											<td>
												<h3 style="text-align:center"><?php echo lang('solar eclipse','c')?></h3>
												<span style="font-weight:bold;font-size:1.3;font-variant:small-caps"><?php echo $type?></span>
												<br>
												<?php echo $dateSolar;?>
												<br>
												<?php echo lang('duration','c')?>: <?php echo $duration?>s
											</td>
										</tr>
									</table>
								</td>
							<?php
							}
						}
						?>
					</tr>
				</table>
			</div>
			<br>
			
			<table style="width:98%;margin:0 auto">
				<tr>
					<td style="width:50%">
						<div style="width:98%;text-align:left;margin:0 auto;font-variant:small-caps">
							<b><?php echo lang("data source",'c')?></b><br>darksky.net, <?php echo $sources?>, yr.no<br><span style="font-size:0.8em">METAR: <?php echo $metarStations?></span>
						</div>
					</td>
					<td>
						<div style="width:98%;text-align:right;margin:0 auto;font-variant:small-caps">
							<b><?php echo lang('forecast','c')?>:</b> <?php echo date($dateTimeFormat,$forecastLoadedTime);?><br>
							<b><?php echo lang('station data','c')?>:</b> <?php echo date($dateTimeFormat,$stationDataLoadedTime);?> <img src="<?php echo $pageURL.$path?>icons/update.png" class="updateIcon" onclick="$('#reloadingCacheSpan').show();window.location='index.php?reloadCache=true'">
							<br><span id="reloadingCacheSpan" style="display:none">Reloading cache...</span>
						</div>
					</td>
				</tr>
			</table>
			
			<br>
		</div>
		<?php include($baseURL."footer.php")?>
		<?php include("../../css/highcharts.php");?>
		<script>
			$(document).ready(function(){
				var tdWidth = $("#oneRecordTD").width() - 10;
				$('.sparklinesT').sparkline('html',
					{
						type: 'line',
						width: tdWidth,
						height: '30px',
						lineColor: '#ffffff',
						fillColor: null,
						lineWidth: 2,
						spotColor: false,
						spotRadius: 4,
						minSpotColor: '#2693ff',
						maxSpotColor: '#8c0000',
						highlightSpotColor: null,
						highlightLineColor: null,
						drawNormalOnTop: false,
						disableInteraction: false,
					}
				);
				$('.sparklinesH').sparkline('html',
					{
						type: 'line',
						width: tdWidth,
						height: '30px',
						lineColor: '#ffffff',
						fillColor: null,
						lineWidth: 2,
						spotColor: false,
						spotRadius: 4,
						minSpotColor: '#ffe599',
						maxSpotColor: '#00d900',
						highlightSpotColor: null,
						highlightLineColor: null,
						drawNormalOnTop: false,
						disableInteraction: false,
					}
				);
				$('.sparklinesP').sparkline('html',
					{
						type: 'line',
						width: tdWidth,
						height: '30px',
						lineColor: '#ffffff',
						fillColor: null,
						lineWidth: 2,
						spotColor: false,
						spotRadius: 4,
						minSpotColor: '#ff8000',
						maxSpotColor: '#a300d9',
						highlightSpotColor: null,
						highlightLineColor: null,
						drawNormalOnTop: false,
						disableInteraction: false,
					}
				);
				$('.sparklinesW').sparkline('html',
					{
						type: 'line',
						width: tdWidth,
						height: '30px',
						lineColor: '#ffffff',
						fillColor: null,
						lineWidth: 2,
						spotColor: false,
						spotRadius: 4,
						minSpotColor: '#999999',
						maxSpotColor: '#a300d9',
						highlightSpotColor: null,
						highlightLineColor: null,
						drawNormalOnTop: false,
						disableInteraction: false,
					}
				);
				$('.sparklinesR').sparkline('html',
					{
						type: 'bar',
						width: tdWidth,
						height: '30px',
						barColor: '#4c79ff',
						zeroColor: '#fff',
						barWidth: 4,
						barSpacing: 1,
						drawNormalOnTop: false,
						disableInteraction: false,
					}
				);
				$(".sectionDivOpener").click(function(){
					$(".sectionDiv").hide();
					divId = $(this).attr('data-id');
					$("#"+divId+"Div").slideDown(700);
					$(".sectionDivOpener").css("color","#<?php echo $color_schemes[$design2]['200']?>");
					$(this).css("color","#fff");
					if(divId=="shortTerm"){
						shortTermGraphs();
					}
					if(divId=="longTerm"){
						longTermGraphs();
					}
					if(divId=="station"){
						stationGraphs();
					}
				});
				$(".yearSpan").css("padding","2px");
				$(".yearHighlighter").click(function(){
					$(".yearSpan").css("background","none");
					$(".yearSpan").css("padding","2px");
					$(".yearSpan").css("color","white");
					selY = $(this).val();
					$(".year"+selY).css("background","#<?php echo $color_schemes[$design2]['200']?>");
					$(".year"+selY).css("color","black");
					$(".year"+selY).css("padding","2px");
				})
				$(".table").tablesorter();
				function shortTermGraphs(){
					$('#todayGraph').highcharts({
						chart: {
							zoomType: "x",
						},
						title: {
							text: '',
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: [
								<?php 
									foreach($hourly['data']['today'] as $point){
										echo "'";
										if($prefferedTime=="12h"){
											echo date("g A",$point['time']);
										}
										else{
											echo date("G",$point['time'])." ".lang('hAbbr','l');
										}
										echo "',";
									}
								?>
							],
							plotBands: [
								<?php 
									$i = -0.5;
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists('icon',$point)){
												$temporaryIcon = "<img src='icons/".$point['icon'].".png' style='width:50px' class='forecastGraphIcon'>";
											}
											else{
												$temporaryIcon = "";
											}
								?>
											{
												color: null,
												from: <?php echo $i?>,
												to: <?php echo ($i+1)?>,
												zIndex: -1,
												label: {
													text: "<?php echo $temporaryIcon?>",
													verticalAlign: 'top',
													useHTML: true,
													y: 0
												}
											},
								<?php
										$i++;
									}
								?>
							],
						},
						yAxis: [
							{
								title: {
									text: '<?php echo lang('temperature','c')?> (°<?php echo $displayTempUnits?>)'
								},
							},
							{
								title: {
									text: '<?php echo lang('rain rate','c')?> (<?php echo $displayRainUnits?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('pressure','c')?> (<?php echo unitformatter($displayPressUnits)?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('humidity','c')?> (%)'
								},
								min: 0,
								max: 100
							},
							{
								title: {
									text: '<?php echo lang('wind speed','c')?> (<?php echo unitformatter($displayWindUnits)?>)'
								},
								opposite: true
							},
						],
						tooltip: {
							shared: true
						},
						series: [
							{
								name: '<?php echo lang('rain rate','c')?>',
								type: 'column',
								color: 'rgba(0, 102, 255,0.5)',
								borderColor: '#fff',
								zIndex: 2,
								borderWidth: 1,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("precipIntensity",$point)){
												echo number_format($point['precipIntensity'],$decimalsR,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 1,
							},
							{
								name: '<?php echo lang('temperature','c')?>',
								type: 'spline',
								color: '#fff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("temperature",$point)){
												echo number_format($point['temperature'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							
							{
								name: '<?php echo lang('pressure','c')?>',
								type: 'spline',
								color: '#ffa64c',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("pressure",$point)){
												echo number_format($point['pressure'],$decimalsP,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 2,
							},
							{
								name: '<?php echo lang('dewpoint','c')?>',
								type: 'spline',
								color: '#4ca6ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("dewPoint",$point)){
												echo number_format($point['dewPoint'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('apparent temperature','c')?>',
								type: 'spline',
								color: '#ff9999',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("apparentTemperature",$point)){
												echo number_format($point['apparentTemperature'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('humidity','c')?>',
								type: 'spline',
								color: '#b3ff99',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("humidity",$point)){
												echo $point['humidity'].",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 3,
							},
							{
								name: '<?php echo lang('wind speed','c')?>',
								type: 'spline',
								color: '#dc73ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['today'] as $point){
											if(array_key_exists("windSpeed",$point)){
												if(array_key_exists("windBearing",$point)){
													$dirImg = strtolower(windAbb($point['windBearing']));
													echo "{y:".$point['windSpeed'].",marker:{symbol:'url(".$pageURL.$path."icons/winddir/".$dirImg.".png)',width:30,height:30}},";
												}
												else{
													echo "{y:".$point['windSpeed']."},";
												}
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 4,
							}
						]
					});
					$('#tomorrowGraph').highcharts({
						chart: {
							zoomType: "x",
						},
						title: {
							text: '',
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: [
								<?php 
									foreach($hourly['data']['tomorrow'] as $point){
										echo "'";
										if($prefferedTime=="12h"){
											echo date("g A",$point['time']);
										}
										else{
											echo date("G",$point['time'])." ".lang('hAbbr','l');
										}
										echo "',";
									}
								?>
							],
							plotBands: [
								<?php 
									$i = -0.5;
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists('icon',$point)){
												$temporaryIcon = "<img src='icons/".$point['icon'].".png' style='width:50px' class='forecastGraphIcon'>";
											}
											else{
												$temporaryIcon = "";
											}
								?>
											{
												color: null,
												//zIndex: 1,
												from: <?php echo $i?>,
												to: <?php echo ($i+1)?>,
												label: {
													text: "<?php echo $temporaryIcon?>",
													verticalAlign: 'top',
													useHTML: true,
													y: 25
												}
											},
								<?php
										$i++;
									}
								?>
							],
						},
						yAxis: [
							{
								title: {
									text: '<?php echo lang('temperature','c')?> (°<?php echo $displayTempUnits?>)'
								},
							},
							{
								title: {
									text: '<?php echo lang('rain rate','c')?> (<?php echo $displayRainUnits?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('pressure','c')?> (<?php echo unitformatter($displayPressUnits)?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('humidity','c')?> (%)'
								},
								min: 0,
								max: 100
							},
							{
								title: {
									text: '<?php echo lang('wind speed','c')?> (<?php echo unitformatter($displayWindUnits)?>)'
								},
								opposite: true
							},
						],
						tooltip: {
							shared: true
						},
						series: [
							{
								name: '<?php echo lang('rain rate','c')?>',
								type: 'column',
								color: 'rgba(0, 102, 255,0.5)',
								borderColor: '#fff',
								borderWidth: 1,
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("precipIntensity",$point)){
												echo number_format($point['precipIntensity'],$decimalsR,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 1,
							},
							{
								name: '<?php echo lang('temperature','c')?>',
								type: 'spline',
								color: '#fff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("temperature",$point)){
												echo number_format($point['temperature'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							
							{
								name: '<?php echo lang('pressure','c')?>',
								type: 'spline',
								color: '#ffa64c',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("pressure",$point)){
												echo number_format($point['pressure'],$decimalsP,'.',"").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 2,
							},
							{
								name: '<?php echo lang('dewpoint','c')?>',
								type: 'spline',
								color: '#4ca6ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("dewPoint",$point)){
												echo number_format($point['dewPoint'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('apparent temperature','c')?>',
								type: 'spline',
								color: '#ff9999',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("apparentTemperature",$point)){
												echo number_format($point['apparentTemperature'],1,'.','').",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('humidity','c')?>',
								type: 'spline',
								color: '#b3ff99',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("humidity",$point)){
												echo $point['humidity'].",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 3,
							},
							{
								name: '<?php echo lang('wind speed','c')?>',
								type: 'spline',
								color: '#dc73ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($hourly['data']['tomorrow'] as $point){
											if(array_key_exists("windSpeed",$point)){
												if(array_key_exists("windBearing",$point)){
													$dirImg = strtolower(windAbb($point['windBearing']));
													echo "{y:".$point['windSpeed'].",marker:{symbol:'url(".$pageURL.$path."icons/winddir/".$dirImg.".png)',width:30,height:30}},";
												}
												else{
													echo "{y:".$point['windSpeed']."},";
												}
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 4,
							}
						]
					});
					$( ".forecastGraphIcon" ).parent().css( "z-index", "-1" );
				} // end short term graphs
				function longTermGraphs(){
					$('#longtermGraph').highcharts({
						chart: {
							zoomType: "x",
						},
						title: {
							text: '',
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: [
								<?php 
									foreach($daily['data'] as $point){
										echo "'";
										echo $weekDays[date("w",$point['time'])];
										echo "',";
									}
								?>
							],
							plotBands: [
								<?php 
									$i = -0.5;
										foreach($daily['data'] as $point){
											if(array_key_exists('icon',$point)){
												$temporaryIcon = "<img src='icons/".$point['icon'].".png' style='width:50px' class='forecastGraphIcon'>";
											}
											else{
												$temporaryIcon = "";
											}
								?>
											{
												color: null,
												from: <?php echo $i?>,
												to: <?php echo ($i+1)?>,
												label: {
													text: "<?php echo $temporaryIcon?>",
													verticalAlign: 'top',
													useHTML: true,
													y: 25
												}
											},
								<?php
										$i++;
									}
								?>
							],
						},
						yAxis: [
							{
								title: {
									text: '<?php echo lang('temperature','c')?> (°<?php echo $displayTempUnits?>)'
								},
							},
							{
								title: {
									text: '<?php echo lang('precipitation','c')?> (<?php echo $displayRainUnits?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('pressure','c')?> (<?php echo unitformatter($displayPressUnits)?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('humidity','c')?> (%)'
								},
								min: 0,
								max: 100
							},
							{
								title: {
									text: '<?php echo lang('wind speed','c')?> (<?php echo unitformatter($displayWindUnits)?>)'
								},
								opposite: true
							},
						],
						tooltip: {
							shared: true
						},
						series: [
							{
								name: '<?php echo lang('precipitation','c')?>',
								type: 'column',
								color: 'rgba(0, 102, 255,0.5)',
								borderColor: '#fff',
								borderWidth: 1,
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("precipIntensity",$point)){
												echo number_format($point['precipIntensity']*24,$decimalsR,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 1,
							},
							{
								name: '<?php echo lang('temperature','c')?>',
								type: 'areasplinerange',
								color: 'rgba(255,255,255,0.4)',
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("temperatureMax",$point)){
												echo "[".number_format($point['temperatureMin'],1,".","").",".number_format($point['temperatureMax'],1,".","")."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							
							{
								name: '<?php echo lang('pressure','c')?>',
								type: 'spline',
								color: '#ffa64c',
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("pressure",$point)){
												echo number_format($point['pressure'],$decimalsP,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 2,
							},
							{
								name: '<?php echo lang('dewpoint','c')?>',
								type: 'spline',
								color: '#4ca6ff',
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("dewPoint",$point)){
												echo number_format($point['dewPoint'],1,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('apparent temperature','c')?>',
								type: 'areasplinerange',
								color: 'rgba(255, 204, 204,0.4)',
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("apparentTemperatureMax",$point)){
												echo "[".number_format($point['apparentTemperatureMin'],1,".","").",".number_format($point['apparentTemperatureMax'],1,'.','')."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('humidity','c')?>',
								type: 'spline',
								color: '#b3ff99',
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("humidity",$point)){
												echo $point['humidity'].",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 3,
							},
							{
								name: '<?php echo lang('wind speed','c')?>',
								type: 'spline',
								color: '#dc73ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($daily['data'] as $point){
											if(array_key_exists("windSpeed",$point)){
												if(array_key_exists("windBearing",$point)){
													$dirImg = strtolower(windAbb($point['windBearing']));
													echo "{y:".$point['windSpeed'].",marker:{symbol:'url(".$pageURL.$path."icons/winddir/".$dirImg.".png)',width:30,height:30}},";
												}
												else{
													echo "{y:".$point['windSpeed']."},";
												}
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 4,
							}
						]
					});
					$( ".forecastGraphIcon" ).parent().css( "z-index", "-1" );
				} // end long term graphs
				function stationGraphs(){
					$('#stationGraph').highcharts({
						chart: {
							zoomType: "x",
						},
						title: {
							text: '',
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: [
								<?php 
									foreach($stationData as $key=>$value){
										echo "'";
										echo date("d",$key)." ".$monthNames[date('n',$key)];
										echo "',";
									}
								?>
							]
						},
						yAxis: [
							{
								title: {
									text: '<?php echo lang('temperature','c')?> (°<?php echo $displayTempUnits?>)'
								},
							},
							{
								title: {
									text: '<?php echo lang('precipitation','c')?> (<?php echo $displayRainUnits?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('pressure','c')?> (<?php echo unitformatter($displayPressUnits)?>)'
								},
								opposite: true
							},
							{
								title: {
									text: '<?php echo lang('humidity','c')?> (%)'
								},
								min: 0,
								max: 100
							},
							{
								title: {
									text: '<?php echo lang('wind speed','c')?> (<?php echo unitformatter($displayWindUnits)?>)'
								},
								opposite: true
							},
						],
						tooltip: {
							shared: true
						},
						series: [
							{
								name: '<?php echo lang('precipitation','c')?>',
								type: 'column',
								color: 'rgba(0, 102, 255,0.5)',
								borderColor: '#fff',
								borderWidth: 1,
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("R",$point)){
												echo number_format(convertR($point['R']),$decimalsR,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 1,
							},
							{
								name: '<?php echo lang('temperature','c')?>',
								type: 'areasplinerange',
								color: 'rgba(255,255,255,0.4)',
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("maxT",$point)){
												echo "[".number_format(convertT($point['minT']),1,".","").",".number_format(convertT($point['maxT']),1,".","")."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},						
							{
								name: '<?php echo lang('pressure','c')?>',
								type: 'spline',
								color: '#ffa64c',
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("P",$point)){
												echo number_format(convertP($point['P']),$decimalsP,".","").",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 2,
							},
							{
								name: '<?php echo lang('apparent temperature','c')?>',
								type: 'areasplinerange',
								color: 'rgba(255, 204, 204,0.4)',
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("minA",$point)){
												echo "[".number_format(convertT($point['minA']),1,".","").",".number_format(convertT($point['maxA']),1,'.','')."],";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 0,
							},
							{
								name: '<?php echo lang('humidity','c')?>',
								type: 'spline',
								color: '#b3ff99',
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("H",$point)){
												echo $point['H'].",";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 3,
							},
							{
								name: '<?php echo lang('wind speed','c')?>',
								type: 'spline',
								color: '#dc73ff',
								zIndex: 2,
								data: [
									<?php 
										foreach($stationData as $point){
											if(array_key_exists("W",$point)){
												echo "{y:".$point['W']."},";
											}
											else{
												echo "null,";
											}
										}
									?>
								],
								yAxis: 4,
							}
						]
					});
				} // end station graphs
				$('.resizer').resizable({
					resize: function() {
						selectedDiv = $(this).find(".graphDiv");
						chart = selectedDiv.highcharts();
						chart.setSize(
							this.offsetWidth - 50, 
							this.offsetHeight - 50,
							false
						);
					}
				});
			})
			$(window).load(function(){
				
				var totalMaxHeight = 0;
				$('.hourDivToday').each(function(index) {
					if($(this).outerHeight(true)>totalMaxHeight){
						totalMaxHeight = $(this).outerHeight();
					}
				});
				$(".hourDivToday").height(totalMaxHeight);
				
				var totalMaxHeight = 0;
				$('.hourDivTomorrow').each(function(index) {
					if($(this).outerHeight(true)>totalMaxHeight){
						totalMaxHeight = $(this).outerHeight();
					}
				});
				$(".hourDivTomorrow").height(totalMaxHeight);
				
				var totalMaxHeight = 0;
				$('.hourDivLongTerm').each(function(index) {
					if($(this).outerHeight(true)>totalMaxHeight){
						totalMaxHeight = $(this).outerHeight();
					}
				});
				$(".hourDivLongTerm").height(totalMaxHeight);
				var totalMaxHeight = 0;
				$('.hourDivStation').each(function(index) {
					if($(this).outerHeight(true)>totalMaxHeight){
						totalMaxHeight = $(this).outerHeight();
					}
				});
				$(".hourDivStation").height(totalMaxHeight);
				$("#todayDetails").hide();
				$("#tomorrowDetails").hide();
				$("#longtermDetails").hide();
				$("#stationDetailsDiv").hide();
				$(".sectionDiv").hide();
			})
		</script>
	</body>
</html>