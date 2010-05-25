<?php

class Pages_ErrorCommand implements Spark_Controller_CommandInterface
{
  
  protected $_exception = null;
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $code = $request->getParam("code");
    if ($code == null) {
      $code = 500;
    }
    
    $exception = $request->getParam("exception");
    
    $pages = new PageMapper;
    
    $pluginPagePath = "/plugins/pages/pages/";
    $pages->setPagePath($pluginPagePath);
    
    /**
     * If some page was not found, look for it in the 
     * Page Path of the Plugin
     */
    if ($defaultPage = $pages->find($request->getParam("page"))) {
      $errorPage = $defaultPage;
    } else {
      $pages->getRenderer()->exception = $exception;
      $pages->getRenderer()->request = $request;
      
      /**
       * First look in the default Page Path for the error view
       */
      $pages->setPagePath(PageMapper::DEFAULT_PAGES_PATH);
      
      if (!$errorPage = $pages->find("errors/{$code}")) {
      
        /**
         * Second, we look in the Page Path of our Plugin for the 
         * default error page
         */
        $pages->setPagePath($pluginPagePath);
        
        if (!$defaultErrorPage = $pages->find("errors/{$code}")) {
          $errorPage = $defaultErrorPage;
        } else {
          $errorPage = $pages->find("errors/500");
        }
      } else {
        $errorPage = $pages->find("errors/500");
      }
    }
    
    $response->appendBody($errorPage->content);
    
  }
  
}
