<?php
use Xicrow\Debug\Memory;

/**
 * Class MemoryTest
 */
class MemoryTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\Debug\Memory::getMetric
	 */
	public function testGetMetric() {
		$expected = 'numeric';
		$result   = Memory::getMetric();
		$this->assertInternalType($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Memory::getMetricFormatted
	 */
	public function testGetMetricFormatted() {
		$expected = '500.0000 B';
		$result   = Memory::getMetricFormatted(500);
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Memory::getMetricResult
	 */
	public function testGetMetricResult() {
		$expected = 'numeric';
		$result   = Memory::getMetricResult(500, 1000);
		$this->assertInternalType($expected, $result);

		$expected = 500;
		$result   = Memory::getMetricResult(500, 1000);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Memory::getMetricResultFormatted
	 */
	public function testGetMetricResultFormatted() {
		$expected = '500.0000 B';
		$result   = Memory::getMetricResultFormatted(500);
		$this->assertContains($expected, $result);
	}
}
