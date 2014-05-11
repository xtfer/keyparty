<?php

/**
 * @file
 * Contains CommonTestMethods for KeyParty
 */

namespace KeyParty\Tests;

use KeyParty\KeyParty;
use KeyParty\Exception\DeleteDatabaseException;

/**
 * Trait CommonTestMethods
 *
 * @package KeyParty\Tests
 */
trait CommonTestMethods {

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

    $this->keyparty = new KeyParty(__DIR__ . '/../../../bin/test');
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown() {

    try {
      $this->keyparty->deleteJar('test');
    }
    catch (DeleteDatabaseException $e) {
      // Nothing to do. This exception is notify on accidental deletion.
    }
  }

  /**
   * Get the Jar type for testing.
   *
   *
   * @return string
   *   A valid jar type.
   */
  public function getTestJarType() {

    return KeyParty::DEFAULT_JAR_TYPE;
  }
}
