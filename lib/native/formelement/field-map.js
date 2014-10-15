jQuery(function(jQuery) {

var geocoder;
var map;
var markers = [];
var default_zoom = 5;
var default_lat = 52.4064;
var default_lng = 16.9252;
var default_type = 'roadmap';



google.maps.event.addDomListener(window, 'load', initialize);
});




function initialize() {

var geocoder;
var map =[];
var markers = [];
var default_zoom = 5;
var default_lat = 52.4064;
var default_lng = 16.9252;
var default_type = 'roadmap';

fields = jQuery('.map-field');
    //console.log(fields);

//for(i = 0; i < fields.length; i++){
 //   field = fields[i];




//console.log(field.id);
    
fields.each(function(){


//console.log(this);
latlong = jQuery(this).find('input.geodata').val();

var field = jQuery(this).find('input.geodata');
//console.log(latlong);
    




    //var latlong = document.getElementById('latlong').value;

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

    //console.log(latlng);
    //console.log(jQuery(this).attr('id'));
    container = 'map_' + jQuery(this).attr('id');
//console.log(container);



    map[container] = init_map(z, latlng, container);

    marker = place_marker(latlng, map[container]);
markers[container] = [];
    markers[container].push(marker);

    set_value( latlng, map[container], field );
    google.maps.event.addListener(marker, 'dragend', function(a) {
        set_value( a.latLng, map[container] , field);
    });





});




jQuery('.code-address').click(function(e){



    e.preventDefault();
//console.log(map);
  
    codeAddress(this, map, markers);
})

   //}
}






function codeAddress(t, map, markers) {

console.info(codeAddress);
geocoder = new google.maps.Geocoder();

var field = jQuery(t).parent().attr('id');

//console.log(fiel)

    var address = document.getElementById('geocode_' + field).value;


map = map['map_' + field];
//console.log(address);
console.info(field);

//map('map_' + field);

    geocoder.geocode( { 'address': address}, function(results, status) {
	if (status === google.maps.GeocoderStatus.OK) {
	    map.setCenter(results[0].geometry.location);
	    marker = new google.maps.Marker({
		map: map,
		position: results[0].geometry.location,
		draggable: true
	    });

f =field.split("_");

	    set_value( marker.getPosition(), map , jQuery('#'+f[1]));
	    /*
	    for (var i = 0; i < markers.length; i++) {
		markers['map_' + field][i].setMap(null);
	    }
	    */

	   console.log(markers['map_' + field]);

	    markers['map_' + field][0].setMap(null);
	    markers['map_' + field] = [];
	    markers['map_' + field].push(marker);
            google.maps.event.addListener(marker, 'dragend', function(a) {
		console.log(a.latLng);
		console.info(field);
f =field.split("_");
                set_value( a.latLng, map, jQuery('#'+f[1]) );
            });
        } else {
            alert( geocode_notfound );
        }
    });
}

function init_map(z, latlng, container){
    var mapOptions = {
        zoom: z,
        center: latlng
    };
    map = new google.maps.Map(document.getElementById(container), mapOptions);
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

function set_value( geo_object, map, field ){
    console.info(set_value);
    console.log(field);
console.log(geo_object.lat().toFixed(4));
console.log(map.getZoom());
    jQuery(field).val(geo_object.lat().toFixed(4) + ',' + geo_object.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId());
    //document.getElementById( 'latlong' ).value = geo_object.lat().toFixed(4) + ',' + geo_object.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId();


}