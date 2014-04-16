<?php

/**
 * @file
 * Contains a JsonServiceTest
 */


namespace KeyParty\Tests;

use KeyParty\Serializer\JsonSerializer;

/**
 * Class JsonServiceTest
 *
 * @package KeyParty\Service
 */
class JsonServiceTest extends \PHPUnit_Framework_TestCase {

  public function testEncode() {
    $json = new JsonSerializer();
    $data = $this->sampleData();

    $encoded = $json->encode($data);

    // We normalise to an array, so do so here too.
    $result = (array) json_decode($encoded);

    foreach ($data as $key => $value) {
      $this->assertArrayHasKey($key, $result);
      $this->assertEquals($value, $result[$key], 'Failed on ' . $key);
    }
  }

  public function testDecode() {
    $data = $this->sampleData();

    $encoded = json_encode($data, JSON_PRETTY_PRINT);

    $json = new JsonSerializer();

    $decoded = $json->decode($encoded);

    foreach ($data as $key => $value) {
      $this->assertEquals($value, $decoded[$key]);
    }

  }

  public function sampleData() {

    $data = array(
      'integer' => 1,
      'float' => 1.34,
      'string' => 'some string',
      'array' => array('one', 'two', 'three'),
      'object' => new \stdClass(),
    );

    return $data;
  }
}
