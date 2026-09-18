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
	#	XML sitemap
	#
	############################################################################
	
	
	include("config.php");

    $sitemap = array();
    
    if(file_exists("sitemap.txt")){
        $sitemap = file_get_contents("sitemap.txt");
        $sitemap = json_decode($sitemap);
    }

    header('Content-type: text/xml');

    echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

    <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <url>
            <loc>
                <?php echo $pageURL.$path?>/index.php
            </loc>
        </url>
<?php
    foreach($sitemap as $heading=>$data){
        if(!is_array($data)){
?>
            <url>
                <loc>
                    <?php echo $data?>
                </loc>
            </url>
<?php
        }
        else{
            for($i=0;$i<count($data);$i++){
        ?>
            <url>
                <loc>
                    <?php echo $data[$i][1]?>
                </loc>
            </url>
<?php
            }
        }
    }

?>
    </urlset> 
