<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */

/* information setup
============================================================================================================= */
$directions_south = array(
	array('text' => 'Following A449 towards Wolverhampton'),
	array('text' => '4 miles to Penkridge, over river bridge'),
	array('text' => 'First right after George & Fox Pub'),
	array('text' => '200 metres on right, before bridge'),
	array('text' => '76 Pinfold Lane blue and gold railings'),
	array('text' => 'Park at rear of building')
);
$directions_north = array(
	array('text' => 'Following A5 towards Telford, 1 mile to island'),
	array('text' => 'Turn right A449 to Stafford, 2 miles'),
	array('text' => 'Over next island at Texaco Garage'),
	array('text' => 'First left after Ford garage into Pinfold Lane'),
	array('text' => '200 metres on right, before bridge'),
	array('text' => '76 Pinfold Lane blue and gold railings'),
	array('text' => 'Park at rear of building')
);

$map_list = array(); $i=0; $page_url = '/contact/';
foreach($contact_sub_nav as $map_name => $map_array) {
	$map_name_safe = url_encode($map_name);
	$map_name_text = formatText($map_name,'title');
	$map_url = '/contact/';
	
	if($map_name_safe!='national') $map_url .= $map_name_safe.'/';
	
	$map_list[$i]['text'] = $map_name_text;
	$map_list[$i]['class'] = array($map_name_safe,'column');
	$map_list[$i]['link'] = $map_url;
	$map_list[$i]['id'] = 'z'.$map_array['zoom'];
	$map_list[$i]['gilder'] = true;
	//$map_list[$i]['accesskey'] = $map_array['accesskey'];
	
	if(!empty($_GET['zoom']) && strtolower($_GET['zoom'])==$map_name_safe) {
		$map_list[$i]['class'][] = 'active';
		$map_image = $map_name_safe;
		$page_url = $map_url;
		$longdesc = '';
		//$longdesc = '/_files/map/'.$map_name_safe.'.txt';
	}
	$i++;
}
$map_image_large = image_setup(0,$map_image,'gif','/images/map/','','','','',$longdesc);

$contact_us_output = form_contact_feedback_show($page_url);


/* form setup
============================================================================================================= */
$g_skiplinksArray['form-contact'] = array('text' => 'Skip to contact form', 'title' => 'Skip to the contact form on this page', 'link' => '#contact-feedback');


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = formatText($currentSection[0],'capitals').' | '.$header->title;
$header->className = $currentSection;
$header->stylesheet[] = array('file' => 'specifics/contact.css', 'media' => 'screen');
$header->dom[] = 'scripts/jquery.metadata.js';
$header->dom[] = 'scripts/jquery.validate.min.js';
$header->dom[] = 'forms.js';
if(!empty($g_apiArray['google']['maps'])) {
	$header->dom[] = 'http://maps.google.com/maps?file=api&v=2&key='.$g_apiArray['google']['maps'];
	$header->dom[] = 'contact.js';
}
$header->heading = 'Contact';
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div id="contact" class="column double">
			<div id="contact-map" class="image">
			<?php echo image_show($map_image_large); ?>
			</div>
			
			<h3>How to find us</h3>
			<h4 class="subtitle">Directions from North</h4>
			<h5>M6 - Junction 13</h5>
			<?php echo createList($directions_north); ?>
			
			<h4 class="subtitle">Directions from South</h4>
			<h5>M6 - Junction 12</h5>
			<?php echo createList($directions_south); ?>
		</div>
		
		<div id="content-navigation" class="column">
			<h3>Content Navigation</h3>
			<?php echo createList($map_list); ?>
		</div>
		
		<div id="contact-address" class="column last vcard">
			<h3>Address</h3>
			<h4 class="fn org"><span class="organization-name">Creation</span> <span class="organization-unit">design &#38; marketing</span></h4>
			<address class="adr">
				<span class="street-address">76 Pinfold Lane</span>
				<span class="locality">Penkridge</span>
				<span class="region">Staffordshire</span>
				<span class="postal-code">ST19 5AP</span>
			</address>
			<dl>
				<dt>Tel:</dt>
				<dd class="tel"><abbr class="type" title="Telephone">Tel:</abbr> <span class="value">01785 716 136</span></dd>
				<dt>Fax:</dt>
				<dd class="tel fax"><abbr class="type" title="Facsimile">Fax:</abbr> <span class="value">01785 716 137</span></dd>
				<dt>Email</dt>
				<dd><a href="mailto:<?php echo $g_company_contact_email ?>" class="email"><?php echo $g_company_contact_email ?></a></dd>
			</dl>
			<div class="geo">
				<span class="latitude" title="52.72570">52:43:33N</span>, 
				<span class="longitude" title="-2.11885">2:07:04W</span>
			</div>
			<a href="#branding" class="include"></a>
		</div>
			
		<div id="contact-information" class="column">
			<h3>Download... <em>Location Map</em></h3>
			<p class="pdf"><a href="/files/directions-to-creation.pdf" type="application/pdf" rel="alternate">Printable PDF <span class="size">60k</span></a></p>
			
			<h3 class="second">Important... <em>Notice!</em></h3>
			<blockquote><p>Watch out for the speed cameras.</p></blockquote>
		</div>
		
		<div id="contact-feedback" class="column last">
			<h3>Something to Say? <em>Contact Us</em></h3>
			<?php echo $contact_us_output; ?>
		</div>

	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>