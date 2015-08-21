<?php
include('includes/wrapper/php-riot-api.php');
include('includes/wrapper/FileSystemCache.php');

//testing classes
//using double quotes seems to make all names work (see issue: https://github.com/kevinohashi/php-riot-api/issues/33)
$summoner_name = "machinaxxx"; 
// $summoner_id = 585897;

$test = new riotapi('oce', '8d44fc30-4ef3-4a8e-8aae-a6b124e37f21');


// $testCache = new riotapi('na', new FileSystemCache('cache/'));
//$r = $test->getSummonerByName($summoner_name);

//$r = $test->getSummoner($summoner_id);
//$r = $test->getSummoner($summoner_id,'masteries');
//$r = $test->getSummoner($summoner_id,'runes');
//$r = $test->getSummoner($summoner_id,'name');
//$r = $test->getStats($summoner_id);
//$r = $test->getStats($summoner_id,'ranked');
//$r = $test->getTeam($summoner_id);
//$r = $test->getLeague($summoner_id);
//$r = $test->getGame($summoner_id);
//$r = $test->getChampion();
try {
	for($i=0; $i < 1; $i++) {
		// $r = $test->getSummonerByName($summoner_name);
		$r = $test->getMatch('98509596','includeTimeline=true');
    	print_r($r);
    	print_r($i);
	}
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
};
// echo "<br>\r\n testing cache:";
// try {
//     $r = $testCache->getSummoner($summoner_id);
//     print_r($r);
// } catch(Exception $e) {
//     echo "Error: " . $e->getMessage();
// };

?>