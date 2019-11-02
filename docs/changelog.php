<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* defaults
============================================================================================================= */
$meta_description = '';

/* database setup
============================================================================================================= */
//array('text' => 'Added tags/categories to news articles and setup these feeds', 'class' => array('code','php')),
//array('text' => '')

// Fixing prev/next icon display in IE
// Flashing prev/next buttons
// Image progress navigation and javascript



/* information setup
============================================================================================================= */
if(!empty($g_skiplinksArray['navigation'])) unset($g_skiplinksArray['navigation']);

$future_featues_array = array(
	array('text' => 'Enable <a href="http://openid.net" class="external" rel="external">OpenID</a> for comment'),
	array('text' => '<del datetime="20070703">Testimonials at /company/testimonials/</del>'),
	array('text' => 'Display tags using rel-tag on news permalink pages'),
	array('text' => 'Main navigation section for Eshots & archives'),
	array('text' => 'Atom/RSS feeds for Eshots'),
	array('text' => 'hAtom->Atom conversion service internally with this site'),
	array('text' => 'Using machine-tags to include information about related events marked up with vevent'),
	array('text' => 'VR movie of studio'),
	array('text' => '<del datetime="20070703">Atom/RSS feeds for employee comments found at /company/full-name/feed/comments/</del>'),
);

