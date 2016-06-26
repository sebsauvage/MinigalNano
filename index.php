<?php

/*
 * This file is part of MinigalNano: https://github.com/sebsauvage/MinigalNano
 *
 * MiniGal Nano is based on an original work by Thomas Rybak (© 2010)
 *
 * MinigalNano is licensed under the AGPL v3 (https://gnu.org/licenses/agpl-3.0.txt).
 */

error_reporting(-1);

// Do not edit below this section unless you know what you are doing!

header('Content-Type: text/html; charset=UTF-8'); // We use UTF-8 for proper international characters handling.
$version = "0.5.0";
ini_set("memory_limit", "256M");

require_once "config-default.php";
require_once "config.php";
require_once "functions.global.php";
require_once "functions.index.php";
//-----------------------
// DEFINE VARIABLES
//-----------------------
$page_navigation = "";
$breadcrumb_navigation = "";
$images = [];
$exif_data = "";
$messages = [];
$folder_comment = "";

define("GALLERY_ROOT", "");
define("THEME_ROOT", GALLERY_ROOT . "templates/" . $template_name . '/');


//-----------------------
// PHP ENVIRONMENT CHECK
//-----------------------
if (!function_exists('exif_read_data') && $display_exif == 1) {
    $display_exif = 0;
    $messages[] = "Error: PHP EXIF is not available on your server. Set &#36;display_exif = 0; in config.php to remove this message";
}

$requested_dir = '';
if (!empty($_GET['dir'])) {
    $requested_dir = $_GET['dir'];
}

$photo_root = GALLERY_ROOT . 'photos/';
$thumb_dir = rtrim('photos/' . $requested_dir, '/');
$current_dir = GALLERY_ROOT . $thumb_dir;

guardAgainstDirectoryTraversal($current_dir);

