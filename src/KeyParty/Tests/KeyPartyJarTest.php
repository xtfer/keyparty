<?php

/**
 * @file
 * Contains a KeyPartyJarTest.
 */


namespace KeyParty\Tests;

use KeyParty\Exception\KeyPartyException;

/**
 * Class Test
 *
 * @package KeyParty\Tests
 */
class KeyPartyJarTest extends \PHPUnit_Framework_TestCase {

  use CommonTestMethods;

  /**
   * Test Jar Addition.
   */
  public function test() {

    try {
      $this->keyparty->addJar('test', $this->getTestJarType(), TRUE);
    }
    catch (KeyPartyException $e) {
      $this->fail('Auto create of Jar failed: ' . $e->getMessage());
    }

    try {
      $this->keyparty->addJar('test', $this->getTestJarType());
    }
    catch (KeyPartyException $e) {
      $this->fail('Reuse of existing Jar failed: ' . $e->getMessage());
    }
  }
}
