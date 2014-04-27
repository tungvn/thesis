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
	<div id="login_wrapper">
		<div class="login_box">
			<div class="login_box_container fl">
				<h1>Back_end of TungVN</h1>
				<p class="login_error"><?php echo @$_SESSION['login_error']; ?></p>
				<form method="POST" action="login.php" class="login_form fl">
					<p><input type="text" name="lg_u" id="lg_u" placeholder="Enter your username..." required></p>
					<p><input type="password" name="lg_p" id="lg_p" placeholder="Enter your password..." required></p>
					<!-- <label for="lg_remember">Remember me </label><input type="checkbox" name="lg_remember" id="lg_remember"> -->
					<p><input type="submit" value="Login"></p>
				</form>
				<p><a href="#">Forgot password?</a></p>
			</div>
		</div>
	</div>
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