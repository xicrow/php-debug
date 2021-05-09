<?php
namespace Xicrow\PhpDebug;

/**
 * Class TimerItem
 *
 * @package Xicrow\PhpDebug
 */
class TimerItem
{
	public ?string $strKey    = null;
	public ?string $strParent = null;
	public ?int    $iLevel    = null;
	public ?int    $iCount    = null;
	public ?float  $fBegin    = null;
	public ?float  $fEnd      = null;

	/**
	 * TimerItem constructor.
	 *
	 * @param string|null $strKey
	 * @param string|null $strParent
	 * @param int|null    $iLevel
	 * @param int|null    $iCount
	 * @param float|null  $fStart
	 * @param float|null  $fStop
	 */
	public function __construct(?string $strKey = null, ?string $strParent = null, ?int $iLevel = null, ?int $iCount = null, ?float $fStart = null, ?float $fStop = null)
	{
		$this->strKey    = $strKey;
		$this->strParent = $strParent;
		$this->iLevel    = $iLevel;
		$this->iCount    = $iCount;
		$this->fBegin    = $fStart;
		$this->fEnd      = $fStop;
	}

	/**
	 * @return float|null
	 */
	public function getElapsed(): ?float
	{
		if ($this->fBegin === null || $this->fEnd === null) {
			return null;
		}

		return $this->fEnd - $this->fBegin;
	}
}
