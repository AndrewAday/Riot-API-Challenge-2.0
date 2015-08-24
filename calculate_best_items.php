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

		if(abs(($participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen']-$champions[$champ][$lane]['gold0'])/$champions[$champ][$lane]['stdev0'])>2.575)
			continue; //cut top/bottom 0.5% of economy. Perhaps adjust in the future, but 0.5% is approx number of leavers. Maybe don't cut the top end.

		if(count($participant['items'])>0)
		{
			$item=$participant['items'][0]
			if(!array_key_exists($item['itemId'], $champions[$champ][$lane]))
			{
				$champions[$champ][$lane][$item['itemId']]['lowWin']=0.0;
				$champions[$champ][$lane][$item['itemId']]['lowLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']]['medWin']=0.0;
				$champions[$champ][$lane][$item['itemId']]['medLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']]['highWin']=0.0;
				$champions[$champ][$lane][$item['itemId']]['highLoss']=0.0;
			}

			//calculate z-score through econ, expected econ, and timestamp
			$zscore=0.0;
			if($item['timestamp']<600000)
			{
				$expected=$champions[$champ][$lane]['gold0'];
				$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev0'];
			}
			else if($item['timestamp']<1200000)
			{
				$expected=$champions[$champ][$lane]['gold10'];
				if($item['timestamp']<780000)//3 minute offset
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*($item['timestamp']-600000))/$item['timestamp'];
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev10'];
			}
			else if($item['timestamp']<1800000)
			{
				$expected=$champions[$champ][$lane]['gold20'];
				if($item['timestamp']<1380000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*($item['timestamp']-600000))/($item['timestamp']-600000);
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev20'];
			}
			else
			{
				$expected=$champions[$champ][$lane]['gold30'];
				if($item['timestamp']<1980000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['thirtyToEnd']*($item['timestamp']-600000))/($item['timestamp']-1200000);
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['thirtyToEnd'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev30'];
			}

			//calculate low/med/high econ. There's only 99% total since I cut 1%
			if($zscore<-0.44) //low econ, bottom 33%
			{
				$econString='low';
			}
			else if($zscore<0.44) //med econ, middle 33%
			{
				$econString='med';
			}
			else //high econ, top 33%
			{
				$econString='high';
			}
			$econString.=$win?'Win':'Loss'; //add win or loss
			$champions[$champ][$lane][$item['itemId']][$econString]++;
		}
		/*if(count($participant['items'])>1)
		{
			//second item
		}
		if(count($participant['items'])==3)
		{
			//third item
		}
		//do calculations*/
	}
}

/*//calculate win rates from totals
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{
		
	}
}*/

$file = fopen('includes/static-data/best_builds.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>