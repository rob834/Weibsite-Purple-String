<?php

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "purplestring_db";

// create initial connection
if (!($con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname))) {
	error_log('DB connection failed: ' . mysqli_connect_error());
	die('Database connection failed.');
}

mysqli_set_charset($con, 'utf8mb4');

// Helper to get (and reconnect if needed) the mysqli connection
function get_db_connection()
{
	global $con, $dbhost, $dbuser, $dbpass, $dbname;

	if (!isset($con) || !mysqli_ping($con)) {
		// attempt reconnect
		@mysqli_close($con);
		$con = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
		if (!$con) {
			error_log('DB reconnect failed: ' . mysqli_connect_error());
			return false;
		}
		mysqli_set_charset($con, 'utf8mb4');
	}
	return $con;
}
