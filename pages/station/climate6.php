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
	#	Monthly tables generation
	#
	# 	A script that generates data for monthly tables
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
	include($baseURL."scripts/stats.php");
	
	if(isset($_GET['var']) && strlen($_GET['var']) == 1){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	include_once("climateFunctions.php");
	
	$monthAvg = array();
	$monthMax = array();
	$monthMin = array();
	$monthTotal = array();
	
	if($var!="R"){
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), MONTH(DateTime), YEAR(DateTime)
			FROM alldata 
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$y = $row['YEAR(DateTime)'];
			$m = $row['MONTH(DateTime)'];
			$monthData['Avg'][$y][$m] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$monthData['Max'][$y][$m] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$monthData['Min'][$y][$m] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$allData['Avg'][] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$allData['Max'][] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$allData['Min'][] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$overallMonth['Avg'][$m][] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$overallMonth['Max'][$m][$y] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$overallMonth['Min'][$m][$y] = chooseConvertor($row['min('.$mySQLCols[2].')']);
		}
	}
	else{
		$result = mysqli_query($con,"
			SELECT YEAR(DateTime), MONTH(DateTime), AVG(DailyRain), MAX(DailyRain), SUM(DailyRain)
			FROM (
				SELECT DateTime, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime) 
				ORDER BY DateTime
			) AS DailyMaxTable
			GROUP BY YEAR(DateTime), MONTH(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$y = $row['YEAR(DateTime)'];
			$m = $row['MONTH(DateTime)'];
			$monthData['Avg'][$y][$m] = chooseConvertor($row['AVG(DailyRain)']);
			$monthData['Max'][$y][$m] = chooseConvertor($row['MAX(DailyRain)']);
			$monthData['Sum'][$y][$m] = chooseConvertor($row['SUM(DailyRain)']);
			$allData['Avg'][] = chooseConvertor($row['AVG(DailyRain)']);
			$allData['Max'][] = chooseConvertor($row['MAX(DailyRain)']);
			$allData['Sum'][] = chooseConvertor($row['SUM(DailyRain)']);
		}
	}
	
	function average($arr){
		return array_sum($arr)/count($arr);
	}
	function deviation($n){
		if($n>0){
			return "+".$n;
		}
		else{
			return $n;
		}
	}
?>


	<style>
		#tabs{
			min-height: 200px;
		}
		.parameters{
			width: 24px;
		}
		.imgHeader{
			width: 40px;
			padding-left: 5px;
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
		.sort5{
			width: 15px;
			cursor: pointer;
			opacity: 0.8;
		}
		.tableHeading2{
			width: 15%;
		}
	</style>
	<div style="width:94%;margin:0 auto;background:#<?php echo $color_schemes[$design]['600']?>;border-radius:15px;padding:2%">
		<div class="exportDiv">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','csv')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','txt')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','excel')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','sql')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','doc')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','png')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport('allMonthTable','json')">
		</div>
		<?php
			if($var!="R"){
		?>
				<table class="table table6" id="allMonthTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th rowspan="2">
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('station','c')." ".lang('average','l')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" rowspan="2">
								<?php echo lang('deviation','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th style="text-align:center;width:11.5%" colspan="2">
								<?php echo lang('alltime','c')." ".lang('maximumAbbr','l')?>								
							</th>
							<th style="text-align:center;width:11.5%" colspan="2">
								<?php echo lang('alltime','c')." ".lang('minimumAbbr','l')?>
							</th>
						</tr>
						<tr>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th>
							
							</th>
							<th style="text-align:center">
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th>
							
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($monthData['Avg'] as $y => $values){
								foreach($values as $m => $values2){
						?>
									<tr>
										<td style="text-align:left">
											<?php echo $y?>/<?php echo $m?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Avg'][$y][$m],array((min($allData['Avg'])-0.001),(max($allData['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Avg'][$y][$m],($dp+1),".","")?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Min'][$y][$m],array((min($allData['Min'])-0.001),(max($allData['Min'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Min'][$y][$m],($dp),".","")?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Max'][$y][$m],array((min($allData['Max'])-0.001),(max($allData['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Max'][$y][$m],($dp),".","")?>
										</td>
										<td>
											<?php echo number_format($monthData['Max'][$y][$m]-$monthData['Min'][$y][$m],($dp),".","")?>
										</td>
										<td>
											<?php echo number_format(average($overallMonth['Avg'][$m]),($dp+1),".","")?>
										</td>
										<td>
											<?php echo deviation(number_format($monthData['Avg'][$y][$m] - average($overallMonth['Avg'][$m]),($dp+1),".",""))?>
										</td>
										<td>
											<?php echo number_format(max($overallMonth['Max'][$m]),($dp),".","")?>
										</td>
										<td style="font-size:0.8em">
											<?php echo implode("<br>",array_keys($overallMonth['Max'][$m],max($overallMonth['Max'][$m])))?>
										</td>
										<td>
											<?php echo number_format(min($overallMonth['Min'][$m]),($dp),".","")?>
										</td>
										<td style="font-size:0.8em">
											<?php echo implode("<br>",array_keys($overallMonth['Min'][$m],min($overallMonth['Min'][$m])))?>
										</td>
									</tr>
						<?php
								}
							}
						?>
					</tbody>
				</table>
		<?php
			}
			else{
		?>
				<table class="table table6" id="allMonthTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th>
							</th>
							<th>
								<?php echo lang('total','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th>
								<?php echo lang('avgAbbr','c')."/".strtolower(lang("day","c"))?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
							<th>
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort5" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($monthData['Avg'] as $y => $values){
								foreach($values as $m => $values2){
						?>
									<tr>
										<td style="text-align:left">
											<?php echo $y?>/<?php echo $m?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Sum'][$y][$m],array((min($allData['Sum'])-0.001),(max($allData['Sum'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Sum'][$y][$m],($dp+1),".","")?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Avg'][$y][$m],array((min($allData['Avg'])-0.001),(max($allData['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Avg'][$y][$m],($dp),".","")?>
										</td>
										<td style="color:white;background:<?php echo fill($monthData['Max'][$y][$m],array((min($allData['Max'])-0.001),(max($allData['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
											<?php echo number_format($monthData['Max'][$y][$m],($dp),".","")?>
										</td>
									</tr>
						<?php
								}
							}
						?>
					</tbody>
				</table>
		<?php
			}
		?>
		<script>
			$(document).ready(function() {
				$('.table6').tablesorter();
			});	
		</script>
	</div>