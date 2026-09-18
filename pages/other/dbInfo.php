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
	#	Database info
	#
	# 	A script showing information about the database, potential spikes and
	#	gaps in data.
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
	
	/*rates - defined in config.php
		$maxRateT = 7;
		$maxRateH = 25;
		$maxRateP = 5;
		$maxRateD = 7;
		$maxRateA = 7;
		$maxRateR = 7;
	*/
	
	$countYearly = array();
	$total = 0;
	
	$result = mysqli_query($con,"
		SELECT  *
		FROM  alldata
		ORDER BY DateTime
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$oldest = strtotime($row['DateTime']);
		$oldestRecord = date($dateTimeFormat,strtotime($row['DateTime']));
		$firstT = convertT($row['T']);
		$firstTmax = $row['Tmax'];
		$firstTmin = $row['Tmin'];
		$firstH = $row['H'];
		$firstP = $row['P'];
		$firstD = $row['D'];
		$firstA = $row['A'];
		$firstW = $row['W'];
		$firstG = $row['G'];
		$firstR = $row['R'];
		$firstRR = $row['RR'];
	}
	
	$result = mysqli_query($con,"
		SELECT  DateTime
		FROM  alldata
		ORDER BY DateTime DESC
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$newest = strtotime($row['DateTime']);
		$newestRecord = date($dateTimeFormat,strtotime($row['DateTime']));
	}
	
	$result = mysqli_query($con,"
		SELECT year(DateTime), count(DateTime)
		FROM  alldata
		GROUP BY year(DateTime)
		"
	);
	while($row = mysqli_fetch_array($result)){
		array_push($countYearly,array($row['year(DateTime)'],$row['count(DateTime)']));
		$total = $total + $row['count(DateTime)'];
	}
	
	$result = mysqli_query($con,"
		SELECT max(Tmax),min(Tmin),max(H),min(H),max(P),min(P),max(S),min(S),max(A),min(A),max(D),min(D),max(R),max(W),min(W),max(G),min(G),max(RR)
		FROM  alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		$alltimeTmax = convertT($row['max(Tmax)']);
		$alltimeTmin = convertT($row['min(Tmin)']);
		$alltimeHmin = $row['min(H)'];
		$alltimeHmax = $row['max(H)'];
		$alltimePmax = convertP($row['max(P)']);
		$alltimePmin = convertP($row['min(P)']);
		$alltimeSmax = $row['max(S)'];
		$alltimeSmin = $row['min(S)'];
		$alltimeAmax = convertT($row['max(A)']);
		$alltimeAmin = convertT($row['min(A)']);
		$alltimeDmax = convertT($row['max(D)']);
		$alltimeDmin = convertT($row['min(D)']);
		$alltimeRmax = convertR($row['max(R)']);
		$alltimeRRmax = convertR($row['max(RR)']);
		$alltimeWmax = convertW($row['max(W)']);
		$alltimeGmax = convertW($row['max(G)']);
	}
	
	$averageInterval = round(($newest-$oldest)/$total/60,2);
	
	$previousDate = $oldest;
	$previousT = $firstT;
	$previousTmax = $firstTmax;
	$previousTmin = $firstTmin;
	$previousH = $firstH;
	$previousP = $firstP;
	$previousD = $firstD;
	$previousA = $firstA;
	$previousR = $firstR;
	
	$gaps = array();
	$spikeT = array();
	$spikeTmax = array();
	$spikeTmin = array();
	$spikeH = array();
	$spikeP = array();
	$spikeD = array();
	$spikeA = array();
	$spikeR = array();
	
	$result = mysqli_query($con,"
		SELECT *
		FROM  alldata
		"
	);
	while($row = mysqli_fetch_array($result)){
		$currentDate = strtotime($row['DateTime']);
		$differenceDate = $currentDate - $previousDate;
		if($differenceDate >= $averageInterval*60*10){
			array_push($gaps,array($previousDate,$currentDate));
		}
		$currentT = convertT($row['T']);
		$currentTmax = convertT($row['Tmax']);
		$currentTmin = convertT($row['Tmin']);
		$currentH = $row['H'];
		$currentP = convertP($row['P']);
		$currentD = convertT($row['D']);
		$currentA = convertT($row['A']);
		$currentR = convertR($row['R']);
		$differenceT = abs($currentT-$previousT);
		$differenceTmax = abs($currentTmax-$previousTmax);
		$differenceTmin = abs($currentTmin-$previousTmin);
		$differenceH = abs($currentH-$previousH);
		$differenceP = abs($currentP-$previousP);
		$differenceD = abs($currentD-$previousD);
		$differenceA = abs($currentA-$previousA);
		$differenceR = $currentR-$previousR; // in case of rain we only want differences upwards, at midnight it falls down
		$rateT = ($differenceT / ($differenceDate+0.0001)) * 60 * 10; // add minimal value to prevent division by 0
		$rateTmax = ($differenceTmax / ($differenceDate+0.0001)) * 60 * 10;
		$rateTmin = ($differenceTmin / ($differenceDate+0.0001)) * 60 * 10;
		$rateH = ($differenceH / ($differenceDate+0.0001)) * 60 * 10;
		$rateP = ($differenceP / ($differenceDate+0.0001)) * 60 * 10;
		$rateD = ($differenceD / ($differenceDate+0.0001)) * 60 * 10;
		$rateA = ($differenceA / ($differenceDate+0.0001)) * 60 * 10;
		$rateR = ($differenceR / ($differenceDate+0.0001)) * 60 * 10;
		if($rateT>$maxRateT){
			array_push($spikeT,array($previousDate,$previousT,$currentDate,$currentT,$rateT));
		}
		if($rateT>$maxRateT){
			array_push($spikeTmax,array($previousDate,$previousTmax,$currentDate,$currentTmax,$rateTmax));
		}
		if($rateTmax>$maxRateT){
			array_push($spikeTmin,array($previousDate,$previousTmin,$currentDate,$currentTmin,$rateTmin));
		}
		if($rateH>$maxRateH){
			array_push($spikeH,array($previousDate,$previousH,$currentDate,$currentH,$rateH));
		}
		if($rateP>$maxRateP){
			array_push($spikeP,array($previousDate,$previousP,$currentDate,$currentP,$rateP));
		}
		if($rateD>$maxRateD){
			array_push($spikeD,array($previousDate,$previousD,$currentDate,$currentD,$rateD));
		}
		if($rateA>$maxRateA){
			array_push($spikeA,array($previousDate,$previousA,$currentDate,$currentA,$rateA));
		}
		if($rateR>$maxRateR){
			array_push($spikeR,array($previousDate,$previousR,$currentDate,$currentR,$rateR));
		}
		$previousDate = $currentDate;
		$previousT = $currentT;
		$previousTmax = $currentTmax;
		$previousTmin = $currentTmin;
		$previousH = $currentH;
		$previousP = $currentP;
		$previousD = $currentD;
		$previousA = $currentA;
		$previousR = $currentR;
	}
	
	// get first date in db and number of years
	$result = mysqli_query($con,"
		SELECT  DateTime, Year(DateTime)
		FROM  alldata
		ORDER BY DateTime 
		LIMIT 1
		"
	);
	while($row = mysqli_fetch_array($result)){
		$firstDate = strtotime($row['DateTime']);
	}
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			.iconDB{
				width: 50px;
				padding: 40px;
			}
			.iconSmallDB{
				width: 30px;
				padding: 20px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main">
			<div style="width:80%;margin-left:auto;margin-right:auto">
				<h1>Database Info</h1>
				Data available since: <?php echo date($dateFormat." ".$timeFormat, $firstDate)?><br>
				Number of entries: <?php echo $total?><br>
				Average interval: <?php echo round($averageInterval,2)?> minutes<br><br>
				<table class="table">
					<thead>
						<tr>
							<th>
								<?php echo lang('year','c');?>
							</th>
							<th>
								Number of entries
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
						for($i=0;$i<count($countYearly);$i++){
					?>
						<tr>
							<th>
								<?php echo $countYearly[$i][0]?>
							</th>
							<td>
								<?php echo $countYearly[$i][1]?>
							</td>
						</tr>
					<?php
						}
					?>
					</tbody>
				</table>
				<br>
				<br>
				<h2><?php echo lang('extremes','c')?></h2>
				<table class="table">
					<thead>
						<th></th>
						<th><?php echo lang('minimumAbbr','c')?></th>
						<th><?php echo lang('maximumAbbr','c')?></th>
					</thead>
					<tbody>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/temp.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								<?php echo $alltimeTmin?>
							</td>
							<td>
								<?php echo $alltimeTmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="iconSmallDB" alt=''>
							</td>
							<td>
							<?php echo $alltimeHmin?>
							</td>
							<td>
							<?php echo $alltimeHmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="iconSmallDB" alt=''>
							</td>
							<td>
							<?php echo $alltimePmin?>
							</td>
							<td>
							<?php echo $alltimePmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								<?php echo $alltimeDmin?>
							</td>
							<td>
								<?php echo $alltimeDmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								<?php echo $alltimeAmin?>
							</td>
							<td>
								<?php echo $alltimeAmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/wind.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								-
							</td>
							<td>
								<?php echo $alltimeWmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/gust.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								-
							</td>
							<td>
								<?php echo $alltimeGmax?>
							</td>
						</tr>
						<tr>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/rain.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								-
							</td>
							<td>
								<?php echo $alltimeRmax?>
							</td>
						</tr>
						<?php 
							if($solarSensor){					
						?>
							<td>
								<img src="<?php echo $pageURL.$path?>icons/sun.png" class="iconSmallDB" alt=''>
							</td>
							<td>
								-
							</td>
							<td>
								<?php echo $alltimeSmax?>
							</td>
						<?php
							}
						?>
					</tbody>
				</table>
				<br><br>
				<h2><?php echo lang('potential spikes','c')?></h2>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('to','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeT);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeT[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeT[$i][1]." °".$displayTempUnits?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeT[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeT[$i][3]." °".$displayTempUnits?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeT[$i][4],2)." °".$displayTempUnits?>
							</td>
						</tr>
					<?php
						}
					?>
				</table>
				<br><br>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('to','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeH);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeH[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeH[$i][1]." %"?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeH[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeH[$i][3]." %"?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeH[$i][4],2)." %"?>
							</td>
						</tr>
					<?php
						}
					?>

				</table>
				<br><br>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('to','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeP);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeP[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeP[$i][1]." ".$displayPressUnits?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeP[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeP[$i][3]." ".$displayPressUnits?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeP[$i][4],3)." ".$displayPressUnits?>
							</td>
						</tr>
					<?php
						}
					?>
				</table>
				<br><br>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/dewpoint.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('to','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeD);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeD[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeD[$i][1]." ".$displayTempUnits?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeD[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeD[$i][3]." ".$displayTempUnits?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeD[$i][4],2)." ".$displayTempUnits?>
							</td>
						</tr>
					<?php
						}
					?>
				</table>
				<br><br>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/apparent.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeA);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeA[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeA[$i][1]." ".$displayTempUnits?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeA[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeA[$i][3]." ".$displayTempUnits?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeA[$i][4],2)." ".$displayTempUnits?>
							</td>
						</tr>
					<?php
						}
					?>
				</table>
				<br><br>
				<table class="table tableSpacing2Padding2">
					<tr>
						<th colspan="5">
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="iconDB" alt=''>
						</th>
					</tr>
					<tr>
						<th colspan="2" style="text-align:center">
							<?php echo lang('from','c');?>
						</th>
						<th colspan="2" style="text-align:center">
							<?php echo lang('to','c');?>
						</th>
						<th>
							<?php echo lang('change rate','c')?> / 10 <?php echo lang('minAbbr','c')?>
						</th>
					</tr>
					<?php
						for($i=0;$i<count($spikeR);$i++){
					?>
						<tr>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeR[$i][0])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeR[$i][1]." ".$displayRainUnits?>
							</td>
							<td style="text-align:center">
								<?php echo date($dateTimeFormat,$spikeR[$i][2])?>
							</td>
							<td style="text-align:right">
								<?php echo $spikeR[$i][3]." ".$displayRainUnits?>
							</td>
							<td style="text-align:right">
								<?php echo number_format($spikeR[$i][4],3)." ".$displayRainUnits?>
							</td>
						</tr>
					<?php
						}
					?>
				</table>
			</div>
		</div>
		<?php include($baseURL."footer.php")?>
	</body>
</html>



