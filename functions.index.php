<?php

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

    if (preg_match($pattern, $path)) {
        die("ERROR: Could not open " . htmlspecialchars(stripslashes($current_dir)) . " for reading!");
    }
}
