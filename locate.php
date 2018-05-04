<?php

	include 'includes/database.php';

	// Get user's country from lat/lon
	function getCountry($latitude, $longitude) {
		$url = 'http://api.geonames.org/countryCodeJSON?lat=' . $latitude . '&lng=' . $longitude . '&username=[key]';
		$json = file_get_contents($url);
		$obj = json_decode($json);
		return $obj->countryName;
	}

	function getLatLon($address) {
		$url = 'https://www.mapquestapi.com/geocoding/v1/address?key=[key]&inFormat=kvp&outFormat=json&location=' . urlencode($address) . '&thumbMaps=false';
		$json = file_get_contents($url);
		$obj = json_decode($json);

		$lat = $obj->results[0]->locations[0]->latLng->lat;
		$lng = $obj->results[0]->locations[0]->latLng->lng;

		return array($lat, $lng);
	}

	// Calculates the great-circle distance between two points with the Haversine formula.
	function haversineGreatCircleDistance(
	  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
		// convert from degrees to radians
		$latFrom = deg2rad($latitudeFrom);
		$lonFrom = deg2rad($longitudeFrom);
		$latTo = deg2rad($latitudeTo);
		$lonTo = deg2rad($longitudeTo);

		$latDelta = $latTo - $latFrom;
		$lonDelta = $lonTo - $lonFrom;

		$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
		cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
		return $angle * $earthRadius;
	}

	// Mark given coordinates on map
	function drawMap($coordinates) {
		$map_url = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $coordinates[0] . ',' . $coordinates[1] . '&size=800x300&sensor=false&markers=color:red|label:A|' . $coordinates[2] . ',' . $coordinates[3] . '&markers=color:red|label:B|' . $coordinates[4] . ',' . $coordinates[5] . '&markers=color:red|label:C|' . $coordinates[6] . ',' . $coordinates[7] . '&markers=color:red|label:D|' . $coordinates[8] . ',' . $coordinates[9] . '&markers=color:red|label:E|' . $coordinates[10] . ',' . $coordinates[11] . '&key=[key]';
		return $map_url;
	}

	if ( isset($_POST['location']) ) {
		$location = explode(',', $_POST['location']);
		$latitudeFrom = $location[0];
		$longitudeFrom = $location[1];

		error_log('>>> location by device: ' . implode(', ', $location));
		
		$target_country = getCountry($latitudeFrom, $longitudeFrom);

		$country_affiliates = $db->query('SELECT latitude, longitude, affiliate_id, name, address, city, county, postcode, kids, telephone, website FROM affiliates WHERE country LIKE("%' . $target_country . '%")');

		$nearby_affiliates = array();
		while ( $affiliate = $country_affiliates->fetchArray() ) {
			$distance = haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, floatval($affiliate[0]), floatval($affiliate[1]));
			$distance = round($distance/1000, 1);
			$nearby_affiliates[$distance] = array(
											'id'=>$affiliate[2],
											'name'=>$affiliate[3], 
											'address'=>$affiliate[3] . '<br />' . $affiliate[4] . '<br />' . $affiliate[5] . '<br />' .$affiliate[6] . '<br />' . $affiliate[7],
											'kids'=>$affiliate[8],
											'telephone'=>$affiliate[9],
											'website'=>$affiliate[10],
											'latitude'=>$affiliate[0],
											'longitude'=>$affiliate[1],
											'distance'=>$distance);
		}

		ksort($nearby_affiliates); // Sort array by distance (clostest first)

		$map_url = drawMap( array($latitudeFrom, $longitudeFrom, array_values($nearby_affiliates)[0]['latitude'], array_values($nearby_affiliates)[0]['longitude'], array_values($nearby_affiliates)[1]['latitude'], array_values($nearby_affiliates)[1]['longitude'], array_values($nearby_affiliates)[2]['latitude'], array_values($nearby_affiliates)[2]['longitude'],array_values($nearby_affiliates)[3]['latitude'], array_values($nearby_affiliates)[3]['longitude'],array_values($nearby_affiliates)[4]['latitude'], array_values($nearby_affiliates)[4]['longitude'] ) );

		// DEBUG
		for ($i=0; $i < 10; $i++) { 
			error_log('> Affiliate: ' . array_values($nearby_affiliates)[$i]['name'] . ' distance = ' .  array_values($nearby_affiliates)[$i]['distance']);
		}

		$json = json_encode( array('map_url'=>$map_url, 'nearby_affiliates'=>array_slice($nearby_affiliates, 0, 5)) );
		echo $json;

	}

	if ( isset($_POST['address']) ) {
		
		$location = getLatLon($_POST['address']);

		$latitudeFrom = $location[0];
		$longitudeFrom = $location[1];

		error_log('>>> location by search: ' . implode(', ', $location));
		
		$target_country = getCountry($latitudeFrom, $longitudeFrom);

		$country_affiliates = $db->query('SELECT latitude, longitude, affiliate_id, name, address, city, county, postcode, kids, telephone, website FROM affiliates WHERE country LIKE("%' . $target_country . '%")');

		$nearby_affiliates = array();
		while ( $affiliate = $country_affiliates->fetchArray() ) {
			$distance = haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, floatval($affiliate[0]), floatval($affiliate[1]));
			$distance = round($distance/1000, 1);
			$nearby_affiliates[$distance] = array(
											'id'=>$affiliate[2],
											'name'=>$affiliate[3], 
											'address'=>$affiliate[3] . '<br />' . $affiliate[4] . '<br />' . $affiliate[5] . '<br />' .$affiliate[6] . '<br />' . $affiliate[7],
											'kids'=>$affiliate[8],
											'telephone'=>$affiliate[9],
											'website'=>$affiliate[10],
											'latitude'=>$affiliate[0],
											'longitude'=>$affiliate[1],
											'distance'=>$distance);
		}

		ksort($nearby_affiliates); // Sort array by distance (clostest first)

		$map_url = drawMap( array($latitudeFrom, $longitudeFrom, array_values($nearby_affiliates)[0]['latitude'], array_values($nearby_affiliates)[0]['longitude'], array_values($nearby_affiliates)[1]['latitude'], array_values($nearby_affiliates)[1]['longitude'], array_values($nearby_affiliates)[2]['latitude'], array_values($nearby_affiliates)[2]['longitude'],array_values($nearby_affiliates)[3]['latitude'], array_values($nearby_affiliates)[3]['longitude'],array_values($nearby_affiliates)[4]['latitude'], array_values($nearby_affiliates)[4]['longitude'] ) );

		// DEBUG
		for ($i=0; $i < 10; $i++) { 
			error_log('> Affiliate: ' . array_values($nearby_affiliates)[$i]['name'] . ' distance = ' .  array_values($nearby_affiliates)[$i]['distance']);
		}

		$json = json_encode( array('map_url'=>$map_url, 'nearby_affiliates'=>array_slice($nearby_affiliates, 0, 5)) );
		echo $json;
	}

	if ( isset($_POST['favourites']) ) {
		
		$favourite_ids = explode('|', $_POST['favourites']);
		$favourite_ids = '"' . implode('", "', $favourite_ids) . '"';

		error_log(' >> ' . $favourite_ids);

		$affiliates = $db->query('SELECT latitude, longitude, affiliate_id, name, address, city, county, postcode, kids, telephone, website FROM affiliates WHERE affiliate_id IN (' . $favourite_ids . ') ORDER BY name, country');

		$favourite_affiliates = array();
		$counter = 0;
		while ( $affiliate = $affiliates->fetchArray() ) {
			$counter = $counter + 1;
			$favourite_affiliates[$counter] = array(
											'id'=>$affiliate[2],
											'name'=>$affiliate[3], 
											'address'=>$affiliate[3] . '<br />' . $affiliate[4] . '<br />' . $affiliate[5] . '<br />' .$affiliate[6] . '<br />' . $affiliate[7],
											'kids'=>$affiliate[8],
											'telephone'=>$affiliate[9],
											'website'=>$affiliate[10],
											'latitude'=>$affiliate[0],
											'longitude'=>$affiliate[1],
											'distance'=>'');
		}

		$json = json_encode( array('map_url'=>'', 'nearby_affiliates'=>array_slice($favourite_affiliates, 0, 5)) );
		echo $json;
	}
	
?>