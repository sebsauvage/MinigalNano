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


USAGE EXAMPLE:
File: createthumb.php
Example: <img src="createthumb.php?filename=photo.jpg&amp;width=100&amp;height=100">
*/
//	error_reporting(E_ALL);

if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) header('Content-type: image/jpeg');
if (preg_match("/.gif$/i", $_GET['filename'])) header('Content-type: image/gif');
if (preg_match("/.png$/i", $_GET['filename'])) header('Content-type: image/png');

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

    if ($_GET['size'] == "") $_GET['size'] = 120; //
       $imgsize = GetImageSize($_GET['filename']);
       $width = $imgsize[0];
       $height = $imgsize[1];
      if ($width > $height) { // If the width is greater than the height it’s a horizontal picture
        $xoord = ceil(($width-$height)/2);
        $width = $height;      // Then we read a square frame that  equals the width
      } else {
        $yoord = ceil(($height-$width)/2);
        $height = $width;
      }

    // Rotate JPG pictures
    if (preg_match("/.jpg$|.jpeg$/i", $_GET['filename'])) {
		if (function_exists('exif_read_data') && function_exists('imagerotate')) {
			$exif = exif_read_data($_GET['filename']);
			$ort = $exif['IFD0']['Orientation'];
			$degrees = 0;
		    switch($ort)
		    {
		        case 6: // 90 rotate right
		            $degrees = 270;
		        break;
		        case 8:    // 90 rotate left
		            $degrees = 90;
		        break;
		    }
			if ($degrees != 0)	$target = imagerotate($target, $degrees, 0);
		}
	}
	
         $target = ImageCreatetruecolor($_GET['size'],$_GET['size']);
         if (preg_match("/.jpg$/i", $_GET['filename'])) $source = ImageCreateFromJPEG($_GET['filename']);
         if (preg_match("/.gif$/i", $_GET['filename'])) $source = ImageCreateFromGIF($_GET['filename']);
         if (preg_match("/.png$/i", $_GET['filename'])) $source = ImageCreateFromPNG($_GET['filename']);
         imagecopyresampled($target,$source,0,0,$xoord,$yoord,$_GET['size'],$_GET['size'],$width,$height);
		 imagedestroy($source);

         if (preg_match("/.jpg$/i", $_GET['filename'])) ImageJPEG($target,null,90);
         if (preg_match("/.gif$/i", $_GET['filename'])) ImageGIF($target,null,90);
         if (preg_match("/.png$/i", $_GET['filename'])) ImageJPEG($target,null,90); // Using ImageJPEG on purpose
         imagedestroy($target);



?>