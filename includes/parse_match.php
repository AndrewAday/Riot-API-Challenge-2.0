<?php

function parseMatch($match, $MAJOR_ITEMS) {
	//Strip fields from participants
	$support_1;
	$support_2;
	$counter = 0;
	foreach($match['participants'] as $participant) {
		if ($participant['timeline']['role'] == 'DUO_SUPPORT' && $participant['teamId'] == 100)
			$support_1 = $participant['participantId'];
		if ($participant['timeline']['role'] == 'DUO_SUPPORT' && $participant['teamId'] == 200)
			$support_2 = $participant['participantId'];
		unset($match['participants'][$counter]['masteries']);
		unset($match['participants'][$counter]['runes']);
		foreach($participant['stats'] as $key => $value) {
			if($key != 'deaths' && $key != 'kills' && $key != 'assists' && $key != 'winner')
				unset($match['participants'][$counter]['stats'][$key]);
		}
		$counter++;
	}
	print($support_1 . "\n");
	print($support_2 . "\n");

	$supports = array($support_1, $support_2);

	unset($match['participantIdentities']);
	unset($match['teams']);
	//Strip events
	$frame_counter = 0;
	foreach($match['timeline']['frames'] as $frame) {
		
		unset($match['timeline']['frames'][$frame_counter]['participantFrames']);

		$counter = 0;
		if (isset($frame['events'])) {
			foreach($frame['events'] as $event) {
				if($event['eventType'] != 'ITEM_PURCHASED') {
					unset($match['timeline']['frames'][$frame_counter]['events'][$counter]);
				} else {
					if ($event['participantId'] != $support_1 && $event['participantId'] != $support_2 && !in_array($event['itemId'], $MAJOR_ITEMS['items']))
						unset($match['timeline']['frames'][$frame_counter]['events'][$counter]); //remove event if not a major purchase by non-support
				}

				$counter++;
			}
			$frame['events'] = array_values($frame['events']); //reset indices
		}
		$frame_counter++;
	}
	return $match;
}

?>
