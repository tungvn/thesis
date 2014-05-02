<?php 
/*
settings.php
All functions actions with Page Settings
*/

// Include databases.php -- all functions to action with postgresql database
include_once('includes/databases.php');

function registerOption($name, $desc) {
	$args = array(
		'name' => $name,
		'value' => $value
	);

	$result = insertRecords(DBNAME, 'settings', $args);
	return $result;
}

function updateOption($name, $value) {
	$sets = array('value' => $value);
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