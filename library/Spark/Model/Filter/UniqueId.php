<?php
/**
 * Mapper Filter replacing the value of an Entity Property with an Unique ID
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Model
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Model
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Model_Filter_UniqueId implements Spark_Model_Filter_Interface
{
  
  protected $_idProperty;
  
  public function __construct($idProperty = "id")
  {
    $this->_idProperty = $idProperty;
  }
  
  public function execute(Spark_Model_Entity $entity)
  {
    $idProperty = $this->_idProperty;
    
    $entity->$idProperty = uniqid();
    
  } 
  
}
