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
	#	Data for long-term statistics hourly temperature graphs
	#
	# 	A script that generates data for long-term statistics hourly graph
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
	<div class="resizer resizer2">
		<div class="inner-resizer">
			<div id="hourGraph" style="height:400px;margin-left:auto;margin-right:auto;width:100%" class="varGraphs2"></div>
		</div>
	</div>
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
						<input type="button" class="button" value="<?php echo lang('avgAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>HourAvg">
					</th>
				<?php
					}
				?>
			</tr>
			<?php
				if($var!="R"){
			?>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('minimumAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>HourMin">
							</th>
						<?php
							}
						?>
					</tr>
			<?php
				}
			?>
			<tr>
				<?php
					for($i=1;$i<13;$i++){
				?>
					<th style="text-align:center">
						<input type="button" class="button" value="<?php echo lang('maximumAbbr','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>HourMax">
					</th>
				<?php
					}
				?>
			</tr>
			<?php
				if($var!="R"){
			?>
					<tr>
						<?php
							for($i=1;$i<13;$i++){
						?>
							<th style="text-align:center">
								<input type="button" class="button" value="<?php echo lang('range','c')?>" style="font-size:0.6em;padding:5px" id="setMonth<?php echo $i?>HourRange">
							</th>
						<?php
							}
						?>
					</tr>
			<?php
				}
			?>
		</thead>
	</table>
	<script>
		$('.resizer2').resizable({
			resize: function() {
				selectedDiv = $(this).find(".varGraphs2");
				chart = selectedDiv.highcharts();
				chart.setSize(
					this.offsetWidth - 50, 
					this.offsetHeight - 50,
					false
				);
			},
		});
		graphHour(1,"avg");
		function graphHour(month,q){
			optionsHourGraph = {
				chart : {
					renderTo : 'hourGraph',
					type : 'spline',
					zoomType: 'xy',
				},
				title: {
					text: '',
				},
				xAxis: {
					categories: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],
					title: {
						text: '<?php echo lang("hour",'c')?>'
					},
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
				url : "climateGraphHourAjax.php?month="+month+"&q="+q+"&var=<?php echo $var?>",
				dataType : 'json',
				success : function (json) {
					optionsHourGraph.series = json['data'];
					optionsHourGraph.title.text = json['title'];
					chart = new Highcharts.Chart(optionsHourGraph);
				},
			});
		}
		function graphHourRange(month){
			optionsHourGraph = {
				chart : {
					renderTo : 'hourGraph',
					type : 'areasplinerange',
					zoomType: 'xy',
				},
				credits: {
					text: '<?php echo $highChartsCreditsText?>',
					href: '<?php echo $pageURL.$path?>'
				},
				title: {
					text: '',
				},
				xAxis: {
					categories: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23],
					title: {
						text: '<?php echo lang("hour",'c')?>'
					},
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
				url : "climateGraphHourAjax.php?q=range&month="+month+"&var=<?php echo $var?>",
				dataType : 'json',
				success : function (json) {
					optionsHourGraph.series = json['data'];
					optionsHourGraph.title.text = json['title'];
					chart = new Highcharts.Chart(optionsHourGraph);
				},
			});
		}
		<?php 
			for($i=1;$i<13;$i++){
		?>
			$('#setMonth<?php echo $i?>HourAvg').click(function() {
				graphHour(<?php echo $i?>,'avg');
			});
			$('#setMonth<?php echo $i?>HourMin').click(function() {
				graphHour(<?php echo $i?>,'min');
			});
			$('#setMonth<?php echo $i?>HourMax').click(function() {
				graphHour(<?php echo $i?>,'max');
			});
			$('#setMonth<?php echo $i?>HourRange').click(function() {
				graphHourRange(<?php echo $i?>);
			});
		<?php
			}
		?>
	</script>