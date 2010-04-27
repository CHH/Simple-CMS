<?php

class Pages_DefaultCommand implements Spark_Controller_CommandInterface
{

  public function execute(
    Spark_Controller_RequestInterface $request,
    Zend_Controller_Response_Abstract $response
  )
  {
    var_dump($request->getParams());
  }

}