<?php

/**
 * @file
 * The main KeyParty file.
 *
 * @license GPL v3 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com dot au
 * @copyright Copyright (c) 2014 Chris Skene
 *
 * Parts of KeyParty were inspired by Flintstone, by Jason M.
 * http://github.com/fire015/flintstone
 */

namespace KeyParty;

use Gaufrette\Exception\FileAlreadyExists;
use KeyParty\Exception\DeleteDatabaseException;
use KeyParty\Exception\KeyPartyException;
use KeyParty\Service\Cache;
use KeyParty\Service\Filesystem;

/**
 * Class KeyParty
 *
 * @package KeyParty
 */
class KeyParty extends \Pimple {

  /**
   * The cache variable.
   *
   * @var array
   */
  protected $cache = array();

  /**
   * Database data
   *
   * @var array
   */
  protected $data = array();

  /**
   * The filesystem variable.
   *
   * @var \Gaufrette\Filesystem
   */
  protected $filesystem;

  /**
   * The flush variable.
   *
   * @var bool
   */
  protected $flush = FALSE;

  /**
   * KeyParty constructor.
   *
   * @param string $database
   *   The database name
   * @param null|string $directory
   *   (Optional) The directory to use. Can be either a relative path name
   *   starting from the current directory, or a full system path. Defaults
   *   to a directory called 'data' inside the current directory.
   * @param string $extension
   *   (Optional) File extension to use. Defaults to 'json'.
   * @param bool $cache
   *   (Optional) Whether to cache data in this object to avoid repeated reads
   *   of the filesystem. Defaults to true. This is just static caching.
   *
   * @throws Exception\KeyPartyException
   * @throws \InvalidArgumentException
   */
  public function __construct($database, $directory = 'data', $extension = 'json', $cache = TRUE) {

    $is_valid = $this->isValidDatabaseName($database);
    if (!$is_valid) {
      throw new KeyPartyException('Invalid characters in database name');
    }

    $this['database.name'] = $database;

    $this['options.dir'] = $directory;
    $this['options.extension'] = $extension;
    $this['options.cache'] = $cache;

    // Add the cache.
    $this['cache'] = $this->share(function () use ($cache) {

      return new Cache($cache);
    });

    // We use Gaufrette for the filesystem, but wrap it in a factory object
    // so it can be lazy loaded.
    $this['filesystem'] = $this->share(function () {

      return new Filesystem();
    });
  }

  /**
   * Delete an item from the database.
   *
   * @param string $key
   *   Key of the item to delete
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return bool
   *   TRUE if the operation is successful
   */
  public function delete($key) {

    if ($this->isValidKey($key)) {
      return $this->deleteKey($key);
    }

    return FALSE;
  }

  /**
   * Get a key from the database.
   *
   * @param string $key
   *   Key of the item to delete.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return mixed
   *   the data
   */
  public function get($key) {

    if ($this->isValidKey($key)) {
      return $this->getKey($key);
    }

    return FALSE;
  }

  /**
   * Get the cache.
   *
   * @return Cache
   *   A Cache service.
   */
  public function getCache() {

    return $this['cache'];
  }

  /**
   * Get the database name.
   *
   * Database names are always normalised to lower case to avoid
   * issues on mixed-case systems.
   *
   * @return string
   *   The database name.
   */
  public function getDatabaseName() {

    return strtolower($this['database.name']);
  }

  /**
   * Get the Filesystem.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return \Gaufrette\Filesystem
   *   A Gaufrette Filesystem
   */
  public function getFilesystem() {

    if (empty($this->filesystem)) {
      $this->initialiseFileystem();
    }

    return $this->filesystem;
  }

  /**
   * Get the Filesystem.
   *
   * @return Filesystem
   *   A Filesystem object.
   */
  public function getFilesystemService() {

    return $this['filesystem'];
  }

  /**
   * Get all keys from the database.
   *
   * @throws \RuntimeException
   * @return array
   *   list of keys
   */
  public function getKeys() {

    return $this->getAllKeys();
  }

  /**
   * Get an individual option.
   *
   * @param string $key
   *   The option to get.
   * @param mixed|null $default
   *   A default value.
   *
   * @return mixed|null
   *   The result or default value.
   */
  public function getOption($key, $default = NULL) {

    if (isset($this['options.' . $key])) {
      return $this['options.' . $key];
    }

    return $default;
  }

  /**
   * Get the options for this KeyParty.
   *
   * @return array
   *   An array of options.
   */
  public function getOptions() {

    return $this['options'];
  }

  /**
   * Check if the database name is valid.
   *
   * @param string $name
   *   The database name to check.
   *
   * @return bool
   *   TRUE if the name is valid, or FALSE if not.
   */
  public function isValidDatabaseName($name) {

    $is_valid = preg_match("/^([A-Za-z0-9_]+)$/", $name);

    return (bool) $is_valid;
  }

  /**
   * Check the database has been loaded and valid key.
   *
   * @param string $key
   *   Key of the item to delete
   *
   * @throws Exception\KeyPartyException
   * @return bool
   *   TRUE if Key of the item to delete is valid.
   */
  public function isValidKey($key) {

    // Check key length.
    $len = strlen($key);

    if ($len < 1) {
      throw new KeyPartyException('No key has been set, or provided key is empty.');
    }

    if ($len > 50) {
      throw new KeyPartyException('Maximum key length is 50 characters');
    }

    return TRUE;
  }

