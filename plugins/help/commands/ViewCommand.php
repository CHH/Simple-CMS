<?php

class Help_ViewCommand implements Spark_Controller_CommandInterface
{

  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract  $response
  )
  {
    $pages = new PageMapper;
    $pages->setPagePath("plugins/help/public/topics");
    
    $page = $pages->find($request->getParam("page"), $request->getParam("topic"));
    
    if($page) {
      $applyLayoutFilter = Spark_Registry::get("Plugins")->Help->getLayoutFilter()
                             ->setLayoutPath(PLUGINS . "/help/public")
                             ->setLayoutName("layout.phtml");
      
      $response->appendBody($page->content);
    } else {
      $response->appendBody($pages->setPagePath(PageMapper::DEFAULT_PAGES_PATH)
               ->find("errors/404")->content);
    }
  }
  
}
