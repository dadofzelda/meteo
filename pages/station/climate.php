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
	#	Long-term statistics
	#
	# 	A script that generates long-term data.
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


	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}

	// create cache directory if does not exist
	createCacheDir();

	// get the latest cache file, its date and check differences
	if($var!="R"){
		if(file_exists("cache/climate".$var."extremes.txt")){
			$data = json_decode(file_get_contents("cache/climate".$var."extremes.txt"),true);
			$result = mysqli_query($con,"
				SELECT max(".$mySQLCols[1]."), min(".$mySQLCols[2].") FROM alldata WHERE DateTime >='".$data['cacheCreated']."' ORDER BY DateTime"
			);
			while($row = mysqli_fetch_array($result)){
				$maxTemporary = ($row['max('.$mySQLCols[1].')']);
				$minTemporary = ($row['min('.$mySQLCols[2].')']);
			}
			// if cached value is higher, just use it
			if($maxTemporary<$data['alltimeMax']){
				$alltimeMax = $data['alltimeMax'];
				$alltimeMaxDate = $data['alltimeMaxDate'];
			}
			// if cached value is same, add new dates
			else if($maxTemporary == $data['alltimeMax']){
				$alltimeMax = $data['alltimeMax'];
				$alltimeMaxDate = $data['alltimeMaxDate'];
				$result = mysqli_query($con,"
					SELECT DateTime
					FROM alldata
					WHERE (DateTime >= '".$data['cacheCreated']."') AND ".$mySQLCols[1]."=$alltimeMax
					ORDER BY DateTime
					"
				);
				while($row = mysqli_fetch_array($result)){
					array_push($alltimeMaxDate,strtotime($row['DateTime']));
				}
			}
			// if cached value is smaller, rewrite values
			else{
				$alltimeMax = $maxTemporary;
				$alltimeMaxDate = array();
				$result = mysqli_query($con,"
					SELECT DateTime
					FROM alldata
					WHERE (DateTime >= '".$data['cacheCreated']."') AND ".$mySQLCols[1]."=$alltimeMax
					ORDER BY DateTime
					"
				);
				while($row = mysqli_fetch_array($result)){
					array_push($alltimeMaxDate,strtotime($row['DateTime']));
				}
			}

			// if cached value is lower, just use it
			if($minTemporary>$data['alltimeMin']){
				$alltimeMin = $data['alltimeMin'];
				$alltimeMinDate = $data['alltimeMinDate'];
			}
			// if cached value is same, add new dates
			else if($minTemporary == $data['alltimeMin']){
				$alltimeMin = $data['alltimeMin'];
				$alltimeMinDate = $data['alltimeMinDate'];
				$result = mysqli_query($con,"
					SELECT DateTime
					FROM alldata
					WHERE (DateTime >= '".$data['cacheCreated']."') AND ".$mySQLCols[2]."=$alltimeMin
					ORDER BY DateTime
					"
				);
				while($row = mysqli_fetch_array($result)){
					array_push($alltimeMinDate,strtotime($row['DateTime']));
				}
			}
			// if cached value is higher, rewrite values
			else{
				$alltimeMin = $minTemporary;
				$alltimeMinDate = array();
				$result = mysqli_query($con,"
					SELECT DateTime
					FROM alldata
					WHERE (DateTime >= '".$data['cacheCreated']."') AND ".$mySQLCols[2]."=$alltimeMin
					ORDER BY DateTime
					"
				);
				while($row = mysqli_fetch_array($result)){
					array_push($alltimeMinDate,strtotime($row['DateTime']));
				}
			}

			$result = mysqli_query($con,"
				SELECT DateTime
				FROM alldata
				ORDER BY DateTime DESC
				LIMIT 1
				"
			);
			while($row = mysqli_fetch_array($result)){
				$data['cacheCreated'] = $row['DateTime'];
			}

			//remove potential duplicates
			if(is_array($alltimeMaxDate)){
				$alltimeMaxDate = array_unique($alltimeMaxDate);
			}
			if(is_array($alltimeMinDate)){
				$alltimeMinDate = array_unique($alltimeMinDate);
			}

			$data['alltimeMax'] = $alltimeMax;
			$data['alltimeMin'] = $alltimeMin;
			$data['alltimeMaxDate'] = $alltimeMaxDate;
			$data['alltimeMinDate'] = $alltimeMinDate;

			// overwrite cache
			file_put_contents("cache/climate".$var."extremes.txt",json_encode($data));

		}

		// no cache file, calculate everything from scratch
		else{
			$result = mysqli_query($con,"
				SELECT max(".$mySQLCols[1]."), min(".$mySQLCols[2].")
				FROM alldata
				"
			);
			while($row = mysqli_fetch_array($result)){
				$alltimeMax = ($row['max('.$mySQLCols[1].')']);
				$alltimeMin = ($row['min('.$mySQLCols[2].')']);
			}

			$result = mysqli_query($con,"
				SELECT DateTime
				FROM alldata
				WHERE ".$mySQLCols[1]." = $alltimeMax
				LIMIT 50"
			);
			while($row = mysqli_fetch_array($result)){
				$alltimeMaxDate [] = strtotime($row['DateTime']);
			}

			$result = mysqli_query($con,"
				SELECT DateTime
				FROM alldata
				WHERE ".$mySQLCols[2]." = $alltimeMin
				LIMIT 50"
			);
			while($row = mysqli_fetch_array($result)){
				$alltimeMinDate [] = strtotime($row['DateTime']);
			}

			$result = mysqli_query($con,"
				SELECT DateTime
				FROM alldata
				ORDER BY DateTime DESC
				LIMIT 1
				"
			);
			while($row = mysqli_fetch_array($result)){
				$data['cacheCreated'] = $row['DateTime'];
			}

			$data['alltimeMax'] = $alltimeMax;
			$data['alltimeMin'] = $alltimeMin;
			$data['alltimeMaxDate'] = $alltimeMaxDate;
			$data['alltimeMinDate'] = $alltimeMinDate;

			file_put_contents("cache/climate".$var."extremes.txt",json_encode($data));

		}

		$alltimeMax = chooseConvertor($alltimeMax);
		$alltimeMin = chooseConvertor($alltimeMin);


		// Summary

		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0].")
			FROM alldata
			"
		);
		while($row = mysqli_fetch_array($result)){
			$alltimeAvg = chooseConvertor($row['avg('.$mySQLCols[0].')']);
		}

		$result = mysqli_query($con,"
			SELECT MONTH(DateTime)
			FROM alldata
			GROUP BY MONTH(DateTime)
			ORDER BY avg(".$mySQLCols[0].")
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$coldestMonth = lang("month".$row['MONTH(DateTime)'],"c");
		}
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime)
			FROM alldata
			GROUP BY MONTH(DateTime)
			ORDER BY avg(".$mySQLCols[0].") DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$warmestMonth = lang("month".$row['MONTH(DateTime)'],"c");
		}
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime)
			FROM alldata
			GROUP BY MONTH(DateTime), DAY(DateTime)
			ORDER BY avg(".$mySQLCols[0].")
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$coldestDay = $row['DAY(DateTime)']." ".lang("month".$row['MONTH(DateTime)'],"c");
		}
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime)
			FROM alldata
			GROUP BY MONTH(DateTime), DAY(DateTime)
			ORDER BY avg(".$mySQLCols[0].") DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$warmestDay = $row['DAY(DateTime)']." ".lang("month".$row['MONTH(DateTime)'],"c");
		}
	}
	else{ // variable is precipitation
		$alltimeMaxDate = array();
		$result = mysqli_query($con,
			"
			SELECT DateTime, avg(DailyRain), max(DailyRain), sum(DailyRain)
			FROM(
				SELECT DateTime, max(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
			) as DailyMaxTable
			"
		);
		while($row = mysqli_fetch_array($result)){
			$alltimeAvg = convertR($row['avg(DailyRain)']);
			$alltimeMax = convertR($row['max(DailyRain)']);
			$alltimeTotal = convertR($row['sum(DailyRain)']);
			$alltimeMaxDb = $row['max(DailyRain)'];
		}

		$result = mysqli_query($con,
			"
			SELECT DateTime
			FROM(
				SELECT DateTime, max(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
			) as DailyMaxTable
			WHERE DailyRain = $alltimeMax
			"
		);
		while($row = mysqli_fetch_array($result)){
			array_push($alltimeMaxDate,strtotime($row['DateTime']));
		}

		$result = mysqli_query($con,
			"
			SELECT MONTH(DATETIME), AVG( DailyRain )
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY MONTH( DATETIME )
			ORDER BY AVG( DailyRain )
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$minMonth = lang("month".$row['MONTH(DATETIME)'],"c");
		}
		$result = mysqli_query($con,
			"
			SELECT MONTH(DATETIME), AVG( DailyRain )
			FROM (
				SELECT DATETIME, MAX( R ) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY MONTH(DATETIME)
			ORDER BY AVG(DailyRain) DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$maxMonth = lang("month".$row['MONTH(DATETIME)'],"c");
		}
		$result = mysqli_query($con,
			"
			SELECT DAY(DATETIME), MONTH(DATETIME), AVG(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DATETIME
				) AS DailyMaxTable
			GROUP BY MONTH(DATETIME), DAY(DATETIME)
			ORDER BY AVG(DailyRain)
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$minDay = $row['DAY(DATETIME)']." ".lang("month".$row['MONTH(DATETIME)'],"c");
		}
		$result = mysqli_query($con,
			"
			SELECT DAY(DATETIME), MONTH(DATETIME), AVG(DailyRain)
			FROM (
				SELECT DATETIME, MAX(R) AS DailyRain
				FROM alldata
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME)
				ORDER BY DATETIME
			) AS DailyMaxTable
			GROUP BY MONTH(DATETIME), DAY (DATETIME)
			ORDER BY AVG(DailyRain) DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			$maxDay = $row['DAY(DATETIME)']." ".lang("month".$row['MONTH(DATETIME)'],"c");
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $heading?></title>
		<?php metaHeader()?>
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
			.value{
				text-align: right;
			}
			.parameters{
				width: 24px;
			}
			.imgHeader{
				width: 40px;
			}
			.descriptions{
				width:auto;
			}
			.button{
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif!important;
				font-size: 0.8em;
			}
			.monthSetter{
				opacity: 0.8;
				cursor: pointer;
			}
			.monthSetter:hover{
				opacity: 1
			}
			.varDivs{
				display:none;
			}
			.climateHeadingDiv{
				width:90%;
				margin: 0 auto;
				background: #<?php echo $color_schemes[$design2]['800']?>;
				border: 1px solid #<?php echo $color_schemes[$design2]['200']?>;
				border-radius: 10px;
				cursor: pointer;
				padding-top: 10px;
				padding-bottom: 10px;
				font-weight: bold;
			}
			.climateHeadingDiv:hover{
				background: #<?php echo $color_schemes[$design2]['400']?>;
			}
			.inner-resizer {
				padding: 10px;
			}
			.resizer {
				margin: 0 auto;
				width: 98%;

			}
			.highlightTR{
				color: white;
				background:#<?php echo $color_schemes[$design2]['900']?>;
			}
		</style>
		<script type="text/javascript" src="//code.highcharts.com/highcharts.js"></script>
		<script type="text/javascript" src="//code.highcharts.com/highcharts-more.js"></script>
		<script type="text/javascript" src="//code.highcharts.com/modules/heatmap.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
		<script>
			Highcharts.setOptions({
				global: {
					useUTC: false
				},
				lang: {
					months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
					shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
					weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
					resetZoom: ['<?php echo lang('default zoom','c')?>'],
				}
			});
		</script>
		<?php include("../../css/highcharts.php");?>
	</head>
	<body>
	<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
			<h1><?php echo $heading?></h1>
			<br>
			<table style="width:98%;margin:0 auto">
				<tr>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="1" >
							<?php echo lang("summary","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="2" >
							<?php echo lang("year","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="3" >
							<?php echo lang("month","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="4" >
							<?php echo lang("day","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="5" >
							<?php echo lang("hour","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="6" >
							<?php echo lang("all months","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="7" >
							<?php echo lang("month graphs","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="8" >
							<?php echo lang("day graphs","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="9" >
							<?php echo lang("hour graphs","c")?>
						</div>
					</td>
					<td style="width:10%;text-align:center">
						<div class="climateHeadingDiv" data-id="10" >
							<?php echo lang("visualizations","c")?>
						</div>
					</td>
				</tr>
			</table>
			<br>
			<div id="varDiv1" style="width:98%;margin:0 auto" class="varDivs">
				<?php
					if($var!="R"){
				?>
				<table class="table">
					<tr>
						<td class="highlightTR">
							<?php echo lang("all time average","c")?>
						</td>
						<td style="text-align:right">
							<?php echo number_format($alltimeAvg,3,".","")?> <?php echo $UoM?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang("all time max","c")?>
						</td>
						<td style="text-align:right">
							<?php echo $alltimeMax?>
							<?php
								echo $UoM;
								if(count($alltimeMaxDate)<6){
									for($i=0;$i<count($alltimeMaxDate);$i++){
										echo "<br>".date($timeFormat.",  ".$dateFormat,$alltimeMaxDate[$i]);
									}
								}
								else{
									echo "<br><span style='font-size:0.8em'>".lang('more than 5 instances','l')."</span>";
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang("all time min","c")?>
						</td>
						<td style="text-align:right">
							<?php echo $alltimeMin?>
							<?php
								echo $UoM;
								if(count($alltimeMinDate)<6){
									for($i=0;$i<count($alltimeMinDate);$i++){
										echo "<br>".date($timeFormat.",  ".$dateFormat,$alltimeMinDate[$i]);
									}
								}
								else{
									echo "<br><span style='font-size:0.8em'>".lang('more than 5 instances','l')."</span>";
								}
							?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang("all time range","c")?>
						</td>
						<td style="text-align:right">
							<?php echo ($alltimeMax - $alltimeMin)?> <?php echo $UoM?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang('average','c')." ".lang("minimumAbbr",'l')." ".lang("month","l")?>
						</td>
						<td style="text-align:right">
							<?php echo $coldestMonth?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang('average','c')." ".lang("maximumAbbr",'l')." ".lang("month","l")?>
						</td>
						<td style="text-align:right">
							<?php echo $warmestMonth?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang('average','c')." ".lang("minimum",'l')." ".lang("day","l")?>
						</td>
						<td style="text-align:right">
							<?php echo $coldestDay?>
						</td>
					</tr>
					<tr>
						<td class="highlightTR">
							<?php echo lang('average','c')." ".lang("maximum",'l')." ".lang("day","l")?>
						</td>
						<td style="text-align:right">
							<?php echo $warmestDay?>
						</td>
					</tr>
				</table>
				<?php
					}
					else{ // parameter is precipitation
				?>
						<table class="table">
							<tr>
								<th>
									<?php echo lang("all time total","c")?>
								</th>
								<td style="text-align:right">
									<?php echo number_format($alltimeTotal,1,".","")?> <?php echo $displayRainUnits?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo lang("all time average","c")?>
								</th>
								<td style="text-align:right">
									<?php echo number_format($alltimeAvg,3,".","")?> <?php echo $displayRainUnits."/".strtolower(lang("day","c"))?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo lang("all time max","c")?>
								</th>
								<td style="text-align:right">
									<?php echo $alltimeMax?>
									<?php
										echo $displayRainUnits."/".strtolower(lang("day","c"));
										if(count($alltimeMaxDate)<6){
											for($i=0;$i<count($alltimeMaxDate);$i++){
												echo "<br>".date($dateFormat,$alltimeMaxDate[$i]);
											}
										}
										else{
											echo "<br><span style='font-size:0.8em'>".lang('more than 5 instances','l')."</span>";
										}
									?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo lang("minimum","c")." ".lang("month","l")?>
								</th>
								<td style="text-align:right">
									<?php echo $minMonth?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo lang("maximum","c")." ".lang("month","l")?>
								</th>
								<td style="text-align:right">
									<?php echo $maxMonth?>
								</td>
							</tr>
							<tr>
								<th>
									<?php echo lang("maximum","c")." ".lang("day","l")?>
								</th>
								<td style="text-align:right">
									<?php echo $maxDay?>
								</td>
							</tr>
						</table>
				<?php
					}
				?>
			</div>
			<?php
				for($i=2;$i<11;$i++){
			?>
					<div id="varDiv<?php echo $i?>" style="width:98%;margin:0 auto;text-align:center" class="varDivs"><img class="mtSpinner" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
			<?php
				}
			?>
			<script>
				$(document).ready(function() {
					$("#varDiv1").show();
					$(".climateHeadingDiv").click(function(){
						$(".varDivs").hide();
						id = $(this).attr("data-id");
						$("#varDiv"+id).show();
						var x = $('#yearGraph').highcharts();x.reflow();
						var x = $('#monthGraph').highcharts();x.reflow();
						var x = $('#hourGraph').highcharts();x.reflow();
						var x = $('#visualization').highcharts();x.reflow();
					});
					$("#varDiv2").load("climate5.php?var=<?php echo $var?>");
					$("#varDiv3").load("climate2.php?var=<?php echo $var?>");
					$("#varDiv4").load("climate3.php?var=<?php echo $var?>");
					$("#varDiv5").load("climate4.php?var=<?php echo $var?>");
					$("#varDiv6").load("climate6.php?var=<?php echo $var?>");
					$("#varDiv7").load("climateGraphAjaxDisplay.php?var=<?php echo $var?>");
					$("#varDiv8").load("climateGraphMonthAjaxDisplay.php?var=<?php echo $var?>");
					$("#varDiv9").load("climateGraphHourAjaxDisplay.php?var=<?php echo $var?>");
					$("#varDiv10").load("climateVisualDisplay.php?var=<?php echo $var?>");
				})
			</script>
			</div>
			<br><br>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
