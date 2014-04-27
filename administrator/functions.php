<?php 
/* Access */
function is_admin() {
	@session_start();
	if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) 
		return true;
	else return false;
}

function is_logged_in() {
	@session_start();
	if (isset($_SESSION['authorized']) && $_SESSION['authorized'] == true) 
		return true;
	else return false;
}

function curPageURL() {
	$pageURL = 'http';
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	}
	else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

/* Menu administrator */

/* Index administrator */
function getNumberObject($obj_type) {
	include_once('includes/databases.php');

	$selects = array('COUNT(*)');
	$wheres = array('type' => $obj_type);
	$rows = viewTable('fimo', 'object', $selects, $wheres);

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row[0];
	}
	return false;
}

function getNumberUser() {
	$selects = array('COUNT(*)');
	$rows = viewTable('fimo', 'users', $selects);
	if($rows === false) return false;

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row[0];
	}

	return false;
}

/* Edit administrator */
function addNewForm($obj_type) { ?>
	<div id="object">
		<div class="grid-1">
			<div class="container">
				<h3 class="div-title">Add new <?php echo $obj_type; ?></h3>
				<form class="grid-4 add-new-object" method="POST">
					<label class="grid-4" for="name">Name<span class="required"></span></label>
					<input class="grid-4 has-border has-border-radius" type="text" name="name" id="name" required>
					<label class="grid-4" for="slug">Slug</label>
					<input class="grid-4 has-border has-border-radius" type="text" name="slug" id="slug">
					<input type="hidden" name="type" id="type" value="<?php echo $obj_type; ?>">
					<?php if($obj_type == 'layer'): ?>
					<label class="grid-4" for="workspace">Workspace<span class="required"></span></label>
					<select name="workspace" id="workspace" class="grid-4 has-border has-border-radius" required>
						<option value=""></option><?php $rows = getObjects('workspace'); 
						if($rows):
							$i = 0;
							while ($row = pg_fetch_array($rows)): ?>
							<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
						<?php endwhile; endif; ?>
					</select>
					<label class="grid-4" for="shpfile">Shapefile (.shp)<span class="required"></span></label>
					<input class="grid-4 has-border has-border-radius" type="file" name="shpfile" id="shpfile" required accept=".shp">
					<label for="publish" class="grid-4">Publish</label>
					<select name="publish" id="publish" class="grid-4 has-border has-border-radius">
						<option value="publish">Publish</option>
						<option value="unpublish">Unpublish</option>
					</select>
					<?php endif; ?>
					<label class="grid-4" for="description">Description</label>
					<textarea class="grid-4 has-border has-border-radius" name="description" id="description" rows="10" style="resize: none;"></textarea>
					<input class="button has-border-radius" type="submit" value="Create">
				</form>
			</div>
		</div>
		<div class="grid-3">
			<div class="container">
				<h3 class="div-title">Lists of <?php echo $obj_type; ?></h3>
				<form class="grid-4 list-object" method="POST">
					<div class="grid-4">
						<select name="action" id="action" class="grid-1-4 has-border has-border-radius">
							<option value=""></option>
							<option value="publish">Publish</option>
							<option value="delete">Delete</option>
						</select>
						<input type="hidden" name="object_type" value="<?php echo $obj_type; ?>">
						<input type="submit" value="Apply" class="button fl has-border-radius" style="padding: 7px 10px 6px; margin-left: 10px;">
					</div>
					<div class="grid-4 table">
						<div class="grid-4 thead has-border">
							<div class="grid-1-16"><input type="checkbox" name="select-all" id="select-all"></div>
							<div class="grid-3-16"><p>ID</p></div>
							<div class="grid-1-4"><p>Name</p></div>
							<div class="grid-1-4"><p>Slug</p></div>
							<div class="grid-1-4"><p>Description</p></div>
						</div>
						<div class="grid-4 tbody">
						<?php $rows = getObjects($obj_type); 
						if($rows) {
							$i = 0;
							while ($row = pg_fetch_array($rows)) { ?>
								<div class="grid-4 row">
									<div class="grid-1-16"><input type="checkbox" name="<?php echo $obj_type . '-' . $row['id']; ?>" id="select[<?php echo $i++; ?>]"></div>
									<div class="grid-3-16"><?php echo $row['id'] ?></div>
									<div class="grid-1-4"><?php echo $row['name'] ?></div>
									<div class="grid-1-4"><?php echo $row['slug'] ?><input type="hidden" name="slug" value="<?php echo $row['slug'] ?>"></div>
									<div class="grid-1-4"><?php echo @$row['desc'] ?></div>
								</div> 
							<?php }
						}
						else { ?>
							<div class="grid-4 row">
								<p>No object found</p>
							</div>
						<?php } ?>
						</div>
						<div class="grid-4 tfoot has-border">
							<ul class="fr">
								<li class="fl current-item"><a href="#">1</a></li>
								<li class="fl"><a href="#">2</a></li>
								<li class="fl"><a href="#">3</a></li>
								<li class="fl"><a href="#">4</a></li>
							</ul>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php
}

