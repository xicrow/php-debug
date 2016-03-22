<?php
use Xicrow\Debug\Timer;

/**
 * Class TimerTest
 */
class TimerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\Debug\Timer::getMetric
	 */
	public function testGetMetric() {
		$expected = 'numeric';
		$result   = Timer::getMetric();
		$this->assertInternalType($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Timer::getMetricFormatted
	 */
	public function testGetMetricFormatted() {
		$expected = '500.0000 MS';
		$result   = Timer::getMetricFormatted(500);
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Timer::getMetricResult
	 */
	public function testGetMetricResult() {
		$expected = 'numeric';
		$result   = Timer::getMetricResult(500, 1000);
		$this->assertInternalType($expected, $result);

		$expected = 500000;
		$result   = Timer::getMetricResult(500, 1000);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Timer::getMetricResultFormatted
	 */
	public function testGetMetricResultFormatted() {
		$expected = '500.0000 MS';
		$result   = Timer::getMetricResultFormatted(500);
		$this->assertContains($expected, $result);
	}
}
