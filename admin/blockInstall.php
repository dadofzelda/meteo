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
	#	Blocks Install
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
	

?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
            .demoLink{
                font-size: 1.5em;
                opacity: 0.8;
                cursor: pointer;
            }
            .demoLink:hover{
				opacity:1;
			}
            .blockFilter{
                opacity: 0.8;
                cursor: pointer;
            }
            .blockFilter:hover{
				opacity:1;
			}
            .showDesc{
                font-variant: small-caps;
                font-size: 1.1em;
                opacity: 0.8;
                cursor: pointer;
            }
            .showDesc:hover{
                opacity:1;
            }
            .installClass{
                font-size: 1.5em;
                opacity: 0.8;
                cursor: pointer;
            }
            .installClass:hover{
                opacity:1;
            }
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<div class="textDiv">
			<h1>Block Installation</h1>
            <table style="margin-left:1%">
                <tr>
                    <td>
                        <span class='fa fa-check-circle' style='font-size:1em'></span>
                    </td>
                    <td style="text-align:left;padding-left:5px">
                        installed
                    </td>
                    <td>
                        <span class="blockFilter fa fa-filter" data-id="2"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='fa fa-times-circle' style='font-size:1em'></span>
                    </td>
                    <td style="text-align:left;padding-left:5px">
                        not installed
                    </td>
                    <td>
                        <span class="blockFilter fa fa-filter" data-id="0"></span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span class='fa fa-refresh' style='font-size:1em'></span>
                    </td>
                    <td style="text-align:left;padding-left:5px">
                        needs update
                    </td>
                    <td>
                        <span class="blockFilter fa fa-filter" data-id="1"></span>
                    </td>
                </tr>
            </table>
			<table class="table" style="width:98%;margin:0 auto">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th style="text-align:center">Namespace</th>
                        <th style="text-align:center">Status</th>
                        <th style="text-align:center">Description</th>
                        <th style="text-align:center">DEMO</th>
                        <th style="text-align:center">Install</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach($availableBlocks as $availableBlock){
                            if(!in_array($availableBlock['nameSpace'], $installedBlocks)){
                                $status = 0;
                            }
                            else{
                                $blockInfo = json_decode(file_get_contents('../homepage/blocks/'.$availableBlock['nameSpace'].'/'.$availableBlock['nameSpace'].'Config.txt'),true);
                                $myVersion = $blockInfo['version'];
                                $latestVersion = $availableBlock['version'];
                                if($myVersion < $latestVersion){
                                    $status = 1;
                                }
                                else{
                                    $status = 2;
                                }
                            }
                    ?>
                            <tr class="blockTR blockTR<?php echo $status?>">
                                <td>
                                    <?php echo str_replace("<br>"," ",$availableBlock['title'])?>
                                </td>
                                <td>
                                    <?php echo $availableBlock['nameSpace']?>
                                </td>
                                <td>
                                    <?php 
                                        if($status==0){
                                            echo "<span class='fa fa-times-circle' style='font-size:1.5em'></span>";
                                        }
                                        if($status==1){
                                            echo "<span class='fa fa-refresh' style='font-size:1.5em'></span>";
                                        }
                                        if($status==2){
                                            echo "<span class='fa fa-check-circle' style='font-size:1.5em'></span>";
                                        }
                                    ?>
                                </td>
                                <td>
                                    <span class="showDesc" onclick="openDesc('<?php echo $availableBlock['description']?>')">Show</span>
                                </td>
                                <td>
                                    <div style="width:40%;float:left;text-align:center">
                                        <span class="demoLink fa fa-external-link" onclick="openWindow('<?php echo $meteotemplateURL?>/template/indexBlockDemoNarrow.php?block=<?php echo $availableBlock['nameSpace']?>','DEMO')"></span>
                                        <br>
                                        Narrow
                                    </div>
                                    <div style="width:40%;float:right;text-align:center">
                                        <span class="demoLink fa fa-external-link" onclick="openWindow('<?php echo $meteotemplateURL?>/template/indexBlockDemoWide.php?block=<?php echo $availableBlock['nameSpace']?>','DEMO')"></span>
                                        <br>
                                        Wide
                                    </div>
                                </td>
                                <td>
                                    <?php
                                        if($status==0){
                                    ?>
                                            <span class="installClass fa fa-download" data-id="<?php echo $availableBlock['nameSpace']?>" data-version="<?php echo number_format($availableBlock['version'],1,'.','')?>"></span>
                                    <?php
                                        }
                                    ?>
                                </td>
                            </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
			<br><br>
		</div>
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
            $(".blockFilter").click(function(){
                id = $(this).attr("data-id");
                $(".blockTR").hide();
                $(".blockTR" + id).show();
            });
            $(".installClass").click(function(){
                id = $(this).attr("data-id");
                version = $(this).attr("data-version");
                confirmIt = confirm("Do you really want to install this block?");
                if(confirmIt){
                    openWindowInstall("blockInstallProcess.php?id=" + id + "_" + version);
                }
            })
			function openWindow(url,title){
				dialogHeight = screen.height*0.7;
				dialogWidth = screen.width*0.9;
				var $dialog = $('<div style="overflow:hidden;background:white;color:black"></div>')
					.html('<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>')
					.dialog({
						autoOpen: false,
						modal: true,
						height: dialogHeight,
						width: dialogWidth,
						title: title,
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
            function openDesc(text){
				dialogHeight = screen.height*0.3;
				dialogWidth = screen.width*0.5;
				var $dialog = $('<div style="overflow:hidden;background:white;color:black"></div>')
					.html(text)
					.dialog({
						autoOpen: false,
						modal: true,
						height: dialogHeight,
						width: dialogWidth,
						title: 'Description',
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
            function openWindowInstall(url,title){
				dialogHeight = screen.height*0.7;
				dialogWidth = screen.width*0.7;
				var $dialog = $('<div style="overflow:hidden;background:white;color:black"></div>')
					.html('<iframe style="border: 0px; " src="' + url + '" width="100%" height="100%"></iframe>')
					.dialog({
						autoOpen: false,
						modal: true,
						height: dialogHeight,
						width: dialogWidth,
						title: title,
						show: {
							effect: "fade",
							duration: 400
						},
						hide: {
							effect: "fade",
							duration: 800
						},
						close : function(){
							location = "blockInstall.php";
						} 
					});
				$dialog.dialog('open');
			}
		</script>
		
	</body>
</html>
	