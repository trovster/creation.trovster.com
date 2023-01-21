<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

// find articles under this date
// if only one date is found, redirct to that permalink
// else redirect 302 to the month version.

$sql = "SELECT
		 d.ID AS Detail_ID,
		 d.Created AS Detail_Created,
		 d.Updated AS Detail_Updated,
		 d.Title AS Detail_Title,
		 d.Safe_URL AS Detail_Safe_URL,
		 d.Summary AS Detail_Summary,
		 d.Description AS Detail_Description,
		 d.Comments AS Comments_Active,
		 COUNT(cj.ID) AS Comments_Total,
		 tj.Category_ID AS Tag_ID,
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
		 LEFT JOIN news_category_join AS tj ON tj.News_Detail_ID = d.ID
		 WHERE d.Active = '1'
		 AND d.News_Section_ID = '1'
		 AND d.Created LIKE '".mysqli_real_escape_string($connect_admin, $_GET['date'])."%'
		 GROUP BY d.ID
		 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC";


$query = mysqli_query($connect_admin, $sql);

if(mysqli_num_rows($query)==1) {
	$array = mysqli_fetch_array($query);
	$news_array = news_setup($array);
	header('HTTP/1.0 302 Permanent Redirect');
	header('Location: '.$news_array['permalink']['link']);
}
else {
	header("HTTP/1.0 302 Permanent Redirect");
	header('Location: /news/'.$_GET['year'].'/'.$_GET['month'].'/');
}
?>
