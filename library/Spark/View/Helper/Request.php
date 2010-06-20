<?php

class Spark_View_Helper_Request extends Zend_View_Helper_Abstract
{
  protected $_request = null;
  
  public function request()
  {
    if(null === $this->_request) {
      $this->_request = Zend_Controller_Front::getInstance()->getRequest();
    }
    return $this->_request;
  } 
  
}

?>
