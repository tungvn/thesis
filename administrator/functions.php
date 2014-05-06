<?php 
/* Access */
/*
Order Role from Highest to Lowest
	1. administrator -- full setting
	2. moderator -- full setting, expect any users' setting
	3. editor -- full editing available workspaces, layers information 
					but can NOT create or remove ones
	4. publisher -- only publish available infomations, expect users verify
	5. subscriber -- only view infomations, can NOT action with anything
*/
// Include databases.php -- all functions to action with postgresql database
include_once $_SERVER['DOCUMENT_ROOT'].'/github/thesis/administrator/includes/databases.php';


function is_admin() {
	@session_start();
	if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == true) 
		return true;
	else return false;
}

function is_moder() {
	@session_start();
	if (isset($_SESSION['is_moder']) && $_SESSION['is_moder'] == true) 
		return true;
	else return false;
}

function is_editor() {
	@session_start();
	if (isset($_SESSION['is_editor']) && $_SESSION['is_editor'] == true) 
		return true;
	else return false;
}

function is_publisher() {
	@session_start();
	if (isset($_SESSION['is_publisher']) && $_SESSION['is_publisher'] == true) 
		return true;
	else return false;
}

function is_subscriber() {
	@session_start();
	if (isset($_SESSION['is_subscriber']) && $_SESSION['is_subscriber'] == true) 
		return true;
	else return false;
}

function is_logged_in() {
	@session_start();
	if (isset($_SESSION['authorized']) && $_SESSION['authorized'] == true) 
		return true;
	else return false;
}

/* Current user */
function getCurrentUserID() {
	@session_start();
	$current_user_id = substr($_SESSION['user_id'], strpos($_SESSION['user_id'], '-')+1);
	return $current_user_id;
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
	$selects = array('COUNT(*)');
	$wheres = array('type' => $obj_type);
	$rows = getRecords(DBNAME, 'object', $selects, $wheres);

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row[0];
	}
	return false;
}

