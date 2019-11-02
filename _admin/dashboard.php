<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_admin/_includes/initialise.php');

/* database setup
============================================================================================================= */

/* information setup
============================================================================================================= */

/* form setup
============================================================================================================= */

/* validate / error form setup & post to update
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Administration area | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Administration';
$header->stylesheet[] = $admin_css_array;
$header->stylesheet = array_merge($header->stylesheet,$admin_css_array);
$header->dom = array_merge($header->dom,$admin_js_array);
$header->Display();
?>

	<div id="content-primary">
	
		<div id="introduction">
			<?php echo profile_admin_display($person_details_array,$greet_array,$greet_tagline_array); ?>
			<div id="section-specific" class="column double last">
				<h3>Magic Quote of the Day</h3>
				<p>Every heart is a revolutionary cell.</p>
			</div>
		</div>
		
		<div class="column double">

			<div id="updates" class="info-box">
				<h3>Updates</h3>
				<p>This will have the latest updates to the site, including <a href="/docs/changelog/">changes</a> and new things to explore.
				Remember, you can look up <a href="/telephone/">speed dials and telephone numbers</a> online!</p>
			</div>
			
			
		</div>
		
		<div class="column double last">
			<div id="quick" class="info-box">
				<h3>Quick Links</h3>
				<p>Here are the tasks you may need to do on a regular basis.</p>
				<?php
				$quick_link_array = array(
					array('text' => 'Add a News Article', 'link' => '/admin/news/', 'class' => array('new','news')),
					array('text' => 'Add a new Hot Topic', 'link' => $hot_topic_link, 'class' => array('new','hot-topic')),
					array('text' => 'Add a new Extra Post', 'link' => '/admin/news/extra/', 'class' => array('new','extra')),
					array('text' => 'Edit your Profile Text', 'link' => $profile_link, 'class' => array('edit','profile')),
					//array('text' => 'New Portfolio Entry', 'link' => '#', 'class' => array('new','portfolio')),
					//array('text' => 'Manage Comments', 'link' => '#', 'class' => array('manage','comments')),
				);
				echo createList($quick_link_array);
				?>
			</div>
		</div>
		
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>