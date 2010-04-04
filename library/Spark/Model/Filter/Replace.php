<?php

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
