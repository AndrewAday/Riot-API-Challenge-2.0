<?php
require_once('includes/db_connect.php');

$champions = json_decode(file_get_contents('includes/static-data/average_gold.json'), true);

foreach($cursor as $match) {
	$participants=$match['participants'];
	foreach($match['timeline']['frames']['events'] as $event)
	{
		$id=$event['participantId'];
		if(!array_key_exists('items', $participants[$id]))
		{
			$participants[$id]['items']=[];
		}
		if(count($participants[$id]['items'])<3)
		{
			$addItem=[];
			$addItem['itemId']=$event['itemId'];
			$addItem['timestamp']=$event['timestamp'];
			$participants[$id]['items'].push($addItem);
		}
			
	}
	foreach($participants as $key => $participant) {
		$champ=$participant['championid'];
		$lane=$participant['ParticipantTimeline']['lane'];
		$win=$match['teams'][$participant['teamId']]['winner'];
		//do calculations
	}
}

$file = fopen('includes/static-data/best_builds.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>