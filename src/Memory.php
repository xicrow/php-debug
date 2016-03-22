<?php
namespace Xicrow\Debug;

/**
 * Class Memory
 *
 * @package Xicrow\Debug
 */
class Memory extends Profiler {
	/**
	 * Force the unit to display memory usage in (B|KB|MB|GB|TB|PB)
	 *
	 * @var bool
	 */
	public static $forceDisplayUnit = false;

	/**
	 * @inheritdoc
	 */
	protected function getMetric() {
		// Return current memory usage
		return memory_get_usage();
	}

	/**
	 * @inheritdoc
	 */
	protected function getMetricFormatted($metric) {
		// Return formatted metric
		return self::formatBytes($metric, 4, static::$forceDisplayUnit);
	}

	/**
	 * @inheritdoc
	 */
	protected function getMetricResult($key) {
		// Get item
		$item = self::$collection->get($key);

		// If item is not started
		if ($item === false || !isset($item['start_value']) || !isset($item['stop_value'])) {
			return 0;
		}

		// Get result
		$result = ($item['stop_value'] - $item['start_value']);

		// Return result
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	protected function getMetricResultFormatted($result) {
		// Return formatted result
		return self::formatBytes($result, 4, static::$forceDisplayUnit);
	}
}
