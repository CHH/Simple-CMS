<?php

class PluginLoader implements PluginLoaderInterface
{

  protected $_pluginPath = null;
  
  protected $_exports = array();
  
  protected $_pluginRegistry = null;
  
  const ERROR_LOADING_PLUGIN = 510;
  const ERROR_BOOTSTRAPPING_PLUGIN = 511;
  
  public function loadDirectory($pluginPath = null) {
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
    
    if($pluginRegistry->has($id)) {
      return false;
    }
    
    $pluginClass         = $this->_getPluginClass($id);

    $ds                  = DIRECTORY_SEPARATOR;
    $pluginBootstrapFile = $this->getPluginPath() . $ds . $id . $ds . $pluginClass . ".php";
    $pluginConfigFile    = $this->getPluginPath() . $ds . $id . $ds . "config" . $ds . "plugin.ini";
    
    $config = null;
    
    if(file_exists($pluginConfigFile)) {
      $config = new Zend_Config_Ini($pluginConfigFile);
      
      if($config->depends_on) {
        $failedDependencies = array();
        
        foreach($config->depends_on as $dependency) {
          try {
            $this->load($dependency);
            
          } catch(PluginLoadException $e) {
            $failedDependencies[] = $dependency;
          }
        }
        
        if($failedDependencies) {
          $failedDependenciesString = join($failedDependencies, ", ");
          
          $e = new PluginLoadException(
            $id, 
            "The plugin \"{$id}\" depends on {$failedDependenciesString}. 
              Make sure that these Plugins are installed.", 
            self::ERROR_LOADING_PLUGIN
          );
          $e->setFailedDependencies($failedDependencies);
          throw $e;
        }
      }
    }
    
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
    
    if ($plugin instanceof Plugin) {
      $plugin->setConfig($config);
      $plugin->setPath($this->getPluginPath() . $ds . $id);
      $plugin->setPluginLoader($this);
    }
       
    try {
      if (!method_exists($plugin, "bootstrap")) {
        throw new Exception("The Method \"bootstrap()\" was not found.");
      }
      $plugin->bootstrap();
      
    } catch(Exception $e) {
      throw new PluginBootstrapException($id, "There was an failure during 
        bootstrapping of the plugin \"{$id}\" with the message {$e->getMessage()}",
        self::ERROR_BOOTSTRAPPING_PLUGIN);
    }
    
    $pluginRegistry->add($id, $plugin);
    
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
      throw new InvalidArgumentException("{$name} is already exported");
    }
    $this->_exports[$name] = $value;
    return $this;
  }
  
  public function getExport($name)
  {
    if (isset($this->_exports[$name])) {
      return $this->_exports[$name];
    }
    throw new InvalidArgumentException("The option with the name {$name} has yet to be set");
  }
  
  protected function _getPluginClass($id)
  {
    $class = str_replace(" ", "", ucwords(str_replace("_", " ", $id)));
    return $class;
  }
}
