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
	#	METAR 
	#
	# 	Ajax called script to load METAR report.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../config.php");
	//include($baseURL."header.php");
	include($baseURL."/css/design.php");

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
	
	include ("phpmyeasyweather.inc.php");

	
	
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
	
	function lang($string,$case){
		global $language;
		global $lang;
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
					$str = encodeUTF8(strtolower(utf8_decode($str)));
				}
				if($case=="u"){
					$str = encodeUTF8(strtoupper(utf8_decode($str)));	
				}
				if($case=="c"){
					$str = ucfirst($str);
				}
				if($case=="w"){
					$str = ucwords($str);
				}
			}
		}
		
		else{
			if (array_key_exists($string, $language['gb'])) { // string exists, but is not yet translated, we return english equivalent
				$str = $language['gb'][$string];
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
			}
		}
		return decodeUTF8($str);
	}
	
	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);
	
	$metarID = $_GET['id'];
	if($metarID == ""){
		$metarID = $stationMETAR;
	}
	
	if ($input = getMetarFromWWW ($metarID)) {
	   $data = $input["metar"];  
	} else {
	   echo "Invalid METAR ID ".$metarID;
	   die;
	}
	
	$stationInfo = getStationInfo($metarID);
	$reportTimeGMT = getIssueTime ($data);
	$tempC = getMetarTemp  ($data);
	if($tempC!=false){
		$tempF = round(($tempC*1.8)+32);
	}
	$humidity = getMetarHumidity ($data);
	$dewpointC = getMetarDewpoint ($data);
	if($dewpointC!=false){
		$dewpointF = round(($dewpointC*1.8)+32);
	}
	$wind = getWind ($data);
	$visibilityKM = getVisibility ($data);
	$visibilityMI = preg_replace('/\D/', '', $visibilityKM);
	$visibilityMI = round($visibilityMI*0.621371,1);
	if (preg_match('/>/', $visibilityKM)){
		$visibilityMI = "> ".$visibilityMI;
	}
	if (preg_match('/</', $visibilityKM)){
		$visibilityMI = "< ".$visibilityMI;
	}
	if(getVisibility ($data)==9999 || getVisibility ($data)==8888){
		$visibilityKM = "> 10";
		$visibilityMI = "> 6";
	}
	
	if($visibilityKM>100){       
		$visibilityKM = $visibilityKM/1000;              
		$visibilityMI = round($visibilityMI/1000, 1);  
	}
	if($visibilityMI>100){        
		$visibilityMI = round($visibilityMI/1000, 1); 
	}
	
	
	$cloudlayer = 0;
	$cloudsRaw = array();
	$clouds = array();
	while ($cloudsRaw = getClouds ($data, $cloudlayer++)) {
       array_push($clouds,$cloudsRaw);
	}
	$pressureHpa = getPressure ($data);
	if($pressureHpa!=false){
		$pressureinHg = round($pressureHpa * 0.0295299830714,2);
	}
	$conditions = getConditions ($data);	
	$conditions = str_replace("light", $language[$lang]["light"], $conditions);
	$conditions = str_replace("heavy", $language[$lang]["heavy"], $conditions);
	$conditions = str_replace("showers of small hail", $language[$lang]["showers of small hail"], $conditions);
	$conditions = str_replace("waft of mist", $language[$lang]["waft of mist"], $conditions);
	$conditions = str_replace("thunderstorm with rain", $language[$lang]["thunderstorm with rain"], $conditions);
	$conditions = str_replace("thunderstorm with hail", $language[$lang]["thunderstorm with hail"], $conditions);
	$conditions = str_replace("thunderstorm with small hail", $language[$lang]["thunderstorm with small hail"], $conditions);
	$conditions = str_replace("rain", $language[$lang]["rain"], $conditions);
	$conditions = str_replace("snow", $language[$lang]["snow"], $conditions);
	$conditions = str_replace("squalls", $language[$lang]["squalls"], $conditions);
	$conditions = str_replace("diamond dust", $language[$lang]["diamond dust"], $conditions);
	$conditions = str_replace("ice pellets", $language[$lang]["ice pellets"], $conditions);
	$conditions = str_replace("mist", $language[$lang]["mist"], $conditions);
	$conditions = str_replace("fog", $language[$lang]["fog"], $conditions);
	$conditions = str_replace("smoke", $language[$lang]["smoke"], $conditions);
	$conditions = str_replace("haze", $language[$lang]["haze"], $conditions);
	$conditions = str_replace("volcanic ash", $language[$lang]["volcanic ash"], $conditions);
	$conditions = str_replace("widespread dust", $language[$lang]["widespread dust"], $conditions);
	$conditions = str_replace("sand", $language[$lang]["sand"], $conditions);
	$conditions = str_replace("dust storm", $language[$lang]["dust storm"], $conditions);
	$conditions = str_replace("funnel cloud", $language[$lang]["funnel cloud"], $conditions);
	$conditions = str_replace("shallow", $language[$lang]["shallow"], $conditions);
	$conditions = str_replace("patches", $language[$lang]["patches"], $conditions);
	$conditions = str_replace("drifting", $language[$lang]["drifting"], $conditions);
	$conditions = str_replace("blowing", $language[$lang]["blowing"], $conditions);
	$conditions = str_replace("thunderstorm", $language[$lang]["thunderstorm"], $conditions);
	$conditions = str_replace("supercooled (freezing)", $language[$lang]["supercooled (freezing)"], $conditions);
	$conditions = str_replace("outside the airport", $language[$lang]["outside the airport"], $conditions);
	$conditions = str_replace("sandstorm", $language[$lang]["sandstorm"], $conditions);
	$conditions = str_replace("snow grains", $language[$lang]["snow grains"], $conditions);
	$conditions = str_replace("showers", $language[$lang]["showers"], $conditions);
	$conditions = str_replace("hail", $language[$lang]["hail"], $conditions);
	$conditions = str_replace("drizzle", $language[$lang]["drizzle"], $conditions);
	$conditions = str_replace("small hail", $language[$lang]["small hail"], $conditions);
	
	// station not found by default script - use alternative
	if($stationInfo['station']==""){	
		$myfile = fopen("stations.txt", "r") or die("Unable to open file!");
		while(!feof($myfile)) {
			$string = fgets($myfile);
			$code = substr($string,20,4);
			if($code==$metarID){
				$stationInfo['country'] = substr($string,81,2);
				$stationInfo['station'] = trim(substr($string,3,16));
				$stationInfo['latitude'] = substr($string,39,2).".".substr($string,43,2)." ".substr($string,45,1);
				$stationInfo['longitude'] = substr($string,47,3).".".substr($string,51,2)." ".substr($string,53,1);
				$stationInfo['altimeter'] = trim(substr($string,55,4));
			}
		}
		fclose($myfile);	
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<style>
			body{
				padding: 20px;
			}
			.parameterIcon{
				width: 50px;
			}
			.parameterIcon2{
				width: 30px;
			}
			.values{
				text-align:center;
				font-weight: bold;
				font-size: 1.1em;
			}
			#headerLine{
				background-color: #<?php echo $color_schemes[$design2]['900']?>;
			}
			#subheaderLine{
				background-color: #<?php echo $color_schemes[$design2]['800']?>;
			}
		</style>
	</head>
	<body>
		<div style="text-align:center">
			<div id="headerLine">
				<table style="width:100%;text-align:center" cellspacing="2" cellpadding="2">
					<tr>
						<td style="text-align:left;width:20%">
							<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo strtolower($stationInfo['country'])?>.png" style="width:80px">
						</td>
						<td style="text-align:center">
							<h1>
							<?php echo $stationInfo['station']?>
							</h1>
						</td>
						<td style="text-align:center;width:20%;font-size:1.4em;font-weight:bold">
							<?php echo $metarID?>
						</td>
					</tr>
				</table>
			</div>
			<div id="subheaderLine">
				<table style="width:100%;text-align:center">
					<tr>
						<td style="width:33%;text-align:center">
							<table style="width:100%;text-align:center">
								<tr>
									<td>
										<img src="<?php echo $pageURL.$path?>icons/lat.png" class="parameterIcon2">
									</td>
									<td style="text-align:center">
										<?php echo $stationInfo['latitude']?>
									</td>
								</tr>
							</table>
						</td>
						<td style="width:33%;text-align:center">
							<table style="width:100%;text-align:center">
								<tr>
									<td>
										<img src="<?php echo $pageURL.$path?>icons/lon.png" class="parameterIcon2">
									</td>
									<td style="text-align:center">
										<?php echo $stationInfo['longitude']?>
									</td>
								</tr>
							</table>
						</td>
						<td style="width:33%;text-align:center">
							<table style="width:100%;text-align:center">
								<tr>
									<td>
										<img src="<?php echo $pageURL.$path?>icons/elevation.png" class="parameterIcon2">
									</td>
									<td style="text-align:left">
										<?php echo $stationInfo['altimeter']?> m
										<br>
										<?php echo round($stationInfo['altimeter']*3.28084)?> ft
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<br><br>
			<table style="width:100%">
				<tr>
					<?php 
						if($tempC!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/temp.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($humidity!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/humidity.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($pressureHpa!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/pressure.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($dewpointC!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($wind!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/wind.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($wind['gust']==1){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/gust.png" class="parameterIcon"></td>
					<?php
						}
					?>
					<?php 
						if($visibilityKM!=false){ 
					?>
							<td style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/visibility.png" class="parameterIcon"></td>
					<?php
						}
					?>
				</tr>
				<tr>
					<?php 
						if($tempC!=false){ 
					?>
							<td class="values"><?php echo $tempC." °C<br>".$tempF." °F";?></td>
					<?php
						}
					?>
					<?php 
						if($humidity!=false){ 
					?>
							<td class="values"><?php echo $humidity." %";?></td>
					<?php
						}
					?>
					<?php 
						if($pressureHpa!=false){ 
					?>
							<td class="values"><?php echo $pressureHpa." hPa<br>".$pressureinHg." inHg";?></td>
					<?php
						}
					?>
					<?php 
						if($dewpointC!=false){ 
					?>
							<td class="values"><?php echo $dewpointC." °C<br>".$dewpointF." °F";?></td>
					<?php
						}
					?>
					<?php 
						if($wind!=false){ 
					?>
							<td class="values"><?php echo $wind['wkm']." km/h<br>".$wind['wms']." m/s<br>".$wind['wkt']." kt<br>".$wind['wmh']." mph";?><br><?php echo lang("direction".windAbb($wind['deg']),'c')?></td>
					<?php
						}
					?>
					<?php 
						if($wind['gust']==1){ 
					?>
							<td class="values"><?php echo $wind['gkm']." km/h<br>".$wind['gms']." m/s<br>".$wind['gkt']." kt<br>".$wind['gmh']." mph";?></td>
					<?php
						}
					?>
					<?php 
						if($visibilityKM!=false){ 
					?>
							<td class="values"><?php echo $visibilityKM." km<br>".$visibilityMI." mi";?></td>
					<?php
						}
					?>
				</tr>
			</table>
			<?php 
				if(count($clouds)>0){
			?>
				<table style="width:100%">
					<tr>
						<td style="text-align:center;width:10%">
							<img src="<?php echo $pageURL.$path?>icons/clouds.png" class="parameterIcon">
						</td>
						<td>
							<table style="width:100%">
								<?php
									for($i=0;$i<count($clouds);$i++){
								?>
									<tr>
										<td style="width:40px">
											<img src="<?php echo $pageURL.$path?>icons/clouds<?php echo $clouds[$i]['cov1']?>.png" style="height: 30px"> 
										</td>
										<td style="width:40px">
											<?php echo strtolower($language[$lang]['To'])?>
										</td>
										<td style="width:40px">
											<img src="<?php echo $pageURL.$path?>icons/clouds<?php echo $clouds[$i]['cov2']?>.png" style="height: 30px">
										</td>
										<td style="padding-left:20px;text-align:left">
											<img src="<?php echo $pageURL.$path?>icons/cloudbase.png" style="height: 30px;padding-right: 10px;">
											<?php echo $clouds[$i]['meters']." m (".$clouds[$i]['feet']." ft)";?>
										</td>
									</tr>
								<?php
									}
								?>
							</table>
						</td>
						<?php
							if($conditions!=false){
						?>
							<td style="text-align:center;width:10%">
								<img src="<?php echo $pageURL.$path?>icons/weather.png" class="parameterIcon">
							</td>
							<td class="values">
								<?php echo $conditions?>
							</td>
						<?php
							}
						?>
					</tr>
				</table>
			<?php
				}
			?>
		</div>
	</body>
</html>

	