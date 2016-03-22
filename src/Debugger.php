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
			if (self::$showCalledFrom) {
				$data = self::getCalledFrom(2) . "\n" . $data;
			}
			echo $data;
		} else {
			if (self::$showCalledFrom) {
				$data = '<strong>' . self::getCalledFrom(2) . '</strong>' . "\n" . $data;
			}

			$style   = [];
			$style[] = 'margin:0;';
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

	/**
	 * @param int $index
	 *
	 * @return string
	 */
	public static function getCalledFrom($index = 1) {
		$backtrace = debug_backtrace();

		if (!isset($backtrace[$index])) {
			return 'Unknown trace with index: ' . $index;
		}

		$calledFrom = '';
		if (isset($backtrace[$index]['file'])) {
			// Get file and line number
			$calledFrom .= $backtrace[$index]['file'] . ' line ' . $backtrace[$index]['line'];

			// Cleanup
			$calledFrom = str_replace('\\', '/', $calledFrom);
			$calledFrom = (!empty(self::$documentRoot) ? substr($calledFrom, strlen(self::$documentRoot)) : $calledFrom);
			$calledFrom = trim($calledFrom, '/');
		} elseif (isset($backtrace[$index]['function'])) {
			// Get function call
			$calledFrom .= (isset($backtrace[$index]['class']) ? $backtrace[$index]['class'] : '');
			$calledFrom .= (isset($backtrace[$index]['type']) ? $backtrace[$index]['type'] : '');
			$calledFrom .= $backtrace[$index]['function'];
		} else {
			$calledFrom = 'Trace has no file or function !';
		}

		return $calledFrom;
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function getDebugInformation($data, $indent = false) {
		$dataType = gettype($data);

		$methodName = 'getDebugInformation' . ucfirst(strtolower($dataType));

		if ($dataType == 'string') {
			$result = (string) '"' . $data . '"';
		} elseif (method_exists('\Xicrow\Debug\Debugger', $methodName)) {
			$result = (string) self::$methodName($data);
		} else {
			$result = 'No method found supporting data type: ' . $dataType;
		}

		if ($indent > 0) {
			$result = str_replace("\n", "\n\t", $result);
		}

		return $result;
	}

	/**
	 * @param null $data
	 *
	 * @return string
	 */
	public static function getDebugInformationNull($data) {
		return 'NULL';
	}

	/**
	 * @param boolean $data
	 *
	 * @return string
	 */
	public static function getDebugInformationBoolean($data) {
		return ($data ? 'TRUE' : 'FALSE');
	}

	/**
	 * @param integer $data
	 *
	 * @return string
	 */
	public static function getDebugInformationInteger($data) {
		return $data;
	}

	/**
	 * @param double $data
	 *
	 * @return string
	 */
	public static function getDebugInformationDouble($data) {
		return $data;
	}

	/**
	 * @param array|object|resource $data
	 * @param string                $prefix
	 * @param string                $suffix
	 *
	 * @return string
	 */
	public static function getDebugInformationIterable($data, $prefix = '', $suffix = '') {
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

		$debugInfo .= ($i > 0 ? "\n" : '');
		$debugInfo .= $suffix;

		if (count($keys)) {
			$padLength = max(array_map('strlen', $keys));
			foreach ($keys as $key) {
				$keyPadded = str_pad($key, $padLength, ' ', STR_PAD_RIGHT);
				$debugInfo = str_replace($key . ' =>', $keyPadded . ' =>', $debugInfo);
			}
		}

		return $debugInfo;
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	public static function getDebugInformationArray($data) {
		return self::getDebugInformationIterable($data, '[', ']');
	}

	/**
	 * @param object $data
	 *
	 * @return string
	 */
	public static function getDebugInformationObject($data) {
		return self::getDebugInformationIterable($data, get_class($data) . ' {', '}');
	}

	/**
	 * @param resource $data
	 *
	 * @return string
	 */
	public static function getDebugInformationResource($data) {
		return self::getDebugInformationIterable($data, get_class($data) . ' {', '}');
	}
}
