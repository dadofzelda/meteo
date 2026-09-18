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
	#	Sunrise, sunset and daylength graphs
	#
	# 	A script to draw sunrise, sunset and daylength graphs for user specified 
	#	date.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../../config.php");
	include("../../header.php");
	include("../../../css/design.php");
	$lat = $stationLat;
	$lon = $stationLon;
	
	$y = $_GET['y'];
	$daylengths = array();
	$dates = array();
	$rises = array();
	$sets = array();
	
	for($a=1;$a<13;$a++){
		for($i=1;$i<32;$i++){
			$currentDate = date("U", strtotime($y."-".$a."-".$i));
			$date_check = checkdate($a, $i, $y);

			if($date_check==true){
				$dateTimeZone = new DateTimeZone($stationTZ);
				$dateTime = new DateTime("now", $dateTimeZone);
				$transition = $dateTimeZone->getTransitions(mktime(0, 0, 0, $a, $i, $y),mktime(23, 59, 0, $a, $i, $y)); 
				$current_offset=($transition[0]['offset'])/3600;
				$rise = date_sunrise($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
				$set = date_sunset($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
				$difference = $set - $rise;
				$rise = date($rise);
				$riseH = date('H',$rise);
				$riseMin = date('i',$rise);
				$set = date($set);
				$setH = date('H',$set);
				$setMin = date('i',$set);
				$riseGraph = array($riseH,$riseMin);
				$setGraph = array($setH,$setMin);
				array_push($rises,$riseGraph);
				array_push($sets,$setGraph);
				array_push($dates,($i.". ".$y.". ".$year_calc));
				array_push($daylengths,round($difference/60));
			}
		}
	}
	
	$axisMin = (date('U',strtotime($y."-01-01 00:00")))*1000;
	$axisMax = (date('U',strtotime($y."-01-01 23:59")))*1000;
	?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	</head>
	<body>
		<div id="daylength" style="width: 95%; height: 400px; margin: 0 auto">
		</div>
		<div id="riset" style="width: 95%; height: 400px; margin: 0 auto">
		</div>
		<br>
	</body>
	<?php include("../../css/highcharts.php");?>
	<script>
		$(function () {
			Highcharts.setOptions({
				lang: {
					months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
					shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
					weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
					resetZoom: ['<?php echo lang('default zoom','c')?>'],
				},
				global: {
					useUTC: true
				}				
			})
		})
	</script>
	<script>
		$(function () {
			$('#daylength').highcharts({
				title: {
					text: '<?php echo lang('day length','c')?>'
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
					min: Date.UTC(<?php echo ($y-1) ?>, 11, 31),
					max: Date.UTC(<?php echo $y ?>, 11, 31),
				},
				yAxis: {
					//type: 'datetime',
					title: {
						text: '<?php echo lang('minAbbr','l')?>'
					},
					/*dateTimeLabelFormats: {
							hour: '%H:%M',
							day: '%H:%M',
							month: '%H:%M',
					},*/
				},
				tooltip: {
					/*formatter: function() {
						date = new Date(this.x);
						dateStr = date.toLocaleDateString();
						return Math.round((this.y/(1000*60))*10)/10 +' <?php echo lang('minutes','l')?></b><br>'+dateStr;
					}	*/
				},
				legend: {
					enabled: false
				},
				series:[
					{
						type: 'spline',
						name: '<?php echo lang('day length','c')?>',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC(<?php echo $y ?>, 0, 01),
						color: '#<?php echo $color_schemes[$design]['300']?>',
						data: [
							<?php
								for($i=0;$i<count($daylengths);$i++){
									echo $daylengths[$i].",";
								}	
							?>
						],
					},
				]
			});
			$('#riset').highcharts({
				title: {
					text: '<?php echo lang('day','c')?>'
				},
				chart: {
					zoomtype: 'xy',
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
					min: Date.UTC(<?php echo ($y-1) ?>, 11, 31),
					max: Date.UTC(<?php echo $y ?>, 11, 31),
				},
				yAxis: {
					type: 'datetime',
					title: {
						text: null
					},        
					dateTimeLabelFormats: {
						hour: '%H:%M',
						day: '%H:%M',
                    },
					min: Date.UTC(2000, 0, 1,0,0),
					max: Date.UTC(2000, 0, 1,23,59,59),
				},
				tooltip: {
					formatter: function() {
						date = new Date(this.x);
						dateStr = date.toLocaleDateString();
						sr = new Date(this.point.low);
						srStr = sr.getUTCHours()+":"+(sr.getMinutes()<10?'0':'') + sr.getMinutes();
						ss = new Date(this.point.high);
						ssStr = ss.getUTCHours()+":"+(ss.getMinutes()<10?'0':'') + ss.getMinutes();
						return dateStr+'<br><?php echo lang('sunrise','c')?>: '+srStr+'<br><?php echo lang('sunset','c')?>: '+ssStr;
					}	
				},
				legend: {
					enabled: false
				},
				series:[
					{
						type: 'areasplinerange',
						pointInterval: 24 * 3600 * 1000,
						pointStart: Date.UTC(<?php echo $y ?>, 0, 01),
						color: '#<?php echo $color_schemes[$design]['300']?>',
						data: [
							<?php
								for($i=0;$i<count($rises);$i++){
							?>
									[Date.UTC(2000,0,1,<?php echo $rises[$i][0]?>,<?php echo $rises[$i][1]?>),Date.UTC(2000,0,1,<?php echo $sets[$i][0]?>,<?php echo $sets[$i][1]?>)],
							<?php
								}	
							?>
						],
					},
				]
			});
		});
	</script>