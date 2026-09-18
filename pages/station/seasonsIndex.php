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
    #	Plugin Name
    #
    # 	Plugin description
    #
    ############################################################################
    #	Version and change log
    #
    # 	v1.0 	2016-M-Y	Initial release
    #
    ############################################################################

    include("../../config.php");
    include($baseURL."css/design.php");
    include($baseURL."header.php");

    // check one year of data
    $result = mysqli_query($con,"
        SELECT DateTime
        FROM alldata
        ORDER BY DateTime
        LIMIT 1
    "
    );
    while($row = mysqli_fetch_array($result)){
        $minimumDBDate = strtotime($row["DateTime"]);
    }
    $result = mysqli_query($con,"
        SELECT DateTime
        FROM alldata
        ORDER BY DateTime DESC
        LIMIT 1
    "
    );
    while($row = mysqli_fetch_array($result)){
        $maximumDBDate = strtotime($row["DateTime"]);
    }

    $oneYear = 60 * 60 * 24 * 365;
    $span = $maximumDBDate - $minimumDBDate;

    if($span>$oneYear){
        $dbOK = true;
    }
    else{
        $dbOK = false;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $pageName?></title>
    <?php metaHeader()?>
    <script src="//code.highcharts.com/highcharts.js"></script>
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/exporting.js"></script>
    <script type="text/javascript" src="<?php echo $pageURL.$path?>scripts/jquery.tablesorter.js"></script>
    <style>
        .statIcon{
            padding:15px;
            cursor: pointer;
            opacity: 0.85;
            font-size: 2.8em;
            color: white;
        }
        .statIcon:hover{
            opacity: 1;
        }
        .sort{
            cursor: pointer;
            opacity: 0.8;
        }
        .sort:hover{
            opacity: 1;
        }
        .inner-resizer {
            padding: 10px;
        }
        .resizer {
            margin: 0 auto;
            width: 98%;
        }
        .times{
            font-size: 0.8em;
        }
        .timeOpener{
            opacity: 0.8;
            cursor: pointer;
            font-size: 0.8em;
            padding-left: 3px;
        }
        .table tfoot td{
            background: #<?php echo $color_schemes[$design2]['900'];?>
        }
        .graphSwitcher{
            opacity: 0.8;
            cursor: pointer;
            font-size: 1.6em;
        }
        .graphSwitcher:hover, .timeOpener:hover, .seasonOpener:hover,.parameterOpenerDiv:hover{
            opacity: 1.0
        }
        .table{
            table-layout: fixed;
        }
        .mainSeasonDiv, .dayDiv{
            width:100%;
            display:none;
            padding-bottom: 160px;
        }
        .seasonOpener{
            opacity: 0.8;
            cursor: pointer;
            font-size: 4em;
        }
        .parameterOpenerDiv{
            opacity: 0.8;
            cursor: pointer;
            font-size: 2em;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
</head>
<body>
    <div id="main_top">
        <?php bodyHeader();?>
        <?php include($baseURL."menu.php");?>
    </div>
    <div id="main" style="text-align:center">
        <br />
        <?php
            if($stationLat>=0){
        ?>
                <span class="mticon-spring" style="font-size:2em"></span>
                <span class="mticon-summer" style="font-size:2em"></span>
                <span class="mticon-autumn" style="font-size:2em"></span>
                <span class="mticon-snow" style="font-size:2em"></span>
        <?php
            }
        ?>
        <?php
            if($stationLat<0){
        ?>
                <span class="mticon-autumn" style="font-size:2em"></span>
                <span class="mticon-snow" style="font-size:2em"></span>
                <span class="mticon-spring" style="font-size:2em"></span>
                <span class="mticon-summer" style="font-size:2em"></span>
        <?php
            }
        ?>
        <br />
        <span style="font-size:2.5em;font-weight:bold;font-variant:small-caps"><?php echo lang('seasons','c');?></span>

        <?php
            if($dbOK){
        ?>
            <br><br />
            <span class="mticon-temp statIcon tooltip" data-id="T" title="<?php echo lang('temperature','c')?>"></span>
            <span class="mticon-day statIcon tooltip" data-id="days" title="<?php echo lang('days','c')?>"></span>
            <span class="mticon-apparent statIcon tooltip" data-id="A" title="<?php echo lang('apparent temperature','c')?>"></span>
            <span class="mticon-dewpoint statIcon tooltip" data-id="D" title="<?php echo lang('dewpoint','c')?>"></span>
            <span class="mticon-humidity statIcon tooltip" data-id="H" title="<?php echo lang('humidity','c')?>"></span>
            <span class="mticon-pressure statIcon tooltip" data-id="P" title="<?php echo lang('pressure','c')?>"></span>
            <span class="mticon-wind statIcon tooltip" data-id="W" title="<?php echo lang('wind speed','c')?>"></span>
            <span class="mticon-gust statIcon tooltip" data-id="G" title="<?php echo lang('wind gust','c')?>"></span>
            <?php
                if($solarSensor){
            ?>
                <span class="mticon-sun statIcon tooltip" data-id="S" title="<?php echo lang('solar radiation','c')?>"></span>
            <?php
                }
            ?>
            <br>
            <div id="seasonsDiv" style="width:98%;margin:0 auto;text-align:center"></div>
            <br><br>
        <?php
            }
            else{
                echo "You need to have at least one year of data in the database for this page to work.";
            }
        ?>
    </div>
    <?php include($baseURL."css/highcharts.php");?>
    <?php include($baseURL."footer.php");?>
    <script>
        $(".statIcon").click(function(){
            $("#seasonsDiv").html("<br><br><img src='<?php echo $pageURL.$path?>icons/logo.png' style='width:100px' class='mtSpinner'>");
            id = $(this).attr("data-id");
            if(id!="days"){
                $("#seasonsDiv").load("seasons.php?var="+id);
            }
            else{
                $("#seasonsDiv").load("seasonsDayNumber.php");
            }
        })
    </script>
</body>
</html>
