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
	#	Station details
	#
	# 	Page providing details about the station.
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
	
	if(file_exists("../../admin/infoPages.txt")){
		$textData = json_decode(file_get_contents("../../admin/infoPages.txt"),true);
	}
	else{
		$textData['weatherStation'] = "Weather Station information not yet provided in the template admin section.";
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $stationModel?></title>
		<?php metaHeader()?>
		<style>
			
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1><?php echo lang('weather station','c')?></h1>
				<?php echo $textData['weatherStation']?>
			</div>
		</div>
		<?php include($baseURL."footer.php")?>
	</body>
</html>
	