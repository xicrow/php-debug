<?php
use Xicrow\Debug\Memory;

/**
 * Class MemoryTest
 *
 * Missing tests:
 *
 * @covers \Xicrow\Debug\Memory::start
 * @covers \Xicrow\Debug\Memory::stop
 * @covers \Xicrow\Debug\Memory::custom
 * @covers \Xicrow\Debug\Memory::callback
 * @covers \Xicrow\Debug\Memory::usage
 * @covers \Xicrow\Debug\Memory::getStats
 * @covers \Xicrow\Debug\Memory::getLastMemoryName
 */
class MemoryTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\Debug\Memory::init
	 */
	public function testInit() {
		$expected = 'null';
		$result   = Memory::$collection;
		$this->assertInternalType($expected, $result);

		Memory::init();

		$expected = 'object';
		$result   = Memory::$collection;
		$this->assertInternalType($expected, $result);

		$expected = '\Xicrow\Debug\Collection';
		$result   = Memory::$collection;
		$this->assertInstanceOf($expected, $result);
	}
}
