<?php
	############################################################################
	#
	#	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#   Brno, Czech Republic
	# 	First release: 2015
	#
	############################################################################
	#
	#	Main Desktop Homepage
	#
	############################################################################
	#
	#
	# 	v19.0 Nectarine 2019-06-27
	#
	############################################################################

	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	require_once("config.php");

	// not an admin
	if(!isset($_SESSION['user'])){
		$_SESSION['user'] = "user";
	}

	// debug mode?
	if(isset($_GET['errors'])){
		$showErrors = "?errors";
	}
	else{
		$showErrors = "";
	}

	$blockColor = $design; // set block color to primary design color from config

	$adminBlocks = array(); // was added in v13, so make sure user has it set

	// load user block settings
	if(file_exists("admin/homepageLayoutDesktop.txt")){
		$homepageData = json_decode(file_get_contents("admin/homepageLayoutDesktop.txt"),true);
		$columnWidths = $homepageData['desktop']['columnWidths'];
		$columns = $homepageData['desktop']['columns'];
		$theme = $homepageData['desktop']['theme'];
		$highlightedBlocks = $homepageData['desktop']['highlightedBlocks'];
		$headerBlock = $homepageData['desktop']['headerBlock'];
		$footerBlock = $homepageData['desktop']['footerBlock'];
		$adminBlock = $homepageData['desktop']['adminBlock'];
		$adminBlocks = explode(";",$adminBlock);
	}
	// no blocks created yet, use default Meteotemplate logo and layout
	else{
		$columnWidths = array(25,50,25);
		$column1 = array();
		$column2 = array("logo");
		$column3 = array();

		$columns = array($column1, $column2, $column3);
		$theme = "dark";
		$highlightedBlocks = "";
		$headerBlock = "";
		$footerBlock = "";
	}

	if(count($adminBlocks)>0){
		$adminBlocks = array_map('trim', $adminBlocks); // in case user thought it would be fun to include extra spaces...
	}

	// variable theme -> check if it is daytime or nightime
	if($theme=="sun"){
		$sunRiseTheme = date_sunrise(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		$sunSetTheme = date_sunset(time(),SUNFUNCS_RET_TIMESTAMP,$stationLat,$stationLon,90.5);
		$theme = time() < $sunRiseTheme || time() > $sunSetTheme ? "dark" : "light";
	}

	include("homepage/css/themeSetter.php");
	include("css/design.php");
	include("header.php");

	// check if update checks enabled 
	if($_SESSION['user']=="admin" && $templateUpdateCheck){
		$updateWarning = "";
		if(!file_exists("cache/latestVersions_".date('Ymd').".txt")){
			// delete all other dates
			$dirs = array_filter(glob('cache/latestVersions*.txt'));
			foreach($dirs as $dir){
				unlink($dir);
			}
			$latestVersions = curlMain($meteotemplateURL."/web/latestVersions.txt",3);
			if($latestVersions==""){
				$latestVersions = file_get_contents($meteotemplateURL."/web/latestVersions.txt");
			}
			if($latestVersions!=""){
				file_put_contents("cache/latestVersions_".date('Ymd').".txt",$latestVersions);
			}
			if(!file_exists("cache/latestVersions_".date('Ymd').".txt")){
				$updateWarning = "The cache file for latest versions check not found. This could be a problem with permissions for the cache folder or a problem with communication between my server and your server. Either way, please DISABLE the latest version check for now, because your server will otherwise be contacting my server upon each load (since cache file is not created) and in such case I will be forced to block your IP completely. You can contact me to see if something can be done about this.";
			}
		}
	}

	if($_SESSION['user']=="admin" && $templateUpdateCheck==true && file_exists("cache/latestVersions_".date('Ymd').".txt")){
		$latestVersions = json_decode(file_get_contents("cache/latestVersions_".date('Ymd').".txt"),true);
		
		// blocks
		$blocksToUpdate = array(); 
		$dirs = array_filter(glob('homepage/blocks/*'), 'is_dir');
		$installedBlocks = array();
		foreach($dirs as $dir){
			$blockNamespace = str_replace("homepage/blocks/","",$dir);
			if(file_exists('homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt')){
				$blockInfo = json_decode(file_get_contents('homepage/blocks/'.$blockNamespace.'/'.$blockNamespace.'Config.txt'),true);
				$version = $blockInfo['version'];
				$name = $blockInfo['name'];
				$installedBlocks[] = array($blockNamespace,1,$version,$name);
			}
			else{
				$installedBlocks[] = array($blockNamespace,0);
			}
		}
		for($i=0;$i<count($installedBlocks);$i++){
			if($latestVersions['blocks'][$installedBlocks[$i][0]]>$installedBlocks[$i][2]){
				$blocksToUpdate[] = $installedBlocks[$i][0];
			}
		}

		// plugins
		$pluginsToUpdate = array();
		$dirs = array_filter(glob('plugins/*'), 'is_dir');
		$installedPlugins = array();
		foreach($dirs as $dir){
			$setup = false;
			$pluginNamespace = str_replace("plugins/","",$dir);
			if(file_exists('plugins/'.$pluginNamespace.'/'.$pluginNamespace.'Version.txt')){
				$versionPlugin = trim(file_get_contents('plugins/'.$pluginNamespace.'/'.$pluginNamespace.'Version.txt'));
			}
			else{
				$versionPlugin = "";
			}
			$installedPlugins[] = array($pluginNamespace,$versionPlugin);
		}
		for($i=0;$i<count($installedPlugins);$i++){
			if(isset($latestVersions['plugins'][$installedPlugins[$i][0]]) && isset($installedPlugins[$i][1])){
				if($latestVersions['plugins'][$installedPlugins[$i][0]]>$installedPlugins[$i][1]){
					$pluginsToUpdate[] = $installedPlugins[$i][0];
				}
			}
		}
	}

	$menus = 0;

	// disable temporarily disabled blocks
	for($i=0;$i<count($columns);$i++){
		for($a=0;$a<count($columns[$i]);$a++){
			if(substr($columns[$i][$a],0,2)=="//"){
				unset($columns[$i][$a]);
			}
		}
		$columns[$i] = array_values($columns[$i]);
	}
	
	// block extensions
	if($blockMaximizeDesktop && !$blockExportDesktop){
		$blockExtraText = '<div style="display:inline-block;width:50%;margin-left:50%;text-align:right"><span class="termWindow fa fa-window-maximize tooltip" onclick="openFullBlock(\\\'indexFull.php?block=XXX\\\')" title="'.lang("fullscreen","c").'"></span></div>';
	}
	else if($blockExportDesktop && !$blockMaximizeDesktop){
		$blockExtraText = '<div style="display:inline-block;width:50%;margin-left:50px;text-align:right"><span class="termWindow fa fa-image tooltip" onclick="exportBlockImage(\\\'XXX\\\')" title="'.lang("export as image","c").'"></span></div>';
	}
	else if($blockExportDesktop && $blockMaximizeDesktop){
		$blockExtraText = '<div style="display:inline-block;width:50%;margin-left:50%;text-align:right"><span class="termWindow fa fa-window-maximize tooltip" onclick="openFullBlock(\\\'indexFull.php?block=XXX\\\')" title="'.lang("fullscreen","c").'"></span><span class="termWindow fa fa-image tooltip" onclick="exportBlockImage(\\\'XXX\\\')" title="'.lang("export as image","c").'"></span></div>';
	}
	else{
		$blockExtraText = "";
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="description" content="<?php echo $pageDesc?>">
		<title><?php echo $pageName?></title>
		<?php metaHeader();?>
		<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $googleMapsAPIKey?>"></script>
		<script src="//code.highcharts.com/stock/highstock.js"></script>
		<script src="//code.highcharts.com/stock/highcharts-more.js"></script>
		<?php include("homepage/css/style.php");?>
		<?php
			if($theme=="light"){
				$menuBlockLoaderColor = "rgba(0, 0, 0, 0.2)";
				$menuBlockLoaderColor2 = "rgba(0, 0, 0, 0.8)";
			}
			else{
				$menuBlockLoaderColor = "rgba(255, 255, 255, 0.2)";
				$menuBlockLoaderColor2 = "rgba(255, 255, 255, 0.8)";
			}
		?>
		<style>
			.termWindow{
				cursor: pointer;
				opacity: 0.8;
				padding-right:10px;
			}
			.termWindow:hover{
				opacity:1;
			}
			.menuBlockMenu{
				opacity:0.7;
				cursor: pointer;
			}
			.menuBlockMenu:hover{
				opacity:1;
			}
			.highlightSubmenuBlock:hover{
				background: #<?php echo $color_schemes[$design2]['700']?>;
			}
			.loaderMenuBlock {
			  margin: 60px auto;
			  font-size: 6px;
			  position: relative;
			  text-indent: -9999em;
			  border-top: 1.1em solid <?php echo $menuBlockLoaderColor?>;
			  border-right: 1.1em solid <?php echo $menuBlockLoaderColor?>;
			  border-bottom: 1.1em solid <?php echo $menuBlockLoaderColor?>;
			  border-left: 1.1em solid <?php echo $menuBlockLoaderColor2?>;
			  -webkit-transform: translateZ(0);
			  -ms-transform: translateZ(0);
			  transform: translateZ(0);
			  -webkit-animation: load8 1.1s infinite linear;
			  animation: load8 1.1s infinite linear;
			}
			.loaderMenuBlock,
			.loaderMenuBlock:after {
			  border-radius: 50%;
			  width: 10em;
			  height: 10em;
			}
			.blockSettingsHover{
				opacity: 0.8;
				cursor: pointer;
			}
			.blockSettingsHover:hover{
				opacity: 1;
			}
			<?php
				if($highlightedBlocks!=""){
					$highlightedBlocksArr = explode(",",$highlightedBlocks);
					if($theme=="dark"){
						$themeNumber = '900';
						$themeNumber2 = '300';
					}
					else{
						$themeNumber = '100';
						$themeNumber2 = '700';
					}
					foreach($highlightedBlocksArr as $highlightedBlock){
			?>
						#<?php echo trim($highlightedBlock)?>Block{
							background: #<?php echo $color_schemes[$design2][$themeNumber]?>;
							border: 2px solid #<?php echo $color_schemes[$design2][$themeNumber2]?>;
						}
			<?php
					}
				}
			?>
			<?php 
				// hide admin blocks if not admin
				if($_SESSION['user']!="admin"){
					for($i=0;$i<count($adminBlocks);$i++){
			?>
						#<?php echo $adminBlocks[$i]?>Block{
							display: none;
						}
			<?php
					}
				}
			?>
			<?php 
				if($hideMultipleBlockBorder){
			?>
					.multipleTable{
						border: <?php echo $customBlockBorderWidth?> solid #<?php echo $theme=="dark" ? $color_schemes[$design2]['700'] : $color_schemes[$design2]['200']?>;
						border-radius: <?php echo $customBlockRadius?>;
					}
					.multipleTD{
						border: 0px;
						border-radius: 0px;
					}
			<?php 
				}
			?>
			<?php 
				if($flatDesignDesktop){
			?>
					.block{
						box-shadow: 0px!important;
						border: 0px!important;
						border-radius: 0px!important;
						-webkit-box-shadow: 0px!important;
						background: none!important;
						padding-bottom: 10px!important;
						padding-top: 10px!important;
					}
					#mainHomepageTable{
						width: 100%!important;
						background: #<?php echo $theme=="dark" ? $color_schemes[$design2]['900'] : $color_schemes[$design2]['300']?>;
						border-top: 2px solid #<?php echo $theme=="dark" ? $color_schemes[$design2]['200'] : $color_schemes[$design2]['800']?>;
					}
			<?php 
				}
			?>
			.ui-dialog .ui-dialog-content{
				padding:0;
			}
			.ui-widget-header{
				background:none;
				font-size:1.5em;
				font-variant:small-caps;
				border: 0px;
			}
			.ui-dialog .ui-dialog-titlebar{
				padding: 0px;
			}
		</style>
	</head>
	<!--<body style="overflow-x:hidden">-->
	<body>
		<div id="main_top">
			<?php
				if($_SESSION['user']=="admin" && $templateUpdateCheck==true && file_exists("cache/latestVersions_".date('Ymd').".txt")){
					echo $updateWarning;
					// blocks
					if(count($blocksToUpdate)>0){
			?>
						<div style="width:100%;margin: 0 auto;background:#<?php echo $color_schemes[$design2]['900']?>">
							<strong>New versions available for these blocks:</strong><br>
							<?php
								echo implode(", ",$blocksToUpdate);
							?>
						</div>
			<?php
					}
					if(count($pluginsToUpdate)>0){
			?>
						<div style="width:100%;margin: 0 auto;background:#<?php echo $color_schemes[$design2]['900']?>">
							<strong>New versions available for these plugins:</strong><br>
							<?php
								echo implode(", ",$pluginsToUpdate);
							?>
						</div>
			<?php
					}
					if($templateVersion<$latestVersions['template']){
			?>
						<div style="width:100%;margin: 0 auto;background:#<?php echo $color_schemes[$design2]['900']?>">
							<strong><span style="color:red">New version of Meteotemplate is available (<?php echo number_format($latestVersions['template'],1,".","")?>). If you do not update your template you must not update any blocks or plugins.</span></strong><br>
						</div>
			<?php
					}
				}
				if($_SESSION['user']=="admin"){
					if(!is_writable("cache")){
						echo "Your cache directory is not writable! Make sure cache folder in template root is writable";
					}
				}
			?>
			<?php bodyHeader();?>
			<?php include("menu.php");?>
		</div>

		<div id="main" style="text-align:center">
			<?php
				if(!file_exists("admin/homepageLayoutDesktop.txt")){
					echo "This is just a default version of your homepage, you need to set it up in the control panel.";
				}
			?>
			<table style="width:98%;margin:0 auto" id="mainHomepageTable">
				<?php
					if($headerBlock!=""){
						$headerBlock = trim($headerBlock);
						// split
						$headerBlocks = explode(";",$headerBlock);
						for($i=0;$i<count($headerBlocks);$i++){
				?>
							<tr>
								<td colspan="<?php echo count($columns)?>" style="padding:5px">
									<div class="block" style="margin-bottom:0px" id="<?php echo $headerBlocks[$i]?>Block"><img class="mtSpinner" style="width:80%;max-width:60px" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
								</td>
							</tr>
				<?php
						}
					}
				?>
				<tr>
					<td style="padding:0px;margin:0px">
						<div style="display:flex;flex-wrap:wrap">
					<?php
						for($i=0;$i<count($columns);$i++){
					?>
							<div style="flex:<?php echo $columnWidths[$i]*0.95?>%;padding:10px;margin:0px">
								<?php
									for($a=0;$a<count($columns[$i]);$a++){
										if (strpos($columns[$i][$a], 'multipleBlock') !== false) {
											preg_match("/multipleBlock\((.*?)\)/",$columns[$i][$a],$matches);
											$blocksMultiple = explode(";",$matches[1]);
											echo "<table style='width:100%' class='multipleTable' cellspacing='0px'><tr>";
											foreach($blocksMultiple as $blockMultiple){
												$blockMultiple = explode(",",$blockMultiple);
												echo "<td style='width:".$blockMultiple[1]."%;margin:0 auto' class='multipleTD'>";

													if (strpos($blockMultiple[0], 'subBlock') !== false) {
														preg_match("/subBlock\[(.*?)\]/",$blockMultiple[0],$matches);
														$subBlocks = explode("&",$matches[1]);
														echo "<table style='width:100%'>";
														foreach($subBlocks as $subBlock){
															$loadedBlocks[] = $subBlock;
															echo "<tr><td>";
								?>
																<div class="block" id="<?php echo $subBlock?>Block" style="width:98%;padding-left:0px;padding-right:0px;border:0px"><img class="mtSpinner" style="width:80%;max-width:60px" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
								<?php
															echo "</tr></td>";
														}
														echo "</table>";
													}
													else{
														$loadedBlocks[] = $blockMultiple[0];
								?>
															<div class="block" id="<?php echo $blockMultiple[0]?>Block" style="width:98%;padding-left:0px;padding-right:0px;border:0px"><img class="mtSpinner" style="width:80%;max-width:60px" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
								<?php
													}
												echo "</td>";
											}
											echo "</tr></table>";
										}
										else if(strpos($columns[$i][$a], 'menuBlock') !== false){
											$thisBlockName = "";
											preg_match("/menuBlock\((.*?)\)/",$columns[$i][$a],$matches);
											$blocksMenu = explode(";",$matches[1]);
											// check for admin blocks
											for($s=0;$s<count($blocksMenu);$s++){
												if(in_array($blocksMenu[$s],$adminBlocks)){
													if($_SESSION['user']!="admin"){
														unset($blocksMenu[$s]); // delete block if it is an admin block and we are not logged in as admin
													}
												}
											}
											$blocksMenu = array_values($blocksMenu); // reset array numbering
											$menus++;
											// see if we have name
											$menuBlockName = explode(",",$blocksMenu[0]);
											if(count($menuBlockName)>1){
												$thisBlockName = trim($menuBlockName[0]);
												$thisBlockName = strtolower($thisBlockName);
												$thisBlockName = lang($thisBlockName,'w');
												// replace first namespace
												$blocksMenu[0] = trim($menuBlockName[1]);
											}
								?>
											<div class="block">
												<?php
													if($thisBlockName!=""){
														echo '<span style="font-weight:bold;font-variant:small-caps;font-size:1.3em">'.$thisBlockName.'</span>';
													}
												?>
												<div style="position:relative;margin-bottom:10px">
													<div style="width:98%;text-align:left;margin:0 auto" class="menuBlockMenu" id="menuBlockMenu<?php echo $menus?>">
														<img src="<?php echo $pageURL.$path?>icons/menu<?php echo ucfirst($theme)?>.png" style="width:25px">
													</div>
													<div style="position:absolute;top:26px;left:8px;color:white;background:#<?php echo$color_schemes[$design2]['900']?>;z-index:10;display:none;padding:10px;padding-right:20px;text-align:left;border-radius:10px;border:1px solid #<?php echo $color_schemes[$design2]['700']?>" id="menuBlockMenuContent<?php echo $menus?>">
														<?php
															for($m=0;$m<count($blocksMenu);$m++){
																$loadedBlocks[] = $blocksMenu[$m];
																if(!file_exists("homepage/blocks/".$blocksMenu[$m]."/".$blocksMenu[$m]."Block.php")){
																	$missingMenuBlocks[] = $blocksMenu[$m];
																}
														?>
																<div style="width:98%;margin:0 auto;padding-top:2px;padding-bottom:2px;padding-left:8px;padding-top:3px;padding-bottom:3px;border-radius:10px" class="menuBlockMenu highlightSubmenuBlock" onclick="loadMenuBlock('<?php echo $menus?>','<?php echo $blocksMenu[$m]?>')"><?php echo lang($blocksMenu[$m],'-')?></div>
														<?php
															}
														?>
													</div>
												</div>
												<div style="width:98%;position:relative;margin:0 auto" id="menu<?php echo $menus?>">

												</div>
												<script>
													// load first block from menu block and append full-screen if selected
													$("#menu<?php echo $menus?>").load("homepage/blocks/<?php echo $blocksMenu[0]?>/<?php echo $blocksMenu[0]?>Block.php",function(){
														$('<?php echo str_replace("XXX",$blocksMenu[0],$blockExtraText)?>').appendTo("#menu<?php echo $menus?>");
														$(".tooltip").tooltipster();
													});
													// attach menu opener
													$("#menuBlockMenu<?php echo $menus?>").click(function(){
														$("#menuBlockMenuContent<?php echo $menus?>").slideToggle();
													});
												</script>
											</div>
								<?php
										}
										else{
											$loadedBlocks[] = $columns[$i][$a];
								?>
											<div class="block" id="<?php echo $columns[$i][$a]?>Block"><img class="mtSpinner" style="width:80%;max-width:60px" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
								<?php
										}
									}
								?>
							</div>
					<?php
						}
					?>
						</div>
					</td>
				</tr>
				<?php
					if($footerBlock!=""){
						$footerBlock = trim($footerBlock);
						$footerBlocks = explode(";",$footerBlock);
						for($i=0;$i<count($footerBlocks);$i++){
				?>
							<tr>
								<td colspan="<?php echo count($columns)?>" style="padding:5px">
									<div class="block" style="margin-bottom:0px" id="<?php echo $footerBlocks[$i]?>Block"><img class="mtSpinner" style="width:80%;max-width:60px" src="<?php echo $pageURL.$path?>icons/logo.png"></div>
								</td>
							</tr>
				<?php
						}
					}
				?>
			</table>
			<br>
			<?php 
				if(isset($missingMenuBlocks)){
					echo "Following menu blocks were not found (check they are installed and check spelling and capitalization.";
					echo "<br><br>";
					echo "<b>Missing blocks: ".implode(",",$missingMenuBlocks);
					echo "</b><br>";
				}
			?>
			<?php
				if($_SESSION['user']=="admin"){
			?>
					<div style="width:98%;margin:0 auto;text-align:right">
						<span class='fa fa-cogs blockSettingsHover tooltip' id="blockSettingsAdminOpener" style="font-size:2em" title="Set up blocks currently on homepage">
					</div>
					<div id="blockSettingsAdmin" style="width:98%;margin:0 auto">
						<h2 style="color:white">Blocks currently on homepage which have some settings</h2>
						<?php
								for($a=0;$a<count($loadedBlocks);$a++){
									if(file_exists('homepage/blocks/'.$loadedBlocks[$a].'/'.$loadedBlocks[$a].'Config.txt')){
										$blockInfo = json_decode(file_get_contents('homepage/blocks/'.$loadedBlocks[$a].'/'.$loadedBlocks[$a].'Config.txt'),true);
										$name = $blockInfo['name'];
										if(array_key_exists('variables',$blockInfo)){
						?>
											<div style="width:98%;margin:0 auto;text-align:left">
												<table>
													<tr>
														<td>
															<img src="<?php echo $pageURL.$path?>icons/settings.png" class="blockSettingsHover" style="width:30px" onclick="window.location.href=('admin/blockSettings.php?id=<?php echo $loadedBlocks[$a]?>')">
														</td>
														<td>
															<?php echo $name?>
														</td>
													</tr>
												</table>
											</div>
						<?php
										}
									}
								}

						?>
					</div>
			<?php
				}
			?>
		</div>
		<a href="" id="gotop" style="display: none;">
			<input type="button" class="button" value="<?php echo lang('gotop','c')?>">
		</a>
		<?php include($baseURL."footer.php");?>
		<script>
			function loadMenuBlock(menuNumber,menuBlockLoad){
				$('#menu' + menuNumber).html('<div class=\'loaderMenuBlock\'>Loading...</div>');
				$('#menu' + menuNumber).load('homepage/blocks/' + menuBlockLoad + '/' + menuBlockLoad + 'Block.php<?php echo $showErrors?>', function(){
					stringExtra = '<?php echo $blockExtraText;?>'; // create string to append for menu block
					stringExtra = stringExtra.replace("exportBlockImage(\'XXX\')","exportBlockImage(\'#menu" + menuNumber + "\')"); // replace with current menu block to load
					stringExtra = stringExtra.replace("XXX",menuBlockLoad); // replace 2nd occurrence
					$(stringExtra).appendTo('#menu' + menuNumber); // append to switched menu block
					$(".tooltip").tooltipster();
				});
				$('#menuBlockMenuContent' + menuNumber).slideToggle();
			}
			$(document).ready(function(){
				<?php
					// HEADER BLOCKS
					// if header block is present, load it
					if($headerBlock!=""){
						$headerBlock = trim($headerBlock);
						$headerBlocks = explode(";",$headerBlock);
						for($i=0;$i<count($headerBlocks);$i++){
							$headerBlocks[$i] = trim($headerBlocks[$i]); // if user decides they want to include extra spaces...
				?>
							<?php 
								if(file_exists("homepage/blocks/".$headerBlocks[$i]."/".$headerBlocks[$i]."Block.php")){
									// this block is for admin only?
									if(in_array($headerBlocks[$i],$adminBlocks)){
										// admin logged in, show it
										if($_SESSION['user']=="admin"){
							?>
											$('#<?php echo $headerBlocks[$i]?>Block').load("homepage/blocks/<?php echo $headerBlocks[$i]?>/<?php echo $headerBlocks[$i]?>Block.php<?php echo $showErrors?>", function() {
												$(".tooltip").tooltipster();
												$('<?php echo str_replace("XXX",$headerBlocks[$i],$blockExtraText)?>').appendTo( "#<?php echo $headerBlocks[$i]?>Block" );
											});
							<?php
										}
										// admin block, but not logged in as admin, hide it
										else{
							?>
											$('#<?php echo $headerBlocks[$i]?>Block').hide();
							<?php
										}
									}
									// header not an admin block, always show
									else{
							?>
										$('#<?php echo $headerBlocks[$i]?>Block').load("homepage/blocks/<?php echo $headerBlocks[$i]?>/<?php echo $headerBlocks[$i]?>Block.php<?php echo $showErrors?>", function() {
											$(".tooltip").tooltipster();
											$('<?php echo str_replace("XXX",$headerBlocks[$i],$blockExtraText)?>').appendTo( "#<?php echo $headerBlocks[$i]?>Block" );
										});
							<?php
									}
								}
							?>
							<?php 
								if(!file_exists("homepage/blocks/".$headerBlocks[$i]."/".$headerBlocks[$i]."Block.php")){
							?>
									$("#<?php echo $headerBlocks[$i]?>Block").html("<br>Block <?php echo $headerBlocks[$i]?> not found. Check that you uploaded the block to the correct folder and also make sure you do not have a typo in the homepage setup.<br><br>");
							<?php 
								}
							?>
				<?php
						}
					}
				?>
				<?php
					// FOOTER BLOCK
					// if footer block is present, load it
					if($footerBlock!=""){
						$footerBlock = trim($footerBlock);
						$footerBlocks = explode(";",$footerBlock);
						for($i=0;$i<count($footerBlocks);$i++){
							$footerBlocks[$i] = trim($footerBlocks[$i]); // if user decides they want to include extra spaces...
				?>
							<?php 
								if(file_exists("homepage/blocks/".$footerBlocks[$i]."/".$footerBlocks[$i]."Block.php")){
									// this block is for admin only?
									if(in_array($footerBlocks[$i],$adminBlocks)){
										// admin logged in, show it
										if($_SESSION['user']=="admin"){
							?>
											$('#<?php echo $footerBlocks[$i]?>Block').load("homepage/blocks/<?php echo $footerBlocks[$i]?>/<?php echo $footerBlocks[$i]?>Block.php<?php echo $showErrors?>", function() {
												$('<?php echo str_replace("XXX",$footerBlocks[$i],$blockExtraText)?>').appendTo( "#<?php echo $footerBlocks[$i]?>Block" );
												$(".tooltip").tooltipster();
											});
							<?php
										}
										// admin block, but not logged in as admin, hide it
										else{
							?>
											$('#<?php echo $footerBlocks[$i]?>Block').hide();
							<?php
										}
									}
									// not an admin block, always show
									else{
							?>
										$('#<?php echo $footerBlocks[$i]?>Block').load("homepage/blocks/<?php echo $footerBlocks[$i]?>/<?php echo $footerBlocks[$i]?>Block.php<?php echo $showErrors?>", function() {
											$('<?php echo str_replace("XXX",$footerBlocks[$i],$blockExtraText)?>').appendTo( "#<?php echo $footerBlocks[$i]?>Block" );
											$(".tooltip").tooltipster();
										});
							<?php
									}
								}
							?>
							<?php 
								if(!file_exists("homepage/blocks/".$footerBlocks[$i]."/".$footerBlocks[$i]."Block.php")){
							?>
									$("#<?php echo $footerBlocks[$i]?>Block").html("<br>Block <?php echo $footerBlocks[$i]?> not found. Check that you uploaded the block to the correct folder and also make sure you do not have a typo in the homepage setup.<br><br>");
							<?php 
								}
							?>
				<?php
						}
					}
				?>
				<?php
					// load all blocks
					for($i=0;$i<count($columns);$i++){
						for($a=0;$a<count($columns[$i]);$a++){
							if (strpos($columns[$i][$a], 'multipleBlock') !== false) {
								preg_match("/multipleBlock\((.*?)\)/",$columns[$i][$a],$matches);
								$blocksMultiple = explode(";",$matches[1]);
								foreach($blocksMultiple as $blockMultiple){
									$blockMultiple = explode(",",$blockMultiple);
									if (strpos($blockMultiple[0], 'subBlock') !== false) {
										preg_match("/subBlock\[(.*?)\]/",$blockMultiple[0],$matches);
										$subBlocks = explode("&",$matches[1]);
										foreach($subBlocks as $subBlock){
				?>
											<?php 
												if(file_exists("homepage/blocks/".$subBlock."/".$subBlock."Block.php")){
													// is the subBlock admin block?
													if(in_array($subBlock,$adminBlocks)){
														if($_SESSION['user']=="admin"){
											?>
															$("#<?php echo $subBlock?>Block").load("homepage/blocks/<?php echo $subBlock?>/<?php echo $subBlock?>Block.php<?php echo $showErrors?>", function() {
																$('<?php echo str_replace("XXX",$subBlock,$blockExtraText)?>').appendTo( "#<?php echo $subBlock?>Block" );
																$(".tooltip").tooltipster();
															});
											<?php
														}
														else{
											?>
															$("#<?php echo $subBlock?>Block").hide();
											<?php
														}
													} // end is admin block 
													else{
											?>
														$("#<?php echo $subBlock?>Block").load("homepage/blocks/<?php echo $subBlock?>/<?php echo $subBlock?>Block.php<?php echo $showErrors?>", function() {
															$('<?php echo str_replace("XXX",$subBlock,$blockExtraText)?>').appendTo( "#<?php echo $subBlock?>Block" );
															$(".tooltip").tooltipster();
														});
											<?php
													}	
												}
											?>
											<?php 
												if(!file_exists("homepage/blocks/".$subBlock."/".$subBlock."Block.php")){
											?>
													$("#<?php echo $subBlock?>Block").html("<br>Block <?php echo $subBlock?> not found. Check that you uploaded the block to the correct folder and also make sure you do not have a typo in the homepage setup.<br><br>");
											<?php 
												}
											?>
				<?php
										}
									}
									else{
				?>
										<?php 
											if(file_exists("homepage/blocks/".$blockMultiple[0]."/".$blockMultiple[0]."Block.php")){
												if(in_array($blockMultiple[0],$adminBlocks)){
													if($_SESSION['user']=="admin"){
										?>
														$("#<?php echo $blockMultiple[0]?>Block").load("homepage/blocks/<?php echo $blockMultiple[0]?>/<?php echo $blockMultiple[0]?>Block.php<?php echo $showErrors?>", function() {
															$('<?php echo str_replace("XXX",$blockMultiple[0],$blockExtraText)?>').appendTo( "#<?php echo $blockMultiple[0]?>Block" );
															$(".tooltip").tooltipster();
														});
										<?php
													}
													else{
										?>	
														$("#<?php echo $blockMultiple[0]?>Block").hide();
										<?php
													}
												}
												else{
										?>
														$("#<?php echo $blockMultiple[0]?>Block").load("homepage/blocks/<?php echo $blockMultiple[0]?>/<?php echo $blockMultiple[0]?>Block.php<?php echo $showErrors?>", function() {
															$('<?php echo str_replace("XXX",$blockMultiple[0],$blockExtraText)?>').appendTo( "#<?php echo $blockMultiple[0]?>Block" );
															$(".tooltip").tooltipster();
														});
										<?php
												}
										?>
										<?php 
											}
										?>
										<?php 
											if(!file_exists("homepage/blocks/".$blockMultiple[0]."/".$blockMultiple[0]."Block.php")){
										?>
												$("#<?php echo $blockMultiple[0]?>Block").html("<br>Block <?php echo $blockMultiple[0]?> not found. Check that you uploaded the block to the correct folder and also make sure you do not have a typo in the homepage setup.<br><br>");
										<?php 
											}
										?>
				<?php
									}
								}
							}
							else if (strpos($columns[$i][$a], 'menuBlock') !== false) {
							}
							else{
				?>
								<?php 
									if(file_exists("homepage/blocks/".$columns[$i][$a]."/".$columns[$i][$a]."Block.php")){
										
										if(in_array($columns[$i][$a],$adminBlocks)){
											if($_SESSION['user']=="admin"){
								?>
												$("#<?php echo $columns[$i][$a]?>Block").load("homepage/blocks/<?php echo $columns[$i][$a]?>/<?php echo $columns[$i][$a]?>Block.php<?php echo $showErrors?>", function() {
													$('<?php echo str_replace("XXX",$columns[$i][$a],$blockExtraText)?>').appendTo( "#<?php echo $columns[$i][$a]?>Block" );
													$(".tooltip").tooltipster();
												});
								<?php 
											}
											else{
								?>
												$("#<?php echo $columns[$i][$a]?>Block").hide();
								<?php
											}
										}
										else{
								?>
											$("#<?php echo $columns[$i][$a]?>Block").load("homepage/blocks/<?php echo $columns[$i][$a]?>/<?php echo $columns[$i][$a]?>Block.php<?php echo $showErrors?>", function() {
												$('<?php echo str_replace("XXX",$columns[$i][$a],$blockExtraText)?>').appendTo( "#<?php echo $columns[$i][$a]?>Block" );
												$(".tooltip").tooltipster();
											});
								<?php
										}
									}
								?>
								<?php 
									if(!file_exists("homepage/blocks/".$columns[$i][$a]."/".$columns[$i][$a]."Block.php")){
								?>
										$("#<?php echo $columns[$i][$a]?>Block").html("<br>Block <?php echo $columns[$i][$a]?> not found. Check that you uploaded the block to the correct folder and also make sure you do not have a typo in the homepage setup.<br><br>");
								<?php 
									}
								?>
				<?php
							}
						}
					}
				?>
			});
			<?php
				// settings dialog
				if($_SESSION['user']=="admin"){
			?>
					dialogHeight = screen.height * 0.8;
					dialogWidth = screen.width * 0.6;
					$("#blockSettingsAdmin").dialog({
						modal: true,
						autoOpen: false,
						height: dialogHeight,
						width: dialogWidth
					});
					$("#blockSettingsAdminOpener").click(function(){
						$("#blockSettingsAdmin").dialog('open');
					});
			<?php
				}
			?>
			function openFullBlock(url){
				dialogHeight = screen.height*0.8;
				dialogWidth = $("#main").width()*0.8;
				var $dialog = $('<div style="overflow:hidden"></div>')
					.html('<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>')
					.dialog({
						autoOpen: false,
						modal: true,
						height: dialogHeight,
						width: dialogWidth,
						title: "",
						show: {
							effect: "fade",
							duration: 400
						},
						hide: {
							effect: "fade",
							duration: 800
						}
					});
				$dialog.dialog('open');
			}
			function exportBlockImage(block){
				if(block.includes("#menu")){
					html2canvas($(block), 
					{
						onrendered: function (canvas) {
							var a = document.createElement('a');
							a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
							a.download = block + '.jpg';
							a.click();
						}
					});
				}
				else{
					html2canvas($('#'+block+"Block"), 
					{
						onrendered: function (canvas) {
							var a = document.createElement('a');
							a.href = canvas.toDataURL("image/jpeg").replace("image/jpeg", "image/octet-stream");
							a.download = block + '.jpg';
							a.click();
						}
					});
				}
				
			}
			$("#gotop").scrollToTop();
		</script>
	</body>
</html>
