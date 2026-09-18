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
	#	Blocks setup
	#
	# 	This page is the main administration page, which allows clearing the
	# 	cache or to enter the database editing page.
	#
	############################################################################

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	if($_SESSION['user']!="admin"){
		die("Unauthorized access.");
	}

	require("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	$blockNameSpace = $_GET['id'];

	if(file_exists('../homepage/blocks/'.$blockNameSpace.'/'.$blockNameSpace.'Config.txt')){
		$blockInfo = json_decode(file_get_contents('../homepage/blocks/'.$blockNameSpace.'/'.$blockNameSpace.'Config.txt'),true);
	}
	if(file_exists('../homepage/blocks/'.$blockNameSpace.'/settings.php')){
		include('../homepage/blocks/'.$blockNameSpace.'/settings.php');
	}

	// iterate variables
	foreach($blockInfo['variables'] as $variable){
		$variableList[] = $variable['variable'];
		if(!isset(${$variable['variable']})){
			${$variable['variable']} = $variable['default'];
		}
		if(${$variable['variable']}=== true){
			${$variable['variable']} = "true";
		}
		if(${$variable['variable']}=== false){
			${$variable['variable']} = "false";
		}
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader();?>
		<style>
			.inputSet{
				font-weight: bold;
				margin: 10px;
				margin-left: 20px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1><?php echo ($blockInfo['name'])?> Settings</h1>
				<form method="post" action="blockSave.php" id='blockSettingsForm'>
					<div style="text-align:left;font-size:1.2em;font-weight:bold;font-variant:small-caps">
						Version: <?php echo number_format($blockInfo['version'],1,".","")?>
					</div>
					<?php
						foreach($blockInfo['variables'] as $variable){
							echo "<br>";
							echo $variable['desc']."<br>";
							$varLength = strlen(${$variable['variable']});
							if($varLength<10){
								$varLength = 50;
							}
							if(${$variable['variable']}=="true"){
								echo "<select name='".$variable['variable']."' class='button inputSet'><option value='true' selected>True</option><option value='false'>False</option></select>";
							}
							else if(${$variable['variable']}=="false"){
								echo "<select name='".$variable['variable']."' class='button inputSet'><option value='true'>True</option><option value='false' selected>False</option></select>";
							}
							else{
								echo "<input name='".$variable['variable']."' value='".${$variable['variable']}."' class='button2 inputSet' size=".$varLength.">";
							}
						}
					?>
					<input type="hidden" name="id" value='<?php echo $blockNameSpace?>'>
					<input type="hidden" name="parameters" value='<?php echo implode(',',$variableList)?>'>
					<input type="hidden" name="version" value='<?php echo $blockInfo['version']?>'>
					<br><br>
					<div style="width:100%;text-align:center">
						<input type="submit" value="Save Settings" class="button2">
					</div>
					<br><br>
				</form>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<?php 
			if($enableKeyboard){
		?>
				<script src="<?php echo $pageURL.$path?>scripts/mousetrap.min.js"></script>
				<script>
					Mousetrap.bind(['alt+s'], function(e) {
						$("#blockSettingsForm").submit();
					});
				</script>
		<?php
			}
		?>
	</body>
</html>
