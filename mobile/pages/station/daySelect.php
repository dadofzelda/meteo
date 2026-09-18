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
				<div id="daySelectMenu" style="margin-left:auto;margin-right:auto"></div>
			</div>
			<br><br>
		</div>
		<?php include("../../footer.php")?>
		<script>
			var availableDays = [<?php echo $availableDays ?>];
			$( "#daySelectMenu" ).datepicker({
				changeMonth: true,
				changeYear: true,
				dayNamesMin: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
				monthNamesShort: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
				monthNames: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
				firstDay: <?php echo $firstWeekday?>,
				minDate: <?php echo $firstDbDay?>,
				maxDate: <?php echo $lastDbDay?>,
				beforeShowDay: function(date) {
					if($.inArray($.datepicker.formatDate('yy-m-d', date ), availableDays) > -1){
						return [true,"",""];
					}
					else{
						return [false,'',""];
					}
				},
				dateFormat: "dd.mm.yy",
				yearRange: "<?php echo min($availableYears)?>:<?php echo max($availableYears)?>",
				onSelect: function(date) {
					d = date.slice(0,2);
					m = date.slice(3,5);
					y = date.slice(6,10);
					url = ("reportDaily.php?d="+d+"&m="+m+"&y="+y);
					window.location = url;
				},
			});
		</script>
	</body>
</html>
	