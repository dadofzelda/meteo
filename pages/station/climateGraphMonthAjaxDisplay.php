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
	#	Data for long-term statistics monthly temperature graphs
	#
	# 	A script that generates data for long-term statistics hourly graph 
	#	of temperature
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."header.php");
	
	$q = $_GET['q'];
	$month = $_GET['month'];
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	include_once("climateFunctions.php");

?>
	<div class="resizer resizer1">
		<div class="inner-resizer">
			<div id="monthGraph" style="height:400px;margin-left:auto;margin-right:auto;width:100%" class="varGraphs1"></div>
		</div>
	</div>
	<?php
		if($var!="R"){
	?>
			<table class="table">
				<thead>
					<tr>
					<?php
						for($i=1;$i<13;$i++){
					?>
						<th style="text-align:center">
							<?php echo lang("month".$i."short","c")?>
						</th>
					<?php
						}
					?>
					</tr>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('avgAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>Avg">
							</th>
						<?php
							}
						?>
					</tr>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('minimumAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>Min">
							</th>
						<?php
							}
						?>
					</tr>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('maximumAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>Max">
							</th>
						<?php
							}
						?>
					</tr>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('range','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>Range">
							</th>
						<?php
							}
						?>
					</tr>
				</thead>
			</table>
	<?php
		}
		else{
	?>
			<table class="table">
				<thead>
					<tr>
					<?php
						for($i=1;$i<13;$i++){
					?>
						<th style="text-align:center">
							<span id="setMonth<?php echo $i?>" class="monthSetter">
								<?php echo lang("month".$i."short","c")?>
							</span>
						</th>
					<?php
						}
					?>
					</tr>
					<tr>
						<?php
						for($i=1;$i<13;$i++){
							?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('total','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>Avg">
							</th>
							<?php
						}
						?>
					</tr>
				</thead>
			</table>
	<?php
		}
	?>
	<script>
		$('.resizer1').resizable({
			resize: function() {
				selectedDiv = $(this).find(".varGraphs1");
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
				graphMonth(1,"avg");
				function graphMonth(month,q){
					optionsMonthGraph = {
						chart : {
							renderTo : 'monthGraph',
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
							categories: []
						},
						yAxis: {
							title: {
								text: '<?php echo $heading?> (<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo " ".$UoM?>',
							valueDecimals: '<?php echo $dp?>',
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
						url : "climateGraphMonthAjax.php?month="+month+"&q="+q+"&var=<?php echo $var?>",
						dataType : 'json',
						success : function (json) {
							optionsMonthGraph.series = json['data'];
							optionsMonthGraph.title.text = json['title'];
							optionsMonthGraph.xAxis.categories = json['categories'];
							chart = new Highcharts.Chart(optionsMonthGraph);
						},
					});
				}
				function graphMonthRange(month){
					optionsMonthGraph = {
						chart : {
							renderTo : 'monthGraph',
							type : 'areasplinerange',
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
							categories: []
						},
						yAxis: {
							title: {
								text: '<?php echo $heading?> (°<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo " ".$UoM?>',
							valueDecimals: '<?php echo $dp?>',
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
						url : "climateGraphMonthAjax.php?q=range&month="+month+"&var=<?php echo $var?>",
						dataType : 'json',
						success : function (json) {
							optionsMonthGraph.series = json['data'];
							optionsMonthGraph.title.text = json['title'];
							chart = new Highcharts.Chart(optionsMonthGraph);
						},
					});
				}
				<?php 
					for($i=1;$i<13;$i++){
				?>
					$('#setMonth<?php echo $i?>Avg').click(function() {
						graphMonth(<?php echo $i?>,'avg');
					});
					$('#setMonth<?php echo $i?>Min').click(function() {
						graphMonth(<?php echo $i?>,'min');
					});
					$('#setMonth<?php echo $i?>Max').click(function() {
						graphMonth(<?php echo $i?>,'max');
					});
					$('#setMonth<?php echo $i?>Range').click(function() {
						graphMonthRange(<?php echo $i?>);
					});
				<?php
					}
				?>
		<?php
			}
			else{
		?>
				graphMonth(1);
				function graphMonth(month){
					
					optionsMonthGraph = {
						chart : {
							renderTo : 'monthGraph',
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
							categories: []
						},
						yAxis: {
							title: {
								text: 'Precipitation (<?php echo $UoM?>)'
							},
						},
						tooltip: {
							valueSuffix: '<?php echo " ".$UoM?>',
							valueDecimals: '<?php echo $dp?>',
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
						url : "climateGraphMonthAjax.php?month="+month+"&var=<?php echo $var?>",
						dataType : 'json',
						success : function (json) {
							optionsMonthGraph.series = json['data'];
							optionsMonthGraph.title.text = json['title'];
							optionsMonthGraph.xAxis.categories = json['categories'];
							chart = new Highcharts.Chart(optionsMonthGraph);
						},
					});
				}
				<?php
				for($i=1;$i<13;$i++){
				?>
				$('#setMonth<?php echo $i?>Avg').click(function() {
					graphMonth(<?php echo $i?>,'avg');
				});
				<?php
				}
				?>
		<?php
			}
		?>
	</script>