function getLayersIn(workspace) {
	/*var arr = new Array(3);
	arr[0] = new Array(4);
	arr[1] = new Array(4);
	arr[2] = new Array(4);
	for (var i = 0; i < arr.length; i++) {
		for (var j = 0; j < arr[i].length; j++) {
			arr[i][j] = i + ' ' + j;
		}
	}
	for (var i = 0; i < arr.length; i++) {
		for (var j = 0; j < arr[i].length; j++) {
			console.log(arr[i][j]);
		}
	}*/
}

var json;

/*$.ajax({
	url: 'http://localhost:8080/geoserver/rest/',
	type: 'POST',
	dataType: 'jsonp',
})
.done(function(data) {
	var json = JSON.parse(data);
	console.log(json);
})
.fail(function() {
	console.log('error');
})
.always(function() {
	console.log('complete');
});*/
/*$.ajax("http://localhost:8080/geoserver/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=geo_demo:VNM_adm2&maxFeatures=50&outputFormat=json&format_options=callback:processJSON",
    { dataType: "jsonp" }
).done(function ( data ) {
    console.log('done will never be called, unfortunately...');
}).error(function() {
    console.log('fail');
});*/

$.ajax({
    url: "http://localhost:8080/geoserver/geo_demo/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=geo_demo:VNM_adm2&maxFeatures=60&outputFormat=json?callback=handler",
    type: 'jsonp',
    jsonpCallback: handler
});
var obj;

function handler(request) {
	alert('123');
}