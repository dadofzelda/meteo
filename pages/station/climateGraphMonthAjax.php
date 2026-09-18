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
	#	Data for long-term statistics monthly temperature graphs
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
	
	
	$month = $_GET['month'];
	
	if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}
	
	include_once("climateFunctions.php");
	
	$monthDays = date("t",strtotime("2016-".$month."-15")); // dummy date in the specified month in leap year
	
	// validate month
	if($month<1 || $month>12){
		echo "Invalid date";
		die();
	}
	
	if($var!="R"){
		$q = $_GET['q'];
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
				SELECT avg(".$mySQLCols[0]."), YEAR(DateTime), DAY(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['DAY(DateTime)']] = number_format(chooseConvertor($row['avg('.$mySQLCols[0].')']),$dp+1,".","");	
			}
		}
		if($q=="min"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), YEAR(DateTime), DAY(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['DAY(DateTime)']] = number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp+1,".","");
			}
		}
		if($q=="max"){
			$result = mysqli_query($con,"
				SELECT max(".$mySQLCols[1]."), YEAR(DateTime), DAY(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['DAY(DateTime)']] = number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp+1,".","");
			}
		}
		if($q=="range"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), max(".$mySQLCols[1]."), YEAR(DateTime), DAY(DateTime)
				FROM alldata 
				WHERE MONTH(DateTime) = $month
				GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['DAY(DateTime)']] = array(number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp,".",""),number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp,".",""));
			}
		}
	}
	else{
		$title = lang("month".$month,"c");
		$result = mysqli_query($con,"
			SELECT max(R), YEAR(DateTime), DAY(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime) = $month
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$yearData[$row['YEAR(DateTime)']][$row['DAY(DateTime)']] = number_format(chooseConvertor($row['max(R)']),$dp+1,".","");
		}
	}
	foreach($yearData as $y=>$values){
		$data = array();
		for($d=1;$d<=$monthDays;$d++){
			if(isset($values[$d])){
				$data[] = $values[$d];
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
	
	for($d=1;$d<=$monthDays;$d++){
		$categories[] = ($d*1)." ".lang("month".$month,"c");
	}
	
	$final['categories'] = $categories;
	
	print json_encode($final, JSON_NUMERIC_CHECK);

?>