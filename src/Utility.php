<?php
namespace Xicrow\PhpDebug;

/**
 * Class Utility
 *
 * @package Xicrow\PhpDebug
 */
class Utility
{
	public static bool   $bOutputEnabled     = true;
	public static bool   $bOutputInlineStyle = true;
	public static string $bInlineStylePath   = __DIR__ . '/Themes/default.css';

	/**
	 * @return bool
	 */
	public static function isCli(): bool
	{
		return php_sapi_name() === 'cli';
	}

	/**
	 * @param string $strHeader
	 * @param string $strContent
	 * @param string $strFooter
	 */
	public static function outputBox(string $strHeader = '', string $strContent = '', string $strFooter = ''): void
	{
		if (!self::$bOutputEnabled || ($strHeader === '' && $strContent === '' && $strFooter === '')) {
			return;
		}

		if (self::isCli()) {
			if ($strHeader !== '') {
				echo "\n" . str_pad(' ' . $strHeader . ' ', 100, '#', STR_PAD_BOTH);
			} else {
				echo "\n" . str_pad(' DEBUG ', 100, '#', STR_PAD_BOTH);
			}
			if ($strContent !== '') {
				echo "\n" . $strContent;
			}
			if ($strFooter !== '') {
				echo "\n" . str_pad(' ' . $strFooter . ' ', 100, '-', STR_PAD_BOTH);
			}
			echo "\n";

			return;
		}

		if (self::$bOutputInlineStyle && is_file(self::$bInlineStylePath)) {
			echo '<style type="text/css">' . file_get_contents(self::$bInlineStylePath) . '</style>';
			self::$bOutputInlineStyle = false;
		}

		echo '<div class="xicrow-php-debug-box">';
		if ($strHeader !== '') {
			echo '<div class="xicrow-php-debug-box-header">';
			echo $strHeader;
			echo '</div>';
		}
		if ($strContent !== '') {
			echo '<div class="xicrow-php-debug-box-content">';
			echo '<pre>';
			echo $strContent;
			echo '</pre>';
			echo '</div>';
		}
		if ($strFooter !== '') {
			echo '<div class="xicrow-php-debug-box-footer">';
			echo $strFooter;
			echo '</div>';
		}
		echo '</div>';
	}

	/**
	 * @param string ...$arrBoxes
	 */
	public static function outputCompareBoxes(string ...$arrBoxes): void
	{
		if (!self::$bOutputEnabled || count($arrBoxes) === 0) {
			return;
		}

		if (!self::isCli()) {
			echo '<div class="xicrow-php-debug-compare">';
		}
		foreach ($arrBoxes as $strBox) {
			echo $strBox;
		}
		if (!self::isCli()) {
			echo '</div>';
		}
	}

	/**
	 * @param string $strDataType
	 * @param string $strData
	 *
	 * @return string
	 */
	public static function wrapDataType(string $strDataType, string $strData): string
	{
		if (!self::isCli() && !in_array(strtolower($strDataType), ['array', 'object'], true)) {
			return '<span class="xicrow-php-debug-data-type-' . strtolower($strDataType) . '">' . $strData . '</span>';
		}

		return $strData;
	}
}
