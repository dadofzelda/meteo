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
	#	Sunrise, sunset and daylength calculation
	#
	# 	AJAX triggered script to calculate and return sunrise, sunset and 
	#	daylength for user specified date.
	#
	#############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../../config.php");
	include("../../header.php");
	
	$lat = $stationLat;
	$lon = $stationLon;
	
	$y = $_GET['y'];
	$m = $_GET['m'];
	$d = $_GET['d'];
	
	$dateTimeZone = new DateTimeZone($stationTZ);
	$dateTime = new DateTime("now", $dateTimeZone);
	$my = $dateTimeZone->getOffset($dateTime);
	$offset = $my/3600;
	
	$dateComplete = $m."/".$d."/".$y;
	
	$dayStart = date("U",strtotime($dateComplete." 00:00"));
	$dayEnd = date("U",strtotime($dateComplete." 23:59"));
	
	/*Normal*/
	$sunRise=date_sunrise(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,90.5,$offset);
	$sunRiseGraph=date_sunrise(strtotime($dateComplete),SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$offset);
	$sunSet=date_sunset(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,90.5,$offset);
	$sunSetGraph=date_sunset(strtotime($dateComplete),SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$offset);
	/*Nautical*/
	$twilightRiseNaut=date_sunrise(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,102,$offset);
	$twilightSetNaut=date_sunset(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,102,$offset);
	/*Astronomical*/
	$twilightRiseAstro=date_sunrise(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,108,$offset);
	$twilightSetAstro=date_sunset(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,108,$offset);
	/*Civil*/
	$twilightRiseCivil=date_sunrise(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,96,$offset);
	$twilightSetCivil=date_sunset(strtotime($dateComplete),SUNFUNCS_RET_STRING,$lat,$lon,96,$offset);
	
	if($twilightRiseAstro==false){
		$twilightRiseAstro = "00:01";
	}
	if($twilightSetAstro==false){
		$twilightSetAstro = "23:59";
	}

	$diff = round(($sunSetGraph - $sunRiseGraph)/2/60);
	$str = "+ ".$diff." minute";
	
	$sunRiseTime = strtotime($dateComplete." ".$sunRise);
	$transitTime = strtotime($str,$sunRiseTime);
	$transit = date('H:i', $transitTime);

	$diff = ($sunSetGraph-$sunRiseGraph)/60;
	$diffN = 1441 - $diff;
	$diffM = $diff % 60;
	if($diffM<10){
		$diffM = "0".$diffM;
	}
	$diffNM = $diffN % 60;
	if($diffNM<10){
		$diffNM = "0".$diffNM;
	}
	$dayLength = floor($diff/60).":".$diffM;
	$nightLength = floor($diffN/60).":".$diffNM;
	
	$final['sunRise'] = falseCheck($sunRise);
	$final['sunSet'] = falseCheck($sunSet);
	$final['transit'] = falseCheck($transit);
	$final['dayLength'] = falseCheck($dayLength);
	$final['nightLength'] = falseCheck($nightLength);
	$final['twilightRiseAstro'] = falseCheck($twilightRiseAstro);
	$final['twilightRiseCivil'] = falseCheck($twilightRiseCivil);
	$final['twilightRiseNaut'] = falseCheck($twilightRiseNaut);
	$final['twilightSetAstro'] = falseCheck($twilightSetAstro);
	$final['twilightSetCivil'] = falseCheck($twilightSetCivil);
	$final['twilightSetNaut'] = falseCheck($twilightSetNaut);
	
	print json_encode($final, JSON_NUMERIC_CHECK);

	function falseCheck($var){
		if($var!==false){
			return $var;
		}
		else{
			return "";
		}
	}
?>