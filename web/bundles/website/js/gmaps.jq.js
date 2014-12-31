/**
 * Created by mortenthorpe on 11/11/13.
 */
/*var lat;
var long;*/
//var mainmap;
var profilemap;
var mainmarker;
var markersArray;

function clearAllMarkers(){
    if (markersArray) {
        for (i in markersArray) {
            markersArray[i].setMap(null);
        }
    }
}

function placeMarker(onMap, location) {
    clearAllMarkers();
    markersArray = new Array();
    var marker = new google.maps.Marker({
        position: location,
        map: onMap
    });
    markersArray.push(marker);
    onMap.setCenter(location);
    var infoWindow = new google.maps.InfoWindow();
        infoWindow.setContent("<div style=\"font-size:12px !important;\">Du befinder dig her!<br/>Listen over fundne steder her på siden viser dig de 20 nærmeste steder her på GladTur.<br/><br/></div>");
    infoWindow.open(onMap, marker);
    onMap.panTo(marker.getPosition());
}


function haversineGreatcirle(latitudeFrom, longitudeFrom, latitudeTo, longitudeTo){
    var R = 6371; // km
    var dLat = (latitudeTo-latitudeFrom).toRad();
    var dLon = (longitudeTo-longitudeFrom).toRad();
    var lat1 = latitudeFrom.toRad();
    var lat2 = latitudeFrom.toRad();

    var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return Math.round(d * 1000) / 1000;
}

/** Converts numeric degrees to radians */
if (typeof(Number.prototype.toRad) === "undefined") {
    Number.prototype.toRad = function() {
        return this * Math.PI / 180;
    }
}