<?php 
/*===================*/
/*Functions*/
/*===================*/
	# Hardly inspired from here : codes-sources.commentcamarche.net/source/35937-creation-d-une-arborescenceI
	# Listing all files of a folder and sub folders. 
	function ListFiles($gallery_link, &$content, $Folder, $SkipFileExts, $SkipObjects)
	{
		$dir = opendir($Folder);
		while (false !== ($Current = readdir($dir))) // Loop on all contained on the folder
		{
			if ($Current !='.' && $Current != '..' && in_array($Current, $SkipObjects)===false)
			{
				if(is_dir($Folder.'/'.$Current)) // If the current element is a folder
				{
					ListFiles($gallery_link, $content, $Folder.'/'.$Current, $SkipFileExts, $SkipObjects); // Recursivity
				}
				else
				{
					$FileExt = strtolower(substr(strrchr($Current ,'.'),1));
					if (in_array($FileExt, $SkipFileExts)===false) // Should we display this extension ?
						$current_adress = $gallery_link . "/" . $Folder.'/'. $Current;
						#debug echo "display current_adress : ". $current_adress . "<br>";
						$content .=  $current_adress. "\n";
				}
			}
		}
		closedir($dir); 
		return $content;
	}

	# Paul's Simple Diff Algorithm v 0.1 : http://paulbutler.org/archives/a-simple-diff-algorithm-in-php/
	function diff($old, $new){
		$matrix = array();
		$maxlen = 0;
		foreach($old as $oindex => $ovalue){
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex){
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
					$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if($matrix[$oindex][$nindex] > $maxlen){
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}   
		}
		if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}

	function print_array($array_to_display) {
		echo '<pre>';
		print_r($array_to_display);
		echo '</pre>';
	}
/*===================*/
/*Variables*/
/*===================*/
	require("config.php");
	#$content = "";
	$old_files_list  = "db_old_files"; //list of files in ./photos
	$new_files_list  = "new_files_list";
	$db_feed_source = "db_feed_source";
	$Folder =  'photos';
	$SkipExts = array('html', 'txt', 'php');
	$SkipObjects = array('UnDossier', 'UnFichier');
	$content = ListFiles($gallery_link, $content, $Folder, $SkipExts, $SkipObjects);
	$to_store = "";
	// Init files
	if (!file_exists($old_files_list))
		file_put_contents($old_files_list, "");
	if (!file_exists($db_feed_source))
		file_put_contents($db_feed_source, "");

/*===================*/
/*Computing*/
/*===================*/
	#Todo : ajouter une condition : dois-je regénérer le flux ou utiliser les anciens fichiers ?

	// Load the list from files.
	$old_files_list_content = explode("\n", file_get_contents($old_files_list));
	$new_files_list_content = explode("\n", $content); #debug 
	
	// Generate and stock new elements
	$differences = diff($old_files_list_content, $new_files_list_content);
	for ($i=0; $i < count($differences); $i++) { 
		if (is_array($differences[$i])){
			for ($j=0; $j < count($differences[$i]["i"]); $j++) { 
				if (strlen($differences[$i]["i"][$j]) > 2)
					$to_store .= $differences[$i]["i"][$j] . "\n";
			}	
		}
	}

	//Add new elements at the top of the feed's source
	$temp = file_get_contents($db_feed_source);
	$temp = $to_store . $temp;
	file_put_contents($db_feed_source, $temp);

	// Store the current file list for the next generation
	file_put_contents($old_files_list, $content);

/*===================*/
/*XML Gen*/
/*===================*/
	$temp = explode("\n", $temp);

	echo "
		<rss version=\"2.0\">
			<channel>
			<title>".$title."</title>
			<link>".$gallery_link."</link>
			<description>".$description."</description>
		";
		for ($i=0; $i <= $nb_items_rss; $i++) { 
			echo
				"<item>
					<title>" . $temp[$i] . "</title>
					<link>". $temp[$i] . "</link>
					<description>
						<![CDATA[ <img src=\"" . $temp[$i] . "\"> ]]>
					</description>
				</item>"
			;
		}
		echo"
			</channel>
		</rss>
	";
?>