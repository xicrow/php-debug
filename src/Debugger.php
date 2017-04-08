<?php
namespace Xicrow\PhpDebug;

/**
 * Class Debugger
 *
 * @package Xicrow\PhpDebug
 */
class Debugger {
	/**
	 * @var string|null
	 */
	public static $documentRoot = null;

	/**
	 * @var bool
	 */
	public static $showCalledFrom = true;

	/**
	 * @var bool
	 */
	public static $output = true;

	/**
	 * @var bool
	 */
	private static $outputStyles = true;

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
			echo '######################### DEBUG #########################';
			echo "\n";
			if (self::$showCalledFrom) {
				echo self::getCalledFrom(2);
				echo "\n";
			}
			echo $data;
			echo "\n";
		} else {
			echo '<pre class="xicrow-php-debug-debugger">';
			if (self::$showCalledFrom) {
				echo '<div class="xicrow-php-debug-debugger__called-from">';
				echo self::getCalledFrom(2);
				echo '</div>';
			}
			echo $data;
			echo '</pre>';
			if (self::$outputStyles) {
				echo '<style type="text/css">';
				echo 'pre.xicrow-php-debug-debugger{';
				echo 'margin:5px 0;';
				echo 'padding:5px;';
				echo 'font-family:Consolas,Courier New,​monospace;';
				echo 'font-weight:normal;';
				echo 'font-size:13px;';
				echo 'line-height:1.2;';
				echo 'color:#555;';
				echo 'background:#FFF;';
				echo 'border:1px solid #CCC;';
				echo 'display:block;';
				echo 'overflow:auto;';
				echo '}';
				echo 'pre.xicrow-php-debug-debugger div.xicrow-php-debug-debugger__called-from{';
				echo 'margin: 0 0 5px 0;';
				echo 'padding: 0 0 5px 0;';
				echo 'border-bottom: 1px solid #CCC;';
				echo 'font-style: italic;';
				echo '}';
				echo '</style>';

				self::$outputStyles = false;
			}
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

		$data         .= 'class ' . $reflectionClass->name . '{';
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

		$reflectionClass    = new \ReflectionClass($class);
		$reflectionProperty = new \ReflectionProperty($class, $property);

		$defaultPropertyValues = $reflectionClass->getDefaultProperties();

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
		$data .= '$' . $reflectionProperty->name;
		if (isset($defaultPropertyValues[$property])) {
			$data .= ' = ' . self::getDebugInformation($defaultPropertyValues[$property]);
		}
		$data .= ';';

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
					$data         .= ' = ' . $defaultValue;
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

		// Return called from
		if ($calledFromFile) {
			return $calledFromFile;
		} elseif ($calledFromFunction) {
			return $calledFromFunction;
		} else {
			return 'Unable to get called from trace';
		}
	}

	/**
	 * @param mixed $data
	 *
	 * @return string
	 */
	public static function getDebugInformation($data, array $options = []) {
		$options = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $options);

		$dataType = gettype($data);

		$methodName = 'getDebugInformation' . ucfirst(strtolower($dataType));

		$result = 'No method found supporting data type: ' . $dataType;
		if ($dataType == 'string') {
			if (php_sapi_name() == 'cli') {
				$result = (string) '"' . $data . '"';
			} else {
				$result = (string) '"' . htmlentities($data) . '"';
			}
		} elseif (method_exists('\Xicrow\PhpDebug\Debugger', $methodName)) {
			$result = (string) self::$methodName($data, [
				'depth'  => ($options['depth'] - 1),
				'indent' => ($options['indent'] + 1),
			]);
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
	 * @param array $data
	 *
	 * @return string
	 */
	private static function getDebugInformationArray($data, array $options = []) {
		$options = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $options);

		$debugInfo = "[";

		$break = $end = null;
		if (!empty($data)) {
			$break = "\n" . str_repeat("\t", $options['indent']);
			$end   = "\n" . str_repeat("\t", $options['indent'] - 1);
		}

		$datas = [];
		if ($options['depth'] >= 0) {
			foreach ($data as $key => $val) {
				// Sniff for globals as !== explodes in < 5.4
				if ($key === 'GLOBALS' && is_array($val) && isset($val['GLOBALS'])) {
					$val = '[recursion]';
				} elseif ($val !== $data) {
					$val = static::getDebugInformation($val, $options);
				}
				$datas[] = $break . static::getDebugInformation($key) . ' => ' . $val;
			}
		} else {
			$datas[] = $break . '[maximum depth reached]';
		}

		return $debugInfo . implode(',', $datas) . $end . ']';
	}

	/**
	 * @param object $data
	 *
	 * @return string
	 */
	private static function getDebugInformationObject($data, array $options = []) {
		$options = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $options);

		$debugInfo = '';
		$debugInfo .= 'object(' . get_class($data) . ') {';

		$break = "\n" . str_repeat("\t", $options['indent']);
		$end   = "\n" . str_repeat("\t", $options['indent'] - 1);

		if ($options['depth'] > 0 && method_exists($data, '__debugInfo')) {
			try {
				$debugArray = static::getDebugInformationArray($data->__debugInfo(), array_merge($options, [
					'depth' => ($options['depth'] - 1),
				]));
				$debugInfo  .= substr($debugArray, 1, -1);

				return $debugInfo . $end . '}';
			} catch (\Exception $e) {
				$message = $e->getMessage();

				return $debugInfo . "\n(unable to export object: $message)\n }";
			}
		}

		if ($options['depth'] > 0) {
			$props      = [];
			$objectVars = get_object_vars($data);
			foreach ($objectVars as $key => $value) {
				$value   = static::getDebugInformation($value, array_merge($options, [
					'depth' => ($options['depth'] - 1),
				]));
				$props[] = "$key => " . $value;
			}

			$ref     = new \ReflectionObject($data);
			$filters = [
				\ReflectionProperty::IS_PROTECTED => 'protected',
				\ReflectionProperty::IS_PRIVATE   => 'private',
			];
			foreach ($filters as $filter => $visibility) {
				$reflectionProperties = $ref->getProperties($filter);
				foreach ($reflectionProperties as $reflectionProperty) {
					$reflectionProperty->setAccessible(true);
					$property = $reflectionProperty->getValue($data);

					$value   = static::getDebugInformation($property, array_merge($options, [
						'depth' => ($options['depth'] - 1),
					]));
					$key     = $reflectionProperty->name;
					$props[] = sprintf('[%s] %s => %s', $visibility, $key, $value);
				}
			}

			$debugInfo .= $break . implode($break, $props) . $end;
		}
		$debugInfo .= '}';

		return $debugInfo;
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
