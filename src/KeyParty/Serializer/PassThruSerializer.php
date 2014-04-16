<?php

/**
 * @file
 * Contains a PassThruSerializer
 */

namespace KeyParty\Serializer;

/**
 * Class PassThruSerializer
 *
 * The PassThruSerializer can be used by services which don't require
 * serializing before storage.
 *
 * @package KeyParty\Serializer
 */
class PassThruSerializer implements SerializerInterface {

  /**
   * Decode data on its way from the database.
   *
   * @param string $data
   *   The data.
   *
   * @return array
   *   An array of data.
   */
  public function decode($data) {

    return $data;
  }

  /**
   * Encode data on its way to the database.
   *
   * @param array $data
   *   The data to write.
   *
   * @return string
   *   An encoded string.
   */
  public function encode($data) {

    return $data;
  }

}
