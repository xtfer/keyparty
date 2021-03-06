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

  use CommonTestMethods;


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

    $this->keyparty->addJar('test', KeyParty::DEFAULT_JAR_TYPE, TRUE);
    $this->keyparty->emptyJar('test');

    $all = $this->keyparty->getAll('test');
    $this->assertEmpty($all, 'No items found');

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

    $this->keyparty->set('test', 'a', $data);

    $item = $this->keyparty->get('test', 'a');

    $this->assertArrayHasKey('integer', $item);
    $this->assertEquals($item['integer'], 1);

    // Test adding another key and getKeys.
    $this->keyparty->set('test', 'b', $data);

    $items = $this->keyparty->getAll('test');

    $this->assertEquals(2, count($items));

    // Test deletion.
    $this->keyparty->remove('test', 'a');

    $items = $this->keyparty->getAll('test');

    $this->assertEquals(1, count($items));
  }

}
