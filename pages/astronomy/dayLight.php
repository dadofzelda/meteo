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
	#	Day/night visualization
	#
	# 	A script showing current daytime on a world map. User can select 
	#	a particular time and date and see the current sun illumination of
	#	the Earth. Also includes possibility to run animations over longer time
	#	intervals.
	#
	############################################################################
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
		#main{
				text-align:center;
		}
		.night {
				stroke: black;
				fill: black;
				fill-opacity: .6;
		}

		.land {
				fill: #777777;
		}
		.AnyTime-cur-btn {
			  border: 1px solid #<?php echo $color_schemes[$design2]['300']?>!important;
			  background-color: #<?php echo $color_schemes[$design2]['900']?>!important;
			  color: #<?php echo $color_schemes[$design2]['300']?>!important;
		}
		.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
			  border: 1px solid #404040;
			  background: #<?php echo $color_schemes[$design2]['900']?>!important;
			  color: #cccccc;
		}
		.boundary {
			  fill: none;
			  stroke: #fff;
			  stroke-width: .8px;
			  stroke-linejoin: round;
			  stroke-linecap: round;
			  pointer-events: none;
		}
		.controlIcon{
			font-size: 1.4em;
			opacity: 0.8;
			cursor: pointer;
			padding: 3px;
		}
		.controlIcon:hover{
			opacity: 1;
		}
		</style>
		<script src="d3.v3.min.js"></script>
		<script src="topojson.v1.min.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/anytime.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/moment.js"></script>
		<link href="<?php echo $pageURL.$path?>css/anytime.css" rel="stylesheet">
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<h1><?php echo lang('daylight','c');?></h1>
			<div id="dateDiv"></div>
			<script>
				var width = (screen.width*0.8);
					height = (screen.height*0.5);

				var π = Math.PI,
					radians = π / 180,
					degrees = 180 / π;

				var projection = d3.geo.equirectangular()
					.translate([width/2, height/2])
					.scale((screen.width*0.1))
					.precision(.1);

				var circle = d3.geo.circle()
					.angle(90);

				var path = d3.geo.path()
					.projection(projection);

				var svg = d3.select(".textDiv").append("svg")
					.attr("width", width)
					.attr("height", height);

				d3.json("world-50m.json", function(error, world) {
					svg.append("path")
						  .datum(topojson.feature(world, world.objects.land))
						  .attr("class", "land")
						  .attr("d", path);
					svg.append("path")
						  .datum(topojson.mesh(world, world.objects.countries, function(a, b) { return a !== b; }))
						  .attr("class", "boundary")
						  .attr("d", path);

				  var night = svg.append("path")
					  .attr("class", "night")
					  .attr("d", path);

					night.datum(circle.origin(antipode(solarPosition(new Date)))).attr("d", path);
					currentDate = new Date();
					formattedDate = currentDate.toLocaleString();
					$("#dateDiv").html("<h2 style='font-size:1.2em'>"+ formattedDate + "</h2>");
					
					function redraw() {			
						current = eval($("#current").val());
						view = new Date(current);
					
						original = $("#datepicker1_hidden").val();
						if(current>=$("#datepicker2_hidden").val()){
							$("#current").val($("#datepicker1_hidden").val());
						}
						
						night.datum(circle.origin(antipode(solarPosition(view)))).attr("d", path);
						
						interval = $("#interval").val();
						
						if(interval=="1h"){
							step = 1000*60*60;
						}
						if(interval=="10min"){
							step = 1000*60*10;
						}
						if(interval=="1min"){
							step = 1000*60*1;
						}
						if(interval=="30min"){
							step = 1000*60*30;
						}
						if(interval=="3h"){
							step = 1000*60*60*3;
						}
						if(interval=="6h"){
							step = 1000*60*60*6;
						}
						if(interval=="12h"){
							step = 1000*60*60*12;
						}
						if(interval=="1d"){
							step = 1000*60*60*24;
						}
						if(interval=="1w"){
							step = 1000*60*60*24*7;
						}
						
						temporary = eval($("#current").val());
						$("#current").val(eval(temporary + step));

						formatted = view.toLocaleString();
						$("#dateDiv").html("<h3>"+ formatted + "</h3>");
					}
				  
					$("#start").click(function() {
						if($("#datepicker1_hidden").val()>=$("#datepicker2_hidden").val()){
							alert("The ending date must be after the start date.");
							return false;
						}
						if (typeof myVar !== 'undefined') {
							clearInterval(myVar);
						}
						$("#current").val($("#datepicker1_hidden").val());
						speed = eval($("#speed").val())*100;
						myVar = setInterval(function(){ 
							redraw() 
						}, speed);
						$("#pause").val("Pause");
					});
					$("#pause").click(function() {
						if($("#pause").val()=="Pause"){
							clearInterval(myVar);
							$("#pause").val("Resume");
						}
						else if($("#pause").val()=="Resume"){
							speed = $("#speed").val()*100;
							myVar = setInterval(function(){ 
								redraw() 
							}, speed);
							$("#pause").val("Pause");
						} 
						else{}
					});
					
					$("#speed").change(function(){
						if (typeof myVar !== 'undefined') {
							clearInterval(myVar);
						}
						speed = eval($("#speed").val())*100;
						myVar = setInterval(function(){ 
							redraw() 
						}, speed);
					});
				  
				});

				d3.select(self.frameElement).style("height", height + "px");

				function antipode(position) {
				  return [position[0] + 180, -position[1]];
				}

				function solarPosition(time) {
				  var centuries = (time - Date.UTC(2000, 0, 1, 12)) / 864e5 / 36525, // since J2000
					  longitude = (d3.time.day.utc.floor(time) - time) / 864e5 * 360 - 180;
				  return [
					longitude - equationOfTime(centuries) * degrees,
					solarDeclination(centuries) * degrees
				  ];
				}

				function equationOfTime(centuries) {
				  var e = eccentricityEarthOrbit(centuries),
					  m = solarGeometricMeanAnomaly(centuries),
					  l = solarGeometricMeanLongitude(centuries),
					  y = Math.tan(obliquityCorrection(centuries) / 2);
				  y *= y;
				  return y * Math.sin(2 * l)
					  - 2 * e * Math.sin(m)
					  + 4 * e * y * Math.sin(m) * Math.cos(2 * l)
					  - 0.5 * y * y * Math.sin(4 * l)
					  - 1.25 * e * e * Math.sin(2 * m);
				}

				function solarDeclination(centuries) {
				  return Math.asin(Math.sin(obliquityCorrection(centuries)) * Math.sin(solarApparentLongitude(centuries)));
				}

				function solarApparentLongitude(centuries) {
				  return solarTrueLongitude(centuries) - (0.00569 + 0.00478 * Math.sin((125.04 - 1934.136 * centuries) * radians)) * radians;
				}

				function solarTrueLongitude(centuries) {
				  return solarGeometricMeanLongitude(centuries) + solarEquationOfCenter(centuries);
				}

				function solarGeometricMeanAnomaly(centuries) {
				  return (357.52911 + centuries * (35999.05029 - 0.0001537 * centuries)) * radians;
				}

				function solarGeometricMeanLongitude(centuries) {
				  var l = (280.46646 + centuries * (36000.76983 + centuries * 0.0003032)) % 360;
				  return (l < 0 ? l + 360 : l) / 180 * π;
				}

				function solarEquationOfCenter(centuries) {
				  var m = solarGeometricMeanAnomaly(centuries);
				  return (Math.sin(m) * (1.914602 - centuries * (0.004817 + 0.000014 * centuries))
					  + Math.sin(m + m) * (0.019993 - 0.000101 * centuries)
					  + Math.sin(m + m + m) * 0.000289) * radians;
				}

				function obliquityCorrection(centuries) {
				  return meanObliquityOfEcliptic(centuries) + 0.00256 * Math.cos((125.04 - 1934.136 * centuries) * radians) * radians;
				}

				function meanObliquityOfEcliptic(centuries) {
				  return (23 + (26 + (21.448 - centuries * (46.8150 + centuries * (0.00059 - centuries * 0.001813))) / 60) / 60) * radians;
				}

				function eccentricityEarthOrbit(centuries) {
				  return 0.016708634 - centuries * (0.000042037 + 0.0000001267 * centuries);
				}

			</script>
			<br><br>
			<div id="optionsDiv" style="text-align:center;margin:0 auto">
			<input type="hidden" id="current" name="current" value="">
			<span class="fa fa-play tooltip controlIcon" style="font-size:1.8em" title="<?php echo lang('start','c')?>" id="start"></span>
			<span class="fa fa-pause tooltip controlIcon" style="font-size:1.8em" title="<?php echo lang('pause','c')?>" id="pause"></span>
			<br><br>
			<table style="width:98%;margin:0 auto;table-layout:fixed;max-width:800px">
				<tr>
					<td>
						<span class="fa fa-tachometer tooltip" style="font-size:1.8em" title="<?php echo lang('speed','c')?>"></span>
					</td>
					<td>
						<span class="fa fa-arrows-h tooltip" style="font-size:1.8em" title="<?php echo lang('interval','c')?>"></span>
					</td>
					<td>
						<span class="fa fa-hourglass-start tooltip" style="font-size:1.8em" title="<?php echo lang('from','c')?>"></span>
					</td>
					<td>
						<span class="fa fa-hourglass-end tooltip" style="font-size:1.8em" title="<?php echo lang('to','c')?>"></span>
					</td>
				</tr>
				<tr>
					<td>
						<select id="speed" class="button">
							<option value="20" >
								1
							</option>
							<option value="10" selected>
								2
							</option>
							<option value="5">
								3
							</option>
							<option value="1">
								4
							</option>
							<option value="0.5">
								5
							</option>
						</select>
					</td>
					<td>
						<select id="interval" class="button">
							<option value="1min" >
								1 <?php echo lang('minAbbr','l')?>
							</option>
							<option value="10min">
								10 <?php echo lang('minAbbr','l')?>
							</option>
							<option value="30min" selected>
								30 <?php echo lang('minAbbr','l')?>
							</option>
							<option value="1h">
								1 <?php echo lang('hAbbr','l')?>
							</option>
							<option value="3h">
								3 <?php echo lang('hAbbr','l')?>
							</option>
							<option value="6h">
								6 <?php echo lang('hAbbr','l')?>
							</option>
							<option value="12h">
								12 <?php echo lang('hAbbr','l')?>
							</option>
							<option value="1d">
								1 <?php echo lang('day','l')?>
							</option>
							<option value="1w">
								1 <?php echo lang('week','l')?>
							</option>
						</select>
					</td>
					<td>
						<input id="datepicker1" class="button">
					</td>
					<td>
						<input id="datepicker2" class="button">
					</td>
				</tr>
			</table>
			<br><br>
			<input id="datepicker1_hidden" type="hidden">
			<input id="datepicker2_hidden" type="hidden">
			</div>
			<script>
				$("#datepicker1").AnyTime_picker({ 
					format: "%Y-%m-%d %H:%i",
					labelTitle: "r",
				});
				  
				$("#datepicker1").change(function(){
					x = moment($("#datepicker1").val(), "YYY-MM-DD hh:mm").toDate();
					x = x.getTime();			
					$("#datepicker1_hidden").val(x);
				});
				
				$("#datepicker2").AnyTime_picker({ 
					format: "%Y-%m-%d %H:%i",
					labelTitle: "r",
				});
				
				$("#datepicker2").change(function(){
					x2 = moment($("#datepicker2").val(), "YYY-MM-DD hh:mm").toDate();
					x2 = x2.getTime();
					$("#datepicker2_hidden").val(x2);
				});
			</script>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
	