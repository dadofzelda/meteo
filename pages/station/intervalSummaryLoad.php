<?php 
	
	include("../../config.php");
	include("../../scripts/functions.php");

	if(isset($_GET['parameter'])){
		if($_GET['parameter']=="T"){
			$parameter = "T";
		}
		else if($_GET['parameter']=="H"){
			$parameter = "H";
		}
		else if($_GET['parameter']=="P"){
			$parameter = "P";
		}
		else if($_GET['parameter']=="W"){
			$parameter = "W";
		}
		else{
			$parameter = "T";
		}
	}
	else{
		$parameter = "T";
	}
	
	if(isset($_GET['from'])){
		$fromRaw = explode("_",$_GET['from']);
		$fromY = $fromRaw[0];
		if(!is_numeric($fromY) || $fromY<1900 || $fromY>date("Y")){
			$fromY = date("Y");
		}
		$fromM = $fromRaw[1];
		if(!is_numeric($fromM) || $fromM<1 || $fromM>12){
			$fromM = date("m");
		}
		$fromD = $fromRaw[2];
		if(!is_numeric($fromD) || $fromD<1 || $fromD>31){
			$fromD = date("d");
		}
		$fromH = $fromRaw[3];
		if(!is_numeric($fromH) || $fromH<0 || $fromH>24){
			$fromH = date("H");
		}
		$fromMin = $fromRaw[4];
		if(!is_numeric($fromMin) || $fromMin<0 || $fromMin>59){
			$fromMin = date("i");
		}
		$from = $fromY."-".$fromM."-".$fromD." ".$fromH.":".$fromMin;
	}
	else{
		$from = date("Y-m-d H:i",strtotime('last month'));
	}
	
	if(isset($_GET['to'])){
		$toRaw = explode("_",$_GET['to']);
		$toY = $toRaw[0];
		if(!is_numeric($toY) || $toY<1900 || $toY>date("Y")){
			$toY = date("Y");
		}
		$toM = $toRaw[1];
		if(!is_numeric($toM) || $toM<1 || $toM>12){
			$toM = date("m");
		}
		$toD = $toRaw[2];
		if(!is_numeric($toD) || $toD<1 || $toD>31){
			$toD = date("d");
		}
		$toH = $toRaw[3];
		if(!is_numeric($toH) || $toH<0 || $toH>24){
			$toH = date("H");
		}
		$toMin = $toRaw[4];
		if(!is_numeric($toMin) || $toMin<0 || $toMin>59){
			$toMin = date("i");
		}
		$to = $toY."-".$toM."-".$toD." ".$toH.":".$toMin;
	}
	else{
		$to = date("Y-m-d H:i",strtotime('last month'));
	}
	
	// check interval size
	$oneYear = 60 * 60 * 24 * 365;
	$fromTimestamp = strtotime($from);
	$toTimestamp = strtotime($to);
	$intervalLength = $toTimestamp - $fromTimestamp;
	$query = "SELECT DateTime, ".$parameter." FROM alldata WHERE DateTime>='$from' AND DateTime<='$to'";
	$avgUsed = false;
	if($intervalLength>$oneYear){
		$query = "SELECT DateTime, avg(".$parameter.") FROM alldata WHERE DateTime>='$from' AND DateTime<='$to' GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime), HOUR(DateTime)";
		$avgUsed = true;
	}
	if($intervalLength>=($oneYear*3)){
		$query = "SELECT DateTime, avg(".$parameter.") FROM alldata WHERE DateTime>='$from' AND DateTime<='$to' GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)";
		$avgUsed = true;
	}
	
	
	$result = mysqli_query($con,$query);
	while($row = mysqli_fetch_array($result)){
		if($parameter == "T"){
			if(!$avgUsed){
				$value = convertT($row['T']);
			}
			else{
				$value = convertT($row['avg(T)']);
			}
		}
		if($parameter == "H"){
			if(!$avgUsed){
				$value = ($row['H']);
			}
			else{
				$value = convertT($row['avg(H)']);
			}
		}
		if($parameter == "P"){
			if(!$avgUsed){
				$value = convertP($row['P']);
			}
			else{
				$value = convertT($row['avg(P)']);
			}
		}
		if($parameter == "W"){
			if(!$avgUsed){
				$value = convertW($row['W']);
			}
			else{
				$value = convertT($row['avg(W)']);
			}
		}
		$date = strtotime($row['DateTime'])*1000;
		$dateFormatted = array(date('Y',$date),date('m',$date),date('d',$date),date('H',$date),date('i',$date));
		$results[$parameter][] = array($date,$value);
	}
	print json_encode($results, JSON_NUMERIC_CHECK);
?>