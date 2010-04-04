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
    
    $id = $request->getParam("page");
    
    $page = $pageMapper->find($id);

    if($page) {
      $content = $page->content;
    }
    
    $response->appendBody($content);
  }
  
}
