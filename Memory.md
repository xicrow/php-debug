# Memory

## Example
See example in `demo/memory.php`.

```PHP
<?php
require_once('../src/autoload.php');

use \Xicrow\Debug\Debugger;
use \Xicrow\Debug\Memory;

// Set debugger options
Debugger::$documentRoot   = 'E:\\GitHub\\';
Debugger::$showCalledFrom = false;

/**
 * Debugger utility functions
 */
function debug($data) {
	return Debugger::debug($data);
}

/**
 * Memory utility functions
 */
function memoryStart($key = null) {
	return Memory::start($key);
}

function memoryStop($key = null) {
	return Memory::stop($key);
}

function memoryCustom($key = null, $start = null, $stop = null) {
	return Memory::custom($key, $start, $stop);
}

function memoryCallback($key = null, $callback) {
	return Memory::callback($key, $callback);
}

function memoryShow($key = null, $options = []) {
	return Memory::show($key, $options);
}

function memoryShowAll($options = []) {
	return Memory::showAll($options);
}

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
```

Output
```
Total                                      |         579,496.00 B
|-- Data generation: 0.5 MB                |         528,696.00 B
|-- debug/demo/memory.php line 75          |             816.00 B
|-- Loop level 1 #1                        |           7,000.00 B
|-- |-- Loop level 2 #1                    |           2,864.00 B
|-- |-- |-- Loop level 3 #1                |             768.00 B
|-- |-- |-- Loop level 3 #2                |             768.00 B
|-- |-- Loop level 2 #2                    |           2,816.00 B
|-- |-- |-- Loop level 3 #3                |             800.00 B
|-- |-- |-- Loop level 3 #4                |             768.00 B
|-- Loop level 1 #2                        |           6,872.00 B
|-- |-- Loop level 2 #3                    |           2,784.00 B
|-- |-- |-- Loop level 3 #5                |             768.00 B
|-- |-- |-- Loop level 3 #6                |             768.00 B
|-- |-- Loop level 2 #4                    |           2,848.00 B
|-- |-- |-- Loop level 3 #7                |             768.00 B
|-- |-- |-- Loop level 3 #8                |             832.00 B
|-- callback: strpos                       |             912.00 B
|-- callback: array_sum                    |           1,400.00 B
|-- callback: array_rand                   |           1,408.00 B
|-- callback: min                          |           1,392.00 B
|-- callback: max                          |           1,392.00 B
|-- callback: Xicrow\Debug\Debugger::debug |           1,384.00 B
|-- callback: closure                      |             760.00 B
|-- 5 KB                                   |           5,120.00 B
|-- 5 MB                                   |       5,242,880.00 B
|-- 5 GB                                   |   5,368,709,120.00 B
```
