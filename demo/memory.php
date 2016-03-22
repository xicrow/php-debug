<?php
require_once('../src/bootstrap.php');

use \Xicrow\Debug\Memory;
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Memory</title>
		<style type="text/css">
			pre {
				margin: 5px;
				padding: 0;
				font-family: Consolas, Courier, monospace;
			}
		</style>
	</head>

	<body>
		<?php
		Memory::start('Total');

		$dataSizeMb = (rand(20, 80) / 100);
		$dataSizeKb = ($dataSizeMb * 1024);
		$dataSizeB  = ($dataSizeKb * 1024);
		Memory::start('Data generation: ' . $dataSizeMb . ' MB');
		$characters = str_split('abcdefghijklmnopqrstuvwxyzæøå');
		$data       = '';
		for ($i = 0; $i <= $dataSizeB; $i++) {
			$data .= $characters[array_rand($characters)];
		}
		Memory::stop();

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
		Memory::callback(null, 'strpos', 'Hello world', 'world');
		Memory::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'array_rand', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Memory::callback(null, ['Xicrow\Debug\Debugger', 'debug'], [1, 2, 3]);
		Memory::callback(null, function () {
			return true;
		});

		// Custom test
		Memory::custom('5 KB', 0, (5 * 1024));
		Memory::custom('5 MB', 0, (5 * 1024 * 1024));
		Memory::custom('5 GB', 0, (5 * 1024 * 1024 * 1024));

		// Show all memories
		Memory::showAll();
		?>
	</body>
</html>
