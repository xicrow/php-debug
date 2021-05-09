<?php
namespace Xicrow\PhpDebug;

use Exception;
use ReflectionObject;
use ReflectionProperty;

/**
 * Class Debugger
 *
 * @package Xicrow\PhpDebug
 */
class Debugger
{
	public static ?string $strDocumentRoot = null;
	public static bool    $bShowCalledFrom = true;

	/**
	 * @param mixed  $mData
	 * @param string $strHeaderText
	 * @param int    $iTraceOffset
	 *
	 * @codeCoverageIgnore
	 */
	public static function debug($mData, string $strHeaderText = '', int $iTraceOffset = 0): void
	{
		Utility::outputBox(
			$strHeaderText,
			self::getDebugInformation($mData),
			self::$bShowCalledFrom ? self::getCalledFrom($iTraceOffset + 1) : ''
		);
	}

	/**
	 * @param array  $arrData
	 * @param string $strHeaderText
	 * @param int    $iTraceOffset
	 *
	 * @codeCoverageIgnore
	 */
	public static function debugMultiple(array $arrData, string $strHeaderText = '', int $iTraceOffset = 0): void
	{
		foreach ($arrData as $mData) {
			self::debug($mData, $strHeaderText, $iTraceOffset + 1);
		}
	}

	/**
	 * @param array  $arrResults
	 * @param string $strHeaderText
	 *
	 * @codeCoverageIgnore
	 */
	public static function compare(array $arrResults, string $strHeaderText = 'Result'): void
	{
		$arrBoxes = [];
		foreach ($arrResults as $iIndex => $mResult) {
			ob_start();
			Utility::outputBox(
				$strHeaderText . ' #' . ($iIndex + 1),
				self::getDebugInformation($mResult),
				self::$bShowCalledFrom ? self::getCalledFrom(1) : ''
			);
			$arrBoxes[] = ob_get_clean();
		}

		Utility::outputCompareBoxes(...$arrBoxes);
	}

	/**
	 * @param bool $bReversed
	 *
	 * @codeCoverageIgnore
	 */
	public static function showTrace(bool $bReversed = false): void
	{
		$arrBacktrace = debug_backtrace();
		if ($bReversed) {
			$arrBacktrace = array_reverse($arrBacktrace);
		}

		$strOutput   = '';
		$iTraceIndex = $bReversed ? 1 : count($arrBacktrace);
		foreach ($arrBacktrace as $arrTrace) {
			$strOutput .= $iTraceIndex . ': ';
			$strOutput .= self::getCalledFromTrace($arrTrace);
			$strOutput .= "\n";

			$iTraceIndex += $bReversed ? 1 : -1;
		}

		Utility::outputBox(
			'Trace',
			$strOutput,
			self::$bShowCalledFrom ? self::getCalledFrom(1) : ''
		);
	}

	/**
	 * @param int $iIndex
	 *
	 * @return string
	 */
	public static function getCalledFrom(int $iIndex = 0): string
	{
		$arrBacktrace = debug_backtrace();

		if (!isset($arrBacktrace[$iIndex])) {
			return 'Unknown trace with index: ' . $iIndex;
		}

		return self::getCalledFromTrace($arrBacktrace[$iIndex]);
	}

	/**
	 * @param array $arrTrace
	 *
	 * @return string
	 */
	public static function getCalledFromTrace(array $arrTrace): string
	{
		// Get file and line number
		if (isset($arrTrace['file'])) {
			$strCalledFrom = $arrTrace['file'] . ' line ' . $arrTrace['line'];
			$strCalledFrom = str_replace('\\', '/', $strCalledFrom);
			$strCalledFrom = (!empty(self::$strDocumentRoot) ? substr($strCalledFrom, strlen(self::$strDocumentRoot)) : $strCalledFrom);
			$strCalledFrom = trim($strCalledFrom, '/');

			return $strCalledFrom;
		}

		// Get function call
		if (isset($arrTrace['function'])) {
			$strCalledFrom = (isset($arrTrace['class']) ? $arrTrace['class'] : '');
			$strCalledFrom .= (isset($arrTrace['type']) ? $arrTrace['type'] : '');
			$strCalledFrom .= $arrTrace['function'] . '()';

			return $strCalledFrom;
		}

		return 'Unable to get called from trace';
	}

	/**
	 * @param mixed $mData
	 * @param array $arrOptions
	 *
	 * @return string
	 */
	public static function getDebugInformation($mData, array $arrOptions = []): string
	{
		// Merge options with default options
		$arrOptions = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $arrOptions);

		// Get data type
		$strDataType = gettype($mData);

		// Set name of method to get debug information for data
		$strMethodName = 'getDebugInformation' . ucfirst(strtolower($strDataType));

		// Get result from debug information method
		$strResult = 'No method found supporting data type: ' . $strDataType;
		if (method_exists(self::class, $strMethodName)) {
			$strResult = (string)self::$strMethodName($mData, [
				'depth'  => ($arrOptions['depth'] - 1),
				'indent' => ($arrOptions['indent'] + 1),
			]);
		}

