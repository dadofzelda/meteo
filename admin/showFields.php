<?php

    ############################################################################
    #
    #   Meteotemplate
    #   http://www.meteotemplate.com
    #   Free website template for weather enthusiasts
    #   Author: Jachym
    #           Brno, Czech Republic
    #   First release: 2015
    #
    ############################################################################
    #           
    #   v2.0    2017-04-20
    #   v2.1    2017-05-02
    #
    ############################################################################

	include("../config.php");
    
	foreach($_GET as $key=>$value){
		$parameters[trim($key)] = trim(urldecode($value));
	}
	
	echo "Loading file...<br><br>";
	echo "Loading CSV/text file from <i>".$parameters['path']."</i><br><br>";
    $log = file($parameters['path']);
	if($log != ""){
        echo "File loaded successfully.<br>";
    }
	else{
		echo "<span style='color:red'>Cannot find the file specified. Check it has the right permissions and double check the path. If you are using URL try using relative path or vice versa.</span><br>";
		die();
	}
	
	// check header
	if($parameters['fileHeader']==1){
		$header = 1;
		echo "Header row in the file, skipping first line, using for field labels.<br>";
	}
	else{
		$header = 0;
		echo "No header, reading from first line<br>";
	}
	echo "<br>";
    
	// limit lines

	echo "Starting to parse data....<br><br>";
    $counter = count($log);
	echo "The tested file has ".$counter." data sets.<br>";
    
	if($header==1){
        if($counter < 2){
            echo "<span style='color:red'>Not enough rows for parsing.</span><br>";
            die();
        }
		$headerRow = trim($log[0]);
		$row = trim($log[1]);
		$delimiter = $parameters['delimiter'];
		if($delimiter=="semicolon"){
            echo "Field delimiter is semicolon (;)<br>";
			$fields = explode(";",$row);
			$columns = explode(";",$headerRow);
		}
		if($delimiter=="comma"){
             echo "Field delimiter is comma (,)<br>";
			$fields = explode(",",$row);
			$columns = explode(",",$headerRow);
		}
		if($delimiter=="space"){
             echo "Field delimiter is space (<SPACE>)<br>";
			$fields = explode(" ",$row);
			$columns = explode(" ",$headerRow);
		}
		if($delimiter=="tab"){
             echo "Field delimiter is tab (<TAB>)<br>";
			$fields = preg_split('/\s+/', $row);
			$columns = preg_split('/\s+/', $headerRow);
		}
		if($delimiter=="colon"){
             echo "Field delimiter is colon (:)<br>";
			$fields = explode(":",$row);
			$columns = explode(":",$headerRow);
		}
		if($delimiter=="vertical"){
             echo "Field delimiter is vertical (|)<br>";
			$fields = explode("|",$row);
			$columns = explode("|",$headerRow);
		}
        echo "First dataset is: ".$row."<br>";
        $nColumns = count($columns);
        $nFields = count($fields);
        echo "The header row contains ".$nColumns." columns.<br>";
        echo "The data row contains ".$nFields." fields.<br>";
        if($nColumns != $nFields){
            echo "<span style='color:red'>Number of columns is not equal to number of fields.</span><br>";
            //die();
        }
		echo "Loaded data:<br>";
		echo "<table>";
		echo "<tr><td>Field</td><td>Field Number</td><td>Value (first data set)</td></tr>";
		for($i=0;$i<count($fields);$i++){
            if(trim($fields[$i]) == ""){
                $fields[$i] = "null";
            }
			echo "<tr><td style='font-weight:bold'>".$columns[$i]."</td><td style='text-align:center'>".$i."</td><td style='text-align:right'>".$fields[$i]."</td></tr>";
		}
		echo "</table>";
	}
	
	if($header==0){
        if($counter < 1){
            echo "<span style='color:red'>Not enough rows for parsing.</span><br>";
		die();
        }
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
        $nFields = count($fields);
        echo "The data row contains ".$nFields." fields.<br>";
		echo "Loaded data:<br>";
		echo "<table>";
		echo "<tr><td>Field Number</td><td>Value (first data set)</td></tr>";
		for($i=0;$i<count($fields);$i++){
            if(trim($fields[$i]) == ""){
                $fields[$i] = "null";
            }
			echo "<tr><td style='font-weight:bold'>".$i."</td><td style='text-align:right'>".$fields[$i]."</td></tr>";
		}
		echo "</table>";
	}
	