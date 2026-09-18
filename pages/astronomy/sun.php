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
	#	Sunrise, sunset and daylength calculation and visualization
	#
	# 	A script to calculate sunrise, sunset and daylength for a particular
	#	date using weather station geographical coordinates.
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		
		<script src="//code.highcharts.com/highcharts.src.js"></script>
		<script src="//code.highcharts.com/highcharts-more.js"></script>
		<script src="//code.highcharts.com/maps/modules/exporting.js"></script>
		
		<style>
			#time{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
				text-align: center;
				width: 100%;
				margin-left: auto;
				margin-right: auto;
				padding-top: 10px;
				padding-bottom: 10px;
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
			}
			#displayedDate{
				font-size: 1.5em;
				font-weight: bold;
			}
			.centered, .ui-tabs, .ui-widget, .ui-widget-content, .ui-corner-all{
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
			}
			#sunTimes, #lengths, #tabs{
				background: #<?php echo $color_schemes[$design2]['700']?>;
				color: #<?php echo $color_schemes[$design2]['font700']?>;
				text-align: center;
				width: 95%;
				margin-left: auto;
				margin-right: auto;
				font-weight: bold;
				padding-top: 10px;
				padding-bottom: 10px;
			}
			.astro{
				width: 50px;
			}
			#showGraphs{
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif!important;
				font-size: 0.8em!important;
			}
			.twilightIcon{
				width: 40px;
				padding-right: 20px;
			}
			.mainTimes{
				font-size: 1.2em;
				font-weight: bold;
			}
			.line{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				width: 95%;
				margin-left: auto;
				margin-right: auto;
				height: 3px;
			}
			#sunGraph, #twilightGraph{
				width: 95%;
				margin-left: auto;
				margin-right: auto;
			}
			.ui-widget-content {
				border: 1px solid #<?php echo $color_schemes[$design2]['900']?>;
				background: #<?php echo $color_schemes[$design2]['800']?>;
				background-color: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.ui-widget-header {
				border: 0px solid #000;
				background: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.ui-widget {
				font-family: "<?php echo $designFont?>",Arial Narrow,Arial,Helvetica,sans-serif;
				font-size: 1.0em;
			}
			.ui-tabs .ui-tabs-panel {
				background: #<?php echo $color_schemes[$design2]['700']?>;
				border: 1px solid #<?php echo $color_schemes[$design]['900']?>;
				border-radius: 5px;
				padding: 0.5em; 
			}
			.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
				border: 0px solid #000;
				background: #<?php echo $color_schemes[$design2]['800']?>;
			}
			.ui-state-active, .ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active {
				border: 1px solid #<?php echo $color_schemes[$design]['900']?>;
				background: #<?php echo $color_schemes[$design2]['900']?>;
			}
			.ui-tabs .ui-tabs-nav {
				margin: 0;
				padding: 0em 0em 0;
			}
			.ui-tabs .ui-tabs-nav .ui-tabs-anchor {
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br{
				border-radius:0px;
			}
			.ui-tabs-active, .ui-state-active{
				background: #<?php echo $color_schemes[$design2]['700']?>!important;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['200']?> !important;
				color: #<?php echo $color_schemes[$design2]['font200']?>;
				-webkit-transition: all .5s ease;
				-moz-transition: all .5s ease;
				-o-transition: all .5s ease;
				transition: all .5s ease;
			}
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
			.astroSVG{
				font-size: 4em;
			}
			#pdfLink{
				font-size:3em;
				cursor: pointer;
				opacity: 0.8;
				padding-bottom:10px;
				padding-top: 10px;
			}
			#pdfLink:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body onload="calculate(new Date());calculateTable(new Date().getFullYear());calculateLength(new Date().getFullYear())">
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			
			<table style="width:98%;margin:0 auto">
				<tr>
					<td style="width:5%;text-align:right">
					</td>
					<td style='text-align:center;vertical-align:top'>
						<h1><?php echo lang('sun','c')?></h1>
					</td>
					<td style="width:5%;text-align:right">
						<a href="sunYearPDF.php" target="_blank"><span class="fa fa-file-pdf-o tooltip" id="pdfLink" title="PDF"></span></a>
					</td>
				</tr>
			</table>
			<br>
			
			<div id="tabs" class="centered">
			<div id="time">
				<table style="margin:0 auto">
					<tr>
						<td>
							<span class="fa fa-angle-double-left controlIcon" onclick="recalculate('-month')"></span>
						</td>
						<td>
							<span class="fa fa-angle-left controlIcon" onclick="recalculate('-day')"></span>
						</td>
						<td>
							<div id="displayedDate" style="padding-left:10px;padding-right:10px">
							</div>
						</td>
						<td>
							<span class="fa fa-angle-right controlIcon" onclick="recalculate('+day')"></span>
						</td>
						<td>	
							<span class="fa fa-angle-double-right controlIcon" onclick="recalculate('+month')"></span>
						</td>
					</tr>
				</table>
				<input type="button" class="button2" value="<?php echo lang('today','c')?>" onclick="recalculate('today')">
			</div>
				<ul style="align:center">
					<li>
						<a href="#tab1"><?php echo (lang("sunrise","w")."/".lang("sunset","w"))?></a>
					</li>
					<li>
						<a href="#tab2"><?php echo lang("twilights","w")?></a>
					</li>
					<li>
						<a href="#tab3"><?php echo lang("day graph","c")?></a>
					</li>
					<li>
						<a href="#tab4"><?php echo lang("annual table","c")?></a>
					</li>
					<li>
						<a href="#tab5"><?php echo lang("annual day length","c")?></a>
					</li>
					<li>
						<a href="#tab6"><?php echo lang("annual graphs","c")?></a>
					</li>
				</ul>
				<div id="tab1">
					<div id="sunTimes">
						<table style="width:100%">
							<tr>
								<td style="text-align:center">
									<span class="mticon-sunrise astroSVG tooltip" title="<?php echo lang('sunrise','c')?>"></span>
								</td>
								<td style="text-align:center">
									<span class="mticon-sunlight-ratio astroSVG tooltip" title="<?php echo lang('sun transit','c')?>"></span>
								</td>
								<td style="text-align:center">
									<span class="mticon-sunset astroSVG tooltip" title="<?php echo lang('sunset','c')?>"></span>
								</td>
							</tr>
							<tr>
								<td style="text-align:center">
									<div id="sunRise" class="mainTimes"></div>
								</td>
								<td style="text-align:center">
									<div id="sunTransit" class="mainTimes"></div>
								</td>
								<td style="text-align:center">
									<div id="sunSet" class="mainTimes"></div>
								</td>
							</tr>
						</table>
					</div>
					<div id="lengths">
						<table style="width:100%">
							<tr>
								<td style="text-align:center">
									<span class="mticon-sun astroSVG tooltip" title="<?php echo lang('day length','c')?>"></span>
								</td>
								<td style="text-align:center">
									<span class="mticon-night astroSVG tooltip" title="<?php echo lang('night length','c')?>"></span>
								</td>
							</tr>
							<tr>
								<td style="text-align:center">
									<div id="dayLength" class="mainTimes"></div>
								</td>
								<td style="text-align:center">
									<div id="nightLength" class="mainTimes"></div>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div id="tab2">
					<table class="table" style="width:98%;margin:0 auto">
						<tr>
							<th>
								<span class="mticon-solarsystem astroSVG" style="font-size:2.3em"></span>
							</th>
							<th style="text-align:left">
								<?php echo lang('astronomical twilight','c')?>
							</th>
							<td style="text-align:right">
								<div id="astronomicalTwilightTimes">
								</div>
							</td>
						</tr>
						<tr>
							<th>
								<span class="fa fa-anchor astroSVG" style="font-size:2.3em"></span>
							</th>
							<th style="text-align:left">
								<?php echo lang('nautical twilight','c')?>
							</th>
							<td style="text-align:right">
								<div id="nauticalTwilightTimes">
								</div>
							</td>
						</tr>
						<tr>
							<th>
								<span class="fa fa-camera astroSVG" style="font-size:2.3em"></span>
							</th>
							<th style="text-align:left">
								<?php echo lang('civil twilight','c')?>
							</th>
							<td style="text-align:right">
								<div id="civilTwilightTimes">
								</div>
							</td>
						</tr>
					</table>
				</div>
				<div id="tab3">
					<div id="twilightGraph" style="height:300px">
					</div>
					<input type="hidden" id="hiddenShown">
				</div>
				<div id="tab4">
					<table style=" margin: 0 auto">
						<tr>
							<td>
								<input type="button" class="button2" value="  <  " onclick="recalculateTable('-y')">
							</td>
							<td>
								<span class="h3" id="tableYear"></span>
							</td>
							<td>
								<input type="button" class="button2" value="  >  " onclick="recalculateTable('+y')">
							</td>
						</tr>
					</table>
					<div id="annualTable">
					</div>
				</div>
				<div id="tab5">
					<table style=" margin: 0 auto">
						<tr>
							<td>
								<input type="button" class="button2" value="  <  " onclick="recalculateLength('-y')">
							</td>
							<td>
								<span class="h3" id="tableLengthYear"></span>
							</td>
							<td>
								<input type="button" class="button2" value="  >  " onclick="recalculateLength('+y')">
							</td>
						</tr>
					</table>
					<div id="annualLength">
					</div>
				</div>
				<div id="tab6">
					<input type="button" class="button2" value="<?php echo lang('show','c')?>" id="showGraphs">
					<table style=" margin: 0 auto">
						<tr>
							<td>
								<input type="button" class="button2" value="  <  " onclick="recalculateGraph('-y')">
							</td>
							<td>
								<span class="h3" id="graphYear"><?php echo date('Y')?></span>
							</td>
							<td>
								<input type="button" class="button2" value="  >  " onclick="recalculateGraph('+y')">
							</td>
						</tr>
					</table>
					<div id="annualGraphs">
					</div>
				</div>
			</div>
			<br><br>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			function calculate(date){
				y = date.getFullYear();
				m = date.getMonth() + 1;
				d = date.getDate();
				
				query = y + "-" + m + "-" + d;
				
				$('#displayedDate').html(date.toLocaleDateString());
				$('#hiddenShown').val(date.getTime());
				
				options = {
				chart: {
					zoomType: 'x',
					renderTo : 'twilightGraph',
				},
				title: {
					text:  ''
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				xAxis: {
					type: 'datetime',
					dateTimeLabelFormats: {
						hour: '<?php echo $graphTimeFormat?>',
						minute: '<?php echo $graphTimeFormat?>',
						day: '<?php echo $graphDateFormat?>',
					},
					title: {
						text: null
					},
				},
				yAxis: {
					title: {
						text: null
					},
					labels: {
						enabled: false
					}
				},
				tooltip: {
					enabled: false
				},
				legend: {
					enabled: true
				},
				navigation: {
					buttonOptions: {
						enabled: false
					}
				},
				credits: {
					enabled: false
				},
				plotOptions: {
					series: {
						animation: {
							duration: 5000
						},
						marker: {
							enabled: false
						},
					},
					areasplinerange:{
						fillOpacity: 1,
					}
					
				},	
				series: [
					{
						type: 'areaspline',
						name: '<?php echo lang('astronomical','c')?>',
						zIndex: 3,
						color: '#<?php echo $color_schemes[$design2]['500']?>',
						data: []
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('civil','c')?>',
						zIndex: 5,
						color: '#<?php echo $color_schemes[$design2]['900']?>',
						data: []
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('nautical','c')?>',
						zIndex: 10,
						color: '#<?php echo $color_schemes[$design2]['700']?>',
						data: []
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('day','c')?>',
						zIndex: 15,
						color: '#<?php echo $color_schemes[$design2]['200']?>',
						data: []
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('night','c')?>',
						zIndex: 1,
						color: '#000000',
						data: []
					},
					{
						type: 'areaspline',
						name: '<?php echo lang('sun','c')?>',
						zIndex: 20,
						color: '#<?php echo $color_schemes[$design2]['100']?>',
						data: []
					},
				]
			}

				$.ajax({
					url : "sunAjax.php?y="+y+"&m="+m+"&d="+d,
					dataType : 'json',
					success : function (json) {
						sunRise = json['sunRise'];
						sunSet = json['sunSet'];
						transit = json['transit'];
						twilightRiseAstro = json['twilightRiseAstro'];
						twilightRiseCivil = json['twilightRiseCivil'];
						twilightRiseNaut = json['twilightRiseNaut'];
						twilightSetAstro = json['twilightSetAstro'];
						twilightSetCivil = json['twilightSetCivil'];
						twilightSetNaut = json['twilightSetNaut'];
						
						sunRise = new Date(y,m,d,sunRise.substring(0,2),sunRise.substring(3,5));
						sunSet = new Date(y,m,d,sunSet.substring(0,2),sunSet.substring(3,5));
						transit = new Date(y,m,d,transit.substring(0,2),transit.substring(3,5));
						twilightRiseAstro = new Date(y,m,d,twilightRiseAstro.substring(0,2),twilightRiseAstro.substring(3,5));
						twilightRiseCivil = new Date(y,m,d,twilightRiseCivil.substring(0,2),twilightRiseCivil.substring(3,5));
						twilightRiseNaut = new Date(y,m,d,twilightRiseNaut.substring(0,2),twilightRiseNaut.substring(3,5));
						twilightSetAstro = new Date(y,m,d,twilightSetAstro.substring(0,2),twilightSetAstro.substring(3,5));
						twilightSetCivil = new Date(y,m,d,twilightSetCivil.substring(0,2),twilightSetCivil.substring(3,5));
						twilightSetNaut = new Date(y,m,d,twilightSetNaut.substring(0,2),twilightSetNaut.substring(3,5));
						
						sunRiseFormatted = sunRise.toLocaleTimeString();
						sunRiseFormatted = sunRiseFormatted.replace(/:\d+ /, ' ');
						sunSetFormatted = sunSet.toLocaleTimeString();
						sunSetFormatted = sunSetFormatted.replace(/:\d+ /, ' ');
						transitFormatted = transit.toLocaleTimeString();
						transitFormatted = transitFormatted.replace(/:\d+ /, ' ');
						twilightRiseAstroFormatted = twilightRiseAstro.toLocaleTimeString();
						twilightRiseAstroFormatted = twilightRiseAstroFormatted.replace(/:\d+ /, ' ');
						twilightSetAstroFormatted = twilightSetAstro.toLocaleTimeString();
						twilightSetAstroFormatted = twilightSetAstroFormatted.replace(/:\d+ /, ' ');
						twilightRiseNautFormatted = twilightRiseNaut.toLocaleTimeString();
						twilightRiseNautFormatted = twilightRiseNautFormatted.replace(/:\d+ /, ' ');
						twilightSetNautFormatted = twilightSetNaut.toLocaleTimeString();
						twilightSetNautFormatted = twilightSetNautFormatted.replace(/:\d+ /, ' ');
						twilightRiseCivilFormatted = twilightRiseCivil.toLocaleTimeString();
						twilightRiseCivilFormatted = twilightRiseCivilFormatted.replace(/:\d+ /, ' ');
						twilightSetCivilFormatted = twilightSetCivil.toLocaleTimeString();
						twilightSetCivilFormatted = twilightSetCivilFormatted.replace(/:\d+ /, ' ');
						$('#sunRise').html(sunRiseFormatted);
						$('#sunSet').html(sunSetFormatted);
						$('#sunTransit').html(transitFormatted);
						$('#astronomicalTwilightTimes').html(twilightRiseAstroFormatted + " - " + twilightSetAstroFormatted);
						$('#nauticalTwilightTimes').html(twilightRiseNautFormatted + " - " + twilightSetNautFormatted);
						$('#civilTwilightTimes').html(twilightRiseCivilFormatted + " - " + twilightSetCivilFormatted);
						$('#dayLength').html(json['dayLength']);
						$('#nightLength').html(json['nightLength']);
						graphData = [];
						graphData[0] = [Date.UTC(y,m,d,json['sunRise'].substring(0,2),json['sunRise'].substring(3,5)),0];
						graphData[1] = [Date.UTC(y,m,d,json['transit'].substring(0,2),json['transit'].substring(3,5)),1];
						graphData[2] = [Date.UTC(y,m,d,json['sunSet'].substring(0,2),json['sunSet'].substring(3,5)),0];
						axisMin = [Date.UTC(y,m,d,0,0)];
						axisMax = [Date.UTC(y,m,d,23,59)];

						twilightGraph = $('#twilightGraph').highcharts();
						astronomicaltwilight = [];
						astronomicaltwilight[0] = [Date.UTC(y,m,d,json['twilightRiseAstro'].substring(0,2),json['twilightRiseAstro'].substring(3,5)),1];
						astronomicaltwilight[1] = [Date.UTC(y,m,d,json['twilightSetAstro'].substring(0,2),json['twilightSetAstro'].substring(3,5)),1];
						civiltwilight = [];
						civiltwilight[0] = [Date.UTC(y,m,d,json['twilightRiseCivil'].substring(0,2),json['twilightRiseCivil'].substring(3,5)),1];
						civiltwilight[1] = [Date.UTC(y,m,d,json['twilightSetCivil'].substring(0,2),json['twilightSetCivil'].substring(3,5)),1];
						nauticaltwilight = [];
						nauticaltwilight[0] = [Date.UTC(y,m,d,json['twilightRiseNaut'].substring(0,2),json['twilightRiseNaut'].substring(3,5)),1];
						nauticaltwilight[1] = [Date.UTC(y,m,d,json['twilightSetNaut'].substring(0,2),json['twilightSetNaut'].substring(3,5)),1];
						dayData = [];
						dayData[0] = [Date.UTC(y,m,d,json['sunRise'].substring(0,2),json['sunRise'].substring(3,5)),1];
						dayData[1] = [Date.UTC(y,m,d,json['sunSet'].substring(0,2),json['sunSet'].substring(3,5)),1];
						nightData = [];
						nightData[0] =  [Date.UTC(y,m,d,0,0),1];
						nightData[1] = [Date.UTC(y,m,d,23,59),1];
						options.series[0].data = astronomicaltwilight;
						options.series[1].data = civiltwilight;
						options.series[2].data = nauticaltwilight;
						options.series[3].data = dayData;
						options.series[4].data = nightData;
						options.series[5].data = graphData;
						//options.xAxis[0].min = axisMin;
						//options.xAxis[0].max = axisMax;
						chart = new Highcharts.Chart(options);
					},
				});
			}
			function recalculate(interval){
				if(interval=="-day"){
					current = eval($('#hiddenShown').val());
					newDate = current - 1000*60*60*24;
					newDate = new Date(newDate);
					calculate(newDate);
				}
				if(interval=="+day"){
					current = eval($('#hiddenShown').val());
					newDate = current + 1000*60*60*24;
					newDate = new Date(newDate);
					calculate(newDate);
				}
				if(interval=="-month"){
					d = new Date(eval($('#hiddenShown').val()));
					day = d.getDate();
					month = d.getMonth();
					year = d.getFullYear();
					if(month!=0){
						newDate = new Date(year,(month-1),day);
					}
					else{
						newDate = new Date((year-1),11,day);
					}
					calculate(newDate);
				}
				if(interval=="+month"){
					d = new Date(eval($('#hiddenShown').val()));
					day = d.getDate();
					month = d.getMonth();
					year = d.getFullYear();
					if(month!=11){
						newDate = new Date(year,(month+1),day);
					}
					else{
						newDate = new Date((year+1),0,day);
					}
					calculate(newDate);
				}
				if(interval=="today"){
					newDate = new Date();
					calculate(newDate);
				}
			}
		</script>
		<script>
			function calculateTable(y){
				$( "#tableYear" ).html( y );
				$( "#annualTable" ).load( "sunAjaxYear.php?y="+y );
				$("#annualTable").addClass( "table" );
			}
			function recalculateTable(str){
				if(str=="-y"){
					current = eval($('#tableYear').html());
					newYear = current-1;
				}
				if(str=="+y"){
					current = eval($('#tableYear').html());
					newYear = current+1;
				}
				calculateTable(newYear);
			}
			function calculateLength(y){
				$( "#tableLengthYear" ).html( y );
				$( "#annualLength" ).load( "sunAjaxLength.php?y="+y );
				$("#annualLength").addClass( "table" );
			}
			function recalculateLength(str){
				if(str=="-y"){
					current = eval($('#tableLengthYear').html());
					newYear = current-1;
				}
				if(str=="+y"){
					current = eval($('#tableLengthYear').html());
					newYear = current+1;
				}
				calculateLength(newYear);
			}
			$("#showGraphs").click(function(){ // this is fix for highcharts not properly resizing when loaded in background tab
				current = eval($('#graphYear').html());
				calculateGraphs(current);
			});
			function calculateGraphs(y){
				$( "#graphYear" ).html( y );
				$( "#annualGraphs" ).load( "sunAjaxGraphs.php?y="+y );
			}
			function recalculateGraph(str){
				if(str=="-y"){
					current = eval($('#graphYear').html());
					newYear = current-1;
				}
				if(str=="+y"){
					current = eval($('#graphYear').html());
					newYear = current+1;
				}
				calculateGraphs(newYear);
			}
		</script>
		<?php include("../../css/highcharts.php");?>
		<script>
			$(function () {
				Highcharts.setOptions({
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>'],
					}
				})
			})
		</script>
		<script>
			
		</script>
		<script>
			$(document).ready(function() {
				$( "#tabs" ).tabs();
			})
		</script>
	</body>
</html>
	