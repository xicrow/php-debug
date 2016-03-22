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

		$expected = 'NULL';
		$result   = Debugger::getDebugInformation(null);
		$this->assertContains($expected, $result);

		$expected = 'FALSE';
		$result   = Debugger::getDebugInformation(false);
		$this->assertContains($expected, $result);

		$expected = '123';
		$result   = Debugger::getDebugInformation(123);
		$this->assertContains($expected, $result);

		$expected = '123.123';
		$result   = Debugger::getDebugInformation(123.123);
		$this->assertContains($expected, $result);

		$expected = 'string';
		$result   = Debugger::getDebugInformation('string');
		$this->assertContains($expected, $result);

		$data     = [1, 2, 3];
		$expected = <<<EXPECT
[
	0 => 1,
	1 => 2,
	2 => 3
]
EXPECT;
		$result   = Debugger::getDebugInformation($data);
		$this->assertContains($expected, $result);

		$data = new DateTime();
		$data->setTimezone(new DateTimeZone('Europe/Copenhagen'));
		$data->setDate(2016, 01, 01);
		$data->setTime(00, 00, 00);
		$expected = <<<EXPECT
DateTime {
	"date"          => "2016-01-01 00:00:00.000000",
	"timezone_type" => 3,
	"timezone"      => "Europe/Copenhagen"
}
EXPECT;
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
		$expected = 'class Xicrow\Debug\Debugger';
		$result   = Debugger::reflectClass('\Xicrow\Debug\Debugger');
		$this->assertContains($expected, $result);

		$expected = 'public static $output';
		$result   = Debugger::reflectClass('\Xicrow\Debug\Debugger');
		$this->assertContains($expected, $result);

		$expected = 'public static function debug(';
		$result   = Debugger::reflectClass('\Xicrow\Debug\Debugger');
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassProperty
	 */
	public function testReflectClassProperty() {
		$expected = '@var bool';
		$result   = Debugger::reflectClassProperty('\Xicrow\Debug\Debugger', 'output');
		$this->assertContains($expected, $result);

		$expected = 'public static $output';
		$result   = Debugger::reflectClassProperty('\Xicrow\Debug\Debugger', 'output');
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassMethod
	 */
	public function testReflectClassMethod() {
		$expected = '@param mixed $data';
		$result   = Debugger::reflectClassMethod('\Xicrow\Debug\Debugger', 'debug');
		$this->assertContains($expected, $result);

		$expected = 'public static function debug(';
		$result   = Debugger::reflectClassMethod('\Xicrow\Debug\Debugger', 'debug');
		$this->assertContains($expected, $result);
	}
}
