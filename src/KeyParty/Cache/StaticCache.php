<?php

/**
 * @file
 * Contains the StaticCache.
 */

namespace KeyParty\Cache;

use KeyParty\Cache\CacheInterface;

/**
 * Class StaticCache
 *
 * @package KeyParty
 */
class StaticCache implements CacheInterface {

  /**
   * The data variable.
   *
   * @var array
   */
  protected $data;

  /**
   * The isCaching variable.
   *
   * @var bool
   */
  protected $isCaching;

  /**
   * Constructor.
   *
   * @param bool $is_caching
   *   TRUE if the object should be caching. FALSE to discard caching behaviour.
   */
  public function __construct($is_caching = TRUE) {
    $this->isCaching = $is_caching;
  }


  /**
   * Set a cache value.
   *
   * @param string $key
   *   The cache key.
   * @param mixed $value
   *   The value.
   */
  public function set($key, $value) {
    if ($this->isCaching == TRUE) {
      $this->data[$key] = $value;
    }
  }

  /**
   * Get a value from the key.
   *
   * @param string $key
   *   The cache key.
   *
   * @return mixed|null
   *   Result of the cache get.
   */
  public function get($key) {

    if (isset($this->data[$key]) && $this->isCaching == TRUE) {
      return $this->data[$key];
    }

    return NULL;
  }

  /**
   * Remove a key from the cache.
   *
   * @param string $key
   *   The cache key.
   */
  public function remove($key) {

    if (isset($this->data[$key])) {
      unset($this->data[$key]);
    }
  }

  /**
   * Clear the cache entirely.
   */
  public function clear() {

    $this->data = array();
  }
}
