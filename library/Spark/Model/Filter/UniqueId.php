<?php

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
