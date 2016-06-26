<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="<? echo $author ?>" />
		<meta name="generator" content="MinigalNano <? echo $version ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name='robots' content='index,follow'>
		<title><? echo $title ?></title>
		<link rel="alternate" type="application/rss+xml" title="<% title %>" href="rss.php" />
		<link rel="stylesheet" href="<? echo THEME_ROOT ?>styles.css">
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
			<?php echo $thumbnails ?>
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
		<script src="<? echo THEME_ROOT ?>script.js"></script>
	</body>
</html>
