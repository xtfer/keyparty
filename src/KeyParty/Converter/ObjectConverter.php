<?php

/**
 * @file
 * Contains an ObjectConverter converter.
 */

namespace KeyParty\Converter;

use KeyParty\Exception\KeyPartyException;
use SebastianBergmann\Exporter\Exception;

/**
 * Class ObjectConverter
 *
 * @package KeyParty\Converter
 */
class ObjectConverter implements ConverterInterface {

  /**
   * Runs conversions on the data prior to storage.
   *
   * @param mixed $data
   *   The incoming data.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return mixed
   *   The data, converted.
   */
  public function toStore($data) {

    if (!is_object($data)) {

      return $data;
    }

    if ($data instanceof ConvertableObjectInterface) {

      $return_data = $data->keyPartyGetObjectData();
    }
    else {

      try {
        $return_data = (array) $data;
      }
      catch (Exception $e) {
        throw new KeyPartyException('Unable to convert object');
      }
    }

    $class_name = get_class($data);
    $return_data['_class_name'] = $class_name;

    return $return_data;
  }

  /**
   * Runs conversions on the data prior after storage retrieval.
   *
   * @param mixed $data
   *   The incoming data.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return mixed
   *   The data, converted.
   */
  public function fromStore($data) {
    $data_clone = (array) $data;

    if (isset($data_clone['_class_name']) && !empty($data_clone['_class_name'])) {
      if (class_exists($data_clone['_class_name'])) {

        $reflection = new \ReflectionClass($data_clone['_class_name']);

        $interfaces = $reflection->getInterfaces();

        // Attempt to write data using the compatibility interfaces first.
        if (in_array('KeyParty\\Converter\\ConvertableObjectInterface', $interfaces)) {
          $mock = new $data_clone['_class_name']();
          $mock->createObjectFromKeyPartyData($data_clone);
        }
        else {
          try {
            $mock = new $data_clone['_class_name']();

            foreach ($data_clone as $key => $value) {
              $mock->$key = $value;
            }
          }
          catch(\Exception $e) {
            throw new KeyPartyException('Unable to create object.');
          }
        }

        if (isset($mock)) {
          return $mock;
        }

        return $data;
      }
    }
  }

}
