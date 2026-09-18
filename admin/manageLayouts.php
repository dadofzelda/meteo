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
	
	// load available layouts
    // Desktop 
    $desktopLayouts = array();
    foreach (glob("layouts/desktop/*.txt") as $fileName) {
        $fileName = str_replace(".txt","",$fileName);
        $desktopLayouts[] = str_replace("layouts/desktop/","",$fileName);
    }
    // Mobile 
    $mobileLayouts = array();
    foreach (glob("layouts/mobile/*.txt") as $fileName) {
        $fileName = str_replace(".txt","",$fileName);
        $mobileLayouts[] = str_replace("layouts/mobile/","",$fileName);
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
				<h1><?php echo lang('saved layouts','c')?></h1>
                <h3><?php echo lang('desktop','c')?></h3>
                <br>
                <?php 
                    if(count($desktopLayouts)==0){
                        echo "No saved desktop layouts.";
                    }
                    else{
                ?>
                        <table style="width:100%;margin:0 auto" class="table">
                            <?php
                                for($i=0;$i<count($desktopLayouts);$i++){
                            ?>
                                    <tr>
                                        <td style="text-align:left;font-weight:bold;font-size:1.2em;width:80%;">
                                            <?php echo ucwords($desktopLayouts[$i])?>
                                        </td>
                                        <td>
                                            <input class="button2" value="<?php echo lang('use','c')?>" onclick="useLayout('<?php echo $desktopLayouts[$i]?>','desktop')"></span>
                                        </td>
                                        <td>
                                            <input class="button2" value="<?php echo lang('delete','c')?>" onclick="deleteLayout('<?php echo $desktopLayouts[$i]?>','desktop')"></span>
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
                <h3><?php echo lang('mobile','c')?></h3>
                <br>
                <?php 
                    if(count($mobileLayouts)==0){
                        echo "No saved mobile layouts.";
                    }
                    else{
                ?>
                        <table style="width:100%;margin:0 auto" class="table">
                            <?php
                                for($i=0;$i<count($mobileLayouts);$i++){
                            ?>
                                    <tr>
                                        <td style="text-align:left;font-weight:bold;font-size:1.2em;width:80%;">
                                            <?php echo ucwords($mobileLayouts[$i])?>
                                        </td>
                                        <td>
                                            <input class="button2" value="<?php echo lang('use','c')?>" onclick="useLayout('<?php echo $mobileLayouts[$i]?>','mobile')"></span>
                                        </td>
                                        <td>
                                            <input class="button2" value="<?php echo lang('delete','c')?>" onclick="deleteLayout('<?php echo $mobileLayouts[$i]?>','mobile')"></span>
                                        </td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </table>
                <?php
                    }
                ?>
				<br>
			</div>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>		
		<script type="text/javascript">
            function useLayout(layout,type){
				confirmIt = confirm("Do you really want to set this layout? (This will overwrite your current layout, so if you did not save it, it would be lost.)");
				if(confirmIt){
					location="layoutUse.php?layout="+layout+"&type="+type;
				}
			}
			function deleteLayout(layout,type){
				confirmIt = confirm("Really delete this layout?");
				if(confirmIt){
					location="layoutDelete.php?layout="+layout+"&type="+type;
				}
			}
		</script>
	</body>
</html>
