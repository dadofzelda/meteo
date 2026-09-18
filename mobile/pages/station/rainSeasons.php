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
		<title><?php echo lang("rain seasons",'c')?></title>
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
				<h1><?php echo lang("rain seasons",'c')?></h1>
				<table style="width:98%;margin:0 auto">
					<tr>
						<td style="width:50%;padding-right:10px;text-align:right">
							<?php echo lang('rain season begins','c')?>
						</td>
						<td style="width:50%;padding-left:10px;text-align:left">
							<select id="seasonBegins" class="button2">
								<option value="" selected><?php echo lang("select",'c')?></option>
								<?php 
									for($i=1;$i<13;$i++){
								?>
										<option value="<?php echo $i?>"><?php echo lang('month'.$i,'c')?></option>
								<?php
									}
								?>
							</select>
						</td>
					</tr>
				</table>
				<div id="resultsSeasonDiv" style="width:98%;margin:0 auto;text-align:center">
				
				</div>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$("#seasonBegins").change(function(){
					m = $(this).val();
					if(m!=""){
						$("#resultsSeasonDiv").html("<br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' style='width:120px' class='mtSpinner'>");
						$("#resultsSeasonDiv").load("rainSeasonsLoad.php?m="+m);
					}
				})
			})			
			
		</script>
		<?php include("../../../css/highcharts.php");?>
		<?php include("../../footer.php");?>
	</body>
</html>