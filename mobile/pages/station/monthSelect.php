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
				<div id="dialogMonthReport" style="text-align:left;width:100%;margin:0 auto" class="forceDefaultDatepicker">
					<div id="monthSelect" style="width:100%;margin:0 auto"></div>
					<br>
					<input type="button" class="button" value="<?php echo lang("ok",'u')?>" id="monthSelected">
				</div>
			</div>
			<br><br>
		</div>
		<?php include("../../footer.php")?>
		<script>
			$('#monthSelect').datepicker({
				changeMonth: true,
				changeYear: true,
				monthNames: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
				showButtonPanel: false,
				minDate: <?php echo $firstDbDay?>,
				maxDate: <?php echo $lastDbDay?>,
				yearRange: "<?php echo min($availableYears)?>:<?php echo max($availableYears)?>",
				dateFormat: 'yy/m'
			}).focus(function() {
				var thisCalendar = $(this);
				$('#monthSelect .ui-datepicker-calendar').detach();
			});
			$("#monthSelected").click(function() {
				month = eval($("#monthSelect .ui-datepicker-month :selected").val())+1;
				year = $("#monthSelect .ui-datepicker-year :selected").val();
				selected = year+"/"+month;
				url = "reportMonthly.php?date="+ (selected);
				window.location = url;
				$("#dialogMonthReport").dialog('close');
			});
		</script>
	</body>
</html>
	