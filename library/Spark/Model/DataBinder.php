<?php

class Spark_Model_DataBinder
{
  
  protected $_bindings = array();
  protected $_dataBinder = null;
  
  public function addBinding(Spark_Model_DataBinding $binding)
  {
    $this->_bindings[$binding->getIdentifier()] = $binding;
    return $this;
  }
  
  public function hasBinding($identifier)
  {
    return array_key_exists($identifier, $this->_bindings) ? true : false;
  }
  
  public function getBinding($identifier)
  {
    if($this->hasBinding($identifier)) {
      return $this->_bindings[$identifier];
    }
  }
  
}
