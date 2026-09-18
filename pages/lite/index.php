<?php 
    include("../../config.php");
	include($baseURL."css/design.php");
    include($baseURL."header.php");

    // load current conditions
    $current = file_get_contents("../../meteotemplateLive.txt");
    $current = json_decode($current, true);

    // load DarkSky forecast
    $fIOURL = "https://api.darksky.net/forecast/".$fIOKey."/".$stationLat.",".$stationLon."?units=si&lang=".$fIOLanguage;

	if(!is_dir("../forecast/cache/")){
		mkdir("../forecast/cache/");
	}
	
	if(file_exists("../forecast/cache/current.txt")){ 
		if (time()-filemtime("../forecast/cache/current.txt") > 60 * 15) { // cache every 15 mins
			unlink("../forecast/cache/current.txt");
		}
	}
	if(file_exists("../forecast/cache/current.txt")){
		$rawData = file_get_contents("../forecast/cache/current.txt");
		$forecastLoadedTime = filemtime("../forecast/cache/current.txt");
	}
	else{
		$rawData = file_get_contents($fIOURL);
		if($rawData!=""){
			file_put_contents("../forecast/cache/current.txt",$rawData);
		}
		$forecastLoadedTime = time();
	}
	
	$rawData = file_get_contents('../forecast/cache/current.txt');
    $forecastData = json_decode($rawData, true);

    $forecast['summary'] = $forecastData['hourly']['summary'];
    $forecast['icon'] = $forecastData['hourly']['icon'];
    // today
    $forecast['today']['date'] = $forecastData['daily']['data'][0]['time'];
    $forecast['today']['summary'] = $forecastData['daily']['data'][0]['summary'];
    $forecast['today']['icon'] = $forecastData['daily']['data'][0]['icon'];
    $forecast['today']['T'] = array(convertor($forecastData['daily']['data'][0]['temperatureMin'],"C",$displayTempUnits),convertor($forecastData['daily']['data'][0]['temperatureMax'],"C",$displayTempUnits));
    $forecast['today']['H'] = $forecastData['daily']['data'][0]['humidity'] * 100;
    $forecast['today']['P'] = convertor($forecastData['daily']['data'][0]['pressure'],"hpa",$displayPressUnits);
    $forecast['today']['R'] = $forecastData['daily']['data'][0]['precipProbability'] * 100;
    // tomorrow
    $forecast['tomorrow']['date'] = $forecastData['daily']['data'][1]['time'];
    $forecast['tomorrow']['summary'] = $forecastData['daily']['data'][1]['summary'];
    $forecast['tomorrow']['icon'] = $forecastData['daily']['data'][1]['icon'];
    $forecast['tomorrow']['T'] = array(convertor($forecastData['daily']['data'][1]['temperatureMin'],"C",$displayTempUnits),convertor($forecastData['daily']['data'][1]['temperatureMax'],"C",$displayTempUnits));
    $forecast['tomorrow']['H'] = $forecastData['daily']['data'][1]['humidity'] * 100;
    $forecast['tomorrow']['P'] = convertor($forecastData['daily']['data'][1]['pressure'],"hpa",$displayPressUnits);
    $forecast['tomorrow']['R'] = $forecastData['daily']['data'][1]['precipProbability'] * 100;
    // week 
    for($i=2;$i<7;$i++){
        $thisDay = array();
        $thisDay['date'] = $forecastData['daily']['data'][$i]['time'];
        $thisDay['summary'] = $forecastData['daily']['data'][$i]['summary'];
        $thisDay['icon'] = $forecastData['daily']['data'][$i]['icon'];
        $thisDay['T'] = array(convertor($forecastData['daily']['data'][$i]['temperatureMin'],"C",$displayTempUnits),convertor($forecastData['daily']['data'][$i]['temperatureMax'],"C",$displayTempUnits));
        $forecast['week'][] = $thisDay;
    }

    $stationDataShowBftW = false;
    $stationDataShowBftG = false;
    $showNow = false;

    // Sun
    $stationTimezone = new DateTimeZone($stationTZ);
	$stationOffset  = $stationTimezone->getOffset(new DateTime)/3600;
	$sunRiseTS = date_sunrise((time()+($stationOffset*60*60)),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,($stationOffset));
	$sunSetTS = date_sunset((time()+($stationOffset*60*60)),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5,($stationOffset));

    // Moon 
    $moontimes = new MoonRiSet($stationLat, $stationLon, $stationTZ);
	$moontimes->setDate(date("Y"), date("m"), date("d"));
	$moonRise = $moontimes->rise["timestamp"];
	$moonSet = $moontimes->set["timestamp"];

    // calculate percentage
	$sr = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
	$ss = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
	
	if(time()>=$sr && time()<=$ss){
		$phaseDay = "sun";
		$phaseColor = "rgba(255,255,38,0.6)";
		$sunAnglePercent = ((time()-$sr)/($ss-$sr))*100;
	}
	if(time()>$ss){
		$tSR = date_sunrise(strtotime('tomorrow'),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		$phaseDay = "moon";
		$phaseColor = "rgba(102,102,102,0.6)";
		$sunAnglePercent = ((time()-$ss)/($tSR-$ss))*100;
	}
	if(time()<$sr){
		$ySS = date_sunset(strtotime('yesterday'),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		$phaseDay = "moon";
		$phaseColor = "rgba(102,102,102,0.6)";
		$sunAnglePercent = ((time()-$ySS)/($sr-$ySS))*100;
	}

    $x = $sunAnglePercent;
	if($x>50){
		$x = 100-$x;
	}

	if($x<10){
		$topHeight = 47.7;
	}
	if($x>=8 && $x<12){
		$topHeight = 20;
	}
	if($x>=12 && $x<17){
		$topHeight = 12;
	}
	if($x>=17 && $x<25){
		$topHeight = 5;
	}
	if($x>=25 && $x<30){
		$topHeight = 0;
	}
	if($x>=30 && $x<40){
		$topHeight = -7;
	}
	if($x>=40 && $x<=50){
		$topHeight = -12;
	}

    // graph 
    $graphColor = "000";
	$gridColor = "666666";

    $interactiveGraphDefaultParameter = "T";
    $interactiveGraphDefaultInterval = "today";
    $interactiveGraphHeight = 400;
    $graphHueColor = getColorHue("#".$color_schemes[$design2]["900"],"hex");




?> 

<!DOCTYPE html>
<html> 
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
        <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery-ui.js"></script>
		<script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tooltipster.js"></script>
        <script src="//code.highcharts.com/stock/highstock.js"></script>
		<script src="//code.highcharts.com/stock/highcharts-more.js"></script>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/vader/jquery-ui.css">
        <link rel="shortcut icon" href="<?php echo $pageURL.$path?>icons/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" href="<?php echo $pageURL.$path?>css/font/styles.css">
		<link rel="stylesheet" href="<?php echo $pageURL.$path?>css/fontAwesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
		<title><?php echo $pageName?> @ Meteotemplate</title>
        <style> 
            body {
                padding-top: 2rem;
            }
            .bg-inverse{
                background-color: #<?php echo $color_schemes[$design2]['900']?>!important;
            }
            .forecastIcon{
                max-width: 50px;
            }
            .forecastIconWeek{
                max-width: 30px;
            }
            .jumbotron{
                background-color: #<?php echo $color_schemes[$design2]['100']?>!important;
            }
            #windity, #astronomy, #footer{
                background-color: #<?php echo $color_schemes[$design2]['900']?>!important;
                color: white;
            }
            .stationDataIcon,{
                font-size: 1.5em;
            }
            .sunIcon{ 
                font-size: 2.5em;
            }
            .innerCircle {
            position: absolute;
            width: 80px;
            height: 80px;
            border-radius: 40px;
            top: 10px;
            left: 10px; 
            background-color: #<?php echo $color_schemes[$design2]['900']?>;
            color: white;
            }
            .innerCircle .contentPie {
            position: absolute;
            display: block;
            width: 80px;
            top: 25px;
            left: 0;
            text-align: center;
            font-size: 25px;
            }
            
            .risetSunmoon {
                position: relative;
                width: 300px;
                height: 60px;
                margin: 0 auto;
                margin-bottom:20px;
            }
            .risetSun-times {
                margin: 0 auto;
                width: 200px;
                height: 60px;
                border-bottom: 2px solid #999;
                overflow: hidden;
                position: relative;
            }
            .risetSun-path {
                width: 98%;
                overflow: hidden!important;
                <?php
                    if($theme=="dark"){
                ?>
                        border: 1px dashed #eeeeee;
                <?php 
                    }
                    else{
                ?>		
                        border: 1px dashed #000000;
                <?php
                    }
                ?>
                border-radius: 50%;
                position: relative;
            }
            .risetSymbolImg{
                width:25px;
                position:absolute;
                z-index:2;
                left: <?php echo 50 + 2*$sunAnglePercent-12.5 - 3?>px;
                top: <?php echo $topHeight?>px;
            }
            .risetSun-animation {
                width: <?php echo $sunAnglePercent?>%;
                height: 150px;
                background-color: <?php echo $phaseColor?>;
            }
			.custom-select{
				cursor:pointer;
			}
			#desktopRedirect{
				font-size: 1.5em;
				cursor: pointer;
				opacity: 0.75;
			}
			#desktopRedirect:hover{
				opacity: 1;
			}
        </style>
    </head> 
    <body> 
        <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top bg-inverse">
            <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand font-weight-bold" href="#"><?php echo $headerTitleText?></a>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#jumbotron"><?php echo lang('current conditions','c')?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#forecast"><?php echo lang('forecast','c')?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#windity"><?php echo lang('map','c')?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#stationHistory"><?php echo lang('station history','c')?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#graph"><?php echo lang('graph','c')?></a>
                    </li>
                </ul>
                <form class="form-inline mt-2 mt-md-0">
                    <img src="<?php echo $pageURL.$path?>imgs/<?php echo $flagIconShape?>/big/<?php echo $stationCountry?>.png" style="max-height:40px">
                </form>
            </div>
        </nav>
        <div class="jumbotron" id="jumbotron">
            <div class="container">
                <h1 class="display-3"><?php echo lang("current conditions",'c')?></h1>
				<h3>&nbsp;&nbsp;<?php echo date($dateTimeFormat, $current['U'])?></h3>
                <div class="row">
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-temp"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['T'],"C",$displayTempUnits),1,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayTempUnits)?></p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-humidity"></span></h2>
                        <h2 class="text-center"><?php echo number_format(($current['H']),1,".","")?></h2>
                        <p class="text-center">%</p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-pressure"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['P'],"hpa",$displayPressUnits),$decimalsP,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayPressUnits)?></p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-rain"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['R'],"mm", $displayRainUnits),$decimalsR,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayRainUnits)?></p>
                    </div>
					<div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-rain"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['RR'],"mm", $displayRainUnits),$decimalsR,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayRainUnits)?>/h</p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-wind"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['W'],"kmh", $displayWindUnits),1,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayWindUnits)?></p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-gust"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['G'],"kmh", $displayWindUnits),1,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayWindUnits)?></p>
                    </div>
                    <?php 
                        if($solarSensor){
                    ?>
                            <div class="col-md-2 col-sm-4">
                                <h2 class="text-center"><span class="mticon-sun"></span></h2>
                                <h2 class="text-center"><?php echo number_format($current['S'],0,".","")?></h2>
                                <p class="text-center">W/m<sup>2</sup></p>
                            </div>
                    <?php 
                        }
                    ?> 
                    <?php 
                        if(isset($current['UV'])){
                    ?>
                            <div class="col-md-2 col-sm-4">
                                <h2 class="text-center"><span class="mticon-uv"></span></h2>
                                <h2 class="text-center"><?php echo number_format($current['UV'],1,".","")?></h2>
                                <p class="text-center"></p>
                            </div>
                    <?php 
                        }
                    ?> 
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-apparent"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['A'],"C",$displayTempUnits),1,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayTempUnits)?></p>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <h2 class="text-center"><span class="mticon-dewpoint"></span></h2>
                        <h2 class="text-center"><?php echo number_format(convertor($current['D'],"C",$displayTempUnits),1,".","")?></h2>
                        <p class="text-center"><?php echo unitFormatter($displayTempUnits)?></p>
                    </div>
                </div>
            </div>
        </div>
        <section id="forecast">
            <div class="container">
                <h1 class="display-3"><?php echo lang("forecast",'c')?></h1>
                <h4><?php echo $forecast['summary']?></h4>
                <div class="row">
                    <div class="col-md-4">
                        <h3><?php echo lang('today','c')?></h3>
                        <p class="initialism">&nbsp;&nbsp;<?php echo date($dateFormat,$forecast['today']['date'])?></p>
                        <p><img src="icons/<?php echo $forecast['today']['icon']?>.png" class="forecastIcon">&nbsp;&nbsp;&nbsp;<span class="font-weight-bold"><?php echo number_format($forecast['today']['T'][0],1,".","")?>&nbsp;/&nbsp;<?php echo number_format($forecast['today']['T'][1],1,".","")?></span>&nbsp;<?php echo unitFormatter($displayTempUnits)?></p>
                        <p><?php echo $forecast['today']['summary']?></p>
                        <p><span class="mticon-humidity"></span>&nbsp;<?php echo number_format($forecast['today']['H'],0,".","")?>&nbsp;%</p>
                        <p><span class="mticon-pressure"></span>&nbsp;<?php echo number_format($forecast['today']['P'],$decimalsP,".","")?>&nbsp;<?php echo unitFormatter($displayPressUnits)?></p>
                        <p><span class="mticon-rain"></span>&nbsp;<?php echo number_format($forecast['today']['R'],0,".","")?>&nbsp;%</p>
                    </div>
                    <div class="col-md-4">
                        <h3><?php echo lang('tomorrow','c')?></h3>
                        <p class="initialism">&nbsp;&nbsp;<?php echo date($dateFormat,$forecast['tomorrow']['date'])?></p>
                        <p><img src="icons/<?php echo $forecast['tomorrow']['icon']?>.png" class="forecastIcon">&nbsp;&nbsp;&nbsp;<span class="font-weight-bold"><?php echo number_format($forecast['tomorrow']['T'][0],1,".","")?>&nbsp;/&nbsp;<?php echo number_format($forecast['tomorrow']['T'][1],1,".","")?></span>&nbsp;<?php echo unitFormatter($displayTempUnits)?></p>
                        <p><?php echo $forecast['tomorrow']['summary']?></p>
                        <p><span class="mticon-humidity"></span>&nbsp;<?php echo number_format($forecast['tomorrow']['H'],0,".","")?>&nbsp;%</p>
                        <p><span class="mticon-pressure"></span>&nbsp;<?php echo number_format($forecast['tomorrow']['P'],$decimalsP,".","")?>&nbsp;<?php echo unitFormatter($displayPressUnits)?></p>
                        <p><span class="mticon-rain"></span>&nbsp;<?php echo number_format($forecast['tomorrow']['R'],0,".","")?>&nbsp;%</p>
                    </div>
                    <div class="col-md-4">
                        <h3><?php echo lang('week','c')?></h3>
                        <table class="table" style="width:98%;margin:0 auto">
                            <?php 
                                foreach($forecast['week'] as $thisDay){
                            ?> 
                                    <tr> 
                                        <td> 
                                            <img src="icons/<?php echo $thisDay['icon']?>.png" class="forecastIconWeek">
                                        </td>
                                        <td> 
                                            <p class="initialism"><?php echo date($dateFormat,$thisDay['date'])?></p>
                                        </td>
                                        <td> 
                                            <p class="initialism"><span class="font-weight-bold"><?php echo number_format($thisDay['T'][0],0,".","")?>&nbsp;/&nbsp;<?php echo number_format($thisDay['T'][1],0,".","")?></span>&nbsp;<?php echo unitFormatter($displayTempUnits)?></p>
                                        </td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </table>
                    </div>
                </div>
            </div>
        </section>
        <section id="windity">
            <div class="container">
                <br>
                <h1 class="display-3" style="color:white"><?php echo lang("map",'c')?></h1>
                <iframe src="https://embed.windy.com/embed2.html?lat=<?php echo $stationLat?>&lon=<?php echo $stationLon?>&zoom=5&level=surface&overlay=temp&menu=&message=&marker=&forecast=12&calendar=now&location=coordinates&type=map&actualGrid=&metricWind=kt&metricTemp=%C2%B0<?php echo $displayTempUnits?>" style="border:none;width:100%;height:600px">
                </iframe>
				<br><br>
            </div>
        </section>
        <section id="stationHistory">
            <div class="container">
                <br>
                <h1 class="display-3"><?php echo lang("station history",'c')?></h1>
                <select class="custom-select" id="historySelector">
                    <option value="" selected>---</option>
                    <option value="today">
						<?php echo lang('today','c')?>
					</option>
					<option value="yesterday">
						<?php echo lang('yesterday','c')?>
					</option>
					<option value="1h">
						1<?php echo " ".lang('hAbbr','l')?>
					</option>
					<option value="24h">
						24<?php echo " ".lang('hAbbr','l')?>
					</option>
					<option value="thisMonth">
						<?php echo lang("this month",'c');?>
					</option>
					<option value="thisYear">
						<?php echo lang("this year",'c');?>
					</option>
					<option value="last365">
						<?php echo lang("last",'c')." 365 ".lang("days",'l');?>
					</option>
					<option value="last7">
						<?php echo lang("last",'c')." 7 ".lang("days",'l');?>
					</option>
					<option value="alltime">
						<?php echo lang('all time','c')?>
					</option>
					<option value="normal">
						<?php echo date('d')." ".lang('month'.date('n'),'c')?>
					</option>
                </select>
                &nbsp;&nbsp;
                <span id="historyLoading" class="initialism"></span>
                <table style="width:100%;display:none" class="table" id="stationHistoryTable">
                    <tr>
                        <td>
                        </td>
                        <td style="font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
                            <?php echo !$showNow ? lang('avgAbbr','c') : lang('now','c')?>
                        </td>
                        <td style="font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
                            <?php echo lang('maximumAbbr','c')?>
                        </td>
                        <td style="font-variant:small-caps;font-size:1.1em;font-weight:bold;padding-bottom:4px">
                            <?php echo lang('minimumAbbr','c')?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-temp stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsTAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsTMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsTMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-humidity stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsHAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsHMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsHMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-pressure stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsPAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsPMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsPMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-wind stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsWAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsWMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsWMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-gust stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsGAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsGMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:bottom">
                            <span id="recordsGMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-rain stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
                            <span style="font-size:0.9em;font-variant:small-caps;font-weight:bold"><?php echo lang('total','c')?></span><br>
                            <span id="recordsR" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
                            <span id="recordsR2Label" style="font-size:0.9em;font-variant:small-caps;font-weight:bold"></span><br>
                            <span id="recordsR2" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c;vertical-align:top">
                            <span id="recordsR3Label" style="font-size:0.9em;font-variant:small-caps;font-weight:bold"></span><br>
                            <span id="recordsR3" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-apparent stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsAAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsAMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsAMin" class="records"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="mticon-dewpoint stationDataIcon"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsDAvg" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsDMax" class="records"></span>
                        </td>
                        <td style="border-bottom:1px solid #9b9b8c">
                            <span id="recordsDMin" class="records"></span>
                        </td>
                    </tr>
                    <?php
                        if($solarSensor){
                    ?>
                            <tr>
                                <td>
                                    <span class="mticon-sun stationDataIcon"></span>
                                </td>
                                <td >
                                    <span id="recordsSAvg" class="records"></span>
                                </td>
                                <td >
                                    <span id="recordsSMax" class="records"></span>
                                </td>
                                <td >
                                    <span id="recordsSMin" class="records"></span>
                                </td>
                            </tr>
                    <?php
                        }
                    ?>
                </table>
                <br><br>
            </div>
        </section>
        <section id="astronomy">
            <div class="container">
                <br>
                <h1 class="display-3"><?php echo lang("almanach",'c')?></h1>
                <div class="row">
                    <div class="col-md-4">
                        <h1><span class='mticon-sun'></span></h1>
                        <table> 
                            <tr>
                                <td> 
                                    <span class="mticon-sunrise sunIcon"></span>
                                </td>
                                <td> 
                                    <?php echo date($timeFormat, $sunRiseTS);?>
                                </td>
                            </tr> 
                            <tr> 
                                <td> 
                                    <span class="mticon-sunset sunIcon"></span>
                                </td>
                                <td> 
                                    <?php echo date($timeFormat, $sunSetTS);?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <h1><span class='mticon-moon'></span></h1>
                        <table> 
                            <tr>
                                <td> 
                                    <span class="mticon-moonrise sunIcon"></span>
                                </td>
                                <td> 
                                    <?php echo date($timeFormat, $moonRise);?>
                                </td>
                            </tr> 
                            <tr> 
                                <td> 
                                    <span class="mticon-moonset sunIcon"></span>
                                </td>
                                <td> 
                                    <?php echo date($timeFormat, $moonSet);?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <h3><?php echo lang('now','c')?></h3>
                        <div class="risetSunmoon">
                            <img src="icons/<?php echo $phaseDay?>.png" class="risetSymbolImg" alt="">
                            <div class="risetSun-times">
                                
                                <div class="risetSun-path">
                                    <div class="risetSun-animation"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
            </div>
        </section>
        <section id="graph">
            <div class="container">
                <br>
                <h1 class="display-3"><?php echo lang("graph",'c')?></h1>
                <table style="margin-left:auto;margin-right:auto">
                    <tr>
                        <td style="width:33%;text-align:center">
                            <select class="custom-select" id="parameterSelectorInteractiveGraph">
                                <option value="T" <?php if($interactiveGraphDefaultParameter=="T"){ echo "selected"; }?>><?php echo lang("temperature",'c')?></option>
                                <option value="H" <?php if($interactiveGraphDefaultParameter=="H"){ echo "selected"; }?>><?php echo lang("humidity",'c')?></option>
                                <option value="D" <?php if($interactiveGraphDefaultParameter=="D"){ echo "selected"; }?>><?php echo lang("dew point",'c')?></option>
                                <option value="P" <?php if($interactiveGraphDefaultParameter=="P"){ echo "selected"; }?>><?php echo lang("pressure",'c')?></option>
                                <option value="W" <?php if($interactiveGraphDefaultParameter=="W"){ echo "selected"; }?>><?php echo lang("wind",'c')?></option>
                                <option value="B" <?php if($interactiveGraphDefaultParameter=="B"){ echo "selected"; }?>><?php echo lang("wind direction",'c')?></option>
                                <option value="R" <?php if($interactiveGraphDefaultParameter=="R"){ echo "selected"; }?>><?php echo lang("precipitation",'c')?></option>
                                <?php if($solarSensor){?>
                                    <option value="S" <?php if($interactiveGraphDefaultParameter=="S"){ echo "selected"; }?>><?php echo lang("solar radiation",'c')?></option>
                                <?php } ?>
                            </select>
                        </td>
                        <td style="width:33%;text-align:center">
                            <select class="custom-select" id="groupingSelectorInteractiveGraph">
                                <option value="all"><?php echo lang("all",'c')?></option>
                                <option value="h"><?php echo lang("hourly averages",'c')?></option>
                                <option value="d"><?php echo lang("daily averages",'c')?></option>
                                <option value="m"><?php echo lang("monthly averages",'c')?></option>
                            </select>
                        </td>
                        <td style="width:33%;text-align:center">
                            <select class="custom-select" id="intervalSelectorInteractiveGraph">
                                <option value="1h" <?php if($interactiveGraphDefaultInterval=="1h"){ echo "selected"; }?>>1<?php echo lang("hAbbr",'')?></option>
                                <option value="24h" <?php if($interactiveGraphDefaultInterval=="24h"){ echo "selected"; }?>>24<?php echo lang("hAbbr",'')?></option>
                                <option value="today" <?php if($interactiveGraphDefaultInterval=="today"){ echo "selected"; }?>><?php echo lang("today",'c')?></option>
                                <option value="yesterday" <?php if($interactiveGraphDefaultInterval=="yesterday"){ echo "selected"; }?>><?php echo lang("yesterday",'c')?></option>
                                <option value="thisweek" <?php if($interactiveGraphDefaultInterval=="thisweek"){ echo "selected"; }?>><?php echo lang("this",'c')." ".lang("week",'l')?></option>
                                <option value="thismonth" <?php if($interactiveGraphDefaultInterval=="thismonth"){ echo "selected"; }?>><?php echo lang("this",'c')." ".lang("month",'l')?></option>
                                <option value="lastweek" <?php if($interactiveGraphDefaultInterval=="lastweek"){ echo "selected"; }?>><?php echo lang("last",'c')." ".lang("week",'l')?></option>
                                <option value="last7days" <?php if($interactiveGraphDefaultInterval=="last7days"){ echo "selected"; }?>><?php echo lang("last",'c')." 7 ".lang("days",'l')?></option>
                                <option value="lastmonth" <?php if($interactiveGraphDefaultInterval=="lastmonth"){ echo "selected"; }?>><?php echo lang("last",'c')." ".lang("month",'l')?></option>
                                <option value="last30days" <?php if($interactiveGraphDefaultInterval=="last30days"){ echo "selected"; }?>><?php echo lang("last",'c')." 30 ".lang("days",'l')?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                <br>
                <div style="position:relative;width:100%;margin: 0 auto;">
                    <div style="width:100%;text-align:center;position:absolute;top:50;left:0;z-index:10">
                        <img src="<?php echo $pageURL.$path?>icons/logo.png" style="width:100px;padding-top:50px" class="mtSpinner" id="spinnerInteractiveGraph">
                    </div>
                    <div id="graphInteractiveGraph" style="width: 98%; height: <?php echo $interactiveGraphHeight?>px; margin: 0 auto;z-index:5">
                    </div>
                </div>

                <br>
                <input type="hidden" id="chosenParameterInteractiveGraph" value="<?php echo $interactiveGraphDefaultParameter ?>">
                <input type="hidden" id="chosenValueInteractiveGraph" value="all">
                <input type="hidden" id="chosenIntervalInteractiveGraph" value="<?php echo $interactiveGraphDefaultInterval ?>">
            </div>
        </section>
        <section id="footer">
            <div class="container">
                <div class="text-center"> 
					<table style="width:98%;margin:0 auto"> 
						<tr> 
							<td> 
							</td> 
							<td>
								<table style="margin:0 auto" cellspacing="0" cellpadding="0">
									<tr>
										<td style="text-align:right">
											&copy; <?php echo date('Y')?>
										</td>
										<td style="text-align:center">
											Meteotemplate
										</td>
										<td style="text-align:left">
											<?php if($hideAdminEntrance){?>
												<a href="<?php echo $pageURL.$path?>admin/login.php">
											<?php }?>
												<img src="<?php echo $pageURL.$path?>icons/footerIcon.png" style="width:15px">
											<?php if($hideAdminEntrance){?>
												</a>
											<?php }?>
										</td>
									</tr>
									<tr>
										<td colspan="3" style="text-align:center">
											<a style="color:white!important" href="<?php echo $meteotemplateURL?>" target="_blank">www.meteotemplate.com</a>
										</td>
									</tr>
									<tr>
										<td colspan="3" style="text-align:center">
											Meteotemplate <?php echo number_format($templateVersion,1,'.','')." ".$versionName;?>
										</td>
									</tr>
								</table>
							</td> 
							<td style="text-align:right"> 
								<span class="fa fa-television" id="desktopRedirect"></span>
							</td>
						</tr> 
					</table>
                </div>
            </div>
        </section>
        <script> 
			$("#desktopRedirect").click(function(){
				location = "../../indexDesktop.php";
			})
            function stationData(period){
                $("#stationHistoryTable").slideDown();
                if(period==""){
                    return false;
                }
                $("#historyLoading").html("<?php echo lang('loading','c')?>...");
                $("#recordsHeading").html("<?php echo lang('loading','c')?>...");
                $.ajax({
                    url : "stationDataAjax.php?period="+period,
                    dataType : 'json',
                    success : function (json) {
                        if(period=="today"){
                            $("#recordsHeading").html("<?php echo lang('today','c')?>");
                        }
                        if(period=="yesterday"){
                            $("#recordsHeading").html("<?php echo lang('yesterday','c')?>");
                        }
                        if(period=="1h"){
                            $("#recordsHeading").html("1<?php echo " ".lang('hAbbr','l')?>");
                        }
                        if(period=="24h"){
                            $("#recordsHeading").html("24<?php echo " ".lang('hAbbr','l')?>");
                        }
                        if(period=="thisMonth"){
                            $("#recordsHeading").html("<?php echo lang("this month",'c');?>");
                        }
                        if(period=="thisYear"){
                            $("#recordsHeading").html("<?php echo lang("this year",'c');?>");
                        }
                        if(period=="last365"){
                            $("#recordsHeading").html("<?php echo lang("last",'c')." 365 ".lang("days",'l');?>");
                        }
                        if(period=="last7"){
                            $("#recordsHeading").html("<?php echo lang("last",'c')." 7 ".lang("days",'l');?>");
                        }
                        if(period=="alltime"){
                            $("#recordsHeading").html("<?php echo lang('all time','c')?>");
                        }
                        if(period=="normal"){
                            $("#recordsHeading").html("<?php echo date('d')." ".lang('month'.date('n'),'c')?>");
                        }
                        $("#recordsTAvg").html("<strong>"+json['avgT']+"</strong>");
                        $("#recordsTMax").html("<strong>"+json['maxT']+"</strong><br><span style='font-size:0.8em'>"+json['maxTtime']+"</span>");
                        $("#recordsTMin").html("<strong>"+json['minT']+"</strong><br><span style='font-size:0.8em'>"+json['minTtime']+"</span>");

                        $("#recordsHAvg").html("<strong>"+json['avgH']+"</strong>");
                        $("#recordsHMax").html("<strong>"+json['maxH']+"</strong><br><span style='font-size:0.8em'>"+json['maxHtime']+"</span>");
                        $("#recordsHMin").html("<strong>"+json['minH']+"</strong><br><span style='font-size:0.8em'>"+json['minHtime']+"</span>");

                        $("#recordsPAvg").html("<strong>"+json['avgP']+"</strong>");
                        $("#recordsPMax").html("<strong>"+json['maxP']+"</strong><br><span style='font-size:0.8em'>"+json['maxPtime']+"</span>");
                        $("#recordsPMin").html("<strong>"+json['minP']+"</strong><br><span style='font-size:0.8em'>"+json['minPtime']+"</span>");

                        $("#recordsWAvg").html("<strong>"+json['avgW']+"</strong>");
                        $("#recordsWAvgBft").html(json['avgWBft']);
                        $("#recordsWAvgBft").css("background",json['avgWBftBg']);
                        $("#recordsWAvgBft").css("color",json['avgWBftColor']);
                        $("#recordsWMax").html("<strong>"+json['maxW']+"</strong><br><span style='font-size:0.8em'>"+json['maxWtime']+"</span>");
                        $("#recordsWMaxBft").html(json['maxWBft']);
                        $("#recordsWMaxBft").css("background",json['maxWBftBg']);
                        $("#recordsWMaxBft").css("color",json['maxWBftColor']);
                        $("#recordsWMin").html(json['minW']);
                        $("#recordsWMinBft").html(json['minWBft']);
                        $("#recordsWMinBft").css("background",json['minWBftBg']);
                        $("#recordsWMinBft").css("color",json['minWBftColor']);

                        $("#recordsGAvg").html("<strong>"+json['avgG']+"</strong>");
                        $("#recordsGAvgBft").html(json['avgGBft']);
                        $("#recordsGAvgBft").css("background",json['avgGBftBg']);
                        $("#recordsGAvgBft").css("color",json['avgGBftColor']);
                        $("#recordsGMax").html("<strong>"+json['maxG']+"</strong><br><span style='font-size:0.8em'>"+json['maxGtime']+"</span>");
                        $("#recordsGMaxBft").html(json['maxGBft']);
                        $("#recordsGMaxBft").css("background",json['maxGBftBg']);
                        $("#recordsGMaxBft").css("color",json['maxGBftColor']);
                        $("#recordsGMin").html(json['minG']);
                        $("#recordsGMinBft").html(json['minGBft']);
                        $("#recordsGMinBft").css("background",json['minGBftBg']);
                        $("#recordsGMinBft").css("color",json['minGBftColor']);

                        $("#recordsDAvg").html("<strong>"+json['avgD']+"</strong>");
                        $("#recordsDMax").html("<strong>"+json['maxD']+"</strong><br><span style='font-size:0.8em'>"+json['maxDtime']+"</span>");
                        $("#recordsDMin").html("<strong>"+json['minD']+"</strong><br><span style='font-size:0.8em'>"+json['minDtime']+"</span>");

                        $("#recordsAAvg").html("<strong>"+json['avgA']+"</strong>");
                        $("#recordsAMax").html("<strong>"+json['maxA']+"</strong><br><span style='font-size:0.8em'>"+json['maxAtime']+"</span>");
                        $("#recordsAMin").html("<strong>"+json['minA']+"</strong><br><span style='font-size:0.8em'>"+json['minAtime']+"</span>");

                        $("#recordsR").html("<strong>"+json['totalR']+"</strong>");
                        $("#recordsR2").html(json['R2']);
                        $("#recordsR3").html(json['R3']);
                        $("#recordsR2Label").text(json['R2Label']);
                        $("#recordsR3Label").text(json['R3Label']);
                        <?php
                            if($solarSensor){
                        ?>
                            $("#recordsSAvg").html("<strong>"+json['avgS']+"</strong>");
                            $("#recordsSMax").html("<strong>"+json['maxS']+"</strong><br><span style='font-size:0.8em'>"+json['maxStime']+"</span>");
                            $("#recordsSMin").html(json['minS']);
                        <?php
                            }
                        ?>
                        $("#historyLoading").html("");
                    },
                });
            }
            $("#historySelector").change(function(){
                period = $(this).val();
                stationData(period);
            });
        </script>
        <script>
			 // Smooth scrolling
			$(function() {
				$('a[href*="#"]:not([href="#"])').click(function() {
				if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
					var target = $(this.hash);
					target = target.length ? target : $('[name=' + this.hash.slice(1) +']');

					if (target.length) {
					$('html, body').animate({
						scrollTop: target.offset().top - 50
					}, 1000, 'easeInOutExpo');

					if ( $(this).parents('.nav-menu').length ) {
						$('.nav-menu .menu-active').removeClass('menu-active');
						$(this).closest('li').addClass('menu-active');
					}

					if ( $('body').hasClass('mobile-nav-active') ) {
						$('body').removeClass('mobile-nav-active');
						$('#mobile-nav-toggle i').toggleClass('fa-times fa-bars');
						$('#mobile-body-overly').fadeOut();
					}
					return false;
					}
				}
				});
			});
			Highcharts.createElement('link', {
				href: 'https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext',
				rel: 'stylesheet',
				type: 'text/css'
			}, null, document.getElementsByTagName('head')[0]);
			Highcharts.theme = {
				chart: {
					backgroundColor: null,
					color: "#<?php echo $color_schemes[$design]['font900']?>",
					style: {
						fontFamily: "'<?php echo $designFont?> Narrow', sans-serif"
					}
				},
				title: {
					style: {
						color: "#<?php echo $graphColor?>",
						textTransform: 'uppercase',
						fontSize: '20px'
					}
				},
				subtitle: {
					style: {
						color: '#E0E0E3',
						textTransform: 'uppercase'
					}
				},
				xAxis: {
					gridLineColor: '#<?php echo $graphColor?>',
					gridLineWidth: 1,
					gridLineDashStyle: 'shortDash',
					labels: {
						style: {
							color: '#<?php echo $graphColor?>',
						}
					},
					lineColor: '#<?php echo $graphColor?>',
					minorGridLineColor: '#<?php echo $graphColor?>',
					tickColor: '#<?php echo $graphColor?>',
					title: {
						style: {
							color: '#<?php echo $graphColor?>',
						}
					}
				},
				yAxis: {
					gridLineColor: '#<?php echo $gridColor?>',
					gridLineWidth: 1,
					gridLineDashStyle: 'shortDash',
					labels: {
						style: {
							color: '#<?php echo $graphColor?>',
						}
					},
					lineColor: '#<?php echo $graphColor?>',
					minorGridLineColor: '#<?php echo $graphColor?>',
					tickColor: '#<?php echo $graphColor?>',
					tickWidth: 1,
					title: {
						style: {
							color: '#<?php echo $graphColor?>',
						}
					}
				},
				tooltip: {
					backgroundColor: '#<?php echo $color_schemes[$design2]['900']?>',
					style: {
						color: '#<?php echo $color_schemes[$design2]['font900']?>'
					}
				},
				plotOptions: {
					series: {
						dataLabels: {
							color: '#<?php echo $color_schemes[$design2]['900']?>'
						},
						marker: {
							lineColor: '#333'
						}
					}
				},
				legend: {
					itemStyle: {
						color: '#<?php echo $graphColor?>'
					},
					itemHoverStyle: {
						color: '#<?php echo $color_schemes[$design2]['500']?>'
					},
					itemHiddenStyle: {
						color: '#999999'
					},
				},
				credits: {
					style: {
						color: '#666'
					},
					text: '<?php echo $pageName." @ Meteotemplate"?>',
					href: '<?php echo $pageURL.$path?>'
				},
				labels: {
					style: {
						color: '#707073'
					}
				},
			};

			// Apply the theme
			Highcharts.setOptions(Highcharts.theme);
		</script>
		<script>
			$("#parameterSelectorInteractiveGraph").change(function(){
				param = $("#parameterSelectorInteractiveGraph").val();
				parameterSetInteractiveGraph(param);
				if(param=="B"){
					$("#groupingSelectorInteractiveGraph").val("all");
					$("#groupingSelectorInteractiveGraph").hide();
				}
				else{
					$("#groupingSelectorInteractiveGraph").show();
				}
			})
			$("#groupingSelectorInteractiveGraph").change(function(){
				param = $("#groupingSelectorInteractiveGraph").val();
				valueSetInteractiveGraph(param);
			})
			$("#intervalSelectorInteractiveGraph").change(function(){
				param = $("#intervalSelectorInteractiveGraph").val();
				intervalSetInteractiveGraph(param);
			})
			function parameterSetInteractiveGraph(x){
				$("#chosenParameterInteractiveGraph").val(x);
				graphInteractiveGraph();
			}
			function valueSetInteractiveGraph(x){
				$("#chosenValueInteractiveGraph").val(x);
				graphInteractiveGraph();
			}
			function intervalSetInteractiveGraph(x){
				$("#chosenIntervalInteractiveGraph").val(x);
				graphInteractiveGraph();
			}
			function graphInteractiveGraph() {
				$('#spinnerInteractiveGraph').show();
				// Global graph options
				Highcharts.setOptions({
					global: {
						useUTC: true
					},
					lang: {
						months: ['<?php echo lang('january','c')?>', '<?php echo lang('february','c')?>', '<?php echo lang('march','c')?>', '<?php echo lang('april','c')?>', '<?php echo lang('may','c')?>', '<?php echo lang('june','c')?>', '<?php echo lang('july','c')?>', '<?php echo lang('august','c')?>', '<?php echo lang('september','c')?>', '<?php echo lang('october','c')?>', '<?php echo lang('november','c')?>', '<?php echo lang('december','c')?>'],
						shortMonths: ['<?php echo lang('janAbbr','c')?>', '<?php echo lang('febAbbr','c')?>', '<?php echo lang('marAbbr','c')?>', '<?php echo lang('aprAbbr','c')?>', '<?php echo lang('mayAbbr','c')?>', '<?php echo lang('junAbbr','c')?>', '<?php echo lang('julAbbr','c')?>', '<?php echo lang('augAbbr','c')?>', '<?php echo lang('sepAbbr','c')?>', '<?php echo lang('octAbbr','c')?>', '<?php echo lang('novAbbr','c')?>', '<?php echo lang('decAbbr','c')?>'],
						weekdays: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
						resetZoom: ['<?php echo lang('default zoom','c')?>']
					}
				});
				// Individual graphs options based on parameter
				optionsT = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x',
						backgroundColor: null,
					},
					title: {
						text:  '<?php echo lang('temperature',"c") ?>',

					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang("temperature","c") ?>'
						},
						labels: {
							format: '{value} °<?php echo $displayTempUnits ?>'
						},
						<?php
							if($showFreezing){
						?>
							plotLines: [{
								<?php
									if($showFreezingColor=="default"){
								?>
									color: "#<?php echo $graphColor?>",
								<?php
									}
									else{
								?>
									color: "<?php echo $showFreezingColor?>",
								<?php
									}
								?>
				                width: 2,
								dashStyle: 'Dot',
								<?php
									if($displayTempUnits=="C"){
										echo "value: 0";
									}
									else{
										echo "value: 32";
									}
				                ?>
							}]
						<?php
							}
						?>
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					tooltip: {
						shared: true
					},
					series: [
						{
							name: '',
							data: [],
							color: "#000",
                            lineWidth: 4
						},
						{
							name: '',
							data: [],
							color: "#000",
							dashStyle: 'ShortDot',
                            lineWidth: 2
						},
						{
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#999",
							data: []
						}
					]
				};
				optionsH = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('humidity','c') ?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('humidity','c') ?>'
						},
						labels: {
							format: '{value} %'
						},
						max: 100
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '<?php echo lang('humidity','c') ?>',
							data: [],
							color: "#000",
						},
						{
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#999",
						}
					]
				};
				optionsD = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('dew point','c') ?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('dew point','c') ?>'
						},
						labels: {
							format: '{value} <?php echo unitFormatter($displayTempUnits)?>'
						},
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '<?php echo lang('dew point','c') ?>',
							data: [],
							color: "#000",
						},
						{
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#999",
							data: []
						}
					]
				};
				optionsP = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
								text:  '<?php echo lang('pressure','c') ?>'
					},
					legend: {
								enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('pressure','c') ?>'
						},
						labels: {
							format: '{value} <?php echo $displayPressUnits ?>'
						}
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '<?php echo lang('pressure','c') ?>',
							data: [],
							color: "#000",
						},
						{
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#999",
							data: []
						}
					]
				};
				optionsS = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('solar radiation','c') ?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('solar radiation','c') ?>'
						},
						labels: {
							format: '{value} W/m2'
						},
						min: 0
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '<?php echo lang('solar radiation','c') ?>',
							data: [],
							color: "#000",
						},
						{
							type: 'areasplinerange',
							name: '<?php echo lang('range','c')?>',
							color: "#999",
							data: []
						}
					]
				};

				optionsW = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
								text:  '<?php echo lang('wind','c') ?>'
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('wind','c') ?>'
						},
						labels: {
							format: '{value} <?php echo $displayWindUnits ?>'
						},
						min: 0
					},
					tooltip: {
						shared: true
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '',
							data: [],
							color: "#000",
						},
						{
							name: '',
							data: [],
							color: "#000",
							dashStyle: 'ShortDot'
						}
					]
				};

				optionsR = {
				   chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'area',
						zoomType: 'x'
					},
					title: {
								text:  '<?php echo lang('cumulative daily precipitation','c') ?>'
					},
					legend: {
								enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						}
					},
					yAxis: {
						title: {
							text: '<?php echo lang('precipitation','c') ?>'
						},
						labels: {
							format: '{value} <?php echo $displayRainUnits ?>'
						},
						min: 0
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: false
							}
						},
						area: {
							fillOpacity: 0.3
						}
					},
					series: [{
						name: '<?php echo lang('precipitation','c') ?>',
						color: "#000",
						data: []
				   }]
				};
				optionsB = {
					chart : {
						renderTo : 'graphInteractiveGraph',
						type : 'spline',
						zoomType: 'x'
					},
					title: {
						text:  '<?php echo lang('bearing','c') ?>'
					},
					legend: {
						enabled: false
					},
					xAxis: {
						type: 'datetime',
						title: {
							text: null
						},
						dateTimeLabelFormats: {
							millisecond: '%H:%M:%S.%L',
							second: '%H:%M:%S',
							minute: '%H:%M',
							hour: '<?php echo $graphTimeFormat ?>',
							day: '<?php echo $graphDateFormat ?>',
							week: '<?php echo $graphDateFormat ?>',
							month: '%b / %y',
							year: '%Y'
						},
						
					},
					yAxis: {
						title: {
							text: '<?php echo lang('bearing','c') ?>'
						},
						labels: {
							format: '{value}°'
						},
						min: 0,
						max: 360,
						endOnTick: false,
						tickInterval: 40
					},
					plotOptions: {
						series: {
							animation: {
								duration: 3000
							},
							marker: {
								enabled: true
							}
						},
						areasplinerange:{
							fillOpacity: 0.5
						}
					},
					series: [
						{
							name: '<?php echo lang('dew point','c') ?>',
							data: [],
							lineWidth: 0,
							color: "#000",
						}
					]
				};
				// get selected parameters
				parameter = $("#chosenParameterInteractiveGraph").val();
				value = $("#chosenValueInteractiveGraph").val();
				interval = $("#chosenIntervalInteractiveGraph").val();
				$('#spinnerInteractiveGraph').show();
				// get data based on user selection
				if(parameter=="T"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");
							optionsT.series[0].name = json['name1'];
							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryT = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryT];
								rowDate.push(value);
							}
							optionsT.series[0].data = rowDate;

							optionsT.series[1].name = json['name2'];
							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryT = eval(json['data2'][i][1]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryT];
								rowDate.push(value);
							}
							optionsT.series[1].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data3'].length; i++){
								temporaryT = eval(json['data3'][i][1]);
								temporaryT2 = eval(json['data3'][i][2]);
								temporaryDate = json['data3'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryT,temporaryT2];
								rowDate.push(value);
							}
							optionsT.series[2].data = rowDate;

							chart = new Highcharts.Chart(optionsT);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="H"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");
							optionsH.series[0].name = json['name1'];
							optionsH.series[1].name = json['name2'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryH = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryH];
								rowDate.push(value);
							}
							optionsH.series[0].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryH = eval(json['data2'][i][1]);
								temporaryH2 = eval(json['data2'][i][2]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryH,temporaryH2];
								rowDate.push(value);
							}
							optionsH.series[1].data = rowDate;

							chartH = new Highcharts.Chart(optionsH);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="D"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							optionsD.series[0].name = json['name1'];
							optionsD.series[1].name = json['name2'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryD = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryD];
								rowDate.push(value);
							}
							optionsD.series[0].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryD = eval(json['data2'][i][1]);
								temporaryD2 = eval(json['data2'][i][2]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryD,temporaryD2];
								rowDate.push(value);
							}
							optionsD.series[1].data = rowDate;

							chartD = new Highcharts.Chart(optionsD);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="P"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							optionsP.series[0].name = json['name1'];
							optionsP.series[1].name = json['name2'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryP = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryP];
								rowDate.push(value);
							}
							optionsP.series[0].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryP = eval(json['data2'][i][1]);
								temporaryP2 = eval(json['data2'][i][2]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryP,temporaryP2];
								rowDate.push(value);
							}
							optionsP.series[1].data = rowDate;

							chartP = new Highcharts.Chart(optionsP);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="S"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							optionsS.series[0].name = json['name1'];
							optionsS.series[1].name = json['name2'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryS = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryS];
								rowDate.push(value);
							}
							optionsS.series[0].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryS = eval(json['data2'][i][1]);
								temporaryS2 = eval(json['data2'][i][2]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryS,temporaryS2];
								rowDate.push(value);
							}
							optionsS.series[1].data = rowDate;

							chartS = new Highcharts.Chart(optionsS);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="R"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							rowDate = new Array();
							for(i=0; i < json['data'].length; i++){
								temporaryR = eval(json['data'][i][1]);
								temporaryDate = json['data'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryR];
								rowDate.push(value);
							}
							optionsR.series[0].data = rowDate;
							chartR = new Highcharts.Chart(optionsR);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="W"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							optionsW.series[0].name = json['name1'];
							optionsW.series[1].name = json['name2'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryW = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryW];
								rowDate.push(value);
							}
							optionsW.series[0].data = rowDate;

							rowDate = new Array();
							for(i=0; i < json['data2'].length; i++){
								temporaryG = eval(json['data2'][i][1]);
								temporaryDate = json['data2'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryG];
								rowDate.push(value);
							}
							optionsW.series[1].data = rowDate;

							chartW = new Highcharts.Chart(optionsW);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
				if(parameter=="B"){
					$.ajax({
						url : "graphAjax.php?parameter="+parameter+"&value="+value+"&interval="+interval,
						dataType : 'json',
						success : function (json) {
							$("#graphInteractiveGraph").html("");

							optionsB.series[0].name = json['name1'];

							rowDate = new Array();
							for(i=0; i < json['data1'].length; i++){
								temporaryW = eval(json['data1'][i][1]);
								temporaryDate = json['data1'][i][0];
								year = temporaryDate[0];
								month = eval(temporaryDate[1]-1);
								day = temporaryDate[2];
								hour = temporaryDate[3];
								minute = temporaryDate[4];
								datum = Date.UTC(year, month, day, hour, minute);
								value = [datum,temporaryW];
								rowDate.push(value);
							}
							optionsB.series[0].data = rowDate;

							chartB = new Highcharts.Chart(optionsB);
							$('#spinnerInteractiveGraph').hide();
						}
					});
				}
			}
			$(document).ready(function() {
				$('#spinnerInteractiveGraph').hide();
				graphInteractiveGraph();
			});
		</script>
    </body> 
</html>