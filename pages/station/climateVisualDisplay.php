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
	#	Data for long-term statistics visualizations
	#
	# 	A script that generates data for long-term statistics visualizations 
	#	of temperature
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
	
	// Get date
	$q = $_GET['q'];
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
?>
	<div class="resizer resizer4">
		<div class="inner-resizer">
			<div id="visualization" style="height:400px;margin-left:auto;margin-right:auto;width:100%" class="varGraphs4"></div>
		</div>
	</div>
	<div style="width:100%;text-align:center">
		<input id="visualValues" value="<?php echo lang("values","c")?>" type="button" class="button2" style="font-size: 0.7em;font-family:'<?php echo $designFont?> Narrow'">
	</div>
	<br>
	<input type="button" class="button2" value="<?php echo lang("average","c")?>" id="setVisualAvg" style="font-size:0.8em">
	<input type="button" class="button2" value="<?php echo lang("maximum","c")?>" id="setVisualMax" style="font-size:0.8em">
	<input type="button" class="button2" value="<?php echo lang("minimum",'c')?>" id="setVisualMin" style="font-size:0.8em">
	<input type="button" class="button2" value="<?php echo lang('range','c')?>" id="setVisualRange" style="font-size:0.8em">
	<script>
		$('.resizer4').resizable({
			resize: function() {
				selectedDiv = $(this).find(".varGraphs4");
				chart = selectedDiv.highcharts();
				chart.setSize(
					this.offsetWidth - 50, 
					this.offsetHeight - 50,
					false
				);
			},
		});
		visual('avg');
		function visual(parameter){
			optionsVisual = {
				chart : {
					renderTo : 'visualization',
					type : 'heatmap',
				},
				title: {
					text:  '',
				},
				xAxis: {
					categories: [
						<?php
							for($i=1;$i<=31;$i++){
								echo "'".$i."',";
							}
						?>
					],
					title: {
						text: ''
					},
				},
				yAxis: {
					categories: [	
						<?php
							for($i=1;$i<13;$i++){
								echo "'".lang("month".$i,'c')."',";
							}
						?>
					],
					reversed: true,
					title: {
						text: ''
					},
				},
				colorAxis: {
					minColor: '#fff',
					maxColor: '#000',
				},
				tooltip: {
					pointFormat: '{point.value}',
				},
				legend: {
					symbolWidth: 400
				},
				series: [{
					name: '',
					borderWidth: 1,
					data: [],
					dataLabels: {
						enabled: false,
						color: '#FFFFFF'
					}
				}]
			};

			$.ajax({
				url : "climateVisual.php?q="+parameter+"&var=<?php echo $var?>",
				dataType : 'json',
				success : function (json) {
					optionsVisual.colorAxis.minColor = json['mincolor1'];
					optionsVisual.colorAxis.maxColor = json['maxcolor1'];
					optionsVisual.series[0].data = eval(json['data1']);
					optionsVisual.series[0].name = json['name1'];
					optionsVisual.title.text = json['title1'];
					optionsVisual.series[0].dataLabels.color = json['labels1'];
					chart2 = new Highcharts.Chart(optionsVisual);
					var showValues = true;
				},
			});
			var showValues = true;
			$('#visualValues').click(function() {
				if(showValues){
					$('#visualValues').val("<?php echo lang("values","c")?>");
				}
				else{
					$('#visualValues').val("<?php echo lang("values","c")?>");
				}
				chart2.series[0].update({
					dataLabels: {
						enabled: showValues,
					}
				});
				showValues = !showValues;
			});				
		}
		$('#setVisualAvg').click(function() {
			visual('avg');
		});
		$('#setVisualMax').click(function() {
			visual('max');
		});
		$('#setVisualMin').click(function() {
			visual('min');
		});
		$('#setVisualRange').click(function() {
			visual('range');
		});
	</script>