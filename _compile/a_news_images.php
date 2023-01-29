<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<content>';

/* database setup
============================================================================================================= */
if(!empty($_GET['id'])) {
	$array['id'] = ltrim($_GET['id'],'e_');
	$images = news_setup_images($array);

/* file output
============================================================================================================= */
	if(!empty($images) && is_array($images) && count($images)>0) {
		foreach($images as $image_array) {
			echo '<img>'."\n";
			echo '<height>'.$image_array['large']['dimensions']['height'].'</height>'."\n";
			echo '<width>'.$image_array['large']['dimensions']['width'].'</width>'."\n";
			echo '<title>'.$image_array['large']['text']['title'].'</title>'."\n";
			echo '<alt>'.$image_array['large']['text']['alt'].'</alt>'."\n";
			echo '<src>'.$image_array['large']['file']['full-path'].'</src>'."\n";
			echo '<id>'.$image_array['large']['id'].'</id>'."\n";
			//echo '<enclosure><![CDATA['.image_show($image_array['large']).']]></enclosure>'."\n";
			echo '</img>'."\n";
		}
	}
	else {
		echo '<error>'."\n";
		echo '<title>Error</title>';
		echo '<description>No images were found.</description>';
		echo '</error>';
	}
}
else {
	echo '<error>'."\n";
	echo '<title>Error</title>';
	echo '<description>There was a problem with this request, please try again.</description>';
	echo '</error>';
}
echo '</content>';
?>
