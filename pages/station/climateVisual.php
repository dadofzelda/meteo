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
	#	Data for long-term statistics visualizations
	#
	# 	A script that generates data for long-term statistics visualizations 
	#	of temperature
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
	include($baseURL."scripts/stats.php");
	
	// Get date
	$q = $_GET['q'];
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	include_once("climateFunctions.php");

	if($q=="avg"){
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), avg(".$mySQLCols[0].")
			FROM alldata 
			GROUP BY MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),number_format(chooseConvertor($row['avg('.$mySQLCols[0].')']),$dp+1,".",""));
			$final['data1'][] = $temporary;
		}
		$final['name1'][] = lang("avgAbbr",'c')." ".strtolower($heading);
		$final['title1'][] = lang("avgAbbr",'c')." ".strtolower($heading);
		$final['mincolor1'] = $colors['min'];
		$final['maxcolor1'] = $colors['max'];
		$final['labels1'][] = "#FFFFFF";
	}
	if($q=="max"){
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), max(".$mySQLCols[1].")
			FROM alldata 
			GROUP BY MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp,".",""));
			$final['data1'][] = $temporary;
		}
		$final['name1'][] = lang('maximumAbbr','c')." ".strtolower($heading);
		$final['title1'][] = lang('maximumAbbr','c')." ".strtolower($heading);
		$final['mincolor1'] = $colors['min'];
		$final['maxcolor1'] = $colors['max'];
		$final['labels1'][] = "#FFFFFF";
	}
	if($q=="min"){
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), min(".$mySQLCols[2].")
			FROM alldata 
			GROUP BY MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp,".",""));
			$final['data1'][] = $temporary;
		}
		$final['name1'][] = lang('minimumAbbr','c')." ".strtolower($heading);
		$final['title1'][] = lang('minimumAbbr','c')." ".strtolower($heading);
		$final['mincolor1'] = $colors['min'];
		$final['maxcolor1'] = $colors['max'];
		$final['labels1'][] = "#FFFFFF";
	}
	if($q=="range"){
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), max(".$mySQLCols[1]."), min(".$mySQLCols[2].")
			FROM alldata 
			GROUP BY MONTH(DateTime), DAY(DateTime)
			"
		);
		while($row = mysqli_fetch_array($result)){
			$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),number_format((chooseConvertor($row['max('.$mySQLCols[1].')']))-chooseConvertor($row['min('.$mySQLCols[2].')'])),$dp,".","");
			$final['data1'][] = $temporary;
		}
		$final['name1'][] = ucfirst($heading)." ".lang('range','l');
		$final['title1'][] = $heading." ".lang('range','l');
		$final['mincolor1'] = $colors['min'];
		$final['maxcolor1'] = $colors['max'];
		$final['labels1'][] = "#FFFFFF";
	}
	print json_encode($final, JSON_NUMERIC_CHECK);
?>