/**
*/

var Geo = new google.maps.Geocoder();
var PointG = {};

/**
	https://developer.mozilla.org/en-US/docs/Web/API/Geolocation/Using_geolocation
*/
function askGeolocation(M)
{
	try {
		if (!navigator.geolocation) {
			return(0);
		}
	} catch (ex) {
		return(0);
	}

	navigator.geolocation.getCurrentPosition(function(position) {

		PointG = new google.maps.LatLng(position.coords.latitude, position.coords.longitude)
		M.setCenter(PointG);

		// Blue Pin
		var mk = new google.maps.Marker({
			map: M,
			position: PointG,
			draggable: false,
			dragCrossMove: false,
			icon: {
				url: 'http://maps.google.com/mapfiles/kml/paddle/blu-stars.png',
			}
		});
	});

}
