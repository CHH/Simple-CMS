<?php

interface PluginInterface
{
  
  public function bootstrap($config = null);
  
  public function setFrontController(Spark_Controller_FrontController $frontController);
  
  public function setLayoutFilter(Spark_Controller_Filter_ApplyLayout $layoutFilter);
  
  public function set($var, $value);
  
  public function get($var);
  
}