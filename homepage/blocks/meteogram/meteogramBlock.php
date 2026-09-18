
<?php

	# 		Meteogram block
	# 		Namespace:		meteogram
	#		Meteotemplate Block
	
	#		v1.1 - Apr 8, 2016
	#			- changes for version 6.0
	#		v1.2 - Oct 5, 2016
	#			- bug fixes
	# 		v1.3 - Feb 3, 2017
	# 			- implemented caching
	# 			- fixed wind arrow color for light theme
	# 		v2.0 - Apr 29, 2017
	# 			- added support for multiple locations
	# 		v3.0 - May 3, 2017
	# 			- optimization, removed iframe
	# 			- CSS tweaks
	# 		v3.1 - Jan 8, 2018
	# 			- bug fixes
	#		v4.0 - Feb 2, 2022
	#			- block rewritten due to changes in yr.no API
	
		
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
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	if(!is_dir("cache")){
		mkdir("cache");
	}
	
?>
	<style>
		
	</style>
	<div id="containerMeteogram" style="width: 98%; height: 310px; margin: 0 auto">
		<div style="margin-top: 100px; text-align: center" id="loading">
			<i class="fa fa-spinner fa-spin"></i> Loading data from external source
		</div>
	</div>
	<script src="https://code.highcharts.com/modules/windbarb.js"></script>
	<script>
		Highcharts.setOptions({
			global: {
				useUTC: false, 
				timezoneOffset: <?php echo $offset*-60?> 
			},
			lang: {
				months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
				shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
				weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
				resetZoom: ['<?php echo lang('default zoom','c')?>']
			}		
		});
	</script>
	<?php include("../../../css/highcharts.php");?>
		<script>

function Meteogram(json, container) {
    // Parallel arrays for the chart data, these are populated as the JSON file
    // is loaded
    this.symbols = [];
    this.precipitations = [];
    this.precipitationsError = []; // Only for some data sets
    this.winds = [];
    this.temperatures = [];
    this.pressures = [];

    // Initialize
    this.json = json;
    this.container = container;

    // Run
    this.parseYrData();
}

/**
 * Mapping of the symbol code in yr.no's API to the icons in their public
 * GitHub repo, as well as the text used in the tooltip.
 *
 * https://api.met.no/weatherapi/weathericon/2.0/documentation
 */
