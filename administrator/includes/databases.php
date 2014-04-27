<?php 
/*
All scripts of databases.
*/
function connectServer($host, $user, $pass) {
	$query = 'host=' . $host . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query) or die();
	return $link;
}

function connectDB($host, $dbname, $user, $pass) {
	$query = 'host=' . $host . ' dbname=' . $dbname . ' user=' . $user . ' password=' . $pass;
	$link = pg_connect($query) or die();
	return $link;
}

function closeDB($link) {
	pg_close($link);
}

function createDB($dbname, $options) {
	$query = 'create database' . $dbname;
	foreach ($optiona as $option => $value) {
		$query .= ' ' . $option . ' = ' . $value;
	}
	$query .= ';';
	$result = pg_query($query);
	if($result != false)
		return true;
	return false;
}

?>