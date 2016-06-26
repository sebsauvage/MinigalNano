<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="<? echo $author ?>" />
        <meta name="description" content="<? echo $description ?>" />
        <meta name="generator" content="MinigalNano <? echo $version ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name='robots' content='index,follow'>
        <title><? echo $title ?></title>
        <link rel="alternate" type="application/rss+xml" title="<% title %>" href="rss.php" />
        <link rel="stylesheet" href="<? echo THEME_ROOT ?>styles.css">
    </head>
    <body>
        <header role="banner">
            <h1><a href="<? echo GALLERY_ROOT ?>"><? echo $title ?></a></h1>

            <nav role="navigation"><? echo $breadcrumb_navigation?></nav>
        </header>

        <main>
            <? if(count($folder_comment) > 0) :?>
                <p><? echo $folder_comment ?></p>
            <? endif; ?>

            <? if(count($messages) > 0) :?>
                <ul class="messages">
                    <? foreach ($messages as $message) : ?>
                        <li class="messages__message"><? echo $messages; ?></li>
                    <? endforeach; ?>
                </ul>
            <? endif; ?>

            <?php if ( count($images) > 0) : ?>
                <ul>
                <?php foreach ($images as $image) : ?>
                    <li>
                        <a href="<?php echo $image["link"]; ?>" title="20131125-2143_elan.png">
                            <img class="b-lazy b-loaded" src="<?php echo $image["thumb_src"]; ?>" alt="<?php echo $image["alt"]; ?>">
                            <?php if($image["type"] == "dir") : ?>
                                <p><?php echo $image["label"] ?></p>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No pictures :(</p>
            <?php endif ?>
        </main>

        <nav role="navigation"><? echo $page_navigation ?></nav>

        <a href="#top" id="backtop">top</a>

        <footer role="contentinfo">
            Gallery by <? echo $author ?> /
            <a href="https://github.com/sebsauvage/MinigalNano" title="Powered by MiniGal Nano" target="_blank">
                Powered by MiniGal Nano <? echo $version ?>
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
