<?php

/**
 * @file
 * Contains a KeyParty Test.
 */


namespace KeyParty\Tests;

use KeyParty\Exception\DeleteDatabaseException;
use KeyParty\KeyParty;

/**
 * Class KeyPartyTest
 *
 * @package KeyParty
 */
class KeyPartyTest extends \PHPUnit_Framework_TestCase {

  /**
   * The keyparty variable.
   *
   * @var \KeyParty\KeyParty
   */
  public $keyparty;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {

    $system_path = getcwd();
    $this->keyparty = new KeyParty('test', $system_path . '/../../../bin/test', 'json', FALSE);
  }

  /**
   * Main test method.
   *
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   * @throws \PHPUnit_Framework_Exception
   */
  public function test() {

    // Tests flush and empty creation.
    $this->keyparty->emptyDatabase();

    $this->assertEmpty($this->keyparty->getKeys(), 'No items found');

    // Test both get and set.
    $data = array(
      'integer' => 1,
      'float' => 1.34,
      'string' => 'some string',
      'single quotes' => "a string containing 'single quotes'",
      'double quotes' => 'a string containing "double quotes"',
      'array' => array('one', 'two', 'three'),
      'object' => new \stdClass(),
    );

    $this->keyparty->set('1', $data);

    $item = $this->keyparty->get('1');

    $this->assertArrayHasKey('integer', $item);
    $this->assertEquals($item['integer'], 1);

    // Test adding another key and getKeys.
    $data['another_key'] = 'foo';

    $this->keyparty->set('2', $data);

    $items = $this->keyparty->getKeys();

    $this->assertEquals(2, count($items));

    // Test deletion.
    $this->keyparty->delete('1');

    $items = $this->keyparty->getKeys();

    $this->assertEquals(1, count($items));
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {

    try {
      $this->keyparty->removeDatabase();
    }
    catch(DeleteDatabaseException $e) {
      // Nothing to do. This exception is notify on accidental deletion.
    }

  }

}
