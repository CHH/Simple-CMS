<?php

interface Spark_Controller_CommandResolverInterface
{
  
  public function getCommand(Spark_Controller_RequestInterface $request);
  
}