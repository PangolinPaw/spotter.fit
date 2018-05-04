<script>
		function addFavourite(affiliate_id) {
			if (typeof(Storage) !== "undefined") {
				var favourite_ids = localStorage.getItem("favourite_ids");

				if ( favourite_ids !== null ) {
					if (!favourite_ids.includes(affiliate_id)) {
						localStorage.setItem("favourite_ids", favourite_ids + '|' + affiliate_id);
					}
				} else {
					localStorage.setItem("favourite_ids", affiliate_id);
				}
				
			} else {
				alert('Sorry, we couldn\'t save this affiliate to your favourites because your browser doesn\'t support local storage.');
			}
		}

		function getFavourites() {
			if (typeof(Storage) !== "undefined") {
				var favourite_ids = localStorage.getItem("favourite_ids");

				// AJAX
				var newXHR = new XMLHttpRequest();
				newXHR.open( 'POST', 'locate.php' );
				newXHR.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
				var formData = 'favourites=' + favourite_ids;
				newXHR.send( formData );

				newXHR.onreadystatechange = function () {
					results_table = '<div>';
					if ( newXHR.readyState == XMLHttpRequest.DONE ) {
                       // Receive JSON-format results from server
						var json = JSON.parse(newXHR.response);
						
						results_table = '';
						for (var i = 0; i < json['nearby_affiliates'].length; i++) {

							var state = json['nearby_affiliates'][i]['address'].split('<br />');
							state = state[state.length - 2];
							
							results_table = results_table + '<div class="favourite">' +
											'<div class="top accent">' +
												'<div class="image">' +
													'<img src="media/affiliate_placeholder.png">' +
												'</div>' +
												'<div class="name">' +
													'<h2>' + json['nearby_affiliates'][i]['name'] + '</h2>' +
													'<table class="contact_table">' +
														'<tr>' +
															'<td class="icon"><i class="icon icon-location"></i></td>' +
															'<td>' + state + '</td>' +
														'</tr>' +
													'</table>' +
												'</div>'+
												'<div class="expand accent_light" id="expand_' + json['nearby_affiliates'][i]['id'] + '" onclick="document.getElementById(\'fav_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'block\';document.getElementById(\'expand_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'none\';document.getElementById(\'contract_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'block\';">'+
'						<i class="icon icon-down"></i>'+
'					</div>'+
'					<div class="expand accent_light" id="contract_' + json['nearby_affiliates'][i]['id'] + '" style="display: none;" onclick="document.getElementById(\'fav_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'none\';document.getElementById(\'expand_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'block\';document.getElementById(\'contract_' + json['nearby_affiliates'][i]['id'] + '\').style.display = \'none\';">'+
'						<i class="icon icon-up"></i>'+
'					</div>'+
'				</div>'+
'				<div class="bottom grey" id="fav_' + json['nearby_affiliates'][i]['id'] + '" style="display:none">'+
'					<div class="content">'+
'						<p></p>'+
'						<table class="contact_table">'+
'							<tbody>'+
'								<tr>'+
'									<td class="icon"><i class="icon icon-globe"></i></td>'+
'									<td>'+
'										<a href="' + json['nearby_affiliates'][i]['website'] + '">' + json['nearby_affiliates'][i]['website'] + '</a>'+
'									</td>'+
'								</tr>'+
'								<tr>'+
'									<td class="icon"><i class="icon icon-location"></i></td>'+
'									<td>' + json['nearby_affiliates'][i]['address'] + '</td>'+
'								</tr>'+
'								<tr>'+
'									<td class="icon"><i class="icon icon-phone"></i></td>'+
'									<td>'+
'										<a href="tel:' + json['nearby_affiliates'][i]['telephone'] + '">' + json['nearby_affiliates'][i]['telephone'] + '</a>'+
'									</td>'+
'								</tr>'+
'							</tbody>'+
'						</table>'+
'					</div>'+
'				</div>'+
'			</div>';
	
;

						}
						
					}
					document.getElementById('results').innerHTML = results_table;
				}

			} else {
				alert('Sorry, we couldn\'t fetch your favourites because your browser doesn\'t support local storage.');
			}
		}

		function toggle_tab(letter) {
			var tab = document.getElementById('tab_' + letter);
			var affiliate = document.getElementById('affiliate_' + letter);

			var letters = ['a', 'b', 'c', 'd', 'e'];

			if ( affiliate.style.display == 'none' ) {
				for (var i = 0; i < letters.length; i++) {
					
					document.getElementById('affiliate_' + letters[i]).style.display = 'none';
					document.getElementById('tab_' + letters[i]).className = 'accent_light';
				}
				affiliate.style.display = 'block';
				tab.className = 'accent';
			}
		}

       (function() {
           var httpRequest;
           document.getElementById('currentLocation').addEventListener('click', geoFindMe);
           document.getElementById('findAddress').addEventListener('click', findAddress);

           	// Get user's location
            function geoFindMe() {
               if (!navigator.geolocation){
                   document.getElementById("map").innerHTML = "<p>Sorry, geolocation is not supported by your browser. Please try searching by address.</p>";
                   return;
               }

               function success(position) {
                   var latitude  = position.coords.latitude;
                   var longitude = position.coords.longitude;

                   document.getElementById("search_location").value = latitude + '°, ' + longitude + '°';

                   // AJAX
                   var newXHR = new XMLHttpRequest();
                   newXHR.open( 'POST', 'locate.php' );
                   newXHR.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
                   var formData = 'location=' + latitude + ',' + longitude;
                   newXHR.send( formData );

                   newXHR.onreadystatechange = function () {
                       if ( newXHR.readyState == XMLHttpRequest.DONE ) {
                           // Receive JSON-format results from server
                           var json = JSON.parse(newXHR.response);

                           document.getElementById('map').innerHTML = '<img src="' + json['map_url'] + '">';

                           results_table = '<div class="tabs">'+
'				<div class="accent" id="tab_a" onclick="toggle_tab(\'a\')">A</div>'+
'				<div class="accent_light" id="tab_b" onclick="toggle_tab(\'b\')">B</div>'+
'				<div class="accent_light" id="tab_c" onclick="toggle_tab(\'c\')">C</div>'+
'				<div class="accent_light" id="tab_d" onclick="toggle_tab(\'d\')">D</div>'+
'				<div class="accent_light" id="tab_e" onclick="toggle_tab(\'e\')">E</div>'+
'			</div>';
                           var markers = ['a', 'b', 'c', 'd', 'e'];
                           for (var i = 0; i < json['nearby_affiliates'].length; i++) {
                           		if ( markers[i] == 'a' ) {
                           			var display = 'block';
                           		} else {
                           			var display = 'none';
                           		}
                            	affiliate = '<div class="page" id="affiliate_' + markers[i] + '" style="display:' + display + '">'+
'				<div class="top accent">'+
'					<div class="image">'+
'						<!--<img src="http://via.placeholder.com/150x150?text=IMAGE">--><span>' + json['nearby_affiliates'][i]['distance'] + 'km</span>' +
'					</div>'+
'					<div class="name">'+
'						<h2>' + json['nearby_affiliates'][i]['name'] + '</h2>'+
'						<table class="contact_table">'+
'							<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-phone"></i>'+
'								</td>'+
'								<td>'+
'									<a href="tel:' + json['nearby_affiliates'][i]['telephone'] + '">' + json['nearby_affiliates'][i]['telephone'] + '</a>'+
'								</td>'+
'							</tr>'+
'						</table>'+
'					</div>'+
'					<div class="expand accent_light" title="Add to my favourites" onclick="addFavourite(\'' + json['nearby_affiliates'][i]['id'] + '\'); this.style.color=\'#B40C02\';">'+
'						<i class="icon icon-heart"></i>'+
'					</div>'+
'				</div>'+
'				<div class="bottom grey">'+
'					<div class="content">'+
'						<p><!-- description --><p>'+
'						<table class="contact_table">'+
'							<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-globe"></i>'+
'								</td>'+
'								<td>'+
'									<a href="' + json['nearby_affiliates'][i]['website'] + '">' + json['nearby_affiliates'][i]['website'] + '</a>'+
'								</td>'+
'							</tr>'+
'							<!--<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-mail"></i>'+
'								</td>'+
'								<td>'+
'									<a href="mailto:"></a>'+
'								</td>'+
'							</tr>'+
'							<tr>-->'+
'								<td class="icon">'+
'									<i class="icon icon-location"></i>'+
'								</td>'+
'								<td>' + json['nearby_affiliates'][i]['address'] + '</td>'+
'							</tr>'+
'						</table>'+
'						<div class="show_more"><a href="#"><i class="icon icon-down"></i> Show more details</a></div>'+
'						<hr>'+
'						<table class="contact_table">'+
'							<tr class="overall">'+
'								<th>'+
'									Overall'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Equipment'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Classes'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star accent_dark-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Atmosphere'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'						</table>'+
'						<div class="show_more"><a href="#"><i class="icon icon-down"></i> Show reviews</a></div>'+
'					</div>'+
'				</div>'+
'			</div>';
                               results_table = results_table + affiliate;
                           }

                           results_table = results_table + '</div>';
                           document.getElementById('results').innerHTML = results_table;
                       }
                   }
               }

               function error() {
                   document.getElementById('map').innerHTML = "<p>Unable to retrieve your location.</p>";
               }

               document.getElementById('map').innerHTML = "<p>Locating…</p>";

               navigator.geolocation.getCurrentPosition(success, error);
            }

            // Check searched location
            function findAddress() {
				address = document.getElementById("search_location").value;

				document.getElementById('map').innerHTML = "<p>Searching…</p>";

				// AJAX
				var newXHR = new XMLHttpRequest();
				newXHR.open( 'POST', 'locate.php' );
				newXHR.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
				var formData = 'address=' + address;
				newXHR.send( formData );

				newXHR.onreadystatechange = function () {
                       if ( newXHR.readyState == XMLHttpRequest.DONE ) {
                           // Receive JSON-format results from server
                           var json = JSON.parse(newXHR.response);

                           document.getElementById('map').innerHTML = '<img src="' + json['map_url'] + '">';

                           results_table = '<div class="tabs">'+
'				<div class="accent" id="tab_a" onclick="toggle_tab(\'a\')">A</div>'+
'				<div class="accent_light" id="tab_b" onclick="toggle_tab(\'b\')">B</div>'+
'				<div class="accent_light" id="tab_c" onclick="toggle_tab(\'c\')">C</div>'+
'				<div class="accent_light" id="tab_d" onclick="toggle_tab(\'d\')">D</div>'+
'				<div class="accent_light" id="tab_e" onclick="toggle_tab(\'e\')">E</div>'+
'			</div>';
                           var markers = ['a', 'b', 'c', 'd', 'e'];
                           for (var i = 0; i < json['nearby_affiliates'].length; i++) {
                           		if ( markers[i] == 'a' ) {
                           			var display = 'block';
                           		} else {
                           			var display = 'none';
                           		}
                            	affiliate = '<div class="page" id="affiliate_' + markers[i] + '" style="display:' + display + '">'+
'				<div class="top accent">'+
'					<div class="image">'+
'						<!--<img src="http://via.placeholder.com/150x150?text=IMAGE">--><span>' + json['nearby_affiliates'][i]['distance'] + 'km</span>' +
'					</div>'+
'					<div class="name">'+
'						<h2>' + json['nearby_affiliates'][i]['name'] + '</h2>'+
'						<table class="contact_table">'+
'							<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-phone"></i>'+
'								</td>'+
'								<td>'+
'									<a href="tel:' + json['nearby_affiliates'][i]['telephone'] + '">' + json['nearby_affiliates'][i]['telephone'] + '</a>'+
'								</td>'+
'							</tr>'+
'						</table>'+
'					</div>'+
'					<div class="expand accent_light" title="Add to my favourites" onclick="addFavourite(\'' + json['nearby_affiliates'][i]['id'] + '\'); this.style.color=\'#B40C02\';">'+
'						<i class="icon icon-heart"></i>'+
'					</div>'+
'				</div>'+
'				<div class="bottom grey">'+
'					<div class="content">'+
'						<p><!-- description --><p>'+
'						<table class="contact_table">'+
'							<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-globe"></i>'+
'								</td>'+
'								<td>'+
'									<a href="' + json['nearby_affiliates'][i]['website'] + '">' + json['nearby_affiliates'][i]['website'] + '</a>'+
'								</td>'+
'							</tr>'+
'							<!--<tr>'+
'								<td class="icon">'+
'									<i class="icon icon-mail"></i>'+
'								</td>'+
'								<td>'+
'									<a href="mailto:"></a>'+
'								</td>'+
'							</tr>'+
'							<tr>-->'+
'								<td class="icon">'+
'									<i class="icon icon-location"></i>'+
'								</td>'+
'								<td>' + json['nearby_affiliates'][i]['address'] + '</td>'+
'							</tr>'+
'						</table>'+
'						<div class="show_more"><a href="#"><i class="icon icon-down"></i> Show more details</a></div>'+
'						<hr>'+
'						<table class="contact_table">'+
'							<tr class="overall">'+
'								<th>'+
'									Overall'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Equipment'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Classes'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star accent_dark-font"></i>'+
'								</td>'+
'							</tr>'+
'							<tr>'+
'								<th>'+
'									Atmosphere'+
'								</th>'+
'								<td>'+
'									<i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i><i class="icon icon-star accent_dark-font"></i>'+
'									</i><i class="icon icon-star pale-font"></i>'+
'								</td>'+
'							</tr>'+
'						</table>'+
'						<div class="show_more"><a href="#"><i class="icon icon-down"></i> Show reviews</a></div>'+
'					</div>'+
'				</div>'+
'			</div>';
                               results_table = results_table + affiliate;
                           }

                           results_table = results_table + '</div>';
                           document.getElementById('results').innerHTML = results_table;
                       }
                   }
                
            }
       })();

	</script>