$changelog_array = array(
	array(
		'text' => '<abbr title="2007-10-23" class="created updated">2007-10-23</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Created a <a href="/docs/">documentation section</a> for the website', 'class' => array('section','reason:documentation')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-10-23'
	),
	array(
		'text' => '<abbr title="2007-10-22" class="created updated">2007-10-22</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Updated feedback section, now populating for a database', 'class' => array('section','modified','reason:update')),
				array('text' => 'Developed database to generate and store data for feedback surveys', 'class' => array('database','created','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-10-22'
	),
	array(
		'text' => '<abbr title="2007-10-17" class="created updated">2007-10-17</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Implemented jQuery front-end "edit-in-place" admin tool for News and Hot Topics', 'class' => array('code','javascript','jquery','news','admin')),
				array('text' => 'Added <a href="http://www.appelsiini.net/projects/jeditable" rel="external">Jeditable</a> – an edit in place plugin for jQuery', 'class' => array('code','javascript','jquery','news','admin')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-10-17'
	),
	array(
		'text' => '<abbr title="2007-10-15" class="created updated">2007-10-15</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Created an admin "auto-save" feature in jQuery for Hot Topics', 'class' => array('code','javascript','jquery','news','admin')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-10-15'
	),
	array(
		'text' => '<abbr title="2007-10-02" class="created updated">2007-10-02</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Created a <a href="/feedback/">feedback section</a> for the website', 'class' => array('section','reason:feedback')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-10-02'
	),
	array(
		'text' => '<abbr title="2007-09-25" class="created updated">2007-09-25</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Started development of the administration section', 'class' => array('section','reason:features','admin')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-09-25'
	),
	array(
		'text' => '<abbr title="2007-07-25" class="created updated">2007-07-25</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Added a basic <a href="/services/pricelist/">price list page</a> for quick reference of all available serivces and prices', 'class' => array('page','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-07-03'
	),
	array(
		'text' => '<abbr title="2007-07-03" class="created updated">2007-07-03</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Employee specific comment feeds can be found at /company/full-name/feed/comments/', 'class' => array('code','feed','php')),
				array('text' => 'Updated the RSS and Atom feed generation script to allow for employee specific comments', 'class' => array('code','feed','php')),
				array('text' => 'Modified comments database to store employee specific ID', 'class' => array('modified','database')),
				array('text' => 'Added a basic <a href="/company/testimonials/">testimonials page</a> based on a new database', 'class' => array('page','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-07-03'
	),
	array(
		'text' => '<abbr title="2007-07-02" class="created updated">2007-07-02</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Changed the code for the changelog, to enable hAtom microformat', 'class' => array('modified','reason:features')),
				array('text' => 'Added "Future Features" to this changelog', 'class' => array('modified','reason:update')),
				array('text' => 'Changed from jQuery v1.1.2 to latest version 1.1.3', 'class' => array('code','javascript','jquery','modified','reason:update')),
				array('text' => 'Setup 301 redirect for the old latest Hot Topic feed because of search engines crawling the old link', 'class' => array('feed','modified','reason:consistency')),
				array('text' => 'Added "Status" to the Hot Topic database for easier removal/disabling of articles', 'class' => array('database','modified','reason:features')),
				array('text' => 'Added "Image_Link" to the Hot Topic database to allow the image to be a link', 'class' => array('database','modified','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-07-02'
	),
	array(
		'text' => '<abbr title="2007-06-29" class="created updated">2007-06-29</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Introduced internal CSS to the Eshots via URL-string which maintains external CSS on in-browser permalink', 'class' => array('code','php')),
				array('text' => 'Wrote an external CSS to internal CSS function to import all rules in to the head', 'class' => array('code','php','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-29'
	),
	array(
		'text' => '<abbr title="2007-06-26" class="created updated">2007-06-26</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Created RSS and Atom feeds for employee specific Hot Topics', 'class' => array('feed')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-26'
	),
	array(
		'text' => '<abbr title="2007-06-21" class="created updated">2007-06-21</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Setup this basic changelog', 'class' => array('page')),
				array('text' => 'Added the class "replaced" to submit buttons which use image replacement and modified the CSS', 'class' => array('code','css','modified','reason:consistency')),
				array('text' => 'Replaced the grey previous/next arrows with red arrows', 'class' => array('design','image','modified','reason:design')),
				array('text' => 'Moved the latest Hot Topic feeds to <a href="/atom/hot-topics/" class="feed" rel="alternate" type="application/atom+xml">/atom/hot-topics/</a>', 'class' => array('modified','reason:consistency')),
				array('text' => 'Modified internal Eshot database to include unsubscribe options and bounce-back recording', 'class' => array('database','modified','reason:features')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-21'
	),
	array(
		'text' => '<abbr title="2007-06-20" class="created updated">2007-06-20</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Modified tag handling to allow for separate machine tags', 'class' => array('code','php','modified')),
				array('text' => 'Changed the "Last Modified" handling for profile pages – now using last Hot Topic date'),
				array('text' => 'Wrote jQuery to show Eshot preview when hovering details', 'class' => array('code','javascript','jquery')),
				array('text' => 'Tested Eshot HTML with <a href="http://www.campaignmonitor.com" rel="external" class="external">Campaign Monitor</a>', 'class' => array('testing')),
				array('text' => 'Implemented the Eshot design, image-structure and CSS', 'class' => array('page')),
				array('text' => 'Created a basic <a href="/eshots/">Eshot section</a> to archive our HTML Eshots', 'class' => array('page')),
				array('text' => 'Designed basic internal Eshot database scheme based', 'class' => array('database')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-20'
	),
	array(
		'text' => '<abbr title="2007-06-19" class="created updated">2007-06-19</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Created RSS and Atom feeds for latest Hot Topics', 'class' => array('feed')),
				array('text' => 'Setup permalink pages and URL structure for personal Hot Topics', 'class' => array('page')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-19'
	),
	array(
		'text' => '<abbr title="2007-06-18" class="created updated">2007-06-18</abbr>',
		'definition' => createList(
			array(
				array('text' => 'Fixed the missing heading-level for the Vision on the <a href="/company/">company page</a>', 'class' => array('code','html','modified','reason:error')),
				array('text' => 'Changed the SEO-inspired level-one heading for the "Hello" text on the homepage, due to accessibility issues', 'class' => array('code','html','modified','reason:accessibility')),
				array('text' => 'Removed the ScrollTo function in the Interface jQuery plugin from the skip links', 'class' => array('code','javascript','jquery','modified','reason:accessibility')),
				array('text' => 'Added :focus/:active states to skip links and used CSS to show visually within the design when focused', 'class' => array('code','css','modified','reason:accessibility')),
				array('text' => 'Modified most image-replacement techniques to now use the <a href="http://levin.grundeis.net/files/20030809/alternatefir.html" rel="external" class="external">Gilder/Levin</a> technique.', 'class' => array('code','css','html','modified','reason:accessibility')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-18'
	),
	array(
		'text' => '<abbr title="2007-06-06" class="created updated">2007-06-06</abbr>',
		'definition' => createList(
			array(
				array('text' => '<strong>Site went live!</strong>', 'class' => array('start','released')),
			),
			'',
			array('entry-content')
		),
		'date' => '2007-06-06'
	)
);


/* form setup
============================================================================================================= */
		

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Changelog | Documentation | '.$header->title;
$header->className = $currentSection;
$header->className[] = 'hatom';
$header->heading = 'Changelog';
//$header->metaDescription = $meta_description;
$header->Display();
?>

	<div id="content-primary" class="hfeed">
		<div id="changelog" class="column triple">
			<h3>Changelog <em>Updates to the site</em></h3>
			<?php
			//echo createDefinitionList($changelog_array);
			foreach($changelog_array as $date => $changelog_div) {
				$class_array = array('hentry');
				echo '<div'.addAttributes('','d'.$changelog_div['date'],$class_array).'>'."\n";
					echo '<h4 class="entry-title">Updates for '.$changelog_div['text'].'</h4>'."\n";
					echo $changelog_div['definition'];
				echo '</div>'."\n\n";
			}
			?>
		</div>
		<div id="upcoming-features" class="column last">
			<h3>Upcoming Features <em>For the Future</em></h3>
			<?php echo createList($future_featues_array); ?>
		</div>
	<!-- end of div id #content-primary -->
	</div>

<?php
if(!empty($eshot_array[0]) && !empty($_GET['permalink'])) require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer_eshot.php');
else require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php');
?>