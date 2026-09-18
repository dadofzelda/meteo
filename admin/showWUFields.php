<?php 

	// check acces authorization
	session_start();
	if($_SESSION['user']!="admin"){
		echo "Unauthorized access.";
		die();
	}
	
	$ID = $_GET['id'];

	$month = date('m');
	$year = date('Y');
	$day = date('d');
	
	$url = 'https://www.wunderground.com/weatherstation/WXDailyHistory.asp?ID='.$ID.'&month='.$month.'&year='.$year.'&day='.$day.'&format=1';
	
	echo "Loading data from: ".$url."<br><br>";
	
	$data = file_get_contents('http://www.wunderground.com/weatherstation/WXDailyHistory.asp?ID='.$ID.'&month='.$month.'&year='.$year.'&day='.$day.'&format=1');
	
	if($data==""){
		die("Unable to load data from WU or incorrect WU specified.");
	}
	
	echo "Data loaded...<br><br>";
	
	$exploded = explode("<br>",$data);
	$fields = explode(",",$exploded[0]);
	
	echo "Showing WU columns for station ".$ID."...<br>";
	
	echo "<br>";
?>
	<table>
		<thead>
			<tr>
				<th>
					Field Number
				</th>
				<th>
					Variable/units
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
				for($i=0;$i<count($fields);$i++){
			?>
					<tr>
						<td>
							<?php echo $i?>
						</td>
						<td>
							<?php echo $fields[$i]?>
						</td>
					</tr>
			<?php
				}
			?>
		</tbody>
	</table>
	
	