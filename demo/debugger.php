<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);

require_once('../src/autoload.php');

use \Xicrow\PhpDebug\Debugger;

Debugger::$documentRoot   = 'E:\\GitHub\\';
Debugger::$showCalledFrom = true;
?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Debugger</title>
	</head>

	<body>
        <?php
        $closure  = function () {
            return true;
        };
        $stdClass = (new stdClass());

        function foo()
        {
            Debugger::showTrace();
        }

        function bar()
        {
            foo();
        }

        Debugger::debug(null);
        Debugger::debug(true);
        Debugger::debug(false);
        Debugger::debug('string');
        Debugger::debug(123);
        Debugger::debug(123.123);
        Debugger::debug([1, 2, 3]);
        Debugger::debug($closure);
        Debugger::debug($stdClass);
        Debugger::debug(fopen('../README.md', 'r'));

        Debugger::showTrace();
        foo();
        bar();

        Debugger::debug(Debugger::getCalledFrom());
        Debugger::debug(Debugger::getCalledFrom(1));

        Debugger::reflectClass('\Xicrow\PhpDebug\Debugger');
        Debugger::reflectClassMethod('\Xicrow\PhpDebug\Debugger', 'showTrace');
        ?>
	</body>
</html>
