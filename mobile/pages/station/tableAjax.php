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
	#	Interactive table data
	#
	# 	A script which generates data for the interactive table.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	include("../../header.php");
	
	$value = $_GET['value'];
	$interval = $_GET['interval'];
	$parametersFull = $_GET['parameters'];
	$parametersFull = str_replace("bracketL","(",$parametersFull);
	$parametersFull = str_replace("bracketR",")",$parametersFull);
	$parameters = explode(",",$parametersFull);
	
	if($parametersFull==""){
		die;
	}
	
	if($interval=="custom"){
		$from = $_GET['from'];
		$to = $_GET['to'];
		$checkFrom = strtotime($from);
		if(date('Y',$checkFrom)<1900 || date('Y',$checkFrom)>2100){
			die();		
		}
		$checkTo = strtotime($to);
		if(date('Y',$checkTo)<1900 || date('Y',$checkTo)>2100){
			die();		
		}
	}
	
	$possibleParameters = array("avg(T)","avg(H)","avg(P)","avg(W)","avg(G)","avg(B)","avg(S)","avg(D)","avg(A)","max(Tmax)","max(H)","max(P)","max(W)","max(G)","max(S)","max(D)","max(A)","min(Tmin)","min(H)","min(P)","min(W)","min(G)","min(S)","min(D)","min(A)","max(Tmax)-min(Tmin)","max(H)-min(H)","max(P)-min(P)","max(W)-min(W)","max(G)-min(G)","max(S)-min(S)","max(D)-min(D)","max(A)-min(A)","max(R)","R","min(R)","max(R)-min(R)","avg(RR)","max(RR)","min(RR)","max(RR)-min(RR)");

	$parametersOriginal = $parameters;
	$parameters = array();
	
	for($i=0;$i<count($parametersOriginal);$i++){
		if(in_array(trim($parametersOriginal[$i]),$possibleParameters)){
			$parameters[] = $parametersOriginal[$i];
		}
	}
	$parametersFull = implode(", ",$parameters);

	$result = array();
	
	// first select grouping
	if($value=="all"){
		$grouping = "YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime), MINUTE(DateTime)";
	}
	if($value=="h"){
		$grouping = "YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)";
	}
	if($value=="d"){
		$grouping = "YEAR(DateTime), MONTH(DateTime), DAY(DateTime)";
	}
	if($value=="m"){
		$grouping = "YEAR(DateTime), MONTH(DateTime)";
	}
	
	// select interval
	if($interval == "1h"){
		$span = "WHERE DateTime >= now() - interval 1 hour";
	}
	if($interval == "today"){
		$span = "WHERE DATE(DateTime) = CURDATE()";
	}
	if($interval == "24h"){
		$span = "WHERE DateTime >= now() - interval 24 hour";
	}
	if($interval == "yesterday"){
		$span = "WHERE DATE(DateTime) = CURDATE() - INTERVAL 1 DAY";
	}
	if($interval == "thisweek"){ // here we have to consider whether user wants Sunday or Monday as first day of the week - set in config file
		$span = "WHERE YEARWEEK(DateTime,$firstWeekday) = YEARWEEK(CURDATE(),$firstWeekday)";
	}
	if($interval == "lastweek"){
		$span = "WHERE YEARWEEK(DateTime,$firstWeekday) = (YEARWEEK(CURDATE(),$firstWeekday)-1)";
	}
	if($interval == "thismonth"){
		$span = "WHERE YEAR(DateTime) = YEAR(CURDATE()) AND MONTH(DateTime) = MONTH(CURDATE())";
	}
	if($interval == "lastmonth"){
		$span = "WHERE YEAR(DateTime) = YEAR(CURRENT_DATE - INTERVAL 1 MONTH) AND MONTH(DateTime) = MONTH(CURRENT_DATE - INTERVAL 1 MONTH)";
	}
	
	if($interval == "custom"){
		$span = "WHERE DateTime >= '$from' AND DateTime <= '$to'";
	}
