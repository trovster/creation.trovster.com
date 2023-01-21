<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

// find articles under this date
// if only one date is found, redirct to that hot-topic
// else redirect 302 to the month version.

$hot_topic_extra = " AND ad.SafeURL = '".mysqli_real_escape_string($connect_admin, $_GET['subsection'])."'";
$hot_topic_extra .= " AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $_GET['date'])."%'";
$hot_topic_array = hot_topic_setup($hot_topic_extra);

if(count($hot_topic_array)==1) {
	header('HTTP/1.0 302 Permanent Redirect');
	header('Location: '.$hot_topic_array[0]['permalink']['link']);
}
else {
	header("HTTP/1.0 302 Permanent Redirect");
	header('Location: /company/'.$_GET['subsection'].'/'.$_GET['year'].'/'.$_GET['month'].'/');
}
?>
