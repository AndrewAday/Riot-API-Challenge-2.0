<?php
include('includes/wrapper/php-riot-api.php');
include('includes/wrapper/FileSystemCache.php');



//Set DB
require_once('includes/db_connect.php');

// Get api key and do the stuff
if(isset($argv[1])) {
	
	list($api_key,$number) = explode(":", $argv[1]);

	//Path to match IDs
	$norms_path = "AP_ITEM_DATASET/5.14/NORMAL_5X5/" . $number;
	$ranked_path = "AP_ITEM_DATASET/5.14/RANKED_SOLO/" . $number;

	if(!is_dir($norms_path) || !is_dir($ranked_path)) {
		exit("paths screwed yo\n");
	}

	foreach(scandir($norms_path) as $file) {
		if ('.' === $file) continue;
        if ('..' === $file) continue;
		$region = strtolower(basename($file, ".json"));
		$region_matches = json_decode(file_get_contents($norms_path."/".$file), true);
		$total_matches = count($region_matches);
		$pos = 0;

		//Initialize API wrapper
		$api = new riotapi($region, $api_key);

		//Begin the calls, yo
		try {
			foreach($region_matches as $match_id) {
				$m = $api->getMatch($match_id,'includeTimeline=true');
				if (isset($m["timeline"])) {
					$matches->insert($m);
					printf("inserted match " . $pos . " / " . $total_matches . " for " . $region . " norms\n");
				} else {
					printf("Missing timeline: match " . $pos . " / " . $total_matches . "\n");
				}
				$pos++;
			}
		} catch(Exception $e) {
			echo "Error: " . $e->getMessage();
		}
	}

	foreach(scandir($ranked_path) as $file) {
		if ('.' === $file) continue;
        if ('..' === $file) continue;
		$region = strtolower(basename($file, ".json"));
		$region_matches = json_decode(file_get_contents($ranked_path."/".$file), true);
		$total_matches = count($region_matches);
		$pos = 0;

		//Initialize API wrapper
		$api = new riotapi($region, $api_key);

		//Begin the calls, yo
		try {
			foreach($region_matches as $match_id) {
				$m = $api->getMatch($match_id,'includeTimeline=true');
				if (isset($m["timeline"])) {
					$matches->insert($m);
					printf("inserted match " . $pos . " / " . $total_matches . " for " . $region . " ranked\n");
				} else {
					printf("Missing timeline: match " . $pos . " / " . $total_matches . "\n");
				}
				$pos++;
			}
		} catch(Exception $e) {
			echo "Error: " . $e->getMessage();
		}
	}



}


?>