  /**
   * Set a key to store in the database.
   *
   * @param string $key
   *   Key of the item to delete
   * @param mixed $data
   *   the data to store
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @return bool
   *   successful set
   */
  public function set($key, $data) {

    if ($this->isValidKey($key)) {
      return $this->setKey($key, $data);
    }

    return FALSE;
  }

  /**
   * Delete a key from the database.
   *
   * @param string $key
   *   Key of the item to delete
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return bool
   *   TRUE if the delete operation is successful
   */
  protected function deleteKey($key) {

    $database = $this->readData();

    if (array_key_exists($key, $database)) {

      unset($database[$key]);
      $this->writeData($database);

      // Remove from cache.
      $this->getCache()->remove($key);
    }
  }

  /**
   * Empty the database.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return bool
   *   TRUE if the database is emptied.
   */
  public function emptyDatabase() {

    $this->getFilesystem()->write($this['filesystem.file'], array(), TRUE);
    $this->getCache()->clear();

    return TRUE;
  }

  /**
   * Get all keys from the database.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return array
   *   All keys from the object.
   */
  protected function getAllKeys() {

    $decoded = (array) $this->readData();

    return $decoded;
  }

  /**
   * Get a key from the database.
   *
   * @param string $key
   *   Key of the item to delete
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return mixed
   *   the data
   */
  protected function getKey($key) {

    // Look in cache for key.
    $result = $this->getCache()->get($key);
    if (!empty($result) && !is_null($result)) {
      return $result;
    }

    $database = $this->readData();

    if (array_key_exists($key, $database)) {

      // Save to cache.
      $this->getCache()->set($key, $database[$key]);

      return $database[$key];
    }

    return NULL;
  }

  /**
   * Initialise the Gaufrette Filesystem.
   */
  protected function initialiseFileystem() {

    $filesystem = $this->getFilesystemService();

    $this['filesystem.dir'] = rtrim($this->getOption('dir'), '/\\') . DIRECTORY_SEPARATOR;

    $ext = $this->getOption('ext', 'json');
    if (substr($ext, 0, 1) !== ".") {
      $ext = "." . $ext;
    }

    $this['filesystem.file'] = $this->getDatabaseName() . $ext;

    try {
      $this->filesystem = $filesystem->load($this['filesystem.dir'], TRUE);
    }
    catch (\Exception $e) {

      throw new KeyPartyException(sprintf('Unable to use filesystem. %s', $e->getMessage()));
    }

    // For a new filesystem, write the file if its not present.
    try {

      $this->filesystem->write($this['filesystem.file'], json_encode(array()));
    }
    catch (FileAlreadyExists $e) {
      // Do nothing, this is the desired result.
    }
  }

  /**
   * Read data from the filesystem.
   *
   * @param bool $flush
   *   If TRUE, to reload the file from disc.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return mixed
   *   Result of the filesystem data.
   */
  protected function readData($flush = TRUE) {

    static $decoded;

    if (empty($decoded) || $flush == TRUE || $this->flush == TRUE) {
      $raw = $this->getFilesystem()->read($this['filesystem.file']);
      // TRUE here tells us to return an array, as this is a list.
      $decoded = json_decode($raw, TRUE);
      $this->flush = FALSE;
    }

    return $decoded;
  }

  /**
   * Set a key to store in the database.
   *
   * @param string $key
   *   Key of the item to delete
   * @param mixed $data
   *   The data to store
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return bool
   *   Successful set
   */
  protected function setKey($key, $data) {

    $database = $this->getAllKeys();

    $database[$key] = $data;

    $result = $this->writeData($database);

    // Save to cache.
    $this->getCache()->set($key, $data);

    if ($result > 0) {

      return TRUE;
    }

    return FALSE;
  }

  /**
   * Write data to the database.
   *
   * @param array $data
   *   The data to write.
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws KeyPartyException
   *
   * @return int
   *   Returns the number of bytes written.
   */
  protected function writeData($data) {

    // @todo
    // Allow options to be passed to json_encode().
    $encoded = json_encode($data, JSON_PRETTY_PRINT);

    $error = $this->jsonLastErrorMsg();
    if (!empty($error)) {

      throw new KeyPartyException(sprintf('Invalid JSON detected: %s', $error));
    }

    return $this->getFilesystem()->write($this['filesystem.file'], $encoded, TRUE);
  }

  /**
   * Alternative to json_last_error_msg().
   *
   * This doesnt send a message when no errors are found.
   *
   * @see http://www.php.net/manual/en/function.json-last-error-msg.php
   *
   * @return NULL|string
   *   The last JSON error.
   */
  protected function jsonLastErrorMsg() {

    static $errors = array(
      JSON_ERROR_NONE => NULL,
      JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
      JSON_ERROR_STATE_MISMATCH => 'Underflow or the modes mismatch',
      JSON_ERROR_CTRL_CHAR => 'Unexpected control character found',
      JSON_ERROR_SYNTAX => 'Syntax error, malformed JSON',
      JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded',
    );
    $error = json_last_error();

    if ($error == JSON_ERROR_NONE) {

      return FALSE;
    }

    return array_key_exists($error, $errors) ? $errors[$error] : "Unknown error ({$error})";
  }

  /**
   * Delete the database.
   *
   * Warning. This is a dangerous operation. It throws an Exception on success.
   * You MUST catch this exception in your code. This ensures you don't delete
   * databases without knowing what you are doing.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   */
  public function removeDatabase() {

    $this->getFilesystem()->delete($this['filesystem.file']);

    throw new DeleteDatabaseException(sprintf('Deleted the database file %s.
    If you can see this message, this is probably an error.', $this['filesystem.file']));
  }
}
