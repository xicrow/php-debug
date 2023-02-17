<?php
ini_set('error_reporting', E_ALL | E_STRICT);
ini_set('display_errors', 1);
ini_set('html_errors', 1);
ini_set('log_errors', 0);

require_once('../vendor/autoload.php');

use Xicrow\PhpDebug\Debugger;
use Xicrow\PhpDebug\Utility;

Debugger::$strDocumentRoot = dirname(__DIR__);
Debugger::$bShowCalledFrom = true;

if (!Utility::isCli()) {
	?>
	<!doctype html>
	<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Xicrow/Debug/Debugger</title>
	</head>

	<body>
	<?php
}

$fnClosure = function () {
	return true;
};
$oStdClass = (new stdClass());

function foo(): void
{
	Debugger::showTrace();
}

function bar(): void
{
	foo();
}

Debugger::compare([null, true, '123', 123, 123.45], 'Simple PHP types');
Debugger::debugMultiple([null, true, false, 123, 123.123, 'string'], 'Simple PHP type');
Debugger::debug([1, 2, 3]);
Debugger::debug($fnClosure);
Debugger::debug($oStdClass);
Debugger::debug(fopen('../README.md', 'r'));

Debugger::showTrace();
foo();
bar();

Debugger::debug(Debugger::getCalledFrom());
Debugger::debug(Debugger::getCalledFrom(1));

if (!Utility::isCli()) {
	?>
	</body>
	</html>
	<?php
}
