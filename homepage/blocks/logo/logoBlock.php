<?php

	# 		Logo
	# 		Namespace:		logo
	#		Meteotemplate Block
	
	# 		Version 1.1 - Jan 29, 2016
	#		- added responsiveness
	
	include("settings.php");
	
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
	
?>
	<style>
		#logoImg{
			<?php
				if($addBorder){
					echo "border: 1px solid #".$color_schemes[$design2]['400'].";";
					echo "border-radius: 10px;";
					echo "max-width: 100%;";
				}
			?>
		}
	</style>
	
	<div style="width:98%;margin:0 auto">
		<img src="homepage/blocks/logo/<?php echo $logoFile?>" id="logoImg"> 
	</div>
	
