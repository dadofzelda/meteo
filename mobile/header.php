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
	# 	Main header for the mobile page with interactive weather background.
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

	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	ini_set('precision', 6); 

	// check last config modify time to load correct CSS
	$configMkTime = filemtime($baseURL."config.php");
	$timestampCSS = date("YmdHi",$configMkTime);

	include($baseURL."scripts/functions.php");
	$templateLog[] = array(microtime(true),"Template core functions loaded","info");

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
			LIMIT 1
		"
		);
		while($row = mysqli_fetch_array($result)){
			$intervalHighT = $row['max(Tmax)'];
			$intervalLowT = $row['min(Tmin)'];
			$intervalHighW = $row['max(G)'];
			$intervalHighR = $row['max(RR)'];
			$intervalHighS = $row['max(S)'];
		}
		if($intervalHighT>=$warningHighT){
			$stationWarningsArray['highTWarning'] = true;
			$stationWarningsArray['highTWarningText'] = number_format(convertT($intervalHighT),1,".","")."°".$displayTempUnits." ".$intervalText;
			$warningActive = true;
		}
		if($intervalLowT<=$warningLowT){
			$stationWarningsArray['lowTWarning'] = true;
			$stationWarningsArray['lowTWarningText'] = number_format(convertT($intervalLowT),1,".","")."°".$displayTempUnits." ".$intervalText;
			$warningActive = true;
		}
		if($intervalHighW>=$warningHighW){
			$stationWarningsArray['highWWarning'] = true;
			$stationWarningsArray['highWWarningText'] = number_format(convertW($intervalHighW),1,".","")." ".$displayWindUnits." ".$intervalText;
			$stationWarningsArray['highWWarningText'] = str_replace("kmh","km/h",$stationWarningsArray['highWWarningText']);
			$stationWarningsArray['highWWarningText'] = str_replace("ms","m/s",$stationWarningsArray['highWWarningText']);
			$warningActive = true;
		}
		if($intervalHighR>=$warningHighR){
			$stationWarningsArray['highRWarning'] = true;
			$stationWarningsArray['highRWarningText'] = number_format(convertR($intervalHighR),$rainWarningDecimals,".","")." ".$displayRainUnits."/".lang('hAbbr','l')." ".$intervalText;
			$warningActive = true;
		}
		if($solarSensor){
			if($intervalHighS>=$warningHighS){
				$stationWarningsArray['highSWarning'] = true;
				$stationWarningsArray['highSWarningText'] = number_format(($intervalHighS),1,".","")."W/m2 ".lang("in the last 60 minutes",'l');
				$warningActive = true;
			}
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

	// load all auto load scripts
	$files = glob($baseURL."load/all/*.php");
	foreach($files as $file){
		include($file);
	}

	/*  CALCULATION FUNCTIONS */
	
	function curl_get_contents($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	function metaHeader(){
		global $design;
		global $design2;
		global $pageName;
		global $authorName;
		global $pageURL;
		global $pageDesc;
		global $googleAnalytics;
		global $GAcode;
		global $color_schemes;
		global $path;
		global $timestampCSS;
		//include("config.php");
		//include($baseURL."css/design.php");
?>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="author" href="<?php echo $authorName?>">
		<meta property="og:image" content="imgs/logo.png">
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
				cursor: pointer;
				opacity: 0.7;
			}
			#userSettings:hover{
				opacity: 1;
			}
			#templateHelp{
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
			.flag{
				width:100px;
			}
			.smallcaps{
				font-variant: small-caps;
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

		<link rel="icon" href="<?php echo $pageURL.$path?>icons/favicon.ico" type="image/x-icon">
		<link rel="shortcut icon" href="<?php echo $pageURL.$path?>icons/favicon.ico" type="image/x-icon">
		<link rel="stylesheet"  href="<?php echo $pageURL.$path?>css/mainMobile.php?v=<?php echo $timestampCSS;?>" media="all" title="screen">
		<link rel="stylesheet" type="text/css" href="<?php echo $pageURL.$path?>css/tooltipster.css" media="all" title="screen">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css">
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/font/styles.css">
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/fontAwesome/css/font-awesome.min.css">
<?php
	}
?>
<?php
	function bodyHeader(){
		global $pageName;
		global $pageURL;
		global $path;
		global $stationCountry;
		global $headerLeftImg;
		global $customHeaderLeftImg;
		global $headerTitleSelect;
		global $headerTitleText;
		global $headerSubtitleSelect;
		global $headerSubtitleText;
		global $color_schemes;
		global $stationWarnings;
		global $stationWarningsArray;
		global $warningActive;
		global $design2;
		global $mobileHeaderImg;
		global $headerConditions;
		global $headerConditionsInterval;
		global $weatherWarningsEnabled;
		global $weatherWarningsCounties;
		global $baseURL;
		global $displayTempUnits;
		global $displayPressUnits;
		global $displayRainUnits;
		global $displayWindUnits;
		global $design2;
		global $flagIconShape;
		include("config.php");
		if(trim($mobileHeaderImg)!=""){
			$mobileHeaderConditionsString = "position: relative;background: url(".$mobileHeaderImg.");background-repeat: no-repeat;background-position: center center;background-size: cover;";
		}
		else{
			$mobileHeaderConditionsString = "";
		}

?>
	<div id="header" style="position:relative;<?php echo $mobileHeaderConditionsString?>">
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
					<div style="color:000;position:absolute;bottom:10px;left:10px;z-index:10;font-weight:bold;font-size:1.0em;text-shadow: 1px 1px #fff;opacity:0.8" id="headerConditionsDiv"></div>
					<input id="headerConditionsCurrent" type="hidden" value="0">
					<script>
						headerConditionsUpdate();
						setInterval(function(){
							headerConditionsUpdate();
						}, (<?php echo $headerConditionsInterval?>*1000));
						function headerConditionsUpdate(){
							var headerConditionsArray = new Array(<?php echo implode(",",$headerConditionsArray);?>);
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
		<table style="width:98%;margin:0 auto">
			<tr>
				<td style="width:15%;text-align:left">
					<?php
						if($headerLeftImg=="flag"){
							$customHeaderLeftImg = $pageURL.$path."imgs/".$flagIconShape."/big/".$stationCountry.".png";
						}
					?>
					<img src="<?php echo $customHeaderLeftImg?>" style="width:100%;max-width:80px;padding-left:5%">
				</td>
				<td>
					<?php if($headerTitleSelect=="show"){echo "<span id='title1'>".$headerTitleText."</span>";}?><?php if($headerSubtitleSelect=="show"){echo "<br><span id='title2'>".$headerSubtitleText."</span>";}?>
				</td>
				<td style="width:15%">

				</td>
			</tr>
		</table>
		<div id="settingsDiv">
			<span class="fa fa-gear" style="font-size:2.2em;color:white;text-shadow:0px 0px 8px #000;" id="userSettings" alt=""></span><?php if(!$hideHelpOpener){?><br><span class="fa fa-question-circle" style="font-size:2.2em;color:white;text-shadow:0px 0px 8px #000;" id="templateHelp" alt=""></span><?php }?>
		</div>
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
						<div style="background:#<?php echo $warningBg?>;color:#<?php echo $warningFont?>;padding:8px 15px;font-weight:bold;font-size:0.95em;border-bottom:1px solid rgba(0,0,0,0.15)">
							<span class="fa fa-exclamation-triangle"></span>
							<?php echo mb_strtoupper($warn['event'],"UTF-8")?> (<?php echo $warn['levelName']?>) &ndash; <?php echo htmlspecialchars($warn['county'])?>
							<?php if($warn['description']!=""){?>
								<span style="cursor:pointer;text-decoration:underline;float:right;font-size:0.9em" onclick="$('#weatherWarningText<?php echo $warningIndex?>').slideToggle();"><?php echo lang('show more','c')?> &#9662;</span>
								<div id="weatherWarningText<?php echo $warningIndex?>" style="display:none;font-weight:normal;margin-top:8px;font-size:0.9em;clear:both;padding-top:8px">
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
		if($stationWarnings){
			if($warningActive){
	?>
				<div style="width:100%;background:#<?php echo $color_schemes[$design2]['900']?>;text-align:center;font-size:0.8em;font-weight:bold;font-variant:small-caps">
					<?php
						if($stationWarningsArray['highTWarning']==true){
					?>
							<img src="<?php echo $pageURL.$path?>icons/warnings/warningHot.png" style="width:40px;margin-top:8px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highTWarningText']?>">
					<?php
						}
					?>
					<?php
						if($stationWarningsArray['lowTWarning']==true){
					?>
							<img src="<?php echo $pageURL.$path?>icons/warnings/warningCold.png" style="width:40px;margin-top:8px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['lowTWarningText']?>">
					<?php
						}
					?>
					<?php
						if($stationWarningsArray['highWWarning']==true){
					?>
							<img src="<?php echo $pageURL.$path?>icons/warnings/warningWind.png" style="width:40px;margin-top:8px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highWWarningText']?>">
					<?php
						}
					?>
					<?php
						if($stationWarningsArray['highRWarning']==true){
					?>
							<img src="<?php echo $pageURL.$path?>icons/warnings/warningRain.png" style="width:40px;margin-top:8px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highRWarningText']?>">
					<?php
						}
					?>
					<?php
						if($solarSensor){
							if($stationWarningsArray['highSWarning']==true){
					?>
								<img src="<?php echo $pageURL.$path?>icons/warnings/warningSun.png" style="width:40px;margin-top:8px;opacity:0.75" class="tooltip" title="<?php echo $stationWarningsArray['highSWarningText']?>">
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
	}
?>
