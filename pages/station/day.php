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
		<title><?php echo lang("day",'c')?></title>
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
				font-size: 3em;
				cursor: pointer;
				opacity: 0.8;
				padding-left: 15px;
				padding-right: 15px;
				color: #<?php echo $color_schemes[$design2]['200']?>;
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
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1><?php echo lang("day calculations",'c')?></h1>
				<br>
				<div id="daySettingsDiv" style="width:96%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['900']?>;border-radius: 20px;padding:1%">
					<div style="width:98%;margin:0 auto;text-align:center">
						<span class="mticon-sun" style="font-size:4em"><br><img src='<?php echo $pageURL.$path?>icons/time.png' style="width:20px;opacity:0.8">
					</div>
					<div id="mainSlider" style="width:80%;margin: 0 auto"></div>
					<div id="mainSliderInfo" style="width:80%;margin: 0 auto;text-align:center">
						<table style="width:50%;margin:0 auto">
							<tr>
								<td style='text-align:left'>
									<span id="sliderFrom">7</span>
								</td>
								<td style='text-align:right'>
									<span id="sliderTo">19</span>
								</td>
							</tr>
						</table>
						<br>
						<span class="mticon-temp varSelectorIcon tooltip" id="varSelectorT" title="<?php echo lang('temperature','c')?>"></span>
						<span class="mticon-apparent varSelectorIcon tooltip" id="varSelectorA" title="<?php echo lang('apparent temperature','c')?>"></span>
						<span class="mticon-dewpoint varSelectorIcon tooltip" id="varSelectorD" title="<?php echo lang('dewpoint','c')?>"></span>
						<span class="mticon-humidity varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('humidity','c')?>"></span>
						<span class="mticon-pressure varSelectorIcon tooltip" id="varSelectorP" title="<?php echo lang('pressure','c')?>"></span>
						<span class="mticon-wind varSelectorIcon tooltip" id="varSelectorW" title="<?php echo lang('wind speed','c')?>"></span>
						<span class="mticon-gust varSelectorIcon tooltip" id="varSelectorG" title="<?php echo lang('wind gust','c')?>"></span>
						<?php 
							if($solarSensor){
						?>
							<span class="mticon-sun varSelectorIcon tooltip" id="varSelectorS" title="<?php echo lang('solar radiation','c')?>">
						<?php 
							}
						?>
					</div>
				</div>
				<br>
				<div id="dayResultsDiv" style="width:98%;margin:0 auto;text-align:center"></div>
				<br><br>
			</div>
		</div>
		
		
		<script>
			$(document).ready(function() {
				$( "#mainSlider" ).slider({
				  range: true,
				  min: 0,
				  max: 23,
				  values: [ 7, 19 ],
				  slide: function( event, ui ) {
					from = eval(ui.values[0]);
					to = eval(ui.values[1]);
					$( "#sliderFrom" ).html(from);
					$( "#sliderTo" ).html(to);
				  }
				});
				$(".varSelectorIcon").click(function(){
					from = $("#sliderFrom").html();
					to = $("#sliderTo").html();
					$("#dayResultsDiv").html("<br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' style='width:100px' class='mtSpinner'>");
					id = $(this).attr("id");
					id = id.replace("varSelector","");
					$("#dayResultsDiv").load("dayLoad.php?from="+from+"&to="+to+"&var="+id);
				})
			})			
			
		</script>
		<?php include($baseURL."css/highcharts.php");?>
		<?php include($baseURL."footer.php");?>
	</body>
</html>