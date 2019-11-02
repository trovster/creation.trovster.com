<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */
$theme_colour_array = array(
	array('text' => 'Concepts', 'colour' => 'Red', 'class' => array('red','concepts'), 'hex' => '', 'cmyk' => ''),
	array('text' => 'Design', 'colour' => 'Blue', 'class' => array('blue','design'), 'hex' => '', 'cmyk' => ''),
	array('text' => 'Websites', 'colour' => 'Green', 'class' => array('green','websites'), 'hex' => '', 'cmyk' => ''),
	array('text' => 'Branding', 'colour' => 'Orange', 'class' => array('orange','branding'), 'hex' => '', 'cmyk' => ''),
	array('text' => 'Advertising', 'colour' => 'Yellow', 'class' => array('yellow','advertising'), 'hex' => '', 'cmyk' => ''),
	array('text' => 'Display', 'colour' => 'Magenta', 'class' => array('magenta','display'), 'hex' => '', 'cmyk' => ''),
);
$theme_list_array = array(); $i=0;
foreach($theme_colour_array as $colour_array) {
	$theme_list_array[$i] = $colour_array;
	$theme_list_array[$i]['text'] .= ' – '.$colour_array['colour'];
	if(!empty($colour_array['hex'])) $theme_list_array[$i]['text'] .= '(#'.$colour_array['hex'].')';
	if(!empty($colour_array['cmyk'])) $theme_list_array[$i]['text'] .= '('.$colour_array['cmyk'].')';
	$i++;
}

/* file setup
=============================================================================================================*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Corporate Identity | Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Corporate Identity';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3>Corporate Identity <em>For Creation</em></h3>
		</div>
		<div class="typography column double last">
			<h3>Font Usage <em>&#38; Typography</em></h3>
			<p>The corporate font for Creation is <em class="font">Interstate Condensed</em>. This is used on the website headings.
			The default body copy for the website is in <em class="font">Helvetica</em>, but falls back to <em class="font">Arial</em> on systems without.</p>
		</div>
		<div class="colours column full second">
			<h3>Colour Usage <em>&#38; Themes</em></h3>
			<div class="column">
				<h4>Colours</h4>
				<p>Headings are made up of two colours,
				<strong class="colour hex_900">red (<code class="css"><span>#900</span></code>)</strong>
				and <strong class="colour hex_999">grey (<code class="css"><span>#999</span></code>)</strong>. Links use the same
				<strong class="colour hex_900">red</strong>.</p>
			</div>
			<div class="column">
				<h4>Theme Colours</h4>
				<?php echo createList($theme_list_array); ?>
			</div>
			<div class="column double last">
				<img src="/images/docs/colours.jpg" alt="" height="356" width="352" />
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