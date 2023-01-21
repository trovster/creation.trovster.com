<?php
function setup_feed($array,$type='') {
	if(!is_array($array)) return false;
	if($type=='comments') {
		$return_array = setup_feed_comments($array);
		setup_feed(setup_feed_information('comments'));
	}
	elseif($type=='author') {
		$return_array = setup_feed_author($array);
		$author_array = setup_feed_author_information($array['author']['id'],'hot-topics');
		$author_comments_array = setup_feed_author_information($array['author']['id'],'comments');
		$return_hot_topic_array = setup_feed_hot_topic($author_array);
		$return_author_comments_array = setup_comments_author($array);
	}
	elseif($type=='tags') {
		$return_array = setup_feed_tags($array);
	}
	else {
		// setup latest 10 comments AND latest 10 comments AND latest hop topics
		$return_array = setup_feed_article($array);
		//echo '<pre>'; print_r($return_array); echo '</pre>';
		//echo '<pre>'; print_r($array); echo '</pre>';
		if(!empty($array['comments']) && !empty($return_array['comments'])) create_feed($array['comments'],$return_array['comments']);
		if(!empty($array['articles']) && !empty($return_array['articles'])) create_feed($array['articles'],$return_array['articles']);
		if(!empty($array['hot-topics']) && !empty($return_array['hot-topics'])) create_feed($array['hot-topics'],$return_array['hot-topics']);
	}

	if($type=='comments' || $type=='author' || $type=='tags') {
		create_feed($array,$return_array);
		if(!empty($author_array) && !empty($return_hot_topic_array)) {
			create_feed($author_array,$return_hot_topic_array);
		}
		if(!empty($author_comments_array) && !empty($return_author_comments_array)) {
			create_feed($author_comments_array,$return_author_comments_array);
		}
	}
}
function setup_feed_comments($array) {
	global $domain;
	$comment_setup = news_comments_setup($array,'c.Created DESC');
	$return_array = array(); $i=0;
	foreach($comment_setup as $key => $comment_array) {
		$return_array[$i]['title'] = 'Comment #'.$comment_array['id'].' on '.$array['title'].' by '.$comment_array['author']['plain'];
		$return_array[$i]['content'] = $comment_array['description']['main'];
		$return_array[$i]['summary'] = $comment_array['description']['summary'];
		$return_array[$i]['link'] = $comment_array['permalink']['link'];
		$return_array[$i]['id'] = $comment_array['permalink']['atom-id'];
		$return_array[$i]['updated'] = $comment_array['updated'];
		$return_array[$i]['published'] = $comment_array['created'];
		$return_array[$i]['author']['name'] = $comment_array['author']['plain'];
		if(!empty($comment_array['author']['url'])) $return_array[$i]['author']['link'] = $comment_array['author']['url'];
		if(!empty($comment_array['author']['image'])) $return_array[$i]['author']['image'] = $comment_array['author']['image'];
		if(!empty($comment_array['author']['admin'])) {
			$return_array[$i]['author']['name'] = $comment_array['author']['admin']['full-name'];
			$return_array[$i]['author']['link'] = $domain.profile_url($comment_array['author']['admin']);
			$return_array[$i]['title'] = 'Comment #'.$comment_array['id'].' on '.$array['title'].' by '.$comment_array['author']['admin']['full-name'];
		}
		$i++;
	}
	return $return_array;
}
function setup_feed_article($array) {
	global $domain;
	$sql_limit = 'LIMIT 0,10';
	$setup_array = related_setup('',$sql_limit);
	$hot_topic_setup_array = hot_topic_setup('',$sql_limit);

	$return_array = array(); $i=0;
	foreach($setup_array as $key => $article_array) {
		$return_array['articles'][$i]['title'] = $article_array['title'];
		$return_array['articles'][$i]['content'] = $article_array['description']['main'];
		$return_array['articles'][$i]['summary'] = $article_array['description']['summary'];
		$return_array['articles'][$i]['link'] = $article_array['permalink']['link'];
		$return_array['articles'][$i]['id'] = $article_array['permalink']['atom-id'];
		$return_array['articles'][$i]['updated'] = $article_array['updated'];
		$return_array['articles'][$i]['published'] = $article_array['created'];
		$return_array['articles'][$i]['author']['name'] = $article_array['author']['full-name'];
		$return_array['articles'][$i]['author']['link'] = $domain.$article_array['author']['url'];
		$return_array['articles'][$i]['author']['image'] = $article_array['author']['image'];
		$return_array['articles'][$i]['tags'] = $article_array['tags']['combined'];
		$i++;
	}

	unset($array['id']);
	$comment_setup = news_comments_setup($array,'c.Created DESC LIMIT 0,10');
	if(!empty($comment_setup)) {
		foreach($comment_setup as $key => $comment_array) {
			$return_array['comments'][$i]['title'] = 'Comment #'.$comment_array['id'].' by '.$comment_array['author']['plain'];
			$return_array['comments'][$i]['content'] = $comment_array['description']['main'];
			$return_array['comments'][$i]['summary'] = $comment_array['description']['summary'];
			$return_array['comments'][$i]['link'] = $comment_array['permalink']['link'];
			$return_array['comments'][$i]['id'] = $comment_array['permalink']['atom-id'];
			$return_array['comments'][$i]['updated'] = $comment_array['updated'];
			$return_array['comments'][$i]['published'] = $comment_array['created'];
			$return_array['comments'][$i]['author']['name'] = $comment_array['author']['plain'];
			if(!empty($comment_array['author']['url'])) $return_array['comments'][$i]['author']['link'] = $comment_array['author']['url'];
			if(!empty($comment_array['author']['image'])) $return_array['comments'][$i]['author']['image'] = $comment_array['author']['image'];
			if(!empty($comment_array['author']['admin'])) {
				$return_array['comments'][$i]['author']['name'] = $comment_array['author']['admin']['full-name'];
				$return_array['comments'][$i]['author']['link'] = $domain.profile_url($comment_array['author']['admin']);
				$return_array['comments'][$i]['title'] = 'Comment #'.$comment_array['id'].' by '.$comment_array['author']['admin']['full-name'];
			}
			$i++;
		}
	}


	foreach($hot_topic_setup_array as $key => $topic_array) {
		$return_array['hot-topics'][$i]['title'] = $topic_array['title'];
		$return_array['hot-topics'][$i]['content'] = $topic_array['description']['main'];
		$return_array['hot-topics'][$i]['summary'] = $topic_array['description']['summary'];
		//$return_array['hot-topics'][$i]['link'] = $topic_array['author']['url'];
		$return_array['hot-topics'][$i]['link'] = $topic_array['permalink']['link'];
		$return_array['hot-topics'][$i]['id'] = $topic_array['permalink']['atom-id'];
		$return_array['hot-topics'][$i]['updated'] = $topic_array['updated'];
		$return_array['hot-topics'][$i]['published'] = $topic_array['created'];
		$return_array['hot-topics'][$i]['author']['name'] = $topic_array['author']['full-name'];
		$return_array['hot-topics'][$i]['author']['link'] = $domain.$topic_array['author']['url'];
		$return_array['hot-topics'][$i]['author']['image'] = $topic_array['author']['image'];
		$i++;
	}


	return $return_array;
}
function setup_feed_author($array) {
	global $domain;
	global $connect_admin;
	$sql_extra = " AND ad.ID = '".mysqli_real_escape_string($connect_admin, $array['author']['id'])."'";
	$sql_limit = 'LIMIT 0,10';
	$setup_array = related_setup($sql_extra,$sql_limit);

	$return_array = array(); $i=0;
	foreach($setup_array as $key => $author_array) {
		$return_array[$i]['title'] = $author_array['title'];
		$return_array[$i]['content'] = $author_array['description']['main'];
		$return_array[$i]['summary'] = $author_array['description']['summary'];
		$return_array[$i]['link'] = $author_array['permalink']['link'];
		$return_array[$i]['id'] = $author_array['permalink']['atom-id'];
		$return_array[$i]['updated'] = $author_array['updated'];
		$return_array[$i]['published'] = $author_array['created'];
		$return_array[$i]['author']['name'] = $array['author']['full-name'];
		$return_array[$i]['author']['link'] = $domain.$array['author']['url'];
		$return_array[$i]['author']['image'] = $array['author']['image'];
		$return_array[$i]['tags'] = $author_array['tags']['combined'];
		$i++;
	}
	return $return_array;
}
function setup_comments_author($array) {
	global $domain;
	global $connect_admin;

	$return_array = array(); $i=0;
	unset($array['id']);
	$comment_setup = news_comments_setup($array,'c.Created DESC LIMIT 0,10',$array['author']['id']);
	if(!empty($comment_setup)) {
		foreach($comment_setup as $key => $comment_array) {
			$return_array[$i]['title'] = 'Comment #'.$comment_array['id'].' by '.$comment_array['author']['admin']['full-name'];
			$return_array[$i]['content'] = $comment_array['description']['main'];
			$return_array[$i]['summary'] = $comment_array['description']['summary'];
			$return_array[$i]['link'] = $comment_array['permalink']['link'];
			$return_array[$i]['id'] = $comment_array['permalink']['atom-id'];
			$return_array[$i]['updated'] = $comment_array['updated'];
			$return_array[$i]['published'] = $comment_array['created'];
			$return_array[$i]['author']['name'] = $comment_array['author']['admin']['full-name'];
			$return_array[$i]['author']['link'] = $domain.profile_url($comment_array['author']['admin']);
			$return_array[$i]['author']['image'] = $comment_array['author']['image'];
			$i++;
		}
	}
	return $return_array;
}
function setup_feed_hot_topic($array) {
	global $domain;
	global $connect_admin;

	$sql_extra = " AND ad.ID = '".mysqli_real_escape_string($connect_admin, $array['author']['id'])."'";
	$sql_limit = 'LIMIT 0,10';
	$setup_array = hot_topic_setup($sql_extra,$sql_limit);

	$return_array = array(); $i=0;
	foreach($setup_array as $key => $author_array) {
		$return_array[$i]['title'] = $author_array['title'];
		$return_array[$i]['content'] = $author_array['description']['main'];
		$return_array[$i]['summary'] = $author_array['description']['main'];
		$return_array[$i]['link'] = $author_array['permalink']['link'];
		$return_array[$i]['id'] = $author_array['permalink']['atom-id'];
		$return_array[$i]['updated'] = $author_array['updated'];
		$return_array[$i]['published'] = $author_array['created'];
		$return_array[$i]['author']['name'] = $array['author']['full-name'];
		$return_array[$i]['author']['link'] = $domain.$array['author']['url'];
		$return_array[$i]['author']['image'] = $array['author']['image'];
		//$return_array[$i]['tags'] = $author_array['tags']['combined'];
		$i++;
	}
	return $return_array;
}
function setup_feed_tags($array) {
	global $domain;
	global $connect_admin;

	$sql_extra = " AND tj.Category_ID = '".mysqli_real_escape_string($connect_admin, $array['id'])."'";
	$sql_limit = 'LIMIT 0,10';
	$sql_order = '';

	$setup_array = related_setup($sql_extra,$sql_limit);

	$return_array = array(); $i=0;
	foreach($setup_array as $key => $author_array) {
		$return_array[$i]['title'] = $author_array['title'];
		$return_array[$i]['content'] = $author_array['description']['main'];
		$return_array[$i]['summary'] = $author_array['description']['main'];
		$return_array[$i]['link'] = $author_array['permalink']['link'];
		$return_array[$i]['id'] = $author_array['permalink']['atom-id'];
		$return_array[$i]['updated'] = $author_array['updated'];
		$return_array[$i]['published'] = $author_array['created'];
		$return_array[$i]['author']['name'] = $author_array['author']['full-name'];
		$return_array[$i]['author']['link'] = $domain.$author_array['author']['url'];
		$return_array[$i]['author']['image'] = $author_array['author']['image'];
		$return_array[$i]['tags'] = $author_array['tags']['combined'];
		$i++;
	}
	return $return_array;
}


