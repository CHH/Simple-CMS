<?php

class PluginLoader implements PluginLoaderInterface
{

  protected $_pluginPath = null;
  
  protected $_pluginOptions = array();
  
  protected $_pluginRegistry = null;
  
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
          
        } catch(PluginLoadException $e) {
          $failedPlugins[] = $entry->getFilename();
        }
      }
    }
    
    if($failedPlugins) {
      $failedPlugins = join($failedPlugins, ", ");
      throw new PluginLoadException("Following plugins could not be loaded: 
        {$failedPlugins}. Make sure these plugins are correctly installed");
    }
    
  }
  
  public function load($id)
  { 
    $pluginRegistry = $this->getPluginRegistry();
    $pluginClass = ucfirst($id);
    
    if(!$pluginRegistry->has($pluginClass)) {
      $ds = DIRECTORY_SEPARATOR;
      $pluginPath = $this->getPluginPath() . $ds . $id . $ds . $pluginClass . ".php";
      $pluginConfigPath = $this->getPluginPath() . $ds . $id . $ds . "config" . $ds . "plugin.ini";
      
      $config = null;
      if(file_exists($pluginConfigPath)) {
        $config = new Zend_Config_Ini($pluginConfigPath);
        
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
            $failedDependencies = join($failedDependencies, ", ");
            
            throw new PluginLoadException("The plugin \"{$id}\" depends on 
              {$failedDependencies}. Make sure that these Plugins are installed.");
          }
        }
      }
      
      if(!include_once($pluginPath)) {
        $pluginDirectory = $this->getPluginPath() . $ds . $id;
        
        throw new PluginLoadException("The Plugin was not found in 
          \"{$pluginDirectory}\". Please make sure you have installed 
          the Plugin \"{$id}\".");
      }
      
      $plugin = new $pluginClass;
      
      $plugin->setConfig($config);
      
      foreach($this->_pluginOptions as $var => $value) {
        $plugin->$var = $value;
      }
      
      try {
        $plugin->bootstrap();
        
      } catch(Exception $e) {
        throw new PluginBootstrapException("There was an failure during 
          bootstrapping of the plugin \"{$id}\" with the message {$e->getMessage()}");
      }
      
      $pluginRegistry->add($id, $plugin);
      
      return $plugin;
    }
    
    return false;
    
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
  
  public function setPluginOption($name, $value)
  {
    $this->_pluginOptions[$name] = $value;
    return $this;
  }
  
  public function __set($name, $value)
  {
    $this->setPluginOption($name, $value);
  }

  
}
