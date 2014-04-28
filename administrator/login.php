<?php session_start(); 
include_once('functions.php');
include_once('includes/users.php');

$url = (isset($_GET['redirect_to'])) ? $_GET['redirect_to'] : 'index.php';
/* Login */
if(isset($_POST['lg_u']) && isset($_POST['lg_p'])) {
	login($_POST['lg_u'], $_POST['lg_p'], $url);
}
/* Logout */
if(isset($_GET['action']) && $_GET['action'] == 'logout')
	logout(); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | Login  | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<div id="login_wrapper">
		<div class="login_box">
			<div class="login_box_container fl">
				<h1>Back_end of TungVN</h1>
				<p class="login_error"><?php echo @$_SESSION['login_error']; ?></p>
				<form method="POST" action="login.php?redirect_to=<?php echo urlencode($url); ?>" class="login_form fl">
					<p><input type="text" name="lg_u" id="lg_u" placeholder="Enter your username..." required></p>
					<p><input type="password" name="lg_p" id="lg_p" placeholder="Enter your password..." required></p>
					<!-- <label for="lg_remember">Remember me </label><input type="checkbox" name="lg_remember" id="lg_remember"> -->
					<p><input type="submit" value="Login"></p>
				</form>
				<p><a href="#">Forgot password?</a></p>
			</div>
		</div>
	</div>
</body>
</html>