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
Example: <img src="createthumb.php?filename=photo.jpg&amp;width=100&amp;height=100">
*/
//	error_reporting(E_ALL);
	error_reporting(0);
/*
if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) header('Content-type: image/jpeg');
if (preg_match("/.gif$/i", $_GET['filename'])) header('Content-type: image/gif');
if (preg_match("/.png$/i", $_GET['filename'])) header('Content-type: image/png');
*/

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

function str_split_php4( $text, $split = 1 ) {
	// place each character of the string into and array
	$array = array();
	for ( $i=0; $i < strlen( $text ); )
	{
		$key = NULL;
		for ( $j = 0; $j < $split; $j++, $i++ )
		{
			$key .= $text[$i];
		}
		array_push( $array, $key );
	}
	return $array;
}

function sanitize($name)
{
	// Sanitize image filename (taken from http://iamcam.wordpress.com/2007/03/20/clean-file-names-using-php-preg_replace/ )
	$fname=$name;
	$replace="_";
	$pattern="/([[:alnum:]_\.-]*)/";
	$fname=str_replace(str_split_php4(preg_replace($pattern,$replace,$fname)),$replace,$fname);
	return $fname;
}

// Make sure the "thumbs" directory exists.
if (!is_dir('thumbs'))
{
	mkdir('thumbs',0700);
}

// Thumbnail file name and path.
// (We always put thumbnails in jpg for simplification)
$thumbname = 'thumbs/'.sanitize($_GET['filename']).'.jpg';

if (file_exists($thumbname))// If thumbnail exists, serve it.
{
	$fd = fopen($thumbname, "r");
	$cacheContent = fread($fd,filesize ($thumbname));
	fclose($fd);
	header('Content-type: image/jpeg');
	echo($cacheContent);
}
else // otherwise, generate thumbnail, send it and save it to file.
{

	// Display error image if file isn't found
	if (!is_file($_GET['filename'])) {
		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG('images/questionmark.jpg');
		ImageJPEG($errorimage,null,90);
	}
	
	// Display error image if file exists, but can't be opened
	if (substr(decoct(fileperms($_GET['filename'])), -1, strlen(fileperms($_GET['filename']))) < 4 OR substr(decoct(fileperms($_GET['filename'])), -3,1) < 4) {
		header('Content-type: image/jpeg');
		$errorimage = ImageCreateFromJPEG('images/cannotopen.jpg');
		ImageJPEG($errorimage,null,90);
	}
	
	// Define variables
	$target = "";
	$xoord = 0;
	$yoord = 0;

	if ($_GET['size'] == "")
	{
		$_GET['size'] = 120;
	}
	$imgsize = GetImageSize($_GET['filename']);
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
	if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename']))
	{
		if (function_exists('exif_read_data') && function_exists('imagerotate'))
		{
			$exif = exif_read_data($_GET['filename'], 0, true);
			$ort = $exif['IFD0']['Orientation'];
			$degrees = 0;
			switch($ort)
			{
				case 3: // 180 rotate right
					$degrees = 180;
				break;
				case 6: // 90 rotate right
					$degrees = 270;
				break;
				case 8:	// 90 rotate left
					$degrees = 90;
				break;
				case 2: // flip vertical
					$flip = 'vertical';
				break;
				// see http://www.daveperrett.com/articles/2012/07/28/exif-orientation-handling-is-a-ghetto/ for more info on orientation
				case 7: // flipped
                                        $degrees = 90;
					$flip = 'vertical';
				break;
				case 5: // flipped
					$degrees = 270;
					$flip = 'vertical';
				break;
				case 4: // flipped
					$degrees = 180;
					$flip = 'vertical';
				break;
			}
		}
	}

	$target = ImageCreatetruecolor($_GET['size'],$_GET['size']);

	//if the picture can be transparent, add a white background instead a black
	if (preg_match("/.gif$/i", $_GET['filename']) || preg_match("/.png$/i", $_GET['filename'])) 
	{
		$backgroundColor = imagecolorallocate($target, 255, 255, 255);
		imagefill($target, 0, 0, $backgroundColor);
	}


	if (preg_match("/.jpg$/i", $_GET['filename'])) $source = ImageCreateFromJPEG($_GET['filename']);
	if (preg_match("/.gif$/i", $_GET['filename'])) $source = ImageCreateFromGIF($_GET['filename']);
	if (preg_match("/.png$/i", $_GET['filename'])) $source = ImageCreateFromPNG($_GET['filename']);
	imagecopyresampled($target,$source,0,0,$xoord,$yoord,$_GET['size'],$_GET['size'],$width,$height);
	imagedestroy($source);

	//if (preg_match("/.jpg$/i", $_GET['filename'])) ImageJPEG($target,null,90);
	//if (preg_match("/.gif$/i", $_GET['filename'])) ImageGIF($target,null,90);
	//if (preg_match("/.png$/i", $_GET['filename'])) ImageJPEG($target,null,90); // Using ImageJPEG on purpose

	//proper rotation by jan niggemann
	if ($degrees != 0)
	{
		$target = imagerotate($target, $degrees, 0);
	}

	ob_start(); // Start output buffering.
	header('Content-type: image/jpeg'); // We always render the thumbnail in JPEG even if the source is GIF or PNG.

	//proper mirror (aka flip) by jan niggemann
	if ($flip == 'vertical')
	{
		//only in php >= 5.5.0 ImageJPEG(imageflip($target, IMG_FLIP_VERTICAL),null,80);
		flipVertical($target);
		flipHorizontal($target);
		flipVertical($target);
		ImageJPEG($target,null,80);
	} else {
		ImageJPEG($target,null,80);
	}

	imagedestroy($target);

	$cachedImage = ob_get_contents(); // Get the buffer content.
	ob_end_flush();// End buffering
	$fd = fopen($thumbname, "w"); // Save buffer to disk
	if ($fd)
	{
		fwrite($fd,$cachedImage);
		fclose($fd);
	}
}

?>
