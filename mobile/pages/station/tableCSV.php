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
	#	CSV data export
	#
	# 	A script which triggers CSV export of user specified data.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=data.csv');

	$value = $_GET['value'];
	$interval = $_GET['interval'];
	$parametersFull = $_GET['parameters'];
	$parametersFull = str_replace("bracketL","(",$parametersFull);
	$parametersFull = str_replace("bracketR",")",$parametersFull);
	$parameters = explode(",",$parametersFull);
		
	if($parametersFull==""){
		die();
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
	

	include("../../../config.php");
	$languageRaw = file_get_contents($baseURL."lang/gb.php");
	$language['gb'] = json_decode($languageRaw,true);
	$languageRaw = file_get_contents($baseURL."lang/".$lang.".php");
	$language[$lang] = json_decode($languageRaw,true);
	include("../../../scripts/functions.php");

	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// print "\xEF\xBB\xBF"; // fix encoding

	$columns = array();
	array_push($columns,"Date");
	array_push($columns,"Time");



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
		
	for($i=0;$i<count($parameters);$i++){
		switch($parameters[$i]){
			case "avg(T)":
				array_push($columns,lang("temperature",'c')." - ".lang('avgAbbr','c')." (".$displayTempUnits.")");
				break;
			case "avg(H)":
				array_push($columns,lang("humidity",'c')." - ".lang('avgAbbr','c')." (%)");
				break;
			case "avg(P)":
				array_push($columns,lang("pressure",'c')." - ".lang('avgAbbr','c')." (".$displayPressUnits.")");
				break;
			case "avg(W)":
				array_push($columns,lang("wind speed",'c')." - ".lang('avgAbbr','c')." (".$displayWindUnits.")");
				break;
			case "avg(G)":
				array_push($columns,lang("wind gust",'c')." - ".lang('avgAbbr','c')." (".$displayWindUnits.")");
				break;
			case "avg(S)":
				array_push($columns,lang("solar radiation",'c')." - ".lang('avgAbbr','c')." (W/m2)");
				break;
			case "avg(D)":
				array_push($columns,lang("dew point",'c')." - ".lang('avgAbbr','c')." (".$displayTempUnits.")");
				break;
			case "avg(A)":
				array_push($columns,lang("apparent temperature",'c')." - ".lang('avgAbbr','c')." (".$displayTempUnits.")");
				break;
			case "max(Tmax)":
				array_push($columns,lang("temperature",'c')." - ".lang('maximumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "max(H)":
				array_push($columns,lang("humidity",'c')." - ".lang('maximumAbbr','c')." (%)");
				break;
			case "max(P)":
				array_push($columns,lang("pressure",'c')." - ".lang('maximumAbbr','c')." (".$displayPressUnits.")");
				break;
			case "max(W)":
				array_push($columns,lang("wind speed",'c')." - ".lang('maximumAbbr','c')." (".$displayWindUnits.")");
				break;
			case "max(G)":
				array_push($columns,lang("wind gust",'c')." - ".lang('maximumAbbr','c')." (".$displayWindUnits.")");
				break;
			case "max(S)":
				array_push($columns,lang("solar radiation",'c')." - ".lang('maximumAbbr','c')." (W/m2)");
				break;
			case "max(D)":
				array_push($columns,lang("dew point",'c')." - ".lang('maximumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "max(A)":
				array_push($columns,lang("apparent temperature",'c')." - ".lang('maximumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "min(Tmin)":
				array_push($columns,lang("temperature",'c')." - ".lang('minimumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "min(H)":
				array_push($columns,lang("humidity",'c')." - ".lang('minimumAbbr','c')." (%)");
				break;
			case "min(P)":
				array_push($columns,lang("pressure",'c')." - ".lang('minimumAbbr','c')." (".$displayPressUnits.")");
				break;
			case "min(W)":
				array_push($columns,lang("wind speed",'c')." - ".lang('minimumAbbr','c')." (".$displayWindUnits.")");
				break;
			case "min(G)":
				array_push($columns,lang("wind gust",'c')." - ".lang('minimumAbbr','c')." (".$displayWindUnits.")");
				break;
			case "min(S)":
				array_push($columns,lang("solar radiation",'c')." - ".lang('minimumAbbr','c')." (W/m2)");
				break;
			case "min(D)":
				array_push($columns,lang("dew point",'c')." - ".lang('minimumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "min(A)":
				array_push($columns,lang("apparent temperature",'c')." - ".lang('minimumAbbr','c')." (".$displayTempUnits.")");
				break;
			case "max(Tmax)-min(Tmin)":
				array_push($columns,lang("temperature",'c')." - ".lang('range','c')." (".$displayTempUnits.")");
				break;
			case "max(H)-min(H)":
				array_push($columns,lang("humidity",'c')." - ".lang('range','c')." (%)");
				break;
			case "max(P)-min(P)":
				array_push($columns,lang("pressure",'c')." - ".lang('range','c')." (".$displayPressUnits.")");
				break;
			case "max(W)-min(W)":
				array_push($columns,lang("wind speed",'c')." - ".lang('range','c')." (".$displayWindUnits.")");
				break;
			case "max(G)-min(G)":
				array_push($columns,lang("wind gust",'c')." - ".lang('range','c')." (".$displayWindUnits.")");
				break;
			case "max(R)-min(R)":
				array_push($columns,lang("Precipitation")." - ".lang('total','c')." (".$displayRainUnits.")");
				break;
			case "max(S)-min(S)":
				array_push($columns,lang("solar radiation",'c')." - ".lang('range','c')." (W/m2)");
				break;
			case "max(D)-min(D)":
				array_push($columns,lang("dew point",'c')." - ".lang('range','c')." (".$displayTempUnits.")");
				break;
			case "max(A)-min(A)":
				array_push($columns,lang("apparent temperature",'c')." - ".lang('range','c')." (".$displayTempUnits.")");
				break;
			default:
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
	
	fputcsv($output, $columns, ';', ' ');
	
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
		SELECT DateTime, $parametersFull
		FROM alldata 
		$span
		GROUP BY $grouping
		ORDER BY DateTime
		"
	);

	while($row = mysqli_fetch_array($x)){
		$rowData = array();
		$currentDate = date_create($row['DateTime']);
		if($value=="all"){
			array_push($rowData,date_format($currentDate,$dateFormat));
			array_push($rowData,date_format($currentDate,$timeFormat));
		}
		if($value=="h"){
			array_push($rowData,date_format($currentDate,$dateFormat));
			array_push($rowData,date_format($currentDate,$timeFormat));
		}
		if($value=="d"){
			array_push($rowData,date_format($currentDate,$dateFormat));
			array_push($rowData," ");
		}
		if($value=="m"){
			array_push($rowData,date_format($currentDate,'m / Y'));
			array_push($rowData," ");
		}
		
		for($i=0;$i<count($parameters);$i++){
			if($parameters[$i]=="avg(T)" || $parameters[$i]=="max(Tmax)" || $parameters[$i]=="min(Tmin)" || $parameters[$i]=="max(Tmax)-min(Tmin)" || $parameters[$i]=="avg(A)" || $parameters[$i]=="max(A)" || $parameters[$i]=="min(A)" || $parameters[$i]=="max(A)-min(A)" || $parameters[$i]=="avg(D)" || $parameters[$i]=="max(D)" || $parameters[$i]=="min(D)" || $parameters[$i]=="max(D)-min(D)"){
				$resultValue = convertT($row[$parameters[$i]]);
				array_push($rowData,number_format($resultValue,2,".",""));
			}
			else if($parameters[$i]=="avg(W)" || $parameters[$i]=="max(W)" || $parameters[$i]=="min(W)" || $parameters[$i]=="max(W)-min(W)" || $parameters[$i]=="avg(G)" || $parameters[$i]=="max(G)" || $parameters[$i]=="min(G)" || $parameters[$i]=="max(G)-min(G)"){
				$resultValue = convertW($row[$parameters[$i]]);
				array_push($rowData,number_format($resultValue,2,".",""));
			}
			else if($parameters[$i]=="avg(P)" || $parameters[$i]=="max(P)" || $parameters[$i]=="min(P)" || $parameters[$i]=="max(P)-min(P)"){
				$resultValue = convertP($row[$parameters[$i]]);
				array_push($rowData,number_format($resultValue,2,".",''));
			}
			else if($parameters[$i]=="max(R)-min(R)"){
				if($value=="all"){
					array_push($rowData," ");
				} // rain total from one measurement is nonsense
				else if($value=="m"){
					array_push($rowData,number_format($monthlySum[$i],2,".","")); // in this case insert previously calculated fix for monthly rain sum
				}
				else{
					$resultValue = convertR($row[$parameters[$i]]);
					array_push($rowData,number_format($resultValue,2,".",""));
				}
			}
			else{
				$resultValue = $row[$parameters[$i]];
				array_push($rowData,number_format($resultValue,2,".",""));
			}
		}
		fputcsv($output, $rowData, ';', ' ');
	}
?>