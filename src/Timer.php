<?php
namespace Xicrow\PhpDebug;

use Closure;
use ErrorException;

/**
 * Class Timer
 *
 * @package Xicrow\PhpDebug
 */
class Timer
{
	/** @var TimerItem[] */
	private static array $arrItems = [];
	/** @var string[] */
	private static array   $arrRunningItems   = [];
	private static ?string $strCurrentItemKey = null;

	/** @var string[] */
	public static array $arrColorThreshold = [];

	/**
	 * @param string|null    $strKey
	 * @param TimerItem|null $oTimerItem
	 *
	 * @return string
	 */
	private static function add(?string $strKey = null, TimerItem $oTimerItem = null): string
	{
		// If no key is given
		if ($strKey === null) {
			// Set key to file and line
			$strKey = Debugger::getCalledFrom(2);
		}

		// If key is allready in use
		if (isset(self::$arrItems[$strKey])) {
			// Get original item
			$oOriginalItem = self::$arrItems[$strKey];

			// Set new item count
			$iItemCount = $oOriginalItem->iCount !== null ? $oOriginalItem->iCount + 1 : 2;

			// Set correct key for the original item
			if (strpos($oOriginalItem->strKey, '#') === false) {
				$oOriginalItem->strKey = $strKey . ' #1';
				$oOriginalItem->iCount = $iItemCount;
			} else {
				self::$arrItems[$strKey]->iCount = $iItemCount;
			}

			// Set new key
			$strKey .= ' #' . $iItemCount;
		} elseif ($oTimerItem === null) {
			$oTimerItem = new TimerItem($strKey);
		}

		// Make sure various options are set
		if ($oTimerItem->strKey === null) {
			$oTimerItem->strKey = $strKey;
		}
		if ($oTimerItem->strParent === null) {
			$oTimerItem->strParent = self::$strCurrentItemKey;
		}
		if ($oTimerItem->iLevel === null) {
			$oTimerItem->iLevel = 0;
			if ($oTimerItem->strParent !== null && isset(self::$arrItems[$oTimerItem->strParent])) {
				$oTimerItem->iLevel = self::$arrItems[$oTimerItem->strParent]->iLevel + 1;
			}
		}

		// Add item to collection
		self::$arrItems[$strKey] = $oTimerItem;

		return $strKey;
	}

	/**
	 * @param string|null $strKey
	 *
	 * @return string
	 */
	public static function begin(?string $strKey = null): string
	{
		// Add new item
		$strKey = self::add($strKey, new TimerItem(null, null, null, null, microtime(true)));

		// Set current item
		self::$strCurrentItemKey = $strKey;

		// Add to running items
		self::$arrRunningItems[$strKey] = true;

		return $strKey;
	}

	/**
	 * @param string|null $strKey
	 *
	 * @return string|null
	 */
	public static function end(?string $strKey = null): ?string
	{
		// If no key is given
		if (is_null($strKey)) {
			// Get key of the last started item
			end(self::$arrRunningItems);
			$strKey = key(self::$arrRunningItems);
		}

		// Check for key duplicates, and find the last one not stopped
		if (isset(self::$arrItems[$strKey]) && isset(self::$arrItems[$strKey . ' #2'])) {
			$bLastNotStopped = false;
			$strCurrentKey   = $strKey;
			$iCurrentIndex   = 1;
			while (isset(self::$arrItems[$strCurrentKey])) {
				if (self::$arrItems[$strCurrentKey]->fEnd === null) {
					$bLastNotStopped = $strCurrentKey;
				}

				$iCurrentIndex++;
				$strCurrentKey = $strKey . ' #' . $iCurrentIndex;
			}

			if ($bLastNotStopped) {
				$strKey = $bLastNotStopped;
			}
		}

		// If item exists in collection
		if (isset(self::$arrItems[$strKey])) {
			// Update the item
			self::$arrItems[$strKey]->fEnd = microtime(true);

			self::$strCurrentItemKey = self::$arrItems[$strKey]->strParent;
		}

		if (isset(self::$arrRunningItems[$strKey])) {
			unset(self::$arrRunningItems[$strKey]);
		}

		return $strKey;
	}

