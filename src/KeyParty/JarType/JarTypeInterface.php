<?php

/**
 * @file
 * Contains a JarTypeInterface
 */

namespace KeyParty\JarType;

use KeyParty\StoreAdapter\StoreAdapterInterface;
use KeyParty\Validator\ValidatorInterface;
use KeyParty\Cache\CacheInterface;
use KeyParty\Serializer\SerializerInterface;
use KeyParty\Jar\JarInterface;

/**
 * Interface JarTypeInterface
 *
 * @package KeyParty\JarType
 */
interface JarTypeInterface {

  /**
   * Get the cache.
   *
   * @return CacheInterface
   *   A Cache object
   */
  public function getCache();

  /**
   * Create a table handler.
   *
   * @param string $jar_name
   *   Name of the Jar to access.
   *
   * @return JarInterface
   *   A Jar object.
   */
  public function getJar($jar_name);

  /**
   * Get the Serializer service.
   *
   * @return SerializerInterface
   *   A Serializer.
   */
  public function getSerializer();

  /**
   * Get the StoreAdapter.
   *
   * @param array $options
   *   Any options to be passed in.
   *
   * @return StoreAdapterInterface
   *   A StoreAdapter
   */
  public function getStoreAdapter(array $options = array());

  /**
   * Get the Validation service.
   *
   * @return ValidatorInterface
   *   A Validation service.
   */
  public function getValidator();
}
