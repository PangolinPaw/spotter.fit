<html><head>
	<title>Spotter | Favourites</title>
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
		<div id="map">
		</div>
		<div id="results">
			
		</div><!-- /resuts -->
	</div> <!-- /container -->
	<?php include 'includes/location_search.php'; ?>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function() {
			getFavourites();
		});
	</script>
	<?php include 'includes/footer.php'; ?>
</body></html>