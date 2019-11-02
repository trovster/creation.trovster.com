<?php
/**
 * Create a directory structure recursively
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/repos/v/function.mkdirr.php
 * @param       string   $pathname    The directory structure to create
 * @return      bool     Returns TRUE on success, FALSE on failure
 */

function mkdirr($pathname, $mode = null) {
	// Check if directory already exists
	if(is_dir($pathname) || empty($pathname)) return true;
	
	// Ensure a file does not already exist with the same name
	$pathname = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $pathname);
	if(is_file($pathname)) {
		trigger_error('mkdirr() File exists', E_USER_WARNING);
		return false;
	}
	
	// Crawl up the directory tree
	$next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
	if(mkdirr($next_pathname, $mode)) {
		if(!file_exists($pathname)) return mkdir($pathname, $mode);
	}
	
	return false;
}


?>