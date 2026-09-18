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
	#	Menu
	#
	############################################################################
	#
	# 	v17.2 Nectarine 2019-06-27
	#
	############################################################################

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	$availableDays = "";
	$firstDbDay = "";
	$lastDbDay = "";
	$availableYears = array();

	$result = mysqli_query($con,"
		SELECT Year(DateTime), Month(DateTime), Day(DateTime)
		FROM alldata
		Group BY Year(DateTime), Month(DateTime), Day(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		if($firstDbDay==""){
			$firstDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
		}
		$currentDate = $row['Year(DateTime)']."-".$row['Month(DateTime)']."-".$row['Day(DateTime)'];
		$availableDays .= "\"".$currentDate."\",";
		$lastDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
	}

	$result = mysqli_query($con,"
		SELECT DISTINCT Year(DateTime)
		FROM alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		array_push($availableYears,$row['Year(DateTime)']);
	}

	$special = special();

	$special = str_replace("mticon-winter","mticon-snow",$special);

	// get menu items
	if(file_exists($baseURL."admin/menu/menuItems.txt")){
		$menuItems = trim(file_get_contents($baseURL."admin/menu/menuItems.txt"));
		if($menuItems=="default"){
			$generateDefault = true;
		}
		else{
			$generateDefault = false;
			$menuItems = json_decode($menuItems,true);
		}
	}
	else{
		$menuItems = array();
	}
	if(file_exists($baseURL."admin/menu/menuOrder.txt")){
		$menuOrder = trim(file_get_contents($baseURL."admin/menu/menuOrder.txt"));
		if($menuOrder=="default"){
			$generateDefault = true;
		}
		else{
			$generateDefault = false;
			$menuOrder = json_decode($menuOrder,true);
		}
	}
	else{
		$menuOrder = array();
	}

	if(file_exists($baseURL."admin/menu/adminPlugins.txt")){
		$adminPlugins = urldecode(file_get_contents($baseURL."admin/menu/adminPlugins.txt"));
	}
	else{
		$adminPlugins = "";
	}

	###################################################################################################################
	// generate default menu with all pages and plugins installed
	if($generateDefault){
		$menuItems = array();
		$menuOrder = array();
		$menuItems['weatherStation']['name'] = "weather station";
		$menuItems['weatherStation']['namespace'] = "weatherStation";
		$menuItems['weatherStation']['tabIcon'] = "mticon-station";
			$temporaryContent = array();
			$temporaryContent[] = ">>>"; 
			$temporaryContent[] = ">> live";
			$temporaryContent[] = "#default(currentData)";
			$temporaryContent[] = ">> interactive views";
			$temporaryContent[] = "#default(interactiveGraph)";
			$temporaryContent[] = "#default(interactiveTable)";
			$temporaryContent[] = "#default(intervalSummary)";
			$temporaryContent[] = ">> reports";
			$temporaryContent[] = "#default(reports)";
			$temporaryContent[] = ">> trends";
			$temporaryContent[] = "#default(trends)";
			$temporaryContent[] = ">>>";
			$temporaryContent[] = ">> statistics";
			$temporaryContent[] = "#default(statisticsT)";
			$temporaryContent[] = "#default(statisticsA)";
			$temporaryContent[] = "#default(statisticsD)";
			$temporaryContent[] = "#default(statisticsH)";
			$temporaryContent[] = "#default(statisticsP)";
			$temporaryContent[] = "#default(statisticsW)";
			$temporaryContent[] = "#default(statisticsG)";
			$temporaryContent[] = "#default(statisticsR)";
			$temporaryContent[] = "#default(statisticsS)";
			$temporaryContent[] = ">>>";
			$temporaryContent[] = ">>";
			$temporaryContent[] = "#default(statisticsDay)";
			$temporaryContent[] = "#default(statisticsNight)";
			$temporaryContent[] = "#default(seasons)";
			$temporaryContent[] = "#default(rainSeasons)";
			$temporaryContent[] = ">>";
			$temporaryContent[] = "#default(calendar)";
			$temporaryContent[] = "#default(history)";
		$menuItems['weatherStation']['content'] = implode("\n",$temporaryContent);

		$menuItems['weather']['name'] = "weather";
		$menuItems['weather']['namespace'] = "weather";
		$menuItems['weather']['tabIcon'] = "mticon-weather";
			$temporaryContent = array();
			$temporaryContent[] = ">>>";
			$temporaryContent[] = ">> forecast";
			$temporaryContent[] = "#default(outlook)";
			$temporaryContent[] = ">> current conditions";
			$temporaryContent[] = "#default(globalMap)";
			$temporaryContent[] = "#default(metar)";
		$menuItems['weather']['content'] = implode("\n",$temporaryContent);

		$menuItems['climate']['name'] = "climate";
		$menuItems['climate']['namespace'] = "climate";
		$menuItems['climate']['tabIcon'] = "mticon-outlook";
			$temporaryContent = array();
			$temporaryContent[] = ">>>";
			$temporaryContent[] = "#default(climateMap)";
			$temporaryContent[] = "#default(climateIndices)";
		$menuItems['climate']['content'] = implode("\n",$temporaryContent);

		$menuItems['astronomy']['name'] = "astronomy";
		$menuItems['astronomy']['namespace'] = "astronomy";
		$menuItems['astronomy']['tabIcon'] = "mticon-solarsystem";
			$temporaryContent = array();
			$temporaryContent[] = ">>>";
			$temporaryContent[] = "#default(moonPhase)";
			$temporaryContent[] = "#default(astroCalendar)";
			$temporaryContent[] = "#default(sunTimes)";
			$temporaryContent[] = "#default(solsticeEquinox)";
			$temporaryContent[] = "#default(annualSolarMax)";
			$temporaryContent[] = ">>>";
			$temporaryContent[] = "#default(dayNight)";
		$menuItems['astronomy']['content'] = implode("\n",$temporaryContent);

		$menuItems['info']['name'] = "info";
		$menuItems['info']['namespace'] = "info";
		$menuItems['info']['tabIcon'] = "fa fa-info-circle";
			$temporaryContent = array();
			$temporaryContent[] = ">>>";
			$temporaryContent[] = ">> about";
			$temporaryContent[] = "#default(aboutLocation)";
			$temporaryContent[] = "#default(aboutPage)";
			$temporaryContent[] = "#default(aboutStation)";
			$temporaryContent[] = "#default(links)";

		$menuItems['info']['content'] = implode("\n",$temporaryContent);

		file_put_contents($baseURL."admin/menu/menuItems.txt",json_encode($menuItems));

		$menuOrder = array("weatherStation","weather","climate","astronomy","info");
		file_put_contents($baseURL."admin/menu/menuOrder.txt",json_encode($menuOrder));
	}

	###################################################################################################################
	###################################################################################################################

	$defaultSpecs = array();
	$defaultSpecs['currentData'] = array("pages/station/redirect.php?url=liveData.php","current data","mticon-weather");
	$defaultSpecs['currentGauges'] = array("pages/station/redirect.php?url=live.php","gauges","mticon-gauges");
	$defaultSpecs['interactiveGraph'] = array("pages/station/graph.php","interactive graph","mticon-graph");
	$defaultSpecs['interactiveTable'] = array("pages/station/table.php","interactive table","mticon-table");
	$defaultSpecs['intervalSummary'] = array("pages/station/intervalSummarySelector.php","interval summary","mticon-shortterm");
	$defaultSpecs['history'] = array("pages/station/redirect.php?url=history.php","history","mticon-history");
	$defaultSpecs['calendar'] = array("pages/station/redirect.php?url=calendar.php","calendar","fa fa-calendar");
	$defaultSpecs['trends'] = array("pages/station/trendsSelect.php","trends","fa fa-line-chart");
	$defaultSpecs['statisticsT'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DT","temperature","mticon-temp");
	$defaultSpecs['statisticsH'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DH","humidity","mticon-humidity");
	$defaultSpecs['statisticsP'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DP","pressure","mticon-pressure");
	$defaultSpecs['statisticsW'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DW","wind speed","mticon-wind");
	$defaultSpecs['statisticsG'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DG","wind gust","mticon-gust");
	$defaultSpecs['statisticsR'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DR","precipitation","mticon-rain");
	$defaultSpecs['statisticsD'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DD","dew point","mticon-dewpoint");
	$defaultSpecs['statisticsA'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DA","apparent temperature","mticon-apparent");
	$defaultSpecs['statisticsS'] = array("pages/station/redirect.php?url=climate.php%3Fvar%3DS","solar radiation","mticon-sun");
	$defaultSpecs['statisticsDay'] = array("pages/station/day.php","daytime","mticon-sunlight-ratio");
	$defaultSpecs['statisticsNight'] = array("pages/station/night.php","nighttime","mticon-night");
	$defaultSpecs['rainSeasons'] = array("pages/station/rainSeasons.php","rain seasons","mticon-rain-range");
	$defaultSpecs['reports'] = array("pages/station/reportSelector.php","reports","mticon-summary");
	$defaultSpecs['outlook'] = array("pages/forecast/index.php","outlook","mticon-outlook");
	$defaultSpecs['metar'] = array("pages/metar/index.php","metar","fa fa-plane");
	$defaultSpecs['globalMap'] = array("pages/maps/global.php","global map","fa fa-globe");
	$defaultSpecs['climateMap'] = array("pages/climate/map.php","climate map","mticon-map");
	$defaultSpecs['moonPhase'] = array("pages/astronomy/moonPhase.php","moon phase","mticon-moon");
	$defaultSpecs['sunTimes'] = array("pages/astronomy/sun.php","sunrise / sunset","mticon-sunlight-ratio");
	$defaultSpecs['astroCalendar'] = array("pages/astronomy/astroCalendar.php","astronomical calendar","mticon-month");
	$defaultSpecs['solsticeEquinox'] = array("pages/astronomy/equisol.php","solstice / equinox","mticon-solarsystem");
	$defaultSpecs['asteroids'] = array("pages/astronomy/asteroids.php","asteroids","mticon-meteor");
	$defaultSpecs['dayNight'] = array("pages/astronomy/dayLight.php","day / night","mticon-daynight");
	$defaultSpecs['links'] = array("pages/other/links.php","links","fa fa-external-link");
	$defaultSpecs['aboutLocation'] = array("pages/other/aboutLocation.php",$stationLocation,"fa fa-map-marker");
	$defaultSpecs['aboutPage'] = array("pages/other/aboutPage.php",$pageName,"mticon-logo");
	$defaultSpecs['aboutStation'] = array("pages/other/aboutStation.php",$stationModel,"mticon-gauges");
	$defaultSpecs['climateIndices'] = array("pages/station/climateIndices.php","station climate","mticon-station");
	$defaultSpecs['seasons'] = array("pages/station/seasonsIndex.php","seasons","mticon-forecast");
	$defaultSpecs['annualSolarMax'] = array("pages/astronomy/annualSolarMax.php","maximum potential solar radiation","mticon-sun");

	$pluginSpecs = array();
	$pluginSpecs['airQualityForecastCA'] = array("index.php","air quality","flag(ca)");
	$pluginSpecs['airQualityCZ'] = array("index.php","air quality","flag(cz)");
	$pluginSpecs['airTraffic'] = array("index.php","air traffic","fa fa-plane");
	$pluginSpecs['antarctica'] = array("index.php","Antarctica","flag(aq)");
	$pluginSpecs['apiViewer'] = array("index.php","API","fa fa-file-code-o");
	$pluginSpecs['apparentTemp'] = array("index.php","apparent temperature","mticon-apparent");
	$pluginSpecs['australiaWeatherMaps'] = array("index.php","weather maps","flag(au)");
	$pluginSpecs['australiaSynopticCharts'] = array("index.php","synoptic charts","flag(au)");
	$pluginSpecs['blog'] = array("index.php","Blog","fa fa-newspaper-o");
	$pluginSpecs['bioIndexes'] = array("index.php","bioindexes","mticon-bioindexes");
	$pluginSpecs['bloomSky'] = array("index.php","bloomsky","mticon-camera");
	$pluginSpecs['calendar'] = array("index.php","calendar","fa fa-file-pdf-o");
	$pluginSpecs['canadaMonthlySummary'] = array("index.php","monthly summary","flag(ca)");
	$pluginSpecs['canadaRadar'] = array("index.php","radar","flag(ca)");
	$pluginSpecs['canadaSummary'] = array("index.php","weather summaries","flag(ca)");
	$pluginSpecs['canadaLightning'] = array("index.php","canada","fa fa-bolt");
	$pluginSpecs['cityConditions'] = array("index.php","world weather","mticon-weather");
	$pluginSpecs['climateCanada'] = array("index.php","climate","flag(ca)");
	$pluginSpecs['climateClassification'] = array("index.php","climate classification","mticon-weather");
	$pluginSpecs['climateCzech'] = array("index.php","climate","flag(cz)");
	$pluginSpecs['climateCustom'] = array("index.php","climate","mticon-weather");
	$pluginSpecs['climateFR'] = array("index.php","climate","flag(fr)");
	$pluginSpecs['climateGermany'] = array("index.php","climate","flag(de)");
	$pluginSpecs['climateMaps'] = array("index.php","climate maps","mticon-map");
	$pluginSpecs['climateNormals'] = array("index.php","climate normals","mticon-outlook");
	$pluginSpecs['climateSpain'] = array("index.php","climate","flag(es)");
	$pluginSpecs['climateUS'] = array("index.php","climate","flag(us)");
	$pluginSpecs['cloudCalculations'] = array("calc.php","cloud calculations","mticon-clouds");
	$pluginSpecs['co2'] = array("co2.php","co2","mticon-co2");
	$pluginSpecs['cocorahs'] = array("index.php","CoCoRaHS","mticon-rain");
	$pluginSpecs['conditionsUS'] = array("index.php","weather","flag(us)");
	$pluginSpecs['contact'] = array("index.php","contact","fa fa-envelope");
	$pluginSpecs['continentality'] = array("index.php","continentality","mticon-continentality");
	$pluginSpecs['countryClimate'] = array("index.php","climate of countries","mticon-map");
	$pluginSpecs['countryDetail'] = array("index.php","country information","mticon-globe");
	$pluginSpecs['czechMeteogram'] = array("index.php","czech meteogram","flag(cz)");
	$pluginSpecs['dailyWeatherMapsUS'] = array("index.php","daily weather maps","flag(us)");
	$pluginSpecs['dataDisplay'] = array("index.php","live data display","mticon-table");
	$pluginSpecs['dataCheck'] = array("index.php","data quality check","fa fa-check-square");
	$pluginSpecs['degreeDays'] = array("index.php","degree days","mticon-hdd");
	$pluginSpecs['deviations'] = array("index.php","deviations","mticon-extremes");
	$pluginSpecs['dewpoint'] = array("index.php","dewpoint","mticon-dewpoint");
	$pluginSpecs['diary'] = array("diaryIndex.php","station diary","mticon-terminology");
	$pluginSpecs['disasters'] = array("disasters.php","disasters","mticon-disasters");
	$pluginSpecs['distributions'] = array("index.php","data distribution","mticon-extremes");
	$pluginSpecs['droughtUS'] = array("index.php","drought","flag(us)");
	$pluginSpecs['earthquakes'] = array("index.php","earthquakes","mticon-earthquake");
	$pluginSpecs['eclipses'] = array("eclipses.php","eclipses","mticon-solar");
	$pluginSpecs['ecoFootprint'] = array("index.php","ecological footprint","mticon-footprint");
	$pluginSpecs['elNino'] = array("index.php","el nino","mticon-elnino");
	$pluginSpecs['et'] = array("index.php","evapotranspiration","mticon-evapotranspiration");
	$pluginSpecs['extendedGraphs'] = array("index.php","extended graphs","mticon-graph");
	$pluginSpecs['extremesUK'] = array("index.php","extremes","flag(gb)");
	$pluginSpecs['fireDanger'] = array("fireDanger.php","fire danger","mticon-fire");
	$pluginSpecs['forecastComparison'] = array("index.php","forecast comparison","mticon-cumulative");
	$pluginSpecs['forecastModelEU'] = array("index.php","forecast model","flag(eu)");
	$pluginSpecs['forecastModelUS'] = array("usNWS.php","forecast model","flag(us)");
	$pluginSpecs['forecastNL'] = array("index.php","forecast","flag(nl)");
	$pluginSpecs['fluUS'] = array("index.php","influenza","flag(us)");
	$pluginSpecs['geography'] = array("index.php","geographical calculations","mticon-globe");
	$pluginSpecs['globalModel'] = array("index.php","global model","mticon-globe");
	$pluginSpecs['globalSnow'] = array("index.php","global snow","mticon-snow");
	$pluginSpecs['grLevelX'] = array("index.php","grlevelX","mticon-doppler");
	$pluginSpecs['growingDegreeDays'] = array("index.php","growing degree days","mticon-gdd");
	$pluginSpecs['guestbook'] = array("index.php","guestbook","fa fa-comments");
	$pluginSpecs['history'] = array("index.php","history","mticon-history");
	$pluginSpecs['hurricanes'] = array("index.php","hurricanes","mticon-hurricane");
	$pluginSpecs['indoorData'] = array("index.php","indoor conditions","mticon-indoor");
	$pluginSpecs['issTracker'] = array("index.php","ISS tracker","mticon-iss");
	$pluginSpecs['jetStream'] = array("index.php","jet stream","mticon-gust");
	$pluginSpecs['lightning'] = array("index.php","lightnings","fa fa-bolt");
	$pluginSpecs['liveGaugesCumulus'] = array("index.php","live gauges","mticon-gauges");
	$pluginSpecs['liveGaugesAPI'] = array("index.php","live gauges","mticon-gauges");
	$pluginSpecs['liveGaugesMB'] = array("index.php","live gauges","mticon-gauges");
	$pluginSpecs['liveGaugesWC'] = array("index.php","live gauges","mticon-gauges");
	$pluginSpecs['liveGaugesWD'] = array("index.php","live gauges","mticon-gauges");
	$pluginSpecs['mapsUK'] = array("index.php","climate maps","flag(gb)");
	$pluginSpecs['marine'] = array("index.php","marine","fa fa-ship");
	$pluginSpecs['meteoHistory'] = array("index.php","History of Meteorology","fa fa-th-list");
	$pluginSpecs['models'] = array("index.php","models","mticon-outlook");
	$pluginSpecs['myMap'] = array("index.php","map","mticon-globe");
	$pluginSpecs['myStation'] = array("index.php","weather station","mticon-station");
	$pluginSpecs['netherlandsClimate'] = array("index.php","climate","flag(nl)");
	$pluginSpecs['netherlandsMaps'] = array("index.php","weather maps","flag(nl)");
	$pluginSpecs['nightSky'] = array("index.php","night sky","mticon-nightsky");
	$pluginSpecs['noaaReport'] = array("index.php","NOAA report","mticon-summary");
	$pluginSpecs['nwsWarnings'] = array("index.php","NWS warnings","flag(us)");
	$pluginSpecs['pageBuilder'] = array("index.php","page builder","fa fa-file-code-o");
	$pluginSpecs['photoGallery'] = array("index.php","photo gallery","fa fa-image");
	$pluginSpecs['pollenCalendar'] = array("index.php","pollen calendar","fa fa-tree");
	$pluginSpecs['pollenForecastEU'] = array("index.php","pollen forecast","flag(eu)");
	$pluginSpecs['precipitationAnalysis'] = array("index.php","precipitation analysis","mticon-rain");
	$pluginSpecs['pressureConversion'] = array("index.php","pressure conversion","mticon-pressure");
	$pluginSpecs['pressureMapUK'] = array("index.php","pressure map","flag(gb)");
	$pluginSpecs['radarAustralia'] = array("australiaRadar.php","radar","flag(au)");
	$pluginSpecs['radarEU'] = array("europeRadar.php","radar","flag(eu)");
	$pluginSpecs['radialGraphs'] = array("index.php","radial graphs","mticon-radials");
	$pluginSpecs['riverHeightsUS'] = array("index.php","river heights","flag(us)");
	$pluginSpecs['satelliteImages'] = array("index.php","satellite images","mticon-satellite");
	$pluginSpecs['seaTemp'] = array("index.php","sea temperature","mticon-temp");
	$pluginSpecs['scales'] = array("index.php","scales","mticon-scale");
	$pluginSpecs['snow'] = array("index.php","snow statistics","mticon-snow");
	$pluginSpecs['snowUSStations'] = array("index.php","snow data","flag(us)");
	$pluginSpecs['socialNetworks'] = array("socialNetworks.php","social networks","fa fa-facebook-official");
	$pluginSpecs['solar'] = array("index.php","sunshine","mticon-sun");
	$pluginSpecs['solarPower'] = array("index.php","solar power","mticon-sun");
	$pluginSpecs['solarSystem'] = array("index.php","solar system","mticon-solarsystem");
	$pluginSpecs['spaceWeather'] = array("index.php","space weather","mticon-mars");
	$pluginSpecs['spcReports'] = array("index.php","SPC reports","flag(us)");
	$pluginSpecs['stationCompare'] = array("index.php","station data comparison","mticon-compare");
	$pluginSpecs['stationExtremes'] = array("index.php","station extremes","mticon-extremes");
	$pluginSpecs['stationFeed'] = array("index.php","station feed","fa fa-rss");
	$pluginSpecs['steelSeries'] = array("index.php","gauges","mticon-gauges");
	$pluginSpecs['sunMoon'] = array("index.php","sun/moon data","mticon-daynight");
	$pluginSpecs['tempSums'] = array("index.php","temperature sums","mticon-temp");
	$pluginSpecs['tempAnalysis'] = array("index.php","temperature analysis","mticon-temp");
	$pluginSpecs['tides'] = array("index.php","tide heights","mticon-tide");
	$pluginSpecs['ukAir'] = array("index.php","air quality","flag(gb)");
	$pluginSpecs['units'] = array("units.php","unit conversion","mticon-scale");
	$pluginSpecs['unitedStates'] = array("index.php","usFull","flag(us)");
	$pluginSpecs['userMap'] = array("index.php","user map","mticon-logo");
	$pluginSpecs['usExtremes'] = array("usExtremes.php","extremes","flag(us)");
	$pluginSpecs['usRadar'] = array("index.php","radar","mticon-doppler");
	$pluginSpecs['usTempChange'] = array("index.php","temperature trends","flag(us)");
	$pluginSpecs['volcanoes'] = array("index.php","volcanoes","mticon-volcano");
	$pluginSpecs['warningsAustralia'] = array("index.php","warnings","flag(au)");
	$pluginSpecs['warningsCanada'] = array("index.php","warnings","flag(ca)");
	$pluginSpecs['warningsEU'] = array("warningsEU.php","warnings","flag(eu)");
	$pluginSpecs['wdParser'] = array("index.php","weather display parser","mticon-snow");
	$pluginSpecs['weatherMETAR'] = array("index.php","METAR weather","mticon-weather");
	$pluginSpecs['weatherRecords'] = array("index.php","weather records","mticon-extremes");
	$pluginSpecs['weatherSymbols'] = array("index.php","weather symbols","fa fa-circle-o");
	$pluginSpecs['weatherTerminology'] = array("index.php","weather terminology","mticon-dictionary");
	$pluginSpecs['weatherTerminologyCZ'] = array("index.php","weather terminology","flag(cz)");
	$pluginSpecs['weatherTerminologyDE'] = array("index.php","weather terminology","flag(de)");
	$pluginSpecs['weatherTerminologyDK'] = array("index.php","weather terminology","flag(dk)");
	$pluginSpecs['weatherTerminologyES'] = array("index.php","weather terminology","flag(es)");
	$pluginSpecs['weatherTerminologyFR'] = array("index.php","weather terminology","flag(fr)");
	$pluginSpecs['weatherTerminologyGR'] = array("index.php","weather terminology","flag(gr)");
	$pluginSpecs['weatherTerminologyHU'] = array("index.php","weather terminology","flag(hu)");
	$pluginSpecs['weatherTerminologyIT'] = array("index.php","weather terminology","flag(it)");
	$pluginSpecs['weatherTerminologyNL'] = array("index.php","weather terminology","flag(nl)");
	$pluginSpecs['weatherTerminologyNO'] = array("index.php","weather terminology","flag(no)");
	$pluginSpecs['weatherTerminologyPT'] = array("index.php","weather terminology","flag(pt)");
	$pluginSpecs['webcam'] = array("index.php","webcam","mticon-webcam");
	$pluginSpecs['weekDays'] = array("index.php","week days","mticon-week");
	$pluginSpecs['windDirection'] = array("index.php","wind direction","mticon-winddirection");
	$pluginSpecs['windPower'] = array("index.php","wind power","mticon-wind");
	$pluginSpecs['worldExtremes'] = array("extremes.php","world extremes","mticon-map");
	$pluginSpecs['worldTime'] = array("index.php","world time","mticon-time");
	$pluginSpecs['wxSim'] = array("forecast.php","wxSim","mticon-weather");
	$pluginSpecs['yearComparison'] = array("index.php","yearly comparison","fa fa-balance-scale");
	$pluginSpecs['yearNormals'] = array("index.php","annual averages","mticon-calendar-year");
	###################################################################################################################

	// are we viewing a plugin?
	$addPluginSetupLink = false;
	$thisPageURL = $_SERVER['REQUEST_URI'];
	if (strpos($thisPageURL, '/plugins/') !== false) {
		// check if this plugin has setup 
		if(file_exists("setup.php")){
			$addPluginSetupLink = true;
			// get plugin name 
			preg_match("/\/plugins\/(.*?)\//",$thisPageURL,$matches);
			if(isset($matches[1])){
				$thisPluginName = $matches[1];
			}
		}
		
	}

	$sitemap = array();


	function replaceLines($string){
		$string = str_replace("\n\r","\n",$string);
		$string = str_replace("\r\n","\n",$string);
		$string = str_replace("\r","\n",$string);
		return $string;
	}
	function resetArray($arr){
		$arr = array_filter($arr); // remove blank
		$arr = array_values($arr); // reset key numbering
		return $arr;
	}
?>
<style>
	.menuSmallIcon{
		height:25px;
	}
	.digimeg-nav-item-content{
		padding: 10px;
	}
	.newItem{
		padding-top: 1px;
		padding-bottom: 1px;
		padding-left: 4px;
		padding-right: 3px;
		font-weight: bold;
		font-size: 0.9em;
		color: #<?php echo $color_schemes[$design2]['900']?>;
		background: #<?php echo $color_schemes[$design2]['200']?>;
		border-radius: 7px;
		border: 2px solid #<?php echo $color_schemes[$design2]['100']?>;
		-webkit-animation: pulsate 1.5s ease-out;
    	-webkit-animation-iteration-count: infinite;
		animation: pulsate 1.5s ease-out;
    	animation-iteration-count: infinite;
	}
	@-webkit-keyframes pulsate {
	    0% {
			-webkit-transform: scale(0.1, 0.1); opacity: 0.6;
		}
	    50% {
			opacity: 1.0;
		}
	    100% {
			-webkit-transform: scale(1.2, 1.2); opacity: 0.6;
		}
	}
	@keyframes pulsate {
	    0% {
			-webkit-transform: scale(0.1, 0.1); opacity: 0.6;
			transform: scale(0.1, 0.1); opacity: 0.6;
		}
	    50% {
			opacity: 1.0;
		}
	    100% {
			-webkit-transform: scale(1.2, 1.2); opacity: 0.6;
			transform: scale(1.2, 1.2); opacity: 0.6;
		}
	}
	.animatedIcon{
		-webkit-animation: pulsate 1.5s ease-out;
    	-webkit-animation-iteration-count: infinite;
		animation: pulsate 1.5s ease-out;
    	animation-iteration-count: infinite;
	}
	.selectedMenu{
		background: #<?php echo $color_schemes[$design2]['400']?>;
	}
</style>
<div class="scroller_anchor"></div>
<div style="width:100%">

<div class="digimeg-nav-wrap" style="font-variant:small-caps">
	<ul class="digimeg-group digimeg-main-nav" style="margin:0;padding:0;">
		<li id="home-subnav">
			<span id="homeIcon" class="<?php echo $special?> homeIcon tooltip" style="padding-top:0.2em!important;padding-bottom:0.1em!important;padding-left:0.5em!important;padding-right:0.5em!important;font-size:2em" onclick="window.location='<?php echo $pageURL.$path?>index.php'" title="<?php echo lang("home","c")?>"> 
		</li>
		<?php
			if(count($menuOrder)>0){
				foreach($menuOrder as $menuTab){
		?>
					<li>
						<?php 
							$thisTabContent = $menuItems[$menuTab]['content'];
							if($thisTabContent!=""){ // tab content is not blank, show as menu tab
								// check if it is icon
								if(strpos($menuItems[$menuTab]['name'],";")!==false){
									$menuTabNameExploded = explode(";",$menuItems[$menuTab]['name']);
									$tabIcon = trim($menuTabNameExploded[0]);
									$realName = trim($menuTabNameExploded[1]);
						?>
									<a href="javascript:void(null);" data-subnav="<?php echo $menuItems[$menuTab]['namespace']?>-subnav" class="digimeg-nav-item" style="padding-top:0.2em!important;padding-bottom:0.1em!important;padding-left:0.5em!important;padding-right:0.5em!important;font-size:2em">
										<span class="<?php echo $tabIcon?> fa-fw"></span>
									</a>
						<?php
								}
								else{
						?>
									<a href="javascript:void(null);" data-subnav="<?php echo $menuItems[$menuTab]['namespace']?>-subnav" class="digimeg-nav-item">
										<?php echo lang($menuItems[$menuTab]['name'],'c')?>
									</a>
						<?php 
								}
							}
							if($thisTabContent==""){ // tab content is blank, use as link
								// check if it is icon
								if(strpos($menuItems[$menuTab]['name'],";")!==false){
									$menuTabNameExploded = explode(";",$menuItems[$menuTab]['name']);
									$tabIcon = trim($menuTabNameExploded[0]);
									$realName = trim($menuTabNameExploded[1]);
									$sitemap[lang($realName,"c")] = $menuItems[$menuTab]['link'];
						?>
									<a href="<?php echo $menuItems[$menuTab]['link']?>" target="_blank" class="digimeg-nav-item" style="padding-top:0.1em!important;padding-bottom:0.1em!important;padding-left:0.5em!important;padding-right:0.5em!important;font-size:2em">
										<span class="<?php echo $tabIcon?> fa-fw"></span>
									</a>
						<?php 
								}
								else{
									$sitemap[lang($menuItems[$menuTab]['name'],'c')] = $menuItems[$menuTab]['link'];
						?>
									<a href="<?php echo $menuItems[$menuTab]['link']?>" target="_blank" class="digimeg-nav-item">
										<?php echo lang($menuItems[$menuTab]['name'],'c')?>
									</a>
						<?php
								}
							}
							
						?>
					</li>
		<?php
				}
			}
		?>
		<?php
			if($_SESSION['user']=="admin"){
		?>
				<li>
					<a href="javascript:void(null);" data-subnav="admin-subnav" class="digimeg-nav-item" style="padding-top:0.1em!important;padding-bottom:0.1em!important;padding-left:0.5em!important;padding-right:0.5em!important;font-size:2em">
						<span class="fa fa-cogs fa-fw"></span>
					</a>
				</li>
		<?php
			}
		?>
	</ul>
	<?php
		if(count($menuOrder)>0){
			foreach($menuOrder as $menuTab){
				// check icons
				if(file_exists($pageURL.$path."icons/menuTabs/".$menuItems[$menuTab]['namespace'].".png")){
					$tabImg = $pageURL.$path."icons/menuTabs/".$menuItems[$menuTab]['namespace'].".png";
				}
				else{
					$tabImg = $pageURL.$path."icons/weather.png";
				}
				// PARSE MENU CONTENT
				// split sections
				$menuTabContent = $menuItems[$menuTab]['content'];
				$menuTabContent = str_replace(">>> ",">>>",$menuTabContent); // trim potential spaces
				$tabSections = explode(">>>",$menuTabContent);
				$tabSections = resetArray($tabSections);
				$sectionWidth = round(80/(count($tabSections)+1)); // at least one section is always there
	?>
				<ul class="digimeg-nav-down-content digimeg-sub-nav">
					<li id="<?php echo $menuItems[$menuTab]['namespace']?>-subnav">
						<div class="digimeg-nav-item-content digimeg-group">
							<div style="display:inline-block;width:18%;vertical-align:top;text-align:center;max-width:220px">
								<span class="<?php echo $menuItems[$menuTab]['tabIcon']?>" style="font-size:7em"></span>
								<?php 
									if(strpos($menuItems[$menuTab]['name'],";")!==false){
										$menuTabNameExploded = explode(";",$menuItems[$menuTab]['name']);
										$tabIcon = trim($menuTabNameExploded[0]);
										$realName = trim($menuTabNameExploded[1]);
									}
									else{
										$realName = $menuItems[$menuTab]['name'];
									}
									$sitemapRealName = lang($realName,'c');
								?>
								<h2 style="text-align:center;color:white"><?php echo lang($realName,'c')?></h2>
							</div>
							<?php
								for($a=0;$a<count($tabSections);$a++){
									$sectionContent = replaceLines($tabSections[$a]);
									$sectionLinks = explode("\n",$sectionContent);
									$sectionLinks = resetArray($sectionLinks);
							?>
									<div style="display:inline-block;width:<?php echo $sectionWidth?>%;vertical-align:top">
										<?php
											for($w=0;$w<count($sectionLinks);$w++){
												//check if we have section
												if(substr($sectionLinks[$w],0,2)==">>"){
													$sectionLinks[$w] = trim(str_replace(">>","",$sectionLinks[$w])); // remove the >> as section indicator
													$sectionTitle = lang(strtolower(trim($sectionLinks[$w])),'c'); // attempt to translate
										?>
													<div style="font-size:1.4em;font-weight:bold;font-variant:small-caps">
													<?php
														if(trim($sectionTitle!="")){
															echo $sectionTitle;
														}
														if($sectionTitle==""){ // insert space if break
															echo "<br>";
														}
													?>
													</div>
										<?php
												}
												else if(substr($sectionLinks[$w],0,2)==">-"){
													$sectionLinks[$w] = trim(str_replace(">-","",$sectionLinks[$w])); // remove the >- as section indicator
													$sectionTitle = lang(strtolower(trim($sectionLinks[$w])),'c'); // attempt to translate
										?>
													<div style="font-size:1.1em;font-weight:bold;font-variant:small-caps;padding-left:5px">
													<?php
														echo $sectionTitle;
													?>
													</div>
										<?php
												}
												// we have separator
												else if(substr($sectionLinks[$w],0,2)=="--"){
													$sectionLinks[$w] = trim(str_replace("--","",$sectionLinks[$w])); // remove the -- as line indicator
													$lineWidth = trim($sectionLinks[$w]); // get line width
													if($lineWidth==""){ // if not set, use full width
														$lineWidth = 100;
													}
										?>
													<div style="font-size:1.4em;font-weight:bold;font-variant:small-caps;border-bottom:1px solid #<?php echo $color_schemes[$design2]['200']?>;margin-top:2px;margin-bottom:2px;width:<?php echo $lineWidth?>%">

													</div>
										<?php
												}
												// we have plugin
												else if(substr($sectionLinks[$w],0,7)=="#plugin"){
													// check if "new" enabled
													$newItem = false; // assume false
													if (strpos($sectionLinks[$w], '(new)') !== false) {
													    $newItem = true;
														$sectionLinks[$w] = str_replace("(new)","",$sectionLinks[$w]);
													}
													$sectionLinks[$w] = trim($sectionLinks[$w]);
													$pluginNamespace = str_replace("#plugin(","",$sectionLinks[$w]);
													$pluginNamespace = str_replace(")","",$pluginNamespace);
													$thisLinkText = $pluginSpecs[$pluginNamespace][1];
													$thisLinkLink = $pageURL.$path."plugins/".$pluginNamespace."/".$pluginSpecs[$pluginNamespace][0];
													if(substr($pluginSpecs[$pluginNamespace][2],0,4)=="flag"){
														$thisPluginFlag = str_replace("flag(","",$pluginSpecs[$pluginNamespace][2]);
														$thisPluginFlag = str_replace(")","",$thisPluginFlag);
														$thisLinkIcon = '<img src="'.$pageURL.$path.'imgs/'.$flagIconShape.'/big/'.$thisPluginFlag.'.png" style="width:25px;max-height:25px;" class="menuItemIcon">';
													}
													else{
														$thisLinkIcon = '<span class="'.$pluginSpecs[$pluginNamespace][2].' menuItemIcon" style="font-size:20px;color:#'.$color_schemes[$design2]['100'].'"></span>';
													}
													$sitemap[$sitemapRealName][] = array(lang(strtolower(trim($thisLinkText)),'c'),$thisLinkLink,$thisLinkIcon);
										?>
													<table style="width:100%">
														<tr>
															<td style="text-align:center;width:32px">
																<?php echo $thisLinkIcon?>
															</td>
															<td style="text-align:left" class="menuItemText">
																<a href="<?php echo $thisLinkLink?>"><?php echo lang(strtolower(trim($thisLinkText)),'c')?></a>
																<?php
																	if($newItem){
																?>
																		<span class="newItem">
																			<?php echo lang('new','c')?>
																		</span>
																<?php
																	}
																?>
															</td>
														</tr>
													</table>
										<?php
												}
												// default template page
												else if(substr($sectionLinks[$w],0,8)=="#default"){
													// check if "new" enabled
													$newItem = false; // assume false
													if (strpos($sectionLinks[$w], '(new)') !== false) {
													    $newItem = true;
														$sectionLinks[$w] = str_replace("(new)","",$sectionLinks[$w]);
													}
													$sectionLinks[$w] = trim($sectionLinks[$w]);
													$defaultLinkNamespace = str_replace("#default(","",$sectionLinks[$w]);
													$defaultLinkNamespace = str_replace(")","",$defaultLinkNamespace);
													$thisLinkText = $defaultSpecs[$defaultLinkNamespace][1];
													$thisLinkLink = $pageURL.$path.$defaultSpecs[$defaultLinkNamespace][0];
													$thisLinkIcon = '<span class="'.$defaultSpecs[$defaultLinkNamespace][2].' menuItemIcon" style="font-size:20px;color:#'.$color_schemes[$design2]['100'].'" ></span>';
													$sitemap[$sitemapRealName][] = array(lang(strtolower(trim($thisLinkText)),'c'),$thisLinkLink,$thisLinkIcon);
										?>
													<table style="width:100%">
														<tr>
															<td style="text-align:center;width:32px">
																<?php echo $thisLinkIcon?>
															</td>
															<td style="text-align:left" class="menuItemText">
																<a href="<?php echo $thisLinkLink?>"><?php echo lang(strtolower(trim($thisLinkText)),'c')?></a>
																<?php
																	if($newItem){
																?>
																		<span class="newItem">
																			<?php echo lang('new','c')?>
																		</span>
																<?php
																	}
																?>
															</td>
														</tr>
													</table>
										<?php
												}
												// custom link
												else{
													// check if "new" enabled
													$newItem = false; // assume false
													if (strpos($sectionLinks[$w], '(new)') !== false) {
													    $newItem = true;
														$sectionLinks[$w] = str_replace("(new)","",$sectionLinks[$w]);
													}
													$sectionLinks[$w] = trim($sectionLinks[$w]);
													$thisLink = explode("|",$sectionLinks[$w]);
													$thisLinkText = trim($thisLink[0]);
													$thisLinkLink = trim($thisLink[1]);
													// check if link is external
													if (strpos($thisLinkLink, 'http') === false) {
														$thisLinkLink = $pageURL.$path.$thisLinkLink; // internal link, add page path
													}
													$thisLinkIcon = trim($thisLink[2]);
													// is it external image link?
													if (strpos($thisLinkIcon, 'http') !== false) {
														$thisLinkIcon = '<img src="'.$thisLinkIcon.'" style="width:25px;max-height:25px;" class="menuItemIcon">';
													}
													else if(substr($thisLinkIcon,0,4)=="flag"){
														$thisLinkFlag = str_replace("flag(","",$pluginSpecs[$pluginNamespace][2]);
														$thisLinkFlag = str_replace(")","",$thisLinkFlag);
														$thisLinkFlag = strtolower($thisLinkFlag);
														$thisLinkIcon = '<img src="'.$pageURL.$path.'imgs/'.$flagIconShape.'/big/'.$thisLinkFlag.'.png" style="width:25px;max-height:25px;" class="menuItemIcon">';
													}
													// is it icon link?
													else if (substr($thisLinkIcon,0,6)=="mticon"){
														$thisLinkIcon = '<span class="'.$thisLinkIcon.' menuItemIcon" style="font-size:20px;color:#'.$color_schemes[$design2]['100'].'"></span>';
													}
													else if (substr($thisLinkIcon,0,5)=="fa fa"){
														$thisLinkIcon = '<span class="'.$thisLinkIcon.' menuItemIcon" style="font-size:20px;color:#'.$color_schemes[$design2]['100'].'"></span>';
													}
													// internal link
													else{
														$thisLinkIcon = '<img src="'.$pageURL.$path.$thisLinkIcon.'" style="width:20px;max-height:20px;">';
													}
													$blankTab = false; // open link in new tab, set false by default
													if(isset($thisLink[3])){
														if(trim($thisLink[3])=="blank"){
															$blankTab = true;
														}
													}
													$sitemap[$sitemapRealName][] = array(lang(strtolower(trim($thisLinkText)),'c'),$thisLinkLink,$thisLinkIcon);
										?>
													<table style="width:100%">
														<tr>
															<td style="text-align:center;width:32px">
																<?php echo $thisLinkIcon?>
															</td>
															<td style="text-align:left" class="menuItemText">
																<a href="<?php echo $thisLinkLink?>" <?php if($blankTab){echo "target='_blank'";}?>><?php echo lang(strtolower(trim($thisLinkText)),'c')?></a>
																<?php
																	if($newItem){
																?>
																		<span class="newItem">
																			<?php echo lang('new','c')?>
																		</span>
																<?php
																	}
																?>
															</td>
														</tr>
													</table>
										<?php
												}
											}
										?>
									</div>
							<?php
								} // end for
							?>
						</div>
					</li>
				</ul>
	<?php
			}
		}
	?>
	<?php
		if($_SESSION['user']=="admin"){ 
	?>
			<ul class="digimeg-nav-down-content digimeg-sub-nav">
				<li id="admin-subnav">
					<div class="digimeg-nav-item-content digimeg-group">
						<div style="display:inline-block;width:18%;vertical-align:top;text-align:center;max-width:200px">
							<span class="fa fa-cogs" style="font-size:7em"></span>
							<h2 style="text-align:center;color:white"><?php echo lang("admin",'c')?></h2>
						</div>
						<div style="display:inline-block;width:20%;padding-left:20px;padding-top:20px;vertical-align:top">
							<div style="font-size:1.4em;font-weight:bold;font-variant:small-caps">
								<?php
									echo lang("setup",'c')."<br>";
								?>
							</div>
							<table>
								<tr>
									<td style="text-align:center;width:32px">
										<span class="mticon-logo" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/index.php'><?php echo lang('control panel','c')?></a>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;width:32px">
										<span class="fa fa-gear" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>install/setup.php'><?php echo lang('main setup','c')?></a>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;width:32px">
										<span class="mticon-blocks" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/blockSetup.php'><?php echo lang('block setup','c')?></a>
									</td>
								</tr>
								<tr>
									<td>
										<span class="mticon-plugin" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/pluginSetup.php'><?php echo lang('plugin setup','c')?></a>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fa fa-sticky-note" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/adminNotes.php'><?php echo lang('my notes','c')?></a>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fa fa-sign-out" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/logout.php'><?php echo lang('logout','c')?></a>
									</td>
								</tr>
								<?php 
									// add link to setup page if this page is a plugin
									if($addPluginSetupLink){
								?>
										<tr>
											<td>
												<span class="mticon-plugin" style="font-size:20px"></span>
											</td>
											<td style="text-align:left">
												<a href='setup.php'><?php echo lang($thisPluginName,'c')." - ".lang('setup','c')?></a>
											</td>
										</tr>
								<?php
									}
								?>
							</table>
							<div style="font-size:1.4em;font-weight:bold;font-variant:small-caps">
								<?php
									echo lang("homepage",'c')."<br>";
								?>
							</div>
							<table>
								<tr>
									<td style="text-align:center;width:32px">
										<span class="fa fa-desktop" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/homepageStart.php?type=desktop'><?php echo lang('desktop homepage','c')?></a>
									</td>
								</tr>
								<tr>
									<td style="text-align:center;width:32px">
										<span class="fa fa-mobile" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/homepageStart.php?type=mobile'><?php echo lang('mobile homepage','c')?></a>
									</td>
								</tr>
							</table>
						</div>
						<div style="display:inline-block;width:20%;padding-left:20px;padding-top:20px;vertical-align:top">
							<div style="font-size:1.4em;font-weight:bold;font-variant:small-caps">
								<?php
									echo lang("menu",'c')."<br>";
								?>
							</div>
							<table>
								<tr>
									<td>
										<span class="fa fa-align-justify" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/menu/menuTabs.php'><?php echo lang('menu setup','c')?></a>
									</td>
								</tr>
								<tr>
									<td>
										<span class="fa fa-refresh" style="font-size:20px"></span>
									</td>
									<td style="text-align:left">
										<a href='<?php echo $pageURL.$path?>admin/menu/updateMenu.php'><?php echo lang('update menu','c')?></a>
									</td>
								</tr>
							</table>
						</div>
						<div style="display:inline-block;width:20%;padding-left:20px;padding-top:20px;vertical-align:top">
							<?php
								if(file_exists($baseURL."plugins/diary/diary.php")){
							?>
									<table>
										<tr>
											<td style="text-align:center;width:32px">
												<span class="mticon-terminology" style="font-size:20px"></span>
											</td>
											<td style="text-align:left">
												<a href='<?php echo $pageURL.$path?>plugins/diary/addDiary.php'><?php echo lang('add to diary','w')?></a>
											</td>
										</tr>
									</table>
							<?php
								}
							?>
							<?php
								if(file_exists($baseURL."plugins/userMap/index.php")){
							?>
									<table>
										<tr>
											<td style="text-align:center;width:32px">
												<span class="mticon-logo" style="font-size:20px"></span>
											</td>
											<td style="text-align:left">
												<a href='<?php echo $pageURL.$path?>plugins/userMap/updateUserMap.php'><?php echo lang('update users file','c')?></a>
											</td>
										</tr>
									</table>
							<?php
								}
							?>
							<?php
								if(file_exists($baseURL."plugins/snow/index.php")){
							?>
									<table>
										<tr>
											<td style="text-align:center;width:32px">
												<span class="mticon-snow" style="font-size:20px"></span>
											</td>
											<td style="text-align:left">
												<a href='<?php echo $pageURL.$path?>plugins/snow/snow.php'><?php echo lang('add snow data','c')?></a>
											</td>
										</tr>
									</table>
							<?php
								}
							?>
							<?php
								if(file_exists($baseURL."homepage/blocks/myForecast/myForecastBlock.php")){
							?>
									<table>
										<tr>
											<td style="text-align:center;width:32px">
												<span class="fa fa-user-circle" style="font-size:20px"></span>
											</td>
											<td style="text-align:left">
												<a href='<?php echo $pageURL.$path?>homepage/blocks/myForecast/add.php'><?php echo lang('add my forecast','c')?></a>
											</td>
										</tr>
									</table>
							<?php
								}
							?>
						</div>
						<div style="display:inline-block;width:20%;padding-left:20px;padding-top:20px;vertical-align:top">
							<?php 
								if(trim($adminPlugins)!=""){
									$adminPluginsList = explode(";",$adminPlugins);
									for($ap=0;$ap<count($adminPluginsList);$ap++){
										$pluginNamespace = trim($adminPluginsList[$ap]);
										$thisLinkText = $pluginSpecs[$pluginNamespace][1];
										$thisLinkLink = $pageURL.$path."plugins/".$pluginNamespace."/".$pluginSpecs[$pluginNamespace][0];
										if(substr($pluginSpecs[$pluginNamespace][2],0,4)=="flag"){
											$thisPluginFlag = str_replace("flag(","",$pluginSpecs[$pluginNamespace][2]);
											$thisPluginFlag = str_replace(")","",$thisPluginFlag);
											$thisLinkIcon = '<img src="'.$pageURL.$path.'imgs/'.$flagIconShape.'/big/'.$thisPluginFlag.'.png" style="width:25px;max-height:25px;" class="menuItemIcon">';
										}
										else{
											$thisLinkIcon = '<span class="'.$pluginSpecs[$pluginNamespace][2].' menuItemIcon" style="font-size:20px;color:#'.$color_schemes[$design2]['100'].'"></span>';
										}
								?>
										<table>
											<tr>
												<td style="text-align:center;width:32px">
													<?php echo $thisLinkIcon?>
												</td>
												<td style="text-align:left" class="menuItemText">
													<a href="<?php echo $thisLinkLink?>"><?php echo lang(strtolower(trim($thisLinkText)),'c')?></a>
												</td>
											</tr>
										</table>
								<?php
									}
								?>
							<?php
								}
							?>
							<?php 
								if(file_exists($baseURL."admin/menu/adminLinks.txt")){
									$adminLinks = trim(file_get_contents($baseURL."admin/menu/adminLinks.txt"));
									$adminLinks = explode(";", $adminLinks);
									foreach($adminLinks as $adminLink){
										$adminLink = explode(",", $adminLink);
								?>
										<table>
											<tr>
												<td style="text-align:center;width:32px">
													<span class="fa fa-link"></span>
												</td>
												<td style="text-align:left" class="menuItemText">
													<a href="<?php echo $adminLink[1]?>"><?php echo $adminLink[0] ?></a>
												</td>
											</tr>
										</table>
								<?php
									}
								?>
							<?php
								}
							?>
						</div>
					</div>
				</li>
			</ul>
		<?php
			}
		?>
	</div>
</div>
<?php 
	file_put_contents($baseURL."sitemap.txt",json_encode($sitemap));
?>
<script>
$(function() {
	$(".menuSlideContent").hide();
	$(".menuSlideHeader").click(function(){
		$(this).next(".menuSlideContent").slideToggle(800);
	});
});
</script>
<script>
	$('.digimeg-main-nav>li>a').on('click', function (){
		var topOffset = $(document).scrollTop();
		$('.digimeg-sub-nav>li').fadeOut({ duration: <?php echo $menuSpeed?>});
		var navContentId = $(this).data('subnav');
		$('#'+navContentId).stop().slideToggle({ queue : false, duration: <?php echo $menuSpeed?>});
	});

	$(".menuItemText").mouseover(function(){
		$(this).closest('td').prev().find( ".menuItemIcon" ).addClass("animatedIcon");
	});
	$(".menuItemText").mouseleave(function(){
		$(this).closest('td').prev().find( ".menuItemIcon" ).removeClass("animatedIcon");
	});

	<?php
		if($menuType=="sticky"){
	?>
		$(window).scroll(function(e) {
			var scroller_anchor = $(".scroller_anchor").offset().top;
			var currentWidth = $('.digimeg-nav-wrap').css('width');
			if ($(this).scrollTop() >= scroller_anchor && $('.digimeg-nav-wrap').css('position') != 'fixed'){
				$('.digimeg-nav-wrap').css({
					'position': 'fixed',
					'top': '0px',
					'z-index': '100',
					'width':currentWidth
				});
				$('.scroller_anchor').css('height', '60px');
			}
			else if ($(this).scrollTop() < scroller_anchor && $('.digimeg-nav-wrap').css('position') != 'relative') {
				$('.scroller_anchor').css('height', '0px');
				$('.digimeg-nav-wrap').css({
					'position': 'relative',
					'z-index': '100',
					'width':currentWidth
				});
			}
		});
	<?php
		}

	?>
</script>
<script>
	$(".digimeg-nav-item").click(function(){	
		if ($(this).hasClass("selectedMenu")) {
			$(".digimeg-nav-item").removeClass("selectedMenu");
		}
		else{
			$(".digimeg-nav-item").removeClass("selectedMenu");
			$(this).addClass("selectedMenu");
		}		
	})
</script>
