<?php
/**
 * Declares that Mapper is able to select data with Zend_Db_Select
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
interface Spark_Model_SelectableInterface
{
  
  public function getSelect();
  
  public function findBySelect(Zend_Db_Select $select);
}
