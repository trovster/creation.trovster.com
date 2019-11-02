<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
$style_block = '';

/* information setup
============================================================================================================= */

/* form setup
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = formatText($currentSection[0],'capitals').' | '.$header->title;
$header->className = $currentSection;
$header->className[] = 'hatom';
$header->stylesheet[] = array('file' => formatText('specifics/'.$currentSection[0],'url').'.css', 'media' => 'screen');
$header->stylesheetBlock = $style_block;
$header->heading = formatText($currentSection[0],'capitals');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
//$header->dom = '';
$header->Display();
?>

	<div id="content-primary" class="hfeed">
		
	<?php //echo news_display_summary($active_article,'',array('column','double','first','hfeed','news')); ?>
	
	<div id="content-navigation" class="column double last">
		<h3>Content Navigation</h3>
		<?php //if(!empty($content_navigation_array['navigation'])) echo createList($content_navigation_array['navigation']); ?>
	<!-- end of div id #content-navigation -->
	</div>
	
	<?php
	/*
	echo '<div'.addAttributes('','',array('column','news','mini-container','hfeed','vcard','author','selection')).'>'."\n";
	echo '<h3>More… <em>News</em></h3>'."\n";
	echo news_display_mini($news_array_setup);
	echo '</div>'."\n";
	*/
	
	echo '<div'.addAttributes('','news-archives',array('column','last','news')).'>'."\n";

		echo '<div'.addAttributes('','',array('second')).'>'."\n";
		echo '<h3>Tag Archive</h3>'."\n";
		echo '</div>'."\n";
	
	echo '<!-- end of div id #news-archives -->'."\n";
	echo '</div>'."\n";
	?>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>