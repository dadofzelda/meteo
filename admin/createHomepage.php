<?php
	
	############################################################################
	# 	Meteotemplate
	# 	http://www.meteotemplate.com
	# 	Free website template for weather enthusiasts
	# 	Author: Jachym
	#           Brno, Czech Republic
	# 	First release: 2015
	#
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
		$columns = $homepageData['desktop']['columns'];
		$theme = $homepageData['desktop']['theme'];
		$highlightedBlocks = $homepageData['desktop']['highlightedBlocks'];
		$headerBlock = $homepageData['desktop']['headerBlock'];
		$footerBlock = $homepageData['desktop']['footerBlock'];
		$adminBlock = $homepageData['desktop']['adminBlock'];
	}
	else if($type=="mobile" && file_exists("homepageLayoutMobile.txt")){
		$homepageData = json_decode(file_get_contents("homepageLayoutMobile.txt"),true);
		$columnWidths = $homepageData['mobile']['columnWidths'];
		$columns = $homepageData['mobile']['columns'];
		$theme = $homepageData['mobile']['theme'];
		$highlightedBlocks = $homepageData['mobile']['highlightedBlocks'];
		$headerBlock = $homepageData['mobile']['headerBlock'];
		$footerBlock = $homepageData['mobile']['footerBlock'];
		$adminBlock = $homepageData['mobile']['adminBlock'];
	}
	else{
		$columnWidths = explode(",",trim($_POST['columnWidths']));
		for($i=0;$i<count($columnWidths);$i++){
			$columns [] = array();
		}
		$theme = "dark";
		$highlightedBlocks = "";
	}
	
	// get all installed blocks
	$dirs = array_filter(glob('../homepage/blocks/*'), 'is_dir');
	$installedBlocks = array();
	foreach($dirs as $dir){
		$blockNamespace = str_replace("../homepage/blocks/","",$dir);
		$installedBlocks[] = $blockNamespace;
	}
		
	// make sure layouts directory exists
	if(!is_dir("layouts")){
		mkdir("layouts");
	}
	if(!is_dir("layouts/desktop")){
		mkdir("layouts/desktop");
	}
	if(!is_dir("layouts/mobile")){
		mkdir("layouts/mobile");
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			.destination { 
				width: 200px; 
				height: 500px;
			}
			.dragDiv{
				width: 150px;
				margin: 10px;
				padding: 10px;
				background: red;
				cursor: pointer;
			}
		</style>
		<script src="<?php echo $pageURL.$path?>scripts/autoSuggest.js"></script>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1>Homepage Layout</h1>
				<h2><?php echo ucwords($type)?></h2>
				<p>
					Now you need to specify which blocks you want to include on your homepage, in which columns and their order. In addition you should also specify if you want to use the light or dark theme for your homepage.
				</p>
				<p>
					Below you see a table that shows the number of columns and widths you specified in the previous step. You can also see the list of "installed blocks". These are blocks you have downloaded from meteotemplate.com and uploaded to the homepage/blocks/ directory. To include a block you simply type it in the corresponding column. You can also use the Add multiple block and Add menu block buttons that will automatically insert a template string, where you change the block names. <strong>In the wiki you can find more information about this. Below is just a brief summary (click Brief Instructions).</strong>
				</p>
				<p>
					In addition, it is possible to save the layout. At the bottom you can save the layout you created and later reload it. In this way you can save several layouts and then easily change from one to the other. If you have already saved some layouts and want to load them, click the Manage layouts button.
				</p>
				<br>
				<div style="width:100%;text-align:right">
					<input type="button" class="button2" value="Change column widths" id="changeWidths">
					<input type="button" class="button2" value="Manage Layouts" id="manageLayouts">
				</div>
				<h3><a href="#" onclick="$('#briefInstructions').slideToggle()">Brief Instructions >>></a></h3>
				<div id="briefInstructions" style="width:98%;margin:0 auto;display:none">
					<ul>
						<li>
							<strong>each block should be on a separate line</strong> - also make sure that you do not include any extra new lines. Each line must correspond to one block (or one menu/multiple block)
							<br><br>
						</li>
						<li>
							each block can only be included once on the homepage
							<br><br>
						</li>
						<li>
							nesting multiple block inside menu block or vice versa is not possible
							<br><br>
						</li>
						<li>
							multiple block syntax:
							<br>
							<strong>multipleBlock(block1,block1 width;block2,block2 width)</strong>
							<br>
							Example: multipleBlock(current,60;stationData,40)
							<br><br>
						</li>
						<li>
							menu block syntax (the "name" must be specified as the first parameter and delimited by a comma, it is optional, if you do not include any name, there simply will be no heading in the menu block, the template will try to translate the name if available in language strings)
							<br>
							<strong>menuBlock(name,block1;block2;block3;block4)</strong>
							<br>
							Example with name: menuBlock(radars,radarEU;radarUS;radarAU;radarCA)<br>
							Example with no name: menuBlock(radarEU;radarUS;radarAU;radarCA)
							<br><br>
						</li>
						<li>
							sub-blocks - sub-blocks are used to stack more blocks in one multiblock slot vertically. The syntax uses <strong>subBlock[block1&block2]</strong>
						</li>
					</ul>
					<p>
						A block can also be temporarily disabled, if you want to temporarily disable a block just put "//" in front of the block name. This can also be used to disable multiple or multi blocks. Just make sure that not all blocks in one particular column are disabled.
					</p>
				</div>
				<br><br>
				<form action='createHomepageWrite.php?type=<?php echo $type?>' method="post" target="_blank" onsubmit="return validateForm()">
					<input type="hidden" name="columnCount" value="<?php echo count($columnWidths)?>">
					<input type="hidden" name="columnWidths" value="<?php echo implode(",",$columnWidths)?>">
					<div style="width:100%;margin:0 auto">
						<span style="font-size:1.3em;color:#<?php echo $color_schemes[$design2]['300']?>">
							Theme: 
						</span>
						<select name="theme" class="button2" style="margin-left:10px">
							<option value="dark" <?php if($theme=="dark"){echo"selected";}?>>Dark</option>
							<option value="light" <?php if($theme=="light"){echo"selected";}?>>Light</option>
							<option value="sun" <?php if($theme=="sun"){echo"selected";}?>>Day (light), Night (Dark)</option>
						</select>
						<br><br>
						It is possible to highlight certain blocks - they will have a different background color based selected color theme. If you want to highlight certain blocks, then simply specify their names in the field below, <strong>separated with a comma (,)</strong>, eg: current,gauges,indoor etc.<br><br>
						<span style="font-size:1.3em;color:#<?php echo $color_schemes[$design2]['300']?>">
							Highlighted blocks:
						</span>
						<br><br>
						<textarea style="display:block;width:100%;margin:0 auto;text-align:left;background:#<?php echo $color_schemes[$design2]['700']?>;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>" name="highlightedBlocks" id="highlightedBlocks" rows="2"><?php echo $highlightedBlocks?></textarea>
						<br><br>
						<span style="font-size:1.3em;color:#<?php echo $color_schemes[$design2]['300']?>">
							Admin blocks:
						</span>
						<br><br>
						It is possible to hide blocks and only show them to you when you are logged in as admin. If you want to do this then list the blocks you want to hide to regular visitor here. Give the namespaces of the blocks which you have specified below on the homepage, but only want to show to yourself. Please keep in mind that this will not look good if you use it for a multipleBlock/subBlock, because it will create an empty space. Only use this for a normal block. Delimit them with a semi-colon.
						<textarea style="display:block;width:100%;margin:0 auto;text-align:left;background:#<?php echo $color_schemes[$design2]['700']?>;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>" name="adminBlock" id="adminBlock" rows="2"><?php echo $adminBlock?></textarea>
						<br><br>
						It is possible to include a so-called "header block" or a "footer block". A header block is simply a block that is inserted right at the top of the homepage and spans the full width of the page, regardless of the columns below. If you do not want to use a header block, simply leave this field blank. Similarly, a footer block is the exact same thing - a block spanning the full page width - but instead of at the top, it would be inserted right at the bottom above the page footer. If you want to include more than one header/footer block, delimit them with a semi-colon (;) and make sure you have no extra spaces, eg: block1;block2 etc. Provide the namespaces of these blocks only here, make sure you don't have them again in the section below for the rest of the page.<br><br>
						<table style="width:98%;margin:0 auto">
							<tr>
								<td style="width:50%;text-align:left">
									<span style="font-size:1.3em;color:#<?php echo $color_schemes[$design2]['300']?>">
										Header block:
									</span>
									<br><br>
									<input name="headerBlock" class="button2" style="text-align:left" size="60" value="<?php echo $headerBlock?>">
									<br><br>
								</td>
								<td style="width:50%;text-align:left">
									<span style="font-size:1.3em;color:#<?php echo $color_schemes[$design2]['300']?>">
										Footer block:
									</span>
									<br><br>
									<input name="footerBlock" class="button2" style="text-align:left" size="60" value="<?php echo $footerBlock?>">
									<br><br>
								</td>
							</tr>
						</table>
						<br><br>
					</div>
					<br>
					<h3>Installed blocks</h3>
					<?php 
						echo implode(", ",$installedBlocks);
					?>
					<br><br>
					<table style="width:98%;margin:0 auto" cellspacing="2" cellpadding="4">
						<tr>
							<?php
								for($i=1;$i<=count($columnWidths);$i++){
							?>
									<td	style="width:<?php echo $columnWidths[($i-1)]?>%;text-align:center;font-variant:small-caps">
										<strong>Column <?php echo ($i)?></strong><br><span style="font-size:0.8em"><?php echo $columnWidths[($i-1)]?>%</span><br><br><input type="button" class="button2" style="font-variant:small-caps" value="Add new multiple block" onclick="addMultiple(<?php echo ($i)?>)"><input type="button" class="button2" style="font-variant:small-caps" value="Add new menu block" onclick="addMenu(<?php echo ($i)?>)">
									</td>
							<?php
								}
							?>
						</tr>
						<tr>
							<?php
								for($i=1;$i<=count($columnWidths);$i++){
							?>
									<td	style="width:<?php echo $columnWidths[($i-1)]?>%;text-align:center">
										<textarea style="display:block;width:90%;margin:0 auto;text-align:center;background:#<?php echo $color_schemes[$design2]['700']?>;font-weight:bold;color:#<?php echo $color_schemes[$design2]['100']?>" name="column<?php echo ($i)?>" id="column<?php echo ($i)?>" rows="40"><?php echo implode("\n",$columns[($i-1)])?></textarea>
									</td>
							<?php
								}
							?>
						</tr>
					</table>
					<br><br>
					<div style="width:90%;margin:0 auto">
						<p>You can also select to save this layout and later reload it. In this way you can have several layouts. If you want to save this layout (number of columns, blocks and all settings for highlighted blocks, header blocks etc.), just check the checkbox below and provide a name for this layout. Please note! For the name use alphanumeric symbols and no spaces or special characters. Examples: winter, myHomepage, my_homepage, layout1, winterMobile, desktop3Columns etc.<br><br>
						<input type="checkbox" name="saveLayout" value="save"> Save layout as:&nbsp;<input class="button2" value="" size="20" name="layoutName"><br><br>
					</div>
					<div style="width:98%;margin:0 auto;text-align:center">
						<input type="submit" class="button2" value="Save">
					</div>
					<br><br>
				</form>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>	
		<script type="text/javascript">
			function validateForm(){
				<?php 
					foreach($installedBlocks as $installedBlock){
						$checkBlocks[] = "'".$installedBlock."'";
					}
				?>
				availableBlocks = [<?php echo implode(",",$checkBlocks)?>];
				<?php 
					for($i=1;$i<=count($columnWidths);$i++){
				?>
						var blocks<?php echo ($i)?> = $('textarea#column<?php echo ($i)?>').val().split("\n");
						for(var i = 0; i < blocks<?php echo ($i)?>.length; i++){	
						}
				<?php 
					}
				?>
			}
			function addMultiple(number){
				var currentContent = $('textarea#column'+number).val();
				newMultiple = "multipleBlock(block1,width1;block2,width2)";
				if(currentContent!=""){
					newContent = currentContent + "\n" + newMultiple;
				}
				else{
					newContent = newMultiple;
				}
				$('textarea#column'+number).val(newContent);
			}
			function addMenu(number){
				var currentContent = $('textarea#column'+number).val();
				newMultiple = "menuBlock(name,block1;block2;block3)";
				if(currentContent!=""){
					newContent = currentContent + "\n" + newMultiple;
				}
				else{
					newContent = newMultiple;
				}
				$('textarea#column'+number).val(newContent);
			}

			availableBlocks = [<?php echo implode(",",$checkBlocks)?>];
			<?php
				for($i=1;$i<=count($columnWidths);$i++){
			?>
					$("#column<?php echo $i?>").asuggest(availableBlocks, {
						'endingSymbols': '',
						'stopSuggestionKeys': [$.asuggestKeys.RETURN],
						'minChunkSize': 1,
						'delimiters': ', \n'
					});
			<?php 
				}
			?>

			$("#changeWidths").click(function(){
				type = "<?php echo $type?>";
				location = "homepageChangeWidths.php?type="+type;
			})
			$("#manageLayouts").click(function(){
				location = "manageLayouts.php";
			})
		</script>
			
	</body>
</html>
	