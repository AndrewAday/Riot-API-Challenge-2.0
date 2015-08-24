<?php

$champions_json = file_get_contents(dirname(__FILE__) . '/static-data/champions_by_id.json');
$CHAMPIONS = json_decode($champions_json, true);


/* Schema
"32":{
	"version":"5.2.1",
	"id":"Amumu",
	"key":"32",
	"name":"Amumu",
	"title":"the Sad Mummy",
	"blurb":"Amumu is a diminutive, animated cadaver who wanders the world, trying to discover his true identity. He rose from an ancient Shuriman tomb bound in corpse wrappings with no knowledge of his past, consumed with an uncontrollable sadness.",
	"info":{
		"attack":2,
		"defense":6,
		"magic":8,
		"difficulty":3
	},
	"image":{
		"full":"Amumu.png",
		"sprite":"champion0.png",
		"group":"champion",
		"x":192,
		"y":0,
		"w":48,
		"h":48
	},
	"tags":[
		"Tank",
		"Mage"
	],
	"partype":"Mana",
	"stats":{
		"hp":613.12,
		"hpperlevel":84,
		"mp":287.2,
		"mpperlevel":40,
		"movespeed":335,
		"armor":23.544,
		"armorperlevel":3.3,
		"spellblock":32.1,
		"spellblockperlevel":1.25,
		"attackrange":125,
		"hpregen":8.875,
		"hpregenperlevel":0.85,
		"mpregen":7.38,
		"mpregenperlevel":0.525,
		"crit":0,
		"critperlevel":0,
		"attackdamage":53.384,
		"attackdamageperlevel":3.8,
		"attackspeedoffset":-0.02,
		"attackspeedperlevel":2.18
	}
},
*/
?>