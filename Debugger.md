# Debugger

## Example

See example in [demo/debugger.php](demo/debugger.php).

Example output:

```
php-debug/demo/debugger.php line 35
NULL

php-debug/demo/debugger.php line 36
TRUE

php-debug/demo/debugger.php line 37
FALSE

php-debug/demo/debugger.php line 38
"string"

php-debug/demo/debugger.php line 39
123

php-debug/demo/debugger.php line 40
123.123

php-debug/demo/debugger.php line 41
[
	0 => 1,
	1 => 2,
	2 => 3
]

php-debug/demo/debugger.php line 42
object(Closure) {

}

php-debug/demo/debugger.php line 43
object(stdClass) {

}

php-debug/demo/debugger.php line 44
Resource id #5 (stream)

php-debug/demo/debugger.php line 46
1: php-debug/demo/debugger.php line 46

php-debug/demo/debugger.php line 28
2: php-debug/demo/debugger.php line 28
1: php-debug/demo/debugger.php line 47

php-debug/demo/debugger.php line 28
3: php-debug/demo/debugger.php line 28
2: php-debug/demo/debugger.php line 32
1: php-debug/demo/debugger.php line 48

php-debug/demo/debugger.php line 50
"php-debug/demo/debugger.php line 50"

php-debug/demo/debugger.php line 51
"Unknown trace with index: 1"

php-debug/demo/debugger.php line 53
/**
 * Class Debugger
 *
 * @package Xicrow\PhpDebug
 */
class Xicrow\PhpDebug\Debugger{
	/**
	 * @var string|null
	 */
	public static $documentRoot = "E:\GitHub\";

	/**
	 * @var bool
	 */
	public static $showCalledFrom = TRUE;

	/**
	 * @var bool
	 */
	public static $output = TRUE;

	/**
	 * @var bool
	 */
	private static $outputStyles = FALSE;

	/**
	 * @param string $data
	 *
	 * @codeCoverageIgnore
	 */
	public static function output($data) {}

	/**
	 * @param mixed $data
	 *
	 * @codeCoverageIgnore
	 */
	public static function debug($data) {}

	/**
	 * @param bool $reverse
	 *
	 * @codeCoverageIgnore
	 */
	public static function showTrace($reverse = FALSE) {}

	/**
	 * @param string $class
	 * @param bool   $output
	 *
	 * @return string
	 */
	public static function reflectClass($class, $output = TRUE) {}

	/**
	 * @param string $class
	 * @param string $property
	 *
	 * @return string
	 */
	public static function reflectClassProperty($class, $property, $output = TRUE) {}

	/**
	 * @param string $class
	 * @param string $method
	 *
	 * @return string
	 */
	public static function reflectClassMethod($class, $method, $output = TRUE) {}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public static function getCalledFrom($index = 0) {}

	/**
	 * @param array $trace
	 *
	 * @return string
	 */
	public static function getCalledFromTrace($trace) {}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function getDebugInformation($data, $options = []) {}

	/**
	 * @return string
	 */
	private static function getDebugInformationNull() {}

	/**
	 * @param boolean $data
	 *
	 * @return string
	 */
	private static function getDebugInformationBoolean($data) {}

	/**
	 * @param integer $data
	 *
	 * @return string
	 */
	private static function getDebugInformationInteger($data) {}

	/**
	 * @param double $data
	 *
	 * @return string
	 */
	private static function getDebugInformationDouble($data) {}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	private static function getDebugInformationArray($data, $options = []) {}

	/**
	 * @param object $data
	 *
	 * @return string
	 */
	private static function getDebugInformationObject($data, $options = []) {}

	/**
	 * @param resource $data
	 *
	 * @return string
	 */
	private static function getDebugInformationResource($data) {}
}

php-debug/demo/debugger.php line 54

	/**
	 * @param bool $reverse
	 *
	 * @codeCoverageIgnore
	 */
	public static function showTrace($reverse = FALSE) {}
```
