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
	#	Menu setup
	#
	############################################################################

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");


	$availableTabs = array();
	$tabOrder = array();

	if(file_exists("menuItems.txt")){
		$menuItems = json_decode(file_get_contents("menuItems.txt"),true);
		if(count($menuItems)>0){
			foreach($menuItems as $key=>$value){
				$tabName = trim($value['name']);
				$tabNamespace = trim($key);
				$availableTabs[] = array($tabName,$tabNamespace);
			}
		}
	}
	if(file_exists("menuOrder.txt")){
		$menuOrder = json_decode(file_get_contents("menuOrder.txt"),true);
	}

	if(file_exists("adminPlugins.txt")){
		$adminPlugins = urldecode(file_get_contents("adminPlugins.txt"));
	}
	else{
		$adminPlugins = "";
	}

	if(file_exists("adminLinks.txt")){
		$adminLinks = file_get_contents("adminLinks.txt");
	}
	else{
		$adminLinks = "";
	}

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
			#sortableList {
				list-style-type: none;
				margin: 0;
				padding: 0;
				cursor: move;
			}
			#sortableList li {
				margin: 0 3px 3px 3px;
				padding: 0.4em;
				padding-left:10px;
				font-weight: bold;
				float:left;
				background: #<?php echo $color_schemes[$design2]['300']?>;
			}
			.menuTabControl{
				opacity: 0.8;
				font-size: 2.0em;
				cursor: pointer;
				padding: 2px;
			}
			.menuTabControl:hover{
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
			<div class="textDiv" style="width:90%">
			<h1>Menu - Main tabs</h1>
			<br />
			<p>
				Before you do anything, make absolutely sure you read the wiki section about the menu. It is necessary so that you know how to build the menu and how to use it to its full potential.
			</p>
			<input type="button" class="button2" value="Generate Default Menu" id="generateDefault">
			<input type="button" class="button2" value="Check My Menu" id="checkMenu">
			<input type="button" class="button2" value="Update Menu" id="updateMenu">
			<br /><br />
			<?php
				if(count($availableTabs)==0){
					echo "No tabs in the menu.";
				}
				else{
			?>
					<table style="width:100%;margin:0 auto" class="table">
						<?php
							for($i=0;$i<count($availableTabs);$i++){
						?>
								<tr>
									<td style="text-align:left;font-weight:bold;font-variant:small-caps;font-size:1.2em">
										<?php echo $availableTabs[$i][0]?>
									</td>
									<td style="text-align:right">
										<span class="fa fa-gear menuTabControl tooltip" onclick="editTab('<?php echo $availableTabs[$i][1]?>')" title="<?php echo lang('edit','c')?>"></span>
										<span class="fa fa-pencil menuTabControl tooltip" onclick="renameTab('<?php echo $availableTabs[$i][1]?>')" title="<?php echo lang('rename','c')?>"></span>
										<span class="fa fa-trash menuTabControl tooltip" onclick="deleteTab('<?php echo $availableTabs[$i][1]?>')" title="<?php echo lang('delete','c')?>"></span>
									</td>
								</tr>
						<?php
							}
						?>
					</table>
				<?php
					}
				?>
				<br><br>
				<h3>Add new tab</h3>
				<table>
					<tr>
						<td>
							<input id="tabName" class="button2">
						</td>
						<td style="text-align:left">
							the tab text - this is the text displayed in the menu when closed. You can also use an icon (this is not the large icon you see when you open the tab, this is if you want to have an icon in the actual menu instead of the text, which when clicked opens the corresponding tab). In such case provide the syntax of the icon + name of the tab that is displayed when opened. If you want to use an icon instead, provide the namespace of of (mticon-... or fa fa-..., available icons are <a href="menuIcons.php" target="_blank"> here </a>) and then a semi-colon followed by the name of this tab. Example: "mticon-weather;weather" will create a new tab in the menu. You will see the "mticon-weather" icon in the menu and when clicked, the tab will open, showing you the name - in this case "weather" and the large icon (specified as the third parameter below). If you want to use just normal text, simply enter it like eg "weather", "climate" etc. 
						</td>
					</tr>
					<tr>
						<td style="text-align:left">
							<input id="tabNamespace" class="button2">
						</td>
						<td style="text-align:left">
							the tab name space (this is the tab ID, it should only contain English alphabet characters and no spaces, for example for weather station, just put weatherStation or weather_station etc.). This will not be shown anywhere on the page, it is only to identify the tab.
						</td>
					</tr>
					<tr>
						<td>
							<input id="tabIcon" class="button2">
						</td>
						<td style="text-align:left">
							specify the namespace of the large icon you want to use (the one you see when the tab is opened, if you want to use an icon in the actual menu as well, see the first parameter "tab text" above), available icons are <a href="menuIcons.php" target="_blank"> here </a>.
						</td>
					</tr>
				</table>
				<br />
				<input type="button" class="button2" value="Add" id="addTab">
				<br><br>
				<h3>Tab order</h3>
				<ul id="sortableList">
					<?php
						if(count($menuOrder)>0){
							foreach($menuOrder as $tab){
					?>
								<li class="ui-state-default" id="<?php echo $menuItems[$tab]['namespace']?>"><?php echo $menuItems[$tab]['name']?></li>
					<?php
							}
						}
					?>
				</ul>
				<br><br>
				<input type="button" class="button2" value="Save new order" onclick="saveOrder()">
				<br /><br />
				<h3>Admin tab plugins</h3>
				<p>If you want to place some plugins only to the "admin" tab of the menu (will only be visible to you), then insert their <b>namespaces</b> below, separated by a semi-colon (;). NOTE: This will not make the plugin completely unaccessible for public viewing. It will still be visible if the user enters the direct URL in the browser address bar. There will, however, be no link to it so it is unlikely they will know about it.</p>
				<br>
				Admin menu plugins: <input id="adminPlugins" class="button2" value="<?php echo $adminPlugins?>"><br><br>
				<input type="button" class="button2" value="Save admin plugins settings" onclick="saveAdminPlugins()">
				<br><br>
				<h3>Admin tab extra links</h3>
				<p>If you want to add custom links to the menu tab, specify them below. Delimit link name from the actual URL by a comma, the links by a semi-colon.<br> 
				Example:<br>
				<i>Link1 name,http://www.mylink.com;Link2 name,http://www.mylink.com</i>
				<br> 
				<input id="adminLinks" class="button2" value="<?php echo $adminLinks?>" size="100"><br>
				<input type="button" class="button2" value="Save custom links" onclick="saveAdminCustomLinks()">
				<br><br>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			$( "#sortableList" ).sortable();
			$( "#sortableList" ).disableSelection();
			$("#addTab").click(function(){
				tabName = encodeURI($("#tabName").val());
				tabNamespace = encodeURI($("#tabNamespace").val());
				tabIcon = encodeURI($("#tabIcon").val());
				location = "menuAddTab.php?name="+tabName+"&namespace="+tabNamespace+"&tabIcon="+tabIcon;
			});
			$("#generateDefault").click(function(){
				confirmThis = confirm("Do you really want to reset your current menu and generate default one?");
				if(confirmThis){
					location = "menuGenerateDefault.php";
				}
			});
			$("#checkMenu").click(function(){
				window.open("menuCheck.php");
			});
			$("#updateMenu").click(function(){
				location = "updateMenu.php";
			})
			function deleteTab(tab){
				confirmIt = confirm("Really delete this tab and all its content?");
				if(confirmIt){
					location="menuDeleteTab.php?tab="+tab;
				}
			}
			function renameTab(tab){
				name = encodeURI(prompt("New name:"));
				namespace = encodeURI(prompt("New namespace"));
				tabIcon = encodeURI(prompt("Tab icon"));
				location = "menuRenameTab.php?name="+name+"&namespace="+namespace+"&tabIcon="+tabIcon+"&tab="+tab;
			}
			function editTab(tab){
				location = "menuEditTab.php?tab="+tab;
			}
			function saveOrder(){
				newOrder = ( $( "#sortableList" ).sortable( "toArray" ));
				newOrderFinal = encodeURI(newOrder.join(";"));
				location = "menuSaveOrder.php?order="+newOrderFinal;
			}
			function saveAdminPlugins(){
				adminPlugins = $("#adminPlugins").val();
				adminPlugins = encodeURI(adminPlugins);
				location = "menuSaveAdminPlugins.php?plugins=" + adminPlugins;
			}
			function saveAdminCustomLinks(){
				adminLinks = $("#adminLinks").val();
				adminLinks = encodeURI(adminLinks);
				location = "menuSaveAdminLinks.php?links=" + adminLinks;
			}
		</script>
	</body>
</html>
