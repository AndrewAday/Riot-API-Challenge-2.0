
<?php

$norms_path = "AP_ITEM_DATASET/5.14/NORMAL_5X5/key_1/";
// require_once 'includes/db_connect.php';
include 'includes/parse_match.php';
include 'includes/major_items.php';

foreach(scandir($norms_path) as $file) {
		if ('.' === $file) continue;
        if ('..' === $file) continue;
		print_r(strtolower(basename($file, ".json").PHP_EOL));
	}

// $doc = $matches->findOne();
$doc = json_decode(file_get_contents('sample_match.json'), true);
$doc = parseMatch($doc, $MAJOR_ITEMS);
print_r(json_encode($doc) . "\n");

// print_r($MAJOR_ITEMS['items']);
//print_r($doc['timeline']);
$counter = 0;
/*foreach($doc['timeline']['frames'] as $frame) {
	
	unset($frame['participantFrames']);
	if (isset($frame['events'])) {
		print_r($counter . PHP_EOL);	
	}
	
	$counter++;
}
print_r($counter."\n");
*/

?>
