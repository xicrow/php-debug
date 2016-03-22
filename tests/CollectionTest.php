<?php
use Xicrow\Debug\Collection;

/**
 * Class CollectionTest
 */
class CollectionTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var null|Collection
	 */
	public $collection = null;

	/**
	 * @inheritdoc
	 * @covers \Xicrow\Debug\Collection::__construct
	 */
	public function __construct($name = null, array $data = [], $dataName = '') {
		parent::__construct($name, $data, $dataName);

		$this->collection = new Collection();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::__toString
	 */
	public function testToString() {
		$expected = print_r([], true);
		$result   = (string) $this->collection;
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::add
	 */
	public function testAdd() {
		$expected = true;
		$result   = $this->collection->add('key', ['foo' => 'bar']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $this->collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->add('key', ['foo' => 'bar']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key #1'
		];
		$result   = $this->collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $this->collection->get('key #1');
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 1,
			'key'   => 'key #2'
		];
		$result   = $this->collection->get('key #2');
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::clear
	 */
	public function testClear() {
		$expected = 0;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->add('key1', ['foo' => 'bar']);

		$expected = 1;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->add('key2', ['foo' => 'bar']);

		$expected = 2;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $this->collection->clear('non-exiting-key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->clear('key1');
		$this->assertEquals($expected, $result);

		$expected = 1;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->clear('key2');
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->add('key1', ['foo' => 'bar']);
		$this->collection->add('key2', ['foo' => 'bar']);
		$this->collection->add('key3', ['foo' => 'bar']);

		$expected = 3;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->clear();
		$this->assertEquals($expected, $result);

		$expected = 0;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::count
	 */
	public function testCount() {
		$expected = 0;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->add('key', ['foo' => 'bar']);

		$expected = 1;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->add('key', ['foo' => 'bar']);

		$expected = 2;
		$result   = $this->collection->count();
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::exists
	 */
	public function testExists() {
		$expected = false;
		$result   = $this->collection->exists('key');
		$this->assertEquals($expected, $result);

		$this->collection->add('key', ['foo' => 'bar']);

		$expected = true;
		$result   = $this->collection->exists('key');
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::get
	 * @covers \Xicrow\Debug\Collection::getAll
	 */
	public function testGet() {
		$expected = false;
		$result   = $this->collection->get('non-existing-key');
		$this->assertEquals($expected, $result);

		$this->collection->add('key', ['foo' => 'bar']);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $this->collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = 'array';
		$result   = $this->collection->getAll();
		$this->assertInternalType($expected, $result);

		$expected = 1;
		$result   = count($this->collection->getAll());
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::sort
	 */
	public function testSort() {
		$this->collection->add('key1', ['foo' => 'bar']);
		$this->collection->add('key2', ['foo' => 'bar']);
		$this->collection->add('key3', ['foo' => 'bar']);

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
		$result   = $this->collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->sort('key', 'desc');
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
		$result   = $this->collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->sort('key', 'asc');
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
		$result   = $this->collection->getAll();
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->sort('foo', 'asc');
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
		$result   = $this->collection->getAll();
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}

	/**
	 * @test
	 * @covers \Xicrow\Debug\Collection::update
	 */
	public function testUpdate() {
		$this->collection->add('key', ['foo' => 'bar']);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key'
		];
		$result   = $this->collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = true;
		$result   = $this->collection->update('key', ['bar' => 'foo']);
		$this->assertEquals($expected, $result);

		$expected = [
			'foo'   => 'bar',
			'index' => 0,
			'key'   => 'key',
			'bar'   => 'foo'
		];
		$result   = $this->collection->get('key');
		$this->assertEquals($expected, $result);

		$expected = false;
		$result   = $this->collection->update('non-existing-key', ['bar' => 'foo']);
		$this->assertEquals($expected, $result);

		$this->collection->clear();
	}
}
