<?php 
/*
settings.php
All functions actions with Page Settings
*/

// Include databases.php -- all functions to action with postgresql database
require_once(dirname(__FILE__) . '/databases.php');

function registerOption($name, $value = '', $desc = '') {
	$args = array(
		'name' => $name,
		'value' => $value,
		'description' => $desc
	);

	$result = insertRecords(DBNAME, 'settings', $args);
	return $result;
}

function updateOption($name, $value, $desc) {
	$sets = array('value' => $value, 'description' => $desc);
	$wheres = array('name' => $name);

	$result = updateRecords(DBNAME, 'settings', $sets, $wheres);
	return $result;
}

function getOption($name) {
	$selects = array('value');
	$wheres = array('name' => $name);
	$rows = getRecords(DBNAME, 'settings', $selects, $wheres);
	if($rows === false) return false;

	if(pg_num_rows($rows) == 1)
		$row = pg_fetch_array($rows);
	return $row['value'];
}

?>