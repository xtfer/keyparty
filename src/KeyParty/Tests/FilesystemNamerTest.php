<?php

/**
 * @file
 * Contains a FilesystemNamerTest.
 */


namespace KeyParty\Tests;

use KeyParty\Utility\FilesystemNamer;

/**
 * Class FilesystemNamerTest
 *
 * @package KeyParty\Filesystem
 */
class FilesystemNamerTest extends \PHPUnit_Framework_TestCase {

  public function testFileName() {

    $file_name = FilesystemNamer::buildTableFileName('bar');
    $this->assertEquals('bar.dat', $file_name);

    $file_name = FilesystemNamer::buildTableFileName('bar', 'json');
    $this->assertEquals('bar.json', $file_name);

    $file_name = FilesystemNamer::buildTableFileName('bar', '.json');
    $this->assertEquals('bar.json', $file_name);
  }
}
