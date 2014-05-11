var map; // global variable
var overlayMaps; // global variable, load custom layer into it

var GEOSERVER_BASE = 'http://localhost:8080'; // without slashes (/)
var layer_name = 'VNM_adm2';

// wms parameters
var wmsparams = [
    "REQUEST=GetMap",
    "SERVICE=WMS",
    "VERSION=1.1.1",
    "BGCOLOR=0xFFFFFF",
    "TRANSPARENT=TRUE",
    "SRS=EPSG:3857",
    "WIDTH=255",
    "HEIGHT=255",
    "format=image/png"
];
// create map and load custom layer
function mapinitialize(latLng_default, zoom_default) {
    // set latlng default if null
    if(typeof(latLng_default) === 'undefined') 
        latLng_default = new google.maps.LatLng(16.55196172197251, 107.138671875);
    if(typeof(zoom_default) === 'undefined')
        zoom_default = 5;
    // options for our map
    var mapOptions = {
        zoom: zoom_default,
        center: latLng_default,
        mapTypeControl: true,
        zoomControl: true,
        zoomControlOptions: {
            style: google.maps.ZoomControlStyle.LARGE,
            position: google.maps.ControlPosition.LEFT_CENTER
        },
        panControl: true,
        panControlOptions: {
            position: google.maps.ControlPosition.LEFT_CENTER
        },
        mapTypeControl:false,
        draggableCursor: 'crosshair',
        scrollwheel: true,
        backgroundColor: "#badbff"
    }
    // create a google map
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    // load custom layer
    /*overlayMaps =[{
        getTileUrl: overlayMapsLoader,
        tileSize: new google.maps.Size(256, 256),
        isPng: true,
        maxZoom: 15,
        minZoom: 4,
        alt: 'Vietnam',
        /*styleMap: new OpenLayers.Style(OpenLayers.Feature.Vector.style["select"])
    }];*/
}

// sets the HTML provided into the nodelist element
function setHTML(response){
    document.getElementById('nodelist').innerHTML = response.responseText;
};
// 
function overlayMapsLoader(coord, zoom) {
    var lULP = new google.maps.Point(coord.x*256,(coord.y+1)*256);
    var lLRP = new google.maps.Point((coord.x+1)*256,coord.y*256);

    var projectionMap = new MercatorProjection();

    var lULg = projectionMap.fromDivPixelToSphericalMercator(lULP, zoom);
    var lLRg  = projectionMap.fromDivPixelToSphericalMercator(lLRP, zoom);

    var lUL_Latitude = lULg.y;
    var lUL_Longitude = lULg.x;
    var lLR_Latitude = lLRg.y;
    var lLR_Longitude = lLRg.x;

    if (lLR_Longitude < lUL_Longitude) {
      lLR_Longitude = Math.abs(lLR_Longitude);
    }

    var bbox = [lUL_Longitude, lUL_Latitude, lLR_Longitude, lLR_Latitude];

    return GEOSERVER_BASE + "/geoserver/wms?" + wmsparams.join("&") + "&layers=" + layer_name + "&bbox=" + bbox.join(',');
}
// push map
function pushOverlayMap() {
	for (i=0; i < overlayMaps.length; i++) {
        var overlayMap = new google.maps.ImageMapType(overlayMaps[i]);
        map.overlayMapTypes.push(overlayMap);
        map.overlayMapTypes.setAt(overlayMaps[i],overlayMap);
    }
}

// hover event
function hoverMapEvent() {
    overlay = new google.maps.OverlayView();
    overlay.draw = function() {};
    overlay.setMap(map);

    google.maps.event.addListener(map, 'mousemove',
        function(event) {
            alert(1);
            var point = overlay.getProjection().fromLatLngToContainerPixel(event.latLng);
            var html = "<p>LatLng: " + event.latLng.lat() + ", " + event.latLng.lng() 
                        + "</p><p>Point: " + roundToTwo(point.x) + ", " + roundToTwo(point.y) + "</p>";
            document.getElementById('map_info').innerHTML = '';
            document.getElementById('map_info').innerHTML = html;
            $('#map_control').slideDown('slow', function() {
                $(this).show();
            });
            $('.arrow-down').hide();
            $('.arrow-up').show();
            map.setCenter(event.latLng);
        }
    );
}

// map-click event
function clickMapEvent() {
	overlay = new google.maps.OverlayView();
    overlay.draw = function() {};
    overlay.setMap(map);

    google.maps.event.addListener(map, 'click',
        function(event) {
            map.setCenter(event.latLng);
        }
    );
}

// select center of current map
function centerCurrentMap() {
    if(map)
        return map.getCenter();
}
// return current zoom level of map
function currentZoomMap() {
    if(map)
        return map.getZoom();
}
// rounding
function roundToTwo(num) {
    return +(Math.round(num + "e+2")  + "e-2");
}
// set map type id
function setMapTypeId(type) {
    // set default is 'roadmap + customlayer' if 'type' is empty
    if(type.length == 0 || typeof(type) === 'undefined') {
        type = ['roadmap'];
    }
    mapinitialize(centerCurrentMap(), currentZoomMap());
    clickMapEvent();
    // reset map
    map.setMapTypeId(null);
    // load map follow selected input
    if($.inArray('roadmap', type) != -1) {
        map.setMapTypeId( google.maps.MapTypeId.ROADMAP );
    } else if($.inArray('satellite', type) != -1) {
        map.setMapTypeId( google.maps.MapTypeId.SATELLITE );
    } else if($.inArray('hybrid', type) != -1) {
        map.setMapTypeId( google.maps.MapTypeId.HYBRID );
    } else if($.inArray('terrain', type) != -1) {
        map.setMapTypeId( google.maps.MapTypeId.TERRAIN );
    }
    if($.inArray('customlayer', type) != -1) {
        pushOverlayMap();
    }
    return false;
}