function getNumberUser() {
	$selects = array('COUNT(*)');
	$rows = getRecords(DBNAME, 'users', $selects);
	if($rows === false) return false;

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row[0];
	}

	return false;
}
/*-------------------------------------edit.php-------------------------------------------------*/
/* Edit administrator */
function addNewForm($obj_type) { ?>
	<div class="container">
		<h3 class="div-title">Add new <?php echo $obj_type; ?></h3>
		<form class="grid-4 add-new-object" method="POST">
			<label class="grid-4" for="name">Name<span class="required"></span></label>
			<input class="grid-4 has-border has-border-radius" type="text" name="name" id="name" required>
			<input class="grid-4 has-border has-border-radius" type="hidden" name="slug" id="slug">
			<input type="hidden" name="type" id="type" value="<?php echo $obj_type; ?>">
			<?php if($obj_type == 'layer'): ?>
			<label class="grid-4" for="workspace">Workspace<span class="required"></span></label>
			<select name="workspace" id="workspace" class="grid-4 has-border has-border-radius" required />
				<?php $rows = getObjects('workspace'); 
				if($rows):
					$i = 0;
					while ($row = pg_fetch_array($rows)): ?>
					<option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?></option>
				<?php endwhile; endif; ?>
			</select>
			<label class="grid-4" for="shpfile">Shapefile (.shp)<span class="required"></span></label>
			<input class="grid-4 has-border has-border-radius" type="file" name="shpfile" id="shpfile" accept=".shp" required>
			<?php endif; ?>
			<label for="publish" class="grid-4">Publish</label>
			<select name="publish" id="publish" class="grid-4 has-border has-border-radius">
				<option value="1">Publish</option>
				<option value="0" selected>Unpublish</option>
			</select>
			<label class="grid-4" for="description">Description</label>
			<textarea class="grid-4 has-border has-border-radius" name="description" id="description" rows="10" style="resize: none;"></textarea>
			<input class="button has-border-radius" type="submit" value="Create">
		</form>
	</div>
	<?php
}
function listObject($obj_type) { ?>
	<div class="container">
		<h3 class="div-title">Lists of <?php echo $obj_type; ?></h3>
		<form class="grid-4 list-object" method="POST">
			<div class="grid-4">
				<?php if(is_admin() || is_moder()): ?>
				<select name="action" id="action" class="grid-1-4 has-border has-border-radius">
					<option value="">Choose action</option>
					<option value="delete">Delete</option>
				</select>
				<input type="hidden" name="object_type" value="<?php echo $obj_type; ?>">
				<input type="submit" value="Apply" class="button fl has-border-radius" style="padding: 7px 10px 6px; margin-left: 10px;">
				<?php endif; ?>
			</div>
			<div class="grid-4 table">
				<div class="grid-4 thead has-border">
					<div class="grid-1-16"><input type="checkbox" name="select-all" id="select-all"></div>
					<div class="grid-3-16"><p>ID</p></div>
					<div class="grid-1-4"><p>Name</p></div>
					<div class="grid-1-4"><p>Description</p></div>
					<div class="grid-1-4"><p><?php echo ($obj_type == 'layer') ? 'Workspace' : 'Layers'; ?></p></div>
				</div>
				<div class="grid-4 tbody">
				<?php $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
				$rows = getObjects($obj_type, 10, ($paged-1)*10); 
				if($rows) {
					$i = 0;
					while ($row = pg_fetch_array($rows)) { ?>
						<div class="grid-4 row">
							<div class="grid-1-16"><input type="checkbox" name="<?php echo $obj_type . '-' . $row['id']; ?>" id="select[<?php echo $i++; ?>]"></div>
							<div class="grid-3-16"><a href="single.php?obj=<?php echo $obj_type; ?>&amp;<?php echo $obj_type . '=' .$row['id']; ?>"><?php echo $row['id'] ?></a></div>
							<div class="grid-1-4"><a href="single.php?obj=<?php echo $obj_type; ?>&amp;<?php echo $obj_type . '=' .$row['id']; ?>"><?php echo $row['name'] ?></a></div>
							<div class="grid-1-4"><?php echo ($row['description'] != '') ? $row['description'] : '&nbsp;'; ?></div>
							<div class="grid-1-4">
							<?php if($obj_type == 'layer'): 
							$selects = array('slug');
							$wheres = array('id' => $row['workspace']);
							$rows_wp = getRecords(DBNAME, 'object', $selects, $wheres);
							if($rows_wp && pg_num_rows($rows_wp) > 0) {
								$row_wp = pg_fetch_array($rows_wp);
							} ?>
								<a href="single.php?obj=workspace&amp;workspace=<?php echo $row['workspace']; ?>">
									<?php echo $row_wp['slug']; ?>
								</a>
							<?php elseif($obj_type == 'workspace'):
								$selects = array('id', 'slug');
								$wheres = array(
									'type' => 'layer',
									'workspace' => $row['id']
								); 
								$rows_inner = getRecords(DBNAME, 'object', $selects, $wheres); 
								if(pg_num_rows($rows_inner) > 0):
									$j = 0;
									while($row_inner = pg_fetch_array($rows_inner)): 
										echo ($j == 0) ? '' : ', '; ?>
									<a href="single.php?obj=layer&amp;layer=<?php echo $row_inner['id']; ?>">
										<?php echo $row_inner['slug']; ?>
									</a>
									<?php $j++; endwhile; endif; ?>
							<?php endif; ?>
							</div>
						</div> 
					<?php }
				}
				else { ?>
					<div class="grid-4 row">
						<p>No object found</p>
					</div>
				<?php } ?>
				</div>
				<?php $selects = array('COUNT(*)'); 
				$wheres = array('type' => $obj_type, 'publish' => 1);
				$rows = getRecords(DBNAME, 'object', $selects, $wheres);
				if($rows === false) return false;

				if (pg_num_rows($rows) == 1)
					$row = pg_fetch_array($rows);
				$sum = $row[0];
				$number_page = (int) ($sum / 10);

				if($number_page > 0): ?>
				<div id="pagination" class="grid-4 tfoot has-border">
					<ul class="fr">
						<?php for($i=1;$i <= $number_page+1;$i++):
						$current = ($i == $paged) ? 'current-item' : '';  ?>
						<li class="fl <?php echo $current; ?>"><a href="edit.php?obj=<?php echo $obj_type; ?>&amp;paged=<?php echo $i; ?>"><?php echo $i; ?></a></li>
						<?php endfor; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</form>
	</div>
	<?php
}

