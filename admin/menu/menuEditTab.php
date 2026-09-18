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
	#	Menu setup
	#
	############################################################################

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	$tab = trim($_GET['tab']);

	// file and tab must exist, otherwise user won't get to this page
	$menuItems = json_decode(file_get_contents("menuItems.txt"),true);

	$tabContent = $menuItems[$tab]['content'];
	$tabLink = $menuItems[$tab]['link'];


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
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%">
			<h1><?php echo ucfirst($menuItems[$tab]['name'])?></h1>
			<p>
				IT IS ABSOLUTELY ESSENTIAL THAT YOU READ THE WIKI SECTION ABOUT THE MENU BEFORE YOU BEGIN THIS!
			</p>
			<p>
				If you want this tab be used as direct link - with no content, then enter the full url, including http(s), below. If you want this tab to be a normal menu with submenus, just leave the URL field blank and enter the menu syntax in the content textarea.
			</p>
			<form method="POST" action="menuSaveTabContent.php">
				Use this tab as a link, URL: <input class="button2" value="<?php echo $tabLink?>" size="50" name="link"><br><br>
				Content (if you want the tab to be used as a link, this must stay blank):
				<textarea name="content" class="button2" style="cursor:auto;text-align:left;display:block;width:100%;margin:0 auto" rows="30"><?php echo $tabContent?></textarea>
				<input type="hidden" name="tab" value="<?php echo $tab?>">
				<br><br>
				<div style="margin:0 auto;text-align:center">
					<input type="submit" class="button2" value="<?php echo lang('save','c')?>">
				</div>
				<br><br>
			</form>

			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>

		</script>
	</body>
</html>
