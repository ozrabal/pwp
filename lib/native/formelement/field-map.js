

window.onload = function() {
   
    var latlng = new google.maps.LatLng(52.4091, 16.9229);
    var map = new google.maps.Map(document.getElementById('map'), {
        center: latlng,
        zoom: 14,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var marker = new google.maps.Marker({
        position: latlng,
        map: map,
        title: 'Set lat/lon values for this property',
        draggable: true
    });
    google.maps.event.addListener(marker, 'dragend', function(a) {
        console.log(a);
	//var t =document.getElementById('map');
        //var div = document.createElement('div');
        //div.innerHTML = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4);
        //document.getElementById('map')[0].appendChild(div);

	document.getElementById('latlong').value = a.latLng.lat().toFixed(4) + ', ' + a.latLng.lng().toFixed(4) + ', '+map.getZoom()+','+map.getMapTypeId();
    });
};
