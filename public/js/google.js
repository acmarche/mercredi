//google.load("maps", "2");
var geocoder;
var map;

function initialize() {
    geocoder = new google.maps.Geocoder();

    longitude = document.getElementById('map_longitude').value;
    latitude = document.getElementById('map_latitude').value;

    if (latitude && longitude) {
        var latlng = new google.maps.LatLng(latitude, longitude);
        var mapOptions = {
            zoom: 16,
            center: latlng
        };
        console.log(latitude);
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        addMarker(map, latlng)
    }
    else {

        codeAddress(function (e) {
            latitude = e.latitude;
            longitude = e.longitude;

            var latlng = new google.maps.LatLng(latitude, longitude);
            console.log(latlng);

            var mapOptions = {
                zoom: 16,
                center: latlng
            };

            map = new google.maps.Map(document.getElementById('map'), mapOptions);
            addMarker(map, latlng);

        });
    }
}

function addMarker(map, location) {
    var address = document.getElementById('map_adresse').value;
    console.log(address);

    var marker = new google.maps.Marker({
        map: map,
        draggable: true,
        title: address,
        position: location
    });

    var infowindow = new google.maps.InfoWindow({
        content: address
    });

    infowindow.open(map, marker);

    /**
     * le curseur est pris en main, je remets a zero 
     * l et L
     */
    google.maps.event.addListener(marker, 'dragstart', function () {

        document.getElementById('map_longitude').value = '';
        document.getElementById('map_latitude').value = '';

    });

    /**
     * quand le curseur est lache, je reobtiens la 
     * longitude et latitude
     */
    google.maps.event.addListener(marker, "dragend", function () {
        var longitude = marker.getPosition().lng();
        var latitude = marker.getPosition().lat();
        document.getElementById('map_longitude').value = longitude;
        document.getElementById('map_latitude').value = latitude;
    });

}

function codeAddress(adresse) {

    var address = document.getElementById('map_adresse').value;
    console.log(address);

    if (!address)
        return null;

    geocoder.geocode({'address': address}, function (results, status) {

        if (status == google.maps.GeocoderStatus.OK) {

            adresse({
                "latitude": results[0].geometry.location.lat(),
                "longitude": results[0].geometry.location.lng()
            });

        } else {
            alert('L\'adresse n\'a pas pu être convertie en coordonnées : ' + status);
            adresse({
                "latitude": '50.22749767848267',
                "longitude": '5.342383982525689'
            });
        }

    });
}

google.maps.event.addDomListener(window, 'load', initialize);
