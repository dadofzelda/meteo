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
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	// Installed blocks
	$dirs = array_filter(glob('../homepage/blocks/*'), 'is_dir');
	$installedBlocks = array();
	foreach($dirs as $dir){
		$blockNamespace = str_replace("../homepage/blocks/","",$dir);
		if(file_exists('../homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt')){
			$blockInfo = json_decode(file_get_contents('../homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt'),true);
			$version = $blockInfo['version'];
			$name = $blockInfo['name'];
			if(is_array($blockInfo)){
				if(array_key_exists('variables',$blockInfo)){
					if(file_exists("../homepage/blocks/".$blockNamespace."/settings.php")){
						$setup = "<a href='blockSettings.php?id=".$blockNamespace."'>Edit settings</a>";
					}
					else{
						$setup = "<a href='blockSettings.php?id=".$blockNamespace."'>Create settings</a>";
					}
				}
			}
			else{
				$setup = "";
			}
			$installedBlocks[] = array($blockNamespace,1,$version,$setup,$name);
		}
		else{
			$installedBlocks[] = array($blockNamespace,0);
		}
	}
	
	// Load latest versions
	$blocksLatest = json_decode(loadContent($meteotemplateURL."/web/blockVersions.txt", 5),true);
?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			.updateIcon{
				width:100%;
				max-width:20px;
				opacity:0.8;
				cursor:pointer;
			}
			.updateIcon:hover{
				opacity:1;
			}
			#cacheDelete{
				opacity: 0.8;
				cursor: pointer;
			}
			#cacheDelete:hover{
				opacity: 1.0;
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
			<h1>Blocks setup</h1>
			<div style="width:98%;margin:0 auto;margin-top:10px;background:#a00000;border:1px solid white;border-radius:5px;padding:1%"> 
				Please note! Do NOT update or install new blocks if you are not using the latest version of the main template. If you use older template version new blocks or updated blocks might cause compatibility issues or even make your homepage completely non-functional. If you decide not to install the latest template version, you can still use Meteotemplate, but you must not install or update anything.
			</div>
			<h2>Installed blocks</h2>
			<div style="width:100%;margin:0 auto">
				<input type="button" class="button2" value="Install new blocks" onclick="location='blockInstall.php'">
			</div>
			<table class="table">
				<thead>
					<tr>
						<th style="text-align:center">Label</th>
						<th style="text-align:center">Name</th>
						<th style="text-align:center">My version</th>
						<th style="text-align:center">Latest version</th>
						<th style="text-align:center"></th>
						<th style="text-align:center">Settings</th>
						<th style="text-align:center">Cache</th>
					</tr>
				</thead>
				<tbody>
				<?php
					for($i=0;$i<count($installedBlocks);$i++){
				?>
						<tr>
							<td>
								<?php echo $installedBlocks[$i][0]?>
							</td>
							<td>
								<?php echo $installedBlocks[$i][4]?>
							</td>
							<td>
								<?php 
									if($installedBlocks[$i][1]==1){
										echo number_format($installedBlocks[$i][2],1,".","");
									}
								?>
							</td>
							<td>
								<?php 
									echo number_format($blocksLatest[$installedBlocks[$i][0]],1,".","");
								?>
							</td>
							<td>
								<?php
									if($blocksLatest[$installedBlocks[$i][0]]>$installedBlocks[$i][2]){
										echo "<span class='termWindow' onclick='openWindow(\"blockUpdate.php?id=".$installedBlocks[$i][0]."_".number_format($blocksLatest[$installedBlocks[$i][0]],1,".","")."\")'><img src='../icons/update.png' class='updateIcon'></span>";
									}
								?>
							</td>
							<td>
								<?php
									echo $installedBlocks[$i][3];
								?>
							</td>
							<td> 
								<?php 
									if(is_dir("../homepage/blocks/".$installedBlocks[$i][0]."/cache")){
								?> 
										<span id="cacheDelete" class="fa fa-trash-o" style="font-size:1.3em" onclick="deleteCache('<?php echo $installedBlocks[$i][0]?>')"></span>
								<?php
									}
								?>
							</td>
						</tr>	
				<?php
					}
				?>
				</tbody>
			</table>
			<br><br>
		</div>
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
			function openWindow(url,title){
				dialogHeight = screen.height*0.7;
				dialogWidth = screen.width*0.7;
				var $dialog = $('<div style="overflow:hidden;background:white;color:black"></div>')
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
						},
						close : function(){
							location = "blockSetup.php";
						} 
					});
				$dialog.dialog('open');
			}
			function deleteCache(namespace){
				window.open("deleteBlockCache.php?block=" + namespace);
			}
		</script>
		
	</body>
</html>