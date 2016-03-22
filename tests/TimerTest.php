<?php
use Xicrow\Debug\Timer;

/**
 * Class TimerTest
 *
 * Missing tests:
 *
 * @covers \Xicrow\Debug\Timer::start
 * @covers \Xicrow\Debug\Timer::stop
 * @covers \Xicrow\Debug\Timer::custom
 * @covers \Xicrow\Debug\Timer::callback
 * @covers \Xicrow\Debug\Timer::elapsed
 * @covers \Xicrow\Debug\Timer::getStats
 * @covers \Xicrow\Debug\Timer::getLastTimerName
 */
class TimerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\Debug\Timer::init
	 */
	public function testInit() {
		$expected = 'null';
		$result   = Timer::$collection;
		$this->assertInternalType($expected, $result);

		Timer::init();

		$expected = 'object';
		$result   = Timer::$collection;
		$this->assertInternalType($expected, $result);

		$expected = '\Xicrow\Debug\Collection';
		$result   = Timer::$collection;
		$this->assertInstanceOf($expected, $result);
	}
}
