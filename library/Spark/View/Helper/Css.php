<?php

class Spark_View_Helper_Css extends Zend_View_Helper_Abstract
{
  
  protected $_view;
  
  public function css()
  {
    return $this;
  }
  
  public function setView(Zend_View_Interface $view)
  {
    $this->_view = $view;
  }
  
  public function getView()
  {
    return $this->_view;
  }
  
  public function getSignature()
  {
    $request = $this->getView()->request();
    
    $signature = $request->getModuleName() . "-"
                 . $request->getControllerName();
    
    if($request->getActionName() != "index") {
      $signature .= "-" . $request->getActionName();
    }
                 
    return $signature;
    
  }
  
}


?>
