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
	
	if(file_exists("infoPages.txt")){
		$textData = json_decode(file_get_contents("infoPages.txt"),true);
	}
	else{
		$textData['weatherStation'] = "This is where you can describe your weather station and in general your hardware, software etc. You can use standard HTML tags.";
		$textData['webpage'] = "<p>This website uses Meteotemplate, a free customizable website template available at <a href='http://www.meteotemplate.com'>www.meteotemplate.com</a>. The template uses its own MySQL database and therefore is able to calculate any parameter or statistic and also does not rely on any other external services. It was first released in July 2015 and is regularly updated. The template is compatible with various programs such as Weather Display, Cumulus, Weather View or Meteobridge, as well as the Davis WL-IP logger, NetAtmo and Raspberry Pi and data can even be retrieved directly from Weather Underground website.</p><p>In addition to the core template files, which include scripts that calculate various statistics and show many graphs and tables from data from the actual weather station, it is also possible to download and easily install tens of additional plugins (add-ons) that extend the website with many further functionalities.</p><p>Meteotemplate, including all the scripts, plugins, webpage etc., is maintained by Jachym from the Czech Republic.</p><p>You can use the <a href='http://www.meteotemplate.com'>main website</a> to download all the content, <a href='http://www.meteotemplate.com/wiki'>wiki</a> includes instructions how to install or you can subscribe to the <a href='http://www.meteotemplate.com/blog'>blog</a> where regular updates are published and where you can also ask for help and support.</p><br><a href='http://www.meteotemplate.com'>Meteotemplate Homepage - info, download</a><br><a href='http://www.meteotemplate.com/blog'>Meteotemplate Blog - updates, info, RSS, support</a><br><a href='http://www.meteotemplate.com/wiki'>Meteotemplate Wiki - install instructions</a>";
		$textData['location'] = "This is where you can describe your location. You can use standard HTML tags.";
		$textData['help'] = "";
	}

	if(!isset($textData['help'])){ // added in version 14
		$textData['help'] = "";
	}
	if(!isset($textData['links'])){ // added in version 13
		$textData['links'] = "";
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
			<h1>Info Pages - Setup</h1>
			<p>The template has several pages which in general describe your station, location etc. On this page you can specify the text to display on those pages. In the actual text, you can use the standard HTML tags to format text. You can also come back to this site any time in the future and make changes in the text.</p>
			<form action="saveInfoSetup.php" method="POST" target="_blank">
				<h2>Weather Station</h2>
				<br>
				<textarea name="weatherStation" rows="20" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $textData['weatherStation']?></textarea>
				<br>
				<h2>Webpage</h2>
				<p>Here you can describe your webpage, you can include when you first made it, what it shows, why you made it etc etc. The below text is about Meteotemplate. It is up to you if you want to leave it there. I would appreciate it, but if you expliticly do not want to have it there, it is ok to delete it.</p><br>
				<textarea name="webpage" rows="20" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $textData['webpage']?></textarea>
				<h2>Location</h2>
				<br>
				<textarea name="location" rows="20" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $textData['location']?></textarea>
				<br>
				<h2>Links</h2>
				<p>
					This section is where you can specify links shown on the "Links" page. Use the following syntax:<br>
					To create a heading, use same syntax as in the menu - two greater than arrows (<i>>></i>), followed by the heading text.<br>
					Links should be given each on a new line and for each link you give 4 parameters: the text of the link, the URL, description and an icon. For the text you can use whatever text you want to display on the site. For the URL, MAKE SURE you include the http(s):// as well. Otherwise the link will not work. For the icon give the namespace of either the Meteotemplate font or Font Awesome - eg. <i>mticon-weather, fa fa-calendar</i> etc.<br><br> The 4 parameters of each link should be given in the order: text, link, description, icon and delimited by |. If you want no description, simply use || (see example below, Link 2 has no description).<br><br>
					Example:<br><br>
				</p>
				<p style='font-family: "Lucida Console", Monaco, monospace'>
					>> Weather station template<br>
					Meteotemplate|http://www.meteotemplate.com|Free weather website template.|mticon-logo<br>
					>> Heading 2<br>
					Link 1|http://www.example.com|This is link 1 description.|fa fa-desktop<br>
					Link 2|https://www.example.com||mticon-weather
					<br><br>
				</p>
				<textarea name="links" rows="20" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $textData['links']?></textarea>
				<br>
				<h2>Help Dialog</h2>
				<p>This text will appear in the help dialog, triggered when you click the help icon in the header. There are icon descriptions and you can optionally include some text about whatever you think might be useful for your visitors to know with regards to your site as such.</p><br>
				<textarea name="help" rows="20" cols="300" style="text-align:justify;cursor:auto;background:white;color:black;font-size:1em;margin:0 auto;padding:5px;max-width:100%;display:block"><?php echo $textData['help']?></textarea>
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
	