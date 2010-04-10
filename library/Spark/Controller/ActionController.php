<?php

class Spark_Controller_ActionController implements Spark_Controller_CommandInterface
{
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $action = $request->getAction();
    
    if($action == null) {
      $action = "index";
    }
    
    $methodName = $action . "Action";
    
    if(method_exists($this, $methodName)) {
      $this->$methodName();
    }
    
  }
}