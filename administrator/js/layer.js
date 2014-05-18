var map_admin;
var mainUrl = GEOSERVERBASE + '/geoserver/wms' ;
// pink tile avoidance
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
// make OL compute scale according to WMS spec
OpenLayers.DOTS_PER_INCH = 25.4 / 0.28; 

OpenLayers.ProxyHost = "proxy.cgi?url=";
var j = 0 ;
function init(arrayLayers, lat, lng) 
{
    console.log(lat + ' ' + lng);
    format = 'image/png';
    
    var bounds = new OpenLayers.Bounds(105.42460118604606,21.798458385566327,106.25372642141726,22.748288441376886);
    var options = {
        //projection : 'EPSG:32448',
        maxExtent: bounds
    };

    console.log(map_admin);
    
    if ( map_admin == null)
        {   
            map_admin = new OpenLayers.Map('map', options);
            console.log("khoi tao map");
        }
    else
    {
        map_admin.destroy();
        map_admin = new OpenLayers.Map('map', options);
            console.log("khoi tao lai map ********");
    }
    
    map_admin.addLayers(arrayLayers);
    map_admin.setCenter(new OpenLayers.LonLat(lat, lng), 2);

    for (var i in infoControls) { 
        infoControls[i].events.register("getfeatureinfo", this, showInfo);
        map_admin.addControl(infoControls[i]); 
    }

    //map.addControl(new OpenLayers.Control.LayerSwitcher());
        //map.zoomToMaxExtent();
    /*    map_admin.zoomTo(6);
    map_admin.panTo(new OpenLayers.LonLat(20.0,105.0));*/

    /*map_admin.addControl(new OpenLayers.Control.PanZoomBar({
        position: new OpenLayers.Pixel(2, 15)
    }));*/
    map_admin.addControl(new OpenLayers.Control.Navigation());
    map_admin.addControl(new OpenLayers.Control.Scale($('scale')));
    map_admin.addControl(new OpenLayers.Control.MousePosition({element: $('location')}));
}




// sets the HTML provided into the nodelist element
function showInfo(evt) {
    if (evt.features && evt.features.length) {
         highlightLayer.destroyFeatures();
         highlightLayer.addFeatures(evt.features);
         highlightLayer.redraw();
    } else {
        document.getElementById('responseText').innerHTML = convertT2U(evt.text);
        //document.getElementById('responseText').innerHTML +=  map.getLonLatFromPixel(evt.xy);
        //document.getElementById('responseText').innerHTML += evt.text.split('<br>');

        
    }
}

function layerInput(layers, lat, lng) {
    // set default
    if(layers.length == 0 || typeof(layers) === 'undefined') {
        layers = new Array('boundary');
    }
    var arrayLayers = new Array();

    for (var i = 0; i < layers.length; i++) {
        if(i == 0)
            var temp = new OpenLayers.Layer.WMS( layers[i],
                mainUrl, 
                {'layers': layers[i], transparent: true, format: 'image/gif',projection : 'EPSG:32448' },
                {isBaseLayer: true}
            );
        else
            var temp = new OpenLayers.Layer.WMS( layers[i],
                mainUrl, 
                {'layers': layers[i], transparent: false, format: 'image/gif',projection : 'EPSG:32448' },
                {isBaseLayer: false, opacity: 0.8}
            );
        arrayLayers.push(temp);
    }
    init(arrayLayers, lat, lng);
}