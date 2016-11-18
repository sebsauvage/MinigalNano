<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="<?php echo $author ?>" />
        <meta name="description" content="<?php echo $description ?>" />
        <meta name="generator" content="MinigalNano <?php echo $version ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="index,follow">
        <title><?php echo $title ?></title>
        <link rel="alternate" type="application/rss+xml" title="<?php echo $title ?>" href="rss.php" />
        <link rel="stylesheet" href="<?php echo THEME_ROOT ?>css/styles.min.css">
        <meta property="og:title" content="<?php echo $title ?>">
        <meta property="og:description" content="<?php echo $description ?>">
        <meta property="og:url" content="<?php echo $homepage_url ?>">
    </head>
    <body>
        <div class="header">
            <div class="container">
                <header role="banner">
                    <h1 class="title">
                        <a class="title__a" href="<?php echo $homepage_url ?>"><?php echo $title ?></a>
                    </h1>

                    <nav role="navigation">
                        <?php if(count($breadcrumbs) > 0) :?>
                            <ol class="breadcrumbs" itemscope itemtype="http://schema.org/BreadcrumbList">
                                <?php foreach ($breadcrumbs as $i=>$crumb) : ?>
                                    <li class="breadcrumbs__crumb" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                                        <a class="breadcrumbs__crumb_a" href="<?php echo $crumb['url']; ?>" itemprop="item">
                                            <span itemprop="name"><?php echo $crumb['label']; ?></span>
                                        </a>
                                        <meta itemprop="position" content="<?php echo $i+1 ?>" />
                                    </li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>
                    </nav>
                </header>


                <?php if(count($messages) > 0) :?>
                    <aside>
                        <ul class="messages">
                            <?php foreach ($messages as $message) : ?>
                                <li class="messages__message"><?php echo $message; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                <?php endif; ?>


                <?php if(count($folder_comment) > 0) :?>
                    <p class="content"><?php echo $folder_comment ?></p>
                <?php endif; ?>
            </div>
        </div>

        <main class="container">
            <?php if ( count($images) > 0) : ?>
                <ul class="grid row">
                <?php foreach ($images as $image) : ?>
                    <li class="grid__element col-xs-12 col-sm-6 col-md-4 col-lg-3">
                        <a class="grid__element__a grid__element__a__<?php echo $image["type"]?>" href="<?php echo $image["link"]; ?>" title="20131125-2143_elan.png">
                            <img
                                class="grid__element__a__img"
                                src="<?php echo $image["thumb_src"]; ?>"
                                alt="<?php echo $image["alt"]; ?>"
                                data-original="<?php echo $image["link"]; ?>">
                            <?php if($image["type"] == "dir") : ?>
                            <p class="grid__element__a__label"><?php echo $image["label"] ?></p>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <div class="empty-folder">
                    <i class="empty-folder__icon-folder-open-empty icon-folder-open-empty"></i>
                    <span>Empty folder</span>
                </div>
            <?php endif ?>
        </main>

        <nav role="navigation"><?php echo $page_navigation ?></nav>

        <button  href="#top" class="back-to-top">top</button>

        <footer class="footer container" role="contentinfo">
            Gallery by <?php echo $author ?> /
            <a href="https://github.com/sebsauvage/MinigalNano" title="Powered by MiniGal Nano" target="_blank">
                Powered by MiniGal Nano <?php echo $version ?>
            </a> /
            <a href="http://tomcanac.com/minigal/" title="Tom Canac" target="_blank">
                Board theme by Tom Canac
            </a> /
            <a  title="<?php echo $title ?> RSS" href="rss.php">
                RSS
            </a>
        </footer>

        <aside class="lightbox">
            <header class="lightbox__header">

            </header>
            <div class="lightbox__body">
                <img src="" alt="" class="lightbox__body__img" id="js_lightbox__body__img">
            </div>
        </aside>

        <script src="<?php echo GALLERY_ROOT ?>js/lazy.js"></script>
        <script src="<?php echo THEME_ROOT ?>js/jquery-3.0.0.min.js"></script>
        <script src="<?php echo THEME_ROOT ?>js/script.js"></script>
    </body>
</html>
