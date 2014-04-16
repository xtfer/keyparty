<?php

/**
 * @file
 * ValidationServiceTest
 */


namespace KeyParty\Tests;

use KeyParty\Exception\InvalidKeyException;
use KeyParty\Validator\JsonValidator;

/**
 * Class ValidationServiceTest
 *
 * @package KeyParty\Service
 */
class ValidationServiceTest extends \PHPUnit_Framework_TestCase {

  public function testValidDatabaseName() {

    $validation = new JsonValidator();

    $this->assertTrue($validation->isValidDatabaseName('foo'));

    $this->assertTrue($validation->isValidDatabaseName('foo_bar'));

    try {
      $this->assertFalse($validation->isValidDatabaseName('my house'));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }

    try {
      $this->assertFalse($validation->isValidDatabaseName(' start_with_space'));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }

  }

  public function testValidKey() {

    $validation = new JsonValidator();

    $this->assertTrue($validation->isValidKey('foo'));

    $this->assertTrue($validation->isValidKey('edea4300-c228-11e3-8a33-0800200c9a66'));

    try {
      $this->assertTrue($validation->isValidKey(1));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }

    try {
      $this->assertTrue($validation->isValidKey('1'));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }

    try {
      $this->assertFalse($validation->isValidKey(''));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }

    try {
      // This should be 1 more character than the maximum key value.
      $this->assertFalse($validation->isValidKey('123456789012345678901234567890123456789012345678901'));
    }
    catch (InvalidKeyException $e) {
      $this->assertTrue(TRUE);
    }
  }


}
