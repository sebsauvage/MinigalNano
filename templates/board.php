<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="<? echo $author ?>" />
		<meta name="generator" content="MinigalNano <? echo $version ?>" />
		<title><? echo title ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="alternate" type="application/rss+xml" title="<% title %>" href="rss.php" />
		<style type="text/css">
			html{
				font-size: 62.5%;
			}
			body {
				position: relative;
				margin: 0;
				padding: 0;
				width: 100%;
				font-size: 1.5rem;
				font-family:Helvetica, Arial, sans-serif;
				background: #E7E6E0;
				color: #333333;
			}
			a {
				color: #333333;
				font-weight: bold;
				text-decoration: none;
			}
			h1 {
				margin: 0;
				padding: 1.5rem 0;
				font-family: Georgia, Lucida, serif;
				font-style: italic;
			}
			h1 a{
				color: #CC2027;
			}
			body > header{
				background-color:#FFF;
				padding: 1px 0 0 0;
				margin-bottom: 1rem;
			}
			#container,
			#innerheader,
			.Message {
				max-width: <% gallery_width %>;
				margin: 0px auto;
			}

			.Message {
				background-color: #2ecc71;
				border-radius: 2px;
			}
			.Message > div {
				padding: 1rem;
			}
			.closeMessage {
				float: right;
			}

			.Comment {
				padding: 2rem 0;
			}
			img {
				border: none;
			}
			nav {
				font-size: 1.3em;
			}
			.NavWrapper {
				padding: 1rem 0;
			}
			body > nav {
				text-align: center;
			}

			.Empty,
			.EmptyAdvice {
				text-align: center;
				text-shadow:0 1px 0 #FFF
			}
			.Empty {
				font-size: 10rem;
			}
			.EmptyAdvice {
				font-size: 2rem;
				font-style: italic;
			}

			#folder_comment
			{
				margin-bottom:10px;
			}
			#folder_comment a {
				color: #FFCC11;
				text-decoration: none;
			}
			#backtop,
			#backtop:hover,
			#backtop:active,
			.b-lazy {
				-webkit-transition:all .2s ease-in;
				-o-transition:all .2s ease-in;
				transition:all .2s ease-in;
			}
			#gallery {
				list-style: none;
				margin: 0;
				padding: 0;
			}
			#gallery li, #gallery li img {
				border-radius: 4px;
			}
			#gallery li {
				float: left;
				position: relative;
				overflow:hidden;
				margin: .5%;
				-webkit-box-shadow:  0px 0px 2px -1px #000;
				box-shadow:  0px 0px 2px -1px #000;
			}
			#gallery li img{
				width: 100%;
			}
			#gallery em {
				background: #FFF;
				text-align: center;
				font-style: normal;
				padding: 8px 0px;
				display: block;
				position: absolute;
				bottom:0px;
				width: 100%;
			}
			.clear {
				clear:both;
			}
			footer {
				padding-top: 2rem;
				margin-bottom: 25px;
				text-align: center;
			}
			footer a {
				text-decoration: none;
				color: #666;
			}
			/*----Back to top button---*/
			#backtop {
				display: block;
				position: fixed;
				bottom: 0;
				right: 0;
				padding: 1rem 4rem;
				background-color: #CC2027;
				color: #FFF;
				margin: .5%;
				border-radius: 4px;
				-webkit-box-shadow:  0px 0px 2px -1px #000;
				box-shadow:  0px 0px 2px -1px #000;
			}
			/*----lazyloading---*/
			.loader{
				background: url('images/loader.gif') center center no-repeat;
			}
			.b-lazy {
				opacity: 0;
			}
			.b-lazy.b-loaded {
				opacity: 1;
			}
			/*----responsive----*/
			#gallery li {
				width: 24%;
				height: 0;
				padding-bottom: 24%;
			}
			@media (max-width: 1000px) {
				#gallery li {
					width: 32%;
					padding-bottom: 32%;
				}
			}
			@media (max-width: 800px) {
				#gallery li {
					width: 48%;
					padding-bottom: 48%;
				}
			}
			@media (max-width: 450px) {
				#gallery li {
					width: 100%;
					padding-bottom: 100%;
				}
			}
		</style>
	</head>
	<body>
		<header id="top" role="banner">
			<div id="innerheader">
				<h1><a href="<? echo GALLERY_ROOT ?>"><? echo $title ?></a></h1>
				<nav role="navigation"><? echo $breadcrumb_navigation?></nav>
				<? if(count($comment) > 0) :?>
				<aside><? echo $comment ?></aside>
				<? endif; ?>
			</div>
			<? if(count($messages) > 0) :?>
				<ul>
				<? foreach ($messages as $message) : ?>
					<li><? echo $messages; ?></li>
				<? endforeach; ?>
				</ul>
			<? endif; ?>
		</header>

		<main id="container">
			<ul id="gallery">
				<% thumbnails %>
			</ul>
		</main>
		<nav class="clear" role="navigation"><? echo $page_navigation ?></nav>
		<a href="#top" id="backtop">top</a>
		<footer role="contentinfo">
			Gallery by <? echo $author ?> /
			<a href="https://github.com/sebsauvage/MinigalNano" title="Powered by MiniGal Nano" target="_blank">
				Powered by MiniGal Nano <? $version ?>
			</a> /
			<a href="http://tomcanac.com/minigal/" title="Tom Canac" target="_blank">
				Board theme by Tom Canac
			</a> /
			<a  title="<? echo $title ?> RSS" href="rss.php">
				RSS
			</a>
		</footer>
		<script src="<? echo GALLERY_ROOT ?>js/lazy.js"></script>
		<script src="<? echo GALLERY_ROOT ?>js/script.js"></script>
		<script src="<? echo GALLERY_ROOT ?>js/mootools1.5.0.js"></script>
		<script src="<? echo GALLERY_ROOT ?>js/mediabox1.5.4.js"></script>
	</body>
</html>
