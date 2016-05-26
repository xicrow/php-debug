<?php
use Xicrow\PhpDebug\Debugger;
use Xicrow\PhpDebug\Timer;

/**
 * Class ProfilerTest
 */
class ProfilerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @inheritdoc
	 */
	public function __construct($name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);

		// Look into mock for Profiler, instead of using the Timer class
		// https://phpunit.de/manual/current/en/test-doubles.html#test-doubles.stubs
		#$stub = $this->getMockForAbstractClass('\Xicrow\PhpDebug\Collection');
		#$stub->expects($this->any())->method('abstractMethod')->will($this->returnValue(true));

		// Set debugger options
		Debugger::$documentRoot   = 'E:\\GitHub\\';
		Debugger::$showCalledFrom = false;
		Debugger::$output         = false;
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::getCollection
	 */
	public function testGetCollection() {
		$expected = '\Xicrow\PhpDebug\Collection';
		$result   = Timer::getCollection();
		$this->assertInstanceOf($expected, $result);

		$expected = [];
		$result   = Timer::getCollection()->getAll();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 */
	public function testAdd() {
		Timer::getCollection()->clear();

		$expected = 0;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::add();
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::start
	 */
	public function testStart() {
		Timer::getCollection()->clear();

		$expected = 0;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::start();
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::start
	 * @covers \Xicrow\PhpDebug\Profiler::stop
	 */
	public function testStop() {
		Timer::getCollection()->clear();

		$expected = 0;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = Timer::stop();
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::start();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::stop();
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = Timer::getCollection()->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::custom
	 */
	public function testCustom() {
		Timer::getCollection()->clear();

		$expected = true;
		$result   = Timer::custom();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::custom('custom1');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::custom('custom2', time());
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::custom('custom3', time(), time());
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::start
	 * @covers \Xicrow\PhpDebug\Profiler::stop
	 * @covers \Xicrow\PhpDebug\Profiler::callback
	 */
	public function testCallback() {
		Timer::getCollection()->clear();

		$expected = time();
		$result   = Timer::callback(null, 'time');
		$this->assertEquals($expected, $result);

		$expected = strpos('Hello world', 'world');
		$result   = Timer::callback(null, 'strpos', 'Hello world', 'world');
		$this->assertEquals($expected, $result);

		$expected = array_sum([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$result   = Timer::callback(null, 'array_sum', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$this->assertEquals($expected, $result);

		$expected = min([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$result   = Timer::callback(null, 'min', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$this->assertEquals($expected, $result);

		$expected = max([1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$result   = Timer::callback(null, 'max', [1, 2, 3, 4, 5, 6, 7, 8, 9]);
		$this->assertEquals($expected, $result);

		$expected = 'Xicrow\PhpDebug\Debugger::getCalledFrom()';
		$result   = Timer::callback(null, ['Xicrow\PhpDebug\Debugger', 'getCalledFrom']);
		$this->assertEquals($expected, $result);

		$dateTime = new DateTime();
		$dateTime->setTimezone(new DateTimeZone('Europe/Copenhagen'));
		$dateTime->setDate(2016, 01, 01);
		$dateTime->setTime(00, 00, 00);
		$expected = '2016-01-01 00:00:00';
		$result   = Timer::callback(null, [$dateTime, 'format'], 'Y-m-d H:i:s');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = Timer::callback(null, function () {
			return true;
		});
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = Timer::callback(null, function () {
			return false;
		});
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = Timer::callback(null, 'non_existing_function');
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::custom
	 * @covers \Xicrow\PhpDebug\Profiler::getStats
	 * @covers \Xicrow\PhpDebug\Profiler::getStatsOneline
	 */
	public function testGetStats() {
		Timer::getCollection()->clear();

		$expected = 'Unknow item in with key: foo';
		$result   = Timer::getStats('foo');
		$this->assertEquals($expected, $result);

		$timerName = 'Foo';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStats($timerName);
		$this->assertContains($timerName, $result);
		$this->assertContains('100.0000 MS', $result);

		$timerName = 'Really, really, really, really, really, really, really, really, really, really, really, really, really long timer name';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStats($timerName);
		$this->assertContains(substr($timerName, -20), $result);
		$this->assertContains('100.0000 MS', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::custom
	 * @covers \Xicrow\PhpDebug\Profiler::getStatsOneline
	 */
	public function testGetStatsOneline() {
		Timer::getCollection()->clear();

		$timerName = 'Foo';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStatsOneline(Timer::getCollection()->get($timerName));
		$this->assertContains($timerName, $result);
		$this->assertContains('100.0000 MS', $result);

		$result = Timer::getStatsOneline(Timer::getCollection()->get($timerName), ['show_start_stop' => true]);
		$this->assertContains(date('Y-m-d H:i'), $result);

		$timerName = 'Really, really, really, really, really, really, really, really, really, really, really, really, really long timer name';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStatsOneline(Timer::getCollection()->get($timerName));
		$this->assertContains(substr($timerName, -20), $result);
		$this->assertContains('100.0000 MS', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::custom
	 * @covers \Xicrow\PhpDebug\Profiler::getStatsMultiline
	 */
	public function testGetStatsMultiline() {
		Timer::getCollection()->clear();

		$timerName = 'Foo';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStatsMultiline(Timer::getCollection()->get($timerName));
		$this->assertContains($timerName, $result);
		$this->assertContains('100.0000 MS', $result);

		$result = Timer::getStatsMultiline(Timer::getCollection()->get($timerName), ['show_start_stop' => true]);
		$this->assertContains(date('Y-m-d H:i'), $result);

		$timerName = 'Really, really, really, really, really, really, really, really, really, really, really, really, really long timer name';
		Timer::custom($timerName, 0.1, 0.2);

		$result = Timer::getStatsMultiline(Timer::getCollection()->get($timerName));
		$this->assertContains($timerName, $result);
		$this->assertContains('100.0000 MS', $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::add
	 * @covers \Xicrow\PhpDebug\Profiler::start
	 * @covers \Xicrow\PhpDebug\Profiler::stop
	 * @covers \Xicrow\PhpDebug\Profiler::getLastItemName
	 */
	public function testGetLastItemName() {
		Timer::getCollection()->clear();

		$expected = false;
		$result   = Timer::getLastItemName();
		$this->assertEquals($expected, $result);

		Timer::start('foo');

		$expected = 'foo';
		$result   = Timer::getLastItemName();
		$this->assertEquals($expected, $result);

		$expected = 'foo';
		$result   = Timer::getLastItemName('started');
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = Timer::getLastItemName('stopped');
		$this->assertEquals($expected, $result);

		Timer::stop('foo');

		$expected = 'foo';
		$result   = Timer::getLastItemName('stopped');
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::formatDateTime
	 */
	public function testFormatDateTime() {
		$unix        = microtime(true);
		$miliseconds = str_pad(substr($unix, strpos($unix, '.')), 5, '0', STR_PAD_RIGHT);

		$expected = date('Y-m-d H:i:s', $unix);
		$result   = Timer::formatDateTime($unix);
		$this->assertEquals($expected, $result);

		$expected = date('Y-m-d H:i:s', $unix) . $miliseconds;
		$result   = Timer::formatDateTime($unix, true);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::formatForUnits
	 */
	public function testFormatForUnits() {
		$units = [
			'S' => 1,
			'M' => 10,
			'L' => 20
		];

		$expected = '500.00 S ';
		$result   = Timer::formatForUnits($units, 500, 2, 'S');
		$this->assertEquals($expected, $result);

		$expected = '5000.00 S ';
		$result   = Timer::formatForUnits($units, 5000, 2, 'S');
		$this->assertEquals($expected, $result);

		$expected = '5.00 M ';
		$result   = Timer::formatForUnits($units, (5 * 10), 2);
		$this->assertEquals($expected, $result);

		$expected = '5.00 L ';
		$result   = Timer::formatForUnits($units, (5 * 10 * 20), 2);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::formatForUnits
	 * @covers \Xicrow\PhpDebug\Profiler::formatMiliseconds
	 */
	public function testFormatMiliseconds() {
		$expected = '500.00 MS';
		$result   = Timer::formatMiliseconds(500, 2, 'MS');
		$this->assertEquals($expected, $result);

		$expected = '5000.00 MS';
		$result   = Timer::formatMiliseconds(5000, 2, 'MS');
		$this->assertEquals($expected, $result);

		$expected = '5.00 S ';
		$result   = Timer::formatMiliseconds((5 * 1000), 2);
		$this->assertEquals($expected, $result);

		$expected = '5.00 M ';
		$result   = Timer::formatMiliseconds((5 * 1000 * 60), 2);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Profiler::formatForUnits
	 * @covers \Xicrow\PhpDebug\Profiler::formatBytes
	 */
	public function testFormatBytes() {
		$expected = '500.00 B ';
		$result   = Timer::formatBytes(500, 2, 'B');
		$this->assertEquals($expected, $result);

		$expected = '5000.00 B ';
		$result   = Timer::formatBytes(5000, 2, 'B');
		$this->assertEquals($expected, $result);

		$expected = '5.00 KB';
		$result   = Timer::formatBytes((5 * 1024), 2);
		$this->assertEquals($expected, $result);

		$expected = '5.00 MB';
		$result   = Timer::formatBytes((5 * 1024 * 1024), 2);
		$this->assertEquals($expected, $result);
	}
}
