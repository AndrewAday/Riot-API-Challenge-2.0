<?php
$champions = json_decode(file_get_contents('includes/static-data/champions.json'), true);
$champNames=[];
foreach($champions['data'] as $name=>$champion);
	$champNames[$champion['key']]=$name;
if(!isset($_GET['champion']))
{
	foreach($champNames as $value)
	{
		echo "<a href='./?champion=$value'>$value</a><br>";
	}
}
else
{
	//show the builds
	//calculate econ
}
?>
