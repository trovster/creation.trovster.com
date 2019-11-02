<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:html="http://www.w3.org/TR/REC-html40" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
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
						
						<h2>Atom feed</h2>
						<div id="content-primary" class=" column triple">							
							<div id="comments-view" class="hfeed">
								<h3 class="section-title"><span>Reactions</span> <em><xsl:value-of select="//html:title"/></em></h3>
								<xsl:for-each select="//atom:entry">
									<div>
										<xsl:attribute name="class">hentry</xsl:attribute>
										<xsl:if test="position() mod 2 = 1">
											<xsl:attribute name="class">hentry odd</xsl:attribute>
										</xsl:if>
										
										<div class="vcard author column">
											<xsl:value-of select="atom:author/html:img"/>
											<p class="fn">
												<xsl:choose>
													<xsl:when test="atom:author/atom:uri">
														<a>
															<xsl:attribute name="rel">no-follow external</xsl:attribute>
															<xsl:attribute name="class">url</xsl:attribute>
															<xsl:attribute name="href">
																<xsl:value-of select="atom:author/atom:uri"/>
															</xsl:attribute>
															<xsl:value-of select="atom:author/atom:name"/>
														</a>
													</xsl:when>
													<xsl:otherwise>
														<xsl:value-of select="atom:author/atom:name"/>
													</xsl:otherwise>
												</xsl:choose>
											</p>
											<p class="timestamp">
												<a>
													<xsl:attribute name="rel">bookmark</xsl:attribute>
													<xsl:attribute name="title">Permalink to this comment</xsl:attribute>
													<xsl:attribute name="href">
														<xsl:value-of select="atom:link/@href"/>
													</xsl:attribute>
													<abbr>
														<xsl:attribute name="class">published updated</xsl:attribute>
														<xsl:attribute name="title"><xsl:value-of select="html:abbr/@title"/></xsl:attribute>
														<xsl:value-of select="html:abbr"/>
													</abbr>
												</a>
											</p>
										</div>
										
										<div class="entry-content column double last">
											<xsl:value-of select="atom:content/text()"/>
										</div>
									</div>
								</xsl:for-each>
							</div>
						</div>
						
						<div id="content-secondary" class="column last">
							<h3>Subscribe Now!</h3>
							<p>Add this feed to a web based news reader. Click on your choice below:</p>
							<ul id="subscribe-icons">
								<li id="yahoo"><a title="Subscribe to this feed on Yahoo!">
									<xsl:attribute name="href">http://add.my.yahoo.com/rss?url=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>My Yahoo
								</a></li>
								<li id="newsgator"><a title="Subscribe to this feed on Newsgator">
									<xsl:attribute name="href">http://www.newsgator.com/ngs/subscriber/subext.aspx?url=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Newsgator
								</a></li>
								<li id="pluck"><a title="Subscribe to this feed on Pluck">
									<xsl:attribute name="href">http://client.pluck.com/pluckit/prompt.aspx?GCID=C12286x053&amp;a=<xsl:value-of select="//atom:link[@rel='self']/@href"/>&amp;t=<xsl:value-of select="//atom:title"/></xsl:attribute>Pluck RSS
								</a></li>
								<li id="rojo"><a title="Subscribe to this feed on Rojo">
									<xsl:attribute name="href">http://www.rojo.com/add-subscription?resource=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Rojo
								</a></li>
								<li id="bloglines"><a title="Subscribe to this feed on Bloglines">
									<xsl:attribute name="href">http://www.bloglines.com/sub/<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Bloglines
								</a></li>
								<li id="google"><a title="Subscribe to this feed on Google">
									<xsl:attribute name="href">http://fusion.google.com/add?feedurl=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Google
								</a></li>
								<li id="msn"><a title="Subscribe to this feed on MSN">
								<xsl:attribute name="href">http://my.msn.com/addtomymsn.armx?id=rss&amp;ut=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>MSN
								</a></li>
								<li id="feedlounge"><a title="Subscribe to this feed on Feed Lounge">
								<xsl:attribute name="href">http://my.feedlounge.com/external/subscribe?url=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Feed Lounge
								</a></li>
								<li id="netvibes"><a title="Subscribe to this feed on Netvibes">
								<xsl:attribute name="href">http://www.netvibes.com/subscribe.php?url=<xsl:value-of select="//atom:link[@rel='self']/@href"/></xsl:attribute>Netvibes
								</a></li>
							</ul>
						</div>
					</div>
					
					<div id="footer">
						<h2>Footer</h2>
						<div id="footer-copyright">
							<p class="vcard author"> 1995-2006 <strong class="org fn">Creation</strong> - <em class="strapline">Design &#38; marketing specialists in print and web development from Staffordshire, UK</em></p>
							<p class="top"><a href="#www-creation-uk-com">Go to top</a></p>
						<!-- end of div id #footer-copyright -->
						</div>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>