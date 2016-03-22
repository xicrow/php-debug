# Timer

## Example
See example in `demo/timer.php`.

```PHP
<?php
require_once('../src/autoload.php');

use \Xicrow\Debug\Debugger;
use \Xicrow\Debug\Timer;

// Set debugger options
Debugger::$documentRoot   = 'E:\\GitHub\\';
Debugger::$showCalledFrom = false;

$scriptStart = microtime(true);

/**
 * Debugger utility functions
 */
function debug($data) {
	return Debugger::debug($data);
}

/**
 * Timer utility functions
 */
function timerStart($key = null) {
	return Timer::start($key);
}

function timerStop($key = null) {
	return Timer::stop($key);
}

function timerCustom($key = null, $start = null, $stop = null) {
	return Timer::custom($key, $start, $stop);
}

function timerCallback($key = null, $callback) {
	return Timer::callback($key, $callback);
}

function timerShow($key = null, $options = []) {
	return Timer::show($key, $options);
}

function timerShowAll($options = []) {
	return Timer::showAll($options);
}

?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Timer</title>
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
		// Only add "ALL" and "PHP boostrap" timer, if we have request time in float, otherwise it is unusable
		if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
			Timer::custom('ALL', $_SERVER['REQUEST_TIME_FLOAT']);
			Timer::custom('PHP bootstrap', $_SERVER['REQUEST_TIME_FLOAT'], $scriptStart);
		}
		Timer::custom('script', $scriptStart);

		// No name test
		Timer::start();
		Timer::stop();

		// Loop timers
		for ($i = 1; $i <= 2; $i++) {
			Timer::start('Loop level 1');
			for ($j = 1; $j <= 2; $j++) {
				Timer::start('Loop level 2');
				for ($k = 1; $k <= 2; $k++) {
					Timer::start('Loop level 3');
					Timer::stop();
				}
				Timer::stop();
			}
			Timer::stop();
		}

		// Callback timers
		Timer::callback(null, 'strpos', 'Hello world', 'world');
		Timer::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, ['Xicrow\Debug\Debugger', 'debug'], [1, 2, 3]);
		Timer::callback(null, function () {
			return true;
		});

		// Custom timers
		Timer::custom('-5 minutes', strtotime('-5minutes'), time());
		Timer::custom('+5 minutes', time(), strtotime('+5minutes'));

		// Show all timers
		Timer::showAll();
		?>
	</body>
</html>
```

Output
```
ALL                                            |             7.70 ms. | 2016-03-22 07:32:00.0130 | 2016-03-22 07:32:00.0207
|-- PHP bootstrap                              |             3.92 ms. | 2016-03-22 07:32:00.0130 | 2016-03-22 07:32:00.0169
|-- script                                     |             3.87 ms. | 2016-03-22 07:32:00.0169 | 2016-03-22 07:32:00.0208
|-- |-- debug/demo/timer.php line 71           |             0.06 ms. | 2016-03-22 07:32:00.0186 | 2016-03-22 07:32:00.0186
|-- |-- Loop level 1 #1                        |             0.50 ms. | 2016-03-22 07:32:00.0187 | 2016-03-22 07:32:00.0192
|-- |-- |-- Loop level 2 #1                    |             0.20 ms. | 2016-03-22 07:32:00.0187 | 2016-03-22 07:32:00.0189
|-- |-- |-- |-- Loop level 3 #1                |             0.04 ms. | 2016-03-22 07:32:00.0188 | 2016-03-22 07:32:00.0188
|-- |-- |-- |-- Loop level 3 #2                |             0.05 ms. | 2016-03-22 07:32:00.0188 | 2016-03-22 07:32:00.0189
|-- |-- |-- Loop level 2 #2                    |             0.20 ms. | 2016-03-22 07:32:00.0190 | 2016-03-22 07:32:00.0192
|-- |-- |-- |-- Loop level 3 #3                |             0.04 ms. | 2016-03-22 07:32:00.0190 | 2016-03-22 07:32:00.0190
|-- |-- |-- |-- Loop level 3 #4                |             0.04 ms. | 2016-03-22 07:32:00.0191 | 2016-03-22 07:32:00.0191
|-- |-- Loop level 1 #2                        |             0.55 ms. | 2016-03-22 07:32:00.0192 | 2016-03-22 07:32:00.0198
|-- |-- |-- Loop level 2 #3                    |             0.21 ms. | 2016-03-22 07:32:00.0193 | 2016-03-22 07:32:00.0195
|-- |-- |-- |-- Loop level 3 #5                |             0.05 ms. | 2016-03-22 07:32:00.0193 | 2016-03-22 07:32:00.0194
|-- |-- |-- |-- Loop level 3 #6                |             0.05 ms. | 2016-03-22 07:32:00.0194 | 2016-03-22 07:32:00.0194
|-- |-- |-- Loop level 2 #4                    |             0.22 ms. | 2016-03-22 07:32:00.0195 | 2016-03-22 07:32:00.0197
|-- |-- |-- |-- Loop level 3 #7                |             0.05 ms. | 2016-03-22 07:32:00.0196 | 2016-03-22 07:32:00.0196
|-- |-- |-- |-- Loop level 3 #8                |             0.05 ms. | 2016-03-22 07:32:00.0197 | 2016-03-22 07:32:00.0197
|-- |-- callback: strpos                       |             0.06 ms. | 2016-03-22 07:32:00.0198 | 2016-03-22 07:32:00.0199
|-- |-- callback: array_sum                    |             0.06 ms. | 2016-03-22 07:32:00.0199 | 2016-03-22 07:32:00.0200
|-- |-- callback: min                          |             0.06 ms. | 2016-03-22 07:32:00.0200 | 2016-03-22 07:32:00.0201
|-- |-- callback: max                          |             0.06 ms. | 2016-03-22 07:32:00.0201 | 2016-03-22 07:32:00.0202
|-- |-- callback: Xicrow\Debug\Debugger::debug |             0.09 ms. | 2016-03-22 07:32:00.0202 | 2016-03-22 07:32:00.0203
|-- |-- callback: closure                      |             0.06 ms. | 2016-03-22 07:32:00.0204 | 2016-03-22 07:32:00.0204
|-- |-- -5 minutes                             |       300,000.00 ms. | 2016-03-22 07:27:00.0000 | 2016-03-22 07:32:00.0000
|-- |-- +5 minutes                             |       300,000.00 ms. | 2016-03-22 07:32:00.0000 | 2016-03-22 07:37:00.0000
```
