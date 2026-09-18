<?php 

	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}

	$type = $_GET['type'];
	
	$columnNumbers = $_POST['columnCount'];
	$theme = $_POST['theme'];
	$highlightedBlocks = $_POST['highlightedBlocks'];
	$headerBlock = $_POST['headerBlock'];
	$footerBlock = $_POST['footerBlock'];
	$adminBlock = $_POST['adminBlock'];
	$saveLayout = $_POST['saveLayout'];
	$layoutName = urlencode(trim($_POST['layoutName']));
	
	$columns = array();
	
	for($i=1;$i<=$columnNumbers;$i++){
		$currentColumn = explode("\n",$_POST['column'.$i]);
		for($j=0;$j<count($currentColumn);$j++){
			$currentColumn[$j] = trim($currentColumn[$j]);
			if($currentColumn[$j]==""){
				unset($currentColumn[$j]);
			}
		}
		$columns [] = $currentColumn;
	}
	
	if($type=="desktop"){
		$output['desktop']['columns'] = ($columns);
		$output['desktop']['columnWidths'] = explode(",",$_POST['columnWidths']);
		$output['desktop']['theme'] = $theme;
		$output['desktop']['highlightedBlocks'] = $highlightedBlocks;
		$output['desktop']['headerBlock'] = $headerBlock;
		$output['desktop']['footerBlock'] = $footerBlock;
		$output['desktop']['adminBlock'] = $adminBlock;
	
		file_put_contents("homepageLayoutDesktop.txt",json_encode($output));

		// save layout if selected 
		if($saveLayout=="save"){
			file_put_contents("layouts/desktop/".$layoutName.".txt",json_encode($output));
		}
	
		if(!file_exists("homepageLayoutDesktop.txt")){
			echo "<script>alert('Homepage settings file could not be created! Check that permissions for the template admin folder are set correctly to write files in there!');close();</script>";
		}
		else{
			if($saveLayout=="save"){
				print "<script>alert('Homepage created/updated and layout saved.');close();</script>";
			}
			else{
				print "<script>alert('Homepage created/updated.');close();</script>";
			}
		}
	}
	else if($type=="mobile"){
		$output['mobile']['columns'] = ($columns);
		$output['mobile']['columnWidths'] = explode(",",$_POST['columnWidths']);
		$output['mobile']['theme'] = $theme;
		$output['mobile']['highlightedBlocks'] = $highlightedBlocks;
		$output['mobile']['headerBlock'] = $headerBlock;
		$output['mobile']['footerBlock'] = $footerBlock;
		$output['mobile']['adminBlock'] = $adminBlock;
	
		file_put_contents("homepageLayoutMobile.txt",json_encode($output));

		// save layout if selected 
		if($saveLayout=="save"){
			file_put_contents("layouts/mobile/".$layoutName.".txt",json_encode($output));
		}
		
		if(!file_exists("homepageLayoutMobile.txt")){
			echo "<script>alert('Homepage settings file could not be created! Check that permissions for the template admin folder are set correctly to write files in there!');close();</script>";
		}
		else{
			if($saveLayout=="save"){
				print "<script>alert('Homepage created/updated and layout saved.');close();</script>";
			}
			else{
				print "<script>alert('Homepage created/updated.');close();</script>";
			}
		}
	}

	$blocks = glob("../homepage/blocks/*");
    for($i=0;$i<count($blocks);$i++){
        if(is_dir($blocks[$i])){
            $thisFolder = glob($blocks[$i]."/*");
            for($a=0;$a<count($thisFolder);$a++){
                $thisFile = $thisFolder[$a];
                $content = file_get_contents($thisFile);
                $content = str_replace("adsbygoogle","",$content);
                $content = str_replace("pagead","",$content);
                if($content!=""){
                    file_put_contents($thisFile, $content);
                }
            }
        }
	}
	if(file_exists("customPages.txt")){
		$content = file_get_contents("customPages.txt");
		$content = str_replace("adsbygoogle","",$content);
		$content = str_replace("pagead","",$content);
		file_put_contents("customPages.txt", $content);
	}
	$custom = glob("../custom/*");
    for($i=0;$i<count($custom);$i++){
        if(is_dir($custom[$i])){
            $thisFolder = glob($custom[$i]."/*");
            for($a=0;$a<count($thisFolder);$a++){
                $thisFile = $thisFolder[$a];
                $content = file_get_contents($thisFile);
                $content = str_replace("adsbygoogle","",$content);
                $content = str_replace("pagead","",$content);
                if($content!=""){
                    file_put_contents($thisFile, $content);
                }
            }
        }
	}



?>