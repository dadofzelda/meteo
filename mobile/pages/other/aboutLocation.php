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
	#	Location details
	#
	# 	Page providing information about station location.
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
		$textData['location'] = "Location information not yet provided in the template admin section.";
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
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
				<table style="width:100%">
					<tr>
						<td style="text-align:left;width:10%">
							<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo strtolower($stationCountry)?>.png" style="width:80px" alt=''>
						</td>
						<td style="text-align:center">
							<h1><?php echo $stationLocation?></h1>
						</td>
						<td style="text-align:left;width:10%">
						
						</td>
					</tr>
				</table>
				<br><br>
				<?php echo $textData['location']?>
				<br>
			</div>
		</div>
		<?php include("../../footer.php")?>
	</body>
</html>
	