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
	#	Custom pages setup
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
	
	if(!isset($_GET['page'])){
        $pageNamespace = "myPage";
        $pageCode = "<h1>My first page</h1>";
    }
    else{
        $pageData = file_get_contents("customPages.txt");
        $pageData = json_decode($pageData,true);
        $pageNamespace = $_GET['page'];
        $pageCode = $pageData[$_GET['page']];
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
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv" style="width:90%">
			<h1>Create/Edit Custom Page</h1>
			<form action="customPageSave.php" method="POST">
				Namespace: <input name="namespace" class="button2" size="20" value="<?php echo $pageNamespace?>"><br><br>
				<textarea name="htmlCode" rows="40" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $pageCode?></textarea>
				<br><br>
				<div style="width:100%;text-align:center">
					<input type="submit" value="Save" class="button2">
				</div>
			</form>
			<br><br>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>		
	</body>
</html>
	