<?php

/**
 * @file
 * Contains a JarTypeBase class
 */

namespace KeyParty\JarType;

use KeyParty\Exception\KeyPartyException;
use KeyParty\Jar\Jar;
use KeyParty\Jar\JarInterface;
use KeyParty\Cache\CacheInterface;
use KeyParty\Cache\StaticCache;

/**
 * Class JarTypeBase
 *
 * @package KeyParty\JarType
 */
abstract class JarTypeBase implements JarTypeInterface {

  /**
   * The options variable.
   *
   * @var array
   */
  protected $options;

  /**
   * Get the cache.
   *
   * @return CacheInterface
   *   A Cache object
   */
  public function getCache() {

    $is_caching = TRUE;
    if (isset($this->options['cache'])) {
      $is_caching = $this->options['cache'];
    }

    return new StaticCache($is_caching);
  }

  /**
   * Create a table handler.
   *
   * @param string $jar_name
   *   The jar name.
   * @param bool $is_creatable
   *   TRUE if the jar can be created if not present.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return JarInterface
   *   A Jar object.
   */
  public function getJar($jar_name, $is_creatable = FALSE) {

    $jar = new Jar($this, $jar_name);

    // If the store doesn't have record of the Jar, we may have to physically
    // create it.
    if (!$this->getStoreAdapter()->storeExists($jar_name)) {

      if ($is_creatable != TRUE) {
        throw new KeyPartyException(sprintf('Invalid Jar "%s" requested. Do you need to create this Jar?', $jar_name));
      }

      // Initialise the Jar with blank data. This is important in case the Jar
      // hasn't auto-created itself.
      $jar->writeData($jar_name, array());
    }

    return $jar;
  }
}
