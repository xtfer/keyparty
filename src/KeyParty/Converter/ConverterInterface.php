<?php

/**
 * @file
 * Contains a ConverterInterface.
 */

namespace KeyParty\Converter;

/**
 * Class ConverterInterface
 *
 * @package KeyParty\Converter
 */
interface ConverterInterface {

  /**
   * Runs conversions on the data prior to storage.
   *
   * @param mixed $data
   *   The incoming data.
   *
   * @return mixed
   *   The data, converted.
   */
  public function toStore($data);

  /**
   * Runs conversions on the data prior after storage retrieval.
   *
   * @param mixed $data
   *   The incoming data.
   *
   * @return mixed
   *   The data, converted.
   */
  public function fromStore($data);
}
