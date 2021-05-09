<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);

require_once('../vendor/autoload.php');

use Xicrow\PhpDebug\Debugger;
use Xicrow\PhpDebug\Timer;
use Xicrow\PhpDebug\Utility;

Debugger::$strDocumentRoot = dirname(__DIR__);
Debugger::$bShowCalledFrom = true;

Timer::$arrColorThreshold = [
	0     => '#56DB3A',
	500   => '#1299DA',
	5000  => '#FF8400',
	50000 => '#B729D9',
];

if (!Utility::isCli()) {
	?>
	<!doctype html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Timer</title>
	</head>

	<body>
	<?php
}
Timer::begin('Total');

// No name test
Timer::begin();
Timer::end();

// Loop test
for ($iLoopLevel1 = 1; $iLoopLevel1 <= 2; $iLoopLevel1++) {
	Timer::begin('Loop level 1');
	for ($iLoopLevel2 = 1; $iLoopLevel2 <= 2; $iLoopLevel2++) {
		Timer::begin('Loop level 2');
		for ($iLoopLevel3 = 1; $iLoopLevel3 <= 2; $iLoopLevel3++) {
			Timer::begin('Loop level 3');
			Timer::end();
		}
		Timer::end();
	}
	Timer::end();
}

// Callback test
Timer::callback('nonExistingFunction');
Timer::callback('time');
Timer::callback('strpos', ['Hello world', 'world']);
Timer::callback('array_sum', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
Timer::callback('array_rand', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
Timer::callback('min', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
Timer::callback('max', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
Timer::callback(['Xicrow\PhpDebug\Debugger', 'getDebugInformation'], [[1, 2, 3]]);
Timer::callback(function () {
	return false;
});

// Custom test
Timer::custom('0.5 seconds', time(), (time() + 0.5));
Timer::custom('5 seconds', time(), (time() + 5));
Timer::custom('50 seconds', time(), (time() + 50));
Timer::custom('5 minutes', time(), (time() + (5 * 60)));
Timer::custom('5 hours  ', time(), (time() + (5 * 60 * 60)));
Timer::custom('5 days   ', time(), (time() + (5 * 60 * 60 * 24)));
Timer::custom('5 weeks  ', time(), (time() + (5 * 60 * 60 * 24 * 7)));

// Show specific timer
Timer::show('50 seconds');

// Show all timers
Timer::showAll();

if (!Utility::isCli()) {
	?>
	</body>
	</html>
	<?php
}
