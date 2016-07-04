<?php
/*
 * This file is part of MinigalNano: https://github.com/sebsauvage/MinigalNano
 *
 * MiniGal Nano is based on an original work by Thomas Rybak (© 2010)
 *
 * MinigalNano is licensed under the AGPL v3 (https://gnu.org/licenses/agpl-3.0.txt).
 */

/**
 *
 * Minigal Nano configuration
 *
 * You may want to duplicate and customise the lines you want
 * in config.php, to keep a backup of the default configuration here.
 *
 */

/** Basic config */
// Number of thumbnails on a single page
$thumbs_pr_page = "39";
// Template filename (must be placed in 'templates' folder)
$template_name = "board";
// Text to be displayed in browser titlebar
$title = "MiniGal Nano";
// Gallery description
$description = "MiniGal Nano";
// Author Name
$author = "John Doe";
// Name of folder icon
$folder_icon = "folder_black.png";
// Sort folders by: [name][date]
$sorting_folders = "name";
// Sort files by: [name][date][size]
$sorting_files = "name";
// Sort direction of folders: SORT_ASC / SORT_DESC
$sortdir_folders = SORT_ASC;
// Sort direction of files: SORT_ASC / SORT_DESC
$sortdir_files = SORT_ASC;
// 0 is pagination, 1 is display all pictures on one page
$lazyload = 1;
// Those files and folders will not be displayed (affects the page and the RSS feed)
// e.g. : $skip_objects = array('aFolder', 'aFile.ext');
$skip_objects = array();

/** Language strings */
// Name of home link in breadcrumb navigation
$label_home = "Home";
// Text to display for new images. Use with $display_new variable
$label_new = "New";
// Text used for page navigation
$label_page = "Page";
// Text used for link to display all images in one page
$label_all = "All";
// Empty folder text
$label_noimages = "No images... yet !";
// Thumbnail loading text
$label_loading = "Loading...";

/** RSS settings */
// Number of elements to display in the feed. If you add a lot of pictures at the time, consider increasing this number
$nb_items_rss = 25;
// Time, in seconds, between two RSS refresh. for example, 3600 = 1update max per hour, 86400 = 1/day.
$rss_refresh_interval = 60;
// Files with one of this extension will not be displayed on the RSS feed
$keep_extensions = array('jpg', 'jpeg', 'png', 'gif');

/** Advanced Settings */
// Thumbnail height/width (square thumbs). Changing this will most likely require manual altering of the template file to make it look properly!
$thumb_size = 320;
// Maximum chars of a folder name that will be displayed on the folder thumbnail
$label_max_length = 40;
// Take care, even if not diplayed EXIF are still readable for visitors. May be a good idea to erase EXIF data...
$display_exif = 0;
// Show file names below the pictures
$display_filename = 0;
