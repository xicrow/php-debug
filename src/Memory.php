<?php
namespace Xicrow\Debug;

/**
 * Class Memory
 *
 * @package Xicrow\Debug
 */
class Memory {
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
			$key = Debugger::getCalledFrom(2);
		}

		// Make sure parent is set
		if (!isset($data['parent'])) {
			$data['parent'] = self::getLastMemoryName('started');
		}

		// Make sure level is set
		if (!isset($data['level'])) {
			$data['level'] = 0;
			if (isset($data['parent']) && $parentMemory = self::$collection->get($data['parent'])) {
				$data['level'] = ($parentMemory['level'] + 1);
			}
		}

		// Add memory
		self::$collection->add($key, $data);
	}

	/**
	 * @param string|null $key
	 *
	 * @return bool
	 */
	public static function start($key = null) {
		// Get memory usage
		$memory = memory_get_usage();

		self::init();

		// Add new memory
		self::add($key, [
			'start'     => $memory,
			'start_pos' => Debugger::getCalledFrom()
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
		// Get memory usage
		$memory = memory_get_usage();

		self::init();

		// If no key is given
		if (is_null($key)) {
			// Get key of the last started memory
			$key = self::getLastMemoryName('started');
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

		// If memory exists
		if (self::$collection->exists($key)) {
			// Update the memory
			self::$collection->update($key, [
				'stop'     => $memory,
				'stop_pos' => Debugger::getCalledFrom()
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

		// Set data for the memory
		$data = [];
		if (!is_null($start)) {
			$data['start']     = $start;
			$data['start_pos'] = Debugger::getCalledFrom();
		}
		if (!is_null($stop)) {
			$data['stop']     = $stop;
			$data['stop_pos'] = Debugger::getCalledFrom();
		}

		// Add memory
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

		// Start memory
		self::start($key);

		// Execute callback, and get result
		$callbackResult = call_user_func_array($callback, $callbackParams);

		// Stop memory
		self::stop($key);

		// Get and clean output buffer
		$callbackOutput = ob_get_clean();

		// If callback was not found
		if (strpos($callbackOutput, 'call_user_func_array() expects parameter 1 to be a valid callback') !== false) {
			// Show error message
			Debugger::debug('Invalid callback sent to Memory::callback: ' . str_replace('callback: ', '', $key));

			// Clear the memory
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
	public static function usage($key = null, $options = []) {
		self::init();

		// Merge options with default options
		$options = array_merge([
			// Size to show memory in (B|KB|MB|GB)
			'size'      => 'B',
			// Precision of the memory usage returned (int)
			'precision' => 2,
			// Format the memory usage (boolean)
			'format'    => true
		], $options);

		// If no key is given
		if (is_null($key)) {
			// Get key of the last stopped memory
			$key = self::getLastMemoryName('stopped');
		}

		// If memory does not exist
		if (!self::$collection->exists($key)) {
			return 0;
		}

		// Get memory
		$memory = self::$collection->get($key);

		// If memory is not started
		if (!isset($memory['start'])) {
			return 0;
		}

		// If memory is not stopped
		if (!isset($memory['stop'])) {
			// Stop the memory
			self::stop($key);

			// Get memory again
			$memory = self::$collection->get($key);
		}

		// Get usage
		$usage = ($memory['stop'] - $memory['start']);

		if ($options['size'] != 'B') {
			switch (strtoupper($options['size'])) {
				case 'KB':
					$usage /= 1024;
				break;
				case 'MB':
					$usage /= (1024 * 1024);
				break;
				case 'GB':
					$usage /= (1024 * 1024 * 1024);
				break;
			}
		}

		// Fixed decimals
		$usage = sprintf('%0.' . $options['precision'] . 'f', $usage);

		// Formatting
		if ($options['format']) {
			$usage = number_format($usage, $options['precision'], '.', ',');
		}

		// Return usage
		return $usage;
	}

	/**
	 * @param string|null $key
	 * @param array       $options
	 *
	 * @return bool
	 *
	 * @codeCoverageIgnore
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
	 *
	 * @codeCoverageIgnore
	 */
	public static function showAll($options = []) {
		self::init();

		// Merge options with default options
		$options = array_merge([
			// Sort memory or false to disable (index|key|start|stop|usage) (string|boolean)
			'sort'       => false,
			// Sort order for memory (asc|desc) (string)
			'sort_order' => false
		], $options);

		// Available sort options
		$sortOptions = ['index', 'key', 'start', 'stop', 'usage'];

		// Get copy of collection
		$collection = clone self::$collection;

		// Add usage to memory if needed
		foreach ($collection as $key => $item) {
			if (!isset($item['usage'])) {
				$collection->update($key, ['usage' => self::usage($key, ['format' => false])]);
			}
		}

		// If valid sort option is given
		if (is_string($options['sort']) && in_array($options['sort'], $sortOptions)) {
			// Sort collection
			$collection->sort($options['sort'], $options['sort_order']);
		}

		// Output memory usage
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
		$options = array_merge([
			// Size to show memory in (B|KB|MB|GB)
			'size'           => 'B',
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
			// Get key of the last stopped memory
			$key = self::getLastMemoryName('stopped');
		}

		// Variable for output
		$output = '';

		if (!self::$collection->exists($key)) {
			// Non-exiting memory
			$output .= 'Unknow memory: ' . $key;
		} else {
			// Get memory
			$memory = self::$collection->get($key);

			// Get memory usage
			$memoryUsage = 'N/A';
			if (isset($memory['start']) && isset($memory['stop'])) {
				$memoryUsage = self::usage($key, $options) . ' ' . $options['size'];
			}

			// Set output
			if ($options['oneline']) {
				// Prep key for output
				$outputName = '';
				if ($options['nested']) {
					$outputName .= str_repeat($options['nested_prefix'], $memory['level']);
				}
				$outputName .= $memory['key'];
				if (strlen($outputName) > $options['oneline_length']) {
					$outputName = '~' . substr($memory['key'], -($options['oneline_length'] - 1));
				}

				// Add memory stats
				$output .= str_pad($outputName, $options['oneline_length'], ' ');
				$output .= ' | ';
				$output .= str_pad($memoryUsage, 20, ' ', ($memoryUsage == 'N/A' ? STR_PAD_RIGHT : STR_PAD_LEFT));
			} else {
				// Add memory stats
				$output .= 'Memory   : ' . $memory['key'];
				$output .= "\n";
				$output .= 'Usage : ' . $memoryUsage;

				// Show as nested
				if ($options['nested']) {
					$output = str_repeat($options['nested_prefix'], $memory['level']) . $output;
					$output = str_replace("\n", "\n" . str_repeat($options['nested_prefix'], $memory['level']), $output);
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
	public static function getLastMemoryName($type = '') {
		self::init();

		// Set default key
		$key = false;

		$memoryList = self::$collection->getAll();

		// Loop throug list of memory reversed and get the last one with a start and no stop
		$memoryKeys = array_reverse(array_keys($memoryList));
		foreach ($memoryKeys as $memoryKey) {
			if ($type == 'started' && isset($memoryList[$memoryKey]['start']) && !isset($memoryList[$memoryKey]['stop'])) {
				$key = $memoryKey;
				break;
			}
			if ($type == 'stopped' && isset($memoryList[$memoryKey]['start']) && isset($memoryList[$memoryKey]['stop'])) {
				$key = $memoryKey;
				break;
			}
			if ($type == '') {
				$key = $memoryKey;
				break;
			}
		}

		// Return the key
		return $key;
	}
}
