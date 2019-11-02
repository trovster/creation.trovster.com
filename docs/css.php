<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/_initialise.php');

/* information setup
============================================================================================================= */

/* file setup
=============================================================================================================*/

/* setup the header information
============================================================================================================= */
$header = new Header();
$header->title = 'Styleguide for CSS | Documentation | '.$header->title;
$header->className = $currentSection;
$header->heading = 'Styleguide for CSS';
$header->stylesheet[] = array('file' => 'specifics/docs.css', 'media' => 'screen');
//$header->metaKeywords = '';
//$header->lastUpdated = $updated;
$header->Display();
?>

	<div id="content-primary">
		<div class="introduction column double">
			<h3>Styleguide for <abbr title="Cascading Style Sheets">CSS</abbr> <em>For Creation</em></h3>
			<p>Documentation how to order <abbr title="Cascading Style Sheets">CSS</abbr> declarations for consistency. Some declarations appear
			on one line, these are similar or work together. The order is based upon the complexity of the property.</p>
		</div>
		
		<div class="reset column double last">
			<h3>Resetting <abbr title="Cascading Style Sheets">CSS</abbr> <em>A Stylesheet</em></h3>
			<p>There are two main methods to resetting stylesheets. A common technique is to use the simple one liner: 
			<code class="css"><em>margin</em>: <span>0</span>; <em>padding</em>: <span>0</span>;</code>.</p>
			<p>Alternatively, people have developed complete resetting stylesheets,
			namely <a href="http://developer.yahoo.com/yui/reset/" title="Yahoo! UI Library: Reset CSS" rel="external">Yahoo!</a> and
			<a href="http://meyerweb.com/eric/thoughts/2007/05/01/reset-reloaded/" title="Eric's Archived Thoughts: Reset Reloaded" rel="external">Eric Meyer</a>,
			which tackle a lot more rules.</p>
			
			<p>The Creation website uses a combination of both of these stylesheets and our
			<a href="/css/reset.css" class="download css">reset stylesheet can be downloaded</a> for your use.</p>
			<p>You can also <a href="/files/css-setup.zip" class="download zip">download the CSS setup zip file</a>,
			which has all the basic CSS files we use on every project.</p>
		</div>
		
		<div class="basics second column full">
			<h3>Rule Logic <em>&#38; Rule Set Example</em></h3>
			<div class="column double">
				<h4>Basic Rule Logic</h4>
				<ol>
					<li>Layout, positioning &#38; movement</li>
					<li>Display &#38; dimensions</li>
					<li>Font &#38; text-based styling</li>
					<li>Layout styling</li>
					<li>Other styling</li>
				</ol>
				<p><a href="http://www.w3.org/TR/CSS21/propidx.html">View a comprehensive listing of <abbr title="Cascading Style Sheets">CSS</abbr> 2.1
				properties and values</a>.</p>
			</div>
			<div class="column double last">
				<h4>Rule Set</h4>
				<pre><code class="css"><strong>element</strong> {
  <em>position</em>: <span>relative</span>; <em>top</em>: <span>length</span>; <em>left</em>: <span>length</span>; <em>z-index</em>: <span>integer</span>;
  <em>clear</em>: <span>both</span>; <em>float</em>: <span>left</span>;
  <em>display</em>: <span>block</span>; <em>overflow</em>: <span>hidden</span>; <em>visibility</em>: <span>visible</span>;
  <em>height</em>: <span>length</span>; <em>width</em>: <span>length</span>;
  <em>margin</em>: <span>length</span>; <em>padding</em>: <span>length</span>; 
  <em>font</em>: <span>font-size/line-height font-family</span>; <em>color</em>: <span>color</span>;
  <em>background</em>: <span>color url(path/to/image.gif) repeat position</span>;
  <em>cursor</em>: <span>pointer</span>;
  <em>content</em>: <span>string</span>;
}
</code></pre>
			</div>
		</div>
		<div class="notes second column double">
			<h3>Notes</h3>
			<p>Any colour properties (<code class="css"><em>color</em>, <em>background-color</em>, <em>border-color</em></code>)
			or position properties (<code class="css"><em>background-position</em></code>) <strong>should not</strong> use keywords.</p>
			<p>Zero values are dimensionless. This means <code class="css"><span>0</span></code> on it's own is sufficient.</p>
		</div>
	<!-- end of div id #content-primary -->
	</div>

<?php require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/footer.php'); ?>