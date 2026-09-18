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
	#	Interactive Graphs
	#
	# 	A script that displays interactive graphs where user can select 
	#	parameter, time span and grouping.
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
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("interactive graph",'c')?></title>
		<?php metaHeader()?>
		
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcore.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxdatetimeinput.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcalendar.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/globalize.js"></script>
		
		<style>
			.parameter{
				padding: 4px;
				font-size:1.7em;
				opacity: 0.6;
				cursor: pointer;
			}
			.parameter:hover{
				opacity: 0.9;
			}
			.calendar{
				padding: 4px;
				font-size:1.7em;
				opacity: 0.6;
				cursor: pointer;
			}
			.calendar:hover{
				opacity: 0.9;
			}
			.dates{
				opacity: 0.6;
				cursor: pointer;
				font-variant: small-caps;
			}
			.dates:hover{
				opacity: 0.9;
			}
			.interval{
				text-align: center;
				width: 8%;
			}
			.tooltipster-default {
				border: 2px solid #<?php echo $color_schemes[$design]['900']?>;
				background: #<?php echo $color_schemes[$design2]['700']?>;
				color: #<?php echo $color_schemes[$design2]['font700']?>;
			}
			.jqx-widget-content {
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif!important;
				color: #<?php echo $color_schemes[$design2]['font900']?>!important;
				border-color: #<?php echo $color_schemes[$design2]['900']?>!important;
				background: #<?php echo $color_schemes[$design2]['800']?>!important;
			}
			.jqx-widget-header {
				border-color: #<?php echo $color_schemes[$design]['900']?>!important;
				background: #<?php echo $color_schemes[$design]['300']?>!important;
				color: #<?php echo $color_schemes[$design]['font300']?>!important;
			}
			.jqx-calendar-cell-today{
				color: #<?php echo $color_schemes[$design]['font900']?>!important;
				background-color: #<?php echo $color_schemes[$design]['900']?>!important;
				border: 1px solid #<?php echo $color_schemes[$design]['200']?>!important;
			}
			.ui-widget-header {
				border: 0px solid #<?php echo $color_schemes[$design]['900']?>!important;
				background: transparent;
			}	
			.spinner {
				position: relative;
				color: #<?php echo $color_schemes[$design2]['300']?>;
				display: inline-block;
				width:  1em; 
				z-index:10;
				height: 1em; 
				font-size: 260px; 
				border-bottom: 10px solid; 
				vertical-align: middle;
				overflow: hidden;  
				text-indent: 100%;
				-webkit-animation: 0.9s spinner linear infinite;
				   -moz-animation: 0.9s spinner linear infinite;
						animation: 0.9s spinner linear infinite;
				&,
				&:after {
					border-radius: 100%;
				}       
				&:after {
					content: "";
					position: absolute;
					top:    0;
					right:  0;
					bottom: 0;
					left:   0;
					border: 1px solid; 
					opacity: 0.5;
				}
			}
			@-webkit-keyframes spinner {
				to {
					-webkit-transform: rotate(360deg);
				}
			}
			@-moz-keyframes spinner {
				to {
					-moz-transform: rotate(360deg);
				}
			}
			@keyframes spinner {

				to {
					transform: rotate(360deg);
				}
			}
			.spinner, .spinner:after {
			  border-radius: 100%;
			}
			#negativeColorSwitcher{
				font-size:1.7em;
				opacity: 0.8;
				cursor: pointer;
			}
			#negativeColorSwitcher:hover{
				opacity: 1;
			}
		</style>
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/jqx.base.css" media="screen" />
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
		<br>
		<h1><?php echo lang("interactive graph",'c')?></h1>
		<br>
		<div style="position:relative;width:95%;margin: 0 auto;">
			<table style="width:98%;margin: 0 auto;position:absolute;top:5px;z-index:10">
				<tr>
					<td style="text-align:left" colspan="">
						<span class="mticon-cold tooltip" id="negativeColorSwitcher" onclick="colorNegative()" title="<?php echo lang('highlight negative','c')?>"></span>
					</td>
				</tr>
			</table>
			<div style="width:100%;text-align:center;position:absolute;top:50;left:0;z-index:10">
				<span class="spinner" id="spinner"><?php echo lang("loading",'c')?>…</span>
			</div>
			<div id="graph" style="min-width: 400px; height: 60%; margin: 0 auto;z-index:5">
			</div>
		</div>
		<br>
		<input type="hidden" id="chosenParameter" value="<?php echo $defaultGraphParameter ?>">
		<input type="hidden" id="chosenValue" value="all">
		<input type="hidden" id="chosenInterval" value="<?php echo $defaultGraphInterval ?>">
		<table style="margin-left:auto;margin-right:auto">
			<tr>
				<td>
					<span class="mticon-temp parameter tooltip" id="parameterT" onclick="parameterSet('T')" title="<?php echo lang("temperature",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-humidity parameter tooltip" id="parameterH" onclick="parameterSet('H')" title="<?php echo lang("humidity",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-dewpoint parameter tooltip" id="parameterD" onclick="parameterSet('D')" title="<?php echo lang("dewpoint",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-pressure parameter tooltip" id="parameterP" onclick="parameterSet('P')" title="<?php echo lang("pressure",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-wind parameter tooltip" id="parameterW" onclick="parameterSet('W')" title="<?php echo lang("wind speed",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-rain parameter tooltip" id="parameterR" onclick="parameterSet('R')" title="<?php echo lang("precipitation",'c')?>"></span>
				</td>
				<?php if($solarSensor){?>
					<td>
						<span class="mticon-sun parameter tooltip" id="parameterS" onclick="parameterSet('S')" title="<?php echo lang("solar radiation",'c')?>"></span>
					</td>
				<?php } ?>
				<td style="border-left:2px solid #<?php echo $color_schemes[$design]['400']?>">
					<span class="calendar tooltip" id="valuesAll" onclick="valueSet('all')" title="<?php echo lang("all",'c')?>" style="opacity:1;font-size:1em">
						<?php echo strtoupper(lang("all",'c'))?>
					</span>
				</td>
				<td>
					<span class="mticon-1h calendar tooltip" id="valuesHour" onclick="valueSet('h')" title="<?php echo lang("hourly averages",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-calendar-day calendar tooltip" id="valuesDay" onclick="valueSet('d')" title="<?php echo lang("daily averages",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-calendar-month calendar tooltip" id="valuesMonth" onclick="valueSet('m')" title="<?php echo lang("monthly averages",'c')?>"></span>
				</td>
				<td class="interval" style="border-left:2px solid #<?php echo $color_schemes[$design]['400']?>">
					<span class="dates" id="interval1h" onclick="intervalSet('1h')">
						1<?php echo lang("hAbbr",'')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="interval24h" onclick="intervalSet('24h')">
						24<?php echo lang("hAbbr",'')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervaltoday" onclick="intervalSet('today')">
						<?php echo lang("today",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalyesterday" onclick="intervalSet('yesterday')">
						<?php echo lang("yesterday",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalthisweek" onclick="intervalSet('thisweek')">
						<?php echo lang("this week",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalthismonth" onclick="intervalSet('thismonth')">
						<?php echo lang("this month",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervallastweek" onclick="intervalSet('lastweek')">
						<?php echo lang("last week",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervallastmonth" onclick="intervalSet('lastmonth')">
						<?php echo lang("last month",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalCustom" onclick="intervalSet('custom')">
						<?php echo lang("custom",'c')?>
					</span>
				</td>
			</tr>
		</table>
		</div>
		<?php include($baseURL."footer.php")?>
		<div id="customDialog" style="text-align:center">
			<?php echo lang("from",'c')?>:
			<div id='customFrom'></div>
			<br>
			<?php echo lang("to",'c')?>:
			<div id='customTo'></div>
			<br>
			<input type="button" class="button" value="<?php echo lang("ok",'u')?>" id="customShow">
		</div>
		<script>
			function colorNegative(){
				hcChart = $("#graph").highcharts();
				for(i=0;i<hcChart.series.length;i++){
					hcChart.series[i].update({
						color: "#ff7373",
						negativeColor: "#265cff",
						softThreshold: <?php echo $freezingLine?>
					});
				}
			}
			function parameterSet(x){
				$("#chosenParameter").val(x);
				graph();
				$(".parameter").css("opacity","0.6");
				$(".parameter").mouseover(function() {
					$(this).css("opacity","1");
				}).mouseout(function() {
					$(this).css("opacity","0.6");
				});
				if(x=="T"){
					$("#parameterT").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="H"){
					$("#parameterH").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="D"){
					$("#parameterD").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="P"){
					$("#parameterP").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="W"){
					$("#parameterW").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="R"){
					$("#parameterR").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="S"){
					$("#parameterS").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
			}
			function valueSet(x){
				$("#chosenValue").val(x);
				graph();
				$(".calendar").css("opacity","0.6");
				$(".calendar").mouseover(function() {
					$(this).css("opacity","1");
				}).mouseout(function() {
					$(this).css("opacity","0.6");
				});
				if(x=="all"){
					$("#valuesAll").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="h"){
					$("#valuesHour").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="d"){
					$("#valuesDay").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="m"){
					$("#valuesMonth").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
			}
			function intervalSet(x){
				$("#chosenInterval").val(x);
				graph();
				$(".dates").css("opacity","0.6");
				$(".dates").mouseover(function() {
					$(this).css("opacity","1");
				}).mouseout(function() {
					$(this).css("opacity","0.6");
				});
				if(x=="1h"){
					$("#interval1h").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="24h"){
					$("#interval24h").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="today"){
					$("#intervaltoday").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="yesterday"){
					$("#intervalyesterday").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="thisweek"){
					$("#intervalthisweek").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});}
				if(x=="lastweek"){
					$("#intervallastweek").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="thismonth"){
					$("#intervalthismonth").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="lastmonth"){
					$("#intervallastmonth").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="custom"){
					$("#intervalcustom").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
			}
			function graph() {
				$('#spinner').show();
				// Global graph options
				Highcharts.setOptions({
					global: {
						useUTC: true
					},
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>']
					}		
				});
				// Individual graphs options based on parameter
				optionsT = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
						text:  '<?php echo lang('temperature',"c") ?>'
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: {
						title: {
							text: '<?php echo lang("temperature","c") ?>'
						},
						labels: {
							format: '{value} °<?php echo $displayTempUnits ?>'
						}
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}								
					},
					tooltip: {
						shared: true
					},
					series: [
						{
							name: '',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>"
						},
						{
							name: '',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot'
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						}
					]
				};
				optionsH = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('humidity','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: {
						title: {
							text: '<?php echo lang('humidity','c') ?>'
						},
						labels: {
							format: '{value} %'
						},
						max: 100
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}								
					},
					series: [
						{
							name: '<?php echo lang('humidity','c') ?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>"
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						}
					]
				};
				optionsD = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('dewpoint','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: {
						title: {
							text: '<?php echo lang('dewpoint','c') ?>'
						},
						labels: {
							format: '{value} <?php echo $displayTempUnits?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}								
					},
					series: [
						{
							name: '<?php echo lang('dewpoint','c') ?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>"
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						}
					]
				};
				optionsP = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
								text:  '<?php echo lang('pressure','c') ?>'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					legend: {
								enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: {
						title: {
							text: '<?php echo lang('pressure','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayPressUnits) ?>'
						}
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},	
						areasplinerange:{
							fillOpacity: 0.5
						}							
					},
					series: [
						{
							name: '<?php echo lang('pressure','c') ?>',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>"
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						}
					]
				};	
				<?php 
					if($solarSensor){
				?>
						optionsS = {
							chart : {
								renderTo : 'graph',
								type : 'spline',
								zoomType: 'x'
							},
							credits: {
								text: '<?php echo $highChartsCreditsText?>',
								href: '<?php echo $pageURL.$path?>'
							},
							title: {
								text:  '<?php echo lang('solar radiation','c') ?>'
							},
							legend: {
								enabled: false
							},
							xAxis: {
								type: 'datetime',
								title: {
									text: null
								},
								dateTimeLabelFormats: {
									millisecond: '%H:%M:%S.%L',
									second: '%H:%M:%S',
									minute: '%H:%M',
									hour: '<?php echo $graphTimeFormat ?>',
									day: '<?php echo $graphDateFormat ?>',
									week: '<?php echo $graphDateFormat ?>',
									month: '%b / %y',
									year: '%Y'
								}	
							},
							yAxis: {
								title: {
									text: '<?php echo lang('solar radiation','c') ?>'
								},
								labels: {
									format: '{value} W/m2'
								},
								min: 0
							},
							plotOptions: {
								series: {
									animation: {
										duration: 3000
									},
									marker: {
										enabled: false
									}
								},
								areasplinerange:{
									fillOpacity: 0.5
								}						
							},
							series: [
								{
									name: '<?php echo lang('solar radiation','c') ?>',
									data: [],
									color: "#<?php echo $color_schemes[$design]['font700']?>"
								},
								{	
									type: 'areasplinerange',
									name: '<?php echo lang('range','c')?>',
									color: "#<?php echo $color_schemes[$design]['font700']?>",
									data: []
								}
							]
						};
				<?php 
					}
				?>
				optionsW = {
					chart : {
						renderTo : 'graph',
						type : 'spline',
						zoomType: 'x'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
								text:  '<?php echo lang('wind','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: {
						title: {
							text: '<?php echo lang('wind','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayWindUnits) ?>'
						},
						min: 0
					},
					tooltip: {
						shared: true
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}							
					},
					series: [
						{
							name: '',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>"
						},
						{
							name: '',
							data: [],
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							dashStyle: 'ShortDot'
						},
						{	
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: []
						}
					]
				};

				optionsR = {
				   chart : {
						renderTo : 'graph',
						type : 'area',
						zoomType: 'x'
					},
					credits: {
						text: '<?php echo $highChartsCreditsText?>',
						href: '<?php echo $pageURL.$path?>'
					},
					title: {
								text:  '<?php echo lang('cumulative daily precipitation','c') ?>'
					},
					legend: {
								enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}	
					},
					yAxis: [
						{
							title: {
								text: '<?php echo lang('precipitation','c') ?>'
							},
							labels: {
								format: '{value} <?php echo $displayRainUnits ?>'
							},
							min: 0
						},
						{
							title: {
								text: '<?php echo lang('cumulative precipitation','c')?>'
							},
							labels: {
								format: '{value} <?php echo $displayRainUnits ?>'
							},
							min: 0,
							opposite: true
						},
					],
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						area: {
							fillOpacity: 0.3
						}			
					},
					series: [
						{
							name: '<?php echo lang('precipitation','c') ?>',
							type: 'column',
							color: "#<?php echo $color_schemes[$design]['font700']?>",
							data: [],
							zIndex: 3,
						},
						{
							name: '<?php echo lang('cumulative precipitation','c') ?>',
							color: "#<?php echo $color_schemes[$design2]['300']?>",
							type: 'areaspline',
							data: [],
							yAxis: 1,
							zIndex: 2,
						},

				]
				};
				
				// get selected parameters
				parameter = $("#chosenParameter").val();
				value = $("#chosenValue").val();
				interval = $("#chosenInterval").val();
				from = $("#customFrom").val();
				to = $("#customTo").val();
				$('#spinner').show();
				$("#negativeColorSwitcher").hide();
				// get data based on user selection
				if(parameter=="T"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {	
							$("#negativeColorSwitcher").show();
							optionsT.series[0].name = json['name1'];
							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryT = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryT];
									rowDate.push(value);
								}						
								optionsT.series[0].data = rowDate;
							}
							
							optionsT.series[1].name = json['name2'];
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryT = eval(json['data2'][i][1]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryT];
									rowDate.push(value);
								}
								optionsT.series[1].data = rowDate;
							}
							
							rowDate = new Array();
							if(("data3" in json) ){
								for(i=0; i < json['data3'].length; i++){
									temporaryT = eval(json['data3'][i][1]);
									temporaryT2 = eval(json['data3'][i][2]);
									temporaryDate = json['data3'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryT,temporaryT2];
									rowDate.push(value);
								}
								optionsT.series[2].data = rowDate;
							}
							chart = new Highcharts.Chart(optionsT);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="H"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							optionsH.series[0].name = json['name1'];
							optionsH.series[1].name = json['name2'];
							
							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryH = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryH];
									rowDate.push(value);
								}						
								optionsH.series[0].data = rowDate;
							}
							
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryH = eval(json['data2'][i][1]);
									temporaryH2 = eval(json['data2'][i][2]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryH,temporaryH2];
									rowDate.push(value);
								}
								optionsH.series[1].data = rowDate;
							}
							chart = new Highcharts.Chart(optionsH);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="D"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							$("#negativeColorSwitcher").show();
							optionsD.series[0].name = json['name1'];
							optionsD.series[1].name = json['name2'];
							
							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryH = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryH];
									rowDate.push(value);
								}						
								optionsD.series[0].data = rowDate;
							}
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryD = eval(json['data2'][i][1]);
									temporaryD2 = eval(json['data2'][i][2]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryD,temporaryD2];
									rowDate.push(value);
								}
								optionsD.series[1].data = rowDate;
							}
							chart = new Highcharts.Chart(optionsD);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="P"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							optionsP.series[0].name = json['name1'];
							optionsP.series[1].name = json['name2'];
							
							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryP = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryP];
									rowDate.push(value);
								}						
								optionsP.series[0].data = rowDate;
							}
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryP = eval(json['data2'][i][1]);
									temporaryP2 = eval(json['data2'][i][2]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryP,temporaryP2];
									rowDate.push(value);
								}
								optionsP.series[1].data = rowDate;
							}
							
							chart = new Highcharts.Chart(optionsP);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="S"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							optionsS.series[0].name = json['name1'];
							optionsS.series[1].name = json['name2'];
							
							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryS = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryS];
									rowDate.push(value);
								}						
								optionsS.series[0].data = rowDate;
							}
							
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryS = eval(json['data2'][i][1]);
									temporaryS2 = eval(json['data2'][i][2]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryS,temporaryS2];
									rowDate.push(value);
								}
								optionsS.series[1].data = rowDate;
							}
							chart = new Highcharts.Chart(optionsS);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="R"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							rowDate = new Array();
							rowDate2 = new Array();
							if(("data" in json) ){
								for(i=0; i < json['data'].length; i++){
									temporaryR = eval(json['data'][i][1]);
									temporaryDate = json['data'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryR];
									rowDate.push(value);
								}						
								optionsR.series[0].data = rowDate;
							}
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryR = eval(json['data2'][i][1]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryR];
									rowDate2.push(value);
								}						
								optionsR.series[1].data = rowDate2;
							}
							chart = new Highcharts.Chart(optionsR);
							$('#spinner').hide();
						}
					});
				}
				if(parameter=="W"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval+"&from="+from+"&to="+to,
						dataType : 'json',
						success : function (json) {
							optionsW.series[0].name = json['name1'];
							optionsW.series[1].name = json['name2'];

							rowDate = new Array();
							if(("data1" in json) ){
								for(i=0; i < json['data1'].length; i++){
									temporaryW = eval(json['data1'][i][1]);
									temporaryDate = json['data1'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryW];
									rowDate.push(value);
								}						
								optionsW.series[0].data = rowDate;
							}
							rowDate = new Array();
							if(("data2" in json) ){
								for(i=0; i < json['data2'].length; i++){
									temporaryG = eval(json['data2'][i][1]);
									temporaryDate = json['data2'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);						
									value = [datum,temporaryG];
									rowDate.push(value);
								}
								optionsW.series[1].data = rowDate;
							}
							rowDate = new Array();
							if(("data3" in json) ){
								for(i=0; i < json['data3'].length; i++){
									temporaryG = eval(json['data3'][i][1]);
									temporaryG2 = eval(json['data3'][i][2]);
									temporaryDate = json['data3'][i][0];
									year = temporaryDate[0];
									month = eval(temporaryDate[1]-1);
									day = temporaryDate[2];
									hour = temporaryDate[3];
									minute = temporaryDate[4];
									datum = Date.UTC(year, month, day, hour, minute);	
									value = [datum,temporaryG,temporaryG2];
									rowDate.push(value);
								}
								optionsW.series[2].data = rowDate;
							}
							chart = new Highcharts.Chart(optionsW);
							$('#spinner').hide();
						}
					});
				}
			}
			$(document).ready(function() {
				$('#spinner').hide();
				$("#parameter<?php echo $defaultGraphParameter?>").css("opacity","1");
				$("#interval<?php echo $defaultGraphInterval?>").css("opacity","1");
				$("#customFrom").jqxDateTimeInput({ 
					width: '300px', 
					height: '30px', 
					formatString: 'yyyy-MM-dd HH:mm'
				});
				$("#customTo").jqxDateTimeInput({ 
					width: '300px', 
					height: '30px',  
					formatString: 'yyyy-MM-dd HH:mm'
				});
				$( "#customDialog" ).dialog({
					autoOpen: false,
					show: {
						effect: "puff",
						duration: 500
					},
					hide: {
						effect: "puff",
						duration: 500
					},
					height: 340,
					width: 350,
					position:{
						my: 'top', 
						at: 'top+30%'
					}
				});
				$( "#intervalCustom" ).click(function() {$( "#customDialog" ).dialog( "open" );});
				$( "#customShow" ).click(function() {
					graph();
					$( "#customDialog" ).dialog( "close" );
				});
				graph();
			});
		</script>
		<?php include("../../css/highcharts.php");?>
	</body>
</html>