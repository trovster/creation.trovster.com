<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */
$table_array = array(
	'header' => array(
		array('text' => 'Namespace', 'id' => 'namespace'),
		array('text' => 'Predicate', 'id' => 'predicate'),
		array('text' => 'Type', 'id' => 'type'),
		array('text' => 'Example', 'id' => 'example'),
	),
	'rows' => array()
);
$summary = 'A documentation list of "Machine Tags"';
$caption = 'Machine Tags';
$total_headers = count($table_array['header']);

$machine_tag_array = array(
	array(
		'namespace' => 'lastfm',
		'predicate' => 'event',
		'value'		=> '97947',
		'type'		=> 'numeric',
		'class'		=> array(),
	),
	array(
		'namespace' => 'upcoming',
		'predicate' => 'event',
		'value'		=> '270061',
		'type'		=> 'numeric',
		'class'		=> array(),
	),
	array(
		'namespace' => 'upcoming',
		'predicate' => 'group',
		'value'		=> '123',
		'type'		=> 'numeric',
		'class'		=> array(),
	),
	array(
		'namespace' => 'flickr',
		'predicate' => 'user',
		'value'		=> 'trovster',
		'type'		=> 'string',
		'class'		=> array('user'),
	),
	array(
		'namespace' => 'adactio',
		'predicate' => 'post',
		'value'		=> '1274',
		'type'		=> 'numeric',
		'class'		=> array('personal'),
	),
	array(
		'namespace' => 'iso',
		'predicate' => 'isbn',
		'value'		=> '0713998393',
		'type'		=> 'numeric',
		'class'		=> array(),
	),
	array(
		'namespace' => 'iso',
		'predicate' => 'issn',
		'value'		=> '15340295',
		'type'		=> 'numeric',
		'class'		=> array(),
	),
	array(
		'namespace' => 'geo',
		'predicate' => 'lat',
		'value'		=> '52.725975',
		'type'		=> 'numeric/decimal',
		'class'		=> array('location'),
	),
	array(
		'namespace' => 'geo',
		'predicate' => 'long',
		'value'		=> '-2.117808',
		'type'		=> 'numeric/decimal',
		'class'		=> array('location'),
	)
);
$i=0;
foreach($machine_tag_array as $array) {
	$table_array['rows'][$i]['class'] = $array['class'];
	foreach($array as $key => $value) {
		if($key=='value' || $key=='class') continue;
		$table_array['rows'][$i]['value'][] = array(
			'text' => $value,
			//'class' => array($key),
		);
	}
	$table_array['rows'][$i]['value'][] = array(
		'text' => $array['namespace'].':'.$array['predicate'].'='.$array['value'],
		'class' => array('example'),
	);
	$i++;
}


$machine_tag_links_array = array(
	array(
		'text' => 'Machine Tags on Flickr',
		'title' => 'Flickr: Discussing Machine tags in Flickr API',
		'link' => 'http://www.flickr.com/groups/api/discuss/72157594497877875/',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Machine Tags: Tagging Revisited',
		'title' => 'by Drew McLellan',
		'link' => 'http://allinthehead.com/retro/307/machine-tags-tagging-revisited',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Machine Tags and <abbr title="International Standard Book Number">ISBN</abbr>s',
		'title' => 'by Richard Rutter (Clagnut)',
		'link' => 'http://clagnut.com/blog/1907/',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Advanced Tagging and TripleTags',
		'title' => 'by Dan Catt',
		'link' => 'http://geobloggers.com/archives/2006/01/11/advanced-tagging-and-tripletags/',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Flickr Ramps up Triple Tag (Machine Tags) Support',
		'title' => 'by Dan Catt',
		'link' => 'http://geobloggers.com/archives/2007/01/24/offtopic-ish-flickr-ramps-up-triple-tag-support/',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Ghost in the Machine Tags',
		'title' => 'by Jeremy Keith (Adactio)',
		'link' => 'http://adactio.com/journal/1274/',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'Machine Tags',
		'title' => 'A website dedicated to machine tags',
		'link' => 'http://machinetags.org',
		'class' => array(),
		'rel' => array('external'),
	),
	array(
		'text' => 'geo.lici.us: Geotagging Hosted Services',
		'title' => 'by Mikel Maron',
		'link' => 'http://brainoff.com/weblog/2004/11/05/124',
		'class' => array(),
		'rel' => array('external'),
	),
);




/* file setup
=============================================================================================================*/


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Machine Tags | Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Machine Tags';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3>Machine Tags <em>An Introduction</em></h3>
			<p>Machine tags are tags that use a special syntax to define extra information about a tag.
			They have a <strong>namespace, a predicate and a value</strong>.
			The namespace defines a class or a facet that a tag belongs to ('geo', 'flickr', etc.)
			The predicate is name of the property for a namespace ('latitude', 'user', etc.)
			And the value is just the value!</p>
			<p>Just like normal tags, there are no rules for machine tags beyond the syntax to specify the parts of a machine tag.</p>
		</div>
		<div class="column double last">
			<h3>Resources for <em>Machine Tags</em></h3>
			<?php echo createList($machine_tag_links_array); ?>
		</div>
		<div class="column triple second">
			<h3>Example <em>Machine Tags</em></h3>
			<?php echo createTable($table_array,$summary,$caption); ?>
		</div>
		<!--
		<div class="notes second column double">
			<h3>Notes</h3>
		</div>
		-->
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>