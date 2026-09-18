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
	#	Annual report visualizations
	#
	# 	A script that generates data for annual report visualizations.
	#
	############################################################################
	#	
	#
	# 	v10.0 Banana 2016-10-28
	#
	############################################################################
	
	include("../../../config.php");
	include("../../../css/design.php");
	include("../../header.php");
	include($baseURL."scripts/stats.php");

	$bearingNames = array(lang("directionN",""),lang("directionNNE",""),lang("directionENE",""),lang("directionE",""),lang("directionESE",""),lang("directionSE",""),lang("directionSSE",""),lang("directionS",""),lang("directionSSW",""),lang("directionSW",""),lang("directionSW",""),lang("directionWSW",""),lang("directionW",""),lang("directionWNW",""),lang("directionNW",""),lang("directionNNW",""));
	
	// Get date
	$chosenYear = $_GET['y'];
	
	// validate year
	if($chosenYear<1900 || $chosenYear>2100){
		echo "Invalid date";
		die();
	}
	

	
		$q = $_GET['q'];
		$span = "Year(DateTime) = ".$chosenYear;
		
		$result = mysqli_query($con,"
			SELECT MONTH(DateTime), DAY(DateTime), avg(T), avg(H), avg(P), avg(W), avg(G), avg(S), avg(A), avg(D), max(Tmax), max(H), max(P), max(W), max(G), max(S), max(A), max(D), min(Tmin), min(H), min(P), min(W), min(G), min(S), min(A), min(D), max(R)
			FROM alldata 
			WHERE $span
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			"
		);
		if($q=="Tavg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['avg(T)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average temperature","c");
			$final['title1'][] = lang("average temperature","c");
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Tmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['max(Tmax)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("temperature",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("temperature",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Tmin"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['min(Tmin)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('minimumAbbr','c')." ".lang("temperature",'c');
			$final['title1'][] = lang('minimumAbbr','c')." ".lang("temperature",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Havg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round($row['avg(H)'],2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average humidity","c");
			$final['title1'][] = lang("average humidity","c");
			$final['mincolor1'][] = "#6CD900";
			$final['maxcolor1'][] = "#002406";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Hmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round($row['max(H)'],2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("humidity",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("humidity",'c');
			$final['mincolor1'][] = "#6CD900";
			$final['maxcolor1'][] = "#002406";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Hmin"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round($row['min(H)'],2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('minimumAbbr','c')." ".lang("temperature",'c');
			$final['title1'][] = lang('minimumAbbr','c')." ".lang("temperature",'c');
			$final['mincolor1'][] = "#6CD900";
			$final['maxcolor1'][] = "#002406";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Pavg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertP($row['avg(P)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average pressure","c");
			$final['title1'][] = lang("average pressure","c");
			$final['mincolor1'][] = "#FF9673";
			$final['maxcolor1'][] = "#660000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Pmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertP($row['max(P)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("pressure",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("pressure",'c');
			$final['mincolor1'][] = "#FF9673";
			$final['maxcolor1'][] = "#660000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Pmin"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertP($row['min(P)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('minimumAbbr','c')." ".lang("pressure",'c');
			$final['title1'][] = lang('minimumAbbr','c')." ".lang("pressure",'c');
			$final['mincolor1'][] = "#FF9673";
			$final['maxcolor1'][] = "#660000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Wavg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertW($row['avg(W)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average wind speed","c");
			$final['title1'][] = lang("average wind speed","c");
			$final['mincolor1'][] = "#FFBFFF";
			$final['maxcolor1'][] = "#400040";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Wmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertW($row['max(W)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("wind speed",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("wind speed",'c');
			$final['mincolor1'][] = "#FFBFFF";
			$final['maxcolor1'][] = "#400040";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Gavg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertW($row['avg(G)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average wind gust","c");
			$final['title1'][] = lang("average wind gust","c");
			$final['mincolor1'][] = "#FFBFFF";
			$final['maxcolor1'][] = "#400040";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Gmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertW($row['max(G)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("wind gust",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("wind gust",'c');
			$final['mincolor1'][] = "#FFBFFF";
			$final['maxcolor1'][] = "#400040";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Davg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['avg(D)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average dew point","c");
			$final['title1'][] = lang("average dew point","c");
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Dmax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['max(D)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("dew point",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("dew point",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Dmin"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['min(D)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('minimumAbbr','c')." ".lang("dew point",'c');
			$final['title1'][] = lang('minimumAbbr','c')." ".lang("dew point",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Aavg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['avg(A)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average apparent temperature","c");
			$final['title1'][] = lang("average apparent temperature","c");
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Amax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['max(A)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("apparent temperature",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("apparent temperature",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="Amin"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertT($row['min(A)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('minimumAbbr','c')." ".lang("apparent temperature",'c');
			$final['title1'][] = lang('minimumAbbr','c')." ".lang("apparent temperature",'c');
			$final['mincolor1'][] = "#0036D9";
			$final['maxcolor1'][] = "#FF0000";
			$final['labels1'][] = "#FFFFFF";
		}
		if($q=="R"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round(convertR($row['max(R)']),2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("precipitation",'c');
			$final['title1'][] = lang("precipitation",'c');
			$final['mincolor1'][] = "#FFFFFF";
			$final['maxcolor1'][] = "#000066";
			$final['labels1'][] = "#000000";
		}
		if($q=="Savg"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round($row['avg(S)'],2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang("average solar radiation","c");
			$final['title1'][] = lang("average solar radiation","c");
			$final['mincolor1'][] = "#FFFFFF";
			$final['maxcolor1'][] = "#B3B300";
			$final['labels1'][] = "#000000";
		}
		if($q=="Smax"){
			while($row = mysqli_fetch_array($result)){
				$temporary = array(($row['DAY(DateTime)']-1),($row['MONTH(DateTime)']-1),round($row['max(S)'],2));
				$final['data1'][] = $temporary;
			}
			$final['name1'][] = lang('maximumAbbr','c')." ".lang("solar radiation",'c');
			$final['title1'][] = lang('maximumAbbr','c')." ".lang("solar radiation",'c');
			$final['mincolor1'][] = "#FFFFFF";
			$final['maxcolor1'][] = "#B3B300";
			$final['labels1'][] = "#000000";
		}
		
	print json_encode($final, JSON_NUMERIC_CHECK);
?>