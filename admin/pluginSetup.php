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
	
	// Installed plugins
	$dirs = array_filter(glob('../plugins/*'), 'is_dir');
	$installedPlugins = array();
	foreach($dirs as $dir){
		$setup = false;
		$pluginNamespace = str_replace("../plugins/","",$dir);
		if(file_exists('../plugins/'.$pluginNamespace.'/'.$pluginNamespace.'Version.txt')){
			$version = trim(file_get_contents('../plugins/'.$pluginNamespace.'/'.$pluginNamespace.'Version.txt'));
		}
		else{
			$version = "";
		}
		if(file_exists('../plugins/'.$pluginNamespace.'/setup.php')){
			$setup = true;
		}
		else{
			$setup = false;
		}
		$installedPlugins[] = array($pluginNamespace,$version,$setup);
	}
	
	// Load latest versions
	$pluginsLatest = json_decode(curl_get_contentsPlugins($meteotemplateURL."/web/pluginVersions.txt"),true);
	
	function curl_get_contentsPlugins($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
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
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv">
			<h1>Plugins setup</h1>
			<div style="width:98%;margin:0 auto;margin-top:10px;background:#a00000;border:1px solid white;border-radius:5px;padding:1%"> 
				Please note! Do NOT update or install new plugins if you are not using the latest version of the main template. If you use older template version new blocks or updated blocks might cause compatibility issues or even make your template completely non-functional. If you decide not to install the latest template version, you can still use Meteotemplate, but you must not install or update anything.
			</div>
			<h2>Installed plugins</h2>
			<table class="table">
				<thead>
					<tr>
						<th >Plugin</th>
						<th style="text-align:center">My version</th>
						<th style="text-align:center">Latest version</th>
						<th></th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php
					for($i=0;$i<count($installedPlugins);$i++){
				?>
						<tr>
							<td style="text-align:left">
								<?php echo $installedPlugins[$i][0]?>
							</td>
							<td style="text-align:center">
								<?php
									if(isset($installedPlugins[$i][1])){
										if(is_numeric($installedPlugins[$i][1])){
											echo number_format($installedPlugins[$i][1],1,".","");
										}
										else{
											echo $installedPlugins[$i][1];
										}
									}
								?>
							</td>
							<td style="text-align:center">
								<?php
									if(isset($pluginsLatest[$installedPlugins[$i][0]])){
										if(is_numeric($pluginsLatest[$installedPlugins[$i][0]])){
											echo number_format($pluginsLatest[$installedPlugins[$i][0]],1,".","");
										}
										else{
											echo $pluginsLatest[$installedPlugins[$i][0]];
										}
									}
								?>
							</td>
							<td>
								<?php
									if(isset($pluginsLatest[$installedPlugins[$i][0]]) && isset($installedPlugins[$i][1])){
										if($pluginsLatest[$installedPlugins[$i][0]]>$installedPlugins[$i][1]){
											echo "<span class='termWindow' onclick='openWindow(\"pluginUpdate.php?id=".$installedPlugins[$i][0]."_".number_format($pluginsLatest[$installedPlugins[$i][0]],1,".","")."\")'><img src='../icons/update.png' class='updateIcon'></span>";
										}
									}
								?>
							</td>
							<td>
								<?php
									if($installedPlugins[$i][2]){
										if(file_exists('../plugins/'.$installedPlugins[$i][0].'/settings.php')){
											echo "<a href='../plugins/".$installedPlugins[$i][0]."/setup.php'>Edit settings</a>";
										}
										else{
											echo "<a href='../plugins/".$installedPlugins[$i][0]."/setup.php'>Create settings</a>";
										}
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
							location = "pluginSetup.php";
						}  
					});
				$dialog.dialog('open');
			}
		</script>
		
	</body>
</html>
	