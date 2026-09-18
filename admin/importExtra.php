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
	#	Data Import
	#
	############################################################################

	require("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    // load api extra sensor settings
    if(!file_exists("../update/apiSettings.txt")){
        die("First go to the API settings in the update setup and specify which extra parameters you want to log.");
    }

    $extraAPIRaw = json_decode(file_get_contents("../update/apiSettings.txt"),true);
    foreach($extraAPIRaw as $extraParam=>$extraValue){
        if($extraValue==1){
            $extraSensors[] = $extraParam;
        }
    }

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader();?>
		<style>
			.firstCell{
				text-align:left;
				vertical-align:top;
				font-weight:bold;
				font-variant:small-caps;
				font-size:1.1em;
			}
			.secondCell{
				text-align:left;
				vertical-align:top;
			}
			.thirdCell{
				text-align:left;
				font-size:0.9em;
			}
			.dateTimeDiv{
				display:none;
				padding-top:10px;
				padding-bottom:10px;
			}
			.importIcon{
				opacity: 0.8;
				cursor: pointer;
				width: 130px;
			}
			.importIcon:hover{
				opacity:1;
			}
			.mainImportDiv{
				display:none;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%;position:relative">
				<form action="saveImportExtra.php" method="POST" target="_blank">
				<h1>Extra Data Import</h1>
				<p>This page lets you import data to the Meteotemplate database with extra parameters. Make sure you read and follow the instructions <strong>very carefully</strong>.</p>
				<br>
				<div id="importFileDiv" class="mainImportDiv">
					<h3>Data Import from txt/CSV file</h3>
					<p>The import will work for almost any file because you can very precisely define its structure. The condition is that the file contains the values necessary and that each line has one set of data poitns, including date and time of measurement.</p>
					<p>Another VERY IMPORTANT thing to remember: <strong>In PHP, numbering starts with a 0, not 1!!!</strong>. This means that when you count the field numbers, do not forget to subtract one. The very first field in the line is field 0, second one is 1 etc.</p>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td class="firstCell" style="width:150px">
								URL (ideally relative path)
							</td>
							<td class="secondCell" colspan="2">
								<input id="filePath" class="button2" style="cursor:auto;text-align:left" size="70" value="" name="filePath">
								<br>
								First thing to do is specify the path to the file. You can either use path relative to this file (i.e. to your template_root/install/) or use the URL (http(s)://...).
							</td>
						</tr>
						<tr>
							<td class="firstCell" style="width:150px">
								Field delimiter
							</td>
							<td class="secondCell">
								<select id="fieldDelimiter"class="button2" style="width:160px" name="fieldDelimiter">
									<option value="semicolon" selected> ; (semi-colon)</option>
									<option value="comma"> , (comma)</option>
									<option value="space">&nbsp;&nbsp;(space)</option>
									<option value="tab">  (tab)</option>
									<option value="colon"> : (colon)</option>
									<option value="vertical"> | (vertical bar)</option>
								</select>
							</td>
							<td class="thirdCell">
								Specify the field delimiter - the symbol, which separates the individual fields.
							</td>
						</tr>
						<tr>
							<td class="firstCell" style="width:150px">
								Decimal separator
							</td>
							<td class="secondCell">
								<select id="decimalSeparator"class="button2" name="decimalSeparator">
									<option value="period"> . (period)</option>
									<option value="comma"> , (comma)</option>
								</select>
							</td>
							<td class="thirdCell">
								Specify the decimal separator. NOTE: If you specify a comma (,), then it is essential that the comma is not used in any other context in the file. For example, if your decimal separator is a comma, then you cannot have the field delimiter also a comma. If that happened the script would not be able to tell which comma is which.
							</td>
						</tr>
						<tr>
							<td class="firstCell" style="width:150px">
								Header row
							</td>
							<td class="secondCell">
								<select id="fileHeader"class="button2" name="fileHeader">
									<option value="0" selected>No</option>
									<option value="1">Yes</option>
								</select>
							</td>
							<td class="thirdCell">
								Select "yes" if the first row of the file contains column headers and the actual values start on line 2.
								<br><br>
							</td>
						</tr>
						<tr>
							<td class="firstCell" style="width:150px">
								Import type
							</td>
							<td class="secondCell">
								<select id="importType"class="button2" style="width:120px" name="importType">
									<option value="skip" selected>Skip existing</option>
									<option value="overwrite">Overwrite</option>
								</select>
							</td>
							<td class="thirdCell">
								This is very important! Select what to do if the import finds a date/time combination that already exists in the database. You can choose what should be done then - either this will be skipped (the existing value will stay in the database) or it can be overwritten (the value from the import file will replace the original value in the database).
								<br><br>
							</td>
						</tr>
					</table>
					<hr>
						<p>
							You can use the link below, which will try to load the file and show you the field numbers. However, make sure you have filled in correct path and field delimiter above.
						</p>
						<input type="button" class="button2" value="Show Field Numbers" id="showFields">
					<hr>
					<h3 style="font-size:1.2em">Date and time</h3>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td class="firstCell" style="width:150px">
								Date and time field(s)
							</td>
							<td class="secondCell">
								<select id="dateTimeType"class="button2" name="dateTimeType">
									<option value="" selected>--Select--</option>
									<option value="single">Date and time in a single field</option>
									<option value="double">Date in one field, time in second field</option>
									<option value="separateDate">Date in separate fields (year, month, day) and time in one field</option>
									<option value="separateTime">Date in one field and time in separate fields (hour, minute)</option>
									<option value="separate">Everything in separate fields (year, month, day, hour, minute)</option>
								</select>
							</td>
							<td class="thirdCell">
								Here you must select how the date and time is specified in the file. Choose the appropriate type and then based on that fill in the details below.
							</td>
						</tr>
					</table>
					<p>
						In case your date and time are not all in separate fields, you must tell the script what is the date/time format of the fields. To do this, you must use the PHP date syntax. The complete documentation can be found <a href="http://www.w3schools.com/php/func_date_date.asp" target="_blank">here</a>.
					</p>
					<br>
					IMPORTANT!!! PHP date formats are case-sensitive, this means that Y and y are not the same thing!!!
					<br><br>
					Here are some examples:
					<table style="width:98%;margin:0 auto" cellpadding="2" cellspacing="2">
						<tr>
							<td style="width:25%;text-align:left">
								Date: <?php echo date("Y-m-d")?><br>
								Format: Y-m-d
							</td>
							<td style="width:25%;text-align:left">
								Date: <?php echo date("m/d/Y")?><br>
								Format: m/d/Y
							</td>
							<td style="width:25%;text-align:left">
								Date: <?php echo date("d.m.Y")?><br>
								Format: d.m.Y
							</td>
							<td style="width:25%;text-align:left">
								Date: <?php echo date("d. m. Y")?><br>
								Format: d. m. Y
							</td>
						</tr>
						<tr>
							<td style="width:20%;text-align:left">
								Time: <?php echo date("H:i")?><br>
								Format: H:i
							</td>
							<td style="width:20%;text-align:left">
								Time: <?php echo date("H:i:s")?><br>
								Format: H:i:s
							</td>
							<td style="width:20%;text-align:left">
								Time: <?php echo date("H.i")?><br>
								Format: H.i
							</td>
							<td style="width:20%;text-align:left">
								Date/time: <?php echo date("Y-m-d H:i")?><br>
								Format: Y-m-d H:i
							</td>
							<td style="width:20%;text-align:left">
								Time: <?php echo date("Y/m/d H.i")?><br>
								Format: Y/m/d H.i
							</td>
						</tr>
					</table>
					<br>
					<div id="divSingle" class="dateTimeDiv">
						<strong>DATE AND TIME IN ONE FIELD</strong><br>
						<input id="dtSingleField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSingleField"> Date and time field number
						<br><br>
						Date and time format
						<br>
						<input id="dtSingleFormat" class="button2" style="cursor:auto;text-align:left" size="20" value="Y/m/d H:i" name="dtSingleFormat">
					</div>
					<div id="divDouble" class="dateTimeDiv">
						<strong>DATE AND TIME IN TWO FIELDS</strong><br>
						<input id="dtDoubleDateField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtDoubleDateField">  Date field number
						<br><br>
						<input id="dtDoubleTimeField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtDoubleTimeField">  Time field number
						<br><br>
						Date format
						<br>
						<input id="dtDoubleDateFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="Y/m/d" name="dtDoubleDateFormat">
						<br><br>
						Time format
						<br>
						<input id="dtDoubleTimeFormat" class="button2" style="cursor:auto;text-align:left" size="8" value="H:i" name="dtDoubleTimeFormat">
					</div>
					<div id="divSeparateTime" class="dateTimeDiv">
						<strong>DATE IN ONE FIELD, TIME SEPARATE</strong><br>
						<input id="dtSeparateTimeDateField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateTimeDateField">  Date field number
						<br><br>
						<input id="dtSeparateTimeHourField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateTimeHourField">  Hour field number
						<br><br>
						<input id="dtSeparateTimeMinuteField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateTimeMinuteField">  Minute field number
						<br><br>
						Date format
						<br>
						<input id="dtSeparateTimeDateFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="Y/m/d" name="dtSeparateTimeDateFormat">
					</div>
					<div id="divSeparateDate" class="dateTimeDiv">
						<strong>DATE IN SEPARATE FIELDS, TIME IN ONE FIELD</strong><br>
						<input id="dtSeparateDateTimeField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateDateTimeField">  Time field number
						<br><br>
						<input id="dtSeparateDateYearField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateDateYearField">  Year field number
						<br><br>
						<input id="dtSeparateDateMonthField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateDateMonthField">  Month field number
						<br><br>
						<input id="dtSeparateDateDayField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateDateDayField">  Day field number
						<br><br>
						Date format
						<br>
						<input id="dtSeparateDateTimeFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="H:i" name="dtSeparateDateTimeFormat">
					</div>
					<div id="divSeparate" class="dateTimeDiv">
						<strong>ALL IN SEPARATE FIELDS</strong><br>
						<input id="dtSeparateYearField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateYearField">  Year field number
						<br><br>
						<input id="dtSeparateMonthField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateMonthField">  Month field number
						<br><br>
						<input id="dtSeparateDayField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateDayField">  Day field number
						<br><br>
						<input id="dtSeparateHourField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateHourField">  Hour field number
						<br><br>
						<input id="dtSeparateMinuteField" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="dtSeparateMinuteField">  Minute field number
					</div>
					<hr>
					<h3 style="font-size:1.2em">Variables</h3>
					<p>Now you have to specify the field numbers for the variables and their units. The parameters correspond to what you have specified in the API settings for the extra parameters.</p>
					<br>
					<table cellspacing="4" cellpadding="4" class="table">
                        <?php 
                            foreach($extraSensors as $extraSensor){
                                if($extraSensor=="UV"){
                                    $sensor['name'] = "UV";
                                    $sensor['units'] = array("-");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="TIN"){
                                    $sensor['name'] = "Indoor temperature";
                                    $sensor['units'] = array("C","F");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="HIN"){
                                    $sensor['name'] = "Indoor humidity";
                                    $sensor['units'] = array("%");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="SN"){
                                    $sensor['name'] = "Snowfall";
                                    $sensor['units'] = array("cm","mm","in");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="SD"){
                                    $sensor['name'] = "Snow depth";
                                    $sensor['units'] = array("cm","mm","in");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="NL"){
                                    $sensor['name'] = "Noise level";
                                    $sensor['units'] = array("dB");
                                    $sensor['id'] = $extraSensor;
                                }
                                if($extraSensor=="L"){
                                    $sensor['name'] = "Lightning";
                                    $sensor['units'] = array("-");
                                    $sensor['id'] = $extraSensor;
                                }
								if($extraSensor=="SS"){
                                    $sensor['name'] = "Sun shine";
                                    $sensor['units'] = array("h");
                                    $sensor['id'] = $extraSensor;
                                }
                                for($i=1;$i<=4;$i++){
                                    if($extraSensor=="T".$i){
                                        $sensor['name'] = "Extra temperature ".$i;
                                        $sensor['units'] = array("C","F");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="H".$i){
                                        $sensor['name'] = "Extra humidity ".$i;
                                        $sensor['units'] = array("%");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="TS".$i){
                                        $sensor['name'] = "Soil temperature ".$i;
                                        $sensor['units'] = array("C","F");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="LT".$i){
                                        $sensor['name'] = "Leaf temperature ".$i;
                                        $sensor['units'] = array("C","F");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="SM".$i){
                                        $sensor['name'] = "Soil moisture ".$i;
                                        $sensor['units'] = array("-");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="LW".$i){
                                        $sensor['name'] = "Leaf Wetness ".$i;
                                        $sensor['units'] = array("-");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="CO2_".$i){
                                        $sensor['name'] = "CO2 sensor ".$i;
                                        $sensor['units'] = array("ppm");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="CO_".$i){
                                        $sensor['name'] = "CO sensor ".$i;
                                        $sensor['units'] = array("ppm");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="NO2_".$i){
                                        $sensor['name'] = "NO2 sensor ".$i;
                                        $sensor['units'] = array("ppm");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="SO2_".$i){
                                        $sensor['name'] = "SO2 sensor ".$i;
                                        $sensor['units'] = array("ppb");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="O3_".$i){
                                        $sensor['name'] = "O3 sensor ".$i;
                                        $sensor['units'] = array("ppm");
                                        $sensor['id'] = $extraSensor;
                                    }
                                    if($extraSensor=="PP".$i){
                                        $sensor['name'] = "Particulate pollution sensor ".$i;
                                        $sensor['units'] = array("ug/m3");
                                        $sensor['id'] = $extraSensor;
                                    }
                                }
                        ?>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<?php echo $sensor['name'];?>
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="field<?php echo $sensor['id']?>" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="field<?php echo $sensor['id']?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="units<?php echo $sensor['id']?>"class="button2" name="units<?php echo $sensor['id']?>">3
                                    <?php 
                                        foreach($sensor['units'] as $unit){
                                    ?>
									    <option value="<?php echo $unit?>"><?php echo $unit?></option>
									<?php 
                                        }
                                    ?>
								</select>
							</td>
						</tr>
						<?php 
                            }
                        ?>
					</table>

					<p>Now you should be ready to start the import. Before you do so, however, click the Test Import button. This will load the file like the import script, but will not yet try to save the data to database and instead show you what would be inserted. Make sure that everything looks ok and if so, you can click the actual Import Data button.</p>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testFileImport" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test Import">
						<input type="button" id="importFile" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Import Data">
					</div>
					<br><br>
				</div>
				<br><br>
				<input type="submit" class="button2" value="Save import settings">
				</form>
				<br><br>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$("#testFileImport").click(function(){
				fileImport("test");
			})
			$("#importFile").click(function(){
				fileImport("import");
			})
			$("#showFields").click(function(){
				path = encodeURI($("#filePath").val());
				delimiter = encodeURI($("#fieldDelimiter").val());
				fileHeader = encodeURI($("#fileHeader").val());
				url = "showFields.php?path="+path+"&delimiter="+delimiter+"&fileHeader="+fileHeader;
				window.open(url);
			})
			$("#dateTimeType").change(function(){
				$(".dateTimeDiv").hide();
				type = $("#dateTimeType").val();
				if(type=="single"){
					$("#divSingle").show();
				}
				if(type=="double"){
					$("#divDouble").show();
				}
				if(type=="separateTime"){
					$("#divSeparateTime").show();
				}
				if(type=="separateDate"){
					$("#divSeparateDate").show();
				}
				if(type=="separate"){
					$("#divSeparate").show();
				}
			})
			$("#importFileDiv").show();
			function fileImport(typeFile){
				path = encodeURI($("#filePath").val());
				delimiter = encodeURI($("#fieldDelimiter").val());
				separator = encodeURI($("#decimalSeparator").val());
				fileHeader = encodeURI($("#fileHeader").val());
				importType = encodeURI($("#importType").val());

                dateformat = encodeURI($("#dateTimeType").val());

				dtSingleField = encodeURI($("#dtSingleField").val());
				dtSingleFormat = encodeURI($("#dtSingleFormat").val());
				dtDoubleDateField = encodeURI($("#dtDoubleDateField").val());
				dtDoubleTimeField = encodeURI($("#dtDoubleTimeField").val());
				dtDoubleDateFormat = encodeURI($("#dtDoubleDateFormat").val());
				dtDoubleTimeFormat = encodeURI($("#dtDoubleTimeFormat").val());
				dtSeparateTimeDateField = encodeURI($("#dtSeparateTimeDateField").val());
				dtSeparateTimeHourField = encodeURI($("#dtSeparateTimeHourField").val());
				dtSeparateTimeMinuteField = encodeURI($("#dtSeparateTimeMinuteField").val());
				dtSeparateTimeDateFormat = encodeURI($("#dtSeparateTimeDateFormat").val());
				dtSeparateDateTimeField = encodeURI($("#dtSeparateTimeDateField").val());
				dtSeparateDateYearField = encodeURI($("#dtSeparateTimeHourField").val());
				dtSeparateDateMonthField = encodeURI($("#dtSeparateTimeMinuteField").val());
				dtSeparateDateDayField = encodeURI($("#dtSeparateTimeDateFormat").val());
				dtSeparateDateTimeFormat = encodeURI($("#dtSeparateDateTimeFormat").val());
				dtSeparateYearField = encodeURI($("#dtSeparateYearField").val());
				dtSeparateMonthField = encodeURI($("#dtSeparateMonthField").val());
				dtSeparateDayField = encodeURI($("#dtSeparateDayField").val());
				dtSeparateHourField = encodeURI($("#dtSeparateHourField").val());
				dtSeparateMinuteField = encodeURI($("#dtSeparateMinuteField").val());

                if(typeFile=="test"){
					url = "testExtraImportFile.php";
				}
				if(typeFile=="import"){
					url = "importExtraFile.php";
				}

                urlParams = "?";
				urlParams += "path="+path;
				urlParams += "&delimiter="+delimiter;
				urlParams += "&separator="+separator;
				urlParams += "&fileHeader="+fileHeader;
				urlParams += "&importType="+importType;

                if(dateformat=="single"){
					urlParams += "&dtSingleField="+dtSingleField;
					urlParams += "&dtSingleFormat="+dtSingleFormat;
				}
				else if(dateformat=="double"){
					urlParams += "&dtDoubleDateField="+dtDoubleDateField;
					urlParams += "&dtDoubleTimeField="+dtDoubleTimeField;
					urlParams += "&dtDoubleDateFormat="+dtDoubleDateFormat;
					urlParams += "&dtDoubleTimeFormat="+dtDoubleTimeFormat;
				}
				else if(dateformat=="separateTime"){
					urlParams += "&dtSeparateTimeDateField="+dtSeparateTimeDateField;
					urlParams += "&dtSeparateTimeHourField="+dtSeparateTimeHourField;
					urlParams += "&dtSeparateTimeMinuteField="+dtSeparateTimeMinuteField;
					urlParams += "&dtSeparateTimeDateFormat="+dtSeparateTimeDateFormat;
				}
				else if(dateformat=="separateDate"){
					urlParams += "&dtSeparateDateTimeField="+dtSeparateDateTimeField;
					urlParams += "&dtSeparateDateYearField="+dtSeparateDateYearField;
					urlParams += "&dtSeparateDateMonthField="+dtSeparateDateMonthField;
					urlParams += "&dtSeparateDateDayField="+dtSeparateDateDayField;
					urlParams += "&dtSeparateDateTimeFormat="+dtSeparateDateTimeFormat;
				}
				else if(dateformat=="separate"){
					urlParams += "&dtSeparateYearField="+dtSeparateYearField;
					urlParams += "&dtSeparateMonthField="+dtSeparateMonthField;
					urlParams += "&dtSeparateDayField="+dtSeparateDayField;
					urlParams += "&dtSeparateHourField="+dtSeparateHourField;
					urlParams += "&dtSeparateMinuteField="+dtSeparateMinuteField;
				}
				else{}

                urlParams += "&dateformat="+dateformat;

                <?php
                    foreach($extraSensors as $extraSensor){
                ?>
                        units<?php echo $extraSensor?> = encodeURI($("#units<?php echo $extraSensor?>").val());
                        field<?php echo $extraSensor?> = encodeURI($("#field<?php echo $extraSensor?>").val());
                        urlParams += "&units<?php echo $extraSensor?>="+units<?php echo $extraSensor?>;
                        urlParams += "&field<?php echo $extraSensor?>="+field<?php echo $extraSensor?>;
                <?php
                    }
                ?>	

				url = url + urlParams;
				window.open(url);
			}

			<?php
				if(file_exists("importExtraSettings.txt")){
					$settingsFile = json_decode(file_get_contents("importExtraSettings.txt"),true);
					foreach($settingsFile as $parameter=>$value){
			?>
						$("#<?php echo $parameter?>").val("<?php echo $value?>");
			<?php
					}
				}
			?>

		</script>
	</body>
</html>
