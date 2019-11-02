<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */
$pages_array = array(
	array('text' => 'Corporate Identity', 'link' => '/docs/ci/'),
	array('text' => 'Conventions for HTML', 'link' => '/docs/html/'),
	array('text' => 'Styleguide for CSS', 'link' => '/docs/css/'),
	array('text' => 'Machine Tags', 'link' => '/docs/machine-tags/'),
	array('text' => 'Changelog', 'link' => '/docs/changelog/'),
	array('text' => 'Plugins', 'link' => '/docs/plugins/'),
	array('text' => 'Examples and Techniques', 'link' => '/docs/examples/'),
);

/* file setup
=============================================================================================================*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Documentation';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3>Documentation <em>For Creation</em></h3>
			<?php echo createList($pages_array); ?>
		</div>
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>