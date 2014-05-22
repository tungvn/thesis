<?php 
/* Access */
/*
Order Role from Highest to Lowest
	1. administrator -- full setting
	2. moderator -- full setting, expect any users' setting
	3. editor -- full editing available workspaces, layers information 
					but can NOT create or remove ones
	4. subscriber -- only view infomations, can NOT action with anything
*/

// Include all functions to action with database
require_once(dirname(__FILE__) . '/config.php');
require_once(dirname(__FILE__) . '/includes/databases.php');
require_once(dirname(__FILE__) . '/includes/settings.php');
require_once(dirname(__FILE__) . '/includes/users.php');
require_once(dirname(__FILE__) . '/includes/GeoserverWrapper.php');

/* Check user permissions */
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

function getNumberSettings() {
	$selects = array('COUNT(*)');
	$rows = getRecords(DBNAME, 'settings', $selects);
	if($rows === false) return false;

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row[0];
	}

	return false;
}

function getObjectID($slug) {
	$selects = array('id');
	$wheres = array('slug' => $slug);
	$rows = getRecords(DBNAME, 'object', $selects, $wheres);
	if($rows === false) return false;

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row['id'];
	}

	return false;
}
function getObjectSlug($id) {
	$selects = array('slug');
	$wheres = array('id' => $id);
	$rows = getRecords(DBNAME, 'object', $selects, $wheres);
	if($rows === false) return false;

	if (pg_num_rows($rows) == 1) {
		$row = pg_fetch_array($rows);
		return $row['slug'];
	}

	return false;
}
/*-------------------------------------edit.php-------------------------------------------------*/
/* Edit administrator */
function addNewForm($obj_type) { ?>
	<div class="container">
		<h3 class="div-title">Add new <?php echo $obj_type; ?></h3>
		<?php $enctype = ($obj_type == 'layer') ? 'enctype="multipart/form-data"' : ''; ?>
		<form class="grid-4 add-new-object" method="POST" action="edit.php?obj=<?php echo $obj_type; ?>" <?php echo $enctype; ?>>
			<?php if($obj_type == 'workspace'): ?>
			<label class="grid-4" for="name">Name<span class="required"></span></label>
			<input class="grid-4 has-border has-border-radius" type="text" name="name" id="name" required>
			<?php endif; ?>
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
			<label class="grid-4" for="shpfile">Shapefile<span class="required"></span><br>(select all shapefiles have the same name)</label>
			<input class="grid-4 has-border has-border-radius" type="file" name="shpfile[]" id="shpfile" title="Select all shapefiles has the same name" multiple required>
			<?php endif; ?>
			<label for="publish" class="grid-4">Publish</label>
			<select name="publish" id="publish" class="grid-4 has-border has-border-radius">
				<option value="1">Publish</option>
				<option value="0" selected>Unpublish</option>
			</select>
			<label class="grid-4" for="description">Description</label>
			<textarea class="grid-4 has-border has-border-radius" name="description" id="description" rows="10" style="resize: none;"></textarea>
			<input class="button has-border-radius" name="submit" type="submit" value="Create">
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
				<?php if(isset($_GET['publish'])): 
					if($_GET['publish'] == 1): ?>
					<a href="edit.php?obj=<?php echo $obj_type; ?>&publish=0" class="fr">Unpublish</a>
					<?php else: ?>
						<a href="edit.php?obj=<?php echo $obj_type; ?>&publish=1" class="fr">Publish</a>
					<?php endif; 
					else: ?>
					<a href="edit.php?obj=<?php echo $obj_type; ?>&publish=0" class="fr">Unpublish</a>
				<?php endif; ?>
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
				$publish = isset($_GET['publish']) ? $_GET['publish'] : 1;
				$rows = getObjects($obj_type, 10, ($paged-1)*10, $publish); 
				if($rows) {
					$i = 0;
					while ($row = pg_fetch_array($rows)) { ?>
						<div class="grid-4 row">
							<?php if(is_subscriber()): ?>
							<div class="grid-1-16"><input type="checkbox" name="<?php echo $obj_type . '-' . $row['id']; ?>" id="select[<?php echo $i++; ?>]"></div>
							<div class="grid-3-16"><?php echo $row['id'] ?></div>
							<div class="grid-1-4"><?php echo $row['name'] ?></div>
							<div class="grid-1-4"><?php echo ($row['description'] != '') ? $row['description'] : '&nbsp;'; ?></div>	
							<?php else: ?>
							<div class="grid-1-16"><input type="checkbox" name="<?php echo $obj_type . '-' . $row['id']; ?>" id="select[<?php echo $i++; ?>]"></div>
							<div class="grid-3-16"><a href="single.php?obj=<?php echo $obj_type; ?>&amp;<?php echo $obj_type . '=' .$row['id']; ?>"><?php echo $row['id'] ?></a></div>
							<div class="grid-1-4"><a href="single.php?obj=<?php echo $obj_type; ?>&amp;<?php echo $obj_type . '=' .$row['id']; ?>"><?php echo $row['name'] ?></a></div>
							<div class="grid-1-4"><?php echo ($row['description'] != '') ? $row['description'] : '&nbsp;'; ?></div>
							<?php endif; ?>
							<div class="grid-1-4">
							<?php if($obj_type == 'layer'): 
							$selects = array('slug');
							$wheres = array('id' => $row['workspace']);
							$rows_wp = getRecords(DBNAME, 'object', $selects, $wheres);
							if($rows_wp && pg_num_rows($rows_wp) > 0) {
								$row_wp = pg_fetch_array($rows_wp);
							} 
							if(is_subscriber()) echo $row_wp['slug']; else { ?>
								<a href="single.php?obj=workspace&amp;workspace=<?php echo $row['workspace']; ?>">
									<?php echo $row_wp['slug']; ?>
								</a>
							<?php } elseif($obj_type == 'workspace'):
								$selects = array('id', 'slug');
								$wheres = array(
									'type' => 'layer',
									'workspace' => $row['id']
								); 
								$rows_inner = getRecords(DBNAME, 'object', $selects, $wheres); 
								if(pg_num_rows($rows_inner) > 0):
									$j = 0;
									while($row_inner = pg_fetch_array($rows_inner)): 
										echo ($j == 0) ? '' : ', '; 
										if(is_subscriber())
											echo $row_inner['slug']; 
										else { ?>
										<a href="single.php?obj=layer&amp;layer=<?php echo $row_inner['id']; ?>">
											<?php echo $row_inner['slug']; ?>
										</a>
									<?php } $j++; endwhile; endif; ?>
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
				<?php if(is_admin()): ?>
				<select name="action" id="action" class="grid-1-4 has-border has-border-radius">
					<option value="">Choose action</option>
					<option value="delete">Delete</option>
				</select>
				<?php $rows = getOption('user_role');
				$rows = explode(', ', $rows); ?>
				<select name="change_role" id="change_role" class="grid-1-4 has-border has-border-radius" style="margin-left: 10px;">
					<option value="">Change role to...</option><?php foreach ($rows as $key => $row): ?>
					<option value="<?php echo $row; ?>"><?php echo ucfirst($row); ?></option>
					<?php endforeach; ?>
				</select>
				<input type="submit" value="Apply" class="button fl has-border-radius" style="padding: 7px 10px 6px; margin-left: 10px;">
				<?php if(isset($_GET['verify'])): 
					if($_GET['verify'] == 1): ?>
					<a href="edit.php?obj=<?php echo $obj_type; ?>&verify=0" class="fr">Non-Verify</a>
					<?php else: ?>
						<a href="edit.php?obj=<?php echo $obj_type; ?>&verify=1" class="fr">Verify</a>
					<?php endif; 
					else: ?>
					<a href="edit.php?obj=<?php echo $obj_type; ?>&verify=0" class="fr">Non-Verify</a>
				<?php endif; ?>
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
				$verify = isset($_GET['verify']) ? $_GET['verify'] : 1;
				$wheres = array('verify' => $verify);
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
		<?php elseif(is_editor() || is_subscriber()): ?>
		<div class="grid-4">
			<?php listObject($obj_type); ?>
		</div>
		<?php endif; ?>
	</div>
	<?php else: ?>
	<div id="object" class="grid-4" for="users">
	<?php if(is_admin() || is_moder()): ?>
		<div class="grid-4">
		<?php listUsers($obj_type); ?>
		</div>
	<?php endif; ?>
	</div>
	<?php endif;
}


function getObjects($obj_type, $limit = 99999, $offset = 0, $publish = 1) {
	$selects = array( 'id', 'name', 'slug', 'workspace', 'description' );
	$wheres = array(
		'type' => $obj_type,
		'publish' => $publish
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
	if(is_admin() || is_moder() || is_editor())
		singleObject($obj_type, $obj);
	else 
		echo '<p>Access denied</p>';
}

function singleObject($obj_type, $obj) { ?>
<form id="update_object" class="grid-4 fl" method="POST">
	<input type="hidden" name="object" value="<?php echo $obj_type; ?>">
	<input type="hidden" name="<?php echo $obj_type; ?>" value="<?php echo getObjectSlug($obj); ?>">
	<?php $selects = array('*');
	$wheres = array('id' => $obj);
	$rows = getRecords(DBNAME, 'object', $selects, $wheres);
	if($rows && pg_num_rows($rows) == 1):
		$row = pg_fetch_array($rows);
	if($obj_type == 'workspace'): ?>
		<div class="row">
			<div class="grid-1-4">
				<label for="<?php echo $obj_type . 'name'; ?>">Name</label>
			</div>
			<div class="grid-3">
				<input type="text" class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'name'; ?>" id="<?php echo $obj_type . 'name'; ?>" value="<?php echo $row['name']; ?>">
				<p class="grid-4"><i>Name of Workspace</i></p>
			</div>
		</div>
		<div class="row">
			<div class="grid-1-4">
				<label for="<?php echo $obj_type . 'slug'; ?>">Slug</label>
			</div>
			<div class="grid-3">
				<input type="text" class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'slug'; ?>" id="<?php echo $obj_type . 'slug'; ?>" value="<?php echo $row['slug']; ?>" readonly>
				<p class="grid-4"><i>Slug of Workspace</i></p>
			</div>
		</div>
		<div class="row">
			<div class="grid-1-4">
				<label for="<?php echo $obj_type . 'publish'; ?>">Publish</label>
			</div>
			<div class="grid-3">
				<?php $selected = ($row['publish'] == 'f') ? 'selected' : ''; ?>
				<select class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'publish'; ?>" id="<?php echo $obj_type . 'publish'; ?>">
					<option value="1">Publish</option>
					<option value="0" <?php echo $selected; ?>>Unpublish</option>
				</select>
				<p class="grid-4"><i>Publish or Unpublish Workspace</i></p>
			</div>
		</div>
		<div class="row">
			<div class="grid-1-4">
				<label for="<?php echo $obj_type . 'description'; ?>">Description</label>
			</div>
			<div class="grid-3">
				<textarea class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'desc'; ?>" id="<?php echo $obj_type . 'description'; ?>" rows="10"><?php echo $row['description']; ?></textarea>
				<p class="grid-4"><i>Description of Workspace</i></p>
			</div>
		</div>
		<?php $selects = array('id', 'name');
		$wheres = array('workspace' => $row['id']);
		$rows_inner = getRecords(DBNAME, 'object', $selects, $wheres);
		if($rows_inner && pg_num_rows($rows_inner) > 0): $i = 0; ?>
		<div class="row">
			<div class="grid-1-4">
				<label>Layers of "<?php echo ucfirst($row['name']); ?>"</label>
			</div>
			<div class="grid-3">
				<p>
				<?php while($row_inner = pg_fetch_array($rows_inner)): ?>
				
					<?php echo ($i++ != 0) ? ', ' : ''; ?>
					<a href="single.php?obj=layer&amp;layer=<?php echo $row_inner['id']; ?>"><?php echo $row_inner['name']; ?></a>
				<?php endwhile; ?>
				</p>
			</div>
		</div>
		<?php endif; ?>
	<?php else: ?>
	<input type="hidden" name="workspace" value="<?php echo getObjectSlug($row['workspace']); ?>">
		<ul class="tab-title-block grid-4">
			<li class="tab-title tab-on" for="info">Layer Info</li>
			<li class="tab-title" for="records">Records</li>
			<li class="tab-title hidden" for="view_map">View Map</li>
		</ul>
		<div class="tab-content tab-show" for="info">
			<div class="row">
				<div class="grid-1-4">
					<label for="<?php echo $obj_type . 'name'; ?>">Name</label>
				</div>
				<div class="grid-3">
					<input type="text" class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'name'; ?>" id="<?php echo $obj_type . 'name'; ?>" value="<?php echo $row['name']; ?>">
					<p class="grid-4"><i>Name of Layer</i></p>
				</div>
			</div>
			<div class="row">
				<div class="grid-1-4">
					<label for="<?php echo $obj_type . 'slug'; ?>">Slug</label>
				</div>
				<div class="grid-3">
					<input type="text" class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'slug'; ?>" id="<?php echo $obj_type . 'slug'; ?>" value="<?php echo $row['slug']; ?>" readonly>
					<p class="grid-4"><i>Slug of Layer</i></p>
				</div>
			</div>
			<div class="row">
				<div class="grid-1-4">
					<label for="<?php echo $obj_type . 'workspace'; ?>">Workspace</label>
				</div>
				<div class="grid-3">
					<select name="<?php echo $obj_type . 'workspace'; ?>" id="<?php echo $obj_type . 'workspace'; ?>" class="has-border has-border-radius grid-3">
						<?php $rows_inner = getObjects('workspace'); 
						if($rows_inner):
							$i = 0;
							while ($row_inner = pg_fetch_array($rows_inner)): 
								$selected = ($row_inner['id'] == $row['workspace']) ? 'selected' : ''; ?>
								<option value="<?php echo $row_inner['id']; ?>" <?php echo $selected; ?>><?php echo $row_inner['name']; ?></option>
						<?php endwhile; endif; ?>
					</select>
					<p class="grid-4"><i>Workspace</i></p>
				</div>
			</div>
			<div class="row">
				<div class="grid-1-4">
					<label for="<?php echo $obj_type . 'publish'; ?>">Publish</label>
				</div>
				<div class="grid-3">
					<?php $selected = ($row['publish'] == 'f') ? 'selected' : ''; ?>
					<select class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'publish'; ?>" id="<?php echo $obj_type . 'publish'; ?>">
						<option value="1">Publish</option>
						<option value="0" <?php echo $selected; ?>>Unpublish</option>
					</select>
					<p class="grid-4"><i>Publish or Unpublish Layer</i></p>
				</div>
			</div>
			<div class="row">
				<div class="grid-1-4">
					<label for="<?php echo $obj_type . 'description'; ?>">Description</label>
				</div>
				<div class="grid-3">
					<textarea class="has-border has-border-radius grid-3" name="<?php echo $obj_type . 'desc'; ?>" id="<?php echo $obj_type . 'description'; ?>" rows="10"><?php echo $row['description']; ?></textarea>
					<p class="grid-4"><i>Description of Layer</i></p>
				</div>
			</div>
		</div>
		<div class="tab-content" for="records">
			<div class="container" style="max-height: 600px; overflow-y: scroll;">
				<p class="div-title"><i>Click cell to edit</i></p>
				<table class="grid-4">
					<thead>
						<tr>
						<?php $fields = getFields(getObjectSlug($row['workspace']), $row['slug']); 
						if($fields) {
							$num_fields = sizeof($fields) + 1;
							foreach ($fields as $column_name => $data_type) { 
								if($column_name != 'gid') { ?>
								<td style="width: <?php echo 90/$num_fields; ?>%; padding: 10px 0;"><?php echo $column_name; ?></td>
								<?php }
								else { ?>
								<td style="width: 5%;">gid</td>
								<?php }
							} 
						} ?>
							<td style="width: 5%;">&nbsp;</td>
						</tr>
					</thead>
					<tbody>
						<?php $records = getRecords(getObjectSlug($row['workspace']), $row['slug']);
						if($records && pg_num_rows($records)):
							$record_key = 0;
							while($record = pg_fetch_array($records)): ?>
						<tr>
							<?php foreach ($fields as $column_name => $data_type) {
							if($column_name == 'geom') {
								$link = connectDB(HOST, getObjectSlug($row['workspace']), DBUSER, DBPASS);
								if(in_array($row['slug'], array('thuyvan', 'lokhoan')))
									$latlng = pg_query('SELECT ST_AsText(ST_Transform(ST_SetSRID(geom,32648),4326)) as latlng FROM ' . $row['slug'] . ' WHERE gid = ' . $record['gid']);
								else
									$latlng = pg_query('SELECT ST_AsText(geom) as latlng FROM ' . $row['slug'] . ' WHERE gid = ' . $record['gid']);
								if($latlng && pg_num_rows($latlng) > 0) {
									$latlng = pg_fetch_array($latlng);
									$latlng = $latlng['latlng'];
									if (strpos($latlng, ',')) {
										$latlng = substr( $latlng, 0, strpos($latlng, ',')+1);
										$lat = substr($latlng, strrpos($latlng, '(')+1, strpos($latlng, ' ') - strrpos($latlng, '('));
										$lng = substr($latlng, strpos($latlng, ' '), strpos($latlng, ',') - strpos($latlng, ' ')); 
									}
									else {
										$lat = substr($latlng, strrpos($latlng, '(')+1, strpos($latlng, ' ') - strrpos($latlng, '('));
										$lng = substr($latlng, strpos($latlng, ' '), strpos($latlng, ')') - strpos($latlng, ' '));
									}
								}
								closeDB($link);
							} ?>
								<td class="has-border" style="text-align: center;">
								<?php if($column_name == 'gid'): ?>
									<p class="grid-4" style="padding: 10px;"><?php echo $record[$column_name]; ?></p>
									<input type="hidden" name="record[<?php echo $record_key; ?>][<?php echo $column_name; ?>]" value="<?php echo $record[$column_name]; ?>">
								<?php else: ?>
									<input class="grid-4" style="padding: 10px;" type="text" name="record[<?php echo $record_key; ?>][<?php echo $column_name; ?>]" value="<?php echo $record[$column_name]; ?>">
								<?php endif; ?>
								</td>
							<?php } $record_key++; ?>
							<td class="has-border" style="text-align: center;">
								<a href="javascript:void(0);" onclick="showResult(<?php echo '\'' . getObjectSlug($row['workspace']) . '\', \'' . $row['slug'] . '\', ' . $lat . ', ' . $lng; ?>);">View</a>
							</td>
						</tr>
						<?php endwhile; endif; ?>
					</tbody>
				</table>
			
			</div>
		</div>
		<div class="tab-content" for="view_map">
			<div id="map"></div>
		</div>
	<?php endif; ?>
	<div class="row" style="margin-top: 20px;">
		<div class="grid-4 fl">
			<input name="submit" type="submit" class="button has-border-radius" value="Update">
		</div>
	</div>
</form>
<?php endif;
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
		case 'search_tool':
			if(isset($_POST['json']))
				searchTools(json_decode($_POST['group'], true), $_POST['keyword']);
			else preSearchTool($_POST['group'], $_POST['keyword']);
			break;
	}
}
/* For Ajax call */
function addNewObject($data) {
	if($data['type'] == 'workspace') {
		$result = createDB($data['slug']);
		if($result) {
			$result_1 = createExtension($data['slug']);
			if($result_1) {
				$args = array(
					'name' => $data['name'],
					'slug' => $data['slug'],
					'type' => $data['type'],
					'description' => $data['description'],
					'publish' => $data['publish']
				);
				$result_2 = insertRecords(DBNAME, 'object', $args);
				if($result_2) {
					$push = array('workspace' => $data['slug'], 'datastore' => $data['slug'], 'dbname' => $data['slug']);
					publish2Frontend('createworkspace', $push);
					echo 'success';
				}
				else echo 'fail_insert_tb';
			}
			else echo 'fail_create_extension';
		}
		else echo 'fail_create_db';
	}
	elseif($data['type'] == 'layer') {
		$selects = array('slug');
		$wheres = array('id' => $data['workspace']);
		$wp_slugs = getRecords(DBNAME, 'object', $selects, $wheres);
		if($wp_slugs && pg_num_rows($wp_slugs) > 0)
			$wp_slug = pg_fetch_array($wp_slugs);
		$wp_slug = $wp_slug['slug'];

		$shp2pgsql = '"C:/Program Files/PostgreSQL/9.3/bin/shp2pgsql" -s 32648 -W LATIN1 -c -D -I '; 
		$psql = '"C:/Program Files/PostgreSQL/9.3/bin/psql" -d ' . $wp_slug . ' -U postgres ';
		$result = exec($shp2pgsql . $data['shpfile'] . " | " . $psql);

		if($result == 'COMMIT') {
			$args = array(
				'name' => $data['name'],
				'slug' => $data['name'],
				'type' => $data['type'],
				'workspace' => $data['workspace'],
				'description' => $data['description'],
				'publish' => $data['publish']
			);
			$result_2 = insertRecords(DBNAME, 'object', $args);
			if($result_2) {
				$push = array(
					'layer' => $data['name'],
					'workspace' => getObjectSlug($data['workspace']),
					'dbname' => getObjectSlug($data['workspace']),
					'datastore' => getObjectSlug($data['workspace']) . '_' . $data['name'],
					'description' => $data['description']
				);
				publish2Frontend('createdatastorepostgis', $push);
				publish2Frontend('createlayer', $push);
				echo 'success';
			}
			else echo 'fail_insert_tb';
		}
		else {
			echo 'fail_import_shapefile';
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
		$selects = array('workspace');
		$wheres = array('id' => $ids[$i]);
		$wp = getRecords(DBNAME, 'object', $selects, $wheres);
		$wp = pg_fetch_array($wp);
		$wp = getObjectSlug($wp['workspace']);

		$result_del_obj = dropRecords(DBNAME, 'object', $wheres);
		if($result_del_obj) {
			if($obj_type == 'workspace') {
				$result_del_db = dropDB($slug);
				if($result_del_db) {
					$wheres = array('workspace' => $id_wp);

					$selects = array('slug');
					$rows_layer = getRecords(DBNAME, 'object', $selects, $wheres);
					if($rows_layer && pg_num_rows($rows_layer) > 0) {
						while($row_layer = pg_fetch_array($rows_layer)) {
							$push = array(
								'layer' => $row_layer['slug'], 
								'datastore' => $slug . '_' . $row_layer['slug'], 
								'workspace' => $slug
							);
							print_r($push);
							publish2Frontend('deletelayer', $push);
							publish2Frontend('deletedatastore', $push);
						}
					}

					$result_del_wp = dropRecords(DBNAME, 'object', $wheres);
					if($result_del_wp) {
						$push = array('workspace' => $slug);
						publish2Frontend('deleteworkspace', $push);
						$check = 'success';
					}
					else $check = 'fail_del_layer_inline';
				}
				else {
					$check = 'fail_del_db';
					break;
				}
			}
			else {
				$result_del_tb = dropTable($wp, $slug);
				if($result_del_tb) {
					$push = array('layer' => $slug, 'datastore' => $wp . '_' . $slug, 'workspace' => $wp);
					publish2Frontend('deletelayer', $push);
					publish2Frontend('deletedatastore', $push);
					$check = 'success';
				}
				else {
					$check = 'fail_del_tb';
				}
			}
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
/* String to slug */
function vn_str_filter($str) {
	$unicode = array(
		'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
		'd'=>'đ',
		'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
		'i'=>'í|ì|ỉ|ĩ|ị',
		'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
		'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
		'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
		'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
		'D'=>'Đ',
		'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
		'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
		'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
		'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
		'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
	);
	foreach($unicode as $nonUnicode=>$uni) {
		$str = preg_replace("/($uni)/i", $nonUnicode, $str);
	}
	return preg_replace('/[^A-Za-z0-9-]+/', '', $str);
}

/* Search Tools */
function preSearchTool($datas, $keyword) {
	$arrayLayers = array();
	foreach ($datas as $data) {
		$temp_arrLayers = array();
		foreach ($data['layers'] as $temp_arrLayer) {
			array_push($temp_arrLayers, substr($temp_arrLayer, strpos($temp_arrLayer, ':')+1));
		}
		$arrayLayers[$data['workspace']] = $temp_arrLayers;
	}

	searchTools($arrayLayers, $keyword, true);
}

function searchTools($arrayLayers, $keyword, $front_end = false) {
	$result = array();
	$key_number = 1;
	foreach ($arrayLayers as $workspace => $layers) {
		foreach ($layers as $key => $layer) {
			$rows = getFields($workspace, $layer);
			if($rows) {
				$select = '';
				$wheres = '';
				$i = 0;
				foreach ($rows as $column_name => $data_type) {
					if($column_name != 'geom') {
						if($i != 0) {
							$select .= ', ';
							$wheres .= ' OR ';
						}
						$select .= $column_name;
						$wheres .= "cast($column_name as text) LIKE '%$keyword%'";
					}
					else {
						if(in_array($layer, array('thuyvan', 'lokhoan')))
							$select_more = ', ST_AsText(ST_Transform(ST_SetSRID(geom,32648),4326)) as latlng';
						else
							$select_more = ', ST_AsText(geom) as latlng';
					}
					$i++;
				}
				$sql = "SELECT $select $select_more FROM $layer WHERE $wheres";
				$rows = pg_query($sql);

				if($rows && pg_num_rows($rows) > 0) {
					while($row = pg_fetch_array($rows)) {
						// array_push($result, $row);
						$latlng = $row['latlng'];
						if (strpos($latlng, ',')) {
							$latlng = substr( $latlng, 0, strpos($latlng, ',')+1);
							$lat = substr($latlng, strrpos($latlng, '(')+1, strpos($latlng, ' ') - strrpos($latlng, '('));
							$lng = substr($latlng, strpos($latlng, ' '), strpos($latlng, ',') - strpos($latlng, ' ')); 
						}
						else {
							$lat = substr($latlng, strrpos($latlng, '(')+1, strpos($latlng, ' ') - strrpos($latlng, '('));
							$lng = substr($latlng, strpos($latlng, ' '), strpos($latlng, ')') - strpos($latlng, ' '));
						}
						if($front_end)
							echo '<a class="search_result" href="javascript:void(0);" onclick="showResult(' . $lat . ', ' . $lng . ');">' . $workspace . ' => ' . $layer . ' (Result ' . $key_number++ . ')</a><br>';
						else {
							foreach ($row as $name => $val) {
								echo '<a href="single.php?obj=layer&layer=' . getObjectID($layer) . ' " class="search_result">' . $workspace . ' => ' . $layer . ' (Result ' . $key_number++ . ')</a><br>';
							}
						}
					}
				}
			}
		}
	}
}

// function for push data from postgresql to geoserver
// Workspace' name is Datastore' name
function publish2Frontend($request, $data) {
	$geoserver = new GeoserverWrapper('http://localhost:8080/geoserver', 'admin', 'geoserver');

	switch ($request) {
		case 'createworkspace':
			$geoserver->createWorkspace($data['workspace']);
			break;
		case 'deleteworkspace':
			print_r($geoserver->deleteWorkspace($data['workspace']));
			break;
		case 'createdatastorepostgis':
			$geoserver->createPostGISDataStore($data['datastore'], $data['workspace'], $data['dbname'], DBUSER, DBPASS, HOST);
			break;
		case 'deletedatastore':
			$geoserver->deleteDataStore($data['datastore'], $data['workspace']);
			break;
		case 'createlayer':
			$geoserver->createLayer($data['layer'], $data['workspace'], $data['datastore'], $data['description']);
			break;
		case 'deletelayer':
			$geoserver->deleteLayer($data['layer'], $data['workspace'], $data['datastore']);
			break;
	}

	return;
}

?>
