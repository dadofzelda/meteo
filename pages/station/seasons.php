<?php

    include("../../config.php");
    include($baseURL."css/design.php");
    include($baseURL."header.php");

    if($stationLat>=0){
        $seasonNames = array("spring","summer","fall","winter");
        $seasonIcons = array("mticon-spring","mticon-summer","mticon-autumn","mticon-snow");
    }
    else{
        $seasonNames = array("fall","winter","spring","summer");
        $seasonIcons = array("mticon-autumn","mticon-snow","mticon-spring","mticon-summer");
    }

    if(isset($_GET['var'])){
		$var = trim($_GET['var']);
	}
	else{
		$var = "T";
	}

	if($var=="T"){
		$heading = lang("temperature",'c');
		$mySQLCols = array("T","Tmax","Tmin");
		$dp = 1;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="H"){
		$heading = lang("humidity",'c');
		$mySQLCols = array("H","H","H");
		$dp = 1;
		$UoM = "%";
	}
	if($var=="P"){
		$heading = lang("pressure",'c');
		$mySQLCols = array("P","P","P");
		$dp = $decimalsP;
		$UoM = unitFormatter($displayPressUnits);
	}
	if($var=="W"){
		$heading = lang("wind speed",'c');
		$mySQLCols = array("W","W","W");
		$dp = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="G"){
		$heading = lang("wind gust",'c');
		$mySQLCols = array("G","G","G");
		$dp = 2;
		$UoM = unitFormatter($displayWindUnits);
	}
	if($var=="A"){
		$heading = lang("apparent temperature",'c');
		$mySQLCols = array("A","A","A");
		$dp = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="D"){
		$heading = lang("dewpoint",'c');
		$mySQLCols = array("D","D","D");
		$dp = 2;
		$UoM = unitFormatter($displayTempUnits);
	}
	if($var=="S"){
		$heading = lang("solar radiation",'c');
		$mySQLCols = array("S","S","S");
		$dp = 1;
		$UoM = "W/m2";
	}

    // calculations
    $result = mysqli_query($con,"
        SELECT DateTime, avg(".$mySQLCols[0]."), max(".$mySQLCols[1]."), min(".$mySQLCols[2].")
        FROM alldata
        GROUP BY YEAR(DateTime), MONTH(DateTime), DAY(DateTime)
        ORDER BY DateTime
    "
    );
    while($row = mysqli_fetch_array($result)){
        $currentDate = strtotime($row['DateTime']);
        $y = date("Y",$currentDate);
        $m = date("n",$currentDate);
        $d = date("j",$currentDate);
        if($stationLat>=0){
            if($m=="3"){
                $period = "spring";
                $periodMonth = 1;
            }
            if($m=="4"){
                $period = "spring";
                $periodMonth = 2;
            }
            if($m=="5"){
                $period = "spring";
                $periodMonth = 3;
            }
            if($m=="6"){
                $period = "summer";
                $periodMonth = 1;
            }
            if($m=="7"){
                $period = "summer";
                $periodMonth = 2;
            }
            if($m=="8"){
                $period = "summer";
                $periodMonth = 3;
            }
            if($m=="9"){
                $period = "fall";
                $periodMonth = 1;
            }
            if($m=="10"){
                $period = "fall";
                $periodMonth = 2;
            }
            if($m=="11"){
                $period = "fall";
                $periodMonth = 3;
            }
            if($m=="12"){
                $period = "winter";
                $periodMonth = 1;
            }
            if($m=="1"){
                $period = "winter";
                $periodMonth = 2;
                $y = $y-1; // here subtract one, the winter is spanning from last year
            }
            if($m=="2"){
                $period = "winter";
                $periodMonth = 3;
                $y = $y-1; // here subtract one, the winter is spanning from last year
            }
        }
        else{
            if($m=="3"){
                $period = "fall";
                $periodMonth = 1;
            }
            if($m=="4"){
                $period = "fall";
                $periodMonth = 2;
            }
            if($m=="5"){
                $period = "fall";
                $periodMonth = 3;
            }
            if($m=="6"){
                $period = "winter";
                $periodMonth = 1;
            }
            if($m=="7"){
                $period = "winter";
                $periodMonth = 2;
            }
            if($m=="8"){
                $period = "winter";
                $periodMonth = 3;
            }
            if($m=="9"){
                $period = "spring";
                $periodMonth = 1;
            }
            if($m=="10"){
                $period = "spring";
                $periodMonth = 2;
            }
            if($m=="11"){
                $period = "spring";
                $periodMonth = 3;
            }
            if($m=="12"){
                $period = "summer";
                $periodMonth = 1;
            }
            if($m=="1"){
                $period = "summer";
                $periodMonth = 2;
                $y = $y-1; // here subtract one, the summer is spanning from last year
            }
            if($m=="2"){
                $period = "summer";
                $periodMonth = 3;
                $y = $y-1; // here subtract one, the summer is spanning from last year
            }
        }
        $data['avg'][$period][$y][$m][$d] = chooseConvertor($row['avg('.$mySQLCols[0].')']);
        $data['max'][$period][$y][$m][$d] = chooseConvertor($row['max('.$mySQLCols[1].')']);
        $data['min'][$period][$y][$m][$d] = chooseConvertor($row['min('.$mySQLCols[2].')']);
    }

    // annual averages/maximums/minimums
    foreach($seasonNames as $seasonName){
        // season average
        foreach($data['avg'][$seasonName] as $year=>$values){
            $temporaryArr = array();
            foreach($values as $values2){
                foreach($values2 as $values3){
                    $temporaryArr[] = $values3;
                    if($var=="T"){
                        if($displayTempUnits=="C"){
                            if($values3<=-10){
                                $dayDistribution['avg'][$seasonName][$year]['<-10'] = $dayDistribution['avg'][$seasonName][$year]['<-10'] + 1;
                            }
                            if($values3<=0 && $values>-10){
                                $dayDistribution['avg'][$seasonName][$year]['0--10'] = $dayDistribution['avg'][$seasonName][$year]['0--10'] + 1;
                            }
                            if($values3>0 && $values3<=10){
                                $dayDistribution['avg'][$seasonName][$year]['0-10'] = $dayDistribution['avg'][$seasonName][$year]['0-10'] + 1;
                            }
                            if($values3>10 && $values3<=20){
                                $dayDistribution['avg'][$seasonName][$year]['10-20'] = $dayDistribution['avg'][$seasonName][$year]['10-20'] + 1;
                            }
                            if($values3>20 && $values3<=25){
                                $dayDistribution['avg'][$seasonName][$year]['20-25'] = $dayDistribution['avg'][$seasonName][$year]['20-25'] + 1;
                            }
                            if($values3>25 && $values3<=30){
                                $dayDistribution['avg'][$seasonName][$year]['25-30'] = $dayDistribution['avg'][$seasonName][$year]['25-30'] + 1;
                            }
                            if($values3>30 && $values3<=35){
                                $dayDistribution['avg'][$seasonName][$year]['30-35'] = $dayDistribution['avg'][$seasonName][$year]['30-35'] + 1;
                            }
                            if($values3>35){
                                $dayDistribution['avg'][$seasonName][$year]['>35'] = $dayDistribution['avg'][$seasonName][$year]['>35'] + 1;
                            }
                        }
                    }
                }
            }
            $annual['avg'][$seasonName][$year] = average($temporaryArr);
        }
        // season absolute maximum + date
        foreach($data['max'][$seasonName] as $year=>$values){
            $temporaryArr = array();
            foreach($values as $month=>$values2){
                foreach($values2 as $day=>$values3){
                    $temporaryArr[date($dateFormat,strtotime($year."-".$month."-".$day))] = $values3;
                }
            }
            $annual['max'][$seasonName][$year] = max($temporaryArr);
            $annual['avgMax'][$seasonName][$year] = average($temporaryArr);
            $annual['maxDate'][$seasonName][$year] = array_keys($temporaryArr,max($temporaryArr));
        }
        // season absolute minimum + date
        foreach($data['min'][$seasonName] as $year=>$values){
            $temporaryArr = array();
            foreach($values as $month=>$values2){
                foreach($values2 as $day=>$values3){
                    $temporaryArr[date($dateFormat,strtotime($year."-".$month."-".$day))] = $values3;
                }
            }
            $annual['min'][$seasonName][$year] = min($temporaryArr);
            $annual['avgMin'][$seasonName][$year] = average($temporaryArr);
            $annual['minDate'][$seasonName][$year] = array_keys($temporaryArr,min($temporaryArr));
        }
        // seasonal maximum day
        foreach($data['avg'][$seasonName] as $year=>$values){
            $temporaryArr = array();
            foreach($values as $month=>$values2){
                foreach($values2 as $day=>$values3){
                    $temporaryArr[date($dateFormat,strtotime($year."-".$month."-".$day))] = $values3;
                }
            }
            $annual['maxDay'][$seasonName][$year] = max($temporaryArr);
            $annual['maxDayDate'][$seasonName][$year] = array_keys($temporaryArr,max($temporaryArr));
        }
        // seasonal minimum day
        foreach($data['avg'][$seasonName] as $year=>$values){
            $temporaryArr = array();
            foreach($values as $month=>$values2){
                foreach($values2 as $day=>$values3){
                    $temporaryArr[date($dateFormat,strtotime($year."-".$month."-".$day))] = $values3;
                }
            }
            $annual['minDay'][$seasonName][$year] = min($temporaryArr);
            $annual['minDayDate'][$seasonName][$year] = array_keys($temporaryArr,min($temporaryArr));
        }
    }

    // overall avg,max,min
    foreach($seasonNames as $seasonName){
        // overall average
        $overall['avg'][$seasonName] = average($annual['avg'][$seasonName]);
        $overall['avgMax'][$seasonName] = average($annual['avgMax'][$seasonName]);
        $overall['avgMin'][$seasonName] = average($annual['avgMin'][$seasonName]);
        // overall maximum
        $overall['max'][$seasonName] = max($annual['max'][$seasonName]);
        // overall maximum date
        $yearObserved = array_keys($annual['max'][$seasonName],max($annual['max'][$seasonName]));
        $overall['maxDate'][$seasonName] = array();
        foreach($yearObserved as $observed){
            $overall['maxDate'][$seasonName] = array_merge($overall['maxDate'][$seasonName],$annual['maxDate'][$seasonName][$observed]);
        }
        // overall minimum
        $overall['min'][$seasonName] = min($annual['min'][$seasonName]);
        // overall minimum date
        $yearObserved = array_keys($annual['min'][$seasonName],min($annual['min'][$seasonName]));
        $overall['minDate'][$seasonName] = array();
        foreach($yearObserved as $observed){
            $overall['minDate'][$seasonName] = array_merge($overall['minDate'][$seasonName],$annual['minDate'][$seasonName][$observed]);
        }
        // overall max day
        $overall['maxDay'][$seasonName] = max($annual['maxDay'][$seasonName]);
        // overall max day date
        $yearObserved = array_keys($annual['maxDay'][$seasonName],max($annual['maxDay'][$seasonName]));
        $overall['maxDayDate'][$seasonName] = array();
        foreach($yearObserved as $observed){
            $overall['maxDayDate'][$seasonName] = array_merge($overall['maxDayDate'][$seasonName],$annual['maxDayDate'][$seasonName][$observed]);
        }
        // overall min day
        $overall['minDay'][$seasonName] = min($annual['minDay'][$seasonName]);
        // overall min day date
        $yearObserved = array_keys($annual['minDay'][$seasonName],min($annual['minDay'][$seasonName]));
        $overall['minDayDate'][$seasonName] = array();
        foreach($yearObserved as $observed){
            $overall['minDayDate'][$seasonName] = array_merge($overall['minDayDate'][$seasonName],$annual['minDayDate'][$seasonName][$observed]);
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
		if($var=="R"){
			return convertR($value);
		}
	}
    function average($arr){
		if(count($arr)>0){
			return array_sum($arr)/count($arr);
		}
	}
    function showDates($arr){
        if(count($arr)>5){
            return lang("more than 5 instances",'l');
        }
        else{
            return implode("<br />",$arr);
        }
    }
    function sumDays($arr,$index){
        $currentSum = 0;
        foreach($arr as $y=>$values){
            $currentSum += $values[$index];
        }
        return number_format($currentSum,0,".","");
    }
?>
<h1><?php echo $heading?></h1>
    <?php
        if($stationLat>=0){
    ?>
            <span class="mticon-spring seasonOpener tooltip" data-id="spring" title="<?php echo lang('spring','c')?>"></span>
            <span class="mticon-summer seasonOpener tooltip" data-id="summer" title="<?php echo lang('summer','c')?>"></span>
            <span class="mticon-autumn seasonOpener tooltip" data-id="fall" title="<?php echo lang('autumn','c')?>"></span>
            <span class="mticon-snow seasonOpener tooltip" data-id="winter" title="<?php echo lang('winter','c')?>"></span>
    <?php
        }
        else{
    ?>
            <span class="mticon-autumn seasonOpener tooltip" data-id="fall" title="<?php echo lang('autumn','c')?>"></span>
            <span class="mticon-snow seasonOpener tooltip" data-id="winter" title="<?php echo lang('winter','c')?>"></span>
            <span class="mticon-spring seasonOpener tooltip" data-id="spring" title="<?php echo lang('spring','c')?>"></span>
            <span class="mticon-summer seasonOpener tooltip" data-id="summer" title="<?php echo lang('summer','c')?>"></span>
    <?php
        }
    ?>
<br><br>
<div id="mainDivSpring" class="mainSeasonDiv">
    <div style="font-size:2em">
        <?php echo lang('spring','c')?>
    </div>
    <br />
    <table class="table" style="width:98%;margin: 0 auto">
        <thead>
            <tr>
                <th></th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('maximumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('minimumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($annual['avg']['spring'] as $year=>$value){
            ?>
                    <tr>
                        <td>
                            <?php echo $year?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avg']['spring'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avg']['spring'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMax']['spring'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMax']['spring'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMin']['spring'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMin']['spring'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['max']['spring'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['max']['spring'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDate']['spring'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['min']['spring'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['min']['spring'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDate']['spring'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['maxDay']['spring'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['maxDay']['spring'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDayDate']['spring'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['minDay']['spring'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['minDay']['spring'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDayDate']['spring'][$year])?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>
                    <?php echo number_format($overall['avg']['spring'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMax']['spring'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMin']['spring'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['max']['spring'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDate']['spring'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['min']['spring'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDate']['spring'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['maxDay']['spring'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDayDate']['spring'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['minDay']['spring'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDayDate']['spring'])?>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <br />
    <div id="springAnnualGraph" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="springAnnualGraph"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="springAnnualGraph"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="springAnnualGraph"></span>
    <br /><br />
    <div id="springAnnualGraph2" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="springAnnualGraph2"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="springAnnualGraph2"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="springAnnualGraph2"></span>
    <br />
</div>

<div id="mainDivSummer" class="mainSeasonDiv">
    <div style="font-size:2em">
        <?php echo lang('summer','c')?>
    </div>
    <br />
    <table class="table" style="width:98%;margin: 0 auto">
        <thead>
            <tr>
                <th></th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('maximumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('minimumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($annual['avg']['summer'] as $year=>$value){
            ?>
                    <tr>
                        <td>
                            <?php
                                if($stationLat>=0){
                                    echo $year;
                                }
                                else{
                                    echo $year."/".($year+1);
                                }
                            ?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avg']['summer'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avg']['summer'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMax']['summer'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMax']['summer'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMin']['summer'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMin']['summer'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['max']['summer'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['max']['summer'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDate']['summer'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['min']['summer'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['min']['summer'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDate']['summer'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['maxDay']['summer'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['maxDay']['summer'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDayDate']['summer'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['minDay']['summer'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['minDay']['summer'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDayDate']['summer'][$year])?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>
                    <?php echo number_format($overall['avg']['summer'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMax']['summer'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMin']['summer'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['max']['summer'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDate']['summer'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['min']['summer'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDate']['summer'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['maxDay']['summer'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDayDate']['summer'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['minDay']['summer'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDayDate']['summer'])?>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <br />
    <div id="summerAnnualGraph" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="summerAnnualGraph"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="summerAnnualGraph"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="summerAnnualGraph"></span>
    <br /><br />
    <div id="summerAnnualGraph2" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="summerAnnualGraph2"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="summerAnnualGraph2"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="summerAnnualGraph2"></span>
    <br />
</div>

<div id="mainDivFall" class="mainSeasonDiv">
    <div style="font-size:2em">
        <?php echo lang('autumn','c')?>
    </div>
    <br />
    <table class="table" style="width:98%;margin: 0 auto">
        <thead>
            <tr>
                <th></th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('maximumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('minimumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($annual['avg']['fall'] as $year=>$value){
            ?>
                    <tr>
                        <td>
                            <?php echo $year?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avg']['fall'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avg']['fall'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMax']['fall'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMax']['fall'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMin']['fall'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMin']['fall'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['max']['fall'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['max']['fall'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDate']['fall'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['min']['fall'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['min']['fall'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDate']['fall'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['maxDay']['fall'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['maxDay']['fall'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDayDate']['fall'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['minDay']['fall'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['minDay']['fall'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDayDate']['fall'][$year])?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>
                    <?php echo number_format($overall['avg']['fall'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMax']['fall'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMin']['fall'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['max']['fall'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDate']['fall'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['min']['fall'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDate']['fall'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['maxDay']['fall'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDayDate']['fall'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['minDay']['fall'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDayDate']['fall'])?>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <br />
    <div id="fallAnnualGraph" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="fallAnnualGraph"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="fallAnnualGraph"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="fallAnnualGraph"></span>
    <br /><br />
    <div id="fallAnnualGraph2" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="fallAnnualGraph2"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="fallAnnualGraph2"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="fallAnnualGraph2"></span>
    <br />
</div>

<div id="mainDivWinter" class="mainSeasonDiv">
    <div style="font-size:2em">
        <?php echo lang('winter','c')?>
    </div>
    <br />
    <table class="table" style="width:98%;margin: 0 auto">
        <thead>
            <tr>
                <th></th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('maximumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
                <th style="text-align">
                    <?php echo lang('minimumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($annual['avg']['winter'] as $year=>$value){
            ?>
                    <tr>
                        <td>
                            <?php
                                if($stationLat>=0){
                                    echo $year."/".($year+1);
                                }
                                else{
                                    echo $year;
                                }
                            ?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avg']['winter'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avg']['winter'][$year],$dp+3,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMax']['winter'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMax']['winter'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['avgMin']['winter'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['avgMin']['winter'][$year],$dp+1,".","");?>
                        </td>
                        <td data-value="<?php echo number_format($annual['max']['winter'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['max']['winter'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDate']['winter'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['min']['winter'][$year],$dp,".","");?>">
                            <?php echo number_format($annual['min']['winter'][$year],$dp,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDate']['winter'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['maxDay']['winter'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['maxDay']['winter'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['maxDayDate']['winter'][$year])?>
                            </div>
                        </td>
                        <td data-value="<?php echo number_format($annual['minDay']['winter'][$year],$dp+1,".","");?>">
                            <?php echo number_format($annual['minDay']['winter'][$year],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                            <div class="times" style="display:none">
                                <?php echo showDates($annual['minDayDate']['winter'][$year])?>
                            </div>
                        </td>
                    </tr>
            <?php
                }
            ?>
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td>
                    <?php echo number_format($overall['avg']['winter'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMax']['winter'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['avgMin']['winter'],$dp+1,".","");?>
                </td>
                <td>
                    <?php echo number_format($overall['max']['winter'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDate']['winter'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['min']['winter'],$dp,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDate']['winter'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['maxDay']['winter'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['maxDayDate']['winter'])?>
                    </div>
                </td>
                <td>
                    <?php echo number_format($overall['minDay']['winter'],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                    <div class="times" style="display:none">
                        <?php echo showDates($overall['minDayDate']['winter'])?>
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
    <br />
    <div id="winterAnnualGraph" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="winterAnnualGraph"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="winterAnnualGraph"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="winterAnnualGraph"></span>
    <br /><br />
    <div id="winterAnnualGraph2" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-splinechart graphSwitcher splineChart" id="winterAnnualGraph2"></span>
    <span class="mticon-splinechart graphSwitcher lineChart" id="winterAnnualGraph2"></span>
    <span class="mticon-barchart-1 graphSwitcher columnChart" id="winterAnnualGraph2"></span>
    <br /><br>
</div>
<table class="table" style="width:98%;margin: 0 auto">
    <thead>
        <tr>
            <th></th>
            <th style="text-align">
                <?php echo lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('maximumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
            <th style="text-align">
                <?php echo lang('minimumAbbr','c')." ".lang('daily','c')." ".lang('avgAbbr','c')?><br><span class="fa fa-sort sort"></span>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($seasonNames as $seasonName){
                if($seasonName=="fall"){
                    $translatedName = lang('autumn','c');
                }
                else{
                    $translatedName = lang($seasonName,'c');
                }
        ?>
                <tr>
                    <td>
                        <?php echo $translatedName?>
                    </td>
                    <td data-value="<?php echo number_format($overall['avg'][$seasonName],$dp+1,".","");?>">
                        <?php echo number_format($overall['avg'][$seasonName],$dp+1,".","");?>
                    </td>
                    <td data-value="<?php echo number_format($overall['avgMax'][$seasonName],$dp+1,".","");?>">
                        <?php echo number_format($overall['avgMax'][$seasonName],$dp+1,".","");?>
                    </td>
                    <td data-value="<?php echo number_format($overall['avgMin'][$seasonName],$dp+1,".","");?>">
                        <?php echo number_format($overall['avgMin'][$seasonName],$dp+1,".","");?>
                    </td>
                    <td data-value="<?php echo number_format($overall['max'][$seasonName],$dp,".","");?>">
                        <?php echo number_format($overall['max'][$seasonName],$dp,".","");?><span class="mticon-day timeOpener"></span>
                        <div class="times" style="display:none">
                            <?php echo showDates($overall['maxDate'][$seasonName])?>
                        </div>
                    </td>
                    <td data-value="<?php echo number_format($overall['min'][$seasonName],$dp,".","");?>">
                        <?php echo number_format($overall['min'][$seasonName],$dp,".","");?><span class="mticon-day timeOpener"></span>
                        <div class="times" style="display:none">
                            <?php echo showDates($overall['minDate'][$seasonName])?>
                        </div>
                    </td>
                    <td data-value="<?php echo number_format($overall['maxDay'][$seasonName],$dp+1,".","");?>">
                        <?php echo number_format($overall['maxDay'][$seasonName],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                        <div class="times" style="display:none">
                            <?php echo showDates($overall['maxDayDate'][$seasonName])?>
                        </div>
                    </td>
                    <td data-value="<?php echo number_format($overall['minDay'][$seasonName],$dp+1,".","");?>">
                        <?php echo number_format($overall['minDay'][$seasonName],$dp+1,".","");?><span class="mticon-day timeOpener"></span>
                        <div class="times" style="display:none">
                            <?php echo showDates($overall['minDayDate'][$seasonName])?>
                        </div>
                    </td>
                </tr>
        <?php
            }
        ?>
    </tbody>

</table>


<h2><?php echo lang('all seasons','c')?></h2>
<div id="allSeasonsGraph" style="width:98%;margin: 0 auto;height:400px"></div>
<span class="mticon-splinechart graphSwitcher splineChart" id="allSeasonsGraph"></span>
<span class="mticon-splinechart graphSwitcher lineChart" id="allSeasonsGraph"></span>
<span class="mticon-barchart-1 graphSwitcher columnChart" id="allSeasonsGraph"></span>
<br />
<script>
    $(".timeOpener").click(function(){
        $(this).next(".times").slideToggle(800);
    })
    $('.splineChart').click(function() {
        var chart = $('#'+$(this).attr("id")).highcharts();
        var series;
        for ( i = 0 ; i < chart.series.length ; i++ ) {
            chart.series[i].update({
                type: "spline"
            });
        }
    });
    $('.lineChart').click(function() {
        var chart = $('#'+$(this).attr("id")).highcharts();
        var series;
        for ( i = 0 ; i < chart.series.length ; i++ ) {
            chart.series[i].update({
                type: "line"
            });
        }
    });
    $('.columnChart').click(function() {
        var chart = $('#'+$(this).attr("id")).highcharts();
        var series;
        for ( i = 0 ; i < chart.series.length ; i++ ) {
            chart.series[i].update({
                type: "column"
            });
        }
    });
    $('.table').tablesorter({
        headers : {
            0 : { sorter: 'idParser' },
            1 : { sorter: 'idParser' },
            2 : { sorter: 'idParser' },
            3 : { sorter: 'idParser' },
            4 : { sorter: 'idParser' },
            5 : { sorter: 'idParser' },
            6 : { sorter: 'idParser' },
            7 : { sorter: 'idParser' },
            8 : { sorter: 'idParser' },
        }
    });
    $.tablesorter.addParser({
        id: 'idParser',
        is: function(s) {
          return false;
        },
        format: function(s, table, cell, cellIndex) {
          return $(cell).attr('data-value');
        },
        type: 'numeric'
    });
    $(".seasonOpener").click(function(){
        $(".mainSeasonDiv").hide();
        id = $(this).attr("data-id");
        if(id=="spring"){
            $("#mainDivSpring").slideDown();
            chart = $("#springAnnualGraph").highcharts();
            chart.reflow();
            chart = $("#springAnnualGraph2").highcharts();
            chart.reflow();
        }
        if(id=="summer"){
            $("#mainDivSummer").slideDown();
            chart = $("#summerAnnualGraph").highcharts();
            chart.reflow();
            chart = $("#summerAnnualGraph2").highcharts();
            chart.reflow();
        }
        if(id=="fall"){
            $("#mainDivFall").slideDown();
            chart = $("#fallAnnualGraph").highcharts();
            chart.reflow();
            chart = $("#fallAnnualGraph2").highcharts();
            chart.reflow();
        }
        if(id=="winter"){
            $("#mainDivWinter").slideDown();
            chart = $("#winterAnnualGraph").highcharts();
            chart.reflow();
            chart = $("#winterAnnualGraph2").highcharts();
            chart.reflow();
        }
    })
    $('#springAnnualGraph').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                <?php
                    foreach($annual['avg']['spring'] as $y=>$values){
                        echo "'".$y."',";
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            {
                name: '<?php echo lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avg']['spring'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMax']['spring'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMin']['spring'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['max']['spring'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['min']['spring'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['maxDay']['spring'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['minDay']['spring'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            }
        ]
    });
    $('#springAnnualGraph2').highcharts({
        chart: {
            type: 'column',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                "<?php echo lang('avgAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>",
                "<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>"
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            <?php
                foreach($annual['avg']['spring'] as $y=>$values){
            ?>
            {
                name: '<?php echo $y?>',
                data: [
                    <?php
                        echo number_format($annual['avg']['spring'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMax']['spring'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMin']['spring'][$y],($dp+1),'.','').",";
                        echo number_format($annual['max']['spring'][$y],($dp),'.','').",";
                        echo number_format($annual['min']['spring'][$y],($dp),'.','').",";
                        echo number_format($annual['maxDay']['spring'][$y],($dp+1),'.','').",";
                        echo number_format($annual['minDay']['spring'][$y],($dp+1),'.','');
                    ?>
                ]
            },
            <?php
                }
            ?>
        ]
    });
    $('#summerAnnualGraph').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                <?php
                    foreach($annual['avg']['summer'] as $y=>$values){
                        if($stationLat>=0){
                            echo "'".$y."',";
                        }
                        else{
                            echo "'".$y."/".($y+1)."',";
                        }
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            {
                name: '<?php echo lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avg']['summer'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMax']['summer'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMin']['summer'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['max']['summer'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['min']['summer'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['maxDay']['summer'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['minDay']['summer'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            }
        ]
    });
    $('#summerAnnualGraph2').highcharts({
        chart: {
            type: 'column',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                "<?php echo lang('avgAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>",
                "<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>"
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            <?php
                foreach($annual['avg']['summer'] as $y=>$values){
            ?>
            {
                name: '<?php if($stationLat>=0){echo $y;} else{ echo $y."/".($y+1);}?>',
                data: [
                    <?php
                        echo number_format($annual['avg']['summer'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMax']['summer'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMin']['summer'][$y],($dp+1),'.','').",";
                        echo number_format($annual['max']['summer'][$y],($dp),'.','').",";
                        echo number_format($annual['min']['summer'][$y],($dp),'.','').",";
                        echo number_format($annual['maxDay']['summer'][$y],($dp+1),'.','').",";
                        echo number_format($annual['minDay']['summer'][$y],($dp+1),'.','');
                    ?>
                ]
            },
            <?php
                }
            ?>
        ]
    });
    $('#fallAnnualGraph').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                <?php
                    foreach($annual['avg']['fall'] as $y=>$values){
                        echo "'".$y."',";
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            {
                name: '<?php echo lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avg']['fall'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMax']['fall'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMin']['fall'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['max']['fall'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['min']['fall'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['maxDay']['fall'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['minDay']['fall'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            }
        ]
    });
    $('#fallAnnualGraph2').highcharts({
        chart: {
            type: 'column',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                "<?php echo lang('avgAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>",
                "<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>"
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            <?php
                foreach($annual['avg']['fall'] as $y=>$values){
            ?>
            {
                name: '<?php echo $y?>',
                data: [
                    <?php
                        echo number_format($annual['avg']['fall'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMax']['fall'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMin']['fall'][$y],($dp+1),'.','').",";
                        echo number_format($annual['max']['fall'][$y],($dp),'.','').",";
                        echo number_format($annual['min']['fall'][$y],($dp),'.','').",";
                        echo number_format($annual['maxDay']['fall'][$y],($dp+1),'.','').",";
                        echo number_format($annual['minDay']['fall'][$y],($dp+1),'.','');
                    ?>
                ]
            },
            <?php
                }
            ?>
        ]
    });
    $('#winterAnnualGraph').highcharts({
        chart: {
            type: 'spline',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                <?php
                    foreach($annual['avg']['winter'] as $y=>$values){
                        if($stationLat>=0){
                            echo "'".$y."/".($y+1)."',";
                        }
                        else{
                            echo "'".$y."',";
                        }
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            {
                name: '<?php echo lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avg']['winter'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMax']['winter'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['avgMin']['winter'] as $y=>$values){
                            echo number_format($values,($dp+1),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['max']['winter'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['min']['winter'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['maxDay']['winter'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            },
            {
                name: '<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>',
                data: [
                    <?php
                        foreach($annual['minDay']['winter'] as $y=>$values){
                            echo number_format($values,($dp),'.','').",";
                        }
                    ?>
                ]
            }
        ]
    });
    $('#winterAnnualGraph2').highcharts({
        chart: {
            type: 'column',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                "<?php echo lang('avgAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('avgAbbr','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>",
                "<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>"
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            <?php
                foreach($annual['avg']['winter'] as $y=>$values){
            ?>
            {
                name: '<?php if($stationLat<0){echo $y;} else{ echo $y."/".($y+1);}?>',
                data: [
                    <?php
                        echo number_format($annual['avg']['winter'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMax']['winter'][$y],($dp+1),'.','').",";
                        echo number_format($annual['avgMin']['winter'][$y],($dp+1),'.','').",";
                        echo number_format($annual['max']['winter'][$y],($dp),'.','').",";
                        echo number_format($annual['min']['winter'][$y],($dp),'.','').",";
                        echo number_format($annual['maxDay']['winter'][$y],($dp+1),'.','').",";
                        echo number_format($annual['minDay']['winter'][$y],($dp+1),'.','');
                    ?>
                ]
            },
            <?php
                }
            ?>
        ]
    });
    $('#allSeasonsGraph').highcharts({
        chart: {
            type: 'column',
            zoomType: 'xy'
        },
        title: {
            text: '',
        },
        xAxis: {
            categories: [
                "<?php echo lang('avgAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('maximumAbbr','c')?>",
                "<?php echo lang('absolute','c')." ".lang('minimumAbbr','c')?>",
                "<?php echo lang('maximumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>",
                "<?php echo lang('minimumAbbr','c')." ".lang('day','c')." ".lang('avgAbbr','c')?>"
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: false
                }
            }
        },
        yAxis: {
            title: {
                text: '<?php echo $heading?> (<?php echo ($UoM)?>)'
            },
        },
        tooltip: {
            valueSuffix: '<?php echo ($UoM)?>',
            shared: true,
        },
        series: [
            <?php
                foreach($seasonNames as $seasonName){
                    if($seasonName=="fall"){
                        $translatedName = lang('autumn','c');
                    }
                    else{
                        $translatedName = lang($seasonName,'c');
                    }
            ?>
            {
                name: '<?php echo $translatedName?>',
                data: [
                    <?php
                        echo number_format($overall['avg'][$seasonName],($dp+1),'.','').",";
                        echo number_format($overall['max'][$seasonName],($dp),'.','').",";
                        echo number_format($overall['min'][$seasonName],($dp),'.','').",";
                        echo number_format($overall['maxDay'][$seasonName],($dp+1),'.','').",";
                        echo number_format($overall['minDay'][$seasonName],($dp+1),'.','');
                    ?>
                ]
            },
            <?php
                }
            ?>
        ]
    });
</script>
<script>
    $('.tooltip').tooltipster({
        delay: 2
    });
</script>