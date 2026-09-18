<?php 

	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCols = array("T","Tmax","Tmin");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCols = array("A","A","A");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="D"){
		$heading = lang("dew point",'c');
		$mySQLCols = array("D","D","D");
		$colors['min'] = "#007FFF";
		$colors['max'] = "#D90000";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCols = array("H","H","H");
		$colors['min'] = "#d9a300";
		$colors['max'] = "#00b300";
		$deviationsDecimals = 1;
		$dp = 1;
		$UoM = "%";
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCols = array("P","P","P");
		$colors['min'] = "#ffa64c";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = $decimalsP + 2;
		$dp = $decimalsP;
		$UoM = unitFormatter($displayPressUnits);
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCols = array("W","W","W");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCols = array("G","G","G");
		$colors['min'] = "#aaaaaa";
		$colors['max'] = "#a300d9";
		$deviationsDecimals = 2;
		$dp = 1;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCols = array("S","S","S");
		$colors['min'] = "#222222";
		$colors['max'] = "#ffd24c";
		$deviationsDecimals = 0;
		$dp = 0;
		$UoM = "W/m2";
	}
	if($var=="R"){
		$heading = lang("precipitation",'c');
		$colors['min'] = "#999999";
		$colors['max'] = "#006cd9";
		$mySQLCols = array("R","R","R");
		if($displayRainUnits=="in"){
			$dp = 2;
		}
		else{
			$dp = 1;
		}
		$UoM = unitFormatter($displayRainUnits);
	}	
	
	function chooseConvertor($value){
		global $var;
		if($var=="T" || $var=="A" || $var=="D"){
			return convertT($value);
		}
		if($var=="H"){
			return ($value);
		}
		if($var=="P"){
			return convertP($value);
		}
		if($var=="W" || $var=="G"){
			return convertW($value);
		}
		if($var=="S"){
			return ($value);
		}
		if($var=="R"){
			return convertR($value);
		}
	}
	
	