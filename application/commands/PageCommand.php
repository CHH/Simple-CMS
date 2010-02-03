<?php

class PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    $config = new Zend_Config_Ini(CONFIGS . "/pages.ini");
    
    $name = $request->getParam("name");
    $pathToPage = WEBROOT . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . $name . ".txt";
    
    if(file_exists($pathToPage)) {
      $contents = file_get_contents($pathToPage);
      
      $textile = new Spark_View_Helper_Textile;
      
      $content = $textile->parse($contents);
    }
    
    if(isset($config->pages->layout_name) and 
       file_exists($config->pages->layout_path . "/" . $config->pages->layout_name)) 
    {
      $layout = new Zend_View;
      $layout->setScriptPath($config->pages->layout_path);
      $layout->content = $content;
      $content = $layout->render($config->pages->layout_name);
    }
    
    $response->appendBody($content);
  }
  
}
