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
	#	Interactive table
	#
	# 	A script which generates the page for interactive table.
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
	include("../../../scripts/stats.php");
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("interactive table",'c')?></title>
		<?php metaHeader()?>

		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcore.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxdatetimeinput.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcalendar.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jqxcheckbox.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/globalize.js"></script>
		
		<style>
			.calendar{
				padding: 5px;
				font-size: 1.7em;
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
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['400']?>;
				color: #<?php echo $color_schemes[$design2]['font400']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['600']?>;
				color: #<?php echo $color_schemes[$design2]['font600']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			.table tfoot td{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.tableSetting{
				font-variant: small-caps;
				color: #<?php echo $color_schemes[$design]['100']?>;
				font-weight: bold;
				background-color: #<?php echo $color_schemes[$design]['600']?>;
			}
			.parameter{
				padding: 2px;
				font-size:1.6em;
				opacity: 0.6;
			}
			.parameterTable{
				padding: 2px;
				width:100%;
				max-width: 25px;
				opacity: 0.6;
			}
			.category{
				opacity: 0.6;
				font-variant: small-caps;
				text-align: left;
			}
			.jqx-checkbox-check-checked{
				background-color: #<?php echo $color_schemes[$design]['600']?>;
				padding: 0px;
				border: none;
				outline: none;
				margin: 0 0 0 0;
			}
			.checkboxCell{
				width:100%;
				max-width: 20px;
				text-align: center;
				margin-left: auto!important;
				margin-right: auto!important;
				opacity: 0.8;
			}
			.csv{
				opacity: 0.8;
				cursor: pointer;
				width:100%;
				max-width: 35px;
			}
			.csv:hover{
				opacity:1;
			}
		</style>
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/jqx.base.css" media="screen" />
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include("../../menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<br>
		<h1><?php echo lang("interactive table",'c')?></h1>
		<br>
		<input type="hidden" id="chosenValue" value="all">
		<input type="hidden" id="chosenInterval" value="lastweek">
		<table style="margin-left:auto;margin-right:auto;font-size:1.5vw">
			<tr>
				<td colspan="4" class="tableSetting">
					<?php echo lang("grouping",'c')?>
				</td>
				<td colspan="9" class="tableSetting">
					<?php echo lang("interval",'c')?>
				</td>
			</tr>
			<tr>
				<td>
					<span class="calendar tooltip" id="valuesAll" onclick="valueSet('all')" style="opacity:1">
						<?php echo lang("all",'u')?>
					</span>
				</td>
				<td>
					<span class="mticon-1h calendar tooltip" id="valuesHour" onclick="valueSet('h')" title="<?php echo lang("hourly data",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-calendar-day calendar tooltip" id="valuesHour" onclick="valueSet('d')" title="<?php echo lang("daily data",'c')?>"></span>
				</td>
				<td>
					<span class="mticon-calendar-month calendar tooltip" id="valuesHour" onclick="valueSet('m')" title="<?php echo lang("monthly data",'c')?>"></span>
				</td>
				<td class="interval">
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
					<span class="dates" id="intervalToday" onclick="intervalSet('today')">
						<?php echo lang("today",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalYesterday" onclick="intervalSet('yesterday')">
						<?php echo lang("yesterday",'c')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalWeek" onclick="intervalSet('thisweek')">
						<?php echo lang("this",'c')."<br>".lang("week",'l')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalMonth" onclick="intervalSet('thismonth')">
						<?php echo lang("this",'c')."<br>".lang("month",'l')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalLastWeek" onclick="intervalSet('lastweek')" style="opacity:1">
						<?php echo lang("last",'c')."<br>".lang("week",'l')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalLastMonth" onclick="intervalSet('lastmonth')">
						<?php echo lang("last",'c')."<br>".lang("month",'l')?>
					</span>
				</td>
				<td class="interval">
					<span class="dates" id="intervalCustom" onclick="intervalSet('custom')">
						<?php echo lang("custom",'c')?>
					</span>
				</td>
			</tr>
			<tr>
				<td colspan="13" class="tableSetting">
					<?php echo lang("parameters",'c')?>
				</td>
			</tr>
			<tr>
				<td colspan="13">
					<table style="width:100%;text-align:left">
						<tr>
							<td>
							</td>
							<td>
								<span class="mticon-temp parameter tooltip" id="T" title="<?php echo lang("temperature",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-humidity parameter tooltip" id="H" title="<?php echo lang("humidity",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-pressure parameter tooltip" id="P" title="<?php echo lang("pressure",'c')?>"></span>
							</td>
							<td>	
								<span class="mticon-wind parameter tooltip" id="W" title="<?php echo lang("wind speed",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-gust parameter tooltip" id="G" title="<?php echo lang("wind gust",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-rain parameter tooltip" id="R" title="<?php echo lang("precipitation",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-apparent parameter tooltip" id="A" title="<?php echo lang("apparent temperature",'c')?>"></span>
							</td>
							<td>
								<span class="mticon-dewpoint parameter tooltip" id="D" title="<?php echo lang("dewpoint",'c')?>"></span>
							</td>
							<td>
								<?php 
									if($solarSensor){
								?>
									<span class="mticon-sun parameter tooltip" id="S" title="<?php echo lang("solar radiation",'c')?>"></span>
								<?php 
									}
								?>
							</td>
							<td style="border-left: 1px solid #<?php echo $color_schemes[$design]['300']?>;width:1px">								
							</td>
						</tr>
						<tr>
							<td class="category">
								<?php echo lang('avgAbbr','c')."/".lang('total','c')?>
							</td>
							<td>
								<div id='avgTBox' class="jqxCheckBox checkboxCell avg T"></div>
							</td>
							<td>
								<div id='avgHBox' class="jqxCheckBox checkboxCell avg H"></div>
							</td>
							<td>
								<div id='avgPBox' class="jqxCheckBox checkboxCell avg P"></div>
							</td>
							<td>
								<div id='avgWBox' class="jqxCheckBox checkboxCell avg W"></div>
							</td>
							<td>
								<div id='avgGBox' class="jqxCheckBox checkboxCell avg G"></div>
							</td>
							<td>
								<div id='totalRBox' class="jqxCheckBox checkboxCell avg R"></div>
							</td>
							<td>
								<div id='avgABox' class="jqxCheckBox checkboxCell avg A"></div>
							</td>
							<td>
								<div id='avgDBox' class="jqxCheckBox checkboxCell avg D"></div>
							</td>
							<td>
								<?php if($solarSensor){?>
									<div id='avgSBox' class="jqxCheckBox checkboxCell avg S"></div>
								<?php }?>
							</td>
							<td style="border-left: 1px solid #<?php echo $color_schemes[$design]['300']?>;width:1px">
								<div id='avgAllBox' class="jqxCheckBox checkboxCell"></div>
							</td>
						</tr>
						<tr>
							<td class="category">
								<?php echo lang('maximumAbbr','c')?>
							</td>
							<td>
								<div id='maxTBox' class="jqxCheckBox checkboxCell max T"></div>
							</td>
							<td>
								<div id='maxHBox' class="jqxCheckBox checkboxCell max H"></div>
							</td>
							<td>
								<div id='maxPBox' class="jqxCheckBox checkboxCell max P"></div>
							</td>
							<td>
								<div id='maxWBox' class="jqxCheckBox checkboxCell max W"></div>
							</td>
							<td>
								<div id='maxGBox' class="jqxCheckBox checkboxCell max G"></div>
							</td>
							<td>
								
							</td>
							<td>
								<div id='maxABox' class="jqxCheckBox checkboxCell max A"></div>
							</td>
							<td>
								<div id='maxDBox' class="jqxCheckBox checkboxCell max D"></div>
							</td>
							<td>
								<?php if($solarSensor){?>
									<div id='maxSBox' class="jqxCheckBox checkboxCell max S"></div>
								<?php }?>
							</td>
							<td style="border-left: 1px solid #<?php echo $color_schemes[$design]['300']?>;width:1px">
								<div id='maxAllBox' class="jqxCheckBox checkboxCell"></div>
							</td>
						</tr>
						<tr>
							<td class="category">
								<?php echo lang('minimumAbbr','c')?>
							</td>
							<td>
								<div id='minTBox' class="jqxCheckBox checkboxCell min T"></div>
							</td>
							<td>
								<div id='minHBox' class="jqxCheckBox checkboxCell min H"></div>
							</td>
							<td>
								<div id='minPBox' class="jqxCheckBox checkboxCell min P"></div>
							</td>
							<td>
								<div id='minWBox' class="jqxCheckBox checkboxCell min W"></div>
							</td>
							<td>
								<div id='minGBox' class="jqxCheckBox checkboxCell min G"></div>
							</td>
							<td>
								
							</td>
							<td>
								<div id='minABox' class="jqxCheckBox checkboxCell min A"></div>
							</td>
							<td>
								<div id='minDBox' class="jqxCheckBox checkboxCell min D"></div>
							</td>
							<td>
								<?php if($solarSensor){?>
									<div id='minSBox' class="jqxCheckBox checkboxCell min S"></div>
								<?php }?>
							</td>
							<td style="border-left: 1px solid #<?php echo $color_schemes[$design]['300']?>;width:1px">
								<div id='minAllBox' class="jqxCheckBox checkboxCell"></div>
							</td>
						</tr>
						<tr>
							<td class="category">
								<?php echo lang('range','c')?>
							</td>
							<td>
								<div id='rangeTBox' class="jqxCheckBox checkboxCell range T"></div>
							</td>
							<td>
								<div id='rangeHBox' class="jqxCheckBox checkboxCell range H"></div>
							</td>
							<td>
								<div id='rangePBox' class="jqxCheckBox checkboxCell range P"></div>
							</td>
							<td>
								<div id='rangeWBox' class="jqxCheckBox checkboxCell range W"></div>
							</td>
							<td>
								<div id='rangeGBox' class="jqxCheckBox checkboxCell range G"></div>
							</td>
							<td>
								
							</td>
							<td>
								<div id='rangeABox' class="jqxCheckBox checkboxCell range A"></div>
							</td>
							<td>
								<div id='rangeDBox' class="jqxCheckBox checkboxCell range D"></div>
							</td>
							<td>
								<?php if($solarSensor){?>
									<div id='rangeSBox' class="jqxCheckBox checkboxCell range S"></div>
								<?php }?>
							</td>
							<td style="border-left: 1px solid #<?php echo $color_schemes[$design]['300']?>;width:1px">
								<div id='rangeAllBox' class="jqxCheckBox checkboxCell"></div>
							</td>
						</tr>
						<tr style="height:10px">
							<td colspan="11"></td>
						</tr>
						<tr>
							<td class="category">
							</td>
							<td>
								<div id='allTBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allHBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allPBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allWBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allGBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allRBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allABox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<div id='allDBox' class="jqxCheckBox checkboxCell"></div>
							</td>
							<td>
								<?php if($solarSensor){?>
									<div id='allSBox' class="jqxCheckBox checkboxCell"></div>
								<?php }?>
							</td>
							<td >
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<table style="text-align:center;width:100%;border-spacing:5px">
			<tr>
				<td style="width:45%;text-align:right">
					<span onclick="csv()" class="csv fa fa-file-excel-o" style='font-size:1.7em'></span>
				</td>
				<td style="text-align:center">
					<input type="button" class="button" value="<?php echo lang("ok",'u')?> " onclick="generate()">
				</td>
				<td style="text-align:left;width:45%">
					<div style="color:#<?php echo $color_schemes[$design]['200']?>" id='fullscreenBox' class="jqxCheckBox">
						<?php echo lang("show fullscreen","c")?><br>
						<span style="font-size: 0.8em">
							<?php echo "(".lang("must enable pop-up windows in browser","l").")"?>
						</span>
					</div>
				</td>
			</tr>
		</table>
		<br><br>
		<div id="tableDiv" style="overflow:auto;max-height:400px"></div>
		</div></div>
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
			$(document).ready(function() {
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
					generate();
					$( "#customDialog" ).dialog( "close" );
				});
				$(".jqxCheckBox").jqxCheckBox({});
				$('#allTBox').on('checked', function (event) { 
					$('.T').jqxCheckBox('check');
				}); 
				$('#allTBox').on('unchecked', function (event) { 
					$('.T').jqxCheckBox('uncheck');
				}); 
				$('#allHBox').on('checked', function (event) { 
					$('.H').jqxCheckBox('check');
				}); 
				$('#allHBox').on('unchecked', function (event) { 
					$('.H').jqxCheckBox('uncheck');
				}); 
				$('#allPBox').on('checked', function (event) { 
					$('.P').jqxCheckBox('check');
				}); 
				$('#allPBox').on('unchecked', function (event) { 
					$('.P').jqxCheckBox('uncheck');
				}); 
				$('#allWBox').on('checked', function (event) { 
					$('.W').jqxCheckBox('check');
				}); 
				$('#allWBox').on('unchecked', function (event) { 
					$('.W').jqxCheckBox('uncheck');
				}); 
				$('#allGBox').on('checked', function (event) { 
					$('.G').jqxCheckBox('check');
				}); 
				$('#allGBox').on('unchecked', function (event) { 
					$('.G').jqxCheckBox('uncheck');
				}); 
				$('#allRBox').on('checked', function (event) { 
					$('.R').jqxCheckBox('check');
				}); 
				$('#allRBox').on('unchecked', function (event) { 
					$('.R').jqxCheckBox('uncheck');
				}); 
				$('#allABox').on('checked', function (event) { 
					$('.A').jqxCheckBox('check');
				}); 
				$('#allABox').on('unchecked', function (event) { 
					$('.A').jqxCheckBox('uncheck');
				}); 
				$('#allDBox').on('checked', function (event) { 
					$('.D').jqxCheckBox('check');
				}); 
				$('#allDBox').on('unchecked', function (event) { 
					$('.D').jqxCheckBox('uncheck');
				}); 
				<?php if($solarSensor){?>
					$('#allSBox').on('checked', function (event) { 
						$('.S').jqxCheckBox('check');
					}); 
					$('#allSBox').on('unchecked', function (event) { 
						$('.S').jqxCheckBox('uncheck');
					}); 
				<?php }?>
				$('#avgAllBox').on('checked', function (event) { 
					$('.avg').jqxCheckBox('check');
				}); 
				$('#avgAllBox').on('unchecked', function (event) { 
					$('.avg').jqxCheckBox('uncheck');
				}); 
				$('#maxAllBox').on('checked', function (event) { 
					$('.max').jqxCheckBox('check');
				}); 
				$('#maxAllBox').on('unchecked', function (event) { 
					$('.max').jqxCheckBox('uncheck');
				}); 
				$('#minAllBox').on('checked', function (event) { 
					$('.min').jqxCheckBox('check');
				}); 
				$('#minAllBox').on('unchecked', function (event) { 
					$('.min').jqxCheckBox('uncheck');
				}); 
				$('#rangeAllBox').on('checked', function (event) { 
					$('.range').jqxCheckBox('check');
				}); 
				$('#rangeAllBox').on('unchecked', function (event) { 
					$('.range').jqxCheckBox('uncheck');
				}); 
			})
		</script>
		<script>
			function valueSet(x){
				$("#chosenValue").val(x);
				generate();
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
				generate();
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
					$("#intervalToday").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="yesterday"){
					$("#intervalYesterday").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="thisweek"){$("#intervalWeek").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});}
				if(x=="lastweek"){
					$("#intervalLastWeek").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="thismonth"){
					$("#intervalMonth").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="lastmonth"){
					$("#intervalLastMonth").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
				if(x=="custom"){
					$("#intervalCustom").mouseover(function() {
						$(this).css("opacity","1");
					}).mouseout(function() {
						$(this).css("opacity","1");
					});
				}
			}
			function generate() {	
				parameters = [];
				
				if($("#avgTBox").val()){
					parameters[parameters.length]  = "avg(T)";
				}
				if($("#avgHBox").val()){
					parameters[parameters.length]  = "avg(H)";
				}
				if($("#avgPBox").val()){
					parameters[parameters.length]  = "avg(P)";
				}
				if($("#avgWBox").val()){
					parameters[parameters.length]  = "avg(W)";
				}
				if($("#avgGBox").val()){
					parameters[parameters.length]  = "avg(G)";
				}
				if($("#avgDBox").val()){
					parameters[parameters.length]  = "avg(D)";
				}
				if($("#avgABox").val()){
					parameters[parameters.length]  = "avg(A)";
				}
				if($("#totalRBox").val()){
					parameters[parameters.length]  = "max(R)-min(R)";
				}
				<?php if($solarSensor){?>
					if($("#avgSBox").val()){
						parameters[parameters.length]  = "avg(S)";
					}
				<?php }?>
				if($("#maxTBox").val()){
					parameters[parameters.length]  = "max(Tmax)";
				}
				if($("#maxHBox").val()){
					parameters[parameters.length]  = "max(H)";
				}
				if($("#maxPBox").val()){
					parameters[parameters.length]  = "max(P)";
				}
				if($("#maxWBox").val()){
					parameters[parameters.length]  = "max(W)";
				}
				if($("#maxGBox").val()){
					parameters[parameters.length]  = "max(G)";
				}
				if($("#maxDBox").val()){
					parameters[parameters.length]  = "max(D)";
				}
				if($("#maxABox").val()){
					parameters[parameters.length]  = "max(A)";
				}
				<?php if($solarSensor){?>
					if($("#maxSBox").val()){
						parameters[parameters.length]  = "max(S)";
					}
				<?php }?>
				if($("#minTBox").val()){
					parameters[parameters.length]  = "min(Tmin)";
				}
				if($("#minHBox").val()){
					parameters[parameters.length]  = "min(H)";
				}
				if($("#minPBox").val()){
					parameters[parameters.length]  = "min(P)";
				}
				if($("#minWBox").val()){
					parameters[parameters.length]  = "min(W)";
				}
				if($("#minGBox").val()){
					parameters[parameters.length]  = "min(G)";
				}
				if($("#minDBox").val()){
					parameters[parameters.length]  = "min(D)";
				}
				if($("#minABox").val()){
					parameters[parameters.length]  = "min(A)";
				}
				<?php if($solarSensor){?>
					if($("#minSBox").val()){
						parameters[parameters.length]  = "min(S)";
					}
				<?php }?>
				if($("#rangeTBox").val()){
					parameters[parameters.length]  = "max(Tmax)-min(Tmin)";
				}
				if($("#rangeHBox").val()){
					parameters[parameters.length]  = "max(H)-min(H)";
				}
				if($("#rangePBox").val()){
					parameters[parameters.length]  = "max(P)-min(P)";
				}
				if($("#rangeWBox").val()){
					parameters[parameters.length]  = "max(W)-min(W)";
				}
				if($("#rangeGBox").val()){
					parameters[parameters.length]  = "max(G)-min(G)";
				}
				if($("#rangeDBox").val()){
					parameters[parameters.length]  = "max(D)-min(D)";
				}
				if($("#rangeABox").val()){
					parameters[parameters.length]  = "max(A)-min(A)";
				}
				<?php if($solarSensor){?>
					if($("#rangeSBox").val()){
						parameters[parameters.length]  = "max(S)-min(S)";
					}
				<?php }?>
				
				parameter = parameters.join();
				parameter = parameter.replace(/\(/g,"bracketL");
				parameter = parameter.replace(/\)/g,"bracketR");
				
				value = $("#chosenValue").val();
				interval = $("#chosenInterval").val();
				from = $("#customFrom").val();
				to = $("#customTo").val();
				$.ajax({
					type: "GET",
					url : "tableAjax.php?value="+value+"&interval="+interval+"&from="+from+"&to="+to+"&parameters="+parameter,
					success : function (json) {
						if($("#fullscreenBox").val()===false){
							$("#tableDiv").html(json);
						}
						else{
							$("#tableDiv").html("");
							html = "<html><head><link rel='stylesheet' type='text/css' href='<?php echo $pageURL.$path?>css/main.css' media='all' title='screen'><style>body{width:100%;background: transparent;color: white;margin: 0;padding: 0;font-family: '<?php echo $designFont?>',ArialNarrow,Arial,Helvetica,sans-serif;font-size: 1.0em;}.table th{ background: #<?php echo $color_schemes[$design2]['900']?>;color: #<?php echo $color_schemes[$design2]['font900']?>;}.table tr:nth-child(even) {background: #<?php echo $color_schemes[$design2]['400']?>;color: #<?php echo $color_schemes[$design2]['font400']?>;}.table tr:nth-child(odd) {background: #<?php echo $color_schemes[$design2]['600']?>;color: #<?php echo $color_schemes[$design2]['font600']?>;}.table tbody tr:hover td{background: #<?php echo $color_schemes[$design2]['800']?>;color: #<?php echo $color_schemes[$design2]['font800']?>;}.table tfoot td{background: #<?php echo $color_schemes[$design2]['900']?>;color: #<?php echo $color_schemes[$design2]['font900']?>;}.parameter{padding: 2px;width: 30px;opacity: 0.6;}.parameterTable{padding: 2px;width: 25px;opacity: 0.6;}</style></head><body>";
							html += json;
							html += "</body></html>";
							var w = window.open();
							$(w.document.body).html(html);
						}
					}
				});			
			}
			function csv() {	
				parameters = [];
				
				if($("#avgTBox").val()){
					parameters[parameters.length]  = "avg(T)";
				}
				if($("#avgHBox").val()){
					parameters[parameters.length]  = "avg(H)";
				}
				if($("#avgPBox").val()){
					parameters[parameters.length]  = "avg(P)";
				}
				if($("#avgWBox").val()){
					parameters[parameters.length]  = "avg(W)";
				}
				if($("#avgGBox").val()){
					parameters[parameters.length]  = "avg(G)";
				}
				if($("#avgDBox").val()){
					parameters[parameters.length]  = "avg(D)";
				}
				if($("#avgABox").val()){
					parameters[parameters.length]  = "avg(A)";
				}
				if($("#totalRBox").val()){
					parameters[parameters.length]  = "max(R)-min(R)";
				}
				<?php if($solarSensor){?>
					if($("#avgSBox").val()){
						parameters[parameters.length]  = "avg(S)";
					}
				<?php }?>
				if($("#maxTBox").val()){
					parameters[parameters.length]  = "max(Tmax)";
				}
				if($("#maxHBox").val()){
					parameters[parameters.length]  = "max(H)";
				}
				if($("#maxPBox").val()){
					parameters[parameters.length]  = "max(P)";
				}
				if($("#maxWBox").val()){
					parameters[parameters.length]  = "max(W)";
				}
				if($("#maxGBox").val()){
					parameters[parameters.length]  = "max(G)";
				}
				if($("#maxDBox").val()){
					parameters[parameters.length]  = "max(D)";
				}
				if($("#maxABox").val()){
					parameters[parameters.length]  = "max(A)";
				}
				<?php if($solarSensor){?>
					if($("#maxSBox").val()){
						parameters[parameters.length]  = "max(S)";
					}
				<?php }?>
				if($("#minTBox").val()){
					parameters[parameters.length]  = "min(Tmin)";
				}
				if($("#minHBox").val()){
					parameters[parameters.length]  = "min(H)";
				}
				if($("#minPBox").val()){
					parameters[parameters.length]  = "min(P)";
				}
				if($("#minWBox").val()){
					parameters[parameters.length]  = "min(W)";
				}
				if($("#minGBox").val()){
					parameters[parameters.length]  = "min(G)";
				}
				if($("#minDBox").val()){
					parameters[parameters.length]  = "min(D)";
				}
				if($("#minABox").val()){
					parameters[parameters.length]  = "min(A)";
				}
				<?php if($solarSensor){?>
					if($("#minSBox").val()){
						parameters[parameters.length]  = "min(S)";
					}
				<?php }?>
				if($("#rangeTBox").val()){
					parameters[parameters.length]  = "max(Tmax)-min(Tmin)";
				}
				if($("#rangeHBox").val()){
					parameters[parameters.length]  = "max(H)-min(H)";
				}
				if($("#rangePBox").val()){
					parameters[parameters.length]  = "max(P)-min(P)";
				}
				if($("#rangeWBox").val()){
					parameters[parameters.length]  = "max(W)-min(W)";
				}
				if($("#rangeGBox").val()){
					parameters[parameters.length]  = "max(G)-min(G)";
				}
				if($("#rangeDBox").val()){
					parameters[parameters.length]  = "max(D)-min(D)";
				}
				if($("#rangeABox").val()){
					parameters[parameters.length]  = "max(A)-min(A)";
				}
				<?php if($solarSensor){?>
					if($("#rangeSBox").val()){
						parameters[parameters.length]  = "max(S)-min(S)";
					}
				<?php }?>
				if(parameters.length==0){
					alert("No parameters selected.");
					return false;
				}
				parameter = parameters.join();
				parameter = parameter.replace(/\(/g,"bracketL");
				parameter = parameter.replace(/\(/g,"bracketR");
				
				value = $("#chosenValue").val();
				interval = $("#chosenInterval").val();
				from = $("#customFrom").val();
				to = $("#customTo").val();
				
				url = "tableCSV.php?value="+value+"&interval="+interval+"&from="+from+"&to="+to+"&parameters="+parameter;
				window.location.href = url;
			}			
			
		</script>
		<?php include("../../footer.php")?>
	</body>
</html>