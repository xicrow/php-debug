<?php
namespace Xicrow\PhpDebug\Test;

use DateTime;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Xicrow\PhpDebug\Debugger;
use Xicrow\PhpDebug\Timer;

/**
 * Class TimerTest
 */
class TimerTest extends TestCase
{
	/**
	 * @inheritdoc
	 */
	public function __construct($strName = null, array $arrData = [], $strDataName = '')
	{
		parent::__construct($strName, $arrData, $strDataName);

		// Set debugger options
		Debugger::$strDocumentRoot = dirname(__DIR__);
		Debugger::$bShowCalledFrom = false;
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Timer::reset
	 * @covers \Xicrow\PhpDebug\Timer::add
	 * @covers \Xicrow\PhpDebug\Timer::begin
	 */
	public function testStart()
	{
		Timer::reset();

		$iExpected = 0;
		$iActual   = Timer::count();
		self::assertEquals($iExpected, $iActual);

		$strExpected = 'test';
		$strActual   = Timer::begin('test');
		self::assertEquals($strExpected, $strActual);

		$iExpected = 1;
		$iActual   = Timer::count();
		self::assertEquals($iExpected, $iActual);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Timer::reset
	 * @covers \Xicrow\PhpDebug\Timer::add
	 * @covers \Xicrow\PhpDebug\Timer::begin
	 * @covers \Xicrow\PhpDebug\Timer::end
	 */
	public function testStop()
	{
		Timer::reset();

		$iExpected = 0;
		$iActual   = Timer::count();
		self::assertEquals($iExpected, $iActual);

		$strExpected = null;
		$strActual   = Timer::end();
		self::assertEquals($strExpected, $strActual);

		$iExpected = 0;
		$iActual   = Timer::count();
		self::assertEquals($iExpected, $iActual);

		$strExpected = 'test';
		$strActual   = Timer::begin('test');
		self::assertEquals($strExpected, $strActual);

		$strExpected = 'test';
		$strActual   = Timer::end('test');
		self::assertEquals($strExpected, $strActual);

		$iExpected = 1;
		$iActual   = Timer::count();
		self::assertEquals($iExpected, $iActual);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Timer::reset
	 * @covers \Xicrow\PhpDebug\Timer::add
	 * @covers \Xicrow\PhpDebug\Timer::custom
	 */
	public function testCustom()
	{
		Timer::reset();

		$strExpected = 'test';
		$strActual   = Timer::custom('test');
		self::assertEquals($strExpected, $strActual);

		$strExpected = 'custom1';
		$strActual   = Timer::custom('custom1');
		self::assertEquals($strExpected, $strActual);

		$strExpected = 'custom2';
		$strActual   = Timer::custom('custom2', time());
		self::assertEquals($strExpected, $strActual);

		$strExpected = 'custom3';
		$strActual   = Timer::custom('custom3', time(), time());
		self::assertEquals($strExpected, $strActual);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Timer::reset
	 * @covers \Xicrow\PhpDebug\Timer::add
	 * @covers \Xicrow\PhpDebug\Timer::begin
	 * @covers \Xicrow\PhpDebug\Timer::end
	 * @covers \Xicrow\PhpDebug\Timer::callback
	 */
	public function testCallback()
	{
		Timer::reset();

		$iExpected = time();
		$iActual   = Timer::callback('time');
		self::assertEquals($iExpected, $iActual);

		$iExpected = strpos('Hello world', 'world');
		$iActual   = Timer::callback('strpos', ['Hello world', 'world']);
		self::assertEquals($iExpected, $iActual);

		$iExpected = array_sum([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$iActual   = Timer::callback('array_sum', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
		self::assertEquals($iExpected, $iActual);

		$iExpected = min([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$iActual   = Timer::callback('min', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
		self::assertEquals($iExpected, $iActual);

		$iExpected = max([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$iActual   = Timer::callback('max', [[1, 2, 3, 4, 5, 6, 7, 8, 9]]);
		self::assertEquals($iExpected, $iActual);

		$strExpected = 'Xicrow\PhpDebug\Debugger::getCalledFrom()';
		$strActual   = Timer::callback(['Xicrow\PhpDebug\Debugger', 'getCalledFrom']);
		self::assertEquals($strExpected, $strActual);

		$oDateTime = new DateTime();
		$oDateTime->setTimezone(new DateTimeZone('Europe/Copenhagen'));
		$oDateTime->setDate(2016, 01, 01);
		$oDateTime->setTime(00, 00, 00);
		$strExpected = '2016-01-01 00:00:00';
		$strActual   = Timer::callback([$oDateTime, 'format'], ['Y-m-d H:i:s']);
		self::assertEquals($strExpected, $strActual);

		$bExpected = true;
		$bActual   = Timer::callback(static function () {
			return true;
		});
		self::assertEquals($bExpected, $bActual);

		$bExpected = false;
		$bActual   = Timer::callback(static function () {
			return false;
		});
		self::assertEquals($bExpected, $bActual);

		$iLineNo = __LINE__ + 9;
		self::expectOutputString(
			<<<TXT
		
		############################################## Error ###############################################
		Invalid callback sent to Timer::callback(): non_existing_function
		----------------------------------- tests/TimerTest.php line {$iLineNo} -----------------------------------

		TXT
		);
		$bExpected = false;
		$bActual   = Timer::callback('non_existing_function');
		self::assertEquals($bExpected, $bActual);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Timer::reset
	 * @covers \Xicrow\PhpDebug\Timer::add
	 * @covers \Xicrow\PhpDebug\Timer::custom
	 * @covers \Xicrow\PhpDebug\Timer::getStats
	 */
	public function testGetStats()
	{
		Timer::reset();

		$strExpected = 'Unknown item with key: foo';
		$strActual   = Timer::getStats('foo');
		self::assertEquals($strExpected, $strActual);

		$strTimerName = 'Foo';
		Timer::custom($strTimerName, 0.1, 0.2);

		$strActual = Timer::getStats($strTimerName);
		self::assertStringContainsString($strTimerName, $strActual);
		self::assertStringContainsString('100.0000 MS', $strActual);

		$strTimerName = 'Really, really, really, really, really, really, really, really, really, really, really, really, really long timer name';
		Timer::custom($strTimerName, 0.1, 0.2);

		$strActual = Timer::getStats($strTimerName);
		self::assertStringContainsString(substr($strTimerName, -20), $strActual);
		self::assertStringContainsString('100.0000 MS', $strActual);
	}
}
