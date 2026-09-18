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
	#	Monthly average tables generation
	#
	# 	A script that generates data for monthly average temperature tables
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
	error_reporting(E_ALL);
	include_once("climateFunctions.php");
	
	$monthAvg = array();
	$monthMax = array();
	$monthMin = array();
	$months = array();
	$monthTotal = array();
	$monthOverall = array();
	
	if($var!="R"){
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), MONTH(DateTime)
			FROM alldata 
			GROUP BY MONTH(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($monthAvg,chooseConvertor($row['avg('.$mySQLCols[0].')']));
			array_push($monthMax,chooseConvertor($row['max('.$mySQLCols[1].')']));
			array_push($monthMin,chooseConvertor($row['min('.$mySQLCols[2].')']));
			array_push($months,lang("month".$row['MONTH(DateTime)'],'c'));
		}
	}
	else{
		$result = mysqli_query($con,
			"
			SELECT MONTH(DATETIME), AVG(DailyRain), MAX(DailyRain), SUM(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY MONTH(DATETIME)
			ORDER BY MONTH(DATETIME)
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($monthAvg,convertR($row['AVG(DailyRain)']));
			array_push($monthMax,convertR($row['MAX(DailyRain)']));
			$daysInMonth = date('t',strtotime("2016-".$row['MONTH(DATETIME)']."-1"));
			array_push($monthTotal,convertR($row['SUM(DailyRain)']));
			array_push($monthOverall,convertR($row['AVG(DailyRain)'])*$daysInMonth);
			array_push($months,lang("month".$row['MONTH(DATETIME)'],"c"));
		}
	}
?>

	<style>
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
		.sort{
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
			<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport('monthTable','csv')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport('monthTable','txt')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport('monthTable','excel')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport('monthTable','sql')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport('monthTable','doc')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport('monthTable','png')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport('monthTable','json')">
		</div>
		<?php
			if($var!="R"){
		?>
				<table class="table table2" id="monthTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th>
							</th>
							<th>
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th>
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th>
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th>
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($months);$i++){
						?>
							<tr>
								<td>
									<?php echo $months[$i]?>
								</td>
								<td style="color:white;background:<?php echo fill($monthAvg[$i],array((min($monthAvg)-0.001),(max($monthAvg)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($monthAvg[$i],($dp+1),".","")?>
								</td>
								<td style="color:white;background:<?php echo fill($monthMin[$i],array((min($monthMin)-0.001),(max($monthMin)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($monthMin[$i],$dp,".","") ?>
								</td>
								<td style="color:white;background:<?php echo fill($monthMax[$i],array((min($monthMax)-0.001),(max($monthMax)+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($monthMax[$i],$dp,".","") ?>
								</td>
								<td>
									<?php echo number_format($monthMax[$i] - $monthMin[$i],$dp,".","") ?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
		<?php
			}
			else{ // variable is precipitation
		?>
				<table class="table table2" id="monthTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th>
							</th>
							<th>
								<?php echo lang('avgAbbr','c')."/".lang('month','l')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th>
								<?php echo lang('avgAbbr','c')."/".strtolower(lang("day","c"))?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th>
								<?php echo lang('maximumAbbr','c')."/".strtolower(lang("day","c"))?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							for($i=0;$i<count($months);$i++){
						?>
							<tr>
								<td>
									<?php echo $months[$i]?>
								</td>
								<td style="color:black;background:<?php echo fill($monthOverall[$i],array((min($monthOverall)-0.001),(max($monthOverall)+0.001)),array("#FFFFFF", "#2693FF"))?>">
									<?php echo number_format($monthOverall[$i],$decimalsR+1,'.','')?>
								</td>
								<td style="color:black;background:<?php echo fill($monthAvg[$i],array((min($monthAvg)-0.001),(max($monthAvg)+0.001)),array("#FFFFFF", "#2693FF"))?>">
									<?php echo number_format($monthAvg[$i],"3",".","")?>
								</td>
								<td style="color:black;background:<?php echo fill($monthMax[$i],array((min($monthMax)-0.001),(max($monthMax)+0.001)),array("#FFFFFF", "#2693FF"))?>">
									<?php echo $monthMax[$i] ?>
								</td>
							</tr>
						<?php
							}
						?>
					</tbody>
				</table>
		<?php
			}
		?>
		<script>
			$(document).ready(function() {
				$('.table2').tablesorter();
			});	
		</script>
	</div>