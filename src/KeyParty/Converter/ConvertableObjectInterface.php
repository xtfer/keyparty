<?php

/**
 * @file
 * Contains an ObjectCreatorConverter.
 */

namespace KeyParty\Converter;

/**
 * Interface ObjectCreatorConverter
 *
 * @package KeyParty\Converter
 */
interface ConvertableObjectInterface {

  /**
   * Creates an object from data contained in KeyParty.
   *
   * @param mixed $data
   *   The data.
   */
  public function keyPartySetPropertiesFromData($data);

  /**
   * Extract object properties for writing to KeyParty.
   *
   * @return array
   *   The data
   */
  public function keyPartyGetObjectData();
}
