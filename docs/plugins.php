<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */
/*
techniques
	CSS:	accessible hiding, image replacement, clearing
	JS:		jeditable on microformat hatom
	HTML:	column layouts

plugins
	JS:		double-select, auto-save
*/


/* file setup
=============================================================================================================*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Plugins | Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Plugins';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="plugin jquery javascript column double">
			<h3>Plugins <em>For jQuery</em></h3>
		</div>
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>