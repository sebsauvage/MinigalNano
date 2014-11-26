<?php
/*
MINIGAL NANO
- A PHP/HTML/CSS based image gallery script

This script and included files are subject to licensing from Creative Commons (http://creativecommons.org/licenses/by-sa/2.5/)
You may use, edit and redistribute this script, as long as you pay tribute to the original author by NOT removing the linkback to www.minigal.dk ("Powered by MiniGal Nano x.x.x")

MiniGal Nano is created by Thomas Rybak

Copyright 2010 by Thomas Rybak
Support: www.minigal.dk
Community: www.minigal.dk/forum

Please enjoy this free script!

Version 0.3.5 modified by Sebastien SAUVAGE (sebsauvage.net):
 - Added thumbnail cache (reduces server CPU load, server bandwith and speeds up client page display).
 - Thumbnails are now always in JPEG even if the source image is PNG or GIF.

USAGE EXAMPLE:
File: createthumb.php
Example: <img src="createthumb.php?filename=photo.jpg&amp;size=100">
*/

error_reporting(0);

$get_filename = $_GET['filename'];
$get_size = @$_GET['size'];
if (empty($get_size)) $get_size = 120;

if (preg_match("/.jpe?g$/i", $get_filename)) $get_filename_type = "JPG";
if (preg_match("/.gif$/i", $get_filename)) $get_filename_type = "GIF";
if (preg_match("/.png$/i", $get_filename)) $get_filename_type = "PNG";

// flip functions from http://stackoverflow.com/questions/8232722/how-to-flip-part-of-an-image-horizontal-with-php-gd
function flipVertical(&$img) {
	$size_x = imagesx($img);
	$size_y = imagesy($img);
	$temp = imagecreatetruecolor($size_x, $size_y);
	$x = imagecopyresampled($temp, $img, 0, 0, 0, ($size_y-1), $size_x, $size_y, $size_x, 0-$size_y);
	if ($x)
	{
		$img = $temp;
	} else {
		die("Unable to flip image");
	}
}

function flipHorizontal(&$img) {
	$size_x = imagesx($img);
	$size_y = imagesy($img);
	$temp = imagecreatetruecolor($size_x, $size_y);
	$x = imagecopyresampled($temp, $img, 0, 0, ($size_x-1), 0, $size_x, $size_y, 0-$size_x, $size_y);
	if ($x)
	{
		$img = $temp;
	} else {
		die("Unable to flip image");
	}
}

function sanitize($name) {
	// note: this allows for thumbname collisions
	return preg_replace("/[^[:alnum:]_.-]/", "_", $name);
}

// Make sure the "thumbs" directory exists.
if (!is_dir('thumbs') && is_writable('.'))
{
	mkdir('thumbs',0700);
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
	echo($cacheContent);
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
if ($width > $height) // If the width is greater than the height it’s a horizontal picture
{
	$xoord = ceil(($width-$height)/2);
	$width = $height; // Then we read a square frame that equals the width
}
else
{
	$yoord = ceil(($height-$width)/2);
	$height = $width;
}

// Rotate JPG pictures
$degrees = 0;
$flip = '';
if ($get_filename_type == "JPG")
{
	if (function_exists('exif_read_data') && function_exists('imagerotate'))
	{
		$exif = exif_read_data($get_filename, 0, true);
		if (isset($exif['IFD0']) && isset($exif['IFD0']['Orientation']))
			$ort = $exif['IFD0']['Orientation'];
		else
			$ort = 0;
		// for more info on orientation see
		// http://www.daveperrett.com/articles/2012/07/28/exif-orientation-handling-is-a-ghetto/
		$ort2deg = array(3=>180, 4=>180, 5=>270, 6=>270, 7=>90, 8=>90);
		$degrees = in_array($ort, $ort2deg) ? $ort2deg[$ort] : 0;
		$ort2flip = array(2, 4, 5, 7);
		$flip = in_array($ort, $flip2deg) ? 'vertical' : '';
	}
}

$target = imagecreatetruecolor($get_size, $get_size);

// if the picture can be transparent, add a white background instead a black
if (in_array($get_filename_type, array("GIF", "PNG")))
{
	$backgroundColor = imagecolorallocate($target, 255, 255, 255);
	imagefill($target, 0, 0, $backgroundColor);
}


if ($get_filename_type == "JPG") $source = imagecreatefromjpeg($get_filename);
if ($get_filename_type == "GIF") $source = imagecreatefromgif($get_filename);
if ($get_filename_type == "PNG") $source = imagecreatefrompng($get_filename);
imagecopyresampled($target, $source, 0, 0, $xoord, $yoord, $get_size, $get_size, $width, $height);
imagedestroy($source);

//proper rotation by jan niggemann
if ($degrees != 0)
{
	$target = imagerotate($target, $degrees, 0);
}

//proper mirror (aka flip) by jan niggemann
if ($flip == 'vertical')
{
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

if (is_writable(dirname($thumbname)))
{
	$fd = fopen($thumbname, "w"); // Save buffer to disk
	if ($fd)
	{
		fwrite($fd,$cachedImage);
		fclose($fd);
	}
}


?>
