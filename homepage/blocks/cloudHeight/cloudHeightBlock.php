<?php

	# 		Cloud Height
	# 		Namespace:		cloudHeight
	#		Meteotemplate Block

	# 		v1.1 - Jan 29, 2016
	#			- added responsiveness
	#		v2.0 - Aug 05, 2016
	#			- added cloudbase height above sea level
	#			- css tweaks
	#		v3.0 - Jan 18, 2017
	#			- added possibility to add heading
	#			- added description of cloud height
	#			- added formula used for calculation
	# 		v4.0 - May 12, 2017
	# 			- added translations

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

	$result = mysqli_query($con,"
		SELECT T, D
		FROM alldata
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$T = $row['T'];
		$D = $row['D'];
	}

	$spread = abs($T-$D);
	if($dataTempUnits=="C"){
		$feetHeight = $spread / 2.5 * 1000;
	}
	if($dataTempUnits=="F"){
		$feetHeight = $spread / 4.4 * 1000;
	}
	$mHeight = $feetHeight * 0.3048;

	if(trim($stationElevationUnits)=="ft"){
		$mHeightSea = $mHeight + round($stationElevation*0.3048);
		$feetHeightSea = $feetHeight + round($stationElevation);
	}
	else{
		$mHeightSea = $mHeight + round($stationElevation);
		$feetHeightSea = $feetHeight + round($stationElevation/0.3048);
	}

?>
	<style>

	</style>
	<div style="width:98%;margin: 0 auto;">
		<?php
			if($cloudHeightHeading){
		?>
				<h2><?php echo lang('cloud height','c')?></h2>
		<?php
			}
		?>
		<table style="width:100%">
			<tr>
				<td style="text-align:center;width:50%">
					<img src="homepage/blocks/cloudHeight/icons/<?php echo $theme?>/cloudHeight.png" style="max-width:70px;width:100%">
				</td>
				<td style="text-align:center;width:50%">
					<img src="homepage/blocks/cloudHeight/icons/<?php echo $theme?>/cloudHeightSea.png" style="max-width:70px;width:100%">
				</td>
			</tr>
			<tr>
				<td style="text-align:center">
					<?php echo round($mHeight)?> m / <?php echo round($feetHeight)?> ft
					<br />
					<span style="font-size:0.8em"><?php echo lang('above ground','l')?></span>
				</td>
				<td style="text-align:center">
					<?php echo round($mHeightSea)?> m / <?php echo round($feetHeightSea)?> ft
					<br />
					<span style="font-size:0.8em"><?php echo lang('above sea level','l')?></span>
				</td>
			</tr>
		</table>
		<div style="width:98%;text-align:justify;font-size:0.9em;margin:0 auto" class="details" id="cloudHeightDetails">
			<p>
				<?php echo lang('the cloud base (height) is the lowest altitude of the visible portion of the cloud. it is traditionally expressed either in m or feet above mean sea level (or planetary surface), or as the corresponding pressure level in hectopascal (hpa, equivalent to millibar).','c')?>
			</p>
			<p>
				<?php echo lang('the height of the cloud base can be measured using a ceilometer or it can be estimated from surface measurements of air temperature and humidity. the calculation is based on the assumption that the air temperature drops 9.84 degrees c per 1000 m of altitude and the dewpoint drops 1.82 degrees c per 1000 meters altitude.','c')?>
			</p>
			<p>
				<div style="margin:0 auto;text-align:center">
					<i>h = ((T - D) / 4.4) * 1000</i>
				</div>
				<span style="font-size:0.8em"><?php echo lang('h - cloud height above ground [ft]')?></span><br />
				<span style="font-size:0.8em"><?php echo lang('t - air temperature [f]')?></span><br />
				<span style="font-size:0.8em"><?php echo lang('d - dew point [f]')?></span><br />
			</p>
		</div>
		<span class="more" onclick="txt = $('#cloudHeightDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('hide','l')?>';$('#cloudHeightDetails').slideToggle(800);$(this).text(txt)">
			<?php echo lang('more','l')?>
		</span>
	</div>
