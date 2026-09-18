<?php

	# 		Sea Temperatures
	# 		Namespace:		seaTemperature
	#		Meteotemplate Block

	# 		v1.0 - Jun 17, 2017
	# 			- initial release
	# 		v1.1 - Jun 18, 2017
	# 			- units bug fix
	# 			- source URL bug fix
	# 		v1.2 - Jun 19, 2017
	# 			- minor bug fixes
	# 		v2.0 - Jun 20, 2017
	# 			- added support for multiple locations
	# 		v2.1 - Oct 30, 2017
	# 			- bug fixes
	# 		v2.2 - Jun 6, 2018
	# 			- bug fixes
	
		
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

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	error_reporting(E_ALL);

	createCacheDir();

	$urls = explode(";",$defaultURL);
	$count = 0;
	foreach($urls as $thisURL){
		if($count<5){
			$thisURL = trim($thisURL,"/");
			$thisURL = trim($thisURL);
			$thisURL = "https://www.seatemperature.org/" . $thisURL;
			$sourceURLs[] = $thisURL;
		}
		$count++;
	}

	// current
	if(file_exists("cache/current.txt")){
		if (time()-filemtime("cache/current.txt") > 60 * 60 * 12) {
			unlink("cache/current.txt");
		}
	}
	if(file_exists("cache/current.txt")){
		$data = json_decode(file_get_contents("cache/current.txt"),true);
	}
	else {
		for($i=0;$i<count($sourceURLs);$i++){
			$rawHTML = loadContent($sourceURLs[$i],8);
			$rawHTML = cleanHTML($rawHTML);

			preg_match('/<div id="sea-temperature".*?>(.*?)&/',$rawHTML,$matchedCurrent);
			$data[$i]['current'] = $matchedCurrent[1];

			// title
			preg_match('/<h1>(.*?)</',$rawHTML,$matchedTitle);
			$data[$i]['title'] = trim(str_replace("Sea Temperature","",$matchedTitle[1]));

			// this month avg
			preg_match('/<li class="hot">Max:(.*?)&.*?<.*?> ?<.*?>Avg:(.*?)&.*?<.*?> ?<li class="cold">Min:(.*?)&/',$rawHTML,$matchedClimate);
			$data[$i]['thisMonthMax'] = trim($matchedClimate[1]);
			$data[$i]['thisMonthAvg'] = trim($matchedClimate[2]);
			$data[$i]['thisMonthMin'] = trim($matchedClimate[3]);
		}

		file_put_contents("cache/current.txt",json_encode($data));
	}

	for($i=0;$i<count($data);$i++){
		$data[$i]['current'] = number_format(convertor($data[$i]['current'],"C",$displayTempUnits),1,".","");
		$data[$i]['thisMonthMax'] = number_format(convertor($data[$i]['thisMonthMax'],"C",$displayTempUnits),1,".","");
		$data[$i]['thisMonthAvg'] = number_format(convertor($data[$i]['thisMonthAvg'],"C",$displayTempUnits),1,".","");
		$data[$i]['thisMonthMin'] = number_format(convertor($data[$i]['thisMonthMin'],"C",$displayTempUnits),1,".","");
	}

?>
	<style>
		#seaTempDiv{
			font-weight: bold;
			font-size: 2.5em;
			padding: 10px;
			padding-top: 0px;
			padding-bottom: 0px;
		}
		.seaTempDiv{
			display: inline-block;
		}
	</style>
	<img src="homepage/blocks/seaTemperature/icons/<?php echo $theme?>/seaTemp.png" style="width:50px"><br>
	<?php 
		for($i=0;$i<count($data);$i++){
	?>
			<div class="seaTempDiv">
				<h2><?php echo $data[$i]['title']?></h2>
				<div id="seaTempDiv">
					<?php echo floor($data[$i]['current'])?>.<span style="font-size:0.6em"><?php echo number_format(($data[$i]['current'] - floor($data[$i]['current']))*10,0,".","")?>&nbsp;<?php echo unitFormatter($displayTempUnits)?></span>
				</div>
				<span class="more" onclick="txt = $('#seaTempMore<?php echo $i?>').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('hide','l')?>';$('#seaTempMore<?php echo $i?>').slideToggle(800);$(this).text(txt)">
						<?php echo lang('more','l')?>
				</span>
			</div>
	<?php 
		}
	?>
	<?php 
		for($i=0;$i<count($data);$i++){
	?>
			<div class="details" id="seaTempMore<?php echo $i?>" style="width:95%;padding-bottom:5px;text-align:justify;margin:0 auto">
				<h2><?php echo $data[$i]['title']?></h2>
				<h3 style="text-align:center"><?php echo lang('month'.date('n'),'c')?></h3>
				<table style="table-layout:fixed;margin:0 auto;font-size:1.2em;min-width:50%;font-variant:small-caps" cellspacing="4" cellpadding="4">
					<tr>
						<td style="width:33%"><?php echo lang('maximumAbbr','c')?><br><?php echo floor($data[$i]['thisMonthMax'])?>.<span style="font-size:0.7em"><?php echo number_format(($data[$i]['thisMonthMax'] - floor($data[$i]['thisMonthMax']))*10,0,".","")?>&nbsp;<?php echo unitFormatter($displayTempUnits)?></span>
						</td>
						<td style="width:33%"><?php echo lang('avgAbbr','c')?><br><?php echo floor($data[$i]['thisMonthAvg'])?>.<span style="font-size:0.7em"><?php echo number_format(($data[$i]['thisMonthAvg'] - floor($data[$i]['thisMonthAvg']))*10,0,".","")?>&nbsp;<?php echo unitFormatter($displayTempUnits)?></span>
						</td>
						<td style="width:33%"><?php echo lang('minimumAbbr','c')?><br><?php echo floor($data[$i]['thisMonthMin'])?>.<span style="font-size:0.7em"><?php echo number_format(($data[$i]['thisMonthMin'] - floor($data[$i]['thisMonthMin']))*10,0,".","")?>&nbsp;<?php echo unitFormatter($displayTempUnits)?></span>
						</td>
					</tr>
				</table>
			</div>
	<?php 
		}
	?>
	<div style="width:90%;margin:0 auto;font-size:0.8em;font-variant:small-caps;text-align:center">
		<?php echo lang('data source','c')?>: seatemperature.org
	</div>


