<?php
	############################################################################
	#
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#  Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Main Homepage - Mobile
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
	
	require_once("config.php");

	// not an admin
	if(!isset($_SESSION['user'])){
		$_SESSION['user'] = "user";
	}

	############################################################################

	# HOMEPAGE SETTINGS

	############################################################################

	$blockColor = $design;

	############################################################################

	$homepageData = json_decode(file_get_contents("admin/homepageLayoutDesktop.txt"),true);
	$theme = $homepageData['desktop']['theme'];

	if($theme=="sun"){
		$sunRiseTheme = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		$sunSetTheme = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		if(time()<$sunRiseTheme || time()>$sunSetTheme){
			$theme = "dark";
		}
		else{
			$theme = "light";
		}
	}

	############################################################################

	include("homepage/css/themeSetter.php");
	include("css/design.php");
	include("header.php");

	$loadedBlock = $_GET['block'];

	

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<meta name="description" content="<?php echo $pageDesc?>">
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsAPIKey?>"></script>
		<script src="//code.highcharts.com/stock/highstock.js"></script>
		<script src="//code.highcharts.com/stock/highcharts-more.js"></script>
		<?php include("homepage/css/style.php");?>
		<?php
			if($theme=="light"){
				$menuBlockLoaderColor = "rgba(0, 0, 0, 0.2)";
				$menuBlockLoaderColor2 = "rgba(0, 0, 0, 0.8)";
			}
			else{
				$menuBlockLoaderColor = "rgba(255, 255, 255, 0.2)";
				$menuBlockLoaderColor2 = "rgba(255, 255, 255, 0.8)";
			}
		?>
		<style>
			@-webkit-keyframes load8 {
			  0% {
				-webkit-transform: rotate(0deg);
				transform: rotate(0deg);
			  }
			  100% {
				-webkit-transform: rotate(360deg);
				transform: rotate(360deg);
			  }
			}
			@keyframes load8 {
			  0% {
				-webkit-transform: rotate(0deg);
				transform: rotate(0deg);
			  }
			  100% {
				-webkit-transform: rotate(360deg);
				transform: rotate(360deg);
			  }
			}
			#footer{
				/*display:none;*/
				max-width: 100%;
			}
			
		</style>
	</head>
	<!--<body style="overflow-x:hidden">-->
	<body style="text-align:center">
		<div id="main" style="text-align:center;width:100%;max-width:100%;">
		<div class="block" id="loadedBlock" style="min-height:90%;width: 100%;padding-left:0px;padding-right:0px;border:0px;margin:0 auto"><?php include("homepage/css/spinner.php");?></div>
		</div>
		<?php include($baseURL."footer.php");?> 
		<script>
			$(document).ready(function(){
				$('#loadedBlock').load("homepage/blocks/<?php echo $loadedBlock?>/<?php echo $loadedBlock?>Block.php", function() {
					$(".tooltip").tooltipster();
				});
			});
		</script>
	</body>
</html>
