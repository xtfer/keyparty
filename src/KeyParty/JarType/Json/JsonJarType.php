<?php

/**
 * @file
 * Contains a JsonJarType
 */

namespace KeyParty\JarType\Json;

use KeyParty\JarType\JarTypeBase;
use KeyParty\StoreAdapter\JsonStoreAdapter;
use KeyParty\JarType\JarTypeInterface;
use KeyParty\Serializer\JsonSerializer;
use KeyParty\Serializer\SerializerInterface;
use KeyParty\Validator\JsonValidator;
use KeyParty\Validator\ValidatorInterface;
use KeyParty\StoreAdapter\StoreAdapterInterface;

/**
 * Class JsonJarType
 *
 * @package KeyParty\JarType
 */
class JsonJarType extends JarTypeBase implements JarTypeInterface {

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

}
