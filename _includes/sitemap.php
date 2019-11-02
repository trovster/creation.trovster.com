<?php
$default_date = '2007-05-21';
$currentSection = array('xml','sitemap');
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
$sitemap_array = array(); $check_array = array();
foreach($navigationArray as $nav_array) {
	$text = url_encode($nav_array['text']);
	if(!in_array($text,$check_array)) {
		$sitemap_array[$text][0]['title'] = $nav_array['text'];
		$sitemap_array[$text][0]['link'] = $nav_array['link'];
		$sitemap_array[$text][0]['lastmod'] = $default_date;
		$sitemap_array[$text][0]['priority'] = '0.50';
		$sitemap_array[$text][0]['changefreq'] = 'never';
		
		if($text=='news' || $text=='portfolio' || $text=='home') {
			$sitemap_array[$text][0]['changefreq'] = 'weekly';
			$sitemap_array[$text][0]['priority'] = '0.75';
		}
	}
}


// company 
$company_array = profile_setup("ORDER BY ad.Forename = 'Leigh' DESC, ad.Surname = 'Webley' DESC, ad.Surname = 'Rees' DESC, ad.Surname ASC, ad.Forename ASC");
$k=1;
for($i=0; $i<count($company_array); $i++) {
	$sitemap_array['company'][$k]['title'] = 'Company Profile - '.$company_array[$i]['author']['full-name'];
	$sitemap_array['company'][$k]['text']['plain'] = $company_array[$i]['text']['corporate'];
	$sitemap_array['company'][$k]['text']['html'] = formatText($sitemap_array['company'][$k]['text']['plain'],'output');
	$sitemap_array['company'][$k]['link'] = $company_array[$i]['permalink']['safe'];
	$sitemap_array['company'][$k]['lastmod'] = $company_array[$i]['created']['iso8601'];
	$sitemap_array['company'][$k]['priority'] = '0.70';
	$sitemap_array['company'][$k]['changefreq'] = 'monthly';

	if(count($company_array[$i]['hot-topics'])>0) {
		$sitemap_array['company'][$k]['lastmod'] = $company_array[$i]['hot-topics'][0]['created']['iso8601'];
	}	
	$k++;
}

//echo '<pre>'; print_r($sitemap_array['company']); echo '</pre>';


// portfolio
$portfolio_category_check_array = array(); $i=0; $j=0;
$portfolio_query = portfolio_sql();
while($array = mysqli_fetch_array($portfolio_query)) {
	$portfolio_array[$j] = portfolio_setup($array,true);
	if(!in_array($portfolio_array[$j]['category']['safe'],$portfolio_category_check_array)) {
		$portfolio_category_check_array[] = $portfolio_array[$j]['category']['safe'];
		
		$k=0; $i++;
		/*
		$sitemap_array['portfolio'][$i][$k]['title'] = 'Portfolio Section: '.$portfolio_array[$j]['category']['title'];
		$sitemap_array['portfolio'][$i][$k]['link'] = $portfolio_array[$j]['category']['permalink'];
		$sitemap_array['portfolio'][$i][$k]['lastmod'] = $portfolio_array[$j]['category']['updated']['iso8601'];
		$sitemap_array['portfolio'][$i][$k]['priority'] = '0.65';
		$sitemap_array['portfolio'][$i][$k]['changefreq'] = 'never';
		$k++;
		*/
		$sitemap_array['portfolio'][$i][$k]['title'] = 'Portfolio - '.$portfolio_array[$j]['company']['title'].' '.$portfolio_array[$j]['title'];
		$sitemap_array['portfolio'][$i][$k]['text']['plain'] = $array['Detail_Summary'];
		$sitemap_array['portfolio'][$i][$k]['text']['html'] = formatText($sitemap_array['portfolio'][$i][$k]['text']['plain'],'output');
		$sitemap_array['portfolio'][$i][$k]['link'] = $portfolio_array[$j]['permalink'];
		$sitemap_array['portfolio'][$i][$k]['lastmod'] = $portfolio_array[$j]['updated']['iso8601'];
		$sitemap_array['portfolio'][$i][$k]['priority'] = '0.75';
		$sitemap_array['portfolio'][$i][$k]['changefreq'] = 'never';
	}
	else {
		$k++;
		$sitemap_array['portfolio'][$i][$k]['title'] = 'Portfolio - '.$portfolio_array[$j]['title'];
		$sitemap_array['portfolio'][$i][$k]['text']['plain'] = '';
		$sitemap_array['portfolio'][$i][$k]['text']['html'] = '';
		$sitemap_array['portfolio'][$i][$k]['link'] = $portfolio_array[$j]['permalink'];
		$sitemap_array['portfolio'][$i][$k]['lastmod'] = $portfolio_array[$j]['updated']['iso8601'];
		$sitemap_array['portfolio'][$i][$k]['priority'] = '0.75';
		$sitemap_array['portfolio'][$i][$k]['changefreq'] = 'never';
	}
}
//echo '<pre>'; print_r($sitemap_array['portfolio']); echo '</pre>';


