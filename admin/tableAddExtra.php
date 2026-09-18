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
	#	Database Editing Page - adding values
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
		<style>
			.headIcon{
				width: 30px;
			}
		</style>
	</head>
	<body>
		<div id="main_top">
			<?php bodyHeader();?>
			<?php include($baseURL."menu.php")?>
		</div>
		<div id="main" style="text-align:center">
			<h1>New data</h1>
			<br>
			<table class="table" style="width:auto;margin:0 auto">
				<thead>
					<tr>
						<th style="text-align:center">
							Year
						</th>
						<th>
							Month
						</th>
						<th>
							Day
						</th>
						<th>
							Hour
						</th>
						<th>
							Minute
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td style="text-align:center">
							<input class="button tooltip" id="y" size="4" title="year in YYYY format" value="<?php echo date('Y')?>">
						</td>
						<td>
							<input class="button tooltip" id="m" size="2" title="month in MM format" value="<?php echo date('m')?>">
						</td>
						<td>
							<input class="button tooltip" id="d" size="2" title="day in DD format" value="<?php echo date('d')?>">
						</td>
						<td>
							<input class="button tooltip" id="h" size="2" title="hour in 24h format" value="<?php echo date('H')?>">
						</td>
						<td>
							<input class="button tooltip" id="i" size="2" title="minute in mm format" value="<?php echo date('i')?>">
						</td>
					</tr>
				</tbody>
			</table>
			<br><br>
			<table class="table" style="width:98%;margin:0 auto">
				<thead>
					<tr>
                        <?php 
                            for($i=0;$i<count($extraCols);$i++){
                        ?>
                            <th style="text-align:center">
                                <?php echo $extraCols[$i]?>
                            </th>
                        <?php 
                            }
                        ?>
					</tr>
				</thead>
				<tbody>
					<tr>
                        <?php 
                            for($i=0;$i<count($extraCols);$i++){
                        ?>
                                <td style="text-align:center">
                                    <input id="<?php echo $extraCols[$i]?>" size="4" class="button tooltip">
                                </td>
						<?php 
                            }
                        ?>
					</tr>
				</tbody>
			</table>
			<br><br><br>
			<input type="button" class="button" value="Save" onclick="save()">
		</div>
		<?php include($baseURL."footer.php")?>
		<script>
			function save(){
				y = eval($("#y").val());
				if(y<1990 || y><?php echo date('Y')?>){
					alert("Incorrect year.");
					return false;
				}
				m = eval($("#m").val());
				if(m<1 || m>12){
					alert("Incorrect month.");
					return false;
				}
				d = eval($("#d").val());
				if(d<1 || d>31){
					alert("Incorrect day.");
					return false;
				}
				h = eval($("#h").val());
				if(h<0 || h>23){
					alert("Incorrect hour.");
					return false;
				}
				i = eval($("#i").val());
				if(i<0 || i>59){
					alert("Incorrect minute.");
					return false;
				}
				<?php 
                    for($i=0;$i<count($extraCols);$i++){
                ?>
                        if($("#<?php echo $extraCols[$i]?>").val()===""){
                            alert("No <?php echo $extraCols[$i]?> specified.");
                            return false;
                        }
                        <?php echo $extraCols[$i]?> = eval($("#<?php echo $extraCols[$i]?>").val());
                <?php 
                    }
                ?>
				
				url="tableAddAjaxExtra.php?y="+y+"&m="+m+"&d="+d+"&h="+h+"&i="+i;
                <?php 
                    for($i=0;$i<count($extraCols);$i++){
                ?>
                        url += "&<?php echo $extraCols[$i]?>=" + <?php echo $extraCols[$i]?>;
                <?php 
                    }
                ?>
				var win = window.open(url, '_blank');
				win.focus();
			}
		</script>
	</body>
</html>
	