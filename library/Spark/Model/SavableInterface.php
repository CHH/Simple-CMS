<?php
/**
 * Declares an Entity as Savable
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
interface Spark_Model_SavableInterface
{
  
  /**
   * Returns the savable data as array
   * 
   * @return array
   */
  public function asSavable();
  
}