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
	#	Night calculations
	#
	# 	A script which generates the page for interactive table.
	#
	############################################################################
	#	
	#
	# 	v10.0 Banana 2016-10-28
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("night",'c')?></title>
		<?php metaHeader()?>
		<script src="//code.highcharts.com/stock/highstock.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<style>
			.sort{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.sort:hover{
				opacity: 1;
			}
			.varSelectorIcon{
				width: 40px;
				cursor: pointer;
				opacity: 0.8;
				padding-left: 15px;
				padding-right: 15px;
			}
			.varSelectorIcon:hover{
				opacity: 1;
			}
			#mainSlider{
				background: #<?php echo $color_schemes[$design2]['200']?>
			}
			.inner-resizer {
				padding: 10px;
			}
			.resizer {   
				margin: 0 auto;
				width: 98%;
				
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1><?php echo lang("night calculations",'c')?></h1>
				<br>
				<div id="nightSettingsDiv" style="width:96%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['900']?>;border-radius: 20px;padding:1%">
					<div style="width:98%;margin:0 auto;text-align:center">
						<img src="<?php echo $pageURL.$path?>icons/moon.png" style="width:50px;padding-bottom:20px"><br><img src='<?php echo $pageURL.$path?>icons/time.png' style="width:20px;opacity:0.8">
					</div>
					<div id="mainSlider" style="width:80%;margin: 0 auto"></div>
					<div id="mainSliderInfo" style="width:80%;margin: 0 auto;text-align:center">
						<table style="width:50%;margin:0 auto">
							<tr>
								<td style='text-align:left'>
									<span id="sliderFrom">19</span>
								</td>
								<td style='text-align:right'>
									<span id="sliderTo">7</span>
								</td>
							</tr>
						</table>
						<br>
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="varSelectorIcon tooltip" id="varSelectorT" title="<?php echo lang('temperature','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="varSelectorIcon tooltip" id="varSelectorA" title="<?php echo lang('apparent temperature','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="varSelectorIcon tooltip" id="varSelectorD" title="<?php echo lang('dewpoint','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('humidity','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="varSelectorIcon tooltip" id="varSelectorP" title="<?php echo lang('pressure','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="varSelectorIcon tooltip" id="varSelectorW" title="<?php echo lang('wind speed','c')?>">
						<img src="<?php echo $pageURL.$path?>icons/gust.png" class="varSelectorIcon tooltip" id="varSelectorG" title="<?php echo lang('wind gust','c')?>">
						<?php 
							if($solarSensor){
						?>
							<img src="<?php echo $pageURL.$path?>icons/sun.png" class="varSelectorIcon tooltip" id="varSelectorS" title="<?php echo lang('solar radiation','c')?>">
						<?php 
							}
						?>
					</div>
				</div>
				<br>
				<div id="nightResultsDiv" style="width:98%;margin:0 auto;text-align:center"></div>
				<br><br>
			</div>
		</div>
		
		
		<script>
			$(document).ready(function() {
				$( "#mainSlider" ).slider({
				  range: true,
				  min: -12,
				  max: 12,
				  values: [ -7, 7 ],
				  slide: function( event, ui ) {
					from = 24 - eval(ui.values[0]*-1);
					if(from>23){
						from = from-24;
					}
					to = eval(ui.values[1]);
					if(to<0){
						to = to+24;
					}
					$( "#sliderFrom" ).html(from);
					$( "#sliderTo" ).html(to);
				  }
				});
				$(".varSelectorIcon").click(function(){
					from = $("#sliderFrom").html();
					to = $("#sliderTo").html();
					$("#nightResultsDiv").html("<br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' style='width:100px' class='mtSpinner'>");
					id = $(this).attr("id");
					id = id.replace("varSelector","");
					$("#nightResultsDiv").load("nightLoad.php?from="+from+"&to="+to+"&var="+id);
				})
			})			
			
		</script>
		<?php include("../../../css/highcharts.php")?>
		<?php include("../../footer.php")?>
	</body>
</html>