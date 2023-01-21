<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
	<xsl:output doctype-system="http://www.w3.org/TR/html4/strict.dtd" doctype-public="-//W3C//DTD HTML 4.1 Strict//EN" encoding="UTF-8" method="html" omit-xml-declaration="yes"/>
	<xsl:template match="/">
		<html>
			<head>
				<title><xsl:value-of select="//atom:title"/></title>

				<xsl:element name="link">
					<xsl:attribute name="type">text/css</xsl:attribute>
					<xsl:attribute name="href">/css/creation.css</xsl:attribute>
					<xsl:attribute name="title">screen</xsl:attribute>
					<xsl:attribute name="media">screen,projection</xsl:attribute>
					<xsl:attribute name="rel">stylesheet</xsl:attribute>
				</xsl:element>

				<xsl:element name="link">
					<xsl:attribute name="type">text/css</xsl:attribute>
					<xsl:attribute name="href">/css/specifics/news.css</xsl:attribute>
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

				<xsl:comment><![CDATA[[if IE 6]><link rel="stylesheet" href="/css/ie.css" type="text/css" /><![endif]]]></xsl:comment>
			</head>

			<body id="www-creation-uk-com" class="feeds atom">
				<div id="container">

					<div id="branding" class="author vcard">
						<h1><a href="/" title="Back to the homepage of Creation" rel="home me" class="org fn url" accesskey="1">Creation</a></h1>
						<p id="branding-strapline"><em class="strapline">Design &#38; marketing specialists in print and web development from Staffordshire, UK</em></p>
					<!-- end of div id #brandng -->
					</div>

					<div id="content">
						<h2 id="page-title"><abbr title="Resource Description Framework Site Summary">RSS</abbr> feed</h2>
						<div id="content-primary">
							<h3><xsl:value-of select="//description"/></h3>
							<div class="information">
								<p>This is an <abbr title="Resource Description Framework Site Summary">RSS</abbr> feed designed to be read by a computer. Which you aren't.</p>
								<p><abbr title="Resource Description Framework Site Summary">RSS</abbr> feeds allow you to soar above fields of data, much in the
								way a great eagle soars above mountain ranges, except without the endangered species designation...</p>
								<p>See <a href="http://news.bbc.co.uk/1/hi/help/3223484.stm?rss=/rss/newsonline_uk_edition/world/americas/rss.xml">What is RSS?</a> at the
								<abbr title="British Broadcasting Corporation">BBC</abbr> website.</p>
							</div>

							<div class="text">
								<h3 id="page-heading">Current Feed Content</h3>
								<xsl:for-each select="//item">
									<div>
										<xsl:attribute name="class">fitt_c</xsl:attribute>
										<xsl:if test="position() mod 2 = 1">
											<xsl:attribute name="class">odd fitt_c</xsl:attribute>
										</xsl:if>
										<h4>
											<a>
												<xsl:attribute name="href">
													<xsl:value-of select="link"/>
												</xsl:attribute>
												<xsl:attribute name="class">fitt_l</xsl:attribute>
												<xsl:value-of select="title"/>
											</a>
										</h4>
										<p><xsl:value-of select="description"/></p>
									</div>
								</xsl:for-each>
							</div>
						</div>
					</div>

					<div id="content-secondary">
						<h3>Subscribe Now!</h3>
						<p>Add this feed to a web based news reader. Click on your choice below:</p>
						<ul id="subscribe-icons">
							<li id="yahoo"><a title="Subscribe to this feed on Yahoo!">
								<xsl:attribute name="href">http://add.my.yahoo.com/rss?url=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>My Yahoo
							</a></li>
							<li id="newsgator"><a title="Subscribe to this feed on Newsgator">
								<xsl:attribute name="href">http://www.newsgator.com/ngs/subscriber/subext.aspx?url=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Newsgator
							</a></li>
							<li id="pluck"><a title="Subscribe to this feed on Pluck">
								<xsl:attribute name="href">http://client.pluck.com/pluckit/prompt.aspx?GCID=C12286x053&amp;a=<xsl:value-of select="//link[@rel='self']/@href"/>&amp;t=<xsl:value-of select="//title"/></xsl:attribute>Pluck RSS
							</a></li>
							<li id="rojo"><a title="Subscribe to this feed on Rojo">
								<xsl:attribute name="href">http://www.rojo.com/add-subscription?resource=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Rojo
							</a></li>
							<li id="bloglines"><a title="Subscribe to this feed on Bloglines">
								<xsl:attribute name="href">http://www.bloglines.com/sub/<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Bloglines
							</a></li>
							<li id="google"><a title="Subscribe to this feed on Google">
							<xsl:attribute name="href">http://fusion.google.com/add?feedurl=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Google
							</a></li>
							<li id="msn"><a title="Subscribe to this feed on MSN">
							<xsl:attribute name="href">http://my.msn.com/addtomymsn.armx?id=rss&amp;ut=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>MSN
							</a></li>
							<li id="feedlounge"><a title="Subscribe to this feed on Feed Lounge">
							<xsl:attribute name="href">http://my.feedlounge.com/external/subscribe?url=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Feed Lounge
							</a></li>
							<li id="netvibes"><a title="Subscribe to this feed on Netviw00tbes">
							<xsl:attribute name="href">http://www.netvibes.com/subscribe.php?url=<xsl:value-of select="//link[@rel='self']/@href"/></xsl:attribute>Netvibes
							</a></li>
						</ul>
					</div>

					<div id="footer">
						<p id="footer-copyright">© 2006 <strong class="org fn"><xsl:value-of select="//title"/></strong></p>
						<p class="vcard">Website by <a href="https://creation.trovster.com" class="url fn org">Creation Design &#38; Marketing Limited</a></p>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>
