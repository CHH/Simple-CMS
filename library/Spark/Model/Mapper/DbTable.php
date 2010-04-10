<?php

class Spark_Model_Mapper_DbTable extends Spark_Model_Mapper_Abstract
  implements Spark_Model_Mapper_Interface, Spark_Model_SelectableInterface
{
  
  protected $_adapter = null;
  
  protected $_selectPrototype = null;
  
  protected $_tableName = null;
  
  protected $_idProperty   = "id";
  
  static protected $_defaultAdapter = null;
  
  public function __construct(Zend_Db_Adapter_Abstract $db = null)
  {
    if(!is_null($db)) {
      $this->setDb($db);
    }
    parent::__construct();
  }
  
  public function find($id)
  {
    if($this->_hasIdentity($id)) {
      return $this->_getIdentity($id);
    }
    
    $idProperty = $this->_idProperty;
    
    $select = $this->select()->where("{$idProperty} = ?", $id);
    
    $result = $this->getAdapter()->fetchRow($select);
    
    $entity = $this->newEntity($result);
    
    $this->_setIdentity($entity->$idProperty, $entity);
    
    return $entity;
  }
  
  public function findAll(Zend_Db_Select $select = null)
  { 
    return $this->findBySelect($select);
  }
  
  public function findBySelect(Zend_Db_Select $select)
  { 
    $resultSet = $this->getAdapter()->fetchAll($select);
    
    $dataSet = $this->mapToEntities($resultSet);
    
    return $dataSet;
  }
  
  public function save($entity)
  {
    if(!($entity instanceof $this->_entityClass)) {
      throw new Spark_Model_Exception("The Entity must be an Instance of the 
       {$this->_entityClass} Class");
    }
    
    $idProperty = $this->_idProperty;
    
    if(!$entity->$idProperty) {
    
      $this->_preSaveFilters->processFilters($entity);
      
      $entity->$idProperty = $this->getAdapter()->insert(
        $this->getTableName(), 
        $entity->asSavable()
      );
      
      $this->_postSaveFilters->processFilters($entity);
      
    } else {
      $where = $this->getAdapter()
                    ->quoteInto("{$idProperty} = ?", $entity->$idProperty);
      
      $this->_preUpdateFilters->processFilters($entity);
      
      $this->getAdapter()->update(
        $this->getTableName(), 
        $entity->asSavable(), 
        $where
      );
      
      $this->_postUpdateFilters->processFilters($entity);
    }
    
    $this->_setIdentity($entity->$idProperty, $entity);
    
    return $entity;
    
  }
  
  public function delete($entity)
  {
    $idProperty = $this->_idProperty;
    
    if($entity instanceof $this->_entityClass) {
      $where = $this->getAdapter()
                    ->quoteInto("{$idProperty} = ?", $entity->$idProperty);
    } else {
      $where = $this->getAdapter()
                    ->quoteInto("{$idProperty} = ?", $entity);
    }
    
    $this->getAdapter()->delete($this->getTableName(), $where);
    
    return $entity;
  }
  
  public function mapToEntities(array $dataSet)
  { 
    if(is_array($dataSet)) {
      $newDataSet = array();
      $idProperty = $this->_idProperty;
      
      foreach($dataSet as $data) {
        if($this->_hasIdentity($data[$idProperty])) {
          $entity = $this->_getIdentity($data[$idProperty]);
        } else {
          $entity = new $this->_entityClass($data);
          $this->_setIdentity($entity->$idProperty, $entity);
        }
        $newDataSet[] = $entity;
      }
      
    } elseif($dataSet == null) {
      return null;
      
    } else {
      throw new Spark_Model_Exception("The supplied Dataset must be an Array!");
    }
    
    return $newDataSet;
  }
  
  public function getSelect()
  {
    if(is_null($this->_selectPrototype)) {
      $this->_selectPrototype = new Zend_Db_Select($this->getAdapter());
      $this->_selectPrototype->from($this->getTableName());
    }
    return clone $this->_selectPrototype;
  }
  
  public function select()
  {
    return $this->getSelect();
  }
  
  public function getAdapter()
  {
    if(is_null($this->_adapter)) {
      if(is_null(self::$_defaultAdapter)) {
        throw new Spark_Model_Exception("An instance of a database adapter object is required for
          Spark_Model_Mapper to work. Please supply one either in the constructor or
          with the setAdapter() method or set an default Adapter with the static
          setDefaultAdapter() method.");
      }
      $this->_adapter = self::$_defaultAdapter;
    }
    
    return $this->_adapter;
  }
  
  public function setAdapter(Zend_Db_Adapter_Abstract $adapter)
  {
    $this->_adapter = $adapter;
    return $this;
  }
  
  static public function setDefaultAdapter(Zend_Db_Adapter_Abstract $adapter)
  {
    self::$_defaultAdapter = $adapter;
  }
  
  public function getTableName()
  {
    if(is_null($this->_tableName)) {
      throw new Spark_Model_Exception("The name of the table in the database is
        required for the Spark_Model_Mapper to work. Please supply the name
        either via overriding the _tableName property in your specific Mapper class
        or with the setTableName method.");
    }
    
    return $this->_tableName;
  }
  
  public function setTableName($tableName)
  {
    $this->_tableName = $tableName;
    return $this;
  }
  
}

?>
