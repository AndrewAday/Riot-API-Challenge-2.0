
<?php

$norms_path = "AP_ITEM_DATASET/5.14/NORMAL_5X5/key_1/";
require_once 'includes/db_connect.php';
include 'includes/parse_match.php';

foreach(scandir($norms_path) as $file) {
		if ('.' === $file) continue;
        if ('..' === $file) continue;
		print_r(strtolower(basename($file, ".json").PHP_EOL));
	}

$doc = $matches->findOne();
$doc = parseMatch($doc);
print_r(json_encode($doc));



?>
