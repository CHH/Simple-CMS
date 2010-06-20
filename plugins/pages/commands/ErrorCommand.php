<?php

class Pages_ErrorCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $pages          = new PageMapper;
    $pluginPagePath = "/plugins/pages/default/";
    $pages->setPagePath($pluginPagePath);
    
    $code      = $request->getParam("code");
    $exception = $request->getParam("exception");
    
    if ($code == null or (APPLICATION_ENVIRONMENT === "production" and $code > 500)) {
      $code = 500;
    }
    
    /**
     * Render a page predefined by our plugin
     */
    if ($defaultPage = $pages->find($request->getParam("page")) and $code == 404) {
      $response->appendBody($defaultPage->content);
      return;
    }
    
    /**
     * Make the Exception which triggered the error and the request
     * available to the error page
     */
    $pages->getRenderer()->exception = $exception;
    $pages->getRenderer()->request   = $request;

    /**
     * First, we look in the Default Path for the error view
     */
    $pages->setPagePath(PageMapper::DEFAULT_PAGES_PATH);

    if($errorPage = $pages->find("errors/{$code}")) {
      $response->appendBody($errorPage->content);
      return;
    }

    /**
    * Second, we look in the Page Path of our Plugin for the 
    * default error page
    */
    $pages->setPagePath($pluginPagePath);

    if ($defaultErrorPage = $pages->find("errors/{$code}")) {
      $errorPage = $defaultErrorPage;
    } else {
      $errorPage = $pages->find("errors/500");
    }
    
    $response->appendBody($errorPage->content);
    
  }
  
}
