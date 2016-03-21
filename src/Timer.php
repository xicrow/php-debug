<?php
namespace Xicrow\Debug;

/**
 * Class Timer
 *
 * @package Xicrow\Debug
 */
class Timer {
	/**
	 * @var array
	 */
	public static $options = [
		// Options for showAll()
		'showAll'  => [
			// Sort timers or false to disable (index|key|start|stop|elapsed) (string|boolean)
			'sort'       => false,
			// Sort order for timers (asc|desc) (string)
			'sort_order' => false
		],
		// Options for getStats()
		'getStats' => [
			// Show timestamp (boolean)
			'timestamp'      => true,
			// Show nested (boolean)
			'nested'         => true,
			// Prefix for nested items (string)
			'nested_prefix'  => '|-- ',
			// Show in one line (boolean)
			'oneline'        => true,
			// If oneline, max key length (int)
			'oneline_length' => 100
		],
		// Options for elapsed()
		'elapsed'  => [
			// Show times in miliseconds instead of seconds (boolean)
			'miliseconds' => true,
			// Precision of the time returned (int)
			'precision'   => 2,
			// Format the time (boolean)
			'format'      => true
		]
	];

	/**
	 * @var Collection
	 */
	public static $collection = null;

	/**
	 *
	 */
	public static function init() {
		if (is_null(self::$collection)) {
			self::$collection = new Collection();
		}
	}

	/**
	 * @param string|null $key
	 * @param array       $data
	 */
	private static function add($key = null, $data = []) {
		self::init();

		// If no key is given
		if (is_null($key)) {
			// Set key to file and line
			$key = Debugger::getCalledFileAndLine(2);
		}

		// Make sure parent is set
		if (!isset($data['parent'])) {
			$data['parent'] = self::getLastTimerName('started');
		}

		// Make sure level is set
		if (!isset($data['level'])) {
			$data['level'] = 0;
			if (isset($data['parent']) && $parentTimer = self::$collection->get($data['parent'])) {
				$data['level'] = ($parentTimer['level'] + 1);
			}
		}

		// Add timer
		self::$collection->add($key, $data);
	}

