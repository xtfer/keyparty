<?php

/**
 * @file
 * Contains the JsonStoreAdapter.
 */

namespace KeyParty\StoreAdapter;

use Gaufrette\Adapter;
use Gaufrette\Adapter\Local;
use Gaufrette\Exception\FileAlreadyExists;
use Gaufrette\Filesystem;
use KeyParty\Exception\KeyPartyException;

/**
 * Class JsonStoreAdapter
 *
 * @package KeyParty\Service
 */
class JsonStoreAdapter implements StoreAdapterInterface {

  /**
   * The filesystem variable.
   *
   * @var \Gaufrette\Filesystem
   */
  protected $filesystem;

  /**
   * The settings variable.
   *
   * @var array
   */
  protected $options = array();

  /**
   * The filePath variable.
   *
   * @var string
   */
  protected $filePath;

  /**
   * Constructor.
   *
   * @param array $options
   *   Any options to be passed in.
   */
  public function __construct($options) {

    $this->options = $options;

    if (isset ($this->options['dir'])) {
      $this->filePath = $this->options['dir'];
      unset($this->options['dir']);
    }
    else {
      $this->filePath = 'data';
    }
  }

  /**
   * Set the file path.
   *
   * @param string $file_path
   *   The file path.
   */
  public function setFilePath($file_path) {
    $this->filePath = $file_path;
  }

  /**
   * Create a store.
   *
   * This will throw a KeyPartyException if the store already exists. If you
   * need an empty store, try emptyStore().
   *
   * @param string $store_name
   *   Name of the store to create.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE if the file exists
   */
  public function createStore($store_name) {

    $file_name = $this->buildTableFileName($store_name);

    try {

      return $this->createBlankFile($file_name);
    }
    catch (FileAlreadyExists $e) {

      throw new KeyPartyException(sprintf('Attempt to create store failed. Jar %s already exists.', $store_name));
    }
  }

  /**
   * Empty a store.
   *
   * @param string $store_name
   *   Name of the store.
   *
   * @return bool
   *   TRUE if the file exists
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   */
  public function emptyStore($store_name) {

    $file_name = $this->buildTableFileName($store_name);

    // Write the file if its not present.
    try {

      return $this->createBlankFile($file_name);
    }
    catch (FileAlreadyExists $e) {
      // Do nothing, we're happy with that result.
    }
  }

  /**
   * Get the Gaufrette Filesystem for use.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return \Gaufrette\Filesystem
   *   The Filesystem.
   */
  public function getFilesystem() {

    if (empty($this->filesystem)) {
      $this->useFilesystem($this->filePath);
    }

    return $this->filesystem;
  }

  /**
   * Read data from a store.
   *
   * No decoding is performed.
   *
   * @param string $store_name
   *   Name of the store.
   *
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return string
   *   Usually, a raw JSON string.
   */
  public function readFromStore($store_name) {

    $file_name = $this->buildTableFileName($store_name);

    return $this->getFilesystem()->read($file_name);
  }

  /**
   * Delete a store.
   *
   * @param string $store_name
   *   Name of the store.
   *
   * @return bool
   *   TRUE if deleted.
   *
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   */
  public function removeStore($store_name) {

    $file_name = $this->buildTableFileName($store_name);

    return $this->getFilesystem()->delete($file_name);
  }

  /**
   * Write data to the filesystem.
   *
   * @param string $file_name
   *   The file name
   * @param array $data
   *   The data, in an array format.
   * @param bool $overwrite
   *   If TRUE, overwrite the file if it already exists.
   *
   * @return int
   *   The number of bytes written.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   */
  public function writeData($file_name, $data = array(), $overwrite = FALSE) {

    return $this->getFilesystem()->write($file_name, $data, $overwrite);
  }

  /**
   * Write data to the database.
   *
   * @param string $store_name
   *   Jar to get the key from.
   * @param array $data
   *   The data to write.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE on a successful write.
   */
  public function writeToStore($store_name, $data) {

    $file_name = $this->buildTableFileName($store_name);

    try {
      $this->writeData($file_name, $data, TRUE);
    }
    catch (\RuntimeException $e) {
      throw new KeyPartyException(sprintf('Error writing file data: %s', $e->getMessage()));
    }

    return TRUE;
  }

  /**
   * Build a store filename from the store name.
   *
   * @param string $table_name
   *   Name of the store.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return string
   *   A valid store file name.
   */
  public function buildTableFileName($table_name) {

    $ext = isset($this->options['extension']) ? $this->options['extension'] : 'json';
    if (substr($ext, 0, 1) !== ".") {
      $ext = "." . $ext;
    }

    $file_name = $table_name . $ext;

    return $file_name;
  }

  /**
   * Check if a store file exists.
   *
   * @param string $name
   *   Name of the store.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return bool
   *   TRUE if the file exists, or FALSE.
   */
  public function storeExists($name) {

    $file_name = $this->buildTableFileName($name);

    return $this->getFilesystem()->has($file_name);
  }

  /**
   * Create a new blank file.
   *
   * @param string $file_name
   *   The file name
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   On a successful creation, TRUE.
   */
  public function createBlankFile($file_name) {

    try {
      $this->writeData($file_name, array(), TRUE);
    }
    catch (\RuntimeException $e) {
      throw new KeyPartyException(sprintf('Error creating store file: %s', $e->getMessage()));
    }

    return TRUE;
  }

  /**
   * Initialise the Gaufrette GaufretteFactory.
   */
  public function useFilesystem($directory) {

    $filesystem_dir = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR;

    try {
      $this->filesystem = $this->getGaufrette($filesystem_dir, TRUE);
    }
    catch (\Exception $e) {

      throw new KeyPartyException(sprintf('Unable to use filesystem. %s', $e->getMessage()));
    }
  }

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
   * @return Filesystem
   *   A Gaufrette GaufretteFactory.
   */
  protected function getGaufrette($directory, $create = FALSE, $mode = 0777) {

    $adapter = $this->getGaufretteAdapter($directory, $create, $mode);

    return $this->getGaufretteFilesystem($adapter);
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
  protected function getGaufretteAdapter($directory, $create = FALSE, $mode = 0777) {

    return new Local($directory, $create, $mode);
  }

  /**
   * Get the Gaufrette adapter.
   *
   * @param Adapter $adapter
   *   A Gaufrette adapter.
   *
   * @return Filesystem
   *   A Gaufrette GaufretteFactory.
   */
  protected function getGaufretteFilesystem(Adapter $adapter) {

    return new Filesystem($adapter);
  }
}
