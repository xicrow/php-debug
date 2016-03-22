<?php
namespace Xicrow\Debug;

/**
 * Class Timer
 *
 * @package Xicrow\Debug
 */
class Timer extends Profiler {
	/**
	 * Force the unit to display elapsed times in (MS|S|M|H|D|W)
	 *
	 * @var bool
	 */
	public static $forceDisplayUnit = false;

	/**
	 * @inheritdoc
	 */
	protected function getMetric() {
		// Return current microtime as float
		return microtime(true);
	}

	/**
	 * @inheritdoc
	 */
	protected function getMetricFormatted($metric) {
		// Return formatted metric
		return self::formatMiliseconds($metric, 4, static::$forceDisplayUnit);
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

		// Get result in miliseconds
		$result = (($item['stop_value'] - $item['start_value']) * 1000);

		// Return result
		return $result;
	}

	/**
	 * @inheritdoc
	 */
	protected function getMetricResultFormatted($result) {
		// Return formatted result
		return self::formatMiliseconds($result, 4, static::$forceDisplayUnit);
	}
}
