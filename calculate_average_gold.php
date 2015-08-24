<?php
require_once('includes/db_connect.php');
include('includes/champions.php');

$champions=[];

/* $champions Schema

$champions = {
	"champion_id" : {
		"name" : "champion_name",
		"LANE" : {
			"gold0" : {
				"players" : int,
				'stdev' : float,
				"gold" : float
			},
			"gold10" : {
				"players" : int,
				'stdev' : float,
				"gold" : float
			}
			"gold20" : {
				"players" : int,
				'stdev' : float,
				"gold" : float
			}
			"gold30" : {
				"players" : int,
				'stdev' : float,
				"gold" : float
			}
		}
	}
}

*/
$gpm_deltas = [
	'zeroToTen' => 'gold0', 
	'tenToTwenty' => 'gold10',
	'twentyToThirty' => 'gold20',
	'thirtyToEnd' => 'gold30'
];

$cursor = $matches->find();
// $cursor = $matches->find()->limit(100);
$total = $matches->count();
$counter = 1;
//Average gold
foreach($cursor as $match) {
	foreach($match['participants'] as $participant) {
		$champ=$participant['championId'];
		if(!array_key_exists($champ, $CHAMPIONS)) {
			//THEN IT'S A FRIGGIN BOT 
			print("SKIPPING BOT " . $champ . "\n");
			continue;
		}
		$champ_name = $CHAMPIONS[$champ]['name'];
		$lane=$participant['timeline']['lane'];
		if(!array_key_exists($champ, $champions))
		{
			$champions[$champ]= []; //Add key as champion id
			$champions[$champ]['name'] = $champ_name; //Add champion name
		}
		//Initialize data for $champ in $lane
		if(!array_key_exists($lane,$champions[$champ]))
		{
			$addLane=[];
			foreach($gpm_deltas as $key => $value) {
				$addLane[$value]['players'] = 0;
				$addLane[$value]['gold'] = 0.0;
				$addLane[$value]['stdev'] = 0.0;
			}
			$champions[$champ][$lane]=$addLane;
		}
		$gpms=$participant['timeline']['goldPerMinDeltas']; //the gpm deltas in 10min intervals
		//update all interval values in $champions
		foreach($gpms as $key => $value) {
			$champions[$champ][$lane][$gpm_deltas[$key]]['gold'] += $value;
			$champions[$champ][$lane][$gpm_deltas[$key]]['players']++;
		}	
		// $champions[$champ][$lane]['gold0']+=$gpms['zeroToTen'];
		// $champions[$champ][$lane]['gold10']+=$gpms['tenToTwenty'];
		// $champions[$champ][$lane]['gold20']+=$gpms['twentyToThirty'];
		// $champions[$champ][$lane]['gold30']+=$gpms['thirtyToEnd'];
		// $champions[$champ][$lane]['players']++;
	}
	print("Cumulating gold " . $match['matchId'] . " " . $counter . "/" . $total . "\n");
	$counter++;
}
//average

//print_r(json_encode($champions) . "\n");


foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{	
		if ($laneKey != 'name') {
			foreach($lane as $interval => $value) {
				if ($value['players'] > 0)
					$champions[$champKey][$laneKey][$interval]['gold'] = $value['gold'] / $value['players'];
			}
		}
		// $champions[$champKey][$laneKey]['gold0']=$lane['gold0']/$lane['players'];
		// $champions[$champKey][$laneKey]['gold10']=$lane['gold10']/$lane['players'];
		// $champions[$champKey][$laneKey]['gold20']=$lane['gold20']/$lane['players'];
		// $champions[$champKey][$laneKey]['gold30']=$lane['gold30']/$lane['players'];
	}
}

//stdev
$counter = 1;
foreach($cursor as $match) {
	foreach($match['participants'] as $participant) {
		$champ=$participant['championId'];
		if(!array_key_exists($champ, $CHAMPIONS)) {
			//THEN IT'S A FRIGGIN BOT 
			print("SKIPPING BOT " . $champ . "\n");
			continue;
		}
		$lane=$participant['timeline']['lane'];
		// if(!array_key_exists($lane,$champions[$champ]['stdev0']))
		// {
		// 	$champions[$champ][$lane]['stdev0']=0.0;
		// 	$champions[$champ][$lane]['stdev10']=0.0;
		// 	$champions[$champ][$lane]['stdev20']=0.0;
		// 	$champions[$champ][$lane]['stdev30']=0.0;
		// }
		$gpms=$participant['timeline']['goldPerMinDeltas'];
		foreach($gpms as $key => $value) {
			$champions[$champ][$lane][$gpm_deltas[$key]]['stdev'] += pow($value-$champions[$champ][$lane][$gpm_deltas[$key]]['gold'],2);
		}
	}
	print("Cumulating stdev match " . $counter . "/" . $total . "\n");
	$counter++;
}
//calculate from totals
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{	
		if ($laneKey != 'name') {
			foreach($lane as $interval => $value) {
				if ($value['players'] > 0)
					$champions[$champKey][$laneKey][$interval]['stdev'] = pow($value['stdev'] / ($value['players']), 0.5);

			}
		}
		// $champions[$champKey][$laneKey]['stdev0']=pow($lane['stdev0']/$lane['players'],0.5);
		// $champions[$champKey][$laneKey]['stdev10']=pow($lane['stdev10']/$lane['players'],0.5);
		// $champions[$champKey][$laneKey]['stdev20']=pow($lane['stdev20']/$lane['players'],0.5);
		// $champions[$champKey][$laneKey]['stdev30']=pow($lane['stdev30']/$lane['players'],0.5);
	}
}

// print_r(json_encode($champions) . "\n");
$file = fopen('includes/static-data/average_gold.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>
