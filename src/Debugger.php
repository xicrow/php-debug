<?php
namespace Xicrow\Debug;

/**
 * Class Debugger
 *
 * @package Xicrow\Debug
 */
class Debugger {
	/**
	 * @var bool
	 */
	public static $output = true;

	/**
	 * @var bool
	 */
	public static $showCalledFrom = true;

	/**
	 * @var string|null
	 */
	public static $documentRoot = null;

	/**
	 * @param string $data
	 *
	 * @codeCoverageIgnore
	 */
	public static function output($data) {
		if (!self::$output || !is_string($data)) {
			return;
		}

		if (php_sapi_name() == 'cli') {
			$data = (self::$showCalledFrom ? self::getCalledFrom(2) . "\n" . $data : $data);

			echo $data;
		} else {
			$data = (self::$showCalledFrom ? '<strong>' . self::getCalledFrom(2) . '</strong>' . "\n" . $data : $data);

			$style   = [];
			$style[] = 'margin:5px 0;';
			$style[] = 'padding:5px 10px;';
			$style[] = 'font-family:Consolas,​Courier,​monospace;';
			$style[] = 'font-weight:normal;';
			$style[] = 'font-size:15px;';
			$style[] = 'line-height:1.3;';
			$style[] = 'color:#555;';
			$style[] = 'background:#F9F9F9;';
			$style[] = 'border:1px solid #CCC;';
			$style[] = 'display:block;';

			echo '<pre style="' . implode(' ', $style) . '">';
			echo $data;
			echo '</pre>';
		}
	}

	/**
	 * @param mixed $data
	 *
	 * @codeCoverageIgnore
	 */
	public static function debug($data) {
		self::output(self::getDebugInformation($data));
	}

	/**
	 * @param bool $reverse
	 *
	 * @codeCoverageIgnore
	 */
	public static function showTrace($reverse = false) {
		$backtrace = ($reverse ? array_reverse(debug_backtrace()) : debug_backtrace());

		$output     = '';
		$traceIndex = ($reverse ? 1 : count($backtrace));
		foreach ($backtrace as $trace) {
			$output .= $traceIndex . ': ';
			$output .= self::getCalledFromTrace($trace);
			$output .= "\n";

			$traceIndex += ($reverse ? 1 : -1);
		}

		self::output($output);
	}

	/**
	 * @param string $class
	 * @param bool   $output
	 *
	 * @return string
	 */
	public static function reflectClass($class, $output = true) {
		$data = '';

		$reflectionClass = new \ReflectionClass($class);

		$comment = $reflectionClass->getDocComment();
		if (!empty($comment)) {
			$data .= $comment;
			$data .= "\n";
		}

		$data .= 'class ' . $reflectionClass->name . '{';
		$firstElement = true;
		foreach ($reflectionClass->getProperties() as $reflectionProperty) {
			if (!$firstElement) {
				$data .= "\n";
			}
			$firstElement = false;

			$data .= self::reflectClassProperty($class, $reflectionProperty->name, false);
		}

		foreach ($reflectionClass->getMethods() as $reflectionMethod) {
			if (!$firstElement) {
				$data .= "\n";
			}
			$firstElement = false;

			$data .= self::reflectClassMethod($class, $reflectionMethod->name, false);
		}
		$data .= "\n";
		$data .= '}';

		if ($output) {
			self::output($data);
		}

		return $data;
	}

	/**
	 * @param string $class
	 * @param string $property
	 *
	 * @return string
	 */
	public static function reflectClassProperty($class, $property, $output = true) {
		$data = '';

		$reflectionProperty = new \ReflectionProperty($class, $property);

		$comment = $reflectionProperty->getDocComment();
		if (!empty($comment)) {
			$data .= "\n";
			$data .= "\t";
			$data .= $comment;
		}

		$data .= "\n";
		$data .= "\t";
		$data .= ($reflectionProperty->isPublic() ? 'public ' : '');
		$data .= ($reflectionProperty->isPrivate() ? 'private ' : '');
		$data .= ($reflectionProperty->isProtected() ? 'protected ' : '');
		$data .= ($reflectionProperty->isStatic() ? 'static ' : '');
		$data .= '$' . $reflectionProperty->name . ';';

		if ($output) {
			self::output($data);
		}

		return $data;
	}

