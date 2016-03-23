<?php
use Xicrow\Debug\Debugger;

/**
 * Class DebuggerTest
 */
class DebuggerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @inheritdoc
	 */
	public function __construct($name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);

		// Set debugger options
		Debugger::$documentRoot   = 'E:\\GitHub\\';
		Debugger::$showCalledFrom = false;
		Debugger::$output         = false;
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::getDebugInformation
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationNull
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationBoolean
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationInteger
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationDouble
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationIterable
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationArray
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationObject
	 * @covers \Xicrow\Debug\Debugger::getDebugInformationResource
	 */
	public function testGetDebugInformation() {
		// Make sure string is allways returned
		$expected = 'string';
		$result   = Debugger::getDebugInformation('string');
		$this->assertInternalType($expected, $result);

		$expected = 'string';
		$result   = Debugger::getDebugInformation(123);
		$this->assertInternalType($expected, $result);

		// Test all PHP data types
		$expected = 'NULL';
		$result   = Debugger::getDebugInformation(null);
		$this->assertContains($expected, $result);

		$expected = 'TRUE';
		$result   = Debugger::getDebugInformation(true);
		$this->assertEquals($expected, $result);

		$expected = 'FALSE';
		$result   = Debugger::getDebugInformation(false);
		$this->assertEquals($expected, $result);

		$expected = '123';
		$result   = Debugger::getDebugInformation(123);
		$this->assertEquals($expected, $result);

		$expected = '123.123';
		$result   = Debugger::getDebugInformation(123.123);
		$this->assertEquals($expected, $result);

		$expected = '"string"';
		$result   = Debugger::getDebugInformation('string');
		$this->assertEquals($expected, $result);

		$data     = [1, 2, 3];
		$expected = <<<EXPECT
[
	0 => 1,
	1 => 2,
	2 => 3
]
EXPECT;
		$result   = Debugger::getDebugInformation($data);
		$this->assertEquals($expected, $result);

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
		$this->assertEquals($expected, $result);

		$resource = fopen(realpath(__DIR__ . '/../README.md'), 'r');
		$expected = '#Resource id \#[0-9]+ \(stream\)#';
		$result   = Debugger::getDebugInformation($resource);
		$this->assertRegExp($expected, $result);
		fclose($resource);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::getCalledFrom
	 * @covers \Xicrow\Debug\Debugger::getCalledFromTrace
	 */
	public function testGetCalledFrom() {
		$expected = 'string';
		$result   = Debugger::getCalledFrom();
		$this->assertInternalType($expected, $result);

		$expected = 'DebuggerTest->testGetCalledFrom';
		$result   = Debugger::getCalledFrom(1);
		$this->assertContains($expected, $result);

		$expected = 'Unknown trace with index: 99';
		$result   = Debugger::getCalledFrom(99);
		$this->assertContains($expected, $result);

		$expected = 'DebuggerTest.php line 86';
		$result   = Debugger::getCalledFromTrace([
			'file' => __DIR__ . 'DebuggerTest.php',
			'line' => 86
		]);
		$this->assertContains($expected, $result);

		$expected = 'DebuggerTest->testGetCalledFrom';
		$result   = Debugger::getCalledFromTrace([
			'class'    => 'DebuggerTest',
			'type'     => '->',
			'function' => 'testGetCalledFrom'
		]);
		$this->assertContains($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClass
	 * @covers \Xicrow\Debug\Debugger::reflectClassProperty
	 * @covers \Xicrow\Debug\Debugger::reflectClassMethod
	 */
	public function testReflectClass() {
		$result = Debugger::reflectClass('\Xicrow\Debug\Collection');
		$this->assertContains('class Xicrow\Debug\Collection', $result);
		$this->assertContains('private $items', $result);
		$this->assertContains('public function __construct(', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassProperty
	 */
	public function testReflectClassProperty() {
		$result = Debugger::reflectClassProperty('\Xicrow\Debug\Collection', 'items');
		$this->assertContains('@var array', $result);
		$this->assertContains('private $items', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Debugger::reflectClassMethod
	 */
	public function testReflectClassMethod() {
		$result = Debugger::reflectClassMethod('\Xicrow\Debug\Collection', '__construct');
		$this->assertContains('@param array $items', $result);
		$this->assertContains('public function __construct(', $result);
	}
}
