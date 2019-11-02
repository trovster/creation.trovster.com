<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */

/* information setup
============================================================================================================= */
$accesskey_array = array(
	array(
		'text' => '1',
		'definition' => 'Homepage'
	),
	array(
		'text' => '2',
		'definition' => 'Skip to content'
	),
	/*
	array(
		'text' => '3',
		'definition' => 'Sitemap'
	),
	*/
	array(
		'text' => '9',
		'definition' => 'Contact'
	),
	/*
	array(
		'text' => '0',
		'definition' => 'Accessibility Statement'
	),
	*/
	array(
		'text' => 'x',
		'definition' => 'Next Image'
	),
	array(
		'text' => 'z',
		'definition' => 'Previous Image'
	)
);
$accesskey_help_array = array(
	array(
		'text' => 'Internet Explorer 7',
		'definition' => 'In the menu bar choose "View" and choose "Text Size" then choose a size, either Largest, Larger, Medium (default size), Smaller or Smallest.'
	),
	array(
		'text' => 'Internet Explorer 6',
		'definition' => 'Hold down the <kbd title="Control">CTRL</kbd> key and press either the "-" (minus) key to reduce the text size or the "+" (plus) key to increase the text size.'
	),
	array(
		'text' => 'Firefox 2',
		'definition' => 'Hold down the <kbd title="Control">CTRL</kbd> key and press either the "-" (minus) key to reduce the text size or the "+" (plus) key to increase the text size. Holding down the <kbd title="Control">CTRL</kbd> key and pressing the "0" key resets the font size to the default.'
	),
	array(
		'text' => 'Mac OSX',
		'definition' => 'If you are using Mac OSX use the <kbd>Command</kbd> key instead of the <kbd title="Control">CTRL</kbd> key.'
	),
);
$font_resize_array = array(
	array('text' => 'View→Text Size'),
	array('text' => 'View→Text Zoom'),
	array('text' => 'View→Zoom'),
	array('text' => 'Ctrl+<kbd>+</kbd>/Ctrl+<kbd>-</kbd>'),
);

$subscribe_array = array(
	array(
		'text' => 'News (Latest Articles & Comments)',
		'link' => '/news/'
	),
	array(
		'text' => 'Company Profiles',
		'link' => '/company/'
	),
); $i=0;

$browser_support_array = array(
	'mozilla-firefox' => array('text' => 'Mozilla Firefox 1.5+'),
	'internet-explorer' => array('text' => 'Internet Explorer 6+'),
	'safari' => array('text' => 'Safari'),
	'opera' => array('text' => 'Opera'),
);
sort($browser_support_array);


