<?php
require_once('includes/db_connect.php');

$champions=[];

$cursor = $matches->find();

//Average gold
foreach($cursor as $match) {
	foreach($match['participants'] as $participant) {
		$champ=$participant['championid'];
		$lane=$participant['ParticipantTimeline']['lane'];
		if(!array_key_exists($participant['championid'], $champions))
		{
			$addChamp=[];
			$champions[$champ]=$addChamp;
		}
		if(!array_key_exists($lane,$champions[$champ]))
		{
			$addLane=[];
			$addLane['gold0']=0.0;
			$addLane['gold10']=0.0;
			$addLane['gold20']=0.0;
			$addLane['gold30']=0.0;
			$addLane['players']=0.0;
			$champions[$champ][$lane]=$addLane;
		}
		$gpms=$participant['ParticipantTimeline']['goldPerMinDeltas'];
		$champions[$champ][$lane]['gold0']+=$gpms['zeroToTen'];
		$champions[$champ][$lane]['gold10']+=$gpms['tenToTwenty'];
		$champions[$champ][$lane]['gold20']+=$gpms['twentyToThirty'];
		$champions[$champ][$lane]['gold30']+=$gpms['thirtyToEnd'];
		$champions[$champ][$lane]['players']++;
	}
}
//average
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{
		$champions[$champKey][$laneKey]['gold0']=$lane['gold0']/$lane['players'];
		$champions[$champKey][$laneKey]['gold10']=$lane['gold10']/$lane['players'];
		$champions[$champKey][$laneKey]['gold20']=$lane['gold20']/$lane['players'];
		$champions[$champKey][$laneKey]['gold30']=$lane['gold30']/$lane['players'];
	}
}

//stdev
foreach($cursor as $match) {
	foreach($match['participants'] as $participant) {
		$champ=$participant['championid'];
		$lane=$participant['ParticipantTimeline']['lane'];
		if(!array_key_exists($lane,$champions[$champ]['stdev0']))
		{
			$champions[$champ][$lane]['stdev0']=0.0;
			$champions[$champ][$lane]['stdev10']=0.0;
			$champions[$champ][$lane]['stdev20']=0.0;
			$champions[$champ][$lane]['stdev30']=0.0;
		}
		$gpms=$participant['ParticipantTimeline']['goldPerMinDeltas'];
		$champions[$champ][$lane]['stdev0']+=pow($gpms['zeroToTen']-$champions[$champKey][$laneKey]['gold0'],2);
		$champions[$champ][$lane]['stdev10']+=pow($gpms['tenToTwenty']-$champions[$champKey][$laneKey]['gold10'],2);
		$champions[$champ][$lane]['stdev20']+=pow($gpms['twentyToThirty']-$champions[$champKey][$laneKey]['gold20'],2);
		$champions[$champ][$lane]['stdev30']+=pow($gpms['thirtyToEnd']-$champions[$champKey][$laneKey]['gold30'],2);
	}
}
//calculate from totals
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{
		$champions[$champKey][$laneKey]['stdev0']=pow($lane['stdev0']/$lane['players'],0.5);
		$champions[$champKey][$laneKey]['stdev10']=pow($lane['stdev10']/$lane['players'],0.5);
		$champions[$champKey][$laneKey]['stdev20']=pow($lane['stdev20']/$lane['players'],0.5);
		$champions[$champKey][$laneKey]['stdev30']=pow($lane['stdev30']/$lane['players'],0.5);
	}
}


$file = fopen('includes/static-data/average_gold.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>