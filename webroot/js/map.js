/**
	Hello Hackers!

	@see https://sites.google.com/site/gmapsdevelopment/
*/

var OPM = null; // OnPageMap
var OMS = null;
var init_map_done = false;


/**
*/
function _map_license_search(arg, cbfn)
{
	if (undefined === arg) {
		arg = {};
	}

	arg.a = 'map-list';
	arg.stat = 200;
	arg.type = $('#map-search-type').val();
	arg.rect = OPM.getBounds().toJSON();

	$.get('https://directory.openthc.com/api/search', arg)
		.done(function(ret) {
			marker_delete();
			ret.forEach(function(n, i) {
				var mk = marker_create(n);
				mk.setMap(OPM);
			});
		})
		.always(cbfn);

}



/**
  @param p0 = array key of the Home point
  @param pN = array of points
  https://freegeographytools.com/2007/drawing-lines-between-points-in-google-maps-straight-and-great-circle
*/
function draw_line(p0, p1)
{
	console.log('draw_line()');


    // Connection Lines
    //var a = pN[ p0 ].getPosition();
    //if (!a) {
    //    return(0);
    //}

    //var c = pN.length;
    //var i = 0;
    //for (i=0;i<c;i++) {
    //    if (i == p0) {
    //        continue;
    //    }

    // https://developers.google.com/maps/documentation/javascript/reference?hl=en#PolylineOptions
        var o = {
            clickable: false,
            geodesic: true,
            map: OPM,
            strokeColor:'#00cc00',
            strokeOpacity: 0.9,
            strokeWeight: 2
        };

        // var b = pN[i].getPosition();
        // if (b) {
            o.path = [p0, p1];
            var l = new google.maps.Polyline(o);
        //}
    //}
    Map_Line_List.push(l);

    return l;
}



/**
       Draw Linkage Between Stores
*/
function license_connection_star()
{
       //$('#google-map').on('click', '.draw-link', function(e) {
       //	var url = '/ajax/map-connections?v=' + $(this).data('lic');
       //	var p0 = Inf.getPosition();
       //	var M = Inf.anchor;
       //	marker_delete();
       //	M.setMap(OPM);

       //	$.get(url, function(res) {

       //		res.forEach(function(x, i) {
       //			var p1 = new google.maps.LatLng(x.lat, x.lon);
       //                      var l = draw_line(p0, p1);
       //                      switch (x.dir) {
       //                      case 'I':
       //                              l.setOptions({ strokeColor: '#dd0000' });
       //                              break;
       //                      case 'O':
       //                              l.setOptions({ strokeColor: '#0000dd' });
       //                              break;
       //                      }
       //                      marker_create({
       //                              name: x.name,
       //                              type: x.type,
       //                              license_guid: x.license_guid,
       //			});
       //		});
       //	});
}

