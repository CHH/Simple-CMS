<?php

class Spark_View_Helper_Textile extends Zend_View_Helper_Abstract
{
  
  protected $_textile;
  
  public function textile($text = null)
  {
    if(!is_null($text)) {
      return $this->parse($text);
    }
    return $this;
  }
  
  public function parse($text)
  {
    if(null == $text) {
      return null;
    } else {
      return $this->getTextile()->TextileThis($text);
    }
  }
  
  public function getTextile()
  {
    if(null === $this->_textile) {
      require_once(realpath(dirname(__FILE__)."/Textile/classTextile.php"));
      $this->_textile = new Textile;
    }
    return $this->_textile;
  }

}
