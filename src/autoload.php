<?php
spl_autoload_register(function ($class) {
	static $classes = null;
	if ($classes === null) {
		$classes = [
			'Xicrow\\Debug\\Collection' => '/Collection.php',
			'Xicrow\\Debug\\Debugger'   => '/Debugger.php',
			'Xicrow\\Debug\\Memory'     => '/Memory.php',
			'Xicrow\\Debug\\Profiler'   => '/Profiler.php',
			'Xicrow\\Debug\\Timer'      => '/Timer.php'
		];
	}
	if (isset($classes[$class])) {
		require __DIR__ . $classes[$class];
	}
}, true, false);
