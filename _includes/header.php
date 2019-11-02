<?php
class Header {
	// initialise variables and provide default values
	var $author;			var $robots;
	var $title;				var $metaDescription;
	var $metaKeywords;		var $revisit;	
	var $stylesheet;		var $className;
	var $dom;				var $email;
	var $heading;			var $rel;
	var $geoPlaceName;		var $geoRegion;	
	var $geoCountry;		var $geoPosition;
	var $lastUpdated;		var $stylesheetBlock;
	var $eshot_details;
		
	// constructor
	function Header() {
		global $switcher;
		global $g_company_domain;
		global $g_company_name;
		global $g_company_contact_email;
		global $g_company_language;
		global $g_company_title;
		
		global $g_meta_description;
		global $g_meta_keywords;
				
		global $g_geo_placename;
		global $g_geo_county;
		global $g_geo_country;
		global $g_geo_longitude;
		global $g_geo_latitude;
		global $g_geo_nation;
		global $g_company_tagline;
	
		// only need to change once.
		$this->author				= 'Creation Design and Marketing Limited, Penkridge, Staffordshire, UK.';
		$this->robots				= 'index,follow';
		$this->revisit				= '7';
		$this->className			= array('welcome','creation'); // default class name
	
		$this->stylesheet			= array(
			array('file' => 'creation.css',				'media' => 'screen', 	'title' => 'Stylesheet for '.$g_company_name),
			array('file' => 'print.css', 				'media' => 'print', 	'title' => 'Print Stylesheet for '.$g_company_name),
			array('file' => 'handheld.css', 			'media' => 'handheld', 	'title' => 'Handheld Stylesheet for '.$g_company_name),
		);
		//$this->stylesheet[] = array('file' => 'alternate/black/creation.css', 'media' => 'screen', 'title' => 'Alternate "Black" Stylesheet for Creation', 'rel' => 'alternate');
		$this->stylesheetBlock		= '';
		
		$this->dom = array('scripts/jquery.js',
							'scripts/jquery.dimensions.js',
							'scripts/jquery.interface.js',
							'scripts/jquery.cookie.js',
							'general.js');
		//$this->dom[] = 'http://www.google-analytics.com/urchin.js';

		global $sifr;
		if(!empty($sifr) && $sifr==true) {
			// include sifr
			$this->dom = array_merge($this->dom,array('scripts/sifr.js','sifr_setup.js'));
		}
				
		$this->siteURL				= $g_company_domain;
		$this->email				= $g_company_contact_email;
		$this->sitename				= $g_company_name;
				
		$this->geoPlaceName			= $g_geo_placename;
		$this->geoRegion			= $g_geo_county;
		$this->geoCountryCode		= $g_geo_country;
		$this->geoNation			= $g_geo_nation;
		if(!empty($g_geo_latitude) && !empty($g_geo_longitude)) $this->geoPosition = $g_geo_latitude.','.$g_geo_longitude;
				
		$this->title				= $g_company_title;
		if(!empty($g_company_tagline)) $this->title	.= ' - '.$g_company_tagline;
		
		$this->metaDescription		= $g_meta_description;
		$this->metaKeywords			= $g_meta_keywords;
		$this->lang					= $g_company_language;
		
		$this->rel = array(
			array('title' => $this->sitename.' Homepage', 'link' => '/', 'rel' => 'home', 'accesskey' => '1'),
			array('title' => 'Contact information '.$this->sitename, 'link' => '/contact/', 'rel' => 'author', 'accesskey' => '9'),
			array('title' => 'Colophon for '.$this->sitename, 'link' => '/colophon/', 'accesskey' => '0')
		);	
	}
	function Display() {
		require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/mimetype.php');
		if(!isset($lang)) $lang = 'en';
		if(!isset($lang_attribute)) $lang_attribute = 'lang';
		if(!isset($charset)) $charset = 'utf-8';
		if(!isset($mime)) $mime = 'text/html';

  ?><head profile="http://gmpg.org/xfn/11">
	<!-- site created by... -->
	<meta name="author" <?php echo $lang_attribute.'="'.$lang.'"'; ?> content="<?php echo formatText($this->author); ?>" />
	
	<!-- details about the site... -->
	<meta name="copyright" content="<?php echo formatText($this->sitename); ?>" />
	<?php
	if(isset($this->metaDescription) && !empty($this->metaDescription)) {
		echo '<meta name="description" content="'.truncateString(formatText(strip_tags($this->metaDescription)),'255').'" />'."\n\t";
	}
	if(isset($this->metaKeywords) && is_array($this->metaKeywords)) {
		$metaLengthMax = '128'; $metaLength = 0; $metaWords = ''; $meta_check_array = array();
		for($i=0; $i<count($this->metaKeywords); $i++) {
			if(strtolower($this->metaKeywords[$i])=='back to the homepage' || strtolower($this->metaKeywords[$i])=='home') continue;
			$check_var = formatText($this->metaKeywords[$i],'url');
			if(in_array($check_var,$meta_check_array)) continue;
			$meta_check_array[] = $check_var;
			$del = ''; $metaLength += strlen($this->metaKeywords[$i]);
			if($i<(count($this->metaKeywords)-1)) $del = ', ';
			$metaWords .= formatText($this->metaKeywords[$i],'title').$del;
		}
		echo '<meta name="keywords" content="'.trim(strtolower(trim(trim($metaWords),','))).'" />'."\n";
	}
	?>
	<meta name="robots" content="<?php echo formatText($this->robots); ?>" />
	<meta name="language" content="<?php echo formatText($lang); ?>" />	
	<meta http-equiv="content-type" content="<?php echo $mime; ?>; charset=<?php echo $charset; ?>" />
	<meta http-equiv="imagetoolbar" content="no" />
<?php
	if(!empty($this->geoCountryCode)) echo "\t".'<meta name="country" content="'.formatText($this->geoCountryCode).'" />'."\n";

	$dcMeta = "\t".'<!-- http://dublincore.org/documents/1998/09/dces/ -->'."\n";
	$dcMeta .= "\t".'<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Title" content="'.formatText($this->sitename).'" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Creator" content="'.formatText($this->sitename).'" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Publisher" content="'.formatText($this->sitename).'" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Rights" content="'.formatText($this->sitename).'" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Language" content="(SCHEME=ISO.639-2) eng" />'."\n";
	$dcMeta .= "\t".'<meta name="DC.Format" content="'.$mime.'" />'."\n";
	if(!empty($this->lastUpdated)) $dcMeta .= "\t".'<meta name="DC.Date" scheme="ISO8601" content="'.$this->lastUpdated.'" />'."\n";
	if(validate($this->siteURL,'url')) $dcMeta .= "\t".'<meta name="DC.Identifier" content="'.validate($this->siteURL,'url').'" />'."\n";
	
	$geoMeta = ''; $icbm = '';
	$tgnMeta = "\t".'<meta name="tgn.id" content="1030300" />'."\n";
	
	if((!empty($this->geoPlaceName)) || (!empty($this->geoRegion))
	|| (!empty($this->geoCountryCode)) || (!empty($this->geoPosition)))
		$geoMeta .= "\t".'<!-- geo-based meta data - get Greasemap (a greasemonkey script for Firefox) -->'."\n";
	if(isset($this->geoPlaceName) && !empty($this->geoPlaceName)) {
		$geoMeta .= "\t".'<meta name="geo.placename" content="'.formatText($this->geoPlaceName).'" />'."\n";
		
		$tgnMeta .= "\t".'<meta name="tgn.name" content="'.formatText($this->geoPlaceName).'" />'."\n";
	}
	if(isset($this->geoRegion) && !empty($this->geoRegion)) {
		$geoMeta .= "\t".'<meta name="geo.region" content="'.formatText($this->geoRegion).'" />'."\n";
	}
	if(isset($this->geoCountryCode) && !empty($this->geoCountryCode)) {
		$geoMeta .= "\t".'<meta name="geo.country" content="'.formatText($this->geoCountryCode).'" />'."\n";
		$dcMeta .= "\t".'<meta name="DC.Language" scheme="RFC1766" content="'.$lang.'-'.formatText($this->geoCountryCode).'" />'."\n";
	}
	if(isset($this->geoPosition) && !empty($this->geoPosition)) {
		$geoMeta .= "\t".'<meta name="geo.position" content="'.$this->geoPosition.'" />'."\n";
		
		$icbm .= "\t".'<!-- http://geourl.org/ -->'."\n";
		$icbm .= "\t".'<meta name="ICBM" content="'.$this->geoPosition.'" />'."\n";
	}
	if(!empty($this->geoNation)) {
		$tgnMeta .= "\t".'<meta name="tgn.nation" content="'.formatText($this->geoNation).'" />'."\n";
	}
	
	echo "\n".$geoMeta;
	echo "\n".$icbm;
	echo "\n".$dcMeta;
	echo "\n".$tgnMeta;
	
?>
	
	<!-- title... -->
	<title><?php echo strip_tags(formatText($this->title)); ?></title>
	
<?php
	if(isset($this->stylesheet) && !empty($this->stylesheet)) {
		echo "\t".'<!-- stylesheets -->'."\n";
		if(!is_array($this->stylesheet)) $this->stylesheet = array($this->stylesheet);
		
		$script_css_array = array();
		$print_css_array = array();
		$other_css_array = array();
		foreach($this->stylesheet as $stylesheet_array) {
			if(empty($stylesheet_array['file'])) continue;
			elseif(!empty($array['media']) && strtolower($stylesheet_array['media'])=='print') $print_css_array[] = $stylesheet_array;
			elseif(preg_match('#^scripts/#',$stylesheet_array['file'])) $script_css_array[] = $stylesheet_array;
			else $other_css_array[] = $stylesheet_array;
		}
		$this->stylesheet = array_merge($script_css_array,$other_css_array);
		$this->stylesheet = array_merge($this->stylesheet,$print_css_array);
		
		$check_array = array();
		foreach($this->stylesheet as $array) {
			$path = '/css/'; $checkPath = $_SERVER['DOCUMENT_ROOT'];

			$title = '';
			$media = '';
			$stylesheet = '';
			$rel = 'stylesheet';
			$accesskey = '';
			
			if(in_array($array['file'],$check_array)) continue;
			else $check_array[] = $array['file'];
			
			if(!empty($array['title'])) 	$title = ' title="'.formatText($array['title']).'"';
			if(!empty($array['file']))		$stylesheet = $array['file'];
			if(!empty($array['media']))		$media = $array['media'];
			if(!empty($array['rel']) && $array['rel']=='alternate') $rel = $array['rel'].' '.$rel;
			if(!empty($array['accesskey'])) $accesskey = ' accesskey="'.$array['accesskey'].'"';
			$accesskey = '';

			if(substr($stylesheet,0,7)=='http://') {
				$path = ''; $checkPath = '';
			}
			else {
				$stylesheet = formatText($stylesheet,'url');
			}
			if(substr($stylesheet,-4,4)!='.css') {
				$stylesheet .= '.css';
			}
			$media = checkStylesheetMedia($media);

			if(is_file($checkPath.$path.htmlentities($stylesheet))) {
				echo "\t".'<link rel="'.$rel.'" href="'.$path.htmlentities($stylesheet).'" type="text/css" media="'.$media.'"'.$title.$accesskey.' />'."\n";
			}
		}
	}
	
	if(!empty($this->stylesheetBlock)) {
		echo "\n\t".'<!-- internal stylesheet — page specific -->'."\n";
		echo "\t".'<style type="text/css" media="screen,projection">'."\n";
		echo $this->stylesheetBlock;
		echo "\t".'</style>'."\n";
	}
	
	global $g_filesArray; $i=0; $check_files = array();
	if(isset($g_filesArray) && is_array($g_filesArray)) {
		foreach($g_filesArray as $key => $files) {
			if(substr($files['local'],0,7)=='http://' || is_file($_SERVER['DOCUMENT_ROOT'].$files['local'])) {
				if($i==0) echo "\n\t".'<!-- alternative content in different shapes and forms! -->'."\n";
				$check_files[] = $key;
				$accesskey = '';
				if(!empty($files['accesskey'])) $accesskey = ' accesskey="'.$files['accesskey'].'"';
				$accesskey = '';
				//if(in_array('latest-news-atom',$check_files) && $key=='latest-news-rss') continue;
				echo "\t".'<link rel="'.$files['rel'].'" type="'.$files['mime'].'" title="'.$files['title'].'" href="'.$files['permalink'].'"'.$accesskey.' />'."\n";
				$i++;
			}
		}
	}

	if(isset($this->rel) && !empty($this->rel) && is_array($this->rel)) {
		echo "\n\t".'<!-- alternate navigation -->'."\n";
		foreach($this->rel as $alt_rel) {
			//print_r($alt_rel);
			if(empty($alt_rel['link']) || empty($alt_rel['title'])) continue;
			//,@$alt_rel['accesskey']
			echo "\t".'<link href="'.$alt_rel['link'].'"'.addAttributes($alt_rel['title'],'','','',@$alt_rel['rel']).' />'."\n";
		}
	}


	global $domain;
	$favicon = '/favicon.ico';
	if(is_file($_SERVER['DOCUMENT_ROOT'].'/'.$favicon)) {
		echo "\n\t".'<!-- favourite icon... -->'."\n";
		echo "\t".'<link rel="icon" href="'.$favicon.'" type="image/x-icon" />'."\n";
		echo "\t".'<link rel="shortcut icon" href="'.$favicon.'" type="image/x-icon" />'."\n";
	}
	if(isset($this->dom) && !empty($this->dom)) {
		echo "\n\t".'<!-- progessive javascript enhancements... -->'."\n";
		if(isset($this->dom)) {
			$check_dom_array = array();
			if(!is_array($this->dom)) $this->dom = array($this->dom);
			
			$http_dom_array = array();
			$script_dom_array = array();
			$general_dom_array = array();
			$other_dom_array = array();
			foreach($this->dom as $dom) {
				// if starts with scripts
				if(preg_match('#^http://#',$dom)) $http_dom_array[] = $dom;
				elseif(preg_match('#^scripts/#',$dom)) $script_dom_array[] = $dom;
				elseif(preg_match('#^general/#',$dom)) $general_dom_array[] = $dom;
				else $other_dom_array[] = $dom;
			}
			$this->dom = array_merge($http_dom_array,$script_dom_array);
			$this->dom = array_merge($this->dom,$general_dom_array);
			$this->dom = array_merge($this->dom,$other_dom_array);
			$this->dom = array_unique($this->dom);
			
			foreach($this->dom as $dom) {
				$path = '/dom/'; $checkPath = $_SERVER['DOCUMENT_ROOT'];
				
				if(substr($dom,0,5)=='/dom/') $path = '';
				if(substr($dom,0,7)=='http://') {
					$path = ''; $checkPath = '';
				}
				elseif(substr($dom,-3,3)!='.js') $dom .= '.js';

				if(is_file($checkPath.$path.$dom) || substr($dom,0,7)=='http://') {
					if(!in_array($dom,$check_dom_array)) {
						$check_dom_array[] = $dom; // makes the script output unique.
						echo "\t".'<script src="'.$path.htmlentities($dom).'" type="text/javascript"></script>'."\n";
					}
				}
			}
		}
	}

	// IE conditional comments...
	$ieStyle = array(
		array('file' => '_ie.css', 'version' => '7'),
		array('file' => '_ie6.css', 'version' => '6'),
		array('file' => '_ie5.css', 'version' => '5.5000')
	);
	if(!empty($ieStyle) && is_array($ieStyle)) {
		echo "\n";
		foreach($ieStyle as $styles) {
			if(substr($styles['file'],-4,4)!='.css') $styles['file'] .= '.css';
			if(is_file($_SERVER['DOCUMENT_ROOT'].'/css/'.$styles['file'])) {
				echo "\t".'<!--[if lte IE '.$styles['version'].']>'."\n";
				echo "\t".'<link rel="stylesheet" type="text/css" href="/css/'.$styles['file'].'" media="screen,projection" />';
				if($styles['version']=='6' && is_file($_SERVER['DOCUMENT_ROOT'].'/dom/general/ie.js')) {
					echo "\n\t".'<script src="/dom/general/ie.js" type="text/javascript"></script>';
				}
				echo "\n\t".'<![endif]-->'."\n";
			}
		}
	}
?>
</head>
<?php
	$bodyClassArray = array(); $moduleClassArray = array(); $layoutClassArray = array(); $h2_class = array();
	$h2_class[] = 'page-title';
	if(isset($this->className) && !is_array($this->className)) $this->className = array($this->className);
	for($i=0; $i<count($this->className); $i++) {
		if(strpos($this->className[$i],'module-')!==false) {
			if(strpos($this->className[$i],'hfeed')!==false) $this->className[$i] = 'hfeed';
			$moduleClassArray[] = $this->className[$i];
		}
		elseif(strpos($this->className[$i],'layout-')!==false) {
			if(strpos($this->className[$i],'hentry')!==false) $this->className[$i] = 'hentry';
			$layoutClassArray[] = $this->className[$i];
		}
		elseif(strpos($this->className[$i],'type-')!==false) {
			if(strpos($this->className[$i],'news')===false) $h2_class[] = 'entry-title';
		}
		else {
			$bodyClassArray[] = $this->className[$i];
		}
	}

global $switcher;
if(isset($switcher) && $switcher===true) $bodyClassArray[] = 'style-switcher';
?>

<body<?php echo addAttributes('',str_replace(array('http://','.'),array('','-'),$this->siteURL),$bodyClassArray); ?>>

<div<?php echo addAttributes('','container'); ?>>

<?php
	if(!empty($this->eshot_details)) {
		echo '<div id="eshot-cant-view">'."\n";
			echo '<p><strong><a href="'.$domain.$this->eshot_details['link'].'">If the email does not display properly, you can view it online.</a></strong></p>';
		echo '</div>'."\n";
	}
	global $g_skiplinksArray;
	global $g_company_title;
	if(isset($g_skiplinksArray) && is_array($g_skiplinksArray)) echo createList($g_skiplinksArray,'skiplinks');
?>

<div id="branding" class="author vcard">
	<?php
	$branding_attributes = addAttributes('Back to the homepage of '.formatText($this->sitename),'',array('url'),array('me'),array('home'),'1');
	//echo '<h1><a href="/"'.$branding_attributes.'>'.formatText($this->sitename).'</a></h1>'."\n";
	echo '<h1><a href="/"'.$branding_attributes.'><img src="/css/images/general/creation-logo.gif" alt="'.formatText($g_company_title).'" width="135" height="32" class="fn org logo" /></a></h1>'."\n";
	global $g_company_tagline;
	if(!empty($g_company_tagline)) {
		echo "\t".'<h2 id="branding-strapline"><em class="strapline note">'.formatText($g_company_tagline).'</em></h2>'."\n";
	}
	?>
<!-- end of div id #brandng -->
</div>

<div<?php echo addAttributes('','content'); ?>>
<?php
	if(isset($this->heading) && !empty($this->heading))
		echo "\n\t".'<h2>'.formatText($this->heading).'</h2>'."\n";
	}
}
?>