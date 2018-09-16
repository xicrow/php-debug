<?php

use Xicrow\PhpDebug\Debugger;
use Xicrow\PhpDebug\Timer;

/**
 * Class TimerTest
 */
class TimerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @inheritdoc
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // Set debugger options
        Debugger::$documentRoot   = 'E:\\GitHub\\';
        Debugger::$showCalledFrom = false;
        Debugger::$output         = false;
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Timer::reset
     * @covers \Xicrow\PhpDebug\Timer::add
     */
    public function testAdd()
    {
        Timer::reset();

        $expected = 0;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);

        $expected = 'test';
        $result   = Timer::add('test');
        $this->assertEquals($expected, $result);

        $expected = 1;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Timer::reset
     * @covers \Xicrow\PhpDebug\Timer::add
     * @covers \Xicrow\PhpDebug\Timer::start
     */
    public function testStart()
    {
        Timer::reset();

        $expected = 0;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);

        $expected = 'test';
        $result   = Timer::start('test');
        $this->assertEquals($expected, $result);

        $expected = 1;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Timer::reset
     * @covers \Xicrow\PhpDebug\Timer::add
     * @covers \Xicrow\PhpDebug\Timer::start
     * @covers \Xicrow\PhpDebug\Timer::stop
     */
    public function testStop()
    {
        Timer::reset();

        $expected = 0;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);

        $expected = null;
        $result   = Timer::stop();
        $this->assertEquals($expected, $result);

        $expected = 0;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);

        $expected = 'test';
        $result   = Timer::start('test');
        $this->assertEquals($expected, $result);

        $expected = 'test';
        $result   = Timer::stop('test');
        $this->assertEquals($expected, $result);

        $expected = 1;
        $result   = count(Timer::$collection);
        $this->assertEquals($expected, $result);
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

        $expected = 'test';
        $result   = Timer::custom('test');
        $this->assertEquals($expected, $result);

        $expected = 'custom1';
        $result   = Timer::custom('custom1');
        $this->assertEquals($expected, $result);

        $expected = 'custom2';
        $result   = Timer::custom('custom2', time());
        $this->assertEquals($expected, $result);

        $expected = 'custom3';
        $result   = Timer::custom('custom3', time(), time());
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Timer::reset
     * @covers \Xicrow\PhpDebug\Timer::add
     * @covers \Xicrow\PhpDebug\Timer::start
     * @covers \Xicrow\PhpDebug\Timer::stop
     * @covers \Xicrow\PhpDebug\Timer::callback
     */
    public function testCallback()
    {
        Timer::reset();

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

        $this->expectOutputString('php-debug/tests/TimerTest.php line 190' . "\n" . 'Invalid callback sent to Timer::callback: non_existing_function');
        $expected = false;
        $result   = Timer::callback(null, 'non_existing_function');
        $this->assertEquals($expected, $result);
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
     * @covers \Xicrow\PhpDebug\Timer::formatMiliseconds
     */
    public function testFormatMiliseconds()
    {
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
}
