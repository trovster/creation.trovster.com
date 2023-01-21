<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/mysql.php');

/* database connections
============================================================================================================= */
function dbConnect() {

	/* DATABASE
	--------------------------------------------------------------------------------------------------*/
	$database_hostname = 'trovster_creation_mysql'; // 127.0.0.1:3321
	$database_username = 'creation';
	$database_password = 'password';
	$database_database = 'creation';

	$connect_admin = mysqli_connect($database_hostname, $database_username, $database_password)
	or die ('I cannot connect to the database because: ' . mysql_error());

	mysqli_select_db($connect_admin, $database_database);

	return $connect_admin;
}
