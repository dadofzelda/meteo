<?php

	# 		Dynamic Graph
	# 		Namespace:		dynamicGraph
	#		Meteotemplate Block 

	# 		v1.0 - Mar 24, 2017
	#  			- initial release
	# 		v2.0 - Mar 27, 2017
	#  			- added support for Cumulus
	# 		v2.1 - Aug 27, 2017
	# 			- bug fixes
	
		
	// load theme
	$designTheme = json_decode(file_get_contents("../../css/theme.txt"),true);
	$theme = $designTheme['theme'];
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");
	
	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "First you need to go to your admin panel and use the blocks settings section to create the settings file.";
		die;
	}

	$var = trim(strtoupper($dynamicGraphParameter));
	
	include("functions.php");	

	$this_tz_str = date_default_timezone_get();
	$this_tz = new DateTimeZone($this_tz_str);
	$now = new DateTime("now", $this_tz);
	$offset = $this_tz->getOffset($now);

	$result = mysqli_query($con,"
		SELECT DateTime,".$var."
		FROM alldata
		ORDER BY DateTime DESC
		LIMIT ".$dynamicGraphPreload."
		"
	);
	while($row = mysqli_fetch_array($result)){
		$data['dates'][] = (strtotime($row['DateTime']) + $offset) * 1000;
		$data['data'][] = chooseConvertor($row[$var]);
	}
	$data['dates'] = array_reverse($data['dates']);
	$data['data'] = array_reverse($data['data']);

	$color1 = $theme=="dark" ? "#fff" : "#000";
	$color2 = $theme=="dark" ? $color_schemes[$design2]['200'] : $color_schemes[$design2]['700'];

	if($designFont=="Bree Serif" || $designFont2=="Bree Serif"){
		$fontURL = "http://fonts.googleapis.com/css?family=Bree+Serif&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="PT Sans" || $designFont2=="PT Sans"){
		$fontURL = "http://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="Roboto" || $designFont2=="Roboto"){
		$fontURL = "http://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin,cyrillic-ext,latin-ext";
	}
	if($designFont=="Dosis" || $designFont2=="Dosis"){
		$fontURL = "http://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Ubuntu" || $designFont2=="Ubuntu"){
		$fontURL = "http://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Lobster" || $designFont2=="Lobster"){
		$fontURL = "http://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext";
	}
	if($designFont=="Kaushan Script" || $designFont2=="Kaushan Script"){
		$fontURL = "http://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext";
	}
	if($designFont=="Open Sans" || $designFont2=="Open Sans"){
		$fontURL = "http://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Play" || $designFont2=="Play"){
		$fontURL = "http://fonts.googleapis.com/css?family=Play:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Open Sans Condensed" || $designFont2=="Open Sans Condensed"){
		$fontURL = "http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,latin-ext";
	}
	if($designFont=="Anton" || $designFont2=="Anton"){
		$fontURL = "http://fonts.googleapis.com/css?family=Anton&subset=latin,latin-ext";
	}
	if($designFont=="Inconsolata" || $designFont2=="Inconsolata"){
		$fontURL = "http://fonts.googleapis.com/css?family=Inconsolata:400,700&subset=latin,latin-ext";
	}
	if($designFont=="Righteous" || $designFont2=="Righteous"){
		$fontURL = "http://fonts.googleapis.com/css?family=Righteous&subset=latin,latin-ext";
	}
	if($designFont=="Marck Script" || $designFont2=="Marck Script"){
		$fontURL = "http://fonts.googleapis.com/css?family=Marck+Script&subset=latin,latin-ext";
	}
	if($designFont=="Poiret One" || $designFont2=="Poiret One"){
		$fontURL = "http://fonts.googleapis.com/css?family=Poiret+One&subset=latin,latin-ext";
	}
	if($designFont=="Cutive Mono" || $designFont2=="Cutive Mono"){
		$fontURL = "http://fonts.googleapis.com/css?family=Cutive+Mono&subset=latin,latin-ext";
	}
?>
	<style>
		
	</style>
	<div id="dynamicGraphMain" style="width:98%;height: 400px; margin: 0 auto"></div>
	<script>
		$(document).ready(function () {
			Highcharts.setOptions({
				global: {
					useUTC: true
				}
			});
			Highcharts.createElement('link', {
				href: '<?php echo $fontURL?>',
				rel: 'stylesheet',
				type: 'text/css'
			}, null, document.getElementsByTagName('head')[0]);
			Highcharts.chart('dynamicGraphMain', {
				chart: {
					type: 'spline',
					animation: Highcharts.svg,
					backgroundColor: null,
					style: {
						fontFamily: "'<?php echo $designFont?>', sans-serif"
					},
				},
				credits: {
					enabled: false
				},
				title: {
					text: '<?php echo $heading?>',
					style: {
						color: "<?php echo $color1?>",
						//fontSize: '20px'
					}
				},
				xAxis: {
					type: 'datetime',
					tickPixelInterval: 50,
					gridLineColor: '<?php echo $color1?>',
					labels: {
						style: {
							color: '<?php echo $color1?>',
							fontSize: '<?php echo $customGraphFontSize?>'
						}
					},
					lineColor: '<?php echo $color1?>',
					minorGridLineColor: '<?php echo $color1?>',
					tickColor: '<?php echo $color1?>',
					title: {
						style: {
							color: '<?php echo $color1?>',
							fontSize: '<?php echo $customGraphFontSize?>'
						}
					}
				},
				yAxis: {
					title: {
						text: '<?php echo $UoM?>',
						style: {
							color: '<?php echo $color1?>',
							fontSize: '<?php echo $customGraphFontSize?>'
						}
					},
					gridLineColor: '<?php echo $color1?>',
					gridLineWidth: 0,
					labels: {
						style: {
							color: '<?php echo $color1?>',
							fontSize: '<?php echo $customGraphFontSize?>'
						}
					},
					lineColor: '<?php echo $color1?>',
					minorGridLineColor: '<?php echo $color1?>',
					tickColor: '<?php echo $color1?>',
					tickWidth: 1
				},
				tooltip: {
					formatter: function () {
						return '<b>' + this.series.name + '</b><br/>' +
							Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) + '<br/>' +
							Highcharts.numberFormat(this.y, 2);
					}
				},
				legend: {
					enabled: false
				},
				exporting: {
					enabled: false
				},
				series: [{
					name: '<?php echo $heading?>',
					color: "#<?php echo $color2?>",
					marker:{
						enabled: true,
						fillColor: "#<?php echo $color2?>",
						radius: 4
					},
					data: [
						<?php 
							for($i=0;$i<count($data['data']);$i++){
								echo "[".$data['dates'][$i].",".$data['data'][$i]."],";
							}
						?>
					]
				}]
			});
		});
		setInterval(updateDynamicGraph, (<?php echo $dynamicGraphInterval?>*1000));
		function updateDynamicGraph(){
			$.ajax({
				url : "homepage/blocks/dynamicGraph/updateData.php?var=<?php echo $var?>",
				dataType : 'json',
				success : function (json) {
					x = json['U'];
					y = json['point'];
					chart = $("#dynamicGraphMain").highcharts();
					chart.series[0].addPoint({
						x: x,
						y: y,
					},true);
				}
			});
		}
	</script>
