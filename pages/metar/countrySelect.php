<?php
$country = $_GET['country'];
$country = strtoupper($country);

$places = array();

$myfile = fopen("stations.txt", "r") or die("Unable to open file!");
while(!feof($myfile)) {
	$string = fgets($myfile);
	if(substr($string,81,2)==$country){
		if($country=="US"||$country=="CA"){
			$places[substr($string,20,4)] = trim(substr($string,3,16)).", ".strtoupper(substr($string,0,2));
		}
		else{
			$places[substr($string,20,4)] = trim(substr($string,3,16));
		}
	}
}
asort($places);
?>
<select id="countryPlaces" class="button" onchange="loadNew()">
	<option value="" selected></option>
	<?php
		foreach ($places as $index => $value) {
			echo "<option value='".$index."'>".ucwords(strtolower($value))."</option>";
		}
	?>
</select>