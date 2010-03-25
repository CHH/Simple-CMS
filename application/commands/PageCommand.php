<?php

class PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    $pagesConfig = Spark_Registry::get("PagesConfig");
    $pageMapper = new PageMapper;
    
    if(isset($pagesConfig->pages->extension)) {
      $ext = $pagesConfig->pages->extension;
    } else {
      $ext = ".txt";
    }
    
    $params = $request->getParams();
    
    $indexOfLast = count($params) - 1;
    
    $id = $params[$indexOfLast];
    unset($params[$indexOfLast]);
    
    $prefix = join($params, "/");
    
    if($prefix != "partials") {
      $page = $pageMapper->find($id, $prefix);
      
      if($page) {
        $content = $page->content;
      }
    } else {
      // Render 404 View
      $errorViews = new Zend_View;
      $errorViews->setScriptPath(APPLICATION_PATH . "/views");
      
      $errorViews->requestedPageName = $params[(count($params) - 1)];
      $errorViews->requestedPath = join($params, "/");
      $errorViews->request = $request;
      
      $content = $errorViews->render("Error/404.phtml");
    }
    
    $response->appendBody($content);
  }
  
}
