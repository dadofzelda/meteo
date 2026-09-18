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
	#	Administratior login
	#
	#
	############################################################################
	
	session_start();
	// check if we are already logged in
	if($_SESSION['user']=="admin"){
		header("Location: index.php");
	}
	
	include("../config.php");
	include($baseURL."css/design.php");
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
		<style>
			body{
				margin: 0 auto;
				background: #<?php echo $color_schemes[$design2]['700']?>;
			}
			#loginSubmit{
				background:#<?php echo $color_schemes[$design2]['600']?>;
				color:#<?php echo $color_schemes[$design2]['200']?>;
				font-weight:bold;
				border:1px solid #<?php echo $color_schemes[$design2]['200']?>;
				text-align:center;font-size:1.2em;
				padding:15px;
				cursor:pointer;
			}
			#loginSubmit:hover{
				background:#<?php echo $color_schemes[$design2]['200']?>;
				color:#<?php echo $color_schemes[$design2]['900']?>;
			}
		</style>
	</head>
	<body>
		<div style="width:90%;margin:0 auto;margin-top:200px;text-align:center">
			<img src="<?php echo $pageURL.$path?>icons/lock.png" style="width:150px">.
			<br><br>
			<form action="authorize.php" method="post">
				<input type="password" name="password" style="background:#<?php echo $color_schemes[$design2]['600']?>;color:white;font-weight:bold;border:1px solid #<?php echo $color_schemes[$design2]['200']?>;text-align:center;font-size:1.2em;padding:5px;">
				<br><br>
				<input type="checkbox" name="keepLogged"> <span style="color:white;font-weight:bold">keep me logged in (you must be using PHP 5.3.7 or higher for this)</span>
				<br><br>
				<input type="submit" value="OK" id="loginSubmit">
			</form>
		</div>
	</body>
</html>
	