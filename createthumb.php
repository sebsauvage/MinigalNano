<?php
/*
 * This file is part of MinigalNano: https://github.com/sebsauvage/MinigalNano
 *
 * MiniGal Nano is based on an original work by Thomas Rybak (© 2010)
 *
 * MinigalNano is licensed under the AGPL v3 (https://gnu.org/licenses/agpl-3.0.txt).
 */

error_reporting(0);

$get_filename = $_GET['filename'];
$get_size = @$_GET['size'];
if (empty($get_size)) {
	$get_size = 120;
}

if (preg_match("/^\/.*/i", $get_filename)) {
	die("Unauthorized access !");
}

if (preg_match("/.jpe?g$/i", $get_filename)) {
	$get_filename_type = "JPG";
}

if (preg_match("/.gif$/i", $get_filename)) {
	$get_filename_type = "GIF";
}

if (preg_match("/.png$/i", $get_filename)) {
	$get_filename_type = "PNG";
}

/**
 * Vertical flip
 * @author http://stackoverflow.com/questions/8232722/how-to-flip-part-of-an-image-horizontal-with-php-gd
 */
function flipVertical(&$img) {
	$size_x = imagesx($img);
	$size_y = imagesy($img);
	$temp = imagecreatetruecolor($size_x, $size_y);
	$x = imagecopyresampled($temp, $img, 0, 0, 0, ($size_y - 1), $size_x, $size_y, $size_x, 0 - $size_y);
	if ($x) {
		$img = $temp;
	} else {
		die("Unable to flip image");
	}
}

/**
 * Horizontal flip
 * @author http://stackoverflow.com/questions/8232722/how-to-flip-part-of-an-image-horizontal-with-php-gd
 */
function flipHorizontal(&$img) {
	$size_x = imagesx($img);
	$size_y = imagesy($img);
	$temp = imagecreatetruecolor($size_x, $size_y);
	$x = imagecopyresampled($temp, $img, 0, 0, ($size_x - 1), 0, $size_x, $size_y, 0 - $size_x, $size_y);
	if ($x) {
		$img = $temp;
	} else {
		die("Unable to flip image");
	}
}

/**
 * Sanitizing (this allows for collisions)
 */
function sanitize($name) {
	return preg_replace("/[^[:alnum:]_.-]/", "_", $name);
}

// Make sure the "thumbs" directory exists.
if (!is_dir('thumbs') && is_writable('.')) {
	mkdir('thumbs', 0700);
}

// Thumbnail file name and path.
// (We always put thumbnails in jpg for simplification)
$thumbname = 'thumbs/' . sanitize($get_filename) . '.jpg';

if (file_exists($thumbname)) // If thumbnail exists, serve it.
{
	$fd = fopen($thumbname, "r");
	$cacheContent = fread($fd, filesize($thumbname));
	fclose($fd);
	header('Content-type: image/jpeg');
	echo ($cacheContent);
	exit;
}

// Display error image if file isn't found
if (!is_file($get_filename)) {
	header('Content-type: image/jpeg');
	$errorimage = imagecreatefromjpeg('images/questionmark.jpg');
	imagejpeg($errorimage, null, 90);
	imagedestroy($errorimage);
	exit;
}

// Display error image if file exists, but can't be opened
if (!is_readable($get_filename)) {
	header('Content-type: image/jpeg');
	$errorimage = imagecreatefromjpeg('images/cannotopen.jpg');
	imagejpeg($errorimage, null, 90);
	imagedestroy($errorimage);
	exit;
}

// otherwise, generate thumbnail, send it and save it to file.

$target = "";
$xoord = 0;
$yoord = 0;

$imgsize = getimagesize($get_filename);
$width = $imgsize[0];
$height = $imgsize[1];
// If the width is greater than the height it’s a horizontal picture
if ($width > $height) {
	$xoord = ceil(($width - $height) / 2);
	// Then we read a square frame that equals the width
	$width = $height;
} else {
	$yoord = ceil(($height - $width) / 2);
	$height = $width;
}

// Rotate JPG pictures
// for more info on orientation see
// http://www.daveperrett.com/articles/2012/07/28/exif-orientation-handling-is-a-ghetto/

$degrees = 0;
$flip = '';
if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) {
	if (function_exists('exif_read_data') && function_exists('imagerotate')) {
		$exif = exif_read_data($_GET['filename'], 0, true);
		$ort = $exif['IFD0']['Orientation'];
		switch ($ort) {
			case 3:	// 180 rotate right
				$degrees = 180;
				break;
			case 6:	// 90 rotate right
				$degrees = 270;
				break;
			case 8:	// 90 rotate left
				$degrees = 90;
				break;
			case 2:	// flip vertical
				$flip = 'vertical';
				break;
			case 7:	// flipped
				$degrees = 90;
				$flip = 'vertical';
				break;
			case 5:	// flipped
				$degrees = 270;
				$flip = 'vertical';
				break;
			case 4:	// flipped
				$degrees = 180;
				$flip = 'vertical';
				break;
		}
	}
}

$target = imagecreatetruecolor($get_size, $get_size);

// if the picture can be transparent, add a white background
if (in_array($get_filename_type, array("GIF", "PNG"))) {
	$backgroundColor = imagecolorallocate($target, 255, 255, 255);
	imagefill($target, 0, 0, $backgroundColor);
}

if ($get_filename_type == "JPG") {
	$source = imagecreatefromjpeg($get_filename);
}

if ($get_filename_type == "GIF") {
	$source = imagecreatefromgif($get_filename);
}

if ($get_filename_type == "PNG") {
	$source = imagecreatefrompng($get_filename);
}

imagecopyresampled($target, $source, 0, 0, $xoord, $yoord, $get_size, $get_size, $width, $height);
imagedestroy($source);

//proper rotation by jan niggemann
if ($degrees != 0) {
	$target = imagerotate($target, $degrees, 0);
}

//proper mirror (aka flip) by jan niggemann
if ($flip == 'vertical') {
	//only in php >= 5.5.0 ImageJPEG(imageflip($target, IMG_FLIP_VERTICAL),null,80);
	flipVertical($target);
	flipHorizontal($target);
	flipVertical($target);
}

ob_start(); // Start output buffering.
header('Content-type: image/jpeg'); // We always render the thumbnail in JPEG even if the source is GIF or PNG.
imagejpeg($target, null, 80);
imagedestroy($target);
$cachedImage = ob_get_contents(); // Get the buffer content.
ob_end_flush(); // End buffering

if (is_writable(dirname($thumbname))) {
	$fd = fopen($thumbname, "w"); // Save buffer to disk
	if ($fd) {
		fwrite($fd, $cachedImage);
		fclose($fd);
	}
}
