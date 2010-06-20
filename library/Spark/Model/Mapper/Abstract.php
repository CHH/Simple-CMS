<?php
/**
 * Abstract Mapper Class, implements Data Mapper, Identity Map and Filter Patterns
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
abstract class Spark_Model_Mapper_Abstract 
  implements Spark_Model_Mapper_Interface
{
  
  protected $_entityClass  = "Spark_Model_Entity";
  
  protected static $_identityMap  = array();
  
  protected $_preSaveFilters = null;
  protected $_postSaveFilters = null;
  
  protected $_preUpdateFilters = null;
  protected $_postUpdateFilters = null;
  
  public function __construct()
  {    
    $this->_preSaveFilters    = new Spark_Model_FilterChain;
    $this->_postSaveFilters   = new Spark_Model_FilterChain;
    $this->_preUpdateFilters  = new Spark_Model_FilterChain;
    $this->_postUpdateFilters = new Spark_Model_FilterChain;
    
    $this->init();
  }
  
  public function init()
  {}
  
  public function create($data = null, $ignoreNotExistingProperties = false) {
    return $this->newEntity($data, $ignoreNotExistingProperties);
  }
  
  public function newEntity($data = null, $ignoreNotExisting = false) {
    return new $this->_entityClass($data, $ignoreNotExisting);
  }
  
  protected function _setIdentity($id, $entity)
  {
    if(!$this->_hasIdentity($id)) {
      self::$_identityMap[$id] = $entity;
    }
    return $entity;
  }
  
  protected function _getIdentity($id)
  {
    if($this->_hasIdentity($id)) {
      return self::$_identityMap[$id];
    }
  }
  
  protected function _hasIdentity($id)
  {
    return array_key_exists($id, self::$_identityMap) ? true : false;
  }
  
  public function addPreSaveFilter(Spark_Model_Filter_Interface $filter)
  {
    $this->_preSaveFilters->addFilter($filter);
  }
  
  public function addPostSaveFilter(Spark_Model_Filter_Interface $filter)
  {
    $this->_postSaveFilters->addFilter($filter);
  }
  
  public function addPreUpdateFilter(Spark_Model_Filter_Interface $filter)
  {
    $this->_preUpdateFilters->addFilter($filter);
  }
  
  public function addPostUpdateFilter(Spark_Model_Filter_Interface $filter)
  {
    $this->_postUpdateFilters->addFilter($filter);
  }  
   
}

