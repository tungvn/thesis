<?php session_start(); 
include_once('functions.php');?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Fimo 2014 | Administrator</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
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
				<li><a href="#">Home</a></li>
				<li><a href="#">Option 1</a></li>
				<li><a href="#">Option 2</a></li>
			</ul>
			<div class="user_menu fr">
				<a href="#">admin</a>
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
						<li><a href="edit.php?obj=user">Users</a></li>
					</ul>
					<a href="javascript:void(0);"></a>
					<p class="footer">&copy; Copyright by TungVN 2014</p>
				</div>
			</div>
			<div class="main_body grid-4">
				<div class="container">
					<h2 class="div-title">Dashboard</h2>
					<div id="notification" class="grid-4 hidden">
						<div class="container has-border has-border-radius">
							<span class="error fl"></span>
							<span class="warning fl hidden"></span>
							<p>Hello</p>
						</div>
					</div>
					<?php if(is_admin()): ?>
						
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</body>
</html>