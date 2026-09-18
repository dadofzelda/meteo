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
	#	Sticker
	#
	# 	A plugin which generates image stickers that can be used on any page,
	#	showing current conditions.
	#
	############################################################################
	#	Version and change log
	#
	# 	v1.0 - 2015-07-23	
	# 		- initial release
	# 	v2.0 - 2016-03-06
	# 		- units clean up
	#  		- rounding
	#  		- addition of date/time
	# 		- new sticker 800x170 with detailed info
	#	v3.0 - 2016-08-02	
	# 		- new interactive sticker
	#	v4.0 - 2016-09-29
	#  		- date/time formatted based on template settings
	#	v5.0 - 2016-12-01	
	# 		- added Christmas theme
	# 	v6.0 - 2017-01-20
	# 		- added possibility to show both metric and imperial units at the same time
	# 	v7.0 - 2017-04-02
	# 		- added support for API
	# 		- minor bug fixes
	# 		- optimization
	# 	v7.1 - 2017-10-10
	# 		- dual units bug fix
	# 	v7.2 - 2017-10-11
	# 		- dual units bug fix
	#
	############################################################################
	
	include("../../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	/*
	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	*/
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Stickers</title>
	<?php metaHeader()?>
	<style>
		hr{
			width:80%;
			margin-right:auto;
			margin-left:auto;
			border:1px solid #<?php echo $color_schemes[$design2]['600']?>;
		}
		img{
			padding-left: 10px;
			padding-right: 10px;
		}
	</style>
</head>
<body>
<div id="main_top">
	<?php bodyHeader()?>
	<?php include($baseURL."menu.php");?>
</div>
<div id="main" style="text-align:center">
	<h1>Stickers</h1>
	<br><hr>
	<div id='text' style="width:70%;text-align:left;margin-left:auto;margin-right:auto">
		You can generate desired sticker by specifying some parameters in the URL:
		<ul>
			<li>
				text - stickers containing text
			</li>
			<li>
				color1 - first color of the two-color combination, corresponds to the available <a href="http://www.meteotemplate.com/template/css/settingExamples.php" target="_BLANK">color themes</a> for the template
			</li>
			<li>
				color2 - first color of the two-color combination, corresponds to the available <a href="http://www.meteotemplate.com/template/css/settingExamples.php" target="_BLANK">color themes</a> for the template
			</li>
			<li>
				parameter - those stickers that only show one parameter, options:
					<ul>
						<li>T - temperature</li>
						<li>H - humidity</li>
						<li>P - pressure</li>
						<li>W - wind speed</li>
						<li>G - wind gust</li>
						<li>A - apparent temperature</li>
						<li>D - dewpoint</li>
						<li>S - solar radiation</li>
					</ul>
			</li>
			<li>
				sticker types available:
					<ul>
						<li>
							sticker800_170 - sticker 800 x 170 px
						</li>
						<li>
							sticker800_150 - sticker 800 x 150 px
						</li>
						<li>
							sticker200_250 - sticker 200 x 250 px
						</li>
						<li>
							sticker200_120 - sticker 200 x 120 px with text
						</li>
						<li>
							sticker200_120icon - sticker 200 x 120 px with icon
						</li>
						<li>
							sticker150_30 - sticker 150 x 30 px
						</li>
						<li>
							interactive sticker - scroll to the bottom for more info
						</li>
					</ul>
			</li>
		</ul>
		<br>
		The URL of the image will therefore look like this:
		<br><br>
		<span style="font-size:1.2em;font-weight:bold">
			....YOUR PAGE URL ... /plugins/stickers/sticker200_120.php?parameter=T&text=Hello&color1=grey&color2=dark_red
		</span>
		<br>
		<br>
		The 800x170px sticker also shows image of your station. What you need to do to show the correct image is very simple. In the plugin folder there is another directory called "stations". Inside this directory you will find images for various station models. Choose the one you want. Take this file and paste it directly in the sticker plugin folder. You will see there is already a file station.png, this is the default. What you need to do is simply replace this file with the one you choose - i.e. delete the original station.png, paste one from the stations folder and rename it again to station.png.
		<br><br>
		Below are some examples, you can view the image URL to see the parameters used for that particular sticker.
	</div>
	<h1>Examples</h1>
	<br>
	<h2>Sticker 800 x 170</h2>
	<img src="sticker800_170.php?text=Meteotemplate&color1=grey&color2=dark_red">
	<br><hr>
	<h2>Sticker 800 x 150</h2>
	<img src="sticker800_150.php?text=Meteotemplate&color1=grey&color2=brown">
	<br><hr>
	<h2>Sticker 200 x 250</h2>
	<img src="sticker200_250.php?text=Meteotemplate&color1=indigo&color2=blue">
	<br><hr>
	<h2>Stickers 200 x 120</h2>
	<img src="sticker200_120.php?parameter=T&text=Meteotemplate&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=H&text=Weather&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=P&text=Current&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=W&text=Station&color1=grey&color2=brown">
	<br>
	<img src="sticker200_120.php?parameter=A&text=Apparent&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=D&text=Dewpoint&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=B&text=Text&color1=grey&color2=brown">
	<img src="sticker200_120.php?parameter=R&text=&color1=grey&color2=brown">
	<br><hr>
	<h2>Stickers 200 x 120</h2>
	<img src="sticker200_120icon.php?parameter=T&color1=blue&color2=green">
	<img src="sticker200_120icon.php?parameter=H&color1=grey&color2=dark_red">
	<img src="sticker200_120icon.php?parameter=P&color1=beige&color2=brown">
	<img src="sticker200_120icon.php?parameter=W&color1=magenta&color2=pink">
	<br>
	<img src="sticker200_120icon.php?parameter=A&color1=purple&color2=deep_purple">
	<img src="sticker200_120icon.php?parameter=D&color1=amber&color2=orange">
	<img src="sticker200_120icon.php?parameter=B&color1=blue_grey&color2=brown">
	<img src="sticker200_120icon.php?parameter=R&color1=lime&color2=green">
	<br><hr>
	<h2>Stickers 150 x 30</h2>
	<br>
	<img src="sticker150_30.php?parameter=T&color1=red&color2=yellow">
	<img src="sticker150_30.php?parameter=H&color1=amber&color2=grey">
	<img src="sticker150_30.php?parameter=P&color1=blue&color2=red">
	<img src="sticker150_30.php?parameter=W&color1=dark_red&color2=magenta">
	<br><br>
	<img src="sticker150_30.php?parameter=A&color1=grey&color2=brown">
	<img src="sticker150_30.php?parameter=D&color1=grey&color2=brown">
	<img src="sticker150_30.php?parameter=B&color1=grey&color2=brown">
	<img src="sticker150_30.php?parameter=R&color1=grey&color2=brown">
	<br><hr>
	<br><br>
	<h2>Interactive Sticker</h2>
	<div id='text' style="width:70%;text-align:left;margin-left:auto;margin-right:auto">
		The interactive sticker offer much more possibilities for setting it up. <br><br>
		The only required parameter is "text" - this is what will show up in the sticker heading.
		<br>
		<br>
		Now let's look at the optional parameters for the default version of the sticker.<br>
		In this plugin directory is a directory 'bgs'. Inside it are backgrounds numbered 1,2,3,.... which you can choose from. Likewise, there is a folder 'fonts', where you can specify the font type. If you only pass the parameter text, a default background and font will be used. However, you can customize the look and feel of the sticker much more by specifying additional paramters, let's have a look at them:
		<ul>
			<li>
				<strong>text</strong> - sticker heading
			</li>
			<li>
				<strong>image</strong> - it is possible to include an image in the sticker. It will be on the left hand side. Your image must be in the 'imgs' directory of this plugin and must have a width and height of 170px. Also, the image must be in the PNG format, using extension '.png'. Then simply set the "image" parameter in the URL to the name of the image, avoid using spaces and unusual symbols in the image name. Ideally use just numbers - eg. 'img01.png' would look like image=img01.
				<br>
				In addition, it is possible to use a random image. In such case, set image to random:<br>
				image=random<br>
				In this case, the plugin will randomly choose one image from the imgs directory.
			</li>
			<li>
				<strong>bg</strong> - the number of the background to be used
			</li>
			<li>
				<strong>dualUnits</strong> - if you include "dualUnits=1", the interactive sticker will show both your chosen template units and in smaller font also the alternative unit (eg. C/F, hPa/inHg, km/h/mph etc.)
			</li>
			<li>
				<strong>color</strong> - this is the color of the text and icons on the background. Obviously this will depend on which background image you use. For light backgrounds make sure to use black, for dark backgrounds, use white.<br>
				Options:<br>
				- black<br>
				- white
			</li>
			<li>
				<strong>shadow</strong> - sometimes it is difficult to choose one particular font color, because one part of the background is light and the other dark. In such case you can use the shadow parameter to insert a shadow to the text, which has the opposite color - in other words, a white text will have a black shadow and vice versa. This way you can make your text visible even on backgrounds with light and dark parts. 
				<br>
				Options:
				<br>
				0 - no shadow
				<br>
				1 - shadow
			</li>
			<li>
				<strong>font</strong> - look inside the 'fonts' directory of this plugin and choose the one you want
			</li>
			<li>
				<strong>realpath</strong> - if you don't visualize any text you may try adding this option
			</li>
			<li>
				<strong>bgColor</strong> - color of the background, default is black. If you set a certain border, then this is the color of the border since it is behind the rest. It must be specified in the HEX format, without the "#" symbol. So for example white would be "ffffff" etc.
			</li>
			<li>
				<strong>border</strong> - this specifies the width of the border. Setting this to 0 would result in no border at all. Use a number between 0 and 10.
			</li>
		</ul>
		<h3>Variable</h3>
		It is also possible to set a variable sticker. One option is use simply a random background, in such case, you simply specify "type=random". You do not give a background number and color. However, in such case a shadow will be used because since the background is random, it can be both dark and light, so we need to make sure the text is visible. In addition to "text" you can specify the "border" and "font".
		<br>
		Probably a more interesting option however, is the option "type=interactive". This is how this option works:
		<br>
		<ol>
			<li>Sun rise and sun set is calcualted for your location</li>
			<li>The script determines what time of the day it is. There are several options - day, night, sun rise and sun set. Sun rise is a one-hour interval in the middle of which is the sun rise. In other words 30 minutes before and 30 minutes after sun rise. Likewise, sun set is 30 minutes before and 30 minutes after sun set. Everything that is between those two intervals is "day" and everything before sun rise or after sun set interval, is night.</li>
			<li>Based on the time of the day, the script chooses a random background corresponding to that time period</li>
			<li>Then, it looks at current rain rate and if it determines that it is more than 0, i.e. it is just raining, it will choose a background with rain drops.</li>
			<li>Finally, if you have a solar sensor and if the solar radiation is currently above 800 W/m2, an image with a sun and sunny weather is chosen as a background.</li>
		</ol>
		In the interactive option, you can still specify the border and the font type, font color is chosen automatically based on background type. And of course you choose the text, which is always required.
		<br><br>
		Best thing to do is just play around with it. But just to give you some examples:
		<br>
		the beginning for the interactive sticker is always like this:<br>
		<strong>mysite.com/plugins/stickers/stickerInteractive.php?....</strong>
		<br><br>
		<ul>
			<li>
				?text=Meteotemplate&bg=1
				<br>
				This would create a sticker with the default font (Ubuntu), background image 1, text Meteotemplate, with no image and default border width (5) and default font color white and default shadow setting (enabled)
				<br><br>
			</li>
			<li>
				?text=Hello%20World&bg=80&border=2&bgColor=ffffff
				<br>
				This would create a sticker with the default font (Ubuntu), background image 80, text Hello World (note, the '%20' is code for space in URLs), with no image, border width 2 and the border and the background would have a white color (ffffff) and default font color white and default shadow setting (enabled)
				<br><br>
			</li>
			<li>
				?text=Meteotemplate&bg=150&shadow=0&image=city&font=Atma-Bold&color=black
				<br>
				This would create a sticker with the text 'Meteotemplate', default border width (5), using font Atma-Bold, with no shadow, font color black (see parameter color, remember you can use either black or white) and on the left would be the image "city.png" that must therefore exist in the imgs directory.
				<br><br>
			</li>
			<li>
				?text=Hello%20World&type=random
				<br>
				This would create a sticker with all the default parameters and a random background image. 
				<br><br>
			</li>
			<li>
				?text=Hello%20World&type=christmas
				<br>
				This will use various Christmas-theme backgrounds.
				<br><br>
			</li>
			<li>
				?text=Hello%20World&type=random&border=0
				<br>
				This would create a sticker with all the default parameters and a random background image with no borders.
				<br><br>
			</li>
			<li>
				?text=Hello%20World&type=random&image=random
				<br>
				This would create a sticker with all the default parameters and a random background image and random image. 
				<br><br>
			</li>
			<li>
				?text=MyText&type=interactive
				<br>
				This would create a sticker with all the default parameters and interactive background (based on time of the day and conditions)
				<br><br>
			</li>
		</ul>
		<br>
		There are endless examples I could use, just play around trying different settings to find the one that suits you best. You can upload as many images as you want in the 'imgs' directory.
	</div>
<?php include($baseURL."footer.php");?>

</body>
</html>
	