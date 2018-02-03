<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);

require_once('../src/autoload.php');

use \Xicrow\PhpDebug\Debugger;
use \Xicrow\PhpDebug\Timer;

Debugger::$documentRoot   = 'E:\\GitHub\\';
Debugger::$showCalledFrom = true;

Timer::$colorThreshold = [
    0     => '#56DB3A',
    500   => '#1299DA',
    5000  => '#FF8400',
    50000 => '#B729D9',
];
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
		Timer::callback(null, ['Xicrow\PhpDebug\Debugger', 'getDebugInformation'], [1, 2, 3]);
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
