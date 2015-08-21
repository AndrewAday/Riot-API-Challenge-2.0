<?php

$item_json = file_get_contents('static-data/items.json');
$ITEMS = json_decode($item_json, true);

?>