/* actual news feeds */
function create_feed($array,$xml_array) {
	global $g_company_domain;
	global $g_company_title;
	global $g_company_name;
	global $g_company_contact_email;
	global $now_timestamp;
	global $domain;
	global $charset;
	global $lang;
	global $mime;

	$xml_rss_body = ''; $xml_atom_body = '';
	$xml_rss_file = ''; $xml_atom_file = '';

	foreach($xml_array as $xml) {
		$xml_atom_body .= "\t".'<entry>'."\n";
		$xml_atom_body .= "\t\t".'<title type="text">'.$xml['title'].'</title>'."\n";
		if(!empty($array['title'])) $xml_atom_body .= "\t\t".'<html:title type="text">'.$array['title'].'</html:title>'."\n";
		$xml_atom_body .= "\t\t".'<content type="html"><![CDATA['.trim($xml['content']).']]></content>'."\n";
		$xml_atom_body .= "\t\t".'<link rel="alternate" href="'.$domain.$xml['link'].'" type="'.$mime.'" hreflang="'.$lang.'" />'."\n";
		$xml_atom_body .= "\t\t".'<id>'.$xml['id'].'</id>'."\n";
		$xml_atom_body .= "\t\t".'<updated>'.$xml['updated']['iso'].'</updated>'."\n";
		$xml_atom_body .= "\t\t".'<published>'.$xml['published']['iso'].'</published>'."\n";
		$xml_atom_body .= "\t\t".'<html:abbr class="published updated" title="'.$xml['updated']['iso8601'].'">'.$xml['updated']['comments'].'</html:abbr>'."\n";
		$xml_atom_body .= "\t\t".'<author>'."\n";
  		$xml_atom_body .= "\t\t\t".'<name>'.$xml['author']['name'].'</name>'."\n";
		if(!empty($xml['author']['image'])) {
			$xml['author']['image']['file']['full-path'] = $domain.$xml['author']['image']['file']['full-path'];
			$xml_atom_body .= "\t\t\t".'<html:img><![CDATA['.trim(image_show($xml['author']['image'])).']]></html:img>'."\n";
		}
		if(!empty($xml['author']['link'])) $xml_atom_body .= "\t\t\t".'<uri>'.$xml['author']['link'].'</uri>'."\n";
		$xml_atom_body .= "\t\t".'</author>'."\n";

		if(!empty($xml['tags'])) {
			foreach($xml['tags'] as $tag) {
				//$xml_atom_body .= "\t\t".'<category term="'.rtrim($tag['safe'],'/').'" label="'.$tag['text'].'" scheme="'.$domain.$tag['link'].'" />'."\n";
			}
		}

		$xml_atom_body .= "\t".'</entry>'."\n";

		$xml_rss_body .= "\t".'<item>'."\n";
		$xml_rss_body .= "\t\t".'<title>'.$xml['title'].'</title>'."\n";
		$xml_rss_body .= "\t\t".'<link>'.$domain.$xml['link'].'</link>'."\n";
		$xml_rss_body .= "\t\t".'<description><![CDATA['.$xml['content'].']]></description>'."\n";
		$xml_rss_body .= "\t\t".'<pubDate>'.$xml['published']['rfc'].'</pubDate>'."\n";
		$xml_rss_body .= "\t\t".'<guid isPermaLink="true">'.$domain.$xml['link'].'</guid>'."\n";
		$xml_rss_body .= "\t\t".'<dc:creator>'.$xml['author']['name'].'</dc:creator>'."\n";
		$xml_rss_body .= "\t".'</item>'."\n";
	}

	if(empty($xml['updated']) || empty($xml_rss_body) || empty($xml_atom_body)) return false;

	/* atom
	============================================================================================================= */
	$xsl_output = '';
	if(is_file($_SERVER['DOCUMENT_ROOT'].$array['xml']['atom']['xsl'])) $xsl_output = '<?xml-stylesheet type="text/xsl" href="'.$array['xml']['atom']['xsl'].'"?>'."\n";

	$xml_atom_file .= '<?xml version="1.0" encoding="'.$charset.'"?>'."\n";
	$xml_atom_file .= $xsl_output;
	$xml_atom_file .= '<feed xmlns="http://www.w3.org/2005/Atom" xml:lang="'.$lang.'"';
	$xml_atom_file .= ' xmlns:slash="http://purl.org/rss/1.0/modules/slash/"';
	$xml_atom_file .= ' xmlns:html="http://www.w3.org/TR/REC-html40"';
	$xml_atom_file .= ' xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
	$xml_atom_file .= '<title type="text">'.$array['xml']['atom']['title'].'</title>'."\n";
	$xml_atom_file .= '<link rel="alternate" type="'.$mime.'" href="'.$domain.$array['permalink']['link'].'" />'."\n";
	$xml_atom_file .= '<link rel="self" type="'.$array['xml']['atom']['mime'].'" href="'.$domain.$array['xml']['atom']['permalink'].'" />'."\n";
	$xml_atom_file .= '<updated>'.formatDate($now_timestamp,'iso').'</updated>'."\n";
	$xml_atom_file .= '<author>'."\n\t".'<name>'.$g_company_name.'</name>'."\n\t".'<email>'.$g_company_contact_email.'</email>'."\n\t".'<uri>'.$domain.'/</uri>'."\n".'</author>'."\n";
	$xml_atom_file .= '<id>'.$domain.'</id>'."\n";
	$xml_atom_file .= '<icon>'.$domain.'/favicon.ico</icon>'."\n";
	$xml_atom_file .= $xml_atom_body;
	$xml_atom_file .= '</feed>';


	/* rss
	============================================================================================================= */
	$xsl_output = '';
	if(is_file($_SERVER['DOCUMENT_ROOT'].$array['xml']['rss']['xsl'])) $xsl_output = '<?xml-stylesheet type="text/xsl" href="'.$array['xml']['rss']['xsl'].'"?>'."\n";

	$xml_rss_file .= '<?xml version="1.0" encoding="'.$charset.'"?>'."\n";
	$xml_rss_file .= $xsl_output;
	$xml_rss_file .= '<rss version="2.0"';
	$xml_rss_file .= ' xmlns:html="http://www.w3.org/TR/REC-html40"';
	$xml_rss_file .= ' xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
	$xml_rss_file .= '<channel>'."\n";
	$xml_rss_file .= '<title>'.$array['xml']['rss']['title'].'</title>'."\n";
	$xml_rss_file .= '<link>'.$domain.'</link>'."\n";
	$xml_rss_file .= '<description>'.$array['xml']['rss']['title'].'</description>'."\n";
	$xml_rss_file .= '<link rel="alternate" type="'.$mime.'" href="'.$domain.$array['permalink']['link'].'" />'."\n";
	$xml_rss_file .= '<link rel="self" type="'.$array['xml']['rss']['mime'].'" href="'.$domain.$array['xml']['rss']['permalink'].'" />'."\n";
	$xml_rss_file .= '<language>'.$lang.'</language>'."\n";
	$xml_rss_file .= '<ttl>300</ttl>'."\n";
	$xml_rss_file .= $xml_rss_body;
	$xml_rss_file .= '</channel>'."\n";
	$xml_rss_file .= '</rss>';


	/* output the files
	============================================================================================================= */
	$filepointer = fopen($_SERVER['DOCUMENT_ROOT'].$array['xml']['atom']['local'], 'w+');
	fputs($filepointer, $xml_atom_file);
	fclose($filepointer);

	$filepointer = fopen($_SERVER['DOCUMENT_ROOT'].$array['xml']['rss']['local'], 'w+');
	fputs($filepointer, $xml_rss_file);
	fclose($filepointer);
}





