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
	#	Current Conditions
	#
	# 	A script showing current conditions and some past data for all 
	#	parameters.
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
	
	$result = mysqli_query($con,"
		SELECT *
		FROM alldata 
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$temperature = convertT($row['T']);
		$humidity = $row['H'];
		$wind = convertW($row['W']);
		$gust = convertW($row['G']);
		$dew = convertT($row['D']);
		$apparent = convertT($row['A']);
		$solar = $row['S'];
		$pressure = convertP($row['P']);
		$rain = convertR($row['R']);
		$direction = $row['B'];
		$rainRate = $row['RR'];
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("live conditions","u")?></title>
		<?php metaHeader()?>
		<style>
			.units{
				font-size: 1em;
			}
			.unitsImg{
				font-size: 2.5em;
			}
			.current{
				<?php
					if($solarSensor){
						echo "width: 14.286%;";
					}
					else{
						echo "width: 16.666%;";
					}
				?>
				text-align: center;
				color: #<?php echo $color_schemes[$design2]['100']?>;
				font-weight:bold;
			}
			.value{
				font-size: 2.7em;
			}
			.detail{
				font-size: 1em;
			}
			.smallImg{
				width: 20px;
				padding-right: 5px;
			}
			.category{
				margin-left: auto;
				margin-right: auto;
			}
			.categoryImg{
				width: 50px;
			}
			.icon{
				font-size:2em;
				opacity: 0.8;
				cursor: pointer;
				padding:10px;
			}
			.icon:hover{
				opacity: 1;
			}
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design]['700']?>;
				color: #<?php echo $color_schemes[$design]['font700']?>;
			}
			.dataCell{
				width: 15%;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<div style="width:98%;text-align:center;margin:0 auto">
				<h1><?php echo lang("current conditions","c")?></h1>
				<br><br>
				<table style="margin-left:auto;margin-right:auto; width:90%">
					<tr>
						<td class="current">
							<span class="mticon-temp unitsImg tooltip" title="<?php echo lang("temperature",'c')?>"></span>
							<br>
							<div class="value"><?php echo number_format($temperature,1)?></div>
						</td>
						<td class="current">
							<span class="mticon-humidity unitsImg tooltip" title="<?php echo lang("humidity",'c')?>"></span>
							<br>
							<div class="value"><?php echo $humidity ?></div>
						</td>
						<td class="current">
							<span class="mticon-pressure unitsImg tooltip" title="<?php echo lang("pressure",'c')?>"></span>
							<br>
							<div class="value"><?php echo number_format($pressure,2,".","") ?></div>
						</td>
						<td class="current">
							<span class="mticon-wind unitsImg tooltip" title="<?php echo lang("wind speed",'c')?>"></span>
							<br>
							<div class="value"><?php echo number_format($wind,1) ?></div>
						</td>
						<td class="current">
							<span class="mticon-gust unitsImg tooltip" title="<?php echo lang("wind gust",'c')?>"></span>
							<br>
							<div class="value"><?php echo number_format($gust,1) ?></div>
						</td>
						<td class="current">
							<span class="mticon-rain unitsImg tooltip" title="<?php echo lang("precipitation",'c')?>"></span>
							<br>
							<div class="value"><?php echo number_format($rain,2) ?></div>
						</td>
						<?php
							if($solarSensor){
						?>
							<td class="current">
								<span class="mticon-sun unitsImg tooltip" title="<?php echo lang("solar radiation",'c')?>"></span>
								<br>
								<div class="value"><?php echo $solar ?></div>
							</td>
						<?php
							}
						?>
					</tr>
					<tr>
						<td class="current">
							<span class="detail"><span class="mticon-apparent unitsImg tooltip" title="<?php echo lang("apparent temperature",'c')?>"></span><br><?php echo $apparent." °".$displayTempUnits?></span>
						</td>
						<td class="current">
							<span class="detail"><span class="mticon-dewpoint unitsImg tooltip" title="<?php echo lang("dew point",'c')?>"></span><br><?php echo $dew." °".$displayTempUnits?></span>
						</td>
						<td class="current">
							<span class="detail"></span>
						</td>
						<td class="current" colspan="2">
							<span class="detail"><span class="mticon-<?php echo strtolower(windAbb($direction))?> unitsImg tooltip" title="<?php echo lang("wind direction",'c')?>"></span><br><?php echo $direction."° ( ".lang("direction".windAbb($direction),'u')." )" ?></span>
						</td>
						<td class="current">
							<span class="detail"><?php echo $rainRate." ".$displayRainUnits ?>/<?php echo lang('hAbbr','l')?></span>
						</td>
						<?php
							if($solarSensor){
						?>
							<td class="current">
								<span class="units"></span>
							</td>
						<?php
							}
						?>
					</tr>
					<tr>
						<td class="current">
							<span class="units">°<?php echo $displayTempUnits?></span>
						</td>
						<td class="current">
							<span class="units">%</span>
						</td>
						<td class="current">
							<span class="units"><?php echo unitFormatter($displayPressUnits) ?></span>
						</td>
						<td class="current" colspan="2">
							<span class="units"><?php echo unitFormatter($displayWindUnits) ?></span>
						</td>
						<td class="current">
							<span class="units"><?php echo unitFormatter($displayRainUnits) ?></span>
						</td>
						<?php
							if($solarSensor){
						?>
							<td class="current">
								<span class="units">W/m<sup>2</sup></span>
							</td>
						<?php
							}
						?>
					</tr>
				</table>
				<br><br><br>
				<table style="width:auto;margin:0 auto">
					<tr>
						<td>
							<span class="mticon-temp icon tooltip" onclick="changeParameter('T')" title="<?php echo lang("temperature",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-humidity icon tooltip" onclick="changeParameter('H')" title="<?php echo lang("humidity",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-pressure icon tooltip" onclick="changeParameter('P')" title="<?php echo lang("pressure",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-wind icon tooltip" onclick="changeParameter('W')" title="<?php echo lang("wind speed",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-gust icon tooltip" onclick="changeParameter('G')" title="<?php echo lang("wind gust",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-rain icon tooltip" onclick="changeParameter('R')" title="<?php echo lang("precipitation",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-apparent icon tooltip" onclick="changeParameter('A')" title="<?php echo lang("apparent temperature",'c')?>"></span>
						</td>
						<td>
							<span class="mticon-dewpoint icon tooltip" onclick="changeParameter('D')" title="<?php echo lang("dew point",'c')?>"></span>
						</td>
						<?php if($solarSensor){?>
							<td>
								<span class="mticon-sun icon tooltip" onclick="changeParameter('S')" title="<?php echo lang("solar radiation",'c')?>"></span>
							</td>
						<?php }?>
					</tr>
				</table>
				<div id="tableData" style="width:98%;margin:0 auto">	
					<br><br><img src="<?php echo $pageURL.$path?>icons/logo.png" class="mtSpinner" style="width:80px">
				</div>
			</div>	
		</div>
		<script>
			$(function() {
				$("#tableData").load("liveDataAjax.php?parameter=T");				
			});
			function changeParameter(id){
				$("#tableData").html('<br><br><img src="<?php echo $pageURL.$path?>icons/logo.png" class="mtSpinner" style="width:80px">');
				$("#tableData").load("liveDataAjax.php?parameter="+id);
			}
		</script>
		<?php include($baseURL."footer.php");?>
	</body>
</html>