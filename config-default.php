<?php
/*
 * This file is part of MinigalNano: https://github.com/sebsauvage/MinigalNano
 *
 * MiniGal Nano is based on an original work by Thomas Rybak (© 2010)
 *
 * MinigalNano is licensed under the AGPL v3 (https://gnu.org/licenses/agpl-3.0.txt).
 */

// Gallery Settings
$thumbs_pr_page = "39"; // Number of thumbnails on a single page
$template_file = "board"; // Template filename (must be placed in 'templates' folder)
$title = "MiniGal Nano"; // Text to be displayed in browser titlebar
$author = "John Doe";
$folder_color = "black"; // Color of folder icons: blue / black / vista / purple / green / grey
$sorting_folders = "name"; // Sort folders by: [name][date]
$sorting_files = "name"; // Sort files by: [name][date][size]
$sortdir_folders = SORT_ASC; // Sort direction of folders: SORT_ASC / SORT_DESC
$sortdir_files = SORT_ASC; // Sort direction of files: SORT_ASC / SORT_DESC
$lazyload = 1; // 0 is pagination, 1 is display all pictures on one page
$skip_objects = array('aFolder', 'aFile.ext'); //Those files and folders will not be displayed (affects the page and the RSS feed)

// Language strings
$label_home = "Home"; // Name of home link in breadcrumb navigation
$label_new = "New"; // Text to display for new images. Use with $display_new variable
$label_page = "Page"; // Text used for page navigation
$label_all = "All"; // Text used for link to display all images in one page
$label_noimages = "No images... yet !"; // Empty folder text
$label_noimages_advice = "Use your FTP to upload some picture !";
$label_loading = "Loading..."; // Thumbnail loading text

// RSS settings
$description = "MiniGal Nano";
$nb_items_rss = 25; // Number of elements to display in the feed. If you add a lot of pictures at the time, consider increasing this number
$rss_refresh_interval = 60; // Time, in seconds, between two RSS refresh. for example, 3600 = 1update max per hour, 86400 = 1/day.
$keep_extensions = array('jpg', 'jpeg', 'png', 'gif'); //Files with one of this extension will not be displayed on the RSS feed

// Advanced Settings
$thumb_size = 320; // Thumbnail height/width (square thumbs). Changing this will most likely require manual altering of the template file to make it look properly!
$label_max_length = 40; // Maximum chars of a folder name that will be displayed on the folder thumbnail
$display_exif = 0; // Take care, even if not diplayed EXIF are still readable for visitors. May be a good idea to erase EXIF data...
$display_filename = 0; // Show file names below the pictures
?>
