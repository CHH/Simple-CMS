<?php
/**
 * Mapper Filter which replaces the property value of an Entity
 * 
 * This source file is subject to the MIT license that is bundled
 * with this package in the file LICENSE.txt.
 *
 * @category   Spark
 * @package    Spark_Event
 * @author     Christoph Hochstrasser <christoph.hochstrasser@gmail.com>
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */

/**
 * @category   Spark
 * @package    Spark_Event
 * @copyright  Copyright (c) 2010 Christoph Hochstrasser
 * @license    MIT License
 */
class Spark_Model_Filter_Replace implements Spark_Model_Filter_Interface
{
  
  protected $_property = "";
  protected $_value = "";
  
  public function __construct($property, $value)
  {
    $this->_property = $property;
    $this->_value = $value;
  }
  
  public function execute(Spark_Model_Entity $entity)
  {
    $property = $this->_property;
    $value = $this->_value;
    
    $entity->$property = $value;
  }

}
