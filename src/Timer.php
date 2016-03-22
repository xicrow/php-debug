<?php
namespace Xicrow\Debug;

/**
 * Class Timer
 *
 * @package Xicrow\Debug
 * @codeCoverageIgnore
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
	public function getMetric() {
		// Return current microtime as float
		return microtime(true);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricFormatted($metric) {
		// Return formatted metric
		return self::formatMiliseconds($metric, 4, self::$forceDisplayUnit);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricResult($start, $stop) {
		// Return result in miliseconds
		return (($stop - $start) * 1000);
	}

	/**
	 * @inheritdoc
	 */
	public function getMetricResultFormatted($result) {
		// Return formatted result
		return self::formatMiliseconds($result, 4, self::$forceDisplayUnit);
	}
}
