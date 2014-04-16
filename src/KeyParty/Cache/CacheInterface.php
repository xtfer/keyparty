<?php

/**
 * @file
 * Contains a CacheInterface
 */

namespace KeyParty\Cache;

/**
 * Interface CacheInterface
 *
 * @package KeyParty\Cache
 */
interface CacheInterface {

  /**
   * Clear the cache entirely.
   */
  public function clear();

  /**
   * Get a value from the key.
   *
   * @param string $key
   *   The cache key.
   *
   * @return mixed|null
   *   Result of the cache get.
   */
  public function get($key);

  /**
   * Remove a key from the cache.
   *
   * @param string $key
   *   The cache key.
   */
  public function remove($key);

  /**
   * Set a cache value.
   *
   * @param string $key
   *   The cache key.
   * @param mixed $value
   *   The value.
   */
  public function set($key, $value);
}
