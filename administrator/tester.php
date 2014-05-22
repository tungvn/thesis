<?php
// See if we're passing a WFS request to the server on private net (or for cross domain protection bypassing proxy)
/*$pathParts = explode('/', $_SERVER['PATH_INFO']);
if ($pathParts[1] == 'wfsauth') {
	$unused = array_shift($pathParts);
	$unused = array_shift($pathParts);
	$user = array_shift($pathParts);
	$pass = array_shift($pathParts);
	
	// Pass through for WFS using username and password to follow
	include_once "includes/GeoserverWrapper.php";
	$geoserver = new GeoserverWrapper('http://localhost:8080/geoserver', $user, $pass);
	$wfs = implode('/', $pathParts);
	if ($_SERVER['QUERY_STRING'] != '') $wfs .= '?' . $_SERVER['QUERY_STRING'];
	echo $geoserver->wfsPost($wfs, file_get_contents('php://input'));
	return;
} else if ($pathParts[1] == 'wfs') {
	$unused = array_shift($pathParts);
	$unused = array_shift($pathParts);
	
	// No auth required
	include_once "includes/GeoserverWrapper.php";
	$geoserver = new GeoserverWrapper('http://localhost:8080/geoserver');
	$wfs = implode('/', $pathParts);
	if ($_SERVER['QUERY_STRING'] != '') $wfs .= '?' . $_SERVER['QUERY_STRING'];
	echo $geoserver->wfsPost($wfs, file_get_contents('php://input'));
	return;
}*/

// No WFS? Proceed with our simple test script...

