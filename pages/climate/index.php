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
	#	Climate
	#
	# 	A script showing climate information for a specified location.
	#
	############################################################################
	#
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################


	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	//error_reporting(E_ALL);

	$climateID = $_GET['climateID'];

	$climateUnitsTemp = $displayTempUnits;
	$climateUnitsRain = $displayRainUnits;
	$climateUnitsElevation = $displayCloudbaseUnits;

	// load data for selected place from CSV
	$file = fopen("complete.csv","r");
	while(! feof($file))
	  {
		$tmp = fgetcsv($file);
		$data = explode(";",$tmp[0]);
		if($data[0]==$climateID){
			break;
		}
	  }
	fclose($file);

	$months = array("0", lang('janAbbr','c'), lang('febAbbr','c'), lang('marAbbr','c'), lang('aprAbbr','c'), lang('mayAbbr','c'), lang('junAbbr','c'), lang('julAbbr','c'), lang('augAbbr','c'), lang('sepAbbr','c'), lang('octAbbr','c'), lang('novAbbr','c'), lang('decAbbr','c'));

	$monthLengths = array("", 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

	$seasons = array(lang('spring','c'), lang('summer','c'), lang('autumn','c'), lang('winter','c'));

	// get variables and convert them to appropriate units
	$temps_max = array();
	$temps = array();
	$temps_min = array();
	$rains = array();
	$humidities = array();
	$sunlights = array();
	$wetdays = array();

	$row = array();
	$row['Name'] = $data[1];
	$row['Country'] = $data[2];
	$row['CountryCode'] = $data[3];
	$row['Lat'] = $data[7];
	$row['Lon'] = $data[8];
	$row['Region'] = $data[4];
	$row['Alt'] = $data[9];
	$row['Climate'] = $data[12];
	$row['Biome'] = $data[14];
	$row['Continent'] = $data[5];

	$row['Tmax_avg'] = convertTemperature($data[27],$climateUnitsTemp);
	$tmaxCheck = $data[15];
	$row['Tmax1'] = convertTemperature($data[15],$climateUnitsTemp);
	$row['Tmax2'] = convertTemperature($data[16],$climateUnitsTemp);
	$row['Tmax3'] = convertTemperature($data[17],$climateUnitsTemp);
	$row['Tmax4'] = convertTemperature($data[18],$climateUnitsTemp);
	$row['Tmax5'] = convertTemperature($data[19],$climateUnitsTemp);
	$row['Tmax6'] = convertTemperature($data[20],$climateUnitsTemp);
	$row['Tmax7'] = convertTemperature($data[21],$climateUnitsTemp);
	$row['Tmax8'] = convertTemperature($data[22],$climateUnitsTemp);
	$row['Tmax9'] = convertTemperature($data[23],$climateUnitsTemp);
	$row['Tmax10'] = convertTemperature($data[24],$climateUnitsTemp);
	$row['Tmax11'] = convertTemperature($data[25],$climateUnitsTemp);
	$row['Tmax12'] = convertTemperature($data[26],$climateUnitsTemp);

	$row['Tavg'] = convertTemperature($data[40],$climateUnitsTemp);
	$tavgCheck = $data[28];
	$row['T1'] = convertTemperature($data[28],$climateUnitsTemp);
	$row['T2'] = convertTemperature($data[29],$climateUnitsTemp);
	$row['T3'] = convertTemperature($data[30],$climateUnitsTemp);
	$row['T4'] = convertTemperature($data[31],$climateUnitsTemp);
	$row['T5'] = convertTemperature($data[32],$climateUnitsTemp);
	$row['T6'] = convertTemperature($data[33],$climateUnitsTemp);
	$row['T7'] = convertTemperature($data[34],$climateUnitsTemp);
	$row['T8'] = convertTemperature($data[35],$climateUnitsTemp);
	$row['T9'] = convertTemperature($data[36],$climateUnitsTemp);
	$row['T10'] = convertTemperature($data[37],$climateUnitsTemp);
	$row['T11'] = convertTemperature($data[38],$climateUnitsTemp);
	$row['T12'] = convertTemperature($data[39],$climateUnitsTemp);

	$row['Tmin_avg'] = convertTemperature($data[55],$climateUnitsTemp);
	$tminCheck = $data[43];
	$row['Tmin1'] = convertTemperature($data[43],$climateUnitsTemp);
	$row['Tmin2'] = convertTemperature($data[44],$climateUnitsTemp);
	$row['Tmin3'] = convertTemperature($data[45],$climateUnitsTemp);
	$row['Tmin4'] = convertTemperature($data[46],$climateUnitsTemp);
	$row['Tmin5'] = convertTemperature($data[47],$climateUnitsTemp);
	$row['Tmin6'] = convertTemperature($data[48],$climateUnitsTemp);
	$row['Tmin7'] = convertTemperature($data[49],$climateUnitsTemp);
	$row['Tmin8'] = convertTemperature($data[50],$climateUnitsTemp);
	$row['Tmin9'] = convertTemperature($data[51],$climateUnitsTemp);
	$row['Tmin10'] = convertTemperature($data[52],$climateUnitsTemp);
	$row['Tmin11'] = convertTemperature($data[53],$climateUnitsTemp);
	$row['Tmin12'] = convertTemperature($data[54],$climateUnitsTemp);

	$row['Rain1'] = convertRain($data[56],$climateUnitsRain);
	$rainCheck = $data[57];
	$row['Rain2'] = convertRain($data[57],$climateUnitsRain);
	$row['Rain3'] = convertRain($data[58],$climateUnitsRain);
	$row['Rain4'] = convertRain($data[59],$climateUnitsRain);
	$row['Rain5'] = convertRain($data[60],$climateUnitsRain);
	$row['Rain6'] = convertRain($data[61],$climateUnitsRain);
	$row['Rain7'] = convertRain($data[62],$climateUnitsRain);
	$row['Rain8'] = convertRain($data[63],$climateUnitsRain);
	$row['Rain9'] = convertRain($data[64],$climateUnitsRain);
	$row['Rain10'] = convertRain($data[65],$climateUnitsRain);
	$row['Rain11'] = convertRain($data[66],$climateUnitsRain);
	$row['Rain12'] = convertRain($data[67],$climateUnitsRain);

	$row['Wetdays1'] = $data[71];
	$row['Wetdays2'] = $data[72];
	$row['Wetdays3'] = $data[73];
	$row['Wetdays4'] = $data[74];
	$row['Wetdays5'] = $data[75];
	$row['Wetdays6'] = $data[76];
	$row['Wetdays7'] = $data[77];
	$row['Wetdays8'] = $data[78];
	$row['Wetdays9'] = $data[79];
	$row['Wetdays10'] = $data[80];
	$row['Wetdays11'] = $data[81];
	$row['Wetdays12'] = $data[82];

	$row['Sunlight_avg'] = $data[96];
	$row['Sunlight1'] = $data[84];
	$row['Sunlight2'] = $data[85];
	$row['Sunlight3'] = $data[86];
	$row['Sunlight4'] = $data[87];
	$row['Sunlight5'] = $data[88];
	$row['Sunlight6'] = $data[89];
	$row['Sunlight7'] = $data[90];
	$row['Sunlight8'] = $data[91];
	$row['Sunlight9'] = $data[92];
	$row['Sunlight10'] = $data[93];
	$row['Sunlight11'] = $data[94];
	$row['Sunlight12'] = $data[95];

	$row['Humidity_avg'] = $data[109];
	$row['Humidity1'] = $data[97];
	$row['Humidity2'] = $data[98];
	$row['Humidity3'] = $data[99];
	$row['Humidity4'] = $data[100];
	$row['Humidity5'] = $data[101];
	$row['Humidity6'] = $data[102];
	$row['Humidity7'] = $data[103];
	$row['Humidity8'] = $data[104];
	$row['Humidity9'] = $data[105];
	$row['Humidity10'] = $data[106];
	$row['Humidity11'] = $data[107];
	$row['Humidity12'] = $data[108];

	$country = $row['Country'];
	$country_code = $row['CountryCode'];
	$lat = $row['Lat'];
	$lon = $row['Lon'];
	$region = $row['Region'];

	// determine coordinates
	if ($lat > 0) {
		$lat_text = number_format($lat, 2, ".", "") . " ° ".lang('coordN','u');
	} else {
		$lat_text = number_format(($lat * -1), 2, ".", "") . " ° ".lang('coordS','u');
	}
	if ($lon > 0) {
		$lon_text = number_format($lon, 2, ".", "") . " ° ".lang('coordE','u');
	} else {
		$lon_text = number_format(($lon * -1), 2, ".", "") . " ° ".lang('coordW','u');
	}

	$alt = convertElevation($row['Alt'],$climateUnitsElevation);
	$climate = $row['Climate'];
	$biome = $row['Biome'];
	$continent = $row['Continent'];
	$name = $row['Name'];

	array_push($temps_max, $row['Tmax_avg']);
	for($i=1;$i<13;$i++){
		array_push($temps_max, $row['Tmax'.$i]);
	}

	array_push($temps, $row['Tavg']);
	for($i=1;$i<13;$i++){
		array_push($temps, $row['T'.$i]);
	}

	array_push($temps_min, $row['Tmin_avg']);
	for($i=1;$i<13;$i++){
		array_push($temps_min, $row['Tmin'.$i]);
	}

	array_push($rains, "");
	for($i=1;$i<13;$i++){
		array_push($rains, $row['Rain'.$i]);
	}
	$rain_avg = array_sum($rains) / 12;
	$rains[0] = $rain_avg;

	array_push($wetdays, "");
	for($i=1;$i<13;$i++){
		array_push($wetdays, $row['Wetdays'.$i]);
	}
	$wetdays_avg = array_sum($wetdays) / 12;
	$wetdays[0] = $wetdays_avg;

	array_push($sunlights, $row['Sunlight_avg']);
	for($i=1;$i<13;$i++){
		array_push($sunlights, $row['Sunlight'.$i]);
	}

	array_push($humidities, $row['Humidity_avg']);
	for($i=1;$i<13;$i++){
		array_push($humidities, $row['Humidity'.$i]);
	}

	// check what variables are available for selected place, initially set all to N/A
	$climate_state = false;
	$biome_state = false;
	$temps_max_state = false;
	$temps_state = false;
	$temps_min_state = false;
	$rains_state = false;
	$humidities_state = false;
	$wetdays_state = false;
	$sunlights_state = false;

	if ($climate != "" && $climate != "-") {
		$climate_state = true;
	}
	if ($biome != "" && $biome != "-") {
		$biome_state = true;
	}
	if ($tmaxCheck > -9999.0) {
		$temps_max_state = true;
	}
	if ($tavgCheck > -9999.0) {
		$temps_state = true;
	}
	if ($tminCheck > -9999.0) {
		$temps_min_state = true;
	}
	if ($rainCheck > -9999.0) {
		$rains_state = true;
	}
	if ($humidities[0] > -9999.0) {
		$humidities_state = true;
	}
	if ($wetdays[0] > -9999.0) {
		$wetdays_state = true;
	}
	if ($sunlights[0] > -9999.0) {
		$sunlights_state = true;
	}


	//  ##############################################  TEMPERATURE
	$max_temp_max = max($temps_max);
	$min_temp_max = min($temps_max);
	$max_temp = max($temps);
	$min_temp = min($temps);
	$max_temp_min = max($temps_min);
	$min_temp_min = min($temps_min);

	$temp_ranges = array();
	array_push($temp_ranges, ($temps_max[0] - $temps_min[0]));
	for ($i = 1; $i < 13; $i++) {
		array_push($temp_ranges, ($temps_max[$i] - $temps_min[$i]));
	}
	$max_temp_range = max($temp_ranges);
	$min_temp_range = min($temp_ranges);


	$max_temp_max_month = array_keys($temps_max, max($temps_max)); // array with month(s) with maximal maximum temperature
	$max_temp_max_month_text = array();
	for ($i = 0; $i < count($max_temp_max_month); $i++) {
		array_push($max_temp_max_month_text, $months[$max_temp_max_month[$i]]);
	} // array with month(s) with maximal maximum temperature, months as text
	$min_temp_max_month = array_keys($temps_max, min($temps_max));
	$min_temp_max_month_text = array();
	for ($i = 0; $i < count($min_temp_max_month); $i++) {
		array_push($min_temp_max_month_text, $months[$min_temp_max_month[$i]]);
	}
	$max_temp_min_month = array_keys($temps_min, max($temps_min));
	$max_temp_min_month_text = array();
	for ($i = 0; $i < count($max_temp_min_month); $i++) {
		array_push($max_temp_min_month_text, $months[$max_temp_min_month[$i]]);
	}
	$min_temp_min_month = array_keys($temps_min, min($temps_min));
	$min_temp_min_month_text = array();
	for ($i = 0; $i < count($min_temp_min_month); $i++) {
		array_push($min_temp_min_month_text, $months[$min_temp_min_month[$i]]);
	}
	$max_temp_month = array_keys($temps, max($temps));
	$max_temp_month_text = array();
	for ($i = 0; $i < count($max_temp_month); $i++) {
		array_push($max_temp_month_text, $months[$max_temp_month[$i]]);
	}
	$min_temp_month = array_keys($temps, min($temps));
	$min_temp_month_text = array();
	for ($i = 0; $i < count($min_temp_month); $i++) {
		array_push($min_temp_month_text, $months[$min_temp_month[$i]]);
	}
	$max_temp_range_month = array_keys($temp_ranges, max($temp_ranges));
	$max_temp_range_month_text = array();
	for ($i = 0; $i < count($max_temp_range_month); $i++) {
		array_push($max_temp_range_month_text, $months[$max_temp_range_month[$i]]);
	}
	$min_temp_range_month = array_keys($temp_ranges, min($temp_ranges));
	$min_temp_range_month_text = array();
	for ($i = 0; $i < count($min_temp_range_month); $i++) {
		array_push($min_temp_range_month_text, $months[$min_temp_range_month[$i]]);
	}

	// seasons
	if ($lat > 0) {
		$hemisphere = "N";
	}
	if ($lat < 0) {
		$hemisphere = "S";
	}
	if ($hemisphere == "N") {
		$spring_temps_max = array($temps_max[3], $temps_max[4], $temps_max[5]);
		$spring_temps = array($temps[3], $temps[4], $temps[5]);
		$spring_temps_min = array($temps_min[3], $temps_min[4], $temps_min[5]);

		$summer_temps_max = array($temps_max[6], $temps_max[7], $temps_max[8]);
		$summer_temps = array($temps[6], $temps[7], $temps[8]);
		$summer_temps_min = array($temps_min[6], $temps_min[7], $temps_min[8]);

		$autumn_temps_max = array($temps_max[9], $temps_max[10], $temps_max[11]);
		$autumn_temps = array($temps[9], $temps[10], $temps[11]);
		$autumn_temps_min = array($temps_min[9], $temps_min[10], $temps_min[11]);

		$winter_temps_max = array($temps_max[12], $temps_max[1], $temps_max[2]);
		$winter_temps = array($temps[12], $temps[1], $temps[2]);
		$winter_temps_min = array($temps_min[12], $temps_min[1], $temps_min[2]);
	}
	if ($hemisphere == "S") {
		$autumn_temps_max = array($temps_max[3], $temps_max[4], $temps_max[5]);
		$autumn_temps = array($temps[3], $temps[4], $temps[5]);
		$autumn_temps_min = array($temps_min[3], $temps_min[4], $temps_min[5]);

		$winter_temps_max = array($temps_max[6], $temps_max[7], $temps_max[8]);
		$winter_temps = array($temps[6], $temps[7], $temps[8]);
		$winter_temps_min = array($temps_min[6], $temps_min[7], $temps_min[8]);

		$spring_temps_max = array($temps_max[9], $temps_max[10], $temps_max[11]);
		$spring_temps = array($temps[9], $temps[10], $temps[11]);
		$spring_temps_min = array($temps_min[9], $temps_min[10], $temps_min[11]);

		$summer_temps_max = array($temps_max[12], $temps_max[1], $temps_max[2]);
		$summer_temps = array($temps[12], $temps[1], $temps[2]);
		$summer_temps_min = array($temps_min[12], $temps_min[1], $temps_min[2]);
	}
	// season averages
	$spring_temps_max_avg = array_sum($spring_temps_max) / count($spring_temps_max);
	$summer_temps_max_avg = array_sum($summer_temps_max) / count($summer_temps_max);
	$autumn_temps_max_avg = array_sum($autumn_temps_max) / count($autumn_temps_max);
	$winter_temps_max_avg = array_sum($winter_temps_max) / count($winter_temps_max);

	$spring_temps_avg = array_sum($spring_temps) / count($spring_temps);
	$summer_temps_avg = array_sum($summer_temps) / count($summer_temps);
	$autumn_temps_avg = array_sum($autumn_temps) / count($autumn_temps);
	$winter_temps_avg = array_sum($winter_temps) / count($winter_temps);

	$spring_temps_min_avg = array_sum($spring_temps_min) / count($spring_temps_min);
	$summer_temps_min_avg = array_sum($summer_temps_min) / count($summer_temps_min);
	$autumn_temps_min_avg = array_sum($autumn_temps_min) / count($autumn_temps_min);
	$winter_temps_min_avg = array_sum($winter_temps_min) / count($winter_temps_min);

	// ##############################################  HUMIDITY
	$max_hum = max($humidities);
	$min_hum = min($humidities);

	$max_hum_month = array_keys($humidities, max($humidities)); // array with month(s) with maximal humidity
	$max_hum_month_text = array();
	for ($i = 0; $i < count($max_hum_month); $i++) {
		array_push($max_hum_month_text, $months[$max_hum_month[$i]]);
	} // array with month(s) with maximal maximum temperature, months as text
	$min_hum_month = array_keys($humidities, min($humidities));
	$min_hum_month_text = array();
	for ($i = 0; $i < count($min_hum_month); $i++) {
		array_push($min_hum_month_text, $months[$min_hum_month[$i]]);
	}

	// seasons

	if ($hemisphere == "N") {
		$spring_hum = array($humidities[3], $humidities[4], $humidities[5]);
		$summer_hum = array($humidities[6], $humidities[7], $humidities[8]);
		$autumn_hum = array($humidities[9], $humidities[10], $humidities[11]);
		$winter_hum = array($humidities[12], $humidities[1], $humidities[2]);
	}
	if ($hemisphere == "S") {
		$autumn_hum = array($humidities[3], $humidities[4], $humidities[5]);
		$winter_hum = array($humidities[6], $humidities[7], $humidities[8]);
		$spring_hum = array($humidities[9], $humidities[10], $humidities[11]);
		$summer_hum = array($humidities[12], $humidities[1], $humidities[2]);
	}
	$spring_hum_avg = array_sum($spring_hum) / count($spring_hum);
	$summer_hum_avg = array_sum($summer_hum) / count($summer_hum);
	$autumn_hum_avg = array_sum($autumn_hum) / count($autumn_hum);
	$winter_hum_avg = array_sum($winter_hum) / count($winter_hum);

	// ##############################################  PRECIPITATION
	$max_rain = max($rains);
	$min_rain = min($temps_max);
	$max_wetdays = max($wetdays);
	$min_wetdays = min($wetdays);

	$max_rain_month = array_keys($rains, max($rains)); // array with month(s) with maximal maximum humidity
	$max_rain_month_text = array();
	for ($i = 0; $i < count($max_rain_month); $i++) {
		array_push($max_rain_month_text, $months[$max_rain_month[$i]]);
	} // array with month(s) with maximal humidity, months as text
	$min_rain_month = array_keys($rains, min($rains));
	$min_rain_month_text = array();
	for ($i = 0; $i < count($min_rain_month); $i++) {
		array_push($min_rain_month_text, $months[$min_rain_month[$i]]);
	}
	$max_wetdays_month = array_keys($wetdays, max($wetdays));
	$max_wetdays_month_text = array();
	for ($i = 0; $i < count($max_wetdays_month); $i++) {
		array_push($max_wetdays_month_text, $months[$max_wetdays_month[$i]]);
	}
	$min_wetdays_month = array_keys($wetdays, min($wetdays));
	$min_wetdays_month_text = array();
	for ($i = 0; $i < count($min_wetdays_month); $i++) {
		array_push($min_wetdays_month_text, $months[$min_wetdays_month[$i]]);
	}

	$rains_total = array_sum($rains) - $rains[0];
	$wetdays_total = array_sum($wetdays) - $wetdays[0];
	// seasons
	if ($hemisphere == "N") {
		$spring_rains = array($rains[3], $rains[4], $rains[5]);
		$spring_wetdays = array($wetdays[3], $wetdays[4], $wetdays[5]);

		$summer_rains = array($rains[6], $rains[7], $rains[8]);
		$summer_wetdays = array($wetdays[6], $wetdays[7], $wetdays[8]);

		$autumn_rains = array($rains[9], $rains[10], $rains[11]);
		$autumn_wetdays = array($wetdays[9], $temps[10], $temps[11]);

		$winter_rains = array($rains[12], $rains[1], $rains[2]);
		$winter_wetdays = array($wetdays[12], $wetdays[1], $wetdays[2]);
	}
	if ($hemisphere == "S") {
		$autumn_rains = array($rains[3], $rains[4], $rains[5]);
		$autumn_wetdays = array($wetdays[3], $wetdays[4], $wetdays[5]);

		$winter_rains = array($rains[6], $rains[7], $rains[8]);
		$winter_wetdays = array($wetdays[6], $wetdays[7], $wetdays[8]);

		$spring_rains = array($rains[9], $rains[10], $rains[11]);
		$spring_wetdays = array($wetdays[9], $wetdays[10], $wetdays[11]);

		$summer_rains = array($rains[12], $rains[1], $rains[2]);
		$summer_wetdays = array($wetdays[12], $wetdays[1], $wetdays[2]);
	}
	$rain_season_totals = array(array_sum($spring_rains), array_sum($summer_rains), array_sum($autumn_rains), array_sum($winter_rains));
	$wetdays_season_totals = array(array_sum($spring_wetdays), array_sum($summer_wetdays), array_sum($autumn_wetdays), array_sum($winter_wetdays));

	$max_rain_season = max($rain_season_totals);
	$min_rain_season = min($rain_season_totals);
	$max_wetdays_season = max($wetdays_season_totals);
	$min_wetdays_season = min($wetdays_season_totals);

	$max_rain_season_month = array_keys($rain_season_totals, max($rain_season_totals)); // array with season(s) with maximal rain
	$max_rain_season_month_text = array();
	for ($i = 0; $i < count($max_rain_season_month); $i++) {
		array_push($max_rain_season_month_text, $seasons[$max_rain_season_month[$i]]);
	} // array with month(s) with maximal rain, months as text
	$min_rain_season_month = array_keys($rain_season_totals, min($rain_season_totals));
	$min_rain_season_month_text = array();
	for ($i = 0; $i < count($min_rain_season_month); $i++) {
		array_push($min_rain_season_month_text, $seasons[$min_rain_season_month[$i]]);
	}
	$max_wetdays_season_month = array_keys($wetdays_season_totals, max($wetdays_season_totals));
	$max_wetdays_season_month_text = array();
	for ($i = 0; $i < count($max_wetdays_season_month); $i++) {
		array_push($max_wetdays_season_month_text, $seasons[$max_wetdays_season_month[$i]]);
	}
	$min_wetdays_season_month = array_keys($wetdays_season_totals, min($wetdays_season_totals));
	$min_wetdays_season_month_text = array();
	for ($i = 0; $i < count($min_wetdays_season_month); $i++) {
		array_push($min_wetdays_season_month_text, $seasons[$min_wetdays_season_month[$i]]);
	}

	// monthly wetdays percentages
	$wetdays_month_percentages = array();
	array_push($wetdays_month_percentages, ($wetdays[0] / (365 / 12)) * 100); //calculate arbitrary average percentage for year
	for ($i = 1; $i < 13; $i++) {
		array_push($wetdays_month_percentages, (($wetdays[$i] / $monthLengths[$i]) * 100));
	}

	//  ##############################################  SUNLIGHT AND DAYLIGHT
	$daylengths_year = array();

	// calculate day length based on coordinates and date
	for($a=1;$a<13;$a++){
		$currentMonthLength = date("t",strtotime("2016-".$a."-15"));
		for ($i = 1; $i <= $currentMonthLength; $i++) {
			$date = $i . "-".$a."-2016";
			$rise = date_sunrise(strtotime($date), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90.5);
			$set = date_sunset(strtotime($date), SUNFUNCS_RET_TIMESTAMP, $lat, $lon, 90.5);
			$length = number_format(($set - $rise) / 60, 3, ".", "");
			if ($rise == "" || $set == "") {
				if ($hemisphere == "N") {
					$length = 0;
				}
				if ($hemisphere == "S") {
					$length = 1440;
				}
			}
			${"daylengths".$a}[$i] = $length;
		}
	}

	$daylengths = array("", $daylengths1, $daylengths2, $daylengths3, $daylengths4, $daylengths5, $daylengths6, $daylengths7, $daylengths8, $daylengths9, $daylengths10, $daylengths11, $daylengths12);
	$daylengths_year = array_merge($daylengths1, $daylengths2, $daylengths3, $daylengths4, $daylengths5, $daylengths6, $daylengths7, $daylengths8, $daylengths9, $daylengths10, $daylengths11, $daylengths12);

	//seasons
	if ($hemisphere == "N") {
		$spring_sunlights = array($sunlights[3], $sunlights[4], $sunlights[5]);
		$summer_sunlights = array($sunlights[6], $sunlights[7], $sunlights[8]);
		$autumn_sunlights = array($sunlights[9], $sunlights[10], $sunlights[11]);
		$winter_sunlights = array($sunlights[12], $sunlights[1], $sunlights[2]);

		$spring_daylengths = array_merge($daylengths3, $daylengths4, $daylengths5);
		$summer_daylengths = array_merge($daylengths6, $daylengths7, $daylengths8);
		$autumn_daylengths = array_merge($daylengths9, $daylengths10, $daylengths11);
		$winter_daylengths = array_merge($daylengths12, $daylengths1, $daylengths2);
	}
	if ($hemisphere == "S") {
		$autumn_sunlights = array($sunlights[3], $sunlights[4], $sunlights[5]);
		$winter_sunlights = array($sunlights[6], $sunlights[7], $sunlights[8]);
		$spring_sunlights = array($sunlights[9], $sunlights[10], $sunlights[11]);
		$summer_sunlights = array($sunlights[12], $sunlights[1], $sunlights[2]);

		$autumn_daylengths = array_merge($daylengths3, $daylengths4, $daylengths5);
		$winter_daylengths = array_merge($daylengths6, $daylengths7, $daylengths8);
		$spring_daylengths = array_merge($daylengths9, $daylengths10, $daylengths11);
		$summer_daylengths = array_merge($daylengths12, $daylengths1, $daylengths2);
	}

	//  ##############################################  CLIMATE CALCULATIONS
	$climate_Koppen = "";
	$climate_trewartha = "";

	if(file_exists("climate_types_".$lang.".php")){
		include('climate_types_'.$lang.'.php');
	}
	else{
		include('climate_types_gb.php');
	}

	if ($temps[0] != -9999) {
		if ($rains[0] != -9999) {
			$climate_Koppen = Koppen($hemisphere, $data[28], $data[29], $data[30], $data[31], $data[32], $data[33], $data[34], $data[35], $data[36], $data[37], $data[38], $data[39], $data[56], $data[57], $data[58], $data[59], $data[60], $data[61], $data[62], $data[63], $data[64], $data[65], $data[66], $data[67]);
			$climate_trewartha = trewartha($hemisphere, $data[28], $data[29], $data[30], $data[31], $data[32], $data[33], $data[34], $data[35], $data[36], $data[37], $data[38], $data[39], $data[56], $data[57], $data[58], $data[59], $data[60], $data[61], $data[62], $data[63], $data[64], $data[65], $data[66], $data[67]);
		}
	}
	if ($climate_Koppen == "") {
		climateDesc($climate);
	} else {
		climateDesc($climate_Koppen);
		$climate = $climate_Koppen;
		climateDesctrewartha($climate_trewartha);
	}

	// calculate spherical coordinates
	if ($lat > 0) {
		$coordinate1 = (($lat) / 90) * (3.14159 * 0.5);
	}
	if ($lat < 0) {
		$coordinate1 = (($lat * -1) / 90) * (3.14159 * 0.5) + 3.14159 * 0.5;
	}

	// conversion functions
	function time_conversion_h($length) {
		$hours = floor($length / 60);
		$minutes = number_format($length - ($hours * 60), 0, ".", "");
		return $hours;
	}
	function time_conversion_min($length) {
		$hours = floor($length / 60);
		$minutes = number_format($length - ($hours * 60), 0, ".", "");
		return $minutes;
	}

	function convertTemperature($value,$climateUnitsTemp){
		if($climateUnitsTemp=="F"){
			$final = round(($value * 9/5) + 32,0);
		}
		else{
			$final = $value;
		}
		return $final;
	}

	function convertRain($value,$climateUnitsRain){
		if($climateUnitsRain=="in"){
			$final = round(($value * 0.0393701),2);
		}
		else{
			$final = $value;
		}
		return $final;
	}

	function convertElevation($value,$climateUnitsElevation){
		if($climateUnitsElevation=="ft"){
			$final = round($value * 3.28084,0);
		}
		else{
			$final = $value;
		}
		return $final;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('climate','c')?></title>

		<?php metaHeader()?>
		<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key=<?php echo $googleMapsAPIKey?>"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/infobox.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.sparkline.min.js"></script>
		<style>
			#main2{
				margin:0 auto;
				width: 94%;
				padding: 2%;
			}
			#intro{
				width: 100%;
				background: #666666;
			}
			#summary{
				width: 94%;
				margin:0 auto;
				background: #<?php echo $color_schemes[$design2]['900']?>;
				padding: 2%;
			}

			#temperature, #temperature-lite, #humidity, #rain, #rain-lite, #light, #light-lite{
				width: 94%;
				margin:0 auto;
				padding-left: 2%;
				padding-right: 2%;
				position: relative;
				background: #<?php echo $color_schemes[$design2]['900']?>;
			}
			#info{
				display: inline-block;
				float:left;
				width: 580px;
				height: 300px;
				background: #666666;
				padding: 10px;
			}
			#map{
				display: inline-block;
				float:right;
				width: 100%;
				height: 320px;
				padding: 10px;
			}
			.icon{
				padding-right: 25px;
				width: 30px;
			}
			.variable{
				padding: 25px;
				width: 40px;
				font-size: 2em;
			}
			.section_heading{
				font-size: 34px;
				color: #<?php echo $color_schemes[$design]['font700']?>;;
				font-variant: small-caps;
			}
			.datagrid {
				width: 100%;
				font: normal 12px/150% Arial, Helvetica, sans-serif;
				background: #fff;
				overflow: hidden;
			}
			.datagrid table {
				border-collapse: collapse;
				text-align: left;
				width: 100%;
			}
			.datagrid table td, .datagrid table th {
				padding: 5px 9px;
				text-align: right;
			}
			.datagrid table thead th {
				text-align: center;
				background-color:#<?php echo $color_schemes[$design2]['600']?>;
				color:#<?php echo $color_schemes[$design2]['font800']?>;
				font-size: 15px;
				font-weight: bold;
				border-left: 1px solid #<?php echo $color_schemes[$design]['900']?>;
			}
			.datagrid table thead th:first-child {
				border: none;
			}
			.datagrid table tbody td {
				color: #000000;
				border-left: 1px solid #8A8A8A;
				font-size: 14px;
				font-weight: normal;
			}
			.datagrid table tbody .alt td {
				background: #EBEBEB; color: #000000;
			}
			.datagrid table tbody td:first-child {
				border-left: none;
			}
			.datagrid table tbody tr:last-child td {
				border-bottom: none;
			}
			#climate-opener, #temperature-opener, #humidity-opener, #rain-opener, #light-opener, #temperature-opener-lite, #rain-opener-lite, #light-opener-lite{
				cursor: pointer;
			}
			#temperature-decor, #humidity-decor, #rain-decor, #light-decor, #temperature-decor-lite, #rain-decor-lite, #light-decor-lite{
				padding-left: 2%;
				padding-right: 2%;
				height: 10px;
				width: 94%;
				margin:0 auto;
			}

			#temperature-decor{
				background: #FF4C4C;
			}
			#humidity-decor{
				background: #2DB300;
			}
			#rain-decor{
				background: #2693FF;
			}
			#light-decor{
				background: #FFFF26;
			}
			#temperature-decor-lite{
				background: #FF4C4C;
			}
			#rain-decor-lite{
				background: #2693FF;
			}
			#light-decor-lite{
				background: #FFFF26;
			}
			.hidden{
				display: none;
			}
			.table th{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design]['font400']?>;
			}
			.tablePadding3 td{
				padding: 3px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
		<h1><?php echo $name?></h1>
		<div id="main2">
			<table style="width:100%" class="tablePadding3">
				<tr>
					<td style="width:50%">
						<table>
							<tr>
								<td style="text-align:center">
									<img src="<?php echo $pageURL.$path?>imgs/climateImgs/outlines/<?php echo $country_code ?>.png" style="width:140px" alt="">
								</td>
								<td>
									<table>
										<tr>
											<td>
												<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo $country_code?>.png" class="icon"  alt="">
											</td>
											<td>
												<?php
													echo " ";
													if ($region != "") {
														echo $region . ", ";
													}
													if (array_key_exists($country, $language[$lang])) {
														echo lang($country,'');
													}
													else{
														echo $country;
													}
												?>
											</td>
										</tr>
										<tr>
											<td>
												<span class='mticon-gps' style='font-size:2.7em'></span>
											</td>
											<td>
												<?php
													echo $lat_text . "<br>" . $lon_text;
												?>
											</td>
										</tr>
										<tr>
											<td>
												<span class='mticon-elevation' style='font-size:2.7em'></span>
											</td>
											<td>
												<?php echo $alt . " " . $climateUnitsElevation ?>
											</td>
										</tr>
										<?php
											if ($biome != "-") {
										?>
											<tr>
												<td>
													<span class='mticon-globe' style='font-size:2.7em'></span>
												</td>
												<td>
													<?php echo lang($biome,'') ?>
												</td>
											</tr>
										<?php
											}
										?>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<td style="width:50%">
						<div id="map">
						</div>
					</td>
				</tr>
				<?php
					if ($climate != "-") {
				?>
					<tr>
						<td colspan="2" style="height:30px">
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table style="width:100%;border-spacing:0px">
								<tr>
									<td style="width: 40px; height: 40px; text-align: center; color: <?php echo $climate_text_color ?>; background-color: <?php echo $climate_color ?>">
										<b>
											<?php echo $climate ?>
										</b>
									</td>
									<td style="padding-left: 15px; text-align:left">
										<?php echo $climate_name ?>
									</td>
									<td style="width:10px; text-align:right">
										<div>
											<span class="section_heading" id="climate-opener">
												+
											</span>
										</div>
									</td>
								</tr>
								<?php
									if ($climate_trewartha != "") {
								?>
									<tr>
										<td style="width: 40px; height: 40px; text-align: center; color: <?php echo $climate_text_color_trewartha ?>; background-color: <?php echo $climate_color_trewartha ?>">
											<b><?php echo $climate_trewartha ?></b>
										</td>
										<td style="padding-left: 15px; text-align:left">
											<?php echo $climate_name_trewartha ?>
										</td>
										<td style="width:10px;text-align:center">
										</td>
									</tr>
								<?php
									}
								?>
								<tr>
									<td colspan="3">
										<div id="climate_div">
											<?php
												echo $Koppen_desc;
												if ($climate_trewartha != "") {
													echo "<br>" . $Trewartha_desc;
												}
											?>
										</div>
									</td>
								</tr>
								<tr>
									<td colspan="3" style="height: 2px; background-color: <?php echo $climate_color ?>">
									</td>
								</tr>
								<?php
									if ($climate_trewartha != "") {
								?>
									<tr>
										<td colspan="3" style="height: 2px; background-color: <?php echo $climate_color_trewartha ?>">
										</td>
									</tr>
								<?php
									}
								?>
							</table>
						</td>
					</tr>
				<?php
					}
				?>
			</table>
		</div>
		<br>
		<div id="summary">
			<span style="text-align:center" class="section_heading">
				<?php echo lang('summary','c') ?>
			</span>
			<table class="zeroSpacingPadding">
				<tr>
					<?php
						if ($temps_state == true && $temps_max_state == false) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-temp variable'></span><br>" . $temps[0] . " °".$climateUnitsTemp;
							echo "</td>";
						}
						if ($temps_max_state == true) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-temp variable'></span><br>" . $temps[0] . " °".$climateUnitsTemp."<br>" . $temps_min[0] . " / " . $temps_max[0];
							echo "</td>";
						}
						if ($rains_state == true && $wetdays_state == false) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-rain variable'></span><br>" . number_format($rains_total, 1, ".", "") . " ".$climateUnitsRain;
							echo "</td>";
						}
						if ($rains_state == true && $wetdays_state == true) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-rain variable'></span><br>" . number_format($rains_total, 1, ".", "") . " ".$climateUnitsRain."<br>" . number_format($wetdays_total, 0, ".", "") . " ".lang('days','l');
							echo "</td>";
						}
						if ($humidities_state == true) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-humidity variable'></span><br>" . $humidities[0] . " %";
							echo "</td>";
						}
						if ($sunlights_state == true) {
							echo "<td style='text-align:center'>";
							echo "<span class='mticon-sun variable'></span><br>" . (floor($sunlights[0] / 60)) . " h " . ($sunlights[0] - (60 * (floor($sunlights[0] / 60)))) . " ".lang('minAbbr','l')."/".lang('day','l');
							echo "</td>";
						}
					?>
				</tr>
			</table>
			<br>
			<table style="width:100%">
				<tr>
					<td style='text-align:center;width:50%'>
						<div id="main_graph" style="width: 100%;margin-left:auto;margin-right:auto">
						</div>
					</td>
					<td style='text-align:center;width:50%'>
						<div id="minor_graph" style="width: 100%;margin-left:auto;margin-right:auto">
						</div>
					</td>
				</tr>
			</table>
		</div>
		<br>
		<div id="temperature">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
					<span class="section_heading">
						<?php echo lang('temperature','c')?>
					</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" id="temperature-opener" style="text-align:right">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>
			<div id="temperature-content">
				<br />
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="12" style="text-align:center">
									<?php echo lang('month','c')?>
								</th>
								<th rowspan="2" style="text-align:center">
									<?php echo lang('average','c')?>
								</th>
							</tr>
							<tr>
								<?php
									for($i=1;$i<13;$i++){
								?>
									<th style="text-align:center">
										<?php echo $i?>
									</th>
								<?php
									}
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('minimumAbbr','c')." ".lang('temperature','c')?>(°<?php echo $climateUnitsTemp ?>)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($temps_min[$i] == $max_temp_min) {
											echo "<td style='color: red'>" . number_format($temps_min[$i], 1, ".", "") . "</td>";
										}
										else if ($temps_min[$i] == $min_temp_min) {
											echo "<td style='color: blue'>" . number_format($temps_min[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($temps_min[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($temps_min[0], 1, ".", "") ?></b>
								</td>
							</tr>
							<tr class="alt">
								<td style="text-align:left">
									<?php echo lang('avgAbbr','c')." ".lang('temperature','c') ?>(°<?php echo $climateUnitsTemp ?>)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($temps[$i] == $max_temp) {
											echo "<td style='color: red'>" . number_format($temps[$i], 1, ".", "") . "</td>";
										}
										else if ($temps[$i] == $min_temp) {
											echo "<td style='color: blue'>" . number_format($temps[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($temps[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($temps[0], 1, ".", "") ?></b>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('maximumAbbr','c')." ".lang('temperature','c') ?>
										(°<?php echo $climateUnitsTemp ?>)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($temps_max[$i] == $max_temp_max) {
											echo "<td style='color: red'>" . number_format($temps_max[$i], 1, ".", "") . "</td>";
										}
										else if ($temps_max[$i] == $min_temp_max) {
											echo "<td style='color: blue'>" . number_format($temps_max[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($temps_max[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($temps_max[0], 1, ".", "") ?></b>
								</td>
							</tr>
							<tr class="alt">
								<td style="text-align:left">
									<?php echo lang('range','c') ?>
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($temp_ranges[$i] == $max_temp_range) {
											echo "<td style='color: red'>" . number_format($temp_ranges[$i], 1, ".", "") . "</td>";
										}
										else if ($temp_ranges[$i] == $min_temp_range) {
											echo "<td style='color: blue'>" . number_format($temp_ranges[$i], 1, ".", "") . "</td>";
										}
										  else {
											echo "<td>" . number_format($temp_ranges[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($temp_ranges[0], 1, ".", "") ?></b>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_temp" style="height:400px; width: 100%" class="graph_div"></div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:left">
							<div class="datagrid" style="width:100%;display:inline-block">
								<table style="width:100%">
									<thead>
										<tr>
											<th>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('month','l') ?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('month','l') ?>
											</th>
											<th>
												<?php echo lang('annual range','c') ?>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('minimumAbbr','c')." ".lang('temperature','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_temp_min_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_temp_min_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($temps_min) - min($temps_min)), 1, ".", "") ?>°<?php echo $climateUnitsTemp ?>
											</td>
										</tr>
										<tr>
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_temp_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_temp_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($temps) - min($temps)), 1, ".", "") ?>°<?php echo $climateUnitsTemp ?>
											</td>
										</tr>
										<tr>
											<td style="text-align:left">
												<?php echo lang('maximumAbbr','c')." ".lang('temperature','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_temp_max_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_temp_max_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($temps_max) - min($temps_max)), 1, ".", "") ?>°<?php echo $climateUnitsTemp ?>
											</td>
										</tr>
										<tr>
											<td style="text-align:left">
												<?php echo lang('temperature range','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_temp_range_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_temp_range_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($temps_max) - min($temps_min)), 1, ".", "") ?>°<?php echo $climateUnitsTemp ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td style="width:50%">
							<div class="datagrid" style="width:auto!important;display:inline-block;float:right">
								<table>
									<thead>
										<tr>
											<th rowspan="2">
											</th>
											<th colspan="12" style="text-align:center">
												<?php echo lang('Month','c')?>
											</th>
											<th rowspan="2" style="text-align:center">
												<?php echo lang('average','c')?>
											</th>
										</tr>
										<tr>
											<?php
												for($i=1;$i<13;$i++){
											?>
												<th style="text-align:center">
													<?php echo $i?>
												</th>
											<?php
												}
											?>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('minimumAbbr','c')." ".lang('temperature','c')?>(°<?php echo $climateUnitsTemp ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($temps_min[$i], array(min($temps_min) - 0.001, max($temps_max)), array("#2693FF", "#FF2626"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
												$color = fill($temps_min[0], array(min($temps_min) - 0.001, max($temps_max)), array("#2693FF", "#FF2626"));
												echo "<td style='background-color:" . $color . ";width:2%'></td>";
											?>
										</tr>
										<tr class="alt">
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>(°<?php echo $climateUnitsTemp ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($temps[$i], array(min($temps_min) - 0.001, max($temps_max)), array("#2693FF", "#FF2626"));
														echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
												$color = fill($temps[0], array(min($temps_min), max($temps_max)), array("#2693FF", "#FF2626"));
												echo "<td style='background-color:" . $color . ";width:2%'></td>";
											?>
										</tr>
										<tr>
											<td style="text-align:left">
												<?php echo lang('maximumAbbr','c')." ".lang('temperature','c')?>(°<?php echo $climateUnitsTemp ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($temps_max[$i], array(min($temps_min) - 0.001, max($temps_max)), array("#2693FF", "#FF2626"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
												$color = fill($temps_max[0], array(min($temps_min), max($temps_max)), array("#2693FF", "#FF2626"));
												echo "<td style='background-color:" . $color . ";width:2%'></td>";
											?>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('minimumAbbr','c')." ".lang('temperature','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_temps_min), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_temps_min), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($spring_temps_min_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_temps_min) - min($spring_temps_min)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format(min($summer_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format($summer_temps_min_avg, 2) ?>
								</td>
								<td>
									<?php echo number_format((max($summer_temps_min) - min($summer_temps_min)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format($autumn_temps_min_avg, 2) ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_temps_min) - min($autumn_temps_min)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format(min($winter_temps_min), 1) ?>
								</td>
								<td>
									<?php echo number_format($winter_temps_min_avg, 2) ?>
								</td>
								<td>
									<?php echo number_format((max($winter_temps_min) - min($winter_temps_min)), 1, ".", "") ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($spring_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_temps) - min($spring_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($summer_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_temps) - min($summer_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($autumn_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_temps) - min($autumn_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($winter_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_temps) - min($winter_temps)), 1, ".", "") ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('maximumAbbr','c')." ".lang('temperature','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($spring_temps_max_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_temps_max) - min($spring_temps_max)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($summer_temps_max_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_temps_max) - min($summer_temps_max)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($autumn_temps_max_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_temps_max) - min($autumn_temps_max)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_temps_max), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($winter_temps_max_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_temps_max) - min($winter_temps_max)), 1, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_temp_seasons" style="height:300px; width: 100%">
				</div>
			</div>
		</div>
		<div id="temperature-decor"></div>
		<div id="temperature-lite">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading">
							<?php echo lang('temperature','c') ?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="temperature-opener-lite">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>
			<div id="temperature-content-lite">
			<br>
			<div class="datagrid">
				<table>
					<thead>
						<tr>
							<th rowspan="2">
							</th>
							<th colspan="12" style="text-align:center">
								<?php echo lang('month','c')?>
							</th>
							<th rowspan="2" style="text-align:center">
								<?php echo lang('average','c')?>
							</th>
						</tr>
						<tr>
							<?php
								for($i=1;$i<13;$i++){
							?>
								<th style="text-align:center">
									<?php echo $i?>
								</th>
							<?php
								}
							?>
						</tr>
					</thead>
					<tbody>
						<tr class="alt">
							<td style="text-align:left">
								<?php echo lang('avgAbbr','c')." ".lang('temperature','c') ?> (°<?php echo $climateUnitsTemp ?>)
							</td>
							<?php
								for ($i = 1; $i < 13; $i++) {
									if ($temps[$i] == $max_temp) {
										echo "<td style='color: red'>" . number_format($temps[$i], 1, ".", "") . "</td>";
									}
									else if ($temps[$i] == $min_temp) {
										echo "<td style='color: blue'>" . number_format($temps[$i], 1, ".", "") . "</td>";
									}
									else {
										echo "<td>" . number_format($temps[$i], 1, ".", "") . "</td>";
									}
								}
							?>
							<td>
								<b><?php echo number_format($temps[0], 1, ".", "") ?></b>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<br>
			<div id="graph_temp_lite" style="height:400px; width: 100%">
			</div>
			<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:left">
							<div class="datagrid" style="width:100%;display:inline-block">
								<table style="width:100%">
									<thead>
										<tr>
											<th>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('annual range','c')?>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_temp_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_temp_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($temps) - min($temps)), 1, ".", "") ?>°<?php echo $climateUnitsTemp ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td style="width:50%">
							<div class="datagrid" style="width:auto!important;display:inline-block;float:right">
								<table>
									<thead>
										<tr>
											<th rowspan="2">
											</th>
											<th colspan="12" style="text-align:center">
												<?php echo lang('month','c')?>
											</th>
											<th rowspan="2" style="text-align:center">
												<?php echo lang('average','c')?>
											</th>
										</tr>
										<tr>
											<?php
												for($i=1;$i<13;$i++){
											?>
												<th style="text-align:center">
													<?php echo $i?>
												</th>
											<?php
												}
											?>
										</tr>
									</thead>
									<tbody>
										<tr class="alt">
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?> (°<?php echo $climateUnitsTemp ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($temps[$i], array(min($temps) - 0.001, max($temps)), array("#2693FF", "#FF2626"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
												$color = fill($temps[0], array(min($temps), max($temps)), array("#2693FF", "#FF2626"));
												echo "<td style='background-color:" . $color . ";width:2%'></td>";
											?>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($spring_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_temps) - min($spring_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($summer_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_temps) - min($summer_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($autumn_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_temps) - min($autumn_temps)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_temps), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($winter_temps_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_temps) - min($winter_temps)), 1, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_temp_seasons_lite" style="height:300px; width: 100%">
				</div>
			</div>
		</div>
		<div id="temperature-decor-lite"></div>
		<br>
		<div id="humidity">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading"><?php echo lang('humidity','c')?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="humidity-opener">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>
			<div id="humidity-content">
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="12" style="text-align:center"><?php echo lang('month','c')?>
								</th>
								<th rowspan="2" style="text-align:center"><?php echo lang('average','c')?>
								</th>
							</tr>
							<tr>
								<?php
									for($i=1;$i<13;$i++){
								?>
									<th style="text-align:center">
										<?php echo $i?>
									</th>
								<?php
									}
								?>
							</tr>
						</thead>
						<tbody>
							<tr class="alt">
								<td style="text-align:left">
									<?php echo lang('avgAbbr','c')." ".lang('humidity','c')?> (%)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($humidities[$i] == $max_hum) {
											echo "<td style='color: red'>" . number_format($humidities[$i], 1, ".", "") . "</td>";
										}
										else if ($humidities[$i] == $min_hum) {
											echo "<td style='color: blue'>" . number_format($humidities[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($humidities[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($temps[0], 1, ".", "") ?></b>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_hum" style="height:400px; width: 100%">
				</div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:left">
							<div class="datagrid" style="width:100%;display:inline-block">
								<table style="width:100%">
									<thead>
										<tr>
											<th style="height:58px">
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('annual range','c')?>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('humidity','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_hum_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_hum_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($humidities) - min($humidities)), 1, ".", "") ?>%
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td style="width:50%">
							<div class="datagrid" style="width:auto!important;display:inline-block;float:right">
								<table>
									<thead>
										<tr>
											<th rowspan="2">
											</th>
											<th colspan="12" style="text-align:center">
												<?php echo lang('month','c')?>
											</th>
											<th rowspan="2" style="text-align:center">
												<?php echo lang('average','c')?>
											</th>
										</tr>
										<tr>
											<?php
												for($i=1;$i<13;$i++){
											?>
												<th style="text-align:center">
													<?php echo $i?>
												</th>
											<?php
												}
											?>
										</tr>
									</thead>
									<tbody>
										<tr class="alt">
											<td style="text-align:left">
												<?php echo lang('avgAbbr','c')." ".lang('humidity','c')?> (%)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($humidities[$i], array(min($humidities) - 0.001, max($humidities)), array("#FFFFFF", "#238C00"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
												$color = fill($humidities[0], array(min($humidities), max($humidities)), array("#FFFFFF", "#238C00"));
												echo "<td style='background-color:" . $color . ";width:2%'></td>";
											?>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left"><?php echo lang('avgAbbr','c')." ".lang('humidity','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($spring_hum_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_hum) - min($spring_hum)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($summer_hum_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_hum) - min($summer_hum)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($autumn_hum_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_hum) - min($autumn_hum)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_hum), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format($winter_hum_avg, 2, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_hum) - min($winter_hum)), 1, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_hum_seasons" style="height:300px; width: 100%">
				</div>
			</div>
			<div class="humidity-decor"></div>
		</div>
		<div id="humidity-decor"></div>
		<br>
		<div id="rain">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading">
							<?php echo lang('precipitation','c')?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="rain-opener">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>

			<div id="rain-content">
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="12" style="text-align:center">
									<?php echo lang('month','c')?>
								</th>
								<th rowspan="2" style="text-align:center">
									<?php echo lang('monthly average','c')?>
								</th>
								<th rowspan="2" style="text-align:center">
									<?php echo lang('annual total','c')?>
								</th>
							</tr>
							<tr>
								<?php
									for($i=1;$i<13;$i++){
								?>
									<th style="text-align:center">
										<?php echo $i?>
									</th>
								<?php
									}
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($rains[$i] == $max_rain) {
											echo "<td style='color: red'>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
										else if ($rains[$i] == $min_rain) {
											echo "<td style='color: blue'>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($rains[0], 1, ".", "") ?></b>
								</td>
								<td>
									<b><?php echo number_format($rains_total, 1, ".", "") ?></b>
								</td>
							</tr>
							<tr class="alt">
								<td style="text-align:left">
									<?php echo lang('wetdays','c')?>
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($wetdays[$i] == $max_wetdays) {
											echo "<td style='color: red'>" . number_format($wetdays[$i], 0, ".", "") . "</td>";
										}
										else if ($wetdays[$i] == $min_wetdays) {
											  echo "<td style='color: blue'>" . number_format($wetdays[$i], 0, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($wetdays[$i], 0, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($wetdays[0], 0, ".", "") ?></b>
								</td>
								<td>
									<b><?php echo number_format($wetdays_total, 0, ".", "") ?></b>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('wetdays','c')." ".lang('ratio','l')?> (%)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($wetdays_month_percentages[$i] == max($wetdays_month_percentages)) {
											echo "<td style='color: red;text-align:center'>" . number_format($wetdays_month_percentages[$i], 1, ".", "");
											echo "<br><span class='inlinesparklinepie'>" . $wetdays[$i] . "," . ($monthLengths[$i] - $wetdays[$i]) . "</span></td>";
										}
										else if ($wetdays_month_percentages[$i] == min($wetdays_month_percentages)) {
											echo "<td style='color: blue;text-align:center'>" . number_format($wetdays_month_percentages[$i], 1, ".", "");
											echo "<br><span class='inlinesparklinepie'>" . $wetdays[$i] . "," . ($monthLengths[$i] - $wetdays[$i]) . "</span></td>";
										}
										else {
											echo "<td style='text-align:center'>" . number_format($wetdays_month_percentages[$i], 1, ".", "");
											echo "<br><span class='inlinesparklinepie'>" . $wetdays[$i] . "," . ($monthLengths[$i] - $wetdays[$i]) . "</span></td>";
										}
									}
								?>
								<td>
									<b><?php
										echo number_format($wetdays_month_percentages[0], 1, ".", "");
										echo "<br><span class='inlinesparklinepie'>" . $wetdays_month_percentages[0] . "," . (100 - $wetdays_month_percentages[0]) . "</span>";
									?></b>
								</td>
								<td>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_rain" style="height:400px; width: 100%"></div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:left">
							<div class="datagrid" style="width:100%;display:inline-block">
								<table style="width:100%;height:150px">
									<thead>
										<tr>
											<th>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('season','c')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('season','c')?>
											</th>
											<th>
												<?php echo lang('annual range','c')?>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('average monthly precipitation','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_rain_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_rain_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_rain_season_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_rain_season_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($rains) - min($rains)), 1, ".", "") ?><?php echo $climateUnitsRain ?>
											</td>
										</tr>
										<tr class="alt">
											<td style="text-align:left">
												<?php echo lang('wetdays','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_wetdays_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_wetdays_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_wetdays_season_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_wetdays_season_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($wetdays) - min($wetdays)), 0, ".", "") ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td style="width:50%">
							<div class="datagrid" style="width:auto!important;display:inline-block;float:right">
								<table style="width:100%;height:150px">
									<thead>
										<tr>
											<th rowspan="2">
											</th>
											<th colspan="12" style="text-align:center">
												<?php echo lang('month','c')?>
											</th>
										</tr>
										<tr>
											<?php
												for($i=1;$i<13;$i++){
											?>
												<th style="text-align:center">
													<?php echo $i?>
												</th>
											<?php
												}
											?>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($rains[$i], array(min($rains) - 0.001, max($rains)), array("#FFFFFF", "#2693FF"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
											?>
										</tr>
										<tr class="alt">
											<td style="text-align:left">
												<?php echo lang('wetdays','c')?> / <?php echo lang('month','l')?>
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($wetdays[$i], array(min($wetdays) - 0.001, max($wetdays)), array("#FFFFFF", "#2693FF"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
											?>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
								</td>
								<td>
									<?php echo number_format(max($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_rains) - min($spring_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_rains) - min($summer_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_rains) - min($autumn_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_rains) - min($winter_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_rains), 1, ".", "") ?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('wetdays','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_wetdays) - min($spring_wetdays)), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_wetdays) - min($summer_wetdays)), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_wetdays) - min($autumn_wetdays)), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_wetdays), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_wetdays) - min($winter_wetdays)), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_wetdays), 0, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_rain_seasons" style="height:300px; width: 100%">
				</div>
			</div>
		</div>
		<div id="rain-decor">
		</div>
		<div id="rain-lite">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading">
							<?php echo lang('precipitation','c')?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="rain-opener-lite">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>
			<div id="rain-content-lite">
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="12" style="text-align:center">
									<?php echo lang('month','c')?>
								</th>
								<th rowspan="2" style="text-align:center">
									<?php echo lang('monthly average','c')?>
								</th>
								<th rowspan="2" style="text-align:center">
									<?php echo lang('annual total','c')?>
								</th>
							</tr>
							<tr>
								<?php
									for($i=1;$i<13;$i++){
								?>
									<th style="text-align:center">
										<?php echo $i?>
									</th>
								<?php
									}
								?>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
								</td>
								<?php
									for ($i = 1; $i < 13; $i++) {
										if ($rains[$i] == $max_rain) {
											echo "<td style='color: red'>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
										else if ($rains[$i] == $min_rain) {
											echo "<td style='color: blue'>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
										else {
											echo "<td>" . number_format($rains[$i], 1, ".", "") . "</td>";
										}
									}
								?>
								<td>
									<b><?php echo number_format($rains[0], 1, ".", "") ?></b>
								</td>
								<td>
									<b><?php echo number_format($rains_total, 1, ".", "") ?></b>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_rain_lite" style="height:400px; width: 100%"></div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:left">
							<div class="datagrid" style="width:100%;display:inline-block">
								<table style="width:100%">
									<thead>
										<tr>
											<th>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('month','l')?>
											</th>
											<th>
												<?php echo lang('maximumAbbr','c')." ".lang('season','c')?>
											</th>
											<th>
												<?php echo lang('minimumAbbr','c')." ".lang('season','c')?>
											</th>
											<th>
												<?php echo lang('annual range','c')?>
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('average monthly precipitation','c')?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_rain_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_rain_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $max_rain_season_month_text); ?>
											</td>
											<td style="text-align:center">
												<?php echo implode(", ", $min_rain_season_month_text); ?>
											</td>
											<td>
												<?php echo number_format((max($rains) - min($rains)), 1, ".", "") ?><?php echo $climateUnitsRain ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td style="width:50%">
							<div class="datagrid" style="width:auto!important;display:inline-block;float:right">
								<table>
									<thead>
										<tr>
											<th rowspan="2">
											</th>
											<th colspan="12" style="text-align:center">
												<?php echo lang('month','c')?>
											</th>
										</tr>
										<tr>
											<th style="text-align:center">
												1
											</th>
											<th style="text-align:center">
												2
											</th>
											<th style="text-align:center">
												3
											</th>
											<th style="text-align:center">
												4
											</th>
											<th style="text-align:center">
												5
											</th>
											<th style="text-align:center">
												6
											</th>
											<th style="text-align:center">
												7
											</th>
											<th style="text-align:center">
												8
											</th>
											<th style="text-align:center">
												9
											</th>
											<th style="text-align:center">
												10
											</th>
											<th style="text-align:center">
												11
											</th>
											<th style="text-align:center">
												12
											</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td style="text-align:left">
												<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
											</td>
											<?php
												for ($i = 1; $i < 13; $i++) {
													$color = fill($rains[$i], array(min($rains) - 0.001, max($rains)), array("#FFFFFF", "#2693FF"));
													echo "<td style='background-color:" . $color . ";width:2%'></td>";
												}
											?>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c') ?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c') ?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('range','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('total','c') ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('average monthly precipitation','c')?> (<?php echo $climateUnitsRain ?>)
								</td>
								<td>
									<?php echo number_format(max($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($spring_rains) - min($spring_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($summer_rains) - min($summer_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($autumn_rains) - min($autumn_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_rains), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format((max($winter_rains) - min($winter_rains)), 1, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_rains), 1, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<div id="graph_rain_seasons_lite" style="height:300px; width: 100%"></div>
			</div>
		</div>
		<div id="rain-decor-lite">
		</div>
		<br>
		<div id="light">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading">
							<?php echo lang('sunlight','c')?> <?php echo " / "?> <?php echo lang('day length','c')?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="light-opener">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>
			<div id="light-content">
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th>
								</th>
								<th>
									<?php echo lang('average daily sunlight','c')?>
								</th>
								<th>
									<?php echo lang('average day length','c')?>
								</th>
								<th>
									<?php echo lang('maximumAbbr','c')." ".lang('day length','l')?>
								</th>
								<th>
									<?php echo lang('minimumAbbr','c')." ".lang('day length','l')?>
								</th>
								<th>
									<?php echo lang('sunlight','c')?>/<?php echo lang('day length','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
								for ($w = 1; $w <= 12; $w++) {
							?>
								<tr>
									<td>
										<?php echo $months[$w] ?>
									</td>
									<td style="text-align:center">
										<?php
											echo $sunlights[$w] . " min<br>" . time_conversion_h($sunlights[$w]) . " h " . time_conversion_min($sunlights[$w]) . " min";
										?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(array_sum($daylengths[$w]) / count($daylengths[$w])) . " h " . time_conversion_min(array_sum($daylengths[$w]) / count($daylengths[$w])) . " min<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(max($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(max($daylengths[$w])) . " h " . time_conversion_min(max($daylengths[$w])) . " min<br>";
											$tmp = array_keys($daylengths[$w], max($daylengths[$w]));
											echo "(" . $tmp[0] . "." . $w . ".)<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(max($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(max($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(min($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(min($daylengths[$w])) . " h " . time_conversion_min(min($daylengths[$w])) . " min<br>";
											$tmp = array_keys($daylengths[$w], min($daylengths[$w]));
											echo "(" . $tmp[0] . "." . $w . ".)<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(min($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(min($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
									<td style="text-align:center">
										<?php
											if ((array_sum($daylengths[$w]) / count($daylengths[$w])) == 0) {
												echo "-";
											} else {
												echo number_format(($sunlights[$w] / (array_sum($daylengths[$w]) / count($daylengths[$w]))) * 100, 1, ".", "") . " %<br>";
												echo "<span class='inlinesparklinepie_sun_big'>" . $sunlights[$w] . "," . number_format((array_sum($daylengths[$w]) / count($daylengths[$w])) - $sunlights[$w], 0, ".", "") . "</span>";
											}
										?>
									</td>
								</tr>
							<?php
								}
							?>
							<tr class="alt">
								<td>
									<b><?php echo lang('year','c')?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format((array_sum($sunlights) / 12), 0, ".", "") . " min<br>" . time_conversion_h(number_format((array_sum($sunlights) / 12), 0, ".", "")) . " h " . time_conversion_min(number_format((array_sum($sunlights) / 12), 0, ".", "")) . " min";
									?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(array_sum($daylengths_year) / count($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(array_sum($daylengths_year) / count($daylengths_year)) . " h " . time_conversion_min(array_sum($daylengths_year) / count($daylengths_year)). " min<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($daylengths_year) / count($daylengths_year), 0, ".", "") . "," . (1440 - number_format(array_sum($daylengths_year) /count($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(max($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(max($daylengths_year)) . " h " . time_conversion_min(max($daylengths_year)) . " min<br>";
										$temporaryDay = array_keys($daylengths_year, max($daylengths_year));
										$dayNumber = $temporaryDay[0];
										$temporary_date = strtotime("January 1st +".($dayNumber-1)." days");
										echo "(" . date('j. n.',$temporary_date) . ")<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(max($daylengths_year), 0, ".", "") . "," . (1440 - number_format(max($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(min($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(min($daylengths_year)) . " h " . time_conversion_min(min($daylengths_year)) . " min<br>";
										$temporaryDay = array_keys($daylengths_year, min($daylengths_year));
										$dayNumber = $temporaryDay[0];
										$temporary_date = strtotime("January 1st +".($dayNumber-1)." days");
										echo "(" . date('j. n.',$temporary_date) . ")<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(min($daylengths_year), 0, ".", "") . "," . (1440 - number_format(min($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
								<td style="text-align:center">
									<?php
										echo number_format(((array_sum($sunlights) / 12) / (array_sum($daylengths_year) / count($daylengths_year))) * 100, 1, ".", "") . " %<br>";
										echo "<span class='inlinesparklinepie_sun_big'>" . number_format(array_sum($sunlights) / 12, 0, ".", "") . "," . number_format((array_sum($daylengths_year) / count($daylengths_year)) - array_sum($sunlights) / 12, 0, ".", "") . "</span>";
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="text-align:center;width:50%">
							<div id="graph_light" style="height:400px; width: 100%;"></div>
						</td>
						<td style="text-align:center;width:50%">
							<div id="graph_daylength" style="height:400px; width: 100%;"></div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="4">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="4">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('sunlight','c')."/".lang('day','l')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('sunlight','c')."/".lang('day','l')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('sunlight','c')."/".lang('day','l')?>
								</th>
								<th style="width:5%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:5%">
									<?php echo lang('sunlight','c')."/".lang('day','l')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('average sunlight','c')?> (<?php echo lang('minAbbr','l')."/".lang('day','l')?>)
								</td>
								<td>
									<?php echo number_format(max($spring_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_sunlights) / 3, 0, ".", "") ?>
								</td>
								<td rowspan="2" style="text-align:center">
									<?php
										echo number_format(((array_sum($spring_sunlights) / 3) / (array_sum($spring_daylengths) / count($spring_daylengths))) * 100, 1, ".", "");
										echo "%<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($spring_sunlights) / 3, 0, ".", "") . "," . number_format(((array_sum($spring_daylengths) / count($spring_daylengths)) - (array_sum($spring_sunlights) / 3)), 0, ".", "") . "</span>"
									?>
								</td>
								<td>
									<?php echo number_format(max($summer_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_sunlights) / 3, 0, ".", "") ?>
								</td>
								<td rowspan="2" style="text-align:center">
									<?php
										echo number_format(((array_sum($summer_sunlights) / 3) / (array_sum($summer_daylengths) / count($summer_daylengths))) * 100, 1, ".", "");
										echo "%<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($summer_sunlights) / 3, 0, ".", "") . "," . number_format(((array_sum($summer_daylengths) / count($summer_daylengths)) - (array_sum($summer_sunlights) / 3)), 0, ".", "") . "</span>"
									?>
								</td>
								<td>
									<?php echo number_format(max($autumn_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_sunlights) / 3, 0) ?>
								</td>
								<td rowspan="2" style="text-align:center">
									<?php
										echo number_format(((array_sum($autumn_sunlights) / 3) / (array_sum($autumn_daylengths) / count($autumn_daylengths))) * 100, 1, ".", "");
										echo "%<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($autumn_sunlights) / 3, 0, ".", "") . "," . number_format(((array_sum($autumn_daylengths) / count($autumn_daylengths)) - (array_sum($autumn_sunlights) / 3)), 0, ".", "") . "</span>"
									?>
								</td>
								<td>
									<?php echo number_format(max($winter_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_sunlights), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_sunlights) / 3, 0, ".", "") ?>
								</td>
								<td rowspan="2" style="text-align:center">
									<?php
										echo number_format(((array_sum($winter_sunlights) / 3) / (array_sum($winter_daylengths) / count($winter_daylengths))) * 100, 1, ".", "");
										echo "%<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($winter_sunlights) / 3, 0, ".", "") . "," . number_format(((array_sum($winter_daylengths) / count($winter_daylengths)) - (array_sum($winter_sunlights) / 3)), 0, ".", "") . "</span>"
									?>
								</td>
							</tr>
							<tr>
								<td style="text-align:left">
									<?php echo lang('day length','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_daylengths) / count($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_daylengths) / count($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_daylengths) / count($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_daylengths) / count($winter_daylengths), 0, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table style="width:100%">
					<tr>
						<td style="text-align:center;width:50%">
							<div id="graph_light_seasons" style="height:400px; width: 100%;"></div>
						</td>
						<td style="text-align:center;width:50%">
							<div id="graph_daylength_seasons" style="height:400px; width: 100%;"></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="light-decor">
		</div>
		<div id="light-lite">
			<table style="width:100%">
				<tr>
					<td style="text-align:left">
						<span class="section_heading">
							<?php echo lang('day length','c')?>
						</span>
					</td>
					<td style="text-align:right">
						<div>
							<span class="section_heading" style="text-align:right" id="light-opener-lite">
								+
							</span>
						</div>
					</td>
				</tr>
			</table>

			<div id="light-content-lite">
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
							  <th>
							  </th>
							  <th>
								<?php echo lang('average day length','c')?>
							  </th>
							  <th>
								<?php echo lang('maximumAbbr','c')." ".lang('day length','l')?>
							  </th>
							  <th>
								<?php echo lang('minimumAbbr','c')." ".lang('day length','l')?>
							  </th>
							</tr>
						</thead>
						<tbody>
							<?php
								for ($w = 1; $w <= 12; $w++) {
							?>
								<tr>
									<td>
										<?php echo $months[$w] ?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(array_sum($daylengths[$w]) / count($daylengths[$w])) . " h " . time_conversion_min(array_sum($daylengths[$w]) / count($daylengths[$w])) . " min<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(array_sum($daylengths[$w]) / count($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(max($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(max($daylengths[$w])) . " h " . time_conversion_min(max($daylengths[$w])) . " min<br>";
											echo "(" . array_keys($daylengths[$w], max($daylengths[$w])) . "." . $w . ".)<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(max($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(max($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
									<td style="text-align:center">
										<?php
											echo number_format(min($daylengths[$w]), 0, ".", "") . " min<br>";
											echo time_conversion_h(min($daylengths[$w])) . " h " . time_conversion_min(min($daylengths[$w])) . " min<br>";
											$tmp = array_keys($daylengths[$w], min($daylengths[$w]));
											echo "(" . $tmp[0] . "." . $w . ".)<br>";
											echo "<span class='inlinesparklinepie_sun'>" . number_format(min($daylengths[$w]), 0, ".", "") . "," . (1440 - number_format(min($daylengths[$w]), 0, ".", "")) . "</span>";
										?>
									</td>
								</tr>
							<?php
								}
							?>
							<tr class="alt">
								<td>
									<b><?php echo lang('year','c')?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(array_sum($daylengths_year) / count($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(array_sum($daylengths_year) / count($daylengths_year)) . " h " . time_conversion_min(array_sum($daylengths_year) / count($daylengths_year)) . " min<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(array_sum($daylengths_year) / count($daylengths_year), 0, ".", "") . "," . (1440 - number_format(array_sum($daylengths_year) / count($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(max($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(max($daylengths_year)) . " h " . time_conversion_min(max($daylengths_year)). " min<br>";
										$tmp = array_keys($daylengths_year, max($daylengths_year));
										$temporary_date = new DateTime(date("Y")."-01-01");
										$temporary_date->modify("+".$tmp[0]." days");
										echo "(" . $temporary_date->format('j. n.') . ")<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(max($daylengths_year), 0, ".", "") . "," . (1440 - number_format(max($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
								<td style="text-align:center">
									<b><?php
										echo number_format(min($daylengths_year), 0, ".", "") . " min<br>";
										echo time_conversion_h(min($daylengths_year)) . " h " . time_conversion_min(min($daylengths_year)). " min<br>";
										$tmp = array_keys($daylengths_year, min($daylengths_year));
										$temporary_date = new DateTime(date("Y")."-01-01");
										$temporary_date->modify("+".$tmp[0]." days");
										echo "(" . $temporary_date->format('j. n.') . ")<br>";
										echo "<span class='inlinesparklinepie_sun'>" . number_format(min($daylengths_year), 0, ".", "") . "," . (1440 - number_format(min($daylengths_year), 0, ".", "")) . "</span>";
									?></b>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table style="width:100%">
					<tr>
						<td>
							<div id="graph_daylength_lite" style="height:400px; width: 100%;"></div>
						</td>
					</tr>
				</table>
				<br>
				<div class="datagrid">
					<table>
						<thead>
							<tr>
								<th rowspan="2">
								</th>
								<th colspan="3">
									<span class='mticon-spring' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
										if ($hemisphere == "S") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
									?>
								</th>
								<th colspan="3">
									<span class='mticon-summer' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
										if ($hemisphere == "S") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
									?>
								</th>
								<th colspan="3">
									<span class='mticon-autumn' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[9] . ", " . $months[10] . ", " . $months[11];
										}
										if ($hemisphere == "S") {
											echo $months[3] . ", " . $months[4] . ", " . $months[5];
										}
									?>
								</th>
								<th colspan="3">
									<span class='mticon-winter' style="font-size:1.5em"></span>
									<br>
									<?php
										if ($hemisphere == "N") {
											echo $months[12] . ", " . $months[1] . ", " . $months[2];
										}
										if ($hemisphere == "S") {
											echo $months[6] . ", " . $months[7] . ", " . $months[8];
										}
									?>
								</th>
							</tr>
							<tr>
								<th style="width:7%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('average','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('maximumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('minimumAbbr','c')?>
								</th>
								<th style="width:7%">
									<?php echo lang('average','c')?>
								</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="text-align:left">
									<?php echo lang('day length','c')?>
								</td>
								<td>
									<?php echo number_format(max($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($spring_daylengths) / count($spring_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($summer_daylengths) / count($summer_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($autumn_daylengths) / count($autumn_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(max($winter_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(min($winter_daylengths), 0, ".", "") ?>
								</td>
								<td>
									<?php echo number_format(array_sum($winter_daylengths) / count($winter_daylengths), 0, ".", "") ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<br>
				<table style="width:100%">
					<tr>
						<td>
							<div id="graph_daylength_seasons_lite" style="height:400px; width: 100%;"></div>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div id="light-decor-lite"></div>
		<script> // map
			var map;
			var position = new google.maps.LatLng(<?php echo $lat?>, <?php echo $lon?>);
			var marker;
			function initialize() {
				var mapOptions = {
					zoom: 8,
					center: position,
					mapTypeId: google.maps.MapTypeId.HYBRID
				};
				map = new google.maps.Map(document.getElementById('map'), mapOptions);
				marker = new google.maps.Marker({
					map:map,
					animation: google.maps.Animation.DROP,
					position: position
				});
			}
			google.maps.event.addDomListener(window, 'load', initialize);
		</script>
		<script type="text/javascript"> //graphs
			$(function () {
				Highcharts.setOptions({
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>'],
					}
				});

			$('#main_graph').highcharts({
				title: {
					text: "<?php echo lang('main graph','w') ?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:[
						<?php
							for($a=1;$a<13;$a++){
								echo "'".$months[$a]."',";
							}
						?>
					],
					labels: {
						rotation: -45,
					}
				},
				yAxis:[
					{
						title: {
							text: "<?php if($humidities[1]>-9998){ echo lang('humidity','c')." (%)";}?>"
						},
					},
					{
						title: {
							text: "<?php if($temps[1]>-9998 || $temps_max[1]>-9998){ echo lang('temperature','c')." (°".$climateUnitsTemp.")";}?>"
						},
					},
					{
						title: {
							text: "<?php echo lang('precipitation','c')?> (mm)",
						},
							<?php
								if($humidities[1]>-9998 || $temps[1]>-9998 || $temps_max[1]>-9998){
							?>
									opposite: true,
							<?php
								}
							?>
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				plotOptions:{
					areasplinerange:{
						fillOpacity: 0.5,
					}
				},
				series: [
					<?php
						if($temps[1]>-9998 && ($temps_min[1]==-9999 || $temps_max[1]==-9999)){
					?>
						{
							name: '<?php echo lang('temperature','c')?>',
							color: '#FFFFFF',
							zIndex: 5,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo $temps[$i].",";
									}
								?>
							],
							type: 'spline',
							yAxis: 1,
						},
					<?php
						}
					?>
					<?php
						if($temps_min[1]!=-9999 && $temps_max[1]!=-9999){
					?>
						{
							name: '<?php echo lang('temperature range','c')?>',
							color: '#8C0000',
							zIndex: 2,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo "[".$temps_min[$i].",".$temps_max[$i]."],";
									}
								?>
							],
							type: 'areasplinerange',
							yAxis: 1,
						},
						{
							name: '<?php echo lang('avgAbbr','c')." ".lang('temperature','c')?>',
							color: '#FFFFFF',
							zIndex: 2,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo $temps[$i].",";
									}
								?>
							],
							type: 'spline',
							yAxis: 1,
						},
					<?php
						}
					?>
					<?php
						if($humidities[1]>-9998){
					?>
						{
							name: '<?php echo lang('humidity','c')?>',
							color: '#00D900',
							zIndex: 4,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo $humidities[$i].",";
									}
								?>
							],
							type: 'spline',
							yAxis: 0,
						},
					<?php
						}
					?>
					<?php
						if($rains[1]>-9998){
					?>
						{
							name: '<?php echo lang('precipitation','c')?>',
							color: '#265CFF',
							borderColor: '#222222',
							fillOpacity: 0.3,
							zIndex: 1,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo $rains[$i].",";
									}
								?>
							],
							type: 'column',
							yAxis: 2,
						}
					<?php
						}
					?>
				]
			});

			$('#minor_graph').highcharts({
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				<?php
					if($sunlights[1]>-9998){
				?>
					title: {
						text: "<?php echo lang('sunlight','c')." ".lang('and','l').lang('day length','c')?>"
					},
				<?php
					}
				?>
				<?php
					if($sunlights[1]==-9999){
				?>
					title: {
						text: "<?php echo lang('day Length','c')?>"
					},
				<?php
					}
				?>
					chart: {
						type: 'column'
					},
					plotOptions: {
						column: {
							stacking: 'normal',
						}
					},
					xAxis: {
						categories:[
							<?php
								for($a=1;$a<13;$a++){
									echo "'".$months[$a]."',";
								}
							?>
						],
						labels: {
							rotation: -45,
						}
					},
					yAxis:[
						{
							title: {
								text: "<?php echo lang('length','c')?> (<?php echo lang('minAbbr','l')?>)"
							},
							max: 1500,
							min: 0,
						},
					],
					series: [{
						name: '<?php echo lang('night','c')?>',
						color: '#000000',
						zIndex: 0,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo number_format(1440-(array_sum($daylengths[$i])/count($daylengths[$i])),0,".","").",";
								}
							?>
						],
					},
					<?php
						if($sunlights[1]>-9998){
					?>
						{
							name: '<?php echo lang('day','c')?>',
							color: '#888888',
							zIndex: 1,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo number_format((1440-(1440-array_sum($daylengths[$i])/count($daylengths[$i]))-$sunlights[$i]),0,".","").",";
									}
								?>
							],
						},
						{
							name: '<?php echo lang('sunlight','c')?>',
							color: '#FFFF73',
							zIndex: 2,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo $sunlights[$i].",";
									}
								?>
							],
						},
					<?php
						}
					?>
					<?php
						if($sunlights[1]==-9999){
					?>
						{
							name: '<?php echo lang('day','c')?>',
							color: '#888888',
							zIndex: 1,
							data: [
								<?php
									for($i=1;$i<13;$i++){
										echo number_format((array_sum($daylengths[$i])/count($daylengths[$i])),0,".","").",";
									}
								?>
							],
						},
					<?php
						}
					?>
				]
			});

			$('#graph_temp').highcharts({
				title: {
					text: '<?php echo lang('temperature','c')?>'
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l').", ".lang('minimum','l').", ".lang('maximum','l').", ".lang('range','l')?>"
				},
				xAxis: {
					categories:
					[
					<?php
					for($a=1;$a<13;$a++){
						echo "'".$months[$a]."',";
					}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('temperature','c')?> (°<?php echo $climateUnitsTemp ?>)"
						},
					},
					{
						title: {
							text: "<?php echo lang('temperature range','c')?> (°<?php echo $climateUnitsTemp ?>)",
						},
						opposite: true,
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('temperature','c')?>',
						color: '#FFFFFF',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $temps[$i].",";
								}
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('minimumAbbr','c')." ".lang('temperature','c')?>',
						color: '#73B9FF',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $temps_min[$i].",";
								}
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('maximumAbbr','c')." ".lang('temperature','c')?>',
						color: '#FF2626',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $temps_max[$i].",";
								}
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('range','c')?>',
						color: '#<?php echo $color_schemes[$design2]['300']?>',
						borderColor: '#222222',
						fillOpacity: 0.3,
						zIndex: 0,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $temp_ranges[$i].",";
								}
							?>
						],
						type: 'column',
						yAxis: 1,
					}
				]
			});

			$('#graph_temp_lite').highcharts({
				title: {
					text: '<?php echo lang('temperature','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=1;$a<13;$a++){
							echo "'".$months[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('temperature','c')?> (°<?php echo $climateUnitsTemp ?>)"
						},
					}
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('temperature','c')?>',
						color: '#FFFFFF',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $temps[$i].",";
								}
							?>
						],
						type: 'spline',
					},
				]
			});

			$('#graph_temp_seasons').highcharts({
				title: {
					text: '<?php echo lang('temperature','c')." (".lang('seasons','l').")"?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l')." ".lang('and','l')." ".lang('range','l')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=0;$a<4;$a++){
							echo "'".$seasons[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('temperature','c')?> (°<?php echo $climateUnitsTemp ?>)"
						},
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('temperature','c')?>',
						color: '#FFFFFF',
						zIndex: 1,
						data: [
							<?php
								echo number_format($spring_temps_avg,2,".","").",".number_format($summer_temps_avg,2,".","").",".number_format($autumn_temps_avg,2,".","").",".number_format($winter_temps_avg,2,".","");
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('range','c')?>',
						color: '#888888',
						zIndex: 0,
						data: [
							<?php
								echo "[".min($spring_temps_min).",".max($spring_temps_max)."],";
								echo "[".min($summer_temps_min).",".max($summer_temps_max)."],";
								echo "[".min($autumn_temps_min).",".max($autumn_temps_max)."],";
								echo "[".min($winter_temps_min).",".max($winter_temps_max)."],";
							?>
						],
						type: 'columnrange',
					},
				]
			});

			$('#graph_temp_seasons_lite').highcharts({
				title: {
					text: '<?php echo lang('temperature','c')." (".lang('seasons','l').")"?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l')." ".lang('and','l')." ".lang('range','l')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=0;$a<4;$a++){
							echo "'".$seasons[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('temperature','c')?> (°<?php echo $climateUnitsTemp ?>)"
						},
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('temperature','c')?>',
						color: '#FFFFFF',
						zIndex: 1,
						data: [
							<?php
								echo number_format($spring_temps_avg,2,".","").",".number_format($summer_temps_avg,2,".","").",".number_format($autumn_temps_avg,2,".","").",".number_format($winter_temps_avg,2,".","");
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('range','c')?>',
						color: '#888888',
						zIndex: 0,
						data: [
							<?php
								echo "[".min($spring_temps).",".max($spring_temps)."],";
								echo "[".min($summer_temps).",".max($summer_temps)."],";
								echo "[".min($autumn_temps).",".max($autumn_temps)."],";
								echo "[".min($winter_temps).",".max($winter_temps)."],";
							?>
						],
						type: 'columnrange',
					},
				]
			});

			$('#graph_hum').highcharts({
				title: {
					text: '<?php echo lang('humidity','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=1;$a<13;$a++){
							echo "'".$months[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('humidity','c')?> (%)"
						},
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('humidity','c')?>',
						color: '#00B300',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $humidities[$i].",";
								}
							?>
						],
						type: 'spline',
					},
				]
			});

			$('#graph_hum_seasons').highcharts({
				title: {
					text: '<?php echo lang('humidity','c')." (".lang('seasons','l').")"?>'
				},
				subtitle: {
						text: "<?php echo lang('average','l')." ".lang('and','l')." ".lang('range','l')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=0;$a<4;$a++){
							echo "'".$seasons[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('humidity','c')?> (%)"
						},
					},
				],
				tooltip: {
					crosshairs: true,
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('humidity','c')?>',
						color: '#00B300',
						zIndex: 1,
						data: [
							<?php
								echo number_format($spring_hum_avg,2,".","").",".number_format($summer_hum_avg,2,".","").",".number_format($autumn_hum_avg,2,".","").",".number_format($winter_hum_avg,2,".","");
							?>
						],
						type: 'spline',
					},
					{
						name: '<?php echo lang('range','c')?>',
						color: '#006600',
						zIndex: 0,
						data: [
							<?php
								echo "[".min($spring_hum).",".max($spring_hum)."],";
								echo "[".min($summer_hum).",".max($summer_hum)."],";
								echo "[".min($autumn_hum).",".max($autumn_hum)."],";
								echo "[".min($winter_hum).",".max($winter_hum)."],";
							?>
						],
						type: 'columnrange',
					},
				]
			});

			$('#graph_rain').highcharts({
				title: {
					text: '<?php echo lang('precipitation','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('monthly average','c')." ".lang('and','l')." ".lang('number of wetdays','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=1;$a<13;$a++){
							echo "'".$months[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('monthly average','c')?> (<?php echo $climateUnitsRain?>)"
						},
					},
					{
						title: {
							text: "<?php echo lang('wetdays','c')?>",
						},
						opposite: true,
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('precipitation','c')?>',
						color: '#2693FF',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $rains[$i].",";
								}
							?>
						],
						type: 'column',
					},
					{
						name: '<?php echo lang('wetdays','c')?>',
						color: '#AAAAAA',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $wetdays[$i].",";
								}
							?>
						],
						type: 'column',
						yAxis: 1,
					},
				]
			});

			$('#graph_rain_lite').highcharts({
				title: {
					text: '<?php echo lang('precipitation','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('monthly average','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=1;$a<13;$a++){
							echo "'".$months[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('monthly average','c')?> (<?php echo $climateUnitsRain?>)"
						},
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('precipitation','c')?>',
						color: '#2693FF',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $rains[$i].",";
								}
							?>
						],
						type: 'column',
					},
				]
			});

			$('#graph_rain_seasons').highcharts({
				title: {
					text: '<?php echo lang('precipitation','c')." (".lang('seasons','l').")"?>'
				},
				subtitle: {
						text: "<?php echo lang('monthly average','c')." ".lang('and','l')." ".lang('number of wetdays','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=0;$a<4;$a++){
							echo "'".$seasons[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('monthly average','c')?> (<?php echo $climateUnitsRain?>)"
						},
					},
					{
						title: {
							text: "<?php echo lang('wetdays','c')?>",
						},
						opposite: true,
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('precipitation','c')?>',
						color: '#2693FF',
						zIndex: 1,
						data: [
							<?php
								echo array_sum($spring_rains).",".array_sum($summer_rains).",".array_sum($autumn_rains).",".array_sum($winter_rains);
							?>
						],
						type: 'column',
					},
					{
						name: '<?php echo lang('wetdays','c')?>',
						color: '#AAAAAA',
						zIndex: 1,
						data: [
							<?php
								echo array_sum($spring_wetdays).",".array_sum($summer_wetdays).",".array_sum($autumn_wetdays).",".array_sum($winter_wetdays);
							?>
						],
						type: 'column',
						yAxis: 1,
					},
				]
			});

			$('#graph_rain_seasons_lite').highcharts({
				title: {
					text: '<?php echo lang('precipitation','c')." (".lang('seasons','l').")"?>'
				},
				subtitle: {
						text: "<?php echo lang('monthly average','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
					<?php
						for($a=0;$a<4;$a++){
							echo "'".$seasons[$a]."',";
						}
					?>
					]
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('monthly average','l')?> (<?php echo $climateUnitsRain?>)"
						},
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('precipitation','c')?>',
						color: '#2693FF',
						zIndex: 1,
						data: [
							<?php
								echo array_sum($spring_rains).",".array_sum($summer_rains).",".array_sum($autumn_rains).",".array_sum($winter_rains);
							?>
						],
						type: 'column',
					}
				]
			});

			$('#graph_light').highcharts({
				title: {
					text: '<?php echo lang('sunlight','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('average daily sunlight duration','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
							for($a=1;$a<13;$a++){
								echo "'".$months[$a]."',";
							}
							echo "'".lang('year','c')."'";
						?>
					],
					labels: {
						rotation: -40,
					}
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('average sunlight duration','c')?> (<?php echo lang('minAbbr','l')?>)"
						},
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('sunlight','l')?>',
						color: '#FFFF4C',
						zIndex: 1,
						data: [
							<?php
								for($i=1;$i<13;$i++){
									echo $sunlights[$i].",";
								}
								echo number_format((array_sum($sunlights)/count($sunlights)),0,".","");
							?>
						],
						type: 'column',
					},
				]
			});

			$('#graph_daylength').highcharts({
				title: {
					text: '<?php echo lang('day length','c')?>'
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
						for($a=1;$a<13;$a++){
							echo "'".$months[$a]."',";
						}
						?>
					],
					labels: {
						rotation: -40,
					}
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('day length','c')?> (<?php echo lang('minAbbr','l')?>)",
						},
						min: 0,
					},

				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('day length','c')." (".lang('range','l').")"?>',
						color: '#AAAAAA',
						data: [
							<?php
								for($i=1;$i<=12;$i++){
									echo "[".number_format(min($daylengths[$i]),0,".","").",".number_format(max($daylengths[$i]),0,".","")."],";
								}
							?>
						],
						type: 'columnrange',
					},
					{
						name: '<?php echo lang('day length','c')?>',
						color: '#FFFF26',
						data: [
							<?php
								for($i=1;$i<=12;$i++){
									echo number_format(array_sum($daylengths[$i])/count($daylengths[$i]),0,".","").",";
								}
							?>
						],
						type: 'spline',
					},
				]
			});

			$('#graph_daylength_lite').highcharts({
				title: {
					text: '<?php echo lang('day length','c')?>'
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
							for($a=1;$a<13;$a++){
								echo "'".$months[$a]."',";
							}
						?>
					],
					labels: {
						rotation: -40,
					}
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('day length','c')?> (<?php echo lang('minAbbr','l')?>)",
						},
						min: 0,
					},

				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('day length','c')." (".lang('range','l').")"?>',
						color: '#AAAAAA',
						data: [
							<?php
								for($i=1;$i<=12;$i++){
									echo "[".number_format(min($daylengths[$i]),0,".","").",".number_format(max($daylengths[$i]),0,".","")."],";
								}
							?>
						],
						type: 'columnrange',
					},
					{
						name: '<?php echo lang('day length','c')?>',
						color: '#FFFF26',
						data: [
							<?php
								for($i=1;$i<=12;$i++){
									echo number_format(array_sum($daylengths[$i])/count($daylengths[$i]),0,".","").",";
								}
							?>
						],
						type: 'spline',
					},
				]
			});

			$('#graph_light_seasons').highcharts({
				title: {
					text: '<?php echo lang('sunlength','c')?>'
				},
				subtitle: {
						text: "<?php echo lang('average daily sunlight duration','c')?>"
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
							for($a=0;$a<4;$a++){
								echo "'".$seasons[$a]."',";
							}
							echo "'".lang('year','c')."'";
						?>
					],
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('average sunlight duration','c')?> (<?php echo lang('minAbbr','l')?>)"
						},
					},
				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('sunlight','c')?>',
						color: '#FFFF4C',
						zIndex: 1,
						data: [
							<?php
								echo number_format(array_sum($spring_sunlights)/3,0,".","").",".number_format(array_sum($summer_sunlights)/3,0,".","").",".number_format(array_sum($autumn_sunlights)/3,0,".","").",".number_format(array_sum($winter_sunlights)/3,0,".","").",";
								echo number_format((array_sum($sunlights)/count($sunlights)),0,".","");
							?>
						],
						type: 'column',
					},
				]
			});

			$('#graph_daylength_seasons').highcharts({
				title: {
					text: '<?php echo lang('day length','c')?>'
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
							for($a=0;$a<4;$a++){
								echo "'".$seasons[$a]."',";
							}
						?>
					],
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('day length','c')?> (<?php echo lang('minAbbr','l')?>)",
						},
						min: 0,
					},

				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('day length','c')." (".lang('range','l').")"?>',
						color: '#AAAAAA',
						data: [
							<?php
								echo "[".number_format(min($spring_daylengths),0,".","").",".number_format(max($spring_daylengths),0,".","")."],";
								echo "[".number_format(min($summer_daylengths),0,".","").",".number_format(max($summer_daylengths),0,".","")."],";
								echo "[".number_format(min($autumn_daylengths),0,".","").",".number_format(max($autumn_daylengths),0,".","")."],";
								echo "[".number_format(min($winter_daylengths),0,".","").",".number_format(max($winter_daylengths),0,".","")."],";
							?>
						],
						type: 'columnrange',
					},
					{
						name: '<?php echo lang('day length','c')?>',
						color: '#FFFF26',
						data: [
							<?php
								echo number_format(array_sum($spring_daylengths)/count($spring_daylengths),0,".","").",";
								echo number_format(array_sum($summer_daylengths)/count($summer_daylengths),0,".","").",";
								echo number_format(array_sum($autumn_daylengths)/count($autumn_daylengths),0,".","").",";
								echo number_format(array_sum($winter_daylengths)/count($winter_daylengths),0,".","").",";
							?>
						],
						type: 'spline',
					},
				]
			});

			$('#graph_daylength_seasons_lite').highcharts({
				title: {
					text: '<?php echo lang('day length','c')?>'
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					categories:
					[
						<?php
							for($a=0;$a<4;$a++){
								echo "'".$seasons[$a]."',";
							}
						?>
					],
				},
				yAxis:[
					{
						title: {
							text: "<?php echo lang('day length','c')?> (<?php echo lang('minAbbr','l')?>)",
						},
						min: 0,
					},

				],
				tooltip: {
					shared: true,
				},
				series: [
					{
						name: '<?php echo lang('day length','c')." (".lang('range','l').")"?>',
						color: '#AAAAAA',
						data: [
							<?php
								echo "[".number_format(min($spring_daylengths),0,".","").",".number_format(max($spring_daylengths),0,".","")."],";
								echo "[".number_format(min($summer_daylengths),0,".","").",".number_format(max($summer_daylengths),0,".","")."],";
								echo "[".number_format(min($autumn_daylengths),0,".","").",".number_format(max($autumn_daylengths),0,".","")."],";
								echo "[".number_format(min($winter_daylengths),0,".","").",".number_format(max($winter_daylengths),0,".","")."],";
							?>
						],
						type: 'columnrange',
					},
					{
						name: '<?php echo lang('day length','c')?>',
						color: '#FFFF26',
						data: [
							<?php
								echo number_format(array_sum($spring_daylengths)/count($spring_daylengths),0,".","").",";
								echo number_format(array_sum($summer_daylengths)/count($summer_daylengths),0,".","").",";
								echo number_format(array_sum($autumn_daylengths)/count($autumn_daylengths),0,".","").",";
								echo number_format(array_sum($winter_daylengths)/count($winter_daylengths),0,".","").",";
							?>
						],
						type: 'spline',
					},
				]
			});
		})
		</script>
		<script type="text/javascript"> //sparkline
			$(function() {
				$(".inlinesparklinepie").sparkline("html", {
					type: "pie",
					sliceColors: ["#2693FF", "#FFFFFF"],
					borderWidth: 1,
					borderColor: "#000000",
				});
			});
			$(function() {
				$(".inlinesparklinepie_sun").sparkline("html", {
					type: "pie",
					sliceColors: ["#FFFF26", "#000000"],
					borderWidth: 1,
					borderColor: "#000000",
				});
			});
			$(function() {
				$(".inlinesparklinepie_sun_big").sparkline("html", {
					type: "pie",
					sliceColors: ["#FFFF26", "#888888"],
					borderWidth: 1,
					width: "30px",
					height: "30px",
					borderColor: "#000000",
				});
			});
		</script>
		<script> //div contents
			$(function() {
				$("#climate_div").hide();
				$("#climate-opener").click(function(){
					var txt = $("#climate_div").is(':visible') ? '+' : '-';
					$("#climate-opener").text(txt);
					$( "#climate_div" ).slideToggle( "slow", function() {
					});
				});
				$("#temperature-content").hide();
				$("#temperature-opener").click(function(){
					var txt = $("#temperature-content").is(':visible') ? '+' : '-';
					$("#temperature-opener").text(txt);
					$( "#temperature-content" ).slideToggle( "slow", function() {
					});
				});
				$("#temperature-content-lite").hide();
				$("#temperature-opener-lite").click(function(){
					var txt = $("#temperature-content-lite").is(':visible') ? '+' : '-';
					$("#temperature-opener-lite").text(txt);
					$( "#temperature-content-lite" ).slideToggle( "slow", function() {
					});
				});
				$("#humidity-content").hide();
				$("#humidity-opener").click(function(){
					var txt = $("#humidity-content").is(':visible') ? '+' : '-';
					$("#humidity-opener").text(txt);
					$( "#humidity-content" ).slideToggle( "slow", function() {
					});
				});
				$("#rain-content").hide();
					$("#rain-opener").click(function(){
					var txt = $("#rain-content").is(':visible') ? '+' : '-';
					$("#rain-opener").text(txt);
					$( "#rain-content" ).slideToggle( "slow", function() {
					});
				});
				$("#rain-content-lite").hide();
				$("#rain-opener-lite").click(function(){
					var txt = $("#rain-content-lite").is(':visible') ? '+' : '-';
					$("#rain-opener-lite").text(txt);
					$( "#rain-content-lite" ).slideToggle( "slow", function() {
					});
				});
				$("#light-content").hide();
				$("#light-opener").click(function(){
					var txt = $("#light-content").is(':visible') ? '+' : '-';
					$("#light-opener").text(txt);
					$( "#light-content" ).slideToggle( "slow", function() {
					});
				});

				$("#light-content-lite").hide();
				$("#light-opener-lite").click(function(){
					var txt = $("#light-content-lite").is(':visible') ? '+' : '-';
					$("#light-opener-lite").text(txt);
					$( "#light-content-lite" ).slideToggle( "slow", function() {
					});
				});
				$("#settings_div").hide();
				$("#settings_button").click(function(){
					$( "#settings_div" ).slideToggle( "slow", function() {
					});
				});
			})
		</script>
		<script> //div display
			$(function() {
				$("#temperature").hide();
				$("#temperature-lite").hide();
				$("#humidity").hide();
				$("#rain").hide();
				$("#rain-lite").hide();
				$("#light").hide();
				$("#temperature-decor").hide();
				$("#temperature-decor-lite").hide();
				$("#humidity-decor").hide();
				$("#rain-decor").hide();
				$("#rain-decor-lite").hide();
				$("#light-decor").hide();

				<?php
					if($temps_state == true){
						echo "$('#temperature-lite').show();";
						echo "$('#temperature-decor-lite').show();";
					}
					if($temps_max_state == true){
						echo "$('#temperature-lite').hide();";
						echo "$('#temperature').show();";
						echo "$('#temperature-decor-lite').hide();";
						echo "$('#temperature-decor').show();";
					}
					if($humidities_state == true){
						echo "$('#humidity').show();";
						echo "$('#humidity-decor').show();";
					}
					if($rains_state == true){
						echo "$('#rain-lite').show();";
						echo "$('#rain-decor-lite').show();";
					}
					if($wetdays_state == true){
						echo "$('#rain-lite').hide();";
						echo "$('#rain').show();";
						echo "$('#rain-decor-lite').hide();";
						echo "$('#rain-decor').show();";
					}
					if($sunlights_state == true){
						echo "$('#light-lite').hide();";
						echo "$('#light').show();";
						echo "$('#light-decor-lite').hide();";
						echo "$('#light-decor').show();";
					}
				?>
			})
		</script>
		<?php include("../../css/highcharts.php");?>
		<br>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
