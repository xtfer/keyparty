<?php

/**
 * @file
 * Contains the Validation service.
 */

namespace KeyParty\Validator;

use KeyParty\Exception\InvalidKeyException;

/**
 * Class JsonValidator
 *
 * @package KeyParty\Service
 */
class JsonValidator implements ValidatorInterface {

  /**
   * The minimum key length.
   */
  const MINIMUM_KEY_LENGTH = 1;

  /**
   * The maximum key length.
   */
  const MAXIMUM_KEY_LENGTH = 50;

  /**
   * Check the database has been loaded and valid key.
   *
   * @param string $key
   *   Key of the item to delete
   *
   * @throws InvalidKeyException
   * @return bool
   *   TRUE if Key of the item to delete is valid.
   */
  public function isValidKey($key) {

    if (is_array($key) || is_object($key)) {
      throw new InvalidKeyException('Objects and arrays cannot be used as keys.');
    }

    // Check key length.
    $len = strlen($key);

    if (filter_var($key, FILTER_VALIDATE_INT)) {
      throw new InvalidKeyException('JSON does not support integer keys.');
    }

    if ($len < 0) {
      throw new InvalidKeyException('No key has been set, or provided key is empty.');
    }

    if ($len < self::MINIMUM_KEY_LENGTH) {
      throw new InvalidKeyException('Key is too short.');
    }

    if ($len > self::MAXIMUM_KEY_LENGTH) {
      throw new InvalidKeyException('Key exceeds maximum key length.');
    }

    return TRUE;
  }

  /**
   * Check if the Database name is valid.
   *
   * @param string $name
   *   The name to check.
   *
   * @throws \KeyParty\Exception\InvalidKeyException
   * @return bool
   *   TRUE if the name is valid. FALSE if not.
   */
  public function isValidDatabaseName($name) {

    $is_valid = (bool) preg_match("/^([A-Za-z0-9_]+)$/", $name);

    if ($is_valid == FALSE) {
      throw new InvalidKeyException('Invalid characters in database name');
    }

    return TRUE;
  }
}
