<?php

/* database connections
============================================================================================================= */
function dbConnect() {

	/* DATABASE
	--------------------------------------------------------------------------------------------------*/
	$database_hostname = 'localhost';
	$database_username = 'root';
	$database_password = '';
	$database_database = 'creation';

	$connect_admin = mysqli_connect($database_hostname, $database_username, $database_password)
	or die ('I cannot connect to the database because: ' . mysql_error());

	mysqli_select_db($connect_admin, $database_database);

	return $connect_admin;
}
?>