function getObjects($obj_type) {
	include_once('includes/databases.php');

	$selects = array( 'id', 'name', 'slug', 'workspace', 'description' );
	$wheres = array(
		'type' => $obj_type,
		'publish' => 1
	);
	$rows = viewTable('fimo', 'object', $selects, $wheres);
	if($rows === false) return false;

	if (pg_num_rows($rows) >= 1)
		return $rows;
	return false;
}

/* Ajax call PHP functions */
if(isset($_POST['action']) && !empty($_POST['action'])) {
	$action = $_POST['action'];
	switch($action) {
		case 'submit_add_new_object' : addNewObject($_POST['data']); break;
		case 'submit_delete_object' : deleteObject($_POST['data']); break;
	}
}
/* For Ajax call */
function addNewObject($datas) {
	$name;
	$slug;
	$type;
	$desc;
	$workspace;
	foreach ($datas as $data) {
		if($data['name'] == 'name') $name = $data['value'];
		if($data['name'] == 'slug') $slug = $data['value'];
		if($data['name'] == 'type') $type = $data['value'];
		if($data['name'] == 'description') $desc = $data['value'];
		$workspace =(isset($data['workspace'])) ? $data['workspace'] : 0;
	}

	include_once('includes/databases.php');

	$result = createDB($slug);
	if($result) {
		$result_1 = commentDB($slug, $desc);
		if($result_1) {
			$args = array(
				'name' => $name,
				'slug' => $slug,
				'type' => $type,
				'workspace' => $workspace,
				'description' => $desc
			);
			$result_2 = insertTable('fimo', 'object', $args);
			if($result_2) {
				echo 'success';
			}
			else echo 'fail_insert_tb';
		}
		else echo 'fail_comment';
	}
	else echo 'fail_create_db';
}

function deleteObject($datas) {
	$ids = array();
	$slugs = array();
	$obj_type = '';
	$check = '';
	for ($i=0; $i < sizeof($datas); $i++) { 
		if($datas[$i]['name'] != 'action' && $datas[$i]['name'] != 'select-all' && $datas[$i]['name'] != 'slug' && $datas[$i]['name'] != 'object_type') {
			array_push($ids, substr($datas[$i]['name'], strpos($datas[$i]['name'], '-')+1));
		}
		if($datas[$i]['name'] == 'slug') {
			array_push($slugs, $datas[$i]['value']);
		}
		if($datas[$i]['name'] == 'object_type')
			$obj_type = $datas[$i]['value'];
	}

	include_once('includes/databases.php');

	foreach ($slugs as $i => $val) {
		$wheres = array('id' => $ids[$i]);
		$result_del_obj = dropRecords('fimo', 'object', $wheres);
		if($result_del_obj) {
			if($obj_type == 'workspace') {
				$result_del_db = dropDB($val);
				if($result_del_db)
					$check = 'success';
				else {
					$check = 'fail_del_db';
					break;
				}
			}
			else $check = 'success';
		}
		else {
			$check = 'fail_del_obj';
			break;
		}
	}
	
	echo $check;
}

/* String */
?>