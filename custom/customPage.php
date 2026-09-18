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
	#	Custom Page
	#
	############################################################################
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

    $customPages = json_decode(file_get_contents("../admin/customPages.txt"), true);
    $pageCode = $customPages[$_GET['page']];

    $pageCode = str_replace("<script","",$pageCode);
    $pageCode = str_replace("< script","",$pageCode);
    $pageCode = str_replace("script/>","",$pageCode);
    $pageCode = str_replace("script />","",$pageCode);

    $pageCode = str_replace("iframe","",$pageCode);

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $_GET['page']?></title>
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
			<div class="textDiv">
				<?php echo $pageCode?>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
	