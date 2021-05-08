<?php
/** @noinspection PhpUnused */
/** @noinspection PhpUnusedPrivateFieldInspection */
/** @noinspection PhpUnusedPrivateMethodInspection */
namespace Xicrow\PhpDebug\Test;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Xicrow\PhpDebug\Debugger;

/**
 * Class DebuggerTest
 */
class DebuggerTest extends TestCase
{
	/**
	 * @inheritdoc
	 */
	public function __construct($name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);

		// Set debugger options
		Debugger::$documentRoot   = realpath('.');
		Debugger::$showCalledFrom = false;
		Debugger::$output         = false;
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformation
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationString
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationNull
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationBoolean
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationInteger
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationDouble
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationArray
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationObject
	 * @covers \Xicrow\PhpDebug\Debugger::getDebugInformationResource
	 */
	public function testGetDebugInformation()
	{
		// Make sure string is allways returned
		$result   = Debugger::getDebugInformation('string');
		self::assertIsString($result);

		$result   = Debugger::getDebugInformation(123);
		self::assertIsString($result);

		// Test all PHP data types
		$expected = 'NULL';
		$result   = Debugger::getDebugInformation(null);
		self::assertEquals($expected, $result);

		$expected = 'TRUE';
		$result   = Debugger::getDebugInformation(true);
		self::assertEquals($expected, $result);

		$expected = 'FALSE';
		$result   = Debugger::getDebugInformation(false);
		self::assertEquals($expected, $result);

		$expected = '123';
		$result   = Debugger::getDebugInformation(123);
		self::assertEquals($expected, $result);

		$expected = '123.123';
		$result   = Debugger::getDebugInformation(123.123);
		self::assertEquals($expected, $result);

		$expected = '"string"';
		$result   = Debugger::getDebugInformation('string');
		self::assertEquals($expected, $result);

		$data     = [1, 2, 3];
		$expected = '[';
		$expected .= "\n";
		$expected .= '	0 => 1,';
		$expected .= "\n";
		$expected .= '	1 => 2,';
		$expected .= "\n";
		$expected .= '	2 => 3';
		$expected .= "\n";
		$expected .= ']';
		$result   = Debugger::getDebugInformation($data);
		self::assertEquals($expected, $result);

		$data = new DateTime();
		$data->setTimezone(new DateTimeZone('Europe/Copenhagen'));
		$data->setDate(2016, 01, 01);
		$data->setTime(00, 00, 00);
		$expected = 'object(DateTime) {'."\n";
		$expected .= "\t\n";
		$expected .= '}';
		$result   = Debugger::getDebugInformation($data);
		self::assertEquals($expected, $result);

		$resource = fopen(realpath(__DIR__ . '/../README.md'), 'r');
		$expected = '#Resource id \#[0-9]+ \(stream\)#';
		$result   = Debugger::getDebugInformation($resource);
		self::assertMatchesRegularExpression($expected, $result);
		fclose($resource);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::getCalledFrom
	 * @covers \Xicrow\PhpDebug\Debugger::getCalledFromTrace
	 */
	public function testGetCalledFrom()
	{
		$result   = Debugger::getCalledFrom();
		self::assertIsString($result);

		$expected = 'DebuggerTest.php line 117';
		$result   = Debugger::getCalledFrom(0);
		self::assertStringContainsString($expected, $result);

		$expected = 'Unknown trace with index: 99';
		$result   = Debugger::getCalledFrom(99);
		self::assertStringContainsString($expected, $result);

		$expected = 'DebuggerTest.php line 86';
		$result   = Debugger::getCalledFromTrace([
			'file' => __DIR__ . 'DebuggerTest.php',
			'line' => 86,
		]);
		self::assertStringContainsString($expected, $result);

		$expected = 'DebuggerTest->testGetCalledFrom';
		$result   = Debugger::getCalledFromTrace([
			'class'    => 'DebuggerTest',
			'type'     => '->',
			'function' => 'testGetCalledFrom',
		]);
		self::assertStringContainsString($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::reflectClass
	 * @covers \Xicrow\PhpDebug\Debugger::reflectClassProperty
	 * @covers \Xicrow\PhpDebug\Debugger::reflectClassMethod
	 */
	public function testReflectClass()
	{
		$result = Debugger::reflectClass(DebuggerTestClass::class);
		self::assertStringContainsString('Class DebuggerTestClass', $result);

		self::assertStringContainsString('public $publicProperty = "public";', $result);
		self::assertStringContainsString('private $privateProperty = "private";', $result);
		self::assertStringContainsString('protected $protectedProperty = "protected";', $result);

		self::assertStringContainsString('public static $publicStaticProperty = "public static";', $result);
		self::assertStringContainsString('private static $privateStaticProperty = "private static";', $result);
		self::assertStringContainsString('protected static $protectedStaticProperty = "protected static";', $result);

		self::assertStringContainsString('public function publicFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
		self::assertStringContainsString('private function privateFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
		self::assertStringContainsString('protected function protectedFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);

		self::assertStringContainsString('public static function publicStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
		self::assertStringContainsString('private static function privateStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
		self::assertStringContainsString('protected static function protectedStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::reflectClassProperty
	 */
	public function testReflectClassProperty()
	{
		$result = Debugger::reflectClassProperty(DebuggerTestClass::class, 'publicProperty');
		self::assertStringContainsString('public $publicProperty = "public"', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::reflectClassMethod
	 */
	public function testReflectClassMethod()
	{
		$result = Debugger::reflectClassMethod(DebuggerTestClass::class, 'publicFunction');
		self::assertStringContainsString('public function publicFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
	}
}

/**
 * Class DebuggerTestClass
 *
 * @codeCoverageIgnore
 */
class DebuggerTestClass
{
	/**
	 * @var string
	 */
	public string $publicProperty = 'public';

	/**
	 * @var string
	 */
	private string $privateProperty = 'private';

	/**
	 * @var string
	 */
	protected string $protectedProperty = 'protected';

	/**
	 * @var string
	 */
	public static string $publicStaticProperty = 'public static';

	/**
	 * @var string
	 */
	private static string $privateStaticProperty = 'private static';

	/**
	 * @var string
	 */
	protected static string $protectedStaticProperty = 'protected static';

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	public function publicFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	private function privateFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	protected function protectedFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	public static function publicStaticFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	private static function privateStaticFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}

	/**
	 * @param        $param1
	 * @param null   $param2
	 * @param bool   $param3
	 * @param int    $param4
	 * @param string $param5
	 * @param array  $param6
	 */
	protected static function protectedStaticFunction($param1, $param2 = null, $param3 = true, $param4 = 4, $param5 = '5', $param6 = [])
	{
	}
}
