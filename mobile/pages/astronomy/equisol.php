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
	#	Equinox and Solstice calculation
	#
	# 	A script to calculate and show equinox and solstice for user defined
	#	year.
	#
	############################################################################
	#
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("equinox and solstice","c")?></title>
		<?php metaHeader()?>
		<style>
			#middle{
				background-color: black;
				margin-right: auto;
				margin-left: auto;
				width: 98%;
				text-align: center;
				padding-top: 10px;
				padding-bottom: 10px;
			}
			#year{
				color: #<?php echo $color_schemes[$design2]['400']?>; 
				margin-right: auto;
				margin-left: auto;
				font-size: 1.5em;
				padding: 5px;
				text-align: center;
				font-weight: bold;
				display: inline-block;
			}
		</style>
	</head>
	<body onload="load('na')">
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
		</div>
		<div id="main">
			<h1><?php echo lang('equinox and solstice','')?></h1>
			<br>
			<div id="middle">
			<table style="margin-left:auto;margin-right:auto">
				<tr>
					<td>
					</td>
					<td style="text-align:center">
						<b>
						<?php 
							if($stationLat>=0){
								echo lang('spring equinox','l');
							}
							else{
								echo lang('autumn equinox','l');
							}
						?>
						</b>
						<br>
						<br>
						<div id="spring">
							<br>
						</div>
					</td>
					<td>
					</td>
				</tr>
				<tr>
					<td style="text-align:center">
						<b>
						<?php 
							if($stationLat>=0){
								echo lang('summer solstice','l');
							}
							else{
								echo lang('winter solstice','l');
							}
						?>
						</b>
						<br><br>
						<div id="summer">
							<br>
						</div>
					</td>
					<td>
						<img src="equisol.jpg" style="width:100%" alt="">
					</td>
					<td style="text-align:center">
						<b>
						<?php 
							if($stationLat>=0){
								echo lang('winter solstice','l');
							}
							else{
								echo lang('summer solstice','l');
							}
						?>
						</b>
						<br><br>
						<div id="winter">
							<br>
						</div>
					</td>
				</tr>
				<tr>
					<td>
					</td>
					<td style="text-align:center">
						<b>
						<?php 
							if($stationLat>=0){
								echo lang('autumn equinox','l');
							}
							else{
								echo lang('spring equinox','l');
							}
						?>
						</b>
						<br><br>
						<div id="autumn">
							<br>
						</div>
					</td>
					<td>
					</td>
				</tr>
			</table>
			</div>
			<br>
			<table style="width:100%;text-align:center">
				<tr>
					<td>
						<input type="button" style="button" value=" <<< " class="button2" id="minus10Year">
						<input type="button" style="button" value=" < " class="button2" id="minusYear">
						<div id="year">
						</div>
						<input type="button" style="button" value=" > " class="button2" id="plusYear">
						<input type="button" style="button" value=" >>> " class="button2" id="plus10Year">
					</td>
				</tr>
			</table>
		</div>
		<script>
			function load(year){
				if(year=="na"){
					d = new Date();
					year = d.getFullYear();
				}
				
				$.ajax({
					url : "equisolAjax.php?y="+year,
					dataType : 'json',
					success : function (json) {
						spring = json['spring'];
						summer = json['summer'];
						autumn = json['autumn'];
						winter = json['winter'];
						$('#spring').html(spring);
						$('#summer').html(summer);
						$('#autumn').html(autumn);
						$('#winter').html(winter);
						$('#year').html(year);
					},
				});
			}
			$(document).ready(function() {
				$("#minusYear").click(function() {
					current = eval($('#year').html());
					newYear = current - 1;
					load(newYear);
				});
				$("#plusYear").click(function() {
					current = eval($('#year').html());
					newYear = current + 1;
					load(newYear);
				});
				$("#minus10Year").click(function() {
					current = eval($('#year').html());
					newYear = current - 10;
					load(newYear);
				});
				$("#plus10Year").click(function() {
					current = eval($('#year').html());
					newYear = current + 10;
					load(newYear);
				});
			});
		</script>
		<?php include("../../footer.php")?>
	</body>
</html>
	