?>
<table class="table">
	<thead>
		<tr>
		<th></th>
		<?php
			for($i=0;$i<count($parameters);$i++){
				echo "<th>";
				switch($parameters[$i]){
					case "avg(T)":
						echo "<img src='".$pageURL.$path."icons/temp.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "avg(H)":
						echo "<img src='".$pageURL.$path."icons/humidity.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(%)</span>";
						break;
					case "avg(P)":
						echo "<img src='".$pageURL.$path."icons/pressure.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayPressUnits.")</span>";
						break;
					case "avg(W)":
						echo "<img src='".$pageURL.$path."icons/wind.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "avg(G)":
						echo "<img src='".$pageURL.$path."icons/gust.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "avg(S)":
						echo "<img src='".$pageURL.$path."icons/sun.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(W/m2)</span>";
						break;
					case "avg(D)":
						echo "<img src='".$pageURL.$path."icons/dewpoint.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "avg(A)":
						echo "<img src='".$pageURL.$path."icons/apparent.png' class='parameterTable'><br><span class='category'>".lang('avgAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(Tmax)":
						echo "<img src='".$pageURL.$path."icons/temp.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(H)":
						echo "<img src='".$pageURL.$path."icons/humidity.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(%)</span>";
						break;
					case "max(P)":
						echo "<img src='".$pageURL.$path."icons/pressure.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayPressUnits.")</span>";
						break;
					case "max(W)":
						echo "<img src='".$pageURL.$path."icons/wind.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "max(G)":
						echo "<img src='".$pageURL.$path."icons/gust.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "max(S)":
						echo "<img src='".$pageURL.$path."icons/sun.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(W/m2)</span>";
						break;
					case "max(D)":
						echo "<img src='".$pageURL.$path."icons/dewpoint.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(A)":
						echo "<img src='".$pageURL.$path."icons/apparent.png' class='parameterTable'><br><span class='category'>".lang('maximumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "min(Tmin)":
						echo "<img src='".$pageURL.$path."icons/temp.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "min(H)":
						echo "<img src='".$pageURL.$path."icons/humidity.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(%)</span>";
						break;
					case "min(P)":
						echo "<img src='".$pageURL.$path."icons/pressure.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayPressUnits.")</span>";
						break;
					case "min(W)":
						echo "<img src='".$pageURL.$path."icons/wind.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "min(G)":
						echo "<img src='".$pageURL.$path."icons/gust.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "min(S)":
						echo "<img src='".$pageURL.$path."icons/sun.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(W/m2)</span>";
						break;
					case "min(D)":
						echo "<img src='".$pageURL.$path."icons/dewpoint.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "min(A)":
						echo "<img src='".$pageURL.$path."icons/apparent.png' class='parameterTable'><br><span class='category'>".lang('minimumAbbr','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(Tmax)-min(Tmin)":
						echo "<img src='".$pageURL.$path."icons/temp.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(H)-min(H)":
						echo "<img src='".$pageURL.$path."icons/humidity.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(%)</span>";
						break;
					case "max(P)-min(P)":
						echo "<img src='".$pageURL.$path."icons/pressure.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayPressUnits.")</span>";
						break;
					case "max(W)-min(W)":
						echo "<img src='".$pageURL.$path."icons/wind.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "max(G)-min(G)":
						echo "<img src='".$pageURL.$path."icons/gust.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayWindUnits.")</span>";
						break;
					case "max(R)-min(R)":
						echo "<img src='".$pageURL.$path."icons/rain.png' class='parameterTable'><br><span class='category'>".lang('total','c')."<br>(".$displayRainUnits.")</span>";
						break;
					case "max(S)-min(S)":
						echo "<img src='".$pageURL.$path."icons/sun.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(W/m2)</span>";
						break;
					case "max(D)-min(D)":
						echo "<img src='".$pageURL.$path."icons/dewpoint.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayTempUnits.")</span>";
						break;
					case "max(A)-min(A)":
						echo "<img src='".$pageURL.$path."icons/apparent.png' class='parameterTable'><br><span class='category'>".lang('range','c')."<br>(".$displayTempUnits.")</span>";
						break;
					default:
				}
				echo "</th>";
			}
		?>
		</tr>
	</thead>
	<tbody>
