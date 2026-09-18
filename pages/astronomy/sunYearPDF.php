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
	#	Annual Daylength graph
	#
	# 	Script to draw annual day length graph.
	#
	#############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################

	include("../../config.php");
	include($baseURL."header.php");
	include($baseURL."css/design.php");
	$lat = $stationLat;
	$lon = $stationLon;
	
	if(isset($_GET['y'])){
        $y = $_GET['y']; 
    }
    else{
        $y = date("Y");
    }

    // PDF
    include($baseURL."scripts/mpdf60/mpdf.php");
    if($defaultPaperSize=="letter"){
        $mpdf = new mPDF('','Letter');
    }
    else{
        $mpdf = new mPDF();
    }
    $mpdf->SetTitle(lang("sunrise",'w')." / ".lang('sunset','c'));
    $mpdf->SetAuthor("Meteotemplate");
    $mpdf->SetCreator("Meteotemplate");
    $mpdf->SetHeader('
        <table style="width:100%" cellspacing="0">
			<tr>
                <td style="text-align:center;color:#'.$color_schemes[$design2]['900'].'">
                     <h1 style="font-size:1.5em">'.lang("sunrise",'w')." / ".lang('sunset','c').'</h1>
					<h2 style="font-size:1.2em">'.$y.'</h2>
                </td>
            </tr>
		</table>
    ');
    $mpdf->setFooter('<span style="color:black;font-style:normal;font-size:0.9em">'.$pageURL.$path.'</span>||<span style="color:black;font-style:normal">Meteotemplate</span>');

    $mpdf->WriteHTML('
		<style>
			.table{
				width: 100%;
                font-size:9pt;
			}
            .table td{
                padding: 5px;
                text-align:center;
            }
			.table tr:nth-child(even) {
				background: #'.$color_schemes[$design2]['200'].';
			}
			.table tr:nth-child(odd) {
				background: #'.$color_schemes[$design2]['100'].';
			}
		</style>
	');

    $mpdf->WriteHTML('
        <br><br>
        <table class="table" cellspacing="0">
            <thead>
                <tr>
                    <th colspan=13 style="text-align:center;background: #'.$color_schemes[$design2]['800'].';color:white">'.lang('month','c').'</th>
                </tr>
                <tr>
                    <th></th>
    ');
                    for($i=1;$i<7;$i++){
                        $mpdf->WriteHTML('<th style="text-align:center;font-size:8pt;font-weight:bold">'.lang("month".($i*1),"c").'</th>');
                    }			
    $mpdf->WriteHTML('
                </tr>
            </thead>
            <tbody>
    ');
                $rises = array();
                $sets = array();
                
                for($i=1;$i<32;$i++){
                    $mpdf->WriteHTML('<tr><td>'.$i.'</td>');
                    for($a=1;$a<7;$a++){
                        $currentDate = date("U", strtotime($y."-".$a."-".$i));
                        $date_check = checkdate($a, $i, $y);
                        if($date_check==true){
                            $dateTimeZone = new DateTimeZone($stationTZ);
                            $dateTime = new DateTime("now", $dateTimeZone);
                            $transition = $dateTimeZone->getTransitions(mktime(0, 0, 0, $a, $i, $y),mktime(23, 59, 0, $a, $i, $y)); 
                            $current_offset=($transition[0]['offset'])/3600; 
                            $rise = date_sunrise($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
                            $set = date_sunset($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
                            $rise = date($timeFormat,$rise);
                            $set = date($timeFormat,$set);
                            array_push($rises,$rise);
                            array_push($sets,$set);
                            $mpdf->WriteHTML('<td>'.$rise.' / '.$set.'</td>');
                        }
                        else{
                            $mpdf->WriteHTML('<td></td>');
                        }
                    }
                    $mpdf->WriteHTML('</tr>');
                }
    $mpdf->WriteHTML("</tbody></table>");

    $mpdf->WriteHTML("<pagebreak>");

    $mpdf->WriteHTML('
        <br><br>
        <table class="table" cellspacing="0">
            <thead>
                <tr>
                    <th colspan=13 style="text-align:center;background: #'.$color_schemes[$design2]['800'].';color:white">'.lang('month','c').'</th>
                </tr>
                <tr>
                    <th></th>
    ');
                    for($i=7;$i<13;$i++){
                        $mpdf->WriteHTML('<th style="text-align:center;font-size:8pt;font-weight:bold">'.lang("month".($i*1),"c").'</th>');
                    }			
    $mpdf->WriteHTML('
                </tr>
            </thead>
            <tbody>
    ');
                $rises = array();
                $sets = array();
                
                for($i=1;$i<32;$i++){
                    $mpdf->WriteHTML('<tr><td>'.$i.'</td>');
                    for($a=7;$a<13;$a++){
                        $currentDate = date("U", strtotime($y."-".$a."-".$i));
                        $date_check = checkdate($a, $i, $y);
                        if($date_check==true){
                            $dateTimeZone = new DateTimeZone($stationTZ);
                            $dateTime = new DateTime("now", $dateTimeZone);
                            $transition = $dateTimeZone->getTransitions(mktime(0, 0, 0, $a, $i, $y),mktime(23, 59, 0, $a, $i, $y)); 
                            $current_offset=($transition[0]['offset'])/3600; 
                            $rise = date_sunrise($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
                            $set = date_sunset($currentDate,SUNFUNCS_RET_TIMESTAMP,$lat,$lon,90.5,$current_offset);
                            $rise = date($timeFormat,$rise);
                            $set = date($timeFormat,$set);
                            array_push($rises,$rise);
                            array_push($sets,$set);
                            $mpdf->WriteHTML('<td>'.$rise.' / '.$set.'</td>');
                        }
                        else{
                            $mpdf->WriteHTML('<td></td>');
                        }
                    }
                    $mpdf->WriteHTML('</tr>');
                }
    $mpdf->WriteHTML("</tbody></table>");

    $mpdf->Output('sunTimes.pdf', 'I');
    exit;
	?>

	