<?php

include 'includes/items.php';

$MAJOR_ITEMS = ['items' => []];
foreach($ITEMS['data'] as $key => $value) {
	if ($value['gold']['total'] >= 1900) {
		$MAJOR_ITEMS[$key] = $value;
		array_push($MAJOR_ITEMS['items'], $key);
	}

}

$file = fopen('includes/static-data/major_items.json', 'w');
fwrite($file, json_encode($MAJOR_ITEMS));
fclose($file);

?>