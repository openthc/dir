/**

*/

var Inf = new google.maps.InfoWindow({
	content: '<h2>Marker</h2>'
});

var Map_Drop_List = {};
var Map_Mark_List = [];
var Map_Line_List = [];

function marker_create(mark)
{
	console.log('marker_create');

	var ol = 0;
	var ot = 0;

	var pt = new google.maps.LatLng(mark.geo_lat, mark.geo_lon);

	var mk = new google.maps.Marker({
		//animation: google.maps.Animation.BOUNCE,
		//animation: google.maps.Animation.DROP,
		draggable:false,
		dragCrossMove:false,
		label: mark.license_type.substring(0, 1),
		icon: {
			url: mark.marker.mark,
			labelOrigin: new google.maps.Point(16, 10)
			//fillColor: mark.marker.color,
			//fillOpacity: 1,
			//path: 'M22-48h-44v43h16l6 5 6-5h16z', // google.maps.SymbolPath.CIRCLE,
			//strokeColor: mark.marker.color,
		},
		position: pt,
		_otd: mark,
	});

	mk.addListener('click', function(e) {
		marker_window(this);
	});

	Map_Mark_List.push(mk);

	return mk;
}

function marker_window(mark)
{
	console.log('marker_window');

	var html = '';
	html += '<div style="border-left: 4px solid ' + mark._otd.marker.color + '; padding-left: 1em;">';
	html += '<h2>';
	html += '<a href="//directory.openthc.com/company?id=' + mark._otd.id + '">';
	html += mark._otd.name;
	html += '</a>';
	html += '</h2>';
	html += '<h3>Company: #' + mark._otd.company_guid + '</h3>';
	html += '<h3>License: #' + mark._otd.license_code + '</h3>';
	html += '</div>';

	Inf.open(OPM, mark);
	Inf.setPosition( mark.getPosition() );
	Inf.setContent(html);

}

function marker_delete()
{
	console.log('marker_delete()');

	Map_Drop_List = {};

	Map_Mark_List.forEach(function(x, i) {
		x.setMap(null);
	});
	Map_Mark_List = [];

	Map_Line_List.forEach(function(x, i) {
		x.setMap(null);
	});
	Map_Line_List = [];

	if (window.OMS) {
		// Clear Spider
		window.OMS.clearMarkers();
	}

}
