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
	#	Annual report extremes
	#
	# 	A script that generates data for annual report extremes.
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
	include($baseURL."scripts/stats.php");
	
	createCacheDir();

	$bearingNames = array(lang("directionN","u"),lang("directionNNE","u"),lang("directionENE","u"),lang("directionE","u"),lang("directionESE","u"),lang("directionSE","u"),lang("directionSSE","u"),lang("directionS","u"),lang("directionSSW","u"),lang("directionSW","u"),lang("directionSW","u"),lang("directionWSW","u"),lang("directionW","u"),lang("directionWNW","u"),lang("directionNW","u"),lang("directionNNW","u"));
	
	// Get date
	$chosenYear = $_GET['y'];
	
	// validate year
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	if(file_exists("cache/".$chosenYear."_1.txt")){
		$data = json_decode(file_get_contents("cache/".$chosenYear."_1.txt"),true);
	}
	else{
		$span = "Year(DateTime) = ".$chosenYear;
		
		$data['monthlyAveragesT'] = array();
		$data['monthlyAveragesH'] = array();
		$data['monthlyAveragesP'] = array();
		$data['monthlyAveragesW'] = array();
		$data['monthlyAveragesG'] = array();
		$data['monthlyAveragesS'] = array();
		$data['monthlyAveragesA'] = array();
		$data['monthlyAveragesD'] = array();
		$data['monthlyAveragesDates'] = array();
		
		$result = mysqli_query($con,"
			SELECT DateTime, avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['monthlyAveragesT'],($row['avg(T)']));
			array_push($data['monthlyAveragesH'],$row['avg(H)']);
			array_push($data['monthlyAveragesP'],($row['avg(P)']));
			array_push($data['monthlyAveragesW'],($row['avg(W)']));
			array_push($data['monthlyAveragesG'],($row['avg(G)']));
			array_push($data['monthlyAveragesS'],$row['avg(S)']);
			array_push($data['monthlyAveragesD'],($row['avg(D)']));
			array_push($data['monthlyAveragesA'],($row['avg(A)']));
			array_push($data['monthlyAveragesDates'],strtotime($row['DateTime']));
		}
		
		$data['dailyAveragesT'] = array();
		$data['dailyAveragesH'] = array();
		$data['dailyAveragesP'] = array();
		$data['dailyAveragesW'] = array();
		$data['dailyAveragesG'] = array();
		$data['dailyAveragesS'] = array();
		$data['dailyAveragesA'] = array();
		$data['dailyAveragesD'] = array();
		$data['dailyR'] = array();
		$data['monthlyR'] = array();
		$data['dailyAveragesDates'] = array();
		
		$result = mysqli_query($con,"
			SELECT DateTime, avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(R), MONTH(DateTime)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($data['dailyAveragesT'],($row['avg(T)']));
			array_push($data['dailyAveragesH'],$row['avg(H)']);
			array_push($data['dailyAveragesP'],($row['avg(P)']));
			array_push($data['dailyAveragesW'],($row['avg(W)']));
			array_push($data['dailyAveragesG'],($row['avg(G)']));
			array_push($data['dailyAveragesS'],$row['avg(S)']);
			array_push($data['dailyAveragesD'],($row['avg(D)']));
			array_push($data['dailyAveragesA'],($row['avg(A)']));
			array_push($data['dailyR'],($row['max(R)']));
			$data['monthlyR'][$row['MONTH(DateTime)']] = $data['monthlyR'][$row['MONTH(DateTime)']] + ($row['max(R)']);
			array_push($data['dailyAveragesDates'],strtotime($row['DateTime']));
		}
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/".$chosenYear."_1.txt",json_encode($data));
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang('annual report','c')?></title>
		<style>
			.parameters{
				width: 24px;
			}
			.imgHeader{
				width: 40px;
				padding-right: 5px;
				padding-left: 0px;
			}
			.descriptions{
				width:auto;
			}
			.values{
				display:none;
			}
			.opener{
				cursor: pointer;
				opacity: 0.8;
			}
			.opener:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>	
		<div id="mainDiv">
			<table id="mainTable" class="table tableSpacing2Padding2">
				<tr>
					<th id="Topener" style="text-align:center!important">
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Hopener">
						<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Popener">
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Wopener">
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Gopener">
						<img src="<?php echo $pageURL.$path?>icons/gust.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Dopener">
						<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Aopener">
						<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="imgHeader opener" alt=''>
					</th>
					<th id="Ropener">
						<img src="<?php echo $pageURL.$path?>icons/rain.png" class="imgHeader opener" alt=''>
					</th>
					<?php
						if($solarSensor){
					?>
						<th id="Sopener">
							<img src="<?php echo $pageURL.$path?>icons/sun.png" class="imgHeader opener" alt=''>
						</th>
					<?php
						}
					?>
				</tr>
			</table>
			<br>
			<div id="T" class="values">
				<table class="table">
					<tr>
						<th colspan="3">			
							<?php echo lang("temperature",'c')?>
						</th>
					</tr>
					<tr>
						<td style="width:50%">
							<?php echo lang("warmest month","c")?>
						</td>
						<td style="width:25%">
							<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesT']), $data['monthlyAveragesT'])])),"c")?>
						</td>
						<td style="width:25%">
							<?php echo number_format(convertT(max($data['monthlyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits)?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("coldest month","c")?>
						</td>
						<td>
							<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesT']), $data['monthlyAveragesT'])])),"c")?>
						</td>
						<td>
							<?php echo number_format(convertT(min($data['monthlyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits)?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("warmest day","c")?>
						</td>
						<td>
							<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesT']), $data['dailyAveragesT'])])?>
						</td>
						<td>
							<?php echo number_format(convertT(max($data['dailyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits)?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo lang("coldest day","c")?>
						</td>
						<td>
							<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesT']), $data['dailyAveragesT'])])?>
						</td>
						<td>
							<?php echo number_format(convertT(min($data['dailyAveragesT'])),2,".","")." ".unitFormatter($displayTempUnits)?>
						</td>
					</tr>
				</table>
			</div>
			<div id="H" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("humidity",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average humidity","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesH']), $data['monthlyAveragesH'])])),'c')?>
							</td>
							<td style="width:25%">
								<?php echo number_format(max($data['monthlyAveragesH']),2,".","")." %"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average humidity","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesH']), $monthlyAveragesH)])),"c")?>
							</td>
							<td>
								<?php echo number_format(min($data['monthlyAveragesH']),2,".","")." %"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average humidity","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesH']), $data['dailyAveragesH'])])?>
							</td>
							<td>
								<?php echo number_format(max($data['dailyAveragesH']),2,".","")." %"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average humidity","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesH']), $data['dailyAveragesH'])])?>
							</td>
							<td>
								<?php echo number_format(min($data['dailyAveragesH']),2,".","")." %"?>
							</td>
						</tr>
					</table>
				</div>
			<div id="P" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("pressure",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average pressure","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesP']), $data['monthlyAveragesP'])])),'c')?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertP(max($data['monthlyAveragesP'])),2,".","")." ".unitFormatter($displayPressUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average pressure","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesP']), $data['monthlyAveragesP'])])),"c")?>
							</td>
							<td>
								<?php echo number_format(convertP(min($data['monthlyAveragesP'])),2,".","")." ".unitFormatter($displayPressUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average pressure","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesP']), $data['dailyAveragesP'])])?>
							</td>
							<td>
								<?php echo number_format(convertP(max($data['dailyAveragesP'])),2,".","")." ".unitFormatter($displayPressUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average pressure","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesP']), $data['dailyAveragesP'])])?>
							</td>
							<td>
								<?php echo number_format(convertP(min($data['dailyAveragesP'])),2,".","")." ".unitFormatter($displayPressUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<div id="W" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("wind speed",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average wind speed","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesW']), $data['monthlyAveragesW'])])),'c')?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertW(max($data['monthlyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average wind speed","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesW']), $data['monthlyAveragesW'])])),"c")?>
							</td>
							<td>
								<?php echo number_format(convertW(min($data['monthlyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average wind speed","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesW']), $data['dailyAveragesW'])])?>
							</td>
							<td>
								<?php echo number_format(convertW(max($data['dailyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average wind speed","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesW']), $data['dailyAveragesW'])])?>
							</td>
							<td>
								<?php echo number_format(convertW(min($data['dailyAveragesW'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<div id="G" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("wind gust",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average gust","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesG']), $data['monthlyAveragesG'])])),'c')?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertW(max($data['monthlyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average gust","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesG']), $monthlyAveragesG)])),"c")?>
							</td>
							<td>
								<?php echo number_format(convertW(min($data['monthlyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average gust","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesG']), $data['dailyAveragesG'])])?>
							</td>
							<td>
								<?php echo number_format(convertW(max($data['dailyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average gust","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesG']), $data['dailyAveragesG'])])?>
							</td>
							<td>
								<?php echo number_format(convertW(min($data['dailyAveragesG'])),2,".","")." ".unitFormatter($displayWindUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<div id="D" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("dew point",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average dew point","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesD']), $data['monthlyAveragesD'])])),"c")?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertT(max($data['monthlyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average dew point","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesD']), $data['monthlyAveragesD'])])),"c")?>
							</td>
							<td>
								<?php echo number_format(convertT(min($data['monthlyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average dew point","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesD']), $data['dailyAveragesD'])])?>
							</td>
							<td>
								<?php echo number_format(convertT(max($data['dailyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average dew point","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesD']), $data['dailyAveragesD'])])?>
							</td>
							<td>
								<?php echo number_format(convertT(min($data['dailyAveragesD'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<div id="A" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("apparent temperature",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average apparent temperature","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesA']), $data['monthlyAveragesA'])])),"c")?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertT(max($data['monthlyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average apparent temperature","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesA']), $data['monthlyAveragesA'])])),"c")?>
							</td>
							<td>
								<?php echo number_format(convertT(min($data['monthlyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average apparent temperature","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesA']), $data['dailyAveragesA'])])?>
							</td>
							<td>
								<?php echo number_format(convertT(max($data['dailyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average apparent temperature","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesA']), $data['dailyAveragesA'])])?>
							</td>
							<td>
								<?php echo number_format(convertT(min($data['dailyAveragesA'])),2,".","")." ".unitFormatter($displayTempUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<div id="R" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("precipitation",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest precipitation","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang("month".array_search(max($data['monthlyR']), $data['monthlyR']),"c")?>
							</td>
							<td style="width:25%">
								<?php echo number_format(convertR(max($data['monthlyR'])),2,".","")." ".unitFormatter($displayRainUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest precipitation","c")?>
							</td>
							<td>
								<?php echo lang("month".array_search(min($data['monthlyR']), $data['monthlyR']),"c")?>
							</td>
							<td>
								<?php echo number_format(convertR(min($data['monthlyR'])),2,".","")." ".unitFormatter($displayRainUnits)?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest precipitation","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyR']), $data['dailyR'])])?>
							</td>
							<td>
								<?php echo number_format(convertR(max($data['dailyR'])),2,".","")." ".unitFormatter($displayRainUnits)?>
							</td>
						</tr>
					</table>
				</div>
			<?php 
				if($solarSensor){
			?>
				<div id="S" class="values">
					<table class="table">
						<tr>
							<th colspan="3">
								<?php echo lang("solar radiation",'c')?>
							</th>
						</tr>
						<tr>
							<td style="width:50%">
								<?php echo lang("month with highest average solar radiation","c")?>
							</td>
							<td style="width:25%">
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(max($data['monthlyAveragesS']), $data['monthlyAveragesS'])])),"c")?>
							</td>
							<td style="width:25%">
								<?php echo number_format(max($data['monthlyAveragesS']),2,".","")." W/m2"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("month with lowest average solar radiation","c")?>
							</td>
							<td>
								<?php echo lang(strtolower(date("F",$data['monthlyAveragesDates'][array_search(min($data['monthlyAveragesS']), $data['monthlyAveragesS'])])),"c")?>
							</td>
							<td>
								<?php echo number_format(min($data['monthlyAveragesS']),2,".","")." W/m2"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with highest average solar radiation","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(max($data['dailyAveragesS']), $data['dailyAveragesS'])])?>
							</td>
							<td>
								<?php echo number_format(max($data['dailyAveragesS']),2,".","")." W/m2"?>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo lang("day with lowest average solar radiation","c")?>
							</td>
							<td>
								<?php echo date($dateFormat,$data['dailyAveragesDates'][array_search(min($data['dailyAveragesS']), $data['dailyAveragesS'])])?>
							</td>
							<td>
								<?php echo number_format(min($data['dailyAveragesS']),2,".","")." W/m2"?>
							</td>
						</tr>
					</table>
				</div>
			<?php
				}
			?>
		</div>
		<script>
			$(document).ready(function() {
				$( "#T" ).show();
				$("#Topener").click(function() {
					$( ".values" ).hide();
					$( "#T" ).show();
				});
				$("#Hopener").click(function() {
					$( ".values" ).hide();
					$( "#H" ).show();
				});
				$("#Popener").click(function() {
					$( ".values" ).hide();
					$( "#P" ).show();
				});
				$("#Wopener").click(function() {
					$( ".values" ).hide();
					$( "#W" ).show();
				});
				$("#Gopener").click(function() {
					$( ".values" ).hide();
					$( "#G" ).show();
				});
				$("#Ropener").click(function() {
					$( ".values" ).hide();
					$( "#R" ).show();
				});
				$("#Sopener").click(function() {
					$( ".values" ).hide();
					$( "#S" ).show();
				});
				$("#Aopener").click(function() {
					$( ".values" ).hide();
					$( "#A" ).show();
				});
				$("#Dopener").click(function() {
					$( ".values" ).hide();
					$( "#D" ).show();
				});
			});	
		</script>
	</body>
</html>