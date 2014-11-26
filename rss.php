<?php
/*============================*/
/* Gallery address definition */
/*============================*/

if (!empty($_SERVER['REQUEST_SCHEME'])) {
    $g_protocol = $_SERVER['REQUEST_SCHEME'];
} elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    $g_protocol = 'https';
} else {
    $g_protocol = 'http';
}
$g_host = $_SERVER['HTTP_HOST'];
$g_port = $_SERVER['SERVER_PORT'];
$g_path = dirname($_SERVER['REQUEST_URI']);

// remove default ports
if (($g_protocol == 'https' && $g_port == '443') ||
    ($g_protocol == 'http' && $g_port == '80')) {
    $g_port = '';
} else {
    $g_port = ':' . $g_port;
}

if ($g_path[0] != '/') {
    $g_path = '/' . $g_path;
}

$gallery_link = $g_protocol . '://' . $g_host . $g_port . $g_path;

/*===================*/
/* Functions         */
/*===================*/
# Hardly inspired from here : codes-sources.commentcamarche.net/source/35937-creation-d-une-arborescenceI
# Listing all files of a folder and sub folders. 
function ListFiles($gallery_link, &$content, $Folder, $SkipFileExts, $SkipObjects)
{
    $dir = opendir($Folder);
    // Loop on all contained on the folder
    while (false !== ($Current = readdir($dir))) {
        if ($Current !='.' && $Current != '..' && in_array($Current, $SkipObjects) === false) {
            if (is_dir($Folder.'/'.$Current)) {
                ListFiles($gallery_link, $content, $Folder . '/' . $Current, $SkipFileExts, $SkipObjects);
            } else {
                $FileExt = strtolower(substr(strrchr($Current, '.'), 1));
                // Should we display this extension ?
                if (in_array($FileExt, $SkipFileExts) === false) {
                    $current_adress = $gallery_link . "/" . $Folder.'/'. $Current;
                    $content .= $current_adress. "\n";
                }
            }
        }
    }
    closedir($dir); 
    return $content;
}

# Paul's Simple Diff Algorithm v 0.1 : http://paulbutler.org/archives/a-simple-diff-algorithm-in-php/
function diff($old, $new)
{
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
    if ($maxlen == 0)
        return array(array('d'=>$old, 'i'=>$new));
    return array_merge(
        diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
        array_slice($new, $nmax, $maxlen),
        diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function print_array($array_to_display)
{
    echo '<pre>';
    print_r($array_to_display);
    echo '</pre>';
}

/*===================*/
/* Variables         */
/*===================*/
require("config-default.php");
include("config.php");
#$content = "";
$old_files_list = "db_old_files"; //list of files in ./photos
$db_feed_source = "db_feed_source";
$db_rss_timestamp = "db_rss_timestamp";
$Folder = 'photos';
$content = ListFiles($gallery_link, $content, $Folder, $SkipExts, $SkipObjects);
$to_store = "";
// Init files
if (!file_exists($old_files_list)) {
    file_put_contents($old_files_list, "");
}
if (!file_exists($db_feed_source)) {
    file_put_contents($db_feed_source, "");
}
if (!file_exists($db_rss_timestamp)) {
    file_put_contents($db_rss_timestamp, "");
}

/*===================*/
/* Computing         */
/*===================*/
#Todo : ajouter une condition : dois-je regénérer le flux ou utiliser les anciens fichiers ?
$temp = file_get_contents($db_feed_source);
$last_rss_gen = file_get_contents($db_rss_timestamp);
$current_time = time();
// If the RSS generation is already launched, don't do a second generation at the same time
if (($current_time - $last_rss_gen) > $rss_refresh_interval && file_exists("rss.locker") == false) {
    file_put_contents("rss.locker", "");
    file_put_contents($db_rss_timestamp, time());
    // Load the list from files.
    $old_files_list_content = explode("\n", file_get_contents($old_files_list));
    $new_files_list_content = explode("\n", $content); #debug 
    // Generate and stock new elements
    $differences = diff($old_files_list_content, $new_files_list_content);
    for ($i=0; $i < count($differences); $i++) {
        if (is_array($differences[$i])) {
            for ($j=0; $j < count($differences[$i]["i"]); $j++) {
                if (strlen($differences[$i]["i"][$j]) > 2) {
                    $to_store .= $differences[$i]["i"][$j] . "\n";
                }
            }
        }
    }
    // Add new elements at the top of the feed's source
    $temp = $to_store . $temp;
    file_put_contents($db_feed_source, $temp);
    // Store the current file list for the next generation
    file_put_contents($old_files_list, $content);
    unlink("rss.locker");
}
/*===================*/
/* XML Gen           */
/*===================*/
$temp = explode("\n", $temp);
$pieceOfTitle;
$titleLenght;
echo "
    <rss version=\"2.0\">
        <channel>
        <title>".$title."</title>
        <link>".$gallery_link."</link>
        <description>".$description."</description>
    ";
for ($i=0; $i < $nb_items_rss; $i++) {
    $pieceOfTitle = strrchr ($temp[$i] , "/");
    $titleLenght = strlen($pieceOfTitle) - strlen(strrchr($pieceOfTitle, "."));
    echo "<item>
            <title>" . substr($pieceOfTitle, 1, $titleLenght-1) . "</title>
            <link>". $temp[$i] . "</link>
            <description>
                <![CDATA[ <img src=\"" . $temp[$i] . "\"> ]]>
            </description>
        </item>";
    if ($temp[$i+1] == NULL)
        break;
}
echo "
        </channel>
    </rss>
";
