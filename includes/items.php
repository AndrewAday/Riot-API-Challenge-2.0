<?php

$item_json = file_get_contents(dirname(__FILE__) . '/static-data/items.json');
$ITEMS = json_decode($item_json, true);

?>