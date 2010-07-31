<?php

class Pages_ErrorCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $pluginPagePath = DIRECTORY_SEPARATOR . "plugins" . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . "default";
    Page::setPath($pluginPagePath);
    
    $code      = $request->getParam("code");
    $exception = $request->getParam("exception");
    
    if ($code == null or (ENVIRONMENT === "production" and $code > 500)) {
      $code = 500;
    }
    
    /**
     * Render a page predefined by our plugin
     */
    if ($defaultPage = Page::find($request->getParam("page")) and $code == 404) {
      $response->appendBody($defaultPage->content);
      return;
    }

    $renderer = Page::getRenderer();
    
    /**
     * Make the Exception which triggered the error and the request
     * available to the error page
     */
    $renderer->exception = $exception;
    $renderer->request   = $request;

    /**
     * First, we look in the Default Path for the error view
     */
    Page::setPath("/pages");

    if($errorPage = Page::find("_errors/{$code}")) {
      $response->appendBody($errorPage->content);
      return;
    }

    /**
    * Second, we look in the Page Path of our Plugin for the 
    * default error page
    */
    Page::setPath($pluginPagePath);

    if ($defaultErrorPage = Page::find("_errors/{$code}")) {
      $errorPage = $defaultErrorPage;
    } else {
      $errorPage = Page::find("_errors/500");
    }
    
    $response->appendBody($errorPage->content);
    
  }
  
}
