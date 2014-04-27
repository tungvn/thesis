var map;
var untiled;
var tiled;
// pink tile avoidance
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
// make OL compute scale according to WMS spec
OpenLayers.DOTS_PER_INCH = 25.4 / 0.28; 

OpenLayers.ProxyHost = "/dadangsinhhoc/proxy.cgi?url=";

function init() {
    format = 'image/png';
    var mainUrl = GEOSERVERBASE + '/geoserver/wms' ;

    var bounds = new OpenLayers.Bounds(
        102.14499664306652, 8.563331604003906,
        109.46942901611357, 23.392730712890618
    );
    var options = {
        maxExtent: bounds
    };
    map = new OpenLayers.Map('map', options);

    // setup tiled layer
    water = new OpenLayers.Layer.WMS("VietNam Layer",
        mainUrl, 
        {'layers': VietnamLayer, transparent: true, format: 'image/gif'},
        {isBaseLayer: true}
    );
    
    highlightLayer = new OpenLayers.Layer.Vector("Highlighted Features", {
        displayInLayerSwitcher: false, 
        isBaseLayer: false 
        }
    );

    infoControls = {
        click: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: [water],
            queryVisible: true,
            infoFormat: "text/html"           
        }),
        hover: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: [water],
            hover: true,
            // defining a custom format options here
            formatOptions: {
                typeName: 'water_bodies', 
                featureNS: 'http://www.openplans.org/topp'
            },
            queryVisible: true
        })
    };

    map.addLayers([water, highlightLayer]); 

    for (var i in infoControls) { 
        infoControls[i].events.register("getfeatureinfo", this, showInfo);
        map.addControl(infoControls[i]); 
    }

    map.addControl(new OpenLayers.Control.LayerSwitcher());

    infoControls.click.activate();
        //map.zoomToMaxExtent();
        map.zoomTo(1);
    map.panTo(new OpenLayers.LonLat(20.0,105.0));

    map.addControl(new OpenLayers.Control.PanZoomBar({
        position: new OpenLayers.Pixel(2, 15)
    }));
    map.addControl(new OpenLayers.Control.Navigation());
    map.addControl(new OpenLayers.Control.Scale($('scale')));
    map.addControl(new OpenLayers.Control.MousePosition({element: $('location')}));

    // build up all controls
    /*map.addControl(new OpenLayers.Control.PanZoomBar({
        position: new OpenLayers.Pixel(2, 15)
    }));
    map.addControl(new OpenLayers.Control.Navigation());
    map.addControl(new OpenLayers.Control.Scale($('scale')));
    map.addControl(new OpenLayers.Control.MousePosition({element: $('location')}));
    map.zoomToExtent(bounds);
    
    // support GetFeatureInfo
    map.events.register('click', map, function (e) {
        document.getElementById('nodelist').innerHTML = "Loading... please wait...";
        var params = {
            REQUEST: "GetFeatureInfo",
            EXCEPTIONS: "application/vnd.ogc.se_xml",
            BBOX: map.getExtent().toBBOX(),
            SERVICE: "WMS",
            INFO_FORMAT: 'text/html',
            QUERY_LAYERS: map.layers[0].params.LAYERS,
            FEATURE_COUNT: 50,
            Layers: 'geo_demo:VNM_adm2',
            WIDTH: map.size.w,
            HEIGHT: map.size.h,
            format: format,
            styles: map.layers[0].params.STYLES,
            srs: map.layers[0].params.SRS};
        
        // handle the wms 1.3 vs wms 1.1 madness
        if(map.layers[0].params.VERSION == "1.3.0") {
            params.version = "1.3.0";
            params.j = parseInt(e.xy.x);
            params.i = parseInt(e.xy.y);
        } else {
            params.version = "1.1.1";
            params.x = parseInt(e.xy.x);
            params.y = parseInt(e.xy.y);
        }
            
        // merge filters
        if(map.layers[0].params.CQL_FILTER != null) {
            params.cql_filter = map.layers[0].params.CQL_FILTER;
        } 
        if(map.layers[0].params.FILTER != null) {
            params.filter = map.layers[0].params.FILTER;
        }
        if(map.layers[0].params.FEATUREID) {
            params.featureid = map.layers[0].params.FEATUREID;
        }
        OpenLayers.loadURL("http://localhost:8080/geoserver/geo_demo/wms", params, this, setHTML, setHTML);
        OpenLayers.Event.stop(e);
    });*/
}
// sets the HTML provided into the nodelist element
function showInfo(evt) {
    if (evt.features && evt.features.length) {
         highlightLayer.destroyFeatures();
         highlightLayer.addFeatures(evt.features);
         highlightLayer.redraw();
    } else {
        document.getElementById('responseText').innerHTML = evt.text;
        //document.getElementById('responseText').innerHTML +=  map.getLonLatFromPixel(evt.xy);
        //document.getElementById('responseText').innerHTML += evt.text.split('<br>');

        
    }
}

