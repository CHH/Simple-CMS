<?php

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
