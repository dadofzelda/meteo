<?php

	############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Database table update
	#
	# 	AJAX script that performs database table update.
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	

		$date = $_GET['date'];
		if(!isset($_GET['resetRain'])){
			$T = $_GET['T'];
			$Tmax = $_GET['Tmax'];
			$Tmin = $_GET['Tmin'];
			$H = $_GET['H'];
			$P = $_GET['P'];
			$W = $_GET['W'];
			$G = $_GET['G'];
			$B = $_GET['B'];
			$R = $_GET['R'];
			$RR = $_GET['RR'];
			$D = $_GET['D'];
			$A = $_GET['A'];
			if($solarSensor){
				$S = $_GET['S'];
			}
			$sql = "UPDATE alldata SET T=$T,H=$H,P=$P,Tmax=$Tmax,Tmin=$Tmin,W=$W,G=$G,R=$R,RR=$RR,D=$D,A=$A,B=$B WHERE DateTime='$date'";
			mysqli_query($con, $sql);
			if($solarSensor){
				$sql = "UPDATE alldata SET S=$S WHERE DateTime='$date'";
				mysqli_query($con, $sql);
			}
			echo "Saved.";
		}
		else{
			if($_GET['resetRain']==1){
				$dateExploded = explode("-",$date);
				$d = $dateExploded[2];
				$m = $dateExploded[1];
				$y = $dateExploded[0];
				$sql = "UPDATE alldata SET R=0,RR=0 WHERE DAY(DateTime)=".$d." AND MONTH(DateTime)=".$m." AND YEAR(DateTime)=".$y;
				mysqli_query($con, $sql);
				header("Location: tableEdit.php?d=".$d."&m=".$m."&y=".$y);
			}
		}
		

?>