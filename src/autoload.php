<?php
spl_autoload_register(function ($class) {
    static $classes = null;
    if ($classes === null) {
        $classes = [
            'Xicrow\\PhpDebug\\Debugger' => '/Debugger.php',
            'Xicrow\\PhpDebug\\Timer'    => '/Timer.php',
        ];
    }
    if (isset($classes[$class])) {
        require __DIR__ . $classes[$class];
    }
}, true, false);
