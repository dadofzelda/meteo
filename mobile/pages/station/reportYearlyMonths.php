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
	#	Annual report monthly values
	#
	# 	A script that generates data for annual report monthly statistics.
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

	$bearingNames = array(lang("directionN",""),lang("directionNNE",""),lang("directionENE",""),lang("directionE",""),lang("directionESE",""),lang("directionSE",""),lang("directionSSE",""),lang("directionS",""),lang("directionSSW",""),lang("directionSW",""),lang("directionSW",""),lang("directionWSW",""),lang("directionW",""),lang("directionWNW",""),lang("directionNW",""),lang("directionNNW",""));
	
	// Get date
	$chosenYear = $_GET['y'];
	
	// validate year
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	
	if(file_exists("cache/annual".$chosenYear."_2.txt")){
		$data = json_decode(file_get_contents("cache/annual".$chosenYear."_2.txt"),true);
	}
	else{
	
		$span = "Year(DateTime) = ".$chosenYear;
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(Tmax), max(H), max(P), max(W), max(G), max(S), max(A), max(D), min(Tmin), min(H), min(P), min(W), min(G), min(S), min(A), min(D), MONTH(DateTime)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$m = $row['MONTH(DateTime)'];
			$data['monthlyAvgT'][$m] = $row['avg(T)'];
			$data['monthlyAvgH'][$m] = $row['avg(H)'];
			$data['monthlyAvgP'][$m] = $row['avg(P)'];
			$data['monthlyAvgW'][$m] = $row['avg(W)'];
			$data['monthlyAvgG'][$m] = $row['avg(G)'];
			$data['monthlyAvgS'][$m] = $row['avg(S)'];
			$data['monthlyAvgD'][$m] = $row['avg(D)'];
			$data['monthlyAvgA'][$m] = $row['avg(A)'];
			
			$data['monthlyMaxT'][$m] = $row['max(Tmax)'];
			$data['monthlyMaxH'][$m] = $row['max(H)'];
			$data['monthlyMaxP'][$m] = $row['max(P)'];
			$data['monthlyMaxW'][$m] = $row['max(W)'];
			$data['monthlyMaxG'][$m] = $row['max(G)'];
			$data['monthlyMaxS'][$m] = $row['max(S)'];
			$data['monthlyMaxD'][$m] = $row['max(D)'];
			$data['monthlyMaxA'][$m] = $row['max(A)'];
			
			$data['monthlyMinT'][$m] = $row['min(Tmin)'];
			$data['monthlyMinH'][$m] = $row['min(H)'];
			$data['monthlyMinP'][$m] = $row['min(P)'];
			$data['monthlyMinW'][$m] = $row['min(W)'];
			$data['monthlyMinG'][$m] = $row['min(G)'];
			$data['monthlyMinS'][$m] = $row['min(S)'];
			$data['monthlyMinD'][$m] = $row['min(D)'];
			$data['monthlyMinA'][$m] = $row['min(A)'];
		}
		$data['firstMonthInterval'] = $data['firstMonthInterval'] - 1;
		
		$data['monthlyR'] = array();
		
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$data['monthlyR'][$row['MONTH(DateTime)']] = $data['monthlyR'][$row['MONTH(DateTime)']] + ($row['max(R)']);
		}
		
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/annual".$chosenYear."_2.txt",json_encode($data));
		}
	}
	
	$result = mysqli_query($con,"
		SELECT avg(T), avg(H), avg(P), avg(D), avg(W), avg(G), avg(A), avg(S), MONTH(DateTime)
		FROM alldata
		GROUP BY MONTH(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		$m = $row['MONTH(DateTime)'];
		$stationAverageT[$m] = convertT($row['avg(T)']);
		$stationAverageA[$m] = convertT($row['avg(A)']);
		$stationAverageD[$m] = convertT($row['avg(D)']);
		$stationAverageH[$m] = ($row['avg(H)']);
		$stationAverageP[$m] = convertP($row['avg(P)']);
		$stationAverageW[$m] = convertW($row['avg(W)']);
		$stationAverageG[$m] = convertW($row['avg(G)']);
		$stationAverageS[$m] = ($row['avg(S)']);
	}
	
	foreach($data['monthlyAvgT'] as $m=>$value){
		$deviationsT[$m] = number_format(convertT($value) - $stationAverageT[$m],2,".","");
	}
	foreach($data['monthlyAvgA'] as $m=>$value){
		$deviationsA[$m] = number_format(convertT($value) - $stationAverageA[$m],2,".","");
	}
	foreach($data['monthlyAvgD'] as $m=>$value){
		$deviationsD[$m] = number_format(convertT($value) - $stationAverageD[$m],2,".","");
	}
	foreach($data['monthlyAvgH'] as $m=>$value){
		$deviationsH[$m] = number_format($value - $stationAverageH[$m],1,".","");
	}
	foreach($data['monthlyAvgP'] as $m=>$value){
		$deviationsP[$m] = number_format(convertP($value) - $stationAverageP[$m],2,".","");
	}
	foreach($data['monthlyAvgW'] as $m=>$value){
		$deviationsW[$m] = number_format(convertW($value) - $stationAverageW[$m],2,".","");
	}
	foreach($data['monthlyAvgG'] as $m=>$value){
		$deviationsG[$m] = number_format(convertW($value) - $stationAverageG[$m],2,".","");
	}
	foreach($data['monthlyAvgS'] as $m=>$value){
		$deviationsS[$m] = number_format($value - $stationAverageS[$m],1,".","");
	}
	
	function deviationFormat($n){
		if($n>0){
			return "+".$n;
		}
		else{
			return $n;
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
				padding-left: 5px;
			}
			.values{
				display:none;
			}
			.values2{
				display:none;
			}
			.opener2{
				cursor: pointer;
				opacity: 0.8;
			}
			.opener2:hover{
				opacity: 1;
			}
			.sort2{
				width: 15px;
				cursor: pointer;
				opacity: 0.8;
			}
			.sort2:hover{
				opacity:1;
			}
			.tableHeading2{
				width: 15%;
			}
		</style>
	</head>
	<body>	
		<div id="mainDiv2">
			<div class="exportDiv">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'csv')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'txt')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'excel')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'sql')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'doc')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'png')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable').val(),'json')">
			</div>
			<input id="shownTable" value="T2" type="hidden">
			<table id="mainTable2" class="table tableSpacing2Padding2">
				<tr>
					<th id="Topener2" style="text-align:center!important">
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Hopener2">
						<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Popener2">
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Wopener2">
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Gopener2">
						<img src="<?php echo $pageURL.$path?>icons/gust.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Dopener2">
						<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Aopener2">
						<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="imgHeader opener2" alt=''>
					</th>
					<th id="Ropener2">
						<img src="<?php echo $pageURL.$path?>icons/rain.png" class="imgHeader opener2" alt=''>
					</th>
					<?php
						if($solarSensor){
					?>
						<th id="Sopener2">
							<img src="<?php echo $pageURL.$path?>icons/sun.png" class="imgHeader opener2" alt=''>
						</th>
					<?php
						}
					?>
				</tr>
			</table>
			<div id="T2" class="values2">
				<table class="table table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgT'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyAvgT'][$i],array((min($data['monthlyAvgT'])-0.001),(max($data['monthlyAvgT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyAvgT'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMaxT'][$i],array((min($data['monthlyMaxT'])-0.001),(max($data['monthlyMaxT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMaxT'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMinT'][$i],array((min($data['monthlyMinT'])-0.001),(max($data['monthlyMinT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMinT'][$i]),1,".","")?>
								</td>
								<td>
									<?php echo number_format(convertT($data['monthlyMaxT'][$i])-convertT($data['monthlyMinT'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($stationAverageT[$i],array((min($stationAverageT)-0.001),(max($stationAverageT)+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format($stationAverageT[$i],2,".","")?>
								</td>
								<?php
									if($deviationsT[$i]<0){
										$colorTemporary = fill($deviationsT[$i],array((min($deviationsT)-0.1),0),array("#007FFF", "#999999"));
									}
									if($deviationsT[$i]>0){
										$colorTemporary = fill($deviationsT[$i],array(0,(max($deviationsT)+0.1)),array("#999999","#D90000"));
									}
									if($deviationsT[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsT[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="H2" class="values2">
				<table class="table table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgH'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyAvgH'][$i],array((min($data['monthlyAvgH'])-0.001),(max($data['monthlyAvgH'])+0.001)),array("#ffbf00", "#00D900"))?>">
									<?php echo number_format($data['monthlyAvgH'][$i],2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMaxH'][$i],array((min($data['monthlyMaxH'])-0.001),(max($data['monthlyMaxH'])+0.001)),array("#ffbf00", "#00D900"))?>">
									<?php echo number_format($data['monthlyMaxH'][$i],1,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMinH'][$i],array((min($data['monthlyMinH'])-0.001),(max($data['monthlyMinH'])+0.001)),array("#ffbf00", "#00D900"))?>">
									<?php echo number_format($data['monthlyMinH'][$i],1,".","")?>
								</td>
								<td>
									<?php echo number_format($data['monthlyMaxH'][$i]-$data['monthlyMinH'][$i],1,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($stationAverageH[$i],array((min($stationAverageH)-0.001),(max($stationAverageH)+0.001)),array("#ffbf00", "#00D900"))?>">
									<?php echo number_format(($stationAverageT[$i]),1,".","")?>
								</td>
								<?php
									if($deviationsH[$i]<0){
										$colorTemporary = fill($deviationsH[$i],array((min($deviationsH)-0.1),0),array("#ffbf00", "#999999"));
									}
									if($deviationsH[$i]>0){
										$colorTemporary = fill($deviationsH[$i],array(0,(max($deviationsH)+0.1)),array("#999999","#00D900"));
									}
									if($deviationsH[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsH[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="P2" class="values2">
				<table class="table table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgP'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyAvgP'][$i],array((min($data['monthlyAvgP'])-0.001),(max($data['monthlyAvgP'])+0.001)),array("#FFC926", "#c926ff"))?>">
									<?php echo number_format(convertP($data['monthlyAvgP'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMaxP'][$i],array((min($data['monthlyMaxP'])-0.001),(max($data['monthlyMaxP'])+0.001)),array("#FFC926", "#c926ff"))?>">
									<?php echo number_format(convertP($data['monthlyMaxP'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMinP'][$i],array((min($data['monthlyMinP'])-0.001),(max($data['monthlyMinP'])+0.001)),array("#FFC926", "#c926ff"))?>">
									<?php echo number_format(convertP($data['monthlyMinP'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertP($data['monthlyMaxP'][$i])-convertP($data['monthlyMinP'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($stationAverageP[$i],array((min($stationAverageP)-0.001),(max($stationAverageP)+0.001)),array("#FFC926", "#c926ff"))?>">
									<?php echo number_format($stationAverageP[$i],2,".","")?>
								</td>
								<?php
									if($deviationsP[$i]<0){
										$colorTemporary = fill($deviationsP[$i],array((min($deviationsP)-0.1),0),array("#FFC926", "#999999"));
									}
									if($deviationsP[$i]>0){
										$colorTemporary = fill($deviationsP[$i],array(0,(max($deviationsP)+0.1)),array("#999999","#c926ff"));
									}
									if($deviationsP[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsP[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="W2" class="values2">
				<table class="table  table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgW'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyAvgW'][$i],array((min($data['monthlyAvgW'])-0.001),(max($data['monthlyAvgW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyAvgW'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMaxW'][$i],array((min($data['monthlyMaxW'])-0.001),(max($data['monthlyMaxW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyMaxW'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMinW'][$i],array((min($data['monthlyMinW'])-0.001),(max($data['monthlyMinW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyMinW'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertW($data['monthlyMaxW'][$i])-convertW($data['monthlyMinW'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($stationAverageW[$i],array((min($stationAverageW)-0.001),(max($stationAverageW)+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format($stationAverageW[$i],2,".","")?>
								</td>
								<?php
									if($deviationsW[$i]<0){
										$colorTemporary = fill($deviationsW[$i],array((min($deviationsW)-0.1),0),array("#FFFFFF", "#999999"));
									}
									if($deviationsW[$i]>0){
										$colorTemporary = fill($deviationsW[$i],array(0,(max($deviationsW)+0.1)),array("#999999","#D900D9"));
									}
									if($deviationsW[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:black;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsW[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="G2" class="values2">
				<table class="table  table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgG'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyAvgG'][$i],array((min($data['monthlyAvgG'])-0.001),(max($data['monthlyAvgG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyAvgG'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMaxG'][$i],array((min($data['monthlyMaxG'])-0.001),(max($data['monthlyMaxG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyMaxG'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyMinG'][$i],array((min($data['monthlyMinG'])-0.001),(max($data['monthlyMinG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['monthlyMinG'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertW($data['monthlyMaxG'][$i])-convertW($data['monthlyMinG'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($stationAverageG[$i],array((min($stationAverageG)-0.001),(max($stationAverageG)+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format($stationAverageG[$i],2,".","")?>
								</td>
								<?php
									if($deviationsG[$i]<0){
										$colorTemporary = fill($deviationsG[$i],array((min($deviationsG)-0.1),0),array("#FFFFFF", "#999999"));
									}
									if($deviationsG[$i]>0){
										$colorTemporary = fill($deviationsG[$i],array(0,(max($deviationsG)+0.1)),array("#999999","#D900D9"));
									}
									if($deviationsG[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:black;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsG[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="A2" class="values2">
				<table class="table  table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgA'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyAvgA'][$i],array((min($data['monthlyAvgA'])-0.001),(max($data['monthlyAvgA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyAvgA'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMaxA'][$i],array((min($data['monthlyMaxA'])-0.001),(max($data['monthlyMaxA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMaxA'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMinA'][$i],array((min($data['monthlyMinA'])-0.001),(max($data['monthlyMinA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMinA'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertT($data['monthlyMaxA'][$i])-convertT($data['monthlyMinA'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($stationAverageA[$i],array((min($stationAverageA)-0.001),(max($stationAverageA)+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(($stationAverageA[$i]),2,".","")?>
								</td>
								<?php
									if($deviationsA[$i]<0){
										$colorTemporary = fill($deviationsA[$i],array((min($deviationsA)-0.1),0),array("#007FFF", "#999999"));
									}
									if($deviationsA[$i]>0){
										$colorTemporary = fill($deviationsA[$i],array(0,(max($deviationsA)+0.1)),array("#999999","#D90000"));
									}
									if($deviationsA[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsA[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<div id="D2" class="values2">
				<table class="table  table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading2">
								<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="tableHeading2">
								<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyAvgD'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyAvgD'][$i],array((min($data['monthlyAvgD'])-0.001),(max($data['monthlyAvgD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyAvgD'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMaxD'][$i],array((min($data['monthlyMaxD'])-0.001),(max($data['monthlyMaxD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMaxD'][$i]),2,".","")." "?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMinD'][$i],array((min($data['monthlyMinD'])-0.001),(max($data['monthlyMinD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['monthlyMinD'][$i]),2,".","")." "?>
								</td>
								<td>
									<?php echo number_format(convertT($data['monthlyMaxD'][$i])-convertT($data['monthlyMinD'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($stationAverageD[$i],array((min($stationAverageD)-0.001),(max($stationAverageD)+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(($stationAverageD[$i]),2,".","")?>
								</td>
								<?php
									if($deviationsD[$i]<0){
										$colorTemporary = fill($deviationsD[$i],array((min($deviationsD)-0.1),0),array("#007FFF", "#999999"));
									}
									if($deviationsD[$i]>0){
										$colorTemporary = fill($deviationsD[$i],array(0,(max($deviationsD)+0.1)),array("#999999","#D90000"));
									}
									if($deviationsD[$i]==0){
										$colorTemporary = "#999999";
									}
								?>
								<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
									<?php echo deviationFormat($deviationsD[$i]);?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
			<?php
				if($solarSensor){
			?>
					<div id="S2" class="values2">
						<table class="table table2">
							<thead>
								<tr>
									<th>	
									</th>
									<th class="tableHeading2">
										<?php echo lang('avgAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th class="tableHeading2">
										<?php echo lang('maximumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th class="tableHeading2">
										<?php echo lang('minimumAbbr','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th class="tableHeading2">
										<?php echo lang('range','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th class="summaryTableHeading">
										<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
									<th class="summaryTableHeading">
										<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($data['monthlyAvgS'] as $i=>$values){
								?>		
									<tr>
										<td>
											<?php 
												echo lang("month".$i,"c");
											?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyAvgS'][$i],array((min($data['monthlyAvgS'])-0.001),(max($data['monthlyAvgS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['monthlyAvgS'][$i],2,".","")?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMaxS'][$i],array((min($data['monthlyMaxS'])-0.001),(max($data['monthlyMaxS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['monthlyMaxS'][$i],2,".","")?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['monthlyMinS'][$i],array((min($data['monthlyMinS'])-0.001),(max($data['monthlyMinS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['monthlyMinS'][$i],2,".","")?>
										</td>
										<td>
											<?php echo number_format($data['monthlyMaxS'][$i]-$data['monthlyMinS'][$i],1,".","")?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($stationAverageS[$i],array((min($stationAverageS)-0.001),(max($stationAverageS)+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format(($stationAverageS[$i]),1,".","")?>
										</td>
										<?php
											if($deviationsS[$i]<0){
												$colorTemporary = fill($deviationsS[$i],array((min($deviationsS)-0.1),0),array("#000000", "#999999"));
											}
											if($deviationsS[$i]>0){
												$colorTemporary = fill($deviationsS[$i],array(0,(max($deviationsS)+0.1)),array("#999999","#D9D900"));
											}
											if($deviationsS[$i]==0){
												$colorTemporary = "#999999";
											}
										?>
										<td style="font-weight:bold;color:white;background:<?php echo $colorTemporary?>">
											<?php echo deviationFormat($deviationsS[$i]);?>
										</td>
									</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
			<?php
				}
			?>
			<div id="R2" class="values2">
				<table class="table table2">
					<thead>
						<tr>
							<th>	
							</th>
							<th>
								<?php echo lang('total','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['monthlyR'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php 
										echo lang("month".$i,"c");
									?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['monthlyR'][($i+1)],array((min($data['monthlyR'])-0.001),(max($data['monthlyR'])+0.001)),array("#FFFFFF", "#00BFFF"))?>">
									<?php echo number_format(convertR($data['monthlyR'][($i+1)]),2,".","")?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<script>
			$(document).ready(function() {
				$( "#T2" ).show();
				$("#Topener2").click(function() {
					$( ".values2" ).hide();
					$( "#T2" ).show();
					$("#shownTable").val("T2");
				});
				$("#Hopener2").click(function() {
					$( ".values2" ).hide();
					$( "#H2" ).show();
					$("#shownTable").val("H2");					
				});
				$("#Popener2").click(function() {
					$( ".values2" ).hide();
					$( "#P2" ).show();
					$("#shownTable").val("P2");
				});
				$("#Wopener2").click(function() {
					$( ".values2" ).hide();
					$( "#W2" ).show();
					$("#shownTable").val("W2");
				});
				$("#Gopener2").click(function() {
					$( ".values2" ).hide();
					$( "#G2" ).show();
					$("#shownTable").val("G2");
				});
				$("#Ropener2").click(function() {
					$( ".values2" ).hide();
					$( "#R2" ).show();
					$("#shownTable").val("R2");
				});
				$("#Sopener2").click(function() {
					$( ".values2" ).hide();
					$( "#S2" ).show();
					$("#shownTable").val("S2");
				});
				$("#Aopener2").click(function() {
					$( ".values2" ).hide();
					$( "#A2" ).show();
					$("#shownTable").val("A2");
				});
				$("#Dopener2").click(function() {
					$( ".values2" ).hide();
					$( "#D2" ).show();
					$("#shownTable").val("D2");
				});
				$('.table2').tablesorter();
			});	
		</script>
	</body>
</html>