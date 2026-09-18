<?php

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	// load core files
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	// check if settings already exists and if so, load it, otherwise set parameters to default values
	if(file_exists("settings.php")){
		include("settings.php");
	}

	if(!isset($clientrawURL)){
		$clientrawURL = "../../clientraw.txt";
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader()?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main" style="text-align:center">
			<h1>Meteohub - Setup</h1>
			<form method="POST" action="saveSettings.php" target="_blank">
				<table style="width:98%;margin:0 auto">
					<tr>
						<td style="text-align:left;width:300px">
							URL
						</td>
						<td style="text-align:left">
							<input name="clientrawURL" class="button2" value="<?php echo $clientrawURL?>" size="20"><br>
							specify the url of the clientraw.txt, make sure you include http(s)://.... clientraw.txt or ideally use a relative path, for example ../../clientraw.txt.
						</td>
					</tr>
				</table>
				<div style="width:50%;text-align:center;margin:0 auto">
					<input type="submit" value="Save" class="button2">
				</div>
				<br>
				After saving, set up your CRON job for <?php echo $pageURL.$path?>plugins/meteohub/update.php?pass=<?php echo $updatePassword?>
				<br><br>
			</form>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>