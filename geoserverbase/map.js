var map,countries;

function mapinitialize() {
    
     OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url=";
    // ----- OLD CODE

    var bounds = new OpenLayers.Bounds(
        102.14499664306652, 8.563331604003906, 109.46942901611357, 23.392730712890618
        );

    var options = {
        maxExtent: bounds,
        projection: 'EPSG:4756',
        units: 'meters'
    };
    map = new OpenLayers.Map('map', options);
            
    countries = new OpenLayers.Layer.WMS(
        VietnamLayer, GEOSERVERBASE + '/geoserver/wms',
        {
            layers: VietnamLayer,
            format: 'image/png'
        }
        );
        
    map.addLayer(countries);

    map.zoomTo(1);
    map.panTo(new OpenLayers.LonLat(20.0,105.0));
    // ------ OLD CODE --- END


    //showInfoDiv();
    var getFeatureControl = new OpenLayers.Control.WMSGetFeatureInfo({
        url: GEOSERVERBASE + '/geoserver/wms' ,
        drillDown: false,
        queryVisible : true,
        hover: false,
        layers: countries,
        eventListeners : {
            getfeatureinfo : function (event){
                popup = new OpenLayers.Popup.FrameCloud(
                        "popinfo",
                        map.getLonLatFromPixel(event.xy),
                        null,
                        event.text,
                        null,
                        true
                    );
                map.addPopup(popup,true);

            }

        }            


    });
    map.addControl(getFeatureControl);
    getFeatureControl.activate();


}
// --- code show info
    /*
    infoControls = {
        click: new OpenLayers.Control.WMSGetFeatureInfo({
            url: 'http://localhost/',
            title: 'Identify feature',
            layers: [water],
            queryVisible : true
        }),
        hover: new OpenLayers.Control.WMSGetFeatureInfo({
            url: 'http://localhost/',
            title: ' Identify feature',
            layers: 'water',
            hover: true,

            formatOptions: {
                typeName: 'water_bodies',
                featureNS: 'http://www.openplans.org/topp'                        
            },
            queryVisible: true
        })

    };

    //map.addLayers([]);


    map.addControl( new OpenLayers.Control.LayerSwitcher());
    infoControls.click.activate();
    */

function showInfo(evt)
{

    if ( evt.features &&  evt.features.length)
    {
        highlightLayer.destroyFeatures();
        highlightLayer.addFeatures(evt.features);
        highlightLayer.redraw();

    }
    else
    {
        document.getElementById('responseText').innerHTML = evt.text;

    }


}
function showInfoDiv()
{
    infoControls = {
        click: new OpenLayers.Control.WMSGetFeatureInfo({
            url: GEOSERVERBASE + '/geoserver/wms',
            title: 'Identify feature',
            layers: [water],
            queryVisible : true
        }),
        hover: new OpenLayers.Control.WMSGetFeatureInfo({
            url: GEOSERVERBASE + '/geoserver/wms',
            title: ' Identify feature',
            layers: [water],
            hover: true,

            formatOptions: {
                typeName: 'water_bodies',
                featureNS: 'http://www.openplans.org/topp'                        
            },
            queryVisible: true
        })

    };


    for (var i in infoControls) { 
            infoControls[i].events.register("getfeatureinfo", this, showInfo);
            map.addControl(infoControls[i]); 
        }
    map.addControl( new OpenLayers.Control.LayerSwitcher());
    infoControls.click.activate();   


    //Setting 

    for ( var key in infoControls)
    {
        var control = infoControls[key];
        control.activate();
        control.infoFormat = "text/html";
        control.layers = [countries];

    }



}

