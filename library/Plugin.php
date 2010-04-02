<?php

abstract class Plugin implements PluginInterface
{
  
  protected $_vars = array();
  
  protected $_frontController = null;
  
  protected $_layoutFilter = null;
  
  protected $_config = null;
  
  public function __construct($config = null)
  {
    $this->setConfig($config);
  }
  
  public function bootstrap()
  {}
  
  public function beforeDispatch()
  {}
  
  public function afterDispatch()
  {}
  
  public function setFrontController(Spark_Controller_FrontController $frontController)
  {
    $this->_frontController = $frontController;
    return $this;
  }
  
  public function getFrontController()
  {
    return $this->_frontController;
  }
  
  public function setLayoutFilter(Spark_Controller_Filter_ApplyLayout $layoutFilter)
  {
    $this->_layoutFilter = $layoutFilter;
    return $this;
  }
  
  public function getLayoutFilter()
  {
    return $this->_layoutFilter;
  }
  
  public function setConfig($config)
  {
    $this->_config = $config;
    return $this;
  }
  
  public function getConfig()
  {
    return $this->_config;
  }
  
  public function set($var, $value)
  {
    $this->_vars[$var] = $value;
    return $this;
  }
  
  public function get($var)
  {
    if(array_key_exists($var, $this->_vars)) {
      return $this->_vars[$var];
    }

    throw new Exception("You tried to access the property {$var}, but it hasn't been set");
  }
  
  public function __set($var, $value)
  {
    $this->set($var, $value);
  }
  
  public function __get($var)
  {
    return $this->get($var);
  }
  
}