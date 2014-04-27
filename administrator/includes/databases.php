<?php 
/*
All scripts of databases.
*/
// Connect to server
function connectServer($host, $user, $pass) {
	$query = 'host=' . $host . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query) or die();
	return $link;
}
// Databases
function createDB($name) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'CREATE DATABASE ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

function renameDB($old_name, $new_name) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'ALTER DATABASE ' . $old_name . ' RENAME ' . $new_name;
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

function dropDB($name) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'DROP DATABASE IF EXISTS ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

function commentDB($name, $desc) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = "COMMENT ON DATABASE $name IS '$desc'";
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

function updateDB($options = array()) {
	return true;
}

function connectDB($host, $dbname, $user, $pass) {
	$query = 'host=' . $host . ' dbname=' . $dbname . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query) or die();
	return $link;
}

function closeDB($link) {
	pg_close($link);
}

// Tables
function createTable() {
	
}

function updateTable() {

}

function dropTable($namedb, $nametb) {
	$link = connectDB('localhost', $namedb, 'postgres', '123456');
	if(!$link) return false;

	$query = 'DROP TABLE IF EXISTS ' . $nametb;
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

// Records
function insertTable($namedb, $nametb, $args = array()) {
	$link = connectDB('localhost', $namedb, 'postgres', '123456');
	if(!$link) return false;

	$cols = '(';
	$vals = '(';
	$i = 0;
	foreach ($args as $col => $val) {
		if($i != 0) {
			$cols .= ', ';
			$vals .= ', ';
		}
		$i++;
		$cols .= "'$col'";
		$vals .= "'$val'";
	}
	$cols .= ')';
	$vals .= ')';

	$query = "INSERT INTO '$nametb' $cols VALUES $vals";
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}

// View table
function viewTable($namedb, $nametb, $selects = array('*'), $wheres = array()) {
	$link = connectDB('localhost', $namedb, 'postgres', '123456');
	if(!$link) return false;

	$sel = '';
	$whe = '';
	$i = 0;
	foreach ($selects as $val) {
		if($i != 0) {
			$sel .= ', ';
		}
		$i++;
		$sel .= $val;
	}
	$i = 0;
	foreach ($wheres as $key => $val) {
		if($i != 0) {
			$whe .= ' AND ';
		}
		$i++;
		$cm = '=';
		if($val === true)
			$cm = 'IS';
		else
			$cm = 'IS NOT';	
		$whe .= "$key $cm '$val'";
	}
	$query = "SELECT $sel FROM $nametb WHERE $whe";
	$result = pg_query($link, $query);

	closeDB($link);

	if(!$result) return true;
	return false;
}
?>
