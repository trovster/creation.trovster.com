<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* database setup
============================================================================================================= */


/* information setup
============================================================================================================= */
$style_block = 'table {
	width: 100%;
	margin-bottom: 40px;
}
table th,
table td {
	padding: 3px 5px;
}
table th {
	font-weight: normal;
}
table thead tr {
	border-bottom: 1px solid #ccc;
}
table tbody tr {
	background-color: #efefef;
}
table tbody td {
	border-right: 1px solid #ccc;
}
table tbody tr.odd {
	background-color: #fff;
}
table tbody tr a:link, table tbody tr a:visited {
	border-bottom-color: #efefef;
}
table tbody tr.odd a:link, table tbody tr.odd a:visited {
	border-bottom-color: #fff;
}
table tr td.highlight {
	background-color: #ddd;
}
table tr.even td.highlight {
	background-color: #bbb;
}
table tr td.highlight a:link, table tr td.highlight a:active {
	border-bottom-color: #ddd;
}
table tr.even td.highlight a:link, table tr.even td.highlight a:active {
	border-bottom-color: #bbb;
}
#content table tbody tr a:hover, #content table tbody tr a:focus, #content table tbody tr a:active {
	border-bottom-color: #900;
}
table #price {
	width: 100px;
}
table #price,
table tbody td[headers=price] {
	text-align: right;
	border-right: 0;
}
table #summary,
table tbody td[headers=summary] {
	padding: 3px 10px;
}';


/* form setup
============================================================================================================= */


/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Price list of services | '.$header->title;
$header->className = $currentSection;
$header->stylesheet[] = array('file' => 'specifics/services.css', 'media' => 'screen');
$header->stylesheetBlock = $style_block;
$header->dom[] = 'scripts/jquery.tablesorter.js';
$header->heading = 'Price List';
$header->Display();
?>

	<div id="content-primary">
		<h3>Price List <em>Our Services</em></h3>
		<?php echo createTable($pricelist_table_array,'Table of all our serivces','Our Services','pricelist'); ?>
	<!-- end of div id #content-primary -->
	</div>
	
	<div id="content-secondary">
	<!-- end of div id #content-secondary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>