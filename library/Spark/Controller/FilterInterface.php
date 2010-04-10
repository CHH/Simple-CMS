<?php

interface Spark_Controller_FilterInterface
{
  
  public function execute(Spark_Controller_RequestInterface $request, 
    Zend_Controller_Response_Abstract $response);
  
}