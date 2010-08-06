<?php

class PluginLoader implements PluginLoaderInterface
{

  protected $_pluginPath = null;
  
  protected $_exports = array();
  
  protected $_pluginRegistry = null;
  
  const ERROR_LOADING_PLUGIN = 510;
  const ERROR_BOOTSTRAPPING_PLUGIN = 511;
  
  public function __construct(array $options = array())
  {
    $this->setOptions($options);
  }
  
  public function setOptions(array $options)
  {
    Spark_Options::setOptions($this, $options);
    return $this;
  }
  
  public function loadDirectory($pluginPath = null) 
  {
    if(is_null($pluginPath)) {
      $pluginPath = $this->getPluginPath();
    }
    
    $pluginIterator = new DirectoryIterator($pluginPath);
    
    $failedPlugins = array();
    
    foreach($pluginIterator as $entry) { 
      if($entry->isDir() and !$entry->isDot()) {
        try {
          $this->load($entry->getFilename());
          
        } catch(PluginException $e) {
          $failedPlugins[$e->getPluginName()] = $e;
        }
      }
    }
    
    if($failedPlugins) {
      $failedPluginList = join(array_keys($failedPlugins), ", ");
      
      $e = new PluginException(
        "main", 
        "Following plugins could not be loaded: {$failedPluginList}. Make sure 
          these plugins are correctly installed",
        self::ERROR_LOADING_PLUGIN,
        $failedPlugins
      );
      
      throw $e;
    }
  }
  
  public function load($id)
  { 
    $pluginRegistry = $this->getPluginRegistry();
    $pluginClass    = $this->_getPluginClass($id);
    
    if($pluginRegistry->has($pluginClass)) {
      return false;
    }
    
    $ds                  = DIRECTORY_SEPARATOR;
    $pluginBootstrapFile = $this->getPluginPath() . $ds . $id . $ds . $pluginClass . ".php";
    
    if(!@include_once($pluginBootstrapFile)) {
      $pluginDirectory = $this->getPluginPath() . $ds . $id;
      
      throw new PluginLoadException(
        $id, 
        "The Plugin was not found in \"{$pluginDirectory}\". Please make sure 
          you have installed the Plugin \"{$id}\".", 
        self::ERROR_LOADING_PLUGIN
      );
    }
    
    $plugin = new $pluginClass;
    
    if (!$plugin instanceof PluginInterface) {
      throw new PluginLoadException(
        $id, 
        "The Plugin \"{$id}\" does not implement the PluginInterface.", 
        self::ERROR_LOADING_PLUGIN
      );
    }
    
    /*
     * If Plugin extends the Abstract Plugin, then give it some more information
     */
    if ($plugin instanceof Plugin) {
      $plugin->setPath($this->getPluginPath() . $ds . $id);
      $plugin->setPluginLoader($this);
    }
       
    try {
      $plugin->init();
      
    } catch(Exception $e) {
      throw new PluginBootstrapException($id, "There was an failure during 
        bootstrapping of the plugin \"{$id}\" with the message {$e->getMessage()}",
        self::ERROR_BOOTSTRAPPING_PLUGIN);
    }
    
    $pluginRegistry->add($pluginClass, $plugin);
    
    return $plugin;
  }
  
  public function setPluginPath($pluginPath)
  {
    $this->_pluginPath = $pluginPath;
    return $this;
  }
  
  public function getPluginPath()
  {
    if(!is_null($this->_pluginPath)) {
      return $this->_pluginPath;
    }
    
    throw new PluginException("Please set the plugin path correctly before you attempt to load plugins");
  }
  
  public function setPluginRegistry(PluginRegistry $registry)
  {
    $this->_pluginRegistry = $registry;
    return $this;
  }
  
  public function getPluginRegistry()
  {
    if(is_null($this->_pluginRegistry)) {
      $this->_pluginRegistry = new PluginRegistry;
      
      if(!Spark_Registry::has("Plugins")) {
        Spark_Registry::set("Plugins", $this->_pluginRegistry);
      }
    }
    return $this->_pluginRegistry;
  }
  
  public function setExport($name, $value)
  {
    if (isset($this->_exports[$name])) {
      throw new InvalidArgumentException("\"{$name}\" was already exported");
    }
    $this->_exports[$name] = $value;
    return $this;
  }
  
  public function getExport($name)
  {
    if (isset($this->_exports[$name])) {
      return $this->_exports[$name];
    }
    throw new InvalidArgumentException("The Export with the name {$name} has yet to be set");
  }
  
  protected function _getPluginClass($id)
  {
    $class = str_replace(" ", "", ucwords(str_replace("_", " ", $id)));
    return $class;
  }
}
