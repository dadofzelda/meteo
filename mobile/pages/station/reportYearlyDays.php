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
	#	Annual report daily values
	#
	# 	A script that generates data for annual report daily statistics.
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
	
	if(file_exists("cache/annual".$chosenYear."_3.txt")){
		$data = json_decode(file_get_contents("cache/annual".$chosenYear."_3.txt"),true);
	}
	else{
	
		$span = "Year(DateTime) = ".$chosenYear;
		
		$result = mysqli_query($con,"
			SELECT avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(Tmax), max(H), max(P), max(W), max(G), max(S), max(A), max(D), min(Tmin), min(H), min(P), min(W), min(G), min(S), min(A), min(D), max(R), DAY(DateTime), MONTH(DateTime)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row['DAY(DateTime)'];
			$m = $row['MONTH(DateTime)'];
			$dummyDate = strtotime($chosenYear."-".$m."-".$d);
			
			$data['dailyAvgT'][$dummyDate] = $row['avg(T)'];
			$data['dailyAvgH'][$dummyDate] = $row['avg(H)'];
			$data['dailyAvgP'][$dummyDate] = $row['avg(P)'];
			$data['dailyAvgW'][$dummyDate] = $row['avg(W)'];
			$data['dailyAvgG'][$dummyDate] = $row['avg(G)'];
			$data['dailyAvgS'][$dummyDate] = $row['avg(S)'];
			$data['dailyAvgD'][$dummyDate] = $row['avg(D)'];
			$data['dailyAvgA'][$dummyDate] = $row['avg(A)'];
			
			$data['dailyMaxT'][$dummyDate] = $row['max(Tmax)'];
			$data['dailyMaxH'][$dummyDate] = $row['max(H)'];
			$data['dailyMaxP'][$dummyDate] = $row['max(P)'];
			$data['dailyMaxW'][$dummyDate] = $row['max(W)'];
			$data['dailyMaxG'][$dummyDate] = $row['max(G)'];
			$data['dailyMaxS'][$dummyDate] = $row['max(S)'];
			$data['dailyMaxD'][$dummyDate] = $row['max(D)'];
			$data['dailyMaxA'][$dummyDate] = $row['max(A)'];
			
			$data['dailyMinT'][$dummyDate] = $row['min(Tmin)'];
			$data['dailyMinH'][$dummyDate] = $row['min(H)'];
			$data['dailyMinP'][$dummyDate] = $row['min(P)'];
			$data['dailyMinW'][$dummyDate] = $row['min(W)'];
			$data['dailyMinG'][$dummyDate] = $row['min(G)'];
			$data['dailyMinS'][$dummyDate] = $row['min(S)'];
			$data['dailyMinD'][$dummyDate] = $row['min(D)'];
			$data['dailyMinA'][$dummyDate] = $row['min(A)'];

			$data['dailyR'][$dummyDate] = $row['max(R)'];		
			
		}
		
		// check if data displayed is current year, if yes, dont save cache, otherwise do so
		if($chosenYear!=date("Y")){
			file_put_contents("cache/annual".$chosenYear."_3.txt",json_encode($data));
		}
	}
	
	$result = mysqli_query($con,"
		SELECT DAY(DateTime), MONTH(DateTime), avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D)
		FROM alldata 
		GROUP BY MONTH(DateTime), DAY(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		$d = $row['DAY(DateTime)'];
		$m = $row['MONTH(DateTime)'];
		$dummyDate = strtotime($chosenYear."-".$m."-".$d);
		$stationAverageT[$dummyDate] = convertT($row['avg(T)']);
	}
	

	foreach($data['dailyAvgT'] as $m=>$value){
		$deviationsT[$m] = number_format(convertT($value) - $stationAverageT[$m],2,".","");
	}
	foreach($data['dailyAvgA'] as $m=>$value){
		$deviationsA[$m] = number_format(convertT($value) - $stationAverageA[$m],2,".","");
	}
	foreach($data['dailyAvgD'] as $m=>$value){
		$deviationsD[$m] = number_format(convertT($value) - $stationAverageD[$m],2,".","");
	}
	foreach($data['dailyAvgH'] as $m=>$value){
		$deviationsH[$m] = number_format($value - $stationAverageH[$m],1,".","");
	}
	foreach($data['dailyAvgP'] as $m=>$value){
		$deviationsP[$m] = number_format(convertP($value) - $stationAverageP[$m],2,".","");
	}
	foreach($data['dailyAvgW'] as $m=>$value){
		$deviationsW[$m] = number_format(convertW($value) - $stationAverageW[$m],2,".","");
	}
	foreach($data['dailyAvgG'] as $m=>$value){
		$deviationsG[$m] = number_format(convertW($value) - $stationAverageG[$m],2,".","");
	}
	foreach($data['dailyAvgS'] as $m=>$value){
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
	</head>
	<style>
		.values3{
			display:none;
		}
		.opener3{
			opacity: 0.8;
			cursor: pointer;
		}
		.opener3:hover{
			opacity: 1;
		}
		.sort3{
			width: 15px;
			cursor: pointer;
			opacity: 0.8;
		}
		.sort3:hover{
			opacity:1;
		}
	</style>
	<body>	
		<div id="mainDiv3">
			<div class="exportDiv">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'csv')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'txt')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'excel')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'sql')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'doc')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'png')">
				<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport($('#shownTable2').val(),'json')">
			</div>
			<input id="shownTable3" value="T3" type="hidden">
			<table id="mainTable3" class="table tableSpacing2Padding2">
				<tr>
					<th id="Topener3" style="text-align:center!important">
						<img src="<?php echo $pageURL.$path?>icons/temp.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Hopener3">
						<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Popener3">
						<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Wopener3">
						<img src="<?php echo $pageURL.$path?>icons/wind.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Gopener3">
						<img src="<?php echo $pageURL.$path?>icons/gust.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Dopener3">
						<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Aopener3">
						<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="imgHeader opener3" alt=''>
					</th>
					<th id="Ropener3">
						<img src="<?php echo $pageURL.$path?>icons/rain.png" class="imgHeader opener3" alt=''>
					</th>
					<?php
						if($solarSensor){
					?>
						<th id="Sopener3">
							<img src="<?php echo $pageURL.$path?>icons/sun.png" class="imgHeader opener3" alt=''>
						</th>
					<?php
						}
					?>
				</tr>
			</table>
			<div id="T3" class="values3">
				<table class="table table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgT'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyAvgT'][$i],array((min($data['dailyAvgT'])-0.001),(max($data['dailyAvgT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyAvgT'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMaxT'][$i],array((min($data['dailyMaxT'])-0.001),(max($data['dailyMaxT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMaxT'][$i]),1,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMinT'][$i],array((min($data['dailyMinT'])-0.001),(max($data['dailyMinT'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMinT'][$i]),1,".","")?>
								</td>
								<td>
									<?php echo number_format(convertT($data['dailyMaxT'][$i])-convertT($data['dailyMinT'][$i]),1,".","")?>
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
			<div id="H3" class="values3">
				<table class="table table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgH'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyAvgH'][$i],array((min($data['dailyAvgH'])-0.001),(max($data['dailyAvgH'])+0.001)),array("#FFFFFF", "#00D900"))?>">
									<?php echo number_format($data['dailyAvgH'][$i],2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMaxH'][$i],array((min($data['dailyMaxH'])-0.001),(max($data['dailyMaxH'])+0.001)),array("#FFFFFF", "#00D900"))?>">
									<?php echo number_format($data['dailyMaxH'][$i],1,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMinH'][$i],array((min($data['dailyMinH'])-0.001),(max($data['dailyMinH'])+0.001)),array("#FFFFFF", "#00D900"))?>">
									<?php echo number_format($data['dailyMinH'][$i],1,".","")?>
								</td>
								<td>
									<?php echo number_format($data['dailyMaxH'][$i]-$data['dailyMinH'][$i],1,".","")?>
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
			<div id="P3" class="values3">
				<table class="table table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgP'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyAvgP'][$i],array((min($data['dailyAvgP'])-0.001),(max($data['dailyAvgP'])+0.001)),array("#FFC926", "#FF0000"))?>">
									<?php echo number_format(convertP($data['dailyAvgP'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMaxP'][$i],array((min($data['dailyMaxP'])-0.001),(max($data['dailyMaxP'])+0.001)),array("#FFC926", "#FF0000"))?>">
									<?php echo number_format(convertP($data['dailyMaxP'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMinP'][$i],array((min($data['dailyMinP'])-0.001),(max($data['dailyMinP'])+0.001)),array("#FFC926", "#FF0000"))?>">
									<?php echo number_format(convertP($data['dailyMinP'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertP($data['dailyMaxP'][$i])-convertP($data['dailyMinP'][$i]),1,".","")?>
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
			<div id="W3" class="values3">
				<table class="table  table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgW'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyAvgW'][$i],array((min($data['dailyAvgW'])-0.001),(max($data['dailyAvgW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyAvgW'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMaxW'][$i],array((min($data['dailyMaxW'])-0.001),(max($data['dailyMaxW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyMaxW'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMinW'][$i],array((min($data['dailyMinW'])-0.001),(max($data['dailyMinW'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyMinW'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertW($data['dailyMaxW'][$i])-convertW($data['dailyMinW'][$i]),1,".","")?>
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
			<div id="G3" class="values3">
				<table class="table  table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgG'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyAvgG'][$i],array((min($data['dailyAvgG'])-0.001),(max($data['dailyAvgG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyAvgG'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMaxG'][$i],array((min($data['dailyMaxG'])-0.001),(max($data['dailyMaxG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyMaxG'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyMinG'][$i],array((min($data['dailyMinG'])-0.001),(max($data['dailyMinG'])+0.001)),array("#FFFFFF", "#D900D9"))?>">
									<?php echo number_format(convertW($data['dailyMinG'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertW($data['dailyMaxG'][$i])-convertW($data['dailyMinG'][$i]),1,".","")?>
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
			<div id="A3" class="values3">
				<table class="table  table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgA'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyAvgA'][$i],array((min($data['dailyAvgA'])-0.001),(max($data['dailyAvgA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyAvgA'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMaxA'][$i],array((min($data['dailyMaxA'])-0.001),(max($data['dailyMaxA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMaxA'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMinA'][$i],array((min($data['dailyMinA'])-0.001),(max($data['dailyMinA'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMinA'][$i]),2,".","")?>
								</td>
								<td>
									<?php echo number_format(convertT($data['dailyMaxA'][$i])-convertT($data['dailyMinA'][$i]),1,".","")?>
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
			<div id="D3" class="values3">
				<table class="table table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th class="tableHeading3">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="tableHeading3">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
							<th class="summaryTableHeading">
								<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyAvgD'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyAvgD'][$i],array((min($data['dailyAvgD'])-0.001),(max($data['dailyAvgD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyAvgD'][$i]),2,".","")?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMaxD'][$i],array((min($data['dailyMaxD'])-0.001),(max($data['dailyMaxD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMaxD'][$i]),2,".","")." "?>
								</td>
								<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMinD'][$i],array((min($data['dailyMinD'])-0.001),(max($data['dailyMinD'])+0.001)),array("#007FFF", "#D90000"))?>">
									<?php echo number_format(convertT($data['dailyMinD'][$i]),2,".","")." "?>
								</td>
								<td>
									<?php echo number_format(convertT($data['dailyMaxD'][$i])-convertT($data['dailyMinD'][$i]),1,".","")?>
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
					<div id="S3" class="values3">
						<table class="table table3">
							<thead>
								<tr>
									<th>	
									</th>
									<th class="tableHeading3">
										<?php echo lang('avgAbbr','c')?>
										<br>
										<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
									<th class="tableHeading3">
										<?php echo lang('maximumAbbr','c')?>
										<br>
										<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
									<th class="tableHeading3">
										<?php echo lang('minimumAbbr','c')?>
										<br>
										<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
									<th class="tableHeading3">
										<?php echo lang('range','c')?>
										<br>
										<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
									<th class="summaryTableHeading">
										<?php echo lang("station",'c')." ".lang("average",'l')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
									<th class="summaryTableHeading">
										<?php echo lang("deviation",'c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach($data['dailyAvgS'] as $i=>$values){
								?>		
									<tr>
										<td>
											<?php echo date($dateFormat,$i) ?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyAvgS'][$i],array((min($data['dailyAvgS'])-0.001),(max($data['dailyAvgS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['dailyAvgS'][$i],2,".","")?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMaxS'][$i],array((min($data['dailyMaxS'])-0.001),(max($data['dailyMaxS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['dailyMaxS'][$i],2,".","")?>
										</td>
										<td style="font-weight:bold;color:white;background:<?php echo fill($data['dailyMinS'][$i],array((min($data['dailyMinS'])-0.001),(max($data['dailyMinS'])+0.001)),array("#000000", "#D9D900"))?>">
											<?php echo number_format($data['dailyMinS'][$i],2,".","")?>
										</td>
										<td>
											<?php echo number_format($data['dailyMaxS'][$i]-$data['dailyMinS'][$i],1,".","")?>
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
			<div id="R3" class="values3">
				<table class="table table3">
					<thead>
						<tr>
							<th>	
							</th>
							<th>
								<?php echo lang('total','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($data['dailyR'] as $i=>$values){
						?>		
							<tr>
								<td>
									<?php echo date($dateFormat,$i) ?>
								</td>
								<td style="font-weight:bold;color:black;background:<?php echo fill($data['dailyR'][$i],array((min($data['dailyR'])-0.001),(max($data['dailyR'])+0.001)),array("#FFFFFF", "#00BFFF"))?>">
									<?php echo number_format(convertR($data['dailyR'][$i]),2,".","")?>
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
				$( "#T3" ).show();
				$("#Topener3").click(function() {
					$( ".values3" ).hide();
					$( "#T3" ).show();
					$("#shownTable2").val("T3");
				});
				$("#Hopener3").click(function() {
					$( ".values3" ).hide();
					$( "#H3" ).show();	
					$("#shownTable2").val("H3");
				});
				$("#Popener3").click(function() {
					$( ".values3" ).hide();
					$( "#P3" ).show();
					$("#shownTable2").val("P3");
				});
				$("#Wopener3").click(function() {
					$( ".values3" ).hide();
					$( "#W3" ).show();
					$("#shownTable2").val("W3");
				});
				$("#Gopener3").click(function() {
					$( ".values3" ).hide();
					$( "#G3" ).show();
					$("#shownTable2").val("G3");
				});
				$("#Ropener3").click(function() {
					$( ".values3" ).hide();
					$( "#R3" ).show();
					$("#shownTable2").val("R3");
				});
				$("#Sopener3").click(function() {
					$( ".values3" ).hide();
					$( "#S3" ).show();
					$("#shownTable2").val("S3");
				});
				$("#Aopener3").click(function() {
					$( ".values3" ).hide();
					$( "#A3" ).show();
					$("#shownTable2").val("A3");
				});
				$("#Dopener3").click(function() {
					$( ".values3" ).hide();
					$( "#D3" ).show();
					$("#shownTable2").val("D3");
				});
				$('.table3').tablesorter();
			});	
		</script>
	</body>
</html>