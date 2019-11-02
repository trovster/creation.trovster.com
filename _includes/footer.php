
<!-- end of div id #content -->
</div>

<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/extras.php');

if(isset($navigationArray)) {
	for($i=0; $i<count($navigationArray); $i++) {
		if(!isset($navigationArray[$i])) continue;
		if(in_array(formatText($navigationArray[$i]['text'],'url'),$currentSection) || (isset($navigationArray[$i]['id']) && in_array(formatText($navigationArray[$i]['id'],'url'),$currentSection))) {
			if(!is_array($navigationArray[$i]['class'])) $navigationArray[$i]['class'] = array($navigationArray[$i]['class']);
			$navigationArray[$i]['class'][] = 'active';
		}
	}
}

if((isset($navigationArray) && is_array($navigationArray)) || (isset($subNavArray) && is_array($subNavArray))) {
	echo '<div id="navigation">'."\n";
	if(isset($navigationArray) && is_array($navigationArray)) {
		echo '<div id="navigation-primary">'."\n";
			echo '<h2>Navigation</h2>'."\n";
			echo createList($navigationArray);
			//echo createList($navigationArray,'','','ul',true);
			echo '<!-- end of div id #navigation-primary -->'."\n";
		echo '</div>'."\n";
	}
	if(isset($subNavArray) && is_array($subNavArray)) {
		echo '<div id="navigation-secondary">'."\n";
			echo '<h2>Secondary Navigation</h2>'."\n";
			echo createList($subNavArray);
			echo '<!-- end of div id #navigation-secondary -->'."\n";
		echo '</div>'."\n";
	}
	echo '<!-- end of div id #navigation -->'."\n";
	echo '</div>'."\n";
	if(isset($logoout)) echo $logoout;
}
?>

<div id="footer">
	<h2>Footer</h2>
	<?php if(!in_array('error',$currentSection) && (empty($admin) || (!empty($admin) && $admin!=true))) {
	/*
	<div id="footer-extra">
		<div id="footer-extra-history" class="column double first">
			<h3>The Dawn of Creation: <em>A brief history</em></h3>
			<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec ante metus, nonummy sed, laoreet fermentum, placerat eget,
			dolor. Vestibulum dui lacus, tempus ac, volutpat et, imperdiet ut, quam. Fusce elementum vestibulum massa. Curabitur mauris
			magna, ultrices et, cursus cursus, blandit sed, leo.</p>
		<!-- end of div id #footer-extra-history -->
		</div>
		<div id="footer-extra-vision" class="column">
			<h3>Our Vision <em>2006</em></h3>
			<blockquote><p><strong>To inspire everyone with our dedication and passion for creativity.</strong></p></blockquote>
		<!-- end of div id #footer-extra-something -->
		</div>
		<div id="footer-extra-feedback" class="column last">
			<h3>Feedback: <em>Contact us here</em></h3>
			<?php echo form_feedback_show(); ?>
		<!-- end of div id #footer-extra-feedback -->
		</div>
	<!-- end of div id #footer-extra-feedback -->
	</div>
	*/
	} ?>
	<div id="footer-copyright">
		<p class="vcard author"><span class="gl-ir"></span>© 1995-<?php echo date('Y',strtotime('NOW'));?> <strong class="org fn"><?php echo formatText($header->sitename); ?></strong>
		<?php if(!empty($g_company_tagline)) echo ' - <em class="strapline">'.formatText($g_company_tagline).'</em>'; ?></p>
		<p class="top"><a href="#www-creation-uk-com"><span class="gl-ir"></span>Go to top</a></p>
		<p class="technorati"><a href="http://technorati.com/claim/g3z5s3rfs" rel="me">Technorati Profile</a></p>
	<!-- end of div id #footer-copyright -->
	</div>
<!-- end of div id #footer -->
</div>

<!-- end of div id #container -->
</div>


<script type="text/javascript">
//_uacct = "UA-2821190-1";
//urchinTracker();
</script>

</body>
</html>

<?php
if(!empty($cache) && is_object($cache)) $cache->end();
/* // Rendering time 
$time_end = microtime_float();
$time = $time_end - $time_start;
echo "Parsed in $time seconds\n";
*/
?>