function listUsers($obj_type) { ?>
	<div class="container">
		<h3 class="div-title">Lists of <?php echo $obj_type; ?></h3>
		<form class="grid-4 list-object list-users" method="POST">
			<div class="grid-4">
				<?php if(is_admin() || is_moder()): ?>
				<select name="action" id="action" class="grid-1-4 has-border has-border-radius">
					<option value="">Choose action</option>
					<option value="delete">Delete</option>
				</select>
				<?php $selects = array('DISTINCT role');
				$verify = (isset($_GET['verify'])) ? $_GET['verify'] : 1;
				$wheres = array('verify' => $verify);
				$rows = getRecords(DBNAME, 'users', $selects, $wheres); ?>
				<select name="change_role" id="change_role" class="grid-1-4 has-border has-border-radius" style="margin-left: 10px;">
					<option value="">Change role to...</option><?php if($rows && pg_num_rows($rows) > 0): while($row = pg_fetch_array($rows)): ?>
					<option value="<?php echo $row['role']; ?>"><?php echo ucfirst($row['role']); ?></option>
					<?php endwhile; endif; ?>
				</select>
				<input type="submit" value="Apply" class="button fl has-border-radius" style="padding: 7px 10px 6px; margin-left: 10px;">
				<?php endif; ?>
			</div>
			<div class="grid-4 table">
				<div class="grid-4 thead has-border">
					<div class="grid-1-16"><input type="checkbox" name="select-all" id="select-all"></div>
					<div class="grid-3-16"><p>ID</p></div>
					<div class="grid-1-4"><p>Name</p></div>
					<div class="grid-1-4"><p>Email</p></div>
					<div class="grid-1-4"><p>Role</p></div>
				</div>
				<div class="grid-4 tbody">
				<?php $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;
				$selects = array('*'); 
				$wheres = array('verify' => 1);
				$rows = getRecords(DBNAME, 'users', $selects, $wheres, 10, ($paged-1)*10); 
				if($rows) {
					$i = 0;
					while ($row = pg_fetch_array($rows)) { ?>
					<div class="grid-4 row">
						<div class="grid-1-16"><input type="checkbox" name="<?php echo 'user-' . $row['id']; ?>" id="select-user[<?php echo $row['id']; ?>]"></div>
						<div class="grid-3-16"><p><?php echo $row['id']; ?></p></div>
						<div class="grid-1-4"><p><?php echo $row['username']; ?></p></div>
						<div class="grid-1-4"><p><?php echo $row['email']; ?></p></div>
						<div class="grid-1-4"><p><?php echo $row['role']; ?></p></div>
					</div>
					<?php } ?>
				<?php } ?>
				</div>
				<?php $selects = array('*'); 
				$wheres = array('verify' => 1);
				$rows = getRecords(DBNAME, 'users', $selects, $wheres);
				if($rows === false) return false;

				if (pg_num_rows($rows) == 1)
					$row = pg_fetch_array($rows);
				$sum = $row[0];
				$number_page = (int) ($sum / 10);

				if($number_page > 0): ?>
				<div id="pagination" class="grid-4 tfoot has-border">
					<ul class="fr">
						<?php for($i=1;$i <= $number_page+1;$i++):
						$current = ($i == $paged) ? 'current-item' : '';  ?>
						<li class="fl <?php echo $current; ?>"><a href="edit.php?obj=<?php echo $obj_type; ?>&amp;paged=<?php echo $i; ?>"><?php echo $i; ?></a></li>
						<?php endfor; ?>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</form>
	</div>
<?php
}

