<?php
namespace Xicrow\DebugTools;

/**
 * Class Timer
 *
 * @package Xicrow\DebugTools
 */
class Timer {
	/**
	 * @var string|null
	 */
	public static $documentRoot = null;

	/**
	 * @var array
	 */
	public static $defaultOptions = [
		// Options for showAll()
		'showAll'  => [
			// Sort timers or false to disable (index|name|start|stop|elapsed) (string|boolean)
			'sort'       => false,
			// Sort order for timers (asc|desc) (string)
			'sort_order' => false
		],
		// Options for getStats()
		'getStats' => [
			// Include start and stop times (boolean)
			'start_stop'     => true,
			// Show nested (boolean)
			'nested'         => false,
			// Prefix for nested items (string)
			'nested_prefix'  => '|-- ',
			// Show in one line (boolean)
			'oneline'        => false,
			// If oneline, max name length (int)
			'oneline_length' => 50
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
	 * @var array
	 */
	private static $timers = [];

	/**
	 * @param string|null           $name
	 * @param string|array|\Closure $callback
	 *
	 * @return bool|mixed
	 */
	public static function callback($name = null, $callback) {
		// Start output buffer to capture any output
		ob_start();

		// Get callback parameters
		$callbackParams = array_slice(func_get_args(), 2);

		// Get name if no name is given
		if (is_null($name)) {
			if (is_string($callback)) {
				$name = $callback;
			} elseif (is_array($callback)) {
				$nameArr = [];
				foreach ($callback as $k => $v) {
					if (is_string($v)) {
						$nameArr[] = $v;
					} elseif (is_object($v)) {
						$nameArr[] = get_class($v);
					}
				}

				if (count($nameArr) > 1) {
					$method = array_pop($nameArr);
					$name   = implode('/', $nameArr);
					$name .= '::' . $method;
				} else {
					$name = implode('', $nameArr);
				}

				unset($nameArr, $method);
			} elseif (is_object($callback) && $callback instanceof \Closure) {
				$name = 'closure';
			}
		}

		// Start timer
		self::start($name);

		// Execute callback, and get result
		$callbackResult = call_user_func_array($callback, $callbackParams);

		// Stop timer
		self::stop($name);

		// Get and clean output buffer
		$callbackOutput = ob_get_clean();

		// If callback was not found
		if (strpos($callbackOutput, 'call_user_func_array() expects parameter 1 to be a valid callback') !== false) {
			// Show error message
			echo '<pre>';
			echo 'Invalid callback sent to Timer::timeThis:';
			echo "\n";
			echo strip_tags($callbackOutput);
			echo '</pre>';

			// Clear the timer
			self::clear($name);

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
	 * @param string|null $name
	 *
	 * @return bool
	 */
	public static function start($name = null) {
		// Get time
		$time = self::getTime();

		// If no name is given
		if (is_null($name)) {
			// Set name to file and line in backtrace
			$backtrace = debug_backtrace();
			$name      = $backtrace[0]['file'] . ' line ' . $backtrace[0]['line'];
			$name      = str_replace('\\', '/', $name);
			$name      = substr($name, strlen(self::getDocumentRoot()));
			$name      = trim($name, '/');
		}

		// If name is allready in use
		if (isset(self::$timers[$name])) {
			// Set correct name for the original timer
			if (strpos(self::$timers[$name]['name'], '#') === false) {
				self::$timers[$name]['name'] = $name . ' #1';
			}

			// Make sure name is unique
			$originalName = $name;
			$i            = 1;
			while (isset(self::$timers[$name])) {
				$name = $originalName . ' #' . ($i + 1);
				$i++;
			}
		}

		// Get parent timer name
		$parentTimerName = self::getLastStartedTimerName();

		// Get parent timer
		$parentTimer = false;
		if ($parentTimerName) {
			$parentTimer = self::$timers[$parentTimerName];
		}

		// Add timer with start
		self::$timers[$name] = [
			'index'  => count(self::$timers),
			'name'   => $name,
			'parent' => $parentTimerName,
			'level'  => (!$parentTimer ? 0 : ($parentTimer['level'] + 1)),
			'start'  => $time
		];

		// Return true
		return true;
	}

	/**
	 * @param string|null $name
	 *
	 * @return bool
	 */
	public static function stop($name = null) {
		// Get time
		$time = self::getTime();

		// If no name is given
		if (is_null($name)) {
			// Get name of the last started timer
			$name = self::getLastStartedTimerName();
		}

		// Check for name duplicates, and find the first one not stopped
		$originalName = $name;
		$i            = 1;
		while (isset(self::$timers[$name])) {
			if (empty(self::$timers[$name]['stop'])) {
				break;
			}

			$name = $originalName . ' #' . ($i + 1);

			$i++;
		}

		// If no name is given or timer does not exist
		if (!$name || !isset(self::$timers[$name])) {
			// Return false
			return false;
		}

		// Add stop to timer
		self::$timers[$name]['stop'] = $time;

		// Return true
		return true;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function clear($name) {
		// Remove timer
		unset(self::$timers[$name]);

		// Return true
		return true;
	}

	/**
	 * @param string|null $name
	 * @param int|null    $start
	 * @param int|null    $stop
	 *
	 * @return bool
	 */
	public static function add($name = null, $start = null, $stop = null) {
		// If no name is given
		if (is_null($name)) {
			// Set name to file and line in backtrace
			$backtrace = debug_backtrace();
			$name      = $backtrace[0]['file'] . ' line ' . $backtrace[0]['line'];
			if (isset($_SERVER['DOCUMENT_ROOT'])) {
				$name = substr($name, strlen($_SERVER['DOCUMENT_ROOT']));
			}
			$name = str_replace('\\', '/', $name);
			$name = trim($name, '/');
		}

		// If name is allready in use
		if (isset(self::$timers[$name])) {
			// Set correct name for the original timer
			if (strpos(self::$timers[$name]['name'], '#') === false) {
				self::$timers[$name]['name'] = $name . ' #1';
			}

			// Make sure name is unique
			$originalName = $name;
			$i            = 1;
			while (isset(self::$timers[$name])) {
				$name = $originalName . ' #' . ($i + 1);
				$i++;
			}
		}

		// Add timer
		self::$timers[$name] = [
			'index'  => count(self::$timers),
			'name'   => $name,
			'parent' => false,
			'level'  => 0
		];

		// Add start for the timer, if given
		if (!is_null($start)) {
			self::$timers[$name]['start'] = $start;
		}

		// Add stop for the timer, if given
		if (!is_null($stop)) {
			self::$timers[$name]['stop'] = $stop;
		}

		// Return true
		return true;
	}

	/**
	 * @param string|null $name
	 * @param array       $options
	 *
	 * @return float|int
	 */
	public static function elapsed($name = null, $options = []) {
		// Merge options with default options
		$options = array_merge(self::$defaultOptions['elapsed'], $options);

		// If no name is given
		if (is_null($name)) {
			// Get name of the last stopped timer
			$name = self::getLastStoppedTimerName();
		}

		// If no name is given or timer does not exist or have start and stop
		if (!$name || !isset(self::$timers[$name]['start']) || !isset(self::$timers[$name]['stop'])) {
			// Return 0 (zero)
			return 0;
		}

		// Get elapsed time
		$elapsed = (self::$timers[$name]['stop'] - self::$timers[$name]['start']);

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
	 * @param string|null $name
	 * @param array       $options
	 */
	public static function show($name = null, $options = []) {
		// Output HTML ?
		$html = true;
		if (php_sapi_name() == 'cli') {
			$html = false;
		}

		if ($html) {
			echo '<pre>';
		}

		echo self::getStats($name, $options);

		if ($html) {
			echo '</pre>';
		}
	}

	/**
	 * @param array $options
	 */
	public static function showAll($options = []) {
		// Merge options with default options
		$options = array_merge(self::$defaultOptions['showAll'], $options);

		// Available sort options
		$sortOptions = ['index', 'name', 'start', 'stop', 'elapsed'];

		// Get copy of timers
		$timers = self::$timers;

		if (is_string($options['sort']) && in_array($options['sort'], $sortOptions)) {
			// Add elapsed to timers if needed
			if ($options['sort'] == 'elapsed') {
				foreach ($timers as $name => $timer) {
					$timers[$name]['elapsed'] = self::elapsed($name, ['format' => false]);
				}
			}

			// Sort timers
			uasort($timers, function ($a, $b) use ($options) {
				$aValue = 0;
				if (isset($a[$options['sort']])) {
					$aValue = $a[$options['sort']];
				}

				$bValue = 0;
				if (isset($a[$options['sort']])) {
					$bValue = $b[$options['sort']];
				}

				if ($aValue == $bValue) {
					return 0;
				}

				return ($aValue < $bValue) ? -1 : 1;
			});

			// If sorting should be reversed
			if (strtolower($options['sort_order']) == 'desc') {
				$timers = array_reverse($timers);
			}
		}

		// Get list of timer names
		$timerNames = array_keys($timers);
		unset($timers);

		// Output timers
		foreach ($timerNames as $timerName) {
			self::show($timerName, $options);
		}
	}

	/**
	 * @param string|null $name
	 * @param array       $options
	 *
	 * @return string
	 */
	public static function getStats($name = null, $options = []) {
		// Merge options with default options
		$options = array_merge(self::$defaultOptions['getStats'], $options);

		// If no name is given
		if (is_null($name)) {
			// Get name of the last stopped timer
			$name = self::getLastStoppedTimerName();
		}

		// Variable for output
		$output = '';

		if (!$name) {
			// No name given
			$output .= 'No timer name given';
		} elseif (!isset(self::$timers[$name])) {
			// Non-exiting timer
			$output .= 'Unknow timer: ' . $name;
		} else {
			// Get timer
			$timer = self::$timers[$name];

			// Get timer start
			$timerStart = 'N/A';
			if (isset($timer['start'])) {
				$timerStart = date('Y-m-d H:i:s', $timer['start']);
			}

			// Get timer stop
			$timerStop = 'N/A';
			if (isset($timer['stop'])) {
				$timerStop = date('Y-m-d H:i:s', $timer['stop']);
			}

			// Get timer elapsed
			$timerElapsed = 'N/A';
			if (isset($timer['start']) && isset($timer['stop'])) {
				$timerElapsed = self::elapsed($name, $options);
				if (isset($options['miliseconds'])) {
					$timerElapsed .= ($options['miliseconds'] ? ' ms.' : ' sec');
				} else {
					$timerElapsed .= (self::$defaultOptions['elapsed']['miliseconds'] ? ' ms.' : ' sec');
				}
			}

			// Set output
			if ($options['oneline']) {
				// Prep name for output
				$outputName = '';
				if ($options['nested']) {
					$outputName .= str_repeat($options['nested_prefix'], $timer['level']);
				}
				$outputName .= $timer['name'];
				if (strlen($outputName) > $options['oneline_length']) {
					$outputName = '~' . substr($timer['name'], -($options['oneline_length'] - 1));
				}

				// Add timer stats
				$output .= str_pad($outputName, $options['oneline_length'], ' ');
				$output .= ' | ';
				$output .= str_pad($timerElapsed, 20, ' ', ($timerElapsed == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));
				if ($options['start_stop']) {
					$output .= ' | ';
					$output .= str_pad($timerStart, 19, ' ');
					$output .= ' | ';
					$output .= str_pad($timerStop, 19, ' ');
				}
			} else {
				// Add timer stats
				$output .= 'Timer   : ' . $timer['name'];
				if ($options['start_stop']) {
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
	 * @return array
	 */
	public static function getTimers() {
		return self::$timers;
	}

	/**
	 * @return float
	 */
	private static function getTime() {
		// Return current time
		return microtime(true);
	}

	/**
	 * @return string
	 */
	private static function getDocumentRoot() {
		if (is_null(self::$documentRoot)) {
			$documentRoot = '';

			if (isset($_SERVER['DOCUMENT_ROOT'])) {
				$documentRoot = $_SERVER['DOCUMENT_ROOT'];
			}

			self::$documentRoot = $documentRoot;
		}

		return str_replace('\\', '/', self::$documentRoot);
	}

	/**
	 * @return bool|string
	 */
	private static function getLastStartedTimerName() {
		// Set default name
		$name = false;

		// Loop throug timers reversed and get the last one with a start and no stop
		$timerNames = array_reverse(array_keys(self::$timers));
		foreach ($timerNames as $timerName) {
			if (isset(self::$timers[$timerName]['start']) && !isset(self::$timers[$timerName]['stop'])) {
				$name = $timerName;
				break;
			}
		}

		// Return the name
		return $name;
	}

	/**
	 * @return bool|string
	 */
	private static function getLastStoppedTimerName() {
		// Set default name
		$name = false;

		// Loop throug timers reversed and get the last one with a start and a stop
		$timerNames = array_reverse(array_keys(self::$timers));
		foreach ($timerNames as $timerName) {
			if (isset(self::$timers[$timerName]['start']) && isset(self::$timers[$timerName]['stop'])) {
				$name = $timerName;
				break;
			}
		}

		// Return the name
		return $name;
	}
}
