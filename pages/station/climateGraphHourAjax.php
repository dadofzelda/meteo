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
	#	of temperature
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
	
	$q = $_GET['q'];
	$month = $_GET['month'];
	
	// validate month
	if($month<1 || $month>12){
		echo "Invalid date";
		die();
	}
	if($var!="R"){
		$title = lang("month".$month,'c')." - ";
		if($q=="avg"){
			$title .= lang("average","c")." ".strtolower($heading);
		}
		if($q=="max"){
			$title .= lang('maximumAbbr','c')." ".strtolower($heading);
		}
		if($q=="min"){
			$title .= lang('minimumAbbr','c')." ".strtolower($heading);
		}
		if($q=="range"){
			$title .= lang('range','c');
		}
		
		if($q=="avg"){
			$result = mysqli_query($con,"
				SELECT avg(".$mySQLCols[0]."), YEAR(DateTime), HOUR(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), HOUR(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['HOUR(DateTime)']] = number_format(chooseConvertor($row['avg('.$mySQLCols[0].')']),$dp+1,".","");
			}
		}
		if($q=="min"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), YEAR(DateTime), HOUR(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), HOUR(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['HOUR(DateTime)']] = number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp,".","");
			}
		}
		if($q=="max"){
			$result = mysqli_query($con,"
				SELECT max(".$mySQLCols[1]."), YEAR(DateTime), HOUR(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), HOUR(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['HOUR(DateTime)']] = number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp,".","");
			}
		}
		if($q=="range"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), max(".$mySQLCols[1]."), YEAR(DateTime), HOUR(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), HOUR(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['HOUR(DateTime)']] = array(number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp,".",""),number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp,".",""));
			}
		}
	}
	else{
		$title = lang("month".$month,"c")." - ";
		if($q=="avg"){
			$title .= lang("average precipitation","c");
		}
		if($q=="max"){
			$title .= lang('maximumAbbr','c')." ".lang("precipitation",'c');
		}
		
		if($q=="avg"){
			$result = mysqli_query($con,"
				SELECT HOUR(DATETIME), AVG(Rain), YEAR(DATETIME), MONTH(DATETIME)
				FROM (
					SELECT DATETIME, (MAX(R)-MIN(R)) AS Rain
					FROM alldata
					GROUP BY YEAR(DATETIME), MONTH(DATETIME), DAY(DATETIME), HOUR(DATETIME)
					ORDER BY DATETIME
				) AS DailyMaxTable
				GROUP BY YEAR(DATETIME), MONTH(DATETIME), HOUR(DATETIME)
				ORDER BY HOUR(DATETIME)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DATETIME)']][$row['HOUR(DATETIME)']] = number_format(chooseConvertor($row['AVG(Rain)']),$dp+1,".","");
			}
		}
		if($q=="max"){
			$result = mysqli_query($con,"
				SELECT HOUR(DateTime), max(Rain), YEAR(DateTime)
				FROM (
					SELECT DateTime, (MAX(R)-MIN(R)) AS Rain
					FROM alldata
					GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)
					ORDER BY DateTime
				) AS DailyMaxTable
				GROUP BY YEAR(DateTime), MONTH(DateTime), HOUR(DateTime)
				ORDER BY HOUR(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['HOUR(DateTime)']] = number_format(chooseConvertor($row['max(Rain)']),$dp,".","");
			}
		}
	}

	foreach($yearData as $y=>$values){
		$data = array();
		for($h=0;$h<24;$h++){
			if(isset($values[$h])){
				$data[] = $values[$h];
			}
			else{
				$data[] = null;
			}
		}
		$currentY['name'] = $y;
		$currentY['data'] = $data;
		$final['data'][] = $currentY;
	}
	$final['title'] = $title;
	
	print json_encode($final, JSON_NUMERIC_CHECK);

?>