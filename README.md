MinigalNano
================

MinigalNano is a very simple image gallery. It adheres to the KISS principle and is very easy to install. MinigalNano does not have a web admin interface: You just upload your images in the photo folder on your server (using FTP, SFTP...). It only requires php and GD (no database, no special libraries like PEAR or ImageMagick).

MinigalNano uses a javascript Lightbox (Use left/right arrows for navigation), but it degrades gracefully if javascript is disabled.

MinigalNano is based on [Minigal Nano SSE by Sebsauvage](https://github.com/sebsauvage/MinigalNano), that is based on [Thomas Rybak's](http://www.minigal.dk/minigal-nano.html) version which seems to have been abandonned in 2010.

It adds new themes and icons, use more modern html/css, updates JS libs, and wants to be more community pull-friendly for the future.

Online demo: [http://boards.tomcanac.com/](http://boards.tomcanac.com/)

Features
============
 * Very simple media gallery
 * Support for PNG, GIF, JPEG image formats
 * Support for OGV ad WEBM video
 * Javascript lightbox, degrades gracefull if JavaScript is disabled

Installation
============

* Requires `php` and `php5-gd`
* Place all the files in a directory on your server.
* Customize options in `config.php`
* Upload your photos to the `photos/` subdirectory.

Adding your photos
==================

* Simply add your photos to the `photos` directory via FTP or SFTP.
* You can create as many subdirectories as you want.

Adding a comment to a gallery
=============================

* Simple create `comment.html` in the gallery's folder.

Adding a comment to an image
============================

* Create an html file. Filename must be the image filename plus `.html`. (eg. `myimage.jpg` → `myimage.jpg.html`)
* The html file can contain:
 * comment
 * title::comment


Using a custom image for folders
================================

* Create `folder.jpg` inside a folder: This image will be used as default image for folder.


Thumbnails
=========
You do not have to care about thumbnails: They are automatically created in the `thumbs` directory.
If some thumbnails are wrong, you can purge this directory: Thumbnails will be automatically re-created.

------------------------------------------------------------

MinigalNano is licensed under the Creative Commons Attribution-Share Alike license (http://creativecommons.org/licenses/by-sa/3.0/)

 * [Project contributors](humans.txt)
 * [Original discussion page (english)](http://sebsauvage.net/wiki/doku.php?id=minigal_nano_en)
 * [Original discussion page (french)](http://sebsauvage.net/wiki/doku.php?id=minigal_nano)
 * [List of modifications between 0.3.5 and 0.3.5 SSE2](http://sebsauvage.net/wiki/doku.php?id=minigal_nano_diff)