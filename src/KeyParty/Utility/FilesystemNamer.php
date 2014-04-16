<?php

/**
 * @file
 * Contains a FilesystemNamer.
 */

namespace KeyParty\Utility;

/**
 * Class FilesystemNamer
 *
 * @package KeyParty\Filesystem
 */
class FilesystemNamer {

  /**
   * Build a table filename from the table name.
   *
   * @param string $table_name
   *   Name of the table.
   * @param string $extension
   *   Extension to use. Defaults to 'dat'
   *
   * @return string
   *   A valid table file name.
   */
  static public function buildTableFileName($table_name, $extension = 'dat') {

    if (substr($extension, 0, 1) !== ".") {
      $extension = "." . $extension;
    }

    $file_name = $table_name . $extension;

    return $file_name;
  }
}