Meteogram.dictionary = {
    clearsky: {
        symbol: '01',
        text: 'Clear sky'
    },
    fair: {
        symbol: '02',
        text: 'Fair'
    },
    partlycloudy: {
        symbol: '03',
        text: 'Partly cloudy'
    },
    cloudy: {
        symbol: '04',
        text: 'Cloudy'
    },
    lightrainshowers: {
        symbol: '40',
        text: 'Light rain showers'
    },
    rainshowers: {
        symbol: '05',
        text: 'Rain showers'
    },
    heavyrainshowers: {
        symbol: '41',
        text: 'Heavy rain showers'
    },
    lightrainshowersandthunder: {
        symbol: '24',
        text: 'Light rain showers and thunder'
    },
    rainshowersandthunder: {
        symbol: '06',
        text: 'Rain showers and thunder'
    },
    heavyrainshowersandthunder: {
        symbol: '25',
        text: 'Heavy rain showers and thunder'
    },
    lightsleetshowers: {
        symbol: '42',
        text: 'Light sleet showers'
    },
    sleetshowers: {
        symbol: '07',
        text: 'Sleet showers'
    },
    heavysleetshowers: {
        symbol: '43',
        text: 'Heavy sleet showers'
    },
    lightsleetshowersandthunder: {
        symbol: '26',
        text: 'Light sleet showers and thunder'
    },
    sleetshowersandthunder: {
        symbol: '20',
        text: 'Sleet showers and thunder'
    },
    heavysleetshowersandthunder: {
        symbol: '27',
        text: 'Heavy sleet showers and thunder'
    },
    lightsnowshowers: {
        symbol: '44',
        text: 'Light snow showers'
    },
    snowshowers: {
        symbol: '08',
        text: 'Snow showers'
    },
    heavysnowshowers: {
        symbol: '45',
        text: 'Heavy show showers'
    },
    lightsnowshowersandthunder: {
        symbol: '28',
        text: 'Light snow showers and thunder'
    },
    snowshowersandthunder: {
        symbol: '21',
        text: 'Snow showers and thunder'
    },
    heavysnowshowersandthunder: {
        symbol: '29',
        text: 'Heavy snow showers and thunder'
    },
    lightrain: {
        symbol: '46',
        text: 'Light rain'
    },
    rain: {
        symbol: '09',
        text: 'Rain'
    },
    heavyrain: {
        symbol: '10',
        text: 'Heavy rain'
    },
    lightrainandthunder: {
        symbol: '30',
        text: 'Light rain and thunder'
    },
    rainandthunder: {
        symbol: '22',
        text: 'Rain and thunder'
    },
    heavyrainandthunder: {
        symbol: '11',
        text: 'Heavy rain and thunder'
    },
    lightsleet: {
        symbol: '47',
        text: 'Light sleet'
    },
    sleet: {
        symbol: '12',
        text: 'Sleet'
    },
    heavysleet: {
        symbol: '48',
        text: 'Heavy sleet'
    },
    lightsleetandthunder: {
        symbol: '31',
        text: 'Light sleet and thunder'
    },
    sleetandthunder: {
        symbol: '23',
        text: 'Sleet and thunder'
    },
    heavysleetandthunder: {
        symbol: '32',
        text: 'Heavy sleet and thunder'
    },
    lightsnow: {
        symbol: '49',
        text: 'Light snow'
    },
    snow: {
        symbol: '13',
        text: 'Snow'
    },
    heavysnow: {
        symbol: '50',
        text: 'Heavy snow'
    },
    lightsnowandthunder: {
        symbol: '33',
        text: 'Light snow and thunder'
    },
    snowandthunder: {
        symbol: '14',
        text: 'Snow and thunder'
    },
    heavysnowandthunder: {
        symbol: '34',
        text: 'Heavy snow and thunder'
    },
    fog: {
        symbol: '15',
        text: 'Fog'
    }
};

/**
 * Draw the weather symbols on top of the temperature series. The symbols are
 * fetched from yr.no's MIT licensed weather symbol collection.
 * https://github.com/YR/weather-symbols
 */
Meteogram.prototype.drawWeatherSymbols = function (chart) {

    chart.series[0].data.forEach((point, i) => {
        if (this.resolution > 36e5 || i % 2 === 0) {

            const [symbol, specifier] = this.symbols[i].split('_'),
                icon = Meteogram.dictionary[symbol].symbol +
                    ({ day: 'd', night: 'n' }[specifier] || '');

            if (Meteogram.dictionary[symbol]) {
                chart.renderer
                    .image(
                        'https://cdn.jsdelivr.net/gh/nrkno/yr-weather-symbols' +
                            `@8.0.1/dist/svg/${icon}.svg`,
                        point.plotX + chart.plotLeft - 8,
                        point.plotY + chart.plotTop - 30,
                        30,
                        30
                    )
                    .attr({
                        zIndex: 5
                    })
                    .add();
            } else {
                console.log(symbol);
            }
        }
    });
};


/**
 * Draw blocks around wind arrows, below the plot area
 */
Meteogram.prototype.drawBlocksForWindArrows = function (chart) {
    const xAxis = chart.xAxis[0];

    for (
        let pos = xAxis.min, max = xAxis.max, i = 0;
        pos <= max + 36e5; pos += 36e5,
        i += 1
    ) {

        // Get the X position
        const isLast = pos === max + 36e5,
            x = Math.round(xAxis.toPixels(pos)) + (isLast ? 0.5 : -0.5);

        // Draw the vertical dividers and ticks
        const isLong = this.resolution > 36e5 ?
            pos % this.resolution === 0 :
            i % 2 === 0;

        chart.renderer
            .path([
                'M', x, chart.plotTop + chart.plotHeight + (isLong ? 0 : 28),
                'L', x, chart.plotTop + chart.plotHeight + 32,
                'Z'
            ])
            .attr({
                stroke: chart.options.chart.plotBorderColor,
                'stroke-width': 1
            })
            .add();
    }

    // Center items in block
    chart.get('windbarbs').markerGroup.attr({
        translateX: chart.get('windbarbs').markerGroup.translateX + 8
    });

};

