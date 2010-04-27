<?php

abstract class Plugin implements PluginInterface
{
  /**
   * @var array
   */
  protected $_vars = array();
  
  /**
   * @var Spark_Controller_FrontController
   */
  protected $_frontController = null;
  
  /**
   * @var Spark_Controller_Filter_ApplyLayout
   */
  protected $_layoutFilter = null;
  
  /**
   * @var array
   */
  protected $_config = array();
  
  /**
   * bootstrap() - Gets called by the main bootstrap when the plugin gets loaded
   */
  public function bootstrap()
  {}
  
  /**
   * beforeDispatch() - FrontController Callback, gets called before a plugin
   * command gets executed
   */
  public function beforeDispatch()
  {}
  
  /**
   * afterDispatch() - FrontController Callback, gets called after a command of 
   *  this plugin is executed and before the response is sent back to the client.
   */
  public function afterDispatch()
  {}
  
  /**
   * setConfig() - Sets a config to the plugin
   *
   * @param mixed $config
   * @return Plugin
   */
  public function setConfig($config)
  {
    if(is_array($config)) {
      $config = new Zend_Config($config);
    }
    
    $this->_config = $config;
    return $this;
  }
  
  /**
   * getConfig() - Returns the plugin config
   *
   * @return array
   */
  public function getConfig()
  {
    return $this->_config;
  }
  
  public function getPath()
  {
    return PLUGINS . DIRECTORY_SEPARATOR . strtolower(get_class($this));
  }
  
  /**
   * set() - Sets a key value pair, which can be accessed like a object property
   *
   * @param string $var
   * @param mixed $value
   * @return Plugin
   */
  public function set($var, $value)
  {
    $this->_vars[$var] = $value;
    return $this;
  }
  
  /**
   * get() - Returns the value for the given key
   *
   * @param string $var
   * @return mixed
   */
  public function get($var)
  {
    if(array_key_exists($var, $this->_vars)) {
      return $this->_vars[$var];
    }

    throw new Exception("You tried to access the property {$var}, but it hasn't been set");
  }
  
  /**
   * __set() - Alias for set(), enables $this->$var = $value assignments
   * @see set()
   * @param string $var
   * @param mixed $value
   */
  public function __set($var, $value)
  {
    $this->set($var, $value);
  }
  
  /**
   * __get - Alias for get()
   * @see get()
   * @param string $var
   * @return mixed
   */
  public function __get($var)
  {
    return $this->get($var);
  }
  
}