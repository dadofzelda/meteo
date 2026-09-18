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
	#	Page details
	#
	# 	Page providing information about your webpage.
	#
	#############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	
	if(file_exists("../../../admin/infoPages.txt")){
		$textData = json_decode(file_get_contents("../../../admin/infoPages.txt"),true);
	}
	else{
		$textData['webpage'] = "Website information not yet provided in the template admin section.";
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $stationLocation?></title>
		<?php metaHeader()?>
		<style>
			
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:60%;margin:0 auto">
				<h1>Website</h1>
				<br>
				<?php echo $textData['webpage']?>
				<br>
			</div>
		</div>
		<?php include("../../footer.php")?>
	</body>
</html>
	