	/**
	 * @param string $class
	 * @param string $method
	 *
	 * @return string
	 */
	public static function reflectClassMethod($class, $method, $output = true) {
		$data = '';

		$reflectionMethod = new \ReflectionMethod($class, $method);

		$comment = $reflectionMethod->getDocComment();
		if (!empty($comment)) {
			$data .= "\n";
			$data .= "\t";
			$data .= $comment;
		}

		$data .= "\n";
		$data .= "\t";
		$data .= ($reflectionMethod->isPublic() ? 'public ' : '');
		$data .= ($reflectionMethod->isPrivate() ? 'private ' : '');
		$data .= ($reflectionMethod->isProtected() ? 'protected ' : '');
		$data .= ($reflectionMethod->isStatic() ? 'static ' : '');
		$data .= 'function ' . $reflectionMethod->name . '(';
		if ($reflectionMethod->getNumberOfParameters()) {
			foreach ($reflectionMethod->getParameters() as $reflectionMethodParameterIndex => $reflectionMethodParameter) {
				$data .= ($reflectionMethodParameterIndex > 0 ? ', ' : '');
				$data .= '$' . $reflectionMethodParameter->name;
				if ($reflectionMethodParameter->isDefaultValueAvailable()) {
					$defaultValue = self::getDebugInformation($reflectionMethodParameter->getDefaultValue());
					$defaultValue = str_replace(["\n", "\t"], '', $defaultValue);
					$data .= ' = ' . $defaultValue;
				}
			}
		}
		$data .= ') {}';

		if ($output) {
			self::output($data);
		}

		return $data;
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public static function getCalledFrom($index = 0) {
		$backtrace = debug_backtrace();

		if (!isset($backtrace[$index])) {
			return 'Unknown trace with index: ' . $index;
		}

		return self::getCalledFromTrace($backtrace[$index]);
	}

	/**
	 * @param array $trace
	 *
	 * @return string
	 */
	public static function getCalledFromTrace($trace) {
		// Get file and line number
		$calledFromFile = '';
		if (isset($trace['file'])) {
			$calledFromFile .= $trace['file'] . ' line ' . $trace['line'];
			$calledFromFile = str_replace('\\', '/', $calledFromFile);
			$calledFromFile = (!empty(self::$documentRoot) ? substr($calledFromFile, strlen(self::$documentRoot)) : $calledFromFile);
			$calledFromFile = trim($calledFromFile, '/');
		}

		// Get function call
		$calledFromFunction = '';
		if (isset($trace['function'])) {
			$calledFromFunction .= (isset($trace['class']) ? $trace['class'] : '');
			$calledFromFunction .= (isset($trace['type']) ? $trace['type'] : '');
			$calledFromFunction .= $trace['function'] . '()';
		}

		if (!empty($calledFromFunction) && !empty($calledFromFile)) {
			$calledFromFunction = ' > ' . $calledFromFunction;
		}

		return $calledFromFile . $calledFromFunction;
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function getDebugInformation($data, $indent = false) {
		$dataType = gettype($data);

		$methodName = 'getDebugInformation' . ucfirst(strtolower($dataType));

		$result = 'No method found supporting data type: ' . $dataType;
		if ($dataType == 'string') {
			$result = (string) '"' . $data . '"';
		} elseif (method_exists('\Xicrow\Debug\Debugger', $methodName)) {
			$result = (string) self::$methodName($data);
		}

		if ($indent > 0) {
			$result = str_replace("\n", "\n\t", $result);
		}

		return $result;
	}

	/**
	 * @return string
	 */
	private static function getDebugInformationNull() {
		return 'NULL';
	}

	/**
	 * @param boolean $data
	 *
	 * @return string
	 */
	private static function getDebugInformationBoolean($data) {
		return ($data ? 'TRUE' : 'FALSE');
	}

	/**
	 * @param integer $data
	 *
	 * @return string
	 */
	private static function getDebugInformationInteger($data) {
		return (string) $data;
	}

	/**
	 * @param double $data
	 *
	 * @return string
	 */
	private static function getDebugInformationDouble($data) {
		return (string) $data;
	}

	/**
	 * @param array|object $data
	 * @param string       $prefix
	 * @param string       $suffix
	 *
	 * @return string
	 */
	private static function getDebugInformationIterable($data, $prefix = '', $suffix = '') {
		$debugInfo = '';
		$debugInfo .= $prefix;

		$keys = [];

		$i = 0;
		foreach ($data as $k => $v) {
			$key   = self::getDebugInformation($k);
			$value = self::getDebugInformation($v, true);

			$keys[] = $key;

			$debugInfo .= ($i > 0 ? ',' : '');
			$debugInfo .= "\n\t" . $key . ' => ' . $value;

			$i++;
		}

		if (count($keys)) {
			$padLength = max(array_map('strlen', $keys));
			foreach ($keys as $key) {
				$keyPadded = str_pad($key, $padLength, ' ', STR_PAD_RIGHT);
				$debugInfo = str_replace($key . ' =>', $keyPadded . ' =>', $debugInfo);
			}
		}

		$debugInfo .= ($i > 0 ? "\n" : '');

		$debugInfo .= $suffix;

		return $debugInfo;
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	private static function getDebugInformationArray($data) {
		return self::getDebugInformationIterable($data, '[', ']');
	}

	/**
	 * @param object $data
	 *
	 * @return string
	 */
	private static function getDebugInformationObject($data) {
		return self::getDebugInformationIterable($data, get_class($data) . ' {', '}');
	}

	/**
	 * @param resource $data
	 *
	 * @return string
	 */
	private static function getDebugInformationResource($data) {
		return (string) $data . ' (' . get_resource_type($data) . ')';
	}
}