function edit($obj_type) { ?>
	<?php if($obj_type != 'users'): ?>
	<div id="object" class="fl" for="object">
		<?php if(is_admin() || is_moder()): ?>
		<div class="grid-1">
			<?php addNewForm($obj_type); ?>
		</div>
		<div class="grid-3">
			<?php listObject($obj_type); ?>
		</div>
		<?php elseif(is_editor()): ?>
		<div class="grid-4">
			<?php listObject($obj_type); ?>
		</div>
		<?php endif; ?>
	</div>
	<?php else: ?>
	<div id="object" class="fl" for="users">
	<?php if(is_admin()): ?>
		<div class="grid-4">
		<?php listUsers($obj_type); ?>
		</div>
	<?php endif; ?>
	</div>
	<?php endif;
}


function getObjects($obj_type, $limit = 99999, $offset = 0) {
	$selects = array( 'id', 'name', 'slug', 'workspace', 'description' );
	$wheres = array(
		'type' => $obj_type,
		'publish' => 1
	);
	$rows = getRecords(DBNAME, 'object', $selects, $wheres, $limit, $offset);
	if($rows === false) return false;

	if (pg_num_rows($rows) >= 1)
		return $rows;
	return false;
}

/*-------------------------------------edit.php-------------------------------------------------*/



/*-------------------------------------single.php-----------------------------------------------*/
function single($obj_type, $obj) {
	echo $obj_type . '<br>' .$obj;
}


/*-------------------------------------single.php-----------------------------------------------*/


/* Ajax call PHP functions */
if(isset($_POST['action']) && !empty($_POST['action'])) {
	$action = $_POST['action'];
	switch($action) {
		case 'submit_add_new_object' : addNewObject($_POST['data']); break;
		case 'submit_delete_object' : deleteObject($_POST['data']); break;
		case 'delete_users' : deleteUsers($_POST['data']); break;
		case 'change_role_users' : changeRole($_POST['data']); break;
	}
}
/* For Ajax call */
function addNewObject($datas) {
	$name;
	$slug;
	$type;
	$desc;
	$workspace;
	$publish;
	$shpfile;
	for($i=0; $i < sizeof($datas);$i++) {
		if($datas[$i]['name'] == 'name') $name = $datas[$i]['value'];
		if($datas[$i]['name'] == 'slug') $slug = $datas[$i]['value'];
		if($datas[$i]['name'] == 'type') $type = $datas[$i]['value'];
		if($datas[$i]['name'] == 'description') $desc = $datas[$i]['value'];
		if($datas[$i]['name'] == 'publish') $publish = $datas[$i]['value'];
		if(isset($type) && $type == 'layer') {
			if($datas[$i]['name'] == 'workspace') $workspace = $datas[$i]['value'];
			if($datas[$i]['name'] == 'shpfile') $shpfile = $datas[$i]['value'];
		}
	}

	if(isset($_FILES)) { ?>
	<script>
	alert(1);
	</script>
	<?php }

	if($type == 'workspace') {
		$result = createDB($slug);
		if($result) {
			$result_1 = createExtension($slug);
			if($result_1) {
				$args = array(
					'name' => $name,
					'slug' => $slug,
					'type' => $type,
					'description' => $desc,
					'publish' => $publish
				);
				$result_2 = insertRecords(DBNAME, 'object', $args);
				if($result_2) {
					echo 'success';
				}
				else echo 'fail_insert_tb';
			}
			else echo 'fail_comment';
		}
		else echo 'fail_create_db';
	}
	elseif($type == 'layer') {
		$selects = array('slug');
		$wheres = array('id' => 71);
		$wp_slugs = getRecords(DBNAME, 'object', $selects, $wheres);
		if($wp_slugs && pg_num_rows($wp_slugs) > 0)
			$wp_slug = pg_fetch_array($wp_slugs);
		$wp_slug = $wp_slug['slug'];

		$shp2pgsql = '"C:/Program Files/PostgreSQL/9.3/bin/shp2pgsql" -s 32448 -W LATIN1 -c -D -I '; 
		$psql = '"C:/Program Files/PostgreSQL/9.3/bin/psql" -d ' . $wp_slug . ' -U postgres ';
		$result = exec($shp2pgsql.$shpfile." | ".$psql);
		if($result == 'COMMIT') {
			$args = array(
				'name' => $name,
				'slug' => $slug,
				'type' => $type,
				'workspace' => $workspace,
				'description' => $desc,
				'publish' => $publish
			);
			$result_2 = insertRecords(DBNAME, 'object', $args);
			if($result_2) {
				echo 'success';
			}
			else echo 'fail_insert_tb';
		}
		else {
			echo 'fail_insert_tb';
		}
	}
}

