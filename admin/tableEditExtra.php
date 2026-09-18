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
	#	Database Editing Page
	#
	# 	This page shows values from the main table of values for a user 
	# 	specified day and allows changing them.
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
	
	$day = $_GET['d'];
	$month = $_GET['m'];
	$year = $_GET['y'];
	
	if($day==""){
		$day = date('d');
		$month = date('m');
		$year =date('Y');
	}

    // check which sensors available
    $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$dbName' AND TABLE_NAME = 'alldataExtra'";
    $x = mysqli_query($con, $query);
    while($row = mysqli_fetch_array($x)){
        if($row['COLUMN_NAME'] != "DateTime"){
            $extraCols[] = $row['COLUMN_NAME'];
        }
    }
    
	
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo lang("administration",'c')?></title>
		<?php metaHeader()?>
		<script src="../scripts/table-edits.js"></script>
		<style>
			.table th{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.table tr:nth-child(even) {
				background: #<?php echo $color_schemes[$design2]['400']?>;
				color: #<?php echo $color_schemes[$design2]['font400']?>;
			}
			.table tr:nth-child(odd) {
				background: #<?php echo $color_schemes[$design2]['600']?>;
				color: #<?php echo $color_schemes[$design2]['font600']?>;
			}
			.table tbody tr:hover td{
				background: #<?php echo $color_schemes[$design2]['800']?>;
				color: #<?php echo $color_schemes[$design2]['font800']?>;
			}
			.table tfoot td{
				background: #<?php echo $color_schemes[$design2]['900']?>;
				color: #<?php echo $color_schemes[$design2]['font900']?>;
			}
			.tableSetting{
				font-variant: small-caps;
				font-size: 1.5em;
				color: #<?php echo $color_schemes[$design]['100']?>;
				font-weight: bold;
				background-color: #<?php echo $color_schemes[$design]['600']?>;
			}
			.headIcon{
				width: 30px;
			}
			#gotop{
				position:fixed;
				top:10px;
				right:10px;
			}
			#addButton{
				width:80px;
				opacity: 0.8;
			}
			#addButton:hover{
				opacity: 1;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php");?>
		</div>
		<div id="main">
			<h1>Database Editing - Extra Sensors</h1>
			<h2>
				<?php 
					$shown = strtotime($month."/".$day."/".$year);
					echo date($dateFormat,$shown);
				?>
			</h2>
			<br>
			<table style="width:98%;margin-left:auto;margin-right:auto">
				<tr>
					<td style="text-align:left">
						<a href="tableAddExtra.php"><img src="<?php echo $pageURL.$path?>icons/plus.png" id="addButton"></a>
					</td>
					<td style="text-align:right">
						<input id="daySelectOpenerAdmin" type="button" value="Select date" class="button">
					</td>
				</tr>
			</table>
            <div style="width:98%;margin:0 auto">
            <p>
                See Meteotemplate WIKI API documentation for parameter abbreviations and units. 
            </p>
            </div>
			<table class="table" style="width:98%;margin:0 auto">
				<thead>
					<tr>
						<th></th>
                        <?php 
                            for($i=0;$i<count($extraCols);$i++){
                        ?>
                            <th>
                                <?php echo $extraCols[$i]?>
                            </th>
						<?php   
                            }
                        ?>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$x = mysqli_query($con,
							"
								SELECT *
								FROM alldataExtra
								WHERE YEAR(DateTime)=$year AND MONTH(DateTime)=$month AND DAY(DateTime)=$day
								ORDER BY DateTime
							"
						);

					while($row = mysqli_fetch_array($x)){
						?>
							<tr data-id="<?php echo $row['DateTime']?>">
								<td><?php echo $row['DateTime']?></td>
                                <?php 
                                    for($i=0;$i<count($extraCols);$i++){
                                ?>
								        <td data-field="<?php echo $extraCols[$i]?>"><?php echo $row[$extraCols[$i]]?></td>
								<?php 
                                    }
                                ?>
								<td><input type="button" class="edit button" value="Edit"></td>
								<td><input type="button" class="button" value="Delete" onclick="deleteRecord(<?php echo strtotime($row['DateTime'])?>)"></td>
							</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php include($baseURL."footer.php");?>
		<div id="dialogDay" style="text-align:center">
			<div id="daySelect" style="margin-left:auto;margin-right:auto"></div>
		</div>
		<a href="" id="gotop" style="display: none;">
			<input type="button" class="button" value="<?php echo lang('gotop','c')?>">
		</a>
		<script>
			function deleteRecord(date){
				var confirmIt = confirm("Do you really want to delete this record?");
				if (confirmIt == false) {
					
				} 
				else {
					url = "tableDeleteExtra.php?date="+date;
					$.ajax({
						type: "GET",
						url : url,
						success : function (text) {
							alert(text);
							location.reload();
						}					
					})
				}
			}
			$("table tr").editable({
				edit: function(values) {
					$("td input", this).addClass('button');
					$("td input", this).attr('size','5');
					$(".edit", this).attr('value', 'Save');
				},
				save: function(values) {
					var confirmIt = confirm("Do you really want to save changes?");
					if (confirmIt == false) {
						
					} 
					else {
						$(".edit", this).attr('value', 'Edit');
						$("td input", this).attr('size','5');
						date = $(this).data('id');
						
                        <?php 
                            for($i=0;$i<count($extraCols);$i++){
                        ?>
						        <?php echo $extraCols[$i]?> = values['<?php echo $extraCols[$i]?>'];
						<?php 
                            }
                        ?>
						
						url = "tableUpdateExtra.php?date="+date;
                        <?php 
                            for($i=0;$i<count($extraCols);$i++){
                        ?>
                                url += "&<?php echo $extraCols[$i]?>="+<?php echo $extraCols[$i]?>;
                        <?php 
                            }
                        ?>
						$.ajax({
							type: "GET",
							url : url,
							success : function (text) {
								alert(text);
							}
						})
					}
				},
			});
			
			$( "#daySelect" ).datepicker({
				changeMonth: true,
				changeYear: true,
				maxDate: '0',
				dayNamesMin: ['<?php echo lang('sundayAbbr','c')?>', '<?php echo lang('mondayAbbr','c')?>', '<?php echo lang('tuesdayAbbr','c')?>', '<?php echo lang('wednesdayAbbr','c')?>', '<?php echo lang('thursdayAbbr','c')?>', '<?php echo lang('fridayAbbr','c')?>', '<?php echo lang('saturdayAbbr','c')?>'],
				monthNamesShort: ['<?php echo lang('month1','c')?>', '<?php echo lang('month2','c')?>', '<?php echo lang('month3','c')?>', '<?php echo lang('month4','c')?>', '<?php echo lang('month5','c')?>', '<?php echo lang('month6','c')?>', '<?php echo lang('month7','c')?>', '<?php echo lang('month8','c')?>', '<?php echo lang('month9','c')?>', '<?php echo lang('month10','c')?>', '<?php echo lang('month11','c')?>', '<?php echo lang('month12','c')?>'],
				firstDay: <?php echo $firstWeekday?>,
				dateFormat: "dd.mm.yy",
				onSelect: function(date) {
					d = date.slice(0,2);
					m = date.slice(3,5);
					y = date.slice(6,10);
					url = "tableEditExtra.php?d="+d+"&m="+m+"&y="+y;
					window.location = url;
				},
			});
			

			$( "#dialogDay" ).dialog({
				autoOpen: false,
				show: {
					effect: "puff",
					duration: 500
				},
				hide: {
					effect: "puff",
					duration: 500
				},
				height: 340,
				width: 350,
				position:{
					my: 'top', 
					at: 'top+30%'
				},
			});
			$( "#daySelectOpenerAdmin" ).click(function() {
				$( "#dialogDay" ).dialog( "open" );
			});
			$(function() {
				$("#gotop").scrollToTop();
			});
		</script>
	</body>
</html>
	