<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/_includes/scripts/markdown.php');
require_once('typogrify.php');

$heading = 'About Creation design & marketing LTD';

$text = "Creation are a team of specialist graphic designers and web developers offering both design for print
and web design consultancy services, based in Staffordshire, UK. In addition you will find on offer a
growing number of customer focused fixed price packages and solutions.

Our work is both innovative and creative, delivering quality solutions for all marketing communications
both in print and online. Take advantage of our years of expertise in a number of market sectors and
allow us to demonstrate the range of skills and techniques available to you. You can see in the
portfolio the broad range of projects we undertake including corporate styling, business literature,
advertising, direct mail and websites.";

$text = '##'.$heading.'##'."\r\n\r\n".$text;


$about 	= 'The only difference between the two boxes above is one [Typogrify](http://code.google.com/p/typogrify/) applied.
The second box is using the [PHP version](http://blog.hamstu.com/2007/05/31/web-typography-just-got-better/) of the original
Python script. This script, along with [PHP port](http://michelf.com/projects/php-smartypants/) of [SmartyPants](http://daringfireball.net)
creates better typographical hooks such as for ampersands, quotes & multiple capitals. The script also helps remove
[widows](http://en.wikipedia.org/wiki/Widows_and_orphans).

The page also makes use of the [EM Calculator](http://riddle.pl/emcalc/) for font sizing.';

$what	=
'*  Widon\'t — as coined by [Shaun Inman](http://shauninman.com)
* SmartyPants – originally created by [John Gruber](http://daringfireball.net)
* Initial quotes wrapped in class="dquo" or class="quo" depending on if they are single or double
* Ampersands wrapped in class="amp". Requires SmartyPants.
* Multiple adjacent capital letters wrapped in class="caps" (also caps separated by .s, and caps mixed with digits)';

// send headings
header('Content-Type: text/html; charset=utf-8');
header('Vary: Accept');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en" dir="ltr">
<head>
	<meta name="author" lang="en" content="Creation Design and Marketing Limited, Penkridge, Staffordshire, UK.">
	<meta name="copyright" content="Creation">
	<meta name="robots" content="index,follow">

	<meta name="language" content="en">	
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<meta http-equiv="imagetoolbar" content="no">
	
	<!-- title... -->
	<title>Typography experiment</title>
	
	<link rel="stylesheet" href="reset.css" type="text/css" media="screen,projection">
	<style type="text/css">
	body {
		padding: 30px 40px;
		font: normal normal 62.5%/1.5 Helvetica, Arial, Verdana, sans-serif;
		line-height: 1.5; /* This is for Safari - http://www.quirksmode.org/bugreports/archives/2006/09/Line_height_declaration_in_short_hand_form.html */
		background-color: #fff;
	}
	#container {
		font-size: 1.2em; /* 12px default now - http://riddle.pl/emcalc/ */
	}
	p, ul, ol, blockquote {
		margin-bottom: 1em;
	}
	ul {
		list-style-type: square;
	}
	a {
		color: #000;
	}
	a:focus, a:hover, a:active {
		text-decoration: none;
	}
	h1, h2, h3 {
		font-family: Georgia, "Times New Roman", Times, serif;
		font-weight: normal;
	}
	h1 {
		margin: 0 0 0.6666em; /* 20px */
		font-size: 2.5em; /* 30px */
	}
	h2 {
		margin-bottom: 0.416667em; /* 10px */
		font-size: 2em; /* 24px */ line-height: 1.166667 /* 28px */;
	}
	h3 {
		margin-bottom: 0.2778em; /* 5px */
		font-size: 1.5em; /* 18px */
	}
		
		
	/* basic column */
	.c {
		float: left;
		width: 190px;
		margin-right: 20px;
	}
	.two {width: 400px;}
	.three {width: 610px;}
	.l {margin-right: 0;}
	.n {clear: left;}
	.t {padding-top: 50px;}
	.b {padding-bottom: 20px;}
	
	
	.before,
	.after {
		padding: 10px 20px;
		background-color: #efefef;
		border: 1px solid #ccc;
	}
	.before {
		 margin-left: -20px;
	}
	
	
	/* typogrify */ 
	span.caps {
		font-size: 0.83333em;
	}
	h2 span.amp,
	h3 span.amp {
		font-family: "Warnock Pro", "Goudy Old Style", "Palatino", "Book Antiqua", serif;
		font-size: 1.166667em; /* 28px */ font-style: italic;
	}
	h2 span.caps,
	h3 span.caps {
		font-size: 0.833333em; /* 20px */
	}
	</style>
</head>
<body>
<div id="container">
<?php
echo '<h1>'.typogrify('Typography experiment').'</h1>'."\n\n";

echo '<div class="c two before">'."\n";
	echo markdown($text)."\n\n";
echo '</div>'."\n";

echo '<div id="typogrify" class="c two l after">'."\n";
	echo typogrify(markdown($text))."\n\n";
echo '</div>'."\n";

echo '<div id="about" class="c three n t b">'."\n";
	echo '<h3>'.typogrify('About & Credits').'</h3>'."\n\n";
	echo typogrify(markdown($about))."\n\n";
echo '</div>'."\n";

echo '<div id="done" class="c three n">'."\n";
	echo '<h3>'.typogrify('What this does…').'</h3>'."\n\n";
	echo typogrify(markdown($what))."\n\n";
echo '</div>'."\n";

?>
</div>
</body>
</html>