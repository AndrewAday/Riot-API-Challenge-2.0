<?php
function erf($x)
{
    $pi = 3.1415926536;
    $a = (8*($pi - 3))/(3*$pi*(4 - $pi));
    $x2 = $x * $x;

    $ax2 = $a * $x2;
    $num = (4/$pi) + $ax2;
    $denom = 1 + $ax2;

    $inner = (-$x2)*$num/$denom;
    $erf2 = 1 - exp($inner);

    return sqrt($erf2);
}

function cdf($n)
{
         return (1 - erf($n / sqrt(2)))/2;
}

require_once('includes/api_key.php');
$champions = json_decode(file_get_contents('includes/static-data/average_gold.json'), true);

if(!(isset($_GET['n'])&&isset($_GET['c'])))
	echo 'error: set summoner name and champion';
else
{
	$api = new riotapi($na, $api_key);
	$summonerId = $api->getSummonerId($_GET['n']);
	$matches = $api->getMatchHistory($summonerId, $_GET['c']);

	$lanes=[];
	//Calculate by going through matches
	foreach($matches as $match)
	{
		foreach($match['participants'] as $participant)
		{
			if($participant['championId']!=$_GET['c'])
				continue;
			$lane=$participant['timeline']['lane'];
			if(!isset($lanes[$lane]))
			{
				$lane=[];
				$lane['counts']=[0,0,0,0];
				$lane['gpms']=[0.0,0.0,0.0,0.0];
			}
			=[0.0,0.0,0.0,0.0];=$participant['timeline']['goldPerMinDeltas'];
			if(isset($gpms['zeroToTen']))
			{
				$lane['counts'][0]++;
				$lane['gpms'][0]+=$gpms['zeroToTen'];
			}
			if(isset($gpms['tenToTwenty']))
			{
				$lane['counts'][1]++;
				$lane['gpms'][1]+=$gpms['tenToTwenty'];
			}
			if(isset($gpms['twentyToThirty']))
			{
				$lane['counts'][2]++;
				$lane['gpms'][2]+=$gpms['twentyToThirty'];
			}
			if(isset($gpms['thirtyToEnd']))
			{
				$lane['counts'][3]++;
				$lane['gpms'][3]+=$gpms['thirtyToEnd'];
			}
		}
	}
	for($i=0;$i<4;i++)
	{
		$lane['gpms'][$i]=$lane['gpms'][$i]/$lane['counts'][$i];
	}
	foreach($lanes as $laneId=>$lane)
	{
		if($lane['counts'][0]<5)
			$lanes[$laneId]['result']="not enough recent ranked games in ".strtolower($lane);
		else
		{
			for($i=0;$i<4;i++)
			{
				$expected=$champions[$_GET['c']][$laneId]["gold$i"]['gold'];
				$stdev=$champions[$_GET['c']][$laneId]["gold$i"]['stdev'];
				$cdfResult=cdf(($lane['gpms'][$i]-$expected)/$stdev)*100;
				if($i==3)
					$lanes[$laneId]['result'][$i]="you earn gold faster than {$cdfResult}% of players from 30 minutes on in ".strtolower($lane);
				else
					$lanes[$laneId]['result'][$i]="you earn gold faster than {$cdfResult}% of players from ".$i*10.' to '.($i*10+10).' minutes in '.strtolower($lane);
				unset($lanes[$laneId]['gpms']);
				unset($lanes[$laneId]['counts']);
			}
		}
	}
	echo json_encode($lanes);
}
?>