<?php
/**
 * Spark Framework
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
interface Spark_UnifiedConstructorInterface
{
  /**
   * __construct()
   *
   * @param mixed $options Usually an Array of underscored_key => value pairs 
   *                       representing the options
   * @return object An instance of the class
   */
  public function __construct($options = null);
  
  /**
   * setOptions()
   *
   * @param $options Options which should be set on the object
   *
   */
  public function setOptions($options);
  
}
