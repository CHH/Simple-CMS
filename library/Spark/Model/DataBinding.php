<?php
/**
 * Binds an Entity Property to an instance of a foreign Entity
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
class Spark_Model_DataBinding
{
  
  protected $_property;
  
  protected $_entityClass;
  
  protected $_mapperName = null;
  
  protected $_foreignKey;
  
  protected $_referenceId;
  
  protected $_dataMapper = null;
  
  static public function staticBind($class)
  {
    $instance = new self;
    return $instance->bind($class);
  }
  
  public function bind($class)
  {
    $this->_entityClass = $class;
    return $this;
  }
  
  public function to($property)
  {
    $this->_property = $property;
    return $this;
  }
  
  public function through($foreignKey)
  {
    $this->_foreignKey = $foreignKey;
    return $this;
  }
  
  public function getIdentifier()
  {
    return $this->_property;
  }
  
  public function setMapper($mapper)
  {
    if (is_string($mapper)) {
      $this->_mapperName = $mapper;
    } elseif ($mapper instanceof Spark_Model_Mapper_Interface) {
      $this->_dataMapper     = $mapper;
    }
    return $this;
  }
  
  public function getReference()
  {
    $mapper = $this->_getDataMapper();
    
    if(!($mapper instanceof Spark_Model_SelectableInterface)) {
      throw new Spark_Model_Exception("The Data Mapper must implement the"
        . " Spark_SelectableInterface");
    }
    
    $select = $mapper->getSelect();
    
    $select->where("{$this->_foreignKey} = ?", $this->getReferenceId());
    
    $dataSet = $mapper->findBySelect($select);
    
    if(count($dataSet) == 1) {
      return current($dataSet);
    }
    
    return $dataSet;
  }
  
  public function getReferenceId()
  {
    return $this->_referenceId;
  }
  
  public function setReferenceId($referenceId)
  {
    $this->_referenceId = $referenceId;
    return $this;
  }
  
  protected function _getDataMapper()
  {
    if(is_null($this->_dataMapper)) {
      if(is_null($this->_mapperName)) {
        $mapperClassName = $this->_entityClass . "Mapper";
        $this->_dataMapper = new $mapperClassName;
      } else {
        $this->_dataMapper = new $this->_mapperName;
      }
    }
    return $this->_dataMapper;
  }
  
} 
