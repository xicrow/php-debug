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
	public function getMetric() {
		// Return current memory usage
		return memory_get_usage();
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricFormatted($metric) {
		// Return formatted metric
		return self::formatBytes($metric, 4, self::$forceDisplayUnit);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricResult($start, $stop) {
		// Return result in bytes
		return ($stop - $start);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricResultFormatted($result) {
		// Return formatted result
		return self::formatBytes($result, 4, self::$forceDisplayUnit);
	}
}
