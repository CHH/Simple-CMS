<?php

abstract class Plugin implements PluginInterface
{
  /**
   * @var array
   */
  protected $_config = array();
  
  /**
   * @var PluginLoaderInterface
   */
  protected $_pluginLoader;
  
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
  
  public function setPluginLoader(PluginLoaderInterface $pluginLoader)
  {
    $this->_pluginLoader = $pluginLoader;
    return $this;
  }
  
  public function getPluginLoader()
  {
    return $this->_pluginLoader;
  }
  
  public function getPath()
  {
    if (is_null($this->_path)) {
      $this->_path = PLUGINS . DIRECTORY_SEPARATOR . strtolower(get_class($this));
    }
    return $this->_path;
  }
  
  public function setPath($path)
  {
    $this->_path = $path;
    return $this;
  }
  
  /**
   * export() - Exports an object so other plugins can import it
   *
   * @param string $var
   * @param mixed $value
   * @return Plugin
   */
  public function export($var, $object)
  {
    $this->getPluginLoader()->setExport($var, $object);
    return $this;
  }
  
  /**
   * import() - Returns an object from the PluginLoader
   *
   * @param string $var
   * @return mixed
   */
  public function import($var)
  {
    return $this->getPluginLoader()->getExport($var);
  }
  
}
