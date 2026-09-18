<?php
	include("../../../config.php");
	include("../../../scripts/functions.php");

	if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "Please go to your admin section and go through the settings for this block first.";
		die();
	}

	$showParameter = trim(strtoupper($showParameter));

	if($showParameter=="T" || $showParameter=="A" || $showParameter=="D"){
		$units = unitformatter($displayTempUnits);
	}
	if($showParameter=="H"){
		$units = "%";
	}
	if($showParameter=="P"){
		$units = unitformatter($displayPressUnits);
	}
	if($showParameter=="W" || $showParameter=="G"){
		$units = unitformatter($displayWindUnits);
	}
	if($showParameter=="R"){
		$units = unitformatter($displayRainUnits);
	}
	if($showParameter=="RR"){
		$units = unitformatter($displayRainUnits)."/h";
	}
	if($showParameter=="UV"){
		$units = "";
	}

	$apiData = file_get_contents("../../../meteotemplateLive.txt");
	$apiData = json_decode($apiData,true);

	if($showParameter=="T"){
		$finalValue = number_format(convertor($apiData['T'],"C",$displayTempUnits),1,".","");
	}
	if($showParameter=="H"){
		$finalValue = number_format($apiData['H'],1,".","");
	}
	if($showParameter=="P"){
		if($displayPressUnits=="hpa"){
			$finalValue = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),1,".","");
		}
		else{
			$finalValue = number_format(convertor($apiData['P'],"hpa",$displayPressUnits),2,".","");
		}
	}
	if($showParameter=="W"){
		$finalValue = number_format(convertor($apiData['W'],"kmh",$displayWindUnits),1,".","");
	}
	if($showParameter=="G"){
		$finalValue = number_format(convertor($apiData['G'],"kmh",$displayWindUnits),1,".","");
	}
	if($showParameter=="R"){
		if($displayRainUnits=="mm"){
			$finalValue = number_format(convertor($apiData['R'],"mm",$displayRainUnits),1,".","");
		}
		else{
			$finalValue = number_format(convertor($apiData['R'],"mm",$displayRainUnits),2,".","");
		}
	}
	if($showParameter=="D"){
		$finalValue = number_format(convertor($apiData['D'],"C",$displayTempUnits),1,".","");
	}
	if($showParameter=="A"){
		$finalValue = number_format(convertor($apiData['A'],"C",$displayTempUnits),1,".","");
	}
	if($showParameter=="S"){
		$finalValue = round($apiData['S'],0);
	}
	if($showParameter=="UV"){
		$finalValue = round($apiData['UV'],1);
	}
	$time = date($timeFormat,$apiData['U']);


	if($showParameter=="RR"){
		// rain rate is calculated and reported differently by various SW, so we use db value for all
		$result = mysqli_query($con,"
			SELECT RR
			FROM alldata
			ORDER BY DateTime DESC
			LIMIT 1
			"
		);
		while($row = mysqli_fetch_array($result)){
			if($displayRainUnits=="mm"){
				$finalValue = number_format(convertR($row['RR']),1,".","");
			}
			else{
				$finalValue = number_format(convertR($row['RR']),2,".","");
			}
		}
	}

	$report = array($finalValue,$units,$time);
	$report = json_encode($report);
	echo $report;
?>
