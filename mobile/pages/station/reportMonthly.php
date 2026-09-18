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
	#	Monthly report
	#
	# 	A script which generates the monthly report for user specified month.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");
	
	//error_reporting(E_ALL);

	// Get date
	$chosenDate = urldecode($_GET['date']);
	$splitDate = explode("/",$chosenDate);
	
	$chosenMonth = $splitDate[1];
	$chosenYear = $splitDate[0];
	
	// validate date and prevent SQL injection
	if($chosenMonth<1 || $chosenMonth>12){
		echo "Invalid date";
		die();
	}
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($chosenMonth) || !is_numeric($chosenYear)){
		echo "Invalid date";
		die();
	}
	
	$day = strtotime($chosenYear."-".$chosenMonth."-15");

	// previous month 
	if($chosenMonth!=1){
		$previousMonth = $chosenMonth - 1;
		$previousYear = $chosenYear;
	}
	else{
		$previousMonth = 12;
		$previousYear = $chosenYear-1;
	}

	// check if previous and next months exist 
	// previous 
	$previousMonthOK = false;
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE MONTH(DateTime)=".$previousMonth." AND YEAR(DateTime)=".$previousYear."
		ORDER BY DateTime
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$previousMonthOK = true;
	}

	// next month 
	if($chosenMonth!=12){
		$nextMonth = $chosenMonth + 1;
		$nextYear = $chosenYear;
	}
	else{
		$nextMonth = 1;
		$nextYear = $chosenYear+1;
	}

	// check if previous and next months exist 
	// previous 
	$nextMonthOK = false;
	$result = mysqli_query($con,"
		SELECT DateTime
		FROM alldata 
		WHERE MONTH(DateTime)=".$nextMonth." AND YEAR(DateTime)=".$nextYear."
		ORDER BY DateTime
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$nextMonthOK = true;
	}

	if($previousMonthOK){
		$previousMonthURL = "reportMonthly.php?date=".$previousYear."%2F".$previousMonth;
	}
	if($nextMonthOK){
		$nextMonthURL = "reportMonthly.php?date=".$nextYear."%2F".$nextMonth;
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("monthly report",'w')?></title>
		<?php metaHeader()?>

		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<style>
			.showtimes{
				width: 13px;
				opacity: 0.8;
				cursor: pointer;
				padding-left: 2px;
			}
			.sort{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.clock{
				width: 20px;
				cursor: pointer;
				opacity: 0.8;
			}
			.showtimes:hover, .clock:hover, .sort:hover{
				opacity: 1;
			}
			<?php
				if($stationLat<0){ // invert Moon image if Southern hemisphere
			?>
				#moonImg{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
			<?php
				}
			?>	
			.inner-resizer {
				padding: 10px;
			}
			.resizer {   
				margin: 0 auto;
				width: 98%;
				
			}
			.table th{
				text-align: center;
			}
			.tableFirstIcon{
				width: 25px;
				padding-left: 5px;
				padding-right: 5px;
			}
			.unitCell{
				text-align: left;
				font-variant: small-caps;
			}
			#summaryTable th{
				width: 8%;
			}
			.times{
				display:none;
			}
			.varSelectorIcon{
				width:40px;
				cursor: pointer;
				opacity: 0.8;
			}
			.varSelectorIcon:hover{
				opacity: 1;
			}
			#mtImage {
				width: 80px;
				-webkit-animation: rotation 2s infinite linear;
			}

			@-webkit-keyframes rotation {
				from {-webkit-transform: rotate(0deg);}
				to   {-webkit-transform: rotate(359deg);}
			}
			#pdfLink{
				font-size:3em;
				cursor: pointer;
				opacity: 0.8;
				padding-bottom:10px;
				padding-top: 10px;
			}
			#pdfLink:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
			<table style="width:100%;table-layout:fixed">
				<tr>
					<td style="width:5%;text-align:right">
					</td>
					<td style='text-align:right;padding-top:10px'>
						<?php 
							if($previousMonthOK){
						?>
								<a href="<?php echo $previousMonthURL?>"><input type="button" class="button2" style="font-weight:bold;font-variant:small-caps" value="<<< <?php echo lang("month".$previousMonth)." ".$previousYear?>"></a>
						<?php
							}
						?>
					</td>
					<td style='text-align:center;vertical-align:top'>
						<h2><?php echo lang("month".date("n",$day),"c")." ".date("Y",$day)?></h2>
					</td>
					<td style='text-align:left'>
						<?php 
							if($nextMonthOK){
						?>
								<a href="<?php echo $nextMonthURL?>"><input type="button" class="button2" style="font-weight:bold;font-variant:small-caps" value="<?php echo lang("month".$nextMonth)." ".$nextYear?> >>>"></a>
						<?php
							}
						?>
					</td>
					<td style="width:5%;text-align:right">
						<a href="<?php echo $pageURL.$path?>pages/station/reportMonthlyPDF.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>
					</td>
				</tr>
			</table>
			<br>
			<div style="width:96%;padding:2%;margin:0 auto;background:#<?php echo $color_schemes[$design2]['700']?>;border-radius:10px">
				<table style="width:100%">
					<tr>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/table.png" class="varSelectorIcon tooltip" id="varSelectorSummary" title="<?php echo lang('summary','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/graph.png" class="varSelectorIcon tooltip" id="varSelectorGraph" title="<?php echo lang('summary graph','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="varSelectorIcon tooltip" id="varSelectorT" title="<?php echo lang('temperature','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="varSelectorIcon tooltip" id="varSelectorA" title="<?php echo lang('apparent temperature','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="varSelectorIcon tooltip" id="varSelectorD" title="<?php echo lang('dewpoint','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('humidity','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="varSelectorIcon tooltip" id="varSelectorP" title="<?php echo lang('pressure','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="varSelectorIcon tooltip" id="varSelectorW" title="<?php echo lang('wind speed','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/gust.png" class="varSelectorIcon tooltip" id="varSelectorG" title="<?php echo lang('wind gust','c')?>">
						</td>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="varSelectorIcon tooltip" id="varSelectorR" title="<?php echo lang('precipitation','c')?>">
						</td>
						<?php 
							if($solarSensor){
						?>
								<td>
									<img src="<?php echo $pageURL.$path?>icons/sun.png" class="varSelectorIcon tooltip" id="varSelectorH" title="<?php echo lang('solar radiation','c')?>">
								</td>
						<?php 
							}
						?>
						<td>
							<img src="<?php echo $pageURL.$path?>icons/daynight.png" class="varSelectorIcon tooltip" id="varSelectorAstro" title="<?php echo lang('astronomy','c')?>">
						</td>
					</tr>
				</table>
			</div>
			<br>
			<div id="varDiv" style="width:98%;margin:0 auto;text-align:center"></div>
			</div>
			<br><br>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {
				$(".varSelectorIcon").click(function(){
					$("#varDiv").html("<br><br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' id='mtImage'></img>");
					id = $(this).attr("id");
					id = id.replace("varSelector","");
					if(id!="Summary" && id!="Graph" && id!="Astro"){
						$("#varDiv").load("reportMonthlyLoad.php?var="+id+"&y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>");
					}
					if(id=="Summary"){
						$("#varDiv").load("reportMonthlySummary.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>");
					}
					if(id=="Graph"){
						$("#varDiv").load("reportMonthlyGraph.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>");
					}
					if(id=="Astro"){
						$("#varDiv").load("reportMonthlyAstro.php?y=<?php echo $chosenYear?>&m=<?php echo $chosenMonth?>");
					}
				})
				$(".showtimes").click(function(){
					$(this).next(".times").slideToggle(800);
				});
			});
		</script>
		<?php include("../../../css/highcharts.php")?>
		<?php include("../../footer.php")?>
	</body>
</html>