<?php

	# 		Sweden Current Conditions
	# 		Namespace:		weatherSweden
	#		Meteotemplate Block
	
	# 		v2.0 - Nov 2, 2016
	#			- converted to use JSON
	#			- rearranged parameters to one table
	# 		v3.0 - Jun 29, 2017
	# 			- added color to table
	# 			- optimization
	
		
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
	
	if(!is_dir("cache")){
		mkdir("cache");
	}
	
	if(file_exists("cache/forecastSwedenCache.txt")){ 
		if (time()-filemtime("cache/forecastSwedenCache.txt") > 60 * 20) {
			unlink("cache/forecastSwedenCache.txt");
		}
	}
	if(file_exists("cache/forecastSwedenCache.txt")){
		$data = json_decode(file_get_contents("cache/forecastSwedenCache.txt"),true);
		$all = json_decode(file_get_contents("cache/all.txt"),true);
	}
	else { 
		// get Temps
		$rawT = json_decode(file_get_contents("http://www.smhi.se/wpt-a/backend_observation/metobs/station/values/t"),true);
		$rawR = json_decode(file_get_contents("http://www.smhi.se/wpt-a/backend_observation/metobs/station/values/p"),true);
		$rawH = json_decode(file_get_contents("http://www.smhi.se/wpt-a/backend_observation/metobs/station/values/r"),true);
		$rawP = json_decode(file_get_contents("http://www.smhi.se/wpt-a/backend_observation/metobs/station/values/msl"),true);
		$rawW = json_decode(file_get_contents("http://www.smhi.se/wpt-a/backend_observation/wind/stations"),true);
		
		foreach($rawT as $stationT){
			$id = $stationT['id'];
			$time = $stationT['values']['t']['values'][0]['time'];
			if(time()-$time<60*60*2){
				$data[$id]['name'] = $stationT['name'];
				$data[$id]['T'] = $stationT['values']['t']['values'][0]['value'];
				$all['T'][] = $stationT['values']['t']['values'][0]['value'];
			}
		}
		
		foreach($rawR as $stationR){
			$id = $stationR['id'];
			$time = $stationR['values']['p']['values'][0]['time'];
			if(time()-$time<60*60*2){
				$data[$id]['name'] = $stationR['name'];
				$data[$id]['R'] = $stationR['values']['p']['values'][0]['value'];
				$all['R'][] = $stationR['values']['p']['values'][0]['value'];
			}
		}
		
		foreach($rawH as $stationH){
			$id = $stationH['id'];
			$time = $stationH['values']['r']['values'][0]['time'];
			if(time()-$time<60*60*2){
				$data[$id]['name'] = $stationH['name'];
				$data[$id]['H'] = $stationH['values']['r']['values'][0]['value'];
				$all['H'][] = $stationH['values']['r']['values'][0]['value'];
			}
		}
		
		foreach($rawP as $stationP){
			$id = $stationP['id'];
			$time = $stationP['values']['msl']['values'][0]['time'];
			if(time()-$time<60*60*2){
				$data[$id]['name'] = $stationP['name'];
				$data[$id]['P'] = $stationP['values']['msl']['values'][0]['value'];
				$all['P'][] = $stationP['values']['msl']['values'][0]['value'];
			}
		}
		
		foreach($rawW as $stationW){
			$id = $stationW['id'];
			$time = $stationW['values']['ws']['values'][0]['time'];
			if(time()-$time<60*60*2){
				$data[$id]['name'] = $stationW['name'];
				$data[$id]['W'] = $stationW['values']['ws']['values'][0]['value'];
				$all['W'][] = $stationW['values']['ws']['values'][0]['value'];
			}
		}
		
		file_put_contents("cache/forecastSwedenCache.txt",json_encode($data));
		file_put_contents("cache/all.txt",json_encode($all));
	}

	function fontColor($color){
		$tone = getColorHue($color,"rgb");
		return $tone=="light" ? "black" : "white";
	}
	

