<?php

/**
 * @file
 * Contains a ValidatorInterface.
 */

namespace KeyParty\Validator;

use KeyParty\Exception\InvalidKeyException;

/**
 * Interface ValidatorInterface
 *
 * @package KeyParty\Validator
 */
interface ValidatorInterface {

  /**
   * Check if the Database name is valid.
   *
   * @param string $name
   *   The name to check.
   *
   * @throws InvalidKeyException
   * @return bool
   *   TRUE if the name is valid.
   */
  public function isValidDatabaseName($name);

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
  public function isValidKey($key);
}
