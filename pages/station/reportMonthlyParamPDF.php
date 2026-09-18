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
	#	Monthly report
	#
	# 	A script which generates the monthly report for user specified month.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	
	//error_reporting(E_ALL);
	
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	include($baseURL."scripts/stats.php");
	
	if(isset($_GET['var'])){
		$var = $_GET['var'];
	}
	else{
		die("No parameter specified.");
	}
	
	if($_GET['m']<1 || $_GET['m']>12){
		echo "Invalid date";
		die();
	}
	if($_GET['y']<1900 || $_GET['y']>2100){
		echo "Invalid date";
		die();
	}
	
	if(!is_numeric($_GET['y']) || !is_numeric($_GET['m'])){
		echo "Invalid date";
		die();
	}
	
	$dateY = $_GET['y'];
	$dateM = $_GET['m'];
	
	if($displayPressUnits=="inhg"){
		$decimalsP = 2;
	}
	else{
		$decimalsP = 1;
	}
	if($displayRainUnits=="in"){
		$decimalsR = 2;
	}
	else{
		$decimalsR = 1;
	}
	
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
		if($displayRainUnits=="in"){
			$dp = 2;
		}
		else{
			$dp = 1;
		}
		$UoM = unitFormatter($displayRainUnits);
	}
	
	$numberDays = date("t",strtotime($dateY."-".$dateM."-15"));
	
	// get monthly data	
	if($var!="R"){ // rain must be treated differently - data in db is cumulative
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), stddev(".$mySQLCols[0]."), DAY(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row['DAY(DateTime)'];
			$dailyAvg[$d] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
			$dailyMax[$d] = chooseConvertor($row['max('.$mySQLCols[1].')']);
			$dailyMaxRaw[$d] = ($row['max('.$mySQLCols[1].')']);
			$dailyMin[$d] = chooseConvertor($row['min('.$mySQLCols[2].')']);
			$dailyMinRaw[$d] = ($row['min('.$mySQLCols[2].')']);
			$dailyRange[$d] = (chooseConvertor($row['max('.$mySQLCols[1].')'])-chooseConvertor($row['min('.$mySQLCols[2].')']));
			if($var=="T" || $var=="A" || $var=="D"){
				$dailyStddev[$d] = convertTStddev($row['stddev('.$mySQLCols[0].')']);
			}
			else{
				$dailyStddev[$d] = chooseConvertor($row['stddev('.$mySQLCols[0].')']);
			}
		}
		
		$monthAvg = avg($dailyAvg);
		$monthMax = max($dailyMax);
		$monthMin = min($dailyMin);
		$result = mysqli_query($con,"
			SELECT max(".$mySQLCols[1]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY." AND ".$mySQLCols[1]."=".max($dailyMaxRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthMaxDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		$result = mysqli_query($con,"
			SELECT min(".$mySQLCols[2]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY." AND ".$mySQLCols[2]."=".min($dailyMinRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$monthMinDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		
		// long-term
		$result = mysqli_query($con,"
			SELECT avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2]."), DAY(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM."
			GROUP BY DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row["DAY(DateTime)"];
			$overallAvg[$d] = chooseConvertor($row["avg(".$mySQLCols[0].")"]);
			$overallMax[$d] = chooseConvertor($row["max(".$mySQLCols[1].")"]);
			$overallMaxRaw[] = ($row["max(".$mySQLCols[1].")"]);
			$overallMin[$d] = chooseConvertor($row["min(".$mySQLCols[2].")"]);
			$overallMinRaw[$d] = ($row["min(".$mySQLCols[2].")"]);
		}
		// calculate deviations
		for($i=1;$i<32;$i++){
			if(array_key_exists($i,$dailyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($dailyAvg[$i],$overallAvg[$i],$deviationsDecimals);
			}
		}
		
		$absoluteAvg = avg($overallAvg);
		$absoluteMax = max($overallMax);
		$absoluteMin = min($overallMin);
		$result = mysqli_query($con,"
			SELECT max(".$mySQLCols[1]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND ".$mySQLCols[1]."=".max($overallMaxRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$absoluteMaxDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
		$result = mysqli_query($con,"
			SELECT min(".$mySQLCols[2]."), DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND ".$mySQLCols[2]."=".min($overallMinRaw)."
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$absoluteMinDate[] = date($dateTimeFormat,strtotime($row['DateTime']));
		}
	}
	if($var=="R"){
		$result = mysqli_query($con,"
			SELECT max(R), DAY(DateTime),DateTime
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row['DAY(DateTime)'];
			$dateTemporary = date($dateFormat,strtotime($row['DateTime']));
			$dailyAvg[$d] = convertR($row['max(R)']);
			$dailyAvgRaw[$dateTemporary] = convertR($row['max(R)']);
		}
		// long-term
		$result = mysqli_query($con,"
			SELECT max(R), DAY(DateTime), YEAR(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM."
			GROUP BY DAY(DateTime), YEAR(DateTime)
			ORDER BY DateTime
			"
		);
		while($row = mysqli_fetch_array($result)){
			$d = $row["DAY(DateTime)"];
			$overallAvg[$d][] = convertR($row['max(R)']);
			$overallYData[$row['YEAR(DateTime)']][] = convertR($row['max(R)']);
			$overalAll[] = convertR($row['max(R)']);
		}	
		
		$monthTotal = array_sum($dailyAvg);
		$monthMax = max($dailyAvg);
		$monthMin = min($dailyAvg);
		$monthMaxDate = array_keys($dailyAvgRaw,max($dailyAvg));
		$monthMinDate = array_keys($dailyAvgRaw,min($dailyAvg));
		
		foreach($overallYData as $temporaryYear => $annualData){
			$annualRains[$temporaryYear] = array_sum($annualData);
		}
		$absoluteAvg = avg($annualRains);
		$absoluteMax = max($annualRains);
		$absoluteMin = min($annualRains);
		$absoluteMaxDate = array_keys($annualRains,max($annualRains));
		$absoluteMinDate = array_keys($annualRains,min($annualRains));
		
		// calculate deviations
		for($i=1;$i<32;$i++){
			if(array_key_exists($i,$dailyAvg)){ // if this exists then in all data must also exist
				$deviationsAvg[$i] = deviation($dailyAvg[$i],avg($overallAvg[$i]),$decimalsR);
			}
		}
	}
	
	if($var=="W" || $var=="G"){
		$result = mysqli_query($con,"
			SELECT B, DAY(DateTime)
			FROM alldata 
			WHERE MONTH(DateTime)=".$dateM." AND YEAR(DateTime)=".$dateY."
			"
		);
		while($row = mysqli_fetch_array($result)){
			$bearings[] = $row['B'];
			$h = $row['DAY(DateTime)'];
			${"bearings".$h}[] = $row['B'];
		}
		for($i=1;$i<32;$i++){
			if(isset(${"bearings".$i})){
				${"bearings".$i."avg"} = avgWind(${"bearings".$i});
			}
		}
	}
	
	// enable interval graphs
	if($var=="T" || $var=="A" || $var=="D" || $var=="H" || $var=="P" || $var=="S"){
		$intervalGraphs = true;
	}
	else{ // no point for wind, precipitation etc. where minimum is almost always zero
		$intervalGraphs = false;
	}
    if($var!="R"){
        $headingWidth = '10.5%';
    }
    else{
        $headingWidth = "18.5%";
    }
	
	function avg($arr){
		if(count($arr)>0){
			return array_sum($arr)/count($arr);
		}
		else{
			return "";
		}
	}
	function deviation($n1,$n2,$dp){
		$deviation = $n1 - $n2;
		$deviation = number_format($deviation,$dp,".","");
		if($deviation>0){
			return "+".$deviation;
		}
		else{
			return $deviation;
		}
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
	}

	// PDF
    include($baseURL."scripts/mpdf60/mpdf.php");
    if($defaultPaperSize=="letter"){
        $mpdf = new mPDF('','Letter');
    }
    else{
        $mpdf = new mPDF();
    }
    $mpdf->SetTitle(lang("monthly report",'w'));
    $mpdf->SetAuthor("Meteotemplate");
    $mpdf->SetCreator("Meteotemplate");

    $mpdf->setFooter('<span style="color:black;font-style:normal;font-size:0.9em">'.$pageURL.$path.'</span>||<span style="color:black;font-style:normal">Meteotemplate</span>');

	$mpdf->WriteHTML('
		<style>
			.cellHeading{
				text-align: center!important;
				background: #'.$color_schemes[$design2]['900'].';
				color:white;
				padding-bottom: 5px;
                vertical-align: top;
                width: '.$headingWidth.';
                font-weight: bold;
			}
			.cellFirst{
				width: 60%;
				padding-left:5px;
				text-align:left;
				font-size: 8pt;
			}
			.cellSecond{
				width: 25%;
			}
			.headingImg{
				width: 30px;
				padding: 5px;
			}
			#summaryTable td{
				text-align:center;
			}
			#summaryTable tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['200'].';
			}
			#summaryTable tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
			.unitCell{
				
				text-align: center;
			}
			.table{
				width: 100%;
			}
			.table tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['200'].';
			}
			.table tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
			.value{
				text-align:right;
				padding-right:2px;
			}
			#tableHourly td{
				text-align:center;
				font-size:8pt;
			}
		</style>
	');
	$mpdf->WriteHTML('<body style="font-family:Helvetica">');

    $mpdf->WriteHTML('
        <table style="width:100%" cellspacing="0">
			<tr>
                <td style="text-align:center;background: #'.$color_schemes[$design2]['900'].';color:white">
                    <h1 style="font-size:1.5em;color:white">'.lang("monthly report",'w').' - '.$heading.' ('.$UoM.')</h1>
					<h2 style="font-size:1.2em;color:white">'.lang("month".($dateM*1),"c")." ".$dateY.'</h2>
                </td>
            </tr>
		</table>
		<br>
	');
	
    if($var!="R"){
        $mpdf->WriteHTML('
            <table style="width:100%;margin:0 auto" class="table" cellspacing="0">
                <tr>
                    <td class="cellHeading">
                        '.lang("average",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("maximum",'c').'<br>
                    </td>
                    <td class="cellHeading">
                        '.lang("minimum",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("average",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("deviation",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("maximum",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("minimum",'c').'
                    </td>
                </tr>
                <tr>
                    <td style="text-align:center">
                        <strong>'.number_format($monthAvg,($dp+1),".","").'</strong>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($monthMax,$dp,".","").'</strong>
                        <div style="font-size:8pt">
        ');
                                if(count($monthMaxDate)<=5){
                                    for($i=0;$i<count($monthMaxDate);$i++){
                                        $mpdf->WriteHTML($monthMaxDate[$i]."<br>");
                                    }
                                }
                                else{
                                    $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($monthMin,$dp,".","").'</strong>
                        <div style="font-size:8pt">
        ');               
                                if(count($monthMinDate)<=5){
                                    for($i=0;$i<count($monthMinDate);$i++){
                                         $mpdf->WriteHTML($monthMinDate[$i]."<br>");
                                    }
                                }
                                else{
                                     $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
         $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($absoluteAvg,($dp+1),".","").'</strong>
                    </td>
                    <td style="text-align:center">
                        <strong>'.deviation($monthAvg,$absoluteAvg,($dp+1)).'</strong>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($absoluteMax,($dp),".","").'</strong>
                        <div style="font-size:8pt">
        ');
                                if(count($absoluteMaxDate)<=5){
                                    for($i=0;$i<count($absoluteMaxDate);$i++){
                                        $mpdf->WriteHTML($absoluteMaxDate[$i]."<br>");
                                    }
                                }
                                else{
                                     $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($absoluteMin,($dp),".","").'</strong>
                        <div style="font-size:8pt">
       ');
                                if(count($absoluteMinDate)<=5){
                                    for($i=0;$i<count($absoluteMinDate);$i++){
                                         $mpdf->WriteHTML($absoluteMinDate[$i]."<br>");
                                    }
                                }
                                else{
                                    $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                </tr>
            </table>
        ');

        $mpdf->WriteHTML("<br>");

        $mpdf->WriteHTML('
            <table id="tableHourly" cellspacing="0" style="font-size:8pt">
				<thead>
					<tr>
						<th class="cellHeading">
							'.lang('day','c').'
						</th>
						<th class="cellHeading">
							'.lang('avgAbbr','c').'
						</th>
						<th class="cellHeading">
							'.lang('maximumAbbr','c').'
						</th>
						<th class="cellHeading">
							'.lang('minimumAbbr','c').'
						</th>
						<th class="cellHeading">
							'.lang('range','c').'
						</th>
						<th class="cellHeading">
							'.lang("sdAbbr",'u').'
						</th>
						<th class="cellHeading">
							'.lang("station average",'c').'
						</th>
						<th class="cellHeading">
							'.lang("deviation",'c').'
						</th>
						<th class="cellHeading">
							'.lang("absolute",'c')." ".lang('maximum','l').'
						</th>
						<th class="cellHeading">
							'.lang("absolute",'c')." ".lang('minimum','l').'
						</th>
					</tr>
				</thead>
				<tbody>
			');
				for($i=1;$i<=$numberDays;$i++){		
					$mpdf->WriteHTML('
						<tr>
							<td style="width: 50px;text-align:center;background: #'.$color_schemes[$design2]['900'].';color:white">
								'.$i.'
							</td>
					');
						if(array_key_exists($i,$dailyAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($dailyAvg[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max'])).';padding:5px">
									'.round($dailyAvg[$i],2).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$dailyMax)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($dailyMax[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round($dailyMax[$i],1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$dailyMin)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($dailyMin[$i],array((min($dailyMin)-0.001),(max($dailyMax)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round($dailyMin[$i],1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$dailyRange)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:black;background:'.fill($dailyRange[$i],array((min($dailyRange)-0.001),(max($dailyRange)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400'])).'">
									'.round($dailyRange[$i],1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$dailyStddev)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:black;background:'.fill($dailyStddev[$i],array((min($dailyStddev)-0.001),(max($dailyStddev)+0.001)),array("#ffffff", "#".$color_schemes[$design2]['400'])).'">
									'.round($dailyStddev[$i],2).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$overallAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($overallAvg[$i],array((min($overallAvg)-0.001),(max($overallAvg)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round($overallAvg[$i],2).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$deviationsAvg)){
							if($deviationsAvg[$i]<0){
								$colorTemporary = fill($deviationsAvg[$i],array((min($deviationsAvg)-0.1),0),array($colors['min'], "#999999"));
							}
							if($deviationsAvg[$i]>0){
								$colorTemporary = fill($deviationsAvg[$i],array(0,(max($deviationsAvg)+0.1)),array("#999999",$colors['max']));
							}
							if($deviationsAvg[$i]==0){
								$colorTemporary = "#999999";
							}
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.$colorTemporary.'">
									'.$deviationsAvg[$i].'
								</td>
							');
						} 
						else{
							$mpdf->WriteHTML('"<td></td>"');
						}
						if(array_key_exists($i,$overallMax)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($overallMax[$i],array((min($overallMax)-0.001),(max($overallMax)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round($overallMax[$i],2).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$overallMin)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($overallMin[$i],array((min($overallMin)-0.001),(max($overallMin)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round($overallMin[$i],2).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
					$mpdf->WriteHTML('
						</tr>
					');
				}
			$mpdf->WriteHTML('
				</tbody>
			</table>
        ');
    } // end is not rain
	else{ // is rain
		 $mpdf->WriteHTML('
            <table style="width:100%;margin:0 auto" class="table" cellspacing="0">
                <tr>
                    <td class="cellHeading">
                        '.lang("total",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("maximum daily total",'c').'<br>
                    </td>
                    <td class="cellHeading">
                        '.lang("minimum daily total",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("average",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("deviation",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("maximum",'c').'
                    </td>
                    <td class="cellHeading">
                        '.lang("alltime",'c').'<br>'.lang("minimum",'c').'
                    </td>
                </tr>
				<tr>
                    <td style="text-align:center">
                        <strong>'.number_format($monthTotal,($dp+1),".","").'</strong>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($monthMax,$dp,".","").'</strong>
                        <div">
        ');
                                if(count($monthMaxDate)<=5){
                                    for($i=0;$i<count($monthMaxDate);$i++){
                                        $mpdf->WriteHTML($monthMaxDate[$i]."<br>");
                                    }
                                }
                                else{
                                    $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center">
                        <strong>'.number_format($monthMin,$dp,".","").'</strong>
                        <div">
        ');               
                                if(count($monthMinDate)<=5){
                                    for($i=0;$i<count($monthMinDate);$i++){
                                         $mpdf->WriteHTML($monthMinDate[$i]."<br>");
                                    }
                                }
                                else{
                                     $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
         $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <strong>'.number_format($absoluteAvg,($dp+1),".","").'</strong>
                    </td>
                    <td style="text-align:center;">
                        <strong>'.deviation($monthTotal,$absoluteAvg,($dp+1)).'</strong>
                    </td>
                    <td style="text-align:center;">
                        <strong>'.number_format($absoluteMax,($dp),".","").'</strong>
                        <div>
        ');
                                if(count($absoluteMaxDate)<=5){
                                    for($i=0;$i<count($absoluteMaxDate);$i++){
                                        $mpdf->WriteHTML($absoluteMaxDate[$i]."<br>");
                                    }
                                }
                                else{
                                     $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <strong>'.number_format($absoluteMin,($dp),".","").'</strong>
                        <div>
       ');
                                if(count($absoluteMinDate)<=5){
                                    for($i=0;$i<count($absoluteMinDate);$i++){
                                         $mpdf->WriteHTML($absoluteMinDate[$i]."<br>");
                                    }
                                }
                                else{
                                    $mpdf->WriteHTML(lang("more than 5 instances",'c'));
                                }
        $mpdf->WriteHTML('
                        </div>
                    </td>
                </tr>
            </table>
        ');

		 $mpdf->WriteHTML("<br>");

		 $mpdf->WriteHTML('
            <table id="tableHourly" cellspacing="0" style="font-size:8pt">
				<thead>
					<tr>
						<th class="cellHeading">
							'.lang('day','c').'
						</th>
						<th class="cellHeading">
							'.lang('total','c').'
						</th>
						<th class="cellHeading">
							'.lang("station average",'c').'
						</th>
						<th class="cellHeading">
							'.lang("deviation",'c').'
						</th>
						<th class="cellHeading">
							'.lang("absolute",'c')." ".lang('maximum','l').'
						</th>
						<th class="cellHeading">
							'.lang("absolute",'c')." ".lang('minimum','l').'
						</th>
					</tr>
				</thead>
				<tbody>
			');
				for($i=1;$i<=$numberDays;$i++){		
					$mpdf->WriteHTML('
						<tr>
							<td style="width: 50px;text-align:center;background: #'.$color_schemes[$design2]['900'].';color:white">
								'.$i.'
							</td>
					');
						if(array_key_exists($i,$dailyAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill($dailyAvg[$i],array((min($dailyAvg)-0.001),(max($dailyAvg)+0.001)),array($colors['min'], $colors['max'])).';padding:5px">
									'.round($dailyAvg[$i],$decimalsR+1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$overallAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill(avg($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round(avg($overallAvg[$i]),$decimalsR+1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$deviationsAvg)){
							if($deviationsAvg[$i]<0){
								$colorTemporary = fill($deviationsAvg[$i],array((min($deviationsAvg)-0.1),0),array($colors['min'], "#999999"));
							}
							if($deviationsAvg[$i]>0){
								$colorTemporary = fill($deviationsAvg[$i],array(0,(max($deviationsAvg)+0.1)),array("#999999",$colors['max']));
							}
							if($deviationsAvg[$i]==0){
								$colorTemporary = "#999999";
							}
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.$colorTemporary.'">
									'.$deviationsAvg[$i].'
								</td>
							');
						} 
						else{
							$mpdf->WriteHTML('"<td></td>"');
						}
						if(array_key_exists($i,$overallAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill(max($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round(max($overallAvg[$i]),$decimalsR+1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
						if(array_key_exists($i,$overallAvg)){
							$mpdf->WriteHTML('
								<td style="font-weight:bold;color:white;background:'.fill(min($overallAvg[$i]),array((min($overalAll)-0.001),(max($overalAll)+0.001)),array($colors['min'], $colors['max'])).'">
									'.round(min($overallAvg[$i]),$decimalsR+1).'
								</td>
							');
						}
						else{
							$mpdf->WriteHTML('"<td></td>"');
						} 
					$mpdf->WriteHTML('
						</tr>
					');
				}
			$mpdf->WriteHTML('
				</tbody>
			</table>
        ');

	}

	$mpdf->Output('monthlyReport_'.$dateY."-".$dateM.'_'.$var.'.pdf', 'I');
    exit;
	?>
