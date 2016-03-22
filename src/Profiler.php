<?php
namespace Xicrow\Debug;

/**
 * Class Profiler
 *
 * @package Xicrow\Debug
 */
abstract class Profiler {
	/**
	 * @var Collection
	 */
	public static $collection = null;

	/**
	 *
	 */
	public static function init() {
		if (is_null(static::$collection)) {
			static::$collection = new Collection();
		}
	}

	/**
	 * @return int|float
	 */
	public abstract function getMetric();

	/**
	 * @param int|float $metric
	 *
	 * @return mixed
	 */
	public abstract function getMetricFormatted($metric);

	/**
	 * @param int|float $start
	 * @param int|float $stop
	 *
	 * @return float|int
	 */
	public abstract function getMetricResult($start, $stop);

	/**
	 * @param float|int $result
	 *
	 * @return mixed
	 */
	public abstract function getMetricResultFormatted($result);

	/**
	 * @param string|null $key
	 * @param array       $data
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
			if (isset($data['parent']) && $parentTimer = static::$collection->get($data['parent'])) {
				$data['level'] = ($parentTimer['level'] + 1);
			}
		}

		// Add item to collection
		static::$collection->add($key, $data);
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
			'start_called_from' => Debugger::getCalledFrom()
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
				'stop_called_from' => Debugger::getCalledFrom()
			]);
		}
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
			$data['start_called_from'] = Debugger::getCalledFrom();
		}
		if (!is_null($stopValue)) {
			$data['stop_value']       = $stopValue;
			$data['stop_time']        = microtime(true);
			$data['stop_called_from'] = Debugger::getCalledFrom();
		}

		// Add item to collection
		static::add($key, $data);
	}

	/**
	 * @param string|null           $key
	 * @param string|array|\Closure $callback
	 *
	 * @return bool|mixed
	 */
	public static function callback($key = null, $callback) {
		static::init();

		// Start output buffer to capture any output
		ob_start();

		// Get callback parameters
		$callbackParams = array_slice(func_get_args(), 2);

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

				if (count($keyArr) > 1) {
					$method = array_pop($keyArr);
					$key    = implode('/', $keyArr);
					$key .= '::' . $method;
				} else {
					$key = implode('', $keyArr);
				}

				unset($keyArr, $method);
			} elseif (is_object($callback) && $callback instanceof \Closure) {
				$key = 'closure';
			}
			$key = 'callback: ' . $key;
		}

		// Start profiler
		static::start($key);

		// Execute callback, and get result
		$callbackResult = call_user_func_array($callback, $callbackParams);

		// Stop profiler
		static::stop($key);

		// Get and clean output buffer
		$callbackOutput = ob_get_clean();

		// If callback was not found
		if (strpos($callbackOutput, 'call_user_func_array() expects parameter 1 to be a valid callback') !== false) {
			// Show error message
			Debugger::debug('Invalid callback sent to Profiler::callback: ' . str_replace('callback: ', '', $key));

			// Clear the item from the collection
			static::$collection->clear($key);

			// Return false
			return false;
		}

		// Return result of the callback, if given
		if (isset($callbackResult)) {
			return $callbackResult;
		}

		// Return true
		return true;
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
			Debugger::debug($output);
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
			// Sort field (index|key|start|stop|result) (string|boolean)
			'sort'       => 'index',
			// Sort order (asc|desc) (string)
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
		Debugger::debug($output);
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
			'nested'         => true,
			// Prefix for nested items (string)
			'nested_prefix'  => '|-- ',
			// Show in one line (boolean)
			'oneline'        => true,
			// If oneline, max key length (int)
			'oneline_length' => 100
		], $options);

		// If no key is given
		if (is_null($key)) {
			// Get key of the last stopped timer
			$key = static::getLastItemName('stopped');
		}

		// Variable for output
		$output = '';

		if (!static::$collection->exists($key)) {
			// Non-exiting timer
			$output .= 'Unknow timer: ' . $key;
		} else {
			// Get item
			$item = static::$collection->get($key);

			// Get item start time
			$itemStart = (isset($item['start_time']) ? static::formatDateTime($item['start_time']) : 'N/A');

			// Get item stop time
			$itemStop = (isset($item['stop_time']) ? static::formatDateTime($item['stop_time']) : 'N/A');

			// Get item result
			$itemResult = (isset($item['start_value']) && isset($item['stop_value']) ? static::getMetricResult($item['start_value'], $item['stop_value']) : 'N/A');

			// Set output
			if ($options['oneline']) {
				// Prep key for output
				$outputName = '';
				if ($options['nested']) {
					$outputName .= str_repeat($options['nested_prefix'], $item['level']);
				}
				$outputName .= $item['key'];
				if (strlen($outputName) > $options['oneline_length']) {
					$outputName = '~' . substr($item['key'], -($options['oneline_length'] - 1));
				}

				// Add item stats
				$output .= str_pad($outputName, $options['oneline_length'], ' ');
				$output .= ' | ';
				$output .= str_pad(static::getMetricResultFormatted($itemResult), 20, ' ', ($itemResult == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));
				$output .= ' | ';
				$output .= str_pad($itemStart, 19, ' ');
				$output .= ' | ';
				$output .= str_pad($itemStop, 19, ' ');
			} else {
				// Add item stats
				$output .= 'Timer   : ' . $item['key'];
				$output .= "\n";
				$output .= 'Start   : ' . $itemStart;
				$output .= "\n";
				$output .= 'Stop    : ' . $itemStop;
				$output .= "\n";
				$output .= 'Result : ' . static::getMetricResultFormatted($itemResult);

				// Show as nested
				if ($options['nested']) {
					$output = str_repeat($options['nested_prefix'], $item['level']) . $output;
					$output = str_replace("\n", "\n" . str_repeat($options['nested_prefix'], $item['level']), $output);
				}
			}
		}

		// Return output
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

		$items = static::$collection->getAll();

		// Loop throug items reversed and get the last one matching the given type
		$itemKeys = array_reverse(array_keys($items));
		foreach ($itemKeys as $itemKey) {
			if ($type == 'started' && isset($items[$itemKey]['start_time']) === true && isset($items[$itemKey]['stop_time']) === false) {
				$key = $itemKey;
				break;
			}
			if ($type == 'stopped' && isset($items[$itemKey]['start_time']) === true && isset($items[$itemKey]['stop_time']) === true) {
				$key = $itemKey;
				break;
			}
			if ($type == '') {
				$key = $itemKey;
				break;
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
	public static function formatDateTime($unixTimestamp, $showMiliseconds = true) {
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
