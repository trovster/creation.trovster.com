<?php
if(!empty($_REQUEST['save']) && $_REQUEST['save']=='true' && !empty($_REQUEST['language_response']) && !empty($entry_id) && is_numeric($entry_id) && $entry_id!=0) {
	$_REQUEST['language_response'] = strtolower($_REQUEST['language_response']);
	if($_REQUEST['language_response']=='xml') {
		// return the XML
		header('Content-type: text/xml');
		echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		echo '<response>'."\n";
		echo '<identifier>'.$entry_id.'</identifier>'."\n";
		echo '<edit>'.$entry_edit_link.'</edit>'."\n";
		echo '</response>';
	}
	exit();
}
?>