<?php

class Rest_ConsoleCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $thisPlugin = Spark_Registry::get("Plugins")->rest;
    $thisPlugin->layout->setLayoutPath($thisPlugin->getPath() . "/views")
                       ->setLayoutName("layouts/default.phtml");
    
  }
  
}