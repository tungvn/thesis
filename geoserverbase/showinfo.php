<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <title>OpenLayers WMS Feature Info Example (GeoServer)</title>
    <script type="text/javascript" src="http://openlayers.org/dev/OpenLayers.js"></script>
    <link rel="stylesheet" href="http://openlayers.org/dev//theme/default/style.css" type="text/css"/>
    <link rel="stylesheet" href="http://openlayers.org/dev/examples/style.css" type="text/css"/>
    <script type="text/javascript" src="base.js"></script>
    <style type="text/css">
    
    
        ul, li {
            padding-left: 0px;
            margin-left: 0px;
            list-style: none;
        }
        #info {
            position: absolute;
            top: 6em;
            left: 550px;
        }
        #info table td {
            border:1px solid #ddd;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            font-size: 90%;
            padding: .2em .1em;
            background:#fff;
    }
    #info table th{
        padding:.2em .2em;
            text-transform: uppercase;
            font-weight: bold;
            background: #eee;
    }
    tr.odd td {
            background:#eee;
    }
    table.featureInfo caption {
            text-align:left;
            font-size:100%;
            font-weight:bold;
            padding:.2em .2em;
    }


    </style>
    <script defer="defer" type="text/javascript">
    OpenLayers.ProxyHost = "proxy.cgi?url=";
    var map, infocontrols, water, highlightlayer;
    
    function load() {
        map = new OpenLayers.Map('map', {
            maxExtent: new OpenLayers.Bounds(102.14499664306652, 8.563331604003906, 109.46942901611357, 23.392730712890618)
        });

        var mainUrl = GEOSERVERBASE + '/geoserver/wms' ;

        /*
        var political = new OpenLayers.Layer.WMS("State Boundaries",
            "http://demo.opengeo.org/geoserver/wms", 
            {'layers': 'topp:tasmania_state_boundaries', transparent: true, format: 'image/gif'},
            {isBaseLayer: true}
        );

        
        var roads = new OpenLayers.Layer.WMS("Roads",
            "http://demo.opengeo.org/geoserver/wms", 
            {'layers': 'topp:tasmania_roads', transparent: true, format: 'image/gif'},
            {isBaseLayer: false}
        );

        var cities = new OpenLayers.Layer.WMS("Cities",
            "http://demo.opengeo.org/geoserver/wms", 
            {'layers': 'topp:tasmania_cities', transparent: true, format: 'image/gif'},
            {isBaseLayer: false}
        );
        */
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
                queryVisible: true            
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
    }

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

    function toggleControl(element) {
        for (var key in infoControls) {
            var control = infoControls[key];
            if (element.value == key && element.checked) {
                control.activate();
            } else {
                //control.deactivate();
            }
        }
    }

    function toggleFormat(element) {
        for (var key in infoControls) {
            var control = infoControls[key];
            control.infoFormat = element.value;
        }
    }

    function toggleLayers(element) {
        for (var key in infoControls) {
            var control = infoControls[key];
            if (element.value == 'Specified') {
                control.layers = [water];
            } else {
                control.layers = null;
            }
        }
    }

    // function toggle(key
    </script>
  </head>
  <body onload="load()">
      <h1 id="title">Feature Info Example</h1>

      <div id="tags">
        WMS, GetFeatureInfo
      </div>

      <p id="shortdesc">
        Demonstrates the WMSGetFeatureInfo control for fetching information about a position from WMS (via GetFeatureInfo request).
      </p>




    <div id="info">
        <h1>Tasmania</h1>
        <p>Click on the map to get feature info.</p>
        <div id="responseText">
        </div>
    </div>
      <div id="map" class="smallmap"></div>

    <div id="docs">
    </div>
        <ul id="control">
            <li>
                <input type="radio" name="controlType" value="click" id="click"
                       onclick="toggleControl(this);" checked="checked" />
                <label for="click">Click</label>
            </li>
            <li>
                <input type="radio" name="controlType" value="hover" id="hover" 
                       onclick="toggleControl(this);" />
                <label for="hover">Hover</label>
            </li>
        </ul>
        <ul id="format">
            <li>
                <input type="radio" name="formatType" value="text/html" id="html"
                       onclick="toggleFormat(this);" checked="checked" />
                <label for="html">Show HTML Description</label>
            </li>
            <li>
                <input type="radio" name="formatType" value="application/json" id="highlight" 
                       onclick="toggleFormat(this);" />
                <label for="highlight">Highlight Feature on Map</label>
            </li>
        </ul>
        <ul id="layers">
            <li>
                <input type="radio" name="layerSelection" value="Specified" id="Specified"
                       onclick="toggleLayers(this);" checked="checked" />
                <label for="Specified">Get water body info</label>
            </li>
            <li>
                <input type="radio" name="layerSelection" value="Auto" id="Auto" 
                       onclick="toggleLayers(this);" />
                <label for="Auto">Get info for visible layers</label>
            </li>
        </ul>

    <p style="margin:20px;">
    This is a sample page to demonstrate how to do a WMS GetFeatureInfo.<br />
    It uses a geoserver service on http://demo.opengeo.org to display Tasmania.<br />
    Please "view source" to see how this page is created - you can copy the page to your own PC and run it.
    </p>
    <p style="margin:20px">
    There are strict security rules in javascript which prevent "cross-domain" access.<br />
    This means a wfs layer must be on the same server, and the same domain, to be accessible<br />
    - in this case it would mean that only maps supplied from http://services.land.vic.gov.au/vicmap_api could be used.<br />
    </p>
    <p style="margin:20px">
    To get around this restriction, a proxy server must be used - this is set up by the line <strong>OpenLayers.ProxyHost = "/cgi-bin/proxy.cgi?url=";</strong><br />
    The proxy server runs on this server, and handles the WFS call to an external server so the javascript thinks it is a "local" request and will accept the results.<br />
    For security, the proxy server has a list of "acceptable" urls built in, and only servers on this list can be accessed.
    </p>
    <p style="margin:20px">
    While you can run this page on your PC and display the map, you will find the WFS call will not work.<br />
    This server is running the Apache web server, and has Python installed to run the proxy server code<br />
    If you install these applications on your PC, and install the proxy server code, you will be able to use the WFS. <br />
    There are other web servers, eg Microsoft IIS, which can also be used.
    </p>


  </body>
</html>
