<?php

class View_Helper_Pages extends Zend_View_Helper_Abstract
{
  
  protected $_pageMapperClass = "PageMapper";
  
  protected $_pageMapper = null;
  
  public function pages()
  {
    return $this;
  }
  
  public function getPageMapper()
  {
    if(is_null($this->_pageMapper)) {
      $this->_pageMapper = new $this->_pageMapperClass;
    }
    return $this->_pageMapper;
  }
  
  public function __call($method, $args)
  {
    return call_user_func_array(array($this->getPageMapper(), $method), $args);
  }
  
}