//-----------------------
// READ FILES AND FOLDERS
//-----------------------
$files = array();
$dirs = array();
if (is_dir($current_dir) && $handle = opendir($current_dir)) {
    while (false !== ($file = readdir($handle)) && !in_array($file, $skip_objects)) {
        /**
         * 1. LOAD FOLDERS
         * TODO : refactor this part to have no code replication
         */
        if (is_dir($current_dir . "/" . $file)) {
            if ($file != "." && $file != "..") {
                checkpermissions($current_dir . "/" . $file); // Check for correct file permission
                // Set thumbnail to folder.jpg if found:
                if (file_exists($current_dir . '/' . $file . '/folder.jpg')) {
                    $link_params = http_build_query(
                        array('dir' => ltrim("$requested_dir/$file", '/')),
                        '',
                        '&amp;'
                    );
                    $link_url = "?$link_params";

                    $img_params = http_build_query(
                        array(
                            'filename' => "$current_dir/$file/folder.jpg",
                            'size' => $thumb_size,
                        ),
                        '',
                        '&amp;'
                    );
                    $img_url = GALLERY_ROOT . "createthumb.php?$img_params";
                    // TODO : do not return html, and setup proper php templating on the themes
                    $dirs[] = array(
                        "type" => "dir",
                        "name" => $file,
                        "date" => filemtime($current_dir . "/" . $file . "/folder.jpg"),
                        //"html" => "<li><a href=\"{$link_url}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$img_url}\"  alt=\"$label_loading\" /></a></li>",
                        "thumb_src" => $img_url,
                        "link" => $link_url,
                        "label" => padstring($file, $label_max_length),
                    );
                } else {
                    // Set thumbnail to first image found (if any):
                    unset($first_image);
                    $first_image = getfirstImage("$current_dir/" . $file);

                    if ($first_image != "") {
                        $link_params = http_build_query(
                            array('dir' => ltrim("$requested_dir/$file", '/')),
                            '',
                            '&amp;'
                        );
                        $link_url = "?$link_params";

                        $img_params = http_build_query(
                            array(
                                'filename' => "$thumb_dir/$file/$first_image",
                                'size' => $thumb_size,
                            ),
                            '',
                            '&amp;'
                        );
                        $img_url = GALLERY_ROOT . "createthumb.php?$img_params";

                        $dirs[] = array(
                            "type" => "dir",
                            "name" => $file,
                            "date" => filemtime($current_dir . "/" . $file),
                            //"html" => "<li><a href=\"{$link_url}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$img_url}\"  alt='$label_loading' /></a></li>",
                            "thumb_src" => $img_url,
                            "link" => $link_url,
                            "label" => padstring($file, $label_max_length),
                        );
                    } else {
                        // If no folder.jpg or image is found, then display default icon:
                        $link_params = http_build_query(
                            array('dir' => ltrim("$requested_dir/$file", '/')),
                            '',
                            '&amp;'
                        );
                        $link_url = "?$link_params";
                        $img_url = GALLERY_ROOT . 'images/' . strtolower($folder_icon);

                        $dirs[] = array(
                            "type" => "dir",
                            "name" => $file,
                            "date" => filemtime($current_dir . "/" . $file),
                            //"html" => "<li><a href=\"{$link_url}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$img_url}\" width='$thumb_size' height='$thumb_size' alt='$label_loading' /></a></li>",
                            "thumb_src" => $img_url,
                            "link" => $link_url,
                            "label" => padstring($file, $label_max_length),
                        );
                    }
                }
            }
        }

        /**
         * 2. LOAD FILES
         */
        if ($file != "." && $file != ".." && $file != "folder.jpg") {
            if ($display_filename) {
                $filename_caption = "<em>" . padstring($file, $label_max_length) . "</em>";
            } else {
                $filename_caption = "";
            }

            // JPG, GIF and PNG
            if (preg_match("/.jpg$|.gif$|.png$/i", $file)) {
                //Read EXIF
                if (!array_key_exists($file, $img_captions)) {
                    if ($display_exif == 1) {
                        $exif_readen = readEXIF($current_dir . "/" . $file);
                        //Add to the caption all the EXIF information
                        $img_captions[$file] = $file . $exif_readen;
                    } else {
                        //If no EXIF, just use the filename as caption
                        $img_captions[$file] = $file;
                    }
                }
                // Read the optionnal image title and caption in html file (image.jpg --> image.jpg.html)
                // Format: title::caption
                // Example: My cat::My cat like to <i>roll</i> on the floor.
                // If file is not provided, image filename will be used instead.
                checkpermissions($current_dir . "/" . $file);

                if (is_file($current_dir . '/' . $file . '.html')) {
                    $img_captions[$file] = $file . '::' . htmlspecialchars(file_get_contents($current_dir . '/' . $file . '.html'), ENT_QUOTES);
                }

                $link_url = str_replace('%2F', '/', rawurlencode("$current_dir/$file"));
                $img_params = http_build_query(
                    array('filename' => "$thumb_dir/$file", 'size' => $thumb_size),
                    '',
                    '&amp;');
                $img_url = GALLERY_ROOT . "createthumb.php?$img_params";
                $imgopts = "src=\"$img_url\"";

                $files[] = array(
                    "name" => $file,
                    "date" => filemtime($current_dir . "/" . $file),
                    "size" => filesize($current_dir . "/" . $file),
                    //"html" => "<li><a href=\"{$link_url}\" title=\"" . htmlentities($img_captions[$file]) . "\"><img $imgopts alt='$label_loading' /></a>" . $filename_caption . "</li>",
                    "link" => $link_url,
                    "thumb_src" => $img_url,
                    "alt" => $label_loading,
                    "label" => htmlentities($img_captions[$file]),
                    "filename_caption" => $filename_caption

                );
            }
            // Other filetypes
            $extension = "";
            if (preg_match("/\.pdf$/i", $file)) {
                $extension = "PDF";
            }
            // PDF
            if (preg_match("/\.zip$/i", $file)) {
                $extension = "ZIP";
            }
            // ZIP archive
            if (preg_match("/\.rar$|\.r[0-9]{2,}/i", $file)) {
                $extension = "RAR";
            }
            // RAR Archive
            if (preg_match("/\.tar$/i", $file)) {
                $extension = "TAR";
            }
            // TARball archive
            if (preg_match("/\.gz$/i", $file)) {
                $extension = "GZ";
            }
            // GZip archive
            if (preg_match("/\.doc$|\.docx$/i", $file)) {
                $extension = "DOCX";
            }
            // Word
            if (preg_match("/\.ppt$|\.pptx$/i", $file)) {
                $extension = "PPTX";
            }
            //Powerpoint
            if (preg_match("/\.xls$|\.xlsx$/i", $file)) {
                $extension = "XLXS";
            }
            // Excel
            if (preg_match("/\.ogv$|\.mp4$|\.mpg$|\.mpeg$|\.mov$|\.avi$|\.wmv$|\.flv$|\.webm$/i", $file)) {
                $extension = "VIDEO";
            }
            // video files
            if (preg_match("/\.aiff$|\.aif$|\.wma$|\.aac$|\.flac$|\.mp3$|\.ogg$|\.m4a$/i", $file)) {
                $extension = "AUDIO";
            }
            // audio files
            if ($extension != "") {
                $files[] = array(
                    "name" => $file,
                    "date" => filemtime($current_dir . "/" . $file),
                    "size" => filesize($current_dir . "/" . $file),
                    "html" => "<li><a href='$current_dir/$file' title='$file'><em-pdf>" . padstring($file, 20) . "</em-pdf><span></span><img src='" . GALLERY_ROOT . "images/filetype_" . $extension . ".png' width='$thumb_size' height='$thumb_size' alt='$file' /></a>$filename_caption</li>");
            }
        }
    }
    closedir($handle);
} else {
    die("ERROR: Could not open " . htmlspecialchars(stripslashes($current_dir)) . " for reading!");
}

