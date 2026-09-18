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
	#	sitemap
	#
	############################################################################
	
	
	include("config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

    $sitemap = array();
    
    if(file_exists("sitemap.txt")){
        $sitemap = file_get_contents("sitemap.txt");
        $sitemap = json_decode($sitemap);
    }

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			#sitemapXMLDiv{
				opacity: 0.8;
				cursor: pointer;
			}
			#sitemapXMLDiv:hover{
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
			<div class="textDiv" style="width:90%">
				<div style="width:100%;text-align:center;padding-top:5px">
					<span class="fa fa-sitemap" style="font-size:3em"></span>
				</div>
				<h1><?php echo lang('sitemap','c')?></h1>
				<h2><?php echo $pageName?></h2>
				<div style="width:98%;margin:0 auto;text-align:right" id="sitemapXMLDiv">
					<span class="fa fa-file-code-o" style="font-size:1.5em"></span>&nbsp;XML
				</div>
				<?php 
					foreach($sitemap as $heading=>$data){
						if(!is_array($data)){
							echo "<a href='".$data."' target='_blank'><h3>".$heading."</h3></a>";
						}
						else{
							echo "<h3>".$heading."</h3>";
							echo "<table>";
							for($i=0;$i<count($data);$i++){
								echo "<tr>";
									echo "<td style='padding-left:25px'>";
										echo $data[$i][2];
									echo "</td>";
									echo "<td style='text-align:left;padding-left:10px'>";
										echo "<a href='".$data[$i][1]."' target='_blank'>".$data[$i][0]."</a>";
									echo "</td>";
								echo "</tr>";
							}
							echo "</table>";
						}
					}
				?>
			</div>
			<br><br>
		</div>
		<?php include($baseURL."footer.php");?>		
		<script type="text/javascript">
            $("#sitemapXMLDiv").click(function(){
				window.open("sitemapXML.php","_blank");
			})
		</script>
	</body>
</html>
