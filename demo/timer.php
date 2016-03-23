<?php
require_once('bootstrap.php');

use \Xicrow\Debug\Timer;
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Timer</title>
	</head>

	<body>
		<?php
		Timer::start('Total');

		// No name test
		Timer::start();
		Timer::stop();

		// Loop test
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

		// Callback test
		Timer::callback(null, 'time');
		Timer::callback(null, 'strpos', 'Hello world', 'world');
		Timer::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'array_rand', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, ['Xicrow\Debug\Debugger', 'getDebugInformation'], [1, 2, 3]);
		Timer::callback(null, function () {
			return false;
		});

		// Custom test
		Timer::custom('5 seconds', time(), (time() + 5));
		Timer::custom('5 minutes', time(), (time() + (5 * 60)));
		Timer::custom('5 hours  ', time(), (time() + (5 * 60 * 60)));
		Timer::custom('5 days   ', time(), (time() + (5 * 60 * 60 * 24)));
		Timer::custom('5 weeks  ', time(), (time() + (5 * 60 * 60 * 24 * 7)));

		// Show all timers
		Timer::showAll();
		?>
	</body>
</html>
