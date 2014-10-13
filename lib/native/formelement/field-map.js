var geocoder;
var map;
var markers = [];
var default_zoom = 5;
var default_lat = 52.4064;
var default_lng = 16.9252;
var default_type = 'roadmap';

function initialize() {

    geocoder = new google.maps.Geocoder();
    var latlong = document.getElementById('latlong').value;
    if(latlong){
        l = latlong.split(",");
	lat = parseFloat(l[0]);
	long = parseFloat(l[1]);
        z = parseInt(l[2]) || default_zoom;
    }else{
        lat = default_lat;
        long = default_lng;
        z = default_zoom;
    }   
    var latlng  = new google.maps.LatLng(lat, long);
    map = init_map(z, latlng);
    marker = place_marker(latlng, map);
    markers.push(marker);
    set_value( latlng, map );
    google.maps.event.addListener(marker, 'dragend', function(a) {
        set_value( a.latLng, map );
    });
}

function codeAddress() {

    var address = document.getElementById('geocode').value;
    geocoder.geocode( { 'address': address}, function(results, status) {
	if (status === google.maps.GeocoderStatus.OK) {
	    map.setCenter(results[0].geometry.location);
	    marker = new google.maps.Marker({
		map: map,
		position: results[0].geometry.location,
		draggable: true
	    });
	    set_value( marker.getPosition(), map );
	    for (var i = 0; i < markers.length; i++) {
		markers[i].setMap(null);
	    }
	    markers = [];
	    markers.push(marker);
            google.maps.event.addListener(marker, 'dragend', function(a) {
                set_value( a.latLng, map );
            });
        } else {
            alert( geocode_notfound );
        }
    });
}

function init_map(z, latlng){
    var mapOptions = {
        zoom: z,
        center: latlng
    };
    map = new google.maps.Map(document.getElementById('map'), mapOptions);
    return map;
}

function place_marker(latlng, map){
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Set lat/lon values for this property',
        draggable: true
    });
    return marker;
}

function set_value( geo_object, map ){
    document.getElementById( 'latlong' ).value = geo_object.lat().toFixed(4) + ',' + geo_object.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId();
}

google.maps.event.addDomListener(window, 'load', initialize);