		// Return result
		return Utility::wrapDataType($strDataType, $strResult);
	}

	/**
	 * @param string $strData
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationString(string $strData): string
	{
		return (string)(Utility::isCli() ? '"' . $strData . '"' : htmlentities($strData, ENT_SUBSTITUTE));
	}

	/**
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationNull(): string
	{
		return 'NULL';
	}

	/**
	 * @param boolean $bData
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationBoolean(bool $bData): string
	{
		return ($bData ? 'TRUE' : 'FALSE');
	}

	/**
	 * @param integer $iData
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationInteger(int $iData): string
	{
		return (string)$iData;
	}

	/**
	 * @param double $fData
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationDouble(float $fData): string
	{
		return (string)$fData;
	}

	/**
	 * @param array|object $mData
	 * @param array        $arrOptions
	 *
	 * @return string
	 */
	private static function getDebugInformationArray($mData, array $arrOptions = []): string
	{
		$arrOptions = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $arrOptions);

		$strDebugInfo = "[";

		$strBreak = $strEnd = '';
		if (!empty($mData)) {
			$strBreak = "\n" . str_repeat("\t", $arrOptions['indent']);
			$strEnd   = "\n" . str_repeat("\t", $arrOptions['indent'] - 1);
		}

		$arrDatas = [];
		if ($arrOptions['depth'] >= 0) {
			foreach ($mData as $mKey => $mVal) {
				// Sniff for globals as !== explodes in < 5.4
				if ($mKey === 'GLOBALS' && is_array($mVal) && isset($mVal['GLOBALS'])) {
					$mVal = '[recursion]';
				} elseif ($mVal !== $mData) {
					$mVal = static::getDebugInformation($mVal, $arrOptions);
				}
				$arrDatas[] = $strBreak . static::getDebugInformation($mKey) . ' => ' . $mVal;
			}
		} else {
			$arrDatas[] = $strBreak . '[maximum depth reached]';
		}

		return $strDebugInfo . implode(',', $arrDatas) . $strEnd . ']';
	}

	/**
	 * @param object $oData
	 * @param array  $arrOptions
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationObject(object $oData, array $arrOptions = []): string
	{
		$arrOptions = array_merge([
			'depth'  => 25,
			'indent' => 0,
		], $arrOptions);

		$strDebugInfo = '';
		$strDebugInfo .= 'object(' . get_class($oData) . ') {';

		$strBreak = "\n" . str_repeat("\t", $arrOptions['indent']);
		$strEnd   = "\n" . str_repeat("\t", $arrOptions['indent'] - 1);

		if ($arrOptions['depth'] > 0 && method_exists($oData, '__debugInfo')) {
			try {
				$strDebugArray = static::getDebugInformationArray($oData->__debugInfo(), array_merge($arrOptions, [
					'depth' => ($arrOptions['depth'] - 1),
				]));
				$strDebugInfo  .= substr($strDebugArray, 1, -1);

				return $strDebugInfo . $strEnd . '}';
			} catch (Exception $oException) {
				$strMessage = $oException->getMessage();

				return $strDebugInfo . "\n(unable to export object: $strMessage)\n }";
			}
		}

		if ($arrOptions['depth'] > 0) {
			$arrProperties = [];
			$arrObjectVars = get_object_vars($oData);
			foreach ($arrObjectVars as $mKey => $mValue) {
				$mValue          = static::getDebugInformation($mValue, array_merge($arrOptions, [
					'depth' => ($arrOptions['depth'] - 1),
				]));
				$arrProperties[] = "$mKey => " . $mValue;
			}

			$oReflectionObject = new ReflectionObject($oData);
			$arrFilters        = [
				ReflectionProperty::IS_PROTECTED => 'protected',
				ReflectionProperty::IS_PRIVATE   => 'private',
			];
			foreach ($arrFilters as $iFilter => $strVisibility) {
				$arrReflectionProperties = $oReflectionObject->getProperties($iFilter);
				foreach ($arrReflectionProperties as $oReflectionProperty) {
					$oReflectionProperty->setAccessible(true);
					$mPropertyValue = $oReflectionProperty->getValue($oData);

					$mValue          = static::getDebugInformation($mPropertyValue, array_merge($arrOptions, [
						'depth' => ($arrOptions['depth'] - 1),
					]));
					$mKey            = $oReflectionProperty->name;
					$arrProperties[] = sprintf('[%s] %s => %s', $strVisibility, $mKey, $mValue);
				}
			}

			$strDebugInfo .= $strBreak . implode($strBreak, $arrProperties) . $strEnd;
		}
		$strDebugInfo .= '}';

		return $strDebugInfo;
	}

	/**
	 * @param resource $rData
	 *
	 * @return string
	 * @noinspection PhpUnusedPrivateMethodInspection
	 */
	private static function getDebugInformationResource($rData): string
	{
		return (string)$rData . ' (' . get_resource_type($rData) . ')';
	}
}
