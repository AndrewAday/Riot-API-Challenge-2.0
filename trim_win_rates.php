<?php
foreach($champions as $champKey=>$champion)
{
	foreach($champion as $laneKey=>$lane)
	{
		$lowMax=0.0;
		$lowMaxId=0;
		$medMax=0.0;
		$medMaxId=0;
		$highMax=0.0;
		$highMaxId=0;
		foreach($lane as $itemKey=>$item)//first item
		{
			if(is_numeric($itemKey))
			{
				if($item['lowWin']>$lowMax)
				{
					$lowMax=$item['lowWin'];
					$lowMaxId=$itemKey;
				}
				if($item['medWin']>$medMax)
				{
					$medMax=$item['medWin'];
					$medMaxId=$itemKey;
				}
				if($item['highWin']>$highMax)
				{
					$highMax=$item['highWin'];
					$highMaxId=$itemKey;
				}
			}
		}
		foreach($lane as $itemKey=>$item)
		{
			if(is_numeric($itemKey))
			{
				if($itemKey!=$lowMaxId&&$itemKey!=$medMaxId&&$itemKey!=$highMaxId)
				{
					unset($champions[$champKey][$laneKey][$itemKey]);
					continue;
				}
				if($itemKey!=$lowMaxId)
					unset($champions[$champKey][$laneKey][$itemKey]['lowWin']);
				if($itemKey!=$medMaxId)
					unset($champions[$champKey][$laneKey][$itemKey]['medWin']);
				if($itemKey!=$highMaxId)
					unset($champions[$champKey][$laneKey][$itemKey]['highWin']);
			}
		}
		foreach($lane as $itemKey=>$item)
		{
			$lowMax=0.0;
			$lowMaxId=0;
			$medMax=0.0;
			$medMaxId=0;
			$highMax=0.0;
			$highMaxId=0;
			foreach($item as $itemKey2=>$item2)
			{
				if(is_numeric($itemKey2))
				{
					if($item2['lowWin']>$lowMax)
					{
						$lowMax=$item2['lowWin'];
						$lowMaxId=$itemKey2;
					}
					if($item2['medWin']>$medMax)
					{
						$medMax=$item2['medWin'];
						$medMaxId=$itemKey2;
					}
					if($item2['highWin']>$highMax)
					{
						$highMax=$item2['highWin'];
						$highMaxId=$itemKey2;
					}
				}
			}
			foreach($item as $itemKey2=>$item2)
			{
				if(is_numeric($itemKey2))
				{
					if($itemKey2!=$lowMaxId&&$itemKey2!=$medMaxId&&$itemKey2!=$highMaxId)
					{
						unset($champions[$champKey][$laneKey][$itemKey][$itemKey2]);
						continue;
					}
					if($itemKey2!=$lowMaxId)
						unset($champions[$champKey][$laneKey][$itemKey][$itemKey2]['lowWin']);
					if($itemKey2!=$medMaxId)
						unset($champions[$champKey][$laneKey][$itemKey][$itemKey2]['medWin']);
					if($itemKey2!=$highMaxId)
						unset($champions[$champKey][$laneKey][$itemKey][$itemKey2]['highWin']);
				}
			}
		}
		foreach($lane as $itemKey=>$item)
		{
			foreach($item as $itemKey2=>$item2)
			{
				$lowMax=0.0;
				$lowMaxId=0;
				$medMax=0.0;
				$medMaxId=0;
				$highMax=0.0;
				$highMaxId=0;
				foreach($item2 as $itemKey3=>$item3)
				{	
					if(is_numeric($itemKey3))
					{
						if($item3['lowWin']>$lowMax)
						{
							$lowMax=$item3['lowWin'];
							$lowMaxId=$itemKey3;
						}
						if($item3['medWin']>$medMax)
						{
							$medMax=$item3['medWin'];
							$medMaxId=$itemKey3;
						}
						if($item3['highWin']>$highMax)
						{
							$highMax=$item3['highWin'];
							$highMaxId=$itemKey3;
						}
					}
				foreach($item2 as $itemKey3=>$item3)
				{	
					if(is_numeric($itemKey3))
					{
						if($itemKey3!=$lowMaxId&&$itemKey3!=$medMaxId&&$itemKey3!=$highMaxId)
						{
							unset($champions[$champKey][$laneKey][$itemKey][$itemKey2][$itemKey3]);
							continue;
						}
						if($itemKey3!=$lowMaxId)
							unset($champions[$champKey][$laneKey][$itemKey][$itemKey2][$itemKey3]['lowWin']);
						if($itemKey3!=$medMaxId)
							unset($champions[$champKey][$laneKey][$itemKey][$itemKey2][$itemKey3]['medWin']);
						if($itemKey3!=$highMaxId)
							unset($champions[$champKey][$laneKey][$itemKey][$itemKey2][$itemKey3]['highWin']);
					}
				}
			}
		}
	}
}

//store builds
$file = fopen('includes/static-data/top_builds.json', 'w');
fwrite($file, json_encode($champions));
fclose($file);
?>