/* setup feed basics
============================================================================================================= */
function setup_feed_information($type='all') {
	global $g_company_name;
	$array = array();

	if(strtolower($type)=='comments' || strtolower($type)=='all') {
		$array['comments']['permalink'] = '/news/';
		$array['comments']['xml']['path'] = '/xml/';
		$array['comments']['xml']['file'] = 'latest-comments';

		$array['comments']['xml']['rss']['class'] = array('rss','comments','feed');
		$array['comments']['xml']['rss']['rel'] = 'alternate';
		$array['comments']['xml']['rss']['type'] = 'xml';
		$array['comments']['xml']['rss']['mime'] = 'application/rss+xml';
		$array['comments']['xml']['rss']['title'] = $g_company_name.': Latest Comments (RSS)';
		$array['comments']['xml']['rss']['name'] = $array['comments']['xml']['file'].'_rss.xml';
		$array['comments']['xml']['rss']['path'] = $array['comments']['xml']['path'];
		$array['comments']['xml']['rss']['local'] = $array['comments']['xml']['path'].$array['comments']['xml']['rss']['name'];
		$array['comments']['xml']['rss']['permalink'] = '/rss/comments/';
		$array['comments']['xml']['rss']['xsl'] = '/xml/xsl/'.$array['comments']['xml']['file'].'_rss.xsl';

		$array['comments']['xml']['atom']['class'] = array('atom','comments','feed');
		$array['comments']['xml']['atom']['rel'] = 'alternate';
		$array['comments']['xml']['atom']['type'] = 'xml';
		$array['comments']['xml']['atom']['mime'] = 'application/atom+xml';
		$array['comments']['xml']['atom']['title'] = $g_company_name.': Latest Comments (Atom)';
		$array['comments']['xml']['atom']['name'] = $array['comments']['xml']['file'].'_atom.xml';
		$array['comments']['xml']['atom']['path'] = $array['comments']['xml']['path'];
		$array['comments']['xml']['atom']['local'] = $array['comments']['xml']['path'].$array['comments']['xml']['atom']['name'];
		$array['comments']['xml']['atom']['permalink'] = '/atom/comments/';
		$array['comments']['xml']['atom']['xsl'] = '/xml/xsl/'.$array['comments']['xml']['file'].'_atom.xsl';
	}

	if(strtolower($type)=='news' || strtolower($type)=='all') {
		$array['articles']['permalink'] = '/news/';
		$array['articles']['xml']['path'] = '/xml/';
		$array['articles']['xml']['file'] = 'latest-articles';

		$array['articles']['xml']['rss']['class'] = array('rss','articles','feed');
		$array['articles']['xml']['rss']['rel'] = 'alternate';
		$array['articles']['xml']['rss']['type'] = 'xml';
		$array['articles']['xml']['rss']['mime'] = 'application/rss+xml';
		$array['articles']['xml']['rss']['title'] = $g_company_name.': Latest Articles (RSS)';
		$array['articles']['xml']['rss']['name'] = $array['articles']['xml']['file'].'_rss.xml';
		$array['articles']['xml']['rss']['path'] = $array['articles']['xml']['path'];
		$array['articles']['xml']['rss']['local'] = $array['articles']['xml']['path'].$array['articles']['xml']['rss']['name'];
		$array['articles']['xml']['rss']['permalink'] = '/rss/';
		$array['articles']['xml']['rss']['xsl'] = '/xml/xsl/'.$array['articles']['xml']['file'].'_rss.xsl';

		$array['articles']['xml']['atom']['class'] = array('atom','articles','feed');
		$array['articles']['xml']['atom']['rel'] = 'alternate';
		$array['articles']['xml']['atom']['type'] = 'xml';
		$array['articles']['xml']['atom']['mime'] = 'application/atom+xml';
		$array['articles']['xml']['atom']['title'] = $g_company_name.': Latest Articles (Atom)';
		$array['articles']['xml']['atom']['name'] = $array['articles']['xml']['file'].'_atom.xml';
		$array['articles']['xml']['atom']['path'] = $array['articles']['xml']['path'];
		$array['articles']['xml']['atom']['local'] = $array['articles']['xml']['path'].$array['articles']['xml']['atom']['name'];
		$array['articles']['xml']['atom']['permalink'] = '/atom/';
		$array['articles']['xml']['atom']['xsl'] = '/xml/xsl/'.$array['articles']['xml']['file'].'_atom.xsl';
	}

	if(strtolower($type)=='hot-topics' || strtolower($type)=='all') {
		$array['hot-topics']['permalink'] = '/company/';
		$array['hot-topics']['xml']['path'] = '/xml/';
		$array['hot-topics']['xml']['file'] = 'latest-hot-topics';

		$array['hot-topics']['xml']['rss']['class'] = array('rss','hot-topics','feed');
		$array['hot-topics']['xml']['rss']['rel'] = 'alternate';
		$array['hot-topics']['xml']['rss']['type'] = 'xml';
		$array['hot-topics']['xml']['rss']['mime'] = 'application/rss+xml';
		$array['hot-topics']['xml']['rss']['title'] = $g_company_name.': Latest Hot Topics (RSS)';
		$array['hot-topics']['xml']['rss']['name'] = $array['hot-topics']['xml']['file'].'_rss.xml';
		$array['hot-topics']['xml']['rss']['path'] = $array['hot-topics']['xml']['path'];
		$array['hot-topics']['xml']['rss']['local'] = $array['hot-topics']['xml']['path'].$array['hot-topics']['xml']['rss']['name'];
		$array['hot-topics']['xml']['rss']['permalink'] = '/rss/hot-topics/';
		$array['hot-topics']['xml']['rss']['xsl'] = '/xml/xsl/'.$array['hot-topics']['xml']['file'].'_rss.xsl';

		$array['hot-topics']['xml']['atom']['class'] = array('atom','hot-topics','feed');
		$array['hot-topics']['xml']['atom']['rel'] = 'alternate';
		$array['hot-topics']['xml']['atom']['type'] = 'xml';
		$array['hot-topics']['xml']['atom']['mime'] = 'application/atom+xml';
		$array['hot-topics']['xml']['atom']['title'] = $g_company_name.': Latest Hot Topics (Atom)';
		$array['hot-topics']['xml']['atom']['name'] = $array['hot-topics']['xml']['file'].'_atom.xml';
		$array['hot-topics']['xml']['atom']['path'] = $array['hot-topics']['xml']['path'];
		$array['hot-topics']['xml']['atom']['local'] = $array['hot-topics']['xml']['path'].$array['hot-topics']['xml']['atom']['name'];
		$array['hot-topics']['xml']['atom']['permalink'] = '/atom/hot-topics/';
		$array['hot-topics']['xml']['atom']['xsl'] = '/xml/xsl/'.$array['hot-topics']['xml']['file'].'_atom.xsl';
	}

	return $array;
}

