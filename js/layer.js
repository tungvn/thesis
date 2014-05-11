var cur_map;
var untiled;
var tiled;
var mainUrl = GEOSERVERBASE + '/geoserver/wms' ;
// pink tile avoidance
OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;
// make OL compute scale according to WMS spec
OpenLayers.DOTS_PER_INCH = 25.4 / 0.28; 

OpenLayers.ProxyHost = "proxy.cgi?url=";
var j = 0 ;
function init(arrayLayers) 
{

    format = 'image/png';
    
    /*
    var bounds = new OpenLayers.Bounds(
        102.14499664306652, 8.563331604003906,
        109.46942901611357, 23.392730712890618
    );
    */

    /*
    var geographic = new OpenLayers.Projection("EPSG:32448");
    var mercator = new OpenLayers.Projection("EPSG:900913");
    var wfs = new OpenLayers.Layer.Vector("States", {
        strategies: [new OpenLayers.Strategy.BBOX()],
        protocol: new OpenLayers.Protocol.WFS({
            version: "1.0.0",
            srsName: "EPSG:4326", // this is the default
            url:  "http://demo.opengeo.org/geoserver/wfs",
            featureType: "states",
            featureNS: "http://www.openplans.org/topp"
        }),
        projection: geographic // specified because it is different than the map 
        //,styleMap: styleMap
    });

    */
    //var bounds = new OpenLayers.Bounds(544237.734262944,  2411497.39669989, 628300.076396695, 2515203.59760352);

    //var bounds = new OpenLayers.Bounds(105.461486816406,21.8054714202881,106.245208740234,22.7394256591797);
    
    var bounds = new OpenLayers.Bounds(105.42460118604606,21.798458385566327,106.25372642141726,22.748288441376886);
    var options = {
        //projection : 'EPSG:32448',
        maxExtent: bounds
    };
    
    //map =1;
    //if ( map == null && j==0)
    console.log(cur_map);
    
    if ( cur_map == null)
        {   
            cur_map = new OpenLayers.Map('map', options);
            console.log("khoi tao map");
        }
    else
    {
        cur_map.destroy();
        cur_map = new OpenLayers.Map('map', options);
            console.log("khoi tao lai map ********");
    }
    
    
    /*
    textxa = new OpenLayers.Layer.WMS("Textxa Layer",
        mainUrl, 
        {'layers': BacKanTextxa, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false}
    );
     

    lokhoan = new OpenLayers.Layer.WMS("Lokhoan Layer",
        mainUrl, 
        {'layers': BacKanLokhoan, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false, opacity: 1.0}
    );

    thuyvan = new OpenLayers.Layer.WMS("Thuyvan Layer",
        mainUrl, 
        {'layers': BacKanThuyvan, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false}
    );

    tuoiline =  new OpenLayers.Layer.WMS("TuoiLine Layer",
        mainUrl, 
        {'layers': BacKanTuoiline, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false}
    );
    tuoi = new OpenLayers.Layer.WMS("Tuoi Layer",
        mainUrl, 
        {'layers': BacKanTuoi, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false}
    );

    
    dutgay = new OpenLayers.Layer.WMS("Dutgay Layer",
        mainUrl, 
        {'layers': BacKanDutgay, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: false}
    );
    
    border = new OpenLayers.Layer.WMS("Boundary Layer",
        mainUrl, 
        {'layers': BacKanBoundary, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: true, opacity: 0.5}
    );
    /* = 
    // setup tiled layer
    contour = new OpenLayers.Layer.WMS("Contour100 Layer",
        mainUrl, 
        {'layers': BacKanContour100, transparent: true, format: 'image/gif',projection : 'EPSG:32448' },
        {isBaseLayer: false}
    );
    */
    /*highlightLayer = new OpenLayers.Layer.Vector("Highlighted Features", {
        displayInLayerSwitcher: false, 
        isBaseLayer: false 
        }
    );*/
/*
    infoControls = {
        click: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: [border],
            queryVisible: true,
            infoFormat: "text/html"           
        }),
        hover: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: [border],
            hover: true,
            // defining a custom format options here
            formatOptions: {
                typeName: 'water_bodies', 
                featureNS: 'http://www.openplans.org/topp'
            },
            queryVisible: true
        })
    };
    var array = [border];
    map.addLayers(array); 
*/
    
    // call function to show map and register event click + hover
    /*temp = new OpenLayers.Layer.WMS("Boundary Layer",
        mainUrl, 
        {'layers': BacKanContour100, transparent: true, format: 'image/gif', projection : 'EPSG:32448'},
        {isBaseLayer: true, opacity: 0.5}
    );*/
    /*var layers1 = new Array(border);
    console.log(layers1);*/
    //var layers = layerInput(new Array('contour100'));
    
    infoControls = {
        click: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: arrayLayers,
            queryVisible: true,
            infoFormat: "text/html"           
        }),
        hover: new OpenLayers.Control.WMSGetFeatureInfo({
            url: mainUrl, 
            title: 'Identify features by clicking',
            layers: arrayLayers,
            hover: true,
            // defining a custom format options here
            formatOptions: {
                typeName: 'water_bodies', 
                featureNS: 'http://www.openplans.org/topp'
            },
            queryVisible: true
        })
    };
    //cur_map.destroy();
    cur_map.addLayers(arrayLayers);
    /*
    for (var i = 0; i < arrayLayers.length; i++) {
        console.log(arrayLayers.length);

    };
    */
    /*
    if ( j == 0)
        {
            cur_map.addLayers([arrayLayers]);
            console.log("them border lan 1");
        }
    
    if ( j==1)
        {
            cur_map.destroy();
            cur_map.addLayers([arrayLayers]);
            console.log("them border lan 2");
        }
        j++;
        */
    //else    map.destroy();
    
        //map.redraw();
    //if ( j!= 0)  map.removeLayer(arrayLayers[0],true);
    
    //
    //console.log("j = " + j);
    console.log(cur_map.layers);

    

    for (var i in infoControls) { 
        infoControls[i].events.register("getfeatureinfo", this, showInfo);
        cur_map.addControl(infoControls[i]); 
    }

    //map.addControl(new OpenLayers.Control.LayerSwitcher());

    infoControls.click.activate();
        //map.zoomToMaxExtent();
        cur_map.zoomTo(1);
    cur_map.panTo(new OpenLayers.LonLat(20.0,105.0));

    cur_map.addControl(new OpenLayers.Control.PanZoomBar({
        position: new OpenLayers.Pixel(2, 15)
    }));
    cur_map.addControl(new OpenLayers.Control.Navigation());
    cur_map.addControl(new OpenLayers.Control.Scale($('scale')));
    cur_map.addControl(new OpenLayers.Control.MousePosition({element: $('location')}));

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
        document.getElementById('responseText').innerHTML = convertT2U(evt.text);
        //document.getElementById('responseText').innerHTML +=  map.getLonLatFromPixel(evt.xy);
        //document.getElementById('responseText').innerHTML += evt.text.split('<br>');

        
    }
}

function layerInput(layers) {
    // set default
    if(layers.length == 0 || typeof(layers) === 'undefined') {
        layers = new Array('boundary');
    }

    var arrayLayers = new Array();

    for (var i = 0; i < layers.length; i++) {
        console.log(layers[i]);
        var temp;
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

    init(arrayLayers);
}