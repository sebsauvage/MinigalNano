Minigal Nano SSE
================

MiniGal Nano is a very simple image gallery. It adheres to the KISS principle and is very easy to install. MiniGal Nano does not have a web admin interface: You just upload your images in the photo folder. It only requires php and GD (no database, no special libraries like PEAR or ImageMagick).

Minigal Nano uses a javascript Lightbox (Use left/right arrows for navigation), but it degrades gracefully if javascript is disabled.

This fork (Minigal Nano SSE) is based on [Thomas Rybak's](http://www.minigal.dk/minigal-nano.html) version which seems to have been abandonned in 2010.

It adds a handfull of features like tumbnails cache and image/folder description.


Online demo: [http://sebsauvage.net/galerie/]()

Installation
============

* Place all the files in a directory on your server.
* Customize `config.php`
* Create the "photos" subdirectory and upload your photos.

Adding your photos
==================

* Simply add your photos to the `photos` directory.
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

--------------------------------------------------

MiniGal Nano SSE by Sébastien SAUVAGE is licensed under a Creative Commons Attribution-Share Alike.
https://creativecommons.org/licenses/by-sa/2.5/

--------------------------------------------------
