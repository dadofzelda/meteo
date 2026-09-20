<?php

	# 		Graph - combined
	# 		Namespace:		combinedGraph
	#		Meteotemplate Block

	# 		v1.0 - Jan 31, 2017
	# 			- initial release
	# 		v2.0 - Feb 3, 2017
	# 			- added pressure
	
		
	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");
	
	$language = loadLangs();

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "<span style='color:#".$graphColor."'>Please go to your admin section and go through the settings for this block first.</span>";
		die();
	}

	if($theme=="light"){
		$graphColor = "000";
		$gridColor = "666666";
	}
	else{
		$graphColor = "fff";
		$gridColor = "b2b2b2";
	}
	$graphHueColor = getColorHue("#".$color_schemes[$design2]["900"],"hex");

	$offsetArr = explode(":",$offset);
	$offset = $offsetArr[0]*-60;

	$graphsToInclude = trim($graphsToInclude);
    $graphsToInclude = str_replace(" ", "", $graphsToInclude);
    $graphsToInclude = explode(",", $graphsToInclude);

	$mainHeight = count($graphsToInclude) * $graphHeight;
	 
?>
	<style>
		.chart {
			width: 100%;
			height: <?php echo $graphHeight?>px;
			margin: 0 auto;
		}
	</style>
	<div id="containerCombined" style="width:98%;margin:0 auto;height:<?php echo $mainHeight?>px"></div>
	<script>
		Highcharts.createElement('link', {
			href: 'https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext',
			rel: 'stylesheet',
			type: 'text/css'
		}, null, document.getElementsByTagName('head')[0]);
		Highcharts.theme = {
			chart: {
				backgroundColor: null,
				color: "#<?php echo $color_schemes[$design]['font900']?>",
				style: {
					fontFamily: "'<?php echo $designFont?> Narrow', sans-serif"
				}
			},
			title: {
				style: {
					color: "#<?php echo $graphColor?>",
					textTransform: 'uppercase',
					fontSize: '20px'
				}
			},
			subtitle: {
				style: {
					color: '#E0E0E3',
					textTransform: 'uppercase'
				}
			},
			xAxis: {
				gridLineColor: '#<?php echo $graphColor?>',
				gridLineWidth: 1,
				gridLineDashStyle: 'shortDash',
				labels: {
					style: {
						color: '#<?php echo $graphColor?>',
					}
				},
				lineColor: '#<?php echo $graphColor?>',
				minorGridLineColor: '#<?php echo $graphColor?>',
				tickColor: '#<?php echo $graphColor?>',
				title: {
					style: {
						color: '#<?php echo $graphColor?>',
					}
				}
			},
			yAxis: {
				gridLineColor: '#<?php echo $gridColor?>',
				gridLineWidth: 1,
				gridLineDashStyle: 'shortDash',
				labels: {
					style: {
						color: '#<?php echo $graphColor?>',
					}
				},
				lineColor: '#<?php echo $graphColor?>',
				minorGridLineColor: '#<?php echo $graphColor?>',
				tickColor: '#<?php echo $graphColor?>',
				tickWidth: 1,
				title: {
					style: {
						color: '#<?php echo $graphColor?>',
					}
				}
			},
			tooltip: {
				backgroundColor: '#<?php echo $color_schemes[$design2]['900']?>',
				style: {
					color: '#<?php echo $color_schemes[$design2]['font900']?>'
				}
			},
			plotOptions: {
				series: {
					dataLabels: {
						color: '#<?php echo $color_schemes[$design2]['900']?>'
					},
					marker: {
						lineColor: '#333'
					}
				}
			},
			legend: {
				itemStyle: {
					color: '#<?php echo $graphColor?>'
				},
				itemHoverStyle: {
					color: '#<?php echo $color_schemes[$design2]['500']?>'
				},
				itemHiddenStyle: {
					color: '#999999'
				},
			},
			credits: {
				style: {
					color: '#666'
				},
				text: '<?php echo $pageName." @ Meteotemplate"?>',
				href: '<?php echo $pageURL.$path?>'
			},
			labels: {
				style: {
					color: '#707073'
				}
			},
		};

		// Apply the theme
		Highcharts.setOptions(Highcharts.theme);
		Highcharts.setOptions({
			global: {
				useUTC: true, 
				timezoneOffset: <?php echo $offset?>
			},
			lang: {
				months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
				shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
				weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
				resetZoom: ['<?php echo lang('default zoom','c')?>']
			}
		});
	</script>
	<script>
		$('#containerCombined').bind('mousemove touchmove touchstart', function (e) {
			var chart,
				point,
				i,
				event;

			for (i = 0; i < Highcharts.charts.length; i = i + 1) {
				chart = Highcharts.charts[i];
				event = chart.pointer.normalize(e.originalEvent);
				point = chart.series[0].searchPoint(event, true);

				if (point) {
					point.highlight(e);
				}
			}
		});
		Highcharts.Pointer.prototype.reset = function () {
			return undefined;
		};
		Highcharts.Point.prototype.highlight = function (event) {
			this.onMouseOver();
			this.series.chart.tooltip.refresh(this);
			this.series.chart.xAxis[0].drawCrosshair(event, this);
		};
		function syncExtremes(e) {
			var thisChart = this.chart;

			if (e.trigger !== 'syncExtremes') {
				Highcharts.each(Highcharts.charts, function (chart) {
					if (chart !== thisChart) {
						if (chart.xAxis[0].setExtremes) {
							chart.xAxis[0].setExtremes(e.min, e.max, undefined, false, { trigger: 'syncExtremes' });
						}
					}
				});
			}
		}
		$.getJSON('homepage/blocks/graphCombined/data.php?callback', function (activity) {
			$.each(activity.datasets, function (i, dataset) {

				// Add X values
				dataset.data = dataset.data.map(function (val, j) {
					return [activity.xData[j], val];
				});

				$('<div class="chart">')
					.appendTo('#containerCombined')
					.highcharts({
						chart: {
							marginLeft: 50,
							spacingTop: 20,
							spacingBottom: 20,
							spacingLeft: 20,
							zoomType: 'x'
						},
						title: {
							text: dataset.name,
							align: 'left',
							margin: 0,
							x: 30
						},
						credits: {
							enabled: false
						},
						legend: {
							enabled: false
						},
						xAxis: {
							crosshair: true,
							events: {
								setExtremes: syncExtremes
							},
							type: 'datetime',
						},
						yAxis: {
							title: {
								text: dataset.unit
							}
						},
						tooltip: {
							positioner: function () {
								return {
									x: this.chart.chartWidth - this.label.width,
									y: 10
								};
							},
							borderWidth: 0,
							backgroundColor: 'none',
							//pointFormat: '{point.y} - {point.x}',
							headerFormat: '',
							shadow: false,
							style: {
								fontSize: '18px'
							},
							formatter: function() {
								var d = new Date(this.x);
								var n = d.toLocaleString();
								return  '<b>'+this.y +'</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' + n;
							},
							valueDecimals: dataset.valueDecimals,
							useHTML: true
						},
						series: [{
							data: dataset.data,
							name: dataset.name,
							type: dataset.type,
							color: '#<?php echo $graphColor?>',
							fillOpacity: 0.3,
							tooltip: {
								valueSuffix: ' ' + dataset.unit
							}
						}]
					});
			});
		});
	</script>
	
