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
	#	Annual Solar Max
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
    include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");

    $day = "2016-1-1"; // use leap year
    for($i=0;$i<366;$i++){
		// find this day solar max 
		$thisMin = strtotime($day." 10:00"); // assume never before 10
		$nextMin = strtotime($day." 15:00"); // assume never after 15
		$currentMax = 0;
		$maxTime = 0;
		for($a=$thisMin;$a<$nextMin;$a = $a+60){
			$solarMax = solarMax($a,$stationLat,$stationLon,$stationElevation,$stationElevationUnits);
			if($solarMax>=$currentMax){
				$currentMax = $solarMax;
				$maxTime = $a; 
			}
		}
		$currentMaxs[] = $currentMax;
		$maxTimes[] = $maxTime;
        $data[] = array($currentMax,$maxTime);
		$U = date("U",strtotime($day." + 1 day"));
		$day = date("Y-m-d",$U);
    }

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("maximum potential solar radiation","c");?></title>

		<?php metaHeader();?>
		<script src="//code.highcharts.com/highcharts.src.js"></script>
		<script src="//code.highcharts.com/highcharts-more.js"></script>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<h1><?php echo lang('maximum potential solar radiation','c')?></h1>
			<div id="solarMax" style="width: 98%; height: 400px; margin: 0 auto"></div>
			<div id="solarMax2" style="width: 98%; height: 400px; margin: 0 auto"></div>
			<br>
			<table class="table" style="width: 98%; margin: 0 auto">
				<tr>
					<th>
						<?php echo lang("annual average maximum",'c')?>
					</th>
					<td style="text-align:right;padding-right:5px;width:50%">
						<?php echo number_format(array_sum($currentMaxs)/count($currentMaxs),2,".","")?> W/m<sup>2</sup>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo lang("minimum",'c')?>
					</th>
					<td style="text-align:right;padding-right:5px;width:50%">
						<?php echo number_format(min($currentMaxs),2,".","")?> W/m<sup>2</sup><br>
						<?php 
							$minimumDate = array_keys($currentMaxs,min($currentMaxs));
							$minimumDate = $maxTimes[$minimumDate[0]];
							echo date("j",$minimumDate)." ".lang('month'.date("n",$minimumDate),'c');
						?>
					</td>
				</tr>
				<tr>
					<th>
						<?php echo lang("maximum",'c')?>
					</th>
					<td style="text-align:right;padding-right:5px;width:50%">
						<?php echo number_format(max($currentMaxs),2,".","")?> W/m<sup>2</sup><br>
						<?php 
							$minimumDate = array_keys($currentMaxs,max($currentMaxs));
							$minimumDate = $maxTimes[$minimumDate[0]];
							echo date("j",$minimumDate)." ".lang('month'.date("n",$minimumDate),'c');
						?>
					</td>
				</tr>
			</table>
			<br>
			<h2><?php echo lang('months','c')?></h2>
			<br>
			<table class="table" style="width: 98%; margin: 0 auto">
				<thead>
					<tr>
						<th>
							<span class='fa fa-calendar' style="font-size:1.5em;padding-left:5px">
						</th>
						<th>
							<span class='fa fa-clock-o' style="font-size:1.5em">
						</th>
						<th>
							<span class='mticon-sun' style="font-size:1.5em">
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						for($i=0;$i<count($data);$i++){
							if(date("j",$data[$i][1])==15){
					?>
								<tr>
									<td>
										<?php echo date("j",$data[$i][1])." ".lang('month'.date("n",$data[$i][1]),'c');?>
									</td>
									<td>
										<?php echo date($timeFormat,$data[$i][1]);?>
									</td>
									<td>
										<?php echo $data[$i][0];?>
									</td>
								</tr>
					<?php
							}
						}
					?>
				</tbody>
			</table>
			<br>
			<h2><?php echo lang('days','c')?></h2>
			<br>
			<table class="table" style="width: 98%; margin: 0 auto">
				<thead>
					<tr>
						<th>
							<span class='fa fa-calendar' style="font-size:1.5em;padding-left:5px">
						</th>
						<th>
							<span class='fa fa-clock-o' style="font-size:1.5em">
						</th>
						<th>
							<span class='mticon-sun' style="font-size:1.5em">
						</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						for($i=0;$i<count($data);$i++){
					?>
							<tr>
								<td>
									<?php echo date("j",$data[$i][1])." ".lang('month'.date("n",$data[$i][1]),'c');?>
								</td>
								<td>
									<?php echo date($timeFormat,$data[$i][1]);?>
								</td>
								<td>
									<?php echo $data[$i][0];?>
								</td>
							</tr>
					<?php
						}
					?>
				</tbody>
			</table>
			<br><br>
		</div>
		<?php include("../../footer.php");?>
		<?php include("../../../css/highcharts.php");?>
		<script>
		$(function () {
			months = [
				<?php 
					for($i=1;$i<13;$i++){
						$monthsArray[] = "'".lang('month'.$i,'c')."'";
					}
					echo implode(",",$monthsArray);
				?>
			];
			$('#solarMax').highcharts({
				title: {
					text: ''
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				plotOptions: {
					series: {
						fillOpacity: 1,
						marker: {
							enabled: false
						}
					}
				},
				xAxis: {
					type: 'datetime',
					title: {
						text: null
					},
					dateTimeLabelFormats: {
							hour: '%H:%M',
							day: '%b',
							month: '%b',
					},
					min: Date.UTC(2016, 0, 1,0,0,1),
					max: Date.UTC(2016, 11, 31,23,59,59),
				},
				yAxis: [
					{
						title: {
							text: '<?php echo lang('solar radiation','c')?>'
						},
						//min: <?php echo $axisMin?>,
						//max: <?php echo $axisMax?>,
					}
				],
				tooltip: {
					formatter: function() {
						date = new Date(this.x);
						dateStr = months[date.getMonth()] + " " + date.getDate();
						return Math.round((this.y)) +' W/m2</b><br>'+dateStr;
					}	
				},
				legend: {
					enabled: false
				},
				series:[
					{
						type: 'areaspline',
						name: '<?php echo lang('maximumAbbr','c')?>',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC(2016, 0, 01),
						fillColor: {
							linearGradient: { x1: 0, y1: 1, x2: 0, y2: 0},
							stops: [
								[0, '#000000'],
								[1, '#f4e542']
							]
						},
						data: [
							<?php
								for($i=0;$i<count($data);$i++){
									echo $data[$i][0].",";
								}	
							?>
						],
					}
				]
			});$('#solarMax2').highcharts({
				title: {
					text: ''
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				plotOptions: {
					series: {
						fillOpacity: 1,
						marker: {
							enabled: false
						}
					}
				},
				xAxis: {
					type: 'datetime',
					title: {
						text: null
					},
					dateTimeLabelFormats: {
							hour: '%H:%M',
							day: '%b',
							month: '%b',
					},
					min: Date.UTC(2016, 0, 1,0,0,1),
					max: Date.UTC(2016, 11, 31,23,59,59),
				},
				yAxis: [
					{
						type: 'datetime',
						title: {
							text: '<?php echo lang('hAbbr','l')?>'
						},
						//min: <?php echo $axisMin?>,
						//max: <?php echo $axisMax?>,
						dateTimeLabelFormats: {
								hour: '%H:%M',
								day: '%H:%M',
								month: '%H:%M',
						}
					}
				],
				tooltip: {
					formatter: function() {
						date = new Date(this.x);
						dateStr = months[date.getMonth()] + " " + date.getDate();
						date2 = new Date(this.y);
						dateStr2H = date2.getUTCHours() + ":";
						minutes = date2.getUTCMinutes();
						if(minutes<10){
							minutes = "0" + minutes;
						}
						dateStr2 = dateStr2H + minutes;
						return "<b>" + dateStr2 + "</b><br>"+dateStr;
					}
				},
				legend: {
					enabled: false
				},
				series:[
					{
						type: 'spline',
						name: '<?php echo lang('maximumAbbr','c')?>',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC(2016, 0, 01),
						color: '#<?php echo $color_schemes[$design]['300']?>',
						data: [
							<?php
								for($i=0;$i<count($data);$i++){
									echo "Date.UTC(2016, 0, 1,".date("H",$data[$i][1]).",".date("i",$data[$i][1])."),";
								}	
							?>
						],
					},
				]
			});
		});
	</script>
	</body>
</html>