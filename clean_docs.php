<?php
//Strip all unnecessary fields from DB
require_once 'includes/db_connect.php';
include 'includes/parse_match.php';
include 'includes/major_items.php';
$number = $matches->count();
$counter = 1;
$cursor = $matches->find();
// foreach($cursor as $doc) {
// 	$matchId = $doc['matchId'];
// 	if ($matches->remove(['matchId' => $matchId])) {
// 		print_r($matchId . " removed " . $counter . "/" . $number . "\n");
// 	}
// 	$match_trim = parseMatch($doc,$MAJOR_ITEMS);
// 	$matches->insert($match_trim);
// 	$counter++;
// }

$doc = $matches->findOne();
$matchId = $doc['matchId'];
	if ($matches->remove(['matchId' => $matchId])) {
		print_r($matchId . " removed " . $counter . "/" . $number . "\n");
	}
	$match_trim = parseMatch($doc,$MAJOR_ITEMS);
	$matches->insert($match_trim);
	$counter++;

?>