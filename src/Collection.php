<?php
namespace Xicrow\Debug;

/**
 * Class Collection
 *
 * @package Xicrow\Debug
 */
class Collection implements \Iterator, \Countable {
	/**
	 * @var array
	 */
	private $items = [];

	/**
	 * Constructor, sets or resets items for the collection
	 *
	 * @param array $items
	 */
	public function __construct($items = []) {
		$this->items = $items;
	}

	/**
	 * @param string $key
	 * @param array  $data
	 *
	 * @return bool
	 */
	public function add($key, $data = []) {
		// If key is allready in use
		if ($this->exists($key)) {
			// Set correct key for the original item
			$item = $this->get($key);
			if (strpos($item['key'], '#') === false) {
				$this->update($key, [
					'key' => $key . ' #1'
				]);
			}

			// Make sure key is unique
			$originalName = $key;
			$i            = 1;
			while ($this->exists($key)) {
				$key = $originalName . ' #' . ($i + 1);
				$i++;
			}
		}

		$this->items[$key] = array_merge([
			'index' => $this->count(),
			'key'   => $key
		], $data);

		return true;
	}

	/**
	 * @param string $key
	 * @param array  $data
	 *
	 * @return bool
	 */
	public function update($key, $data = []) {
		if ($this->exists($key)) {
			$this->items[$key] = array_merge($this->items[$key], $data);

			return true;
		}

		return false;
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	public function exists($key) {
		// Return if item exists
		return isset($this->items[$key]);
	}

	/**
	 * @param string $key
	 *
	 * @return array|bool
	 */
	public function get($key) {
		// Return item if it exists
		if ($this->exists($key)) {
			return $this->items[$key];
		}

		return false;
	}

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->items;
	}

	/**
	 * @param string|null $key
	 *
	 * @return bool
	 */
	public function clear($key = null) {
		if (is_null($key)) {
			$this->items = [];

			return true;
		} elseif (self::exists($key)) {
			unset($this->items[$key]);

			return true;
		}

		return false;
	}

	/**
	 * @param string $field
	 * @param string $order
	 *
	 * @return bool
	 */
	public function sort($field = '', $order = 'asc') {
		uasort($this->items, function ($a, $b) use ($field) {
			$aValue = 0;
			if (isset($a[$field])) {
				$aValue = $a[$field];
			}

			$bValue = 0;
			if (isset($a[$field])) {
				$bValue = $b[$field];
			}

			if ($field == 'start' || $field == 'stop') {
				$aValue += $a['index'];
				$bValue += $b['index'];
			}

			if ($aValue == $bValue) {
				return 0;
			}

			return ($aValue < $bValue) ? -1 : 1;
		});

		if (strtolower($order) == 'desc') {
			$this->items = array_reverse($this->items);
		}

		return true;
	}

	/**
	 * @inheritdoc
	 * @see Iterator::rewind
	 */
	public function rewind() {
		return reset($this->items);
	}

	/**
	 * @inheritdoc
	 * @see Iterator::current
	 */
	public function current() {
		return current($this->items);
	}

	/**
	 * @inheritdoc
	 * @see Iterator::key
	 */
	public function key() {
		return key($this->items);
	}

	/**
	 * @inheritdoc
	 * @see Iterator::next
	 */
	public function next() {
		return next($this->items);
	}

	/**
	 * @inheritdoc
	 * @see Iterator::valid
	 */
	public function valid() {
		return key($this->items) !== null;
	}

	/**
	 * @inheritdoc
	 * @see Countable::count
	 */
	public function count() {
		return count($this->items);
	}
}