function setup_feed_author_information($id,$type='articles') {
	global $g_company_name;
	global $connect_admin;

	$author_sql = "SELECT ad.ID AS Author_ID,
					ad.Forename as Author_Forename,
					ad.Surname AS Author_Surname,
					ad.Email AS Author_Email,
					CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name
					FROM author_details AS ad
					WHERE ad.ID = '".$id."'";

	$author_query = mysqli_query($connect_admin, $author_sql);
	if(mysql_num_rows($author_query)!='1') return false;
	$author_array = mysqli_fetch_array($author_query);

	$name_array = name($author_array['Author_Forename'],$author_array['Author_Surname']);
	$latest_title = 'Articles';

	$author = array();
	$author['author'] = $name_array;
	$author['author']['id'] = $author_array['Author_ID'];
	$author['author']['image'] = image_setup('0',url_encode($name_array['full-name']),'gif','/images/icons/large/','Photo of '.$name_array['full-name'],'','',array('photo'));
	$author['author']['url'] = profile_url($name_array);

	$author['permalink'] = profile_url($name_array);
	$author['xml']['path'] = '/xml/authors/';

	if(strtolower($type)=='hot-topics') {
		$author['xml']['path'] = '/xml/hot-topics/';
		$latest_title = 'Hot Topics';
	}
	if(strtolower($type)=='comments') {
		$author['xml']['path'] = '/xml/authors/comments/';
		$latest_title = 'Comments';
	}

	$author['xml']['file'] = '['.$author_array['Author_ID'].']_'.url_encode($author['author']['full-name']);

	$author['xml']['rss']['class'] = array('rss','feed');
	$author['xml']['rss']['rel'] = 'alternate';
	$author['xml']['rss']['type'] = 'xml';
	$author['xml']['rss']['mime'] = 'application/rss+xml';
	$author['xml']['rss']['title'] = $g_company_name.': Latest '.$latest_title.' by '.$author['author']['full-name'].' (RSS)';
	$author['xml']['rss']['name'] = $author['xml']['file'].'_rss.xml';
	$author['xml']['rss']['path'] = $author['xml']['path'];
	$author['xml']['rss']['local'] = $author['xml']['path'].$author['xml']['rss']['name'];
	$author['xml']['rss']['permalink'] = profile_url($name_array).'rss/';
	$author['xml']['rss']['xsl'] = '/xml/xsl/authors_rss.xsl';

	$author['xml']['atom']['class'] = array('atom','feed');
	$author['xml']['atom']['rel'] = 'alternate';
	$author['xml']['atom']['type'] = 'xml';
	$author['xml']['atom']['mime'] = 'application/atom+xml';
	$author['xml']['atom']['title'] = $g_company_name.': Latest '.$latest_title.' by '.$author['author']['full-name'].' (Atom)';
	$author['xml']['atom']['name'] = $author['xml']['file'].'_atom.xml';
	$author['xml']['atom']['path'] = $author['xml']['path'];
	$author['xml']['atom']['local'] = $author['xml']['path'].$author['xml']['atom']['name'];
	$author['xml']['atom']['permalink'] = profile_url($name_array).'atom/';
	$author['xml']['atom']['xsl'] = '/xml/xsl/authors_atom.xsl';

	if(strtolower($type)=='hot-topics' || strtolower($type)=='comments') {
		$author['xml']['atom']['permalink'] .= strtolower($type).'/';
		$author['xml']['rss']['permalink'] .= strtolower($type).'/';
	}

	return $author;
}