	/**
	 * @param string|null $strKey
	 * @param float|null  $fBegin
	 * @param float|null  $fEnd
	 *
	 * @return string
	 */
	public static function custom(?string $strKey = null, ?float $fBegin = null, ?float $fEnd = null): string
	{
		// Add new item
		self::add($strKey, new TimerItem(null, null, null, null, $fBegin, $fEnd));

		// If no stop value is given
		if ($fEnd === null) {
			// Set current item
			self::$strCurrentItemKey = $strKey;

			// Add to running items
			self::$arrRunningItems[$strKey] = true;
		}

		return $strKey;
	}

	/**
	 * @param string|array|Closure $fnCallback
	 * @param array                $arrCallbackParameters
	 * @param string|null          $strKey
	 *
	 * @return bool|string|mixed
	 */
	public static function callback($fnCallback, array $arrCallbackParameters = [], ?string $strKey = null)
	{
		// Get key if no key is given
		if ($strKey === null) {
			if (is_string($fnCallback)) {
				$strKey = $fnCallback;
			} elseif (is_array($fnCallback)) {
				$arrKeys = [];
				foreach ($fnCallback as $mValue) {
					if (is_string($mValue)) {
						$arrKeys[] = $mValue;
					} elseif (is_object($mValue)) {
						$arrKeys[] = get_class($mValue);
					}
				}

				$strKey = implode('', $arrKeys);
				if (count($arrKeys) > 1) {
					$strMethod = array_pop($arrKeys);
					$strKey    = implode('/', $arrKeys);
					$strKey    .= '::' . $strMethod;
				}

				unset($arrKeys, $strMethod);
			} elseif (is_object($fnCallback) && $fnCallback instanceof Closure) {
				$strKey = 'closure';
			}

			$strKey = 'callback: ' . $strKey;
		}

		// Set default return value
		$bReturnValue = true;

		try {
			// Set error handler, to convert errors to exceptions
			set_error_handler(static function (int $iErrno, string $strErrstr, string $strErrfile, int $iErrline) {
				throw new ErrorException($strErrstr, 0, $iErrno, $strErrfile, $iErrline);
			});

			// Start output buffer to capture any output
			ob_start();

			// Start profiler
			self::begin($strKey);

			// Execute callback, and get result
			$mCallbackResult = call_user_func_array($fnCallback, $arrCallbackParameters);

			// Stop profiler
			self::end($strKey);

			// Get and clean output buffer
			$strCallbackOutput = ob_get_clean();
		} catch (ErrorException $oCallbackException) {
			// Stop and clean output buffer
			ob_end_clean();

			// Show error message
			Utility::outputBox(
				'Error',
				'Invalid callback sent to Timer::callback(): ' . str_replace('callback: ', '', $strKey),
				Debugger::getCalledFrom(1)
			);

			// Cleanup
			unset(self::$arrItems[$strKey]);
			unset(self::$arrRunningItems[$strKey]);
			end(self::$arrRunningItems);
			self::$strCurrentItemKey = key(self::$arrRunningItems);

			// Clear callback result and output
			unset($mCallbackResult, $strCallbackOutput);

			// Set return value to false
			$bReturnValue = false;
		}

		// Restore error handler
		restore_error_handler();

		// Return result, output or true
		return (isset($mCallbackResult) ? $mCallbackResult : (!empty($strCallbackOutput) ? $strCallbackOutput : $bReturnValue));
	}

