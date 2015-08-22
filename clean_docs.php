<?php
//Strip all unnecessary fields from DB
require_once 'includes/db_connect.php';

$cursor = $matches->find()
foreach($cursor as $doc) {
	$matchId = $doc['matchId'];
	$matches->remove(['matchId' => $matchId]);

}

?>