//-----------------------
// SORT FILES AND FOLDERS
//-----------------------
if (sizeof($dirs) > 0) {
    foreach ($dirs as $key => $row) {
        if ($row["name"] == "") {
            unset($dirs[$key]); //Delete empty array entries
            continue;
        }
        $name[$key] = strtolower($row['name']);
        $date[$key] = strtolower($row['date']);
    }
    @array_multisort($$sorting_folders, $sortdir_folders, $name, $sortdir_folders, $dirs);
}

if (sizeof($files) > 0) {
    foreach ($files as $key => $row) {
        if ($row["name"] == "") {
            unset($files[$key]); //Delete empty array entries
            continue;
        }
        $name[$key] = strtolower($row['name']);
        $date[$key] = strtolower($row['date']);
        $size[$key] = strtolower($row['size']);
    }
    @array_multisort($$sorting_files, $sortdir_files, $name, SORT_ASC, $files);
}

//-----------------------
// OFFSET DETERMINATION
//-----------------------
if (!isset($_GET["page"])) {
    $_GET["page"] = 1;
}

$offset_start = ($_GET["page"] * $thumbs_pr_page) - $thumbs_pr_page;
$offset_end = $offset_start + $thumbs_pr_page;
if ($offset_end > sizeof($dirs) + sizeof($files)) {
    $offset_end = sizeof($dirs) + sizeof($files);
}

if ($_GET["page"] == "all" || $lazyload) {
    $offset_start = 0;
    $offset_end = sizeof($dirs) + sizeof($files);
}

