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
	#	Moonphase, full moon and new moon calculation and visualization
	#
	# 	A script to calculate and visualize moon phase at user defined date, 
	#	also showing annual tables of new and full Moons.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("moon","c")?></title>
		<?php metaHeader()?>
		<style>
			#moonIcon{
				margin-left:auto;
				margin-right:auto;
				text-align: center;
			}
			.ui-datepicker-inline, .ui-datepicker, .ui-widget, .ui-widget-content, .ui-helper-clearfix, .ui-corner-all{
				margin-left:auto;
				margin-right:auto;
			}
			.ui-datepicker, .ui-datepicker-title{
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
			}
			.ui-widget-header {
			  border: 0px solid #404040;
			  background-color: transparent!important;
			  color: #<?php echo $color_schemes[$design2]['font900']?>;
			  font-weight: bold;
			}
			.ui-widget-content {
				border: 0px solid #404040;
				color: #eeeeee;
			}
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['600']?>;
				color: #<?php echo $color_schemes[$design2]['font600']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['700']?>;
				color: #<?php echo $color_schemes[$design2]['font700']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			<?php
				if($stationLat<0){
			?>
				#mainImg{
					-webkit-transform: rotate(-180deg);
					-moz-transform: rotate(-180deg);
					-ms-transform: rotate(-180deg);
					-o-transform: rotate(-180deg);
					filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=6);
				}
			<?php
				}
			?>
			.controlIcon{
				font-size: 3em;
				opacity: 0.8;
				cursor: pointer;
				padding-left:5px;
				padding-right:5px;
			}
			.controlIcon:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body onload="load('now');calcMoonPhase('now')">
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<h1><?php echo lang('moon phase','w')?></h1>
			<table style="width:98%;margin:0 auto">
				<tr>
					<td style="text-align:center;vertical-align: top;">
						
						<div style="margin-left:auto;margin-right:auto;text-align:center">
							<div id="dayPicker" style="margin-left:auto;margin-right:auto"></div>
						</div>
						<br>
						<input type="button" id="animStart" value="<?php echo lang('start','c')?>" class="button">
						<input type="button" id="animStop" value="<?php echo lang('stop','c')?>" class="button">
						<select id="animSpeed" class="button">
							<option value="200">
								<?php echo lang('very fast','l')?>
							</option>
							<option value="500">
								<?php echo lang('fast','l')?>
							</option>
							<option value="1000" selected>
								<?php echo lang('normalSpeed','l')?>
							</option>
							<option value="2500">
								<?php echo lang('slow','l')?>
							</option>
							<option value="5000">
								<?php echo lang('very slow','l')?>
							</option>
						</select>
						<select id="animStep" class="button">
							<option value="Day" selected>
								<?php echo lang('day','c')?>
							</option>
							<option value="7 days">
								<?php echo lang('week','c')?>
							</option>
							<option value="Month">
								<?php echo lang('month','c')?>
							</option>
							<option value="Year">
								<?php echo lang('year','c')?>
							</option>
						</select>
						<br>
						<div id="moonIcon">
						</div>
						<br>
						<div id="percentIllumination"></div>
					</td>
				</tr>
				<tr>
					<td style="text-align:center;vertical-align: top">
						<table style="width:100%">
							<tr>
								<td colspan="2" style="text-align:center">
									<table style="margin:0 auto">
										<tr>
											<td>
												<span class="fa fa-angle-double-left controlIcon" id="minus10Year"></span>
											</td>
											<td>
												<span class="fa fa-angle-left controlIcon" id="minusYear"></span>
											</td>
											<td>
												<input id="currentYear" class="button" value="" size="4" style="font-size:1.2em;text-align:center">
											</td>
											<td>
												<span class="fa fa-angle-right controlIcon" id="plusYear"></span>
											</td>
											<td>	
												<span class="fa fa-angle-double-right controlIcon" id="plus10Year"></span>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="width:50%;vertical-align: top;">
									<div id="newMoons" style="width:100%;font-size:1.5vw"></div>
								</td>
								<td style="width:50%;vertical-align: top">
									<div id="fullMoons" style="width:100%;font-size:1.5vw"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		</div>
		<script>
			function load(d){
				now = false;
				if(d=="now"){
					d = new Date();
					now = true;
				}
				else{
					d = new Date(d*1);
				}
				illumination = getMoonIllumination(d)['fraction']*10000;
				illumination = Math.round(illumination);
				illumination = illumination / 100;
				illumination = illumination + " %";
				phase = getMoonIllumination(d)['phase'];
				intervals = 118;
				if(phase<0.5){
					phase = phase + 0.5;
				}
				else{
					phase = phase - 0.5;
				}
				moonIcon = Math.round((phase/(1/intervals)));
				
				
				$('#percentIllumination').html(illumination);
				$('#moonIcon').html("<img src='<?php echo $pageURL.$path?>imgs/moonImgs/"+moonIcon+".png' style='width:40%' id='mainImg'>");
			}
		</script>
		<script>
			var PI   = Math.PI,
				sin  = Math.sin,
				cos  = Math.cos,
				tan  = Math.tan,
				asin = Math.asin,
				atan = Math.atan2,
				acos = Math.acos,
				rad  = PI / 180;
			var dayMs = 1000 * 60 * 60 * 24,
				J1970 = 2440588,
				J2000 = 2451545;
			var e = rad * 23.4397; // obliquity of the Earth

			function toJulian(date) { return date.valueOf() / dayMs - 0.5 + J1970; }
			function toDays(date)   { return toJulian(date) - J2000; }
			function getMoonIllumination(date) {

				var d = toDays(date),
					s = sunCoords(d),
					m = moonCoords(d),

					sdist = 149598000, // distance from Earth to Sun in km

					phi = acos(sin(s.dec) * sin(m.dec) + cos(s.dec) * cos(m.dec) * cos(s.ra - m.ra)),
					inc = atan(sdist * sin(phi), m.dist - sdist * cos(phi)),
					angle = atan(cos(s.dec) * sin(s.ra - m.ra), sin(s.dec) * cos(m.dec) -
							cos(s.dec) * sin(m.dec) * cos(s.ra - m.ra));

				return {
					fraction: (1 + cos(inc)) / 2,
					phase: 0.5 + 0.5 * inc * (angle < 0 ? -1 : 1) / Math.PI,
					angle: angle
				};
			};
			function sunCoords(d) {

				var M = solarMeanAnomaly(d),
					L = eclipticLongitude(M);

				return {
					dec: declination(L, 0),
					ra: rightAscension(L, 0)
				};
			}
			function solarMeanAnomaly(d) { return rad * (357.5291 + 0.98560028 * d); }

			function eclipticLongitude(M) {

				var C = rad * (1.9148 * sin(M) + 0.02 * sin(2 * M) + 0.0003 * sin(3 * M)), // equation of center
					P = rad * 102.9372; // perihelion of the Earth

				return M + C + P + PI;
			}
			function rightAscension(l, b) { return atan(sin(l) * cos(e) - tan(b) * sin(e), cos(l)); }
			function declination(l, b)    { return asin(sin(b) * cos(e) + cos(b) * sin(e) * sin(l)); }

			function azimuth(H, phi, dec)  { return atan(sin(H), cos(H) * sin(phi) - tan(dec) * cos(phi)); }
			function altitude(H, phi, dec) { return asin(sin(phi) * sin(dec) + cos(phi) * cos(dec) * cos(H)); }

			function siderealTime(d, lw) { return rad * (280.16 + 360.9856235 * d) - lw; }

			</script>
			<script>
			//-----Utility Funtions------------------------------------------------------------
			function INT(  n )  { return Math.floor( n ); } 	//Emulates BASIC's INT Funtion
			function POW2( n )  { return Math.pow( n, 2 );}	//Square a number
			function noSubmit() { return false; }  //Prevent form submission
			function moonCoords(d) { // geocentric ecliptic coordinates of the moon

				var L = rad * (218.316 + 13.176396 * d), // ecliptic longitude
					M = rad * (134.963 + 13.064993 * d), // mean anomaly
					F = rad * (93.272 + 13.229350 * d),  // mean distance

					l  = L + rad * 6.289 * sin(M), // longitude
					b  = rad * 5.128 * sin(F),     // latitude
					dt = 385001 - 20905 * cos(M);  // distance to the moon in km

				return {
					ra: rightAscension(l, b),
					dec: declination(l, b),
					dist: dt
				};
			}

			 // Validates that the input year is a valid integer between -4712 to +3500
			 
			//-----Correct TDT to UTC----------------------------------------------------------------
			// Meeus Astronomical Algorithms Chapter 10
			function fromTDTtoUTC( tobj ) {
				// Correction lookup table has entry for every even year between TBLfirst and TBLlast
				var TBLfirst = 1620, TBLlast = 2002;	// Range of years in lookup table
				var TBL = new Array(					// Corrections in Seconds
					/*1620*/ 121,112,103, 95, 88,  82, 77, 72, 68, 63,  60, 56, 53, 51, 48,  46, 44, 42, 40, 38,
					/*1660*/  35, 33, 31, 29, 26,  24, 22, 20, 18, 16,  14, 12, 11, 10,  9,   8,  7,  7,  7,  7,
					/*1700*/   7,  7,  8,  8,  9,   9,  9,  9,  9, 10,  10, 10, 10, 10, 10,  10, 10, 11, 11, 11,
					/*1740*/  11, 11, 12, 12, 12,  12, 13, 13, 13, 14,  14, 14, 14, 15, 15,  15, 15, 15, 16, 16,
					/*1780*/  16, 16, 16, 16, 16,  16, 15, 15, 14, 13,  
					/*1800*/ 13.1, 12.5, 12.2, 12.0, 12.0,  12.0, 12.0, 12.0, 12.0, 11.9,  11.6, 11.0, 10.2,  9.2,  8.2,
					/*1830*/  7.1,  6.2,  5.6,  5.4,  5.3,   5.4,  5.6,  5.9,  6.2,  6.5,   6.8,  7.1,  7.3,  7.5,  7.6,
					/*1860*/  7.7,  7.3,  6.2,  5.2,  2.7,   1.4, -1.2, -2.8, -3.8, -4.8,  -5.5, -5.3, -5.6, -5.7, -5.9,
					/*1890*/ -6.0, -6.3, -6.5, -6.2, -4.7,  -2.8, -0.1,  2.6,  5.3,  7.7,  10.4, 13.3, 16.0, 18.2, 20.2,
					/*1920*/ 21.1, 22.4, 23.5, 23.8, 24.3,  24.0, 23.9, 23.9, 23.7, 24.0,  24.3, 25.3, 26.2, 27.3, 28.2,
					/*1950*/ 29.1, 30.0, 30.7, 31.4, 32.2,  33.1, 34.0, 35.0, 36.5, 38.3,  40.2, 42.2, 44.5, 46.5, 48.5,
					/*1980*/ 50.5, 52.5, 53.8, 54.9, 55.8,  56.9, 58.3, 60.0, 61.6, 63.0,  63.8, 64.3); /*2002 last entry*/
					// Values for Delta T for 2000 thru 2002 from NASA
				var deltaT = 0; // deltaT = TDT - UTC (in Seconds)
				var Year = tobj.getUTCFullYear();
				var t = (Year - 2000) / 100;	// Centuries from the epoch 2000.0
				
				if ( Year >= TBLfirst && Year <= TBLlast ) { // Find correction in table
					if (Year%2) { // Odd year - interpolate
						deltaT = ( TBL[(Year-TBLfirst-1)/2] + TBL[(Year-TBLfirst+1)/2] ) / 2;
					} else { // Even year - direct table lookup
						deltaT = TBL[(Year-TBLfirst)/2];
					}
				} else if( Year < 948) { 
					deltaT = 2177 + 497*t + 44.1*POW2(t);
				} else if( Year >=948) {
					deltaT =  102 + 102*t + 25.3*POW2(t);
					if (Year>=2000 && Year <=2100) { // Special correction to avoid discontinurity in 2000
						deltaT += 0.37 * ( Year - 2100 );
					}
				} else {  }
				return( new Date( tobj.getTime() - (deltaT*1000) ) ); // JavaScript native time is in milliseonds
			} // End fromTDTtoUTC

			//-----Julian Date to UTC Date Object----------------------------------------------------
			// Meeus Astronomical Algorithms Chapter 7 
			function fromJDtoUTC( JD ){
				// JD = Julian Date, possible with fractional days
				// Output is a JavaScript UTC Date Object
				var A, alpha;
				var Z = INT( JD + 0.5 ); // Integer JD's
				var F = (JD + 0.5) - Z;	 // Fractional JD's
				if (Z < 2299161) { A = Z; }
				else {
					alpha = INT( (Z-1867216.25) / 36524.25 );
					A = Z + 1 + alpha - INT( alpha / 4 );
				}
				var B = A + 1524;
				var C = INT( (B-122.1) / 365.25 );
				var D = INT( 365.25*C );
				var E = INT( ( B-D )/30.6001 );
				var DT = B - D - INT(30.6001*E) + F;	// Day of Month with decimals for time
				var Mon = E - (E<13.5?1:13);			// Month Number
				var Yr  = C - (Mon>2.5?4716:4715);		// Year    
				var Day = INT( DT ); 					// Day of Month without decimals for time
				var H = 24*(DT - Day);					// Hours and fractional hours 
				var Hr = INT( H ); 						// Integer Hours
				var M = 60*(H - Hr);					// Minutes and fractional minutes
				var Min = INT( M );						// Integer Minutes
				var Sec = INT( 60*(M-Min) );			// Integer Seconds (Milliseconds discarded)
				//Create and set a JavaScript Date Object and return it
				var theDate1 = new Date(0);
				theDate1.setUTCFullYear(Yr, Mon-1, Day);
				theDate1.setUTCHours(Hr, Min, Sec);
				return( theDate1 );
			} //End fromJDtoUTC

			//-----Moon Phase Calculation-----------------------------------------------------
			function calcMoonPhase( Y ) {
				if(Y=='now'){
					d = new Date();
					Y = d.getFullYear();
				}
				newMoons = [];
				fullMoons = [];
			  //Converted from Basic by Roger W. Sinnot, Sky & Telescope, March 1985.
			  var R1 = Math.PI / 180;
			  var U = false;
			  var K0, T, T2, T3, J0, F0, J, F, M0, M1, B1, K9, K, M5, M6, B6, str;
			  var s = ""; // Formatted Output String
			  K0 = INT((Y-1900)*12.3685);
			  T = (Y-1899.5) / 100;
			  T2 = T*T; T3 = T*T*T;
			  J0 = 2415020 + 29*K0;
			  F0 = 0.0001178*T2 - 0.000000155*T3;
			  F0 += (0.75933 + 0.53058868*K0);
			  F0 -= (0.000837*T + 0.000335*T2);
			  M0 = K0*0.08084821133;
			  M0 = 360*(M0 - INT(M0)) + 359.2242;
			  M0 -= 0.0000333*T2;
			  M0 -= 0.00000347*T3;
			  M1 = K0*0.07171366128;
			  M1 = 360*(M1 - INT(M1)) + 306.0253;
			  M1 += 0.0107306*T2;
			  M1 += 0.00001236*T3;
			  B1 = K0*0.08519585128;
			  B1 = 360*(B1 - INT(B1)) + 21.2964;
			  B1 -= 0.0016528*T2;
			  B1 -= 0.00000239*T3;
			  for ( K9=0; K9 <= 28; K9++ ) {
				J = J0 + 14*K9; F = F0 + 0.765294*K9;
				K = K9/2;
				M5 = (M0 + K* 29.10535608)*R1;
				M6 = (M1 + K*385.81691806)*R1;
				B6 = (B1 + K*390.67050646)*R1;
				F -= 0.4068*Math.sin(M6);
				F += (0.1734 - 0.000393*T)*Math.sin(M5);
				F += 0.0161*Math.sin(2*M6);
				F += 0.0104*Math.sin(2*B6);
				F -= 0.0074*Math.sin(M5 - M6);
				F -= 0.0051*Math.sin(M5 + M6);
				F += 0.0021*Math.sin(2*M5);
				F += 0.0010*Math.sin(2*B6-M6);
				F += 0.5 / 1440; //Adds 1/2 minute for proper rounding to minutes per Sky & Tel article
				var JDE = J + F; 				// Julian Empheris Day with fractions for time of day
				var TDT = fromJDtoUTC( JDE );		// Convert Julian Days to TDT in a Date Object
				var UTC = fromTDTtoUTC( TDT );		// Correct TDT to UTC, both as Date Objects	
				if ( Y==UTC.getFullYear() ) {	//Filter output
				
				if ( !U ) { 
					newMoons[newMoons.length] = (UTC.toLocaleString());
				}
				else{
					fullMoons[fullMoons.length] = (UTC.toLocaleString());
				}
				
				
				}
				U = !U;
			  } // Next
			  str = "<table id='newMoonTable' style='font-size:2vw'><tr><th style='text-align:center'><?php echo lang('new moons','w')?><br><img src='<?php echo $pageURL.$path?>imgs/moonImgs/56.png' style='width:35px'></th></tr>";
			  for(i=0;i<newMoons.length;i++){
				var str = str + "<tr><td>"+newMoons[i]+"</td></tr>";
			  }
			  str = str + "</table>";
			  $("#newMoons").html(str);
			  $("#newMoonTable").addClass( "table" );
			  
			  str2 = "<table id='fullMoonTable' style='font-size:2vw'><tr><th style='text-align:center'><?php echo lang('full moons','w')?><br><img src='<?php echo $pageURL.$path?>imgs/moonImgs/1.png' style='width:35px'></th></tr>";
			  for(i=0;i<fullMoons.length;i++){
				var str2 = str2 + "<tr><td>"+fullMoons[i]+"</td></tr>";
			  }
			  str2 = str2 + "</table>";
			  $("#fullMoons").html(str2);
			  $("#fullMoonTable").addClass( "table" );
			  
			  $("#currentYear").val(Y);
			} //End calcMoonPhase
			$(document).ready(function() {
				jQuery('#dayPicker').datepicker({
					dateFormat: "@",
					dayNamesShort: [ "<?php echo lang('sundayAbbr','c')?>", "<?php echo lang('mondayAbbr','c')?>", "<?php echo lang('tuesdayAbbr','c')?>", "<?php echo lang('wednesdayAbbr','c')?>", "<?php echo lang('thursdayAbbr','c')?>", "<?php echo lang('fridayAbbr','c')?>", "<?php echo lang('saturdayAbbr','c')?>" ],
					dayNamesMin: [ "<?php echo lang('sundayAbbr','c')?>", "<?php echo lang('mondayAbbr','c')?>", "<?php echo lang('tuesdayAbbr','c')?>", "<?php echo lang('wednesdayAbbr','c')?>", "<?php echo lang('thursdayAbbr','c')?>", "<?php echo lang('fridayAbbr','c')?>", "<?php echo lang('saturdayAbbr','c')?>" ],
					onSelect: function(date) {
						load(date);
					},
					firstDay: <?php echo $firstWeekday?>,
				});
				$("#minusYear").click(function(){
					current = eval($("#currentYear").val());
					newYear = current-1;
					calcMoonPhase(newYear);
					$("#currentYear").val(newYear);
				});
				$("#plusYear").click(function(){
					current = eval($("#currentYear").val());
					newYear = current+1;
					calcMoonPhase(newYear);
					$("#currentYear").val(newYear);
				});
				$("#minus10Year").click(function(){
					current = eval($("#currentYear").val());
					newYear = current-10;
					calcMoonPhase(newYear);
					$("#currentYear").val(newYear);
				});
				$("#plus10Year").click(function(){
					current = eval($("#currentYear").val());
					newYear = current+10;
					calcMoonPhase(newYear);
					$("#currentYear").val(newYear);
				});
				$("#animStart").click(function(){
					animStart();
				});
				$("#animSpeed").change(function(){
					$("#animStop").click();
					animStart();
				});
				
				$.fn.preload = function() {
					this.each(function(){
						$('<img/>')[0].src = this;
					});
				}
				// image preloading
				/*
				$([
					<?php 
						$images = array();
						for($i=1;$i<119;$i++){
							array_push($images,"'moonImgs/".$i.".png'");
						}
						echo implode(",",$images);
					?>
				]).preload();
				*/
			})
		</script>
		<script>
			function daysInMonth(anyDateInMonth) {
				return new Date(anyDateInMonth.getYear(), anyDateInMonth.getMonth()+1,  0).getDate();
			}
			function animStart(){
				d = new Date(($('#dayPicker').val())*1);
				
				var myVar = setInterval(function(){ 
					load(d);
					step = $("#animStep").val();
					if(step=="Day"){
						ms = d.getTime();	
						newDate = eval(ms + 60*60*24*1000);
						d = new Date(newDate);
					}
					if(step=="7 days"){
						ms = d.getTime();	
						newDate = eval(ms + 60*60*24*7*1000);
						d = new Date(newDate);
					}
					if(step=="Month"){
						day = d.getDate();
						month = d.getMonth();
						year = d.getFullYear();
						if(month!=11){
							d = new Date(year,(month+1),day);
						}
						else{
							d = new Date((year+1),0,day);
						}
					}
					if(step=="Year"){
						day = d.getDate();
						month = d.getMonth();
						year = d.getFullYear();
						d = new Date((year+1),month,day);
					}
					$( "#dayPicker" ).datepicker( "setDate", d );
				}, $("#animSpeed").val());
				
				$("#animStop").click(function(){
					clearInterval(myVar);
				});
			}
		</script>
		<?php include("../../footer.php")?>
	</body>
</html>
	