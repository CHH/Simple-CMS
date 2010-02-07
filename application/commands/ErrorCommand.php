<?php

class ErrorCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    
    $view = new Zend_View;
    $view->setScriptPath(APPLICATION_PATH . "/views");
    
    $view->request = $request;
    
    $response->appendBody($view->render("Error/error.phtml"));
    
  }
  
}