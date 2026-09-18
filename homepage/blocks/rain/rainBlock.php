<?php

	# 		Rain Summary
	# 		Namespace:		rain
	#		Meteotemplate Block

	# 		v1.1 - Jan 29, 2016
	#			- added responsiveness
	# 		v1.2 - Mar 16, 2016
	#			- fixed decimals
	# 		v2.0 - May 30, 2016
	#			- added last rain time/date
	#		v3.0 - Aug 29, 2016
	#			- added gauges
	#			- CSS tweaks
	#		v3.1 - Dec 11, 2016
	#			- fixed IE compatibility issue
	#		v3.2 - Dec 12, 2016
	#			- bug fixes
	# 		v4.0 - Feb 21, 2017
	# 			- added rain table
	# 		v5.0 - Mar 14, 2017
	# 			- changed to percentages and averages
	# 		v6.0 - Mar 15, 2017
	# 			- percentages optional
	# 			- minor tweaks
	# 		v6.1 - May 5, 2017
	# 			- bug fixes
	# 		v6.2 - Jul 3, 2017
	# 			- minor tweaks
	# 			- optimization (size reduction by 50%)
	# 		v7.0 - Oct 19, 2017
	# 			- added rain this season
	# 		v7.1 - Oct 25, 2017
	# 			- CSS tweaks

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
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}
	
	// error_reporting(E_ALL);

	if($displayRainUnits=="mm"){
		$todayMax = $todayMaxMM;
		$monthMax = $monthMaxMM;
		$yearMax = $yearMaxMM;
	}
	else{
		$todayMax = $todayMaxIN;
		$monthMax = $monthMaxIN;
		$yearMax = $yearMaxIN;
	}

	$result = mysqli_query($con, "
			SELECT  R, RR
			FROM alldata
			ORDER BY DateTime DESC
			LIMIT 1
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$rainToday = convertR($row['R']);
		$rainRateNow = convertR($row['RR']);
	}

	$monthlyRains = array();
	$annualRains = array();
	// load all rains, group them
	$result = mysqli_query($con, "
			SELECT  max(R), DateTime
			FROM  alldata
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$month = date("m",strtotime($row['DateTime']));
		$year = date("Y",strtotime($row['DateTime']));
		$day = date("d",strtotime($row['DateTime']));
		$dayOfYear = date("z",strtotime($row['DateTime']));
		
		// save current day
		if(date('m')==$month && date("d")==$day){
			$thisDayRains[] = convertR($row['max(R)']);
		}
		// save current month
		if(date('m')==$month && date("Y")==$year){
			$monthlyRains[] = convertR($row['max(R)']);
		}
		// save current year
		if(date("Y")==$year){
			$annualRains[] = convertR($row['max(R)']);
		}
		// save year averages
		$averageRains[$dayOfYear][] = convertR($row['max(R)']);
	}
	
	if(empty($monthlyRains)===false){
		$monthlyAvgR = array_sum($monthlyRains)/count($monthlyRains);
		$monthlyMaxR = max($monthlyRains);
	}
	$rainMonth = array_sum($monthlyRains);
	if(empty($annualRains)===false){
		$annualAvgR = array_sum($annualRains)/count($annualRains);
		$annualMaxR = max($annualRains);
	}
	$rainYear = array_sum($annualRains);

	// now analyze averages
	// check we have at least 364 days logged
	if(count($averageRains)>=364){
		ksort($averageRains);
		// average them out 
		foreach($averageRains as $dayVal=>$values){
			$averages[$dayVal] = array_sum($values)/count($values);
		}
		// annual average
		$annualAvg = array_sum($averages);
		// this month average
		$thisMonthAvg = 0;
		$firstDayOfMonth = date("z",strtotime(date("Y")."-".date("m")."-1"));
		$daysInMonth = date('t',strtotime(date("m")));
		$lastDayOfMonth = $firstDayOfMonth + $daysInMonth;
		for($i=$firstDayOfMonth;$i<=$lastDayOfMonth;$i++){
			$thisMonthAvg += $averages[$i]; 
		}
		// this day average
		$thisDayAvg = array_sum($thisDayRains)/count($thisDayRains);
		// find averages up to today
		for($i=$firstDayOfMonth;$i<=date("z");$i++){
			$monthUpToNow += $averages[$i]; 
		}
		for($i=1;$i<=date("z");$i++){
			$yearUpToNow += $averages[$i]; 
		}
		if($rainMonth==$monthUpToNow){ // to prevent division by zero
			$monthlyPercentage = 100;
		}
		else{
			$monthlyPercentage = number_format(($rainMonth / $monthUpToNow)*100,1,".","");
		}
		if($rainYear==$yearUpToNow){ // to prevent division by zero
			$annualPercentage = 100;
		}
		else{
			$annualPercentage = number_format(($rainYear / $yearUpToNow)*100,1,".","");
		}
		if($thisDayAvg==$rainToday){ // to prevent division by zero
			$dayPercentage = 100;
		}
		else{
			$dayPercentage = number_format(($rainToday / $thisDayAvg)*100,1,".","");
		}		
	}

	$result = mysqli_query($con, "
			SELECT  max(R)
			FROM alldata
			WHERE DateTime >= now() - interval 1 week
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime DESC
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$dailyRains[] = convertR($row['max(R)']);
	}
	$last7Day = array_sum($dailyRains);

	// last rain
	// first find the day
	$result = mysqli_query($con, "
			SELECT  DateTime
			FROM  alldata
			WHERE R>0
			ORDER BY DateTime DESC
			LIMIT 1
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$lastRainDay = strtotime($row['DateTime']);
	}

	// now iterate day
	$d = date('d',$lastRainDay);
	$m = date('m',$lastRainDay);
	$y = date('Y',$lastRainDay);

	$lastRain = 0;

	$result = mysqli_query($con, "
			SELECT R, DateTime
			FROM  alldata
			WHERE Day(DateTime)=$d AND MONTH(DateTime)=$m AND YEAR(DateTime)=$y
			ORDER BY DateTime
			"
	);
	while ($row = mysqli_fetch_array($result)) {
		$currentRain = $row['R'];
		if($currentRain>$lastRain){
			$lastRainTimeTS = strtotime($row['DateTime']);
		}
		$lastRain = $currentRain;
	}

	$difference = dateDiff($lastRainTimeTS, time());
	function dateDiff($time1, $time2, $precision = 6) {
		if (!is_int($time1)) {
		  $time1 = strtotime($time1);
		}
		if (!is_int($time2)) {
		  $time2 = strtotime($time2);
		}
		if ($time1 > $time2) {
		  $ttime = $time1;
		  $time1 = $time2;
		  $time2 = $ttime;
		}
		$intervals = array('year','month','day','hour','minute','second');
		$diffs = array();
		foreach ($intervals as $interval) {
		  $ttime = strtotime('+1 ' . $interval, $time1);
		  $add = 1;
		  $looped = 0;
		  while ($time2 >= $ttime) {
			$add++;
			$ttime = strtotime("+" . $add . " " . $interval, $time1);
			$looped++;
		  }
		  $time1 = strtotime("+" . $looped . " " . $interval, $time1);
		  $diffs[$interval] = $looped;
		}
		$count = 0;
		$times = array();
		foreach ($diffs as $interval => $value) {
		  if ($count >= $precision) {
			break;
		  }
		  if ($value > 0) {
			$times[] = $value . " " . $interval;
			$count++;
		  }
		}
		$result = implode(" ", $times);
		$result = str_replace("year","y",$result);
		$result = str_replace("month","m",$result);
		$result = str_replace("day","d",$result);
		$result = str_replace("hour","h",$result);
		$result = str_replace("minute","min",$result);
		$result = str_replace("second","s",$result);
		return $result;
	}

	if($seasonsREnabled){
		$cumRain = 0;
		if(date("n")<$seasonRStart){
			$getYear = date("Y") - 1;
		}
		else{
			$getYear = date("Y");
		}
		$startDate = $getYear."-".$seasonRStart."-1";
		$result = mysqli_query($con, "
			SELECT  max(R)
			FROM alldata
			WHERE DateTime >= '$startDate'
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while ($row = mysqli_fetch_array($result)) {
			$cumRain += round(convertR($row['max(R)']),2);
		}
	}
?>
	<script src="<?php echo $pageURL.$path?>pages/astronomy/d3.v3.min.js" language="JavaScript"></script>
	<script src="homepage/blocks/rain/liquidGauge.js"></script>
	<style>

	</style>
	<span class="mticon-rain" style="font-size:2.7em"></span>
	<table style="width:98%;margin:0 auto;table-layout:fixed;font-variant:small-caps" id="rainBlockTable">
		<thead>
			<tr>
				<td id="rainGaugeTD1">
				</td>
				<td id="rainGaugeTD2">
				</td>
				<td id="rainGaugeTD3">
				</td>
			</tr>
		</thead>
	</table>
	<br>
	<div id="rainBlockDetails" class="details" style="width:100%">
		<div style="width:94%;margin: 0 auto;text-align:left">
			<strong><?php echo lang('last rain','c')?></strong><br><?php echo date($dateTimeFormat,$lastRainTimeTS)." (-".$difference?>)
		</div>
		<table style="width:94%;margin: 0 auto" class="table">
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('today','c')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($rainToday,$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?><br><span style="font-size:0.8em"><?php echo $dayPercentage."% ".lang('avgAbbr','l')." (".number_format($thisDayAvg,$decimalsR,".","").unitFormatter($displayRainUnits).")"?></span>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('rain rate','c')." ".lang('now','l')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($rainRateNow,$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?>/<?php echo lang('hAbbr','l')?>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('yesterday','c')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($dailyRains[1],$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('last 7 days','c')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($last7Day,$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('this month','c')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($rainMonth,$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?><br><span style="font-size:0.8em"><?php echo $monthlyPercentage."% ".lang('avgAbbr','l')." (1 ".lang("month".date("n")."short",'c')." - ".date("j")." ".lang("month".date("n")."short",'c').", ".number_format($monthUpToNow,$decimalsR+1,".","").unitFormatter($displayRainUnits).")"?></span>
				</td>
			</tr>
			<tr>
				<td style="text-align:left;font-weight:bold">
					<?php echo lang('this year','c')?>
				</td>
				<td style="text-align:right">
					<?php echo number_format($rainYear,$decimalsR,".","")?> <?php echo unitFormatter($displayRainUnits)?><br><span style="font-size:0.8em"><?php echo $annualPercentage."% ".lang('avgAbbr','l')." (1 ".lang("month1short",'c')." - ".date("j")." ".lang("month".date("n")."short",'c').", ".number_format($yearUpToNow,$decimalsR+1,".","").unitFormatter($displayRainUnits).")"?></span>
				</td>
			</tr>
			<?php 
				if($seasonsREnabled){
			?>
					<tr>
						<td style="text-align:left;font-weight:bold">
							<?php echo lang('this season','c')?><br><span style="font-size:0.8em">(<?php echo lang("month".round($seasonRStart),"c")?> <?php echo $getYear?> - <?php echo lang("now",'l')?>)
						</td>
						<td style="text-align:right">
							<?php echo $cumRain." ".unitFormatter($displayRainUnits)?></span>
						</td>
					</tr>
			<?php 
				}
			?>
		</table>
	</div>
	<span class="more" onclick="txt = $('#rainBlockDetails').is(':visible') ? '<?php echo lang('more','l')?>' : '<?php echo lang('hide','l')?>';$('#rainBlockDetails').slideToggle(800);$(this).text(txt)">
		<?php echo lang('more','l')?>
	</span>
	<script>
		<?php
			if($theme=="dark"){
				$gaugeFontColor = "#fff";
			}
			else{
				$gaugeFontColor = "#000";
			}
		?>
		var config1 = liquidFillGaugeDefaultSettings();
		var config2 = liquidFillGaugeDefaultSettings();
		var config3 = liquidFillGaugeDefaultSettings();

		// today's gauge
		config1.minValue =  0;
		<?php 
			if(count($averageRains)<364 || !$usePercentages){
		?>
        		config1.maxValue =  <?php echo $todayMax?>;
		<?php
			}
			else{
				if($dayPercentage<100){
					echo "config1.maxValue = 100;";
				}
				else{
					echo "config1.maxValue = ".$dayPercentage.";";
				}
			}
		?>
		config1.circleColor = "#178BCA";
		config1.textColor = "<?php echo $gaugeFontColor?>";
		config1.waveTextColor = "#99ccff";
		config1.waveColor = "#0040ff";
		config1.circleThickness = 0.2;
		config1.textVertPosition = 0.5;
		config1.waveAnimateTime = 2000;
		config1.textSize = 0.7;
		config1.waveRiseTime = 2500;
		<?php 
			if(count($averageRains)>=364 && $usePercentages){
		?>
				config1.displayPercent = "%";
		<?php
			}
			else{
		?>
				config1.displayPercent = " <?php echo $displayRainUnits?>";
		<?php
			}
		?>
		// month gauge
		config2.minValue =  0;
        <?php 
			if(count($averageRains)<364 || !$usePercentages){
		?>
        		config2.maxValue =  <?php echo $monthMax?>;
		<?php
			}
			else{
				if($monthlyPercentage<100){
					echo "config2.maxValue = 100;";
				}
				else{
					echo "config2.maxValue = ".$monthlyPercentage.";";
				}
			}
		?>
		config2.circleColor = "#178BCA";
		config2.textColor = "<?php echo $gaugeFontColor?>";
		config2.waveTextColor = "#99ccff";
		config2.waveColor = "#0040ff";
		config2.circleThickness = 0.2;
		config2.textVertPosition = 0.5;
		config2.waveAnimateTime = 2000;
		config2.textSize = 0.7;
		config2.waveRiseTime = 2500;
		<?php 
			if(count($averageRains)>=364 && $usePercentages){
		?>
				config2.displayPercent = "%";
		<?php
			}
			else{
		?>
				config2.displayPercent = " <?php echo $displayRainUnits?>";
		<?php
			}
		?>

		// annual gauge
		config3.minValue =  0;
		<?php 
			if(count($averageRains)<364 || !$usePercentages){
		?>
        		config3.maxValue =  <?php echo $yearMax?>;
		<?php
			}
			else{
				if($annualPercentage<100){
					echo "config3.maxValue = 100;";
				}
				else{
					echo "config3.maxValue = ".$annualPercentage.";";
				}
			}
		?>
		config3.circleColor = "#178BCA";
		config3.textColor = "<?php echo $gaugeFontColor?>";
		config3.waveTextColor = "#99ccff";
		config3.waveColor = "#0040ff";
		config3.circleThickness = 0.2;
		config3.textVertPosition = 0.5;
		config3.waveAnimateTime = 2000;
		config3.textSize = 0.6;
		<?php 
			if(count($averageRains)>=364){
		?>
				config3.textSize = 0.7;
		<?php
			}
		?>
		config3.waveRiseTime = 3000;
		<?php 
			if(count($averageRains)>=364 && $usePercentages){
		?>
				config3.displayPercent = "%";
		<?php
			}
			else{
		?>
				config3.displayPercent = " <?php echo $displayRainUnits?>";
				<?php 
					if($rainYear>999.99){
				?>
						config3.textSize = 0.55;
				<?php
					}
				?>
		<?php
			}
		?>
		rainBlockWidth = $("#rainBlockTable").outerWidth() / 3;
		<?php 
			if(count($averageRains)>=364){
		?>
        		svg1Rain = "<svg id='fillgauge1' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('today','c')?><br><?php echo number_format($rainToday,$decimalsR,".","").$displayRainUnits?><br><?php echo number_format($dayPercentage,$decimalsR,".","")?>% <?php echo lang('avgAbbr','l')?>";
				svg2Rain = "<svg id='fillgauge2' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('this month','c')?><br><?php echo number_format($rainMonth,$decimalsR,".","").$displayRainUnits?><br><?php echo number_format($monthlyPercentage,$decimalsR,".","")?>% <?php echo lang('avgAbbr','l')?>";
				svg3Rain = "<svg id='fillgauge3' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('this year','c')?><br><?php echo number_format($rainYear,$decimalsR,".","").$displayRainUnits?><br><?php echo number_format($annualPercentage,$decimalsR,".","")?>% <?php echo lang('avgAbbr','l')?>";
		<?php
			}
			else{
		?>
				svg1Rain = "<svg id='fillgauge1' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('today','c')?>";
				svg2Rain = "<svg id='fillgauge2' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('this month','c')?>";
				svg3Rain = "<svg id='fillgauge3' height='80' width='"+rainBlockWidth+"'></svg><?php echo lang('this year','c')?>";
		<?php
			}
		?>
		
		$("#rainGaugeTD1").html(svg1Rain);
		$("#rainGaugeTD2").html(svg2Rain);
		$("#rainGaugeTD3").html(svg3Rain);
		<?php 
			if(count($averageRains)>=364 && $usePercentages){
		?>
				var gauge1 = loadLiquidFillGauge("fillgauge1", <?php echo number_format($dayPercentage,$decimalsR,".","")?>, config1);
				var gauge2 = loadLiquidFillGauge("fillgauge2", <?php echo number_format($monthlyPercentage,$decimalsR,".","")?>, config2);
				var gauge3 = loadLiquidFillGauge("fillgauge3", <?php echo number_format($annualPercentage,$decimalsR,".","")?>, config3);
		<?php 
			}
			else{
		?>
				var gauge1 = loadLiquidFillGauge("fillgauge1", <?php echo number_format($rainToday,$decimalsR,".","")?>, config1);
				var gauge2 = loadLiquidFillGauge("fillgauge2", <?php echo number_format($rainMonth,$decimalsR,".","")?>, config2);
				var gauge3 = loadLiquidFillGauge("fillgauge3", <?php echo number_format($rainYear,$decimalsR,".","")?>, config3);
		<?php
			}
		?>
		$("#fillgauge1").width(rainBlockWidth);
	</script>
