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
	#	Daily tables generation
	#
	# 	A script that generates data for daily temperature tables
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
	
	if($var!="R"){
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), MONTH(DateTime), DAY(DateTime)
			FROM alldata 
			GROUP BY MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Avg'] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Max'] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Min'] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$monthOverall[$row["MONTH(DateTime)"]]['Avg'][] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$monthOverall[$row["MONTH(DateTime)"]]['Max'][] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$monthOverall[$row["MONTH(DateTime)"]]['Min'][] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$allData['Avg'][] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$allData['Max'][] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$allData['Min'][] = chooseConvertor($row['min('.$mySQLCols[2].')']);
		}
	}
	else{
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), avg(DailyRain), max(DailyRain), min(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME) 
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY MONTH(DATETIME), DAY(DATETIME)
			ORDER BY MONTH(DATETIME), DAY(DATETIME)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Avg'] = chooseConvertor($row['avg(DailyRain)']);
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Max'] = chooseConvertor($row['max(DailyRain)']);
			$monthData[$row["MONTH(DateTime)"]][$row['DAY(DateTime)']]['Min'] = chooseConvertor($row['min(DailyRain)']);
			$monthOverall[$row["MONTH(DateTime)"]]['Avg'][] = chooseConvertor($row['avg(DailyRain)']);
			$monthOverall[$row["MONTH(DateTime)"]]['Max'][] = chooseConvertor($row['max(DailyRain)']);
			$monthOverall[$row["MONTH(DateTime)"]]['Min'][] = chooseConvertor($row['min(DailyRain)']);
			$allData['Avg'][] = chooseConvertor($row['avg(DailyRain)']);
			$allData['Max'][] = chooseConvertor($row['max(DailyRain)']);
			$allData['Min'][] = chooseConvertor($row['min(DailyRain)']);
		}
	}
?>

	<style>
		.opener{
			cursor: pointer;
			opacity: 0.8;
			width: 7.68%;
			text-align:center;
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
		.monthDiv{
			margin-left: auto;
			margin-right: auto;
			width: 90%;
			display: none;
		}
		.tableHeading2{
			width: 15%;
		}
	</style>
	<div style="width:94%;margin:0 auto;background:#<?php echo $color_schemes[$design]['600']?>;border-radius:15px;padding:2%">
		<table class="table" style="width:90%;margin: 0 auto;" cellpadding="3"> 
			<thead>
				<tr>
					<th class="opener" id="openerAll" style="text-align:center">
						<?php echo lang('all','c')?>
					</th>
					<?php
						foreach($monthData as $m=>$values){
					?>
						<th class="opener" id="opener<?php echo $m?>">
							<?php echo $m?>
						</th>
					<?php
						}
					?>
				</tr>
			</thead>
		</table>
		<br>
		<?php 
			foreach($monthData as $m=>$values){
		?>
			<div id="month<?php echo $m?>" class="monthDiv">
				<h2 style="background:#<?php echo $color_schemes[$design2]['900']?>">
					<?php echo lang("month".$m,"c")?>
				</h2>
				<table class="table table3">
					<thead>
						<tr>
							<th style="text-align:center">
								<?php echo lang("day","c")?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('avgAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('minimumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('maximumAbbr','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
							<th style="text-align:center">
								<?php echo lang('range','c')?>
								<br>
								<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach($values as $d=>$values2){
						?>
							<tr>
								<td style="width:20%; text-align:center">
									<?php echo $d?>
								</td>
								<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values2['Avg'],array((min($monthOverall[$m]['Avg'])-0.001),(max($monthOverall[$m]['Avg'])+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($values2['Avg'],2,".","")?>
								</td>
								<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values2['Min'],array((min($monthOverall[$m]['Min'])-0.001),(max($monthOverall[$m]['Min'])+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($values2['Min'],($dp),".","")?>
								</td>
								<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values2['Max'],array((min($monthOverall[$m]['Max'])-0.001),(max($monthOverall[$m]['Max'])+0.001)),array($colors['min'], $colors['max']))?>">
									<?php echo number_format($values2['Max'],($dp),".","")?>
								</td>
								<td style="width:20%; text-align:center">
									<?php echo number_format($values2['Max']-$values2['Min'],($dp),".","")?>
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
		<div id="allDays" class="monthDiv">
			<table class="table table3">
				<thead>
					<tr>
						<th style="text-align:center">
							<?php echo lang("day","c")?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="text-align:center">
							<?php echo lang("month",'c')?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="text-align:center">
							<?php echo lang('avgAbbr','c')?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="text-align:center">
							<?php echo lang('minimumAbbr','c')?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="text-align:center">
							<?php echo lang('maximumAbbr','c')?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
						<th style="text-align:center">
							<?php echo lang('range','c')?>
							<br>
							<img src="<?php echo $pageURL.$path?>icons/sort.png" class="sort" alt=''>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($monthData as $m=>$values){
							foreach($values as $d=>$values2){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo $d?>
							</td>
							<td style="text-align:center">
								<?php echo $m?>
							</td>
							<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values[2]['Avg'],array((min($allData['Avg'])-0.001),(max($allData['Max'])+0.001)),array("#0036D9", "#D90000"))?>">
								<?php echo number_format($values2['Avg'],($dp+1),".","")?>
							</td>
							<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values[2]['Min'],array((min($allData['Min'])-0.001),(max($allData['Min'])+0.001)),array("#0036D9", "#D90000"))?>">
								<?php echo number_format($values2['Min'],($dp),".","")?>
							</td>
							<td style="width:20%; text-align:center;color:white;background:<?php echo fill($values[2]['Max'],array((min($allData['Max'])-0.001),(max($allData['Max'])+0.001)),array("#0036D9", "#D90000"))?>">
								<?php echo number_format($values2['Max'],($dp),".","")?>
							</td>
							<td style="width:20%; text-align:center">
								<?php echo number_format($values2['Max']-$values2['Min'],($dp),".","")?>
							</td>
						</tr>
					<?php
							}
						}
					?>
				</tbody>
			</table>
		</div>
		<script>
			$(document).ready(function() {
				$('.table3').tablesorter();
				$("#month1").show();
				<?php
					foreach($monthData as $m=>$values){
				?>
					$("#opener<?php echo $m?>").click(function() {
						$(".monthDiv").hide();
						$("#month<?php echo $m?>").show();
					});
				<?php
					}
				?>
				$("#openerAll").click(function() {
					$(".monthDiv").hide();
					$("#allDays").show();
				});	
			});	
		</script>
	</div>