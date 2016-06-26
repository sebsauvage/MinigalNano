<?php

# Hardly inspired from here : codes-sources.commentcamarche.net/source/35937-creation-d-une-arborescenceI
# Listing all files of a folder and sub folders.
function listFiles(&$content, $folder, $keep_extensions, $SkipObjects) {
    $dir = opendir($folder);
    // Loop on all contained on the folder
    while (false !== ($current = readdir($dir))) {
        if ($current != '.' && $current != '..' && in_array($current, $SkipObjects) === false) {
            if (is_dir($folder . '/' . $current)) {
                ListFiles($content, $folder . '/' . $current, $keep_extensions, $SkipObjects);
            } else {
                $file_ext = strtolower(substr(strrchr($current, '.'), 1));
                // Should we display this extension ?
                if (in_array($file_ext, $keep_extensions)) {
                    $current_adress = $folder . '/' . $current;
                    array_push($content, $current_adress);
                }
            }
        }
    }
    closedir($dir);
    return $content;
}

# Paul's Simple Diff Algorithm v 0.1 : http://paulbutler.org/archives/a-simple-diff-algorithm-in-php/
function diff($old, $new) {
    $matrix = array();
    $maxlen = 0;
    foreach ($old as $oindex => $ovalue) {
        $nkeys = array_keys($new, $ovalue);
        foreach ($nkeys as $nindex) {
            $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
                $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
            if ($matrix[$oindex][$nindex] > $maxlen) {
                $maxlen = $matrix[$oindex][$nindex];
                $omax = $oindex + 1 - $maxlen;
                $nmax = $nindex + 1 - $maxlen;
            }
        }
    }
    if ($maxlen == 0) {
        return array(array('d' => $old, 'i' => $new));
    }

    return array_merge(
        diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}