<?php
	
	$current = "";
	// first make a fix for the rain - we need to calculate the daily values and them sum those
	$monthlySum = array();
	$a = mysqli_query($con,
		"
		SELECT DateTime, max(R), MONTH(DateTime)
		FROM alldata 
		$span
		GROUP BY Year(DateTime), Month(DateTime), Day(DateTime)
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($a)){
		if($current==""){
			$current = convertR($row['max(R)']);
			$currentMonth = $row['MONTH(DateTime)'];
		}
		else{
			if($row['MONTH(DateTime)']==$currentMonth){
				$current += convertR($row['max(R)']);
			}
			else{
				array_push($monthlySum, $current);
				$currentMonth = $row['MONTH(DateTime)'];
				$current = convertR($row['max(R)']);
			}
		}
	}
	
	array_push($monthlySum, $current);

	$x = mysqli_query($con,
		"
		SELECT DateTime, $parametersFull, R
		FROM alldata 
		$span
		GROUP BY $grouping
		ORDER BY DateTime
		"
	);

	while($row = mysqli_fetch_array($x)){
		$currentDate = date_create($row['DateTime']);
		if($value=="all"){
			$dateFormated = date_format($currentDate,$dateFormat." ".$timeFormat);
		}
		if($value=="h"){
			$dateFormated = date_format($currentDate,$dateFormat." ".$timeFormat);
		}
		if($value=="d"){
			$dateFormated = date_format($currentDate,$dateFormat);
		}
		if($value=="m"){
			$dateFormated = date_format($currentDate,'m / Y');
		}
		echo "<tr><td>".$dateFormated."</td>";
		for($i=0;$i<count($parameters);$i++){
			if($parameters[$i]=="avg(T)" || $parameters[$i]=="max(Tmax)" || $parameters[$i]=="min(Tmin)" || $parameters[$i]=="max(Tmax)-min(Tmin)" || $parameters[$i]=="avg(A)" || $parameters[$i]=="max(A)" || $parameters[$i]=="min(A)" || $parameters[$i]=="max(A)-min(A)" || $parameters[$i]=="avg(D)" || $parameters[$i]=="max(D)" || $parameters[$i]=="min(D)" || $parameters[$i]=="max(D)-min(D)"){
				$resultValue = convertT($row[$parameters[$i]]);
				echo "<td>".number_format($resultValue,2)."</td>";
			}
			else if($parameters[$i]=="avg(W)" || $parameters[$i]=="max(W)" || $parameters[$i]=="min(W)" || $parameters[$i]=="max(W)-min(W)" || $parameters[$i]=="avg(G)" || $parameters[$i]=="max(G)" || $parameters[$i]=="min(G)" || $parameters[$i]=="max(G)-min(G)"){
				$resultValue = convertW($row[$parameters[$i]]);
				echo "<td>".number_format($resultValue,2)."</td>";
			}
			else if($parameters[$i]=="avg(P)" || $parameters[$i]=="max(P)" || $parameters[$i]=="min(P)" || $parameters[$i]=="max(P)-min(P)"){
				$resultValue = convertP($row[$parameters[$i]]);
				echo "<td>".number_format($resultValue,2,".",'')."</td>";
			}
			else if($parameters[$i]=="max(R)-min(R)"){
				if($value=="all"){
					$resultValue = convertR($row['R']);
					echo "<td>".number_format($resultValue,2)."</td>";
				} // rain total from one measurement is nonsense
				else if($value=="m"){
					echo "<td>".number_format($monthlySum[$i],2)."</td>"; // in this case insert previously calculated fix for monthly rain sum
				} 
				else{
					$resultValue = convertR($row[$parameters[$i]]);
					echo "<td>".number_format($resultValue,2)."</td>";
				}
			}
			else{
					$resultValue = $row[$parameters[$i]];
				echo "<td>".number_format($resultValue,2)."</td>";
			}
		}
		echo "</tr>";
	}

?>

	</tbody>
</table>