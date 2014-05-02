<?php session_start(); 
include_once('functions.php');
include_once('includes/users.php');
include_once('includes/settings.php');

$url = (isset($_GET['redirect_to'])) ? $_GET['redirect_to'] : 'index.php';
/* Login */
if(isset($_POST['lg_u']) && isset($_POST['lg_p'])) {
	login($_POST['lg_u'], $_POST['lg_p'], $url);
}
/* Logout */
if(isset($_GET['action']) && $_GET['action'] == 'logout')
	logout(); 
/* Register */
/*if(isset($_POST['rg_u']) && isset($_POST['rg_p']) && isset($_POST['rg_e'])) {
	$result = register($_POST['rg_u'], $_POST['rg_p'], $_POST['rg_e']);
	if($result) {
		$_SESSION['register_error'] = '';
		login($_POST['rg_u'], $_POST['rg_p'], 'index.php');
	}
	else {
		$error['register_error'] = 'Something are wrong... Register failed!';
	}
}*/ ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | Login  | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<div id="login_wrapper">
		<div id="login_box" class="login_box">
			<div class="login_box_container fl">
				<h1 style="text-align: center;">Hệ thống lưu trữ, quản lý dữ liệu không gian bằng<br> PHP và PostgreSQL</h1>
				<p><i>Ngoc Tung Vu - Khanh Toan Tran</i></p>
				<p class="login_error" style="margin-bottom: 30px;"><?php echo @$_SESSION['login_error']; ?></p>
				<h3 style="text-align: center;">Login</h3>
				<form id="login_form" method="POST" action="login.php?redirect_to=<?php echo urlencode($url); ?>" class="login_form fl">
					<p><input type="text" name="lg_u" id="lg_u" placeholder="Enter your username..." required></p>
					<p><input type="password" name="lg_p" id="lg_p" placeholder="Enter your password..." required></p>
					<!-- <label for="lg_remember">Remember me </label><input type="checkbox" name="lg_remember" id="lg_remember"> -->
					<p><input type="submit" value="Login"></p>
				</form>
				<p><a href="#">Forgot password?</a><?php if(getOption('anyone_can_register') == 1) { ?> | <a id="redirect_register" href="javascript:void(0);">Register</a><?php } ?></p>
			</div>
		</div>
		<div id="register_box" class="login_box hidden">
			<div class="login_box_container fl">
				<h3 style="text-align: center;">Register</h3>
				<p><i>It's free!</i></p>
				<p class="register_error" style="margin-bottom: 30px; color: red;"><?php echo @$error['register_error']; ?></p>
				<form id="register_form" method="POST" action="login.php" class="login_form fl">
					<p><input type="text" name="rg_u" id="rg_u" placeholder="Enter your username..." required></p>
					<p><input type="email" name="rg_e" id="rg_e" placeholder="Enter your email..." required></p>
					<p><input type="password" name="rg_p" id="rg_p" placeholder="Enter your password..." required></p>
					<p><input type="password" name="rg_rep" id="rg_rep" placeholder="Re-enter your password..." required></p>
					<p style="font-size: 11px; color: #ababab;">Bạn đồng ý với <a href="#">điều khoản sử dụng và cam kết</a> của chúng tôi bằng cách đăng ký. </p>
					<p><input type="submit" value="Register"></p>
				</form>
				<p><a id="redirect_login" href="javascript:void(0);">Back to Login</a></p>
			</div>
		</div>
	</div>
</body>
</html>