/**
 * Build and return the Highcharts options structure
 */
<?php
				if($theme=="dark"){
					$innerGraph = "#fff";
				}
				else{
					$innerGraph = "#000";
				}
			?>
Meteogram.prototype.getChartOptions = function () {
    return {
        chart: {
            renderTo: this.container,
            marginBottom: 70,
            marginRight: 40,
            marginTop: 50,
            plotBorderWidth: 1,
            height: 310,
            alignTicks: false,
            scrollablePlotArea: {
                minWidth: 720
            }
        },

        defs: {
            patterns: [{
                id: 'precipitation-error',
                path: {
                    d: [
                        'M', 3.3, 0, 'L', -6.7, 10,
                        'M', 6.7, 0, 'L', -3.3, 10,
                        'M', 10, 0, 'L', 0, 10,
                        'M', 13.3, 0, 'L', 3.3, 10,
                        'M', 16.7, 0, 'L', 6.7, 10
                    ].join(' '),
                    stroke: '#68CFE8',
                    strokeWidth: 1
                }
            }]
        },

        title: {
            text: 'Meteogram',
            align: 'left',
            style: {
                whiteSpace: 'nowrap',
                textOverflow: 'ellipsis',
				color: "<?php echo $innerGraph?>",
            }
        },

        credits: {
			text: '<?php echo lang('data source','c')?> yr.no',
			href: "https://yr.no",
			position: {
				x: -40
			}
		},

        tooltip: {
            shared: true,
            useHTML: true,
            headerFormat:
                '<small>{point.x:%A, %b %e, %H:%M} - {point.point.to:%H:%M}</small><br>' +
                '<b>{point.point.symbolName}</b><br>'

        },

        xAxis: [{ // Bottom X axis
            type: 'datetime',
			style: {
				color: "<?php echo $innerGraph?>",
			},
            tickInterval: 2 * 36e5, // two hours
            minorTickInterval: 36e5, // one hour
            tickLength: 0,
            gridLineWidth: 1,
            gridLineColor: '<?php echo $innerGraph?>',
            startOnTick: false,
            endOnTick: false,
            minPadding: 0,
            maxPadding: 0,
            offset: 30,
            showLastLabel: true,
            labels: {
                format: '{value:%H}',
				style: {
					color: "<?php echo $innerGraph?>",
				},
            },
            crosshair: true,
			lineColor: '<?php echo $innerGraph?>',
			minorGridLineColor: '#888888',
			tickColor: '<?php echo $innerGraph?>',
        }, { // Top X axis
            linkedTo: 0,
            type: 'datetime',
            tickInterval: 24 * 3600 * 1000,
            labels: {
                format: '{value:<span style="font-size: 12px; font-weight: bold">%a</span> %b %e}',
                align: 'left',
                x: 3,
                y: -5,
				style: {
					color: "<?php echo $innerGraph?>",
				},
            },
            opposite: true,
            tickLength: 20,
            gridLineWidth: 1,
			gridLineColor: '<?php echo $innerGraph?>',
        }],

        yAxis: [{ // temperature axis
            title: {
                text: null
            },
            labels: {
                format: '{value}° <?php echo $displayTempUnits?>',
                style: {
                    fontSize: '10px',
					color: "<?php echo $innerGraph?>",
                },
                x: -3
            },
            plotLines: [{ // zero plane
                value: 0,
                color: '<?php echo $innerGraph?>',
                width: 1,
                zIndex: 2
            }],
            maxPadding: 0.3,
            minRange: 8,
            tickInterval: 1,
            gridLineColor: '<?php echo $innerGraph?>',
			style: {
                    fontSize: '10px',
                    color: "<?php echo $innerGraph?>"
				},

        }, { // precipitation axis
            title: {
                text: null
            },
            labels: {
                enabled: false
            },
            gridLineWidth: 0,
            tickLength: 0,
            minRange: 10,
			gridLineColor: '<?php echo $innerGraph?>',
            min: 0,
			style: {
                    fontSize: '10px',
                    color: "<?php echo $innerGraph?>"
				},

        }, { // Air pressure
            allowDecimals: false,
            title: { // Title on top of axis
                text: '<?php echo $displayPressUnits?>',
                offset: 0,
                align: 'high',
                rotation: 0,
                style: {
                    fontSize: '10px',
                    color: "<?php echo $innerGraph?>"
				},
                textAlign: 'left',
                x: 3
            },
            labels: {
                style: {
                    fontSize: '8px',
                    color: "<?php echo $innerGraph?>"},
                y: 2,
                x: 3
            },
            gridLineWidth: 0,
            opposite: true,
            showLastLabel: false,
			gridLineColor: '<?php echo $innerGraph?>',
			style: {
                    fontSize: '10px',
                    color: "<?php echo $innerGraph?>"
				},
        }],

        legend: {
            enabled: false
        },

        plotOptions: {
            series: {
                pointPlacement: 'between'
            }
        },


        series: [{
            name: 'Temperature',
            data: this.temperatures,
            type: 'spline',
            marker: {
                enabled: false,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            },
            tooltip: {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
                    '{series.name}: <b>{point.y}°<?php echo $displayTempUnits?></b><br/>'
            },
            zIndex: 1,
            color: '#FF3333',
            negativeColor: '#48AFE8'
        }, {
            name: 'Precipitation',
            data: this.precipitationsError,
            type: 'column',
            color: 'url(#precipitation-error)',
            yAxis: 1,
            groupPadding: 0,
            pointPadding: 0,
            tooltip: {
                valueSuffix: ' <?php echo $displayRainUnits?>',
                pointFormat: '<span style="color:{point.color}">\u25CF</span> ' +
                    '{series.name}: <b>{point.minvalue} <?php echo $displayRainUnits?> - {point.maxvalue} <?php echo $displayRainUnits?></b><br/>'
            },
            grouping: false,
            dataLabels: {
                enabled: this.hasPrecipitationError,
                filter: {
                    operator: '>',
                    property: 'maxValue',
                    value: 0
                },
                style: {
                    fontSize: '8px',
                    color: 'gray'
                }
            }
        }, {
            name: 'Precipitation',
            data: this.precipitations,
            type: 'column',
            color: '#68CFE8',
            yAxis: 1,
            groupPadding: 0,
            pointPadding: 0,
            grouping: false,
            dataLabels: {
                enabled: !this.hasPrecipitationError,
                filter: {
                    operator: '>',
                    property: 'y',
                    value: 0
                },
                style: {
                    fontSize: '8px',
                    color: 'gray'
                }
            },
            tooltip: {
                valueSuffix: ' <?php echo $displayRainUnits?>'
            }
        }, {
            name: 'Air pressure',
            color: Highcharts.getOptions().colors[2],
            data: this.pressures,
            marker: {
                enabled: false
            },
            shadow: false,
            tooltip: {
                valueSuffix: ' <?php echo $displayPressUnits?>'
            },
            dashStyle: 'shortdot',
            yAxis: 2
        }, {
            name: 'Wind',
            type: 'windbarb',
            id: 'windbarbs',
            color: Highcharts.getOptions().colors[1],
            lineWidth: 1.5,
            data: this.winds,
            vectorLength: 18,
            yOffset: -15,
            tooltip: {
                valueSuffix: ' <?php echo $displayWindUnits?>'
            }
        }]
    };
};

