<?php

/**
 * @file
 * Contains a JSON service.
 */

namespace KeyParty\Serializer;
use KeyParty\Exception\KeyPartyException;

/**
 * Class JsonSerializer
 *
 * @package KeyParty\Service
 */
class JsonSerializer implements SerializerInterface {

  /**
   * Write data to the database.
   *
   * @param array $data
   *   The data to write.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws KeyPartyException
   *
   * @return string
   *   A JSON encoded string.
   */
  public function encode($data) {

    // Normalise objects to arrays before saving.
    $data = (array) $data;

    // @todo
    // Allow options to be passed to json_encode().
    $encoded = json_encode($data, JSON_PRETTY_PRINT);

    $error = $this->jsonLastErrorMsg();
    if (!empty($error)) {

      throw new KeyPartyException(sprintf('Invalid JSON detected: %s', $error));
    }

    return $encoded;
  }

  /**
   * Decode JSON.
   *
   * @param string $data
   *   Raw JSON.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return array
   *   An array of JSON data.
   */
  public function decode($data) {

    $decoded = json_decode($data);

    $error = $this->jsonLastErrorMsg();
    if (!empty($error)) {

      throw new KeyPartyException(sprintf('Invalid JSON detected: %s', $error));
    }

    // json_decode returns an object. When returning an array, it flattens
    // internal objects as well. We convert it manually to preserve internal
    // objects.
    return (array) $decoded;
  }

  /**
   * Alternative to json_last_error_msg().
   *
   * This doesnt send a message when no errors are found.
   *
   * @see http://www.php.net/manual/en/function.json-last-error-msg.php
   *
   * @return NULL|string
   *   The last JSON error.
   */
  protected function jsonLastErrorMsg() {

    static $errors = array(
      JSON_ERROR_NONE => NULL,
      JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
      JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
      JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
      JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
      JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
    );
    $error = json_last_error();

    if ($error == JSON_ERROR_NONE) {

      return FALSE;
    }

    return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
  }
}
