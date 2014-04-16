<?php

/**
 * @file
 * Contains a CacheServiceTest
 */


namespace KeyParty\Tests;

use KeyParty\Cache\StaticCache;

/**
 * Class CacheServiceTest
 *
 * @package KeyParty\Service
 */
class CacheServiceTest extends \PHPUnit_Framework_TestCase {

  /**
   * The cacheService variable.
   *
   * @var StaticCache
   */
  protected $cacheService;

  public function setUp() {

    $this->cacheService = new StaticCache();
  }

  /**
   * Tests the get and set.
   */
  public function testSetGet() {

    $this->cacheService->set('item_1', array('foo', 'bar'));

    $result = $this->cacheService->get('item_1');

    $this->assertArrayHasKey(0, $result);
    $this->assertEquals('foo', $result[0]);
  }

  public function testClear() {

    $this->cacheService->clear();

    $empty = $this->cacheService->get('foo');

    $this->assertEmpty($empty);
  }

  public function testRemove() {

    $this->cacheService->set('item_2', array('foo', 'bar'));

    $result = $this->cacheService->get('item_2');

    $this->assertArrayHasKey(0, $result);
    $this->assertEquals('foo', $result[0]);

    $this->cacheService->remove('item_2');

    $result = $this->cacheService->get('item_2');

    $this->assertEmpty($result);
  }
}
