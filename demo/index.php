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
		require_once('../src/Timer.php');

		use \Xicrow\DebugTools\Timer;

		#Timer::$documentRoot = 'Set project root';

		Timer::$defaultOptions['getStats']['nested']         = true;
		Timer::$defaultOptions['getStats']['oneline']        = true;
		Timer::$defaultOptions['getStats']['oneline_length'] = 50;

		// Start "Total" timer
		Timer::start('Total');

		// Sleep test
		Timer::start('Sleep');
		sleep(1);
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

		// No name test
		Timer::start();
		Timer::stop();

		// Test callbacks
		class TimerTest {
			public function objectOriented() {
				return 'objectOriented';
			}

			public static function objectOrientedStatic() {
				return 'objectOrientedStatic';
			}
		}

		$timerTest = new TimerTest();

		Timer::callback(null, 'strpos', 'Hello world', 'world');
		Timer::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		Timer::callback(null, [$timerTest, 'objectOriented']);
		Timer::callback(null, ['TimerTest', 'objectOrientedStatic']);
		#Timer::callback(null, ['TimerTest', 'nonExistingMethod']);
		#Timer::callback(null, ['nonExistingClass', 'nonExistingMethod']);
		Timer::callback(null, function () {
			return 'closure';
		});

		// Stop "Total" timer
		Timer::stop();

		// Add custom timers
		Timer::add('-5 minutes', strtotime('-5minutes'), time());
		Timer::add('+5 minutes', time(), strtotime('+5minutes'));

		// Show all timers
		Timer::showAll();

		#echo '<pre>' . print_r(Timer::getTimers(), true) . '</pre>';
		?>
	</body>
</html>
