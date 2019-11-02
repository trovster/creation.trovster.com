<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */
$layout_basic_array = array(
	array('text' => 'Container – #container', 'definition' => 'Wrapper for the entire website.'),
	array('text' => 'Header – #branding', 'definition' => 'Features the site name/logo and strapline. Marked up with a vcard.'),
	array('text' => 'Content – #content', 'definition' => 'Wrapper for the site content.'),
	array('text' => 'Primary Content – #content-primary', 'definition' => 'The main content for the website.'),
	array('text' => 'Navigation – #navigation', 'definition' => 'Wrapper for site navigation.'),
	array('text' => 'Primary Navigation – #navigation-primary', 'definition' => 'The main navigation for the website.'),
	array('text' => 'Footer – #footer', 'definition' => 'Footer of the website.'),
);
$layout_grid_array = array(
	array('text' => '.column', 'definition' => 'Set up a basic single column with right margin.'),
	array('text' => '.column.double', 'definition' => 'A double column with right margin.'),
	array('text' => '.column.triple', 'definition' => 'A triple column with right margin.'),
	array('text' => '.column.full', 'definition' => 'Full width of the site.'),
	array('text' => '.column.last', 'definition' => 'Removes the right margin.'),
);

/* file setup
=============================================================================================================*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Conventions for HTML | Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Conventions for HTML';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3>Conventions for <abbr title="Hypertext Markup Language">HTML</abbr> <em>For Creation</em></h3>
			<p>Documentation for <abbr title="Hypertext Markup Language">HTML</abbr> on the Creation website including naming conventions
			for containers and creating columns.</p>
		</div>
		
		<div class="layout column full second">
			<h3>Layout Basics <em>&#38; Grid Setup</em></h3>
			<div class="column double">
				<h4>Layout Basics</h4>
				<p>Every page has four main sections: the header, content, navigation and footer. The order and IDs for the main sections are as follows:</p>
				<?php echo createDefinitionList($layout_basic_array); ?>
			</div>
			<div class="column double last">
				<h4>Grid</h4>
				<p>The site is based upon a <strong>four-column</strong> grid. Each column is 168px wide, with a right-side margin of 16px.
				Below are the following classes which are used to create the columns:</p>
				<?php echo createDefinitionList($layout_grid_array); ?>
				<p>Any combination of these rules can used, but to successfully create the columns, each area should add up to four, and the last element
				must have the class 'last' in order for the columns to fit correctly.</p>
				<p><strong>Note:</strong> You can not use the multiple classes above in the <abbr title="Cascading Style Sheets">CSS</abbr>
				– this will not work correctly in <abbr title="Internet Explorer">IE</abbr>. Use them singularly in the
				<abbr title="Cascading Style Sheets">CSS</abbr>, and in multiples within the <abbr title="Hypertext Markup Language">HTML</abbr>.</p>
			</div>
		</div>
		
		<!--
		<div class="notes second column double">
			<h3>Notes</h3>
		</div>
		-->
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>