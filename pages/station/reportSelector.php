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


?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>

		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<div class="textDiv">
				<h1><?php echo lang('reports','c')?></h1>
				<table style="width:98%">
					<tr>
						<td style="width:33%;vertical-align:top">
							<h2><?php echo lang('daily report','c')?></h2>
							<br>
							<table style="margin:0 auto">
								<tr>
									<td>
										<div id="daySelectMenu" style="margin-left:auto;margin-right:auto"></div>
									</td>
								</tr>
							</table>
						</td>
						<td style="width:33%;vertical-align:top">
							<h2><?php echo lang('monthly report','c')?></h2>
							<br>
							<table style="margin:0 auto">
								<tr>
									<td>
										<div id="monthSelect" style="margin-left:auto;margin-right:auto"></div>&nbsp;&nbsp;
										<input type="button" class="button" value="<?php echo lang("ok",'u')?>" id="monthSelected">
									</td>
								</tr>
							</table>

						</td>
						<td style="width:33%;vertical-align:top">
							<h2><?php echo lang('annual report','c')?></h2>
							<br>
							<select id="selectedYear" class="button">
								<option value="">--<?php echo lang('select','c')?>--</option>
								<?php
									for($i=0;$i<count($availableYears);$i++){
										echo "<option value=".$availableYears[$i].">".$availableYears[$i]."</option>";
									}
								?>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$(document).ready(function(){
				var availableDays = [<?php echo $availableDays ?>];
				$( "#daySelectMenu" ).datepicker({
					changeMonth: true,
					changeYear: true,
					dayNamesMin: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
					monthNamesShort: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
					monthNames: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
					firstDay: <?php echo $firstWeekday?>,
					minDate: <?php if($firstDbDay!=""){ echo $firstDbDay;} else{ echo "''";}?>,
					maxDate: <?php if($lastDbDay!=""){ echo $lastDbDay;} else{ echo "''";}?>,
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
				$('#monthSelect').datepicker({
					changeMonth: true,
					changeYear: true,
					monthNames: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
					monthNamesShort: ['<?php echo lang('month1short','c')?>', '<?php echo lang('month2short','c')?>', '<?php echo lang('month3short','c')?>', '<?php echo lang('month4short','c')?>', '<?php echo lang('month5short','c')?>', '<?php echo lang('month6short','c')?>', '<?php echo lang('month7short','c')?>', '<?php echo lang('month8short','c')?>', '<?php echo lang('month9short','c')?>', '<?php echo lang('month10short','c')?>', '<?php echo lang('month11short','c')?>', '<?php echo lang('month12short','c')?>'],
					showButtonPanel: false,
					minDate: <?php if($firstDbDay!=""){ echo $firstDbDay;} else{ echo "''";}?>,
					maxDate: <?php if($lastDbDay!=""){ echo $lastDbDay;} else{ echo "''";}?>,
					yearRange: "<?php echo min($availableYears)?>:<?php echo max($availableYears)?>",
					dateFormat: 'yy/m'
				}).focus(function() {
					var thisCalendar = $(this);
					$('#monthSelect .ui-datepicker-calendar').detach();
				});
				$("#selectedYear").change(function(){
					y = $(this).val();
					url = ("reportYearly.php?y="+y);
					window.location = url;
				});
				$("#monthSelected").click(function() {
					month = eval($("#monthSelect .ui-datepicker-month :selected").val())+1;
					year = $("#monthSelect .ui-datepicker-year :selected").val();
					selected = year+"/"+month;
					url = "reportMonthly.php?date="+ (selected);
					window.location = url;
				});
			})
		</script>
	</body>
</html>
