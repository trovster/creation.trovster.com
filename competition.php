<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/specifics/eshot.php');


/*
Competition URL Structure

/competition/									=> overview of competition, information about the latest competition
/competition/title-of-competition/				=> specific competition, rules, leaderboard, price info, winner information
*/



/* defaults
============================================================================================================= */


/* database setup
============================================================================================================= */


/* information setup
============================================================================================================= */
$meta_description = '';


/* form setup
============================================================================================================= */
		

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Competition | '.$g_company_title;
$header->className = $currentSection;
$header->className[] = 'hatom';
$header->stylesheet[] = array('file' => 'specifics/competition.css', 'media' => 'screen');
$header->heading = formatText($currentSection[0],'capitals');
$header->metaDescription = $meta_description;
$header->Display();
?>

	<div id="content-primary" class="hfeed">
		<h3>Competition</h3>
	<!-- end of div id #content-primary -->
	</div>

<?php
if(!empty($eshot_array[0]) && !empty($_GET['permalink'])) require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer_eshot.php');
else require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php');
?>