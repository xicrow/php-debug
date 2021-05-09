<?php
namespace Xicrow\PhpDebug\Test;

use PHPUnit\Framework\TestCase;
use Xicrow\PhpDebug\Utility;

/**
 * Class UtilityTest
 *
 * @package Xicrow\PhpDebug\Test
 */
class UtilityTest extends TestCase
{
	public function testIsCli(): void
	{
		self::assertEquals(true, Utility::isCli());
	}

	public function testOutputBox(): void
	{
		ob_start();
		Utility::outputBox('Header');
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		
		############################################## Header ##############################################
		
		TXT;
		self::assertEquals($strExpected, $strActual);

		ob_start();
		Utility::outputBox('', 'Content');
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		
		############################################## DEBUG ###############################################
		Content
		
		TXT;
		self::assertEquals($strExpected, $strActual);

		ob_start();
		Utility::outputBox('', '', 'Footer');
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		
		############################################## DEBUG ###############################################
		---------------------------------------------- Footer ----------------------------------------------
		
		TXT;
		self::assertEquals($strExpected, $strActual);

		ob_start();
		Utility::outputBox('Header', 'Content', 'Footer');
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		
		############################################## Header ##############################################
		Content
		---------------------------------------------- Footer ----------------------------------------------
		
		TXT;
		self::assertEquals($strExpected, $strActual);
	}

	public function testOutputCompareBoxes(): void
	{
		ob_start();
		Utility::outputCompareBoxes('Box #1', 'Box #2', 'Box #3');
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		Box #1Box #2Box #3
		TXT;
		self::assertEquals($strExpected, $strActual);

		ob_start();
		Utility::outputCompareBoxes("Box #1\n", "Box #2\n", "Box #3");
		$strActual   = ob_get_clean();
		$strExpected = <<<TXT
		Box #1
		Box #2
		Box #3
		TXT;
		self::assertEquals($strExpected, $strActual);
	}

	public function testWrapDataType(): void
	{
		self::assertEquals('null', Utility::wrapDataType('null', 'null'));
		self::assertEquals('true', Utility::wrapDataType('boolean', 'true'));
		self::assertEquals('1234', Utility::wrapDataType('integer', '1234'));
		self::assertEquals('12.34', Utility::wrapDataType('double', '12.34'));
		self::assertEquals('test', Utility::wrapDataType('string', 'test'));
	}
}
