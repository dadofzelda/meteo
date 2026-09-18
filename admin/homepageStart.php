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
	
	$type = $_GET['type'];
	
	if($type=="desktop" && file_exists("homepageLayoutDesktop.txt")){
		header("Location: createHomepage.php?type=desktop");
	}
	else if($type=="mobile" && file_exists("homepageLayoutMobile.txt")){
		header("Location: createHomepage.php?type=mobile");
	}
	else{

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
			<div class="textDiv" style="width:90%">
				<h1>Homepage Main Setup</h1>
				<p>
					You have not yet set up your homepage or you reset it. Before you specify the blocks you want to show you first need to choose how many columns you want and how wide should they be. In the below field specify the widths in percentages of the columns you want, separated by a comma. They must add up to 100%.
				</p>
				<p>
					<h3>Examples</h3>
					<ul>
						<li><strong>25,50,25</strong> - this would create 3 columns, the one on the left and right will occupy 25% each, the middle one would be twice as wide (50%)</li>
						<li><strong>50,50</strong> - this would create a homepage with two equal-width columns</li>
						<li><strong>100</strong> - this would create homepge with just one column that will span the width of the whole page</li>
					</ul>
				</p>
				<br>
				<form action="createHomepage.php?type=<?php echo $type?>" method="post" onsubmit="return validateForm()">
					<div style="width:98%;text-align:center;margin:0 auto">
						<input type="hidden" name="site" value="<?php echo $siteType?>">
						<input name="columnWidths" value="25,50,25" class="button2" id="columnWidths">
						<br><br>
						<input type="submit" class="button" value="Continue">
					</div>
				</form>
			</div>
		</div>
		</div>
		<?php include($baseURL."footer.php")?>		
		<script type="text/javascript">
			function validateForm(){
				var sum = 0;
				specifiedWidths = $("#columnWidths").val();
				specifiedWidths = specifiedWidths.replace(/\s/g, ''); // remove spaces if necessary
				$("#columnWidths").val(specifiedWidths);
				var widths = specifiedWidths.split(',');
				for(var i = 0; i < widths.length; i++){	
					number = eval(widths[i]);
					sum = sum + number;
					if(number<=100){}
					else{
						alert("Incorrect input. Make sure you only enter the widths as numbers, separated by commas, the numbers must add up to 100%.");
						return false;
					}		
				}
				if(sum!=100){
					alert("The numbers do not add up to 100%");
					return false;
				}
			}
		</script>
	</body>
</html>

<?php
	}
?>