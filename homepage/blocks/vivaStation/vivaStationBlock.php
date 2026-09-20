<?php

	# 		ViVa Station
	# 		Namespace:		vivaStation
	#		Meteotemplate Block (custom, not from meteotemplate.com)

	# 		v1.0 - Sep 21, 2026
	# 			- initial release
	#			- shows live values from a Sjofartsverket ViVa marine station
	#			  (https://viva.sjofartsverket.se/), any of the ~185 available
	#			  stations. Different stations report different parameters
	#			  (wind, water level, water temperature, currents, sight,
	#			  air pressure/temperature/humidity, river flow) - the block
	#			  renders whatever the selected station actually reports,
	#			  no hardcoded assumption about which fields exist.

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

	// unique id in case this block is ever placed more than once on the same page
	$blockUID = "vivaStation_".substr(md5(uniqid()),0,8);

?>
	<style>
		#<?php echo $blockUID?> .vivaStationName{
			font-weight: bold;
			font-size: 1.2em;
			text-align: center;
			margin-bottom: 5px;
		}
		#<?php echo $blockUID?> .vivaUpdated{
			text-align: center;
			opacity: 0.7;
			font-size: 0.85em;
			margin-bottom: 10px;
		}
		#<?php echo $blockUID?> .vivaSwitcher{
			text-align: center;
			margin-bottom: 10px;
		}
		#<?php echo $blockUID?> .vivaTiles{
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			gap: 10px;
		}
		#<?php echo $blockUID?> .vivaTile{
			min-width: 100px;
			padding: 8px 12px;
			text-align: center;
			border-radius: 6px;
			background: rgba(128,128,128,0.12);
		}
		#<?php echo $blockUID?> .vivaTile .vivaTileIcon{
			font-size: 1.4em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .vivaTile .vivaTileValue{
			font-weight: bold;
			font-size: 1.1em;
		}
		#<?php echo $blockUID?> .vivaTile .vivaTileLabel{
			font-size: 0.8em;
			opacity: 0.8;
		}
		#<?php echo $blockUID?> .vivaMsg{
			text-align: center;
			opacity: 0.8;
			padding: 15px 0;
		}
	</style>
	<div id="<?php echo $blockUID?>">
		<?php if($vivaAllowSwitch=="true"){?>
		<div class="vivaSwitcher">
			<select class="button2 vivaStationSelect">
				<option value="<?php echo $vivaStationID?>"><?php echo lang('loading','c')?>...</option>
			</select>
		</div>
		<?php }?>
		<div class="vivaStationName">&nbsp;</div>
		<div class="vivaTiles"><div class="vivaMsg"><i class="fa fa-spinner fa-spin"></i></div></div>
		<div class="vivaUpdated">&nbsp;</div>
	</div>
	<script>
	(function(){
		var blockUID = "<?php echo $blockUID?>";
		var defaultStationID = "<?php echo $vivaStationID?>";
		var allowSwitch = <?php echo ($vivaAllowSwitch=="true") ? "true" : "false"?>;
		var $block = $("#"+blockUID);

		// icon + friendly label per ViVa sample "Type" - falls back gracefully
		// for any type not in this list, since not every station reports the
		// same parameters.
		var vivaTypeInfo = {
			"wind":       {icon:"fa-flag"},
			"heading":    {icon:"fa-location-arrow"},
			"level":      {icon:"fa-arrows-v"},
			"watertemp":  {icon:"fa-thermometer-half"},
			"airtemp":    {icon:"fa-thermometer-half"},
			"pressure":   {icon:"fa-tachometer"},
			"air":        {icon:"fa-tint"},
			"sight":      {icon:"fa-eye"},
			"water":      {icon:"fa-tint"},
			"stream":     {icon:"fa-refresh"}
		};

		function rememberStation(id){
			try{ localStorage.setItem("vivaStationLast", id); }catch(e){}
		}
		function lastRememberedStation(){
			try{ return localStorage.getItem("vivaStationLast"); }catch(e){ return null; }
		}

		function renderCurrent(data){
			var result = data && data.GetSingleStationWithDirectionsAsParametersResult;
			if(!result || result.Felmeddelande || !result.Samples || result.Samples.length===0){
				$block.find(".vivaStationName").html(result && result.Name ? result.Name : "");
				$block.find(".vivaTiles").html("<div class='vivaMsg'>"+"<?php echo lang('no data available','c')?>"+"</div>");
				$block.find(".vivaUpdated").html("&nbsp;");
				return;
			}
			$block.find(".vivaStationName").text(result.Name);
			var tilesHtml = "";
			$.each(result.Samples, function(i, sample){
				var info = vivaTypeInfo[sample.Type] || {icon:"fa-info-circle"};
				tilesHtml += "<div class='vivaTile'>";
				tilesHtml += "<div class='vivaTileIcon'><i class='fa "+info.icon+"'></i></div>";
				tilesHtml += "<div class='vivaTileValue'>"+sample.Value+(sample.Unit ? " "+sample.Unit : "")+"</div>";
				tilesHtml += "<div class='vivaTileLabel'>"+sample.Name+"</div>";
				tilesHtml += "</div>";
			});
			$block.find(".vivaTiles").html(tilesHtml);
			var updated = result.Samples[0].Updated || "";
			$block.find(".vivaUpdated").text(updated ? ("<?php echo lang('updated','c')?>"+": "+updated) : "");
		}

		function loadCurrent(id){
			$block.find(".vivaTiles").html("<div class='vivaMsg'><i class='fa fa-spinner fa-spin'></i></div>");
			$.getJSON("homepage/blocks/vivaStation/vivaCurrentAjax.php?id="+encodeURIComponent(id))
				.done(function(data){
					renderCurrent(data);
				})
				.fail(function(){
					$block.find(".vivaTiles").html("<div class='vivaMsg'>"+"<?php echo lang('no data available','c')?>"+"</div>");
				});
		}

		if(allowSwitch){
			$.getJSON("homepage/blocks/vivaStation/vivaStationsListAjax.php")
				.done(function(data){
					var stations = (data && data.GetStationsResult && data.GetStationsResult.Stations) || [];
					stations.sort(function(a,b){ return a.Name.localeCompare(b.Name); });
					var startID = lastRememberedStation() || defaultStationID;
					var optionsHtml = "";
					var found = false;
					$.each(stations, function(i, s){
						if(String(s.ID)===String(startID)){ found = true; }
						optionsHtml += "<option value='"+s.ID+"'"+(String(s.ID)===String(startID) ? " selected" : "")+">"+s.Name+"</option>";
					});
					if(!found){ startID = defaultStationID; }
					$block.find(".vivaStationSelect").html(optionsHtml);
					$block.find(".vivaStationSelect").on("change", function(){
						var id = $(this).val();
						rememberStation(id);
						loadCurrent(id);
					});
					loadCurrent(startID);
				})
				.fail(function(){
					loadCurrent(defaultStationID);
				});
		}
		else{
			loadCurrent(defaultStationID);
		}
	})();
	</script>
