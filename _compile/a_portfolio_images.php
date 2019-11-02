<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<content>';

/* database setup
============================================================================================================= */
//
if(!empty($_POST['category']) && !empty($_POST['company']) && !empty($_POST['detail']) && !empty($_POST['id'])) {
	$array['id'] = ltrim($_POST['id'],'e_');
	$query = portfolio_sql($array['id']);
	while($array_setup = mysqli_fetch_array($query)) {
		$array = portfolio_setup($array_setup);
	}

/* file output
============================================================================================================= */
	if(!empty($array['images']) && is_array($array['images']) && count($array['images'])>0) {
		unset($array['images']['total']);
		foreach($array['images'] as $image_array) {
			echo '<img>'."\n";
			echo '<height>'.$image_array['dimensions']['height'].'</height>'."\n";
			echo '<width>'.$image_array['dimensions']['width'].'</width>'."\n";
			echo '<title>'.$image_array['text']['title'].'</title>'."\n";
			echo '<alt>'.$image_array['text']['alt'].'</alt>'."\n";
			echo '<src>'.$image_array['file']['full-path'].'</src>'."\n";
			echo '<id>'.$image_array['id'].'</id>'."\n";
			//echo '<enclosure><![CDATA['.image_show($image_array).']]></enclosure>'."\n";
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