/**
 * Post-process the chart from the callback function, the second argument
 * Highcharts.Chart.
 */
Meteogram.prototype.onChartLoad = function (chart) {

    this.drawWeatherSymbols(chart);
    this.drawBlocksForWindArrows(chart);

};

/**
 * Create the chart. This function is called async when the data file is loaded
 * and parsed.
 */
Meteogram.prototype.createChart = function () {
    this.chart = new Highcharts.Chart(this.getChartOptions(), chart => {
        this.onChartLoad(chart);
    });
};

Meteogram.prototype.error = function () {
    document.getElementById('loading').innerHTML =
        '<i class="fa fa-frown-o"></i> Failed loading data, please try again later';
};

/**
 * Handle the data. This part of the code is not Highcharts specific, but deals
 * with yr.no's specific data format
 */
Meteogram.prototype.parseYrData = function () {

    let pointStart;

    if (!this.json) {
        return this.error();
    }

    // Loop over hourly (or 6-hourly) forecasts
    this.json.properties.timeseries.forEach((node, i) => {

        const x = Date.parse(node.time),
            nextHours = node.data.next_1_hours || node.data.next_6_hours,
            symbolCode = nextHours && nextHours.summary.symbol_code,
            to = node.data.next_1_hours ? x + 36e5 : x + 6 * 36e5;

        if (to > pointStart + 48 * 36e5) {
            return;
        }

        // Populate the parallel arrays
        this.symbols.push(nextHours.summary.symbol_code);

		temp = convertTemp(node.data.instant.details.air_temperature);

        this.temperatures.push({
            x,
            y: temp,
            // custom options used in the tooltip formatter
            to,
            symbolName: Meteogram.dictionary[
                symbolCode.replace(/_(day|night)$/, '')
            ].text
        });

		rain = convertPrecip(nextHours.details.precipitation_amount);
        this.precipitations.push({
            x,
            y: rain
        });

		wind = convertWind(node.data.instant.details.wind_speed);

        if (i % 2 === 0) {
            this.winds.push({
                x,
                value: wind,
                direction: node.data.instant.details.wind_from_direction
            });
        }

		press = convertPress(node.data.instant.details.air_pressure_at_sea_level);
        this.pressures.push({
            x,
            y: press
        });

        if (i === 0) {
            pointStart = (x + to) / 2;
        }
    });

    // Create the chart when the data is loaded
    this.createChart();
};
// End of the Meteogram protype


