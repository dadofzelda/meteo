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
	
	if(isset($_GET['m'])){
		$q = $_GET['m'];
	}
	else{
		$q = 1;
	}
	
	if($q<1 || $q>12){
		$q = 1;
	}
	
	// create sorted month index array
	for($i=$q;$i<13;$i++){
		$indexes[] = $i;
	}
	for($i=1;$i<$q;$i++){
		$indexes[] = $i;
	}
	
	if($displayRainUnits=="in"){
		$dpR = 2;
	}
	else{
		$dpR = 1;
	}
	
	// load all data by days
	$result = mysqli_query($con,"
		SELECT DATETIME, Rain
		FROM (
			SELECT DATETIME, MAX(R) AS Rain
			FROM alldata
			GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
			ORDER BY DATETIME
		) AS DailyMaxTable
		GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
		ORDER BY DATETIME
		"
	);
	while($row = mysqli_fetch_array($result)){
		$date = strtotime($row['DATETIME']);
		$dateFormatted = date($dateFormat,$date);
		$y = date("Y",$date);
		$m = date("m",$date)*1;
		$d = date("d",$date)*1;
		// adjust year if necessary
		if($m<$q){
			$yAdj = $y - 1;
		}
		else{
			$yAdj = $y;
		}
		// overall monthly totals
		$monthlyData[$m][$y][] = convertR($row['Rain']);
		
		// daily values by month
		$monthlyDays[$m][$dateFormatted] = convertR($row['Rain']);
		
		// seasonal totals - use adjusted year!
		$seasonalData[$yAdj][$dateFormatted] = convertR($row['Rain']);
		if($row['Rain']==0){
			if(isset($seasonalDays[$yAdj]['dry'])){
				$seasonalDays[$yAdj]['dry'] = $seasonalDays[$yAdj]['dry'] + 1;
			}
			else{
				$seasonalDays[$yAdj]['dry'] = 1;
			}
		}
		else{
			if(isset($seasonalDays[$yAdj]['wet'])){
				$seasonalDays[$yAdj]['wet'] = $seasonalDays[$yAdj]['wet'] + 1;
			}
			else{
				$seasonalDays[$yAdj]['wet'] = 1;
			}
		}
		$seasonalMonthlyDataRaw[$yAdj][$m][] = convertR($row['Rain']);
		
		// all months
		$allMonths[$y][$m][$dateFormatted] = convertR($row['Rain']);
	}
	
	foreach($monthlyData as $key=>$values){
		foreach($values as $key2=>$values2){
			$total = array_sum($values2);
			$allMonthTotals[$key][$key2] = $total;
		}
	}
	
	foreach($seasonalMonthlyDataRaw as $key=>$values){
		foreach($values as $key2=>$values2){
			$total = array_sum($values2);
			$seasonalMonthlyData[$key][$key2] = $total; 
		}
		
	}
	
	function findDates($searchArr,$type){
		if($type=="max"){
			$arr = array_keys($searchArr,max($searchArr));
			if(count($arr)>5){
				return ">5 ".lang("days","l");
			}
			else{
				return implode("<br>",$arr);
			}
		}
		if($type=="min"){
			$arr = array_keys($searchArr,min($searchArr));
			if(count($arr)>5){
				return ">5 ".lang("days","l");
			}
			else{
				return implode("<br>",$arr);
			}
		}
	}
	function findDatesMonth($searchArr,$type){
		if($type=="max"){
			$arr = array_keys($searchArr,max($searchArr));
			if(count($arr)>5){
				return ">5 ".lang("months","l");
			}		
			else{
				for($i=0;$i<count($arr);$i++){
					$arr[$i] = lang('month'.$arr[$i],'c');
				}
				return implode("<br>",$arr);
			}
		}
		if($type=="min"){
			$arr = array_keys($searchArr,min($searchArr));
			if(count($arr)>5){
				return ">5 ".lang("months","l");
			}
			else{
				for($i=0;$i<count($arr);$i++){
					$arr[$i] = lang('month'.$arr[$i],'c');
				}
				return implode("<br>",$arr);
			}
		}
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="<?php echo $pageURL.$path?>pages/astronomy/d3.v3.min.js" language="JavaScript"></script>
		<script src="<?php echo $pageURL.$path?>scripts/liquidGauge.js"></script>
		<style>
			.graphSwitcherIcon{
				width: 30px;
				cursor: pointer;
				opacity: 0.8;
				padding: 10px;
			}
			.graphSwitcherIcon:hover{
				opacity: 1;
			}
			.periodSelector{
				cursor: pointer;
				font-weight: bold;
				font-variant: small-caps;
				padding: 5px;
				border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
				background: #<?php echo $color_schemes[$design2]['700']?>;
				border-radius: 10px;
				width: 50%;
				text-align:center;
				margin: 0 auto;
			}
			.periodSelector:hover{
				background: #<?php echo $color_schemes[$design2]['400']?>;
			}
			.sectionDiv{
				display:none;
				width:100%;
				padding-top: 10px;
			}
			.summaryTotal{
				width: 150px;
				display: inline-block;
				margin: 0 auto;
				text-align:center;
			}
		</style>
	</head>
	<body>
		<div>
			<br>
			<?php
				$seasonNo = 1;
				foreach($seasonalData as $season=>$values){
			?>
					<div class="summaryTotal">
						<svg id="fillgauge<?php echo $seasonNo?>" width="130" height="130"></svg>
						<?php 
							if($q==1){
								echo $season;
							}
							else{
								echo $season."/".($season+1);
							}
						?>
					</div>
			<?php 
				$seasonNo++;
				}
			?>
			<br>
			<br>
			<table style="width:98%;margin:0 auto" id="">
				<tr>
					<td style="width:33%;text-align:center">
						<div class="periodSelector" id="selectorMonthlyAverages"><?php echo lang('monthly averages','c')?></div>
					</td>
					<td style="width:33%;text-align:center">
						<div class="periodSelector" id="selectorSeasonalData"><?php echo lang('seasonal data','c')?></div>
					</td>
					<td style="width:33%;text-align:center">
						<div class="periodSelector" id="selectorAllMonths"><?php echo lang('all months','c')?></div>
					</td>
				</tr>
			</table>
			<div id="monthlyAveragesDiv" class="sectionDiv">
				<div class="resizer">
					<div class="inner-resizer">
						<div id="monthlyAveragesGraph" style="width:100%;margin:0 auto;height:500px" class="resizerGraph"></div>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="graphSwitcherIcon" data-id="spline" data-graph="monthlyAveragesGraph">
					<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="graphSwitcherIcon" data-id="column" data-graph="monthlyAveragesGraph">
				</div>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="monthlyAveragesGraph2" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="graphSwitcherIcon" data-id="spline" data-graph="monthlyAveragesGraph2">
					<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="graphSwitcherIcon" data-id="column" data-graph="monthlyAveragesGraph2">
				</div>
				<table class="table" cellspacing="3" cellpadding="2">
					<thead>
						<tr>
							<th rowspan="2"><?php echo lang('month','c')?></th>
							<th style="text-align:center"><?php echo lang('average','c')." ".lang('total','l')?></th>
							<th colspan="2" style="text-align:center"><?php echo lang('maximumAbbr','c')." ".lang('total','l')?></th>
							<th colspan="2" style="text-align:center"><?php echo lang('minimumAbbr','c')." ".lang('total','l')?></th>
							<th colspan="2" style="text-align:center"><?php echo lang('maximum','c')?> <?php echo lang('daily total','l')?></th>
							<th colspan="2" style="text-align:center"><?php echo lang('minimum','c')?> <?php echo lang('daily total','l')?></th>
						</tr>
						<tr>
							<th style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
							<th style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
							<th></th>
							<th style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
							<th></th>
							<th style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
							<th></th>
							<th style="text-align:center"><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''></th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($indexes as $id){
								if(array_key_exists($id,$allMonthTotals)){
							
						?>
									<tr>
										<td><?php echo lang('month'.$id,"c")?></td>
										<td style="width:10%">
											<?php echo number_format(array_sum($allMonthTotals[$id])/count($allMonthTotals[$id]),$dpR,".","");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo number_format(max($allMonthTotals[$id]),$dpR,".","");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo findDates($allMonthTotals[$id],"max");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo number_format(min($allMonthTotals[$id]),$dpR,".","");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo findDates($allMonthTotals[$id],"min");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo number_format(max($monthlyDays[$id]),$dpR,".","");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo findDates($monthlyDays[$id],"max");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo number_format(min($monthlyDays[$id]),$dpR,".","");?>
										</td>
										<td style="text-align:center;width:10%">
											<?php echo findDates($monthlyDays[$id],"min");?>
										</td>
									</tr>
						<?php	
								}
							}
						?>
					</tbody>
				</table>	
			</div>
			<div id="seasonalDataDiv" class="sectionDiv">
				<table class="table" cellspacing="3" cellpadding="2">
					<thead>
						<tr>
							<th style="text-align:left">
								<?php echo lang('season','c')?>
							</th>
							<th style="text-align:center;width:11%">
								<?php echo lang('total','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%">
								<?php echo lang('daily average','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('wet days','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('dry days','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('monthly maximum','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('monthly minimum','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('daily maximum','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center;width:11%" colspan="2">
								<?php echo lang('daily minimum','c')?><br><img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($seasonalData as $season=>$values){
						?>
								<tr>
									<td>
										<?php 
											if($q==1){
												echo $season;
											}
											else{
												echo $season."/".($season+1);
											}
										?>
									</td>
									<td>
										<?php 
											echo number_format(array_sum($values),$dpR,".","");
										?>
									</td>
									<td>
										<?php 
											echo number_format(array_sum($values)/count($values),$dpR+1,".","");
										?>
									</td>
									<td>
										<?php 
											echo $seasonalDays[$season]['wet'];
										?>
									</td>
									<td>
										<?php 
											echo number_format($seasonalDays[$season]['wet']/($seasonalDays[$season]['wet']+$seasonalDays[$season]['dry'])*100,1,".","")." %";
										?>
									</td>
									<td>
										<?php 
											echo $seasonalDays[$season]['dry'];
										?>
									</td>
									<td>
										<?php 
											echo number_format($seasonalDays[$season]['dry']/($seasonalDays[$season]['wet']+$seasonalDays[$season]['dry'])*100,1,".","")." %";
										?>
									</td>
									<td>
										<?php 
											echo number_format(max($seasonalMonthlyData[$season]),$dpR,".","");
										?>
									</td>
									<td>
										<?php 
											echo findDatesMonth($seasonalMonthlyData[$season],"max");
										?>
									</td>
									<td>
										<?php 
											echo number_format(min($seasonalMonthlyData[$season]),$dpR,".","");
										?>
									</td>
									<td>
										<?php 
											echo findDatesMonth($seasonalMonthlyData[$season],"min");
										?>
									</td>
									<td>
										<?php 
											echo number_format(max($values),$dpR,".","");
										?>
									</td>
									<td>
										<?php 
											echo findDates($values,"max");
										?>
									</td>
									<td>
										<?php 
											echo number_format(min($values),$dpR,".","");
										?>
									</td>
									<td>
										<?php 
											echo findDates($values,"min");
										?>
									</td>
								</tr>
						<?php
							}
						?>
					</tbody>
				</table>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="seasonalGraph" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="seasonalGraph2" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="graphSwitcherIcon" data-id="spline" data-graph="seasonalGraph2">
					<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="graphSwitcherIcon" data-id="column" data-graph="seasonalGraph2">
				</div>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="seasonalGraph3" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<img src="<?php echo $pageURL.$path?>icons/areaSpline.png" class="graphSwitcherIcon" data-id="areaspline" data-graph="seasonalGraph3">
					<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="graphSwitcherIcon" data-id="column" data-graph="seasonalGraph3">
				</div>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="seasonalGraphDays" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div class="resizer">
					<div class="inner-resizer">
						<div id="seasonalGraphDaysPercent" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
			</div>
			<div id="allMonthsDiv" class="sectionDiv">
				<div class="resizer">
					<div class="inner-resizer">
						<div id="allMonthsGraph" style="width:100%;margin:0 auto;height:300px" class="resizerGraph"></div>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="graphSwitcherIcon" data-id="spline" data-graph="allMonthsGraph">
					<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="graphSwitcherIcon" data-id="column" data-graph="allMonthsGraph">
				</div>
				<table class="table" cellspacing="3" cellpadding="2">
					<thead>
						<tr>
							<th style="text-align:center">
								<?php echo lang('year','c')?>
							</th>
							<th style="text-align:center">
								<?php echo lang('month','c')?>
							</th>
							<th style="text-align:center">
								<?php echo lang('total','c')?>
							</th>
							<th colspan="2" style="text-align:center">
								<?php echo lang('daily maximum','c')?>
							</th>
							<th colspan="2" style="text-align:center">
								<?php echo lang('daily minimum','c')?>
							</th>
						</tr>
						<tr>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th></th>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($allMonths as $key=>$values){
								foreach($values as $key2=>$values2){
						?>
									<tr>
										<td style="text-align:center">
											<?php echo $key?>
										</td>
										<td style="width:14%">
											<?php echo $key2?>
										</td>
										<td style="width:14%">
											<?php echo number_format(array_sum($values2),$dpR,'.','');?>
										</td>
										<td style="width:14%">
											<?php echo number_format(max($values2),$dpR,".","");?>
										</td>
										<td style="width:14%">
											<?php echo findDates($values2,"max");?>
										</td>
										<td style="width:14%">
											<?php echo number_format(min($values2),$dpR,".","");?>
										</td>
										<td style="width:14%">
											<?php 
												echo findDates($values2,"min");
											?>
										</td>
									</tr>
						<?php
								}
							}
						?>
					</tbody>
				</table>
			</div>
			<br><br>
		</div>
		<script>
			$(document).ready(function() {
				$(".table").tablesorter();
				$('.resizer').resizable({
					resize: function() {
						selectedDiv = $(this).find(".resizerGraph");
						chart = selectedDiv.highcharts();
						chart.setSize(
							this.offsetWidth - 50, 
							this.offsetHeight - 50,
							false
						);
					},
				});
				$("#selectorMonthlyAverages").click(function(){
					$(".sectionDiv").hide();
					$("#monthlyAveragesDiv").show();
					monthlyAveragesDisplay();
				})
				$("#selectorSeasonalData").click(function(){
					$(".sectionDiv").hide();
					$("#seasonalDataDiv").show();
					seasonalDataDisplay();
				})
				$("#selectorAllMonths").click(function(){
					$(".sectionDiv").hide();
					$("#allMonthsDiv").show();
					allMonthsDisplay();
				})
				

				<?php
					$seasonNo = 1;
					foreach($seasonalData as $season=>$values){
						$seasonalTotals[] = array_sum($values);
					}
					$maxSeason = max($seasonalTotals)*1.1;
					foreach($seasonalData as $season=>$values){
				?>
						var config<?php echo $seasonNo?> = liquidFillGaugeDefaultSettings();
						config<?php echo $seasonNo?>.minValue =  0;
						config<?php echo $seasonNo?>.maxValue =  <?php echo $maxSeason?>;
						config<?php echo $seasonNo?>.circleColor = "#<?php echo $color_schemes[$design2]['300']?>";
						config<?php echo $seasonNo?>.textColor = "fff";
						config<?php echo $seasonNo?>.waveTextColor = "#99ccff";
						config<?php echo $seasonNo?>.waveColor = "#0040ff";
						config<?php echo $seasonNo?>.circleThickness = 0.2;
						config<?php echo $seasonNo?>.textVertPosition = 0.5;
						config<?php echo $seasonNo?>.waveAnimateTime = 2000;
						config<?php echo $seasonNo?>.textSize = 0.5;
						config<?php echo $seasonNo?>.displayPercent = " <?php echo $displayRainUnits?>";
						var gauge<?php echo $seasonNo?> = loadLiquidFillGauge("fillgauge<?php echo $seasonNo?>", <?php echo number_format(array_sum($values),$dpR,".","");?>, config<?php echo $seasonNo?>);
				<?php 
					$seasonNo++;
					}
				?>
			})
			function monthlyAveragesDisplay(){
				$('#monthlyAveragesGraph').highcharts({
					chart: {
						type: 'spline'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text: '',
					},
					xAxis: {
						categories: [
							<?php
								foreach($indexes as $id){
									echo "'".lang("month".$id,"c")."',";
								}
							?>
						]
					},
					yAxis: {
						title: {
							text: '<?php echo lang("monthly totals",'c')?>'
						},
						min:0,
					},
					tooltip: {
						valueSuffix: '<?php echo ($displayRainUnits)?>',
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang('average','c')?>',
							color: 'white',
							data: [
								<?php 
									foreach($indexes as $id){
										if(array_key_exists($id,$allMonthTotals)){
											echo number_format(array_sum($allMonthTotals[$id])/count($allMonthTotals[$id]),$dpR,".","").",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
						{
							name: '<?php echo lang('maximum total','c')?>',
							color: '#4ca6ff',
							data: [
								<?php 
									foreach($indexes as $id){
										if(array_key_exists($id,$allMonthTotals)){
											echo number_format(max($allMonthTotals[$id]),$dpR,".","").",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
						{
							name: '<?php echo lang('minimum total','c')?>',
							color: '#ffc926',
							data: [
								<?php 
									foreach($indexes as $id){
										if(array_key_exists($id,$allMonthTotals)){
											echo number_format(min($allMonthTotals[$id]),$dpR,".","").",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
					]
				});
				$('#monthlyAveragesGraph2').highcharts({
					chart: {
						type: 'spline'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($indexes as $id){
									echo "'".lang("month".$id,"c")."',";
								}
							?>
						]
					},
					yAxis: {
						title: {
							text: '<?php echo lang("monthly daily totals",'c')?>'
						},
						min:0,
					},
					tooltip: {
						valueSuffix: '<?php echo ($displayRainUnits)?>',
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang('maximum daily total','c')?>',
							color: '#4ca6ff',
							data: [
								<?php 
									foreach($indexes as $id){
										if(array_key_exists($id,$allMonthTotals)){
											echo number_format(max($monthlyDays[$id]),$dpR,".","").",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
						{
							name: '<?php echo lang('minimum daily total','c')?>',
							color: '#ffc926',
							data: [
								<?php 
									foreach($indexes as $id){
										if(array_key_exists($id,$allMonthTotals)){
											echo number_format(min($monthlyDays[$id]),$dpR,".","").",";
										}
										else{
											echo "null,";
										}
									}
								?>
							]
						},
					]
				});
				$('.graphSwitcherIcon').click(function() {
					id = $(this).attr('data-id');
					graphID = $(this).attr('data-graph');
					var seasonalChart = $('#' + graphID).highcharts();
					for(i=0;i<seasonalChart.series.length;i++){
						seasonalChart.series[i].update({
							type: id
						});
					}
				});
			}
			function seasonalDataDisplay(){
				$('#seasonalGraph').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($seasonalData as $season=>$values){
									echo "'";
									if($q==1){
										echo $season;
									}
									else{
										echo $season."/".($season+1);
									}
									echo "',";
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang("seasonal data",'c')?>'
							},
							min:0,
						},
						{
							title: {
								text: '<?php echo lang("daily average",'c')?>'
							},
							min:0,
							opposite: true,
						},
					],
					tooltip: {
						valueSuffix: '<?php echo ($displayRainUnits)?>',
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang('total','c')?>',
							color: '#0080ff',
							data: [
								<?php 
									foreach($seasonalData as $season=>$values){
										echo number_format(array_sum($values),$dpR,".","").",";
									}
								?>
							]
						},
						{
							name: '<?php echo lang('monthly maximum','c')?>',
							color: '#0000ff',
							data: [
								<?php 
									foreach($seasonalData as $season=>$values){
										echo number_format(max($seasonalMonthlyData[$season]),$dpR,".","").",";
									}
								?>
							]
						},
						{
							name: '<?php echo lang('daily average','c')?>',
							color: '#ffffff',
							type: 'spline',
							yAxis: 1,
							data: [
								<?php 
									foreach($seasonalData as $season=>$values){
										echo number_format(array_sum($values)/count($values),$dpR+1,".","").",";
									}
								?>
							]
						},
					]
				});
				$('#seasonalGraph2').highcharts({
					chart: {
						type: 'spline'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($indexes as $id){
									echo "'".lang("month".$id,"c")."',";
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang("total",'c')?>'
							},
							min:0,
						}
					],
					tooltip: {
						valueSuffix: '<?php echo ($displayRainUnits)?>',
						shared: true,
					},
					series: [
						<?php 
							foreach($seasonalMonthlyData as $season=>$values){
						?>
								{
									name: '<?php 
												if($q==1){
													echo $season;
												}
												else{
													echo $season."/".($season+1);
												}
											?>',
									data: [
										<?php 
											foreach($indexes as $id){
												if(array_key_exists($id,$seasonalMonthlyData[$season])){
													echo number_format($seasonalMonthlyData[$season][$id],$dpR,".","").",";
												}
												else{
													echo "null,";
												}
											}
										?>
									],
									type: 'spline'
								},
						<?php 
							}
						?>
					]
				});
				$('#seasonalGraph3').highcharts({
					chart: {
						type: 'areaspline'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($indexes as $id){
									echo "'".lang("month".$id,"c")."',";
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang("cumulative total",'c')?>'
							},
							min:0,
						}
					],
					plotOptions: {
						series: {
							fillOpacity: 0.5
						}
					},
					tooltip: {
						valueSuffix: '<?php echo ($displayRainUnits)?>',
						shared: true,
					},
					series: [
						<?php 
							foreach($seasonalMonthlyData as $season=>$values){
						?>
								{
									name: '<?php 
												if($q==1){
													echo $season;
												}
												else{
													echo $season."/".($season+1);
												}
											?>',
									data: [
										<?php 
											$initialTotal = 0;
											foreach($indexes as $id){
												if(array_key_exists($id,$seasonalMonthlyData[$season])){
													$initialTotal += $seasonalMonthlyData[$season][$id]; 
													echo number_format($initialTotal,$dpR,".","").",";
													 
												}
												else{
													echo $initialTotal.",";
												}
											}
										?>
									],
									type: 'areaspline'
								},
						<?php 
							}
						?>
					]
				});
				$('#seasonalGraphDays').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($seasonalData as $season=>$values){
									echo "'";
									if($q==1){
										echo $season;
									}
									else{
										echo $season."/".($season+1);
									}
									echo "',";
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang("days",'c')?>'
							},
							min:0,
						}
					],
					tooltip: {
						valueSuffix: ' <?php echo lang('days','l')?>',
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang('wet days','c')?>',
							color: '#0000ff',
							data: [
								<?php 
									foreach($seasonalDays as $season=>$values){
										echo $values['wet'].",";
									}
								?>
							]
						},
						{
							name: '<?php echo lang('dry days','c')?>',
							color: '#ffbf00',
							data: [
								<?php 
									foreach($seasonalDays as $season=>$values){
										echo $values['dry'].",";
									}
								?>
							]
						},
					]
				});
				$('#seasonalGraphDaysPercent').highcharts({
					chart: {
						type: 'column'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					plotOptions: {
						column: {
							stacking: 'percent'
						}
					},
					xAxis: {
						categories: [
							<?php
								foreach($seasonalData as $season=>$values){
									echo "'";
									if($q==1){
										echo $season;
									}
									else{
										echo $season."/".($season+1);
									}
									echo "',";
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '%'
							},
							min:0,
						}
					],
					tooltip: {
						valueSuffix: '',
						shared: true,
						pointFormat: '<span>{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br>',
					},
					series: [
						{
							name: '<?php echo lang('wet days','c')?>',
							color: '#0000ff',
							data: [
								<?php 
									foreach($seasonalDays as $season=>$values){
										echo $values['wet'].",";
									}
								?>
							]
						},
						{
							name: '<?php echo lang('dry days','c')?>',
							color: '#ffbf00',
							data: [
								<?php 
									foreach($seasonalDays as $season=>$values){
										echo $values['dry'].",";
									}
								?>
							]
						},
					]
				});
				$('.graphSwitcherIcon').click(function() {
					id = $(this).attr('data-id');
					graphID = $(this).attr('data-graph');
					var seasonalChart = $('#' + graphID).highcharts();
					for(i=0;i<seasonalChart.series.length;i++){
						seasonalChart.series[i].update({
							type: id
						});
					}
				});
			}
			function allMonthsDisplay(){
				$('#allMonthsGraph').highcharts({
					chart: {
						type: 'spline'
					},
					title: {
						text: '',
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					xAxis: {
						categories: [
							<?php
								foreach($allMonths as $key=>$values){
									foreach($values as $key2=>$values2){
										echo "'".$key."/".$key2."',";
									}
								}
							?>
						]
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang('total','c')?>'
							},
							min:0,
						}
					],
					tooltip: {
						valueSuffix: '<?php echo $displayRainUnits?>',
						shared: true,
					},
					series: [
						{
							name: '<?php echo lang('total','c')?>',
							color: '#4ca6ff',
							data: [
								<?php
									foreach($allMonths as $key=>$values){
										foreach($values as $key2=>$values2){
											echo number_format(array_sum($values2),$dpR,'.','').",";
										}
									}
								?>
							]
						}
					]
				});
				$('.graphSwitcherIcon').click(function() {
					id = $(this).attr('data-id');
					graphID = $(this).attr('data-graph');
					var seasonalChart = $('#' + graphID).highcharts();
					for(i=0;i<seasonalChart.series.length;i++){
						seasonalChart.series[i].update({
							type: id
						});
					}
				});
			}
		</script>
	</body>
</html>