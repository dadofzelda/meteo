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
	#	Interval - selector
	#
	# 	Select interval for summary
	#
	############################################################################
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	$result = mysqli_query($con,"
		SELECT YEAR(DateTime)
		FROM alldata 
		GROUP BY YEAR(DateTime)
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$years[] = $row['YEAR(DateTime)'];
	}
	$minYear = min($years);
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Meteotemplate</title>
		<?php metaHeader()?>
		<style>

		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<h1><?php echo lang('interval summary','c')?></h1>
			<div class="textDiv;text-align:center">
				<table style="margin: 0 auto;width:50%">
					<tr>
						<td style="width:50%;text-align:center">
							<span class="fa fa-hourglass-start tooltip" style="font-size:2.5em" title="<?php echo lang("from",'c')?>"></span>
							<br><br>
							<input id="fromY" type="number" min=<?php echo $minYear?> max=<?php echo date("Y")?> class="button2" size="4" value="<?php echo date("Y")?>">
							<input id="fromM" type="number" min=1 max=12 class="button2" size="2" value="<?php echo date("m")?>">
							<input id="fromD" type="number" min=1 max=31 class="button2" size="2" value="<?php echo date("d")?>">
							<br>
							<input id="fromH" type="number" min=0 max=24 class="button2" size="2" value="<?php echo date("H")?>"> : 
							<input id="fromMin" type="number" min=0 max=59 class="button2" size="2" value="<?php echo date("i")?>">
						</td>
						<td>
							<span class="fa fa-hourglass-end tooltip" style="font-size:2.5em" title="<?php echo lang("to",'c')?>"></span>
							<br><br>
							<input id="toY" type="number" min=<?php echo $minYear?> max=<?php echo date("Y")?> class="button2" size="4" value="<?php echo date("Y")?>">
							<input id="toM" type="number" min=1 max=12 class="button2" size="2" value="<?php echo date("m")?>">
							<input id="toD" type="number" min=1 max=31 class="button2" size="2" value="<?php echo date("d")?>">
							<br>
							<input id="toH" type="number" min=0 max=24 class="button2" size="2" value="<?php echo date("H")?>"> : 
							<input id="toMin" type="number" min=0 max=59 class="button2" size="2" value="<?php echo date("i")?>">
						</td>
					</tr>
				</table>
				<br>
			</div>
			<div style="margin:0 auto;width:100%;text-align:center">
				<input id="showSummary" type="button" class="button" value="<?php echo lang('show','c')?>">
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$("#showSummary").click(function(){
				fromY = $("#fromY").val();
				fromM = $("#fromM").val();
				fromD = $("#fromD").val();
				fromH = $("#fromH").val();
				fromMin = $("#fromMin").val();
				toY = $("#toY").val();
				toM = $("#toM").val();
				toD = $("#toD").val();
				toH = $("#toH").val();
				toMin = $("#toMin").val();
				fromDate = fromY+"_"+fromM+"_"+fromD+"_"+fromH+"_"+fromMin;
				toDate = toY+"_"+toM+"_"+toD+"_"+toH+"_"+toMin;
				url = "intervalSummary.php?from="+fromDate+"&to="+toDate;
				window.location = url;
			})
		</script>
	</body>
</html>