<?php 
/*
All functions action with databases.
Version 1.0.0
*/

// Connect to server
/* 
@para
	host: hostname
	user: user to login server
	pass: pass to login server
@return 
	- A [connection] if successful connect
	- [FALSE] if fail connect
*/
function connectServer($host, $user, $pass) {
	$query = 'host=' . $host . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query, PGSQL_CONNECT_FORCE_NEW);
	return $link;
}
// Databases

/* 
@para
	name: name database to create
@return 
	- [TRUE] if successful create
	- [FALSE] if fail create
*/
function createDB($name) {
	$link = connectServer(HOST, DBUSER, DBPASS);
	if(!$link) return false;

	$query = 'CREATE DATABASE ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

/* 
@para
	old_name: 
	new_name:
@return 
	- [TRUE] if successful rename
	- [FALSE] if fail rename
*/
function renameDB($old_name, $new_name) {
	$link = connectServer(HOST, DBUSER, DBPASS);
	if(!$link) return false;

	$query = 'ALTER DATABASE ' . $old_name . ' RENAME ' . $new_name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function createExtension($dbname) {
	$link = connectDB(HOST, $dbname, DBUSER, DBPASS);
	if(!$link) return false;

	$query = 'CREATE EXTENSION postgis SCHEMA public VERSION "2.1.1"';
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function dropDB($name) {
	if(@pg_dbname())
		closeDB(pg_dbname());
	$link = connectServer(HOST, DBUSER, DBPASS);
	if(!$link) return false;

	$query = 'DROP DATABASE IF EXISTS ' . $name;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

function commentDB($name, $desc) {
	$link = connectServer(HOST, DBUSER, DBPASS);
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
	$link = connectDB(HOST, $namedb, DBUSER, DBPASS);
	if(!$link) return false;

	$query = 'DROP TABLE IF EXISTS ' . $nametb;
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}

// Records
function insertRecords($namedb, $nametb, $args = array()) {
	$link = connectDB(HOST, $namedb, DBUSER, DBPASS);
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
	$link = connectDB(HOST, $namedb, DBUSER, DBPASS);
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
	$link = connectDB(HOST, $namedb, DBUSER, DBPASS);
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

function updateRecords($namedb, $nametb, $sets, $wheres) {
	$link = connectDB(HOST, $namedb, DBUSER, DBPASS);
	if(!$link) return false;

	$st = '';
	$i = 0;
	foreach ($sets as $key => $set) {
		if($i != 0) 
			$st .= ', ';
		$i++;
		$st .= "$key = '$set' ";

	}

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

	$query = "UPDATE $nametb SET $st WHERE $whe";
	$result = pg_query($link, $query);

	closeDB($link);

	if($result) return true;
	return false;
}
?>
