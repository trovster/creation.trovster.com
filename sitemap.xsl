<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:html="http://www.w3.org/TR/REC-html40">
	<xsl:output doctype-system="http://www.w3.org/TR/html4/strict.dtd" doctype-public="-//W3C//DTD HTML 4.1 Strict//EN" encoding="UTF-8" method="html" omit-xml-declaration="yes"/>
	<xsl:template match="/">
		<html>
			<head>
				<title>Sitemap</title>
				
				<xsl:element name="link">
					<xsl:attribute name="type">text/css</xsl:attribute>
					<xsl:attribute name="href">/css/creation.css</xsl:attribute>
					<xsl:attribute name="title">screen</xsl:attribute>
					<xsl:attribute name="media">screen,projection</xsl:attribute>
					<xsl:attribute name="rel">stylesheet</xsl:attribute>
				</xsl:element>
				
				<xsl:element name="link">
					<xsl:attribute name="type">text/css</xsl:attribute>
					<xsl:attribute name="href">/css/xml.css</xsl:attribute>
					<xsl:attribute name="title">screen</xsl:attribute>
					<xsl:attribute name="media">screen,projection</xsl:attribute>
					<xsl:attribute name="rel">stylesheet</xsl:attribute>
				</xsl:element>
				
				<xsl:element name="script">
					<xsl:attribute name="type">text/javascript</xsl:attribute>
					<xsl:attribute name="src">/dom/scripts/jquery.js</xsl:attribute>
				</xsl:element>
				<xsl:element name="script">
					<xsl:attribute name="type">text/javascript</xsl:attribute>
					<xsl:attribute name="src">/dom/scripts/jquery.tablesorter.js</xsl:attribute>
				</xsl:element>
				<xsl:element name="script">
					<xsl:attribute name="type">text/javascript</xsl:attribute>
					<xsl:attribute name="src">/dom/general.js</xsl:attribute>
				</xsl:element>
				
				<xsl:comment><![CDATA[[if IE 6]><link rel="stylesheet" href="/css/ie.css" type="text/css" /><![endif]]]></xsl:comment>
			</head>

			<body id="www-creation-uk-com" class="sitemap xml">
				<div id="container">
				
					<div id="branding" class="author vcard">
						<h1><a href="/" title="Back to the homepage of Creation" rel="home me" class="url" accesskey="1"><img src="/css/images/general/creation-logo.gif" alt="Creation design &#38; marketing" width="135" height="32" class="fn org logo" /></a></h1>
						<h2 id="branding-strapline"><em class="strapline note">Specialist graphic designers and web developers based in Staffordshire, UK</em></h2>
					<!-- end of div id #brandng -->
					</div>
					
					<div id="content">
						
						<p>This is a generated sitemap.</p>
						<!-- You can reorder the information by clicking on the column headers. -->
					
						<table id="sitemap-table" summary="Sitemap for Creation" class="rowstyle-alternative no-arrow sortable-onload-1">
						<caption>Sitemap for Creation</caption>
						<thead>
							<tr>
								<th scope="column" class="sortable" id="url" title="Full URL of the page, beginning with the protocol and ending with a trailing slash and less than 2048 characters.">Page</th>
								<th scope="column" class="sortable" id="lastmod" title="The date of last modification of the file.">Last Modified</th>
								<!--<th scope="column" class="sortable" id="changefreq" title="How frequently the page is likely to change; may not correlate exactly to how often search engines crawl the page.">Change Frequency</th>-->
								<!--<th scope="column" class="sortable" id="priority" title="Lets the search engines know which of your pages you deem most important so they can order the crawl of your pages in the way you would most like.">Priority</th>-->
							</tr>
						</thead>
						<tbody class="hfeed">
						<xsl:for-each select="//sitemap:url">
							<tr >
								<xsl:attribute name="class">hentry</xsl:attribute>
								<xsl:if test="position() mod 2 = 1">
									<xsl:attribute name="class">hentry even</xsl:attribute>
								</xsl:if>
								<td headers="url" class="entry-title">
									<a class="url" rel="bookmark">
										<xsl:attribute name="href">
											<xsl:value-of select="sitemap:loc" />
										</xsl:attribute>
										<xsl:value-of select="html:title" />
									</a>
								</td>
								<td headers="lastmod">
									<abbr class="updated">
										<xsl:attribute name="title">
											<xsl:value-of select="sitemap:lastmod" />
										</xsl:attribute>
										<xsl:value-of select="sitemap:lastmod" />
									</abbr>
								</td>
								<!--
								<td headers="changefreq">
									<xsl:value-of select="sitemap:changefreq" />
								</td>
								<td headers="priority">
									<xsl:value-of select="sitemap:priority" />
								</td>
								-->
							</tr>
						</xsl:for-each>
						</tbody>
						</table>
					</div>
					
					<div id="navigation">
						<div id="navigation-primary">
							<h2>Navigation</h2>
							<ul>
							  <li class="home welcome active fli"><a href="/" title="Home" rel="home" accesskey="1"><span class="gl-ir"></span>Home</a></li>
							  <li class="company even"><a href="/company/" title="Company" rel="section"><span class="gl-ir"></span>Company</a></li>
							  <li class="portfolio"><a href="/portfolio/" title="Portfolio" rel="section"><span class="gl-ir"></span>Portfolio</a></li>
							  <li class="services even"><a href="/services/" title="Services" rel="section"><span class="gl-ir"></span>Services</a></li>
							  <li class="contact"><a href="/contact/" title="Contact" rel="section author" accesskey="9"><span class="gl-ir"></span>Contact</a></li>
							  <li class="blog news archives lli even"><a href="/news/" title="News" rel="section"><span class="gl-ir"></span>News</a></li>
							</ul>
						<!-- end of div id #navigation-primary -->
						</div>
					<!-- end of div id #navigation -->
					</div>
					
					<div id="footer">
						<h2>Footer</h2>
						<div id="footer-copyright">
							<p class="vcard author"><span class="gl-ir"></span>© 1995-2007 <strong class="org fn">Creation</strong>
							 - <em class="strapline">Specialist graphic designers and web developers based in Staffordshire, UK</em></p>
							<p class="top"><a href="#www-creation-uk-com"><span class="gl-ir"></span>Go to top</a></p>
						<!-- end of div id #footer-copyright -->
						</div>
					</div>
				</div>
				
				<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
				<script type="text/javascript">
				_uacct = "UA-113889-5";
				urchinTracker();
				</script>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>