<?php
	
	############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Database Editing Page - adding values
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("administration",'c')?></title>
		<?php metaHeader()?>
		<style>
			.headIcon{
				width: 30px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main" style="text-align:center">
			<h1>New data</h1>
			<br>
			<table class="table" style="width:auto;margin:0 auto">
				<thead>
					<tr>
						<th style="text-align:center">
							Year
						</th>
						<th>
							Month
						</th>
						<th>
							Day
						</th>
						<th>
							Hour
						</th>
						<th>
							Minute
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align:center">
							<input class="button tooltip" id="y" size="4" title="year in YYYY format" value="<?php echo date('Y')?>">
						</td>
						<td>
							<input class="button tooltip" id="m" size="2" title="month in MM format" value="<?php echo date('m')?>">
						</td>
						<td>
							<input class="button tooltip" id="d" size="2" title="day in DD format" value="<?php echo date('d')?>">
						</td>
						<td>
							<input class="button tooltip" id="h" size="2" title="hour in 24h format" value="<?php echo date('H')?>">
						</td>
						<td>
							<input class="button tooltip" id="i" size="2" title="minute in mm format" value="<?php echo date('i')?>">
						</td>
					</tr>
				</tbody>
			</table>
			<br><br>
			<table class="table">
				<thead>
					<tr>
						<th style="text-align:center">
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="headIcon">
							<br>
							<?php echo lang('avgAbbr','u')?><br>
							(<?php echo $dataTempUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="headIcon">
							<br>
							<?php echo lang('maximumAbbr','u')?><br>
							(<?php echo $dataTempUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/temp.png" class="headIcon">
							<br>
							<?php echo lang('minimumAbbr','u')?><br>
							(<?php echo $dataTempUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/humidity.png" class="headIcon">
							<br>
							(%)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/pressure.png" class="headIcon">
							<br>
							(<?php echo $dataPressUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="headIcon">
							<br>
							(<?php echo $dataWindUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/gust.png" class="headIcon">
							<br>
							(<?php echo $dataWindUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="headIcon">
							<br>
							(<?php echo $dataRainUnits ?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/rain.png" class="headIcon">
							<br>
							(<?php echo $dataRainUnits ?>/<?php echo lang('hAbbr','l')?>)
						</th>
						<th>
							<img src="<?php echo $pageURL.$path?>icons/wind.png" class="headIcon">
							<br>
							(°)
						</th>
						<?php
							if($solarSensor){
								?>
									<th>
										<img src="<?php echo $pageURL.$path?>icons/sun.png" class="headIcon">
										<br>
										(W/m<sup>2</sup>)
									</th>
								<?php
							}
						?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align:center">
							<input id="T" size="4" class="button tooltip" title="Average temperature in °<?php echo $dataTempUnits ?>">
						</td>
						<td>
							<input id="Tmax" size="4" class="button tooltip" title="Maximum temperature in °<?php echo $dataTempUnits ?>">
						</td>
						<td>
							<input id="Tmin" size="4" class="button tooltip" title="Minimum temperature in °<?php echo $dataTempUnits ?>">
						</td>
						<td>
							<input id="H" size="3" class="button tooltip" title="Average humidity in %">
						</td>
						<td>
							<input id="P" size="6" class="button tooltip" title="Average pressure in <?php echo $dataPressUnits ?>">
						</td>
						<td>
							<input id="W" size="4" class="button tooltip" title="Average wind speed in <?php echo $dataWindUnits ?>">
						</td>
						<td>
							<input id="G" size="4" class="button tooltip" title="Maximum wind gust in <?php echo $dataWindUnits ?>">
						</td>
						<td>
							<input id="R" size="4" class="button tooltip" title="Cumulative daily rain in <?php echo $dataRainUnits ?>">
						</td>
						<td>
							<input id="RR" size="4" class="button tooltip" title="Rain rate in <?php echo $dataRainUnits ?>/h">
						</td>
						<td>
							<input id="B" size="4" class="button tooltip" title="Wind bearing in °">
						</td>
						<?php
							if($solarSensor){
								?>
									<td>
									<input id="S" size="6" class="button tooltip" title="Average solar radiation in W/m2"></td>
								<?php
							}
						?>
					</tr>
				</tbody>
			</table>
			<br><br><br>
			<input type="button" class="button" value="Save" onclick="save()">
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
			function save(){
				y = eval($("#y").val());
				if(y<1990 || y><?php echo date('Y')?>){
					alert("Incorrect year.");
					return false;
				}
				m = eval($("#m").val());
				if(m<1 || m>12){
					alert("Incorrect month.");
					return false;
				}
				d = eval($("#d").val());
				if(d<1 || d>31){
					alert("Incorrect day.");
					return false;
				}
				h = eval($("#h").val());
				if(h<0 || h>23){
					alert("Incorrect hour.");
					return false;
				}
				i = eval($("#i").val());
				if(i<0 || i>59){
					alert("Incorrect minute.");
					return false;
				}
				
				if($("#T").val()===""){
					alert("No average temperature specified.");
					return false;
				}
				if($("#Tmax").val()===""){
					alert("No maximum temperature specified.");
					return false;
				}
				if($("#Tmin").val()===""){
					alert("No minimum temperature specified.");
					return false;
				}
				if($("#H").val()===""){
					alert("No humidity specified.");
					return false;
				}
				if($("#P").val()===""){
					alert("No pressure specified.");
					return false;
				}
				if($("#W").val()===""){
					alert("No wind speed specified.");
					return false;
				}
				if($("#G").val()===""){
					alert("No wind gust specified.");
					return false;
				}
				if($("#B").val()===""){
					alert("No wind bearing specified.");
					return false;
				}
				if($("#R").val()===""){
					alert("No precipitation specified.");
					return false;
				}
				if($("#RR").val()===""){
					alert("No rain rate specified.");
					return false;
				}
				<?php
					if($solarSensor){
				?>
						if($("#S").val()===""){
							alert("No solar radiation specified.");
							return false;
						}
				<?php
					}
				?>
				
				T = eval($("#T").val());
				if(T<<?php echo $limitTempMin?> || T><?php echo $limitTempMax?>){
					alert("Average temperature is out of the range specified in config.php.");
					return false;
				}
				Tmax = eval($("#Tmax").val());
				if(Tmax<<?php echo $limitTempMin?> || Tmax><?php echo $limitTempMax?>){
					alert("Maximum temperature is out of the range specified in config.php.");
					return false;
				}
				Tmin = eval($("#Tmin").val());
				if(Tmax<<?php echo $limitTempMin?> || Tmin><?php echo $limitTempMax?>){
					alert("Minimum temperature is out of the range specified in config.php.");
					return false;
				}
				H = eval($("#H").val());
				if(H<0 || H>100){
					alert("Humidity must be between 0 and 100 %.");
					return false;
				}
				P = eval($("#P").val());
				if(P<<?php echo $limitPressureMin?> || P><?php echo $limitPressureMax?>){
					alert("Pressure is out of the range specified in config.php.");
					return false;
				}
				W = eval($("#W").val());
				if(W<0 || W><?php echo $limitWindMax?>){
					alert("Wind speed is out of the range specified in config.php.");
					return false;
				}
				G = eval($("#G").val());
				if(G<0 || G><?php echo $limitWindMax?>){
					alert("Gust speed is out of the range specified in config.php.");
					return false;
				}
				R = eval($("#R").val());
				if(R<0 || R><?php echo $limitRainMax?>){
					alert("Daily rain is out of the range specified in config.php.");
					return false;
				}
				RR = eval($("#RR").val());
				if(RR<0 || RR><?php echo $limitRainRateMax?>){
					alert("Rain rate is out of the range specified in config.php.");
					return false;
				}
				B = eval($("#B").val());
				if(B<0 || B>360){
					alert("Wind bearing is out of the range.");
					return false;
				}
				<?php
					if($solarSensor){
				?>
						S = eval($("#S").val());
						if(S<0 || S><?php echo $limitSolarMax?>){
							alert("Solar radiation is out of the range specified in config.php.");
							return false;
						}
				<?php
					}
				?>
				
				url="tableAddAjax.php?y="+y+"&m="+m+"&d="+d+"&h="+h+"&i="+i+"&T="+T+"&Tmax="+Tmax+"&Tmin="+Tmin+"&H="+H+"&P="+P+"&W="+W+"&G="+G+"&R="+R+"&RR="+RR+"&B="+B;
				<?php
					if($solarSensor){
				?>
						url = url + "&S="+S;
				<?php
					}
				?>
				var win = window.open(url, '_blank');
				win.focus();
			}
		</script>
	</body>
</html>
	