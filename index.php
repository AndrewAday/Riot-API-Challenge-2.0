<?php
$champions = json_decode(file_get_contents('includes/static-data/champions.json'), true);
if(!isset($_GET['champion']))
{
	foreach($champNames as $value)
	{
		echo "<a href='./?champion=$value'>$value</a><br>";
	}
}
else
{
	$championsEcon = json_decode(file_get_contents('includes/static-data/champions.json'), true);
	$champion=$championsEcon[$champions['data'][$_GET['champion']]['key'];
	//show the builds
	//calculate econ
}
?>
