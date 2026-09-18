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
	#	Station details
	#
	# 	Page providing details about the station.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	
	$availableDays = "";
	$firstDbDay = "";
	$lastDbDay = "";
	$availableYears = array();

	$result = mysqli_query($con,"
		SELECT Year(DateTime), Month(DateTime), Day(DateTime)
		FROM alldata
		Group BY Year(DateTime), Month(DateTime), Day(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		if($firstDbDay==""){
			$firstDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
		}
		$currentDate = $row['Year(DateTime)']."-".$row['Month(DateTime)']."-".$row['Day(DateTime)'];
		$availableDays .= "\"".$currentDate."\",";
		$lastDbDay = "new Date(".$row['Year(DateTime)'].", ".$row['Month(DateTime)']." - 1, ".$row['Day(DateTime)'].")";
	}

	$result = mysqli_query($con,"
		SELECT DISTINCT Year(DateTime)
		FROM alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		array_push($availableYears,$row['Year(DateTime)']);
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $stationModel?></title>
		<?php metaHeader()?>
		<style>
			
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv">
				<br><br>
				<select id="selectedYear" class="button2">
				<?php
					for($i=0;$i<count($availableYears);$i++){
						echo "<option value=".$availableYears[$i].">".$availableYears[$i]."</option>";
					}
				?>
				</select>
				<br><br>
				<input type="button" class="button" value="<?php echo lang("ok",'u')?>" id="yearSelected">
			</div>
			<br><br>
		</div>
		<?php include("../../footer.php")?>
		<script>
			$("#yearSelected").click(function() {
				year = $("#selectedYear").val();
				url = "reportYearly.php?y="+ year;
				window.location = url;
				$("#dialogYearReport").dialog('close');
			});
		</script>
	</body>
</html>
	