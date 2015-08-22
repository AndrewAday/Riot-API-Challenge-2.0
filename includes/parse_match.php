<?php
function parseMatch($match) {
	//Strip fields from participants
	foreach($match['participants'] as $participant) {
		unset($participant['masteries']);
		unset($participant['runes']);
		foreach($participant['stats'] as $key => $value) {
			if($key != ('deaths' || 'kills' || 'assists' || 'winner'))
				unset($participant['stats'][$key]);
		}
	}
	unset($match['participantIdentities']);
	unset($match['teams']);
	//Strip events
	foreach($match['timeline']['frames'] as $frame) {
		
		unset($frame['participantFrames']);

		$counter = 0;
		if (isset($frame['events'])) {
			foreach($frame['events'] as $event) {
				if($event['eventType'] != 'ITEM_PURCHASED')
					unset($frame['events'][$counter]);
				$counter++;
			}
			$frame['events'] = array_values($frame['events']); //reset indices
		}
	}
}

?>
