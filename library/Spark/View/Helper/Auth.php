<?php

class Spark_View_Helper_Auth extends Zend_View_Helper_Abstract
{
  
  protected $_auth = null;
  
  public function auth()
  {
    if(null === $this->_auth) {
      $this->_auth = Zend_Auth::getInstance();
    }
    return $this->_auth; 
  }  
  
  
}
