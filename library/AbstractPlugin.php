<?php

abstract class AbstractPlugin implements Plugin
{ 
  /**
   * Instance of the plugin loader
   * @var PluginLoader
   */
  protected $pluginLoader;
  
  /**
   * Absolute path to the directory of the plugin
   * @var string
   */
  protected $path;
  
  /**
   * bootstrap() - Gets called by the main bootstrap when the plugin gets loaded
   */
  public function init()
  {}
  
  /**
   * preDispatch() - FrontController Callback, gets called before a plugin
   * command gets executed
   */
  public function preDispatch($request, $response)
  {}
  
  /**
   * postDispatch() - FrontController Callback, gets called after a command of 
   * this plugin is executed and before the response is sent back to the client.
   */
  public function postDispatch($request, $response)
  {}
  
  public function setPluginLoader(PluginLoader $pluginLoader)
  {
    $this->pluginLoader = $pluginLoader;
    return $this;
  }
  
  /**
   * Returns the plugin loader
   *
   * @return PluginLoader
   */
  public function getPluginLoader()
  {
    return $this->pluginLoader;
  }
  
  /**
   * Returns the absolute path to the plugin directory
   *
   * @return string
   */
  public function getPath()
  {
    if (is_null($this->path)) {
      $this->path = PLUGINS . DIRECTORY_SEPARATOR . strtolower(get_class($this));
    }
    return $this->path;
  }
  
  /**
   * Sets the absolute path to the plugin directory
   *
   * @param  string
   * @return Plugin
   */
  public function setPath($path)
  {
    $this->path = $path;
    return $this;
  }

  /**
   * Load an other Plugin
   *
   * @throws InvalidArgumentException
   *
   * @param  string $plugin,... Plugins to load
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