function setup_feed_tag_information($id) {
	global $connect_admin;
	global $g_company_name;
	$tag_sql = "SELECT
				t.ID AS Tag_ID,
				t.Category AS Tag_Title,
				t.Safe_URL AS Tag_Safe_URL,
				t.Description AS Tag_Description
				FROM news_category AS t
				WHERE t.ID = '".mysqli_real_escape_string($connect_admin, $id)."'
				ORDER BY t.Category ASC, t.Created ASC";

	$tag_query = mysqli_query($connect_admin, $tag_sql);
	if(mysql_num_rows($tag_query)!='1') return false;
	$tag_array = mysqli_fetch_array($tag_query);

	$tags = array();
	$tags['id'] = $tag_array['Tag_ID'];
	$tags['permalink'] = '/archives/'.$tag_array['Tag_Safe_URL'];

	$tags['xml']['path'] = '/xml/tags/';
	$tags['xml']['file'] = '['.$tag_array['Tag_ID'].']_'.trim($tag_array['Tag_Safe_URL'],'/');

	$tags['xml']['rss']['class'] = array('rss','feed');
	$tags['xml']['rss']['rel'] = 'alternate';
	$tags['xml']['rss']['type'] = 'xml';
	$tags['xml']['rss']['mime'] = 'application/rss+xml';
	$tags['xml']['rss']['title'] = $g_company_name.': Latest Articles with the Tag "'.formatText($tag_array['Tag_Title'],'capitals').'" (RSS)';
	$tags['xml']['rss']['name'] = $tags['xml']['file'].'_rss.xml';
	$tags['xml']['rss']['path'] = $tags['xml']['path'];
	$tags['xml']['rss']['local'] = $tags['xml']['path'].$tags['xml']['rss']['name'];
	$tags['xml']['rss']['permalink'] = $tags['permalink'].'rss/';
	$tags['xml']['rss']['xsl'] = '/xml/xsl/tags_rss.xsl';

	$tags['xml']['atom']['class'] = array('atom','feed');
	$tags['xml']['atom']['rel'] = 'alternate';
	$tags['xml']['atom']['type'] = 'xml';
	$tags['xml']['atom']['mime'] = 'application/atom+xml';
	$tags['xml']['atom']['title'] = $g_company_name.': Latest Articles with the Tag "'.formatText($tag_array['Tag_Title'],'capitals').'" (Atom)';
	$tags['xml']['atom']['name'] = $tags['xml']['file'].'_atom.xml';
	$tags['xml']['atom']['path'] = $tags['xml']['path'];
	$tags['xml']['atom']['local'] = $tags['xml']['path'].$tags['xml']['atom']['name'];
	$tags['xml']['atom']['permalink'] = $tags['permalink'].'atom/';
	$tags['xml']['atom']['xsl'] = '/xml/xsl/tags_atom.xsl';

	//echo '<pre>'; print_r($tags); echo '</pre>';

	return $tags;
}
?>
