<?php

/**
 * @file
 * Contains a SerializerInterface.
 */

namespace KeyParty\Serializer;

use KeyParty\Exception\KeyPartyException;

/**
 * Interface SerializerInterface
 *
 * @package KeyParty\Serializer
 */
interface SerializerInterface {

  /**
   * Decode data on its way from the database.
   *
   * @param string $data
   *   The data.
   *
   * @return array
   *   An array of data.
   */
  public function decode($data);

  /**
   * Encode data on its way to the database.
   *
   * @param array $data
   *   The data to write.
   *
   * @return string
   *   An encoded string.
   */
  public function encode($data);
}
