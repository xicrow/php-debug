<?php
use Xicrow\PhpDebug\Collection;

/**
 * Class CollectionTest
 */
class CollectionTest extends PHPUnit_Framework_TestCase {
	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::__toString
	 * @covers \Xicrow\PhpDebug\Collection::getAll
	 */
	public function testToString() {
		$collection = new Collection();

		$expected = [];
		$result   = $collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = print_r([], true);
		$result   = (string) $collection;
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::count
	 * @covers \Xicrow\PhpDebug\Collection::exists
	 * @covers \Xicrow\PhpDebug\Collection::get
	 * @covers \Xicrow\PhpDebug\Collection::update
	 */
	public function testAdd() {
		$collection = new Collection();

		$expected = true;
		$result   = $collection->add('key', ['foo' => 'bar']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->add('key', ['foo' => 'bar']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key #1'
		];
		$result   = $collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $collection->get('key #1');
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 1,
			'key'   => 'key #2'
		];
		$result   = $collection->get('key #2');
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::clear
	 * @covers \Xicrow\PhpDebug\Collection::count
	 */
	public function testClear() {
		$collection = new Collection();

		$expected = 0;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$collection->add('key1', ['foo' => 'bar']);

		$expected = 1;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$collection->add('key2', ['foo' => 'bar']);

		$expected = 2;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $collection->clear('non-exiting-key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->clear('key1');
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->clear('key2');
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$collection->add('key1', ['foo' => 'bar']);
		$collection->add('key2', ['foo' => 'bar']);
		$collection->add('key3', ['foo' => 'bar']);

		$expected = 3;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->clear();
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::count
	 */
	public function testCount() {
		$collection = new Collection();

		$expected = 0;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$collection->add('key', ['foo' => 'bar']);

		$expected = 1;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);

		$collection->add('key', ['foo' => 'bar']);

		$expected = 2;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::exists
	 */
	public function testExists() {
		$collection = new Collection();

		$expected = false;
		$result   = $collection->exists('key');
		$this->assertEquals($expected, $result);

		$collection->add('key', ['foo' => 'bar']);

		$expected = true;
		$result   = $collection->exists('key');
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::get
	 * @covers \Xicrow\PhpDebug\Collection::getAll
	 */
	public function testGet() {
		$collection = new Collection();

		$expected = false;
		$result   = $collection->get('non-existing-key');
		$this->assertEquals($expected, $result);

		$collection->add('key', ['foo' => 'bar']);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = 'array';
		$result   = $collection->getAll();
		$this->assertInternalType($expected, $result);

		$expected = 1;
		$result   = count($collection->getAll());
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::getAll
	 * @covers \Xicrow\PhpDebug\Collection::sort
	 */
	public function testSort() {
		$collection = new Collection();

		$collection->add('key1', ['foo' => 'bar']);
		$collection->add('key2', ['foo' => 'bar']);
		$collection->add('key3', ['foo' => 'bar']);

		$expected = [
			'key1' => [
				'index' => 0,
				'key'   => 'key1',
				'foo'   => 'bar'
			],
			'key2' => [
				'index' => 1,
				'key'   => 'key2',
				'foo'   => 'bar'
			],
			'key3' => [
				'index' => 2,
				'key'   => 'key3',
				'foo'   => 'bar'
			]
		];
		$result   = $collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->sort('key', 'desc');
		$this->assertEquals($expected, $result);

		$expected = [
			'key3' => [
				'index' => 2,
				'key'   => 'key3',
				'foo'   => 'bar'
			],
			'key2' => [
				'index' => 1,
				'key'   => 'key2',
				'foo'   => 'bar'
			],
			'key1' => [
				'index' => 0,
				'key'   => 'key1',
				'foo'   => 'bar'
			]
		];
		$result   = $collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->sort('key', 'asc');
		$this->assertEquals($expected, $result);

		$expected = [
			'key1' => [
				'index' => 0,
				'key'   => 'key1',
				'foo'   => 'bar'
			],
			'key2' => [
				'index' => 1,
				'key'   => 'key2',
				'foo'   => 'bar'
			],
			'key3' => [
				'index' => 2,
				'key'   => 'key3',
				'foo'   => 'bar'
			]
		];
		$result   = $collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->sort('foo', 'asc');
		$this->assertEquals($expected, $result);

		$expected = [
			'key1' => [
				'index' => 0,
				'key'   => 'key1',
				'foo'   => 'bar'
			],
			'key2' => [
				'index' => 1,
				'key'   => 'key2',
				'foo'   => 'bar'
			],
			'key3' => [
				'index' => 2,
				'key'   => 'key3',
				'foo'   => 'bar'
			]
		];
		$result   = $collection->getAll();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::add
	 * @covers \Xicrow\PhpDebug\Collection::get
	 * @covers \Xicrow\PhpDebug\Collection::update
	 */
	public function testUpdate() {
		$collection = new Collection();

		$collection->add('key', ['foo' => 'bar']);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->update('key', ['bar' => 'foo']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key',
			'bar'   => 'foo'
		];
		$result   = $collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $collection->update('non-existing-key', ['bar' => 'foo']);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\PhpDebug\Collection::__construct
	 * @covers \Xicrow\PhpDebug\Collection::rewind
	 * @covers \Xicrow\PhpDebug\Collection::current
	 * @covers \Xicrow\PhpDebug\Collection::next
	 * @covers \Xicrow\PhpDebug\Collection::prev
	 * @covers \Xicrow\PhpDebug\Collection::key
	 * @covers \Xicrow\PhpDebug\Collection::valid
	 * @covers \Xicrow\PhpDebug\Collection::count
	 */
	public function testInheritedMethods() {
		$collection = new Collection([1, 2, 3]);

		$expected = 1;
		$result   = $collection->current();
		$this->assertEquals($expected, $result);

		$expected = 2;
		$result   = $collection->next();
		$this->assertEquals($expected, $result);

		$expected = 3;
		$result   = $collection->next();
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $collection->next();
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = $collection->rewind();
		$this->assertEquals($expected, $result);

		$expected = 2;
		$result   = $collection->next();
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = $collection->prev();
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = $collection->key();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $collection->valid();
		$this->assertEquals($expected, $result);

		$expected = 3;
		$result   = $collection->count();
		$this->assertEquals($expected, $result);
	}
}