function deleteObject($datas) {
	$ids = array();
	$slugs = array();
	$obj_type = '';
	$check = '';
	
	for ($i=0; $i < sizeof($datas); $i++) { 
		if($datas[$i]['name'] != 'action' && $datas[$i]['name'] != 'select-all' && $datas[$i]['name'] != 'object_type') {
			array_push($ids, substr($datas[$i]['name'], strpos($datas[$i]['name'], '-')+1));
		}
		if($datas[$i]['name'] == 'object_type')
			$obj_type = $datas[$i]['value'];
	}

	foreach ($ids as $i => $id) {
		$selects = array('slug');
		$wheres = array('id' => $id);
		$rows = getRecords(DBNAME, 'object', $selects, $wheres);
		if($rows === false) return false;

		if (pg_num_rows($rows) > 0) {
			while($row = pg_fetch_array($rows)) {
				array_push($slugs, $row['slug']);
			}
		}
	}

	foreach ($slugs as $i => $slug) {
		$wheres = array('id' => $ids[$i]);
		$result_del_obj = dropRecords(DBNAME, 'object', $wheres);
		if($result_del_obj) {
			if($obj_type == 'workspace') {
				$result_del_db = dropDB($slug);
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

function deleteUsers($datas) {
	$ids = array();
	$check = '';
	
	for ($i=0; $i < sizeof($datas); $i++) { 
		if($datas[$i]['name'] != 'action' && $datas[$i]['name'] != 'change_role' && $datas[$i]['name'] != 'select-all' && $datas[$i]['name'] != 'object_type') {
			array_push($ids, substr($datas[$i]['name'], strpos($datas[$i]['name'], '-')+1));
		}
	}

	foreach ($ids as $i => $id) {
		$wheres = array('id' => $id);
		$result_del_users = dropRecords(DBNAME, 'users', $wheres);
		if($result_del_users) {
			$check = 'success_del_users';
		}
		else {
			$check = 'fail_del_users';
			break;
		}
	}
	
	echo $check;
}

function changeRole($datas) {
	$ids = array();
	$change_to = '';
	$check = '';

	for ($i=0; $i < sizeof($datas); $i++) { 
		if($datas[$i]['name'] != 'action' && $datas[$i]['name'] != 'change_role' && $datas[$i]['name'] != 'select-all') {
			array_push($ids, substr($datas[$i]['name'], strpos($datas[$i]['name'], '-')+1));
		}
		if($datas[$i]['name'] == 'change_role') {
			$change_to = $datas[$i]['value'];
		}
	}

	foreach ($ids as $i => $id) {
		$sets = array('role' => $change_to);
		$wheres = array('id' => $id);
		$result_change_role = updateRecords(DBNAME, 'users', $sets, $wheres);
		if($result_change_role) {
			$check = 'success_change_role';
		}
		else {
			$check = 'fail_change_role_users';
			break;
		}
	}
	
	echo $check;
}

?>