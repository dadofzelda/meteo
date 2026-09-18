<?php 
    include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	if($fIOKey==""){
		die("No forecast IO key specified, please get your free API key at darksky.net and insert it in the Main settings.");
	}
	
	if(!is_dir("cache")){
		mkdir("cache");
	}
	
	// error_reporting(E_ALL);
	
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
	
	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
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

	function dateLimiter($arr=array()){
		if(count($arr)>3){
			return "> 3 ".lang("cases",'l');
		}
		else{
			return implode(", ",$arr);
		}
	}

    // PDF
    include($baseURL."scripts/mpdf60/mpdf.php");
    if($defaultPaperSize=="letter"){
        $mpdf = new mPDF('','Letter');
    }
    else{
        $mpdf = new mPDF();
    }
    $mpdf->SetTitle(lang("outlook",'c'));
    $mpdf->SetAuthor("Meteotemplate");
    $mpdf->SetCreator("Meteotemplate");
    /*$mpdf->SetHeader('
        <table style="width:100%" cellspacing="0">
			<tr>
                <td style="text-align:center;color:#'.$color_schemes[$design2]['900'].'">
                     <h1 style="font-size:1.5em">'.lang("forecast",'w').'</h1>
                </td>
            </tr>
		</table>
    ');*/
    $mpdf->setFooter('<span style="color:black;font-style:normal;font-size:0.9em">'.$pageURL.$path.'</span>||<span style="color:black;font-style:normal">Meteotemplate</span>');

    $mpdf->WriteHTML('
		<style>
			@page{
				background: linear-gradient(top, #'.$color_schemes[$design]['100'].', #'.$color_schemes[$design]['300'].');
			}
			.table{
				width: 100%;
                font-size:9pt;
				border-radius:5pt 5pt;
			}
            .table td{
                padding: 5px;
                text-align:center;
            }
            .table th{
                padding: 5px;
                text-align:center;
                color:white;
            }
			.table tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['200'].';
			}
			.table tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
			.stationExtremeTD{
				width: 12.5%;
			}
			.tdLineBorder{
				border-bottom: 2px solid #'.$color_schemes[$design2]['900'].'
			}
			.tableAlmanac{
				width: 100%;
            	font-size:9pt;
				border-radius: 5pt 5pt;
			}
			.tableAlmanac td{
                padding: 5px;
                text-align:center;
            }
            .tableAlmanac th{
                padding: 5px;
                text-align:center;
                color:white;
            }
			.tableAlmanac tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['100'].';
			}
			.tableAlmanac tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
		</style>
	');

    $mpdf->WriteHTML('<h2>'.lang('short-term forecast','c').'</h2>');

    if(count($hourly['data']['today'])>0){
        $mpdf->WriteHTML('
            <h2 style="text-align:center">'.lang('today','c').'</h2>
        ');
        foreach($hourly['data']['today'] as $hour){
            $todayDate = date($dateFormat,$hour['time']);
        }
        $mpdf->WriteHTML('
            <h3 style="text-align:center">'.$todayDate.'</h3>
        ');
        $mpdf->WriteHTML('
            <table class="table" cellspacing="0">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <img src="../../icons/temp.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                        <th>
                            <img src="../../icons/humidity.png" style="width:25px"><br>%
                        </th>
                        <th>
                            <img src="../../icons/pressure.png" style="width:25px"><br>'.unitFormatter($displayPressUnits).'
                        </th>
                        <th>
                            <img src="../../icons/wind.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
                        <th>
                            <img src="../../icons/rain.png" style="width:25px"><br>'.unitFormatter($displayRainUnits)."/".lang('hAbbr','l').'
                        </th>
                        <th>
                            <img src="../../icons/apparent.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                    </tr>
                </thead>
        ');
        foreach($hourly['data']['today'] as $hour){
            $mpdf->WriteHTML('
                <tr>
                    <td>
            ');
                        if(array_key_exists("time",$hour)){
                            if($prefferedTime=="12h"){
								$mpdf->WriteHTML(date("g A",$hour['time']));
                            }
                            else{
								$mpdf->WriteHTML(date("G",$hour['time'])." ".lang('hAbbr','l'));
							}
                        }
            $mpdf->WriteHTML('
                    </td>
                    <td>
            ');
                        if(array_key_exists("icon",$hour)){
                            $mpdf->WriteHTML('<img src="icons/'.$hour['icon'].'.png" style="width:30px">');
                        }
                        else{
                            $mpdf->WriteHTML('<img src="icons/na.png" style="width:30px">');
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td style="text-align:left">
            ');
                        if(array_key_exists("summary",$hour)){
                            $mpdf->WriteHTML($hour['summary']);
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("temperature",$hour)){
                            $mpdf->WriteHTML(number_format($hour['temperature'],1,".",""));
                        }
            $mpdf->WriteHTML(' 
                    </td>
                    <td>
            ');
                        if(array_key_exists("humidity",$hour)){
                            $mpdf->WriteHTML(number_format($hour['humidity'],0,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("pressure",$hour)){
                            $mpdf->WriteHTML(number_format($hour['pressure'],$decimalsP,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("windSpeed",$hour)){
                            $mpdf->WriteHTML(number_format($hour['windSpeed'],1,".",""));
                        }
                        if(array_key_exists("windBearing",$hour)){
                            $mpdf->WriteHTML(' <img src="../../icons/pdf/winddir/'.strtolower(windAbb($hour['windBearing'])).'.png" style="width:18px">');
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("precipIntensity",$hour)){
                            $mpdf->WriteHTML(number_format($hour['precipIntensity'],$decimalsR,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("apparentTemperature",$hour)){
                            $mpdf->WriteHTML(number_format($hour['apparentTemperature'],1,".",""));
                        }
            $mpdf->WriteHTML(' 
                    </td>
                </tr>
            ');
        }
        $mpdf->WriteHTML('</table>');
    }

    if(count($hourly['data']['today'])>0){
        $mpdf->WriteHTML('
            <pagebreak>
            <h2 style="text-align:center">'.lang('tomorrow','c').'</h2>
        ');
        foreach($hourly['data']['tomorrow'] as $hour){
            $tomorrowDate = date($dateFormat,$hour['time']);
        }
        $mpdf->WriteHTML('
            <h3 style="text-align:center">'.$tomorrowDate.'</h3>
        ');
        $mpdf->WriteHTML('
            <table class="table" cellspacing="0">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <img src="../../icons/temp.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                        <th>
                            <img src="../../icons/humidity.png" style="width:25px"><br>%
                        </th>
                        <th>
                            <img src="../../icons/pressure.png" style="width:25px"><br>'.unitFormatter($displayPressUnits).'
                        </th>
                        <th>
                            <img src="../../icons/wind.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
                        <th>
                            <img src="../../icons/rain.png" style="width:25px"><br>'.unitFormatter($displayRainUnits)."/".lang('hAbbr','l').'
                        </th>
                        <th>
                            <img src="../../icons/apparent.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                    </tr>
                </thead>
        ');
        foreach($hourly['data']['tomorrow'] as $hour){
            $mpdf->WriteHTML('
                <tr>
                    <td>
            ');
                        if(array_key_exists("time",$hour)){
                            if($prefferedTime=="12h"){
								$mpdf->WriteHTML(date("g A",$hour['time']));
                            }
                            else{
								$mpdf->WriteHTML(date("G",$hour['time'])." ".lang('hAbbr','l'));
							}
                        }
            $mpdf->WriteHTML('
                    </td>
                    <td>
            ');
                        if(array_key_exists("icon",$hour)){
                            $mpdf->WriteHTML('<img src="icons/'.$hour['icon'].'.png" style="width:30px">');
                        }
                        else{
                            $mpdf->WriteHTML('<img src="icons/na.png" style="width:30px">');
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td style="text-align:left">
            ');
                        if(array_key_exists("summary",$hour)){
                            $mpdf->WriteHTML($hour['summary']);
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("temperature",$hour)){
                            $mpdf->WriteHTML(number_format($hour['temperature'],1,".",""));
                        }
            $mpdf->WriteHTML(' 
                    </td>
                    <td>
            ');
                        if(array_key_exists("humidity",$hour)){
                            $mpdf->WriteHTML(number_format($hour['humidity'],0,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("pressure",$hour)){
                            $mpdf->WriteHTML(number_format($hour['pressure'],$decimalsP,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("windSpeed",$hour)){
                            $mpdf->WriteHTML(number_format($hour['windSpeed'],1,".",""));
                        }
                        if(array_key_exists("windBearing",$hour)){
                            $mpdf->WriteHTML(' <img src="../../icons/pdf/winddir/'.strtolower(windAbb($hour['windBearing'])).'.png" style="width:18px">');
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("precipIntensity",$hour)){
                            $mpdf->WriteHTML(number_format($hour['precipIntensity'],$decimalsR,".",""));
                        }
            $mpdf->WriteHTML('     
                    </td>
                    <td>
            ');
                        if(array_key_exists("apparentTemperature",$hour)){
                            $mpdf->WriteHTML(number_format($hour['apparentTemperature'],1,".",""));
                        }
            $mpdf->WriteHTML(' 
                    </td>
                </tr>
            ');
        }
        $mpdf->WriteHTML('</table>');
    }
    if(count($daily['data'])>0){
        $mpdf->WriteHTML('
            <pagebreak>
            <h2 style="text-align:center">'.lang('long-term forecast','c').'</h2>
        ');
        $mpdf->WriteHTML('
            <table class="table" cellspacing="0" style="font-size:9pt" cellpadding="4">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <img src="../../icons/temp.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                        <th>
                            <img src="../../icons/humidity.png" style="width:25px"><br>%
                        </th>
                        <th>
                            <img src="../../icons/pressure.png" style="width:25px"><br>'.unitFormatter($displayPressUnits).'
                        </th>
                        <th colspan="2">
                            <img src="../../icons/wind.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
                        <th>
                            <img src="../../icons/rain.png" style="width:25px"><br>'.unitFormatter($displayRainUnits)."/".lang('hAbbr','l').'
                        </th>
                    </tr>
                </thead>
        ');
                foreach($daily['data'] as $day){
                    $mpdf->WriteHTML('
                        <tr>
                            <td style="text-align:left;font-weight:bold">
                    ');
                                if(array_key_exists("time",$day)){
                                    $mpdf->WriteHTML($weekDays[date("w",$day['time'])]);
                                }
                    $mpdf->WriteHTML('
                            </td>
                            <td>
                    ');
                                if(array_key_exists("icon",$day)){
                                    $mpdf->WriteHTML('<img src="icons/'.$day['icon'].'.png" style="width:30px">');
                                }
                                else{
                                    $mpdf->WriteHTML('<img src="icons/na.png" style="width:30px">');
                                }
                    $mpdf->WriteHTML('     
                            </td>
                            <td style="text-align:left;width:33%">
                    ');
                                if(array_key_exists("summary",$day)){
                                    $mpdf->WriteHTML($day['summary']);
                                }
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                if(array_key_exists("temperatureMax",$day)){
                                    $mpdf->WriteHTML(number_format($day['temperatureMax'],1,".",""));
                                    $mpdf->WriteHTML(' / ');
                                    $mpdf->WriteHTML(number_format($day['temperatureMin'],1,".",""));
                                }
                    $mpdf->WriteHTML(' 
                            </td>
                            <td>
                    ');
                                if(array_key_exists("humidity",$day)){
                                    $mpdf->WriteHTML(number_format($day['humidity'],0,".",""));
                                }
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                if(array_key_exists("pressure",$day)){
                                    $mpdf->WriteHTML(number_format($day['pressure'],$decimalsP,".",""));
                                }
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                if(array_key_exists("windSpeed",$day)){
                                    $mpdf->WriteHTML(number_format($day['windSpeed'],1,".",""));
                                }
                    $mpdf->WriteHTML('     
                            </td>
							<td>
                    ');
                                if(array_key_exists("windBearing",$day)){
                                    $mpdf->WriteHTML(' <img src="../../icons/pdf/winddir/'.strtolower(windAbb($day['windBearing'])).'.png" style="width:18px">');
                                }
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                if(array_key_exists("precipIntensity",$day)){
                                    $mpdf->WriteHTML(number_format($day['precipIntensity'],$decimalsR,".",""));
                                }
                    $mpdf->WriteHTML('     
                            </td>
                        </tr>
                    ');
                }
            $mpdf->WriteHTML('</table>');
        }  
		$mpdf->WriteHTML('
            <h2 style="text-align:center">'.lang('station averages','c').'</h2>
        ');
		$mpdf->WriteHTML('
            <table class="table" cellspacing="0" style="font-size:9pt" cellpadding="4">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th>
                            <img src="../../icons/temp.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                        <th>
                            <img src="../../icons/humidity.png" style="width:25px"><br>%
                        </th>
                        <th>
                            <img src="../../icons/pressure.png" style="width:25px"><br>'.unitFormatter($displayPressUnits).'
                        </th>
                        <th>
                            <img src="../../icons/wind.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
                        <th>
                            <img src="../../icons/rain.png" style="width:25px"><br>'.unitFormatter($displayRainUnits)."/".lang('hAbbr','l').'
                        </th>
						<th>
                            <img src="../../icons/apparent.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                    </tr>
                </thead>
        ');
			foreach($stationData as $key=>$value){
                    $mpdf->WriteHTML('
                        <tr>
                            <td style="text-align:left;font-weight:bold">
                    ');
                             	$mpdf->WriteHTML(date("j",$key)." ".$monthNames[date('n',$key)]);
                    $mpdf->WriteHTML('
                            </td>
                            <td>
                    ');
                                $mpdf->WriteHTML(number_format(convertT($value['maxT']),1,".","")." / ".number_format(convertT($value['minT']),1,".",""));
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                               $mpdf->WriteHTML($value['H']);
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                $mpdf->WriteHTML(number_format(convertP($value['P']),$decimalsP,".",""));
                    $mpdf->WriteHTML(' 
                            </td>
                            <td>
                    ');
                                $mpdf->WriteHTML(number_format(convertW($value['W']),1,".",""));
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                                $mpdf->WriteHTML(number_format(convertR($value['R']),$decimalsR,".",""));
                    $mpdf->WriteHTML('     
                            </td>
                            <td>
                    ');
                               $mpdf->WriteHTML(number_format(convertT($value['maxA']),1,".","")." / ".number_format(convertT($value['minA']),1,".",""));
                    $mpdf->WriteHTML('
                        </tr>
                    ');
                }
            $mpdf->WriteHTML('</table>');

		$mpdf->WriteHTML('
			<pagebreak>
            <h2 style="text-align:center">'.lang('station extremes','c').'</h2>
    	');
		$mpdf->WriteHTML('
            <table class="table" cellspacing="0" style="font-size:9pt" cellpadding="1">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th>
                            <img src="../../icons/temp.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                        <th>
                            <img src="../../icons/humidity.png" style="width:25px"><br>%
                        </th>
                        <th>
                            <img src="../../icons/pressure.png" style="width:25px"><br>'.unitFormatter($displayPressUnits).'
                        </th>
                        <th>
                            <img src="../../icons/wind.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
						<th>
                            <img src="../../icons/gust.png" style="width:25px"><br>'.unitFormatter($displayWindUnits).'
                        </th>
                        <th>
                            <img src="../../icons/rain.png" style="width:25px"><br>'.unitFormatter($displayRainUnits)."/".lang('hAbbr','l').'
                        </th>
						<th>
                            <img src="../../icons/apparent.png" style="width:25px"><br>'.unitFormatter($displayTempUnits).'
                        </th>
                    </tr>
                </thead>
        ');
			foreach($stationData as $key=>$value){
				$mpdf->WriteHTML('
					<tr>
						<td style="text-align:left;padding-left:2px;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'" rowspan="2">
				');
							$mpdf->WriteHTML("&nbsp;&nbsp;".date("j",$key)." ".$monthNames[date('n',$key)]);
				$mpdf->WriteHTML('
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordTmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertT($value['recordTmax']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordTmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordHmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(($value['recordHmax']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordHmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordPmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertP($value['recordPmax']),$decimalsP,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordPmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML(' 
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordWmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertW($value['recordWmax']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordWmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordGmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertW($value['recordGmax']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordGmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordRmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertR($value['recordRmax']),$decimalsR,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordRmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD">
				');
							if(array_key_exists("recordAmax",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertT($value['recordAmax']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordAmaxY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('
						</td>
					</tr>
					<tr>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordTmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertT($value['recordTmin']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordTminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordHmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(($value['recordHmin']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordHminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordPmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertP($value['recordPmin']),$decimalsP,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordPminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML(' 
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordWmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertW($value['recordWmin']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordWminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordGmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertW($value['recordGmin']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordGminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordRmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertR($value['recordRmin']),$decimalsR,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordRminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('     
						</td>
						<td class="stationExtremeTD tdLineBorder">
				');
							if(array_key_exists("recordAmin",$value)){
								$mpdf->WriteHTML("<b>".number_format(convertT($value['recordAmin']),1,".","")."</b><br><span style='font-size:8pt'>".dateLimiter($value['recordAminY']));
							}
							else{
								$mpdf->WriteHTML("-");
							}
				$mpdf->WriteHTML('
						</td>
					</tr>
				');
			}
		$mpdf->WriteHTML('</table>');

		$mpdf->WriteHTML('
			<pagebreak>
            <h2 style="text-align:center">'.lang('almanac','c').'</h2>
    	');
		$mpdf->WriteHTML('
            <table class="tableAlmanac" cellspacing="0" style="font-size:9pt" cellpadding="1">
                <thead>
                    <tr style="color:white;background:#'.$color_schemes[$design2]['900'].'">
                        <th></th>
                        <th>
                            <img src="../../icons/sunrise.png" style="width:25px">
                        </th>
                        <th>
                            <img src="../../icons/sunset.png" style="width:25px">
                        </th>
                        <th>
                            <img src="../../icons/sun.png" style="width:25px">
                        </th>
                        <th>
                            <img src="../../icons/night.png" style="width:25px">
                        </th>
						<th>
                        </th>
                    </tr>
                </thead>
        ');
			foreach($daily['data'] as $day){
				$mpdf->WriteHTML('
					<tr>
						<td style="text-align:left;padding-left:2px;font-weight:bold;color:white;background:#'.$color_schemes[$design2]['900'].'" rowspan="2">
				');
							$mpdf->WriteHTML("&nbsp;&nbsp;".date("j",$day['time'])." ".$monthNames[date('n',$day['time'])]);
				$mpdf->WriteHTML('
						</td>
						<td rowspan="2" class="tdLineBorder">
				');
							if(array_key_exists("sunriseTime",$day)){
								$mpdf->WriteHTML(date($timeFormat,$day['sunriseTime']));
							}
				$mpdf->WriteHTML('     
						</td>
						<td rowspan="2" class="tdLineBorder">
				');
							if(array_key_exists("sunsetTime",$day)){
								$mpdf->WriteHTML(date($timeFormat,$day['sunsetTime']));
							}
				$mpdf->WriteHTML('     
						</td>
				');
				if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
					$morning = $day['sunriseTime'] - strtotime(date("Y",$day['sunriseTime'])."-".date("m",$day['sunriseTime'])."-".date("d",$day['sunriseTime'])." 00:00");
					$dayTime = $day['sunsetTime'] - $day['sunriseTime'];
					$daySeconds = 60 * 60 * 24;
					$evening = $daySeconds - $dayTime - $morning;
					$morningPerc = round($morning/$daySeconds,2)*100;
					$dayPerc = round($dayTime/$daySeconds,2)*100;
					$eveningPerc = round($evening/$daySeconds,2)*100;
				}
				$mpdf->WriteHTML('
						<td>
				');
							$dayLengthS = 60 * 60 * 24;
							if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
								$dayMin = ($day['sunsetTime'] - $day['sunriseTime'])/60;
								$nightMin = 1440 - $dayMin;
								$mpdf->WriteHTML(round($dayMin,0)." ".lang("minAbbr",'l'));
							}
				$mpdf->WriteHTML(' 
						</td>
						<td>
				');
							if(array_key_exists("sunriseTime",$day) && array_key_exists("sunsetTime",$day)){
								$mpdf->WriteHTML(round($nightMin,0)." ".lang("minAbbr",'l'));
							}
				$mpdf->WriteHTML('     
						</td>
						<td rowspan="2" class="tdLineBorder">
				');
							if(array_key_exists("moonPhase",$day)){
								$phase = $day['moonPhase'];
								if($phase<0.5){
									$phase = $phase + 0.5;
								}
								else{
									$phase = $phase - 0.5;
								}
								$moonIcon = round(($phase/(1/118)));
								$mpdf->WriteHTML('<img src="../../imgs/moonImgs/'.$moonIcon.'.png" style="width:50px" id="moonImg">');
							}
				$mpdf->WriteHTML('     
						</td>
					</tr>
					<tr>
						<td colspan="2" class="tdLineBorder">
							<table style="width:80%;margin: 0 auto" cellspacing="0" cellpadding="0">
								<tr>
									<td>
										<img src="../../icons/pdf/moon.png" style="width:12px">
									</td>
									<td>
										<img src="../../icons/pdf/sun.png" style="width:12px">
									</td>
									<td>
										<img src="../../icons/pdf/moon.png" style="width:12px">
									</td>
								</tr>
								<tr>
									<td style="height:7px;background:black;border:1px solid black;border-right:0px;width:'.$morningPerc.'%"></td>
									<td style="height:7px;background:#4ca6ff;border:1px solid black;border-right:0px;border-left:0px;width:'.$dayPerc.'%"></td>
									<td style="height:7px;background:black;border:1px solid black;border-left:0px;width:'.$eveningPerc.'%"></td>
								</tr>
							</table>
						</td>
					</tr>
				');
			}
		$mpdf->WriteHTML('</table>');

    $mpdf->Output('forecast_'.date("Y-m-d").'.pdf', 'I');
    exit;
    