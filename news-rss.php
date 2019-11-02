<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
if(!empty($_GET['date']) && !empty($_GET['month']) && !empty($_GET['permalink']) && ($_GET['xml_type']=='rss' || $_GET['xml_type']=='atom')) {
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
				 AND d.Safe_URL = '".mysql_real_escape_string($_GET['permalink'])."/'
				 AND d.Created LIKE '".mysql_real_escape_string($_GET['date'])."%'
				 GROUP BY d.ID
				 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
				 LIMIT 0,1";

	$news_query = mysqli_query($connect_admin, $news_sql);
	
	if(mysql_num_rows($news_query)==0) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
	
	$news_array = mysqli_fetch_array($news_query);
	$news_array_standard = news_setup($news_array);

	if(is_file($_SERVER['DOCUMENT_ROOT'].$news_array_standard['xml'][$_GET['xml_type']]['local'])) {
		header('Content-type: '.$news_array_standard['xml'][$_GET['xml_type']]['mime']);
		require_once($_SERVER['DOCUMENT_ROOT'].$news_array_standard['xml'][$_GET['xml_type']]['local']);
		exit();
	}
	else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
}
else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
?>