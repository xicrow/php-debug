<?php
require_once('bootstrap.php');

use \Xicrow\Debug\Memory;
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Memory</title>
	</head>

	<body>
		<?php
		Memory::start('Total');

		// No name test
		Memory::start();
		Memory::stop();

		// Loop test
		for ($i = 1; $i <= 2; $i++) {
			Memory::start('Loop level 1');
			for ($j = 1; $j <= 2; $j++) {
				Memory::start('Loop level 2');
				for ($k = 1; $k <= 2; $k++) {
					Memory::start('Loop level 3');
					Memory::stop();
				}
				Memory::stop();
			}
			Memory::stop();
		}

		// Callback test
		Memory::callback(null, 'time');
		Memory::callback(null, 'strpos', 'Hello world', 'world');
		Memory::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'array_rand', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, ['Xicrow\Debug\Debugger', 'getDebugInformation'], [1, 2, 3]);
		Memory::callback(null, function () {
			return false;
		});

		// Custom test
		Memory::custom('5 B ', 0, 5);
		Memory::custom('5 KB', 0, (5 * 1024));
		Memory::custom('5 MB', 0, (5 * 1024 * 1024));
		Memory::custom('5 GB', 0, (5 * 1024 * 1024 * 1024));
		Memory::custom('5 TB', 0, (5 * 1024 * 1024 * 1024 * 1024));

		// Random test
		$dataSizeMb = (rand(20, 80) / 100);
		$dataSizeB  = ($dataSizeMb * 1024 * 1024);
		Memory::start('Random data generation: ' . $dataSizeMb . ' MB');
		$data = '';
		for ($i = 0; $i <= $dataSizeB; $i++) {
			$data .= ' ';
		}
		Memory::stop();

		// Show all memories
		Memory::showAll();
		?>
	</body>
</html>
