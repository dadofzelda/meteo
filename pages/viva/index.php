<?php

	############################################################################
	#
	#	ViVa (Sjofartsverket marine data) page - custom, not from
	#	meteotemplate.com. See tech/projects/meteo/08-viva-block-och-sida.md
	#	in the vault for background.
	#
	#	Lets a visitor pick one or several of Sjofartsverket's ~185 ViVa
	#	marine stations (wind, water level, water temperature, currents,
	#	sight, air pressure/temperature/humidity, river flow - varies per
	#	station) and see recent history for each parameter, one chart per
	#	parameter with all selected stations that report it overlaid.
	#
	############################################################################

	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?> - ViVa</title>
		<?php metaHeader()?>
		<script src="https://code.highcharts.com/stock/highstock.js"></script>
		<style>
			#vivaMain{
				width: 90%;
				margin-left: auto;
				margin-right: auto;
			}
			#vivaStationFilter{
				width: 100%;
				max-width: 400px;
				margin-bottom: 10px;
			}
			#vivaStationListBox{
				max-width: 400px;
				max-height: 300px;
				overflow-y: auto;
				border: 1px solid #<?php echo $color_schemes[$design2]['900']?>;
				padding: 8px;
				margin-bottom: 10px;
			}
			.vivaStationCheckRow{
				padding: 2px 0;
			}
			#vivaLoadButton{
				margin-bottom: 20px;
			}
			.vivaParamChart{
				width: 100%;
				height: 350px;
				margin-bottom: 25px;
			}
			#vivaStatusMsg{
				opacity: 0.8;
				padding: 10px 0;
			}
		</style>
	</head>
	<body style="padding:0px">
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main">
			<div id="vivaMain">
				<br>
				<h2><?php echo lang('viva marine data','c')?></h2>
				<p><?php echo lang('viva page description','c')?></p>
				<input type="text" id="vivaStationFilter" class="button2" placeholder="<?php echo lang('filter stations','c')?>...">
				<div id="vivaStationListBox">
					<div id="vivaStatusMsg"><i class="fa fa-spinner fa-spin"></i> <?php echo lang('loading','c')?>...</div>
				</div>
				<button id="vivaLoadButton" class="button2"><?php echo lang('show selected stations','c')?></button>
				<div id="vivaResultsDiv"></div>
			</div>
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
			var vivaAllStations = [];
			var vivaTypeInfo = {
				"wind":       {label:"<?php echo lang('wind speed','c')?>"},
				"heading":    {label:"<?php echo lang('direction','c')?>"},
				"level":      {label:"<?php echo lang('water level','c')?>"},
				"watertemp":  {label:"<?php echo lang('water temperature','c')?>"},
				"airtemp":    {label:"<?php echo lang('temperature','c')?>"},
				"pressure":   {label:"<?php echo lang('pressure','c')?>"},
				"air":        {label:"<?php echo lang('humidity','c')?>"},
				"sight":      {label:"<?php echo lang('visibility','c')?>"},
				"water":      {label:"<?php echo lang('water flow','c')?>"},
				"stream":     {label:"<?php echo lang('current','c')?>"}
			};

			$(document).ready(function(){

				$.getJSON("vivaStationsListAjax.php")
					.done(function(data){
						vivaAllStations = (data && data.GetStationsResult && data.GetStationsResult.Stations) || [];
						vivaAllStations.sort(function(a,b){ return a.Name.localeCompare(b.Name); });
						renderStationList(vivaAllStations);
					})
					.fail(function(){
						$("#vivaStationListBox").html("<?php echo lang('no data available','c')?>");
					});

				function renderStationList(stations){
					var html = "";
					$.each(stations, function(i, s){
						html += "<div class='vivaStationCheckRow'><label><input type='checkbox' class='vivaStationCheck' value='"+s.ID+"' data-name=\""+s.Name+"\"> "+s.Name+"</label></div>";
					});
					$("#vivaStationListBox").html(html);
				}

				$("#vivaStationFilter").on("keyup", function(){
					var checkedIds = {};
					$(".vivaStationCheck:checked").each(function(){ checkedIds[$(this).val()] = true; });
					var term = $(this).val().toLowerCase();
					var filtered = $.grep(vivaAllStations, function(s){
						return s.Name.toLowerCase().indexOf(term) !== -1;
					});
					renderStationList(filtered);
					// restore checked state for stations still in view
					$.each(checkedIds, function(id){
						$(".vivaStationCheck[value='"+id+"']").prop("checked", true);
					});
				});

				$("#vivaLoadButton").on("click", function(){
					var selected = [];
					$(".vivaStationCheck:checked").each(function(){
						selected.push({id: $(this).val(), name: $(this).data("name")});
					});
					if(selected.length===0){
						$("#vivaResultsDiv").html("<div id='vivaStatusMsg'><?php echo lang('select country','c')?></div>");
						return;
					}
					loadSelectedStations(selected);
				});

				function loadSelectedStations(selected){
					$("#vivaResultsDiv").html("<div id='vivaStatusMsg'><i class='fa fa-spinner fa-spin'></i> <?php echo lang('loading','c')?>...</div>");

					// step 1: find out which parameters each selected station currently reports
					var currentCalls = $.map(selected, function(st){
						return $.getJSON("vivaCurrentAjax.php?id="+encodeURIComponent(st.id)).then(function(data){
							return {station: st, data: data};
						}, function(){
							return {station: st, data: null};
						});
					});

					$.when.apply($, currentCalls).done(function(){
						var results = currentCalls.length===1 ? [arguments[0]] : Array.prototype.slice.call(arguments);
						// build map: paramName -> {type, unit, stations: [{id,name}]}
						var paramMap = {};
						$.each(results, function(i, r){
							var result = r.data && r.data.GetSingleStationWithDirectionsAsParametersResult;
							if(!result || result.Felmeddelande || !result.Samples){ return; }
							$.each(result.Samples, function(j, sample){
								if(!paramMap[sample.Name]){
									paramMap[sample.Name] = {type: sample.Type, unit: sample.Unit, stations: []};
								}
								paramMap[sample.Name].stations.push(r.station);
							});
						});

						if($.isEmptyObject(paramMap)){
							$("#vivaResultsDiv").html("<div id='vivaStatusMsg'><?php echo lang('no data available','c')?></div>");
							return;
						}

						$("#vivaResultsDiv").empty();
						$.each(paramMap, function(paramName, info){
							buildParamChart(paramName, info);
						});
					});
				}

				function buildParamChart(paramName, info){
					var chartId = "vivaChart_"+paramName.replace(/[^a-zA-Z0-9]/g,"_");
					var typeInfo = vivaTypeInfo[info.type] || {label: paramName};
					$("#vivaResultsDiv").append("<div class='vivaParamChart' id='"+chartId+"'></div>");

					var historyCalls = $.map(info.stations, function(st){
						return $.getJSON("vivaHistoryAjax.php?id="+encodeURIComponent(st.id)+"&param="+encodeURIComponent(paramName)).then(function(data){
							return {station: st, data: data};
						}, function(){
							return {station: st, data: null};
						});
					});

					$.when.apply($, historyCalls).done(function(){
						var results = historyCalls.length===1 ? [arguments[0]] : Array.prototype.slice.call(arguments);
						var series = [];
						$.each(results, function(i, r){
							var hist = r.data && r.data.GetHistoryResult && r.data.GetHistoryResult.StationHistory;
							if(!hist){ return; }
							var seriesData = $.map(hist, function(pt){
								var t = Date.parse(pt.Time.replace(" ","T"));
								var v = parseFloat(pt.Value);
								if(isNaN(v)){ return null; }
								return [t, v];
							});
							series.push({name: r.station.name, data: seriesData});
						});

						if(series.length===0){
							$("#"+chartId).html("<div id='vivaStatusMsg'>"+paramName+": <?php echo lang('no data available','c')?></div>");
							return;
						}

						Highcharts.stockChart(chartId, {
							title: {text: paramName+" ("+typeInfo.label+")"+(info.unit ? " ["+info.unit+"]" : "")},
							credits: {enabled: false},
							rangeSelector: {selected: 1},
							xAxis: {type: "datetime"},
							series: series
						});
					});
				}

			});
		</script>
	</body>
</html>
