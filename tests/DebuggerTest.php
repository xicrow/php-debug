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
        $this->assertEquals($expected, $result);

        $data = new DateTime();
        $data->setTimezone(new DateTimeZone('Europe/Copenhagen'));
        $data->setDate(2016, 01, 01);
        $data->setTime(00, 00, 00);
        $expected = 'object(DateTime) {';
        $expected .= "\n";
        $expected .= '	date => "2016-01-01 00:00:00.000000"';
        $expected .= "\n";
        $expected .= '	timezone_type => 3';
        $expected .= "\n";
        $expected .= '	timezone => "Europe/Copenhagen"';
        $expected .= "\n";
        $expected .= '}';
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

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Debugger::reflectClass
     * @covers \Xicrow\PhpDebug\Debugger::reflectClassProperty
     * @covers \Xicrow\PhpDebug\Debugger::reflectClassMethod
     */
    public function testReflectClass()
    {
        $result = Debugger::reflectClass('DebuggerTestClass');
        $this->assertContains('class DebuggerTestClass', $result);

        $this->assertContains('public $publicProperty = "public";', $result);
        $this->assertContains('private $privateProperty = "private";', $result);
        $this->assertContains('protected $protectedProperty = "protected";', $result);

        $this->assertContains('public static $publicStaticProperty = "public static";', $result);
        $this->assertContains('private static $privateStaticProperty = "private static";', $result);
        $this->assertContains('protected static $protectedStaticProperty = "protected static";', $result);

        $this->assertContains('public function publicFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
        $this->assertContains('private function privateFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
        $this->assertContains('protected function protectedFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);

        $this->assertContains('public static function publicStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
        $this->assertContains('private static function privateStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
        $this->assertContains('protected static function protectedStaticFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Debugger::reflectClassProperty
     */
    public function testReflectClassProperty()
    {
        $result = Debugger::reflectClassProperty('DebuggerTestClass', 'publicProperty');
        $this->assertContains('public $publicProperty = "public"', $result);
    }

    /**
     * @test
     * @covers \Xicrow\PhpDebug\Debugger::reflectClassMethod
     */
    public function testReflectClassMethod()
    {
        $result = Debugger::reflectClassMethod('DebuggerTestClass', 'publicFunction');
        $this->assertContains('public function publicFunction($param1, $param2 = NULL, $param3 = TRUE, $param4 = 4, $param5 = "5", $param6 = [])', $result);
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
    public $publicProperty = 'public';

    /**
     * @var string
     */
    private $privateProperty = 'private';

    /**
     * @var string
     */
    protected $protectedProperty = 'protected';

    /**
     * @var string
     */
    public static $publicStaticProperty = 'public static';

    /**
     * @var string
     */
    private static $privateStaticProperty = 'private static';

    /**
     * @var string
     */
    protected static $protectedStaticProperty = 'protected static';

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
