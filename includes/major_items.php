<?php

$major_item_json = file_get_contents(dirname(__FILE__) . '/static-data/major_items.json');
$MAJOR_ITEMS = json_decode($major_item_json, true);

?>