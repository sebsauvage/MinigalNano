Minigal Nano SSE
================

MiniGal Nano is a very simple image gallery. It only requires php and GD (no database, no special libraries like PEAR or ImageMagick). MiniGal Nano does not have a web admin interface: You only have to upload your images in the photo folder.

This fork (Minigal Nano SSE) is based on Thomas Rybak's version which seems to have been abandonned in 2010: http://www.minigal.dk/minigal-nano.html

This fork holds some improvements, like thumbnail cache and folder/image description.

Installation
============

* Place all the files in a directory on your server.
* Create the "photos" subdirectory.

Adding your photos
==================

* Simply add your photos to the "photos" directory.
* You can create as many subdirectories as you want.
* To add a folder comment, simply create "comment.html" file in the folder.
* To add a title and caption on an image, create a .html files with: title::caption.
  Example: For: "myimage.jpg", create the file "myimage.jpg.html" containing:
         My cat::My cat <i>loves</i> to roll on the floor.
  The title and caption will be displayed when clicking on the image.

------------------------------------------------------------------------------

MiniGal Nano SSE by Sébastien SAUVAGE is licensed under a Creative Commons Attribution-Share Alike.
https://creativecommons.org/licenses/by-sa/2.5/

------------------------------------------------------------------------------
