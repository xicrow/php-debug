<?php
require_once('../src/Timer.php');

use \Xicrow\DebugTools\Timer;
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/DebugTools/Timer</title>
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
		// Set project document root
		Timer::$documentRoot = 'E:\\GitHub\\';

		// Set default options
		Timer::$defaultOptions['getStats']['timestamp']      = false;
		Timer::$defaultOptions['getStats']['nested']         = true;
		Timer::$defaultOptions['getStats']['oneline']        = true;
		Timer::$defaultOptions['getStats']['oneline_length'] = 50;

		// Start "Total" timer
		Timer::start('Total');

		// No name test
		Timer::start();
		Timer::stop();

		// Default timers
		Timer::start('Sleep');
		sleep(1);
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
		Timer::callback(null, ['nonExistingClass', 'nonExistingMethod'], 'param1', 'param2');
		Timer::callback(null, function () {
			return 'closure';
		});

		// Custom timers
		Timer::custom('-5 minutes', strtotime('-5minutes'), time());
		Timer::custom('+5 minutes', time(), strtotime('+5minutes'));

		// Stop "Total" timer
		Timer::stop();

		// Show all timers
		Timer::showAll();

		echo '<hr />';
		echo '<pre>' . print_r(Timer::$timers, true) . '</pre>';
		?>
	</body>
</html>
