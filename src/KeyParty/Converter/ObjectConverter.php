<?php

/**
 * @file
 * Contains an ObjectConverter converter.
 */

namespace KeyParty\Converter;

use KeyParty\Exception\KeyPartyException;

/**
 * Class ObjectConverter
 *
 * @package KeyParty\Converter
 */
class ObjectConverter implements ConverterInterface {

  /**
   * Create a new object.
   *
   * @param string $class_name
   *   The class name.
   * @param array $data
   *   An array of data.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return mixed
   *   The class, or NULL.
   */
  public function createObject($class_name, $data) {

    try {
      $new_object = new $class_name();

      foreach ($data as $key => $value) {
        $new_object->$key = $value;
      }
    }
    catch (\Exception $e) {
      throw new KeyPartyException('Unable to create object.');
    }

    return $new_object;
  }

  /**
   * Create a class.
   *
   * @param string $class_name
   *   The class name.
   * @param array $data
   *   An array of data.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return mixed
   *   The class, or NULL.
   */
  public function createSupportedObject($class_name, $data) {
    $new_object = NULL;

    $reflection = new \ReflectionClass($class_name);
    $interfaces = $reflection->getInterfaces();

    // Attempt to write data using the compatibility interfaces first.
    if (in_array('KeyParty\\Converter\\ConvertableObjectInterface', $interfaces)) {
      $new_object = new $class_name();

      /* @var ConvertableObjectInterface $new_object */
      $new_object->keyPartySetPropertiesFromData($data);

      return $new_object;
    }

    // Try to create the object 'manually'.
    return $this->createObject($class_name, $data);
  }

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
      catch (\Exception $e) {
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

        $class_name = $data_clone['_class_name'];
        unset($data_clone['_class_name']);

        $new_object = $this->createSupportedObject($class_name, $data_clone);
      }
    }

    if (!empty($new_object)) {
      return $new_object;
    }

    return $data;
  }

}
