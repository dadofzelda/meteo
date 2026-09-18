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
				<form action="saveImport.php" method="POST" target="_blank">
				<h1>Data Import</h1>
				<p>This page lets you import data to the Meteotemplate database. Make sure you read and follow the instructions <strong>very carefully</strong>.</p>
				<br>
				<p>Data can be imported from two different sources:</p>
				<ul>
					<li>file (txt, csv etc.)</li>
					<li>Weather Underground site - currently not working due to recent changes of WU API</li>
				</ul>
				<p>
					If you have the possibility to choose, then only use the WU option if absolutely necessary. It is the least reliable and also takes the longest time. Use data import from a file if possible.
				</p>
				<br>
				<table style="width:100%">
					<tr>
						<td style="width:50%;text-align:center">
							<img src="adminIcons/importFile.png" class="importIcon" id="importFileDivOpener">
						</td>
						<!--<td style="width:50%;text-align:center">
							<img src="adminIcons/importWU.png" class="importIcon" id="importWUDivOpener">
						</td>-->
					</tr>
				</table>
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
					<p>Now you have to specify the field numbers for the variables and their units. The units will be automatically converted to match what you specified in the Main settings for "database units". Just <strong>make sure that you have the limits in main settings set correctly!!!</strong> If for example you are importing pressure in inHg then make sure that the pressure limit in Main settings is not 960 to 1040 (that is the default value if you have not changed it). You would also see this when you run the import test - there would be no value for the pressure. All the parameters must be included, except solar radiation. Solar radiation is optional, <strong> if you do not have a solar sensor, then specify this in the Main settings and the solar data will be ignored.</strong></p>
					<p>If for example you do not have wind gust available, then specify the same field number as average wind.</p>
					<br>
					<table cellspacing="4" cellpadding="4" class="table">
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/temp.png" style="width:25px"><br>Temperature
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldT" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldT">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsT"class="button2" name="unitsT">
									<option value="C">Celsius</option>
									<option value="F">Farenheit</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/humidity.png" style="width:25px"><br>Humidity
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldH" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldH">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: %
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/pressure.png" style="width:25px"><br>Pressure
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldP" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldP">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsP"class="button2" name="unitsP">
									<option value="hpa">hPa</option>
									<option value="inhg">inHg</option>
									<option value="mmhg">mmhg</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/wind.png" style="width:25px"><br>Wind speed
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldW" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldW">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsW"class="button2" name="unitsW">
									<option value="kmh">km/h</option>
									<option value="ms">m/s</option>
									<option value="mph">mph</option>
									<option value="kts">knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/gust.png" style="width:25px"><br>Wind gust
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldG" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldG">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsG"class="button2" name="unitsG">
									<option value="kmh">km/h</option>
									<option value="ms">m/s</option>
									<option value="mph">mph</option>
									<option value="kts">knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/rain.png" style="width:25px"><br>Cumulative daily precipitation
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldR" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldR">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsR"class="button2" name="unitsR">
									<option value="mm">mm</option>
									<option value="in">in</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/rain.png" style="width:25px"><br>Rain rate
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldRR" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldRR">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsRR"class="button2" name="unitsRR">
									<option value="mm">mm/h</option>
									<option value="in">in/h</option>
									<option value="mmmin">mm/min</option>
									<option value="inmin">in/min</option>
									<option value="mm10min">mm/10 min</option>
									<option value="in10min">in/10 min</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/wind.png" style="width:25px"><br>Wind direction
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldB" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldB">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: degrees
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/sun.png" style="width:25px"><br>Solar radiation
								<br>
								<select id="solarData" class="button2" name="solarData">
									<option value="1" <?php if($solarSensor){ echo "selected"; }?>>Enabled</option>
									<option value="0" <?php if(!$solarSensor){ echo "selected"; }?>>Disabled</option>
								</select>
								* overrides Main settings
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldS" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldS">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: W/m2
							</td>
						</tr>
					</table>

					<p>Now you should be ready to start the import. Before you do so, however, click the Test Import button. This will load the file like the import script, but will not yet try to save the data to database and instead show you what would be inserted. Make sure that everything looks ok and if so, you can click the actual Import Data button.</p>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testFileImport" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test Import">
						<input type="button" id="importFile" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Import Data">
					</div>
					<br><br>
				</div>
				<div id="importWUDiv" class="mainImportDiv">
					<h3>Data Import from Weather Underground</h3>
					<p>
						This import option will import data from Weather Underground. Only use this if you have no other possibility. The problem is that WU provides data in XML format on a daily basis, which means the script has to load each day separately. This can take substantial time and depending on your server speed and the maximum PHP execution time set on your server, not all records might load. Select the interval you want to import and then see if all the data is imported at once. The page can be loading for quite some time. If you end up getting a time out error, you need to check which day was last imported.</p>
					<p>
						What this script does is that it creates a little text file on your server in the admin folder, same folder as this script is in. The text file is called importWUCache.txt. After each imported day, it overwrites whatever is in that file with that day's date. This means that if the script times out, you can look inside this file and see, which day was the last successfully imported one and there will also be the date and time when the import was executed, so you need to check it was the import you just did and also see if the day is the last day you wanted to import, or if the script failed before importing all data.
					</p>
					<p>
						Now you need to set up the import.
					</p>
					<hr>
					<input id="WUID" class="button2" size="15" name="WUID">&nbsp;&nbsp;&nbsp;Your Weather Underground ID
					<br><br>
					<p>
						In this next step you need to specify the field numbers and units. Weather Underground is very unexpectable. So the units can be various and you need to tell the template which ones are in the file, so that it can do all the necessary conversions before saving the data to your database. The data will be converted to the units you have set in your Main settings for Database Units.
					</p>
					<p>
						To see what units are used by WU and what the field numbers are, just click the button below (make sure the WU ID is specified in the field above).
						<br>
						<input type="button" class="button2" value="Show Field Numbers" id="showWUFields">
					</p>
					<p>
						Import type:
						<select id="importTypeWU"class="button2" style="width:120px" name="importTypeWU">
							<option value="skip" selected>Skip existing</option>
							<option value="overwrite">Overwrite</option>
						</select>
					</p>
					<hr>
					<h3 style="font-size:1.2em">Variables</h3>
					<table cellspacing="4" cellpadding="4" class="table">
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/temp.png" style="width:25px"><br>Temperature
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUT" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUT">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWUT"class="button2" name="unitsWUT">
									<option value="C">Celsius</option>
									<option value="F">Farenheit</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/humidity.png" style="width:25px"><br>Humidity
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUH" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUH">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: %
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/pressure.png" style="width:25px"><br>Pressure
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUP" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUP">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWUP"class="button2" name="unitsWUP">
									<option value="hpa">hPa</option>
									<option value="inhg">inHg</option>
									<option value="mmhg">mmhg</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/wind.png" style="width:25px"><br>Wind speed
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUW" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUW">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWUW"class="button2" name="unitsWUW">
									<option value="kmh">km/h</option>
									<option value="ms">m/s</option>
									<option value="mph">mph</option>
									<option value="kts">knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/gust.png" style="width:25px"><br>Wind gust
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUG" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUG">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWUG"class="button2" name="unitsWUG">
									<option value="kmh">km/h</option>
									<option value="ms">m/s</option>
									<option value="mph">mph</option>
									<option value="kts">knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/rain.png" style="width:25px"><br>Cumulative daily precipitation
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUR" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUR">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWUR"class="button2" name="unitsWUR">
									<option value="mm">mm</option>
									<option value="in">in</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/rain.png" style="width:25px"><br>Rain rate<br>(for WU "hourly precipitation")
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWURR" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWURR">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units:
								<select id="unitsWURR"class="button2" name="unitsWURR">
									<option value="mm">mm/h</option>
									<option value="in">in/h</option>
									<option value="mmmin">mm/min</option>
									<option value="inmin">in/min</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/wind.png" style="width:25px"><br>Wind direction
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUB" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUB">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: degrees
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$path?>icons/sun.png" style="width:25px"><br>Solar radiation
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldWUS" class="button2" style="cursor:auto;text-align:left" size="3" value="" name="fieldWUS">
								<br>
								* leave blank if disabled in Main settings
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: W/m2
							</td>
						</tr>
					</table>
					<hr>
					<h3 style="font-size:1.2em">Date and time</h3>
					<p>
						Now specify the interval which you want to import. Make sure you read carefully how the import works above. It is quite possible that the import will not finish, this depends on the speed of your server and the speed of WU. The speed will depend on how fast it is possible to retrieve data from WU and then how fast the data can be saved to the database. Usually one month of data in one go is ok. Also note: the "to" date is including. So if you specify for example from 1st January to 2nd January, it will import both days. If you only want to import one day, you would put from 1st January to 1st January. Data is imported from midnight to midnight.
					</p>
					<p>
						<table style="width:100%">
							<tr>
								<td style="width:50%;text-align:center">
									From
									<br><br>
									<select id="wuImportFromY" class="button2">
										<option value="" selected></option>
										<?php
											for($i=date("Y");$i>2000;$i--){
										?>
												<option value="<?php echo $i?>"><?php echo $i?></option>
										<?php
											}
										?>
									</select>
									<select id="wuImportFromM" class="button2">
										<option value="" selected></option>
										<option value="1">January</option>
										<option value="2">February</option>
										<option value="3">March</option>
										<option value="4">April</option>
										<option value="5">May</option>
										<option value="6">June</option>
										<option value="7">July</option>
										<option value="8">August</option>
										<option value="9">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
									</select>
									<select id="wuImportFromD" class="button2">
										<option value="" selected></option>
										<?php
											for($i=1;$i<32;$i++){
										?>
												<option value="<?php echo $i?>"><?php echo $i?></option>
										<?php
											}
										?>
									</select>
								</td>
								<td style="width:50%;text-align:center">
									To
									<br><br>
									<select id="wuImportToY" class="button2">
										<option value="" selected></option>
										<?php
											for($i=date("Y");$i>2000;$i--){
										?>
												<option value="<?php echo $i?>"><?php echo $i?></option>
										<?php
											}
										?>
									</select>
									<select id="wuImportToM" class="button2">
										<option value="" selected></option>
										<option value="1">January</option>
										<option value="2">February</option>
										<option value="3">March</option>
										<option value="4">April</option>
										<option value="5">May</option>
										<option value="6">June</option>
										<option value="7">July</option>
										<option value="8">August</option>
										<option value="9">September</option>
										<option value="10">October</option>
										<option value="11">November</option>
										<option value="12">December</option>
									</select>
									<select id="wuImportToD" class="button2">
										<option value="" selected></option>
										<?php
											for($i=1;$i<32;$i++){
										?>
												<option value="<?php echo $i?>"><?php echo $i?></option>
										<?php
											}
										?>
									</select>
								</td>
							</tr>
						</table>
					</p>
					<br><br>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testWUImport" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test Import">
						<input type="button" id="importWU" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Import Data">
					</div>
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
			$("#testWUImport").click(function(){
				wuImport("test");
			})
			$("#importWU").click(function(){
				wuImport("import");
			})
			$("#showFields").click(function(){
				path = encodeURI($("#filePath").val());
				delimiter = encodeURI($("#fieldDelimiter").val());
				fileHeader = encodeURI($("#fileHeader").val());
				url = "showFields.php?path="+path+"&delimiter="+delimiter+"&fileHeader="+fileHeader;
				window.open(url);
			})
			$("#showWUFields").click(function(){
				id = encodeURI($("#WUID").val());
				url = "showWUFields.php?id="+id;
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
			$("#importFileDivOpener").click(function(){
				$(".mainImportDiv").hide();
				$("#importFileDiv").show();
			})
			$("#importWUDivOpener").click(function(){
				$(".mainImportDiv").hide();
				$("#importWUDiv").show();
			})
			function fileImport(typeFile){
				path = encodeURI($("#filePath").val());
				delimiter = encodeURI($("#fieldDelimiter").val());
				separator = encodeURI($("#decimalSeparator").val());
				fileHeader = encodeURI($("#fileHeader").val());
				importType = encodeURI($("#importType").val());

				solarData = $("#solarData").val()

				unitsT = encodeURI($("#unitsT").val());
				unitsP = encodeURI($("#unitsP").val());
				unitsW = encodeURI($("#unitsW").val());
				unitsG = encodeURI($("#unitsG").val());
				unitsR = encodeURI($("#unitsR").val());
				unitsRR = encodeURI($("#unitsRR").val());

				fieldT = encodeURI($("#fieldT").val());
				fieldH = encodeURI($("#fieldH").val());
				fieldP = encodeURI($("#fieldP").val());
				fieldW = encodeURI($("#fieldW").val());
				fieldG = encodeURI($("#fieldG").val());
				fieldB = encodeURI($("#fieldB").val());
				fieldR = encodeURI($("#fieldR").val());
				fieldRR = encodeURI($("#fieldRR").val());
				fieldS = encodeURI($("#fieldS").val());
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
					url = "testImportFile.php";
				}
				if(typeFile=="import"){
					url = "importFile.php";
				}

				urlParams = "?";
				urlParams += "path="+path;
				urlParams += "&delimiter="+delimiter;
				urlParams += "&separator="+separator;
				urlParams += "&fileHeader="+fileHeader;
				urlParams += "&importType="+importType;

				urlParams += "&solarData="+solarData;

				urlParams += "&unitsT="+unitsT;
				urlParams += "&unitsP="+unitsP;
				urlParams += "&unitsW="+unitsW;
				urlParams += "&unitsG="+unitsG;
				urlParams += "&unitsR="+unitsR;
				urlParams += "&unitsRR="+unitsRR;
				urlParams += "&fieldT="+fieldT;
				urlParams += "&fieldH="+fieldH;
				urlParams += "&fieldP="+fieldP;
				urlParams += "&fieldW="+fieldW;
				urlParams += "&fieldG="+fieldG;
				urlParams += "&fieldB="+fieldB;
				urlParams += "&fieldR="+fieldR;
				urlParams += "&fieldRR="+fieldRR;
				urlParams += "&fieldS="+fieldS;

				urlParams += "&dateformat="+dateformat;

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

				url = url + urlParams;
				//alert(url);
				window.open(url);
			}
			function wuImport(typeWU){
				WUID = encodeURI($("#WUID").val());
				importType = encodeURI($("#importTypeWU").val());

				unitsT = encodeURI($("#unitsWUT").val());
				unitsP = encodeURI($("#unitsWUP").val());
				unitsW = encodeURI($("#unitsWUW").val());
				unitsG = encodeURI($("#unitsWUG").val());
				unitsR = encodeURI($("#unitsWUR").val());
				unitsRR = encodeURI($("#unitsWURR").val());

				fieldT = encodeURI($("#fieldWUT").val());
				fieldH = encodeURI($("#fieldWUH").val());
				fieldP = encodeURI($("#fieldWUP").val());
				fieldW = encodeURI($("#fieldWUW").val());
				fieldG = encodeURI($("#fieldWUG").val());
				fieldB = encodeURI($("#fieldWUB").val());
				fieldR = encodeURI($("#fieldWUR").val());
				fieldRR = encodeURI($("#fieldWURR").val());
				<?php
					if($solarSensor){
				?>
						fieldS = encodeURI($("#fieldWUS").val());
				<?php
					}
				?>

				fromY = $("#wuImportFromY").val();
				fromM = $("#wuImportFromM").val();
				fromD = $("#wuImportFromD").val();
				toY = $("#wuImportToY").val();
				toM = $("#wuImportToM").val();
				toD = $("#wuImportToD").val();


				if(typeWU=="test"){
					url = "testWU.php";
				}
				else if(typeWU=="import"){
					url = "importWU.php";
				}

				urlParams = "?";
				urlParams += "WUID="+WUID;
				urlParams += "&importType="+importType;

				urlParams += "&unitsT="+unitsT;
				urlParams += "&unitsP="+unitsP;
				urlParams += "&unitsW="+unitsW;
				urlParams += "&unitsG="+unitsG;
				urlParams += "&unitsR="+unitsR;
				urlParams += "&unitsRR="+unitsRR;
				urlParams += "&fieldT="+fieldT;
				urlParams += "&fieldH="+fieldH;
				urlParams += "&fieldP="+fieldP;
				urlParams += "&fieldW="+fieldW;
				urlParams += "&fieldG="+fieldG;
				urlParams += "&fieldB="+fieldB;
				urlParams += "&fieldR="+fieldR;
				urlParams += "&fieldRR="+fieldRR;
				<?php
					if($solarSensor){
				?>
						urlParams += "&fieldS="+fieldS;
				<?php
					}
				?>

				urlParams += "&fromY="+fromY;
				urlParams += "&fromM="+fromM;
				urlParams += "&fromD="+fromD;
				urlParams += "&toY="+toY;
				urlParams += "&toM="+toM;
				urlParams += "&toD="+toD;

				url = url + urlParams;
				//alert(url);
				window.open(url);
			}

			<?php
				if(file_exists("importSettings.txt")){
					$settingsFile = json_decode(file_get_contents("importSettings.txt"),true);
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
