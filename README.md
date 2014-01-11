MiniBoardNano SSE
================

miniBoardNano is a fork of MiniGal Nano by Sebsauvage. It just make it more graphic, with new themes and icons. Some little technicals improvements are made, but all the MiniGal Nano stuff you can find over the web should be compatible with miniBoardNano. The following text is nearly the same as the official documentation of MiniGal Nano by Sebsauvage.

miniBoardNano is a very simple image gallery. It adheres to the KISS principle and is very easy to install. miniBoardNano does not have a web admin interface: You just upload your images in the photo folder. It only requires php and GD (no database, no special libraries like PEAR or ImageMagick).
Boardigal Nano uses a javascript Lightbox (Use left/right arrows for navigation), but it degrades gracefully if javascript is disabled.

miniBoardNano is based on Minigal Nano SEE by [Sebsauvage](https://github.com/sebsauvage/MinigalNano), that is is based on [Thomas Rybak's](http://www.minigal.dk/minigal-nano.html) version which seems to have been abandonned in 2010.

Its add new themes and icons, use more modern html/css, update JS libs, and wants to be more community pull-friendly for the future.

Online demo: [http://boards.tomcanac.com/](http://boards.tomcanac.com/)

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

* Icons in the images folder are from the nitrux pack [http://store.nitrux.in/](http://store.nitrux.in/)

miniBoardNano SSE by TomCanac is **highly** based on MiniGal Nano by Sébastien SAUVAGE  and is under the same licensed : Creative Commons Attribution-Share Alike.
https://creativecommons.org/licenses/by-sa/2.5/
--------------------------------------------------
