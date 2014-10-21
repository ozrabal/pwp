jQuery( function( jQuery ) {
    google.maps.event.addDomListener( window, 'load', initialize );
});

function initialize() {
    var geocoder;
    var map = [];
    var markers = [];
    var default_zoom = 5;
    var default_lat = 52.4064;
    var default_lng = 16.9252;
    var default_type = 'roadmap';

    fields = jQuery( '.map-field' );
    fields.each( function() {
	//get values from input field
	latlong = jQuery( this ).find( 'input.geodata' ).val();
	var field = jQuery( this ).find( 'input.geodata' );
	if( latlong ) {
	    l = latlong.split( "," );
	    lat = parseFloat( l[0] );
	    long = parseFloat( l[1] );
	    z = parseInt( l[2] ) || default_zoom;
	} else {
	    lat = default_lat;
	    long = default_lng;
	    z = default_zoom;
	}
	var latlng  = new google.maps.LatLng( lat, long );
	container = 'map_' + jQuery( this ).attr( 'id' );
	//map initialization
	map[container] = init_map( z, latlng, container );
	//new marker
	marker = place_marker( latlng, map[container] );
	//remove old markers
	markers[container] = [];
	//place new marker
	markers[container].push( marker );
	set_value( latlng, map[container], field );
	//drag marker - update location field
	google.maps.event.addListener( marker, 'dragend', function( a ) {
	    set_value( a.latLng, map[container] , field) ;
	});
    });
    //geocode
    jQuery( '.code-address' ).click( function( e ){
	e.preventDefault();
	codeAddress(this, map, markers);
    });
}

//geocoder ask google api
function codeAddress( t, map, markers ) {
    //init
    geocoder = new google.maps.Geocoder();
    var field = jQuery( t ).parent().attr( 'id' );
    var address = document.getElementById( 'geocode_' + field ).value;
    map = map['map_' + field];
    //geocode
    geocoder.geocode( { 'address' : address }, function( results, status ) {
	if( status === google.maps.GeocoderStatus.OK ) {
	    //set map & place marker
	    map.setCenter( results[0].geometry.location );
	    marker = new google.maps.Marker({
		map: map,
		position: results[0].geometry.location,
		draggable: true
	    });
	    f = field.split( "_" );
	    set_value( marker.getPosition(), map , jQuery( '#' + f[1] ) );
	    markers['map_' + field][0].setMap( null );
	    markers['map_' + field] = [];
	    markers['map_' + field].push( marker );
            google.maps.event.addListener( marker, 'dragend', function( a ) {
		f = field.split( "_" );
                set_value( a.latLng, map, jQuery( '#' + f[1] ) );
            });
        } else {
            alert( geocode_notfound );
        }
    });
}

//initialize map in container
function init_map( z, latlng, container ) {
    var mapOptions = {
        zoom: z,
        center: latlng
    };
    map = new google.maps.Map( document.getElementById( container ), mapOptions );
    return map;
}
//places marker on latlng
function place_marker( latlng, map ) {
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Set lat/lon values for this property',
        draggable: true
    });
    return marker;
}
//set value from map to input field on event (drop, place marker,initialization)
function set_value( geo_object, map, field ) {
    jQuery( field ).val( geo_object.lat().toFixed(4) + ',' + geo_object.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId());
}