	/**
	 * @param string $strKey
	 * @param array  $arrOptions
	 *
	 * @codeCoverageIgnore
	 */
	public static function show(string $strKey, array $arrOptions = [])
	{
		$strStats = self::getStats($strKey, $arrOptions);
		if ($strStats !== '') {
			if (Utility::isCli()) {
				$strOutput = $strStats;
			} else {
				$strOutput = '<div class="xicrow-php-debug-timer">' . $strStats . '</div>';
			}

			Utility::outputBox(
				$strKey,
				$strOutput,
				Debugger::getCalledFrom(1)
			);
		}
	}

	/**
	 * @param array $arrOptions
	 *
	 * @codeCoverageIgnore
	 */
	public static function showAll(array $arrOptions = [])
	{
		// Stop started items
		if (count(self::$arrRunningItems)) {
			foreach (self::$arrRunningItems as $strKey => $arrValue) {
				self::end($strKey);
			}
		}

		// Output items
		$strOutput  = '';
		$iItemCount = 1;
		foreach (self::$arrItems as $strKey => $oItem) {
			$strStats = self::getStats($strKey, $arrOptions);

			if (Utility::isCli()) {
				$strOutput .= (!empty($strOutput) ? "\n" : '') . $strStats;
			} else {
				$strOutput .= '<div class="xicrow-php-debug-timer">' . $strStats . '</div>';
			}

			$iItemCount++;

			unset($strStats);
		}
		unset($iItemCount);

		Utility::outputBox(
			'All timers',
			$strOutput,
			Debugger::getCalledFrom(1)
		);
	}

	/**
	 * @param string $strKey
	 * @param array  $arrOptions
	 *
	 * @return string
	 */
	public static function getStats(string $strKey, array $arrOptions = []): string
	{
		// Merge options with default options
		$arrOptions = array_merge([
			// Show nested (boolean)
			'nested'         => true,
			// Prefix for nested items (string)
			'nested_prefix'  => '|-- ',
			// Max key length (int)
			'max_key_length' => 100,
		], $arrOptions);

		// If item does not exist
		if (!isset(self::$arrItems[$strKey])) {
			return 'Unknown item with key: ' . $strKey;
		}

		// Get item
		$oItem = self::$arrItems[$strKey];

		// Get item result
		$fItemElapsed           = null;
		$strItemResultFormatted = 'N/A';
		if ($oItem->fBegin !== null && $oItem->fEnd !== null) {
			$fItemElapsed           = $oItem->getElapsed() * 1000;
			$strItemResultFormatted = Utility::formatMiliseconds($fItemElapsed, 4);
		}

		// Variable for output
		$strOutput = '';

		// Prep key for output
		$strOutputName = '';
		$strOutputName .= ($arrOptions['nested'] ? str_repeat($arrOptions['nested_prefix'], $oItem->iLevel) : '');
		$strOutputName .= $oItem->strKey;
		if (mb_strlen($strOutputName) > $arrOptions['max_key_length']) {
			$strOutputName = '~' . mb_substr($oItem->strKey, -($arrOptions['max_key_length'] - 1));
		}

		// Add item stats
		$strOutput .= str_pad($strOutputName, ($arrOptions['max_key_length'] + (strlen($strOutputName) - mb_strlen($strOutputName))));
		$strOutput .= ' | ';
		$strOutput .= str_pad($strItemResultFormatted, 20, ' ', ($fItemElapsed === null ? STR_PAD_RIGHT : STR_PAD_LEFT));

		if (!Utility::isCli() && count(self::$arrColorThreshold) > 0) {
			krsort(self::$arrColorThreshold);
			foreach (self::$arrColorThreshold as $fValue => $strColor) {
				if ($fItemElapsed !== null && $fItemElapsed >= $fValue) {
					$strOutput = '<span style="color: ' . $strColor . ';">' . $strOutput . '</span>';
				}
			}
		}

		return $strOutput;
	}

	public static function count(): int
	{
		return count(self::$arrItems);
	}

	public static function countRunning(): int
	{
		return count(self::$arrRunningItems);
	}

	public static function reset(): void
	{
		self::$arrItems          = [];
		self::$arrRunningItems   = [];
		self::$strCurrentItemKey = null;
	}
}
