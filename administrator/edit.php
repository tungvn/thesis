<?php session_start(); 
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/includes/settings.php'); ?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | <?php echo ucfirst($_GET['obj']); ?> | Administrator | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<?php // Add new object
	if(isset($_POST['submit'])) {
		$slug = vn_str_filter($_POST['name']);
		$args = array(
			'name' => $_POST['name'],
			'slug' => $slug,
			'type' => $_POST['type'],
			'publish' => $_POST['publish'],
			'description' => $_POST['description']
		);
		if($_POST['type'] = 'layer') {
			$args['workspace'] = $_POST['workspace'];
			$args['path'] = array();

			$allowedExts = array('dbf', 'prj', 'sbn', 'sbx', 'shp', 'shx');
			$dir = $_SERVER['DOCUMENT_ROOT'].'/github/thesis/uploads/';

			foreach ($_FILES['shpfile']['name'] as $key => $file) {
				$temp = explode('.', $_FILES['shpfile']['name'][$key]);
				$extension = end($temp);
				if(is_uploaded_file($_FILES['shpfile']['tmp_name'][$key]) && in_array($extension, $allowedExts)) {
					// Save file
					move_uploaded_file($_FILES['shpfile']['tmp_name'][$key], $dir . $_FILES['shpfile']['name'][$key]);
					if($extension == 'shp')
						$args['shpfile'] = $dir . $_FILES['shpfile']['name'][$key];
				}
			}
		}
		addNewObject($args);
	} ?>
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
				<!-- <li><a href="#">Option 1</a></li>
				<li><a href="#">Option 2</a></li> -->
			</ul>
			<div class="user_menu fr">
				<a href="#"><?php echo getCurrentUserID(); ?></a>
				<a href="login.php?action=logout">Log out</a>
			</div>
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
					<p class="footer">&copy; Copyright by TungVN 2014</p>
				</div>
			</div>
			<div class="main_body grid-4">
				<div class="container">
					<?php if(isset($_GET['obj'])): ?>
					<h2 class="div-title"><?php echo ucfirst($_GET['obj']); ?></h2>
					<div id="notification" class="grid-4">
						<div class="container">
							<span class="info fl hidden"></span>
							<span class="error fl hidden"></span>
							<span class="warning fl hidden"></span>
							<p><?php echo @$_POST['notification']; ?></p>
						</div>
					</div>
					<?php if(isset($_GET['obj']) && ($_GET['obj'] == 'workspace' || $_GET['obj'] == 'layer'))
						edit($_GET['obj']);
					elseif($_GET['obj'] == 'users')
						edit($_GET['obj']);
					else
						echo 'Access denied!';
					endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</body>
</html>