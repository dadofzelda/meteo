<?php
	include("../config.php");
	include($baseURL."css/design.php");
	include($baseURL."header.php");
	
	$availableSchemes = array();
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $pageName?></title>
	<?php metaHeader()?>
	<style>
		@import url(https://fonts.googleapis.com/css?family=Bree+Serif);
		@import url(https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic-ext,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=PT+Sans+Narrow:400,700&subset=latin,cyrillic-ext,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&subset=latin,cyrillic-ext,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Dosis:400,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Ubuntu:400,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Lobster&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Kaushan+Script&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Play:400,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Anton&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Inconsolata:400,700&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Righteous&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Marck+Script&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Poiret+One&subset=latin,latin-ext);
		@import url(https://fonts.googleapis.com/css?family=Cutive+Mono&subset=latin,latin-ext);
	</style>
</head>
<body>
<div id="main_top">
	<?php bodyHeader();?>
	<?php include($baseURL."menu.php");?>
</div>
<div id="main" style="text-align:center">
	<h1>Design settings</h1>
	<?php 
		if($userCustomColor){
	?>
		<table style="width:90%;margin-right:auto;margin-left:auto" class="table">
			<thead>
				<tr>
					<th colspan="10"></th>
					<th>Color 1</th>
					<th>Color 2</th>
				</tr>
			</thead>
		<?php
			$availableSchemes = array();
			foreach( array_keys( $color_schemes ) as $index=>$key ) {
				array_push($availableSchemes,$key);
			}
			$i = 0;
			foreach($color_schemes as $scheme){
		?>
				<tr>
					<th style="text-align:left"><?php echo ucfirst($availableSchemes[$i])?></th>
					<td style="width:7%;background-color:#<?php echo $scheme['100']?>;color:#<?php echo $scheme['font100']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['200']?>;color:#<?php echo $scheme['font200']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['300']?>;color:#<?php echo $scheme['font300']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['400']?>;color:#<?php echo $scheme['font400']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['500']?>;color:#<?php echo $scheme['font500']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['600']?>;color:#<?php echo $scheme['font600']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['700']?>;color:#<?php echo $scheme['font700']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['800']?>;color:#<?php echo $scheme['font800']?>">abc</td>
					<td style="width:7%;background-color:#<?php echo $scheme['900']?>;color:#<?php echo $scheme['font900']?>">abc</td>
					<th>
						<input type="button" class="button" id="setDesign1<?php echo $availableSchemes[$i]?>" value="set">
					</th>
					<th>
						<input type="button" class="button" id="setDesign2<?php echo $availableSchemes[$i]?>" value="set">
					</th>
				</tr>
		<?php
				$i++;
			}
		?>
		</table>
		<br><br>
	<?php
		}
	?>
	<?php 
		if($userCustomFont){
	?>
		<h1>Font examples</h1>
		<table style="width:90%;margin-right:auto;margin-left:auto" class="table tableSpacing2Padding2">
			<thead>
				<tr>
					<th colspan="7"></th>
					<th>Font 1</th>
					<th>Font 2</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$availableFonts = array("PT Sans","Roboto","Dosis","Ubuntu","Lobster","Kaushan Script","Open Sans","Play","Open Sans Condensed","Anton","Arial","Tahoma","Times New Roman","Helvetica","Inconsolata",'Righteous','Marck Script','Poiret One','Cutive Mono');
					for($i=0;$i<count($availableFonts);$i++){
				?>
					<tr>
						<th style="text-align:left;font-family:'<?php echo $availableFonts[$i]?>'>"><?php echo $availableFonts[$i]?></th>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:1.0em"><?php echo ucfirst(strtolower($pageName))?></td>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:1.3em"><?php echo ucfirst(strtolower($pageName))?></td>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:2.2em"><?php echo ucfirst(strtolower($pageName))?></td>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:2.2em;font-weight:bold"><?php echo ucfirst(strtolower($pageName))?></td>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:2.2em;font-variant:small-caps">AaBbCc12345</td>
						<td style="text-align:center;font-family:'<?php echo $availableFonts[$i]?>';font-size:2.2em;font-weight:bold">AaBbCc12345</td>
						<th>
							<input type="button" class="button" id="setFont1<?php echo str_replace(' ','-',$availableFonts[$i])?>" value="set">
						</th>
						<th>
							<input type="button" class="button" id="setFont2<?php echo str_replace(' ','-',$availableFonts[$i])?>" value="set">
						</th>
					</tr>
				<?php
					}
				?>
			</tbody>
		</table>
	<?php
		}
	?>
</div>
<?php include($baseURL."footer.php")?>
<script>
	<?php 
		for($i=0;$i<count($availableSchemes);$i++){
	?>
		$("#setDesign1<?php echo $availableSchemes[$i]?>").click(function() {
			$.ajax({url: "<?php echo $pageURL.$path?>css/layoutSet.php?parameter=design&value=<?php echo $availableSchemes[$i]?>", success: function(result){		
				location.reload();
			}});
		});
		$("#setDesign2<?php echo $availableSchemes[$i]?>").click(function() {
			$.ajax({url: "<?php echo $pageURL.$path?>css/layoutSet.php?parameter=design2&value=<?php echo $availableSchemes[$i]?>", success: function(result){		
				location.reload();
			}});
		});
	<?php 
		}
	?>
	<?php 
		for($i=0;$i<count($availableFonts);$i++){
	?>
		$("#setFont1<?php echo str_replace(' ','-',$availableFonts[$i])?>").click(function() {
			$.ajax({url: "<?php echo $pageURL.$path?>css/layoutSet.php?parameter=designFont&value=<?php echo str_replace(' ','-',$availableFonts[$i])?>", success: function(result){		
				location.reload();
			}});
		});
		$("#setFont2<?php echo str_replace(' ','-',$availableFonts[$i])?>").click(function() {
			$.ajax({url: "<?php echo $pageURL.$path?>css/layoutSet.php?parameter=designFont2&value=<?php echo str_replace(' ','-',$availableFonts[$i])?>", success: function(result){		
				location.reload();
			}});
		});
	<?php
		}
	?>
</script>
</body>
</html>
	