<?php

/**
 * @file
 * Contains an ObjectConverterTest
 */


namespace KeyParty\Tests;

use KeyParty\Converter\ObjectConverter;
use KeyParty\Tests\Mocks\ConvertibleObject;

/**
 * Class ObjectConverterTest
 *
 * @package KeyParty\Tests
 */
class ObjectConverterTest extends \PHPUnit_Framework_TestCase {

  /**
   * Test the object creator.
   */
  public function testCreateObject() {
    $converter = new ObjectConverter();

    $result = $converter->createObject('stdClass', array(
      'one' => 'foo',
      'two' => 'bar',
    ));

    $this->assertEquals('stdClass', get_class($result));
    $this->assertEquals('foo', $result->one);
  }

  /**
   * Test the supportedObjectCreator.
   */
  public function testSupportedObjectCreator() {

    $converter = new ObjectConverter();

    $result = $converter->createSupportedObject('KeyParty\\Tests\\Mocks\\ConvertibleObject', array(
      'one' => 'foo',
      'two' => 'bar',
    ));

    $this->assertEquals('KeyParty\\Tests\\Mocks\\ConvertibleObject', get_class($result));
    $this->assertEquals('foo', $result->one);
  }

  /**
   * Test toStore().
   */
  public function testToStore() {

    $converter = new ObjectConverter();

    // Test convertable object.
    $convertable = new ConvertibleObject();
    $result = $converter->toStore($convertable);

    $this->assertArrayHasKey('one', $result);
    $this->assertArrayHasKey('two', $result);
    $this->assertEquals('foo', $result['one']);
    $this->assertEquals('bar', $result['two']);

    // Test raw data.
    $data = array(
      'one' => 'foo',
      'two' => 'bar',
    );

    $result = $converter->toStore($data);

    $this->assertArrayHasKey('one', $result);
    $this->assertArrayHasKey('two', $result);
    $this->assertEquals('foo', $result['one']);
    $this->assertEquals('bar', $result['two']);

    // Test (array) syntax.
    $new_object = new \stdClass();
    $new_object->one = 'foo';
    $new_object->two = 'bar';

    $result = $converter->toStore($new_object);

    $this->assertArrayHasKey('one', $result);
    $this->assertArrayHasKey('two', $result);
    $this->assertEquals('foo', $result['one']);
    $this->assertEquals('bar', $result['two']);
  }

  /**
   * Test fromStore().
   */
  public function testFromStore() {

    $converter = new ObjectConverter();

    $source = array(
      'one' => 'foo',
      'two' => 'bar',
    );

    // Test (array) syntax.
    $result = $converter->fromStore($source);

    $this->assertArrayHasKey('one', $result);
    $this->assertArrayHasKey('two', $result);
    $this->assertEquals('foo', $result['one']);
    $this->assertEquals('bar', $result['two']);

    // Test convertable object.
    $source['_class_name'] = 'KeyParty\\Tests\\Mocks\\ConvertibleObject';

    $result = $converter->fromStore($source);

    $this->assertEquals('KeyParty\\Tests\\Mocks\\ConvertibleObject', get_class($result));
    $this->assertEquals('foo', $result->one);
  }
}
