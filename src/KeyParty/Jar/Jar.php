<?php

/**
 * @file
 * Contains a Jar class.
 */

namespace KeyParty\Jar;

use KeyParty\Exception\InvalidKeyException;
use KeyParty\Exception\KeyPartyException;
use KeyParty\JarType\JarTypeInterface;

/**
 * Class Jar
 *
 * @package KeyParty
 */
class Jar implements JarInterface {

  /**
   * The cache variable.
   *
   * @var \KeyParty\Cache\CacheInterface
   */
  protected $cache;

  /**
   * The data variable.
   *
   * @var array
   */
  protected $data;

  /**
   * The flush variable.
   *
   * @var bool
   */
  protected $flush;

  /**
   * The broker variable.
   *
   * @var JarTypeInterface
   */
  protected $jarType;

  /**
   * The tableName variable.
   *
   * @var string
   */
  protected $name;

  /**
   * Constructor.
   *
   * @param JarTypeInterface $jar_type
   *   The Container.
   * @param string $name
   *   Name of the table to use.
   */
  public function __construct(JarTypeInterface $jar_type, $name) {
    $this->jarType = $jar_type;
    $this->name = $name;

    $this->cache = $jar_type->getCache();
  }

  /**
   * Check that the key has been set on the row.
   *
   * @param string $key
   *   The key.
   *
   * @return bool
   *   TRUE if the key is valid
   * @throws KeyPartyException
   */
  public function checkKey($key) {

    try {
      $this->getJarType()->getValidator()->isValidKey($key);
    }
    catch(InvalidKeyException $e) {
      throw new KeyPartyException(sprintf('Could not insert row: %s', $e->getMessage()));
    }

    return TRUE;
  }

  /**
   * Delete a row from the table.
   *
   * @param string $identifier
   *   ID of the item to remove.
   *
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE on successful delete.
   */
  public function delete($identifier) {

    $result = FALSE;

    $this->jarType->getValidator()->isValidKey($identifier);

    $table_data = $this->readData($this->name);

    if (array_key_exists($identifier, $table_data)) {

      unset($table_data[$identifier]);
      $result = $this->writeData($this->name, $table_data);

      // Remove from cache.
      $this->cache->remove($identifier);
    }

    return $result;
  }

  /**
   * Empty the jar.
   */
  public function emptyJar() {

    $this->jarType->getStoreAdapter()->emptyStore($this->getName());
  }

  /**
   * Get the value for JarType.
   *
   * @return JarTypeInterface
   *   The value of JarType.
   */
  public function getJarType() {

    return $this->jarType;
  }

  /**
   * Get the value for Name.
   *
   * @return string
   *   The value of Name.
   */
  public function getName() {

    return $this->name;
  }

  /**
   * Insert a row into the table.
   *
   * @param string $key
   *   The key to use
   * @param mixed $row
   *   The data to set
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return bool
   *   TRUE on a successful write.
   */
  public function insert($key, $row) {

    $this->checkKey($key);

    return $this->writeRow($key, $row, FALSE);
  }

  /**
   * Select a row from the table.
   *
   * @param string $key
   *   The key to select
   *
   * @throws KeyPartyException
   * @return mixed
   *   Result.
   */
  public function select($key) {

    $this->jarType->getValidator()->isValidKey($key);

    // Look in cache for key.
    $result = $this->cache->get($key);
    if (!empty($result) && !is_null($result)) {
      return $result;
    }

    $table_data = $this->readData($this->name);

    // @todo array keys can't be numeric. indexes??
    if (array_key_exists($key, $table_data)) {

      // Save to cache.
      $this->cache->set($key, $table_data[$key]);

      return $table_data[$key];
    }

    return NULL;
  }

  /**
   * Get all keys from the database.
   *
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return array
   *   All keys from the object.
   */
  public function selectAll() {

    $decoded = (array) $this->readData($this->name);

    return $decoded;
  }

  /**
   * Update a row in the table.
   *
   * @param string $key
   *   The key to use
   * @param mixed $row
   *   The data to set
   *
   * @throws KeyPartyException
   * @return bool
   *   TRUE on a successful write.
   */
  public function update($key, $row) {

    $this->checkKey($key);

    return $this->writeRow($key, $row, TRUE);
  }

  /**
   * Upsert a row in the table.
   *
   * @param string $key
   *   The key to use
   * @param mixed $row
   *   The data to set
   *
   * @throws KeyPartyException
   * @return bool
   *   TRUE on a successful write.
   */
  public function upsert($key, $row) {

    $this->checkKey($key);

    return $this->writeRow($key, $row, TRUE);
  }

  /**
   * Read data from the filesystem.
   *
   * @param string $table
   *   Jar to get the key from.
   * @param bool $flush
   *   If TRUE, to reload the file from disc.
   *
   * @throws \RuntimeException
   * @throws KeyPartyException
   * @throws \Gaufrette\Exception\FileNotFound
   *
   * @return mixed
   *   Result of the filesystem data.
   */
  protected function readData($table, $flush = TRUE) {

    static $decoded = array();

    if (isset($decoded[$table]) && $flush == FALSE && $this->flush == FALSE) {

      return $decoded[$table];
    }

    $raw = $this->jarType->getStoreAdapter()->readFromStore($table);

    // TRUE here tells us to return an array, as this is a list.
    $decoded[$table] = $this->jarType->getSerializer()->decode($raw);
    $this->flush = FALSE;

    return $decoded[$table];
  }

  /**
   * Write data to the database.
   *
   * @param string $table
   *   Jar to get the key from.
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
  public function writeData($table, $data) {

    // @todo
    // Allow options to be passed to json_encode().
    $encoded = $this->jarType->getSerializer()->encode($data);

    return $this->jarType->getStoreAdapter()->writeToStore($table, $encoded);
  }

  /**
   * Handle the row write operation for insert, update and upsert.
   *
   * @param string $key
   *   Key of the row to write.
   * @param array $row
   *   The row value
   * @param bool $allow_overwrite
   *   Whether to allow the row to be overwritten. Defaults to TRUE.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   * @return bool
   *   TRUE if the write succeeds.
   */
  protected function writeRow($key, $row, $allow_overwrite = TRUE) {

    $table_data = $this->selectAll();

    if ($allow_overwrite == FALSE && isset($table_data[$key])) {
      throw new KeyPartyException('Cannot insert. Key exists');
    }

    $table_data[$key] = $row;

    $result = $this->writeData($this->name, $table_data);

    // Save to cache.
    $this->cache->set($key, $row);

    if ($result > 0) {

      return TRUE;
    }

    return FALSE;
  }
}
