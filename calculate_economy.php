<?php
require_once('includes/api_key.php');
if(!(isset($_GET['n'])&&isset($_GET['c'])))
	echo 'error: set summoner name and champion';
else
{
	$api = new riotapi($na, $api_key);
	$summonerId = $api->getSummonerId($_GET['n']);
	$matches = $api->getMatchHistory($summonerId, $_GET['c']);
	//Calculate by going through matches
}
?>