<?php
namespace Xicrow\PhpDebug;

/**
 * Class Timer
 *
 * @package Xicrow\PhpDebug
 */
class Timer {
	/**
	 * @var array
	 */
	public static $collection = [];

	/**
	 * @var boolean|string
	 */
	public static $currentItem = false;

	/**
	 * @var array
	 */
	public static $runningItems = [];

	/**
	 * Force the unit to display elapsed times in (MS|S|M|H|D|W)
	 *
	 * @var null|string
	 */
	public static $forceDisplayUnit = null;

	/**
	 * Color threshold for output (5 => 'red', all items with values of 5 or higher will be red)
	 *
	 * @var array
	 */
	public static $colorThreshold = [];

	/**
	 *
	 */
	public static function reset() {
		self::$collection   = [];
		self::$currentItem  = false;
		self::$runningItems = [];
	}

	/**
	 * @param string|null $key
	 * @param array       $data
	 *
	 * @return string
	 */
	public static function add($key = null, $data = []) {
		// If no key is given
		if (is_null($key)) {
			// Set key to file and line
			$key = Debugger::getCalledFrom(2);
		}

		// If key is allready in use
		if (isset(self::$collection[$key])) {
			// Get original item
			$item = self::$collection[$key];

			// Set new item count
			$itemCount = (isset($item['count']) ? ($item['count'] + 1) : 2);

			// Set correct key for the original item
			if (strpos($item['key'], '#') === false) {
				self::$collection[$key] = array_merge($item, [
					'key'   => $key . ' #1',
					'count' => $itemCount
				]);
			} else {
				self::$collection[$key] = array_merge($item, [
					'count' => $itemCount
				]);
			}

			// Set new key
			$key = $key . ' #' . $itemCount;
		}

		// Make sure various options are set
		if (!isset($data['key'])) {
			$data['key'] = $key;
		}
		if (!isset($data['parent'])) {
			$data['parent'] = self::$currentItem;
		}
		if (!isset($data['level'])) {
			$data['level'] = 0;
			if (isset($data['parent']) && isset(self::$collection[$data['parent']])) {
				$data['level'] = (self::$collection[$data['parent']]['level'] + 1);
			}
		}

		// Add item to collection
		self::$collection[$key] = $data;

		return $key;
	}

	/**
	 * @param string|null $key
	 *
	 * @return string
	 */
	public static function start($key = null) {
		// Add new item
		$key = self::add($key, [
			'start' => microtime(true)
		]);

		// Set current item
		self::$currentItem = $key;

		// Add to running items
		self::$runningItems[$key] = true;

		return $key;
	}

	/**
	 * @param string|null $key
	 *
	 * @return string
	 */
	public static function stop($key = null) {
		// If no key is given
		if (is_null($key)) {
			// Get key of the last started item
			end(self::$runningItems);
			$key = key(self::$runningItems);
		}

		// Check for key duplicates, and find the first one not stopped
		if (isset(self::$collection[$key]) && isset(self::$collection[$key]['stop'])) {
			$originalName = $key;
			$i            = 1;
			while (isset(self::$collection[$key])) {
				if (!isset(self::$collection[$key]['stop'])) {
					break;
				}

				$key = $originalName . ' #' . ($i + 1);

				$i++;
			}
		}

		// If item exists in collection
		if (isset(self::$collection[$key])) {
			// Update the item
			self::$collection[$key]['stop'] = microtime(true);

			self::$currentItem = self::$collection[$key]['parent'];
		}

		if (isset(self::$runningItems[$key])) {
			unset(self::$runningItems[$key]);
		}

		return $key;
	}

	/**
	 * @param string|null    $key
	 * @param int|float|null $start
	 * @param int|float|null $stop
	 *
	 * @return string
	 */
	public static function custom($key = null, $start = null, $stop = null) {
		// Add new item
		self::add($key, [
			'start' => $start,
			'stop'  => $stop
		]);

		// If no stop value is given
		if (is_null($stop)) {
			// Set current item
			self::$currentItem = $key;

			// Add to running items
			self::$runningItems[$key] = true;
		}

		return $key;
	}

