<?php

class Pages_ErrorController implements Spark_Controller_Controller
{
  
  public function execute(
    Zend_Controller_Request_Abstract  $request,
    Zend_Controller_Response_Abstract $response
  )
  { 
    $code      = $request->getParam("code");
    $exception = $request->getParam("exception");
    
    if ($code == null or (ENVIRONMENT === "production" and $code > 500)) {
      $code = 500;
    }
    
    /*
     * Make the Exception which triggered the error and the request
     * available to the error page
     */
    Spark_Registry::set("exception", $exception);
    Spark_Registry::set("request", $request);
    
    /*
     * First, we look in the Default Path for the error view
     */
    
    if($errorPage = Page::find("_errors/{$code}")) {
      $errorPage->setAttribute("exception", $exception)
                ->setAttribute("request", $request);
      $response->appendBody($errorPage->getContent());
      return;
    }
    
    throw new Exception("No Error Page Template found");
  }
  
}