// services
$services_sql = "SELECT
					DISTINCT pd.ID AS Detail_ID,
					pd.Title AS Detail_Title,
					pd.Type AS Detail_Type,
					pd.Created AS Detail_Created,
					pd.Updated AS Detail_Updated,
					pd.Safe_URL AS Detail_Safe_URL,
					pd.Summary AS Detail_Summary,
					pd.Description AS Detail_Description,
					pd.Price AS Detail_Price,
					cat.ID AS Category_ID,
					cat.Category,
					cat.Safe_URL AS Category_Safe_URL,
					cat.Created AS Category_Created,
					cat.Updated AS Category_Updated,
					cat.Colour_Dark AS Category_Colour_Dark,
					cat.Colour_Light AS Category_Colour_Light
					FROM services_details AS pd
					LEFT JOIN services_category AS cat ON cat.ID = Services_Category_ID
					WHERE pd.Active = '1'
					GROUP BY pd.ID
					ORDER BY
					cat.Category = 'Consultancy' DESC, cat.Category = 'Print' DESC, cat.Category = 'Websites' DESC,
					cat.Category = 'Branding' DESC, cat.Category = 'Advertising' DESC, cat.Category = 'Display' DESC,
					cat.Category ASC, pd.Position ASC, pd.Title ASC";

$services_query = mysqli_query($connect_admin, $services_sql);
$services_category_check_array = array(); $i=0; $c=0;
while($array = mysqli_fetch_array($services_query)) {
	$services_setup[$c] = services_setup($array,true);
	
	if(!in_array($services_setup[$c]['category']['safe'],$services_category_check_array)) {
		$services_category_check_array[] = $services_setup[$c]['category']['safe'];
		
		$k=0; $i++;
		$sitemap_array['services'][$i][$k]['title'] = 'Services - '.$services_setup[$c]['title'];
		$sitemap_array['services'][$i][$k]['text']['plain'] = $array['Detail_Summary'];
		$sitemap_array['services'][$i][$k]['text']['html'] = formatText($sitemap_array['services'][$i][$k]['text']['plain'],'output');
		$sitemap_array['services'][$i][$k]['link'] = $services_setup[$c]['category']['permalink'];
		$sitemap_array['services'][$i][$k]['lastmod'] = $services_setup[$c]['updated']['iso8601'];
		$sitemap_array['services'][$i][$k]['priority'] = '0.75';
		$sitemap_array['services'][$i][$k]['changefreq'] = 'never';
	}
	else {
		$k++;
		$sitemap_array['services'][$i][$k]['title'] = 'Services - '.$services_setup[$c]['category']['title'].' Solution: '.$services_setup[$c]['title'];
		$sitemap_array['services'][$i][$k]['text']['plain'] = '';
		$sitemap_array['services'][$i][$k]['text']['html'] = '';
		$sitemap_array['services'][$i][$k]['link'] = $services_setup[$c]['permalink'];
		$sitemap_array['services'][$i][$k]['lastmod'] = $services_setup[$c]['updated']['iso8601'];
		$sitemap_array['services'][$i][$k]['priority'] = '0.75';
		$sitemap_array['services'][$i][$k]['changefreq'] = 'never';
	}
}




