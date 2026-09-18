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
	#	Blank Page
	#
	# 	Template for blank page
	#
	############################################################################
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
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
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main">
			<div class="textDiv">
				<h1>Heading</h1>
				<br><br>
				Text
				<br>
				<br>
				<h3>Heading3</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec quis nulla turpis. Suspendisse ac ipsum magna. Maecenas ut suscipit mauris, dignissim gravida diam. Nunc nec molestie risus, at aliquet lacus. Nam porttitor, augue vitae sollicitudin interdum, velit neque sollicitudin est, eu commodo lorem libero a lectus. Nulla sapien metus, vestibulum nec sem eget, porta dignissim mi. Phasellus quis bibendum libero. Nunc tristique erat quis mi semper porta.</p>
				
				<p>Vestibulum iaculis laoreet elit ac auctor. Donec mattis diam sed cursus bibendum. Duis in sagittis lorem, a eleifend libero. Nulla molestie libero ut dui lacinia pharetra. Suspendisse pulvinar justo non dictum dictum. Nam cursus sapien nibh, et viverra lectus commodo sit amet. Pellentesque arcu nibh, rutrum eu tristique non, ultrices ac sem.</p>
			</div>
		</div>
		<?php include($baseURL."footer.php")?>
	</body>
</html>
	