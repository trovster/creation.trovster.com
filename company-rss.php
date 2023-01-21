<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */
if(!empty($_GET['section']) && strtolower($_GET['section'])=='company' && !empty($_GET['subsection']) && ($_GET['xml_type']=='rss' || $_GET['xml_type']=='atom')) {

	$sql = "SELECT p.Created AS Profile_Created,
			p.Updated AS Profile_Updated,
			p.Title AS Profile_Title,
			p.Text_Corporate AS Profile_Corporate_Text,
			p.Text_Personal AS Profile_Personal_Text,
			p.DOB AS Profile_DOB,
			ad.ID AS Author_ID,
			ad.Forename as Author_Forename,
			ad.Surname AS Author_Surname,
			ad.Email AS Author_Email,
			CONCAT_WS(' ',ad.Forename,ad.Surname) AS Author_Full_Name
			FROM author_profile AS p
			LEFT JOIN author_details AS ad ON ad.ID = p.Author_Detail_ID
			WHERE ad.Active = '1'
			AND ad.SafeURL = '".mysqli_real_escape_string($connect_admin, strip_tags($_GET['subsection']))."'";

	$query = mysqli_query($connect_admin, $sql);
	$array = mysqli_fetch_array($query);

	if(mysql_num_rows($query)==0) die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));

	if(!empty($_GET['type'])) {
		if(strtolower($_GET['type'])==='hot-topics' || strtolower($_GET['type'])==='comments') {
			$setup_feed_author_array = setup_feed_author_information($array['Author_ID'],strtolower($_GET['type']));
			if(!empty($setup_feed_author_array) && is_file($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml'][$_GET['xml_type']]['local'])) {
				header('Content-type: '.$setup_feed_author_array['xml'][$_GET['xml_type']]['mime']);
				require_once($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml'][$_GET['xml_type']]['local']);
				exit();
			}
			else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
		}
		else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
	}

	$setup_feed_author_array = setup_feed_author_information($array['Author_ID']);
	if(!empty($setup_feed_author_array) && is_file($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml'][$_GET['xml_type']]['local'])) {
		header('Content-type: '.$setup_feed_author_array['xml'][$_GET['xml_type']]['mime']);
		require_once($_SERVER['DOCUMENT_ROOT'].$setup_feed_author_array['xml'][$_GET['xml_type']]['local']);
		exit();
	}
	else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
}
else die(require_once($_SERVER['DOCUMENT_ROOT'].'/_error.php'));
?>
