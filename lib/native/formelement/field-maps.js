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
	var latlng;
var z = parseInt(l[2]) || default_zoom;


var lat = parseFloat(l[0]);
var long = parseFloat(l[1]);



latlng  = new google.maps.LatLng(lat, long);

map = init_map(z, latlng);

//	var mapOptions = {
//    zoom: z,
//    center: latlng
//  };
//
//  map = new google.maps.Map(document.getElementById('map'), mapOptions);





    }else{



//  var latlng;
//var lat;
//var long;

//  function success(position) {
//
//     lat = position.coords.latitude;
//     long = position.coords.longitude;
//
//
////latlng  = new google.maps.LatLng(lat, long);
//
//}


navigator.geolocation.getCurrentPosition(function(position){
     lat = position.coords.latitude;
     long = position.coords.longitude;


//latlng  = new google.maps.LatLng(lat, long);

var latlng = new google.maps.LatLng(parseFloat(lat), parseFloat(long));


//  var mapOptions = {
//    zoom: 5,
//    center: latlng
//  }
//  map = new google.maps.Map(document.getElementById('map'), mapOptions);

map = init_map(default_zoom, latlng);

});

console.log(latlng);


     console.log(lat);
console.log(long);
//var latlng = new google.maps.LatLng(lat, long);
  //console.log(latlng);

  }

 
  
  var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Set lat/lon values for this property',
        draggable: true
    });

markers.push(marker);


  google.maps.event.addListener(marker, 'dragend', function(a) {
        if(a){
	   console.log(marker.getPosition());
       // alert(a)

	//var t =document.getElementById('map');
        //var div = document.createElement('div');
        //div.innerHTML = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);
        //document.getElementById('map')[0].appendChild(div);
//getlatlng(a);

set_value( a.latLng, map );
	//document.getElementById('latlong').value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4) + ', '+map.getZoom()+','+map.getMapTypeId();
	}
	   });


}



var marker;

function codeAddress() {
  var address = document.getElementById('pac-input').value;

  geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      map.setCenter(results[0].geometry.location);
      
	  

      marker = new google.maps.Marker({
          map: map,
          position: results[0].geometry.location,
	  draggable: true
      });
set_value( marker.getPosition(), map );
//document.getElementById('latlong').value = marker.getPosition().lat().toFixed(4) + ', ' + marker.getPosition().lng().toFixed(4) + ', '+map.getZoom()+','+map.getMapTypeId();



//delete markers
for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
	markers = [];
  
markers.push(marker);
console.log(markers);

 google.maps.event.addListener(marker, 'dragend', function(a) {
        if(a){
	   console.log(marker.getPosition());
        //alert(a)

	//var t =document.getElementById('map');
        //var div = document.createElement('div');
        //div.innerHTML = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);
        //document.getElementById('map')[0].appendChild(div);

	//document.getElementById('latlong').value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4) + ', '+map.getZoom()+','+map.getMapTypeId();
//getlatlng(a);

set_value( a.latLng, map );

     }
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


function set_value( geo_object, map ){
    document.getElementById( 'latlong' ).value = geo_object.lat().toFixed(4) + ',' + geo_object.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId();
}

function getlatlng(a){
    document.getElementById( 'latlong' ).value = a.latLng.lat().toFixed(4) + ',' + a.latLng.lng().toFixed(4) + ',' + map.getZoom() + ',' + map.getMapTypeId();

}

google.maps.event.addDomListener(window, 'load', initialize);