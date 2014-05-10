<?php include_once('/functions.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>UI Demo | Fimo Center | 2014</title>
	<link rel="stylesheet" href="css/style_map.css">
	<script type="text/javascript" src="js/OpenLayers.js"></script>
	<script type="text/javascript" src="js/base.js"></script>
	<script type="text/javascript" src="js/layer.js"></script>
</head>
<body onload="init()">
	<div id="wrapper">
		<div id="header">
			<div class="logo">
				<a href="#" target="_blank">
					<img src="#" alt="Logo">
				</a>
			</div>
			<!-- end .logo -->
			<form class="search_box">
				<input type="text" name="s" id="s" placeholder="Enter your keywords" autocomplete="off">
				<input type="submit" value="Search">
				<a href="#">Advance Search</a>
			</form>
			<div class="nav">
				<ul class="menu">
					<li><a href="index.html">Home</a></li>
					<li class="current-menu-item"><a href="map.php">Map</a></li>
					<li><a id="charts_link" href="javascript:void(0);">Charts</a></li>
				</ul>
			</div>
		</div>
		<!-- end #header -->
		<div id="content">
			<div class="left_col">
				<div class="col_title">
					<h2>Lớp bản đồ</h2>
				</div>
				<div class="col_content">
					<div id="map_control">
						<div class="map_option_block">
							<fieldset>
								<legend>Layer selected</legend>
								<?php $selects = array('id', 'name', 'slug');
								$wheres = array('type' => 'workspace');
								$wps = getRecords(DBNAME, 'object', $selects, $wheres);

								if($wps && pg_num_rows($wps) > 0) { 
									while($wp = pg_fetch_array($wps)) {
										$num = 0;
										$selects = array('id', 'name', 'slug');
										$wheres = array('workspace' => $wp['id']);
										$rows = getRecords(DBNAME, 'object', $selects, $wheres);

										if($rows && pg_num_rows($rows) > 0) {
											if($num == 0) {
												echo '<p>' . $wp['name'] . '</p>';
												$num++;
											}
											$i = 0;
											while ($row = pg_fetch_array($rows)) { ?>
											<p>
												<input type="checkbox" name="layer-<?php echo $row['id']; ?>" id="map_layer_option[<?php echo $i; ?>]" value="<?php echo $wp['slug'] . ':' . $row['slug']; ?>">
												<label for="map_layer_option[<?php echo $i++; ?>]"><?php echo $row['name']; ?></label>
											</p>
											<?php }
										}
									}
								} ?>
							</fieldset>
						</div>
						<div class="map_option_block">
							<legend for="map_info">Point Infomation</legend>
							<div id="map_info">
								<p><i>Click to view map-point information!</i></p>
							</div>
						</div>
						<div class="map_option_block">
							<legend for="map_info">Search Result</legend>
							<ul id="search-result" class="search-result">
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="main_col">
				<div id="map"></div>
				<!-- show map -->
				<div id="responseText"></div>
			</div>
			<div class="right_col">
				<div class="col_title">
					<h2>Biểu đồ</h2>
					<span id="close_charts_col"></span>
				</div>
				<div class="col_content">
					<div class="chart-block">
						<div id="chart_div"></div>
					</div>
				</div>
			</div>
		</div>
		<!-- end #map -->
		<div id="footer">
			<p>&copy; 2014 by Fimo Center. Site by TungVN</p>
		</div>
		<!-- end #footer -->
	</div>
	<!-- end #wrapper -->
	<div id="wrapper-extra">
		<!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
		<script src="js/jquery-1.9.1.js"></script>
		<script>
		jQuery(document).ready(function() {
			// Default set map height = windows height
			setMapHeight();

			// Set map height = windows height 
			function setMapHeight() {
				var window_height = $(window).height();
				$('#content > .left_col, #content > .main_col, #content > .right_col').height(window_height-69);
			}

			// Window resize
			$(window).resize(function() {
				setMapHeight();
			});

			// Show and hide charts
			$('#charts_link').click(function() {

				$('#content .main_col').stop().animate({width: '50%'}, 400);
				$('#responseText').animate({width: '50%'}, 400);
				$('#content .right_col').stop().animate({right: 0}, 400);

				$('#close_charts_col').click(function() {
					hide_charts_col();
				});

				function hide_charts_col() {
					$('#content .main_col').stop().animate({width: '80%'}, 400);
					$('#content .right_col').stop().animate({right: '-30%'}, 400);
					$('#responseText').animate({width: '100%'}, 400);
				}
			});

			$('input#s').keyup(function(event) {
				event.preventDefault();
				if(event.keyCode == 32) {
					//setTimeout(500);
					var data = $.trim($(this).val());
					if ( data!='')
					{	
						data = convertU2T(data);
						var request = {
							keyword: data
						}
						$.ajax({
							url: 'functions.php',
							type: 'POST',
							data: request,
							
						})
						.done(function(response) {
							console.log(response);
							response = convertT2U(response);	
												
							$("ul#search-result").html(response);
						})
						.fail(function() {
							console.log("error");
						})
						.always(function() {
							console.log("complete");
						});
					}
				}
			});
		});

		</script>
		<script type="text/javascript" src="js/map.js"></script>

		<!--- 
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&language=en"></script> 
		<script src="wms.js"></script>
		<script src="map-functions.js"></script>-->
		
		<!-- Openlayers -->
		<!--<script type="text/javascript" src="http://localhost:8080/geoserver/openlayers/OpenLayers.js"></script>-->
		

		<!-- Google Charts
		<script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		// Load the Visualization API and the piechart package.
		google.load('visualization', '1.0', {'packages':['corechart']});

		// Set a callback to run when the Google Visualization API is loaded.
		google.setOnLoadCallback(drawChart);

		// Callback that creates and populates a data table, 
		// instantiates the pie chart, passes in the data and
		// draws it.
		function drawChart() {
			// Create the data table.
			var data = new google.visualization.DataTable();
			data.addColumn('string', 'Topping');
			data.addColumn('number', 'Slices');
			data.addRows([
				['Mushrooms', 3],
				['Onions', 1],
				['Olives', 1], 
				['Zucchini', 1],
				['Pepperoni', 2]
			]);

			// Set chart options
			var options = {
				'title': 'How Much Pizza I Ate Last Night'
			};

			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
			chart.draw(data, options);
		}
		</script>-->
		
		<!-- Search tool -->
		<script src="js/vietuni.js" type='text/javascript'></script>
		<script src="js/vumods.js" type='text/javascript'></script>
		<script src="js/vumaps.js" type='text/javascript'></script>
		<script src="js/vumaps2.js" type='text/javascript'></script>


		<script type="text/javascript">
	function convertU2T(xau)
	{
		var srcid1 = "UNICODE";
		var srcmap1 = initCharMap(srcid1);
		var destmap1 = initCharMap(parseMapID("TCVN-3"));
		var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
		return xau1;
	}
	function convertT2U(xau)
	{
		var srcid1 = "TCVN-3";
		var srcmap1 = initCharMap(srcid1);
		var destmap1 = initCharMap(parseMapID("UNICODE"));
		var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
		return xau1;
	}

</script>
	</div>
</body>
</html>