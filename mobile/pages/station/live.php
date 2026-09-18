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
	#	Current Conditions Gauges
	#
	# 	A script which shows current conditions in a form of gauges.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	$defaultState = "show"; // default state of the labels showing values - show/hide
	
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	include("../../../scripts/stats.php");
	
	$result = mysqli_query($con,"
		SELECT *
		FROM alldata 
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$temperature = number_format(convertT($row['T']),1,".","");
		$humidity = $row['H'];
		$wind = number_format(convertW($row['W']),1,".","");
		$gust = number_format(convertW($row['G']),1,".","");
		$dew = number_format(convertT($row['D']),1,".","");
		$apparent =number_format(convertT($row['A']),1,".","");
		$solar = $row['S'];
		if($displayPressUnits=="in"){
			$pressure = number_format(convertP($row['P']),2,".","");
		}
		else{
			$pressure = number_format(convertP($row['P']),1,".","");
		}
		if($displayRainUnits=="in"){
			$rain = number_format(convertR($row['R']),2,".","");
		}
		else{
			$rain = number_format(convertR($row['R']),1,".","");
		}
		$direction = $row['B'];
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo lang("live","u")?></title>
	<?php metaHeader()?>
	<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/jqx.base.css" media="screen" />
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcore.js"></script>
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxdraw.js"></script>
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxgauge.js"></script>
	
	<style>
		.jqx-gauge-label, .jqx-gauge-caption{
			fill: #<?php echo $color_schemes[$design]['100']?>;
			color: #<?php echo $color_schemes[$design]['100']?>;
			font-size: 80%;
			font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
		}
		.table{
			width: 100%;
			padding: 0px;
			border-spacing: 0px;
			margin-top: 5px;
			margin-bottom: 5px;
		}
		.table th, td{
			padding: 2px;
			text-align: center;
		}
		.table td:first-child,th:first-child  {
			text-align: center;
		}
		.spinner {
			position: relative;
			color: #<?php echo $color_schemes[$design2]['300']?>;
			display: inline-block;
			width:  1em; 
			height: 1em; 
			font-size: 30px; 
			border-bottom: 3px solid; 
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
	</style>

</head>
<body>
	<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php")?>
		</div>
		<div id="main" style="text-align:center">
		<div style="width:100%;text-align:center;margin-top:auto;margin-bottom:auto;position:relative">
			<br>
			<input type="button" class="button2" value="<?php echo lang("show values","c")?>" id="valueToggle" style="padding-left:3px">
			<input type="button" class="button2" value="<?php echo lang("update",'c')?>" id="update" style="padding-left:3px" onclick="doUpdate()">
			<span class="spinner" id="updateSpinner">Loading…</span>
			<br>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;width:30%;color:#<?php echo $color_schemes[$design]['100']?>">
				<div id="gaugeApparent" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<br>
				<div>
					<div id="apparentValue">
						<span id="apparentValueNumber" style="font-size:1.2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $apparent?>
						</span> 
						 °<?php echo $displayTempUnits?>
					</div>
				</div>
				<div id="apparentLabel">
					<?php echo lang("apparent temperature",'c')?>
				</div>
			</div>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;width:30%;color:#<?php echo $color_schemes[$design]['100']?>">
				<div id="gaugeTemp" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<br>
				<div>
					<div id="tempValue">
						<span id="tempValueNumber" style="font-size:1.2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $temperature?>
						</span> 
						 °<?php echo $displayTempUnits?>
					</div>
				</div>
				<div id="tempLabel">
					<?php echo lang("temperature",'c')?>
				</div>
			</div>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;width:30%;color:#<?php echo $color_schemes[$design]['100']?>">
				<div id="gaugeDew" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<br>
				<div>
					<div id="dewValue">
						<span id="dewValueNumber" style="font-size:1.2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $dew?>
						</span>
						°<?php echo $displayTempUnits?>
					</div>
				</div>
				<div id="dewLabel">
					<?php echo lang("dew point",'c')?>
				</div>
			</div>
			<br>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em;text-align:center">
				<div id="gaugeHumidity" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<div style="color:#<?php echo $color_schemes[$design]['100']?>">
					<div id="humidityValue">
						<span id="humidityValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $humidity?>
						</span>
						%
					</div>
				</div> 
			</div>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em;text-align:center">
				<div id="gaugePress" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<div style="color:#<?php echo $color_schemes[$design]['100']?>">
					<div id="pressValue">
						<span id="pressValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $pressure?>
						</span>
						<?php echo $displayPressUnits?>
					</div>
				</div> 
			</div>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em;text-align:center">
				<div id="gaugeWind" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<div style="color:#<?php echo $color_schemes[$design]['100']?>">
					<div id="windValue">
						<span id="windValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $wind?>
						</span>
						<?php echo $displayWindUnits?>
					</div>
				</div> 
			</div>
			<div style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em;text-align:center">
				<div id="gaugeGust" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
				<div style="color:#<?php echo $color_schemes[$design]['100']?>">
					<div id="gustValue">
						<span id="gustValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
							<?php echo $gust?>
						</span>
						<?php echo $displayWindUnits?>
					</div>
				</div> 
			</div>
			<br>
			<table style="width:100%;text-align:center">
				<?php if($solarSensor){ ?>
				<tr>
					<td style="width:33%">
						<div id="gaugeRain" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
					</td>
					<td style="width:33%">
						<div id="gaugeDirection" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
					</td>
					<td style="width:33%">
						<div id="gaugeSolar" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
					</td>
				</tr>
				<tr>
					<td style="text-align:center">
						<div style="color:#<?php echo $color_schemes[$design]['100']?>">
							<div id="rainValue"> 
								<span id="rainValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
									<?php echo $rain?>
								</span> <?php echo $displayRainUnits?>
							</div>
						</div>
					</td>
					<td style="text-align:center">
						<div style="color:#<?php echo $color_schemes[$design]['100']?>;font-size:2em">
							<div id="directionValue"> 
								<span id="directionValueNumber" style="font-size:1em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
									<?php echo $direction?>
								</span>°
							</div>
						</div>
					</td>
					<td style="text-align:center">
						<div style="color:#<?php echo $color_schemes[$design]['100']?>">
							<div id="solarValue"> 
								<span id="solarValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
									<?php echo $solar?>
								</span> W/m<span style="vertical-align: top; font-size: 0.9em">2</span>
							</div>
						</div>
					</td>
				</tr>
				<?php } else {?>
					<tr>
					<td style="width:50%">
						<div id="gaugeRain" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
					</td>
					<td style="width:50%">
						<div id="gaugeDirection" style="margin-left:auto;margin-right:auto;display:inline-block;font-size:0.9em"></div>
					</td>
				</tr>
				<tr>
					<td style="text-align:center">
						<div style="color:#<?php echo $color_schemes[$design]['100']?>">
							<div id="rainValue"> 
								<span id="rainValueNumber" style="font-size:2em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
									<?php echo $rain?>
								</span> <?php echo $displayRainUnits?>
							</div>
						</div>
					</td>
					<td style="text-align:center">
						<div style="color:#<?php echo $color_schemes[$design]['100']?>;font-size:2em">
							<div id="directionValue"> 
								<span id="directionValueNumber" style="font-size:1em;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>">
									<?php echo $direction?>
								</span>°
							</div>
						</div>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
	<?php include("../../footer.php")?>
<script type="text/javascript">
        $(document).ready(function () {       
            $('#gaugeHumidity').jqxGauge({
				width: 200,
				radius: 100,
				height: 170,
				border: { 
					visible: false 
				},
                cap: { 
					visible: 0.04,
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
                caption: { 
					offset: [0, -5], 
					value: '<?php echo lang("humidity",'c')?><br>%', 
					position: 'bottom' 
				},
                value: 0,
				easing: 'linear',
				pointer:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
				min: 0,
				max: 100,
                style: { 
					stroke: '#<?php echo $color_schemes[$design]['900']?>', 
					'stroke-width': '1px', 
					fill: '#<?php echo $color_schemes[$design]['900']?>' 
				},
                animationDuration: 2500,
                labels: { 
					visible: true, 
					position: 'inside', 
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						color: '#<?php echo $color_schemes[$design]['100']?>'
					}
				},
                ticksMinor: { 
					interval: 2.5, 
					size: '5%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				},
                ticksMajor: { 
					interval: 5, 
					size: '10%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '3px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				}
            });
        
            $('#gaugePress').jqxGauge({
				width: 200,
				radius: 100,
				height: 170,
				border: { visible: false },
                cap: { 
					visible: 0.04,
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
                caption: { 
					offset: [0, -5], 
					value: '<?php echo lang("pressure",'c')?><br><?php echo $displayPressUnits?>', 
					position: 'bottom' 
				},
                value: 0,
				easing: 'linear',
				pointer:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
				min: <?php echo $pressureGaugeMin?>,
				max: <?php echo $pressureGaugeMax?>,
                style: { 
					stroke: '#<?php echo $color_schemes[$design]['900']?>', 
					'stroke-width': '1px', 
					fill: '#<?php echo $color_schemes[$design]['900']?>' 
				},
                animationDuration: 2000,
                labels: { 
					visible: true, 
					position: 'inside', 
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						color: '#<?php echo $color_schemes[$design]['100']?>' 
					}
				},
                ticksMinor: { 
					interval: <?php echo round(($pressureGaugeMax-$pressureGaugeMin)/20,1)?>, 
					size: '5%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					}
				},
                ticksMajor: { 
					interval: <?php echo round(($pressureGaugeMax-$pressureGaugeMin)/10,1)?>, 
					size: '10%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '3px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				}
            });
              
            $('#gaugeWind').jqxGauge({
				width: 200,
				radius: 100,
				height: 170,
				border: { visible: false },
                cap: { 
					visible: 0.04,
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
                caption: { 
					offset: [0, -5], 
					value: '<?php echo lang('wind','c')?><br><?php echo $displayWindUnits?>', 
					position: 'bottom' 
				},
                value: 0,
				easing: 'linear',
				pointer:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
				min: 0,
				max: <?php echo $windGaugeMax?>,
                style: { 
					stroke: '#<?php echo $color_schemes[$design]['900']?>', 
					'stroke-width': '1px', 
					fill: '#<?php echo $color_schemes[$design]['900']?>' 
				},
                animationDuration: 2500,
                labels: { 
					visible: true, 
					position: 'inside', 
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						color: '#<?php echo $color_schemes[$design]['100']?>' 
					}
				},
                ticksMinor: { 
					interval: <?php echo round(($windGaugeMax-0)/20,1)?>, 
					size: '5%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				},
                ticksMajor: { 
					interval: <?php echo round(($windGaugeMax-0)/10,1)?>, 
					size: '10%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '3px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				}
            });
     
            $('#gaugeGust').jqxGauge({
				width: 200,
				radius: 100,
				height: 170,
				border: { 
					visible: false 
				},
                cap: { 
					visible: 0.04,
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
                caption: { 
					offset: [0, -5], 
					value: '<?php echo lang("wind gust",'c')?><br><?php echo $displayWindUnits?>', 
					position: 'bottom' 
				},
                value: 0,
				easing: 'linear',
				pointer:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
				min: 0,
				max: <?php echo $gustGaugeMax?>,
                style: { 
					stroke: '#<?php echo $color_schemes[$design]['900']?>', 
					'stroke-width': '1px', 
					fill: '#<?php echo $color_schemes[$design]['900']?>' 
				},
                animationDuration: 2500,
                labels: { 
					visible: true, 
					position: 'inside', 
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						color: '#<?php echo $color_schemes[$design]['100']?>' 
					}
				},
                ticksMinor: { 
					interval: 0.25, 
					size: '5%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				},
                ticksMajor: { 
					interval:0.5, 
					size: '10%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '3px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				}
            });   
			
			<?php 
				if($solarSensor){
			?>		
				$('#gaugeSolar').jqxGauge({
					width: 250,
					radius: 125,
					height: 200,
					border: { 
						visible: false 
					},
					cap: { 
						visible: 0.04,
						style: { 
							stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
							'stroke-width': '1px', 
							fill: '#<?php echo $color_schemes[$design2]['300']?>' 
						}
					},
					caption: { 
						offset: [0, 5], 
						value: '<?php echo lang("solar radiation",'c')?><br>W/m2', 
						position: 'bottom' 
					},
					value: 0,
					easing: 'linear',
					pointer:{
						style: { 
							stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
							'stroke-width': '1px', 
							fill: '#<?php echo $color_schemes[$design2]['300']?>' 
						}
					},
					min: 0,
					max: <?php echo $solarGaugeMax?>,
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['900']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design]['900']?>' 
					},
					animationDuration: 2500,
					labels: { 
						visible: true, 
						position: 'inside', 
						style: { 
							stroke: '#<?php echo $color_schemes[$design]['100']?>', 
							'stroke-width': '1px', 
							color: '#<?php echo $color_schemes[$design]['100']?>' 
						},
						interval: 250
					},
					ticksMinor: { 
						interval: 100, 
						size: '5%',
						style: { 
							stroke: '#<?php echo $color_schemes[$design]['100']?>', 
							'stroke-width': '1px', 
							fill: '#<?php echo $color_schemes[$design]['100']?>' 
						} 
					},
					ticksMajor: { 
						interval: 200, 
						size: '10%',
						style: { 
							stroke: '#<?php echo $color_schemes[$design]['100']?>', 
							'stroke-width': '3px', 
							fill: '#<?php echo $color_schemes[$design]['100']?>' 
						} 
					}
				});  
			<?php 
				}
			?>

			$('#gaugeTemp').jqxLinearGauge({
				width: 250,
				height: 300,
				scaleLength: '80%',
                orientation: 'vertical',
                labels: { 
					interval: 20, 
					formatValue: function (value, position) {
						if(value===<?php echo $tempGaugeMin?> || value===<?php echo $tempGaugeMax?>){
							if(position==="near"){
								return value + "°<?php echo $displayTempUnits?>";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°F";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°C";
								}
							}
						}
						else{
							if(position==="near"){
								return value + "°";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°";
								}
							}
						}
					}
                },
				scaleStyle: {
					stroke: '#<?php echo $color_schemes[$design]['100']?>'			
				},
				background:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['700']?>', 
						fill: '#<?php echo $color_schemes[$design]['700']?>'
					},
					showGradient: false,
					visible: false
				},
                ticksMajor: { 
					size: '10%', 
					interval: 20 
				},
                ticksMinor: { 
					size: '5%', 
					interval: 5, 
					style: { 
						'stroke-width': 1, 
						stroke: '#<?php echo $color_schemes[$design]['100']?>'
					} 
				},
				showRanges: false,
                min: <?php echo $tempGaugeMin?>,
				max: <?php echo $tempGaugeMax?>,
                pointer: { 
					size: '6%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				}
            });
			
			$('#gaugeApparent').jqxLinearGauge({
				width: 250,
				height: 300,
				scaleLength: '80%',
                orientation: 'vertical',
                labels: { 
					interval: 20, 
					formatValue: function (value, position) {
						if(value===<?php echo $tempGaugeMin?>  || value===<?php echo $tempGaugeMax?>){
							if(position==="near"){
								return value + "°<?php echo $displayTempUnits?>";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°F";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°C";
								}
							}
						}
						else{
							if(position==="near"){
								return value + "°";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°";
								}
							}
						}
					}
                },
				scaleStyle: {
					stroke: '#<?php echo $color_schemes[$design]['100']?>'			
				},
				background:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['700']?>', 
						fill: '#<?php echo $color_schemes[$design]['700']?>'
					},
					showGradient: false,
					visible: false
				},
                ticksMajor: { 
					size: '10%', 
					interval: 20 
				},
                ticksMinor: { 
					size: '5%', 
					interval: 5, 
					style: { 
						'stroke-width': 1, 
						stroke: '#<?php echo $color_schemes[$design]['100']?>'
					} 
				},
				showRanges: false,
                min: <?php echo $tempGaugeMin?>,
				max: <?php echo $tempGaugeMax?>,
                pointer: { 
					size: '6%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				}
            });
			
			$('#gaugeDew').jqxLinearGauge({
				width: 250,
				height: 300,
				scaleLength: '80%',
                orientation: 'vertical',
                labels: { 
					interval: 20, 
					formatValue: function (value, position) {
						if(value===<?php echo $tempGaugeMin?>  || value===<?php echo $tempGaugeMax?>){
							if(position==="near"){
								return value + "°<?php echo $displayTempUnits?>";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°F";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°C";
								}
							}
						}
						else{
							if(position==="near"){
								return value + "°";
							}
							if(position==="far"){
								if("<?php echo $displayTempUnits?>"==="C"){
									value = (value * 1.8) + 32;
									value = Math.round(value*10)/10;
									return value + "°";
								}
								if("<?php echo $displayTempUnits?>"==="F"){
									value = (value - 32) * (5/9);
									value = Math.round(value*10)/10;
									return value + "°";
								}
							}
						}
					}
                },
				scaleStyle: {
					stroke: '#<?php echo $color_schemes[$design]['100']?>'			
				},
				background:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['700']?>', 
						fill: '#<?php echo $color_schemes[$design]['700']?>'
					},
					showGradient: false,
					visible: false
				},
                ticksMajor: { 
					size: '10%', 
					interval: 20 
				},
                ticksMinor: { 
					size: '5%', 
					interval: 5, 
					style: { 
						'stroke-width': 1, 
						stroke: '#<?php echo $color_schemes[$design]['100']?>'
					} 
				},
				showRanges: false,
                min: <?php echo $tempGaugeMin?>,
				max: <?php echo $tempGaugeMax?>,
                pointer: { 
					size: '6%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				}
            });
			
			$('#gaugeRain').jqxLinearGauge({
				width: 200,
				height: 250,
				scaleLength: '80%',
                orientation: 'vertical',
                labels: { 
					interval: 5, 
					formatValue: function (value, position) {
						if (value === 0){
							if (position === 'far') {
								value = Math.round(0.393701 * value)/10;
								return value + ' in';
							}
							if (position === 'near') {
								return 'mm ' + value;
							}
						}
						else{
							if (position === 'far') {
								value = Math.round(0.393701 * value)/10;
								return value;
							}
							if (position === 'near') {
								return value;
							}
						}
					}
                },
				scaleStyle: {
					stroke: '#<?php echo $color_schemes[$design]['100']?>'				
				},
				background:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['700']?>', 
						fill: '#<?php echo $color_schemes[$design]['700']?>'
					},
					showGradient: false,
					visible: false
				},
                ticksMajor: { 
					size: '10%', 
					interval: 20 
				},
                ticksMinor: { 
					size: '5%', 
					interval: 5, 
					style: { 
						'stroke-width': 1, 
						stroke: '#<?php echo $color_schemes[$design]['100']?>'
					} 
				},
				showRanges: false,
                max: <?php echo $rainGaugeMax?>,
				min: 0,
                pointer: { 
					size: '6%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				}
            });
			digits = {
                0: '<?php echo lang('directionN','u')?>',
                22.5: '<?php echo lang('directionNNE','u')?>',
                45: '<?php echo lang('directionNE','u')?>',
                67.5: '<?php echo lang('directionENE','u')?>',
                90: '<?php echo lang('directionE','u')?>',
                112.5: '<?php echo lang('directionESE','u')?>',
                135: '<?php echo lang('directionSE','u')?>',
                157.5: '<?php echo lang('directionSSE','u')?>',
                180: '<?php echo lang('directionS','u')?>',
                202.5: '<?php echo lang('directionSSW','u')?>',
                225: '<?php echo lang('directionSW','u')?>',
                247.5: '<?php echo lang('directionWSW','u')?>',
				270: '<?php echo lang('directionW','u')?>',
				292.5: '<?php echo lang('directionWNW','u')?>',
				315: '<?php echo lang('directionNW','u')?>',
				337.5: '<?php echo lang('directionNNW','u')?>',
				360: '<?php echo lang('directionN','u')?>'
            };

			$('#gaugeDirection').jqxGauge({
				width: 240,
				radius: 125,
				height: 250,
				border: { 
					visible: false 
				},
                cap: { 
					visible: 0.04,
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
                caption: { 
					offset: [0, -25], 
					value: '', 
					position: 'bottom' 
				},
                value: 0,
				easing: 'linear',
				pointer:{
					style: { 
						stroke: '#<?php echo $color_schemes[$design2]['300']?>', 
						'stroke-width': '1px', 
						fill: '#<?php echo $color_schemes[$design2]['300']?>' 
					}
				},
				min: 0,
				max: 360,
				startAngle: -90,
                endAngle: 270,
                style: { 
					stroke: '#<?php echo $color_schemes[$design]['900']?>', 
					'stroke-width': '1px', 
					fill: '#<?php echo $color_schemes[$design]['900']?>' 
				},
                animationDuration: 2500,
                labels: { 
					visible: true, 
					interval: 22.5,
					position: 'outside', 
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '1px', 
						color: '#<?php echo $color_schemes[$design]['100']?>' 
					},
					distance: '35%',
                    formatValue: function (val) {
                        if (val === 0) {
                            return '';
                        }
                        return digits[val];
                    }
				},
                ticksMinor: { 
					interval: 11.25, 
					size: '5%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '3px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				},
                ticksMajor: { 
					interval: 22.5, 
					size: '10%',
					style: { 
						stroke: '#<?php echo $color_schemes[$design]['100']?>', 
						'stroke-width': '4px', 
						fill: '#<?php echo $color_schemes[$design]['100']?>' 
					} 
				}
            });   
		
			$('#apparentValue').hide();
			$('#tempValue').hide();
			$('#dewValue').hide();
			$('#humidityValue').hide();
			$('#pressValue').hide();
			$('#windValue').hide();
			$('#gustValue').hide();
			$('#directionValue').hide();
			$('#rainValue').hide();
			<?php if($solarSensor){ ?>
				$('#solarValue').hide();
			<?php } ?>
			$('#updateSpinner').hide();
			
			$("#valueToggle").click(function(){
				if($("#valueToggle").val()==="<?php echo lang("show values",'c')?>"){
					$("#valueToggle").val("<?php echo lang("hide values",'c')?>");
				}
				else{
					$("#valueToggle").val("<?php echo lang("show values",'c')?>");
				}
				$("#apparentValue" ).slideToggle( "slow", function() {
				});
				$("#tempValue" ).slideToggle( "slow", function() {
				});
				$("#dewValue" ).slideToggle( "slow", function() {
				});
				$("#humidityValue" ).slideToggle( "slow", function() {
				});
				$("#pressValue" ).slideToggle( "slow", function() {
				});
				$("#windValue" ).slideToggle( "slow", function() {
				});
				$("#gustValue" ).slideToggle( "slow", function() {
				});
				$("#rainValue" ).slideToggle( "slow", function() {
				});
				$("#directionValue" ).slideToggle( "slow", function() {
				});
				<?php if($solarSensor){ ?>
					$("#solarValue" ).slideToggle( "slow", function() {
					});
				<?php } ?>
			});
			
			//set values initially and hide value divs
			
			$('#gaugeHumidity').jqxGauge('setValue', <?php echo $humidity?>);
			$('#gaugeWind').jqxGauge('setValue', <?php echo $wind?>);
			$('#gaugeGust').jqxGauge('setValue', <?php echo $gust?>);
			$('#gaugePress').jqxGauge('setValue', <?php echo $pressure?>);
			$('#gaugeTemp').jqxLinearGauge('value', <?php echo $temperature?>);
			$('#gaugeApparent').jqxLinearGauge('value', <?php echo $apparent?>);
			$('#gaugeDew').jqxLinearGauge('value', <?php echo $dew?>);
			$('#gaugeRain').jqxLinearGauge('value', <?php echo $rain?>);
			$('#gaugeDirection').jqxGauge('setValue', <?php echo $direction?>);
			<?php 
				if($solarSensor){ 
					if($solar==""){
						$solar=0;
					}
				?>
				$('#gaugeSolar').jqxGauge('setValue', <?php echo $solar?>);
			<?php 
				} 
			?>
			
			<?php
				if($defaultState=="show"){
					echo "$('#valueToggle').click();";
				}
			?>
        });
    </script>
	<script>
		function doUpdate(){
			$("#update").hide();
			$("#updateSpinner").show();
			$.ajax({
				url : "liveAjax.php",
				dataType : 'json',
				success : function (json) {
					$('#gaugeHumidity').jqxGauge('setValue', json['humidity']);
					$('#humidityValueNumber').html(json['humidity']);
					$('#gaugeWind').jqxGauge('setValue', json['wind']);
					$('#windValueNumber').html(json['wind']);
					$('#gaugeGust').jqxGauge('setValue', json['gust']);
					$('#gustValueNumber').html(json['gust']);
					$('#gaugePress').jqxGauge('setValue', json['pressure']);
					$('#pressValueNumber').html(json['pressure']);
					$('#gaugeTemp').jqxLinearGauge('value', json['temperature']);
					$('#tempValueNumber').html(json['temperature']);
					$('#gaugeApparent').jqxLinearGauge('value', json['apparent']);
					$('#apparentValueNumber').html(json['apparent']);
					$('#gaugeDew').jqxLinearGauge('value', json['dew']);
					$('#dewValueNumber').html(json['dew']);
					$('#gaugeRain').jqxLinearGauge('value', json['rain']);
					$('#rainValueNumber').html(json['rain']);
					$('#gaugeDirection').jqxGauge('setValue', json['direction']);
					$('#directionValueNumber').html(json['direction']);
					<?php if($solarSensor){ ?>
						$('#gaugeSolar').jqxGauge('setValue', json['solar']);
						$('#solarValueNumber').html(json['solar']);
					<?php } ?>
					$("#update").show();
					$("#updateSpinner").hide();
				}
			});
		};
	</script>
</body>
</html>