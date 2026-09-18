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

    if(!file_exists("customPages.txt")){
        file_put_contents("customPages.txt","");
    }

    $availablePages = json_decode(file_get_contents("customPages.txt"),true);


?>

<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $pageName?></title>
		<?php metaHeader()?>
		<style>
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
			<h1>Custom Pages</h1>
			<p>Here you can create your own pages providing HTML code. The header, menu, footer, styling etc. will be automatically added. Use normal HTML tags. For security reasons, Javascript is disabled and will not work if you include it in your code as well as iframes.</p>
            <p>
                To add link to your custom page in your menu, use the syntax for external link. The URL of your custom page is:<br>
                <pre><?php echo $pageURL.$path?>custom/customPage.php?page=XXXXXX</pre>
                where the XXXXXX is the namespace of that particular page you want to use.
            </p>
			<br>
            <span class="fa fa-plus-circle menuTabControl tooltip" onclick="location='customPageEdit.php'"></span>&nbsp;<?php echo lang('new page','c')?>
            <br><br><br>
			<?php
				if(count($availablePages)==0){
					echo "No custom pages created so far.";
				}
				else{
			?>
					<table style="width:100%;margin:0 auto" class="table">
						<?php
							foreach($availablePages as $pageNamespace=>$code){
						?>
								<tr>
									<td style="text-align:left;font-weight:bold;font-variant:small-caps;font-size:1.2em">
										<?php echo $pageNamespace?>
									</td>
									<td style="text-align:right">
                                        <span class="fa fa-eye menuTabControl tooltip" title="<?php echo lang('show','c')?>" onclick="window.open('<?php echo $pageURL.$path?>custom/customPage.php?page=<?php echo $pageNamespace?>','_blank')"></span>
										<span class="fa fa-gear menuTabControl tooltip" onclick="location='customPageEdit.php?page=<?php echo $pageNamespace?>'" title="<?php echo lang('edit','c')?>"></span>
										<span class="fa fa-trash menuTabControl tooltip" onclick="deletePage('<?php echo $pageNamespace?>')" title="<?php echo lang('delete','c')?>"></span>
									</td>
								</tr>
						<?php
							}
						?>
					</table>
            <?php
                }
            ?>
		</div>
		</div>
		<?php include($baseURL."footer.php");?>
        <script>
            function deletePage(page){
				confirmIt = confirm("Really delete this page and all its content?");
				if(confirmIt){
					location="customPageDelete.php?page="+page;
				}
			}
        </script>
	</body>
</html>
	