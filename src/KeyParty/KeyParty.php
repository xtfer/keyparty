<?php

/**
 * @file
 * The main KeyParty file.
 *
 * @license GPL v3 http://www.fsf.org/licensing/licenses/gpl.html
 * @author Chris Skene chris at xtfer dot com dot au
 * @copyright Copyright (c) 2014 Chris Skene
 */

namespace KeyParty;

use KeyParty\Converter\ConverterInterface;
use KeyParty\Exception\KeyPartyException;
use KeyParty\Jar\JarInterface;
use KeyParty\JarType\JarTypeInterface;

/**
 * Class KeyParty
 *
 * Contains a KeyParty Facade over the Container, which handles all the nitty-
 * gritty.
 *
 * @package KeyParty
 */
class KeyParty {

  const DEFAULT_JAR_TYPE = 'json';

  /**
   * The jars variable.
   *
   * @var JarInterface[]
   */
  protected $jars;

  /**
   * The jarTypes variable.
   *
   * @var JarTypeInterface[]
   */
  protected $jarTypes;

  /**
   * The dataDirectory variable.
   *
   * @var null|string
   */
  protected $dataDirectory;

  /**
   * The createJars variable.
   *
   * @var bool
   */
  protected $createJars;

  /**
   * The converter variable.
   *
   * @var ConverterInterface
   */
  protected $converter;

  /**
   * KeyParty constructor.
   *
   * @param null|string $data_directory
   *   (Optional) The directory to use. Can be either a relative path name
   *   starting from the current directory, or a full system path. Defaults
   *   to a directory called 'data' inside the current directory.
   */
  public function __construct($data_directory = 'data') {
    $this->dataDirectory = $data_directory;

    // Register the base JSON Jar.
    $this->registerJarType('json', 'KeyParty\\JarType\\Json\\JsonJarType');
  }

  /**
   * Add a new jar for use.
   *
   * This will also overwrite any existing jar at this location. Note that if
   * the underlying jar storage system doesnt handle overwriting, then this
   * "new" jar will simply access the old one.
   *
   * @param string $jar_name
   *   Name of the jar
   * @param string $jar_type
   *   Type of jar. This must already have been registered with the
   *   registerJarType() sale. The default 'json' type is already available.
   * @param bool $is_creatable
   *   (Optional) If TRUE, create the Jar if it doesn't exist. Defaults to FALSE
   *
   * @throws Exception\KeyPartyException
   *
   * @return JarInterface
   *   A Jar
   */
  public function addJar($jar_name, $jar_type = KeyParty::DEFAULT_JAR_TYPE, $is_creatable = FALSE) {

    $jar_type = $this->createJarType($jar_type);

    $jar_type->getValidator()->isValidDatabaseName($jar_name);

    $jar = $jar_type->getJar($jar_name, $this->isCreatable($is_creatable));

    $this->jars[$jar_name] = $jar;

    return $this->jars[$jar_name];
  }

  /**
   * Use a Jar.
   *
   * @param string $jar_name
   *   Name of the jar to use
   *
   * @throws Exception\KeyPartyException
   *
   * @return JarInterface
   *   A Jar
   */
  public function useJar($jar_name) {
    if (!isset($this->jars[$jar_name])) {
      throw new KeyPartyException('Invalid Jar requested.');
    }

    return $this->jars[$jar_name];
  }

  /**
   * Empty a jar.
   *
   * @param string $jar_name
   *   Name of the jar to empty.
   *
   * @throws Exception\KeyPartyException
   */
  public function emptyJar($jar_name) {

    $jar = $this->useJar($jar_name);
    $jar->emptyJar();
  }

  /**
   * Delete a Jar.
   *
   * @param string $jar_name
   *   Name of the jar to delete
   *
   * @throws Exception\KeyPartyException
   */
  public function deleteJar($jar_name) {

    $jar = $this->useJar($jar_name);
    $jar->getJarType()->getStoreAdapter()->removeStore($jar_name);
  }

  /**
   * Register a jar type for use.
   *
   * @param string $type_name
   *   Name of the type.
   * @param string $class
   *   Fully qualified class name.
   */
  public function registerJarType($type_name, $class) {
    $this->jarTypes[$type_name] = $class;
  }

  /**
   * Delete an item from the database.
   *
   * @param string $jar_name
   *   Jar to get the key from.
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
  public function remove($jar_name, $key) {

    return $this->useJar($jar_name)->delete($key);
  }

  /**
   * Get a key from the database.
   *
   * @param string $jar_name
   *   Jar to get the key from.
   * @param string $key
   *   Key of the item to delete.
   *
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   *
   * @return mixed
   *   the data
   */
  public function get($jar_name, $key) {

    $data = $this->useJar($jar_name)->select($key);

    if (isset($this->converter) && !empty($this->converter)) {
      $data = $this->converter->fromStore($data);
    }

    return $data;
  }

  /**
   * Get all keys from the database.
   *
   * @param string $jar_name
   *   Jar to get the key from.
   *
   * @throws Exception\KeyPartyException
   * @return array
   *   list of keys
   */
  public function getAll($jar_name) {

    $datas = $this->useJar($jar_name)->selectAll();

    if (isset($this->converter) && !empty($this->converter)) {
      foreach ($datas as $key => $data) {
        $datas[$key] = $this->converter->fromStore($data);
      }
    }

    return $datas;
  }

  /**
   * Set a key to store in the database.
   *
   * @param string $table
   *   Jar to get the key from.
   * @param string $key
   *   Key to use for the data
   * @param mixed $data
   *   the data to store
   *
   * @throws \Gaufrette\Exception\FileAlreadyExists
   * @throws \RuntimeException
   * @throws Exception\KeyPartyException
   * @return bool
   *   successful set
   */
  public function set($table, $key, $data) {

    if (isset($this->converter) && !empty($this->converter)) {
      $data = $this->converter->toStore($data);
    }

    return $this->useJar($table)->upsert($key, $data);
  }

  /**
   * Create a JarType.
   *
   * @param string $jar_type
   *   Type of jar. This must already have been registered with the
   *   registerJarType() sale
   *
   * @throws Exception\KeyPartyException
   * @return JarTypeInterface
   *   A JarType
   */
  protected function createJarType($jar_type) {

    if (!isset($this->jarTypes[$jar_type])) {
      throw new KeyPartyException('Invalid Jar Type');
    }

    if (!class_exists($this->jarTypes[$jar_type])) {
      throw new KeyPartyException('Invalid Jar Type class specified');
    }

    return new $this->jarTypes[$jar_type]();
  }

  /**
   * Determine if a Jar can be created.
   *
   * @param bool $default
   *   An optional default value. Defaults to FALSE.
   *
   * @return bool
   *   TRUE if the JAR can be created on the fly.
   */
  public function isCreatable($default = FALSE) {

    if (isset($this->createJars)) {
      return $this->createJars;
    }

    return $default;
  }
}
