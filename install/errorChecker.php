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
	#	Database Update
	#
	############################################################################
	
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    $checks = array();

    $correctDiv = "<span class='fa fa-check-circle' style='font-size:2.5em;color:#00bf1c'></span>";
    $incorrectDiv = "<span class='fa fa-times-circle' style='font-size:2.5em;color:#bf0000'></span>";

    $title = "PHP version";
    if(PHP_MAJOR_VERSION<5 || (PHP_MAJOR_VERSION==5 && PHP_MINOR_VERSION<4)){
        $span = $incorrectDiv;
        $text = "You are using PHP version ".PHP_VERSION.". The template requires using version 5.4+ to work correctly.";
        $correct = "To correct this you need to upgrade your server PHP. If you are using external webhosting, see if this is possible to do in your hosting control panel.";
    }
    else{
        $span = $correctDiv;
        $text = "You are using PHP version ".PHP_VERSION.". The template requires using version 5.4+. If possible, always try to use the latest version of PHP available. If you are using external webhosting, see if you can upgrade in your hosting control panel. The latest version of PHP is currently 7.1 and this works perfectly fine with Meteotemplate.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "cURL";
    if(function_exists("curl_init")){
        $span = $correctDiv;
        $text = "cURL is enabled on your server. Good :-)";
        $correct = "";
    }
    else{
        $span = $incorrectDiv;
        $text = "cURL is not enabled on your server. This is likely to cause issues when loading data from external sources, for example forecasts etc.";
        $correct = "You need to enable this on your server. If you are running your own server, change this in the php.ini, if external webhosting, ask your provider about this.";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "allow_url_fopen";
    if( ini_get('allow_url_fopen') ) {
        $span = $correctDiv;
        $text = "allow_url_fopen is enabled on your server. Good :-)";
        $correct = "";
    }
    else{
        $span = $incorrectDiv;
        $text = "allow_url_fopen is not enabled on your server. This is absolutely crucial for the template to work correctly, load settings files etc.";
        $correct = "You need to enable this on your server. If you are running your own server, change this in the php.ini, if external webhosting, ask your provider about this.";
    }
    $checks[] = array($title,$span,$text,$correct);
	
    $title = "Passwords";
    if($adminPassword==$updatePassword){
        $span = $incorrectDiv;
        $text = "You are using the same password for both the updates and for logging in to the control panel. The update password is used by the update script and so cannot be secured as much. You should therefore use different password, the one used for logging in is better secured.";
        $correct = "Go to your main settings and change on of the passwords.";
    }
    else{
        $span = $correctDiv;
        $text = "Admin and update passwords don't match, good :-)";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Admin password strength";
    $notStrong = false;
    if(strlen($adminPassword)<6){
        $notStrong = true;
    }
    if(is_numeric($adminPassword)){
        $notStrong = true;
    }
    if(!$notStrong){
        $span = $correctDiv;
        $text = "Admin password strength seems ok";
        $correct = "";
    }
    else{
        $span = $incorrectDiv;
        $text = "Your admin password seems to be quite week. Make sure you use at least 6 characters, use at least one symbol/letter, not just numbers and that your password is not one of the most common passwords used";
        $correct = "Aim for ideally 8+ characters, one upper and lower case letter and not a common word.";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Update password strength";
    $notStrong = false;
    if(strlen($updatePassword)<6){
        $notStrong = true;
    }
    if(is_numeric($updatePassword)){
        $notStrong = true;
    }
    if(!$notStrong){
        $span = $correctDiv;
        $text = "Update password strength seems ok";
        $correct = "";
    }
    else{
        $span = $incorrectDiv;
        $text = "Your update password seems to be quite week. Make sure you use at least 6 characters, use at least one symbol/letter, not just numbers and that your password is not one of the most common passwords used";
        $correct = "Aim for ideally 8+ characters, one upper and lower case letter and not a common word.";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Location";
    if($stationLat==0 && $stationLon==0){
        $span = $incorrectDiv;
        $text = "You did not specify GPS co-ordinates of your place, this will lead to errors and incorrect calculation of sunrise/set times etc.";
        $correct = "Go to main setup and specify your GPS co-ordinates in the location section.";
    }
    else{
        $span = $correctDiv;
        $text = "GPS specified.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Language";
    if(strlen($lang)!=2){
        $span = $incorrectDiv;
        $text = "Make sure you select default language for the template.";
        $correct = "Go to main setup and in the language section click on the flag of the language you want to use.";
    }
    else{
        $span = $correctDiv;
        $text = "Language specified.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Google Maps Key";
    if(file_exists("googleMapsKey.txt")){
		$googleMapsAPI = trim(file_get_contents("googleMapsKey.txt"));
		if($googleMapsAPI=="XXXXXX" || $googleMapsAPI==""){
            $span = $incorrectDiv;
            $text = "Double-check your Google Maps Key.";
            $correct = "Get your Google Maps API Key (see wiki) and then insert it into install/googleMapsKey.txt file.";
        }
        else{
            $span = $correctDiv;
            $text = "Google Maps Key found.<br>Please note: This script cannot test that the key is valid, it only checks if you provided the key at all. If the referrer is not correctly set, it will not be detected by this script.";
            $correct = "";
        }
    }
    else{
        $span = $incorrectDiv;
        $text = "Double-check your Google Maps Key.";
        $correct = "Get your Google Maps API Key (see wiki) and then insert it into install/googleMapsKey.txt file.";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "MySQL";
    if(!$con){
        $span = $incorrectDiv;
        $text = "There is a problem with connecting to your MySQL database.";
        $correct = "Go to main setup and make sure you correctly specify the host, database name, username and password for MySQL. Then make sure the Test connection button shows success.";
    }
    else{
        $span = $correctDiv;
        $text = "Connection to MySQL successfully established.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "API file";
    if(!file_exists("../meteotemplateLive.txt")){
        $span = $incorrectDiv;
        $text = "API file not found. This means your template is not being updated.";
        $correct = "Double check your template updates.";
    }
    else{
        $span = $correctDiv;
        $text = "Current API file found.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Pressure limits";
    if($dataPressUnits=="inhg" && $limitPressureMin > 40){
        $span = $incorrectDiv;
        $text = "Double check your pressure limits. You set your units to inHg, but your minimum acceptable value based on limits in Main settings is set for hPa.";
        $correct = "Change pressure limits in Main settings.";
    }
    else{
        $span = $correctDiv;
        $text = "Pressure limits seem ok.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "DarkSky API Key";
    if($fIOKey==""){
        $span = $incorrectDiv;
        $text = "No Darksky API key provided, this is essential for forecasts etc.";
        $correct = "Go to main settings and there you will see field for DarkSky API key as well as link to the page where you can get one.";
    }
    else{
        $span = $correctDiv;
        $text = "DarkSky API key filled in.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Update checking";
    if($templateUpdateCheck==0){
        $span = $incorrectDiv;
        $text = "The mechanism to check for updates (blocks, plugins, template) are turned off. This is not really an error, but make sure this is what you wanted.";
        $correct = "If you want to enable the update check, switch the select box in the Main settings to enabled.";
    }
    else{
        $span = $correctDiv;
        $text = "Update checking enabled.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Header left image";
    if($headerLeftImage=="custom" && $customHeaderLeftImg==""){
        $span = $incorrectDiv;
        $text = "You selected you want to use custom image in the header on the left instead of the flag, however you did not provide any URL for this custom image.";
        $correct = "Go to your Main settings and either change this option to Flag or specify URL of the image you want to use.";
    }
    else{
        $span = $correctDiv;
        $text = "OK.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Header text";
    if($headerTitleText == 'Meteotemplate'){
        $span = $incorrectDiv;
        $text = "The main header text is set to the default Meteotemplate.";
        $correct = "Unless you really want your page to be called Meteotemplate, go to your Main settings and in the last section - Customization, specify the text to be used.";
    }
    else{
        $span = $correctDiv;
        $text = "OK.";
        $correct = "";
    }
    $checks[] = array($title,$span,$text,$correct);

    $title = "Menu";
    if(!(file_exists("../admin/menu/menuItems.txt"))){
        $span = $incorrectDiv;
        $text = "Menu not found.";
        $correct = "You need to create the menu. To get you started, go to the Control panel, select menu and click Generate menu. This will create the default menu, then edit it as you want to - see the wiki for instructions.";
    }
    else{
        $span = $correctDiv;
        $text = "Menu created.";
        $correct = "";
    }

    $title = "ZIP support";
    if(extension_loaded("zip")){
        $span = $correctDiv;
        $text = "ZIP support is enabled on your server.";
        $correct = "";
    }
    else{
        $span = $incorrectDiv;
        $text = "ZIP is not enabled on your server. 
        This is likely to cause issues with the <auto update> function.";
        $correct = "You need to enable this on your server.
         If you are running your own server, change this in the php.ini, 
         if external webhosting, ask your provider about this, 
         or enable \'Zip support\' in your Web Control Panel";
    }

    $checks[] = array($title,$span,$text,$correct);

    
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
			<div class="textDiv" style="width:90%;position:relative">
				<h1>Error Checker</h1>
				<p>This script will check various settings of your template and server to make sure everything is correct.</p>
                <table style="width:98%;margin:0 auto">
                    <?php 
                        foreach($checks as $check){
                    ?>
                            <tr>
                                <td style="text-align:left;width:50px">
                                    <?php echo $check[1]?>
                                </td>
                                <td style="text-align:left;;padding-left:10px">
                                    <span style="font-size:1.2em;font-weight:bold;font-variant:bold"><?php echo $check[0]?></span>
                                    <br>
                                    <?php echo $check[2]?><br>
                                </td>
                            </tr>
                    <?php
                        }
                    ?>
                </table>
                <br><br>
			</div>
		</div>
		<?php include($baseURL."footer.php");?>
		<script>
			
		</script>
	</body>
</html>
		