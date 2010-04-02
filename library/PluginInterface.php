<?php

interface PluginInterface
{
  
  public function bootstrap();
  
  public function setFrontController(Spark_Controller_FrontController $frontController);
  
  public function setLayoutFilter(Spark_Controller_Filter_ApplyLayout $layoutFilter);
  
  public function set($var, $value);
  
  public function get($var);
  
  public function setConfig($config);
  
  public function getConfig();
  
}