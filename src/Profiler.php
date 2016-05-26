<?php
namespace Xicrow\PhpDebug;

/**
 * Class Profiler
 *
 * @package Xicrow\PhpDebug
 */
abstract class Profiler {
	/**
	 * @var Collection
	 */
	public static $collection = null;

	/**
	 * Make sure Collection is set
	 */
	public static function init() {
		if (is_null(static::$collection)) {
			static::$collection = new Collection();
		}
	}

	/**
	 * @return int|float
	 * @codeCoverageIgnore
	 */
	public static function getMetric() {
		// Since we're not able to create abstract static functions, this'll do
		trigger_error('Implement getMetric() in child class');
	}

	/**
	 * @param int|float $metric
	 *
	 * @return mixed
	 * @codeCoverageIgnore
	 */
	public static function getMetricFormatted($metric) {
		// Since we're not able to create abstract static functions, this'll do
		trigger_error('Implement getMetricFormatted() in child class');
	}

	/**
	 * @param int|float $start
	 * @param int|float $stop
	 *
	 * @return float|int
	 * @codeCoverageIgnore
	 */
	public static function getMetricResult($start, $stop) {
		// Since we're not able to create abstract static functions, this'll do
		trigger_error('Implement getMetricResult() in child class');
	}

	/**
	 * @param float|int $result
	 *
	 * @return mixed
	 * @codeCoverageIgnore
	 */
	public static function getMetricResultFormatted($result) {
		// Since we're not able to create abstract static functions, this'll do
		trigger_error('Implement getMetricResultFormatted() in child class');
	}

	/**
	 * @param string|null $key
	 * @param array       $data
	 *
	 * @return bool
	 */
	public static function add($key = null, $data = []) {
		static::init();

		// If no key is given
		if (is_null($key)) {
			// Set key to file and line
			$key = Debugger::getCalledFrom(2);
		}

		// Make sure parent is set
		if (!isset($data['parent'])) {
			$data['parent'] = static::getLastItemName('started');
		}

		// Make sure level is set
		if (!isset($data['level'])) {
			$data['level'] = 0;
			if (isset($data['parent']) && $parent = static::$collection->get($data['parent'])) {
				$data['level'] = ($parent['level'] + 1);
			}
		}

		// Add item to collection
		return static::$collection->add($key, $data);
	}

	/**
	 * @param string|null $key
	 *
	 * @return bool
	 */
	public static function start($key = null) {
		// Get metric
		$metric = static::getMetric();

		static::init();

		// Add new item
		static::add($key, [
			'start_value'       => $metric,
			'start_time'        => microtime(true),
			'start_called_from' => Debugger::getCalledFrom(1)
		]);

		// Return true
		return true;
	}

	/**
	 * @param string|null $key
	 *
	 * @return bool
	 */
	public static function stop($key = null) {
		// Get metric
		$metric = static::getMetric();

		static::init();

		// If no key is given
		if (is_null($key)) {
			// Get key of the last started item
			$key = static::getLastItemName('started');
		}

		// Check for key duplicates, and find the first one not stopped
		$originalName = $key;
		$i            = 1;
		while (static::$collection->exists($key)) {
			if (empty(static::$collection->get($key)['stop_value'])) {
				break;
			}

			$key = $originalName . ' #' . ($i + 1);

			$i++;
		}

		// If item exists in collection
		if (static::$collection->exists($key)) {
			// Update the item
			static::$collection->update($key, [
				'stop_value'       => $metric,
				'stop_time'        => microtime(true),
				'stop_called_from' => Debugger::getCalledFrom(1)
			]);

			return true;
		}

		return false;
	}

	/**
	 * @param string|null $key
	 * @param int|null    $startValue
	 * @param int|null    $stopValue
	 *
	 * @return bool
	 */
	public static function custom($key = null, $startValue = null, $stopValue = null) {
		static::init();

		// Set data for the item
		$data = [];
		if (!is_null($startValue)) {
			$data['start_value']       = $startValue;
			$data['start_time']        = microtime(true);
			$data['start_called_from'] = Debugger::getCalledFrom(1);
		}
		if (!is_null($stopValue)) {
			$data['stop_value']       = $stopValue;
			$data['stop_time']        = microtime(true);
			$data['stop_called_from'] = Debugger::getCalledFrom(1);
		}

		// Add item to collection
		return static::add($key, $data);
	}

