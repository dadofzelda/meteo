<?php
	
	############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Bulk database changes
	#
	############################################################################
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
            .smallIcon{
                font-size: 1.3em;
            }
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%">
			<h1>Bulk Database Editing</h1>
            <h2>Interval</h2>
            <table style="table-layout:fixed;width:98%;margin:0 auto">
                <tr>
                    <td style="width:50%;text-align:center">
                        <span class="fa fa-hourglass-start tooltip" style="font-size:2.5em" title="<?php echo lang("from",'c')?>"></span>
                        <br><br>
                        <input id="fromY" type="number" min=2000 max=<?php echo date("Y")?> class="button2" size="4" value="<?php echo date("Y")?>">
                        <input id="fromM" type="number" min=1 max=12 class="button2" size="2" value="<?php echo date("m")?>">
                        <input id="fromD" type="number" min=1 max=31 class="button2" size="2" value="<?php echo date("d")?>">
                        <br>
                        <input id="fromH" type="number" min=0 max=24 class="button2" size="2" value="<?php echo date("H")?>"> : 
                        <input id="fromMin" type="number" min=0 max=59 class="button2" size="2" value="<?php echo date("i")?>">
                    </td>
                    <td>
                        <span class="fa fa-hourglass-end tooltip" style="font-size:2.5em" title="<?php echo lang("to",'c')?>"></span>
                        <br><br>
                        <input id="toY" type="number" min=2000 max=<?php echo date("Y")?> class="button2" size="4" value="<?php echo date("Y")?>">
                        <input id="toM" type="number" min=1 max=12 class="button2" size="2" value="<?php echo date("m")?>">
                        <input id="toD" type="number" min=1 max=31 class="button2" size="2" value="<?php echo date("d")?>">
                        <br>
                        <input id="toH" type="number" min=0 max=24 class="button2" size="2" value="<?php echo date("H")?>"> : 
                        <input id="toMin" type="number" min=0 max=59 class="button2" size="2" value="<?php echo date("i")?>">
                    </td>
                </tr>
            </table>
            <h2>Deleting ALL data</h2>
            <div style="width:98%;margin:0 auto;text-align:center">
                <br>
                <input type="button" class="button2" value="DELETE ALL VALUES" id="deleteAll">
                <br><br>
            </div>
            <h2>Deleting sensor data</h2>
            <div style="width:98%;margin:0 auto;text-align:justify">
                <br>
                <p>
                    Select sensors for which you want to delete data. Make sure at least one sensor is maintained. Otherwise use the delete all option above. Leaving only the date will result in errors.
                </p>
                    <table style="table-layout:fixed;width:98%;margin:0 auto">
                        <tr>
                            <td>
                                <span class="mticon-temp smallIcon"></span><br>Temperature<br>
                                <select class="button2 sensorSelector" id="sensorT" data-id="T" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoT">
                                    <input class="button2" id="sensorTVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataTempUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-humidity smallIcon"></span><br>Humidity<br>
                                <select class="button2 sensorSelector" id="sensorH" data-id="H" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoH">
                                    <input class="button2" id="sensorHVal" value="" size="4">&nbsp;%
                                </div>
                            </td>
                            <td>
                                <span class="mticon-pressure smallIcon"></span><br>Pressure<br>
                                <select class="button2 sensorSelector" id="sensorP" data-id="P" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoP">
                                    <input class="button2" id="sensorPVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataPressUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-wind smallIcon"></span><br>Wind speed and gust<br>
                                <select class="button2 sensorSelector" id="sensorW" data-id="W" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoW">
                                    <input class="button2" id="sensorWVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataWindUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-bearing smallIcon"></span><br>Wind direction<br>
                                <select class="button2 sensorSelector" id="sensorB" data-id="B" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoB">
                                    <input class="button2" id="sensorBVal" value="" size="3">&deg;
                                </div>
                            </td>  
                        </tr>
                        <tr>
                            <td colspan="5" style="height:15px"></td>
                        </tr>
                        <tr>
                            <td>
                                <span class="mticon-rain smallIcon"></span><br>Precipitation<br>
                                <select class="button2 sensorSelector" id="sensorR" data-id="R" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoR">
                                    <input class="button2" id="sensorRVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataRainUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-rain smallIcon"></span><br>Rain rate<br>
                                <select class="button2 sensorSelector" id="sensorRR" data-id="RR" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoRR">
                                    <input class="button2" id="sensorRRVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataRainUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-dewpoint smallIcon"></span><br>Dewpoint<br>
                                <select class="button2 sensorSelector" id="sensorD" data-id="D" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoD">
                                    <input class="button2" id="sensorDVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataTempUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-apparent smallIcon"></span><br>Apparent<br>
                                <select class="button2 sensorSelector" id="sensorA" data-id="A" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoA">
                                    <input class="button2" id="sensorAVal" value="" size="6">&nbsp;<?php echo unitFormatter($dataTempUnits)?>
                                </div>
                            </td>
                            <td>
                                <span class="mticon-sun smallIcon"></span><br>Solar radiation<br>
                                <select class="button2 sensorSelector" id="sensorS" data-id="S" style="margin-top:5px">
                                    <option value="0" selected>No change</option>
                                    <option value="1">DELETE ALL</option>
                                    <option value="2">DELETE HIGHER THAN</option>
                                    <option value="3">DELETE LOWER THAN</option>
                                </select>
                                <div class="moreInfo" style="width:100%;display:none" id="moreInfoS">
                                    <input class="button2" id="sensorSVal" value="" size="6">&nbsp;W/m<sup>2</sup>
                                </div>
                            </td>
                        </tr>
                    </table>
                <br>
                <div style="width:98%;margin:0 auto;text-align:center">
                    <input type="button" class="button2" value="DELETE SENSOR VALUES" id="deleteSensors">
                </div>
            </div>
            <br>
            <h2>Fixing database rain</h2>
            <div style="width:98%;margin:0 auto;text-align:justify">
                <br>
                <p>
                    Sometimes when you have short API update intervals and your server is slower or has a temporary glitch, the cache file around midnight might not be deleted in time and used by the subsequent API update. This leads to the day 1 rain being copied over to day 2. The below button will check your database for any such cases and fix them.
                </p>
                <br>
                <input type="button" class="button2" value="CHECK AND FIX RAIN" id="rainCheck">
                <br><br>
            </div>
			<br><br>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>
        <script>
            $(".sensorSelector").change(function(){
                sen = $(this).attr("data-id");
                val = eval($(this).val());
                if(val == 2 || val == 3){
                    $("#moreInfo" + sen).show();
                }
                else{
                    $("#moreInfo" + sen).hide();
                }
            })
            $("#deleteAll").click(function(){
                confirmIt = confirm("Are you sure you want to delete ALL data in the interval specified?");
                if(confirmIt){
                    confirmIt2 = confirm("This action cannot be reverted, this will permanently delete the data from your database.\n\nAre you sure you want to continue?");
                    if(confirmIt2){
                        alert("If this is the first time you run this script it might take longer. Make sure you wait until the page is fully loaded.");
                        fromY = $("#fromY").val();
                        fromM = $("#fromM").val();
                        fromD = $("#fromD").val();
                        fromH = $("#fromH").val();
                        fromMin = $("#fromMin").val();
                        toY = $("#toY").val();
                        toM = $("#toM").val();
                        toD = $("#toD").val();
                        toH = $("#toH").val();
                        toMin = $("#toMin").val();
                        fromDate = fromY+"_"+fromM+"_"+fromD+"_"+fromH+"_"+fromMin;
                        toDate = toY+"_"+toM+"_"+toD+"_"+toH+"_"+toMin;
                        url = "bulkDBProcess.php?type=all&from="+fromDate+"&to="+toDate;
                        openWindow(url, "Deleting all values");
                    }
                }
            });
            $("#deleteSensors").click(function(){
                confirmIt = confirm("Are you sure you want to delete the specified sensor data in the interval specified?");
                if(confirmIt){
                    confirmIt2 = confirm("This action cannot be reverted, this will permanently delete the data from your database.\n\nAre you sure you want to continue?");
                    if(confirmIt2){
                        alert("If this is the first time you run this script it might take longer. Make sure you wait until the page is fully loaded.");
                        fromY = $("#fromY").val();
                        fromM = $("#fromM").val();
                        fromD = $("#fromD").val();
                        fromH = $("#fromH").val();
                        fromMin = $("#fromMin").val();
                        toY = $("#toY").val();
                        toM = $("#toM").val();
                        toD = $("#toD").val();
                        toH = $("#toH").val();
                        toMin = $("#toMin").val();
                        fromDate = fromY+"_"+fromM+"_"+fromD+"_"+fromH+"_"+fromMin;
                        toDate = toY+"_"+toM+"_"+toD+"_"+toH+"_"+toMin;

                        url = "bulkDBProcess.php?type=sensor&from="+fromDate+"&to="+toDate;

                        <?php 
                            $parameters = array("T","H","P","W","A","D","R","RR","S","B");
                            foreach($parameters as $parameter){
                        ?>
                                <?php echo $parameter?> = $("#sensor<?php echo $parameter?>").val();
                                url += "&<?php echo $parameter?>="+<?php echo $parameter?>;
                                val = $("#sensor<?php echo $parameter?>Val").val();
                                url += "&<?php echo $parameter?>val="+val;
                        <?php
                            }
                        ?>
                        openWindow(url, "Deleting sensor values");
                    }
                }
            });
            $("#rainCheck").click(function(){
                url = "bulkDBProcess.php?type=rainCheck"
                openWindow(url, "Rain check");
            });
            function openWindow(url,title){
				dialogHeight = screen.height*0.7;
				dialogWidth = screen.width*0.7;
				var $dialog = $('<div style="overflow:hidden"></div>')
					.html('<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>')
					.dialog({
						autoOpen: false,
						modal: true,
						height: dialogHeight,
						width: dialogWidth,
						title: title,
						show: {
							effect: "fade",
							duration: 400
						},
						hide: {
							effect: "fade",
							duration: 800
						}
					});
				$dialog.dialog('open');
			}
        </script>		
	</body>
</html>
	