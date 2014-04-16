<?php

/**
 * @file
 * Contains a JsonJarType
 */

namespace KeyParty\JarType\Json;

use KeyParty\Filesystem\GaufretteFactory;
use KeyParty\StoreAdapter\JsonStoreAdapter;
use KeyParty\Cache\StaticCache;
use KeyParty\JarType\JarTypeInterface;
use KeyParty\Serializer\JsonSerializer;
use KeyParty\Serializer\SerializerInterface;
use KeyParty\Validator\JsonValidator;
use KeyParty\Validator\ValidatorInterface;
use KeyParty\Jar\Jar;
use KeyParty\Filesystem\FilesystemFactoryInterface;
use KeyParty\Cache\CacheInterface;
use KeyParty\StoreAdapter\StoreAdapterInterface;
use KeyParty\Jar\JarInterface;

/**
 * Class JsonJarType
 *
 * @package KeyParty\JarType
 */
class JsonJarType implements JarTypeInterface {

  /**
   * Get the GaufretteFactory.
   *
   * @return FilesystemFactoryInterface
   *   A FilesystemFactoryInterface object.
   */
  public function getFilesystemFactory() {

    return new GaufretteFactory();
  }

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
   * Get the Serializer service.
   *
   * @return SerializerInterface
   *   A Serializer.
   */
  public function getSerializer() {

    return new JsonSerializer();
  }

  /**
   * Get the StoreAdapter.
   *
   * @param array $options
   *   Any options to be passed in.
   *
   * @return StoreAdapterInterface
   *   A StoreAdapter
   */
  public function getStoreAdapter(array $options = array()) {

    return new JsonStoreAdapter($options);
  }

  /**
   * Get the Validation service.
   *
   * @return ValidatorInterface
   *   A Validation service.
   */
  public function getValidator() {

    return new JsonValidator();
  }

  /**
   * Create a table handler.
   *
   * @param string $jar_name
   *   The jar name.
   *
   * @return JarInterface
   *   A Jar object.
   */
  public function getJar($jar_name) {

    $jar = new Jar($this, $jar_name);

    return $jar;
  }
}
