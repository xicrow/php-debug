<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);

require('../vendor/autoload.php');
require_once('../src/autoload.php');

use \Xicrow\PhpDebug\Debugger;

?>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Debugger</title>
	</head>

	<body>
        <?php
        Debugger::debug(null);
        Debugger::debug(true);
        Debugger::debug(false);
        Debugger::debug('string');
        Debugger::debug(123);
        Debugger::debug([1, 2, 3]);
        Debugger::debug(function () {
            return true;
        });
        Debugger::debug(new DateTime());
        ?>
	</body>
</html>
