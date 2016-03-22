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
	 * @param mixed $data
	 *
	 * @codeCoverageIgnore
	 */
	private static function output($data) {
		if (!self::$output) {
			return;
		}

		if (php_sapi_name() == 'cli') {
			if (self::$showCalledFrom) {
				$data = self::getCalledFrom(2) . "\n" . $data;
			}
			echo $data;
		} else {
			if (self::$showCalledFrom) {
				$data = '<strong>' . self::getCalledFrom(2) . '</strong>' . "\n" . $data;
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
	 * @codeCoverageIgnore
	 */
	public static function debug($data) {
		self::output(self::getDebugInformation($data));
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function getDebugInformation($data) {
		if (is_null($data) || is_bool($data) || is_numeric($data)) {
			ob_start();
			// @codingStandardsIgnoreStart
			var_dump($data);
			// @codingStandardsIgnoreEnd
			$data = trim(ob_get_clean(), "\n");
		}

		if (is_array($data) || is_object($data) || is_resource($data)) {
			$data = print_r($data, true);
		}

		return (string) $data;
	}

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public static function getCalledFrom($index = 1) {
		$backtrace = debug_backtrace();

		$calledFrom = '';
		if (isset($backtrace[$index])) {
			if (isset($backtrace[$index]['file'])) {
				// Get file and line number
				$calledFrom .= $backtrace[$index]['file'] . ' line ' . $backtrace[$index]['line'];

				// Cleanup
				$calledFrom = str_replace('\\', '/', $calledFrom);
				if (!empty(self::$documentRoot)) {
					$calledFrom = substr($calledFrom, strlen(self::$documentRoot));
				}
				$calledFrom = trim($calledFrom, '/');
			} elseif (isset($backtrace[$index]['function'])) {
				// Get function call
				if (isset($backtrace[$index]['class'])) {
					$calledFrom .= $backtrace[$index]['class'];
				}
				if (isset($backtrace[$index]['type'])) {
					$calledFrom .= $backtrace[$index]['type'];
				}
				$calledFrom .= $backtrace[$index]['function'];
			}
		} else {
			$calledFrom = 'Unknown trace with index: ' . $index;
		}

		return $calledFrom;
	}

	/**
	 * @codeCoverageIgnore
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
	 * @param string $class
	 *
	 * @codeCoverageIgnore
	 */
	public static function showReflectClass($class) {
		self::output(self::reflectClass($class));
	}

	/**
	 * @param string $class
	 * @param string $property
	 *
	 * @codeCoverageIgnore
	 */
	public static function showreflectClassProperty($class, $property) {
		self::output(self::reflectClassProperty($class, $property));
	}

	/**
	 * @param string $class
	 * @param string $method
	 *
	 * @codeCoverageIgnore
	 */
	public static function showReflectClassMethod($class, $method) {
		self::output(self::reflectClassMethod($class, $method));
	}

	/**
	 * @param string $class
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
	 * @param string $class
	 * @param string $property
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
	 * @param string $class
	 * @param string $method
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
