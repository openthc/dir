<?php
/**
 *
 */

?>

<div class="row mt-4">

<div class="col">
<div class="form-group">
	<input class="form-control" id="map-search-term" name="q" placeholder="Search" type="text" value="<?= h($_GET['q']) ?>">
</div>
</div>

<div class="col">
<div class="form-group">
	<select class="form-control" id="map-search-type" name="type">
		<option selected value="">- All License Types -</option>
		<?php
		foreach ($data['license_type_list'] as $l) {
		?>
			<option
				<?= $l['type'] == $data['license_type_pick'] ? ' selected' : null ?>
				value="<?= h($l['type']) ?>"><?= h($l['type']) ?></option>
		<?php
		}
		?>
	</select>
</div>
</div>

<div class="col">
<div class="form-group">
	<button class="btn btn-outline-primary" id="map-search">Search</button>
	<button class="btn btn-outline-danger map-mark-wipe"><i class="fas fa-ban"></i> Clear</button>
	<button class="btn btn-outline-secondary" id="map-re-center"><i class="fas fa-crosshairs"></i><!-- fa-dot-circle-o --></button>
	<a class="btn btn-outline-secondary" href="/map" id="map-link"><i class="fas fa-link"></i></a>
</div>
</div>
</div>


<div style="border:2px solid #333; position: relative;">
	<div id="google-map" style="width:100%; height:750px;"></div>
	<div id="bounding-box-display-nw" style="background: #ffffff99; padding: 0.125em; position: absolute; top:0; left: 0;">NW</div>
	<div id="bounding-box-display-se" style="background: #ffffff99; padding: 0.125em; position: absolute; bottom:0; right: 0;">SE</div>
</div>

<div>
	<div id="bounding-box-display" style="text-align:center;"></div>
</div>

<!-- <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= \OpenTHC\Config::get('google/map_api_key_js') ?>&amp;libraries=places"></script>
<script src="/js/map.js"></script>
<script src="/js/map-geo.js"></script>
<script src="/js/map-marker.js"></script>
<script>
$(function() {

	var div = document.getElementById('google-map');
	var opt = {
		// draggable: false,
        keyboardShortcuts: false,
        // mapTypeControl: false,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
        navigationControl: false,
        // overviewMapControl: false,
        // panControl: false,
        rotateControl: false,
        // scaleControl: false,
        // scrollwheel: false,
        streetViewControl: false,
        styles: [
			{
				featureType: "poi",
				elementType: "labels",
				stylers: [
					{
						visibility: "off"
					}
				]
			}
		],
		zoom: 8,
		zoomControlOptions:{
			style: google.maps.ZoomControlStyle.SMALL
		}
	};

	var cpt = new google.maps.LatLng(<?= floatval($data['center']['lat']) ?>, <?= floatval($data['center']['lon']) ?>);
	var x = localStorage.getItem('cpt');
	if (x) {
		cpt = new google.maps.LatLng(JSON.parse(x));
	}

    x = localStorage.getItem('cpz');
    if (x) {
    	opt.zoom = parseInt(x, 10) || opt.zoom;
    }

	OPM = new google.maps.Map(div, opt);
	OPM.setCenter(cpt);

	OPM.addListener('bounds_changed', function() {

		var rect = OPM.getBounds();
		var ne = rect.getNorthEast();
		var sw = rect.getSouthWest();

		var n = parseFloat(ne.lat(), 10);
		var w = parseFloat(sw.lng(), 10);
		var s = parseFloat(sw.lat(), 10);
		var e = parseFloat(ne.lng(), 10);

		$('#bounding-box-display-nw').html( n.toFixed(6) + ',' + w.toFixed(6));
		$('#bounding-box-display-se').html( s.toFixed(6) + ',' + e.toFixed(6));

		// var text = [];
		// text.push('N:' + n);
		// text.push('S:' + s);
		// text.push('W:' + w);
		// text.push('E:' + e);
		// $('#bounding-box-display').html(  text.join(' ') );

		localStorage.setItem('cpt', JSON.stringify(OPM.getCenter().toJSON()) );
		localStorage.setItem('cpz', JSON.stringify(OPM.getZoom()) );

	});

	OPM.addListener('dragend', function() {
		_map_license_search();
	});

	OPM.addListener('idle', function() {

		if (!init_map_done) {

			var arg = {
				q: $('#map-search-term').val(),
				type: $('#map-search-type').val(),
			};
			_map_license_search(arg);
			init_map_done = true;
		}

		var args = {
			q: $('#map-search-term').val(),
			t: $('#map-search-type').val(),
			c: OPM.getCenter().toUrlValue(),
			z: OPM.getZoom(),
		};
		var link = '/map?' + Object.keys(args).map(function(k) { return encodeURIComponent(k) + '=' + encodeURIComponent(args[k]); }).join('&');
		$('#map-link').attr('href', link);

	});

	$('.map-mark-wipe').on('click', function() {
		marker_delete();
	});

	$('#map-search').on('click', function() {

		$b = $(this);
		$b.attr('disabled', 'disabled');
		$b.html('Search <i class="fas fa-sync fa-spin"></i>');

		// Reload
		var arg = {
			q: $('#map-search-term').val(),
			type: $('#map-search-type').val(),
		};

		_map_license_search(arg, function() {
			$b.removeAttr('disabled');
			$b.html('Search');
		});

	});

	$('#map-re-center').on('click', function() {
		askGeolocation(OPM);
	});

	var opts = {
		types: ['(cities)'],
		componentRestrictions: {
			country: "us"
		}
	};
	var autocomplete = new google.maps.places.Autocomplete( document.getElementById('map-search-term'), opts );
	autocomplete.bindTo('bounds', OPM);
	autocomplete.addListener('place_changed', function(e) {

		//infowindow.close();
		//marker.setVisible(false);
		var place = autocomplete.getPlace();

		if (!place.geometry) {
			// User entered the name of a Place that was not suggested and
			// pressed the Enter key, or the Place Details request failed.
			//window.alert("No details available for input: '" + place.name + "'");
			return;
		}


		// If the place has a geometry, then present it on a map.
		if (place.geometry.viewport) {
			OPM.fitBounds(place.geometry.viewport);
		} else {
			OPM.setCenter(place.geometry.location);
			//OPM.setZoom(17);  // Why 17? Because it looks good.
		}

		//marker.setPosition(place.geometry.location);
		//marker.setVisible(true);
        //
		//var address = '';
		//if (place.address_components) {
		//address = [
		//  (place.address_components[0] && place.address_components[0].short_name || ''),
		//  (place.address_components[1] && place.address_components[1].short_name || ''),
		//  (place.address_components[2] && place.address_components[2].short_name || '')
		//].join(' ');
		//}
        //
		//infowindowContent.children['place-icon'].src = place.icon;
		//infowindowContent.children['place-name'].textContent = place.name;
		//infowindowContent.children['place-address'].textContent = address;
		//infowindow.open(map, marker);

		$('#map-search-term').val('');

	});
});
</script>
