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
	#	Administration Page
	#
	# 	This page is the main administration page, which allows clearing the
	# 	cache or to enter the database editing page.
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


	// check if "station offline" alert has been sent
	$alertFile = "../update/alertSent.txt";
	if(file_exists($alertFile)){
		$alertSent = true;
	}
	else{
		$alertSent = false;
	}

	// check available years in db 
	$result = mysqli_query($con,"
		SELECT DISTINCT YEAR(DateTime)
		FROM alldata
		ORDER BY DateTime
		"
	);
	while($row = mysqli_fetch_array($result)){
		$dbYears[] = $row['YEAR(DateTime)'];
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			h3{
				padding-bottom:10px;
				padding-top:10px;
			}
			.adminIcon{
				opacity: 0.7;
				cursor: pointer;
				padding: 10px;
			}
			.adminIcon:hover{
				opacity: 1;
			}
			h3{
				text-align: center;
				font-size: 2em;
			}
			.subheadingAdmin{
				font-variant: small-caps;
				color: #<?php echo $color_schemes[$design2]['200']?>;
				font-weight: bold;
			}
			.opacityTable{
				cursor: pointer;
				opacity: 0.7;
				vertical-align:top;
			}
			.opacityTable:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:98%;position:relative">
			<img src="adminIcons/logout.png" style="position:absolute;top:10px;right:15px;max-width:50px" class="opacityTable tooltip" title="Log-out" id="logout">
			<h1>Meteotemplate <?php echo lang("control panel",'w')?></h1>
			<br>
			<table style="width:98%;margin:0 auto" cellspacing="10">
				<tr>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px">
						<span class="mticon-blocks" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("homepage",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto">
							<tr>
								<td style="width:33%">
									<img src="adminIcons/layout.png" style="width:100%;max-width:60px;opacity:0.8"><br><span class="subheadingAdmin"><?php echo lang("setup",'c')?></span>
								</td>
								<td style="width:33%">
									<img src="adminIcons/resetHomepage.png" style="width:100%;max-width:60px;opacity:0.8"><br><span class="subheadingAdmin"><?php echo lang("reset",'c')?></span>
								</td>
								<td style="width:33%" class="opacityTable tooltip" id="blocksSetupButton" rowspan="2" title="Blocks setup and version check">
									<img src="adminIcons/blockSetup.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin " >Install/Setup Blocks</span>
								</td>
							</tr>
							<tr>
								<td>
									<img src="<?php echo $pageURL.$path?>mobile/pc.png" style="width:100%;max-width:30px" class="adminIcon tooltip" id="homepageSetupDesktop" title="Create/Edit desktop homepage layout">
									<img src="<?php echo $pageURL.$path?>mobile/mobile.png" style="width:100%;max-width:30px" class="adminIcon tooltip" id="homepageSetupMobile" title="Create/Edit mobile homepage layout">
								</td>
								<td>
									<img src="<?php echo $pageURL.$path?>mobile/pc.png" style="width:100%;max-width:30px" class="adminIcon tooltip" id="homepageResetDesktop" title="Reset desktop homepage layout">
									<img src="<?php echo $pageURL.$path?>mobile/mobile.png" style="width:100%;max-width:30px" class="adminIcon tooltip" id="homepageResetMobile" title="Reset desktop homepage layout">
								</td>
							</tr>
						</table>
					</td>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px">
						<span class="mticon-logo" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("administration",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto;table-layout:fixed">
							<?php
								if(!file_exists("templateRegistered.txt")){
							?>
								<tr>
									<td class="opacityTable tooltip" id="showSetup" title="Main template settings">
										<img src="adminIcons/mainSetup.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("main setup",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="showMenu" title="Menu">
										<img src="<?php echo $pageURL.$path?>icons/menuDark.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("menu",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="pluginsSetupButton" title="Plugins setup and version check">
										<img src="adminIcons/pluginSetup.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin " ><?php echo lang("plugin setup",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="updateLangs" title="Update language files">
										<img src="adminIcons/update.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin " ><?php echo lang("languages",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="infoPagesSetup" title="Specify content for info pages">
										<img src="<?php echo $pageURL.$path?>icons/info.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("info pages",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="adminNotes" title="Space for your notes">
										<img src="<?php echo $pageURL.$path?>icons/diary.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("my notes",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="createPage" title="Create new custom page">
										<img src="<?php echo $pageURL.$path?>icons/plus.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("custom pages",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="registerOpener" title="Register template">
										<img src="adminIcons/users.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("registration",'c')?></span>
									</td>
								</tr>
								<tr>
									<td colspan="5">
										<div id="registrationDetailsDiv" style="width:98%;margin:0 auto">
											<br><br>
											<?php echo lang("name",'c')?>: <input id="registerName"><br>
											<?php echo lang("email",'c')?>: <input id="registerMail"><br>
											<br>
											<input type="button" class="button" value="Register Template" id="registerTemplate">
										</div>
									</td>
								</tr>
							<?php
								}
								else{
							?>
									<tr>
									<td class="opacityTable tooltip" id="showSetup" title="Main template settings">
										<img src="adminIcons/mainSetup.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("main setup",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="showMenu" title="Menu">
										<img src="<?php echo $pageURL.$path?>icons/menuDark.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("menu",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="pluginsSetupButton" title="Plugins setup and version check">
										<img src="adminIcons/pluginSetup.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin " ><?php echo lang("plugin setup",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="updateLangs" title="Update language files">
										<img src="../icons/update.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin " ><?php echo lang("languages",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="infoPagesSetup" title="Specify content for info pages">
										<img src="<?php echo $pageURL.$path?>icons/info.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("info pages",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="adminNotes" title="Specify content for info pages">
										<img src="<?php echo $pageURL.$path?>icons/diary.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("my notes",'c')?></span>
									</td>
									<td class="opacityTable tooltip" id="createPage" title="Create new custom page">
										<img src="<?php echo $pageURL.$path?>icons/plus.png" style="width:100%;max-width:50px"><br><span class="subheadingAdmin"><?php echo lang("custom pages",'c')?></span>
									</td>
								</tr>
							<?php
								}
							?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="height:20px"></td>
				</tr>
				<tr>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px;text-align:center">
						<span class="fa fa-database" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("database",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto;table-layout:fixed">
							<tr>
								<td class="opacityTable tooltip" id="dataUpdate" title="Database Update Setup">
									<span class="fa fa-refresh" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("database update",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="extraSensors" title="Extra sensors">
									<span class="fa fa-plus-circle" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("extra sensors",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="mysqlAdmin" title="View and edit station data">
									<span class="fa fa-table" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("edit data",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="mysqlAdminExtra" title="View and edit station data">
									<span class="fa fa-table" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("edit data",'c')?> <br><?php echo lang("extra sensors",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="dataImport" title="Import History Data">
									<span class="fa fa-upload" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("data import",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="dataImportExtra" title="Import History Data - Extra sensors">
									<span class="fa fa-upload" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("data import",'c')?><br><?php echo lang('extra sensors','c')?></span>
								</td>
								<td class="opacityTable tooltip" id="dbBulk" title="Bulk database editing">
									<span class="fa fa-database" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("bulk database editing",'c')?></span>
								</td>
							</tr>
						</table>
					</td>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px">
						<span class="fa fa-server" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("backup",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto;table-layout:fixed">
							<tr>
								<td class="tooltip" title="Backup station data table">
									<span class="fa fa-database" style="font-size:2.5em;opacity:0.8"></span><br>
									<br>
									<select class="button2" id="dbBackupYear">
										<option value="all" selected><?php echo lang('all','c')?></option>
										<?php 
											for($i=0;$i<count($dbYears);$i++){
										?>
												<option value="<?php echo $dbYears[$i]?>"><?php echo $dbYears[$i]?></option>
										<?php
											}
										?>
									</select>
									<input type="button" class="button2" value="<?php echo lang('ok','u')?>" id="mysqlBackup">
								</td>
								<td class="opacityTable tooltip" id="filesBackup" title="Backup core template files and settings">
									<span class="fa fa-folder" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("main files and settings",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="offlineNotification" title="Send an email when station goes offline.">
									<span class="fa fa-wifi" style="font-size:2.5em;opacity:0.8"></span><br><span class="subheadingAdmin"><?php echo lang("offline notification",'c')?></span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<br><div id="bckupProgress"></div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="height:20px"></td>
				</tr>
				<tr>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px">
						<span class="fa fa-trash" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("empty cache",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto">
							<tr>
								<td style="width:50%" class="opacityTable tooltip" id="metarCache" title="Empty METAR cache">
									<img src="adminIcons/metar.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin">METAR</span>
								</td>
								<td style="width:50%" class="opacityTable tooltip" id="stationCache" title="Empty station cache">
									<img src="<?php echo $pageURL.$path?>icons/station.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin"><?php echo lang("station data",'c')?></span>
								</td>
							</tr>
						</table>
					</td>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px">
						<span class="fa fa-info-circle" style="font-size:3.5em;opacity:0.8"></span>
						<h3><?php echo lang("template info",'c')?></h3>
						<br>
						<table style="width:80%;margin:0 auto;table-layout:fixed">
							<tr>
								<td class="opacityTable tooltip" id="showPHPInfo" title="Information about server configuration.">
									<img src="adminIcons/php.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin">PHP info</span>
								</td>
								<td class="opacityTable tooltip" id="showBlocks" title="List of installed blocks">
									<img src="adminIcons/blocks.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin"><?php echo lang("installed blocks",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="showPlugins" title="List of installed plugins">
									<img src="adminIcons/plugin.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin"><?php echo lang("installed plugins",'c')?></span>
								</td>
								<td class="opacityTable tooltip" id="errorChecker" title="Check for problems">
									<img src="../icons/settings.png" style="width:100%;max-width:60px"><br><span class="subheadingAdmin"><?php echo lang("check for problems",'c')?></span>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<!--
				<tr>
					<td colspan="2" style="height:20px"></td>
				</tr>
				<tr>
					<td style="text-align:center;vertical-align:top;border:1px solid grey;padding:20px;width:50%;border-radius:10px;text-align:center" colspan="2">
						<img src="<?php echo $pageURL.$path?>icons/warning.png" style="width:70px;opacity:0.8">
						<h3><?php echo lang("notifications",'c')?></h3>
						<br>
						<?php
							if($alertSent){
						?>
							<span style="opacity:0.7"><?php echo lang("a notifcation has been sent",'c')?>: <?php echo date("Y-m-d H:i",filemtime($alertFile))?>. <?php echo lang('until the notification system is reset, no more emails will be sent','c')?>.</span>
							<br>
							<input type="button" class="button" value="Reset alerting" id="alertResetter">
						<?php
							}
							else{
						?>
							<span style="opacity:0.7"></span>
						<?php
							}
						?>
					</td>
				</tr>
				-->
			</table>
			<br>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>

		<script>
			$("#registrationDetailsDiv").hide();
			$("#metarCache").click(function(){
				url = "adminAction.php?action=metarCache";
				$.ajax({
					type: "GET",
					url : url,
					success : function () {
						alert("Metar cache emptied.");
					}
				})
			});
			$("#filesBackup").click(function(){
				url = "adminAction.php?action=filesBackup";
				$.get(url, function(zipURL){
					alert("Core files backed up.");
					window.location.href = zipURL;
				})
			});
			$("#offlineNotification").click(function(){
				location = "offlineNotification.php";
			});
			
			$("#stationCache").click(function(){
				confirmIt = confirm("Are you sure you want to completely delete your station cache? This will delete all cached data for station-related pages!");
				if(confirmIt){
					url = "adminAction.php?action=stationCache";
					$.ajax({
						type: "GET",
						url : url,
						success : function () {
							alert("Station cache emptied.");
						}
					})
				}
			});
			$("#mysqlBackup").click(function(){
				year = $("#dbBackupYear").val();
				alert("Alldata table will now be backed up to CSV files. Depending on the size of your database this can take a while. It might look like nothing is hapenning, but the database will be backing up in the backend.\n\nWait until you get another message that the process was finished!!!\n\nIMPORTANT!\nIf you selected the option to backup all years, then in case the script fails to execute completely (you do not get a download dialog to download the backup file, instead this page just refreshes), it means your server ran out of time/memory available to PHP and you will need to backup the data for each year separately.");
				$("#bckupProgress").html("<br>Backing-up, please wait....");
				url = "adminAction.php?action=dbBackup&backupYear="+year;
				$.ajax({
					type: "GET",
					url : url,
					success : function (zipURL) {
						alert("Alldata table has been backed up to CSV files by years. It is however better to check on your server that files exist in the backup folder in your template root. Now wait and in a short while you should get a download dialog to download your file to local PC as well, if not you need to make the backup year by year.");
						$("#bckupProgress").html("");
						window.location.href = zipURL;
					}
				})
			});
			$("#mysqlAdmin").click(function(){
				url = "tableEdit.php";
				window.location = url;
			});
			$("#mysqlAdminExtra").click(function(){
				url = "tableEditExtra.php";
				window.location = url;
			});
			$("#dataImport").click(function(){
				url = "import.php";
				window.location = url;
			});
			$("#dataImportExtra").click(function(){
				url = "importExtra.php";
				window.location = url;
			});

			$("#dataUpdate").click(function(){
				url = "../update/updateSetup.php";
				window.location = url;
			});

			$("#extraSensors").click(function(){
				url = "../update/apiSetup.php";
				window.location = url;
			});

			$("#dbBulk").click(function(){
				url = "bulkDBEdit.php";
				window.location = url;
			});

			$("#alertResetter").click(function(){
				url = "alertReset.php";
				window.location = url;
			});
			$("#blocksSetupButton").click(function(){
				url = "blockSetup.php";
				window.location = url;
			});
			$("#pluginsSetupButton").click(function(){
				url = "pluginSetup.php";
				window.location = url;
			});
			$("#updateLangs").click(function(){
				confirmIt = confirm("Update language files?");
				if(confirmIt){
					openWindow("updateLangs.php", "Language files update");
				}
				else{
					return false;
				}
			});
			$("#showSetup").click(function(){
				url = "../install/setup.php";
				window.location = url;
			});
			$("#homepageSetupDesktop").click(function(){
				url = "homepageStart.php?type=desktop";
				window.location = url;
			});
			$("#homepageSetupMobile").click(function(){
				url = "homepageStart.php?type=mobile";
				window.location = url;
			});
			$("#infoPagesSetup").click(function(){
				url = "infoSetup.php";
				window.location = url;
			});
			$("#adminNotes").click(function(){
				url = "adminNotes.php";
				window.location = url;
			});
			$("#createPage").click(function(){
				url = "customPages.php";
				window.location = url;
			});
			$("#errorChecker").click(function(){
				url = "../install/errorChecker.php";
				window.location = url;
			});
			$("#homepageResetDesktop").click(function(){
				confirmIt = confirm("Are you sure you want to reset your desktop homepage?");
				if(confirmIt){
					url = "adminAction.php?action=resetDesktop";
					$.ajax({
						type: "GET",
						url : url,
						success : function () {
							alert("Desktop homepage layout reset.");
						}
					})
				}
			});
			$("#homepageResetMobile").click(function(){
				confirmIt = confirm("Are you sure you want to reset your mobile homepage?");
				if(confirmIt){
					url = "adminAction.php?action=resetMobile";
					$.ajax({
						type: "GET",
						url : url,
						success : function () {
							alert("Mobile homepage layout reset.");
						}
					})
				}
			});
			$("#showPHPInfo").click(function(){
				openWindow('adminAction.php?action=phpInfo','PHP info');
			});
			$("#showBlocks").click(function(){
				openWindow('adminAction.php?action=showBlocks','Blocks');
			});
			$("#showPlugins").click(function(){
				openWindow('adminAction.php?action=showPlugins','Plugins');
			});
			$("#showMenu").click(function(){
				location = "menu/menuTabs.php";
			});

			$("#logout").click(function(){
				url = "logout.php";
				window.location = url;
			});
			$("#registerOpener").click(function(){
				$("#registrationDetailsDiv").slideToggle(600);
			});
			$("#registerTemplate").click(function(){
				name = $("#registerName").val();
				if(name==""){
					alert("Please fill in your name");
					return false;
				}
				email = $("#registerMail").val();
				var atpos = email.indexOf("@");
				var dotpos = email.lastIndexOf(".");
				if (atpos<1 || dotpos<atpos+2 || dotpos+2>=email.length) {
					alert("Not a valid email address");
					return false;
				}
				alert("Thanks for registering the template. Registration and addition to user map will be confirmed to you shortly. If you do not get a confirmation email please contact me directly through the blog or at my email address, likewise if you still see the message template unregistered after you registered, let me know.\n\nPLEASE NOTE: Feel free to test the template and if you like it, please keep in mind this template is DONATIONWARE and your support is necessary to keep this project alive as I cannot afford to cover the costs myself and as there is now hundreds of users, the costs are not negligible.\n\n Enjoy using the template! Jachym");
				url = "adminAction.php?action=registerUser&name="+name+"&emailAddress="+email;
				$.ajax({
					type: "GET",
					url : url,
					success : function () {
						document.location.reload();
					}
				})
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
