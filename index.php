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
$version = "0.4.0";
ini_set("memory_limit", "256M");

require "config-default.php";
include "config.php";

//-----------------------
// DEFINE VARIABLES
//-----------------------
$page_navigation = "";
$breadcrumb_navigation = "";
$thumbnails = "";
$new = "";
$images = "";
$exif_data = "";
$messages = "";
$comment = "";

//-----------------------
// PHP ENVIRONMENT CHECK
//-----------------------
if (!function_exists('exif_read_data') && $display_exif == 1) {
	$display_exif = 0;
	$messages = "Error: PHP EXIF is not available. Set &#36;display_exif = 0; in config.php to remove this message";
}

//-----------------------
// FUNCTIONS
//-----------------------
function padstring($name, $length) {
	global $label_max_length;
	if (!isset($length)) {
		$length = $label_max_length;
	}
	if (strlen($name) > $length) {
		return substr($name, 0, $length) . "...";
	}
	return $name;
}

function getfirstImage($dirname) {
	$imageName = false;
	$extensions = array("jpg", "png", "jpeg", "gif");
	if ($handle = opendir($dirname)) {
		while (false !== ($file = readdir($handle))) {
			if ($file[0] == '.') {
				continue;
			}
			$pathinfo = pathinfo($file);
			if (empty($pathinfo['extension'])) {
				continue;
			}
			$ext = strtolower($pathinfo['extension']);
			if (in_array($ext, $extensions)) {
				$imageName = $file;
				break;
			}
		}
		closedir($handle);
	}
	return $imageName;
}

function parse_fraction($v, $round = 0) {
	list($x, $y) = array_map('intval', explode('/', $v));
	if (empty($x) || empty($y)) {
		return $v;
	}
	if ($x % $y == 0) {
		return $x / $y;
	}
	if ($y % $x == 0) {
		return "1/" . $y / $x;
	}
	return round($x / $y, $round);
}

function readEXIF($file) {
	$exif_arr = array();
	$exif_data = exif_read_data($file);

	$exif_val = @$exif_data['Model'];
	if (!empty($exif_val)) {
		$exif_arr[] = $exif_val;
	}

	$exif_val = @$exif_data['FocalLength'];
	if (!empty($exif_val)) {
		$exif_arr[] = parse_fraction($exif_val) . "mm";
	}

	$exif_val = @$exif_data['ExposureTime'];
	if (!empty($exif_val)) {
		$exif_arr[] = parse_fraction($exif_val, 2) . "s";
	}

	$exif_val = @$exif_data['FNumber'];
	if (!empty($exif_val)) {
		$exif_arr[] = "f" . parse_fraction($exif_val);
	}

	$exif_val = @$exif_data['ISOSpeedRatings'];
	if (!empty($exif_val)) {
		$exif_arr[] = "ISO " . $exif_val;
	}

	if (count($exif_arr) > 0) {
		return "::" . implode(" | ", $exif_arr);
	}

	return $exif_arr;
}

function checkpermissions($file) {
	global $messages;

	if (!is_readable($file)) {
		$messages = "At least one file or folder has wrong permissions. "
		. "Learn how to <a href='http://minigal.dk/faq-reader/items/"
		. "how-do-i-change-file-permissions-chmod.html' target='_blank'>"
		. "set file permissions</a>";
	}
}

function guardAgainstDirectoryTraversal($path) {
    $pattern = "/^(.*\/)?(\.\.)(\/.*)?$/";
    $directory_traversal = preg_match($pattern, $path);

    if ($directory_traversal === 1) {
        die("ERROR: Could not open " . htmlspecialchars(stripslashes($current_dir)) . " for reading!");
    }
}

if (!defined("GALLERY_ROOT")) {
	define("GALLERY_ROOT", "");
}

$requestedDir = '';
if (!empty($_GET['dir'])) {
	$requestedDir = $_GET['dir'];
}

$photo_root = GALLERY_ROOT . 'photos/';
$thumbdir = rtrim('photos/' . $requestedDir, '/');
$current_dir = GALLERY_ROOT . $thumbdir;

guardAgainstDirectoryTraversal($current_dir);

