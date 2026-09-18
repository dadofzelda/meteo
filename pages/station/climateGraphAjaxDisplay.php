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
	#	Data for long-term statistics temperature graphs
	#
	# 	A script that generates data for long-term statistics graph.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../config.php");
	include($baseURL."header.php");
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	include_once("climateFunctions.php");

?>
	<style>
		.chart{
			width: 30px;
			padding: 15px;
			opacity: 0.8;
			cursor: pointer;
		}
		.chart:hover{
			opacity: 1;
		}
	</style>
	<?php
		if($var!="R"){
	?>
		<div class="resizer resizer3">
			<div class="inner-resizer">
				<div id="yearGraph" style="height:400px;margin-left:auto;margin-right:auto;width:100%" class="varGraphs3"></div>
			</div>
		</div>
		<input type="button" class="button2" value="<?php echo lang("average","c")?>" id="setYearAvg" style="font-size:0.8em">
		<input type="button" class="button2" value="<?php echo lang("maximum","c")?>" id="setYearMax" style="font-size:0.8em">
		<input type="button" class="button2" value="<?php echo lang("minimum","c")?>" id="setYearMin" style="font-size:0.8em">
		<input type="button" class="button2" value="<?php echo lang('range','c')?>" id="setYearRange" style="font-size:0.8em">
	<?php
		}
		else{
	?>
			<div class="resizer resizer3">
				<div class="inner-resizer">
					<div id="yearGraph" style="height:400px;margin-left:auto;margin-right:auto;width:100%" class="varGraphs3"></div>
				</div>
			</div>
			<table style="margin-left:auto;margin-right:auto">
				<tr>
					<th colspan="2" style="text-align:center">
						<?php echo lang('total','c')?>
					</th>
					<th colspan="2" style="text-align:center">
						<?php echo lang("average","c")?>
					</th >
					<th colspan="2" style="text-align:center">
						<?php echo lang("maximum","c")?>
					</th>
				</tr>
				<tr>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="chart" id="setYearTotalLine" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="chart" id="setYearTotalBar" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="chart" id="setYearAvgLine" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="chart" id="setYearAvgBar" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/linechart.png" class="chart" id="setYearMaxLine" alt=''>
					</th>
					<th>
						<img src="<?php echo $pageURL.$path?>icons/barchart.png" class="chart" id="setYearMaxBar" alt=''>
					</th>
				</tr>
			</table>
	<?php
		}
	?>
	<script>
		$('.resizer3').resizable({
			resize: function() {
				selectedDiv = $(this).find(".varGraphs3");
				chart = selectedDiv.highcharts();
				chart.setSize(
					this.offsetWidth - 50, 
					this.offsetHeight - 50,
					false
				);
			},
		});
		<?php
			if($var!="R"){
		?>
				graphYear('avg');
				function graphYear(q){
					optionsYearGraph = {
						chart : {
							renderTo : 'yearGraph',
							type : 'spline',
							zoomType: 'xy',
						},
						title: {
							text: '',
						},
						credits: {
							text: '<?php echo $highChartsCreditsText?>',
							href: '<?php echo $pageURL.$path?>'
						},
						xAxis: {
							categories: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>']
						},
						yAxis: {
							title: {
								text: '<?php echo $heading?> (<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo $UoM?>',
							shared: true
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0
						},
						series: []
					};
					$.ajax({
						url : "climateGraphAjax.php?q="+q+"&var=<?php echo $var?>",
						dataType : 'json',
						success : function (json) {
							optionsYearGraph.series = json['data'];
							optionsYearGraph.title.text = json['title'];
							chart = new Highcharts.Chart(optionsYearGraph);
						},
					});
				}
				function graphYearRange(){
					optionsYearGraph = {
						chart : {
							renderTo : 'yearGraph',
							type : 'areasplinerange',
							zoomType: 'xy',
						},
						title: {
							text: '',
						},
						xAxis: {
							categories: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>']
						},
						yAxis: {
							title: {
								text: '<?php echo $heading?> (<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo $UoM?>',
							shared: true
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0
						},
						plotOptions: {
							series: {
								fillOpacity: 0.3
							}
						},
						series: []
					};
					$.ajax({
						url : "climateGraphAjax.php?q=range&var=<?php echo $var?>",
						dataType : 'json',
						success : function (json) {
							optionsYearGraph.series = json['data'];
							optionsYearGraph.title.text = json['title'];
							chart = new Highcharts.Chart(optionsYearGraph);
						},
					});
				}
				$('#setYearAvg').click(function(){
					graphYear('avg');
				});
				$('#setYearMax').click(function(){
					graphYear('max');
				});
				$('#setYearMin').click(function(){
					graphYear('min');
				});
				$('#setYearRange').click(function(){
					graphYearRange();
				});
		<?php
			}
			else{
		?>
				graphYear('total','column');
				function graphYear(q,type){
					optionsYearGraph = {
						chart : {
							renderTo : 'yearGraph',
							type : type,
							zoomType: 'xy',
						},
						title: {
							text: '',
						},
						xAxis: {
							categories:  ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>']
						},
						yAxis: {
							title: {
								text: 'Precipitation (<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo $UoM?>',
							shared: true
						},
						legend: {
							layout: 'vertical',
							align: 'right',
							verticalAlign: 'middle',
							borderWidth: 0
						},
						series: []
					};
					$.ajax({
						url : "climateGraphAjax.php?q="+q+"&var=R",
						dataType : 'json',
						success : function (json) {
							optionsYearGraph.series = json['data'];
							optionsYearGraph.title.text = json['title'];
							chart = new Highcharts.Chart(optionsYearGraph);
						},
					});
				}
				$('#setYearAvgLine').click(function() {
					graphYear('avg','spline');
				});
				$('#setYearAvgBar').click(function() {
					graphYear('avg','column');
				});
				$('#setYearMaxLine').click(function() {
					graphYear('max','spline');
				});
				$('#setYearMaxBar').click(function() {
					graphYear('max','column');
				});
				$('#setYearTotalLine').click(function() {
					graphYear('total','spline');
				});
				$('#setYearTotalBar').click(function() {
					graphYear('total','column');
				});
		<?php
			}
		?>
	</script>