if (isset($_REQUEST['action'])) {
	include_once "includes/GeoserverWrapper.php";
	$geoserver = new GeoserverWrapper('http://localhost:8080/geoserver', $_REQUEST['username'], $_REQUEST['password']);

	switch ($_REQUEST['action']) {
		case 'listworkspaces':
			print_r($geoserver->listWorkspaces());
			break;
		case 'createworkspace':
			print_r($geoserver->createWorkspace($_REQUEST['workspace']));
			break;
		case 'deleteworkspace':
			print_r($geoserver->deleteWorkspace($_REQUEST['workspace']));
			break;

		case 'listdatastores':
			print_r($geoserver->listDataStores($_REQUEST['workspace']));
			break;
		case 'createdatastore':
			print_r($geoserver->createShpDirDataStore($_REQUEST['datastore'], $_REQUEST['workspace'], $_REQUEST['location']));
			break;
		case 'createdatastorepostgis':
			print_r($geoserver->createPostGISDataStore($_REQUEST['datastore'], $_REQUEST['workspace'], $_REQUEST['dbname'], $_REQUEST['dbuser'], $_REQUEST['dbpass'], $_REQUEST['dbhost']));
			break;
		case 'deletedatastore':
			print_r($geoserver->deleteDataStore($_REQUEST['datastore'], $_REQUEST['workspace']));
			break;
		
		case 'listlayers':
			print_r($geoserver->listLayers($_REQUEST['workspace'], $_REQUEST['datastore']));
			break;
		case 'createlayer':
			print_r($geoserver->createLayer($_REQUEST['layer'], $_REQUEST['workspace'], $_REQUEST['datastore'], $_REQUEST['description']));
			break;
		case 'deletelayer':
			print_r($geoserver->deleteLayer($_REQUEST['layer'], $_REQUEST['workspace'], $_REQUEST['datastore']));
			break;
		case 'viewlayer':
			if ($_REQUEST['format'] == 'LEGEND') {
				echo '<img alt="Embedded Image" src="data:image/png;base64,'.base64_encode($geoserver->viewLayerLegend($_REQUEST['layer'], $_REQUEST['workspace'])).'"/>';

			} else {
				print_r($geoserver->viewLayer($_REQUEST['layer'], $_REQUEST['workspace'], $_REQUEST['format']));
			}
			break;

		case 'liststyles':
			print_r($geoserver->listStyles());
			break;
		case 'createstyle':
			print_r($geoserver->createStyle($_REQUEST['stylename'], $_REQUEST['sld']));
			break;
		case 'deletestyle':
			print_r($geoserver->deleteStyle($_REQUEST['stylename']));
			break;
		case 'assignstyle':
			print_r($geoserver->addStyleToLayer($_REQUEST['layer'], $_REQUEST['workspace'], $_REQUEST['stylename']));
			break;

		case 'wfs-t':
			print_r($geoserver->executeWFSTransaction(stripslashes($_REQUEST['transaction'])));
			break;
	}

	return;
}
?>
<html>
	<head>
		<script src="../js/jquery-1.9.1.js"></script>
		<title>Test Page for GeoServer PHP Wrapper</title>
	</head>

	<body>
		<h3>This page tests and demonstrates the GeoServer PHP Wrapper.</h3>
		<p>Choose a function below to execute. You may optionally provide a username and password to access restricted datasets:</p>

		<table border="0">
			<tr><td>Username:</td><td><input id="username"></td></tr>
			<tr><td>Password:</td><td><input id="password"></td></tr>
		</table>

		<hr style="position: absolute; left: 10;" width="500px" />
		
		<h4>List Workspaces</h4>
		<table border="0">
			<tr><td width="75px"></td><td><button onclick="$('#listworkspaces_results').load('<?=$_SERVER["PHP_SELF"]?>?action=listworkspaces&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()));">Execute</button></td></tr>
		</table>

		<b>Results</b>:<br />
		<pre id="listworkspaces_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Create Workspace</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="createworkspace_workspace"></td></tr>
			<tr><td></td><td><button onclick="$('#createworkspace_results').load('<?=$_SERVER["PHP_SELF"]?>?action=createworkspace&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#createworkspace_workspace').val()));">Execute</button></td></tr>
		</table>

		<b>Results</b>:<br />
		<pre id="createworkspace_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Delete Workspace</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="deleteworkspace_workspace"></td></tr>
			<tr><td></td><td><button onclick="$('#deleteworkspace_results').load('<?=$_SERVER["PHP_SELF"]?>?action=deleteworkspace&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#deleteworkspace_workspace').val()));">Execute</button></td></tr>
		</table>

		<b>Results</b>:<br />
		<pre id="deleteworkspace_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>List Data Stores in Workspace</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="listdatastores_workspace"></td></tr>
			<tr><td></td><td><button onclick="$('#listdatastores_results').load('<?=$_SERVER["PHP_SELF"]?>?action=listdatastores&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#listdatastores_workspace').val()));">Execute</button></td></tr>
		</table>

		<pre id="listdatastores_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Create Data Store (Existing Shapefile Directory)</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="createdatastore_workspace"></td></tr>
			<tr><td>Data Store:</td><td><input id="createdatastore_datastore"></td></tr>
			<tr><td>Location:</td><td><input id="createdatastore_location"> (relative to GeoServer data dir, e.g. data/usa)</td></tr>
			<tr><td></td><td><button onclick="$('#createdatastore_results').load('<?=$_SERVER["PHP_SELF"]?>?action=createdatastore&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#createdatastore_workspace').val()) + '&datastore=' + escape($('#createdatastore_datastore').val()) + '&location=' + escape($('#createdatastore_location').val()));">Execute</button></td></tr>
		</table>

		<pre id="createdatastore_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Create Data Store (Existing PostGIS Database)</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="createdatastorepostgis_workspace"></td></tr>
			<tr><td>Data Store:</td><td><input id="createdatastorepostgis_datastore"></td></tr>
			<tr><td>Host:</td><td><input id="createdatastorepostgis_host" value="localhost"></td></tr>
			<tr><td>Database Name:</td><td><input id="createdatastorepostgis_dbname"></td></tr>
			<tr><td>Database User:</td><td><input id="createdatastorepostgis_dbuser"></td></tr>
			<tr><td>Database Password:</td><td><input id="createdatastorepostgis_dbpass"></td></tr>
			<tr><td></td><td><button onclick="$('#createdatastorepostgis_results').load('<?=$_SERVER["PHP_SELF"]?>?action=createdatastorepostgis&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#createdatastorepostgis_workspace').val()) + '&datastore=' + escape($('#createdatastorepostgis_datastore').val()) + '&dbhost=' + escape($('#createdatastorepostgis_host').val()) + '&dbname=' + escape($('#createdatastorepostgis_dbname').val()) + '&dbuser=' + escape($('#createdatastorepostgis_dbuser').val()) + '&dbpass=' + escape($('#createdatastorepostgis_dbpass').val()));">Execute</button></td></tr>
		</table>

		<pre id="createdatastorepostgis_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Delete Data Store</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="deletedatastore_workspace"></td></tr>
			<tr><td>Data Store:</td><td><input id="deletedatastore_datastore"></td></tr>
			<tr><td></td><td><button onclick="$('#deletedatastore_results').load('<?=$_SERVER["PHP_SELF"]?>?action=deletedatastore&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#deletedatastore_workspace').val()) + '&datastore=' + escape($('#deletedatastore_datastore').val()));">Execute</button></td></tr>
		</table>

		<pre id="deletedatastore_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>List Layers in Workspace &amp; Datastore</h4>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="listlayers_workspace"></td></tr>
			<tr><td>Datastore:</td><td><input id="listlayers_datastore"></td></tr>
			<tr><td></td><td><button onclick="$('#listlayers_results').load('<?=$_SERVER["PHP_SELF"]?>?action=listlayers&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()) + '&workspace=' + escape($('#listlayers_workspace').val()) + '&datastore=' + escape($('#listlayers_datastore').val()));">Execute</button></td></tr>
		</table>

		<pre id="listlayers_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Create Layer</h4>
		<script type="text/javascript">
			function doCreateLayer() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'createlayer',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'workspace': $('#createlayer_workspace').val(),
				'datastore': $('#createlayer_datastore').val(),
				'layer': $('#createlayer_layer').val(),
				'description': $('#createlayer_description').val()
				}, function(ret) {
					$('#createlayer_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="createlayer_workspace"></td></tr>
			<tr><td>Datastore:</td><td><input id="createlayer_datastore"></td></tr>
			<tr><td>Layer Name</td><td><input id="createlayer_layer"></td></tr>
			<tr><td width="400px" colspan="2">Layer name should be the shapefile name (filename only, no path) for shapefile directory data stores, or the PostGIS table name for PostGIS stores.</td></tr>
			<tr><td>Description:</td><td><input id="createlayer_description"></td></tr>
			<tr><td></td><td><button onclick="doCreateLayer();">Execute</button></td></tr>
		</table>

		<pre id="createlayer_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Delete Layer</h4>
		<script type="text/javascript">
			function doDeleteLayer() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'deletelayer',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'workspace': $('#deletelayer_workspace').val(),
				'datastore': $('#deletelayer_datastore').val(),
				'layer': $('#deletelayer_layer').val()
				}, function(ret) {
					$('#deletelayer_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="deletelayer_workspace"></td></tr>
			<tr><td>Datastore:</td><td><input id="deletelayer_datastore"></td></tr>
			<tr><td>Layer Name:</td><td><input id="deletelayer_layer"></td></tr>
			<tr><td></td><td><button onclick="doDeleteLayer();">Execute</button></td></tr>
		</table>

		<pre id="deletelayer_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>View Layer</h4>
		<script type="text/javascript">
			function doViewLayer() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'viewlayer',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'workspace': $('#viewlayer_workspace').val(),
				'layer': $('#viewlayer_layer').val(),
				'format': $('#viewlayer_format').val()
				}, function(ret) {
					$('#viewlayer_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="viewlayer_workspace"></td></tr>
			<tr><td>Layer Name:</td><td><input id="viewlayer_layer"></td></tr>
			<tr><td>Format:</td><td><select id="viewlayer_format">
					<option>GML</option>
					<option>KML</option>
					<option>LEGEND</option>
				</select></td></tr>
			<tr><td></td><td><button onclick="doViewLayer();">Execute</button></td></tr>
		</table>

		<pre id="viewlayer_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>List Styles</h4>
		<table border="0">
			<tr><td width="75px"></td><td><button onclick="$('#liststyles_results').load('<?=$_SERVER["PHP_SELF"]?>?action=liststyles&username=' + escape($('#username').val()) + '&password=' + escape($('#password').val()));">Execute</button></td></tr>
		</table>

		<b>Results</b>:<br />
		<pre id="liststyles_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Create Style</h4>
		<script type="text/javascript">
			function doCreateStyle() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'createstyle',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'stylename': $('#createstyle_stylename').val(),
				'sld': $('#createstyle_sld').val()
				}, function(ret) {
					$('#createstyle_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Style Name:</td><td><input id="createstyle_stylename"></td></tr>
			<tr><td>SLD: (paste full SLD, e.g. <a href="http://docs.geoserver.org/latest/en/user/_downloads/line_dashedline.sld">dashed line</a>)</td><td><textarea id="createstyle_sld"></textarea></td></tr>
			<tr><td></td><td><button onclick="doCreateStyle();">Execute</button></td></tr>
		</table>

		<pre id="createstyle_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />
		
		<h4>Delete Style</h4>
		<script type="text/javascript">
			function doDeleteStyle() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'deletestyle',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'stylename': $('#deletestyle_stylename').val()
				}, function(ret) {
					$('#deletestyle_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Style Name:</td><td><input id="deletestyle_stylename"></td></tr>
			<tr><td></td><td><button onclick="doDeleteStyle();">Execute</button></td></tr>
		</table>
		
		<pre id="deletestyle_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />
		
		<h4>Assign Style to Layer</h4>
		<script type="text/javascript">
			function doAssignStyle() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'assignstyle',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'workspace': $('#assignstyle_workspace').val(),
				'layer': $('#assignstyle_layer').val(),
				'stylename': $('#assignstyle_stylename').val()
				}, function(ret) {
					$('#assignstyle_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		<table border="0">
			<tr><td>Workspace:</td><td><input id="assignstyle_workspace"></td></tr>
			<tr><td>Layer Name:</td><td><input id="assignstyle_layer"></td></tr>
			<tr><td>Style Name:</td><td><input id="assignstyle_stylename"></td></tr>
			<tr><td></td><td><button onclick="doAssignStyle();">Execute</button></td></tr>
		</table>

		<pre id="assignstyle_results">(Press "Execute" First)</pre>

		<hr style="position: absolute; left: 10;" width="500px" width="500px" />

		<h4>Execute WFS-T Transaction</h4>
		<script type="text/javascript">
			function doWFST() {
				$.post('<?=$_SERVER["PHP_SELF"]?>', {
				'action': 'wfs-t',
				'username': $('#username').val(),
				'password': $('#password').val(),
				'transaction': $('#wfst_transaction').val(),
				}, function(ret) {
					$('#wfst_results').html(escape(ret).replace(/%(..)/g,"&#x$1;"));		
				}, 'html');
			}
		</script>
		Transaction:<br />(Layer and workspace/namespace are specified as part of transaction XML)<br />
		<textarea cols="40" rows="6"  id="wfst_transaction"></textarea><br />
		<button onclick="doWFST();">Execute</button>

		<pre id="wfst_results">(Press "Execute" First)</pre>

	</body>
</html>
