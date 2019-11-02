<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
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

/*
$news_query = mysqli_query($connect_admin, $news_sql);
$news_array_setup = array(); $i=0;
while($news_array = mysqli_fetch_array($news_query)) {
	$news_array_setup[$i] = news_setup($news_array);
	foreach($news_array_setup[$i]['tags']['combined'] as $tag_array) {
		$tag_array = setup_feed_tag_information($tag_array['identifier']);
		setup_feed($tag_array,'tags'); // tags RSS feed
	}
	$i++;
}
*/
// setup author-related feeds - hot-topics / articles
$author_array = company_navigation();
foreach($author_array as $author) {
	$author_array = setup_feed_author_information($author['identifier']);
	//setup_feed($author_array,'author'); // author RSS feed
}
// set up latest feeds
setup_feed(setup_feed_information());
//setup_feed(setup_feed_information('hot-topics'));
setup_feed(setup_feed_information('comments'));
//setup_feed(setup_feed_information('articles'));


/* form setup
============================================================================================================= */

/* setup the header information
============================================================================================================= */