// news
$news_sql = "SELECT
			 d.ID AS Detail_ID,
			 d.Created AS Detail_Created,
			 d.Updated AS Detail_Updated,
			 d.Title AS Detail_Title,
			 d.Safe_URL AS Detail_Safe_URL,
			 d.Summary AS Detail_Summary,
			 d.Description AS Detail_Description,
			 d.Comments AS Comments_Active,
			 COUNT(cj.ID) AS Comments_Total,
			 s.Section AS Section_Name,
			 ad.ID AS Author_ID,
			 ad.Forename as Author_Forename,
			 ad.Surname AS Author_Surname,
			 ad.Email AS Author_Email,
			 CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name,
			 ap.Title AS Author_Title
			 FROM news_details AS d
			 LEFT JOIN news_comments_join AS cj ON cj.News_Detail_ID = d.ID
			 LEFT JOIN news_section AS s ON s.ID = d.News_Section_ID
			 LEFT JOIN author_details AS ad ON ad.ID = d.CreatedID
			 LEFT JOIN author_profile AS ap ON ad.ID = ap.Author_Detail_ID
			 WHERE d.Active = '1'
			 AND d.News_Section_ID = '1'
			 GROUP BY d.ID
			 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC";
			 
$news_query = mysqli_query($connect_admin, $news_sql);
$news_array = array(); $i=1;
while($array = mysqli_fetch_array($news_query)) {
	$news_array[$i] = news_setup($array);
	$sitemap_array['news'][$i]['title'] = 'News Archive - '.$news_array[$i]['title'];
	$sitemap_array['news'][$i]['text']['plain'] = $array['Detail_Summary'];
	$sitemap_array['news'][$i]['text']['html'] = formatText($sitemap_array['news'][$i]['text']['plain'],'output');
	$sitemap_array['news'][$i]['link'] = $news_array[$i]['permalink']['link'];
	$sitemap_array['news'][$i]['lastmod'] = $news_array[$i]['updated']['iso8601'];
	$sitemap_array['news'][$i]['priority'] = '1.00';
	$sitemap_array['news'][$i]['changefreq'] = 'never';
	if($i==1) $latest_date = $sitemap_array['news'][$i]['lastmod'];
	$i++;
}
$sitemap_array['news'][0]['lastmod'] = $latest_date;

/*
$tag_array = news_tags_setup();
foreach($tag_array as $tag) {
	$sitemap_array['news'][$i]['title'] = 'News Category: '.$tag['text'];
	$sitemap_array['news'][$i]['link'] = $tag['permalink']['link'];
	$sitemap_array['news'][$i]['lastmod'] = $tag['created']['iso8601'];
	$sitemap_array['news'][$i]['priority'] = '0.65';
	$i++;
}
*/

/*
$news_tags_sql = "SELECT t.Created AS Tag_Created,
				  t.Updated AS Tag_Updated,
				  t.Category AS Tag_Title,
				  t.Safe_URL AS Tag_Safe_URL
				  FROM news_category AS t
				  ORDER BY t.Category ASC, t.Created ASC";
$news_tags_query = mysqli_query($connect_admin, $news_tags_sql);
$news_array = array(); $i=count($sitemap_array['news']);
while($array = mysqli_fetch_array($news_tags_query)) {
	$tag_date = news_date_setup($array['Tag_Created']);
	// news_tags_setup
	$sitemap_array['news'][$i]['title'] = 'News Category: '.formatText($array['Tag_Title'],'title');
	$sitemap_array['news'][$i]['link'] = '/news/'.$array['Tag_Safe_URL'];
	$sitemap_array['news'][$i]['lastmod'] = $tag_date['iso8601'];
	$sitemap_array['news'][$i]['priority'] = '0.65';
	$i++;
}
*/

//echo '<pre>'; print_r($sitemap_array); echo '</pre>';
?>