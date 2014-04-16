<?php

/**
 * @file
 * Contains a JsonStoreAdapterTest.
 */

namespace KeyParty\Tests;

use KeyParty\StoreAdapter\JsonStoreAdapter;

/**
 * Class JsonStoreAdapterTest
 *
 * @package KeyParty\Service
 */
class JsonStoreAdapterTest extends \PHPUnit_Framework_TestCase {

  /**
   * The tableManager variable.
   *
   * @var \KeyParty\StoreAdapter\JsonStoreAdapter
   */
  protected $adapter;

  public function setUp() {

    $this->adapter = new JsonStoreAdapter(array());
  }

  public function sampleData() {

    $data = array(
      'integer' => 1,
      'float' => 1.34,
      'string' => 'some string',
      'array' => array('one', 'two', 'three'),
      'object' => new \stdClass(),
    );

    return $data;
  }

  public function testCreate() {
    $result = $this->adapter->createStore('foo');

    $this->assertTrue($result);
  }

  public function testWrite() {

    $result = $this->adapter->writeToStore('foo', json_encode(array('bar' => 'baz')));

    $this->assertTrue($result);
  }

  public function testRead() {

    $data = $this->adapter->readFromStore('foo');

    $data = (array) json_decode($data);

    $this->assertArrayHasKey('bar', $data);
    $this->assertEquals('baz', $data['bar']);
  }

  public function testEmpty() {

    $result = $this->adapter->emptyStore('foo');
    $this->assertTrue($result);

    $data = $this->adapter->readFromStore('foo');

    $data = (array) json_decode($data);

    $this->assertEmpty($data);
  }

  public function testRemove() {
    $result = $this->adapter->removeStore('foo');
    $this->assertTrue($result);
  }
}
