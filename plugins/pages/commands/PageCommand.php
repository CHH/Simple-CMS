<?php

class Pages_PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    $pagesConfig = Spark_Registry::get("PagesConfig");
    $pageMapper = new PageMapper;
    
    if(isset($pagesConfig->pages->extension)) {
      $pageMapper->setPageExtension($pagesConfig->pages->extension);
    }
    
    $id = $request->getParam("page");
    
    $page = $pageMapper->find($id);
    
    $content = $page->content;
    
    $response->appendBody($content);
  }
  
}
