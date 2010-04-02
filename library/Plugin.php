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
  
  public function __construct($config = null)
  {
    $this->setConfig($config);
  }
  
  /**
   * bootstrap() - Gets called by the main bootstrap when the plugin gets loaded
   */
  public function bootstrap()
  {}
  
  /**
   * beforeDispatch() - FrontController Callback, gets called before routing is done
   */
  public function beforeDispatch()
  {}
  
  /**
   * afterDispatch() - FrontController Callback, gets called after routing
   */
  public function afterDispatch()
  {}
  
  /**
   * setFrontController() - Set FrontController for the Plugin
   *
   * @param Spark_Controller_FrontController $frontController
   * @return Plugin
   */
  public function setFrontController(Spark_Controller_FrontController $frontController)
  {
    $this->_frontController = $frontController;
    return $this;
  }
  
  /**
   * getFrontController() - Returns the FrontController
   *
   * @return Spark_Controller_FrontController
   */
  public function getFrontController()
  {
    return $this->_frontController;
  }
  
  /**
   * setLayoutFilter() - Sets the layout filter to the plugin, so plugins can modify the layout
   * (Set placeholder values, layout variables, view helpers and so on)
   *
   * @param Spark_Controller_Filter_ApplyLayout $layoutFilter
   * @return Plugin
   */
  public function setLayoutFilter(Spark_Controller_Filter_ApplyLayout $layoutFilter)
  {
    $this->_layoutFilter = $layoutFilter;
    return $this;
  }
  
  /**
   * getLayoutFilter() - Returns the LayoutFilter instance
   * 
   * @return Spark_Controller_Filter_ApplyLayout
   */
  public function getLayoutFilter()
  {
    return $this->_layoutFilter;
  }
  
  /**
   * setConfig() - Sets a config to the plugin
   *
   * @param mixed $config
   * @return Plugin
   */
  public function setConfig($config)
  {
    if($config instanceof Zend_Config_Ini) {
      $config = $config->toArray();
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