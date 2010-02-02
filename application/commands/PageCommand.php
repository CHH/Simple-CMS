<?php

class PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    
    $name = $request->getParam("name");
    $pathToPage = WEBROOT . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR . $name . ".txt";
    
    if(file_exists($pathToPage)) {
      $contents = file_get_contents($pathToPage);
      
      $textile = new Spark_View_Helper_Textile;
      
      $response->appendBody($textile->parse($contents));
    }
    
  }
  
}