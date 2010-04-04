<?php

class ErrorCommand implements Spark_Controller_CommandInterface
{
  
  protected $_exception = null;
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $code = $request->getParam("code");
    if($code == null) {
      $code = 500;
    }
    
    $exception = $request->getParam("exception");
    
    $pages = new PageMapper;
    
    $pages->getRenderer()->exception = $exception;
    $pages->getRenderer()->request = $request;
    
    if(!$errorPage = $pages->find("errors/{$code}")) {
      $errorPage = $pages->find("errors/500");
    }
    
    $response->appendBody($errorPage->content);
    
  }
  
}