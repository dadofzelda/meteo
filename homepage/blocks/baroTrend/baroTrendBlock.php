<?php

	# 		Barometric Pressure Trend
	# 		Namespace:		baroTrend
	#		Meteotemplate Block
	
	# 		v1.1 - Jan 29, 2016
	#			- added responsiveness
	#			- bug fixes
	# 		v2.0 - May 16, 2017
	# 			- added pressure tendency
	# 			- added forecasted weather
	# 			- conversion to SVG
	# 			- optimization (size reductin by 92%)
		
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
	
	$P3Days = "";
	$PWeek = "";
	$P48h = "";
	$P24h = "";
	$P12h = "";
	$P6h = "";
	$P3h = "";
	$P1h = "";
	
	$result = mysqli_query($con, "
			SELECT  DateTime, P
			FROM  alldata
			WHERE DateTime >= now() - interval 1 week
			ORDER BY DateTime
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		if(strtotime($row['DateTime']) >= strtotime('-3 Days')){
			if($P3Days==""){
				$P3Days = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-1 week')){
			if($PWeek==""){
				$PWeek = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-48 hours')){
			if($P48h==""){
				$P48h = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-24 hours')){
			if($P24h==""){
				$P24h = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-6 hours')){
			if($P6h==""){
				$P6h = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-3 hours')){
			if($P3h==""){
				$P3h = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-12 hours')){
			if($P12h==""){
				$P12h = convertP($row['P']);
			}
		}
		if(strtotime($row['DateTime']) >= strtotime('-60 minutes')){
			if($P1h==""){
				$P1h = convertP($row['P']);
			}
		}
		$PCurrent = convertP($row['P']);
	}
	if($displayPressUnits=="inhg"){
		$decimals = 2;
	}
	else{
		$decimals = 1;
	}
	
	$P1hTrend = number_format(($PCurrent - $P1h),$decimals,".","");
	if($P1hTrend>0){
		$P1hTrend = "+".$P1hTrend;
	}
	$P3hTrend = number_format(($PCurrent - $P3h),$decimals,".","");
	if($P3hTrend>0){
		$P3hTrend = "+".$P3hTrend;
	}
	$P6hTrend = number_format(($PCurrent - $P6h),$decimals,".","");
	if($P6hTrend>0){
		$P6hTrend = "+".$P6hTrend;
	}
	$P12hTrend = number_format(($PCurrent - $P12h),$decimals,".","");
	if($P12hTrend>0){
		$P12hTrend = "+".$P12hTrend;
	}
	$P24hTrend = number_format(($PCurrent - $P24h),$decimals,".","");
	if($P24hTrend>0){
		$P24hTrend = "+".$P24hTrend;
	}
	$P48hTrend = number_format(($PCurrent - $P48h),$decimals,".","");
	if($P48hTrend>0){
		$P48hTrend = "+".$P48hTrend;
	}
	$PWeekTrend = number_format(($PCurrent - $PWeek),$decimals,".","");
	if($PWeekTrend>0){
		$PWeekTrend = "+".$PWeekTrend;
	}
	$P3DaysTrend = number_format(($PCurrent - $P3Days),$decimals,".","");
	if($P3DaysTrend>0){
		$P3DaysTrend = "+".$P3DaysTrend;
	}

	// get tendencies
	$standardized3h = convertor($P3hTrend,$displayPressUnits,"hpa");
	$standardized1h = convertor($P1hTrend,$displayPressUnits,"hpa");

	if($standardized3h>=6){
		$tendency['3h'] = lang('rising very rapidly','l');
		$tendency['3hIcon'][] = "mticon-sun";
		$tendency['3hIcon'][] = "mticon-wind";
	}
	if($standardized3h>=3.6 && $standardized3h<6){
		$tendency['3h'] = lang('rising quickly','l');
		$tendency['3hIcon'][] = "mticon-sun";
		$tendency['3hIcon'][] = "mticon-wind";
	}
	if($standardized3h>=1.6 && $standardized3h<3.6){
		$tendency['3h'] = lang('rising','l');
		$tendency['3hIcon'][] = "mticon-sun";
	}
	if($standardized3h>=0.3 && $standardized3h<1.6){
		$tendency['3h'] = lang('rising slowly','l');
	}
	if($standardized3h>=-0.3 && $standardized3h<0.3){
		$tendency['3h'] = lang('steady','l');
	}
	if($standardized3h>=-1.6 && $standardized3h<-0.3){
		$tendency['3h'] = lang('falling slowly','l');
	}
	if($standardized3h>=-3.6 && $standardized3h<-1.6){
		$tendency['3h'] = lang('falling','l');
		$tendency['3hIcon'][] = "mticon-rain";
	}
	if($standardized3h>=-6 && $standardized3h<-3.6){
		$tendency['3h'] = lang('falling quickly','l');
		$tendency['3hIcon'][] = "mticon-rain";
		$tendency['3hIcon'][] = "mticon-wind";
	}
	if($standardized3h<=-6){
		$tendency['3h'] = lang('falling very rapidly','l');
		$tendency['3hIcon'][] = "mticon-rain";
		$tendency['3hIcon'][] = "mticon-wind";
	}
?>
	<style>
		.baroTrendIcon{
			font-size: 2.5em;
		}
		.baroTrendIconSmall{
			font-size: 1.5em;
		}
	</style>
	<div style="margin:0 auto;text-align:center">
		<span class='mticon-pressure tooltip' style="font-size:2.5em" title="<?php echo lang('pressure trend','c')?>"></span><br><?php echo unitFormatter($displayPressUnits)?>
	</div>
	<table style="width:98%;margin:0 auto">
		<tr>
			<td style="width:25%">
				<?php
					if($P1hTrend>0){ 
						$trendIcon = "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P1hTrend<0){ 
						$trendIcon =  "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P1hTrend==0){ 
						$trendIcon =  "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<span class="mticon-1h baroTrendIcon tooltip" title="1 <?php echo lang('hour','l')?>"></span><br><?php echo $trendIcon?><br><?php echo $P1hTrend?>
			</td>
			<td style="width:25%">
				<?php
					if($P3hTrend>0){ 
						$trendIcon = "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P3hTrend<0){ 
						$trendIcon =  "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P3hTrend==0){ 
						$trendIcon =  "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<span class="mticon-3h baroTrendIcon tooltip" title="3 <?php echo lang('hours','l')?>"></span><br><?php echo $trendIcon?><br><?php echo $P3hTrend?>
			</td>
			<td style="width:25%">
				<span class="mticon-6h baroTrendIcon tooltip" title="6 <?php echo lang('hours','l')?>"></span><br>
				<?php
					if($P6hTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P6hTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P6hTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $P6hTrend?>
			</td>
			<td style="width:25%">
				<span class="mticon-12h baroTrendIcon tooltip" title="12 <?php echo lang('hours','l')?>"></span><br>
				<?php
					if($P12hTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P12hTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P12hTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $P12hTrend?>
			</td>
		</tr>
		<tr style="padding-top:8px">
			<td style="width:25%">
				<span class="mticon-24h baroTrendIcon tooltip" title="24 <?php echo lang('hours','l')?>"></span><br>
				<?php
					if($P24hTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P24hTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P24hTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $P24hTrend?>
			</td>
			<td style="width:25%">
				<span class="mticon-48h baroTrendIcon tooltip" title="48 <?php echo lang('hours','l')?>"></span><br>
				<?php
					if($P48hTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P48hTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P48hTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $P48hTrend?>
			</td>
			<td style="width:25%">
				<span class="mticon-3days baroTrendIcon tooltip" title="3 <?php echo lang('days','l')?>"></span><br>
				<?php
					if($P3DaysTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($P3DaysTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($P3DaysTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $P3DaysTrend?>
			</td>
			<td style="width:25%">
				<span class="mticon-week baroTrendIcon tooltip" title="<?php echo lang('week','c')?>"></span><br>
				<?php
					if($PWeekTrend>0){ 
						echo "<span class='mticon-trendup baroTrendIconSmall'></span>";
					}
					if($PWeekTrend<0){ 
						echo "<span class='mticon-trenddown baroTrendIconSmall'></span>";
					}
					if($PWeekTrend==0){ 
						echo "<span class='mticon-trendneutral baroTrendIconSmall'></span>";
					}
				?>
				<br>
				<?php echo $PWeekTrend?>
			</td>
		</tr>
	</table>
	<div style="width:98%;margin:0 auto;padding-top:5px;font-size:1.3em;font-weight:bold;font-variant:small-caps">
		<?php echo $tendency['3h']?>
	</div>
	<?php 
		if(isset($tendency['3hIcon'])){
	?>
			<div style="width:98%;margin:0 auto;padding-top:2px;font-size:2.2em">
				<?php 
					foreach($tendency['3hIcon'] as $icon){
				?>
						<span class="<?php echo $icon?>"></span>
				<?php
					}
				?>
			</div>
	<?php
		}
	?>