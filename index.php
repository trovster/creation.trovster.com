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
			 d.Active AS Detail_Status,
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
			 ORDER BY d.Created DESC, d.Title ASC, d.Updated DESC
			 LIMIT 0,1";

$news_query = mysqli_query($connect_admin, $news_sql);
$news_array = mysqli_fetch_array($news_query);
$news_array_standard = news_setup($news_array);

/* information setup
============================================================================================================= */
$content_navigation = array(
	array('text' => 'New Agila launch', 'link' => '/news/2008/04/14/new-agila-launch/', 'class' => array('news','news-agila','column')),
	array('text' => 'Rojac Brochure — A Print Portfolio Item', 'link' => '/portfolio/print/rojac-brochure/', 'class' => array('portfolio','portfolio-rojac','column')),

	array('text' => 'Print Services', 'link' => '/services/print/', 'class' => array('services','services-print','column')),
	array('text' => 'Tamworth College Website — A Website Portfolio Item', 'link' => '/portfolio/websites/tamworth-lichfield-college-website/', 'class' => array('portfolio','portfolio-tlc','column')),

	array('text' => 'Website Services', 'link' => '/services/websites/', 'class' => array('portfolio','services-websites','column')),
	array('text' => 'The Fruit Box Co. Logos — A Branding Portfolio Item', 'link' => '/portfolio/branding/the-fruit-box-co-logos/', 'class' => array('services','portfolio-fruitbox','column')),
);
for($i=0; $i<count($content_navigation); $i++) $content_navigation[$i]['gilder'] = true;

$style_block = 'body.welcome #content-navigation ul li.news-agila a,
body.welcome #content-navigation ul li.news-agila a span.gl-ir {
	background-image: url(/images/news/71_cd-and-inner-spread_small.jpg);
}

body.welcome #content-navigation ul li.services-print a,
body.welcome #content-navigation ul li.services-print a span.gl-ir {
	background-image: url(/css/images/services/nav-print.jpg);
}
body.welcome #content-navigation ul li.services-websites a,
body.welcome #content-navigation ul li.services-websites a span.gl-ir {
	background-image: url(/css/images/services/nav-websites.jpg);
}

body.welcome #content-navigation ul li.portfolio-tlc a,
body.welcome #content-navigation ul li.portfolio-tlc a span.gl-ir {
	background-image: url(/images/portfolio/websites/35_tamworth-lichfield-college-website_navigation.jpg);
}
body.welcome #content-navigation ul li.portfolio-rojac a,
body.welcome #content-navigation ul li.portfolio-rojac a span.gl-ir {
	background-image: url(/images/portfolio/print/34_rojac-brochure_navigation.jpg);
}
body.welcome #content-navigation ul li.portfolio-fruitbox a,
body.welcome #content-navigation ul li.portfolio-fruitbox a span.gl-ir {
	background-image: url(/images/portfolio/branding/27_the-fruit-box-co-logos_navigation.jpg);
}';


// TASC, F1 Events,
$portfolio_sql_extra = " AND (pd.Safe_URL = 'instant-training-website/' OR pd.Safe_URL = 'product-branding/')";
$portfolio_query = portfolio_sql('',$portfolio_sql_extra);
$portfolio_array_list = array(); $i=0;
while($portfolio_array = mysqli_fetch_array($portfolio_query)) {
	$portfolio_array_list[] = portfolio_setup($portfolio_array);
}

// f1-events-website/
// product-branding/


/* form setup
============================================================================================================= */
/*
<div id="hello">
	<p><span class="gl-ir"></span><?php echo formatText($g_meta_description); ?></p>
</div>
*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->className = array_merge($header->className,$currentSection);
$header->className[] = 'hatom';
$header->dom[] = 'scripts/jquery.flash.js';
$header->dom = array_merge($header->dom,$logged_in_dom_array);
$header->stylesheetBlock = $style_block;
$header->Display();
?>

	<div id="content-primary" class="hfeed">



		<?php echo news_display_summary($news_array_standard,'latest-news',array('column','double')); ?>

		<div id="content-navigation" class="column double last">
			<h3>Content Navigation</h3>
			<?php if(!empty($content_navigation)) echo createList($content_navigation); ?>
		<!-- end of div id #content-navigation -->
		</div>

		<div class="column selection">
			<h3>Latest Projects… <em>Who's Been Keeping us Busy</em></h3>
			<?php echo createDefinitionList($portfolio_array_list); ?>
		</div>

		<div id="our-vision" class="column last">
			<h3>Creation… <em>Our Vision</em></h3>
			<blockquote>
				<p>To inspire everyone with our dedication and passion for creativity.</p>
			</blockquote>
		</div>

	<!-- end of div id #content-primary -->
	</div>

	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>