//-----------------------
// PAGE NAVIGATION
//-----------------------
if (!$lazyload && sizeof($dirs) + sizeof($files) > $thumbs_pr_page) {
    $page_navigation .= "$label_page ";
    for ($i = 1; $i <= ceil((sizeof($files) + sizeof($dirs)) / $thumbs_pr_page); $i++) {
        if ($_GET["page"] == $i) {
            $page_navigation .= "$i";
        } else {
            $page_navigation .= "<a href='?dir=" . $requested_dir . "&amp;page=" . ($i) . "'>" . $i . "</a>";
        }

        if ($i != ceil((sizeof($files) + sizeof($dirs)) / $thumbs_pr_page)) {
            $page_navigation .= " | ";
        }

    }
    //Insert link to view all images
    if ($_GET["page"] == "all") {
        $page_navigation .= " | $label_all";
    } else {
        $page_navigation .= " | <a href='?dir=" . $requested_dir . "&amp;page=all'>$label_all</a>";
    }

}

//-----------------------
// BREADCRUMB NAVIGATION
//-----------------------
if ($requested_dir != "" && $requested_dir != "photos") {
    $breadcrumb_navigation = "<div class=\"NavWrapper\">";
    $breadcrumb_navigation .= "<a href='?dir='>" . $label_home . "</a> ";
    $navitems = explode("/", htmlspecialchars($_REQUEST['dir']));
    for ($i = 0; $i < sizeof($navitems); $i++) {
        if ($i == sizeof($navitems) - 1) {
            $breadcrumb_navigation .= $navitems[$i];
        } else {
            $breadcrumb_navigation .= "<a href='?dir=";
            for ($x = 0; $x <= $i; $x++) {
                $breadcrumb_navigation .= $navitems[$x];
                if ($x < $i) {
                    $breadcrumb_navigation .= "/";
                }

            }
            $breadcrumb_navigation .= "'>" . $navitems[$i] . "</a> $breadcrumb_separator ";
        }
    }
    $breadcrumb_navigation .= "</div>";
}

//Include hidden links for all images BEFORE current page so lightbox is able to browse images on different pages
for ($y = 0; $y < $offset_start - sizeof($dirs); $y++) {
    $breadcrumb_navigation .= "<a href='" . $current_dir . "/" . $files[$y]["name"] . "' class='hidden' title='" . $img_captions[$files[$y]["name"]] . "'></a>";
}

//-----------------------
// DISPLAY FOLDERS
//-----------------------
if (count($dirs) + count($files) == 0) {
    // empty folder
    if ($current_dir == "photos") {
        // empty root folder
        $messages[] =
        "It looks like you have just installed MiniGal Nano.
            Please run the <a href='system_check.php'>system check tool</a>. <br>
            And why not have a look to config.php and customize some values ?";
    }
}
$offset_current = $offset_start;
for ($x = $offset_start; $x < sizeof($dirs) && $x < $offset_end; $x++) {
    $offset_current++;
    $images[] = $dirs[$x];
}

//-----------------------
// DISPLAY FILES
//-----------------------
for ($i = $offset_start - sizeof($dirs); $i < $offset_end && $offset_current < $offset_end; $i++) {
    if ($i >= 0) {
        $offset_current++;
        $images[] = $files[$i];
    }
}

//Include hidden links for all images AFTER current page so lightbox is able to browse images on different pages
if ($i < 0) {
    $i = 1;
}

for ($y = $i; $y < sizeof($files); $y++) {
    $page_navigation .= "<a href='" . $current_dir . "/" . $files[$y]["name"] . "'  class='hidden' title='" . $img_captions[$files[$y]["name"]] . "'></a>";
}

//-----------------------
// OUTPUT MESSAGES
//-----------------------

// Read folder comment.
$comment_filepath = $current_dir . $file . "/comment.html";
if (file_exists($comment_filepath)) {
    $fd = fopen($comment_filepath, "r");
    $folder_comment = htmlspecialchars(fread($fd, filesize($comment_filepath)));
    fclose($fd);
}

// Process template file
if (!$template_name) {
    $template_name = "board";
}

$template_name = "templates/" . $template_name . "/index.php";

if (!$fd = fopen($template_name, "r")) {
    echo "Template " . htmlspecialchars(stripslashes($template_name)) . " not found!";
    exit();
}

require_once $template_name;
