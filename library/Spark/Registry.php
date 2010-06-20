<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Registry
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Registry
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Registry
{
  /**
   * @var array
   */
  static protected $_registry = array();

  /**
   * set() - Sets a key in the Registry
   * 
   * @param string $key
   * @param mixed  $value
   */
  static public function set($key, $value)
  {
    self::$_registry[$key] = $value;
  }

  /**
   * get() - Returns the value stored under the given key
   *
   * @param string $key
   * @return mixed
   */
  static public function get($key)
  {
    if(self::has($key)) {
      return self::$_registry[$key];
    }
    return null;
  }

  /**
   * has() - Checks if a key is set in the Registry
   *
   * @param $key
   * @return bool
   */
  static public function has($key)
  {
    return array_key_exists($key, self::$_registry) ? true : false;
  }
  
}
