<?php
//Strip all unnecessary fields from DB
require_once 'includes/db_connect.php';
include 'includes/parse_match.php';
include 'includes/major_items.php';
$number = $matches->count();
$counter = 1;
$cursor = $matches->find()->skip(124500);



var_dump($cursor->getNext());

// while ($counter <= $number) {
// 	try {
// 		$doc = $cursor->next();
// 		print_r($doc . "\n");
// 		$matchId = $doc['matchId'];
// 	 	$match_trim = parseMatch($doc,$MAJOR_ITEMS);
// 	 	if ($matches->update($doc, $match_trim)) {
// 			print_r($matchId . " updated " . $counter . "/" . $number . "\n");
// 		}	
// 	} catch (Exception $e) {
// 		echo 'Caught exception: ',  $e->getMessage(), "\n";
// 		echo 'skip insertion' . $counter;
// 	}
// 	$counter++;
// }


/*
$doc = $matches->findOne(['matchId' => 573468770]);
$matchId = $doc['matchId'];
	if ($matches->remove(['matchId' => $matchId])) {
		print_r($matchId . " removed " . $counter . "/" . $number . "\n");
	}
	$match_trim = parseMatch($doc,$MAJOR_ITEMS);
	$matches->insert($match_trim);
	$counter++;
*/
?>
