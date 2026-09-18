<?php
	if($designFont=="Bree Serif" || $designFont2=="Bree Serif"){
		$fontURL = "https://fonts.googleapis.com/css?family=Bree+Serif&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="PT Sans" || $designFont2=="PT Sans"){
		$fontURL = "https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="Roboto" || $designFont2=="Roboto"){
		$fontURL = "https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="Dosis" || $designFont2=="Dosis"){
		$fontURL = "https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Ubuntu" || $designFont2=="Ubuntu"){
		$fontURL = "https://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Lobster" || $designFont2=="Lobster"){
		$fontURL = "https://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext";
	}
	if($designFont=="Kaushan Script" || $designFont2=="Kaushan Script"){
		$fontURL = "https://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext";
	}
	if($designFont=="Open Sans" || $designFont2=="Open Sans"){
		$fontURL = "https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Play" || $designFont2=="Play"){
		$fontURL = "https://fonts.googleapis.com/css?family=Play:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Open Sans Condensed" || $designFont2=="Open Sans Condensed"){
		$fontURL = "https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,latin-ext";
	}
	if($designFont=="Anton" || $designFont2=="Anton"){
		$fontURL = "https://fonts.googleapis.com/css?family=Anton&subset=latin,latin-ext";
	}
	if($designFont=="Inconsolata" || $designFont2=="Inconsolata"){
		$fontURL = "https://fonts.googleapis.com/css?family=Inconsolata:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Righteous" || $designFont2=="Righteous"){
		$fontURL = "https://fonts.googleapis.com/css?family=Righteous&subset=latin,latin-ext";
	}
	if($designFont=="Marck Script" || $designFont2=="Marck Script"){
		$fontURL = "https://fonts.googleapis.com/css?family=Marck+Script&subset=latin,latin-ext";
	}
	if($designFont=="Poiret One" || $designFont2=="Poiret One"){
		$fontURL = "https://fonts.googleapis.com/css?family=Poiret+One&subset=latin,latin-ext";
	}
	if($designFont=="Cutive Mono" || $designFont2=="Cutive Mono"){
		$fontURL = "https://fonts.googleapis.com/css?family=Cutive+Mono&subset=latin,latin-ext";
	}
?>
<script>
	Highcharts.createElement('link', {
		href: '<?php echo $fontURL?>',
		rel: 'stylesheet',
		type: 'text/css'
	}, null, document.getElementsByTagName('head')[0]);

	Highcharts.theme = {
		colors: ["#<?php echo $color_schemes['grey']['200']?>", "#<?php echo $color_schemes['red']['200']?>", "#<?php echo $color_schemes['blue']['200']?>", "#<?php echo $color_schemes['green']['200']?>", "#<?php echo $color_schemes['yellow']['200']?>", "#<?php echo $color_schemes['deep_purple']['200']?>", "#<?php echo $color_schemes['orange']['200']?>", "#<?php echo $color_schemes['teal']['200']?>", "#<?php echo $color_schemes['cyan']['200']?>", "#<?php echo $color_schemes['pink']['200']?>", "#<?php echo $color_schemes['brown']['200']?>"],	
		chart: {
			backgroundColor: null,
			color: "#008800",
			style: {
				fontFamily: "'<?php echo $designFont?>', sans-serif"
			},
			plotBorderColor: '#606063'
		},
		title: {
			style: {
				color: "#fff",
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
			gridLineColor: '#fff',
			labels: {
				style: {
					color: '#fff',
					fontSize: '<?php echo $customGraphFontSize?>'
				}
			},
			lineColor: '#fff',
			minorGridLineColor: '#fff',
			tickColor: '#fff',
			title: {
				style: {
					color: '#fff',
					fontSize: '<?php echo $customGraphFontSize?>'
				}
			}
		},
		yAxis: {
			gridLineColor: '#fff',
			gridLineWidth: 0,
			labels: {
				style: {
					color: '#fff',
					fontSize: '<?php echo $customGraphFontSize?>'
				}
			},
			lineColor: '#fff',
			minorGridLineColor: '#fff',
			tickColor: '#fff',
			tickWidth: 1,
			title: {
				style: {
					color: '#fff',
					fontSize: '<?php echo $customGraphFontSize?>'
				}
			}
		},
		tooltip: {
			backgroundColor: '#<?php echo $color_schemes[$design2]['700']?>',
			style: {
				color: '#<?php echo $color_schemes[$design2]['font700']?>',
				fontSize: '<?php echo $customGraphFontSize?>'
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
			},
			boxplot: {
				fillColor: '#505053'
			},
			candlestick: {
				lineColor: 'white'
			},
			errorbar: {
				color: 'white'
			}
		},
		legend: {
			itemStyle: {
				color: '#<?php echo $color_schemes[$design2]['400']?>'
			},
			itemHoverStyle: {
				color: '#<?php echo $color_schemes[$design2]['700']?>'
			},
			itemHiddenStyle: {
				color: '#<?php echo $color_schemes[$design2]['200']?>'
			},
			style: {
				fontSize: '<?php echo $customGraphFontSize?>'
			}
		},
		credits: {
			style: {
				color: '#666',
				fontSize: '<?php echo $customGraphFontSize?>'
			}
		},
		labels: {
			style: {
				color: '#707073',
				fontSize: '<?php echo $customGraphFontSize?>'
			}
		},

		drilldown: {
			activeAxisLabelStyle: {
				color: '#F0F0F3'
			},
			activeDataLabelStyle: {
				color: '#F0F0F3'
			}
		},

		navigation: {
			buttonOptions: {
				symbolStroke: '#DDDDDD',
				theme: {
					fill: '#505053'
				}
			}
		},

		// scroll charts
		rangeSelector: {
			buttonTheme: {
				fill: '#505053',
				stroke: '#000000',
				style: {
					color: '#CCC'
				},
				states: {
					hover: {
						fill: '#707073',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					},
					select: {
						fill: '#000003',
						stroke: '#000000',
						style: {
							color: 'white'
						}
					}
				}
			},
			inputBoxBorderColor: '#505053',
			inputStyle: {
				backgroundColor: '#333',
				color: 'silver'
			},
			labelStyle: {
				color: 'silver'
			}
		},

		navigator: {
			handles: {
				backgroundColor: '#666',
				borderColor: '#AAA'
			},
			outlineColor: '#CCC',
			maskFill: 'rgba(255,255,255,0.1)',
			series: {
				color: '#7798BF',
				lineColor: '#A6C7ED'
			},
			xAxis: {
				gridLineColor: '#505053'
			}
		},

		scrollbar: {
			barBackgroundColor: '#808083',
			barBorderColor: '#808083',
			buttonArrowColor: '#CCC',
			buttonBackgroundColor: '#606063',
			buttonBorderColor: '#606063',
			rifleColor: '#FFF',
			trackBackgroundColor: '#404043',
			trackBorderColor: '#404043'
		},

		// special colors for some of the
		legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
		background2: '#505053',
		dataLabelsColor: '#B0B0B3',
		textColor: '#C0C0C0',
		contrastTextColor: '#F0F0F3',
		maskColor: 'rgba(255,255,255,0.3)'
	};

	// Apply the theme
	Highcharts.setOptions(Highcharts.theme);
</script>