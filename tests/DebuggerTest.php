<?php

use Xicrow\PhpDebug\Debugger;

/**
 * Class DebuggerTest
 */
class DebuggerTest extends PHPUnit_Framework_TestCase
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
     * @covers \Xicrow\PhpDebug\Debugger::getCalledFrom
     * @covers \Xicrow\PhpDebug\Debugger::getCalledFromTrace
     */
    public function testGetCalledFrom()
    {
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
            'line' => 86,
        ]);
        $this->assertContains($expected, $result);

        $expected = 'DebuggerTest->testGetCalledFrom';
        $result   = Debugger::getCalledFromTrace([
            'class'    => 'DebuggerTest',
            'type'     => '->',
            'function' => 'testGetCalledFrom',
        ]);
        $this->assertContains($expected, $result);
    }
}
