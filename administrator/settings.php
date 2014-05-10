<?php session_start();
/*
settings.php

All settings of system
*/
include_once('functions.php');
include_once('includes/settings.php'); ?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="author" content="TungVN - Ngoc Tung Vu">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Thesis 2014 | Settings | Administrator | Fimo Center</title>
	<link rel="stylesheet" href="css/style.css">
	<!--<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>-->
	<script src="../js/jquery-1.9.1.js"></script>
	<script src="js/main.js"></script>
</head>
<body>
	<?php if(!isset($_SESSION['authorized'])):
		$link = urlencode(curPageURL());
		header('Location: login.php?redirect_to=' . $link);
	else:
		$selects = array('id', 'name', 'value');
		$rows = getRecords(DBNAME, 'settings', $selects);
		if(isset($_POST['setting']) && isset($_POST['desc'])) {
			if($rows) {
				$i = 0;
				while ($row = pg_fetch_array($rows)) {
					$result = updateOption($row['name'], $_POST['setting'][$i][$row['name']], $_POST['desc'][$i++][$row['name']]);
					if(!$result) {
						break;
					}
				}
			}
		}
		if(isset($_POST['add_new_option_name'])) {
			$option_value = isset($_POST['add_new_option_value']) ? $_POST['add_new_option_value'] : '';
			$option_desc = isset($_POST['add_new_option_desc']) ? $_POST['add_new_option_desc'] : '';
			$result = registerOption($_POST['add_new_option_name'], $option_value, $option_desc);
		} ?>
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
					<a href="javascript:void(0);"></a>
					<p class="footer">&copy; Copyright by TungVN 2014</p>
				</div>
			</div>
			<div class="main_body grid-4">
				<div class="container">
					<h2 class="div-title">Settings</h2>
					<div id="notification" class="grid-4">
						<div class="container">
							<span class="info fl hidden"></span>
							<span class="error fl hidden"></span>
							<span class="warning fl hidden"></span>
							<p><?php echo @$_POST['notification']; ?></p>
						</div>
					</div>
					<?php if(is_admin() || is_moder()): ?>
					<form id="setting" class="grid-4" method="POST" action="settings.php">
					<?php else: ?>
					<div id="setting" class="grid-4">
					<?php endif; ?>
						<table class="grid-4">
							<?php $readonly = (is_admin() || is_moder()) ? ' ' : 'readonly';
							$selects = array('id', 'name', 'value', 'description');
							$rows = getRecords(DBNAME, 'settings', $selects);
							if($rows):
							if(pg_num_rows($rows) > 0): $i = 0; while($row = pg_fetch_array($rows)): ?>
							<tr>
								<td class="grid-1"><label for="setting_<?php echo $row['name']; ?>"><strong><?php echo $row['name']; ?></strong></label></td>
								<td class="grid-1">
									<input type="text" class="has-border has-border-radius" id="setting_<?php echo $row['name']; ?>" name="setting[<?php echo $i; ?>][<?php echo $row['name']; ?>]" value="<?php echo $row['value']; ?>" <?php echo $readonly; ?>>
								</td>
								<td class="grid-1"><input type="text" id="desc_<?php echo $row['name']; ?>" name="desc[<?php echo $i++; ?>][<?php echo $row['name'] ?>]" value="<?php echo $row['description']; ?>" class="has-border has-border-radius" style="width: 100%" <?php echo $readonly; ?>></td>
							</tr>
							<?php endwhile; endif; endif; ?>
						</table>
						<?php if(is_admin() || is_moder()): ?>
						<div class="grid-4" style="padding: 20px 0 0 20px;">
							<input id="save_setting" type="submit" value="Save" class="button has-border-radius clearfix">
							<?php if(is_admin()): ?>
							<input type="button" value="Add New Option" id="add_new_option" class="button has-border-radius clearfix">
							<?php endif; ?>
						</div>
						<?php endif; ?>
					<?php if(is_admin() || is_moder()): ?>
					</form>
					<?php else: ?>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</body>
</html>
