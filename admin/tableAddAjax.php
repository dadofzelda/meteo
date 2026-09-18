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
	#	Database add record
	#
	############################################################################

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	// get configuration
	include("../config.php");


	// get data
	$T = $_GET['T'];
	$Tmax = $_GET['Tmax'];
	$Tmin = $_GET['Tmin'];
	$H = $_GET["H"];
	$P = $_GET["P"];
	$W = $_GET["W"];
	$G = $_GET["G"];
	$B = $_GET["B"];
	$R = $_GET["R"];
	$RR = $_GET["RR"];
	if($solarSensor){
		$S = $_GET["S"];
	}
	
	$year = $_GET["y"];
	$month = $_GET["m"];
	$day = $_GET["d"];
	$hours = $_GET["h"];
	$minutes = $_GET["i"];
	
	$date = $year."-".$month."-".$day." ".$hours.":".$minutes.":00";
	
	$A = apparent($T,$H,$W);
	$D = dewpoint($T,$H);
	
	function dewpoint($T,$H){
		$Tc = $T;
		global $dataTempUnits;
		if($dataTempUnits=="F"){
			$Tc = (($T - 32)*5/9);
		}
		$D = round(((pow(($H/100), 0.125))*(112+0.9*$Tc)+(0.1*$Tc)-112),1);
		if($dataTempUnits=="F"){
			$D = (($D * 9/5) + 32);
		}
		return $D;
	}
	
	function apparent($T,$H,$W){
		$Tc = $T;
		$Wms = $W;
		global $dataTempUnits;
		global $dataWindUnits;
		if($dataTempUnits=="F"){
			$Tc = (($T - 32)*5/9);
		}
		if($dataWindUnits=="kmh"){
			$Wms = $W/3.6;
		}
		if($dataWindUnits=="mph"){
			$Wms = $W * 0.44704;
		}
		if($dataWindUnits=="kt"){
			$Wms = $W * 0.514444;
		}
		$e = ($H/100)*6.105*pow(2.71828, ((17.27*$Tc)/(237.7+$Tc)));
		$A = round(($Tc + 0.33*$e-0.7*$Wms-4),1);
		if($dataTempUnits=="F"){
			$A = (($A * 9/5) + 32);
		}
		return $A;
	}	
	
	// create string for data upload to MySQL
	$parameters = array();
	$values = array();
	
	array_push($parameters,"DateTime");
	array_push($values,"'".$date."'");
	
	array_push($parameters,"T");
	array_push($values,$T);

	array_push($parameters,"Tmax");
	array_push($values,$Tmax);

	array_push($parameters,"Tmin");
	array_push($values,$Tmin);

	array_push($parameters,"D");
	array_push($values,$D);

	array_push($parameters,"A");
	array_push($values,$A);

	array_push($parameters,"H");
	array_push($values,$H);

	array_push($parameters,"P");
	array_push($values,$P);

	array_push($parameters,"W");
	array_push($values,$W);

	array_push($parameters,"G");
	array_push($values,$G);

	array_push($parameters,"B");
	array_push($values,$B);

	array_push($parameters,"R");
	array_push($values,$R);

	array_push($parameters,"RR");
	array_push($values,$RR);
	
	if($solarSensor){
		array_push($parameters,"S");
		array_push($values,$S);
	}
	
	
	$query = "INSERT INTO alldata (".implode(',',$parameters).") values (".implode(',',$values).")";
	echo $query;
	mysqli_query($con,$query);
	
	echo "<script type='text/javascript'>";
	echo "window.close();";
	echo "</script>";

?>