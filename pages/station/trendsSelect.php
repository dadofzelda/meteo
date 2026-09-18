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
	#	Trends
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
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("trends",'c')?></title>
		<?php metaHeader()?>
		<style>
			.statIcon{
				font-size:3em;
				padding:15px;
				cursor: pointer;
				opacity: 0.85;
			}
			.statIcon:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="text-align:center">
				<h1><?php echo lang('trends','c')?></h1>
				<br><br>
				<span class="mticon-temp statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DT'" title="<?php echo lang('temperature','c')?>"></span>
				<span class="mticon-apparent statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DA'" title="<?php echo lang('apparent temperature','c')?>"></span>
				<span class="mticon-dewpoint statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DD'" title="<?php echo lang('dew point','c')?>"></span>
				<span class="mticon-humidity statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DH'" title="<?php echo lang('humidity','c')?>"></span>
				<span class="mticon-pressure statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DP'" title="<?php echo lang('pressure','c')?>"></span>
				<span class="mticon-wind statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DW'" title="<?php echo lang('wind speed','c')?>"></span>
				<span class="mticon-gust statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DG'" title="<?php echo lang('wind gust','c')?>"></span>
				<?php
					if($solarSensor){
				?>
					<span class="mticon-sun statIcon tooltip" onclick="location='<?php echo $pageURL.$path?>pages/station/redirect.php?url=trends.php?var%3DS'" title="<?php echo lang('solar radiation','c')?>"></span>
				<?php 
					}
				?>
			</div>
		</div>
	<?php include($baseURL."footer.php");?>
	</body>
</html>
