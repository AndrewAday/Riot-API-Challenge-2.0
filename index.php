<?php
$champions = json_decode(file_get_contents('includes/static-data/champions.json'), true);
$champNames=[];
foreach($champions['data'] as $name=>$champion);
	$champNames[$champion['key']]=$name;
?>