/* form setup
============================================================================================================= */

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = formatText($currentSection[0],'capitals').' | '.$header->title;
$header->className = $currentSection;
$header->heading = formatText($currentSection[0],'capitals');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="column double">
			
			<div id="colophon">
				<span class="gl-ir"></span>
				<h3>Colophon</h3>
				<p>A publisher's emblem or imprint, especially one on the title page or spine of a book.
				Historical a statement at the end of a book, typically wit ha rinter's emblem, giving information
				about its authorship and printing. Origin early 17th centuary, via late Latin from Geek kolophõn 'summmit or finishing touch.'</p>
			</div>
			
			<div id="information">
				<h3>Information… <em>About this Site</em></h3>
				<p>You are looking at the official website for <strong><?php echo formatText($g_company_title); ?></strong> —
				<?php echo formatText($g_company_tagline); ?>. We create the very best in graphic design and accessible websites
				for all of our clients both nationally and internationally.</p>
				
				<h4>Creating the site</h4>
				<p>This site is designed on <a href="http://www.apple.com/uk" title="Macintosh" rel="external" class="external">Macs</a> using Adobe Photoshop and
				created on <a href="http://www.microsoft.com" rel="external" class="external">Windows XP</a> using Dreamweaver,
				<abbr title="PHP Hypertext Preprocessor" class="initialism language recursive">PHP</abbr> 4 and <abbr title="My Structured Query Language" class="acronym language database">MySQL</abbr>.
				Our hosting is provided by <a href="http://www.phurix.co.uk" rel="external" class="external hosting">Phurix</a>. Fonts used include
				<a href="http://www.fontbureau.com/fonts/Interstate" rel="external" class="external font">Interstate Condensed</a> and
				<a href="http://www.dsg4.com/04/extra/bitmap/" rel="external" class="external font">04b-24</a> within graphic elements, and
				<a href="http://en.wikipedia.org/wiki/Helvetica" rel="external" class="external font">Helvetica</a> for live text.</p>

				<p>All content has been conceived, compiled, written and submitted by members of the <a href="/company/">Creation team</a>.</p>
				
				<p>We welcome your feedback and suggestions for improvement to our website. Please send your comments to
				<a href="mailto:leigh@creation.uk.com" class="email">leigh@creation.uk.com</a></p>
			</div>
			

			<div id="subscriptions" class="second">
				<h3>Subscriptions <em>and web feeds</em></h3>
				<p>This website has multiple feeds which allow you to be informed when new content is published. Feeds are commonly know as <abbr title="RDF Site Summary">RSS</abbr>.
				You can find more information <a href="http://news.bbc.co.uk/1/hi/help/rss/default.stm" rel="external" class="external">about news feeds on the <abbr title="British Broadcasting Corporation">BBC website</abbr></a>.</p>
				<?php
				if(!empty($subscribe_array)) {
					echo '<p>You can find feeds for the following information:</p>'."\n";
					echo createList($subscribe_array);
				}
				?>
			</div>
		</div>
		<div class="column double last">
			<div id="document-validation">
				<h3>Document strucure, <em>Validation and Browser Support</em></h3>
				<p>The content of each page is marked up in structural <abbr title="HyperText Markup Language">HTML</abbr>.
				All design related images and style is applied with <abbr title="Cascading Style Sheets">CSS</abbr>. Applications can be configured
				to turn off this style and present the pure semantic markup.</p>
				
				<p>The website follows the <a href="http://www.w3c.org" rel="external" class="external" title="W3C">World Wide Web Consortium</a> recommendations
				for <abbr title="HyperText Markup Language">HTML</abbr> and <abbr title="Cascading Style Sheets">CSS</abbr> and strives to have
				valid codes throughout. Accessibility also is important and this website follows many of the checkpoints outlined in
				the <abbr title="World Wide Web Consortium">W3C</abbr> Web Accessibilty Initiative <a href="http://www.w3.org/TR/WCAG10/" rel="external" class="external">Web Content Accessibility Guidelines 1.0</a></p>
			
				<p>Progessive enhancement is used throughout the website in the form of both <abbr title="Cascading Style Sheets">CSS</abbr> and
				JavaScript (<a href="http://jquery.com" rel="external" class="external">jQuery</a>). Every effort has been taken so that information is accessible when these technologies are unavailable.</p>
				
				<p>This website aims to operate fully featured in modern web browsers, including (but not exclusive to):</p>
				<?php echo createList($browser_support_array);?>
			</div>
			
			<div id="accesskeys" class="second">
				<h3>Accesskey <em>Information</em></h3>
				<p>Most Web browsers support jumping to specific links by typing keys defined on the Web site.
				In Windows, you can press <kbd>Alt</kbd> + an access key (if you are using Internet Explorer, press <kbd>Return</kbd> to follow the link);
				on Macintosh, you can press <kbd>Control</kbd> + an access key.</p>
				<p>The accesskeys used throughout this website are based on the UK Government's recommendations.</p>
				<?php echo createDefinitionList($accesskey_array); ?>
			</div>
			
			<div id="font-resizing" class="second">
				<h3>Font <em>resizing</em></h3>
				<p>Font sizes throughout this website are defined relatively, which means that they are determined by the browser preferences.
				Text can be resized using the following common commands:</p>
				<?php echo createList($font_resize_array); ?>
			</div>

		</div>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>