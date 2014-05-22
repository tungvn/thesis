<?php session_start(); 
require_once('functions.php');?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | Dashboard | Administrator | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="js/vietuni.js" type='text/javascript'></script>
	<script src="js/vumods.js" type='text/javascript'></script>
	<script src="js/vumaps.js" type='text/javascript'></script>
	<script src="js/vumaps2.js" type='text/javascript'></script>
	<script src="js/main.js"></script>
</head>
<body>
	<?php if(!isset($_SESSION['authorized'])): ?>
		<?php $link = urlencode(curPageURL());
		header('Location: login.php?redirect_to=' . $link); ?>
	<?php else: ?>
	<div id="wrapper">
		<div id="header" class="fl clearfix">
			<div class="logo fl">
				<a href="#"><img src="#" alt="logo"></a>
			</div>
			<ul class="top_menu fl">
				<li><a href="<?php echo getOption('administrator_url'); ?>">Home</a></li>
				<li><a href="<?php echo getOption('base_url'). '/map.php'; ?>" target="_blank">Visit Map</a></li>
				<!-- <li><a href="#">Option 2</a></li> -->
			</ul>
			<div class="user_menu fr">
				<a href="#"><?php echo getCurrentUserID(); ?></a>
				<a href="login.php?action=logout">Log out</a>
			</div>
			<form class="search_box fr" action="search.php" method="POST">
				<div class="relative">
					<input class="has-border-radius" type="text" name="s" id="s" placeholder="Enter your keywords" autocomplete="on" style="width: 300px;">
					<div class="advanced_search map_option_block hidden">
						<?php $selects = array('id', 'name', 'slug');
						$wheres = array('type' => 'workspace');
						$wps = getRecords(DBNAME, 'object', $selects, $wheres);

						if($wps && pg_num_rows($wps) > 0) { 
							echo '<h3 class="div-title">Choose Layers to Search</h3>';
							while($wp = pg_fetch_array($wps)) {
								$num = 0;
								$selects = array('id', 'name', 'slug');
								$wheres = array('workspace' => $wp['id']);
								$rows = getRecords(DBNAME, 'object', $selects, $wheres);

								if($rows && pg_num_rows($rows) > 0) { 
									echo '<div class="workspace" for=' . $wp['slug'] . '>';
									if($num == 0) {
										echo '<p class="div-title">' . ucfirst($wp['name']) . '</p>';
										echo '<div class="the-content" style="padding-left: 20px;">';
										$num++;
									}
									$i = 0;
									while ($row = pg_fetch_array($rows)) { ?>
									<p>
										<input type="checkbox" name="layer-<?php echo $row['id']; ?>" id="map_layer_option[<?php echo $wp['id']; ?>][<?php echo $i; ?>]" value="<?php echo $wp['slug'] . ':' . $row['slug']; ?>">
										<label for="map_layer_option[<?php echo $wp['id']; ?>][<?php echo $i++; ?>]"><?php echo $row['name']; ?></label>
									</p>
									<?php }
									echo '</div></div>';
								}
							}
						}
						else {
							echo '<div class="div-title">Not found any objects to search! Add new object at <a href="edit.php?obj=workspace">here</a> or <a href="edit.php?obj=layer">here</a></div>';
						} ?>
						<span class="close"><a href="javascript:void(0);">Close</a></span>
					</div>
				</div>
				<input class="hidden" type="submit" value="Search" name="submit">
				<!-- <a href="#">Advance Search</a> -->
			</form>
		</div>
		<div id="body" class="fl clearfix">
			<div class="left_menu fl">
				<div class="container" style="padding: 0px;">
					<ul>
						<li><a href="index.php">Dashboard</a></li>
						<li><a href="edit.php?obj=workspace">Workspaces</a></li>
						<li><a href="edit.php?obj=layer">Layers</a></li>
						<li><a href="edit.php?obj=users">Users</a></li>
						<li><a href="settings.php">Settings</a></li>
					</ul>
					<a href="javascript:void(0);"></a>
					<p class="footer">&copy; Copyright by TungVN 2014</p>
				</div>
			</div>
			<div class="main_body grid-4">
				<div class="container">
					<h2 class="div-title">Search Result</h2>
					<div id="notification" class="grid-4">
						<div class="container">
							<span class="info fl hidden"></span>
							<span class="error fl hidden"></span>
							<span class="warning fl hidden"></span>
							<p><?php echo @$_POST['notification']; ?></p>
						</div>
					</div>
					<!-- search result -->
					<?php $check = 0;
					$keyword = '';
					$data = array();
					$wp = array();
					if(isset($_POST['submit'])) {
						while (list($name, $value) = each($_POST)) {
							if($name == 's') $keyword = $value;
							$layers = array();
							if(strpos($name, 'layer') !== false) {
								$temp_wp_name = substr($value, 0, strpos($value, ':'));
								$temp_layer_name = substr($value, strpos($value, ':')+1);
								if(empty($wp) || !in_array($temp_wp_name, $wp)) {
									array_push($wp, $temp_wp_name);
									$data[$temp_wp_name] = array();
								}
								array_push($data[$temp_wp_name], $temp_layer_name);
							}
						}
						unset($wp);
						$check = 1;
						searchTools($data, $keyword);
					} ?>
					<div class="search_result">
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</body>
<script>
/*function convertT2U(xau) {
	var srcid1 = "TCVN-3";
	var srcmap1 = initCharMap(srcid1);
	var destmap1 = initCharMap(parseMapID("UNICODE"));
	var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
	return xau1;
}
function convertU2T(xau) {
	var srcid1 = "UNICODE";
	var srcmap1 = initCharMap(srcid1);
	var destmap1 = initCharMap(parseMapID("TCVN-3"));
	var xau1 = srcmap1.convertTxtTo( xau, destmap1 )	;
	return xau1;
}
if(<?php echo $check; ?> == 1) {
	var request = {
		keyword: convertU2T('<?php echo $keyword ?>'),
		group: '<?php echo json_encode($data); ?>',
		action: 'complete_search_tool',
		json: true
	}
	$.ajax({
		type: 'POST',
		data: request
	})
	.done(function(response) {
		$('#body .container .search_result').html(response);
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
}*/
</script>
</html>