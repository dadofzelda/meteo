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
	#	Header
	#
	# 	Main header for the page with interactive weather background.
	#
	############################################################################
	#
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	if($headerImg=="interactive"){
		$interactiveBanner = true;
	}
	else{
		$interactiveBanner = false;
	}

	// check last config modify time to load correct CSS
	$configMkTime = filemtime($baseURL."config.php");
	$timestampCSS = date("YmdHi",$configMkTime);

	############################################################################
	ini_set('precision', 6);
	mysqli_set_charset($con,"utf8");

	include($baseURL."scripts/functions.php");
	include($baseURL."scripts/headerMetar.php");

	date_default_timezone_set($stationTZ);

	if($stationWarnings){
		$warningActive = false;
		$stationWaringsArray['highTWarning'] = false;
		$stationWaringsArray['lowTWarning'] = false;
		$stationWaringsArray['highWWarning'] = false;
		$stationWaringsArray['highRWarning'] = false;
		$stationWaringsArray['highSWarning'] = false;

		if($stationWarningsInterval=="interval 10 minute"){
			$intervalText = lang('in the last','l')." 10 ".lang('minutes','l');
		}
		else if($stationWarningsInterval=="interval 20 minute"){
			$intervalText = lang('in the last','l')." 20 ".lang('minutes','l');
		}
		else if($stationWarningsInterval=="interval 30 minute"){
			$intervalText = lang('in the last','l')." 30 ".lang('minutes','l');
		}
		else if($stationWarningsInterval=="interval 45 minute"){
			$intervalText = lang('in the last','l')." 45 ".lang('minutes','l');
		}
		else if($stationWarningsInterval=="interval 1 hour"){
			$intervalText = lang('in the last','l')." 1 ".lang('hour','l');
		}
		else{
			$intervalText = "";
		}

		if($displayRainUnits=="mm"){
			$rainWarningDecimals = 1;
		}
		else{
			$rainWarningDecimals = 2;
		}
		$result = mysqli_query($con,"
			SELECT max(Tmax), min(Tmin), max(G), max(RR), max(S)
			FROM alldata
			WHERE DateTime >= now() - ".$stationWarningsInterval."
			ORDER BY DateTime DESC
			LIMIT 1"
		);
		while($row = mysqli_fetch_array($result)){
			$intervalHighT = $row['max(Tmax)'];
			$intervalLowT = $row['min(Tmin)'];
			$intervalHighW = $row['max(G)'];
			$intervalHighR = $row['max(RR)'];
			$intervalHighS = $row['max(S)'];
		}
		if(isset($intervalHighT)){
			if($intervalHighT>=$warningHighT){
				$stationWarningsArray['highTWarning'] = true;
				$stationWarningsArray['highTWarningText'] = lang("high temperature warning","c").": ".number_format(convertT($intervalHighT),1,".","")."°".$displayTempUnits." ".$intervalText;
				$warningActive = true;
			}
		}
		if(isset($intervalLowT)){
			if($intervalLowT<=$warningLowT){
				$stationWarningsArray['lowTWarning'] = true;
				$stationWarningsArray['lowTWarningText'] = lang("low temperature warning","c").": ".number_format(convertT($intervalLowT),1,".","")."°".$displayTempUnits." ".$intervalText;
				$warningActive = true;
			}
		}
		if(isset($intervalHighW)){
			if($intervalHighW>=$warningHighW){
				$stationWarningsArray['highWWarning'] = true;
				$stationWarningsArray['highWWarningText'] = lang("high wind warning","c").": ".number_format(convertW($intervalHighW),1,".","")." ".$displayWindUnits." ".$intervalText;
				$stationWarningsArray['highWWarningText'] = str_replace("kmh","km/h",$stationWarningsArray['highWWarningText']);
				$stationWarningsArray['highWWarningText'] = str_replace("ms","m/s",$stationWarningsArray['highWWarningText']);
				$warningActive = true;
			}
		}
		if(isset($intervalHighR)){
			if($intervalHighR>=$warningHighR){
				$stationWarningsArray['highRWarning'] = true;
				$stationWarningsArray['highRWarningText'] = lang("heavy rain warning","c").": ".number_format(convertR($intervalHighR),$rainWarningDecimals,".","")." ".$displayRainUnits."/".lang("hAbbr",'l')." ".$intervalText;
				$warningActive = true;
			}
		}
		if($solarSensor){
			if(isset($intervalHighS)){
				if($intervalHighS>=$warningHighS){
					$stationWarningsArray['highSWarning'] = true;
					$stationWarningsArray['highSWarningText'] = lang("intense sun shine warning","c").": ".number_format(($intervalHighS),1,".","")."W/m2 ".lang("in the last 60 minutes",'l');
					$warningActive = true;
				}
			}
		}
	}

	$sun = date_sun_info(time(),$stationLat, $stationLon);
	$sunRise = $sun['sunrise'];
	$sunSet = $sun['sunset'];
	//$sunSet = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,0);

	if($interactiveBanner){	
		if(time()>$sunRise && time()<$sunSet){
			$dayTime = "d";
			$moonview = false;
		}
		else{
			$dayTime = "n";
		}

		// Get Moon phase
		$mp = new moonPhase();
		$img_phase = $mp->getPositionInCycle();
		$intervals = 118;
		$moonIcon = round(($img_phase/(1/$intervals)));

		// Get METAR - besökaren kan välja en egen station via inställningsdialogen
		// (cookie weatherWarningsPrefs), annars adminens $stationMETAR
		$ICAO = $stationMETAR;
		if(isset($_COOKIE['weatherWarningsPrefs'])){
			$userPrefs = json_decode($_COOKIE['weatherWarningsPrefs'],true);
			if(is_array($userPrefs) && isset($userPrefs['metar']) && trim($userPrefs['metar'])!=""){
				$ICAO = trim($userPrefs['metar']);
			}
		}

		// Interactive banner caching
		if(file_exists($baseURL."cache/interactiveBanner.txt")){ 
			if (time()-filemtime($baseURL."cache/interactiveBanner.txt") > 60 * 60) {
				unlink($baseURL."cache/interactiveBanner.txt");
			}
		}
		if(file_exists($baseURL."cache/interactiveBanner.txt")){
			$condition = file_get_contents($baseURL."cache/interactiveBanner.txt");
		}
		else{
			$urlMETAR = "https://tgftp.nws.noaa.gov/data/observations/metar/stations/".strtoupper($ICAO).".TXT";
			$metarDataString = curlMain($urlMETAR,3);
			$metarDataString = cleanHTML($metarDataString);
			preg_match("/^.*?".strtoupper($ICAO)."(.*?)$/",$metarDataString,$matchedHeaderMetar);
			if(isset($matchedHeaderMetar[1])){
				$metarDataString = $matchedHeaderMetar[1];
				$condition = getWeatherGrafic($metarDataString,$ICAO); // options: clear, few, partly, mostly, overcast, NA, fog, drizzle, rain, lightrain, snow, thunderstorm
			}
			else{
				$condition = "NA";
			}
			file_put_contents($baseURL."cache/interactiveBanner.txt",$condition);
		}

		// Select background image
		if($condition=="clear"){
			if($dayTime=="d"){
				$bgImage = "sunny";
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "night_main";
				$moonView = true;
			}
		}
		if($condition=="few" || $condition=="partly"){
			if($dayTime=="d"){
				$bgImage = "scattered".rand(1,4);
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "cloudy_night".rand(1,3);
				$moonView = true;
			}
		}
		if($condition=="mostly"){
			if($dayTime=="d"){
				$bgImage = "cloudy".rand(1,4);
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "cloudy_night".rand(1,3);
				$moonView = false;
			}
		}
		if($condition=="overcast" || $condition=="drizzle" || $condition=="lightrain" || $condition=="rain"){
			if($dayTime=="d"){
				$bgImage = "overcast".rand(1,5);
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "overcast_night".rand(1,2);
				$moonView = false;
			}
		}
		if($condition=="fog"){
			if($dayTime=="d"){
				$bgImage = "fog";
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "fog_night";
				$moonView = false;
			}
		}
		if($condition=="snow"){
			if($dayTime=="d"){
				$bgImage = "snow".rand(1,2);
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "overcast_night";
				$moonView = false;
			}
		}
		if($condition=="thunderstorm"){
			if($dayTime=="d"){
				$bgImage = "thunder_day".rand(1,4);
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "thunder_night".rand(1,3);
				$moonView = false;
			}
		}
		if($condition=="NA"){
			if($dayTime=="d"){
				$bgImage = "day_main";
				$moonView = false;
			}
			if($dayTime=="n"){
				$bgImage = "night_main";
				$moonView = true;
			}
		}

		if($condition=="snow"){
			$issnowing = true;
		}
		else{
			$issnowing = false;
		}

		if($condition=="drizzle" || $condition=="lightrain" || $condition=="rain"){
			$israining = true;
			// Get wind gust if raining
			$result = mysqli_query($con,"
				SELECT G
				FROM alldata
				ORDER BY DateTime DESC
				LIMIT 1
				"
			);
			while($row = mysqli_fetch_array($result)){
				$bannerGust = $row['G'];
			}
		}
		else{
			$israining = false;
			$bannerGust = 0;
		}

		if($condition=="drizzle"){
			$bannerRain['dropWidth'] = 1;
			$bannerRain['dropLength'] = 5;
			$bannerRain['dropIntensity'] = array(50,50);
			$bannerRain['dropStep'] = 2;
		}
		if($condition=="lightrain"){
			$bannerRain['dropWidth'] = 2;
			$bannerRain['dropLength'] = 5;
			$bannerRain['dropIntensity'] = array(20,20);
			$bannerRain['dropStep'] = 2;
		}
		if($condition=="rain"){
			$bannerRain['dropWidth'] = 3;
			$bannerRain['dropLength'] = 6;
			$bannerRain['dropIntensity'] = array(20,20);
			$bannerRain['dropStep'] = 4;
		}
	}

	if(isset($headerConditions)){
		if($headerConditions){
			$sun = date_sun_info(time(),$stationLat, $stationLon);
			$sunRise = $sun['sunrise'];
			$sunSet = $sun['sunset'];
			/*$sunRise = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90,0);
			$sunSet = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90,0);*/

			if(time()>$sunRise && time()<$sunSet){
				$headerConditionsColor = "black";
				$headerConditionsShadowColor = "#fff";
			}
			else{
				$headerConditionsColor = "white";
				$headerConditionsShadowColor = "#000";
			}
		}
	}
	if($displayTempUnits=="C"){
		$freezingLine = 0;
	}
	else{
		$freezingLine = 32;
	}

	// load all auto load scripts
	$files = glob($baseURL."load/all/*.php");
	if(($files) != ""){ 
		foreach($files as $file){
			include($file);
		}
	}

	// check admin login 
	if(isset($_COOKIE["meteotemplateAdmin"])) {
		if(version_compare(PHP_VERSION, '5.3.7') >= 0) {
			require($baseURL."admin/hash.php");
			if (password_verify($adminPassword, $_COOKIE["meteotemplateAdmin"])) {
				if (session_status() == PHP_SESSION_NONE) {
					session_start();
				}
				$_SESSION['user'] = "admin";
			}
			else{
				// incorrect cookie hash
			}
		}
	}

	// determine IP
	if (!empty($_SERVER['HTTP_CLIENT_IP'])){
		$myIP = $_SERVER['HTTP_CLIENT_IP'];
	}
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$myIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}
	else{
		$myIP = $_SERVER['REMOTE_ADDR'];
	}
	// check if admin allowed 
	if(!isset($enableAdminIP)){ // user updated template and did not update setup file
		$enableAdminIP = false;
	}
	if($enableAdminIP){
		$allowedAccess = false;
		$allowedIPs = explode(",",$adminIPs);
		for($i=0;$i<count($allowedIPs);$i++){
			$thisIP = trim($allowedIPs[$i]);
			if (strpos($myIP, $thisIP) !== false) {
				$allowedAccess = true;
			}
		}
		if(!$allowedAccess){
			$_SESSION['user'] = "user";
		}
	}

	$highChartsCreditsText = $pageName." @ Meteotemplate";

	// decimal points 
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


	function curl_get_contents($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function metaHeader(){
		global $issnowing;
		global $stationLat;
		global $design;
		global $design2;
		global $bgImage;
		include("config.php");
		include($baseURL."css/design.php");
		global $interactiveBanner;
		global $condition;
		global $bannerRain;
		global $timestampCSS;
?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="author" content="Jachym Brzezina">
		<meta property="og:image" content="icons/logoBlack.png">
		<meta property="og:title" content="<?php echo $pageName?>">
		<meta property="og:url" content="<?php echo $pageURL?>">
		<meta property="og:site_name" content="<?php echo $pageName?>">
		<meta property="og:description" content="<?php echo $pageDesc?>">
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.min.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tooltipster.js"></script>
		<script src="<?php echo $pageURL.$path?>scripts/scrolltop.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/tableExport.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/html2canvas.js"></script>
		<?php
			if($interactiveBanner){
				if($issnowing==true){
					echo "<script src='".$pageURL.$path."scripts/snowfall.jquery.js'></script>";
				}
			}
		?>

		<?php
			if($googleAnalytics){
		?>
				<script>
					  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

					  ga('create', '<?php echo $GAcode?>', 'auto');
					  ga('send', 'pageview');
				</script>
		<?php
			}
		?>
		<style>
			#userSettings{
				width: 40px;
				cursor: pointer;
				opacity: 0.7;
			}
			#userSettings:hover{
				opacity: 1;
			}
			#templateHelp{
				width: 40px;
				cursor: pointer;
				opacity: 0.7;
			}
			#templateHelp:hover{
				opacity: 1;
			}
			#settingsDialog{
				z-index: 9999!important;
			}
			.customUnitsIcon{
				width: 30px;
			}
			.langIcon{
				width: 30px;
				opacity: 0.7;
				cursor: pointer;
			}
			.langIcon:hover{
				opacity: 1;
			}
			#header{
				/*height: 180px;*/
			}
			#title1{
				<?php 
					if($titleSmallCaps){
				?>
						font-size: 3.5em;
						font-variant: small-caps;
				<?php 
					}
					else{
				?>
						font-size: 3.1em;
						font-variant: normal;
				<?php
					}
				?>	
				text-shadow: 2px 2px #333333;
				<?php 
					if($titleBoldText){
				?>
					font-weight: bold;
				<?php 
					}
				?>
			}
			#title2{
				<?php 
					if($subtitleSmallCaps){
				?>
						font-size: 2.5em;
						font-variant: small-caps;
				<?php 
					}
					else{
				?>
						font-size: 2.1em;
						font-variant: normal;
				<?php
					}
				?>	
				text-shadow: 2px 2px #333333;
				<?php 
					if($subtitleBoldText){
				?>
					font-weight: bold;
				<?php 
					}
				?>
			}
			#country_text{
				font-variant: small-caps;
				text-shadow: 2px 2px #333333;
				font-family:"PT Sans","Arial Narrow",Arial,Helvetica,sans-serif;
			}
			.banner {
				height: 150px;
				border-radius: 5px;
				margin-left:auto;
				margin-right:auto;
				border: 0.8px solid #<?php echo $color_schemes[$design2]['900']?>;
				color: white;
				padding: 10px;
				background-repeat: no-repeat;
				background-size: 100% 100%;
				position: relative;
			}
			.banner table {
				color: white;
			}
			.flag{
				width:100px;
			}
			.canvas {
				margin: 0px auto;
				width: 98%;
				height: 100px;
			}
			.canvas-moon{
				margin: 0px auto;
				width: 100px;
				height: 100px;
			}
			.canvas-snow {
				margin: 0px auto;
				width: 98%;
				height: 160px;
			}
			<?php
				if($interactiveBanner){
			?>
					.bg{
						background-image: url("<?php echo $pageURL.$path?>imgs/forecastImgs/weather_bg/<?php echo $bgImage?>.jpg");
					}
			<?php
				}
				else{
			?>
					.bg{
						background-image: url("<?php echo $customHeaderImg?>");
					}
			<?php
				}
			?>
			.smallcaps{
				font-variant: small-caps;
			}
			<?php
				if($stationLat<0){
			?>
				#moon2{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
			<?php
				}
			?>

			.punct{
				background-color: #aaaaaa;
				position:absolute;
				width:<?php echo $bannerRain['dropWidth']?>px;
				height:<?php echo $bannerRain['dropLength']?>px;
				border-radius:2px;
			}
			.helpIcon{
				font-size: 1.5em;
			}
			.iconHelpDiv{
				display:inline-block;
				padding:5px;
				min-width: 100px;
				background: #<?php echo $color_schemes[$design2]['900']?>;
				border-radius: 10px;
				color: white;
				margin-top:5px;
			}
		</style>

		<link rel="icon" href="icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="<?php echo $pageURL.$path?>icons/favicon.ico" type="image/x-icon">
		<link rel="stylesheet"  href="<?php echo $pageURL.$path?>css/main.php?v=<?php echo $timestampCSS?>" media="all" title="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo $pageURL.$path?>css/tooltipster.css" media="all" title="screen">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/font/styles.css">
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/fontAwesome/css/font-awesome.min.css">

		<?php
			if($cookieNotice){
		?>
				<link rel="stylesheet" type="text/css" href="<?php echo $pageURL.$path?>css/cookieconsent.min.css" />
				<script src="<?php echo $pageURL.$path?>scripts/cookieconsent.min.js"></script>
				<script>
				window.addEventListener("load", function(){
				window.cookieconsent.initialise({
				"palette": {
					"popup": {
					"background": "#252e39"
					},
					"button": {
					"background": "#<?php echo $color_schemes[$design2]['900']?>"
					}
				},
				"showLink": false,
				"content": {
					"message": "<?php echo lang('This webpage uses cookies','-')?>.",
					"dismiss": "OK"
				}
				})});
				</script>
		<?php
			}
		?>
