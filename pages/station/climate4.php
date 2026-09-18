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
	#	Hourly tables generation
	#
	# 	A script that generates data for hourly temperature tables
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
	
	if($var!="R"){
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), HOUR(DateTime)
			FROM alldata 
			GROUP BY HOUR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$h = $row['HOUR(DateTime)'];
			$hourData['Avg'][$h] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$hourData['Max'][$h] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$hourData['Min'][$h] = chooseConvertor($row['min('.$mySQLCols[2].')']);
		}
	}
	else{
		$result = mysqli_query($con,
			"
			SELECT HOUR(DateTime), avg(Rain), max(Rain), min(Rain)
			FROM (
				SELECT DateTime, (MAX(R)-MIN(R)) AS Rain
				FROM alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)
				ORDER BY DateTime
			) AS DailyMaxTable
			GROUP BY HOUR(DateTime)
			ORDER BY HOUR(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$h = $row['HOUR(DateTime)'];
			$hourData['Avg'][$h] = chooseConvertor($row['avg(Rain)']);
			$hourData['Max'][$h] = chooseConvertor($row['max(Rain)']);
			$hourData['Min'][$h] = chooseConvertor($row['min(Rain)']);
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
		.sort3{
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
			<img src="<?php echo $pageURL.$path?>icons/filetypes/csv.png" class="exportIcon" alt='' onClick="tableExport('hourTable','csv')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/txt.png" class="exportIcon" alt='' onClick="tableExport('hourTable','txt')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/xls.png" class="exportIcon" alt='' onClick="tableExport('hourTable','excel')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/sql.png" class="exportIcon" alt='' onClick="tableExport('hourTable','sql')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/doc.png" class="exportIcon" alt='' onClick="tableExport('hourTable','doc')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/png.png" class="exportIcon" alt='' onClick="tableExport('hourTable','png')">
			<img src="<?php echo $pageURL.$path?>icons/filetypes/json.png" class="exportIcon" alt='' onClick="tableExport('hourTable','json')">
		</div>
		<table class="table table4" id="hourTable" style="border: 1px solid #<?php echo $color_schemes[$design2]['300']?>">
			<thead>
				<tr>
					<th style="text-align:center">
						<img src="<?php echo $pageURL.$path?>icons/time.png" style="width:20px" alt=''>
						<br>
						<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
					</th>
					<th style="text-align:center">
						<?php echo lang('avgAbbr','c')?>
						<br>
						<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
					</th>
					<th style="text-align:center">
						<?php echo lang('minimumAbbr','c')?>
						<br>
						<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
					</th>
					<th style="text-align:center">
						<?php echo lang('maximumAbbr','c')?>
						<br>
						<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
					</th>
					<?php 
						if($var!="R"){
					?>
							<th style="text-align:center">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort3" alt=''>
							</th>
					<?php
						}
					?>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach($hourData['Avg'] as $h=>$values){
				?>
						<tr>
							<td style="text-align:center">
								<?php echo $h?>
							</td>
							<td style="color:white;background:<?php echo fill($values,array((min($hourData['Avg'])-0.001),(max($hourData['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
								<?php echo number_format($values,($dp+1),".","")?>
							</td>
							<td style="color:white;background:<?php echo fill($hourData['Min'][$h],array((min($hourData['Min'])-0.001),(max($hourData['Min'])+0.001)),array($colors['min'], $colors['max']))?>">
								<?php echo number_format($hourData['Min'][$h],($dp),".","")?>
							</td>
							<td style="color:white;background:<?php echo fill($hourData['Max'][$h],array((min($hourData['Max'])-0.001),(max($hourData['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
								<?php echo number_format($hourData['Max'][$h],($dp),".","")?>
							</td>
							<?php 
								if($var!="R"){
							?>
									<td>
										<?php echo number_format($hourData['Max'][$h]-$hourData['Min'][$h],($dp),".","")?>
									</td>
							<?php
								}
							?>
						</tr>
				<?php
					}
				?>
			</tbody>
		</table>
		
		<script>
			$(document).ready(function() {
				$('.table4').tablesorter();
			});	
		</script>
	</div>