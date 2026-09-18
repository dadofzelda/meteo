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
    $statParams = array('avg','max','min');
    $statNames = array(lang('avgAbbr','c'), lang('maximumAbbr','c'), lang('minimumAbbr','c'));

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
                    else{
                        if($values3<=0){
                            $dayDistribution['avg'][$seasonName][$year]['<0'] = $dayDistribution['avg'][$seasonName][$year]['<0'] + 1;
                        }
                        if($values3<=0 && $values>32){
                            $dayDistribution['avg'][$seasonName][$year]['0-32'] = $dayDistribution['avg'][$seasonName][$year]['0-32'] + 1;
                        }
                        if($values3>32 && $values3<=50){
                            $dayDistribution['avg'][$seasonName][$year]['32-50'] = $dayDistribution['avg'][$seasonName][$year]['32-50'] + 1;
                        }
                        if($values3>50 && $values3<=60){
                            $dayDistribution['avg'][$seasonName][$year]['50-60'] = $dayDistribution['avg'][$seasonName][$year]['50-60'] + 1;
                        }
                        if($values3>60 && $values3<=70){
                            $dayDistribution['avg'][$seasonName][$year]['60-70'] = $dayDistribution['avg'][$seasonName][$year]['60-70'] + 1;
                        }
                        if($values3>70 && $values3<=85){
                            $dayDistribution['avg'][$seasonName][$year]['70-85'] = $dayDistribution['avg'][$seasonName][$year]['70-85'] + 1;
                        }
                        if($values3>85 && $values3<=100){
                            $dayDistribution['avg'][$seasonName][$year]['85-100'] = $dayDistribution['avg'][$seasonName][$year]['85-100'] + 1;
                        }
                        if($values3>100){
                            $dayDistribution['avg'][$seasonName][$year]['>100'] = $dayDistribution['avg'][$seasonName][$year]['>100'] + 1;
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
                    if($displayTempUnits=="C"){
                        if($values3<=-10){
                            $dayDistribution['max'][$seasonName][$year]['<-10'] = $dayDistribution['max'][$seasonName][$year]['<-10'] + 1;
                        }
                        if($values3<=0 && $values>-10){
                            $dayDistribution['max'][$seasonName][$year]['0--10'] = $dayDistribution['max'][$seasonName][$year]['0--10'] + 1;
                        }
                        if($values3>0 && $values3<=10){
                            $dayDistribution['max'][$seasonName][$year]['0-10'] = $dayDistribution['max'][$seasonName][$year]['0-10'] + 1;
                        }
                        if($values3>10 && $values3<=20){
                            $dayDistribution['max'][$seasonName][$year]['10-20'] = $dayDistribution['max'][$seasonName][$year]['10-20'] + 1;
                        }
                        if($values3>20 && $values3<=25){
                            $dayDistribution['max'][$seasonName][$year]['20-25'] = $dayDistribution['max'][$seasonName][$year]['20-25'] + 1;
                        }
                        if($values3>25 && $values3<=30){
                            $dayDistribution['max'][$seasonName][$year]['25-30'] = $dayDistribution['max'][$seasonName][$year]['25-30'] + 1;
                        }
                        if($values3>30 && $values3<=35){
                            $dayDistribution['max'][$seasonName][$year]['30-35'] = $dayDistribution['max'][$seasonName][$year]['30-35'] + 1;
                        }
                        if($values3>35){
                            $dayDistribution['max'][$seasonName][$year]['>35'] = $dayDistribution['max'][$seasonName][$year]['>35'] + 1;
                        }
                    }
                    else{
                        if($values3<=0){
                            $dayDistribution['max'][$seasonName][$year]['<0'] = $dayDistribution['max'][$seasonName][$year]['<0'] + 1;
                        }
                        if($values3<=0 && $values>32){
                            $dayDistribution['max'][$seasonName][$year]['0-32'] = $dayDistribution['max'][$seasonName][$year]['0-32'] + 1;
                        }
                        if($values3>32 && $values3<=50){
                            $dayDistribution['max'][$seasonName][$year]['32-50'] = $dayDistribution['max'][$seasonName][$year]['32-50'] + 1;
                        }
                        if($values3>50 && $values3<=60){
                            $dayDistribution['max'][$seasonName][$year]['50-60'] = $dayDistribution['max'][$seasonName][$year]['50-60'] + 1;
                        }
                        if($values3>60 && $values3<=70){
                            $dayDistribution['max'][$seasonName][$year]['60-70'] = $dayDistribution['max'][$seasonName][$year]['60-70'] + 1;
                        }
                        if($values3>70 && $values3<=85){
                            $dayDistribution['max'][$seasonName][$year]['70-85'] = $dayDistribution['max'][$seasonName][$year]['70-85'] + 1;
                        }
                        if($values3>85 && $values3<=100){
                            $dayDistribution['max'][$seasonName][$year]['85-100'] = $dayDistribution['max'][$seasonName][$year]['85-100'] + 1;
                        }
                        if($values3>100){
                            $dayDistribution['max'][$seasonName][$year]['>100'] = $dayDistribution['max'][$seasonName][$year]['>100'] + 1;
                        }
                    }
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
                    if($displayTempUnits=="C"){
                        if($values3<=-10){
                            $dayDistribution['min'][$seasonName][$year]['<-10'] = $dayDistribution['min'][$seasonName][$year]['<-10'] + 1;
                        }
                        if($values3<=0 && $values>-10){
                            $dayDistribution['min'][$seasonName][$year]['0--10'] = $dayDistribution['min'][$seasonName][$year]['0--10'] + 1;
                        }
                        if($values3>0 && $values3<=10){
                            $dayDistribution['min'][$seasonName][$year]['0-10'] = $dayDistribution['min'][$seasonName][$year]['0-10'] + 1;
                        }
                        if($values3>10 && $values3<=20){
                            $dayDistribution['min'][$seasonName][$year]['10-20'] = $dayDistribution['min'][$seasonName][$year]['10-20'] + 1;
                        }
                        if($values3>20 && $values3<=25){
                            $dayDistribution['min'][$seasonName][$year]['20-25'] = $dayDistribution['min'][$seasonName][$year]['20-25'] + 1;
                        }
                        if($values3>25 && $values3<=30){
                            $dayDistribution['min'][$seasonName][$year]['25-30'] = $dayDistribution['min'][$seasonName][$year]['25-30'] + 1;
                        }
                        if($values3>30 && $values3<=35){
                            $dayDistribution['min'][$seasonName][$year]['30-35'] = $dayDistribution['min'][$seasonName][$year]['30-35'] + 1;
                        }
                        if($values3>35){
                            $dayDistribution['min'][$seasonName][$year]['>35'] = $dayDistribution['min'][$seasonName][$year]['>35'] + 1;
                        }
                    }
                    else{
                        if($values3<=0){
                            $dayDistribution['min'][$seasonName][$year]['<0'] = $dayDistribution['min'][$seasonName][$year]['<0'] + 1;
                        }
                        if($values3<=0 && $values>32){
                            $dayDistribution['min'][$seasonName][$year]['0-32'] = $dayDistribution['min'][$seasonName][$year]['0-32'] + 1;
                        }
                        if($values3>32 && $values3<=50){
                            $dayDistribution['min'][$seasonName][$year]['32-50'] = $dayDistribution['min'][$seasonName][$year]['32-50'] + 1;
                        }
                        if($values3>50 && $values3<=60){
                            $dayDistribution['min'][$seasonName][$year]['50-60'] = $dayDistribution['min'][$seasonName][$year]['50-60'] + 1;
                        }
                        if($values3>60 && $values3<=70){
                            $dayDistribution['min'][$seasonName][$year]['60-70'] = $dayDistribution['min'][$seasonName][$year]['60-70'] + 1;
                        }
                        if($values3>70 && $values3<=85){
                            $dayDistribution['min'][$seasonName][$year]['70-85'] = $dayDistribution['min'][$seasonName][$year]['70-85'] + 1;
                        }
                        if($values3>85 && $values3<=100){
                            $dayDistribution['min'][$seasonName][$year]['85-100'] = $dayDistribution['min'][$seasonName][$year]['85-100'] + 1;
                        }
                        if($values3>100){
                            $dayDistribution['min'][$seasonName][$year]['>100'] = $dayDistribution['min'][$seasonName][$year]['>100'] + 1;
                        }
                    }
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
<h1><?php echo $heading?> - <?php echo lang('number of days','c')?></h1>
<span data-id="avg" class="parameterOpenerDiv"><?php echo lang('avgAbbr','c')?></span>
<span data-id="max" class="parameterOpenerDiv"><?php echo lang('maximumAbbr','c')?></span>
<span data-id="min" class="parameterOpenerDiv"><?php echo lang('minimumAbbr','c')?></span>

<div id="avgDayDiv" class="dayDiv">
    <?php
        if($stationLat>=0){
    ?>
    <span class="mticon-spring" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-summer" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-autumn" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-snow" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <?php
        if($stationLat<0){
    ?>
    <span class="mticon-autumn" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-snow" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-spring" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
    <span class="mticon-summer" style="font-size:2.2em"></span><br />
    <div id="avgDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <br /><br />
    <?php
        if($displayTempUnits=="C"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('avgAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; -10&deg;C
                            </th>
                            <th>
                                -10 <?php echo lang('to','l')?> 0&deg;C
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 10&deg;C
                            </th>
                            <th>
                                10 <?php echo lang('to','l')?> 20&deg;C
                            </th>
                            <th>
                                20 <?php echo lang('to','l')?> 25&deg;C
                            </th>
                            <th>
                                25 <?php echo lang('to','l')?> 30&deg;C
                            </th>
                            <th>
                                30 <?php echo lang('to','l')?> 35&deg;C
                            </th>
                            <th>
                                > 35&deg;C
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['avg'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($seasonNames[$i]!="winter"){
                                            echo $year;
                                        }
                                        else{
                                            echo $year."/".($year+1);
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<-10'],0,".","")?>">
                                    <?php echo number_format($values['<-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0--10'],0,".","")?>">
                                    <?php echo number_format($values['0--10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-10'],0,".","")?>">
                                    <?php echo number_format($values['0-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['10-20'],0,".","")?>">
                                    <?php echo number_format($values['10-20'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['20-25'],0,".","")?>">
                                    <?php echo number_format($values['20-25'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['25-30'],0,".","")?>">
                                    <?php echo number_format($values['25-30'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['30-35'],0,".","")?>">
                                    <?php echo number_format($values['30-35'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>35'],0,".","")?>">
                                    <?php echo number_format($values['>35'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"<-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"0--10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"0-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"10-20")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"20-25")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"25-30")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"30-35")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],">35")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <?php
        if($displayTempUnits=="F"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('avgAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; 0&deg;F
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 32&deg;F
                            </th>
                            <th>
                                32 <?php echo lang('to','l')?> 50&deg;F
                            </th>
                            <th>
                                50 <?php echo lang('to','l')?> 60&deg;F
                            </th>
                            <th>
                                60 <?php echo lang('to','l')?> 70&deg;F
                            </th>
                            <th>
                                70 <?php echo lang('to','l')?> 85&deg;F
                            </th>
                            <th>
                                85 <?php echo lang('to','l')?> 100&deg;F
                            </th>
                            <th>
                                > 100&deg;F
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['avg'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($stationLat>=0){
                                            if($seasonNames[$i]!="winter"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                        else{
                                            if($seasonNames[$i]!="summer"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<0'],0,".","")?>">
                                    <?php echo number_format($values['<0'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-32'],0,".","")?>">
                                    <?php echo number_format($values['0-32'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['32-50'],0,".","")?>">
                                    <?php echo number_format($values['32-50'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['50-60'],0,".","")?>">
                                    <?php echo number_format($values['50-60'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['60-70'],0,".","")?>">
                                    <?php echo number_format($values['60-70'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['70-85'],0,".","")?>">
                                    <?php echo number_format($values['70-85'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['85-100'],0,".","")?>">
                                    <?php echo number_format($values['85-100'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>100'],0,".","")?>">
                                    <?php echo number_format($values['>100'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"<0")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"0-32")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"32-50")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"50-60")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"60-70")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"70-85")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],"85-100")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['avg'][$seasonNames[$i]],">100")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <br />
</div>
<div id="maxDayDiv" class="dayDiv">
    <?php
        if($stationLat>=0){
    ?>
            <span class="mticon-spring" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-summer" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-autumn" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-snow" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <?php
        if($stationLat<0){
    ?>
            <span class="mticon-autumn" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-snow" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-spring" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-summer" style="font-size:2.2em"></span><br />
            <div id="maxDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <br /><br />
    <?php
        if($displayTempUnits=="C"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('maximumAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; -10&deg;C
                            </th>
                            <th>
                                -10 <?php echo lang('to','l')?> 0&deg;C
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 10&deg;C
                            </th>
                            <th>
                                10 <?php echo lang('to','l')?> 20&deg;C
                            </th>
                            <th>
                                20 <?php echo lang('to','l')?> 25&deg;C
                            </th>
                            <th>
                                25 <?php echo lang('to','l')?> 30&deg;C
                            </th>
                            <th>
                                30 <?php echo lang('to','l')?> 35&deg;C
                            </th>
                            <th>
                                > 35&deg;C
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['max'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($stationLat>=0){
                                            if($seasonNames[$i]!="winter"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                        else{
                                            if($seasonNames[$i]!="summer"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<-10'],0,".","")?>">
                                    <?php echo number_format($values['<-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0--10'],0,".","")?>">
                                    <?php echo number_format($values['0--10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-10'],0,".","")?>">
                                    <?php echo number_format($values['0-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['10-20'],0,".","")?>">
                                    <?php echo number_format($values['10-20'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['20-25'],0,".","")?>">
                                    <?php echo number_format($values['20-25'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['25-30'],0,".","")?>">
                                    <?php echo number_format($values['25-30'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['30-35'],0,".","")?>">
                                    <?php echo number_format($values['30-35'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>35'],0,".","")?>">
                                    <?php echo number_format($values['>35'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"<-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"0--10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"0-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"10-20")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"20-25")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"25-30")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"30-35")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],">35")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <?php
        if($displayTempUnits=="F"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('maximumAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; 0&deg;F
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 32&deg;F
                            </th>
                            <th>
                                32 <?php echo lang('to','l')?> 50&deg;F
                            </th>
                            <th>
                                50 <?php echo lang('to','l')?> 60&deg;F
                            </th>
                            <th>
                                60 <?php echo lang('to','l')?> 70&deg;F
                            </th>
                            <th>
                                70 <?php echo lang('to','l')?> 85&deg;F
                            </th>
                            <th>
                                85 <?php echo lang('to','l')?> 100&deg;F
                            </th>
                            <th>
                                > 100&deg;F
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['max'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($seasonNames[$i]!="winter"){
                                            echo $year;
                                        }
                                        else{
                                            echo $year."/".($year+1);
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<0'],0,".","")?>">
                                    <?php echo number_format($values['<0'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-32'],0,".","")?>">
                                    <?php echo number_format($values['0-32'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['32-50'],0,".","")?>">
                                    <?php echo number_format($values['32-50'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['50-60'],0,".","")?>">
                                    <?php echo number_format($values['50-60'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['60-70'],0,".","")?>">
                                    <?php echo number_format($values['60-70'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['70-85'],0,".","")?>">
                                    <?php echo number_format($values['70-85'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['85-100'],0,".","")?>">
                                    <?php echo number_format($values['85-100'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>100'],0,".","")?>">
                                    <?php echo number_format($values['>100'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"<0")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"0-32")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"32-50")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"50-60")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"60-70")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"70-85")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],"85-100")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],">100")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <br />
</div>
<div id="minDayDiv" class="dayDiv">
    <?php
        if($stationLat>=0){
    ?>
            <span class="mticon-spring" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-summer" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-autumn" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-snow" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <?php
        if($stationLat<0){
    ?>
            <span class="mticon-autumn" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphFall" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-snow" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphWinter" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-spring" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphSpring" style="width:98%;margin: 0 auto;height:400px"></div>
            <span class="mticon-summer" style="font-size:2.2em"></span><br />
            <div id="minDaysGraphSummer" style="width:98%;margin: 0 auto;height:400px"></div>
    <?php
        }
    ?>
    <br /><br />
    <?php
        if($displayTempUnits=="C"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('minimumAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; -10&deg;C
                            </th>
                            <th>
                                -10 <?php echo lang('to','l')?> 0&deg;C
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 10&deg;C
                            </th>
                            <th>
                                10 <?php echo lang('to','l')?> 20&deg;C
                            </th>
                            <th>
                                20 <?php echo lang('to','l')?> 25&deg;C
                            </th>
                            <th>
                                25 <?php echo lang('to','l')?> 30&deg;C
                            </th>
                            <th>
                                30 <?php echo lang('to','l')?> 35&deg;C
                            </th>
                            <th>
                                > 35&deg;C
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['min'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($seasonNames[$i]!="winter"){
                                            echo $year;
                                        }
                                        else{
                                            echo $year."/".($year+1);
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<-10'],0,".","")?>">
                                    <?php echo number_format($values['<-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0--10'],0,".","")?>">
                                    <?php echo number_format($values['0--10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-10'],0,".","")?>">
                                    <?php echo number_format($values['0-10'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['10-20'],0,".","")?>">
                                    <?php echo number_format($values['10-20'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['20-25'],0,".","")?>">
                                    <?php echo number_format($values['20-25'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['25-30'],0,".","")?>">
                                    <?php echo number_format($values['25-30'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['30-35'],0,".","")?>">
                                    <?php echo number_format($values['30-35'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>35'],0,".","")?>">
                                    <?php echo number_format($values['>35'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"<-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"0--10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"0-10")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"10-20")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"20-25")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"25-30")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"30-35")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],">35")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <?php
        if($displayTempUnits=="F"){
    ?>
            <h2><?php echo lang('days','c')?> - <?php echo lang('maximumAbbr','c')?></h2>
            <br />
            <?php
                for($i=0;$i<count($seasonNames);$i++){
            ?>
                <span class="<?php echo $seasonIcons[$i]?>" style="font-size:2.2em;padding-top:5px"></span>
                <table class="table" style="width:98%;margin: 0 auto">
                    <thead>
                        <tr>
                            <th></th>
                            <th>
                                &le; 0&deg;F
                            </th>
                            <th>
                                0 <?php echo lang('to','l')?> 32&deg;F
                            </th>
                            <th>
                                32 <?php echo lang('to','l')?> 50&deg;F
                            </th>
                            <th>
                                50 <?php echo lang('to','l')?> 60&deg;F
                            </th>
                            <th>
                                60 <?php echo lang('to','l')?> 70&deg;F
                            </th>
                            <th>
                                70 <?php echo lang('to','l')?> 85&deg;F
                            </th>
                            <th>
                                85 <?php echo lang('to','l')?> 100&deg;F
                            </th>
                            <th>
                                > 100&deg;F
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach($dayDistribution['min'][$seasonNames[$i]] as $year=>$values){
                        ?>
                            <tr>
                                <td>
                                    <?php
                                        if($stationLat>=0){
                                            if($seasonNames[$i]!="winter"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                        else{
                                            if($seasonNames[$i]!="summer"){
                                                echo $year;
                                            }
                                            else{
                                                echo $year."/".($year+1);
                                            }
                                        }
                                    ?>
                                </td>
                                <td data-value="<?php echo number_format($values['<0'],0,".","")?>">
                                    <?php echo number_format($values['<0'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['0-32'],0,".","")?>">
                                    <?php echo number_format($values['0-32'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['32-50'],0,".","")?>">
                                    <?php echo number_format($values['32-50'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['50-60'],0,".","")?>">
                                    <?php echo number_format($values['50-60'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['60-70'],0,".","")?>">
                                    <?php echo number_format($values['60-70'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['70-85'],0,".","")?>">
                                    <?php echo number_format($values['70-85'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['85-100'],0,".","")?>">
                                    <?php echo number_format($values['85-100'],0,".","")?>
                                </td>
                                <td data-value="<?php echo number_format($values['>100'],0,".","")?>">
                                    <?php echo number_format($values['>100'],0,".","")?>
                                </td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    <tfoot>
                        <td>

                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"<0")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"0-32")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"32-50")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"50-60")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"60-70")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"70-85")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['min'][$seasonNames[$i]],"85-100")?>
                        </td>
                        <td>
                            <?php echo sumDays($dayDistribution['max'][$seasonNames[$i]],">100")?>
                        </td>
                    </tfoot>
                </table>
                <br />
            <?php
                }
            ?>
            <br /><br />
    <?php
        }
    ?>
    <br />
</div>
<script>
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
    $(".parameterOpenerDiv").click(function(){
        $(".dayDiv").hide();
        id = $(this).attr("data-id");
        $("#"+id+"DayDiv").slideDown();
        chart = $("#"+id+"DaysGraphSpring").highcharts();
        chart.reflow();
        chart = $("#"+id+"DaysGraphSummer").highcharts();
        chart.reflow();
        chart = $("#"+id+"DaysGraphFall").highcharts();
        chart.reflow();
        chart = $("#"+id+"DaysGraphWinter").highcharts();
        chart.reflow();
    })
    $('#avgDaysGraphSpring').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['avg']['spring'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#avgDaysGraphSummer').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['avg']['summer'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#avgDaysGraphFall').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['avg']['fall'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#avgDaysGraphWinter').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['avg']['winter'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year."/".($year+1)?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });

    $('#maxDaysGraphSpring').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['max']['spring'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#maxDaysGraphSummer').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['max']['summer'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#maxDaysGraphFall').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['max']['fall'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#maxDaysGraphWinter').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['max']['winter'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year."/".($year+1)?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });

    $('#minDaysGraphSpring').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['min']['spring'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#minDaysGraphSummer').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['min']['summer'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#minDaysGraphFall').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['min']['fall'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
    $('#minDaysGraphWinter').highcharts({
        chart: {
            type: 'areaspline',
            zoomType: 'xy'
        },
        credits: {
            text: '<?php echo $highChartsCreditsText?>',
            href: '<?php echo $pageURL.$path?>'
        },
        title: {
            text: '',
        },
        xAxis: {
            labels: {
                useHTML: true,
            },
            categories: [
                <?php
                    if($displayTempUnits=="C"){
                ?>
                        "&le;-10°C",
                        "-10 <?php echo lang('to','l')?> 0°C",
                        "0 <?php echo lang('to','l')?> 10°C",
                        "10 <?php echo lang('to','l')?> 20°C",
                        "20 <?php echo lang('to','l')?> 25°C",
                        "25 <?php echo lang('to','l')?> 30°C",
                        "30 <?php echo lang('to','l')?> 35°C",
                        ">35°C",
                <?php
                    }
                ?>
                <?php
                    if($displayTempUnits=="F"){
                ?>
                        "&le;0°F",
                        "0 <?php echo lang('to','l')?> 32°F",
                        "32 <?php echo lang('to','l')?> 50°F",
                        "50 <?php echo lang('to','l')?> 60°F",
                        "60 <?php echo lang('to','l')?> 70°F",
                        "70 <?php echo lang('to','l')?> 85°F",
                        "85 <?php echo lang('to','l')?> 100°F",
                        ">100°F",
                <?php
                    }
                ?>
            ]
        },
        plotOptions:{
            spline: {
                marker: {
                    enabled: true
                }
            },

        },
        yAxis: {
            title: {
                text: '<?php echo lang('days','c')?>'
            },
        },
        tooltip: {
            shared: true,
        },
        series: [
            <?php
                foreach($dayDistribution['min']['winter'] as $year=>$values){
            ?>
                {
                    name: '<?php echo $year."/".($year+1)?>',
                    fillOpacity: 0.3,
                    data: [
                        <?php
                            if($displayTempUnits=="C"){
                                echo number_format($values["<-10"],0,'.','').",";
                                echo number_format($values["0--10"],0,'.','').",";
                                echo number_format($values["0-10"],0,'.','').",";
                                echo number_format($values["10-20"],0,'.','').",";
                                echo number_format($values["20-25"],0,'.','').",";
                                echo number_format($values["25-30"],0,'.','').",";
                                echo number_format($values["30-35"],0,'.','').",";
                                echo number_format($values[">35"],0,'.','').",";
                            }
                            else{
                                echo number_format($values["<0"],0,'.','').",";
                                echo number_format($values["0-32"],0,'.','').",";
                                echo number_format($values["32-50"],0,'.','').",";
                                echo number_format($values["50-60"],0,'.','').",";
                                echo number_format($values["60-70"],0,'.','').",";
                                echo number_format($values["70-85"],0,'.','').",";
                                echo number_format($values["85-100"],0,'.','').",";
                                echo number_format($values[">100"],0,'.','').",";
                            }
                        ?>
                    ]
                },
            <?php
                }
            ?>
        ]
    });
</script>
