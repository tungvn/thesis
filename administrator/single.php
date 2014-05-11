<?php session_start(); 
include_once('functions.php');
include_once('includes/settings.php');?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | <?php echo ucfirst($_GET['obj']); ?> - <?php echo $_GET[$_GET['obj']]; ?> | Administrator | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<?php if(!isset($_SESSION['authorized'])): ?>
		<?php $link = urlencode(curPageURL());
		header('Location: login.php?redirect_to=' . $link); ?>
	<?php else: 
	if(isset($_POST['submit'])) {

	} ?>
	<div id="wrapper">
		<div id="header" class="fl clearfix">
			<div class="logo fl">
				<a href="#"><img src="#" alt="logo"></a>
			</div>
			<ul class="top_menu fl">
				<li><a href="<?php echo getOption('administrator_url'); ?>">Home</a></li>
				<li><a href="<?php echo getOption('base_url'). '/map.php'; ?>">Visit Map</a></li>
				<!-- <li><a href="#">Option 1</a></li>
				<li><a href="#">Option 2</a></li> -->
			</ul>
			<div class="user_menu fr">
				<a href="#"><?php echo getCurrentUserID(); ?></a>
				<a href="login.php?action=logout">Log out</a>
			</div>
			<form class="search_box fr">
				<input class="has-border-radius" type="text" name="s" id="s" placeholder="Enter your keywords" autocomplete="on" style="width: 300px;">
				<input class="hidden" type="submit" value="Search">
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
					<?php if(isset($_GET['obj']) && isset($_GET[$_GET['obj']])): 
					$obj_type = $_GET['obj']; 
					$obj = $_GET[$_GET['obj']];
					$selects = array('name', 'slug');
					$wheres = array('id' => $obj);
					$rows = getRecords(DBNAME, 'object', $selects, $wheres);
					if($rows && pg_num_rows($rows) == 1)
						$row = pg_fetch_array($rows); ?>
					<h2 class="div-title"><a href="edit.php?obj=<?php echo $obj_type; ?>"><?php echo ucfirst($obj_type) . '</a> > <span style="color: #1155ff;">' . $row['name'] . '</span>'; ?></h2>
					<div id="notification" class="grid-4">
						<div class="container">
							<span class="info fl hidden"></span>
							<span class="error fl hidden"></span>
							<span class="warning fl hidden"></span>
							<p><?php echo @$_POST['notification']; ?></p>
						</div>
					</div>
					<?php if((is_admin() || is_moder() || is_editor()) && ($obj_type == 'workspace' || $obj_type == 'layer'))
						single($obj_type, $obj);
					elseif($obj_type == 'users')
						single($obj_type, $obj);
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