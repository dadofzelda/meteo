<?php

	foreach($_GET as $key=>$value){
		$parameters[trim($key)] = trim(urldecode($value));
	}

	echo "Loading file...<br><br>";

	echo "Loading CSV/text file from <i>".$parameters['path']."</i><br><br>";

	$log = file($parameters['path']);
	if(count($log)>0){
		echo "File loaded successfully.<br>";
	}
	else{
		die("<span style='color:red'>Cannot find the file specified. Check it has the right permissions and double check the path. If you are using URL try using relative path or vice versa.</span><br>");
	}

	$header = 0;
	echo "<br><br>Starting to parse data....<br><br>";

	if($header==0){
		$limit = 1;
	}
	else{
		$limit = 2;
	}

	if($header==1){
		$headerRow = trim($log[0]);
		$row = trim($log[1]);

		$delimiter = $parameters['delimiter'];
		if($delimiter=="semicolon"){
			$fields = explode(";",$row);
			$columns = explode(";",$headerRow);
		}
		if($delimiter=="comma"){
			$fields = explode(",",$row);
			$columns = explode(",",$headerRow);
		}
		if($delimiter=="space"){
			$fields = explode(" ",$row);
			$columns = explode(" ",$headerRow);
		}
		if($delimiter=="tab"){
			$fields = preg_split('/\s+/', $row);
			$columns = preg_split('/\s+/', $headerRow);
		}
		if($delimiter=="colon"){
			$fields = explode(":",$row);
			$columns = explode(":",$headerRow);
		}
		if($delimiter=="vertical"){
			$fields = explode("|",$row);
			$columns = explode("|",$headerRow);
		}
		echo "Loaded data:<br>";
		echo "<table><tr><td>Field</td><td>Field Number</td><td>Value (first data set)</td></tr>";
		for($i=0;$i<count($fields);$i++){
			echo "<tr><td style='font-weight:bold'>".$columns[$i]."</td><td style='text-align:center'>".$i."</td><td style='text-align:right'>".$fields[$i]."</td></tr>";
		}
		echo "</table>";
	}

	if($header==0){
		$row = $log[0];

		$delimiter = $parameters['delimiter'];
		if($delimiter=="semicolon"){
			$fields = explode(";",$row);
		}
		if($delimiter=="comma"){
			$fields = explode(",",$row);
		}
		if($delimiter=="space"){
			$fields = explode(" ",$row);
		}
		if($delimiter=="tab"){
			$fields = preg_split('/\s+/', $row);
		}
		if($delimiter=="colon"){
			$fields = explode(":",$row);
		}
		if($delimiter=="vertical"){
			$fields = explode("|",$row);
		}
		echo "Loaded data:<br>";
		echo "<table>";
		echo "<tr><td>Field Number</td><td>Value</td></tr>";
		for($i=0;$i<count($fields);$i++){
			echo "<tr><td style='font-weight:bold'>".$i."</td><td style='text-align:right'>".$fields[$i]."</td></tr>";
		}
		echo "</table>";
	}
