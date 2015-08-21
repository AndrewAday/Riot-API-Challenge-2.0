<?php

if ($mongo  = new MongoClient("mongodb://localhost")) {
	$db = $mongo->LeagueOfLegends;
   	$matches = $db->matches;
} else {
	printf("no db\n");
	exit;
}

?>