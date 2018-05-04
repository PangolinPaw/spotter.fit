<html><head>
	<title>Spotter</title>
	<?php include 'includes/head.php'; ?>
</head>
<body>
	<div class="nav accent">
		<a href="/">
			<div class="logo">
				Spotter
			</div>
		</a>
		<div class="search">
			<div id="currentLocation">
				<span class="white_font button" title="Use my current location"><i class="icon icon-crosshair"></i></span>
			</div>
			<div id="findAddress">
				<span class="white_font button" title="Search by address"><i class="icon icon-zoom"></i></span>
			</div>
			<div>
				<input type="text" name="search_location" id="search_location" placeholder="Search by address">
			</div>
		</div>
	</div><!-- /nav -->
	<div class="container">

		<div id="map"><img src="/media/map_placeholder.png"></div><!-- /map -->

		<div id="results">
			<div class="placeholder_message">
				<p>To list nearby affiliates, either <i class="icon icon-zoom" onclick="document.getElementById('search_location').focus();"></i> search by address or use your <i class="icon icon-crosshair" onclick="document.getElementById('currentLocation').click();"></i> current location.</p>
				<p>Alternatively, view your <a href="favourites.php"><i class="icon icon-heart"></i></a> favourtites or <a href="add.php"><i class="icon icon-add"></i></a> add an affiliate you think we've missed.</p>
			</div>

		</div><!-- /resuts -->

	</div> <!-- /container -->

	<?php include 'includes/location_search.php'; ?>
	<?php include 'includes/footer.php'; ?>
</body></html>