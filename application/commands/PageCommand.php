<?php

class PageCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response
  )
  {
    $params = $request->getParams();

    unset($params["command"]);
    unset($params["action"]);
    
    $path =  join($params, "/");
    
    $pathToPage = WEBROOT . DIRECTORY_SEPARATOR . "pages" . DIRECTORY_SEPARATOR 
                  . $path . ".txt";
    
    if(file_exists($pathToPage)) {
      $contents = file_get_contents($pathToPage);
      
      $textile = new Spark_View_Helper_Textile;
      
      $content = $textile->parse($contents);
    }
    
    $response->appendBody($content);
  }
  
}
