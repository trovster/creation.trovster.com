<?php
$currentSection = array('xml','sitemap');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/sitemap.php');

/* file information
============================================================================================================= */
$g_filesArray = array(
	'sitemap-xml' => array(
		'title' => 'Google sitemap',
		'rel' => 'alternate',
		'type' => 'xml',
		'mime' => 'application/xml',
		'name' => 'sitemap.xml',
		'path' => '/',
		'local' => '/sitemap.xml',
		'permalink' => '/sitemap/',
		'xsl' => ''
	)
);

$xml_type = 'sitemap-xml';
$file = $g_filesArray[$xml_type]['local'];
$xslt = str_replace('.xml','.xsl',$file);

/* file output - loop
============================================================================================================= */
function sitemap_loop($array) {
	global $domain; global $xml;
	foreach($array as $key => $item) {
		if(is_array($item) && !isset($item['link'])) {
			sitemap_loop($item);
		}
		else {
			$xml .= '<url>'."\n";
			$xml .= "  ".'<loc>'.$domain.$item['link'].'</loc>'."\n";
			$xml .= "  ".'<html:title>'.$item['title'].'</html:title>'."\n";
			if(!empty($item['lastmod'])) 	$xml .= "  ".'<lastmod>'.$item['lastmod'].'</lastmod>'."\n";
			if(!empty($item['changefreq'])) $xml .= "  ".'<changefreq>'.$item['changefreq'].'</changefreq>'."\n";
			if(!empty($item['priority'])) 	$xml .= "  ".'<priority>'.$item['priority'].'</priority>'."\n";
			$xml .= '</url>'."\n";
		}
	}
}
$xml = '';
sitemap_loop($sitemap_array);

$xsltFile = '';
if(is_file($_SERVER['DOCUMENT_ROOT'].$xslt)) $xsltFile = '<?xml-stylesheet type="text/xsl" href="'.$xslt.'"?>';


/* file output
============================================================================================================= */
$xmlFile = '<?xml version="1.0" encoding="UTF-8"?>
'.$xsltFile.'
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:html="http://www.w3.org/TR/REC-html40">
'.trim($xml).'
</urlset>';


/* create the file
============================================================================================================= */
$filepointer = fopen($_SERVER['DOCUMENT_ROOT'].$file, 'w');
fputs($filepointer, $xmlFile);
fclose($filepointer);

//header('Location: '.$g_filesArray[$xml_type]['link']);
?>