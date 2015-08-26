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
			$item=$participant['items'][0];
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
		if(count($participant['items'])>1)
		{
			$item2=$participant['items'][1];
			if(!array_key_exists($item2['itemId'], $champions[$champ][$lane][$item]))
			{
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['lowWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['lowLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['medWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['medLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['highWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']]['highLoss']=0.0;
			}

			//calculate z-score through econ, expected econ, and timestamp
			$zscore=0.0;
			if($item2['timestamp']<600000)
			{
				$expected=$champions[$champ][$lane]['gold0'];
				$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev0'];
			}
			else if($item2['timestamp']<1200000)
			{
				$expected=$champions[$champ][$lane]['gold10'];
				if($item2['timestamp']<780000)//3 minute offset
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*($item2['timestamp']-600000))/$item2['timestamp'];
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev10'];
			}
			else if($item2['timestamp']<1800000)
			{
				$expected=$champions[$champ][$lane]['gold20'];
				if($item2['timestamp']<1380000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*($item2['timestamp']-600000))/($item2['timestamp']-600000);
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev20'];
			}
			else
			{
				$expected=$champions[$champ][$lane]['gold30'];
				if($item2['timestamp']<1980000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['thirtyToEnd']*($item2['timestamp']-600000))/($item2['timestamp']-1200000);
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
			$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$econString]++;
		}
		if(count($participant['items'])==3)
		{
			$item3=$participant['items'][2];
			if(!array_key_exists($item3['itemId'], $champions[$champ][$lane][$item3))
			{
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['lowWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['lowLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['medWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['medLoss']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['highWin']=0.0;
				$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']]['highLoss']=0.0;
			}

			//calculate z-score through econ, expected econ, and timestamp
			$zscore=0.0;
			if($item3['timestamp']<600000)
			{
				$expected=$champions[$champ][$lane]['gold0'];
				$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev0'];
			}
			else if($item3['timestamp']<1200000)
			{
				$expected=$champions[$champ][$lane]['gold10'];
				if($item3['timestamp']<780000)//3 minute offset
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['zeroToTen']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*($item3['timestamp']-600000))/$item3['timestamp'];
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev10'];
			}
			else if($item3['timestamp']<1800000)
			{
				$expected=$champions[$champ][$lane]['gold20'];
				if($item3['timestamp']<1380000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['tenToTwenty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*($item3['timestamp']-600000))/($item3['timestamp']-600000);
				}
				else
					$econ=$participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty'];
				$zscore=($econ-$expected)/$champions[$champ][$lane]['stdev20'];
			}
			else
			{
				$expected=$champions[$champ][$lane]['gold30'];
				if($item3['timestamp']<1980000)
				{
					$econ=($participant['ParticipantTimeline']['goldPerMinDeltas']['twentyToThirty']*600000+$participant['ParticipantTimeline']['goldPerMinDeltas']['thirtyToEnd']*($item3['timestamp']-600000))/($item3['timestamp']-1200000);
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
			$champions[$champ][$lane][$item['itemId']][$item2['itemId']][$item3['itemId']][$econString]++;
		}
	}
}

//calculate win rates from totals
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{
		foreach($lane as $itemKey=>$item)//first item
		{
			if(is_numeric($itemKey))
			{
				if($item['medWin']+$item['medLoss']<100)//Might want to tune these cutoffs
				{
					unset($champions[$champ][$lane][$itemKey]);
					continue;
				}
				$champions[$champ][$lane][$itemKey]['lowWin']=$item['lowWin']/($item['lowWin']+$item['lowLoss']);
				$champions[$champ][$lane][$itemKey]['medWin']=$item['medWin']/($item['medWin']+$item['medLoss']);
				$champions[$champ][$lane][$itemKey]['highWin']=$item['highWin']/($item['highWin']+$item['highLoss']);
				unset($champions[$champ][$lane][$itemKey]['lowLoss']);
				unset($champions[$champ][$lane][$itemKey]['medLoss']);
				unset($champions[$champ][$lane][$itemKey]['highLoss']);

				foreach($item as $itemKey2=>$item2)//second item
				{
					if(is_numeric($itemKey2))
					{
						if($item2['medWin']+$item2['medLoss']<25)
						{
							unset($champions[$champ][$lane][$itemKey][$itemKey2]);
							continue;
						}
						$champions[$champ][$lane][$itemKey][$itemKey2]['lowWin']=$item2['lowWin']/($item2['lowWin']+$item2['lowLoss']);
						$champions[$champ][$lane][$itemKey][$itemKey2]['medWin']=$item2['medWin']/($item2['medWin']+$item2['medLoss']);
						$champions[$champ][$lane][$itemKey][$itemKey2]['highWin']=$item2['highWin']/($item2['highWin']+$item2['highLoss']);
						unset($champions[$champ][$lane][$itemKey][$itemKey2]['lowLoss']);
						unset($champions[$champ][$lane][$itemKey][$itemKey2]['medLoss']);
						unset($champions[$champ][$lane][$itemKey][$itemKey2]['highLoss']);

						foreach($item2 as $itemKey3=>$item3)//third item
						{
							if(is_numeric($itemKey3))
							{
								if($item3['medWin']+$item3['medLoss']<15)
								{
									unset($champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]);
									continue;
								}
								$champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['lowWin']=$item3['lowWin']/($item3['lowWin']+$item3['lowLoss']);
								$champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['medWin']=$item3['medWin']/($item3['medWin']+$item3['medLoss']);
								$champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['highWin']=$item3['highWin']/($item3['highWin']+$item3['highLoss']);
								unset($champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['lowLoss']);
								unset($champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['medLoss']);
								unset($champions[$champ][$lane][$itemKey][$itemKey2][$itemKey3]['highLoss']);
							}
						}
					}
				}
			}
		}
	}
}

$file = fopen('includes/static-data/build_win_rates.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>