<?php
$year = array("2015","2000", "1985");
$coordinates = find_coordinates($year);
for ($i=0; $i < count($year); $i++) { 
	$url = "http://earthquake.usgs.gov/fdsnws/event/1/query.geojson?starttime=".$year[$i]."-01-01%2000%3A00%3A00&endtime=".$year[$i]."-12-30%2023%3A59%3A59&maxlatitude=50&minlatitude=24.6&maxlongitude=-65&minlongitude=-125&minmagnitude=4.5&orderby=time";
	$output = file_get_contents($url);
	$count = find_count($output);
	$cut = $output;
	$yearData = $coordinates;
	while ($count>0) {
		$pos = strpos($cut, 'mag":') + 5;
		$cut = substr($cut, $pos);
		$mag = substr($cut, 0, 3);
		$mag /= 10;
		$mag .= "0";
		$latpos = strpos($cut, '[') + 1;
		$cut = substr($cut, $latpos);
		$lat = substr($cut, 0, 5);
		$lat = round($lat);
		$longpos = strpos($cut, ',')+ 1;
		$long = substr($cut, $longpos, 2);
		$dataset = $long . "," . $lat . "," . $mag;
		if ($count!=1) {
			$dataset .= ",";
			$temp = str_replace($long.",".$lat.","."0.000,",$dataset, $yearData);
		}
		else {
			$temp = str_replace($long.",".$lat.","."0.000",$dataset, $yearData);
		}
		if ($temp) {
			$yearData = $temp;
		}
		$count-=1;
	}
	if ($i==0) {
		$final="[".'"'.$year[$i].'",'."[".$yearData."]]";
	}
	else {
		$final = "[".'"'.$year[$i].'",'."[".$yearData."]],".$final;
	}
}
$final = "[".$final."]";
echo $final;
function find_count($data){
	$pos = strpos($data, 'count":') + 7;
	$cut = substr($data, $pos);
	$end = strpos($cut, "}");
	$count = substr($cut, 0, $end);
	return $count;
}
function find_coordinates($year){
	for ($i=0; $i < count($year); $i++) { 
		$url = "http://earthquake.usgs.gov/fdsnws/event/1/query.geojson?starttime=".$year[$i]."-01-01%2000%3A00%3A00&endtime=".$year[$i]."-12-30%2023%3A59%3A59&maxlatitude=50&minlatitude=24.6&maxlongitude=-65&minlongitude=-125&minmagnitude=4.5&orderby=time";
		$output = file_get_contents($url);
		$count = find_count($output);
		$cut = $output;
		while ($count>0) {
			$pos = strpos($cut, 'mag":') + 5;
			$cut = substr($cut, $pos);
			$latpos = strpos($cut, '[') + 1;
			$cut = substr($cut, $latpos);
			$lat = substr($cut, 0, 5);
			$lat = round($lat);
			$longpos = strpos($cut, ',')+ 1;
			$long = substr($cut, $longpos, 2);
			$yearData .= $long . "," . $lat .",0.000";
			if ($count!=1) {
				$yearData .= ",";
			}
			$count-=1;
		}
	}
	return $yearData;
}
?>