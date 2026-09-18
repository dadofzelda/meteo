<?php

	# 		Steel Series Gauges
	# 		Namespace:		ssGauges
	#		Meteotemplate Block

	# 		v2.0 - Feb 18, 2017
	# 			- added time of last update
	# 			- added possibility to set gauge order and type regardless of plugin setup
	# 			- design tweaks
	# 		v2.1 - Feb 27, 2017
	# 			- added possibility to disable clock at the top
	# 		v2.2 - Mar 17, 2017
	# 			- added update timeout
	# 		v3.0 - May 7, 2017
	# 			- optimization
	# 			- prevent loading of Google Analytics twice on homepage
	# 		v3.1 - May 18, 2017
	# 			- bug fixes
	# 		v3.2 - Jul 18, 2021
	# 			- fixed browser error: MIME type mismatch (X-Content-Type-Options: nosniff)

		
	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");
	
	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	// check SteelSeries plugin and load settings
	if(!file_exists("../../../plugins/steelSeries/settings.php")){
		die("You need the SteelSeries Plugin for this block to work.");
	}

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}
?>
	<style>
		
	</style>
	<iframe src="homepage/blocks/ssGauges/index.php?theme=<?php echo $theme?>&size=<?php echo $ssGaugeSize?>" style="width:98%;margin:0 auto;border:0px" onload="resizeSSGauges(this)"></iframe>
	<script>
		function resizeSSGauges(obj) {
			obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
		}
	</script>

