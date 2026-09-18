<?php
	
	############################################################################
	# 	
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Links page
	#
	# 	Page with custom links.
	#
	############################################################################
	#	
	#
	# 	v19.0 Cranberry 2023-08-20
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

	// load info data 
	if(file_exists("../../admin/infoPages.txt")){
		$rawText = json_decode(file_get_contents("../../admin/infoPages.txt"),true);
		if(isset($rawText['links'])){
			$linksText = $rawText['links'];
			// split lines 
			$linksText = explode("\r\n",$linksText);
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("links",'c')?></title>
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
				<h1><?php echo lang('links','c')?></h1>
				<?php 
					for($i=0;$i<count($linksText);$i++){
						$line = $linksText[$i];
						$line = trim($line);
						if(substr($line,0,2)==">>"){
							$line = str_replace(">>","",$line);
							$line = trim($line);
							echo "<h3 style='padding-bottom:5px'>".$line."</h3>";
						}
						else{
							$linkData = explode("|",$line);
							$linkData = array_map('trim',$linkData);
							echo "<table style='margin-left:10px'><tr><td style='text-align:center;width:80px'>";
							echo "<span class='".$linkData[3]."' style='font-size:2em'></span>";
							echo "</td>";
							echo "<td style='text-align:left;padding-left:10px'>";
							echo "<a href='".$linkData[1]."' target='_blank' style='font-weight:bold;font-variant:small-caps'>".$linkData[0]."</a>";
							if($linkData[2]!=""){
								echo " - ".$linkData[2];
							} 
							echo "</td></tr></table>";
						}
					}
				?>
			</div>
			<br><br>
		</div>
		<?php include($baseURL."footer.php");?>
	</body>
</html>
	