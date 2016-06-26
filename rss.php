<?php
/*
 * This file is part of MinigalNano: https://github.com/sebsauvage/MinigalNano
 *
 * MiniGal Nano is based on an original work by Thomas Rybak (© 2010)
 *
 * MinigalNano is licensed under the AGPL v3 (https://gnu.org/licenses/agpl-3.0.txt).
 */

require_once "config-default.php";
require_once "config.php";
require_once "functions.global.php";
require_once "functions.rss.php";

/*============================*/
/* Gallery address definition */
/*============================*/

$gallery_link = get_gallery_url();

/*===================*/
/* Variables         */
/*===================*/
$folder = "photos";

$content = array();
$content = listFiles($content, $folder, $keep_extensions, $skip_objects);
usort($content, function ($a, $b) {return filemtime($a) < filemtime($b);});

if (is_writeable(".")) {
    $to_store = "";
    $old_files_list = "db_old_files"; //list of files in ./photos
    $db_feed_source = "db_feed_source";
    $db_rss_timestamp = "db_rss_timestamp";

    // Init files
    if (!file_exists($old_files_list)) {
        file_put_contents($old_files_list, "");
    }
    if (!file_exists($db_feed_source)) {
        file_put_contents($db_feed_source, "");
    }
    if (!file_exists($db_rss_timestamp)) {
        file_put_contents($db_rss_timestamp, "");
    }

    /*===================*/
    /* Computing         */
    /*===================*/
    // Todo : ajouter une condition : dois-je regénérer le flux ou utiliser les anciens fichiers ?
    $temp = file_get_contents($db_feed_source);
    $last_rss_gen = file_get_contents($db_rss_timestamp);
    $current_time = time();
    // If the RSS generation is already launched, don't do a second generation at the same time
    if (($current_time - $last_rss_gen) > $rss_refresh_interval && file_exists("rss.locker") == false) {
        file_put_contents("rss.locker", "");
        file_put_contents($db_rss_timestamp, time());
        // Load the list from files.
        $old_files_list_content = explode("\n", file_get_contents($old_files_list));
        $new_files_list_content = $content; #debug
        // Generate and stock new elements
        $differences = diff($old_files_list_content, $new_files_list_content);
        for ($i = 0; $i < count($differences); $i++) {
            if (is_array($differences[$i])) {
                for ($j = 0; $j < count($differences[$i]["i"]); $j++) {
                    if (strlen($differences[$i]["i"][$j]) > 2) {
                        $to_store .= $differences[$i]["i"][$j] . "\n";
                    }
                }
            }
        }
        // Add new elements at the top of the feed's source
        $temp = $to_store . $temp;
        file_put_contents($db_feed_source, $temp);
        // Store the current file list for the next generation
        file_put_contents($old_files_list, join("/n", $content));
        unlink("rss.locker");
    }
    $content = explode("\n", $temp);
}

/*===================*/
/* XML Gen           */
/*===================*/
header('Content-Type: text/xml');
echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo "<rss version='2.0'>\n<channel>";
echo "<title>$title</title>";
echo "<link>$gallery_link</link>";
echo "<description>$description</description>\n";
for ($i = 0; $i < $nb_items_rss && $i < count($content); $i++) {
    if (empty($content[$i])) {
        continue;
    }

    $link = $gallery_link . '/' . str_replace(' ', '%20', $content[$i]);
    echo "<item>\n";
    echo " <title>" . basename($link) . "</title>\n";
    echo " <link>" . $link . "</link>\n";
    echo " <guid>" . $link . "</guid>\n";
    echo " <description><![CDATA[ <img src='" . $link . "'> ]]></description>\n";
    echo " <pubDate>" . date("D, j M Y H:i:s O", filemtime($content[$i])) . "</pubDate>";
    echo "</item>\n";
}
echo "</channel></rss>\n";
