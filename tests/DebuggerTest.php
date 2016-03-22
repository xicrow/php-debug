<?php
use Xicrow\Debug\Debugger;

/**
 * Class DebuggerTest
 */
class DebuggerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::getDebugInformation
	 */
	public function testGetDebugInformation() {
		$expected = 'string';
		$result   = Debugger::getDebugInformation('string');
		$this->assertInternalType($expected, $result);

		$expected = 'string';
		$result   = Debugger::getDebugInformation(123);
		$this->assertInternalType($expected, $result);

		$expected = 'null';
		$result   = Debugger::getDebugInformation(null);
		$this->assertContains($expected, $result, '', true);

		$expected = 'false';
		$result   = Debugger::getDebugInformation(false);
		$this->assertContains($expected, $result, '', true);

		$expected = 'int(123)';
		$result   = Debugger::getDebugInformation((int) 123);
		$this->assertContains($expected, $result);

		$expected = 'float(123.123)';
		$result   = Debugger::getDebugInformation((float) 123.123);
		$this->assertContains($expected, $result);

		$expected = 'string';
		$result   = Debugger::getDebugInformation('string');
		$this->assertContains($expected, $result);

		$data     = [1, 2, 3];
		$expected = print_r($data, true);
		$result   = Debugger::getDebugInformation($data);
		$this->assertContains($expected, $result);

		$data     = new DateTime();
		$expected = print_r($data, true);
		$result   = Debugger::getDebugInformation($data);
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::getCalledFrom
	 */
	public function testGetCalledFrom() {
		$expected = 'string';
		$result   = Debugger::getCalledFrom();
		$this->assertInternalType($expected, $result);

		$expected = 'DebuggerTest->testGetCalledFrom';
		$result   = Debugger::getCalledFrom();
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClass
	 */
	public function testReflectClass() {
		$result = Debugger::reflectClass('\Xicrow\Debug\Debugger');
		$this->assertContains('class Xicrow\Debug\Debugger', $result);
		$this->assertContains('public static $output', $result);
		$this->assertContains('private static function output(', $result);
		$this->assertContains('public static function debug(', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassProperty
	 */
	public function testReflectClassProperty() {
		$result = Debugger::reflectClassProperty('\Xicrow\Debug\Debugger', 'output');
		$this->assertContains('@var bool', $result);
		$this->assertContains('public static $output', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassMethod
	 */
	public function testReflectClassMethod() {
		$result = Debugger::reflectClassMethod('\Xicrow\Debug\Debugger', 'debug');
		$this->assertContains('@param mixed $data', $result);
		$this->assertContains('public static function debug(', $result);
	}
}
