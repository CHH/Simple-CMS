<?php

class Sample_IndexCommand implements Spark_Controller_CommandInterface
{
  
  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    $response->appendBody("Hello from Sample");
  }
  
}