// On DOM ready...

// Set the hash to the yr.no URL we want to parse
if (!location.hash) {
					location.hash = 'https://api.met.no/weatherapi/locationforecast/2.0/compact?lat=<?php echo $stationLat?>&lon=<?php echo $stationLon?>';
				}
				const url = location.hash.substr(1);
$.ajax({url: url, 
					success: json => {
						window.meteogram = new Meteogram(json, 'containerMeteogram');
					},
					headers: {
						// Override the Content-Type to avoid preflight problems with CORS
						// in the Highcharts demos
						'Content-Type': 'text/plain'
					}
				});
function convertTemp(val){
	unit = "<?php echo strtolower($displayTempUnits)?>";
	if(unit == "F"){
		val = val * 1.8 + 32;
	}
	val = Math.round((val * 10))/10;
	return val;
}
function convertPrecip(val){
	unit = "<?php echo strtolower($displayRainUnits)?>";
	if(unit == "in"){
		val = val * 0.0393701;
		val = Math.round((val * 100))/100;
	}
	return val;
}
function convertWind(val){
	unit = "<?php echo strtolower($displayWindUnits)?>";
	if(unit == "ms"){
		val = val / 3.6;
		val = Math.round((val * 10))/10;
	}
	if(unit == "mph"){
		val = val * 0.621371;
		val = Math.round((val * 10))/10;
	}
	if(unit == "kt"){
		val = val * 0.539957;
		val = Math.round((val * 10))/10;
	}
	return val;
}
function convertPress(val){
	unit = "<?php echo strtolower($displayPressUnits)?>";
	if(unit == "inhg"){
		val = val *0.0295299830714;
		val = Math.round((val * 100))/100;
	}
	if(unit == "mmhg"){
		val = val *0.75006375541921;
		val = Math.round((val * 10))/10;
	}
}
		</script>
	
