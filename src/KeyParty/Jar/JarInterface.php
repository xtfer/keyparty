<?php

/**
 * @file
 * Contains a JarInterface
 */

namespace KeyParty\Jar;

use KeyParty\JarType\JarTypeInterface;
use KeyParty\Exception\InvalidKeyException;
use KeyParty\Exception\KeyPartyException;

/**
 * Class JarInterface
 *
 * @package KeyParty\Jar
 */
interface JarInterface {

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
  public function checkKey($key);

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
  public function delete($identifier);

  /**
   * Empty the jar.
   */
  public function emptyJar();

  /**
   * Get the value for JarType.
   *
   * @return JarTypeInterface
   *   The value of JarType.
   */
  public function getJarType();

  /**
   * Get the value for Name.
   *
   * @return string
   *   The value of Name.
   */
  public function getName();

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
  public function insert($key, $row);

  /**
   * Select a row from the table.
   *
   * @param $key
   *
   * @throws KeyPartyException
   * @return mixed
   */
  public function select($key);

  /**
   * Get all keys from the database.
   *
   * @throws \RuntimeException
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return array
   *   All keys from the object.
   */
  public function selectAll();

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
  public function update($key, $row);

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
  public function upsert($key, $row);

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
  public function writeData($table, $data);
}
