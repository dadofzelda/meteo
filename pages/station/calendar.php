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
	#	Calendar
	#
	# 	A script that shows a yearly calendar with highlighted record values.
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
	
	$monthLengths = array(31,29,31,30,31,30,31,31,30,31,30,31);
	$availableMonths = 0;
	$availableYears = array();
	$firstMonth = "";
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	
	if(isset($_GET['y'])){
		$year = $_GET['y'];
	}
	else{
		$year = "";
	}
	
	if($year!=""){
		// validate
		$year = substr($year,0,4);
		if($year < 1900 || $year > 2100){
			die();
		}
		$condition = " AND year(DateTime)=".$year." ";
	}
	else{
		$condition = "";
	}
	
	// find the available years in database
	$result = mysqli_query($con,"
		SELECT DISTINCT year(DateTime)
		FROM alldata
		GROUP BY year(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		array_push($availableYears,$row['year(DateTime)']);
	}
	
	// no year specified, use all data
	if($year==""){
		$result = mysqli_query($con,"
			SELECT DISTINCT month(DateTime)
			FROM alldata
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$availableMonths++;
			if($firstMonth==""){
				$firstMonth = $row['month(DateTime)'];		
			}
		}	
	}
	
	// particular year specified
	else{
		$result = mysqli_query($con,"
			SELECT DISTINCT month(DateTime)
			FROM alldata
			WHERE year(DateTime)= $year
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$availableMonths++;
			if($firstMonth==""){
				$firstMonth = $row['month(DateTime)'];
			}
		}
	}
	$firstMonth2 = $firstMonth;
	$month = $firstMonth;
	$displayMonth = $firstMonth;
	
	for($i=1;$i<=$availableMonths;$i++){
		$result = mysqli_query($con,"
			SELECT avg(T),avg(H), avg(W), avg(P)
			FROM alldata
			WHERE month(DateTime)=$month".$condition."
			GROUP BY day(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			${"T".$month}[] = number_format(convertT($row["avg(T)"]),2,".","");
			${"H".$month}[] = number_format($row["avg(H)"],1,".","");
			${"W".$month}[] = number_format(convertW($row["avg(W)"]),2,".","");
			${"P".$month}[] = number_format(convertP($row["avg(P)"]),$decimalsP,".","");
		}
		${"maxT".$month} = max(${"T".$month});
		${"minT".$month} = min(${"T".$month});
		${"maxH".$month} = max(${"H".$month});
		${"minH".$month} = min(${"H".$month});
		${"maxW".$month} = max(${"W".$month});
		${"minW".$month} = min(${"W".$month});
		${"maxP".$month} = max(${"P".$month});
		${"minP".$month} = min(${"P".$month});
		
		$month++;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("weather calendar","c")?></title>
		<?php metaHeader()?>
		<style>
			.cell{
				border: 1px solid #<?php echo $color_schemes[$design2]['400']?>;
			}
			.value,.value2{
				font-size:0.6em;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<div class="textDiv">
			<h1><?php echo lang('calendar','c')?> - 
				<?php
					if($year == ""){
						echo lang('all time','c');
					}
					else{
						echo $year;
					}
				?>
			</h1>
			<div style="width:100%;text-align:center">
				<select id="yearSelect" class="button">
					<option value=""><?php echo lang('all time','l')?></option>
					<?php
						for($i=0;$i<count($availableYears);$i++){
							echo "<option value=".$availableYears[$i].">".$availableYears[$i]."</option>";
						}
					?>
				</select>
				<input type="button" value="<?php echo lang('ok','u')?>" class="button" id="submit">
			</div>
			<br>
			<table style="width:100%;border-spacing:0px">
				<tr>
					<td></td>
					<?php
						$widthCell = 100/32;
						for($i=1;$i<32;$i++){
							echo "<td style='padding:0px;width:".$widthCell."%'>".$i."</td>";
						}
					?>
				</tr>
				<?php
					for($a=1;$a<=$availableMonths;$a++){
				?>
						<tr>
							<td style="text-align:left">
								<?php echo lang(('month'.$firstMonth.'short'),'c')?>
							</td>
							<?php
								for($i=1;$i<=$monthLengths[($firstMonth-1)];$i++){
							?>	
								<td class="cell">
									<table style="width:100%;border-spacing:0;padding:0px">
										<tr>
											<td class='temp<?php echo $firstMonth?>' style="height:10px"><div class='value'><?php echo ${"T".$firstMonth}[($i-1)]?></div></td>
											<td class='humidity<?php echo $firstMonth?>' style="height:10px"><div class='value'><?php echo ${"H".$firstMonth}[($i-1)]?></div></td>
										</tr>
										<tr>
											<td class='wind<?php echo $firstMonth?>' style="height:10px"><div class='value'><?php echo ${"W".$firstMonth}[($i-1)]?></div></td>
											<td class='pressure<?php echo $firstMonth?>' style="height:10px"><div class='value'><?php echo number_format(${"P".$firstMonth}[($i-1)],$decimalsP,".","")?></div></td>
										</tr>
									</table>
								</td>
							<?php
								}
							?>	
							<?php
								for($i=1;$i<=31-($monthLengths[($firstMonth-1)]);$i++){
							?>	
								<td></td>
							<?php
								}
							?>	
						</tr>
				<?php
						$firstMonth++;
					}
				?>
			</table>
			<br><br>
			<table style="width:100%;border-spacing:0px" id="valueTable">
				<tr>
					<td></td>
					<?php
						$widthCell = 100/32;
						for($i=1;$i<32;$i++){
							echo "<td style='padding:0px;width:".$widthCell."%'>".$i."</td>";
						}
					?>
				</tr>
				<?php
					for($a=1;$a<=$availableMonths;$a++){
				?>
						<tr>
							<td style="text-align:left">
								<?php echo lang(('month'.$firstMonth2.'short'),'c')?>
							</td>
							<?php
								for($i=1;$i<=$monthLengths[($firstMonth2-1)];$i++){
							?>	
								<td class="cell">
									<table style="width:100%;border-spacing:0;padding:0px">
										<tr>
											<td class='temp<?php echo $firstMonth2?>' style="height:10px">
												<div class='value2'><?php echo ${"T".$firstMonth2}[($i-1)]?></div>
											</td>
										</tr>
										<tr>
											<td class='humidity<?php echo $firstMonth2?>' style="height:10px"><div class='value2'><?php echo ${"H".$firstMonth2}[($i-1)]?></div></td>
										</tr>
										<tr>
											<td class='wind<?php echo $firstMonth2?>' style="height:10px"><div class='value2'><?php echo ${"W".$firstMonth2}[($i-1)]?></div></td>
										</tr>
										<tr>
											<td class='pressure<?php echo $firstMonth2?>' style="height:10px"><div class='value2'><?php echo number_format((${"P".$firstMonth2}[($i-1)]),$decimalsP,".","")?></div></td>
										</tr>
									</table>
								</td>
							<?php
								}
							?>	
							<?php
								for($i=1;$i<=31-($monthLengths[($firstMonth2-1)]);$i++){
							?>	
								<td></td>
							<?php
								}
							?>	
						</tr>
				<?php
						$firstMonth2++;
					}
				?>
			</table>
			<br>
			<div id="legend">
				<table>
					<tr>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#FFBFBF"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('lowest','c')." ".lang('temperature','l')?>
						</td>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#B30000"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('highest','c')." ".lang('temperature','l')?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#CFFFBF"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('lowest','c')." ".lang('humidity','l')?>
						</td>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#00661A"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('highest','c')." ".lang('humidity','l')?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#FFBFFF"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('lowest','c')." ".lang('wind','l')?>
						</td>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#69008C"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('highest','c')." ".lang('wind','l')?>
						</td>
					</tr>
					<tr>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#FFDFBF"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('lowest','c')." ".lang('pressure','l')?>
						</td>
						<td style="text-align:left">
							<div style="width:20px;height:20px;background-color:#FF8000"></div>
						</td>
						<td style="text-align:left">
							<?php echo lang('highest','c')." ".lang('pressure','l')?>
						</td>
					</tr>
				</table>
			</div>
			<br>
			<div style="margin:0 auto;text-align:center">
				<input class="button2" type="button" value="<?php echo lang('values','c')?>" onclick="$('#valueTable').slideToggle()">
			</div>
		</div>
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
		<?php
			for($a=1;$a<=12;$a++){
		?>
			$(".temp<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"maxT".$a}?>";
			}).css("background-color", "#B30000");
			$(".temp<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"minT".$a}?>";
			}).css("background-color", "#FFBFBF").css("color", "#000000");;
			$(".humidity<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"maxH".$a}?>";
			}).css("background-color", "#00661A");
			$(".humidity<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"minH".$a}?>";
			}).css("background-color", "#CFFFBF").css("color", "#000000");
			$(".wind<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"maxW".$a}?>";
			}).css("background-color", "#69008C");
			$(".wind<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"minW".$a}?>";
			}).css("background-color", "#FFBFFF").css("color", "#000000");
			$(".pressure<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"maxP".$a}?>";
			}).css("background-color", "#FF8000");
			$(".pressure<?php echo $a?>").filter(function() {
				return $(this).text() == "<?php echo ${"minP".$a}?>";
			}).css("background-color", "#FFDFBF").css("color", "#000000");
		<?php
		}
		?>
		$(".value").hide();
		$("#valueTable").hide();
		$(".temp").css("width", "3%");
		$(".temp").css("height", "5px");
		$(".humidity").css("width", "3%");
		$(".humidity").css("height", "20px");
		$(".wind").css("width", "3%");
		$(".wind").css("height", "20px");
		$(".pressure").css("width", "3%");
		$(".pressure").css("height", "20px");
		$(".cell").filter(function() {
			return $(this).text() == "";
		}).css("border", "0px solid white");
		$("#submit").click(function() {
			year = $("#yearSelect").val();
			window.location = "calendar.php?y="+year;
		});
		</script>
	</body>
</html>
	