?>
	<style>
		.swedenCurrentIcon{
			opacity: 0.7;
			cursor: pointer;
			width: 100%;
			max-width:40px;
			padding-bottom:10px;
			padding-left: 10px;
			padding-right: 10px;
		}
		.swedenCurrentIcon:hover{
			opacity: 1;
		}
		.sort{
			cursor: pointer;
			opacity: 0.8;
		}
		.sort:hover{
			opacity: 1;
		}				
	</style>
	<table style="width:90%;margin: 0 auto">
		<tr>
			<td style="text-align:left;width:25%">
				<img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/se.png" style="width:100%;max-width:60px">
			</td>
			<td style="text-align:center">
				<h2><?php echo lang('current conditions','c')?></h2>
			</td>
			<td style="text-align:right;width:25%">	
				<img src="<?php echo $pageURL.$path?>imgs/climateImgs/outlines/se.png" style="width:100%;max-width:50px">
			</td>			
		</tr>
	</table>
	<br>
	<div id="swedenCurrentT" class="swedenCurrentDiv">
		<table style="width:98%;margin:0 auto" class="table tableSweden">
			<thead>
				<tr>
					<th>
					</th>
					<th style="text-align:center">
						°C<br><span class="fa fa-unsorted sort"></span>
					</th>
					<th style="text-align:center">
						mm<br><span class="fa fa-unsorted sort"></span>
					</th>
					<th style="text-align:center">
						%<br><span class="fa fa-unsorted sort"></span>
					</th>
					<th style="text-align:center">
						hPa<br><span class="fa fa-unsorted sort"></span>
					</th>
					<th style="text-align:center">
						km/h<br><span class="fa fa-unsorted sort"></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php 
					foreach($data as $station){
						$colorT = fill($station['T'],array((min($all['T'])-0.001),(max($all['T'])+0.001)),array("#0040ff","#b30000"));
						$colorH = fill($station['H'],array((min($all['H'])-0.001),(max($all['H'])+0.001)),array("#96600a","#159b2e"));
						$colorW = fill($station['W'],array((min($all['W'])-0.001),(max($all['W'])+0.001)),array("#666666","#7901c4"));
						$colorP = fill($station['P'],array((min($all['P'])-0.001),(max($all['P'])+0.001)),array("#c18100","#7901c4"));
						$colorR = fill($station['R'],array((min($all['R'])-0.001),(max($all['R'])+0.001)),array("#fff","#003fa5"));
						if($station['R']==0){
							$colorR = "rgb(255,255,255)";
						}
				?>
						<tr>
							<td style="text-align:left">
								<?php echo $station['name']?>
							</td>
							<?php
									if(array_key_exists("T",$station)){
							?>
									<td style="background:<?php echo $colorT?>;color:<?php echo fontColor($colorT)?>">
										<?php
											echo number_format($station['T'],1,".","");
										?>
									</td>
							<?php 
									}
									else{
										echo "<td></td>";
									}
							?>
							<?php
									if(array_key_exists("R",$station)){
							?>
									<td style="background:<?php echo $colorR?>;color:<?php echo fontColor($colorR)?>">
										<?php
											echo number_format($station['R'],1,".","");
										?>
									</td>
							<?php 
									}
									else{
										echo "<td></td>";
									}
							?>
							<?php
									if(array_key_exists("H",$station)){
							?>
									<td style="background:<?php echo $colorH?>;color:<?php echo fontColor($colorH)?>">
										<?php
											echo number_format($station['H'],1,".","");
										?>
									</td>
							<?php 
									}
									else{
										echo "<td></td>";
									}
							?>	
							<?php
									if(array_key_exists("P",$station)){
							?>
									<td style="background:<?php echo $colorP?>;color:<?php echo fontColor($colorP)?>">
										<?php
											echo number_format($station['P'],1,".","");
										?>
									</td>
							<?php 
									}
									else{
										echo "<td></td>";
									}
							?>
							<?php
									if(array_key_exists("W",$station)){
							?>
									<td style="background:<?php echo $colorW?>;color:<?php echo fontColor($colorW)?>">
										<?php
											echo number_format($station['W'],1,".","");
										?>
									</td>
							<?php 
									}
									else{
										echo "<td></td>";
									}
							?>
						</tr>
				<?php
					}
				?>
			</tbody>
		</table>
		<div style="width:90%;margin:0 auto;font-size:0.8em;font-variant:small-caps">
			<span class="more" onclick="$('.swedenCurrentDiv').hide();"><?php echo lang('close','c')?></span>
		</div>
	</div>
	<div style="width:90%;margin:0 auto;font-size:0.8em;font-variant:small-caps">
		<?php echo lang('source','c')?>: Sveriges meteorologiska och hydrologiska institut
	</div>
	<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
	<script>
		$(".tableSweden").tablesorter();
	</script>
	