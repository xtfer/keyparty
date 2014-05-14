<?php

/**
 * @file
 * Contains a ConvertibleObject mock class.
 */

namespace KeyParty\Tests\Mocks;

use KeyParty\Converter\ConvertableObjectInterface;

/**
 * Class ConvertibleObject
 *
 * @package KeyParty\Tests\Mocks
 */
class ConvertibleObject implements ConvertableObjectInterface {

  public $one;
  public $two;

  /**
   * Constructor.
   */
  public function __construct() {
    $this->one = 'foo';
    $this->two = 'bar';
  }

  /**
   * Creates an object from data contained in KeyParty.
   *
   * @param mixed $data
   *   The data.
   */
  public function keyPartySetPropertiesFromData($data) {

    $this->one = $data['one'];
    $this->two = $data['two'];
  }

  /**
   * Extract object properties for writing to KeyParty.
   *
   * @return array
   *   The data
   */
  public function keyPartyGetObjectData() {

    return array(
      'one' => $this->one,
      'two' => $this->two,
    );
  }

}
