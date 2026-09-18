<?php
header('Content-Type: application/json');
header('Content-type: text/html; charset=UTF-8');

$results = array();
$dataArray = array();
$dataArray2 = array();

$data = array();
$file = fopen("complete.csv","r");
while(! feof($file))
  {
	
	$tmp = fgetcsv($file);
	$data = explode(';',$tmp[0]);
	if($data[0]!=""){
		$lat = $data[7];
		$lon = $data[8];
		$name = $data[1];
		$id = $data[0];
		$country = $data[2];
		$region = $data[4];
		$countrycode = $data[3];
		$elevation = $data[9];
		$tavg = $data[40];
		$mintavg = $data[55];
		$maxtavg = $data[27];
		$havg = $data[109];
		$rain = $data[68];
		$sunlight = $data[96];
		$wetday = $data[83];
		$daylength = $data[123];
		$temprange = $data[41];
		$rainrange = $data[69];
		$humidityrange = $data[110];
		$ratio = $data[96];
		$koppen = $data[12];
		$trewartha = $data[13];
		
		$results['lat'] = $lat;
		$results['lon'] = $lon;
		$results['id'] = $id;
		$results['name'] = $name;
		$results['region'] = $region;
		$results['country'] = $country;
		$results['countrycode'] = $countrycode;
		$results['elevation'] = $elevation;
		$results['temp'] = $tavg;
		$results['mintemp'] = $mintavg;
		$results['maxtemp'] = $maxtavg;
		$results['humidity'] = $havg;
		$results['rain'] = $rain;
		$results['sunlight'] = $sunlight;
		$results['wetdays'] = $wetday;
		$results['daylength'] = $daylength;
		$results['temprange'] = $temprange;
		$results['rainrange'] = $rainrange;
		$results['humidityrange'] = $humidityrange;
		$results['ratio'] = $ratio;
		$results['koppen'] = $koppen;
		$results['trewartha'] = $trewartha;
		
		array_push($dataArray, $results);
	}
  }
fclose($file);

echo "var json = ";

print json_encode($dataArray);
?>