	/**
	 * @param string|null $key
	 *
	 * @return bool
	 */
	public static function start($key = null) {
		// Get time
		$time = microtime(true);

		self::init();

		// Add new time
		self::add($key, [
			'start'     => $time,
			'start_pos' => Debugger::getCalledFileAndLine()
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
		// Get time
		$time = microtime(true);

		self::init();

		// If no key is given
		if (is_null($key)) {
			// Get key of the last started timer
			$key = self::getLastTimerName('started');
		}

		// Check for key duplicates, and find the first one not stopped
		$originalName = $key;
		$i            = 1;
		while (self::$collection->exists($key)) {
			if (empty(self::$collection->get($key)['stop'])) {
				break;
			}

			$key = $originalName . ' #' . ($i + 1);

			$i++;
		}

		// If timer exists
		if (self::$collection->exists($key)) {
			// Update the timer
			self::$collection->update($key, [
				'stop'     => $time,
				'stop_pos' => Debugger::getCalledFileAndLine()
			]);
		}
	}

	/**
	 * @param string|null $key
	 * @param int|null    $start
	 * @param int|null    $stop
	 *
	 * @return bool
	 */
	public static function custom($key = null, $start = null, $stop = null) {
		self::init();

		// Set data for the timer
		$data = [];
		if (!is_null($start)) {
			$data['start']     = $start;
			$data['start_pos'] = Debugger::getCalledFileAndLine();
		}
		if (!is_null($stop)) {
			$data['stop']     = $stop;
			$data['stop_pos'] = Debugger::getCalledFileAndLine();
		}

		// Add timer
		self::add($key, $data);
	}

	/**
	 * @param string|null           $key
	 * @param string|array|\Closure $callback
	 *
	 * @return bool|mixed
	 */
	public static function callback($key = null, $callback) {
		self::init();

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

		// Start timer
		self::start($key);

		// Execute callback, and get result
		$callbackResult = call_user_func_array($callback, $callbackParams);

		// Stop timer
		self::stop($key);

		// Get and clean output buffer
		$callbackOutput = ob_get_clean();

		// If callback was not found
		if (strpos($callbackOutput, 'call_user_func_array() expects parameter 1 to be a valid callback') !== false) {
			// Show error message
			Debugger::debug('Invalid callback sent to Timer::callback: ' . str_replace('callback: ', '', $key));

			// Clear the timer
			self::$collection->clear($key);

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
	 * @return float|int
	 */
	public static function elapsed($key = null, $options = []) {
		self::init();

		// Merge options with default options
		$options = array_merge(self::$options['elapsed'], $options);

		// If no key is given
		if (is_null($key)) {
			// Get key of the last stopped timer
			$key = self::getLastTimerName('stopped');
		}

		// If timer does not exist
		if (!self::$collection->exists($key)) {
			return 0;
		}

		// Get timer
		$timer = self::$collection->get($key);

		// If timer is not started
		if (!isset($timer['start'])) {
			return 0;
		}

		// If timer is not stopped
		if (!isset($timer['stop'])) {
			// Stop the timer
			self::stop($key);

			// Get timer again
			$timer = self::$collection->get($key);
		}

		// Get elapsed time
		$elapsed = ($timer['stop'] - $timer['start']);

		if ($options['miliseconds']) {
			$elapsed = ($elapsed * 1000);
		}

		// Fixed decimals
		$elapsed = sprintf('%0.' . $options['precision'] . 'f', $elapsed);

		// Formatting
		if ($options['format']) {
			$elapsed = number_format($elapsed, $options['precision'], '.', ',');
		}

		// Return elapsed time
		return $elapsed;
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @return bool
	 */
	public static function show($key = null, $options = []) {
		self::init();

		$output = self::getStats($key, $options);

		if (!empty($output)) {
			Debugger::debug($output);
		}

		return true;
	}

	/**
	 * @param array $options
	 *
	 * @return bool
	 */
	public static function showAll($options = []) {
		self::init();

		// Merge options with default options
		$options = array_merge(self::$options['showAll'], $options);

		// Available sort options
		$sortOptions = ['index', 'key', 'start', 'stop', 'elapsed'];

		// Get copy of collection
		$collection = clone self::$collection;

		// Add elapsed to timers if needed
		foreach ($collection as $key => $item) {
			if (!isset($item['elapsed'])) {
				$collection->update($key, ['elapsed' => self::elapsed($key, ['format' => false])]);
			}
		}

		// If valid sort option is given
		if (is_string($options['sort']) && in_array($options['sort'], $sortOptions)) {
			// Sort collection
			$collection->sort($options['sort'], $options['sort_order']);
		}

		// Output timers
		$output = '';
		foreach ($collection as $key => $item) {
			if (!empty($output)) {
				$output .= "\n";
			}
			$output .= self::getStats($key, $options);
		}
		Debugger::debug($output);

		return true;
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @return string
	 */
	public static function getStats($key = null, $options = []) {
		self::init();

		// Merge options with default options
		$options = array_merge(self::$options['getStats'], $options);

		// If no key is given
		if (is_null($key)) {
			// Get key of the last stopped timer
			$key = self::getLastTimerName('stopped');
		}

		// Variable for output
		$output = '';

		if (!self::$collection->exists($key)) {
			// Non-exiting timer
			$output .= 'Unknow timer: ' . $key;
		} else {
			// Get timer
			$timer = self::$collection->get($key);

			// Get timer start
			$timerStart = 'N/A';
			if (isset($timer['start'])) {
				$timerStart  = date('Y-m-d H:i:s', $timer['start']);
				$miliseconds = 0;
				if (strpos($timer['start'], '.') !== false) {
					$miliseconds = substr($timer['start'], (strpos($timer['start'], '.') + 1));
				}
				$timerStart .= '.' . str_pad($miliseconds, 4, '0', STR_PAD_RIGHT);
			}

			// Get timer stop
			$timerStop = 'N/A';
			if (isset($timer['stop'])) {
				$timerStop   = date('Y-m-d H:i:s', $timer['stop']);
				$miliseconds = 0;
				if (strpos($timer['stop'], '.') !== false) {
					$miliseconds = substr($timer['stop'], (strpos($timer['stop'], '.') + 1));
				}
				$timerStop .= '.' . str_pad($miliseconds, 4, '0', STR_PAD_RIGHT);
			}

			// Get timer elapsed
			$timerElapsed = 'N/A';
			if (isset($timer['start']) && isset($timer['stop'])) {
				$timerElapsed = self::elapsed($key, $options);
				if (isset($options['miliseconds'])) {
					$timerElapsed .= ($options['miliseconds'] ? ' ms.' : ' sec');
				} else {
					$timerElapsed .= (self::$options['elapsed']['miliseconds'] ? ' ms.' : ' sec');
				}
			}

			// Set output
			if ($options['oneline']) {
				// Prep key for output
				$outputName = '';
				if ($options['nested']) {
					$outputName .= str_repeat($options['nested_prefix'], $timer['level']);
				}
				$outputName .= $timer['key'];
				if (strlen($outputName) > $options['oneline_length']) {
					$outputName = '~' . substr($timer['key'], -($options['oneline_length'] - 1));
				}

				// Add timer stats
				$output .= str_pad($outputName, $options['oneline_length'], ' ');
				$output .= ' | ';
				$output .= str_pad($timerElapsed, 20, ' ', ($timerElapsed == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));
				if ($options['timestamp']) {
					$output .= ' | ';
					$output .= str_pad($timerStart, 19, ' ');
					$output .= ' | ';
					$output .= str_pad($timerStop, 19, ' ');
				}
			} else {
				// Add timer stats
				$output .= 'Timer   : ' . $timer['key'];
				if ($options['timestamp']) {
					$output .= "\n";
					$output .= 'Start   : ' . $timerStart;
					$output .= "\n";
					$output .= 'Stop    : ' . $timerStop;
				}
				$output .= "\n";
				$output .= 'Elapsed : ' . $timerElapsed;

				// Show as nested
				if ($options['nested']) {
					$output = str_repeat($options['nested_prefix'], $timer['level']) . $output;
					$output = str_replace("\n", "\n" . str_repeat($options['nested_prefix'], $timer['level']), $output);
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
	public static function getLastTimerName($type = '') {
		self::init();

		// Set default key
		$key = false;

		$timers = self::$collection->getAll();

		// Loop throug timers reversed and get the last one with a start and no stop
		$timerKeys = array_reverse(array_keys($timers));
		foreach ($timerKeys as $timerKey) {
			if ($type == 'started' && isset($timers[$timerKey]['start']) && !isset($timers[$timerKey]['stop'])) {
				$key = $timerKey;
				break;
			}
			if ($type == 'stopped' && isset($timers[$timerKey]['start']) && isset($timers[$timerKey]['stop'])) {
				$key = $timerKey;
				break;
			}
			if ($type == '') {
				$key = $timerKey;
				break;
			}
		}

		// Return the key
		return $key;
	}
}
