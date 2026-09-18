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
	#	Offline notifications
	#
	############################################################################
	
	session_start();
	
	
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");

    // setup page or CRON?
    if(isset($_GET['password'])){
        if($_GET['password'] != $updatePassword){
            die("Unauthorized.");
        }

        if(!file_exists("offlineNotificationsSettings.txt")){
            die("Missing notifications settings file");
        }

        // last station data
        $data = file_get_contents("../meteotemplateLive.txt");
        $data = json_decode($data, true);
        $lastTime = $data['U'];

        $notifyData = json_decode(file_get_contents("offlineNotificationsSettings.txt"), true);
        $notifyInterval = $notifyData['period'] * 60; // convert to seconds

        $notifyThreshold = time() - $notifyInterval;

        if($lastTime < $notifyThreshold){
            $online = false;
        }
        else{
            $online = true;
        }

        if($online){
            // back online? 
            if(file_exists("../cache/stationOffline.txt")){
                unlink("../cache/stationOffline.txt");
	            mail($notifyData['email'],"Station back online","Your weather station is now back online.");
                echo "Station back online.";
            }
            else{
                echo "Station online.";
            }
        }
        else{
            if(!file_exists("../cache/stationOffline.txt")){
                file_put_contents("../cache/stationOffline.txt",$lastTime);
                mail($notifyData['email'],"Station offline","Your weather station is offline, last time data received: ".date($dateTimeFormat,$lastTime).".");
                echo "Station now offline, email sent.";
            }
            else{
                echo "Statino still offline.";
            }
        }
        die();   
    }

    if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

    if(isset($_GET['email'])){
        $save['email'] = $_GET['email'];
        $save['period'] = $_GET['period'];
        file_put_contents("offlineNotificationsSettings.txt",json_encode($save));
    }

    if(file_exists("offlineNotificationsSettings.txt")){
        $notifyData = json_decode(file_get_contents("offlineNotificationsSettings.txt"), true);
        $notifyEmail = $notifyData['email'];
        $notifyPeriod = $notifyData['period'];
    }
    else{
        $notifyEmail = "email@gmail.com";
        $notifyPeriod = 30;
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
			<div class="textDiv">
			<h1>Offline Notifications</h1>
            <p>
                On this page you can set up email notifications sent when your station is not reporting. What you will need is a CRON job to run this check periodically. 
            </p>
            <p>
                First things first, let's check your server is correctly configured for the notifcations to work. We need to check the mail server is working (otherwise no emails can be sent) and we need to check few other things. 
            </p>
            <form method="POST" target="_self">
                Specify the email address where you want the notifications to be sent and click the "Test email" button. This will send a test email to the specified address. If you do not receive this email it means the mail server is not working correctly and the notifications will not work, so make sure you do this. 
                <br><br> 
                Email: <input id="email" name="email" class="button2" value="<?php echo $notifyEmail?>">&nbsp;<input type="button" class="button" value="Test email" id="testEmail">
                <br><br>
                If you received the test email you can continue with this setup. Now click the button below to test few other things on your server necessary for the notifications to work correctly. 
                <br><br>
                <input type="button" value="Test server configuration" class="button" id="testServer">
                <br>
                <hr>
                <p>
                    If you received the test email and the server check was successful you can now setup the notifications. Email used for sending the offline notification will be the one specified above.
                </p>
                <input type="number" class="button" value="<?php echo $notifyPeriod?>" id="period"> time in minutes (if station is not reporting for longer than this, an email will be sent)<br>
                <p> 
                    Once the notification email is sent, no more emails will be sent as long as the station is offline. As soon as station is deteced online, another email will be sent and the offline status reset. If you are encountering problems where your station goes offline and online several times a day and you receive too many emails, simply stop the CRON job until the problem is solved. 
                </p>
                <p>
                    MAKE SURE YOU CLICK SAVE SO THAT THE NOTIFICATION EMAIL AND PERIOD ARE SAVED!<br> 
                    <input type="button" value="Save" class="button" id="save">
                </p>
                <p>
                    <strong>Set up a CRON job for <?php echo $pageURL.$path?>admin/offlineNotification.php?password=<?php echo $updatePassword?></strong>. Make sure the CRON job interval is relatively short otherwise there will be a delay.
                </p>
            </form>
			<br><br>
		</div>
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
            $("#testEmail").click(function(){
				mailAddress = $("#email").val();
				window.open("../install/testEmail.php?address="+encodeURIComponent(mailAddress));
			})

            $("#testServer").click(function(){
				window.open("offlineCheck.php");
			})
            $("#save").click(function(){
                mailAddress = $("#email").val();
                period = $("#period").val();
				location = "offlineNotification.php?email="+mailAddress+"&period="+period;
			})
		</script>
		
	</body>
</html>
	