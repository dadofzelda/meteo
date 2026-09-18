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
	#	Info pages setup
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
	
	$type = $_GET['type'];

    if($type=="desktop" && file_exists("homepageLayoutDesktop.txt")){
		$homepageData = json_decode(file_get_contents("homepageLayoutDesktop.txt"),true);
		$columnWidths = $homepageData['desktop']['columnWidths'];
	}
	else if($type=="mobile" && file_exists("homepageLayoutMobile.txt")){
        $homepageData = json_decode(file_get_contents("homepageLayoutMobile.txt"),true);
		$columnWidths = $homepageData['mobile']['columnWidths'];
	}
	else{
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
				<h1>Homepage Change Widths - <?php echo ucwords($type)?></h1>
                <p>
                    Make sure the numbers add up to 100, delimit them with a comma (,).<br><br>
                    <strong>NOTE! The number of columns cannot be changed, only the widths. If you want to change the number of columns you need to reset your homepage. This is only for changing the column widths, so the total number of columns MUST correspond to the original number of columns.</strong>
                </p>
				<br>
				<form action="homepageChangeWidthsSave.php?type=<?php echo $type?>" method="post">
					<div style="width:98%;text-align:center;margin:0 auto">
						<input name="newColumnWidths" value="<?php echo implode(",",$columnWidths)?>" class="button2">
						<br><br>
						<input type="submit" class="button" value="Save">
					</div>
				</form>
			</div>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>		
		<script type="text/javascript">
			
		</script>
	</body>
</html>
