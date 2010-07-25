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
  public function init()
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
   * Load an other Plugin
   *
   * @throws InvalidArgumentException
   *
   * @param  string $plugin,... Plugins which should be loaded
   * @return Plugin
   */ 
  public function dependOn()
  {
    $plugins      = func_get_args();
    $pluginLoader = $this->getPluginLoader();
    
    if (!$plugins) {
      throw new InvalidArgumentException("No plugin given");
    }
    
    foreach ($plugins as $plugin) {
      $pluginLoader->load($plugin);
    }

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
