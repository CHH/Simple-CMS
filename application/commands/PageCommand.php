<?php

class PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    $pagesConfig = Spark_Registry::get("PagesConfig");
    
    if(isset($pagesConfig->pages->extension)) {
      $ext = $pagesConfig->pages->extension;
    } else {
      $ext = ".txt";
    }
    
    $params = $request->getParams();
    
    $pathToPage = WEBROOT . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR 
                  . join($params, "/") . $ext;
    
    if(file_exists($pathToPage)) {
      $contents = file_get_contents($pathToPage);
      
      $textile = new Spark_View_Helper_Textile;
      
      $content = $textile->parse($contents);
    } else {
      // Render 404 View
      $errorViews = new Zend_View;
      $errorViews->setScriptPath(APPLICATION_PATH . "/views/Error");
      
      $errorViews->requestedPageName = $params[(count($params) - 1)];
      $errorViews->requestedPath = join($params, "/");
      $errorViews->request = $request;
      
      $content = $errorViews->render("404.phtml");
    }
    
    $response->appendBody($content);
  }
  
}
