<?php

/**
 * @file
 * Contains a StoreAdapterInterface.
 */

namespace KeyParty\StoreAdapter;

/**
 * Interface StoreAdapterInterface
 *
 * @package KeyParty\Filesystem
 */
interface StoreAdapterInterface {

  /**
   * Create a table.
   *
   * @param string $store_name
   *   Name of the table to create.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE if the file exists
   */
  public function createStore($store_name);

  /**
   * Empty a table.
   *
   * @param string $store_name
   *   Name of the table.
   *
   * @return bool
   *   TRUE if the file exists
   *
   * @throws \KeyParty\Exception\KeyPartyException
   */
  public function emptyStore($store_name);

  /**
   * Delete a table.
   *
   * @param string $store_name
   *   Name of the table.
   *
   * @return bool
   *   TRUE if deleted.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   */
  public function removeStore($store_name);

  /**
   * Check if a table file exists.
   *
   * @param string $name
   *   Name of the table.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE if the file exists, or FALSE.
   */
  public function storeExists($name);

  /**
   * Read data from a table.
   *
   * No decoding is performed.
   *
   * @param string $store_name
   *   Name of the table.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return string
   *   Usually, a raw JSON string.
   */
  public function readFromStore($store_name);

  /**
   * Write data to the database.
   *
   * @param string $store_name
   *   Jar to get the key from.
   * @param array $data
   *   The data to write.
   *
   * @throws \KeyParty\Exception\KeyPartyException
   *
   * @return bool
   *   TRUE on a successful write.
   */
  public function writeToStore($store_name, $data);
}
