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
	public function __construct($strName = null, array $arrData = [], $strDataName = '')
	{
		parent::__construct($strName, $arrData, $strDataName);

		// Set debugger options
		Debugger::$strDocumentRoot = realpath('.');
		Debugger::$bShowCalledFrom = false;
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
		$strActual = Debugger::getDebugInformation('string');
		self::assertIsString($strActual);

		$strActual = Debugger::getDebugInformation(123);
		self::assertIsString($strActual);

		// Test all PHP data types
		$strActual   = Debugger::getDebugInformation(null);
		$strExpected = 'NULL';
		self::assertEquals($strExpected, $strActual);

		$strActual   = Debugger::getDebugInformation(true);
		$strExpected = 'TRUE';
		self::assertEquals($strExpected, $strActual);

		$strActual   = Debugger::getDebugInformation(false);
		$strExpected = 'FALSE';
		self::assertEquals($strExpected, $strActual);

		$strActual   = Debugger::getDebugInformation(123);
		$strExpected = '123';
		self::assertEquals($strExpected, $strActual);

		$strActual   = Debugger::getDebugInformation(123.123);
		$strExpected = '123.123';
		self::assertEquals($strExpected, $strActual);

		$strActual   = Debugger::getDebugInformation('string');
		$strExpected = '"string"';
		self::assertEquals($strExpected, $strActual);

		$arrActual   = [1, 2, 3];
		$strExpected = '[';
		$strExpected .= "\n";
		$strExpected .= '	0 => 1,';
		$strExpected .= "\n";
		$strExpected .= '	1 => 2,';
		$strExpected .= "\n";
		$strExpected .= '	2 => 3';
		$strExpected .= "\n";
		$strExpected .= ']';
		$result      = Debugger::getDebugInformation($arrActual);
		self::assertEquals($strExpected, $result);

		$oActual = new DateTime();
		$oActual->setTimezone(new DateTimeZone('Europe/Copenhagen'));
		$oActual->setDate(2016, 01, 01);
		$oActual->setTime(00, 00, 00);
		$strExpected = 'object(DateTime) {' . "\n";
		$strExpected .= "\t\n";
		$strExpected .= '}';
		$result      = Debugger::getDebugInformation($oActual);
		self::assertEquals($strExpected, $result);

		$resource    = fopen(realpath(__DIR__ . '/../README.md'), 'r');
		$strExpected = '#Resource id \#[0-9]+ \(stream\)#';
		$result      = Debugger::getDebugInformation($resource);
		self::assertMatchesRegularExpression($strExpected, $result);
		fclose($resource);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Debugger::getCalledFrom
	 * @covers \Xicrow\PhpDebug\Debugger::getCalledFromTrace
	 */
	public function testGetCalledFrom()
	{
		$strActual = Debugger::getCalledFrom();
		self::assertIsString($strActual);

		$strExpected = 'DebuggerTest.php line ' . (__LINE__ + 1);
		$strActual   = Debugger::getCalledFrom();
		self::assertStringContainsString($strExpected, $strActual);

		$strExpected = 'Unknown trace with index: 99';
		$strActual   = Debugger::getCalledFrom(99);
		self::assertStringContainsString($strExpected, $strActual);

		$strExpected = 'DebuggerTest.php line 86';
		$strActual   = Debugger::getCalledFromTrace([
			'file' => __DIR__ . 'DebuggerTest.php',
			'line' => 86,
		]);
		self::assertStringContainsString($strExpected, $strActual);

		$strExpected = 'DebuggerTest->testGetCalledFrom';
		$strActual   = Debugger::getCalledFromTrace([
			'class'    => 'DebuggerTest',
			'type'     => '->',
			'function' => 'testGetCalledFrom',
		]);
		self::assertStringContainsString($strExpected, $strActual);
	}
}
