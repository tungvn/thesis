<?php 
/*
All scripts of databases.
*/
// Connect to server
function connectServer($host, $user, $pass) {
	$query = 'host=' . $host . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query, PGSQL_CONNECT_FORCE_NEW);
	return $link;
}
// Databases
function createDB($name) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'CREATE DATABASE ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function renameDB($old_name, $new_name) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'ALTER DATABASE ' . $old_name . ' RENAME ' . $new_name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function dropDB($name) {
	if(@pg_dbname())
		closeDB(pg_dbname());
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = 'DROP DATABASE IF EXISTS ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function commentDB($name, $desc) {
	$link = connectServer('localhost', 'postgres', '123456');
	if(!$link) return false;

	$query = "COMMENT ON DATABASE $name IS '$desc'";
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function updateDB($options = array()) {
	return true;
}

function connectDB($host, $dbname, $user, $pass) {
	$query = 'host=' . $host . ' dbname=' . $dbname . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query);
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

	if($result) return true;
	return false;
}

// Records
function insertRecords($namedb, $nametb, $args = array()) {
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
		$cols .= $col;
		$vals .= "'$val'";
	}
	$cols .= ')';
	$vals .= ')';

	$query = "INSERT INTO $nametb $cols VALUES $vals";
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function getRecords($namedb, $nametb, $selects = array('*'), $wheres = array(), $limit = 99999, $offset = 0) {
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
		elseif ($val === false)
			$cm = 'IS NOT';	
		$whe .= "$key $cm '$val'";
	}

	$query = "SELECT $sel FROM $nametb";
	if(sizeof($wheres) > 0) 
		$query .= " WHERE $whe";
	$query .= " LIMIT $limit OFFSET $offset";
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return $result;
	return false;
}

function dropRecords($namedb, $nametb, $wheres) {
	$link = connectDB('localhost', $namedb, 'postgres', '123456');
	if(!$link) return false;

	$whe = '';
	$i = 0;
	foreach ($wheres as $key => $val) {
		if($i != 0) {
			$whe .= ' AND ';
		}
		$i++;
		$cm = '=';
		if($val === true)
			$cm = 'IS';
		elseif ($val === false)
			$cm = 'IS NOT';	
		$whe .= "$key $cm '$val'";
	}

	$query = "DELETE FROM $nametb WHERE $whe";
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}
?>