<?php
	}
?>
<?php
	function bodyHeader(){
		global $con;
		global $moonIcon;
		global $condition;
		global $bannerRain;
		global $pageName;
		global $bannerGust;
		global $stationCountry;
		global $stationLocation;
		global $issnowing;
		global $israining;
		global $bannerGust;
		global $moonView;
		global $interactiveBanner;
		global $headerSubtitleSelect;
		global $headerSubtitleText;
		global $headerTitleSelect;
		global $headerTitleText;
		global $color_schemes;
		global $stationWarnings;
		global $stationWarningsArray;
		global $warningActive;
		global $headerConditions;
		global $headerConditionsColor;
		global $headerConditionsShadowColor;
		global $headerConditionsInterval;
		global $weatherWarningsEnabled;
		global $weatherWarningsCounties;
		global $baseURL;
		global $pageURL;
		global $path;
		global $displayTempUnits;
		global $displayPressUnits;
		global $displayRainUnits;
		global $displayWindUnits;
		global $design2;
		global $flagIconShape;
		global $enableKeyboard;
		global $_SESSION;
		include("config.php");
?>
	<div id="header" style="position:relative">
		<?php
			if($stationWarnings){
				if($warningActive){
		?>
					<div style="position:absolute;bottom:10px;right:10px;z-index:10;background:#<?php echo $color_schemes[$design2]['700']?>;padding-left:5px;padding-right:5px;border:1px solid #<?php echo $color_schemes[$design2]['500']?>;border-radius:10px;" id="warningsDiv">
						<?php
							if($stationWarningsArray['highTWarning']==true){
						?>
								<img src="<?php echo $pageURL.$path?>icons/warnings/warningHot.png" style="width:40px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highTWarningText']?>">
						<?php
							}
						?>
						<?php
							if($stationWarningsArray['lowTWarning']==true){
						?>
								<img src="<?php echo $pageURL.$path?>icons/warnings/warningCold.png" style="width:40px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['lowTWarningText']?>">
						<?php
							}
						?>
						<?php
							if($stationWarningsArray['highWWarning']==true){
						?>
								<img src="<?php echo $pageURL.$path?>icons/warnings/warningWind.png" style="width:40px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highWWarningText']?>">
						<?php
							}
						?>
						<?php
							if($stationWarningsArray['highRWarning']==true){
						?>
								<img src="<?php echo $pageURL.$path?>icons/warnings/warningRain.png" style="width:40px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highRWarningText']?>">
						<?php
							}
						?>
						<?php
							if($solarSensor){
								if($stationWarningsArray['highSWarning']==true){
						?>
									<img src="<?php echo $pageURL.$path?>icons/warnings/warningSun.png" style="width:40px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highSWarningText']?>">
						<?php
								}
							}
						?>
					</div>
		<?php
				}
			}
		?>
		<?php
			if(isset($headerConditions)){
				if($headerConditions && $con instanceof mysqli){
					$resultHeader = mysqli_query($con,"
						SELECT T,H,P,W,D,S,R
						FROM alldata
						ORDER BY DateTime DESC
						LIMIT 1
						"
					);
					while($row = mysqli_fetch_array($resultHeader)){
						$headerConditionValues['T'] = number_format(convertT($row['T']),1,".","");
						$headerConditionsArray[] = "'".$headerConditionValues['T']." ".unitFormatter($displayTempUnits)."'";
						$headerConditionValues['H'] = number_format($row['H'],1,".","");
						$headerConditionsArray[] = "'".$headerConditionValues['H']." %'";
						if($displayPressUnits=="hpa"){
							$headerConditionValues['P'] = number_format(convertP($row['P']),1,".","");
						}
						else{
							$headerConditionValues['P'] = number_format(convertP($row['P']),2,".","");
						}
						$headerConditionsArray[] = "'".$headerConditionValues['P']." ".unitFormatter($displayPressUnits)."'";
						$headerConditionValues['W'] = number_format(convertW($row['W']),1,".","");
						$headerConditionsArray[] = "'".$headerConditionValues['W']." ".unitFormatter($displayWindUnits)."'";
						if($displayRainUnits=="mm"){
							$headerConditionValues['R'] = number_format(convertR($row['R']),1,".","");
						}
						else{
							$headerConditionValues['R'] = number_format(convertR($row['R']),2,".","");
						}
						$headerConditionsArray[] = "'".$headerConditionValues['R']." ".unitFormatter($displayRainUnits)."'";
						if($solarSensor){
							$headerConditionValues['S'] = $row['S'];
							$headerConditionsArray[] = "'".$headerConditionValues['S']." W/m2'";
						}
					}
		?>
					<div style="color:<?php echo $headerConditionsColor?>;position:absolute;bottom:10px;left:10px;z-index:10;font-weight:bold;font-size:1.0em;text-shadow: 1px 1px <?php echo $headerConditionsShadowColor?>;opacity:0.8" id="headerConditionsDiv"></div>
					<input id="headerConditionsCurrent" type="hidden" value="0">
					<script>
						headerConditionsUpdate();
						setInterval(function(){
							headerConditionsUpdate();
						}, (<?php echo $headerConditionsInterval?>*1000));
						function headerConditionsUpdate(){
							<?php 
								if(!isset($headerConditionsArray)){
									$headerConditionsArray = array();
								}
							?>
							var headerConditionsArray = new Array(<?php if(count($headerConditionsArray) > 0){echo implode(",",$headerConditionsArray);}?>);
							headerConditionsValue = eval($("#headerConditionsCurrent").val());
							$("#headerConditionsDiv").html(headerConditionsArray[headerConditionsValue]);

							maxHeaderConditionsValue = <?php echo (count($headerConditionsArray)-1);?>;
							if(headerConditionsValue<maxHeaderConditionsValue){
								$("#headerConditionsCurrent").val((headerConditionsValue+1));
							}
							else{
								$("#headerConditionsCurrent").val(0);
							}
						}
					</script>
		<?php
				}
			}
		?>
		<?php
			if($interactiveBanner){
		?>
			<div class="banner bg" id="mainBanner" style="position:relative">
				<div id="canvas-snow" style="position:absolute; z-index:5; top:10px" class="canvas-snow"></div>
				<div style="position:absolute; z-index:20; width:100%">
					<table style="vertical-align: middle;width:100%;height:100%">
						<tr>
							<td style="border: 0 solid #000000;width:120px;text-align:center">
								<?php
									if($headerLeftImg=="flag"){
								?>
										<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo strtolower($stationCountry) ?>.png" class='flag' style="border: none" alt="">
								<?php
									}
									else{
								?>
										<img src="<?php echo $customHeaderLeftImg?>" class='flag' style="border: none" alt="">
								<?php
									}
								?>
							</td>
							<td style="border: 0 solid #000000;text-align:center">
								<?php if($headerTitleSelect=="show"){echo "<a href='".$pageURL.$path."'><span id='title1'>".$headerTitleText."</span></a>";}?><?php if($headerSubtitleSelect=="show"){echo "<br><a href='".$pageURL.$path."'><span id='title2'>".$headerSubtitleText."</span></a>";}?>
							</td>
							<td style="border: 0 solid #000000;text-align:right;width:120px">
								<div id="moon2" style="opacity: 0.5;padding-right:50px" class="canvas-moon"></div>
							</td>
						</tr>
					</table>
				</div>
				<div style="height:100px;position:absolute;width:80%;margin:0 auto;left:10%" id="rainTopDiv"></div>
			</div>
		<?php
			}
			else{
		?>
				<div class="banner bg" id="mainBanner">
					<div style="position:absolute; z-index:20; width:100%">
						<table style="vertical-align: middle;width:100%;height:100%">
							<tr>
								<td style="border: 0 solid #000000;width:120px;text-align:center">
									<?php
										if($headerLeftImg=="flag"){
									?>
											<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo strtolower($stationCountry) ?>.png" class='flag' style="border: none" alt="">
									<?php
										}
										else{
									?>
											<img src="<?php echo $customHeaderLeftImg?>" class='flag' style="border: none" alt="">
									<?php
										}
									?>
								</td>
								<td style="border: 0 solid #000000;text-align:center">
									<?php if($headerTitleSelect=="show"){echo "<a href='".$pageURL.$path."'><span id='title1'>".$headerTitleText."</span></a>";}?><?php if($headerSubtitleSelect=="show"){echo "<br><a href='".$pageURL.$path."'><span id='title2'>".$headerSubtitleText."</span></a>";}?>
								</td>
								<td style="border: 0 solid #000000;text-align:right;width:120px">
								</td>
							</tr>
						</table>
					</div>
				</div>
				
		<?php
			}
		?>
		<div id="settingsDiv">
			<span class="fa fa-gear" style="font-size:2.2em;color:white;text-shadow:0px 0px 8px #000;" id="userSettings" alt=""></span><?php if(!$hideHelpOpener){?><br><span class="fa fa-question-circle" style="font-size:2.2em;color:white;text-shadow:0px 0px 8px #000;" id="templateHelp" alt=""></span><?php }?>
		</div>
		<?php
			if(!file_exists($baseURL."admin/templateRegistered.txt")){
		?>
				<div style="width:98%;margin:0 auto;font-size:1.2em;font-variant:small-caps">
					<span style="color:red;font-weight:bold">Template unregistered - Go to Meteotemplate Control Panel and Register Your Template :)</span>
				</div>
		<?php
			}
		?>
	</div>
	<?php
		if(isset($weatherWarningsEnabled) && $weatherWarningsEnabled){
			$activeWeatherWarnings = getActiveWeatherWarnings();
			if(count($activeWeatherWarnings) > 0){
	?>
			<div id="weatherWarningBanner" style="width:100%">
				<?php
					foreach($activeWeatherWarnings as $warningIndex => $warn){
						$warningBg = $color_schemes[$warn['levelColor']]['700'];
						$warningFont = $color_schemes[$warn['levelColor']]['font700'];
				?>
						<div style="background:#<?php echo $warningBg?>;color:#<?php echo $warningFont?>;padding:8px 15px;font-weight:bold;font-size:1.05em;border-bottom:1px solid rgba(0,0,0,0.15)">
							<span class="fa fa-exclamation-triangle"></span>
							<?php echo mb_strtoupper($warn['event'],"UTF-8")?> (<?php echo $warn['levelName']?>) &ndash; <?php echo htmlspecialchars($warn['county'])?>
							<?php if($warn['description']!=""){?>
								<span style="cursor:pointer;text-decoration:underline;float:right;font-size:0.9em" onclick="$('#weatherWarningText<?php echo $warningIndex?>').slideToggle();">Visa mer &#9662;</span>
								<div id="weatherWarningText<?php echo $warningIndex?>" style="display:none;font-weight:normal;margin-top:8px;font-size:0.95em;clear:both;padding-top:8px">
									<?php echo htmlspecialchars($warn['description'])?>
								</div>
							<?php }?>
						</div>
				<?php
					}
				?>
			</div>
	<?php
			}
		}
	?>
	<?php
		if($interactiveBanner){
	?>
			<script>
				$(document).ready(function(){
					<?php
						if($moonView==false){
							echo "$('#moon2').hide();";
						}
					?>
					$('#moon2').html("<img src='<?php echo $pageURL.$path?>imgs/moon/<?php echo $moonIcon?>.png' style='width:100px'>");
				})
			</script>
			<?php
				if($issnowing){
			?>
					<script type='text/javascript'>
						$('#canvas-snow').snowfall({
							flakeCount : 600,
							flakeColor : '#EEEEEE',
							flakeIndex: 999999,
							minSize : 1,
							maxSize : 6,
							minSpeed : 1,
							maxSpeed : 6,
							round : true,
							shadow : false,
						});
					</script>
			<?php
				}
				if($israining){
					if($bannerGust<5){
						$bannerWindAngle = 0;
					}
					else if($bannerGust>=5 && $bannerGust<15){
						$bannerWindAngle = 1;
					}
					else if($bannerGust>=15 && $bannerGust<25){
						$bannerWindAngle = 2;
					}
					else if($bannerGust>25){
						$bannerWindAngle = 3;
					}
					else{
						$bannerWindAngle = 0;
					}
			?>
					<script>
						$(document).ready(function(){
							start();
							function strop(cleft, ctop, d) {
								for(i=0;i<1;i++){
									var drop = document.createElement('div');
									drop.className = 'punct';
									drop.style.left = cleft + 'px';
									drop.style.top = ctop + 'px';
									drop.id = d;
									document.getElementById('rainTopDiv').appendChild(drop);
								}
							}
							function randomFromInterval(from, to) {
								return Math.floor(Math.random() * (to - from + 1) + from);
							}
							var interval;
							function newDrop() {
								var x = randomFromInterval(1, $("#rainTopDiv").width()),
									y = randomFromInterval(1, $("#rainTopDiv").height());
								strop(x, y);
								setTimeout(newDrop, <?php echo $bannerRain['dropIntensity'][0]?>);
							}

							function start() {
								newDrop();
								interval = setInterval(function() {
									var drops = document.getElementsByClassName('punct'),
										newY;
									if (drops.length == 0) {
										clearInterval(interval);
										return;
									}
									for (var i = 0; i < drops.length; i++) {
										newY = drops[i].offsetTop + <?php echo $bannerRain['dropStep']?>;
										newX = drops[i].offsetLeft + <?php echo $bannerWindAngle?>;
										if (newY > drops[i].parentNode.offsetHeight) {
											drops[i].parentNode.removeChild(drops[i]);
										}
										else {
											drops[i].style.top = newY + 'px';
											drops[i].style.left = newX + 'px';
										}
									}
								}, <?php echo $bannerRain['dropIntensity'][1]?>);
							}
						})
					</script>
			<?php
				}
			?>
	<?php
		}
	?>
	<?php 
		if($enableKeyboard && $_SESSION['user'] == "admin"){
	?>
			<script src="<?php echo $pageURL.$path?>scripts/mousetrap.min.js"></script>
			<script>
				Mousetrap.bind(['alt+d'], function(e) {
					location = "<?php echo $pageURL.$path?>admin/homepageStart.php?type=desktop";
					return false;
				});
				Mousetrap.bind(['alt+m'], function(e) {
					location = "<?php echo $pageURL.$path?>admin/homepageStart.php?type=mobile";
					return false;
				});
				Mousetrap.bind(['alt+c'], function(e) {
					location = "<?php echo $pageURL.$path?>admin/index.php";
					return false;
				});
				Mousetrap.bind(['alt+p'], function(e) {
					location = "<?php echo $pageURL.$path?>admin/menu/menuTabs.php";
					return false;
				});
			</script>
	<?php
		}
	?>
<?php
	}
?>
