<?php
return [
	 "files" => ["1" => "cache",
				 "2" => "cobblestone",
				 "3" => "inferno",
				 "4" => "mirage",
				 "5" => "nuke",
				 "6" => "overpass",
				 "7" => "train",
		 		 "100" => "Jumbotron ON but 360 is OFF"
				],
	 "paths" => ["gamestatus" => storage_path('/esea/gamestatus.txt'),
	 			 "xml-vive" => storage_path('/esea/xml-vive/'),
	 			 "xml-mobile" => storage_path('/esea/xml-mobile/'),
	 			 "play360" => storage_path('/esea/play360.json'),
		         "live360" => storage_path('/esea/live360.json'),
		         "cloud360" => storage_path('/esea/cloud360.json'),
	 ],
	'casterModeIntervalTime' => 3*60*1000,
	'allowIpAddress' => [
		"127.0.0.0","127.0.0.1"
	],
];

?>
