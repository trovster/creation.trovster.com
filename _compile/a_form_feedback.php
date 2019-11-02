<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<content>';

/* database setup
============================================================================================================= */
if(!empty($_POST['message-required']) && !empty($_POST['email-required']) && !empty($_POST['location'])) {
	
	$array['message-required'] = $_POST['message-required'];
	$array['email-required'] = $_POST['email-required'];
	$array = stripTags($array,'db');	
	$feedback_array = form_feedback_send($array,$_POST['location'],1);


/* file output
============================================================================================================= */
	echo '<feedback>'."\n";
	echo '<title>'.$feedback_array['title'].'</title>'."\n";
	echo '<text><![CDATA['.formatText($feedback_array['text'],'output').']]></text>'."\n";
	echo '<class>'."\n";
		foreach($feedback_array['class'] as $class_name) echo '<name>'.$class_name.'</name>'."\n";
	echo '</class>'."\n";
	echo '</feedback>'."\n";
}
else {
	echo '<feedback>'."\n";
	echo '<title>Error</title>';
	echo '<text>There was a problem with this request, please try again.</text>';
	echo '<class>error</class>';
	echo '</feedback>';
}
echo '</content>';
?>