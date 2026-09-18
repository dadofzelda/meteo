<?php 

    include("../../../config.php");
	include("../../../css/design.php");
	include("../../../scripts/functions.php");

    if(file_exists("settings.php")){
		include("settings.php");
	}
	else{
		echo "<span style='color:#".$graphColor."'>Please go to your admin section and go through the settings for this block first.</span>";
		die();
	}

    $language = loadLangs();

    $graphsToInclude = trim($graphsToInclude);
    $graphsToInclude = str_replace(" ", "", $graphsToInclude);
    $graphsToInclude = explode(",", $graphsToInclude);

    $graphSpan = trim($graphSpan);

    if($graphSpan == "today"){
        $span = "WHERE DATE(DateTime) = CURDATE()";
    }
    if($graphSpan == "24h"){
        $span = "WHERE DateTime >= now() - interval 24 hour";
    }
    if($graphSpan == "24h"){
        $span = "WHERE DateTime >= now() - interval 24 hour";
    }
    if($graphSpan == "7d"){
        $span = "WHERE DateTime >= now() - interval 7 day";
    }
    if($graphSpan == "30d"){
        $span = "WHERE DateTime >= now() - interval 30 day";
    }

    $query = "
        SELECT  DateTime, " . implode(",", $graphsToInclude) ."
        FROM  alldata
        ". $span
    ;

    $result = mysqli_query($con, $query);
    $Rcumul = 0;
    $Rprevious = 0;
    while ($row = mysqli_fetch_array($result)) {
        $rawData['Dates'][] = strtotime($row['DateTime'])*1000;
        if(in_array("T", $graphsToInclude)){
            if($row['T'] === "" || $row['T'] === null){
                $rawData['T'][] = null;
            }
            else{
                $rawData['T'][] = convertT($row['T']);
            }
        }
        if(in_array("H", $graphsToInclude)){
            if($row['H'] === "" || $row['H'] === null){
                $rawData['H'][] = null;
            }
            else{
                $rawData['H'][] = ($row['H']);
            }
        }
        if(in_array("P", $graphsToInclude)){
            if($row['P'] === "" || $row['P'] === null){
                $rawData['P'][] = null;
            }
            else{
                $rawData['P'][] = ($row['P']);
            }
        }
        if(in_array("W", $graphsToInclude)){
            if($row['W'] === "" || $row['W'] === null){
                $rawData['W'][] = null;
            }
            else{
                $rawData['W'][] = convertW($row['W']);
            }
        }
        if(in_array("G", $graphsToInclude)){
            if($row['G'] === "" || $row['G'] === null){
                $rawData['G'][] = null;
            }
            else{
                $rawData['G'][] = convertW($row['G']);
            }
        }
        if(in_array("S", $graphsToInclude)){
            if($row['S'] === "" || $row['S'] === null){
                $rawData['S'][] = null;
            }
            else{
                $rawData['S'][] = ($row['S']);
            }
        }
        if(in_array("A", $graphsToInclude)){
            if($row['A'] === "" || $row['A'] === null){
                $rawData['A'][] = null;
            }
            else{
                $rawData['A'][] = convertT($row['A']);
            }
        }
        if(in_array("D", $graphsToInclude)){
            if($row['D'] === "" || $row['D'] === null){
                $rawData['D'][] = null;
            }
            else{
                $rawData['D'][] = convertT($row['D']);
            }
        }
        if(in_array("R", $graphsToInclude)){
            if($row['R'] === "" || $row['R'] === null){
                $rawData['R'][] = $Rcumul;
            }
            else{
                $rawR = convertR($row['R']);
                $thisR = $rawR - $Rprevious;
                $Rprevious = $rawR;
                if($thisR > 0){
                    $Rcumul += $thisR;
                }
                $rawData['R'][] = $Rcumul;
            }
        }
    }

    $data['xData'] = $rawData['Dates'];
    foreach($graphsToInclude as $parameter){
        if($parameter == "T"){
            $data['datasets'][] = array("name" => lang('temperature','c'), "data" => $rawData['T'], "unit" => unitFormatter($displayTempUnits), "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "H"){
            $data['datasets'][] = array("name" => lang('humidity','c'), "data" => $rawData['H'], "unit" => "%", "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "P"){
            $data['datasets'][] = array("name" => lang('pressure','c'), "data" => $rawData['P'], "unit" => unitFormatter($displayPressUnits), "type" => "line", "valueDecimals" => 2);
        }
        if($parameter == "W"){
            $data['datasets'][] = array("name" => lang('wind speed','c'), "data" => $rawData['W'], "unit" => unitFormatter($displayWindUnits), "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "G"){
            $data['datasets'][] = array("name" => lang('wind gust','c'), "data" => $rawData['G'], "unit" => unitFormatter($displayWindUnits), "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "R"){
            $data['datasets'][] = array("name" => lang('precipitation','c'), "data" => $rawData['R'], "unit" => unitFormatter($displayRainUnits), "type" => "areaspline", "valueDecimals" => 2);
        }
        if($parameter == "A"){
            $data['datasets'][] = array("name" => lang('apparent temperature','c'), "data" => $rawData['A'], "unit" => unitFormatter($displayTempUnits), "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "D"){
            $data['datasets'][] = array("name" => lang('dew point','c'), "data" => $rawData['D'], "unit" => unitFormatter($displayTempUnits), "type" => "line", "valueDecimals" => 1);
        }
        if($parameter == "S"){
            $data['datasets'][] = array("name" => lang('solar radiation','c'), "data" => $rawData['S'], "unit" => "W/m2", "type" => "line", "valueDecimals" => 1);
        }
    }
    

    echo json_encode($data, JSON_NUMERIC_CHECK);
?>