	/**
	 * @param string|null           $key
	 * @param string|array|\Closure $callback
	 *
	 * @return mixed
	 */
	public static function callback($key = null, $callback) {
		// Get parameters for callback
		$callbackParams = func_get_args();
		unset($callbackParams[0], $callbackParams[1]);
		$callbackParams = array_values($callbackParams);

		// Get key if no key is given
		if (is_null($key)) {
			if (is_string($callback)) {
				$key = $callback;
			} elseif (is_array($callback)) {
				$keyArr = [];
				foreach ($callback as $k => $v) {
					if (is_string($v)) {
						$keyArr[] = $v;
					} elseif (is_object($v)) {
						$keyArr[] = get_class($v);
					}
				}

				$key = implode('', $keyArr);
				if (count($keyArr) > 1) {
					$method = array_pop($keyArr);
					$key    = implode('/', $keyArr);
					$key .= '::' . $method;
				}

				unset($keyArr, $method);
			} elseif (is_object($callback) && $callback instanceof \Closure) {
				$key = 'closure';
			}
			$key = 'callback: ' . $key;
		}

		// Set default return value
		$returnValue = true;

		// Set error handler, to convert errors to exceptions
		set_error_handler(function ($errno, $errstr, $errfile, $errline, array $errcontext) {
			throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
		});

		try {
			// Start output buffer to capture any output
			ob_start();

			// Start profiler
			self::start($key);

			// Execute callback, and get result
			$callbackResult = call_user_func_array($callback, $callbackParams);

			// Stop profiler
			self::stop($key);

			// Get and clean output buffer
			$callbackOutput = ob_get_clean();
		} catch (\ErrorException $callbackException) {
			// Stop and clean output buffer
			ob_end_clean();

			// Show error message
			Debugger::output('Invalid callback sent to Timer::callback: ' . str_replace('callback: ', '', $key));

			// Clear the item from the collection
			unset(self::$collection[$key]);

			// Clear callback result and output
			unset($callbackResult, $callbackOutput);

			// Set return value to false
			$returnValue = false;
		}

		// Restore error handler
		restore_error_handler();

		// Return result, output or true
		return (isset($callbackResult) ? $callbackResult : (!empty($callbackOutput) ? $callbackOutput : $returnValue));
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @codeCoverageIgnore
	 */
	public static function show($key = null, $options = []) {
		$output = self::getStats($key, $options);

		if (!empty($output)) {
			Debugger::output($output);
		}
	}

	/**
	 * @param array $options
	 *
	 * @codeCoverageIgnore
	 */
	public static function showAll($options = []) {
		// Stop started items
		if (count(self::$runningItems)) {
			foreach (self::$runningItems as $key => $value) {
				self::stop($key);
			}
		}

		// Output items
		$output    = '';
		$itemCount = 1;
		foreach (self::$collection as $key => $item) {
			$stats = self::getStats($key, $options);

			if (php_sapi_name() == 'cli') {
				$output .= (!empty($output) ? "\n" : '') . $stats;
			} else {
				$output .= '<div class="xicrow-php-debug-timer">';
				$output .= $stats;
				$output .= '</div>';
			}

			$itemCount++;

			unset($stats);
		}
		unset($itemCount);

		$output .= '<style type="text/css">';
		$output .= 'pre.xicrow-php-debug-debugger div.xicrow-php-debug-timer{';
		$output .= 'cursor: pointer;';
		$output .= '}';
		$output .= 'pre.xicrow-php-debug-debugger div.xicrow-php-debug-timer:hover{';
		$output .= 'font-weight: bold;';
		$output .= 'background-color: #EEE;';
		$output .= '}';
		$output .= '</style>';

		Debugger::output($output);
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @return string
	 */
	public static function getStats($key, $options = []) {
		// Merge options with default options
		$options = array_merge([
			// Show nested (boolean)
			'nested'         => true,
			// Prefix for nested items (string)
			'nested_prefix'  => '|-- ',
			// Max key length (int)
			'max_key_length' => 100
		], $options);

		// If item does not exist
		if (!isset(self::$collection[$key])) {
			return 'Unknow item in with key: ' . $key;
		}

		// Get item
		$item = self::$collection[$key];

		// Get item result
		$itemResult          = 'N/A';
		$itemResultFormatted = 'N/A';
		if (isset($item['start']) && isset($item['stop'])) {
			$itemResult          = (($item['stop'] - $item['start']) * 1000);
			$itemResultFormatted = self::formatMiliseconds($itemResult, 4, self::$forceDisplayUnit);
		}

		// Variable for output
		$output = '';

		// Prep key for output
		$outputName = '';
		$outputName .= ($options['nested'] ? str_repeat($options['nested_prefix'], $item['level']) : '');
		$outputName .= $item['key'];
		if (strlen($outputName) > $options['max_key_length']) {
			$outputName = '~' . substr($item['key'], -($options['max_key_length'] - 1));
		}

		// Add item stats
		$output .= str_pad($outputName, $options['max_key_length'], ' ');
		$output .= ' | ';
		$output .= str_pad($itemResultFormatted, 20, ' ', ($itemResult == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));

		if (php_sapi_name() != 'cli' && is_array(self::$colorThreshold) && count(self::$colorThreshold)) {
			krsort(self::$colorThreshold);
			foreach (self::$colorThreshold as $value => $color) {
				if (is_numeric($itemResult) && $itemResult >= $value) {
					$output = '<span style="color: ' . $color . ';">' . $output . '</span>';
				}
			}
		}

		return $output;
	}

	/**
	 * @param int|float   $number
	 * @param int         $precision
	 * @param null|string $forceUnit
	 *
	 * @return string
	 */
	public static function formatMiliseconds($number = 0, $precision = 2, $forceUnit = null) {
		$units = [
			'MS' => 1,
			'S'  => 1000,
			'M'  => 60,
			'H'  => 60,
			'D'  => 24,
			'W'  => 7
		];

		if (is_null($forceUnit)) {
			$forceUnit = self::$forceDisplayUnit;
		}

		$value = $number;
		if (!empty($forceUnit) && array_key_exists($forceUnit, $units)) {
			$unit = $forceUnit;
			foreach ($units as $k => $v) {
				$value = ($value / $v);
				if ($k == $unit) {
					break;
				}
			}
		} else {
			$unit = '';
			foreach ($units as $k => $v) {
				if (empty($unit) || ($value / $v) > 1) {
					$value = ($value / $v);
					$unit  = $k;
				} else {
					break;
				}
			}
		}

		return sprintf('%0.' . $precision . 'f', $value) . ' ' . str_pad($unit, 2, ' ', STR_PAD_RIGHT);
	}
}
