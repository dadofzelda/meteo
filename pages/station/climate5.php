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
	#	Annual tables generation
	#
	# 	A script that generates data for annual tables
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
	
	$yearAvg = array();
	$yearMax = array();
	$yearMin = array();
	$yearMax = array();
	
	if($var!="R"){
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), YEAR(DateTime)
			FROM alldata 
			GROUP BY YEAR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$y = $row['YEAR(DateTime)'];
			$yearData['Avg'][$y] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$yearData['Max'][$y] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$yearData['Min'][$y] = chooseConvertor($row['min('.$mySQLCols[2].')']);
		}
	}
	else{
		$result = mysqli_query($con,"
			SELECT YEAR(DATETIME), avg(DailyRain), max(DailyRain), sum(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY YEAR(DATETIME)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$y = $row['YEAR(DATETIME)'];
			$yearData['Avg'][$y] = chooseConvertor($row['avg(DailyRain)']);
			$yearData['Max'][$y] = chooseConvertor($row['max(DailyRain)']);
			$yearData['Sum'][$y] = chooseConvertor($row['sum(DailyRain)']);
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
		.sort4{
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
			<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport('yearTable','csv')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport('yearTable','txt')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport('yearTable','excel')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport('yearTable','sql')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport('yearTable','doc')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport('yearTable','png')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport('yearTable','json')">
		</div>
		<?php
			if($var!="R"){
		?>
				<table class="table table5" id="yearTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th style="text-align:center">
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($yearData['Avg'] as $y=>$values){
						?>
								<tr>
									<td style="text-align:center">
										<?php echo $y?>
									</td>
									<td style="color:white;background:<?php echo fill($values,array((min($yearData['Avg'])-0.001),(max($yearData['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($values,($dp+1),".","")?>
									</td>
									<td style="color:white;background:<?php echo fill($yearData['Min'][$y],array((min($yearData['Min'])-0.001),(max($yearData['Min'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($yearData['Min'][$y],($dp),".","")?>
									</td>
									<td style="color:white;background:<?php echo fill($yearData['Max'][$y],array((min($yearData['Max'])-0.001),(max($yearData['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($yearData['Max'][$y],($dp),".","")?>
									</td>
									<td>
										<?php echo number_format($yearData['Max'][$y]-$yearData['Min'][$y],($dp),".","")?>
									</td>
								</tr>				<?php
							}
						?>
					</tbody>
				</table>
		<?php
			}
			else{	
		?>
				<table class="table table5" id="yearTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
					<thead>
						<tr>
							<th style="text-align:center">
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('total','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort4" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($yearData['Sum'] as $y=>$values){
						?>
								<tr>
									<td style="text-align:center">
										<?php echo $y?>
									</td>
									<td style="color:white;background:<?php echo fill($values,array((min($yearData['Sum'])-0.001),(max($yearData['Sum'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($values,($dp+1),".","")?>
									</td>
									<td style="color:white;background:<?php echo fill($yearData['Avg'][$y],array((min($yearData['Min'])-0.001),(max($yearData['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($yearData['Avg'][$y],($dp),".","")?>
									</td>
									<td style="color:white;background:<?php echo fill($yearData['Max'][$y],array((min($yearData['Max'])-0.001),(max($yearData['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
										<?php echo number_format($yearData['Max'][$y],($dp),".","")?>
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
		<br>
	</div>
	<script>
		$(document).ready(function() {
			$('.table5').tablesorter();
		});	
	</script>