//-----------------------
// READ FILES AND FOLDERS
//-----------------------
$files = array();
$dirs = array();
$img_captions = array();
if (is_dir($current_dir) && $handle = opendir($current_dir)) {
	// 1. LOAD CAPTIONS
	$caption_filename = "$current_dir/captions.txt";
	if (is_readable($caption_filename)) {
		$caption_handle = fopen($caption_filename, "rb");
		while (!feof($caption_handle)) {
			$caption_line = fgetss($caption_handle);
			if (empty($caption_line)) {
				continue;
			}
			list($img_file, $img_text) = explode('|', $caption_line);
			$img_captions[$img_file] = trim($img_text);
		}
		fclose($caption_handle);
	}

	while (false !== ($file = readdir($handle)) && !in_array($file, $skip_objects)) {
		// 2. LOAD FOLDERS
		if (is_dir($current_dir . "/" . $file)) {
			if ($file != "." && $file != "..") {
				checkpermissions($current_dir . "/" . $file); // Check for correct file permission
				// Set thumbnail to folder.jpg if found:
				if (file_exists($current_dir . '/' . $file . '/folder.jpg')) {
					$linkParams = http_build_query(
						array('dir' => ltrim("$requestedDir/$file", '/')),
						'',
						'&amp;'
					);
					$linkUrl = "?$linkParams";

					$imgParams = http_build_query(
						array(
							'filename' => "$current_dir/$file/folder.jpg",
							'size' => $thumb_size,
						),
						'',
						'&amp;'
					);
					$imgUrl = GALLERY_ROOT . "createthumb.php?$imgParams";

					$dirs[] = array(
						"name" => $file,
						"date" => filemtime($current_dir . "/" . $file . "/folder.jpg"),
						"html" => "<li><a href=\"{$linkUrl}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$imgUrl}\"  alt=\"$label_loading\" /></a></li>",
					);
				} else {
					// Set thumbnail to first image found (if any):
					unset($firstimage);
					$firstimage = getfirstImage("$current_dir/" . $file);

					if ($firstimage != "") {
						$linkParams = http_build_query(
							array('dir' => ltrim("$requestedDir/$file", '/')),
							'',
							'&amp;'
						);
						$linkUrl = "?$linkParams";

						$imgParams = http_build_query(
							array(
								'filename' => "$thumbdir/$file/$firstimage",
								'size' => $thumb_size,
							),
							'',
							'&amp;'
						);
						$imgUrl = GALLERY_ROOT . "createthumb.php?$imgParams";

						$dirs[] = array(
							"name" => $file,
							"date" => filemtime($current_dir . "/" . $file),
							"html" => "<li><a href=\"{$linkUrl}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$imgUrl}\"  alt='$label_loading' /></a></li>",
						);
					} else {
						// If no folder.jpg or image is found, then display default icon:
						$linkParams = http_build_query(
							array('dir' => ltrim("$requestedDir/$file", '/')),
							'',
							'&amp;'
						);
						$linkUrl = "?$linkParams";
						$imgUrl = GALLERY_ROOT . 'images/folder_' . strtolower($folder_color) . '.png';

						$dirs[] = array(
							"name" => $file,
							"date" => filemtime($current_dir . "/" . $file),
							"html" => "<li><a href=\"{$linkUrl}\"><em>" . padstring($file, $label_max_length) . "</em><span></span><img src=\"{$imgUrl}\" width='$thumb_size' height='$thumb_size' alt='$label_loading' /></a></li>",
						);
					}
				}
			}
		}

		// 3. LOAD FILES
		if ($file != "." && $file != ".." && $file != "folder.jpg") {
			if ($display_filename) {
				$filename_caption = "<em>" . padstring($file, $label_max_length) . "</em>";
			} else {
				$filename_caption = "";
			}

			// JPG, GIF and PNG
			if (preg_match("/.jpe?g$|.gif$|.png$/i", $file)) {
				//Read EXIF
				if (!array_key_exists($file, $img_captions)) {
					if ($display_exif == 1) {
						$exifReaden = readEXIF($current_dir . "/" . $file);
						//Add to the caption all the EXIF information
						$img_captions[$file] = $file . $exifReaden;
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

				$linkUrl = str_replace('%2F', '/', rawurlencode("$current_dir/$file"));
				$imgParams = http_build_query(
					array('filename' => "$thumbdir/$file", 'size' => $thumb_size),
					'',
					'&amp;');
				$imgUrl = GALLERY_ROOT . "createthumb.php?$imgParams";
				if ($lazyload) {
					$imgopts = "class=\"b-lazy\" src=data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw== data-src=\"$imgUrl\"";
				} else {
					$imgopts = "src=\"{$imgUrl}\"";
				}

				$files[] = array(
					"name" => $file,
					"date" => filemtime($current_dir . "/" . $file),
					"size" => filesize($current_dir . "/" . $file),
					"html" => "<li><a href=\"{$linkUrl}\" rel='lightbox[billeder]' title=\"" . htmlentities($img_captions[$file]) . "\"><img $imgopts alt='$label_loading' /></a>" . $filename_caption . "</li>");
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
			$page_navigation .= "<a href='?dir=" . $requestedDir . "&amp;page=" . ($i) . "'>" . $i . "</a>";
		}

		if ($i != ceil((sizeof($files) + sizeof($dirs)) / $thumbs_pr_page)) {
			$page_navigation .= " | ";
		}

	}
	//Insert link to view all images
	if ($_GET["page"] == "all") {
		$page_navigation .= " | $label_all";
	} else {
		$page_navigation .= " | <a href='?dir=" . $requestedDir . "&amp;page=all'>$label_all</a>";
	}

}

//-----------------------
// BREADCRUMB NAVIGATION
//-----------------------
if ($requestedDir != "" && $requestedDir != "photos") {
	$breadcrumb_navigation = "<div class=\"NavWrapper\">";
	$breadcrumb_navigation .= "<a href='?dir='>" . $label_home . "</a> $breadcrumb_separator ";
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
	$thumbnails .= "<div class=\"Empty\">$label_noimages</div> <div class=\"EmptyAdvice\">$label_noimages_advice</div>"; //Display 'no images' text
	if ($current_dir == "photos") {
		$messages =
		"It looks like you have just installed MiniGal Nano.
            Please run the <a href='system_check.php'>system check tool</a>. <br>
            And why not have a look to config.php and customize some values ?";
	}
}
$offset_current = $offset_start;
for ($x = $offset_start; $x < sizeof($dirs) && $x < $offset_end; $x++) {
	$offset_current++;
	$thumbnails .= $dirs[$x]["html"];
}

//-----------------------
// DISPLAY FILES
//-----------------------
for ($i = $offset_start - sizeof($dirs); $i < $offset_end && $offset_current < $offset_end; $i++) {
	if ($i >= 0) {
		$offset_current++;
		$thumbnails .= $files[$i]["html"];
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
if ($messages != "") {
	$messages = $messages . "<div><a id=\"closeMessage\" class=\"closeMessage\" href=\"#\"><img src=\"images/close.png\" /></a><div>";
}

// Read folder comment.
$comment_filepath = $current_dir . $file . "/comment.html";
if (file_exists($comment_filepath)) {
	$fd = fopen($comment_filepath, "r");
	$comment = "<div class=\"Comment\">" . fread($fd, filesize($comment_filepath)) . "</div>";
	fclose($fd);
}

//PROCESS TEMPLATE FILE
if (GALLERY_ROOT != "") {
	$templatefile = GALLERY_ROOT . "templates/integrate.html";
} else {
	$templatefile = "templates/" . $templatefile . ".html";
}

if (!$fd = fopen($templatefile, "r")) {
	echo "Template " . htmlspecialchars(stripslashes($templatefile)) . " not found!";
	exit();
} else {
	$template = fread($fd, filesize($templatefile));
	fclose($fd);
	$template = stripslashes($template);
	$template = preg_replace("/<% title %>/", $title, $template);
	$template = preg_replace("/<% messages %>/", $messages, $template);
	$template = preg_replace("/<% author %>/", $author, $template);
	$template = preg_replace("/<% gallery_root %>/", GALLERY_ROOT, $template);
	$template = preg_replace("/<% images %>/", "$images", $template);
	$template = preg_replace("/<% thumbnails %>/", "$thumbnails", $template);
	$template = preg_replace("/<% breadcrumb_navigation %>/", "$breadcrumb_navigation", $template);
	$template = preg_replace("/<% page_navigation %>/", "$page_navigation", $template);
	$template = preg_replace("/<% folder_comment %>/", "$comment", $template);
	$template = preg_replace("/<% bgcolor %>/", "$backgroundcolor", $template);
	$template = preg_replace("/<% gallery_width %>/", "$gallery_width", $template);
	$template = preg_replace("/<% version %>/", "$version", $template);
	echo "$template";
}
