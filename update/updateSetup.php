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
	#	Database Update
	#
	############################################################################
	
	
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	$pathOriginal = $path;
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	if(file_exists("updateSettings.php")){
		include("updateSettings.php");
		$pathNew = $path;
		if($type=="cumulus"){
			$pathCumulus = $path;
			$separatorCumulus = $separator;
		}
		if($type=="custom"){
			$pathCustom = $path;
			$separator = $separator;
		}
	}
	
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			.sectionDiv{
				display: none;
			}
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
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php
				$path = $pathOriginal;
			?>
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
			<?php
				$path = $pathNew;
			?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%;position:relative">
				<h1>Database Update Setup</h1>
				<p>This page lets you set up probably the most important part - the database updates. You have several options how this can be done. Choose from the select box below the update type you want to use. If you are using a software which supports the API (Weather Display, Meteobridge, WeeWx etc.), then use that to do the updates via the api.php script, in such case you can IGNORE this page. The WD and MB options below are for backward compatibility and should not be used by new users!</p>
				<br>
				<br>
				<div style="width:98%;margin:0 auto;text-align:center">
					<select id="updateType" class="button2" style="font-size:1.4em;font-weight:bold">
						<option value="" <?php if(!isset($type)){ echo "selected"; }?>>--SELECT--</option>
						<option value="API" <?php if(isset($type)){ echo "selected"; }?>>API</option>
						<option value="Cumulus" <?php if(isset($type) && $type=="cumulus"){ echo "selected";}?>>Cumulus</option>
						<option value="WU" <?php if(isset($type) && $type=="wu"){ echo "selected";}?>>Weather Underground</option>
						<option value="Custom" <?php if(isset($type) && $type=="custom"){ echo "selected";}?>>Custom file</option>
					</select>
				</div>
				<br>
				<div id="updateAPIDiv" class="sectionDiv" style="width:98%;margin:0 auto;text-align:justify">
					Use the built-in function in your software, you do not need to use this page or any CRON job.
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="saveAPI" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Save Settings" onclick="save('api')">
					</div>
				</div>
				<div id="updateWDDiv" class="sectionDiv" style="width:98%;margin:0 auto;text-align:justify">
					<h2>Clientraw.txt</h2>
					<p>
						This is for older versions of WD, <strong>use the API</strong>! This is for programs which generate the clientraw.txt in the same format as Weather Display, but if you are using Weather Display, use the API instead! This file contains the current conditions with standardized units - this means you do not have to worry about units, the units will be automatically converted to whatever you have set for your <i>database units</i> in the Main settings.
					</p>
					<br>
					<p>
						Path to clientraw: <input id="pathWD" class="button2" size="30" value="<?php if(isset($pathWD)){ echo $pathWD; }?>">
					</p>
					<br>
					<br><br>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testWD" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test clientraw" onclick="update('testWD')">
						<input type="button" id="saveWD" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Save Settings" onclick="save('wd')">
					</div>
				</div>
				<br>
				<div id="updateCumulusDiv" class="sectionDiv" style="width:98%;margin:0 auto;text-align:justify">
					<h2>Cumulus</h2>
					<p>
						For updates via Cumulus you will use the realtime.txt file. This file contains the current conditions.
					</p>
					<p>
						First thing is to make sure that you have Cumulus set to upload realtime.txt periodically to your server! The ideal is if this file is updated at least once every minute, but every 5 minutes at most. What you have to specify is the path to your realtime.txt. The easiest thing to do is to use the URL of that file, i.e. <i>http://www.mysite.com/realtime.txt</i>. However, if your server does not allow this for security reasons and the file cannot be found (when using the Test update below), then you will need to use a path relative to the template <i>update</i> folder.
					</p>
					<h3>Setup Update File</h3>
					<p>
						Updates via Cumulus should be farily straight forward, all you need to do is specify the path to your realtime.txt and the date/time format and field delimiter. Then to make sure it is working, try the Test Update. Realtime is a standardized file with fixed field numbers so you do not need to specify this. If you see any errors contact me (Jachym). However, first make sure that your realtime is up-to-date and not damaged.
					</p>
					<p>
						Realtime.txt also includes fields that give units in which the numbers are given. These will be converted if necessary to match your <i>database units</i> set in the Main settings.
					</p>
					<p>
						Look at your realtime.txt and based on this fill in the fields below.
					</p>
					<br>
					<p>
						Path to realtime: <input id="pathCumulus" class="button2" size="30" value="<?php if(isset($pathCumulus)){ echo $pathCumulus; }?>">
					</p>
					<p>
						Date separator: <input id="separatorCumulus" class="button2" size="3" value="<?php if(isset($separatorCumulus)){ echo $separatorCumulus; }?>"> (look at the first field, if for example it is 19/08/09, then insert /, if it is 19-08-09, insert - etc.)
					</p>
					<br>
					<p>
						Before starting the updates let's make sure that your realtime is correctly read by the script. Simply click the Test WD button and look carefully at the output. If everything looks ok, click the Save Settings and you are ready.
					</p>
					<br><br>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testCumulus" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test Cumulus" onclick="update('testCumulus')">
						<input type="button" id="saveCumulus" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Save Settings" onclick="save('cumulus')">
					</div>
					<h3>Set up CRON job</h3>
					<p>
						To update the database you will use a so-called CRON job. Go to the Meteotemplate Wiki and read the section about CRON jobs. It gives you explanation of what it is and how to set it up.
					</p>
					<p>
						So, all you have to do is, make sure the Test Update works - the clientraw is correctly read. Then, set a CRON job for the update script URL and set it to be executed ideally in minutely intervals, but 5 minutes at most.
					</p>
					<p>
						Path for your CRON job: <br><br>
						<i><?php echo $pageURL.$pathOriginal?>update/update.php?password=<?php echo $updatePassword?></i>
					</p>
				</div>
				<br>
				<br>
				<div id="updateWUDiv" class="sectionDiv" style="width:98%;margin:0 auto;text-align:justify">
					<h2>Weather Underground</h2>
					<p>
						For updates via Weather Underground we will use data from WU.com for your station. You will need to specify only your WU station ID. Then once you specify this, make sure you run the Test Update script to see if there are no errors. Then last, you will need to set up a CRON job, which will periodically execute the update script and save data to the database.
					</p>
					<br>
					<p>
						WU station ID: <input id="WUID" class="button2" size="15" value="<?php if(isset($WUID)){ echo $WUID; }?>">
					</p>
					<br>
					<br><br>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testWU" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test WU" onclick="update('testWU')">
						<input type="button" id="saveWU" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Save Settings" onclick="save('wu')">
					</div>
					<h3>Set up CRON job</h3>
					<p>
						To update the database you will use a so-called CRON job. Go to the Meteotemplate Wiki and read the section about CRON jobs. It gives you explanation of what it is and how to set it up.
					</p>
					<p>
						So, all you have to do is, make sure the data from WU is correctly read. Then, set a CRON job for the update script URL and set it to be executed ideally in minutely intervals, but 5 minutes at most.
					</p>
					<p>
						Path for your CRON job: <br><br>
						<i><?php echo $pageURL.$pathOriginal?>update/update.php?password=<?php echo $updatePassword?></i>
					</p>
				</div>
				<br>
				<div id="updateCustomDiv" class="sectionDiv" style="width:98%;margin:0 auto;text-align:justify">
					<h2>Custom File</h2>
					<p>
						For updates via custom text file you will need to specify exactly the format of that file.
					</p>
					<p>
						First thing is to make sure that you have that file being periodically updated, ideally every minute, or every 5 minutes at most. What you have to specify is the path to this file. The easiest thing to do is to use the URL of that file, i.e. <i>http://www.mysite.com/myfile.txt</i>. However, if your server does not allow this for security reasons and the file cannot be found (when using the Test update below), then you will need to use a path relative to the template <i>update</i> folder.
					</p>
					<h3>Setup Update File</h3>
					<p>
						Now specify the units, the date/time format, field numbers and field delimiter. Then to make sure it is working, try the Test Update. If you see any errors, make sure you set up correctly the fields below, if it still does not work, contact me (Jachym). 
					</p>
					<br>				
					<table style="width:98%;margin:0 auto">
						<tr>
							<td class="firstCell" style="width:150px">
								Path
							</td>
							<td class="secondCell" colspan="2">
								<input id="pathCustom" class="button2" style="cursor:auto;text-align:left" size="70" value="<?php if(isset($pathCustom)){ echo $pathCustom; }?>">
								<br>
								First thing to do is specify the path to the file. You can either use path relative to this file (i.e. to your template root/update/) or use the URL (http://...).
							</td>
						</tr>
						<tr>
							<td class="firstCell" style="width:150px">
								Field delimiter
							</td>
							<td class="secondCell">
								<select id="customDelimiter"class="button2" style="width:160px">
									<option value="semicolon" <?php if(isset($type) && $type=="custom" && $delimiter=="semicolon"){ echo "selected";}?>> ; (semi-colon)</option>
									<option value="comma" <?php if(isset($type) && $type=="custom" && $delimiter=="comma"){ echo "selected";}?>> , (comma)</option>
									<option value="space" <?php if(isset($type) && $type=="custom" && $delimiter=="space"){ echo "selected";}?>>&nbsp;&nbsp;(space)</option>
									<option value="tab" <?php if(isset($type) && $type=="custom" && $delimiter=="tab"){ echo "selected";}?>>  (tab)</option>
									<option value="colon" <?php if(isset($type) && $type=="custom" && $delimiter=="colon"){ echo "selected";}?>> : (colon)</option>
									<option value="vertical" <?php if(isset($type) && $type=="custom" && $delimiter=="vertical"){ echo "selected";}?>> | (vertical bar)</option>
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
								<select id="customSeparator"class="button2">
									<option value="period" <?php if(isset($type) && $type=="custom" && $separator=="period"){ echo "selected";}?>> . (period)</option>
									<option value="comma" <?php if(isset($type) && $type=="custom" && $separator=="comma"){ echo "selected";}?>> , (comma)</option>
								</select>
							</td>
							<td class="thirdCell">
								Specify the decimal separator. NOTE: If you specify a comma (,), then it is essential that the comma is not used in any other context in the file. For example, if your decimal separator is a comma, then you cannot have the field delimiter also a comma. If that happened the script would not be able to tell which comma is which.
							</td>
						</tr>
					</table>
					<br>
					<p>
						You can use the link below, which will try to load the file and show you the field numbers. However, make sure you have filled in correct path and field delimiter above.
					</p>
					<input type="button" class="button2" value="Show Field Numbers" id="showFields">
					<h3 style="font-size:1.2em">Date and time</h3>
					<table style="width:98%;margin:0 auto">
						<tr>
							<td class="firstCell" style="width:150px">
								Date and time field(s)
							</td>
							<td class="secondCell">
								<select id="customDateType"class="button2">
									<option value="">--Select--</option>
									<option value="single" <?php if(isset($type) && $type=="custom" && $dateformat=="single"){ echo "selected";}?>>Date and time in a single field</option>
									<option value="double" <?php if(isset($type) && $type=="custom" && $dateformat=="double"){ echo "selected";}?>>Date in one field, time in second field</option>
									<option value="separateDate" <?php if(isset($type) && $type=="custom" && $dateformat=="separateDate"){ echo "selected";}?>>Date in separate fields (year, month, day) and time in one field</option>
									<option value="separateTime" <?php if(isset($type) && $type=="custom" && $dateformat=="separateTime"){ echo "selected";}?>>Date in one field and time in separate fields (hour, minute)</option>
									<option value="separate" <?php if(isset($type) && $type=="custom" && $dateformat=="separate"){ echo "selected";}?>>Everything in separate fields (year, month, day, hour, minute)</option>
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
							<td style="width:25%;text-align:left">
								Time: <?php echo date("H:i")?><br>
								Format: H:i
							</td>
							<td style="width:25%;text-align:left">
								Time: <?php echo date("H.i")?><br>
								Format: H.i
							</td>
							<td style="width:25%;text-align:left">
								Date/time: <?php echo date("Y-m-d H:i")?><br>
								Format: Y-m-d H:i
							</td>
							<td style="width:25%;text-align:left">
								Time: <?php echo date("Y/m/d H.i")?><br>
								Format: Y/m/d H.i
							</td>
						</tr>
					</table>
					<br>
					<div id="divSingle" class="dateTimeDiv">
						<strong>DATE AND TIME IN ONE FIELD</strong><br>
						<input id="dtSingleField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSingleField)){echo $dtSingleField;}?>"> Date and time field number
						<br><br>
						Date and time format
						<br>
						<input id="dtSingleFormat" class="button2" style="cursor:auto;text-align:left" size="20" value="<?php if(isset($dtSingleFormat)){echo $dtSingleFormat;}?>">
					</div>
					<div id="divDouble" class="dateTimeDiv">
						<strong>DATE AND TIME IN TWO FIELDS</strong><br>
						<input id="dtDoubleDateField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtDoubleDateField)){echo $dtDoubleDateField;}?>">  Date field number
						<br><br>
						<input id="dtDoubleTimeField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtDoubleTimeField)){echo $dtDoubleTimeField;}?>">  Time field number
						<br><br>
						Date format
						<br>
						<input id="dtDoubleDateFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="<?php if(isset($dtDoubleDateFormat)){echo $dtDoubleDateFormat;}?>">
						<br><br>
						Time format
						<br>
						<input id="dtDoubleTimeFormat" class="button2" style="cursor:auto;text-align:left" size="8" value="<?php if(isset($dtDoubleTimeFormat)){echo $dtDoubleTimeFormat;}?>">
					</div>
					<div id="divSeparateTime" class="dateTimeDiv">
						<strong>DATE IN ONE FIELD, TIME SEPARATE</strong><br>
						<input id="dtSeparateTimeDateField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateTimeDateField)){echo $dtSeparateTimeDateField;}?>">  Date field number
						<br><br>
						<input id="dtSeparateTimeHourField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateTimeHourField)){echo $dtSeparateTimeHourField;}?>">  Hour field number
						<br><br>
						<input id="dtSeparateTimeMinuteField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateTimeMinuteField)){echo $dtSeparateTimeMinuteField;}?>">  Minute field number
						<br><br>
						Date format
						<br>
						<input id="dtSeparateTimeDateFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="<?php if(isset($dtSeparateTimeDateFormat)){echo $dtSeparateTimeDateFormat;}?>">
					</div>
					<div id="divSeparateDate" class="dateTimeDiv">
						<strong>DATE IN SEPARATE FIELDS, TIME IN ONE FIELD</strong><br>
						<input id="dtSeparateDateTimeField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateDateTimeField)){echo $dtSeparateDateTimeField;}?>">  Time field number
						<br><br>
						<input id="dtSeparateDateYearField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateDateYearField)){echo $dtSeparateDateYearField;}?>">  Year field number
						<br><br>
						<input id="dtSeparateDateMonthField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateDateMonthField)){echo $dtSeparateDateMonthField;}?>">  Month field number
						<br><br>
						<input id="dtSeparateDateDayField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateDateDayField)){echo $dtSeparateDateDayField;}?>">  Day field number
						<br><br>
						Date format
						<br>
						<input id="dtSeparateDateTimeFormat" class="button2" style="cursor:auto;text-align:left" size="12" value="<?php if(isset($dtSeparateDateTimeFormat)){echo $dtSeparateDateTimeFormat;}?>">
					</div>
					<div id="divSeparate" class="dateTimeDiv">
						<strong>ALL IN SEPARATE FIELDS</strong><br>
						<input id="dtSeparateYearField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateYearField)){echo $dtSeparateYearField;}?>">  Year field number
						<br><br>
						<input id="dtSeparateMonthField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateMonthField)){echo $dtSeparateMonthField;}?>">  Month field number
						<br><br>
						<input id="dtSeparateDayField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateDayField)){echo $dtSeparateDayField;}?>">  Day field number
						<br><br>
						<input id="dtSeparateHourField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateHourField)){echo $dtSeparateHourField;}?>">  Hour field number
						<br><br>
						<input id="dtSeparateMinuteField" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($dtSeparateMinuteField)){echo $dtSeparateMinuteField;}?>">  Minute field number
					</div>
					<hr>
					<h3 style="font-size:1.2em">Variables</h3>
					<p>Now you have to specify the field numbers for the variables and their units. The units will be automatically converted to match what you specified in the Main settings for "database units". Just <strong>make sure that you have the limits in main settings set correctly!!!</strong> If for example you are importing pressure in inHg then make sure that the pressure limit in Main settings is not 960 to 1040 (that is the default value if you have not changed it). You would also see this when you run the import test - there would be no value for the pressure. All the parameters must be included, except solar radiation. Solar radiation is optional, <strong> if you do not have a solar sensor, then specify this in the Main settings and the solar data will be ignored.</strong></p>
					<p>If for example you do not have wind gust available, then specify the same field number as average wind.</p>
					<br>
					<table cellspacing="4" cellpadding="4" class="table">
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/temp.png" style="width:25px"><br>Temperature
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldT" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldT)){echo $fieldT;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsT"class="button2">
									<option value="C" <?php if(isset($type) && $type=="custom" && $unitsT=="C"){ echo "selected";}?>>Celsius</option>
									<option value="F" <?php if(isset($type) && $type=="custom" && $unitsT=="F"){ echo "selected";}?>>Farenheit</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/humidity.png" style="width:25px"><br>Humidity
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldH" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldH)){echo $fieldH;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: %
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/pressure.png" style="width:25px"><br>Pressure
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldP" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldP)){echo $fieldP;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsP"class="button2">
									<option value="hpa" <?php if(isset($type) && $type=="custom" && $unitsP=="hpa"){ echo "selected";}?>>hPa</option>
									<option value="inhg" <?php if(isset($type) && $type=="custom" && $unitsP=="inhg"){ echo "selected";}?>>inHg</option>
									<option value="mmhg" <?php if(isset($type) && $type=="custom" && $unitsP=="mmhg"){ echo "selected";}?>>mmhg</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/wind.png" style="width:25px"><br>Wind speed
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldW" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldW)){echo $fieldW;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsW"class="button2">
									<option value="kmh" <?php if(isset($type) && $type=="custom" && $unitsW=="kmh"){ echo "selected";}?>>km/h</option>
									<option value="ms" <?php if(isset($type) && $type=="custom" && $unitsW=="ms"){ echo "selected";}?>>m/s</option>
									<option value="mph" <?php if(isset($type) && $type=="custom" && $unitsW=="mph"){ echo "selected";}?>>mph</option>
									<option value="kts" <?php if(isset($type) && $type=="custom" && $unitsW=="kts"){ echo "selected";}?>>knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/gust.png" style="width:25px"><br>Wind gust
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldG" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldG)){echo $fieldG;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsG"class="button2">
									<option value="kmh" <?php if(isset($type) && $type=="custom" && $unitsG=="kmh"){ echo "selected";}?>>km/h</option>
									<option value="ms" <?php if(isset($type) && $type=="custom" && $unitsG=="ms"){ echo "selected";}?>>m/s</option>
									<option value="mph" <?php if(isset($type) && $type=="custom" && $unitsG=="mph"){ echo "selected";}?>>mph</option>
									<option value="kts" <?php if(isset($type) && $type=="custom" && $unitsG=="kts"){ echo "selected";}?>>knots</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/rain.png" style="width:25px"><br>Cumulative daily precipitation
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldR" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldR)){echo $fieldR;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsR"class="button2">
									<option value="mm" <?php if(isset($type) && $type=="custom" && $unitsR=="mm"){ echo "selected";}?>>mm</option>
									<option value="in" <?php if(isset($type) && $type=="custom" && $unitsR=="in"){ echo "selected";}?>>in</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/rain.png" style="width:25px"><br>Rain rate
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldRR" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldRR)){echo $fieldRR;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: 
								<select id="unitsRR"class="button2">
									<option value="mm" <?php if(isset($type) && $type=="custom" && $unitsRR=="mm"){ echo "selected";}?>>mm/h</option>
									<option value="in" <?php if(isset($type) && $type=="custom" && $unitsRR=="in"){ echo "selected";}?>>in/h</option>
									<option value="mmmin" <?php if(isset($type) && $type=="custom" && $unitsRR=="mmmin"){ echo "selected";}?>>mm/min</option>
									<option value="inmin" <?php if(isset($type) && $type=="custom" && $unitsRR=="inmin"){ echo "selected";}?>>in/min</option>
								</select>
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/wind.png" style="width:25px"><br>Wind direction
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldB" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldB)){echo $fieldB;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: degrees
							</td>
						</tr>
						<tr>
							<td style="padding-left:10px;text-align:left">
								<img src="<?php echo $pageURL.$pathOriginal?>icons/sun.png" style="width:25px"><br>Solar radiation
								<br>
								* leave blank if solar sensor is disabled in Main settings
							</td>
							<td style="padding-left:10px;text-align:left">
								Field number: <input id="fieldS" class="button2" style="cursor:auto;text-align:left" size="3" value="<?php if(isset($fieldS)){echo $fieldS;}?>">
							</td>
							<td style="padding-left:10px;text-align:left">
								Units: W/m2
							</td>
						</tr>
					</table>
					<p>
						Before starting the updates let's make sure that your realtime is correctly read by the script. Simply click the Test WD button and look carefully at the output. If everything looks ok, click the Save Settings and you are ready.
					</p>
					<br><br>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="button" id="testCustom" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Test Custom" onclick="update('testCustom')">
						<input type="button" id="saveCustom" class="button2" style="font-size:1.2em;font-variant:small-caps;font-weight:bold;padding:5px" value="Save Settings" onclick="save('custom')">
					</div>
					<h3>Set up CRON job</h3>
					<p>
						To update the database you will use a so-called CRON job. Go to the Meteotemplate Wiki and read the section about CRON jobs. It gives you explanation of what it is and how to set it up.
					</p>
					<p>
						So, all you have to do is, make sure the Test Update works - the clientraw is correctly read. Then, set a CRON job for the update script URL and set it to be executed ideally in minutely intervals, but 5 minutes at most.
					</p>
					<p>
						Path for your CRON job: <br><br>
						<i><?php echo $pageURL.$pathOriginal?>update/update.php?password=<?php echo $updatePassword?></i>
					</p>
				</div>
				<br><br>
			</div>
		</div>
		<?php
			$path = $pathOriginal;
		?>
		<?php include($baseURL."footer.php");?>
		<script>
			$("#updateType").change(function(){
				type = $("#updateType").val();
				$(".sectionDiv").hide();
				$("#update"+type+"Div").show();
			});
			$("#showWUFields").click(function(){
				id = encodeURI($("#WUID").val());
				url = "../admin/showWUFields.php?id="+id;
				window.open(url);
			});
			$("#showFields").click(function(){
				path = encodeURI($("#pathCustom").val());
				delimiter = encodeURI($("#customDelimiter").val());
				url = "showFields.php?path="+path+"&delimiter="+delimiter;
				window.open(url);
			})
			$("#customDateType").change(function(){
				$(".dateTimeDiv").hide();
				dttype = $("#customDateType").val();
				if(dttype=="single"){
					$("#divSingle").show();
				}
				if(dttype=="double"){
					$("#divDouble").show();
				}
				if(dttype=="separateTime"){
					$("#divSeparateTime").show();
				}
				if(dttype=="separateDate"){
					$("#divSeparateDate").show();
				}
				if(dttype=="separate"){
					$("#divSeparate").show();
				}
			})
			dttype = $("#customDateType").val();
			if(dttype=="single"){
				$("#divSingle").show();
			}
			if(dttype=="double"){
				$("#divDouble").show();
			}
			if(dttype=="separateTime"){
				$("#divSeparateTime").show();
			}
			if(dttype=="separateDate"){
				$("#divSeparateDate").show();
			}
			if(dttype=="separate"){
				$("#divSeparate").show();
			}
			<?php
				if(isset($type)){
					if($type=="api"){
						echo "$('#updateAPIDiv').show();";
					}
					if($type=="cumulus"){
						echo "$('#updateCumulusDiv').show();";
					}
					if($type=="wu"){
						echo "$('#updateWUDiv').show();";
					}
					if($type=="custom"){
						echo "$('#updateCustomDiv').show();";
					}
				}
			?>
			function update(type){
				if(type=="testWD"){
					path = encodeURI($("#pathWD").val());
					type = "wd";
					url = "testUpdate.php?";
					urlParams = "";
					urlParams += "path="+path;
					urlParams += "&type="+type;
					url += urlParams;
					window.open(url);
				}
				if(type=="testCumulus"){
					path = encodeURI($("#pathCumulus").val());
					type = "cumulus";
					separator = encodeURI($("#separatorCumulus").val());
					url = "testUpdate.php?";
					urlParams = "";
					urlParams += "path="+path;
					urlParams += "&type="+type;
					urlParams += "&separator="+separator;
					url += urlParams;
					window.open(url);
				}
				if(type=="testWU"){
					WUID = encodeURI($("#WUID").val());
					type = "wu";
					
					url = "testUpdate.php?";
					urlParams = "";
					urlParams += "WUID="+WUID;
					urlParams += "&type="+type;
					
					url += urlParams;
					window.open(url);
				}
				if(type=="testCustom"){
					path = encodeURI($("#pathCustom").val());
					type = "custom";
					
					delimiter = encodeURI($("#customDelimiter").val());
					separator = encodeURI($("#customSeparator").val());
					
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
					dateformat = encodeURI($("#customDateType").val());
					
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
					
					url = "testUpdate.php";
		
					urlParams = "?";
					urlParams += "type="+type;
					urlParams += "&path="+path;
					urlParams += "&delimiter="+delimiter;
					urlParams += "&separator="+separator;
					
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
					window.open(url);
				}
			}
			function save(type){
				if(type=="api"){
					type = "api";
					url = "saveUpdateSettings.php?";
					urlParams = "";
					urlParams += "&type="+type;
					url += urlParams;
					window.open(url);
				}
				if(type=="wd"){
					path = encodeURI($("#pathWD").val());
					type = "wd";
					url = "saveUpdateSettings.php?";
					urlParams = "";
					urlParams += "path="+path;
					urlParams += "&type="+type;
					url += urlParams;
					window.open(url);
				}
				if(type=="cumulus"){
					path = encodeURI($("#pathCumulus").val());
					type = "cumulus";
					separator = encodeURI($("#separatorCumulus").val());
					
					url = "saveUpdateSettings.php?";
					urlParams = "";
					urlParams += "path="+path;
					urlParams += "&type="+type;
					urlParams += "&separator="+separator;
					url += urlParams;
					window.open(url);
				}
				if(type=="mb"){
					type = "mb";
					
					url = "saveUpdateSettings.php?";
					urlParams = "";
					urlParams += "&type="+type;
					
					url += urlParams;
					window.open(url);
				}
				if(type=="wu"){
					WUID = encodeURI($("#WUID").val());
					type = "wu";
					
					url = "saveUpdateSettings.php?";
					urlParams = "";
					urlParams += "WUID="+WUID;
					urlParams += "&type="+type;
					
					url += urlParams;
					window.open(url);
				}
				if(type=="custom"){
					path = encodeURI($("#pathCustom").val());
					type = "custom";
					
					delimiter = encodeURI($("#customDelimiter").val());
					separator = encodeURI($("#customSeparator").val());
					
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
					dateformat = encodeURI($("#customDateType").val());
					
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
					
					url = "saveUpdateSettings.php";
		
					urlParams = "?";
					urlParams += "type="+type;
					urlParams += "&path="+path;
					urlParams += "&delimiter="+delimiter;
					urlParams += "&separator="+separator;
					
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
					window.open(url);
				}
			}
		</script>
	</body>
</html>
		