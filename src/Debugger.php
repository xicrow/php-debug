<?php
namespace Xicrow\DebugTools;

/**
 * Class Debugger
 *
 * @package Xicrow\DebugTools
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
	 * @param $data
	 */
	private static function output($data) {
		if (!self::$output) {
			return;
		}

		if (php_sapi_name() == 'cli') {
			if (self::$showCalledFrom) {
				$data = self::getCalledFileAndLine(2) . "\n" . $data;
			}
			echo $data;
		} else {
			if (self::$showCalledFrom) {
				$data = '<strong>' . self::getCalledFileAndLine(2) . '</strong>' . "\n" . $data;
			}

			$style   = [];
			$style[] = 'margin:5px;';
			$style[] = 'padding:5px;';
			$style[] = 'font:12px normal \'Courier New\';';
			$style[] = 'color:#333;';
			$style[] = 'background:#F9F9F9;';
			$style[] = 'border:1px solid #000;';
			$style[] = 'display:block;';

			echo '<pre style="' . implode(' ', $style) . '">';
			echo $data;
			echo '</pre>';
		}
	}

	/**
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public static function debug($data) {
		if (is_null($data) || is_bool($data)) {
			ob_start();
			var_dump($data);
			$data = ob_get_clean();
		}

		if (is_array($data) || is_object($data)) {
			$data = print_r($data, true);
		}

		self::output($data);

		return true;
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public static function getCalledFileAndLine($index = 1) {
		$backtrace = debug_backtrace();

		if (isset($backtrace[$index])) {
			$fileAndLine = $backtrace[$index]['file'] . ' line ' . $backtrace[$index]['line'];
			$fileAndLine = str_replace('\\', '/', $fileAndLine);
			if (!empty(self::$documentRoot)) {
				$fileAndLine = substr($fileAndLine, strlen(self::$documentRoot));
			}
			$fileAndLine = trim($fileAndLine, '/');
		} else {
			$fileAndLine = 'Unknown trace with index ' . $index;
		}

		return $fileAndLine;
	}

	/**
	 *
	 */
	public static function showTrace() {
		$output = '';

		$backtrace = debug_backtrace();
		foreach ($backtrace as $trace) {
			if (isset($trace['class'])) {
				$output .= $trace['class'] . '::' . $trace['function'];
			} else {
				$output .= $trace['function'];
			}

			$output .= "\n";
		}

		self::output($output);
	}

	/**
	 * @param $class
	 */
	public static function showReflectClass($class) {
		self::output(self::reflectClass($class));
	}

	/**
	 * @param $class
	 * @param $property
	 */
	public static function showreflectClassProperty($class, $property) {
		self::output(self::reflectClassProperty($class, $property));
	}

	/**
	 * @param $class
	 * @param $method
	 */
	public static function showReflectClassMethod($class, $method) {
		self::output(self::reflectClassMethod($class, $method));
	}

	/**
	 * @param $class
	 *
	 * @return string
	 */
	public static function reflectClass($class) {
		$output = '';

		$reflectionClass = new \ReflectionClass($class);

		$comment = $reflectionClass->getDocComment();
		if (!empty($comment)) {
			$output .= $comment;
			$output .= "\n";
		}

		$output .= 'class ' . $reflectionClass->name . '{';
		$firstElement = true;
		foreach ($reflectionClass->getProperties() as $reflectionProperty) {
			if (!$firstElement) {
				$output .= "\n";
			}
			$firstElement = false;

			$output .= self::reflectClassProperty($class, $reflectionProperty->name);
		}

		foreach ($reflectionClass->getMethods() as $reflectionMethod) {
			if (!$firstElement) {
				$output .= "\n";
			}
			$firstElement = false;

			$output .= self::reflectClassMethod($class, $reflectionMethod->name);
		}
		$output .= "\n";
		$output .= '}';

		return $output;
	}

	/**
	 * @param $class
	 * @param $property
	 *
	 * @return string
	 */
	public static function reflectClassProperty($class, $property) {
		$output = '';

		$reflectionProperty = new \ReflectionProperty($class, $property);

		$comment = $reflectionProperty->getDocComment();
		if (!empty($comment)) {
			$output .= "\n";
			$output .= "\t";
			$output .= $comment;
		}

		$output .= "\n";
		$output .= "\t";
		if ($reflectionProperty->isPublic()) {
			$output .= 'public ';
		} elseif ($reflectionProperty->isPrivate()) {
			$output .= 'private ';
		} elseif ($reflectionProperty->isProtected()) {
			$output .= 'protected ';
		}
		if ($reflectionProperty->isStatic()) {
			$output .= 'static ';
		}
		$output .= '$' . $reflectionProperty->name . ';';

		return $output;
	}

	/**
	 * @param $class
	 * @param $method
	 *
	 * @return string
	 */
	public static function reflectClassMethod($class, $method) {
		$output = '';

		$reflectionMethod = new \ReflectionMethod($class, $method);

		$comment = $reflectionMethod->getDocComment();
		if (!empty($comment)) {
			$output .= "\n";
			$output .= "\t";
			$output .= $comment;
		}

		$output .= "\n";
		$output .= "\t";
		if ($reflectionMethod->isPublic()) {
			$output .= 'public ';
		} elseif ($reflectionMethod->isPrivate()) {
			$output .= 'private ';
		} elseif ($reflectionMethod->isProtected()) {
			$output .= 'protected ';
		}
		if ($reflectionMethod->isStatic()) {
			$output .= 'static ';
		}
		$output .= 'function ' . $reflectionMethod->name . '(';
		if ($reflectionMethod->getNumberOfParameters()) {
			foreach ($reflectionMethod->getParameters() as $reflectionMethodParameterIndex => $reflectionMethodParameter) {
				if ($reflectionMethodParameterIndex > 0) {
					$output .= ', ';
				}
				$output .= '$' . $reflectionMethodParameter->name;
				if ($reflectionMethodParameter->isDefaultValueAvailable()) {
					$defaultValue = $reflectionMethodParameter->getDefaultValue();
					if (is_null($defaultValue)) {
						$defaultValue = 'null';
					}
					if (is_string($defaultValue)) {
						$defaultValue = '\'' . $defaultValue . '\'';
					}
					if (is_bool($defaultValue)) {
						if (!$defaultValue) {
							$defaultValue = 'false';
						} else {
							$defaultValue = 'true';
						}
					}
					if (is_array($defaultValue)) {
						$defaultValue = '[' . implode(', ', $defaultValue) . ']';
					}
					$output .= ' = ' . $defaultValue;
				}
			}
		}
		$output .= ') {}';

		return $output;
	}
}