	/**
	 * @param string|null           $key
	 * @param string|array|\Closure $callback
	 * @param array                 ...$callbackParams
	 *
	 * @return mixed
	 */
	public static function callback($key = null, $callback, ...$callbackParams) {
		static::init();

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
			static::start($key);

			// Execute callback, and get result
			$callbackResult = call_user_func_array($callback, $callbackParams);

			// Stop profiler
			static::stop($key);

			// Get and clean output buffer
			$callbackOutput = ob_get_clean();
		} catch (\ErrorException $callbackException) {
			// Stop and clean output buffer
			ob_end_clean();

			// Show error message
			Debugger::output('Invalid callback sent to Profiler::callback: ' . str_replace('callback: ', '', $key));

			// Clear the item from the collection
			static::$collection->clear($key);

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
		static::init();

		$output = static::getStats($key, $options);

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
		static::init();

		// Merge options with default options
		$options = array_merge([
			// Sort field: index|key|start|stop|result
			'sort'       => 'index',
			// Sort order: asc|desc
			'sort_order' => 'asc'
		], $options);

		// Available sort options
		$sortOptions = ['index', 'key', 'start_value', 'stop_value', 'result'];

		// Get copy of collection
		$collection = clone static::$collection;

		// Add result to items if needed
		foreach ($collection as $key => $item) {
			if (!isset($item['result'])) {
				// If item is not started
				if (!isset($item['start_value'])) {
					$collection->clear($key);
					continue;
				}

				// If item is not stopped
				if (!isset($item['stop_value'])) {
					// Stop the item
					static::stop($key);

					$item = static::$collection->get($key);
				}

				$collection->update($key, ['result' => static::getMetricResult($item['start_value'], $item['stop_value'])]);
			}
		}

		// If valid sort option is given
		if (in_array($options['sort'], $sortOptions)) {
			// Sort collection
			$collection->sort($options['sort'], $options['sort_order']);
		}

		// Output items
		$output = '';
		foreach ($collection as $key => $item) {
			$output .= (!empty($output) ? "\n" : '');
			$output .= static::getStats($key, $options);
		}
		Debugger::output($output);
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @return string
	 */
	public static function getStats($key = null, $options = []) {
		static::init();

		// Merge options with default options
		$options = array_merge([
			// Show nested (boolean)
			'nested'          => true,
			// Prefix for nested items (string)
			'nested_prefix'   => '|-- ',
			// Show in one line (boolean)
			'oneline'         => true,
			// If oneline, max key length (int)
			'oneline_length'  => 100,
			// Show start stop for the item (boolean)
			'show_start_stop' => false
		], $options);

		// Get key of the last stopped item, if no key is given
		$key = (is_null($key) ? static::getLastItemName('stopped') : $key);

		// If item does not exist
		if (!static::$collection->exists($key)) {
			return 'Unknow item in with key: ' . $key;
		}

		// Get item
		$item = static::$collection->get($key);

		// Return stats
		return ($options['oneline'] ? self::getStatsOneline($item, $options) : self::getStatsMultiline($item, $options));
	}

	/**
	 * @param array $item
	 * @param array $options
	 *
	 * @return string
	 */
	public static function getStatsOneline($item, $options = []) {
		// Merge options with default options
		$options = array_merge([
			// Show nested (boolean)
			'nested'          => true,
			// Prefix for nested items (string)
			'nested_prefix'   => '|-- ',
			// If oneline, max key length (int)
			'oneline_length'  => 100,
			// Show start stop for the item (boolean)
			'show_start_stop' => false
		], $options);

		// Get item result
		$itemResult = 'N/A';
		if (isset($item['start_value']) && isset($item['stop_value'])) {
			$itemResult = static::getMetricResult($item['start_value'], $item['stop_value']);
		}

		// Variable for output
		$output = '';

		// Prep key for output
		$outputName = '';
		$outputName .= ($options['nested'] ? str_repeat($options['nested_prefix'], $item['level']) : '');
		$outputName .= $item['key'];
		if (strlen($outputName) > $options['oneline_length']) {
			$outputName = '~' . substr($item['key'], -($options['oneline_length'] - 1));
		}

		// Add item stats
		$output .= str_pad($outputName, $options['oneline_length'], ' ');
		$output .= ' | ';
		$output .= str_pad(static::getMetricResultFormatted($itemResult), 20, ' ', ($itemResult == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));
		if ($options['show_start_stop']) {
			$output .= ' | ';
			$output .= str_pad((isset($item['start_time']) ? static::formatDateTime($item['start_time']) : 'N/A'), 19, ' ');
			$output .= ' | ';
			$output .= str_pad((isset($item['stop_time']) ? static::formatDateTime($item['stop_time']) : 'N/A'), 19, ' ');
		}

		return $output;
	}

	/**
	 * @param array $item
	 * @param array $options
	 *
	 * @return string
	 */
	public static function getStatsMultiline($item, $options = []) {
		// Merge options with default options
		$options = array_merge([
			// Show nested (boolean)
			'nested'          => true,
			// Prefix for nested items (string)
			'nested_prefix'   => '|-- ',
			// Show start stop for the item (boolean)
			'show_start_stop' => false
		], $options);

		// Get item result
		$itemResult = 'N/A';
		if (isset($item['start_value']) && isset($item['stop_value'])) {
			$itemResult = static::getMetricResult($item['start_value'], $item['stop_value']);
		}

		// Variable for output
		$output = '';

		// Add item stats
		$output .= 'Item   : ' . $item['key'];
		if ($options['show_start_stop']) {
			$output .= "\n";
			$output .= 'Start   : ' . (isset($item['start_time']) ? static::formatDateTime($item['start_time']) : 'N/A');
			$output .= "\n";
			$output .= 'Stop    : ' . (isset($item['stop_time']) ? static::formatDateTime($item['stop_time']) : 'N/A');
		}
		$output .= "\n";
		$output .= 'Result : ' . static::getMetricResultFormatted($itemResult);

		// Show as nested
		if ($options['nested']) {
			$output = str_repeat($options['nested_prefix'], $item['level']) . $output;
			$output = str_replace("\n", "\n" . str_repeat($options['nested_prefix'], $item['level']), $output);
		}

		return $output;
	}

	/**
	 * @param string $type
	 *
	 * @return bool|string
	 */
	public static function getLastItemName($type = '') {
		static::init();

		// Set default key
		$key = false;

		// Get collection items
		$items = static::$collection->getAll();

		// Get reverse list of item keys
		$itemKeys = array_reverse(array_keys($items));

		// If unknown type is given
		if ($type != 'started' && $type != 'stopped') {
			// Get current/last key
			$key = current($itemKeys);
		} else {
			// Loop throug items reversed and get the last one matching the given type
			foreach ($itemKeys as $itemKey) {
				$itemKeyIntersect = array_values(array_intersect(array_keys($items[$itemKey]), ['start_time', 'stop_time']));

				if ($type == 'stopped' && $itemKeyIntersect == ['start_time', 'stop_time']) {
					$key = $itemKey;
					break;
				}

				if ($type == 'started' && $itemKeyIntersect == ['start_time']) {
					$key = $itemKey;
					break;
				}
			}
		}

		// Return the key
		return $key;
	}

	/**
	 * @param int|float $unixTimestamp
	 * @param bool      $showMiliseconds
	 *
	 * @return string
	 */
	public static function formatDateTime($unixTimestamp, $showMiliseconds = false) {
		$formatted = date('Y-m-d H:i:s', $unixTimestamp);

		if ($showMiliseconds) {
			$miliseconds = 0;
			if (strpos($unixTimestamp, '.') !== false) {
				$miliseconds = substr($unixTimestamp, (strpos($unixTimestamp, '.') + 1));
			}

			$formatted .= '.' . str_pad($miliseconds, 4, '0', STR_PAD_RIGHT);
		}

		return $formatted;
	}

	/**
	 * @param array     $units
	 * @param int|float $number
	 * @param int       $precision
	 * @param null      $forceUnit
	 *
	 * @return string
	 */
	public static function formatForUnits($units = [], $number = 0, $precision = 2, $forceUnit = null) {
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

	/**
	 * @param      $value
	 * @param int  $precision
	 * @param null $forceUnit
	 *
	 * @return string
	 */
	public static function formatMiliseconds($value, $precision = 2, $forceUnit = null) {
		return self::formatForUnits([
			'MS' => 1,
			'S'  => 1000,
			'M'  => 60,
			'H'  => 60,
			'D'  => 24,
			'W'  => 7
		], $value, $precision, $forceUnit);
	}

	/**
	 * @param      $value
	 * @param int  $precision
	 * @param null $forceUnit
	 *
	 * @return string
	 */
	public static function formatBytes($value, $precision = 2, $forceUnit = null) {
		return self::formatForUnits([
			'B'  => 1,
			'KB' => 1024,
			'MB' => 1024,
			'GB' => 1024,
			'TB' => 1024,
			'PB' => 1024
		], $value, $precision, $forceUnit);
	}
}
