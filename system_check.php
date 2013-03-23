<?php
ini_set("memory_limit","256M");

$exif = "No";
$gd = "No";
$update = "No";
if (function_exists('exif_read_data')) $exif = "Yes";
if (extension_loaded('gd') && function_exists('gd_info')) $gd = "Yes";
if (ini_get("allow_url_fopen") == 1) $update = "Yes";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="robots" content="noindex, nofollow">
<title>MiniGal Nano system check</title>
<style type="text/css">
body {
	background-color: #daddd8;
	font: 12px Arial, Tahoma, "Times New Roman", serif;
}
h1 {
	font-size: 30px;
	margin: 20px 0 5px 0;
	letter-spacing: -2px;
}
div {
	line-height: 20px;
}
.left {
	width: 300px;
	display: inline-table;
	background-color: #fdffbe;
	padding: 2px;
}
.middle-neutral {
	font-weight: bold;
	text-align: center;
	width: 100px;
	display: inline-table;
	background-color: #fdffbe;
	padding: 2px;
}
.middle-no {
	font-weight: bold;
	text-align: center;
	width: 100px;
	display: inline-table;
	background-color: #ff8181;
	padding: 2px;
}
.middle-yes {
	font-weight: bold;
	text-align: center;
	width: 100px;
	display: inline-table;
	background-color: #98ffad;
	padding: 2px;
}
.right {
	width: 600px;
	display: inline-table;
	background-color: #eaf1ea;
	padding: 2px;
}
</style>
<body>
<h1>MiniGal Nano system check</h1>
<div class="left">
	PHP Version
</div>
<div class="<?php if(version_compare(phpversion(), "4.0", '>')) echo 'middle-yes'; else echo 'middle-no' ?>">
	<?php echo phpversion(); ?>
</div>
<div class="right">
	<a href="http://www.php.net/" target="_blank">PHP</a> scripting language version 4.0 or greater is needed
</div>
<br />

<div class="left">
	GD library support
</div>
<div class="<?php if($gd == "Yes") echo 'middle-yes'; else echo 'middle-no' ?>">
	<?php echo $gd; ?>
</div>
<div class="right">
	<a href="http://www.boutell.com/gd/" target="_blank">GD image manipulation</a> library is used to create thumbnails. Bundled since PHP 4.3
</div>
<br />

<div class="left">
	EXIF support
</div>
<div  class="<?php if($exif == "Yes") echo 'middle-yes'; else echo 'middle-neutral' ?>">
	<?php echo $exif; ?>
</div>
<div class="right">
	Ability to extract and display <a href="http://en.wikipedia.org/wiki/Exif" target="_blank">EXIF information</a>. The script will work without it, but not display image information
</div>
<br />

<div class="left">
	PHP memory limit
</div>
<div class="middle-neutral">
	<?php echo ini_get("memory_limit"); ?>
</div>
<div class="right">
	Memory is needed to create thumbnails. Bigger images uses more memory	
</div>
<br />

<div class="left">
	New version check
</div>
<div class="middle-neutral">
	<?php echo $update ?>
</div>
<div class="right">
	The ability to check for new version and display this automatically. The script will work without it	
</div>
<br /><br />
<a href="http://www.minigal.dk/minigal-nano.html" target="_blank">Support website</a>
| <a href="http://www.minigal.dk/forum" target="_blank">Support forum</a>
</body>
</html>
