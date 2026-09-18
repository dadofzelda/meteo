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
	#	Data for long-term statistics temperature graphs
	#
	# 	A script that generates data for long-term statistics graph.
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
	
	if($var!="R"){
		if($q=="avg"){
			$title = lang("average","c")." ".strtolower($heading);
		}
		if($q=="max"){
			$title = lang('maximumAbbr','c')." ".strtolower($heading);
		}
		if($q=="min"){
			$title = lang('minimumAbbr','c')." ".strtolower($heading);
		}
		if($q=="range"){
			$title = lang('range','c');
		}
		
		$yearsData = array();
		if($q=="avg"){
			$result = mysqli_query($con,"
				SELECT avg(".$mySQLCols[0]."), YEAR(DateTime), MONTH(DateTime)
				FROM alldata 
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['avg('.$mySQLCols[0].')']),($dp+1),".","");
			}
		}
		if($q=="max"){
			$result = mysqli_query($con,"
				SELECT max(".$mySQLCols[1]."), YEAR(DateTime), MONTH(DateTime)
				FROM alldata 
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),($dp),".","");
			}
		}
		if($q=="min"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), YEAR(DateTime), MONTH(DateTime)
				FROM alldata 
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),($dp),".","");
			}
		}
		if($q=="range"){
			$result = mysqli_query($con,"
				SELECT min(".$mySQLCols[2]."), max(".$mySQLCols[1]."), YEAR(DateTime), MONTH(DateTime)
				FROM alldata 
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY DateTime
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = array(number_format(chooseConvertor($row['min('.$mySQLCols[2].')']),$dp,".",""),number_format(chooseConvertor($row['max('.$mySQLCols[1].')']),$dp,".",""));
			}
		}
	}
	else{
		if($q=="avg"){
			$title = lang("average","c")."/".lang("day","c");
		}
		if($q=="max"){
			$title = lang('maximumAbbr','c')."/".lang("day","c");
		}
		if($q=="total"){
			$title = lang('total','c');
		}
		
		$yearsData = array();
		if($q=="avg"){
			$result = mysqli_query($con,"
				SELECT YEAR(DateTime), MONTH(DateTime), avg(DailyRain)
				FROM (
					SELECT DateTime, MAX(R) AS DailyRain
					FROM alldata
					GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime) 
					ORDER BY DateTime
				) AS DailyMaxTable
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY YEAR(DateTime), MONTH(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['avg(DailyRain)']),($dp+1),".","");
			}
		}
		if($q=="max"){
			$result = mysqli_query($con,"
				SELECT YEAR(DateTime), MONTH(DateTime), max(DailyRain)
				FROM (
					SELECT DateTime, MAX(R) AS DailyRain
					FROM alldata
					GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime) 
					ORDER BY DateTime
				) AS DailyMaxTable
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY YEAR(DateTime), MONTH(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['max(DailyRain)']),($dp),".","");
			}
		}
		if($q=="total"){
			$result = mysqli_query($con,"
				SELECT YEAR(DateTime), MONTH(DateTime), sum(DailyRain)
				FROM (
					SELECT DateTime, MAX(R) AS DailyRain
					FROM alldata
					GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime) 
					ORDER BY DateTime
				) AS DailyMaxTable
				GROUP BY YEAR(DateTime), MONTH(DateTime)
				ORDER BY YEAR(DateTime), MONTH(DateTime)
				"
			);
			while($row = mysqli_fetch_array($result)){
				$yearData[$row['YEAR(DateTime)']][$row['MONTH(DateTime)']] = number_format(chooseConvertor($row['sum(DailyRain)']),($dp),".","");
			}
		}
	}
	
	foreach($yearData as $y=>$values){
		$data = array();
		for($i=1;$i<=12;$i++){
			if(isset($values[$i])){
				$data[] = $values[$i];
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