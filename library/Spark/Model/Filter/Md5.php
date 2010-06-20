<?php
/**
 * Mapper filter to hash a property of an Entity
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
class Spark_Model_Filter_Md5 implements Spark_Model_Filter_Interface
{
  
  protected $_property;
  protected $_append;
  protected $_prepend;
  
  public function __construct($property = "password", $append = null, $prepend = null)
  {
    $this->_property = $property;
    $this->_append = $append;
    $this->_prepend = $prepend;
    
  }
  
  public function execute(Spark_Model_Entity $entity)
  {
    
    $property = $this->_property;
    
    $entity->$property = md5($this->_prepend . $entity->$property . $this->_append);
    
  }
  
}
