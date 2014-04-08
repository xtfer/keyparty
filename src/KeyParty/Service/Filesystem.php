<?php

/**
 * @file
 * Contains a Filesystem Service.
 */

namespace KeyParty\Service;

use Gaufrette\Adapter\Local;
use Gaufrette\Adapter;

/**
 * Class Filesystem
 *
 * @package KeyParty
 */
class Filesystem {

  /**
   * Connect and load the filesystem.
   *
   * @param string $directory
   *   Directory where the filesystem is located
   * @param bool $create
   *   Whether to create the directory if it does not exist (default FALSE)
   * @param int $mode
   *   Mode for mkdir
   *
   * @return \Gaufrette\Filesystem
   *   A Gaufrette Filesystem.
   */
  public function load($directory, $create = FALSE, $mode = 0777) {
    $adapter = $this->getAdapter($directory, $create, $mode);
    return $this->getFilesystem($adapter);
  }

  /**
   * Get the Gaufrette adapter.
   *
   * @param string $directory
   *   Directory where the filesystem is located
   * @param bool $create
   *   Whether to create the directory if it does not exist (default FALSE)
   * @param int $mode
   *   Mode for mkdir
   *
   * @return Local
   *   A Gaufrette Local Adaptor
   */
  public function getAdapter($directory, $create = FALSE, $mode = 0777) {

    return new Local($directory, $create, $mode);
  }

  /**
   * Get the Gaufrette adapter.
   *
   * @param Adapter $adapter
   *   A Gaufrette adapter.
   *
   * @return \Gaufrette\Filesystem
   *   A Gaufrette Filesystem.
   */
  public function getFilesystem(Adapter $adapter) {

    return new \Gaufrette\Filesystem($adapter);
  }
}
